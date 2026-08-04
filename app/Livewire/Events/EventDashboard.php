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
    public string $generatedTokenWhatsappUrl = '';

    public array $batchLinks = [];
    public int $batchQuantity = 5;
    public string $batchCategory = 'no_details';
    public string $batchRole = 'vvip';

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

        // Formulate WhatsApp share message
        $waMessage = "🎉 You're invited to *" . $this->event->name . "*!\n\n" .
                     "📅 Date: " . ($this->event->starts_at ? $this->event->starts_at->format('M j, Y g:i A') : 'TBD') . "\n" .
                     "📍 Venue: " . ($this->event->venue_name ?: 'Main Entry Checkpoint') . "\n\n" .
                     "🎟️ Claim & Download your digital pass here:\n" . $this->generatedTokenLink . "\n\n" .
                     "⚠️ Note: This invitation pass link is strictly 1-time single-use valid.";
        $this->generatedTokenWhatsappUrl = "https://api.whatsapp.com/send?text=" . urlencode($waMessage);

        session()->flash('success', ($noDetails ? 'Direct Pass (NO DETAILS)' : 'Interactive Form (GET DETAILS)') . ' 1-time invitation link generated!');
    }

    public string $batchWhatsappBulkUrl = '';
    public string $batchBulkMessageText = '';

    public function generateBatchTokens()
    {
        $qty = min(max((int)$this->batchQuantity, 1), 50);
        $isNoDetails = $this->batchCategory === 'no_details';
        $this->batchLinks = [];

        $linksListText = "";

        for ($i = 0; $i < $qty; $i++) {
            $token = 'inv_' . \Illuminate\Support\Str::random(16);

            \App\Models\EventInvitation::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'event_id' => $this->event->id,
                'token' => $token,
                'access_role' => $this->batchRole,
                'no_details' => $isNoDetails,
                'max_uses' => 1,
                'use_count' => 0,
                'created_by' => auth()->id(),
            ]);

            $params = [
                'event_slug' => $this->event->slug,
                'token' => $token
            ];
            if ($isNoDetails) {
                $params['no_details'] = 1;
            }

            $link = route('events.public.invite', $params);
            $num = $i + 1;
            $linksListText .= "Pass #{$num}: {$link}\n";

            $waSingleMessage = "🎉 You're invited to *" . $this->event->name . "*!\n\n" .
                               "📅 Date: " . ($this->event->starts_at ? $this->event->starts_at->format('M j, Y g:i A') : 'TBD') . "\n" .
                               "📍 Venue: " . ($this->event->venue_name ?: 'Main Venue') . "\n\n" .
                               "🎟️ Claim & Download your digital pass here:\n" . $link . "\n\n" .
                               "⚠️ Note: This link is strictly 1-time single-use valid.";

            $this->batchLinks[] = [
                'id' => $num,
                'token' => $token,
                'link' => $link,
                'role' => strtoupper($this->batchRole),
                'type' => $isNoDetails ? 'NO DETAILS (Direct Pass)' : 'GET DETAILS (Form)',
                'whatsapp_url' => "https://api.whatsapp.com/send?text=" . urlencode($waSingleMessage),
            ];
        }

        // Formulate Multi-Link Bundle Message for WhatsApp Broadcast / Multi-contact selection
        $roleTitle = strtoupper($this->batchRole) . ($isNoDetails ? ' (Direct Pass)' : ' (RSVP Form)');
        $this->batchBulkMessageText = "🎉 You're invited to *" . $this->event->name . "*!\n" .
                                      "📅 Date: " . ($this->event->starts_at ? $this->event->starts_at->format('M j, Y g:i A') : 'TBD') . "\n" .
                                      "📍 Venue: " . ($this->event->venue_name ?: 'Main Venue') . "\n\n" .
                                      "🎟️ *{$qty} Unique {$roleTitle} Digital Passes:* \n" .
                                      "Each guest must click their OWN designated pass link below:\n\n" .
                                      $linksListText . "\n" .
                                      "⚠️ Each link above is strictly 1-time single-use valid for 1 person.";

        $this->batchWhatsappBulkUrl = "https://api.whatsapp.com/send?text=" . urlencode($this->batchBulkMessageText);

        session()->flash('success', "🚀 Batch of {$qty} unique single-use WhatsApp invitation links generated successfully!");
    }

    public function clearBatchLinks()
    {
        $this->batchLinks = [];
    }

    public function render()
    {
        $recentScans = CheckIn::with(['attendee', 'gate', 'scanner'])
            ->where('event_id', $this->event->id)
            ->latest('scanned_at')
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('livewire.events.event-dashboard', [
            'recentScans' => $recentScans,
        ]);
    }
}
