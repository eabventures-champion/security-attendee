<?php

namespace App\Livewire\Registration;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\QrCode;
use App\Models\EventInvitation;
use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Enums\AccessRole;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

#[Layout('layouts.guest')]
#[Title('Private Event Invitation — AttendFlow')]
class PrivateInvitationForm extends Component
{
    public $event;
    public bool $isSuccess = false;
    public bool $isVip = false;
    public bool $isNoDetailsMode = false;
    public bool $isClaimed = false;
    public string $qrToken = '';

    // Token enforcement properties
    public ?EventInvitation $invitationTokenObj = null;
    public bool $hasValidToken = false;
    public bool $isTokenConsumed = false;
    public string $tokenNotice = '';

    // Form fields
    public string $full_name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $job_title = '';
    public string $country = '';
    public string $gender = '';
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $dietary_preferences = '';
    public string $accessibility_needs = '';
    public string $registration_reason = '';
    public array $custom_answers = [];
    public bool $consent = false;

    public function mount($event_slug = null, $eventSlug = null)
    {
        $slug = $event_slug ?: $eventSlug;

        if ($slug) {
            $this->event = Event::where('slug', $slug)->first();
            if (!$this->event) {
                $this->event = Event::where('uuid', $slug)->orWhere('id', $slug)->first();
            }
        }

        if (!$this->event) {
            abort(404, 'Event not found.');
        }

        $this->isVip = request()->boolean('vip');
        $noDetailsParam = request()->boolean('no_details');
        $eventDefaultNoDetails = (isset($this->event->settings['default_entry_mode']) && $this->event->settings['default_entry_mode'] === 'no_details');

        if ($eventDefaultNoDetails || $noDetailsParam) {
            $this->isNoDetailsMode = true;
        }

        // Check for secure single-use invitation token
        $tokenString = request()->query('token');
        if ($tokenString) {
            $inv = EventInvitation::where('event_id', $this->event->id)
                ->where('token', $tokenString)
                ->first();

            if ($inv) {
                $this->invitationTokenObj = $inv;
                if ($inv->no_details || $noDetailsParam || $eventDefaultNoDetails) {
                    $this->isNoDetailsMode = true;
                }

                if ($inv->isValid()) {
                    $this->hasValidToken = true;
                    if ($inv->access_role === 'vvip' || $inv->access_role === 'vip') {
                        $this->isVip = true;
                    }
                    if ($inv->email && empty($this->email)) {
                        $this->email = $inv->email;
                    }
                } else {
                    $this->isTokenConsumed = true;
                    if ($inv->no_details || $noDetailsParam || $eventDefaultNoDetails) {
                        $this->tokenNotice = "⛔ Access Denied: This single-use invitation pass has already been claimed and downloaded. Each invitation link is strictly 1-time valid.";
                    } else {
                        $emailLockInfo = $inv->email ? " by {$inv->email}" : "";
                        $this->tokenNotice = "This single-use invitation link has already been redeemed{$emailLockInfo}. Any new email submitted via this link will undergo Administrator Verification (No instant QR pass).";
                    }
                }
            } else {
                $this->isTokenConsumed = true;
                $this->tokenNotice = 'Invalid invitation link token. Access denied.';
            }
        } else {
            // Public invitation (No token required)
            $this->hasValidToken = true;
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
                'max:255'
            ];
        } else {
            $rules['email'] = [
                'nullable',
                'email',
                'max:255'
            ];
        }

        // phone rule
        $phoneState = $stdConfig['phone'] ?? 'required';
        if ($phoneState === 'required') {
            $rules['phone'] = [
                'required',
                'string',
                Rule::unique('attendees', 'phone')->where(fn ($query) => $query->where('event_id', $eventId)->whereNotNull('phone')->where('phone', '!=', ''))
            ];
        } elseif ($phoneState === 'optional') {
            $rules['phone'] = 'nullable|string|max:255';
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

        if ($this->isTokenConsumed && empty($rules['registration_reason'])) {
            $rules['registration_reason'] = 'required|string|min:5|max:1000';
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
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered for this event.',
            'consent.accepted' => 'You must accept the terms and conditions to confirm attendance.',
            'registration_reason.required' => 'Please state your reason for filling this form before proceeding.',
            'registration_reason.min' => 'Please provide a brief reason (at least 5 characters).',
        ];
    }

    // Recognition properties
    public bool $isRecognized = false;
    public string $recognizedName = '';
    public string $recognizedOrganization = '';
    public string $recognizedPastEvent = '';

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

