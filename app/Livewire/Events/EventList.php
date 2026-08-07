<?php

namespace App\Livewire\Events;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Event;
use App\Enums\EventStatus;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
#[Title('Events Management')]
class EventList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 12;
    public $viewMode = 'grid'; // grid or table

    public array $expandedOrgs = [];
    public bool $groupedView = true;

    public function toggleExpandOrg(int $orgId): void
    {
        if (in_array($orgId, $this->expandedOrgs)) {
            $this->expandedOrgs = array_diff($this->expandedOrgs, [$orgId]);
        } else {
            $this->expandedOrgs[] = $orgId;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'table' : 'grid';
        $this->groupedView = false;
    }

    public function createEvent(): mixed
    {
        if (auth()->check() && !auth()->user()->isSuperAdmin() && auth()->user()->hasRole('organization_admin') && auth()->user()->invitation_status !== 'confirmed') {
            session()->flash('error', "⚠️ Action Locked: Please confirm receipt of your workspace invitation via your email inbox (" . auth()->user()->email . ") before creating events.");
            return null;
        }

        return redirect()->route('events.create', ['fresh' => 1]);
    }

    public function setGridMode(): void
    {
        $this->viewMode = 'grid';
    }

    public function setTableMode(): void
    {
        $this->viewMode = 'table';
    }

    public function deleteOrganization(int $orgId): void
    {
        $org = \App\Models\Organization::findOrFail($orgId);
        $events = \App\Models\Event::where('organization_id', $org->id)->get();
        foreach ($events as $event) {
            \App\Models\CheckIn::where('event_id', $event->id)->delete();
            \App\Models\Gate::where('event_id', $event->id)->delete();
            \App\Models\QrCode::where('event_id', $event->id)->delete();
            \App\Models\Attendee::where('event_id', $event->id)->delete();
            $event->delete();
        }
        $org->delete();
        session()->flash('success', 'Organization workspace deleted successfully.');
    }

    public function archiveEvent($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $event->status = EventStatus::Archived;
        $event->save();
        session()->flash('success', 'Event archived successfully.');
    }

    public function unarchiveEvent($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $event->status = EventStatus::Draft;
        $event->save();
        session()->flash('success', 'Event unarchived and moved to Draft status.');
    }

    public function deleteEvent($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $event->delete();
        session()->flash('success', 'Event deleted successfully.');
    }

    public function duplicateEvent($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $newDoc = $event->replicate();
        $newDoc->name = $newDoc->name . ' (Copy)';
        $newDoc->status = EventStatus::Draft;
        $newDoc->uuid = (string) Str::uuid();
        $newDoc->save();
        session()->flash('success', 'Event duplicated successfully.');
    }

    public function togglePublish($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $event->status = $event->status === EventStatus::Published ? EventStatus::Draft : EventStatus::Published;
        if ($event->status === EventStatus::Published) {
            $event->published_at = now();
        } else {
            $event->published_at = null;
        }
        $event->save();
        session()->flash('success', 'Event status updated.');
    }

    public function render()
    {
        $user = auth()->user();
        $assignedEventIds = $user->getAssignedEventIds();

        $baseQuery = Event::query();

        // Security personnel: only show events they are assigned to
        if ($assignedEventIds !== null) {
            $baseQuery->whereIn('id', $assignedEventIds);
        }

        $totalCount = (clone $baseQuery)->count();
        $publishedCount = (clone $baseQuery)->where('status', EventStatus::Published)->count();
        $draftCount = (clone $baseQuery)->where('status', EventStatus::Draft)->count();
        $cancelledCount = (clone $baseQuery)->where('status', EventStatus::Cancelled)->count();
        $completedCount = (clone $baseQuery)->where('status', EventStatus::Completed)->count();
        $archivedCount = (clone $baseQuery)->where('status', EventStatus::Archived)->count();

        $events = Event::query()
            ->when($assignedEventIds !== null, function ($query) use ($assignedEventIds) {
                $query->whereIn('id', $assignedEventIds);
            })
            ->withCount([
                'attendees as total_registrations_count',
                'attendees as verified_attendees_count' => function ($q) {
                    $q->where('verification_status', \App\Enums\VerificationStatus::Verified);
                },
                'checkIns as checked_in_count' => function ($q) {
                    $q->where('scan_result', \App\Enums\ScanResult::Granted);
                },
                'gates as gates_count'
            ])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $isSuperAdmin = auth()->user()->hasRole('super_admin') || auth()->user()->email === 'superadmin@attendflow.com';

        $organizationsTree = collect();
        if ($isSuperAdmin) {
            $organizationsTree = \App\Models\Organization::with(['users' => function($q) {
                $q->whereHas('roles', fn($r) => $r->where('name', 'organization_admin'));
            }, 'events' => function($q) {
                $q->withCount([
                    'attendees as total_registrations_count',
                    'attendees as verified_attendees_count' => fn($k) => $k->where('verification_status', \App\Enums\VerificationStatus::Verified),
                    'checkIns as checked_in_count' => fn($k) => $k->where('scan_result', \App\Enums\ScanResult::Granted),
                    'gates as gates_count'
                ]);
                if ($this->search) {
                    $q->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%');
                }
                if ($this->statusFilter) {
                    $q->where('status', $this->statusFilter);
                }
            }])->get();
        }

        return view('livewire.events.event-list', [
            'events' => $events,
            'totalCount' => $totalCount,
            'publishedCount' => $publishedCount,
            'draftCount' => $draftCount,
            'cancelledCount' => $cancelledCount,
            'completedCount' => $completedCount,
            'archivedCount' => $archivedCount,
            'isSuperAdmin' => $isSuperAdmin,
            'organizationsTree' => $organizationsTree,
            'viewMode' => $this->viewMode,
            'groupedView' => $this->groupedView,
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'expandedOrgs' => $this->expandedOrgs,
        ]);
    }
}
