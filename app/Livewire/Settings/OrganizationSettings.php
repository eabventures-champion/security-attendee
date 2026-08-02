<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Organization;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
#[Title('Organization Settings')]
class OrganizationSettings extends Component
{
    // Brand Preferences
    public string $name = '';
    public string $description = '';
    public string $brand_color = '#3b82f6';
    public string $website = '';
    public string $timezone = 'UTC';

    // Domain & Whitelabel
    public string $subdomain = '';
    public string $custom_domain = '';
    public bool $enable_qr_watermark = true;
    public bool $custom_email_branding = true;

    // API & Webhooks
    public string $api_key = '';
    public string $webhook_url = '';
    public bool $webhook_active = true;

    public function mount(): void
    {
        $orgId = auth()->user()->organization_id ?? session('current_organization_id');
        $org = Organization::find($orgId);

        if ($org) {
            $this->name = $org->name ?? 'TechConf Global';
            $this->description = $org->description ?? 'Event Attendance Management System';
            $this->brand_color = $org->brand_color ?? '#3b82f6';
            $this->website = $org->website ?? 'https://attendflow.com';
            $this->timezone = $org->timezone ?? 'UTC';
            $this->subdomain = $org->slug ?? 'techconf';
            $this->custom_domain = $org->settings['custom_domain'] ?? 'events.techconf.com';
            $this->enable_qr_watermark = $org->settings['qr_watermark'] ?? true;
            $this->custom_email_branding = $org->settings['email_branding'] ?? true;
            $this->webhook_url = $org->settings['webhook_url'] ?? 'https://api.techconf.com/webhooks/checkin';
            $this->api_key = $org->settings['api_key'] ?? 'af_live_' . Str::random(24);
        } else {
            $this->name = 'TechConf Global';
            $this->api_key = 'af_live_' . Str::random(24);
        }
    }

    public function saveBrandSettings(): void
    {
        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'brand_color' => 'required|string|max:7',
            'website' => 'nullable|url',
        ]);

        $orgId = auth()->user()->organization_id ?? session('current_organization_id');
        $org = Organization::find($orgId);

        if ($org) {
            $org->name = $this->name;
            $org->description = $this->description;
            $org->brand_color = $this->brand_color;
            $org->website = $this->website;
            $org->timezone = $this->timezone;
            $org->save();
        }

        session()->flash('brand_success', 'Brand preferences updated successfully.');
    }

    public function saveDomainSettings(): void
    {
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

        if (trim($this->resetConfirmationText) !== 'RESET PROJECT') {
            $this->addError('resetConfirmationText', 'Please type RESET PROJECT exactly as shown to confirm.');
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

            // Clear cache and session notifications
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');

            $this->closeResetModal();
            session()->flash('reset_success', '🚀 System factory reset complete! All events, attendees, notifications, gates, and organizations cleared. Clean fresh master workspace initialized.');
            session()->flash('message', '🚀 System factory reset complete! All events, attendees, notifications, gates, and organizations cleared.');

            return redirect()->route('dashboard')->with('message', '🚀 System factory reset complete! All data cleared.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \Illuminate\Support\Facades\Log::error("System Reset Failed: " . $e->getMessage());
            session()->flash('reset_error', 'System reset failed: ' . $e->getMessage());
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
