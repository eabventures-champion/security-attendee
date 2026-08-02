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
use App\Enums\ScanResult;
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
                'availableGates' => collect(),
                'totalScans' => 0,
                'grantedScans' => 0,
                'deniedScans' => 0,
            ]);
        }

        $query = CheckIn::with(['attendee', 'gate'])->where('event_id', $this->event->id);

        if ($this->gateFilter) {
            $query->where('gate_id', $this->gateFilter);
        }

        if ($this->resultFilter) {
            $query->where('scan_result', $this->resultFilter);
        }

        if ($this->search) {
            $query->whereHas('attendee', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $scanLogs = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

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
            'availableGates' => $gates,
            'gates' => $gates,
            'totalScans' => $totalScans,
            'grantedScans' => $grantedScans,
            'deniedScans' => $deniedScans,
            'search' => $this->search ?? '',
            'gateFilter' => $this->gateFilter ?? '',
            'statusFilter' => $this->statusFilter ?? '',
            'perPage' => $this->perPage ?? 15,
            'isSuperAdmin' => $isSuperAdmin,
            'organizationsTree' => $organizationsTree,
            'allEvents' => $allEvents,
            'expandedOrgs' => $this->expandedOrgs ?? [],
        ]);
    }
}
