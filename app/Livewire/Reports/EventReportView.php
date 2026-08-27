<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Event;
use App\Models\CheckIn;
use App\Models\Attendee;
use App\Models\Gate;
use App\Models\QrCode;
use App\Models\NotificationLog;
use App\Enums\ScanResult;
use App\Enums\VerificationStatus;
use App\Enums\NotificationChannel;
use App\Enums\NotificationType;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
#[Title('Reports & Analytics')]
class EventReportView extends Component
{
    use WithPagination;

    public $eventUuid;
    public $event;
    public $gateFilter = '';
    public $resultFilter = '';
    public $search = '';
    public $perPage = 10;
    public array $expandedOrgs = [];

    // Notification / Bulk Pass delivery report filters
    public string $activeReportTab = 'scans'; // 'scans' or 'notifications'
    public string $notificationChannel = '';
    public string $notificationStatus = '';
    public string $notificationSearch = '';

    public function mount($eventUuid = null)
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
    }

    public function switchEvent(string $uuid): void
    {
        $this->eventUuid = $uuid;
        $this->event = Event::where('uuid', $uuid)->first();
        $this->resetPage();
    }

    public function toggleExpandOrg(int $orgId): void
    {
        if (in_array($orgId, $this->expandedOrgs)) {
            $this->expandedOrgs = array_values(array_filter($this->expandedOrgs, fn($id) => $id !== $orgId));
        } else {
            $this->expandedOrgs[] = $orgId;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingGateFilter()
    {
        $this->resetPage();
    }

    public function updatingResultFilter()
    {
        $this->resetPage();
    }

    public function updatingNotificationSearch()
    {
        $this->resetPage('notifsPage');
    }

    public function updatingNotificationChannel()
    {
        $this->resetPage('notifsPage');
    }

    public function updatingNotificationStatus()
    {
        $this->resetPage('notifsPage');
    }

    /**
     * Reset Option 1: Reset Delivery Logs Only (Leaves attendee QR codes and status intact)
     */
    public function clearDeliveryLogsOnly(): void
    {
        if (!$this->event) return;

        NotificationLog::where('event_id', $this->event->id)->delete();
        $this->resetPage('notifsPage');
        session()->flash('success', "🧹 Delivery logs cleared successfully for {$this->event->name}. Attendee QR codes and verification statuses remain unchanged.");
    }

    /**
     * Reset Option 2: Full Reset: Clear Delivery Logs AND Reset Attendee QR Status / Verification for fresh testing
     */
    public function fullResetLogsAndAttendeeStatus(): void
    {
        if (!$this->event) return;

        // 1. Clear notification logs
        NotificationLog::where('event_id', $this->event->id)->delete();

        // 2. Delete QR Codes for this event
        QrCode::where('event_id', $this->event->id)->delete();

        // 3. Reset attendee verification statuses to Pending
        Attendee::where('event_id', $this->event->id)->update([
            'verification_status' => VerificationStatus::Pending,
            'verified_at' => null,
        ]);

        $this->resetPage('notifsPage');
        session()->flash('success', "🔄 Full Reset Complete for {$this->event->name}: Delivery logs cleared, and all attendees reset to Pending with QR codes cleared for fresh re-testing.");
    }

    public function exportCsv(string $type): StreamedResponse
    {
        $fileName = "event-{$type}-report-" . date('Y-m-d') . ".csv";

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'gate') {
                fputcsv($handle, ['ID', 'Attendee Name', 'Email', 'Gate Name', 'Result', 'Scanned At', 'IP Address']);
                $scans = CheckIn::with(['attendee', 'gate'])->where('event_id', $this->event->id)->latest()->get();
                foreach ($scans as $scan) {
                    fputcsv($handle, [
                        $scan->uuid,
                        $scan->attendee->full_name ?? 'N/A',
                        $scan->attendee->email ?? 'N/A',
                        $scan->gate->name ?? 'Default Gate',
                        is_object($scan->scan_result) ? $scan->scan_result->value : $scan->scan_result,
                        $scan->scanned_at ? $scan->scanned_at->format('Y-m-d H:i:s') : $scan->created_at->format('Y-m-d H:i:s'),
                        $scan->ip_address ?? '127.0.0.1'
                    ]);
                }
            } elseif ($type === 'verification') {
                fputcsv($handle, ['UUID', 'Full Name', 'Email', 'Phone', 'Company', 'Verification Status', 'Verified At']);
                $attendees = Attendee::where('event_id', $this->event->id)->get();
                foreach ($attendees as $att) {
                    fputcsv($handle, [
                        $att->uuid,
                        $att->full_name,
                        $att->email,
                        $att->phone ?? '',
                        $att->company ?? '',
                        is_object($att->verification_status) ? $att->verification_status->value : $att->verification_status,
                        $att->verified_at ? $att->verified_at->format('Y-m-d H:i:s') : 'Unverified'
                    ]);
                }
            } elseif ($type === 'email_delivery' || $type === 'notifications') {
                fputcsv($handle, ['Timestamp / Time Issued', 'Event Name', 'Attendee Name', 'Recipient Email', 'Channel', 'Delivery Status', 'Triggered By / Admin', 'Error Details']);
                $logs = NotificationLog::with(['attendee', 'user', 'event'])
                    ->where('event_id', $this->event->id)
                    ->latest()
                    ->get();
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A',
                        $log->event->name ?? $this->event->name,
                        $log->attendee->full_name ?? 'N/A',
                        $log->attendee->email ?? ($log->metadata['recipient_email'] ?? 'N/A'),
                        is_object($log->channel) ? $log->channel->value : $log->channel,
                        $log->status,
                        $log->user->name ?? 'Admin',
                        $log->error_message ?? ''
                    ]);
                }
            } else {
                // Attendance Summary
                fputcsv($handle, ['Event Name', 'Total Registrations', 'Verified Attendees', 'Total Check-ins', 'Checked In %']);
                $totalReg = $this->event->attendees()->count();
                $verified = $this->event->attendees()->where('verification_status', \App\Enums\VerificationStatus::Verified)->count();
                $checkedIn = $this->event->checkIns()->where('scan_result', ScanResult::Granted)->count();
                $percent = $totalReg > 0 ? round(($checkedIn / $totalReg) * 100, 1) . '%' : '0%';
                
                fputcsv($handle, [$this->event->name, $totalReg, $verified, $checkedIn, $percent]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    public function render()
    {
        if (!$this->event) {
            return view('livewire.reports.event-report-view', [
                'scanLogs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'notificationLogs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
                'availableGates' => collect(),
                'totalScans' => 0,
                'grantedScans' => 0,
                'deniedScans' => 0,
                'totalNotifications' => 0,
                'emailSuccessCount' => 0,
                'emailFailedCount' => 0,
                'whatsappCount' => 0,
                'deliveryRate' => 0,
            ]);
        }

        // Gate Scan Logs Query
        $scanQuery = CheckIn::with(['attendee', 'gate'])->where('event_id', $this->event->id);

        if ($this->gateFilter) {
            $scanQuery->where('gate_id', $this->gateFilter);
        }

        if ($this->resultFilter) {
            $scanQuery->where('scan_result', $this->resultFilter);
        }

        if ($this->search) {
            $scanQuery->whereHas('attendee', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $scanLogs = $scanQuery->orderBy('created_at', 'desc')->paginate($this->perPage, ['*'], 'scansPage');

        // Notification / Pass Delivery Logs Query
        $notifQuery = NotificationLog::with(['attendee', 'user', 'event'])->where('event_id', $this->event->id);

        if ($this->notificationChannel) {
            $notifQuery->where('channel', $this->notificationChannel);
        }

        if ($this->notificationStatus) {
            if ($this->notificationStatus === 'delivered') {
                $notifQuery->whereIn('status', ['delivered', 'sent']);
            } else {
                $notifQuery->where('status', $this->notificationStatus);
            }
        }

        if ($this->notificationSearch) {
            $searchTerm = trim($this->notificationSearch);
            $notifQuery->where(function ($q) use ($searchTerm) {
                $q->whereHas('attendee', function ($aq) use ($searchTerm) {
                    $aq->where('full_name', 'like', '%' . $searchTerm . '%')
                       ->orWhere('email', 'like', '%' . $searchTerm . '%')
                       ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                       ->orWhere('company', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('user', function ($uq) use ($searchTerm) {
                    $uq->where('name', 'like', '%' . $searchTerm . '%')
                       ->orWhere('email', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('error_message', 'like', '%' . $searchTerm . '%')
                ->orWhere('metadata->recipient_email', 'like', '%' . $searchTerm . '%');
            });
        }

        $notificationLogs = $notifQuery->orderBy('created_at', 'desc')->paginate($this->perPage, ['*'], 'notifsPage');

        // Automatically clamp out-of-bounds page numbers (e.g. if URL has ?notifsPage=5 but there are only 2 pages)
        if ($notificationLogs->currentPage() > $notificationLogs->lastPage() && $notificationLogs->lastPage() > 0) {
            $this->setPage(1, 'notifsPage');
            $notificationLogs = $notifQuery->orderBy('created_at', 'desc')->paginate($this->perPage, ['*'], 'notifsPage');
        }

        // Notification Metrics for this event
        $totalAttendeesCount = $this->event->attendees()->count();

        // Distinct attendees who have successfully received their email pass
        $deliveredAttendeeIds = NotificationLog::where('event_id', $this->event->id)
            ->where('channel', NotificationChannel::Email)
            ->whereIn('status', ['delivered', 'sent'])
            ->pluck('attendee_id')
            ->filter()
            ->unique()
            ->values();

        $emailSuccessCount = $deliveredAttendeeIds->count();

        // Distinct attendees whose delivery failed and have NOT yet succeeded
        $failedAttendeeIds = NotificationLog::where('event_id', $this->event->id)
            ->where('channel', NotificationChannel::Email)
            ->where('status', 'failed')
            ->whereNotIn('attendee_id', $deliveredAttendeeIds)
            ->pluck('attendee_id')
            ->filter()
            ->unique()
            ->values();

        $emailFailedCount = $failedAttendeeIds->count();
        $totalNotifications = NotificationLog::where('event_id', $this->event->id)->count();

        // Specific count breakdown for filter dropdowns
        $deliveredLogCount = NotificationLog::where('event_id', $this->event->id)
            ->whereIn('status', ['delivered', 'sent'])
            ->count();

        $failedLogCount = NotificationLog::where('event_id', $this->event->id)
            ->where('status', 'failed')
            ->count();

        $emailChannelCount = NotificationLog::where('event_id', $this->event->id)
            ->where('channel', NotificationChannel::Email)
            ->count();

        $whatsappChannelCount = NotificationLog::where('event_id', $this->event->id)
            ->where('channel', NotificationChannel::WhatsApp)
            ->count();

        $whatsappCount = NotificationLog::where('event_id', $this->event->id)
            ->where('channel', NotificationChannel::WhatsApp)
            ->whereIn('status', ['delivered', 'sent'])
            ->pluck('attendee_id')
            ->filter()
            ->unique()
            ->count();

        $deliveryRate = $totalAttendeesCount > 0 ? round(($emailSuccessCount / $totalAttendeesCount) * 100) : 0;

        $gates = Gate::where('event_id', $this->event->id)->get();

        $totalScans = CheckIn::where('event_id', $this->event->id)->count();
        $grantedScans = CheckIn::where('event_id', $this->event->id)->where('scan_result', ScanResult::Granted)->count();
        $deniedScans = CheckIn::where('event_id', $this->event->id)->where('scan_result', '!=', ScanResult::Granted)->count();

        $isSuperAdmin = auth()->user()->hasRole('super_admin') || auth()->user()->email === 'superadmin@attendflow.com';

        $organizationsTree = \App\Models\Organization::with(['users' => function($q) {
            $q->whereHas('roles', fn($r) => $r->where('name', 'organization_admin'));
        }, 'events' => function($q) {
            $q->withCount([
                'attendees as total_registrations_count',
                'checkIns as total_scans_count',
                'gates as gates_count'
            ]);
        }])->get();

        $allEvents = Event::with('organization')->latest()->get();

        return view('livewire.reports.event-report-view', [
            'event' => $this->event,
            'scanLogs' => $scanLogs,
            'notificationLogs' => $notificationLogs,
            'availableGates' => $gates,
            'gates' => $gates,
            'totalScans' => $totalScans,
            'grantedScans' => $grantedScans,
            'deniedScans' => $deniedScans,
            'totalAttendeesCount' => $totalAttendeesCount,
            'totalNotifications' => $totalNotifications,
            'emailSuccessCount' => $emailSuccessCount,
            'emailFailedCount' => $emailFailedCount,
            'deliveredLogCount' => $deliveredLogCount,
            'failedLogCount' => $failedLogCount,
            'emailChannelCount' => $emailChannelCount,
            'whatsappChannelCount' => $whatsappChannelCount,
            'whatsappCount' => $whatsappCount,
            'deliveryRate' => $deliveryRate,
            'search' => $this->search ?? '',
            'gateFilter' => $this->gateFilter ?? '',
            'resultFilter' => $this->resultFilter ?? '',
            'notificationChannel' => $this->notificationChannel ?? '',
            'notificationStatus' => $this->notificationStatus ?? '',
            'notificationSearch' => $this->notificationSearch ?? '',
            'activeReportTab' => $this->activeReportTab ?? 'scans',
            'perPage' => $this->perPage ?? 15,
            'isSuperAdmin' => $isSuperAdmin,
            'organizationsTree' => $organizationsTree,
            'allEvents' => $allEvents,
            'expandedOrgs' => $this->expandedOrgs ?? [],
        ]);
    }
}