    public function confirmRsvp()
    {
        $this->validate();

        // Under Get Details (Form Entry / Attendance Category), all attendee submissions require Org Admin approval
        $verificationStatus = VerificationStatus::Pending;
        $targetRole = ($this->invitationTokenObj && $this->invitationTokenObj->access_role)
            ? AccessRole::from($this->invitationTokenObj->access_role)
            : ($this->isVip ? AccessRole::Vvip : AccessRole::GeneralAdmission);

        $fullNameValue = trim((string)$this->full_name) ?: 'Guest Attendee';
        $emailValue = trim((string)$this->email) ?: ('guest_' . Str::random(8) . '@attendee.local');

        // Check if attendee already exists for this event
        $existing = Attendee::where('event_id', $this->event->id)
            ->where('email', $emailValue)
            ->first();

        if ($existing) {
            $existingMetadata = is_array($existing->metadata) ? $existing->metadata : [];
            $existingMetadata['custom_fields'] = array_merge(
                $existingMetadata['custom_fields'] ?? [],
                $this->custom_answers
            );

            $existing->update([
                'verification_status' => $verificationStatus,
                'full_name' => $fullNameValue,
                'phone' => $this->phone ?: $existing->phone,
                'company' => $this->company ?: $existing->company,
                'job_title' => $this->job_title ?: $existing->job_title,
                'country' => $this->country ?: $existing->country,
                'gender' => $this->gender ?: $existing->gender,
                'emergency_contact_name' => $this->emergency_contact_name ?: $existing->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone ?: $existing->emergency_contact_phone,
                'dietary_preferences' => $this->dietary_preferences ?: $existing->dietary_preferences,
                'accessibility_needs' => $this->accessibility_needs ?: $existing->accessibility_needs,
                'registration_reason' => $this->registration_reason ?: $existing->registration_reason,
                'access_role' => $targetRole,
                'metadata' => $existingMetadata,
            ]);
            $attendee = $existing;
        } else {
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
                'access_role' => $targetRole,
                'verification_status' => $verificationStatus,
                'consent' => $this->consent,
                'metadata' => [
                    'custom_fields' => $this->custom_answers,
                ],
            ]);
        }

        // If single-use token was supplied, mark it used
        if ($this->invitationTokenObj && $this->hasValidToken) {
            $this->invitationTokenObj->increment('use_count');
            $this->invitationTokenObj->refresh();

            $updateData = ['used_at' => now()];
            if ($this->invitationTokenObj->max_uses == 1) {
                $updateData['email'] = $this->email;
            }
            $this->invitationTokenObj->update($updateData);

            if ($this->invitationTokenObj->use_count >= $this->invitationTokenObj->max_uses) {
                $this->hasValidToken = false;
                $this->isTokenConsumed = true;
            }
        }

        // Leave $qrToken empty so attendee sees Pending Verification screen
        $this->qrToken = '';

        // Send In-App Admin Notification
        try {
            \App\Services\AdminNotificationService::send(
                $this->event->organization_id,
                'Private Invitation RSVP Confirmed',
                "{$attendee->full_name} confirmed attendance for '{$this->event->name}'. Status: {$verificationStatus->value}",
                'registration',
                route('attendees.index', $this->event->uuid)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send admin notification: ' . $e->getMessage());
        }

        $this->isSuccess = true;
    }

    public function claimDirectPass(): void
    {
        if ($this->isTokenConsumed || ($this->invitationTokenObj && !$this->hasValidToken)) {
            session()->flash('error', '⛔ Access Denied: This single-use invitation pass has already been claimed and downloaded.');
            return;
        }

        $targetRole = ($this->invitationTokenObj && $this->invitationTokenObj->access_role)
            ? AccessRole::from($this->invitationTokenObj->access_role)
            : ($this->isVip ? AccessRole::Vvip : AccessRole::GeneralAdmission);

        $guestNumber = rand(1000, 9999);
        $roleLabel = ($targetRole === AccessRole::Vvip || $targetRole === AccessRole::Vip) ? 'VVIP' : 'General';
        $guestName = "{$roleLabel} Guest Pass #{$guestNumber}";
        $guestEmail = 'guest_' . Str::random(8) . '@attendflow.pass';

        // Create Auto-Verified Attendee Record
        $attendee = Attendee::create([
            'uuid' => (string) Str::uuid(),
            'event_id' => $this->event->id,
            'organization_id' => $this->event->organization_id,
            'full_name' => $guestName,
            'email' => $guestEmail,
            'phone' => '000' . rand(1000000, 9999999),
            'access_role' => $targetRole,
            'verification_status' => VerificationStatus::Verified,
            'consent' => true
        ]);

        // Consume Single-Use Token immediately
        if ($this->invitationTokenObj) {
            $this->invitationTokenObj->increment('use_count');
            $this->invitationTokenObj->refresh();

            $updateData = ['used_at' => now()];
            if ($this->invitationTokenObj->max_uses == 1) {
                $updateData['email'] = $guestEmail;
            }
            $this->invitationTokenObj->update($updateData);

            if ($this->invitationTokenObj->use_count >= $this->invitationTokenObj->max_uses) {
                $this->hasValidToken = false;
                $this->isTokenConsumed = true;
            }
        }

        // Generate QrCode for verified attendee
        $token = Str::random(32);
        $qrCode = QrCode::create([
            'uuid' => (string) Str::uuid(),
            'attendee_id' => $attendee->id,
            'event_id' => $this->event->id,
            'secure_token' => $token,
            'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
            'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
            'issued_at' => now(),
            'expires_at' => $this->event->ends_at ? $this->event->ends_at->addDays(1) : now()->addYear(),
            'is_revoked' => false,
        ]);

        $this->qrToken = $qrCode->secure_token;
        $this->isSuccess = true;
        $this->isClaimed = true;

        session()->flash('success', '🎉 Your Digital Pass has been generated! Click below to download your QR Code.');
    }

    public function render()
    {
        return view('livewire.registration.private-invitation-form');
    }
}
