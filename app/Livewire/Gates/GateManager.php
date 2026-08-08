<?php

namespace App\Livewire\Gates;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Event;
use App\Models\Gate;
use App\Enums\AccessRole;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
#[Title('Gate Management')]
class GateManager extends Component
{
    public $eventUuid;
    public $event;
    public $gates = [];
    
    public $showModal = false;
    public $isEditing = false;
    
    // Form fields
    public $gateId;
    public $name = '';
    public $location = '';
    public $is_active = true;
    public $allowed_roles = [];

    public $allEvents = [];
    public $availableRoles = [];

    public function mount($eventUuid = null)
    {
        $this->eventUuid = $eventUuid;
        if ($eventUuid) {
            $this->event = Event::where('uuid', $eventUuid)->first();
        }

        // Build the user's allowed events list first (security users only see assigned events)
        $assignedEventIds = auth()->user()->getAssignedEventIds();
        $eventsQuery = Event::orderBy('name');
        if ($assignedEventIds !== null) {
            $eventsQuery->whereIn('id', $assignedEventIds);
        }
        $this->allEvents = $eventsQuery->get();

        // If no valid event was found from the URL, default to the user's first allowed event
        if (!$this->event) {
            if ($this->allEvents->isNotEmpty()) {
                // Use the first event the user is allowed to access
                $this->event = $this->allEvents->first();
            } else {
                // Fallback for admins with no event filter
                $this->event = Event::latest()->first();
            }

            if ($this->event) {
                $this->eventUuid = $this->event->uuid;
            }
        }

        // Verify security user has access to the selected event
        if ($this->event && $assignedEventIds !== null && !in_array($this->event->id, $assignedEventIds)) {
            // User doesn't have access to this event, redirect to their first assigned event
            $this->event = $this->allEvents->first();
            if ($this->event) {
                $this->eventUuid = $this->event->uuid;
            }
        }

        $this->availableRoles = array_map(function($role) {
            return $role->value;
        }, AccessRole::cases());
        
        if ($this->event) {
            $this->loadGates();

            // Auto-seed primary default gate for this specific event if none exists
            if ($this->gates->isEmpty()) {
                $this->createDefaultGate();
            }
        }
    }

    public function switchEvent($uuid)
    {
        return redirect()->route('gates.index', ['eventUuid' => $uuid]);
    }

    public function createDefaultGate()
    {
        Gate::create([
            'uuid' => (string) Str::uuid(),
            'event_id' => $this->event->id,
            'name' => 'Main Entrance Gate',
            'location' => $this->event->venue_name ?: 'Main Entry Checkpoint',
            'is_active' => true,
            'allowed_roles' => json_encode([]),
        ]);
        $this->loadGates();
    }

    public function loadGates()
    {
        $query = Gate::with(['assignedSecurityUsers', 'assignedSecurityAttendees'])->where('event_id', $this->event->id);

        if (auth()->check() && auth()->user()->isSecurityPersonnel()) {
            $assignedGate = auth()->user()->assignedGateForEvent($this->event->id);
            if ($assignedGate) {
                $query->where('id', $assignedGate->id);
            }
        }

        $this->gates = $query->get();
    }

    private function checkPermission(): bool
    {
        $user = auth()->user();
        if ($user && $user->isSecurityPersonnel()) {
            session()->flash('error', 'Security personnel do not have permission to create, edit, or delete gates.');
            return false;
        }
        return true;
    }

    public function createGate()
    {
        if (!$this->checkPermission()) return;
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function editGate($uuid)
    {
        if (!$this->checkPermission()) return;
        $this->resetForm();
        $gate = Gate::where('uuid', $uuid)->orWhere('id', $uuid)->firstOrFail();
        $this->gateId = $gate->id;
        $this->name = $gate->name;
        $this->location = $gate->location ?? '';
        $this->is_active = (bool) $gate->is_active;
        $this->allowed_roles = is_array($gate->allowed_roles) ? $gate->allowed_roles : (json_decode($gate->allowed_roles, true) ?? []);
        
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function toggleAllRoles()
    {
        $allRoles = ['general_admission', 'vip', 'vvip', 'speaker', 'exhibitor', 'sponsor', 'staff', 'volunteer', 'media', 'organizer', 'security'];
        if (count($this->allowed_roles) >= count($allRoles)) {
            $this->allowed_roles = [];
        } else {
            $this->allowed_roles = $allRoles;
        }
    }

    public function saveGate()
    {
        if (!$this->checkPermission()) return;
        $this->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'allowed_roles' => 'array'
        ]);

        $data = [
            'event_id' => $this->event->id,
            'name' => $this->name,
            'location' => $this->location,
            'is_active' => $this->is_active,
            'allowed_roles' => json_encode($this->allowed_roles),
        ];

        if ($this->isEditing && $this->gateId) {
            Gate::where('id', $this->gateId)->update($data);
            session()->flash('success', 'Gate updated successfully.');
        } else {
            $data['uuid'] = (string) Str::uuid();
            Gate::create($data);
            session()->flash('success', 'Gate created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
        $this->loadGates();
    }

    public function deleteGate($uuid)
    {
        if (!$this->checkPermission()) return;
        Gate::where('uuid', $uuid)->orWhere('id', $uuid)->delete();
        session()->flash('success', 'Gate deleted successfully.');
        $this->loadGates();
    }

    public function resetForm()
    {
        $this->gateId = null;
        $this->name = '';
        $this->location = '';
        $this->is_active = true;
        $this->allowed_roles = [];
    }

    public function render()
    {
        return view('livewire.gates.gate-manager', [
            'event' => $this->event,
            'gates' => $this->gates,
            'allEvents' => $this->allEvents,
            'showModal' => $this->showModal ?? false,
            'isEditing' => $this->isEditing ?? false,
            'allowed_roles' => $this->allowed_roles ?? [],
        ]);
    }
}
