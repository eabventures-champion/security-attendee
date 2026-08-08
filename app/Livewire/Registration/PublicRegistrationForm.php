<?php

namespace App\Livewire\Registration;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Event;
use App\Models\Attendee;
use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Enums\AccessRole;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventRegistrationConfirmation;

#[Layout('layouts.guest')]
#[Title('Event Registration')]
class PublicRegistrationForm extends Component
{
    public $event;
    public string $qrToken = '';
    public bool $isSuccess = false;
    
    // Form fields
    public $full_name = '';
    public $email = '';
    public $phone = '';
    public $company = '';
    public $job_title = '';
    public $country = '';
    public $gender = '';
    public $emergency_contact_name = '';
    public $emergency_contact_phone = '';
    public $dietary_preferences = '';
    public $accessibility_needs = '';
    public $registration_reason = '';
    public array $custom_answers = [];
    public bool $consent = false;

    // Recognition properties
    public bool $isRecognized = false;
    public string $recognizedName = '';
    public string $recognizedOrganization = '';
    public string $recognizedPastEvent = '';

    public function mount($event_slug = null, $eventSlug = null)
    {
        $slug = $event_slug ?: $eventSlug;

        if ($slug) {
            $this->event = Event::where('slug', $slug)->where('status', EventStatus::Published)->first();
            if (!$this->event) {
                $this->event = Event::where(function($q) use ($slug) {
                    $q->where('uuid', $slug)->orWhere('id', $slug);
                })->where('status', EventStatus::Published)->first();
            }
        }

        if (!$this->event) {
            abort(404, 'Event not found or not currently active.');
        }
    }

