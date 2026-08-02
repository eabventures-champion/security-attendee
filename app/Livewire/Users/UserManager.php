<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
#[Title('Team & Role Management')]
class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public bool $showModal = false;

    public ?int $editingUserId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $selectedRole = 'event_manager';
    public $assigned_gate_id = null;
    public bool $is_active = true;

    public array $selectedUsers = [];
    public bool $selectAll = false;

    protected function rules(): array
    {
        $userId = $this->editingUserId;
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($userId ?? 'NULL'),
            'phone' => ['nullable', 'string', 'regex:/^[0-9]{10}$/'],
            'password' => $userId ? 'nullable|min:8' : 'required|min:8',
            'selectedRole' => 'required|string',
            'assigned_gate_id' => 'nullable',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'phone.regex' => 'Phone number must be exactly 10 digits (e.g. 0246345698).',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingUserId', 'name', 'email', 'phone', 'password', 'selectedRole', 'assigned_gate_id', 'is_active']);
        $this->selectedRole = 'event_manager';
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->password = '';
        $this->selectedRole = $user->roles->first()?->name ?? 'event_manager';
        $this->assigned_gate_id = $user->assigned_gate_id;
        $this->is_active = $user->is_active;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editingUserId', 'name', 'email', 'phone', 'password', 'assigned_gate_id']);
    }

    public function saveUser(): void
    {
        $this->validate();

        $currentUser = auth()->user();
        $orgId = $currentUser->organization_id ?? 1;

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->name = $this->name;
            $user->email = $this->email;
            $user->phone = $this->phone ?: null;
            if ($this->password) {
                $user->password = Hash::make($this->password);
            }
            $user->is_active = $this->is_active;
            $user->assigned_gate_id = $this->assigned_gate_id ?: null;
            if (!$user->invitation_token) {
                $user->invitation_token = (string) Str::uuid();
            }
            if (!$user->organization_id && !$user->hasRole('super_admin')) {
                $user->organization_id = $orgId;
            }
            $user->save();

            $user->syncRoles([$this->selectedRole]);
            session()->flash('message', 'User details updated successfully.');
        } else {
            $token = (string) Str::uuid();
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone ?: null,
                'password' => Hash::make($this->password),
                'organization_id' => $orgId,
                'assigned_gate_id' => $this->assigned_gate_id ?: null,
                'is_active' => false, // Account remains inactive until email invitation link is accepted
                'uuid' => (string) Str::uuid(),
                'invitation_token' => $token,
                'invitation_status' => 'pending',
            ]);

            $user->assignRole($this->selectedRole);

            // Send Email Invitation
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TeamMemberInvitation($user, $this->password));
                session()->flash('message', "✨ New team member added & invitation email sent to {$user->email}.");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send team invitation email to {$user->email}: " . $e->getMessage());
                session()->flash('message', "New team member added successfully.");
            }
        }

        $this->closeModal();
    }

    public ?int $resentUserId = null;

    public function resendInvitation(int $userId): void
    {
        $user = User::findOrFail($userId);
        if (!$user->invitation_token) {
            $user->invitation_token = (string) Str::uuid();
            $user->save();
        }

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TeamMemberInvitation($user));
            $this->resentUserId = $userId;
            session()->flash('message', "✉️ Invitation email with access link has been sent to {$user->email}.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to resend invitation email to {$user->email}: " . $e->getMessage());
            session()->flash('error', "Could not send email to {$user->email}. Please verify mail settings.");
        }
    }

    public function toggleUserStatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();

        session()->flash('message', 'User status updated.');
    }

    public function deleteUser(int $userId): void
    {
        if ($userId === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user = User::findOrFail($userId);
        $orgId = $user->organization_id;
        $user->delete();

        if ($orgId) {
            $org = \App\Models\Organization::find($orgId);
            if ($org && $org->users()->count() === 0 && $org->events()->count() === 0) {
                $org->delete();
            }
        }

        session()->flash('message', 'User removed successfully.');
    }

    public array $expandedOrgs = [];

    public function toggleExpandOrg(int $orgId): void
    {
        if (in_array($orgId, $this->expandedOrgs)) {
            $this->expandedOrgs = array_diff($this->expandedOrgs, [$orgId]);
        } else {
            $this->expandedOrgs[] = $orgId;
        }
    }

    public function togglePremiumTypography(int $orgId): void
    {
        $org = \App\Models\Organization::findOrFail($orgId);
        $org->has_premium_typography = !$org->has_premium_typography;
        $org->save();

        $status = $org->has_premium_typography ? 'UNLOCKED' : 'RESTRICTED';
        session()->flash('message', "🎨 Premium Typography Subscription {$status} for '{$org->name}'.");
    }

    public function disableOrgAdmin(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->approval_status = 'suspended';
        $user->is_active = false;
        $user->save();

        if ($user->organization_id) {
            $org = \App\Models\Organization::find($user->organization_id);
            if ($org) {
                $org->approval_status = 'suspended';
                $org->is_active = false;
                $org->save();

                // Cascade suspension to all team members under this organization
                User::where('organization_id', $org->id)->update([
                    'is_active' => false,
                    'approval_status' => 'suspended',
                ]);
            }
        }

        session()->flash('message', "⚠️ Suspended workspace and all associated team members for '{$user->name}'.");
    }

    public function approveOrgAdmin(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->approval_status = 'approved';
        $user->is_active = true;
        $user->invitation_status = 'confirmed';
        $user->approved_at = now();
        $user->save();

        if ($user->organization_id) {
            $org = \App\Models\Organization::find($user->organization_id);
            if ($org) {
                $org->approval_status = 'approved';
                $org->is_active = true;
                $org->approved_at = now();
                $org->save();

                // Cascade reactivation to all team members under this organization
                User::where('organization_id', $org->id)->update([
                    'is_active' => true,
                    'approval_status' => 'approved',
                ]);
            }
        }

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OrgAdminActivatedNotification($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send activation email to {$user->email}: " . $e->getMessage());
        }

        session()->flash('message', "🎉 Approved & activated workspace for '{$user->name}' and all team members.");
    }

    public function deleteOrgWorkspace(int $userId): void
    {
        $user = User::findOrFail($userId);
        $orgId = $user->organization_id;

        if ($orgId) {
            $org = \App\Models\Organization::find($orgId);
            if ($org) {
                $events = \App\Models\Event::where('organization_id', $org->id)->get();
                foreach ($events as $event) {
                    \App\Models\CheckIn::where('event_id', $event->id)->delete();
                    \App\Models\Gate::where('event_id', $event->id)->delete();
                    \App\Models\QrCode::where('event_id', $event->id)->delete();
                    \App\Models\Attendee::where('event_id', $event->id)->delete();
                    $event->delete();
                }

                User::where('organization_id', $org->id)->forceDelete();
                $org->delete();
            }
        } else {
            $user->forceDelete();
        }

        session()->flash('message', '🗑️ Organization workspace and all associated data deleted successfully.');
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $currentUser = auth()->user();
            $isSuperAdmin = $currentUser->hasRole('super_admin') || $currentUser->email === 'superadmin@attendflow.com';

            if ($isSuperAdmin) {
                $this->selectedUsers = User::where(function ($q) {
                    $q->whereHas('roles', fn($r) => $r->where('name', 'organization_admin'))
                      ->orWhereNotNull('organization_id');
                })
                ->whereDoesntHave('roles', fn($r) => $r->where('name', 'super_admin'))
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
            } else {
                $orgId = $currentUser->organization_id ?? 1;
                $this->selectedUsers = User::where('organization_id', $orgId)
                    ->whereDoesntHave('roles', fn($q) => $q->where('name', 'super_admin'))
                    ->pluck('id')
                    ->map(fn($id) => (string) $id)
                    ->toArray();
            }
        } else {
            $this->selectedUsers = [];
        }
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedUsers)) {
            return;
        }

        $count = count($this->selectedUsers);
        foreach ($this->selectedUsers as $userId) {
            try {
                $this->deleteOrgWorkspace((int) $userId);
            } catch (\Exception $e) {
                // Ignore if already deleted
            }
        }

        $this->selectedUsers = [];
        $this->selectAll = false;

        session()->flash('message', "🗑️ Permanently deleted {$count} selected workspace(s) and all associated data.");
    }

    public function approveTypographyRequest(int $requestId, int $orgId): void
    {
        $org = \App\Models\Organization::findOrFail($orgId);
        $org->has_premium_typography = true;
        $org->save();

        $feedback = \App\Models\SystemFeedback::find($requestId);
        if ($feedback) {
            $feedback->update([
                'status' => 'resolved',
                'admin_response' => 'Approved & activated Premium Typography Pack for your organization workspace!',
                'responded_at' => now(),
            ]);
        }

        session()->flash('message', "🎉 Approved & activated Premium Typography Subscription for workspace '{$org->name}'.");
    }

    public function render()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->hasRole('super_admin') || $currentUser->email === 'superadmin@attendflow.com';

        $pendingTypographyRequests = collect();
        if ($isSuperAdmin) {
            $pendingTypographyRequests = \App\Models\SystemFeedback::with(['organization', 'user'])
                ->where('type', 'request')
                ->where('subject', 'like', '%Typography%')
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        if ($isSuperAdmin) {
            // For Super Admin: Retrieve Organization Admins (1 row per Organization Workspace)
            $orgAdminQuery = User::with(['organization.users.roles', 'organization.users.assignedGate.event', 'roles'])
                ->whereHas('roles', fn($r) => $r->where('name', 'organization_admin'));

            if ($this->search) {
                $orgAdminQuery->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('organization', fn($o) => $o->where('name', 'like', '%' . $this->search . '%'));
                });
            }

            if ($this->roleFilter) {
                $orgAdminQuery->whereHas('roles', fn($r) => $r->where('name', $this->roleFilter));
            }

            $users = $orgAdminQuery->latest()->paginate(10);
        } else {
            // For Organization Admin: Retrieve only team members within their organization
            $orgId = $currentUser->organization_id ?? 1;
            $query = User::with(['roles', 'assignedGate.event'])
                ->where('organization_id', $orgId)
                ->whereDoesntHave('roles', fn($q) => $q->where('name', 'super_admin'));

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->roleFilter) {
                $query->whereHas('roles', fn($r) => $r->where('name', $this->roleFilter));
            }

            $users = $query->latest()->paginate(10);
        }

        $roles = Role::whereNotIn('name', ['super_admin'])->get();

        if ($isSuperAdmin) {
            $gates = \App\Models\Gate::has('event')->with('event')->get();
        } else {
            $orgId = $currentUser->organization_id ?? 1;
            $gates = \App\Models\Gate::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
                ->with('event')
                ->get();
        }

        return view('livewire.users.user-manager', [
            'users' => $users,
            'roles' => $roles,
            'gates' => $gates,
            'isSuperAdmin' => $isSuperAdmin,
            'pendingTypographyRequests' => $pendingTypographyRequests,
            'selectedUsers' => $this->selectedUsers ?? [],
            'expandedOrgs' => $this->expandedOrgs ?? [],
            'showModal' => $this->showModal ?? false,
            'editingUserId' => $this->editingUserId ?? null,
            'selectedRole' => $this->selectedRole ?? 'event_manager',
            'assigned_gate_id' => $this->assigned_gate_id ?? null,
            'search' => $this->search ?? '',
            'roleFilter' => $this->roleFilter ?? '',
            'selectAll' => $this->selectAll ?? false,
        ]);
    }
}
