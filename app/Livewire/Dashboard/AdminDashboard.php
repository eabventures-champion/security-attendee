<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\CheckIn;
use App\Models\AuditLog;
use App\Enums\VerificationStatus;
use App\Enums\ScanResult;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class AdminDashboard extends Component
{
    public int $totalEvents = 0;
    public int $totalRegistrations = 0;
    public int $verifiedAttendees = 0;
    public int $pendingVerifications = 0;
    public int $rejectedCount = 0;
    public int $checkedInToday = 0;
    public float $attendancePercentage = 0;

    public bool $showBreakdownModal = false;
    public string $breakdownMetric = '';
    public string $breakdownTitle = '';
    public array $breakdownData = [];
    public bool $showWelcomeGuide = true;

    public function toggleWelcomeGuide(): void
    {
        $this->showWelcomeGuide = !$this->showWelcomeGuide;
        session(['hide_workspace_guide' => !$this->showWelcomeGuide]);
    }

    public function closeWelcomeGuide(): void
    {
        $this->showWelcomeGuide = false;
        session(['hide_workspace_guide' => true]);
    }

    public function openBreakdown(string $metric): void
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin') || $user->email === 'superadmin@attendflow.com';

        $this->breakdownMetric = $metric;
        $this->breakdownData = [];

        if ($isSuperAdmin) {
            // Super Admin: Breakdown by Organization Admin
            $organizations = \App\Models\Organization::with(['users'])->get();

            foreach ($organizations as $org) {
                $orgAdmin = $org->users->firstWhere(fn($u) => $u->hasRole('organization_admin')) ?? $org->users->first();
                $val = 0;

                if ($metric === 'events') {
                    $this->breakdownTitle = 'Total Events Breakdown by Organization Admin';
                    $val = Event::where('organization_id', $org->id)->count();
                } elseif ($metric === 'registrations') {
                    $this->breakdownTitle = 'Total Registrations Breakdown by Organization Admin';
                    $val = Attendee::where('organization_id', $org->id)->count();
                } elseif ($metric === 'verified') {
                    $this->breakdownTitle = 'Verified Attendees Breakdown by Organization Admin';
                    $val = Attendee::where('organization_id', $org->id)
                        ->where('verification_status', VerificationStatus::Verified)
                        ->count();
                } elseif ($metric === 'checked_in') {
                    $this->breakdownTitle = 'Checked In Today Breakdown by Organization Admin';
                    $val = CheckIn::whereHas('event', fn($q) => $q->where('organization_id', $org->id))
                        ->where('scan_result', ScanResult::Granted)
                        ->whereDate('scanned_at', Carbon::today())
                        ->count();
                }

                $this->breakdownData[] = [
                    'org_id' => $org->id,
                    'org_name' => $org->name,
                    'admin_name' => $orgAdmin->name ?? 'Unassigned Admin',
                    'admin_email' => $orgAdmin->email ?? 'N/A',
                    'count' => $val,
                ];
            }
        } else {
            // Organization Admin: Breakdown by Event within Organization
            $orgId = $user->organization_id ?? 1;
            $events = Event::where('organization_id', $orgId)->get();

            foreach ($events as $event) {
                $val = 0;
                if ($metric === 'events') {
                    $this->breakdownTitle = 'Workspace Events Overview';
                    $val = Attendee::where('event_id', $event->id)->count();
                } elseif ($metric === 'registrations') {
                    $this->breakdownTitle = 'Total Registrations Breakdown by Event';
                    $val = Attendee::where('event_id', $event->id)->count();
                } elseif ($metric === 'verified') {
                    $this->breakdownTitle = 'Verified Attendees Breakdown by Event';
                    $val = Attendee::where('event_id', $event->id)
                        ->where('verification_status', VerificationStatus::Verified)
                        ->count();
                } elseif ($metric === 'checked_in') {
                    $this->breakdownTitle = 'Checked-In Today Breakdown by Event';
                    $val = CheckIn::where('event_id', $event->id)
                        ->where('scan_result', ScanResult::Granted)
                        ->whereDate('scanned_at', Carbon::today())
                        ->count();
                }

                $this->breakdownData[] = [
                    'org_id' => $event->id,
                    'org_name' => $event->name,
                    'admin_name' => $event->venue_name ?: 'Venue Not Specified',
                    'admin_email' => $event->starts_at ? $event->starts_at->format('M j, Y g:i A') : 'Date TBD',
                    'count' => $val,
                ];
            }
        }

        usort($this->breakdownData, fn($a, $b) => $b['count'] <=> $a['count']);
        $this->showBreakdownModal = true;
    }

    public function closeBreakdown(): void
    {
        $this->showBreakdownModal = false;
        $this->breakdownData = [];
    }

    public function mount(): void
    {
        $this->showWelcomeGuide = !session('hide_workspace_guide', false);
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin') || $user->email === 'superadmin@attendflow.com';

        $eventQuery = Event::query();
        $attendeeQuery = Attendee::query();
        $checkInQuery = CheckIn::where('scan_result', ScanResult::Granted)->whereDate('scanned_at', Carbon::today());

        if (!$isSuperAdmin && $user->organization_id) {
            $eventQuery->where('organization_id', $user->organization_id);
            $attendeeQuery->where('organization_id', $user->organization_id);
            $checkInQuery->whereHas('event', fn($q) => $q->where('organization_id', $user->organization_id));
        }

        $this->totalEvents = $eventQuery->count();
        $this->totalRegistrations = $attendeeQuery->count();
        $this->verifiedAttendees = (clone $attendeeQuery)->where('verification_status', VerificationStatus::Verified)->count();
        $this->pendingVerifications = (clone $attendeeQuery)->where('verification_status', VerificationStatus::Pending)->count();
        $this->rejectedCount = (clone $attendeeQuery)->where('verification_status', VerificationStatus::Rejected)->count();
        $this->checkedInToday = $checkInQuery->count();

        $this->attendancePercentage = $this->totalRegistrations > 0
            ? round(($this->verifiedAttendees / $this->totalRegistrations) * 100, 1)
            : 0;
    }

    #[Computed]
    public function upcomingEvents()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin') || $user->email === 'superadmin@attendflow.com';

        $query = Event::query();
        if (!$isSuperAdmin && $user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }

        $upcoming = (clone $query)->where('starts_at', '>=', now())->orderBy('starts_at', 'asc')->take(5)->get();

        if ($upcoming->isEmpty()) {
            return $query->latest()->take(5)->get();
        }

        return $upcoming;
    }

    public function getRegistrationChartData(): array
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin') || $user->email === 'superadmin@attendflow.com';

        $labels = collect(range(0, 14))->map(fn ($day) => now()->subDays(14 - $day)->format('M d'));
        $data = collect(range(0, 14))->map(function ($day) use ($user, $isSuperAdmin) {
            $date = now()->subDays(14 - $day)->toDateString();
            $q = Attendee::whereDate('created_at', $date);
            if (!$isSuperAdmin && $user->organization_id) {
                $q->where('organization_id', $user->organization_id);
            }
            return $q->count();
        });

        return [
            'labels' => $labels->toArray(),
            'data' => $data->toArray(),
            'max' => max($data->max(), 5),
        ];
    }

    public function refreshStats(): void
    {
        $this->loadStats();
        session()->flash('stats_refreshed', 'Dashboard stats refreshed successfully!');
        $this->dispatch('stats-updated');
    }

    public function render()
    {
        return view('livewire.dashboard.admin-dashboard', [
            'totalEvents' => $this->totalEvents,
            'totalRegistrations' => $this->totalRegistrations,
            'verifiedAttendees' => $this->verifiedAttendees,
            'pendingVerifications' => $this->pendingVerifications,
            'rejectedCount' => $this->rejectedCount,
            'checkedInToday' => $this->checkedInToday,
            'attendancePercentage' => $this->attendancePercentage,
            'showBreakdownModal' => $this->showBreakdownModal,
            'breakdownMetric' => $this->breakdownMetric,
            'breakdownTitle' => $this->breakdownTitle,
            'breakdownData' => $this->breakdownData,
            'showWelcomeGuide' => $this->showWelcomeGuide,
            'chart' => $this->getRegistrationChartData(),
            'upcomingEvents' => $this->upcomingEvents,
        ]);
    }
}
