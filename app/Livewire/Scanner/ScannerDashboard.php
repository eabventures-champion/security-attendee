<?php

namespace App\Livewire\Scanner;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Event;
use App\Models\Gate;
use App\Models\Attendee;
use App\Models\CheckIn;
use App\Models\QrCode;
use App\Enums\ScanResult;
use App\Enums\VerificationStatus;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('QR Scanner')]
class ScannerDashboard extends Component
{
    public $eventUuid;
    public $gateUuid;
    public $event;
    public $gate;
    
    public $scanResult = null;
    public $scanMessage = '';
    public $scannedAttendee = null;
    public $recentScans = [];
    public $manualSearchQuery = '';
    
    public $stats = [
        'total' => 0,
        'granted' => 0,
        'denied' => 0
    ];

    public function mount($eventUuid = null, $gateUuid = null)
    {
        $this->eventUuid = $eventUuid;
        if ($eventUuid) {
            $this->event = Event::where('uuid', $eventUuid)->first();
        }

        if (!$this->event) {
            $this->event = Event::latest()->first();
            if ($this->event) {
                $this->eventUuid = $this->event->uuid;
            }
        }

        if ($this->event) {
            if (Auth::check() && Auth::user()->isSecurityPersonnel()) {
                $assignedGate = Auth::user()->assignedGateForEvent($this->event->id);
                if ($assignedGate) {
                    $this->gate = $assignedGate;
                    $this->gateUuid = $assignedGate->uuid;
                }
            }
            
            if (!$this->gate) {
                if ($gateUuid) {
                    $this->gateUuid = $gateUuid;
                    $this->gate = Gate::where('uuid', $gateUuid)->first();
                }
                
                if (!$this->gate) {
                    $this->gate = Gate::where('event_id', $this->event->id)->where('is_active', true)->first();
                    if (!$this->gate) {
                        $this->gate = Gate::create([
                            'uuid' => (string) Str::uuid(),
                            'event_id' => $this->event->id,
                            'name' => 'Main Gate A',
                            'location' => 'Main Entrance',
                            'is_active' => true,
                            'allowed_roles' => array_map(fn($r) => $r->value, \App\Enums\AccessRole::cases()),
                        ]);
                    }
                    $this->gateUuid = $this->gate->uuid;
                }
            }
            
            $this->loadRecentScans();
            $this->updateStats();
        }
    }

    public function processQrScan($qrContent)
    {
        if (empty($qrContent)) return;

        // 1. Find QR Code entry by secure token or uuid
        $qrCode = QrCode::where('secure_token', $qrContent)
            ->orWhere('uuid', $qrContent)
            ->first();

        if (!$qrCode) {
            $this->recordScanResult('denied', 'Invalid QR code. Record not found.');
            return;
        }

        $attendee = $qrCode->attendee;

        if (!$attendee) {
            $this->recordScanResult('denied', 'No attendee associated with this QR code.');
            return;
        }

        // 2. Validate event match
        if ($attendee->event_id !== $this->event->id) {
            $this->recordScanResult('denied', 'QR code belongs to a different event.');
            return;
        }

        // 3. Check revocation / expiration
        if ($qrCode->is_revoked) {
            $this->recordScanResult('denied', 'QR code has been revoked.', $attendee);
            return;
        }

        if ($qrCode->isExpired()) {
            $this->recordScanResult('denied', 'QR code has expired.', $attendee);
            return;
        }

        // 4. Check verification status
        if ($attendee->verification_status !== VerificationStatus::Verified) {
            $this->recordScanResult('denied', 'Attendee pre-event verification is pending.', $attendee);
            return;
        }

        // 5. Check duplicate check-in
        $alreadyCheckedIn = CheckIn::where('attendee_id', $attendee->id)
            ->where('event_id', $this->event->id)
            ->where('scan_result', ScanResult::Granted)
            ->exists();

        if ($alreadyCheckedIn) {
            $this->recordScanResult('warning', 'Already checked in at this event.', $attendee);
            return;
        }

        // 6. Grant access
        $checkIn = CheckIn::create([
            'uuid' => (string) Str::uuid(),
            'attendee_id' => $attendee->id,
            'event_id' => $this->event->id,
            'gate_id' => $this->gate->id,
            'qr_code_id' => $qrCode->id,
            'scanned_by' => Auth::id(),
            'scan_result' => ScanResult::Granted,
            'scanned_at' => now(),
        ]);

        $qrCode->markScanned();

        $this->recordScanResult('granted', 'Access granted! Welcome, ' . $attendee->full_name, $attendee);
    }

    public function manualCheckIn($attendeeUuid)
    {
        $attendee = Attendee::where('uuid', $attendeeUuid)->firstOrFail();

        $alreadyCheckedIn = CheckIn::where('attendee_id', $attendee->id)
            ->where('event_id', $this->event->id)
            ->where('scan_result', ScanResult::Granted)
            ->exists();

        if ($alreadyCheckedIn) {
            $this->recordScanResult('warning', 'Attendee already checked in.', $attendee);
            return;
        }

        CheckIn::create([
            'uuid' => (string) Str::uuid(),
            'attendee_id' => $attendee->id,
            'event_id' => $this->event->id,
            'gate_id' => $this->gate->id,
            'scanned_by' => Auth::id(),
            'scan_result' => ScanResult::Granted,
            'scanned_at' => now(),
        ]);

        $this->recordScanResult('granted', 'Manual check-in granted for ' . $attendee->full_name, $attendee);
        $this->manualSearchQuery = '';
    }

    private function recordScanResult($result, $message, $attendee = null)
    {
        $this->scanResult = $result;
        $this->scanMessage = $message;
        $this->scannedAttendee = $attendee;

        $this->loadRecentScans();
        $this->updateStats();
    }

    public function clearResult()
    {
        $this->scanResult = null;
        $this->scanMessage = '';
        $this->scannedAttendee = null;
    }

    public function loadRecentScans()
    {
        $this->recentScans = CheckIn::with(['attendee', 'gate'])
            ->where('event_id', $this->event->id)
            ->latest('scanned_at')
            ->take(10)
            ->get();
    }

    public function updateStats()
    {
        $this->stats['total'] = CheckIn::where('event_id', $this->event->id)->count();
        $this->stats['granted'] = CheckIn::where('event_id', $this->event->id)->where('scan_result', ScanResult::Granted)->count();
        $this->stats['denied'] = CheckIn::where('event_id', $this->event->id)->where('scan_result', '!=', ScanResult::Granted)->count();
    }

    public function getSearchResultsProperty()
    {
        if (strlen($this->manualSearchQuery) < 2) {
            return collect();
        }

        return Attendee::where('event_id', $this->event->id)
            ->where(function($query) {
                $query->where('full_name', 'like', '%' . $this->manualSearchQuery . '%')
                      ->orWhere('email', 'like', '%' . $this->manualSearchQuery . '%');
            })
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.scanner.scanner-dashboard', [
            'searchResults' => $this->searchResults,
            'recentScans' => $this->recentScans ?? collect(),
            'event' => $this->event,
            'gate' => $this->gate,
            'stats' => $this->stats,
            'scanResult' => $this->scanResult,
            'manualSearchQuery' => $this->manualSearchQuery ?? '',
            'selectedTab' => $this->selectedTab ?? 'scan',
            'manualReason' => $this->manualReason ?? '',
        ]);
    }
}
