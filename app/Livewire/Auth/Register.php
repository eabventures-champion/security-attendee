<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Layout('layouts.guest')]
#[Title('Create Account — AttendFlow')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $organization_name = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'organization_name' => ['required', 'string', 'min:2', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
            'terms' => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Full name must be at least 3 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'organization_name.required' => 'Please enter your organization name.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password_confirmation.required' => 'Please confirm your password.',
            'password_confirmation.same' => 'Passwords do not match.',
            'terms.accepted' => 'You must agree to the Terms of Service.',
        ];
    }

    public bool $registeredPending = false;

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function register(): mixed
    {
        $this->validate();

        $organization = Organization::create([
            'name' => $this->organization_name,
            'slug' => Str::slug($this->organization_name) . '-' . Str::random(4),
            'brand_color' => '#3b82f6',
            'is_active' => false,
            'approval_status' => 'pending_approval',
        ]);

        $approvalToken = (string) Str::uuid();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'organization_id' => $organization->id,
            'is_active' => false,
            'approval_status' => 'pending_approval',
            'approval_token' => $approvalToken,
        ]);

        try {
            $user->assignRole('organization_admin');
        } catch (\Exception $e) {
            // Ignore if role seeder not run in test
        }

        // Notify Super Admin via Email
        try {
            $superAdmins = User::whereHas('roles', fn($q) => $q->where('name', 'super_admin'))->pluck('email')->toArray();
            $recipient = !empty($superAdmins) ? $superAdmins : 'superadmin@attendflow.com';

            \Illuminate\Support\Facades\Mail::to($recipient)->send(new \App\Mail\SuperAdminOrgApprovalRequest($user, $organization));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send super admin approval request email: " . $e->getMessage());
        }

        // Notify Super Admin via Notification Bell
        try {
            $superAdminUsers = User::whereHas('roles', fn($q) => $q->where('name', 'super_admin'))
                ->orWhere('email', 'superadmin@attendflow.com')
                ->get();

            foreach ($superAdminUsers as $sa) {
                $sa->notify(new \App\Notifications\NewAccountRegistrationNotification($user, $organization));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send in-app notification bell alert: " . $e->getMessage());
        }

        $this->registeredPending = true;
        return null;
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
