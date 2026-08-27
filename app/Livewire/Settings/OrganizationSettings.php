<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Organization;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Organization Settings')]
class OrganizationSettings extends Component
{
    use WithFileUploads;

    // Brand Preferences
    public string $name = '';
    public string $description = '';
    public string $brand_color = '#3b82f6';
    public string $website = '';
    public string $timezone = 'UTC';
    public $logo = null;
    public ?string $existing_logo_path = null;

    // Domain & Whitelabel
    public string $subdomain = '';
    public string $custom_domain = '';
    public bool $enable_qr_watermark = true;
    public bool $custom_email_branding = true;

    // API & Webhooks
    public string $api_key = '';
    public string $webhook_url = '';
    public bool $webhook_active = true;

    protected function getOrganization(): ?Organization
    {
        $user = auth()->user();
        if ($user && $user->organization) {
            return $user->organization;
        }
        $orgId = $user ? $user->organization_id : session('current_organization_id');
        if ($orgId) {
            return Organization::find($orgId);
        }
        return Organization::first();
    }

    public function mount(): void
    {
        $org = $this->getOrganization();

        if ($org) {
            $this->name = $org->name ?? 'Federation of African Law Students';
            $this->description = $org->description ?? 'Event Attendance Management System';
            $this->brand_color = $org->brand_color ?? '#3b82f6';
            $this->website = $org->website ?? 'https://attendflow.com';
            $this->timezone = $org->timezone ?? 'UTC';
            $this->existing_logo_path = $org->logo_path ?? null;
            $this->subdomain = $org->slug ?? 'techconf';
            $this->custom_domain = $org->settings['custom_domain'] ?? 'events.techconf.com';
            $this->enable_qr_watermark = $org->settings['qr_watermark'] ?? true;
            $this->custom_email_branding = $org->settings['email_branding'] ?? true;
            $this->webhook_url = $org->settings['webhook_url'] ?? 'https://api.techconf.com/webhooks/checkin';
            $this->api_key = $org->settings['api_key'] ?? 'af_live_' . Str::random(24);
        } else {
            $this->name = 'Federation of African Law Students';
            $this->api_key = 'af_live_' . Str::random(24);
        }
    }

    protected function authorizeOrgAdmin(): bool
    {
        $user = auth()->user();
        if (!$user || (!$user->isSuperAdmin() && !$user->isOrganizationAdmin())) {
            session()->flash('error', '⚠️ Access Restricted: Only Organization Administrators can modify workspace settings.');
            return false;
        }
        return true;
    }

