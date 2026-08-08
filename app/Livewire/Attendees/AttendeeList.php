<?php

namespace App\Livewire\Attendees;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\QrCode;
use App\Enums\VerificationStatus;
use App\Enums\AccessRole;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendeePrivateInvitation;

#[Layout('layouts.app')]
#[Title('Attendees Management')]
class AttendeeList extends Component
{
    use WithPagination;

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

    // Secure Single-Use Link Generator fields
    public bool $showLinkGeneratorModal = false;
    public string $gen_event_id = ''; // Explicit event selector for secure link
    public string $gen_access_role = 'vvip';
    public string $gen_category = 'details'; // 'details', 'no_details'
    public string $gen_email = '';
    public int $gen_max_uses = 1;
    public string $generated_invite_url = '';
    public string $generated_whatsapp_url = '';

    public function updatedGenEventId($value)
    {
        if ($value) {
            $event = Event::find($value);
            if ($event) {
                $this->gen_category = $event->settings['default_entry_mode'] ?? 'details';
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
            }
        } else {
            $this->gen_event_id = '';
            $this->gen_category = 'details';
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

    public function generateSingleUseLink()
    {
        if (empty($this->gen_event_id)) {
            session()->flash('error', 'Please select an event to generate the link for.');
            return;
        }

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

            session()->flash('success', "Attendee '{$attendee->full_name}' approved & verified! Official QR pass emailed to {$attendee->email}.");
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
            $attendee->delete();
            session()->flash('success', 'Attendee removed successfully.');
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

    public function getFilteredAttendeesQuery()
    {
        $query = Attendee::whereHas('event')
            ->with(['event', 'qrCode', 'assignedGate', 'latestCheckIn.gate', 'latestCheckIn.scanner']);

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

    public function bulkDeleteAttendees()
    {
        if (empty($this->selectedAttendees)) return;

        $attendees = Attendee::whereIn('uuid', $this->selectedAttendees)->get();
        $count = $attendees->count();
        $attendeeIds = $attendees->pluck('id')->toArray();

        if (!empty($attendeeIds)) {
            \App\Models\CheckIn::whereIn('attendee_id', $attendeeIds)->delete();
            \App\Models\QrCode::whereIn('attendee_id', $attendeeIds)->delete();
            Attendee::whereIn('id', $attendeeIds)->delete();
        }

        $this->selectedAttendees = [];
        $this->selectAllOnPage = false;
        $this->selectAll = false;
        session()->flash('success', "{$count} attendee(s) deleted successfully.");
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
        // Implement export logic
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
        $events = Event::select('id', 'uuid', 'name')->get();

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
            'showLinkGeneratorModal' => $this->showLinkGeneratorModal,
            'gen_access_role' => $this->gen_access_role,
            'gen_email' => $this->gen_email,
            'gen_max_uses' => $this->gen_max_uses,
            'generated_invite_url' => $this->generated_invite_url,
            'selectedAttendee' => $this->selectedAttendee,
        ]);
    }

    public function updatedSelectAll($value): void
    {
        $this->updatedSelectAllOnPage($value);
    }
}
