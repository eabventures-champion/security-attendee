<div class="space-y-8 max-w-5xl font-inter">
    <!-- Header -->
    <div>
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Organization Settings</h1>
        <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm mt-1 font-medium">Manage your brand preferences, domain settings, and API access.</p>
    </div>

    <!-- Active Subscription Banner -->
    <div class="bg-gradient-to-r from-blue-900/10 via-indigo-900/10 to-purple-900/10 dark:from-blue-900/40 dark:via-purple-900/40 dark:to-slate-900/60 backdrop-blur-xl border border-blue-500/30 rounded-2xl p-4 sm:p-6 shadow-sm dark:shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-500/20 border border-blue-500/40 flex items-center justify-center text-blue-600 dark:text-blue-400 font-black text-lg sm:text-xl shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Enterprise Pro Plan</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/30">Active Subscription</span>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 font-medium leading-relaxed">Unlimited Events, Whitelabel Domains, Custom QR Codes, and Real-Time Webhooks enabled.</p>
            </div>
        </div>
        <div class="text-xs text-slate-600 dark:text-slate-400 bg-white/60 dark:bg-white/5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 font-medium shrink-0 self-start sm:self-auto">
            Renews: <strong class="text-slate-900 dark:text-white">Jan 2027</strong>
        </div>
    </div>

    <div class="space-y-8">
        <!-- 1. Brand Preferences -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-blue-500/10 rounded-xl text-blue-600 dark:text-blue-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 73v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Brand Preferences</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Customize organization name, colors, and website details</p>
                    </div>
                </div>
                @if (session()->has('brand_success'))
                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                        ✓ {{ session('brand_success') }}
                    </span>
                @endif
            </div>

            <form wire:submit.prevent="saveBrandSettings" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Organization Name <span class="text-rose-500">*</span></label>
                        <input wire:model="name" type="text" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Primary Brand Color</label>
                        <div class="flex items-center gap-3">
                            <input wire:model.live="brand_color" type="color" class="w-12 h-10 bg-transparent rounded-lg cursor-pointer border border-slate-200 dark:border-white/20">
                            <input wire:model="brand_color" type="text" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Organization Description</label>
                    <textarea wire:model="description" rows="3" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium" placeholder="Brief summary about your organization..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Official Website</label>
                        <input wire:model="website" type="url" placeholder="https://company.com" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Timezone</label>
                        <select wire:model="timezone" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                            <option value="UTC">UTC (Coordinated Universal Time)</option>
                            <option value="America/New_York">EST (Eastern Standard Time)</option>
                            <option value="Europe/London">GMT (London)</option>
                            <option value="Africa/Lagos">WAT (West Africa Time)</option>
                            <option value="Asia/Tokyo">JST (Tokyo)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-blue-500/25 transition-all cursor-pointer">
                        Save Brand Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Domain & Whitelabel Settings -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-purple-500/10 rounded-xl text-purple-600 dark:text-purple-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Domain & Whitelabel Settings</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Configure custom subdomains, CNAME records, and watermark preferences</p>
                    </div>
                </div>
                @if (session()->has('domain_success'))
                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                        ✓ {{ session('domain_success') }}
                    </span>
                @endif
            </div>

            <form wire:submit.prevent="saveDomainSettings" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">AttendFlow Subdomain</label>
                        <div class="flex rounded-xl overflow-hidden border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                            <input wire:model="subdomain" type="text" class="w-full px-4 py-2.5 bg-transparent text-slate-900 dark:text-white focus:outline-none font-medium">
                            <span class="px-4 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs flex items-center font-mono border-l border-slate-300 dark:border-white/10 font-semibold">.attendflow.com</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Custom CNAME Domain</label>
                        <input wire:model="custom_domain" type="text" placeholder="events.yourcompany.com" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none font-medium">
                    </div>
                </div>

                <div class="p-4 bg-purple-500/10 border border-purple-500/20 rounded-xl text-xs text-purple-700 dark:text-purple-300 flex items-center justify-between font-medium">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        SSL Status: <strong>Automatic SSL Provided & Active</strong>
                    </span>
                    <span class="font-mono text-[11px] text-purple-600 dark:text-purple-400">CNAME target: cname.attendflow.com</span>
                </div>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Enable QR Code Brand Watermark</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Embed your organization logo inside generated QR passes</p>
                        </div>
                        <input wire:model="enable_qr_watermark" type="checkbox" class="w-5 h-5 rounded bg-slate-100 dark:bg-white/5 border-slate-300 dark:border-white/20 text-purple-600 focus:ring-purple-500/50 cursor-pointer">
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">Custom Email Header Branding</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Remove AttendFlow branding from attendee verification emails</p>
                        </div>
                        <input wire:model="custom_email_branding" type="checkbox" class="w-5 h-5 rounded bg-slate-100 dark:bg-white/5 border-slate-300 dark:border-white/20 text-purple-600 focus:ring-purple-500/50 cursor-pointer">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-purple-500/25 transition-all cursor-pointer">
                        Save Domain Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. API Access & Webhook Integrations -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-emerald-500/10 rounded-xl text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">API Access & Real-Time Webhooks</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Manage developer secret keys and check-in event webhook URLs</p>
                    </div>
                </div>
                @if (session()->has('api_success'))
                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                        ✓ {{ session('api_success') }}
                    </span>
                @endif
            </div>

            <form wire:submit.prevent="saveApiSettings" class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Secret API Bearer Key</label>
                        <button type="button" wire:click="regenerateApiKey" wire:confirm="Are you sure? This will invalidate your old API key." class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-semibold cursor-pointer">Regenerate Key</button>
                    </div>
                    <div class="flex gap-2" x-data="{ copied: false }">
                        <input wire:model="api_key" type="text" readonly class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl text-emerald-700 dark:text-emerald-400 font-mono text-sm focus:outline-none font-bold">
                        <button type="button" @click="navigator.clipboard.writeText($wire.api_key); copied = true; setTimeout(() => copied = false, 2000)" class="px-4 py-2.5 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-xs font-semibold shrink-0 cursor-pointer">
                            <span x-show="!copied">Copy Key</span>
                            <span x-show="copied" class="text-emerald-600 dark:text-emerald-400 font-bold" style="display:none;">Copied!</span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Check-In Event Webhook Endpoint</label>
                    <input wire:model="webhook_url" type="url" placeholder="https://api.yourcompany.com/attendflow-webhook" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white font-mono text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">We will POST real-time JSON payloads whenever an attendee is scanned at a gate.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-emerald-500/25 transition-all cursor-pointer">
                        Save API & Webhook Settings
                    </button>
                </div>
            </form>
        </div>

        @if($isSuperAdmin)
            <!-- 4. Danger Zone — Super Admin Factory System Reset -->
            <div class="bg-rose-950/20 backdrop-blur-xl border border-rose-500/30 rounded-2xl p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-rose-500/20">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-rose-500/20 rounded-xl text-rose-400 border border-rose-500/30 shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-300 border border-rose-500/30 uppercase tracking-widest">SUPER ADMIN PRIVILEGE</span>
                            <h3 class="text-lg font-extrabold text-white mt-1">Danger Zone: Project Factory Reset</h3>
                            <p class="text-xs text-rose-300/80 font-medium">Wipe all application data to start completely fresh for production deployment.</p>
                        </div>
                    </div>
                    @if (session()->has('reset_error'))
                        <span class="text-xs text-rose-400 font-bold bg-rose-500/20 px-3 py-1 rounded-full border border-rose-500/40">
                            {{ session('reset_error') }}
                        </span>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-2">
                    <div class="text-xs text-slate-300 space-y-1">
                        <p class="font-bold text-rose-200">What will be cleared:</p>
                        <ul class="list-disc list-inside text-slate-400 space-y-0.5">
                            <li>All Events, Attendees, Passes, QR Codes, Gates & Check-in Logs</li>
                            <li>All Organizations & Team Accounts (excluding Main Super Admin)</li>
                            <li>All Reports & Audit Logs</li>
                        </ul>
                        <p class="text-emerald-400 font-semibold pt-1">✓ RBAC Roles, System Permissions & Super Admin Login Details will be preserved.</p>
                    </div>

                    <button type="button" wire:click="openResetModal" class="px-5 py-3 bg-gradient-to-r from-rose-600 to-red-700 hover:from-rose-700 hover:to-red-800 text-white font-black text-xs rounded-xl shadow-lg shadow-rose-600/30 transition-all cursor-pointer shrink-0 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Factory System Reset
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Factory Reset Confirmation Modal -->
    @if($showResetModal)
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-3 sm:p-4 pt-8 sm:pt-4 overflow-y-auto bg-slate-950/80 backdrop-blur-md animate-fadeInUp">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl sm:rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4 my-4 sm:my-auto">
                <div class="flex items-center gap-3.5 border-b border-rose-500/20 pb-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-300 border border-rose-500/30 uppercase tracking-widest">CRITICAL SYSTEM RESET</span>
                        <h3 class="text-xl font-black text-white mt-1">Confirm System Reset</h3>
                    </div>
                </div>

                <div class="text-xs text-slate-300 space-y-3 bg-slate-950/70 p-4 rounded-2xl border border-white/5">
                    <p class="font-bold text-rose-300 text-sm">⚠️ Warning: This action cannot be undone.</p>
                    <p class="leading-relaxed">
                        Executing a system reset will permanently erase all events, attendees, QR codes, gate configurations, scan logs, and team accounts.
                    </p>
                    <p class="text-emerald-400 font-medium">
                        ✓ Your Super Admin login credentials and RBAC roles will remain active so you can immediately start fresh.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider mb-2">
                        To confirm, please type <span class="text-rose-400 select-all font-mono font-black">RESET PROJECT</span> below:
                    </label>
                    <input wire:model="resetConfirmationText" type="text" placeholder="RESET PROJECT" class="w-full px-4 py-3 bg-slate-950 border border-rose-500/40 rounded-xl text-rose-300 font-mono text-sm font-black focus:ring-2 focus:ring-rose-500 focus:outline-none placeholder-slate-600">
                    @error('resetConfirmationText')
                        <p class="text-xs text-rose-400 font-bold mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="pt-3 border-t border-white/10 flex justify-end gap-3">
                    <button type="button" wire:click="closeResetModal" class="px-4 py-2.5 border border-white/10 rounded-xl text-slate-300 hover:bg-white/5 font-bold text-xs cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" wire:click="executeSystemReset" wire:confirm="🚨 FINAL SYSTEM CONFIRMATION: Are you 100% sure you want to perform a complete project reset? This will permanently wipe all database tables except Super Admin login credentials and RBAC roles." wire:loading.attr="disabled" class="px-6 py-2.5 bg-gradient-to-r from-rose-600 to-red-700 hover:from-rose-700 hover:to-red-800 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-rose-600/30 transition-all cursor-pointer flex items-center gap-2">
                        <span wire:loading.remove>Execute Factory Reset</span>
                        <span wire:loading class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Wiping System Data...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
