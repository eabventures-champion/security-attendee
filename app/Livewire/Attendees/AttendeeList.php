<?php

namespace App\Livewire\Attendees;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\QrCode;
use App\Enums\VerificationStatus;
use App\Enums\AccessRole;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\AttendeePrivateInvitation;

#[Layout('layouts.app')]
#[Title('Attendees Management')]
class AttendeeList extends Component
{
    use WithPagination, WithFileUploads;

    public $eventUuid;
    public $search = '';
    public $statusFilter = '';
    public $roleFilter = '';
    public $categoryFilter = '';
    public array $expandedOrgs = [];
    public array $expandedEvents = [];
    public bool $groupedView = true;
    public int $perPage = 10;
    public array $selectedAttendees = [];
    public bool $selectAll = false;
    public bool $selectAllOnPage = false;

    public function toggleExpandOrg(int $orgId): void
    {
        if (in_array($orgId, $this->expandedOrgs)) {
            $this->expandedOrgs = array_diff($this->expandedOrgs, [$orgId]);
        } else {
            $this->expandedOrgs[] = $orgId;
        }
    }

    public function toggleExpandEvent(int $eventId): void
    {
        if (in_array($eventId, $this->expandedEvents)) {
            $this->expandedEvents = array_diff($this->expandedEvents, [$eventId]);
        } else {
            $this->expandedEvents[] = $eventId;
        }
    }

    public bool $showMobileOrgModal = false;
    public ?int $mobileOrgId = null;
    public ?int $mobileSelectedEventId = null;

    public function openMobileOrgModal(int $orgId): void
    {
        $this->mobileOrgId = $orgId;
        $this->mobileSelectedEventId = null;
        $this->showMobileOrgModal = true;
    }

    public function closeMobileOrgModal(): void
    {
        $this->showMobileOrgModal = false;
        $this->mobileOrgId = null;
        $this->mobileSelectedEventId = null;
    }

    public function selectMobileEvent(?int $eventId): void
    {
        if ($this->mobileSelectedEventId === $eventId) {
            $this->mobileSelectedEventId = null;
        } else {
            $this->mobileSelectedEventId = $eventId;
        }
    }

    public bool $showMobileAttendeesModal = false;
    public ?int $mobileEventId = null;

    public function openMobileAttendeesModal(int $eventId): void
    {
        $this->mobileEventId = $eventId;
        $this->showMobileAttendeesModal = true;
    }

    public function closeMobileAttendeesModal(): void
    {
        $this->showMobileAttendeesModal = false;
        $this->mobileEventId = null;
    }

    public bool $showAddModal = false;
    public bool $showDetailsModal = false;
    public bool $showBulkInviteModal = false;
    public $selectedAttendee = null;

    // Form fields for adding attendee
    public $new_event_id = '';
    public $new_full_name = '';
    public $new_email = '';
    public $new_phone = '';
    public $new_access_role = 'general_admission';
    public $new_assigned_gate_id = null;
    public $new_verification_status = 'verified';
    public bool $auto_generate_qr = true;

    // Bulk invite fields
    public string $bulk_emails = '';
    public string $bulk_access_role = 'general_admission';
    public bool $bulk_auto_verify = true;

    // Import CSV fields
    public bool $showImportCsvModal = false;
    public string $import_event_id = '';
    public $csv_file = null;
    public array $importResults = [];
    public array $importEventFields = [];

    // Secure Single-Use Link Generator fields
    public bool $showLinkGeneratorModal = false;
    public string $gen_event_id = ''; // Explicit event selector for secure link
    public string $gen_access_role = 'vvip';
    public string $gen_category = 'details'; // 'details', 'no_details'
    public string $gen_email = '';
    public int $gen_max_uses = 1;
    public string $generated_invite_url = '';
    public string $generated_whatsapp_url = '';
    public array $gen_standard_fields = [];
    public array $gen_custom_fields = [];

    public function updatedGenEventId($value)
    {
        if ($value) {
            $event = Event::find($value);
            if ($event) {
                $this->gen_category = $event->settings['default_entry_mode'] ?? 'details';
                $config = $event->form_fields_config;
                $this->gen_standard_fields = $config['standard_fields'];
                $this->gen_custom_fields = $config['custom_fields'];
            }
        }
    }

