<?php

namespace App\Livewire\Events;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Event;
use App\Enums\EventStatus;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
#[Title('Event Form')]
class EventForm extends Component
{
    use WithFileUploads;

    public $eventUuid = null;
    public bool $isRestoredDraft = false;
    
    // Step 1: Basic Info
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $invitation_title = 'PRIVATE VVIP INVITATION';
    public string $invitation_description = 'You have received an exclusive private VVIP invitation directly from the event organizers. Confirming your attendance grants you VVIP access privileges and a pre-verified digital pass.';
    public string $title_font = 'Alex Brush';
    public $cover_image = null;
    public string $existing_cover_image_path = '';

    // Step 2: Schedule
    public bool $is_multi_day = false;
    public string $starts_at = '';
    public string $ends_at = '';

    // Step 3: Venue
    public string $venue_name = '';
    public string $venue_address = '';
    public string $venue_city = '';
    public string $venue_country = 'Ghana';

    // Step 4: Settings
    public $capacity = null;
    public string $registration_deadline = '';
    public int $is_free = 1;
    public bool $is_private = false;
    public string $status = 'draft';
    public $organization_id = null;

    public int $currentStep = 1;
    public int $totalSteps = 4;

    public function mount($uuid = null)
    {
        if (auth()->check() && auth()->user()->invitation_status !== 'confirmed') {
            session()->flash('error', "⚠️ Action Locked: Please confirm receipt of your workspace invitation via your email inbox (" . auth()->user()->email . ") before creating or editing events.");
            return redirect()->route('events.index');
        }

        if (request()->has('fresh')) {
            session()->forget('active_event_draft_uuid');
            session()->forget('active_event_draft_step');
        }

        $targetUuid = $uuid ?? session('active_event_draft_uuid');

        if ($targetUuid) {
            $event = Event::where('uuid', $targetUuid)->first();
            if ($event) {
                $this->eventUuid = $event->uuid;
                $this->name = $event->name ?? '';
                $this->slug = $event->slug ?? '';
                $this->description = $event->description ?? '';
                $this->invitation_title = !empty($event->invitation_title) ? $event->invitation_title : 'PRIVATE VVIP INVITATION';
                $this->invitation_description = !empty($event->invitation_description) ? $event->invitation_description : 'You have received an exclusive private VVIP invitation directly from the event organizers. Confirming your attendance grants you VVIP access privileges and a pre-verified digital pass.';
                $this->title_font = $event->title_font ?? 'Alex Brush';
                $this->existing_cover_image_path = $event->cover_image_path ?? '';
                $this->is_multi_day = (bool) ($event->is_multi_day ?? false);
                $this->starts_at = $event->starts_at ? $event->starts_at->format('Y-m-d\TH:i') : '';
                $this->ends_at = $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '';
                $this->venue_name = $event->venue_name ?? '';
                $this->venue_address = $event->venue_address ?? '';
                $this->venue_city = $event->venue_city ?? '';
                $this->venue_country = $event->venue_country ?: 'Ghana';
                $this->capacity = $event->capacity;
                $this->registration_deadline = $event->registration_deadline ? $event->registration_deadline->format('Y-m-d\TH:i') : '';
                $this->is_free = $event->is_free ? 1 : 0;
                $this->is_private = (bool) ($event->is_private ?? false);
                $this->organization_id = $event->organization_id;
                $this->status = $event->status instanceof EventStatus ? $event->status->value : ($event->status ?? 'draft');

                if (!$uuid && session('active_event_draft_step')) {
                    $this->currentStep = min($this->totalSteps, max(1, (int) session('active_event_draft_step')));
                    $this->isRestoredDraft = true;
                }
            }
        }
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|min:3|max:255',
            'cover_image' => 'nullable|image|max:3072',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'required|string',
        ];

        if ($this->status === EventStatus::Published->value || $this->status === 'published') {
            $rules['starts_at'] = 'required|date';
            $rules['ends_at'] = 'nullable|date|after_or_equal:starts_at';
        }