    public function rules()
    {
        $eventId = $this->event ? $this->event->id : null;
        $config = $this->event ? $this->event->form_fields_config : Event::defaultFormFieldsConfig();
        $stdConfig = $config['standard_fields'];
        $customConfig = $config['custom_fields'];

        $rules = [];

        // full_name rule
        $fnState = $stdConfig['full_name'] ?? 'required';
        if ($fnState === 'required') {
            $rules['full_name'] = 'required|string|min:2|max:255';
        } else {
            $rules['full_name'] = 'nullable|string|max:255';
        }

        // email rule
        $emailState = $stdConfig['email'] ?? 'required';
        if ($emailState === 'required') {
            $rules['email'] = [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'max:255',
                Rule::unique('attendees', 'email')->where(fn ($query) => $query->where('event_id', $eventId))
            ];
        } else {
            $rules['email'] = [
                'nullable',
                'email',
                'max:255',
                Rule::unique('attendees', 'email')->where(fn ($query) => $query->where('event_id', $eventId)->whereNotNull('email'))
            ];
        }

        // phone rule
        $phoneState = $stdConfig['phone'] ?? 'required';
        if ($phoneState === 'required') {
            $rules['phone'] = [
                'required',
                'string',
                'regex:/^[0-9]{10}$/',
                Rule::unique('attendees', 'phone')->where(fn ($query) => $query->where('event_id', $eventId)->whereNotNull('phone')->where('phone', '!=', ''))
            ];
        } elseif ($phoneState === 'optional') {
            $rules['phone'] = [
                'nullable',
                'string',
                'regex:/^[0-9]{10}$/',
            ];
        }

        // Other standard fields
        $otherStandard = ['company', 'job_title', 'country', 'gender', 'emergency_contact_name', 'emergency_contact_phone', 'dietary_preferences', 'accessibility_needs', 'registration_reason'];
        foreach ($otherStandard as $fieldKey) {
            $state = $stdConfig[$fieldKey] ?? 'disabled';
            if ($state === 'required') {
                $rules[$fieldKey] = 'required|string|max:255';
            } elseif ($state === 'optional') {
                $rules[$fieldKey] = 'nullable|string|max:255';
            }
        }

        $rules['consent'] = 'accepted';

        // Custom extra fields validation
        foreach ($customConfig as $cField) {
            $cId = $cField['id'] ?? null;
            if ($cId) {
                $ruleKey = "custom_answers.{$cId}";
                if (!empty($cField['required'])) {
                    $rules[$ruleKey] = 'required';
                } else {
                    $rules[$ruleKey] = 'nullable';
                }
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.regex' => 'Please enter a valid email address with a domain extension (e.g. .com, .org).',
            'email.unique' => 'This email address is already registered for this event.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number must be exactly 10 digits (e.g. 0240303609).',
            'phone.unique' => 'This phone number is already registered for this event.',
            'consent.accepted' => 'You must accept the terms & conditions to register.'
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        if ($propertyName === 'email') {
            $this->checkExistingAttendee();
        }
    }

    public function checkExistingAttendee()
    {
        if (!empty($this->email) && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $this->email) && $this->event) {
            // Check if attendee registered for any event under the SAME organization
            $pastAttendee = Attendee::with('event')
                ->where('organization_id', $this->event->organization_id)
                ->where('email', $this->email)
                ->latest()
                ->first();

            if ($pastAttendee) {
                $this->isRecognized = true;
                $this->recognizedName = $pastAttendee->full_name;
                $this->recognizedOrganization = $this->event->organization->name ?? 'this organization';
                $this->recognizedPastEvent = $pastAttendee->event->name ?? 'a previous event';

                // Auto-prefill empty fields from past registration
                if (empty($this->full_name)) $this->full_name = $pastAttendee->full_name;
                if (empty($this->phone) && $pastAttendee->phone) $this->phone = $pastAttendee->phone;
                if (empty($this->company) && $pastAttendee->company) $this->company = $pastAttendee->company;
                if (empty($this->job_title) && $pastAttendee->job_title) $this->job_title = $pastAttendee->job_title;
            } else {
                $this->isRecognized = false;
            }
        } else {
            $this->isRecognized = false;
        }
    }

    public function restoreDraftValues($name = '', $email = '', $phone = '', $company = '', $jobTitle = '')
    {
        if (empty($this->full_name) && !empty($name)) {
            $this->full_name = $name;
        }
        if (empty($this->email) && !empty($email)) {
            $this->email = $email;
            $this->checkExistingAttendee();
        }
        if (empty($this->phone) && !empty($phone)) {
            $this->phone = $phone;
        }
        if (empty($this->company) && !empty($company)) {
            $this->company = $company;
        }
        if (empty($this->job_title) && !empty($jobTitle)) {
            $this->job_title = $jobTitle;
        }
    }

    public function register()
    {
        $this->validate();

        // Check capacity
        if ($this->event->capacity && $this->event->attendees()->count() >= $this->event->capacity) {
            session()->flash('error', 'Sorry, this event is at full capacity.');
            return;
        }

        $fullNameValue = trim((string)$this->full_name) ?: 'Guest Attendee';
        $emailValue = trim((string)$this->email) ?: ('guest_' . Str::random(8) . '@attendee.local');

        $attendee = Attendee::create([
            'uuid' => (string) Str::uuid(),
            'event_id' => $this->event->id,
            'organization_id' => $this->event->organization_id,
            'full_name' => $fullNameValue,
            'email' => $emailValue,
            'phone' => $this->phone ?: null,
            'company' => $this->company ?: null,
            'job_title' => $this->job_title ?: null,
            'country' => $this->country ?: null,
            'gender' => $this->gender ?: null,
            'emergency_contact_name' => $this->emergency_contact_name ?: null,
            'emergency_contact_phone' => $this->emergency_contact_phone ?: null,
            'dietary_preferences' => $this->dietary_preferences ?: null,
            'accessibility_needs' => $this->accessibility_needs ?: null,
            'registration_reason' => $this->registration_reason ?: null,
            'access_role' => AccessRole::GeneralAdmission,
            'verification_status' => VerificationStatus::Pending,
            'consent' => $this->consent,
            'metadata' => [
                'custom_fields' => $this->custom_answers,
            ],
        ]);

        // Keep qrToken empty so attendee sees Pending Verification screen
        $this->qrToken = '';

        // Send In-App Admin Notification
        try {
            \App\Services\AdminNotificationService::send(
                $this->event->organization_id,
                'New Event Registration',
                "{$attendee->full_name} registered for '{$this->event->name}'.",
                'registration',
                route('attendees.index', $this->event->uuid)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send admin notification: ' . $e->getMessage());
        }

        $this->isSuccess = true;
        
        $this->dispatch('registration-successful');
    }

    public function render()
    {
        return view('livewire.registration.public-registration-form');
    }
}