    public function openLinkGeneratorModal()
    {
        $this->showLinkGeneratorModal = true;
        $this->generated_invite_url = '';
        $this->generated_whatsapp_url = '';

        // Pre-select event from current filter, or default to first event
        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            $this->gen_event_id = $event ? (string) $event->id : '';
            if ($event) {
                $this->gen_category = $event->settings['default_entry_mode'] ?? 'details';
                $config = $event->form_fields_config;
                $this->gen_standard_fields = $config['standard_fields'];
                $this->gen_custom_fields = $config['custom_fields'];
            }
        } else {
            $firstEvent = Event::first();
            if ($firstEvent) {
                $this->gen_event_id = (string) $firstEvent->id;
                $this->gen_category = $firstEvent->settings['default_entry_mode'] ?? 'details';
                $config = $firstEvent->form_fields_config;
                $this->gen_standard_fields = $config['standard_fields'];
                $this->gen_custom_fields = $config['custom_fields'];
            } else {
                $this->gen_event_id = '';
                $this->gen_category = 'details';
                $defaultConfig = Event::defaultFormFieldsConfig();
                $this->gen_standard_fields = $defaultConfig['standard_fields'];
                $this->gen_custom_fields = [];
            }
        }
    }

    public function closeLinkGeneratorModal()
    {
        $this->showLinkGeneratorModal = false;
        $this->generated_invite_url = '';
        $this->generated_whatsapp_url = '';
        $this->gen_email = '';
        $this->gen_event_id = '';
    }

    public function setGenFieldStatus(string $fieldKey, string $status): void
    {
        $this->gen_standard_fields[$fieldKey] = $status;
        $this->persistGenFormFields();
    }

    public function addGenCustomField(): void
    {
        $this->gen_custom_fields[] = [
            'id' => 'field_' . \Illuminate\Support\Str::random(8),
            'label' => '',
            'type' => 'text',
            'required' => false,
            'options' => '',
        ];
        $this->persistGenFormFields();
    }

    public function removeGenCustomField(int $index): void
    {
        unset($this->gen_custom_fields[$index]);
        $this->gen_custom_fields = array_values($this->gen_custom_fields);
        $this->persistGenFormFields();
    }

    public function persistGenFormFields(): void
    {
        if (!empty($this->gen_event_id)) {
            $event = Event::find($this->gen_event_id);
            if ($event) {
                $currentSettings = is_array($event->settings) ? $event->settings : [];
                $currentSettings['form_fields'] = [
                    'standard_fields' => $this->gen_standard_fields,
                    'custom_fields' => array_values(array_filter($this->gen_custom_fields, fn($f) => !empty(trim($f['label'] ?? '')))),
                ];
                $event->settings = $currentSettings;
                $event->save();
            }
        }
    }

    public function generateSingleUseLink()
    {
        if (empty($this->gen_event_id)) {
            session()->flash('error', 'Please select an event to generate the link for.');
            return;
        }

        $this->persistGenFormFields();

        $event = Event::find($this->gen_event_id);

        if (!$event) {
            session()->flash('error', 'Event not found.');
            return;
        }

        $token = 'inv_' . \Illuminate\Support\Str::random(16);
        $isNoDetails = ($this->gen_category === 'no_details');

        \App\Models\EventInvitation::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'event_id' => $event->id,
            'email' => $this->gen_email ? trim($this->gen_email) : null,
            'token' => $token,
            'access_role' => $this->gen_access_role,
            'no_details' => $isNoDetails,
            'max_uses' => $this->gen_max_uses ?: 1,
            'use_count' => 0,
            'created_by' => auth()->id(),
        ]);

        $params = ['event_slug' => $event->slug, 'token' => $token];
        if ($isNoDetails) {
            $params['no_details'] = 1;
        }

        $this->generated_invite_url = route('events.public.invite', $params);

        $roleLabel = strtoupper(str_replace('_', ' ', $this->gen_access_role));
        $shareMsg = "🎉 You are invited to *" . $event->name . "*!\n\n🎟️ Access Role: " . $roleLabel . "\n\n🔗 Claim your digital entry pass here:\n" . $this->generated_invite_url;
        $this->generated_whatsapp_url = 'https://api.whatsapp.com/send?text=' . urlencode($shareMsg);

        session()->flash('message', 'Secure single-use invitation link generated!');
    }

    public function mount($eventUuid = null)
    {
        $this->eventUuid = $eventUuid;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingEventUuid()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->resetAddForm();

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            if ($event) {
                $this->new_event_id = $event->id;
            }
        } else {
            $firstEvent = Event::first();
            if ($firstEvent) {
                $this->new_event_id = $firstEvent->id;
            }
        }

        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetAddForm();
    }

    public function viewAttendeeDetails($idOrUuid)
    {
        $this->selectedAttendee = Attendee::with(['event', 'qrCode', 'assignedGate'])
            ->where('uuid', $idOrUuid)
            ->orWhere('id', $idOrUuid)
            ->first();

        if ($this->selectedAttendee) {
            $this->showDetailsModal = true;
        }
    }

    public function openDetailsModal($idOrUuid)
    {
        $this->viewAttendeeDetails($idOrUuid);
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedAttendee = null;
    }

    public function openBulkInviteModal()
    {
        $this->bulk_emails = '';
        $this->bulk_access_role = 'general_admission';
        $this->bulk_auto_verify = true;

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            if ($event) {
                $this->new_event_id = $event->id;
            }
        } else {
            $firstEvent = Event::first();
            if ($firstEvent) {
                $this->new_event_id = $firstEvent->id;
            }
        }

        $this->showBulkInviteModal = true;
    }

    public function closeBulkInviteModal()
    {
        $this->showBulkInviteModal = false;
        $this->bulk_emails = '';
    }

    public function sendBulkInvitations()
    {
        $event = Event::find($this->new_event_id);
        if (!$event) {
            session()->flash('error', 'Please select a valid event.');
            return;
        }

        // Parse comma or newline separated email list
        $emailList = array_filter(array_map('trim', preg_split('/[\s,]+/', $this->bulk_emails)));

        if (empty($emailList)) {
            // Bulk resend invitation to all existing attendees of this event who need pass
            $attendees = Attendee::where('event_id', $event->id)->get();
            $sentCount = 0;
            foreach ($attendees as $attendee) {
                // Ensure unique QR pass code exists
                if (!$attendee->qrCode) {
                    QrCode::create([
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'attendee_id' => $attendee->id,
                        'event_id' => $event->id,
                        'secure_token' => \Illuminate\Support\Str::random(32),
                        'issued_at' => now(),
                        'is_revoked' => false,
                    ]);
                    $attendee->load('qrCode');
                }

                try {
                    Mail::to($attendee->email)->send(new AttendeePrivateInvitation($attendee));
                    $sentCount++;
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send invitation to {$attendee->email}: " . $e->getMessage());
                }
            }
            session()->flash('success', "Bulk invitations dispatched to {$sentCount} existing attendees with unique security passes.");
            $this->closeBulkInviteModal();
            return;
        }

        // Bulk invite new email recipients
        $sentCount = 0;
        foreach ($emailList as $email) {
            if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                continue;
            }

            // Check if attendee exists for this event
            $attendee = Attendee::where('event_id', $event->id)->where('email', $email)->first();

            if (!$attendee) {
                $namePart = explode('@', $email)[0];
                $fullName = ucwords(str_replace(['.', '_', '-'], ' ', $namePart));

                $attendee = Attendee::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'event_id' => $event->id,
                    'organization_id' => $event->organization_id,
                    'full_name' => $fullName,
                    'email' => $email,
                    'access_role' => $this->bulk_access_role,
                    'verification_status' => $this->bulk_auto_verify ? VerificationStatus::Verified : VerificationStatus::Pending,
                    'verified_at' => $this->bulk_auto_verify ? now() : null,
                    'consent' => true,
                ]);
            }

            // Generate unique QR Code pass if not existing
            if (!$attendee->qrCode) {
                QrCode::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'attendee_id' => $attendee->id,
                    'event_id' => $event->id,
                    'secure_token' => \Illuminate\Support\Str::random(32),
                    'issued_at' => now(),
                    'is_revoked' => false,
                ]);
                $attendee->load('qrCode');
            }

            try {
                Mail::to($attendee->email)->send(new AttendeePrivateInvitation($attendee));
                $sentCount++;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send bulk invitation to {$email}: " . $e->getMessage());
            }
        }

        session()->flash('success', "Successfully created & sent unique invitations to {$sentCount} attendees.");
        $this->closeBulkInviteModal();
    }

    public function resetAddForm()
    {
        $this->new_full_name = '';
        $this->new_email = '';
        $this->new_phone = '';
        $this->new_access_role = 'general_admission';
        $this->new_verification_status = 'verified';
        $this->auto_generate_qr = true;
        $this->resetErrorBag();
    }

    public function rules()
    {
        return [
            'new_event_id' => 'required|exists:events,id',
            'new_full_name' => 'required|string|min:2|max:255',
            'new_email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'max:255',
                Rule::unique('attendees', 'email')->where(fn ($query) => $query->where('event_id', $this->new_event_id))
            ],
            'new_phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10}$/',
                Rule::unique('attendees', 'phone')->where(fn ($query) => $query->where('event_id', $this->new_event_id)->whereNotNull('phone')->where('phone', '!=', ''))
            ],
            'new_access_role' => 'required|string',
            'new_verification_status' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'new_event_id.required' => 'Please select an event.',
            'new_full_name.required' => 'Full name is required.',
            'new_email.required' => 'Email address is required.',
            'new_email.email' => 'Please enter a valid email address.',
            'new_email.regex' => 'Please enter a valid email address with a domain extension (e.g. .com, .org).',
            'new_email.unique' => 'This email address is already registered for this event.',
            'new_phone.regex' => 'Phone number must be exactly 10 digits (e.g. 0246345698).',
            'new_phone.unique' => 'This phone number is already registered for this event.',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveAttendee()
    {
        $this->validate();

        $attendee = Attendee::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'event_id' => $this->new_event_id,
            'full_name' => $this->new_full_name,
            'email' => $this->new_email,
            'phone' => $this->new_phone ?: null,
            'access_role' => $this->new_access_role,
            'assigned_gate_id' => $this->new_assigned_gate_id ?: null,
            'verification_status' => $this->new_verification_status,
            'verified_at' => $this->new_verification_status === 'verified' ? now() : null,
            'organization_id' => auth()->user()->organization_id ?? session('current_organization_id'),
            'consent' => true,
        ]);

        if ($this->new_access_role === 'security' || (is_object($this->new_access_role) && $this->new_access_role === AccessRole::Security)) {
            $this->ensureSecurityUserAccount($attendee);
        }

        // Auto-generate QR code if verified or explicitly requested
        if ($this->new_verification_status === 'verified' || $this->auto_generate_qr) {
            QrCode::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'attendee_id' => $attendee->id,
                'event_id' => $attendee->event_id,
                'secure_token' => \Illuminate\Support\Str::random(32),
                'issued_at' => now(),
                'is_revoked' => false,
            ]);
        }

        // Send In-App Admin Notification
        try {
            \App\Services\AdminNotificationService::send(
                $attendee->organization_id,
                'New Manual Registration',
                "{$attendee->full_name} was registered manually for '{$attendee->event->name}'.",
                'registration',
                route('attendees.index', $attendee->event->uuid)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send admin notification: ' . $e->getMessage());
        }

        session()->flash('success', "Attendee '{$attendee->full_name}' manually registered successfully.");
        $this->closeAddModal();
    }

    public function verifyAttendee($uuid)
    {
        $attendee = Attendee::with(['event', 'qrCode'])->where('uuid', $uuid)->first();
        if ($attendee) {
            $attendee->verification_status = VerificationStatus::Verified;
            $attendee->verified_at = now();
            $attendee->save();

            // Create QR Code if missing
            if (!$attendee->qrCode) {
                $token = \Illuminate\Support\Str::random(32);
                QrCode::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'attendee_id' => $attendee->id,
                    'event_id' => $attendee->event_id,
                    'secure_token' => $token,
                    'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
                    'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
                    'issued_at' => now(),
                    'expires_at' => ($attendee->event && $attendee->event->ends_at) ? $attendee->event->ends_at->addDays(1) : now()->addYear(),
                    'is_revoked' => false,
                ]);
                $attendee->load('qrCode');
            }

            // Send Confirmation Email with QR Pass upon Org Admin Approval
            try {
                Mail::to($attendee->email)->send(new \App\Mail\EventRegistrationConfirmation($attendee));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send approval confirmation email: ' . $e->getMessage());
            }

            // Auto-Dispatch WhatsApp Pass & Log Delivery Status
            try {
                \App\Services\WhatsAppDispatchService::dispatchQrPass($attendee);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to auto-dispatch WhatsApp pass on approval: ' . $e->getMessage());
            }

            session()->flash('success', "Attendee '{$attendee->full_name}' approved & verified! Official QR pass dispatched.");
        }
    }

    public function bulkApproveAttendees()
    {
        if (empty($this->selectedAttendees)) return;

        $attendees = Attendee::with(['event', 'qrCode'])->whereIn('uuid', $this->selectedAttendees)->get();
        $approvedCount = 0;

        foreach ($attendees as $attendee) {
            $attendee->verification_status = VerificationStatus::Verified;
            $attendee->verified_at = now();
            $attendee->save();

            if (!$attendee->qrCode) {
                $token = \Illuminate\Support\Str::random(32);
                QrCode::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'attendee_id' => $attendee->id,
                    'event_id' => $attendee->event_id,
                    'secure_token' => $token,
                    'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
                    'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
                    'issued_at' => now(),
                    'expires_at' => ($attendee->event && $attendee->event->ends_at) ? $attendee->event->ends_at->addDays(1) : now()->addYear(),
                    'is_revoked' => false,
                ]);
                $attendee->load('qrCode');
            }

            try {
                Mail::to($attendee->email)->send(new \App\Mail\EventRegistrationConfirmation($attendee));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send bulk approval confirmation email: ' . $e->getMessage());
            }

            try {
                \App\Services\WhatsAppDispatchService::dispatchQrPass($attendee);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to auto-dispatch bulk WhatsApp pass: ' . $e->getMessage());
            }

            $approvedCount++;
        }

        $this->selectedAttendees = [];
        $this->selectAllOnPage = false;
        $this->selectAll = false;
        session()->flash('success', "{$approvedCount} attendee(s) approved & verified successfully! Official QR passes emailed to guests.");
    }

    public function resendPassEmail($uuid = null)
    {
        $targetUuid = $uuid ?: ($this->selectedAttendee->uuid ?? null);
        if (!$targetUuid) return;

        $attendee = Attendee::with(['event', 'qrCode'])->where('uuid', $targetUuid)->first();
        if (!$attendee) return;

        // Ensure QR code exists
        if (!$attendee->qrCode) {
            $token = \Illuminate\Support\Str::random(32);
            QrCode::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'attendee_id' => $attendee->id,
                'event_id' => $attendee->event_id,
                'secure_token' => $token,
                'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
                'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
                'issued_at' => now(),
                'expires_at' => $attendee->event->ends_at ? $attendee->event->ends_at->addDays(1) : now()->addYear(),
                'is_revoked' => false,
            ]);
            $attendee->load('qrCode');
        }

        try {
            Mail::to($attendee->email)->send(new AttendeePrivateInvitation($attendee));
            session()->flash('message', "Pass email re-sent successfully to {$attendee->email}!");
            if ($this->selectedAttendee && $this->selectedAttendee->uuid === $targetUuid) {
                $this->selectedAttendee = $attendee;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to resend pass email to {$attendee->email}: " . $e->getMessage());
            session()->flash('error', "Could not send email: " . $e->getMessage());
        }
    }

    public function deleteAttendee($uuid)
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if ($attendee) {
            DB::transaction(function () use ($attendee) {
                \App\Models\CheckIn::where('attendee_id', $attendee->id)->delete();
                \App\Models\QrCode::where('attendee_id', $attendee->id)->delete();
                \App\Models\NotificationLog::where('attendee_id', $attendee->id)->delete();
                $attendee->forceDelete();
            });
            session()->flash('success', 'Attendee permanently deleted from the database.');
        }
    }

    public function toggleAttendeeRole($uuid, $newRole)
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if ($attendee) {
            $attendee->access_role = $newRole;
            $attendee->save();
            session()->flash('success', "Role updated to '" . AccessRole::from($newRole)->label() . "' for {$attendee->full_name}.");
        }
    }

    public function sendWhatsAppPass($uuid = null)
    {
        $targetUuid = $uuid ?: ($this->selectedAttendee->uuid ?? null);
        if (!$targetUuid) return;

        $attendee = Attendee::with(['event', 'qrCode', 'notificationLogs'])->where('uuid', $targetUuid)->first();
        if (!$attendee) return;

        // Ensure QR code exists
        if (!$attendee->qrCode) {
            $token = \Illuminate\Support\Str::random(32);
            QrCode::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'attendee_id' => $attendee->id,
                'event_id' => $attendee->event_id,
                'secure_token' => $token,
                'encrypted_payload' => base64_encode(json_encode(['token' => $token, 'attendee_uuid' => $attendee->uuid])),
                'digital_signature' => hash_hmac('sha256', $token, config('app.key')),
                'issued_at' => now(),
                'expires_at' => ($attendee->event && $attendee->event->ends_at) ? $attendee->event->ends_at->addDays(1) : now()->addYear(),
                'is_revoked' => false,
            ]);
            $attendee->load('qrCode');
        }

        $result = \App\Services\WhatsAppDispatchService::dispatchQrPass($attendee);

        if ($this->selectedAttendee && $this->selectedAttendee->uuid === $targetUuid) {
            $this->selectedAttendee = $attendee->fresh(['event', 'qrCode', 'notificationLogs']);
        }

        if ($result['success']) {
            session()->flash('success', "📱 WhatsApp QR Pass message dispatched for {$attendee->full_name}!");
            $this->js("window.open('{$result['url']}', '_blank')");
        } else {
            session()->flash('error', "⚠️ WhatsApp Dispatch Warning: {$result['message']}");
        }
    }

    public function markWhatsAppFailed(string $uuid, string $reason = 'Number not on WhatsApp'): void
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if (!$attendee) return;

        \App\Models\NotificationLog::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'attendee_id' => $attendee->id,
            'event_id' => $attendee->event_id,
            'user_id' => auth()->id(),
            'channel' => \App\Enums\NotificationChannel::WhatsApp->value,
            'type' => \App\Enums\NotificationType::QrDelivery->value,
            'status' => 'failed',
            'error_message' => $reason ?: 'Number not registered on WhatsApp',
            'metadata' => [
                'recipient_phone' => $attendee->phone,
                'manual_override' => true,
            ],
        ]);

        if ($this->selectedAttendee && $this->selectedAttendee->uuid === $uuid) {
            $this->selectedAttendee = $attendee->fresh(['event', 'qrCode', 'notificationLogs']);
        }

        session()->flash('warning', "Status updated to 'WhatsApp Failed' for {$attendee->full_name}.");
    }

    public function markWhatsAppSent(string $uuid): void
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if (!$attendee) return;

        \App\Models\NotificationLog::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'attendee_id' => $attendee->id,
            'event_id' => $attendee->event_id,
            'user_id' => auth()->id(),
            'channel' => \App\Enums\NotificationChannel::WhatsApp->value,
            'type' => \App\Enums\NotificationType::QrDelivery->value,
            'status' => 'delivered',
            'sent_at' => now(),
            'error_message' => null,
            'metadata' => [
                'recipient_phone' => $attendee->phone,
                'manual_override' => true,
            ],
        ]);

        if ($this->selectedAttendee && $this->selectedAttendee->uuid === $uuid) {
            $this->selectedAttendee = $attendee->fresh(['event', 'qrCode', 'notificationLogs']);
        }

        session()->flash('success', "Status updated to 'WhatsApp Sent' for {$attendee->full_name}.");
    }

    public function getFilteredAttendeesQuery()
    {
        $query = Attendee::whereHas('event')
            ->with(['event', 'qrCode', 'assignedGate', 'latestCheckIn.gate', 'latestCheckIn.scanner', 'notificationLogs']);

        $isSuperAdmin = auth()->user()->hasRole('super_admin') || auth()->user()->email === 'superadmin@attendflow.com';
        if (!$isSuperAdmin && auth()->user()->organization_id) {
            $query->whereHas('event', fn($q) => $q->where('organization_id', auth()->user()->organization_id));
        }

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            if ($event) {
                $query->where('event_id', $event->id);
            }
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('full_name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('verification_status', $this->statusFilter);
        }

        if ($this->roleFilter) {
            $query->where('access_role', $this->roleFilter);
        }

        if ($this->categoryFilter === 'no_details') {
            $query->where(function($q) {
                $q->where('email', 'like', '%@attendflow.pass')
                  ->orWhere('phone', 'like', '000%')
                  ->orWhere('full_name', 'like', '%Guest Pass%');
            });
        } elseif ($this->categoryFilter === 'details') {
            $query->where('email', 'not like', '%@attendflow.pass')
                  ->where('phone', 'not like', '000%')
                  ->where('full_name', 'not like', '%Guest Pass%');
        }

        return $query;
    }

    public function updatedSelectAllOnPage($value)
    {
        if ($value) {
            $pageUuids = $this->getFilteredAttendeesQuery()
                ->latest()
                ->paginate($this->perPage, ['*'], 'page', $this->getPage())
                ->pluck('uuid')
                ->map(fn($uuid) => (string) $uuid)
                ->toArray();

            $this->selectedAttendees = array_values(array_unique(array_merge($this->selectedAttendees, $pageUuids)));
        } else {
            $pageUuids = $this->getFilteredAttendeesQuery()
                ->latest()
                ->paginate($this->perPage, ['*'], 'page', $this->getPage())
                ->pluck('uuid')
                ->map(fn($uuid) => (string) $uuid)
                ->toArray();

            $this->selectedAttendees = array_values(array_diff($this->selectedAttendees, $pageUuids));
        }
        $this->selectAll = $value;
    }

    public function selectAllFilteredAttendees(): void
    {
        $this->selectedAttendees = $this->getFilteredAttendeesQuery()
            ->pluck('uuid')
            ->map(fn($uuid) => (string) $uuid)
            ->toArray();
        $this->selectAll = true;
        $this->selectAllOnPage = true;
    }

    public function bulkDeleteAttendees(): void
    {
        if (empty($this->selectedAttendees)) return;

        $attendees = Attendee::whereIn('uuid', $this->selectedAttendees)->get();
        $count = $attendees->count();
        $attendeeIds = $attendees->pluck('id')->toArray();

        if (!empty($attendeeIds)) {
            DB::transaction(function () use ($attendeeIds) {
                \App\Models\CheckIn::whereIn('attendee_id', $attendeeIds)->delete();
                \App\Models\QrCode::whereIn('attendee_id', $attendeeIds)->delete();
                \App\Models\NotificationLog::whereIn('attendee_id', $attendeeIds)->delete();
                Attendee::whereIn('id', $attendeeIds)->forceDelete();
            });
        }

        $this->selectedAttendees = [];
        $this->selectAllOnPage = false;
        $this->selectAll = false;
        session()->flash('success', "{$count} attendee(s) permanently deleted from the database.");
    }

    public function deleteAllFilteredAttendees(): void
    {
        $attendees = $this->getFilteredAttendeesQuery()->get();
        $count = $attendees->count();
        if ($count === 0) return;

        $attendeeIds = $attendees->pluck('id')->toArray();

        if (!empty($attendeeIds)) {
            DB::transaction(function () use ($attendeeIds) {
                \App\Models\CheckIn::whereIn('attendee_id', $attendeeIds)->delete();
                \App\Models\QrCode::whereIn('attendee_id', $attendeeIds)->delete();
                \App\Models\NotificationLog::whereIn('attendee_id', $attendeeIds)->delete();
                Attendee::whereIn('id', $attendeeIds)->forceDelete();
            });
        }

        $this->selectedAttendees = [];
        $this->selectAllOnPage = false;
        $this->selectAll = false;
        session()->flash('success', "All {$count} attendee(s) in the table have been permanently deleted from the database.");
    }

    public function bulkChangeRole($newRole)
    {
        if (empty($this->selectedAttendees)) return;

        $count = Attendee::whereIn('uuid', $this->selectedAttendees)->update(['access_role' => $newRole]);

        $this->selectedAttendees = [];
        $this->selectAllOnPage = false;
        $this->selectAll = false;
        session()->flash('success', "{$count} attendee(s) updated to '" . AccessRole::from($newRole)->label() . "'.");
    }

    public function revokeQr($uuid)
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if ($attendee) {
            QrCode::where('attendee_id', $attendee->id)->update(['is_revoked' => true]);
            session()->flash('success', 'QR code revoked.');
        }
    }

    public function resendVerification($uuid)
    {
        session()->flash('success', 'Verification email resent.');
    }

    public function export()
    {
        if (!empty($this->selectedAttendees)) {
            $attendees = Attendee::with(['event', 'latestCheckIn', 'qrCode'])
                ->whereIn('uuid', $this->selectedAttendees)
                ->latest()
                ->get();
        } else {
            $attendees = $this->getFilteredAttendeesQuery()
                ->with(['event', 'latestCheckIn', 'qrCode'])
                ->latest()
                ->get();
        }

        if ($attendees->isEmpty()) {
            session()->flash('warning', 'No attendees found to export with the current filters.');
            return null;
        }

        // Collect custom extra fields from events / metadata
        $customFieldHeaders = [];
        foreach ($attendees as $att) {
            if ($att->event && !empty($att->event->form_fields_config['custom_fields'])) {
                foreach ($att->event->form_fields_config['custom_fields'] as $cf) {
                    $cId = $cf['id'] ?? ($cf['label'] ?? '');
                    $cLabel = $cf['label'] ?? $cId;
                    if ($cId && !isset($customFieldHeaders[$cId])) {
                        $customFieldHeaders[$cId] = $cLabel;
                    }
                }
            }
            if (is_array($att->metadata)) {
                foreach ($att->metadata as $mKey => $mVal) {
                    if (!isset($customFieldHeaders[$mKey])) {
                        $customFieldHeaders[$mKey] = Str::headline($mKey);
                    }
                }
            }
        }

        $eventName = 'attendees';
        if ($this->eventUuid) {
            $selectedEvent = Event::where('uuid', $this->eventUuid)->first();
            if ($selectedEvent) {
                $eventName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $selectedEvent->name);
            }
        }

        $fileName = "{$eventName}_export_" . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($attendees, $customFieldHeaders) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $baseHeaders = [
                'Full Name',
                'Email Address',
                'Phone Number',
                'Event Name',
                'Access Role',
                'Verification Status',
                'Check-In Status',
                'Checked-In At',
                'Company / Organization',
                'Job Title',
                'Country',
                'Gender',
                'Emergency Contact Name',
                'Emergency Contact Phone',
                'Dietary Preferences',
                'Accessibility Needs',
                'Registration Reason',
                'Registration Date',
            ];

            $allHeaders = array_merge($baseHeaders, array_values($customFieldHeaders));
            fputcsv($handle, $allHeaders);

            foreach ($attendees as $attendee) {
                $roleLabel = is_object($attendee->access_role) ? $attendee->access_role->label() : ($attendee->access_role ?? 'General Admission');
                $statusLabel = is_object($attendee->verification_status) ? $attendee->verification_status->value : ($attendee->verification_status ?? 'verified');
                $isCheckedIn = $attendee->latestCheckIn && (is_object($attendee->latestCheckIn->scan_result) ? $attendee->latestCheckIn->scan_result->value === 'granted' : $attendee->latestCheckIn->scan_result === 'granted');
                $checkInTime = ($isCheckedIn && $attendee->latestCheckIn->scanned_at) ? $attendee->latestCheckIn->scanned_at->format('Y-m-d H:i:s') : 'N/A';

                $row = [
                    $attendee->full_name,
                    $attendee->email,
                    $attendee->phone ?? '',
                    $attendee->event->name ?? 'N/A',
                    $roleLabel,
                    ucfirst((string) $statusLabel),
                    $isCheckedIn ? 'Checked In' : 'Not Checked In',
                    $checkInTime,
                    $attendee->company ?? '',
                    $attendee->job_title ?? '',
                    $attendee->country ?? '',
                    $attendee->gender ?? '',
                    $attendee->emergency_contact_name ?? '',
                    $attendee->emergency_contact_phone ?? '',
                    $attendee->dietary_preferences ?? '',
                    $attendee->accessibility_needs ?? '',
                    $attendee->registration_reason ?? '',
                    $attendee->created_at ? $attendee->created_at->format('Y-m-d H:i:s') : '',
                ];

                // Append any custom extra fields
                foreach (array_keys($customFieldHeaders) as $cKey) {
                    $val = '';
                    if (is_array($attendee->metadata)) {
                        $val = $attendee->metadata[$cKey] ?? '';
                        if (is_array($val)) {
                            $val = implode(', ', $val);
                        }
                    }
                    $row[] = (string) $val;
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    // ─── CSV Import Methods ─────────────────────────────────────

    private const HEADER_FIELD_MAP = [
        'Full Name' => 'full_name',
        'Email Address' => 'email',
        'Phone Number' => 'phone',
        'Company / Organization' => 'company',
        'Job Title' => 'job_title',
        'Country' => 'country',
        'Gender' => 'gender',
        'Emergency Contact Name' => 'emergency_contact_name',
        'Emergency Contact Phone' => 'emergency_contact_phone',
        'Dietary Preferences' => 'dietary_preferences',
        'Accessibility Needs' => 'accessibility_needs',
        'Registration Reason' => 'registration_reason',
        'Role' => 'access_role',
        'Verification Status' => 'verification_status',
    ];

    private const FIELD_LABEL_MAP = [
        'full_name' => 'Full Name',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'company' => 'Company / Organization',
        'job_title' => 'Job Title',
        'country' => 'Country',
        'gender' => 'Gender',
        'emergency_contact_name' => 'Emergency Contact Name',
        'emergency_contact_phone' => 'Emergency Contact Phone',
        'dietary_preferences' => 'Dietary Preferences',
        'accessibility_needs' => 'Accessibility Needs',
        'registration_reason' => 'Registration Reason',
    ];

    public function openImportCsvModal(): void
    {
        $this->csv_file = null;
        $this->importResults = [];
        $this->importEventFields = [];

        if ($this->eventUuid) {
            $event = Event::where('uuid', $this->eventUuid)->first();
            $this->import_event_id = $event ? (string) $event->id : '';
        } else {
            $firstEvent = Event::first();
            $this->import_event_id = $firstEvent ? (string) $firstEvent->id : '';
        }

        $this->loadImportEventFields();
        $this->showImportCsvModal = true;
    }

    public function closeImportCsvModal(): void
    {
        $this->showImportCsvModal = false;
        $this->csv_file = null;
        $this->importResults = [];
        $this->import_event_id = '';
        $this->importEventFields = [];
    }

    public function updatedImportEventId($value): void
    {
        $this->importResults = [];
        $this->csv_file = null;
        $this->loadImportEventFields();
    }

    private function loadImportEventFields(): void
    {
        if (empty($this->import_event_id)) {
            $this->importEventFields = [];
            return;
        }

        $event = Event::find($this->import_event_id);
        if (!$event) {
            $this->importEventFields = [];
            return;
        }

        $config = $event->form_fields_config;
        $fields = [];

        foreach ($config['standard_fields'] as $key => $status) {
            if ($status !== 'disabled') {
                $fields[] = [
                    'key' => $key,
                    'label' => self::FIELD_LABEL_MAP[$key] ?? ucwords(str_replace('_', ' ', $key)),
                    'status' => $status,
                    'type' => 'standard',
                ];
            }
        }

        foreach ($config['custom_fields'] as $customField) {
            if (!empty(trim($customField['label'] ?? ''))) {
                $fields[] = [
                    'key' => $customField['id'] ?? $customField['label'],
                    'label' => $customField['label'],
                    'status' => ($customField['required'] ?? false) ? 'required' : 'optional',
                    'type' => 'custom',
                ];
            }
        }

        $this->importEventFields = $fields;
    }

    public function downloadCsvTemplate()
    {
        if (empty($this->import_event_id)) {
            session()->flash('error', 'Please select an event first.');
            return;
        }

        $event = Event::find($this->import_event_id);
        if (!$event) {
            session()->flash('error', 'Event not found.');
            return;
        }

        $config = $event->form_fields_config;
        $headers = [];
        $exampleRow = [];

        // Add non-disabled standard fields
        foreach ($config['standard_fields'] as $key => $status) {
            if ($status !== 'disabled') {
                $label = self::FIELD_LABEL_MAP[$key] ?? ucwords(str_replace('_', ' ', $key));
                $headers[] = $label;
                $exampleRow[] = $this->getExampleValue($key, $status);
            }
        }

        // Add custom fields
        foreach ($config['custom_fields'] as $customField) {
            if (!empty(trim($customField['label'] ?? ''))) {
                $headers[] = $customField['label'];
                $exampleRow[] = '';
            }
        }

        // Always include Role and Verification Status
        if (!in_array('Role', $headers)) {
            $headers[] = 'Role';
            $exampleRow[] = 'general_admission';
        }
        if (!in_array('Verification Status', $headers)) {
            $headers[] = 'Verification Status';
            $exampleRow[] = 'verified';
        }

        $safeEventName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $event->name);
        $fileName = "import_template_{$safeEventName}_" . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($headers, $exampleRow) {
            $handle = fopen('php://output', 'w');
            // Add BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $headers);
            fputcsv($handle, $exampleRow);
            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Normalizes phone number from CSV import (restoring leading 0 stripped by Excel)
     * into a standard 10-digit Ghana format (0XXXXXXXXX) or valid international digits.
     */
    private function normalizePhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Strip all non-digits
        $digits = preg_replace('/[^0-9]/', '', (string)$phone);

        if (empty($digits)) {
            return null;
        }

        // Case 1: Excel stripped leading 0 for 9-digit local numbers (e.g. 547977840, 243036092)
        if (strlen($digits) === 9) {
            return '0' . $digits;
        }

        // Case 2: International Ghana number starting with 233 (e.g. 233547977840 -> 0547977840)
        if (strlen($digits) === 12 && str_starts_with($digits, '233')) {
            return '0' . substr($digits, 3);
        }

        // Case 3: Already 10-digit standard local number (e.g. 0547977840)
        if (strlen($digits) === 10) {
            return $digits;
        }

        return $digits;
    }

    private function getExampleValue(string $fieldKey, string $status): string
    {
        $suffix = $status === 'required' ? ' (required)' : ' (optional)';
        return match ($fieldKey) {
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0241234567',
            'company' => 'Acme Inc',
            'job_title' => 'Manager',
            'country' => 'Ghana',
            'gender' => 'Male',
            'emergency_contact_name' => 'Jane Doe',
            'emergency_contact_phone' => '0209876543',
            'dietary_preferences' => 'None',
            'accessibility_needs' => 'None',
            'registration_reason' => 'Networking',
            default => '',
        };
    }

    public function importCsv(): void
    {
        if (empty($this->import_event_id)) {
            session()->flash('error', 'Please select an event.');
            return;
        }

        if (!$this->csv_file) {
            session()->flash('error', 'Please upload a CSV file.');
            return;
        }

        $event = Event::find($this->import_event_id);
        if (!$event) {
            session()->flash('error', 'Event not found.');
            return;
        }

        $config = $event->form_fields_config;
        $requiredFields = [];
        foreach ($config['standard_fields'] as $key => $status) {
            if ($status === 'required') {
                $requiredFields[] = $key;
            }
        }

        // Parse CSV using fgetcsv to properly handle quoted multi-line cells and exact row numbering
        $path = $this->csv_file->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            session()->flash('error', 'Unable to open CSV file.');
            return;
        }

        // Read header row
        $rawHeader = fgetcsv($handle);
        if (!$rawHeader || empty(array_filter($rawHeader, fn($h) => trim((string)$h) !== ''))) {
            fclose($handle);
            session()->flash('error', 'CSV file must contain a valid header row.');
            return;
        }

        // Remove UTF-8 BOM from the first header column if present
        $rawHeader[0] = preg_replace('/^\x{FEFF}/u', '', (string)$rawHeader[0]);
        $csvHeaders = array_map('trim', $rawHeader);

        // Build column index map: CSV column index => db field or custom field key
        $columnMap = [];
        $customFieldLabels = [];
        foreach ($config['custom_fields'] as $cf) {
            if (!empty(trim($cf['label'] ?? ''))) {
                $customFieldLabels[strtolower(trim($cf['label']))] = $cf['id'] ?? $cf['label'];
            }
        }

        foreach ($csvHeaders as $index => $header) {
            $normalizedHeader = trim($header);
            // Check standard field map
            if (isset(self::HEADER_FIELD_MAP[$normalizedHeader])) {
                $columnMap[$index] = ['type' => 'standard', 'field' => self::HEADER_FIELD_MAP[$normalizedHeader]];
            }
            // Check custom fields (case-insensitive)
            elseif (isset($customFieldLabels[strtolower($normalizedHeader)])) {
                $columnMap[$index] = ['type' => 'custom', 'field' => $customFieldLabels[strtolower($normalizedHeader)], 'label' => $normalizedHeader];
            }
        }

        $imported = 0;
        $skipped = 0;
        $skipReasons = [];
        $errors = [];
        $organizationId = $event->organization_id;
        $validRoles = array_column(AccessRole::cases(), 'value');
        $validStatuses = array_column(VerificationStatus::cases(), 'value');

        // Track seen emails and phones within the CSV to prevent intra-file duplicates
        $seenEmails = [];
        $seenPhones = [];

        $rowNumber = 1; // Header is row 1
        $totalRowsProcessed = 0;

        while (($values = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip entirely blank rows (e.g. trailing empty rows in spreadsheet exports)
            if (empty(array_filter($values, fn($v) => trim((string)$v) !== ''))) {
                continue;
            }

            $totalRowsProcessed++;

            $rowData = [];
            $customData = [];

            foreach ($columnMap as $colIndex => $mapping) {
                $value = isset($values[$colIndex]) ? trim((string)$values[$colIndex]) : '';
                if ($mapping['type'] === 'standard') {
                    $rowData[$mapping['field']] = $value;
                } else {
                    $customData[$mapping['field']] = $value;
                }
            }

            // Validate required fields
            $missingFields = [];
            foreach ($requiredFields as $reqField) {
                if (empty($rowData[$reqField] ?? '')) {
                    $label = self::FIELD_LABEL_MAP[$reqField] ?? $reqField;
                    $missingFields[] = $label;
                }
            }

            if (!empty($missingFields)) {
                $errors[] = "Row {$rowNumber}: Missing required fields — " . implode(', ', $missingFields);
                continue;
            }

            // Must have email at minimum for deduplication
            if (empty($rowData['email'] ?? '')) {
                $errors[] = "Row {$rowNumber}: Email address is required.";
                continue;
            }

            // Validate email format
            if (!filter_var($rowData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNumber}: Invalid email address '{$rowData['email']}'.";
                continue;
            }

            // Normalize phone numbers (handles Excel stripping leading 0 e.g. 547977840 -> 0547977840)
            if (isset($rowData['phone'])) {
                $rowData['phone'] = $this->normalizePhoneNumber($rowData['phone']);
            }
            if (isset($rowData['emergency_contact_phone'])) {
                $rowData['emergency_contact_phone'] = $this->normalizePhoneNumber($rowData['emergency_contact_phone']);
            }

            $email = strtolower(trim($rowData['email']));
            $phone = !empty($rowData['phone'] ?? '') ? trim($rowData['phone']) : null;

            // Check for duplicate email within this CSV file
            if (isset($seenEmails[$email])) {
                $skipped++;
                $skipReasons[] = "Row {$rowNumber}: Duplicate email '{$rowData['email']}' (same as row {$seenEmails[$email]})";
                continue;
            }

            // Check for duplicate phone within this CSV file
            if ($phone && isset($seenPhones[$phone])) {
                $skipped++;
                $skipReasons[] = "Row {$rowNumber}: Duplicate phone '{$phone}' (same as row {$seenPhones[$phone]})";
                continue;
            }

            // Check for duplicate email in database
            $emailExists = Attendee::where('event_id', $event->id)
                ->where('email', $rowData['email'])
                ->exists();

            if ($emailExists) {
                $skipped++;
                $skipReasons[] = "Row {$rowNumber}: Email '{$rowData['email']}' is already registered for this event";
                continue;
            }

            // Check for duplicate phone in database (checks 0547977840, 547977840, and 233547977840)
            if ($phone) {
                $phoneExists = Attendee::where('event_id', $event->id)
                    ->where(function($q) use ($phone) {
                        $q->where('phone', $phone);
                        if (str_starts_with($phone, '0')) {
                            $q->orWhere('phone', substr($phone, 1))
                              ->orWhere('phone', '233' . substr($phone, 1));
                        }
                    })
                    ->exists();

                if ($phoneExists) {
                    $skipped++;
                    $skipReasons[] = "Row {$rowNumber}: Phone '{$phone}' is already registered for this event";
                    continue;
                }
            }

            // Track this row's email and phone as seen
            $seenEmails[$email] = $rowNumber;
            if ($phone) {
                $seenPhones[$phone] = $rowNumber;
            }

            // Determine role and verification status
            $role = $rowData['access_role'] ?? 'general_admission';
            if (!in_array($role, $validRoles)) {
                $role = 'general_admission';
            }
            unset($rowData['access_role']);

            $verificationStatus = $rowData['verification_status'] ?? 'verified';
            if (!in_array($verificationStatus, $validStatuses)) {
                $verificationStatus = 'verified';
            }
            unset($rowData['verification_status']);

            // Build metadata with custom fields
            $metadata = !empty($customData) ? $customData : null;

            try {
                $attendee = Attendee::create(array_merge($rowData, [
                    'uuid' => (string) Str::uuid(),
                    'event_id' => $event->id,
                    'organization_id' => $organizationId,
                    'access_role' => $role,
                    'verification_status' => $verificationStatus,
                    'verified_at' => $verificationStatus === 'verified' ? now() : null,
                    'consent' => true,
                    'metadata' => $metadata,
                ]));

                // Auto-generate QR code for verified attendees
                if ($verificationStatus === 'verified') {
                    QrCode::create([
                        'uuid' => (string) Str::uuid(),
                        'attendee_id' => $attendee->id,
                        'event_id' => $event->id,
                        'secure_token' => Str::random(32),
                        'issued_at' => now(),
                        'is_revoked' => false,
                    ]);
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);

        $this->importResults = [
            'imported' => $imported,
            'skipped' => $skipped,
            'skip_reasons' => $skipReasons,
            'errors' => $errors,
            'total_rows' => $totalRowsProcessed,
        ];

        $this->csv_file = null;

        if ($imported > 0) {
            session()->flash('success', "{$imported} attendee(s) imported successfully" . ($skipped > 0 ? ", {$skipped} duplicate(s) skipped" : '') . '.');
        } elseif ($skipped > 0) {
            session()->flash('warning', "No new attendees imported. {$skipped} duplicate(s) skipped.");
        }
    }

    public function assignGateToAttendee($uuid, $gateId)
    {
        $attendee = Attendee::where('uuid', $uuid)->first();
        if ($attendee) {
            $gate = $gateId ? \App\Models\Gate::find($gateId) : null;
            $attendee->assigned_gate_id = $gate ? $gate->id : null;
            
            $this->ensureSecurityUserAccount($attendee);
            $attendee->save();
            
            $gateNotice = $gate ? "assigned to '{$gate->name}'" : "unassigned from gate";
            session()->flash('success', "Security personnel '{$attendee->full_name}' {$gateNotice}. Login credentials ready for {$attendee->email}.");
        }
    }

    protected function ensureSecurityUserAccount(Attendee $attendee)
    {
        $user = \App\Models\User::where('email', $attendee->email)->first();

        if (!$user) {
            $user = \App\Models\User::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'name' => $attendee->full_name,
                'email' => $attendee->email,
                'password' => \Illuminate\Support\Facades\Hash::make('Security@123'),
                'phone' => $attendee->phone,
                'organization_id' => $attendee->organization_id,
                'is_active' => true,
            ]);
        }

        if (method_exists($user, 'assignRole')) {
            try {
                $user->assignRole('gate_staff');
            } catch (\Exception $e) {
                // Role might not exist in spatie roles
            }
        }

        $attendee->user_id = $user->id;
    }

    public function render()
    {
        $countQuery = $this->getFilteredAttendeesQuery();

        $totalCount = (clone $countQuery)->count();
        $verifiedCount = (clone $countQuery)->where('verification_status', VerificationStatus::Verified)->count();
        $pendingCount = (clone $countQuery)->where('verification_status', VerificationStatus::Pending)->count();
        $rejectedCount = (clone $countQuery)->where('verification_status', VerificationStatus::Rejected)->count();

        $attendees = $this->getFilteredAttendeesQuery()->latest()->paginate($this->perPage);

        // Keep selectAllOnPage checkbox in sync with current page items
        $currentPageUuids = $attendees->pluck('uuid')->map(fn($u) => (string) $u)->toArray();
        if (count($currentPageUuids) > 0 && count(array_diff($currentPageUuids, $this->selectedAttendees)) === 0) {
            $this->selectAllOnPage = true;
        } else {
            $this->selectAllOnPage = false;
        }
        $events = Event::select('id', 'uuid', 'name', 'settings')->get();

        $availableGates = \App\Models\Gate::all();
        if ($this->eventUuid) {
            $currEvt = Event::where('uuid', $this->eventUuid)->first();
            if ($currEvt) {
                $availableGates = \App\Models\Gate::where('event_id', $currEvt->id)->get();
            }
        }

        $isSuperAdmin = auth()->user()->hasRole('super_admin') || auth()->user()->email === 'superadmin@attendflow.com';

        $organizationsTree = collect();
        if ($isSuperAdmin) {
            $organizationsTree = \App\Models\Organization::with(['events' => function($q) {
                $q->withCount('attendees');
            }])->get();
        } else {
            $currentOrgId = auth()->user()->organization_id;
            if ($currentOrgId) {
                $organizationsTree = \App\Models\Organization::where('id', $currentOrgId)->with(['events' => function($q) {
                    $q->withCount('attendees');
                }])->get();
            }
        }

        $mobileOrg = null;
        if ($this->showMobileOrgModal && $this->mobileOrgId) {
            $mobileOrg = \App\Models\Organization::with(['users', 'events' => function($q) {
                $q->withCount('attendees');
            }])->find($this->mobileOrgId);
        }

        $mobileEvent = null;
        $mobileAttendees = collect();
        if ($this->showMobileAttendeesModal && $this->mobileEventId) {
            $mobileEvent = Event::with('organization')->find($this->mobileEventId);
            $mobileAttendees = Attendee::where('event_id', $this->mobileEventId)
                ->with(['qrCode', 'assignedGate', 'latestCheckIn.gate', 'latestCheckIn.scanner'])
                ->latest()
                ->get();
        }

        return view('livewire.attendees.attendee-list', [
            'mobileOrg' => $mobileOrg,
            'mobileEvent' => $mobileEvent,
            'mobileAttendees' => $mobileAttendees,
            'showMobileAttendeesModal' => $this->showMobileAttendeesModal,
            'attendees' => $attendees,
            'events' => $events,
            'availableGates' => $availableGates,
            'totalCount' => $totalCount,
            'verifiedCount' => $verifiedCount,
            'pendingCount' => $pendingCount,
            'rejectedCount' => $rejectedCount,
            'isSuperAdmin' => $isSuperAdmin,
            'organizationsTree' => $organizationsTree,
            'eventUuid' => $this->eventUuid,
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'roleFilter' => $this->roleFilter,
            'expandedOrgs' => $this->expandedOrgs,
            'expandedEvents' => $this->expandedEvents,
            'groupedView' => $this->groupedView,
            'perPage' => $this->perPage,
            'selectedAttendees' => $this->selectedAttendees ?? [],
            'selectAll' => $this->selectAll,
            'showAddModal' => $this->showAddModal,
            'showDetailsModal' => $this->showDetailsModal,
            'showBulkInviteModal' => $this->showBulkInviteModal,
            'showImportCsvModal' => $this->showImportCsvModal,
            'importResults' => $this->importResults,
            'importEventFields' => $this->importEventFields,
            'showLinkGeneratorModal' => $this->showLinkGeneratorModal,
            'gen_access_role' => $this->gen_access_role,
            'gen_email' => $this->gen_email,
            'gen_max_uses' => $this->gen_max_uses,
            'generated_invite_url' => $this->generated_invite_url,
            'selectedAttendee' => $this->selectedAttendee,
            'gen_standard_fields' => $this->gen_standard_fields,
            'gen_custom_fields' => $this->gen_custom_fields,
        ]);
    }

    public function updatedSelectAll($value): void
    {
        $this->updatedSelectAllOnPage($value);
    }
}
