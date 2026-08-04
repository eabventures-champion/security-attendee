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
    public int $pricingWaitlistCount = 0;
    public string $waitlistSearch = '';
    public string $waitlistStatusFilter = 'all';

    public bool $showBreakdownModal = false;
    public string $breakdownMetric = '';
    public string $breakdownTitle = '';
    public array $breakdownData = [];
    public bool $showWelcomeGuide = false;

    public bool $showSendVipEmailModal = false;
    public ?int $targetWaitlistId = null;
    public string $emailTargetAddress = '';
    public string $emailSubject = '🎉 Your AttendFlow VIP Early Access & 50% Off Promo Code!';
    public string $emailPromoCode = 'ATTENDFLOW50VIP';
    public string $emailCustomMessage = '';

    public function toggleWelcomeGuide(): void
    {
        $this->showWelcomeGuide = !$this->showWelcomeGuide;
        session(['show_workspace_guide' => $this->showWelcomeGuide]);
    }

    public function closeWelcomeGuide(): void
    {
        $this->showWelcomeGuide = false;
        session(['show_workspace_guide' => false]);
    }

    public function openBreakdown(string $metric): void
    {
        $user = auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;

        $this->breakdownMetric = $metric;
        $this->breakdownData = [];

        if ($isSuperAdmin) {
            // Super Admin: Breakdown by Organization Admin (excluding Super Admin personal/master workspace)
            $organizations = \App\Models\Organization::with(['users.roles'])->get();

            foreach ($organizations as $org) {
                $orgAdmin = $org->users->firstWhere(fn($u) => $u->hasRole('organization_admin'));

                // Skip workspaces without an Organization Admin or owned by Super Admin
                if (!$orgAdmin || $orgAdmin->hasRole('super_admin') || $orgAdmin->email === 'superadmin@attendflow.com') {
                    continue;
                }

                $val = 0;

                if ($metric === 'events') {
                    $this->breakdownTitle = 'Total Events Breakdown by Organization Admin';
                    $val = Event::where('organization_id', $org->id)->count();
                } elseif ($metric === 'registrations') {
                    $this->breakdownTitle = 'Total Registrations Breakdown by Organization Admin';
                    $val = Attendee::whereHas('event')->where('organization_id', $org->id)->count();
                } elseif ($metric === 'verified') {
                    $this->breakdownTitle = 'Verified Attendees Breakdown by Organization Admin';
                    $val = Attendee::whereHas('event')->where('organization_id', $org->id)
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
                    $val = Attendee::whereHas('event')->where('event_id', $event->id)->count();
                } elseif ($metric === 'registrations') {
                    $this->breakdownTitle = 'Total Registrations Breakdown by Event';
                    $val = Attendee::whereHas('event')->where('event_id', $event->id)->count();
                } elseif ($metric === 'verified') {
                    $this->breakdownTitle = 'Verified Attendees Breakdown by Event';
                    $val = Attendee::whereHas('event')->where('event_id', $event->id)
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
        $this->showWelcomeGuide = session('show_workspace_guide', false);
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $user = auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;

        $eventQuery = Event::query();
        $attendeeQuery = Attendee::whereHas('event');
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

        $this->pricingWaitlistCount = \App\Models\PricingWaitlist::count();
    }

    #[Computed]
    public function pricingWaitlistSubscribers()
    {
        $user = auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;

        if (!$isSuperAdmin) {
            return collect();
        }

        $query = \App\Models\PricingWaitlist::query();

        if (!empty($this->waitlistSearch)) {
            $query->where('email', 'like', '%' . trim($this->waitlistSearch) . '%');
        }

        if ($this->waitlistStatusFilter !== 'all') {
            $query->where('status', $this->waitlistStatusFilter);
        }

        return $query->latest()->take(50)->get();
    }

    public function updateWaitlistStatus(int $id, string $status): void
    {
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && $user->email !== 'superadmin@attendflow.com') {
            return;
        }

        $entry = \App\Models\PricingWaitlist::find($id);
        if ($entry) {
            $entry->update(['status' => $status]);
            session()->flash('stats_refreshed', 'Waitlist status updated successfully!');
        }
    }

    public function deleteWaitlistEntry(int $id): void
    {
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && $user->email !== 'superadmin@attendflow.com') {
            return;
        }

        $entry = \App\Models\PricingWaitlist::find($id);
        if ($entry) {
            $entry->delete();
            $this->loadStats();
            session()->flash('stats_refreshed', 'Waitlist entry deleted.');
        }
    }

    public function exportWaitlistCsv()
    {
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && $user->email !== 'superadmin@attendflow.com') {
            return null;
        }

        $filename = 'vip-pricing-waitlist-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Email', 'IP Address', 'Status', 'Submitted At']);

            \App\Models\PricingWaitlist::latest()->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->email,
                        $row->ip_address ?? 'N/A',
                        ucfirst($row->status),
                        $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : 'N/A',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function openSendVipEmailModal(int $id): void
    {
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && $user->email !== 'superadmin@attendflow.com') {
            return;
        }

        $entry = \App\Models\PricingWaitlist::find($id);
        if ($entry) {
            $this->targetWaitlistId = $entry->id;
            $this->emailTargetAddress = $entry->email;
            $this->emailSubject = '🎉 Your AttendFlow VIP Early Access & 50% Off Promo Code!';
            $this->emailPromoCode = 'ATTENDFLOW50VIP';
            $this->emailCustomMessage = 'Thank you for joining the AttendFlow VIP Early Access waitlist! We are excited to announce that our premium event attendance management platform and subscription packages are now officially open.';
            $this->showSendVipEmailModal = true;
        }
    }

    public function closeSendVipEmailModal(): void
    {
        $this->showSendVipEmailModal = false;
        $this->targetWaitlistId = null;
        $this->emailTargetAddress = '';
    }

    public function sendVipEmail(): void
    {
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && $user->email !== 'superadmin@attendflow.com') {
            return;
        }

        if (!$this->emailTargetAddress) {
            return;
        }

        try {
            \Illuminate\Support\Facades\Mail::to($this->emailTargetAddress)->send(
                new \App\Mail\VipWaitlistDiscountNotification(
                    subscriberEmail: $this->emailTargetAddress,
                    promoCode: $this->emailPromoCode ?: 'ATTENDFLOW50VIP',
                    customMessage: $this->emailCustomMessage,
                    subjectText: $this->emailSubject
                )
            );

            if ($this->targetWaitlistId) {
                $entry = \App\Models\PricingWaitlist::find($this->targetWaitlistId);
                if ($entry) {
                    $entry->update(['status' => 'notified']);
                }
            }

            $this->closeSendVipEmailModal();
            $this->loadStats();
            session()->flash('stats_refreshed', '🎉 VIP Early Access email & 50% promo code sent to ' . $this->emailTargetAddress . '!');
        } catch (\Throwable $e) {
            session()->flash('stats_refreshed', '❌ Error sending email: ' . $e->getMessage());
        }
    }

    #[Computed]
    public function upcomingEvents()
    {
        $user = auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;

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
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;

        $labels = collect(range(0, 14))->map(fn ($day) => now()->subDays(14 - $day)->format('M d'));
        $data = collect(range(0, 14))->map(function ($day) use ($user, $isSuperAdmin) {
            $date = now()->subDays(14 - $day)->toDateString();
            $q = Attendee::whereHas('event')->whereDate('created_at', $date);
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
        $this->loadStats();

        return view('livewire.dashboard.admin-dashboard', [
            'totalEvents' => $this->totalEvents,
            'totalRegistrations' => $this->totalRegistrations,
            'verifiedAttendees' => $this->verifiedAttendees,
            'pendingVerifications' => $this->pendingVerifications,
            'rejectedCount' => $this->rejectedCount,
            'checkedInToday' => $this->checkedInToday,
            'attendancePercentage' => $this->attendancePercentage,
            'pricingWaitlistCount' => $this->pricingWaitlistCount,
            'pricingWaitlistSubscribers' => $this->pricingWaitlistSubscribers,
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