    public function saveBrandSettings(): void
    {
        if (!$this->authorizeOrgAdmin()) return;

        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'brand_color' => 'required|string|max:20',
            'website' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:5120',
        ]);

        $org = $this->getOrganization();

        if ($org) {
            $org->name = trim($this->name);
            $org->description = $this->description;
            $org->brand_color = $this->brand_color;
            $org->website = $this->website;
            $org->timezone = $this->timezone;

            if ($this->logo) {
                $logoPath = $this->logo->store('organizations/logos', 'public');
                $org->logo_path = $logoPath;
                $this->existing_logo_path = $logoPath;
                $this->logo = null;
            }

            $org->save();
        }

        session()->flash('brand_success', 'Brand preferences and logo updated successfully!');
    }

    public function removeLogo(): void
    {
        if (!$this->authorizeOrgAdmin()) return;

        $orgId = auth()->user()->organization_id ?? session('current_organization_id');
        $org = Organization::find($orgId);

        if ($org) {
            if ($org->logo_path && !str_starts_with($org->logo_path, 'http')) {
                Storage::disk('public')->delete($org->logo_path);
            }
            $org->logo_path = null;
            $org->save();
            $this->existing_logo_path = null;
            $this->logo = null;
        }

        session()->flash('brand_success', 'Organization logo removed.');
    }

    public function saveDomainSettings(): void
    {
        if (!$this->authorizeOrgAdmin()) return;

        $this->validate([
            'subdomain' => 'required|string|min:2|max:50',
            'custom_domain' => 'nullable|string|max:255',
        ]);

        $orgId = auth()->user()->organization_id ?? session('current_organization_id');
        $org = Organization::find($orgId);

        if ($org) {
            $settings = $org->settings ?? [];
            $settings['custom_domain'] = $this->custom_domain;
            $settings['qr_watermark'] = $this->enable_qr_watermark;
            $settings['email_branding'] = $this->custom_email_branding;
            $org->settings = $settings;
            $org->save();
        }

        session()->flash('domain_success', 'Domain and whitelabel preferences saved.');
    }

    public function saveApiSettings(): void
    {
        if (!$this->authorizeOrgAdmin()) return;

        $this->validate([
            'webhook_url' => 'nullable|url',
        ]);

        $orgId = auth()->user()->organization_id ?? session('current_organization_id');
        $org = Organization::find($orgId);

        if ($org) {
            $settings = $org->settings ?? [];
            $settings['webhook_url'] = $this->webhook_url;
            $settings['api_key'] = $this->api_key;
            $org->settings = $settings;
            $org->save();
        }

        session()->flash('api_success', 'API access & webhook settings updated.');
    }

    public bool $showResetModal = false;
    public string $resetConfirmationText = '';

    public function openResetModal(): void
    {
        $this->resetConfirmationText = '';
        $this->showResetModal = true;
    }

    public function closeResetModal(): void
    {
        $this->showResetModal = false;
        $this->resetConfirmationText = '';
    }

    public function executeSystemReset()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->hasRole('super_admin') || $currentUser->email === 'superadmin@attendflow.com';

        if (!$isSuperAdmin) {
            session()->flash('reset_error', 'Unauthorized action. Only the main Super Admin can reset the system.');
            return;
        }

        $normalizedText = strtoupper(preg_replace('/\s+/', ' ', trim((string) $this->resetConfirmationText)));
        if ($normalizedText !== 'RESET PROJECT') {
            $this->addError('resetConfirmationText', 'Please type RESET PROJECT to confirm.');
            return;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // 1. Clear Check-ins, Scans & Gate Logs
            \Illuminate\Support\Facades\DB::table('check_ins')->truncate();
            \Illuminate\Support\Facades\DB::table('scan_devices')->truncate();

            // 2. Clear QR Codes & Attendee Passes
            \Illuminate\Support\Facades\DB::table('qr_codes')->truncate();
            \Illuminate\Support\Facades\DB::table('attendees')->truncate();

            // 3. Clear Gates, Sessions, Invitations, Categories & Waiting Lists
            \Illuminate\Support\Facades\DB::table('gates')->truncate();
            \Illuminate\Support\Facades\DB::table('event_sessions')->truncate();
            \Illuminate\Support\Facades\DB::table('event_invitations')->truncate();
            \Illuminate\Support\Facades\DB::table('ticket_categories')->truncate();
            \Illuminate\Support\Facades\DB::table('waiting_lists')->truncate();

            // 4. Clear Events
            \Illuminate\Support\Facades\DB::table('events')->truncate();

            // 5. Clear Audit, Notifications & System Logs
            \Illuminate\Support\Facades\DB::table('notifications')->truncate();
            \Illuminate\Support\Facades\DB::table('notification_logs')->truncate();
            \Illuminate\Support\Facades\DB::table('audit_logs')->truncate();
            \Illuminate\Support\Facades\DB::table('system_feedbacks')->truncate();
            \Illuminate\Support\Facades\DB::table('organization_subscriptions')->truncate();
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->truncate();

            // 6. Delete Non-SuperAdmin Users
            \App\Models\User::whereDoesntHave('roles', fn($q) => $q->where('name', 'super_admin'))
                ->where('email', '!=', 'superadmin@attendflow.com')
                ->where('id', '!=', $currentUser->id)
                ->forceDelete();

            // 7. Reset Organizations Table & Create Fresh Master Organization
            \Illuminate\Support\Facades\DB::table('organizations')->truncate();

            $freshOrg = \App\Models\Organization::create([
                'uuid' => (string) Str::uuid(),
                'name' => 'Main Workspace',
                'slug' => 'main-workspace',
                'description' => 'Primary Organization Workspace',
                'brand_color' => '#3b82f6',
                'timezone' => 'UTC',
                'is_active' => true,
            ]);

            // 8. Ensure Super Admin remains active & linked to fresh master organization
            $currentUser->organization_id = $freshOrg->id;
            $currentUser->is_active = true;
            $currentUser->approval_status = 'approved';
            $currentUser->approved_at = now();
            $currentUser->save();

            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \Illuminate\Support\Facades\DB::commit();

            $this->showResetModal = false;
            $this->resetConfirmationText = '';

            session()->flash('message', '🚀 System factory reset complete! All events, attendees, notifications, gates, and organizations cleared. Master workspace initialized.');
            session()->flash('success', '🚀 System factory reset complete! All events, attendees, notifications, gates, and organizations cleared.');

            return redirect()->to('/dashboard');

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \Illuminate\Support\Facades\Log::error("System Reset Failed: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->addError('resetConfirmationText', 'System reset error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $isSuperAdmin = auth()->user()->hasRole('super_admin') || auth()->user()->email === 'superadmin@attendflow.com';

        return view('livewire.settings.organization-settings', [
            'isSuperAdmin' => $isSuperAdmin,
            'showResetModal' => $this->showResetModal ?? false,
            'resetConfirmInput' => $this->resetConfirmInput ?? '',
        ]);
    }
}
