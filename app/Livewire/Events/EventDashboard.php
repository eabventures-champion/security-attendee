<?php

namespace App\Livewire\Events;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\CheckIn;
use App\Enums\EventStatus;

#[Layout('layouts.app')]
#[Title('Event Dashboard')]
class EventDashboard extends Component
{
    public $eventUuid;
    public $event;
    
    public function mount($uuid)
    {
        $this->eventUuid = $uuid;
        $this->loadEvent();
    }

    public function loadEvent()
    {
        $this->event = Event::where('uuid', $this->eventUuid)->firstOrFail();
    }

    public function publishEvent()
    {
        $this->event->status = EventStatus::Published;
        $this->event->published_at = now();
        $this->event->save();
        session()->flash('success', 'Event published successfully.');
    }

    public function cancelEvent()
    {
        $this->event->status = EventStatus::Cancelled;
        $this->event->save();
        session()->flash('success', 'Event cancelled.');
    }

    public function archiveEvent()
    {
        $this->event->status = EventStatus::Archived;
        $this->event->save();
        session()->flash('success', 'Event archived successfully.');
    }

    public function unarchiveEvent()
    {
        $this->event->status = EventStatus::Draft;
        $this->event->save();
        session()->flash('success', 'Event unarchived and moved to Draft status.');
    }

    public function deleteEvent()
    {
        $this->event->delete();
        session()->flash('success', 'Event deleted successfully.');
        return redirect()->route('events.index');
    }

    public string $generatedTokenLink = '';
    public string $generatedTokenType = '';

    public function generateSingleUseToken($role = 'vvip', $noDetails = false)
    {
        $token = 'inv_' . \Illuminate\Support\Str::random(16);

        \App\Models\EventInvitation::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'event_id' => $this->event->id,
            'token' => $token,
            'access_role' => $role,
            'no_details' => (bool) $noDetails,
            'max_uses' => 1,
            'use_count' => 0,
            'created_by' => auth()->id(),
        ]);

        $params = [
            'event_slug' => $this->event->slug,
            'token' => $token
        ];
        if ($noDetails) {
            $params['no_details'] = 1;
        }

        $this->generatedTokenLink = route('events.public.invite', $params);
        $this->generatedTokenType = $noDetails ? 'NO DETAILS (Direct Pass)' : 'GET DETAILS (Interactive Form)';

        session()->flash('success', ($noDetails ? 'Direct Pass (NO DETAILS)' : 'Interactive Form (GET DETAILS)') . ' 1-time invitation link generated!');
    }

    public function render()
    {
        return view('livewire.events.event-dashboard');
    }
}