        return $rules;
    }

    public function saveDraftStep(): void
    {
        if (empty(trim($this->name))) {
            return;
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug ?: Str::slug($this->name) . '-' . rand(1000, 9999),
            'description' => $this->description ?: null,
            'invitation_title' => $this->invitation_title ?: null,
            'invitation_description' => $this->invitation_description ?: null,
            'title_font' => $this->title_font ?: 'Alex Brush',
            'is_multi_day' => $this->is_multi_day,
            'starts_at' => $this->starts_at ?: null,
            'ends_at' => $this->ends_at ?: null,
            'venue_name' => $this->venue_name ?: null,
            'venue_address' => $this->venue_address ?: null,
            'venue_city' => $this->venue_city ?: null,
            'venue_country' => $this->venue_country ?: 'Ghana',
            'capacity' => $this->capacity ?: null,
            'registration_deadline' => $this->registration_deadline ?: null,
            'is_free' => (bool) $this->is_free,
            'is_private' => (bool) $this->is_private,
            'status' => $this->status ?: 'draft',
        ];

        if ($this->cover_image) {
            $path = $this->cover_image->store('events/covers', 'public');
            $data['cover_image_path'] = $path;
            $this->existing_cover_image_path = $path;
        }

        if ($this->eventUuid) {
            Event::where('uuid', $this->eventUuid)->update($data);
        } else {
            $data['uuid'] = (string) Str::uuid();
            $data['organization_id'] = auth()->user()->organization_id ?? session('current_organization_id');
            $event = Event::create($data);
            $this->eventUuid = $event->uuid;
        }

        session(['active_event_draft_uuid' => $this->eventUuid]);
        session(['active_event_draft_step' => $this->currentStep]);
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validateOnly('name');
            if ($this->cover_image) {
                $this->validateOnly('cover_image');
            }
        } elseif ($this->currentStep === 2) {
            if ($this->starts_at) {
                $this->validateOnly('starts_at');
            }
            if ($this->ends_at) {
                $this->validateOnly('ends_at');
            }
        }

        // Auto-save draft progress to DB
        $this->saveDraftStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            session(['active_event_draft_step' => $this->currentStep]);
        }
    }

    public function previousStep(): void
    {
        $this->saveDraftStep();

        if ($this->currentStep > 1) {
            $this->currentStep--;
            session(['active_event_draft_step' => $this->currentStep]);
        }
    }

    public function setStep(int $step): void
    {
        $this->saveDraftStep();

        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            session(['active_event_draft_step' => $this->currentStep]);
        }
    }

    public function startFresh(): mixed
    {
        session()->forget('active_event_draft_uuid');
        session()->forget('active_event_draft_step');
        return redirect()->to(route('events.create') . '?fresh=1');
    }

    public function save(): mixed
    {
        if (auth()->check() && auth()->user()->invitation_status !== 'confirmed') {
            session()->flash('error', "⚠️ Action Locked: Please confirm receipt of your workspace invitation via your email inbox (" . auth()->user()->email . ") before creating or saving events.");
            return null;
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug ?: Str::slug($this->name) . '-' . rand(1000, 9999),
            'description' => $this->description ?: null,
            'invitation_title' => $this->invitation_title ?: null,
            'invitation_description' => $this->invitation_description ?: null,
            'title_font' => $this->title_font ?: 'Alex Brush',
            'is_multi_day' => $this->is_multi_day,
            'starts_at' => $this->starts_at ?: null,
            'ends_at' => $this->ends_at ?: null,
            'venue_name' => $this->venue_name ?: null,
            'venue_address' => $this->venue_address ?: null,
            'venue_city' => $this->venue_city ?: null,
            'venue_country' => $this->venue_country ?: 'Ghana',
            'capacity' => $this->capacity ?: null,
            'registration_deadline' => $this->registration_deadline ?: null,
            'is_free' => (bool) $this->is_free,
            'is_private' => (bool) $this->is_private,
            'status' => $this->status,
        ];

        if ($this->cover_image) {
            $path = $this->cover_image->store('events/covers', 'public');
            $data['cover_image_path'] = $path;
        }

        if ($this->status === EventStatus::Published->value) {
            $data['published_at'] = now();
        }

        if ($this->eventUuid) {
            Event::where('uuid', $this->eventUuid)->update($data);
            session()->flash('success', 'Event saved successfully.');
        } else {
            $data['uuid'] = (string) Str::uuid();
            $data['organization_id'] = auth()->user()->organization_id ?? session('current_organization_id');
            $newEvent = Event::create($data);

            // Auto-seed primary gate for this new event
            \App\Models\Gate::create([
                'uuid' => (string) Str::uuid(),
                'event_id' => $newEvent->id,
                'name' => 'Main Entrance Gate',
                'location' => $newEvent->venue_name ?: 'Main Entry Checkpoint',
                'is_active' => true,
                'allowed_roles' => json_encode([]),
            ]);

            session()->flash('success', 'Event created successfully.');
        }

        session()->forget('active_event_draft_uuid');
        session()->forget('active_event_draft_step');

        return redirect()->route('events.index');
    }

    public function getIsSuperAdminProperty(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->email === 'superadmin@attendflow.com');
    }

    public function getHasFullFontAccessProperty(): bool
    {
        if ($this->isSuperAdmin) {
            return true;
        }

        $user = auth()->user();
        $org = $user ? \App\Models\Organization::find($user->organization_id) : null;
        return $org ? (bool) $org->has_premium_typography : false;
    }

    public function requestFontSubscription($fontName = 'PRO Font Pack'): void
    {
        $user = auth()->user();
        if ($user) {
            $org = \App\Models\Organization::find($user->organization_id);
            $orgName = $org->name ?? 'Organization Workspace';

            // Check if already submitted a pending request to avoid duplicates
            $existing = \App\Models\SystemFeedback::where('user_id', $user->id)
                ->where('type', 'request')
                ->where('subject', 'like', '%Typography%')
                ->where('status', 'pending')
                ->first();

            if (!$existing) {
                \App\Models\SystemFeedback::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'organization_id' => $user->organization_id,
                    'user_id' => $user->id,
                    'type' => 'request',
                    'subject' => "Premium Typography Pack Subscription Request: {$fontName}",
                    'message' => "Workspace '{$orgName}' (Admin: {$user->name}, Email: {$user->email}) has requested a subscription to unlock the Premium Typography Pack ({$fontName}) for their events.",
                    'status' => 'pending',
                ]);

                // Send in-app notification to Super Admin
                \App\Services\AdminNotificationService::sendSuperAdmin(
                    "🎨 Typography Pack Subscription Request",
                    "Workspace '{$orgName}' requested a subscription to unlock the Premium Typography Pack ({$fontName}).",
                    'warning',
                    route('users.index')
                );
            }
        }

        session()->flash('subscription_request', "✨ Your request for Premium Typography Pack ({$fontName}) has been sent to Super Admin! Super Admin can activate your workspace subscription under Team & Roles or Resource Center.");
    }

    public function render()
    {
        return view('livewire.events.event-form');
    }
}
