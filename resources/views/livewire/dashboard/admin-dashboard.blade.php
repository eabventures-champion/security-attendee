<div class="space-y-8 font-inter">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center animate-fadeInUp" style="animation-delay: 0.1s">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-emerald-400">
                Welcome back, {{ auth()->user()->name ?? 'Admin' }}
            </h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1 text-sm font-medium">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            @if (session()->has('stats_refreshed'))
                <span x-data="{ show: true }" 
                      x-init="setTimeout(() => show = false, 3000)" 
                      x-show="show" 
                      x-transition:leave="transition ease-in duration-300"
                      x-transition:leave-start="opacity-100 scale-100"
                      x-transition:leave-end="opacity-0 scale-95"
                      class="text-xs text-emerald-400 font-semibold bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/20 flex items-center gap-1.5 animate-fadeInUp">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('stats_refreshed') }}
                </span>
            @endif

            @if(auth()->user()->hasRole('organization_admin'))
                <button type="button" wire:click="toggleWelcomeGuide" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/40 text-xs font-bold transition-all shadow-sm cursor-pointer">
                    📖 Workspace Guide
                </button>
            @endif

            <button wire:click="refreshStats" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-white/5 hover:bg-slate-50 dark:hover:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-sm cursor-pointer active:scale-95" title="Refresh Dashboard Stats">
                <svg wire:loading.class="animate-spin text-blue-400" class="w-4 h-4 text-slate-500 dark:text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Refresh Stats</span>
            </button>
        </div>
    </div>

    <!-- Flash Notifications (Factory Reset / System Messages) -->
    @if (session()->has('message') || session()->has('reset_success') || session()->has('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 text-sm font-semibold flex items-center justify-between shadow-xl shadow-emerald-500/10 animate-fadeInUp">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <h4 class="font-extrabold text-white text-sm">System Reset Complete</h4>
                    <p class="text-xs text-emerald-300/90 font-medium">{{ session('message') ?? session('reset_success') ?? session('success') }}</p>
                </div>
            </div>
            <button type="button" @click="show = false" class="p-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @php
        $isOrgAdmin = auth()->user()->hasRole('organization_admin');
        $org = auth()->user()->organization;
    @endphp

    @if($isOrgAdmin && $showWelcomeGuide)
        <!-- Organization Admin Welcome Note & Navigation Guide -->
        <div class="bg-gradient-to-r from-blue-900/40 via-indigo-900/40 to-purple-900/40 border border-blue-500/30 backdrop-blur-2xl rounded-3xl p-5 sm:p-8 shadow-2xl space-y-6 animate-fadeInUp relative">
            <!-- Absolute Top Right Close 'X' Button -->
            <button type="button" wire:click="closeWelcomeGuide" class="absolute top-4 right-4 sm:top-6 sm:right-6 p-2 rounded-xl bg-white/10 hover:bg-rose-500/30 text-slate-300 hover:text-white transition-all border border-white/10 cursor-pointer flex items-center justify-center group shadow-md z-10" title="Close Workspace Guide">
                <svg class="w-4 h-4 text-slate-300 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="flex flex-col sm:flex-row items-start gap-4 pr-8 sm:pr-12">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-black text-lg sm:text-xl flex items-center justify-center shadow-lg shadow-blue-500/30 shrink-0">
                    {{ strtoupper(substr($org->name ?? 'OA', 0, 2)) }}
                </div>
                <div class="space-y-1.5 min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg sm:text-2xl font-black text-white leading-snug">Welcome to AttendFlow, {{ auth()->user()->name }}! 🎉</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase tracking-widest inline-block">
                            ACTIVE WORKSPACE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed">
                        Your organization workspace <strong class="text-white font-bold">{{ $org->name ?? 'Organization' }}</strong> is fully active. Follow this step-by-step navigation guide to manage your events, attendees, entry gates, scanners, and team.
                    </p>
                </div>
            </div>

            <!-- Navigation Guide Steps Grid -->
            <div class="pt-4 border-t border-white/10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Step 1: Events -->
                <div class="bg-slate-900/80 border border-white/10 rounded-2xl p-5 space-y-3 hover:border-blue-500/50 transition-all group">
                    <div class="flex items-center justify-between">
                        <div class="p-2.5 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-500/30 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded-full border border-blue-500/20">STEP 1</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-base">1. Create & Manage Events</h4>
                        <p class="text-xs text-slate-400 leading-relaxed mt-1">Set up upcoming events, configure venue locations, capacities, dates, and customize invitation forms.</p>
                    </div>
                    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-blue-400 hover:text-blue-300 group-hover:translate-x-1 transition-all">
                        Manage Events ➔
                    </a>
                </div>

                <!-- Step 2: Attendees -->
                <div class="bg-slate-900/80 border border-white/10 rounded-2xl p-5 space-y-3 hover:border-purple-500/50 transition-all group">
                    <div class="flex items-center justify-between">
                        <div class="p-2.5 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded-full border border-purple-500/20">STEP 2</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-base">2. Register & Verify Attendees</h4>
                        <p class="text-xs text-slate-400 leading-relaxed mt-1">Add attendees, assign roles (VVIP, VIP, Speaker, Guest), issue QR passes, and verify registrations.</p>
                    </div>
                    <a href="{{ route('attendees.index') }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-purple-400 hover:text-purple-300 group-hover:translate-x-1 transition-all">
                        Manage Attendees ➔
                    </a>
                </div>

                <!-- Step 3: Gates -->
                <div class="bg-slate-900/80 border border-white/10 rounded-2xl p-5 space-y-3 hover:border-emerald-500/50 transition-all group">
                    <div class="flex items-center justify-between">
                        <div class="p-2.5 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">STEP 3</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-base">3. Setup Entry Gates</h4>
                        <p class="text-xs text-slate-400 leading-relaxed mt-1">Configure venue entry checkpoints, map gates to specific events, and assign security officers.</p>
                    </div>
                    <a href="{{ route('gates.index') }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-emerald-400 hover:text-emerald-300 group-hover:translate-x-1 transition-all">
                        Configure Gates ➔
                    </a>
                </div>

                <!-- Step 4: Scanner -->
                <div class="bg-slate-900/80 border border-white/10 rounded-2xl p-5 space-y-3 hover:border-amber-500/50 transition-all group">
                    <div class="flex items-center justify-between">
                        <div class="p-2.5 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full border border-amber-500/20">STEP 4</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-base">4. Launch QR Scanner</h4>
                        <p class="text-xs text-slate-400 leading-relaxed mt-1">Open live QR scanner dashboard on mobile or desktop to scan and grant access to attendees instantly.</p>
                    </div>
                    <a href="{{ route('scanner.index') }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-amber-400 hover:text-amber-300 group-hover:translate-x-1 transition-all">
                        Launch Scanner ➔
                    </a>
                </div>

                <!-- Step 5: Team Management -->
                <div class="bg-slate-900/80 border border-white/10 rounded-2xl p-5 space-y-3 hover:border-cyan-500/50 transition-all group md:col-span-2 lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <div class="p-2.5 rounded-xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-full border border-cyan-500/20">STEP 5</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-base">5. Invite Team Members & Assign Privileges</h4>
                        <p class="text-xs text-slate-400 leading-relaxed mt-1">Invite team members (Event Managers, Gate Security, Check-in Staff) under your organization, assign RBAC roles, and manage their gate assignments.</p>
                    </div>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-cyan-400 hover:text-cyan-300 group-hover:translate-x-1 transition-all">
                        Manage Workspace Team ➔
                    </a>
                </div>
            </div>
        </div>
    @endif

    @php
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin') || $user->email === 'superadmin@attendflow.com';
        $isSecurity = $user->hasRole('security') || $user->hasRole('gate_security') || $user->hasRole('security_officer');
        $breakdownBtnLabel = $isSuperAdmin ? 'Org Breakdown ➔' : 'Event Breakdown ➔';
    @endphp

    @if($isSecurity)
        <!-- Security Officer Quick Gate & Scanner Access Card (Mobile Best Viewed) -->
        <div class="bg-gradient-to-br from-blue-950 via-slate-900 to-indigo-950 border border-blue-500/30 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-2xl space-y-4 animate-fadeInUp">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-xl sm:rounded-2xl bg-blue-500/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/10">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <div class="space-y-1 min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-black text-blue-400 uppercase tracking-widest bg-blue-500/10 px-2.5 py-0.5 rounded-full border border-blue-500/20 inline-block">GATE SECURITY ACCESS</span>
                        <h3 class="text-base sm:text-lg font-extrabold text-white leading-snug">Live Entry Gate & Scanner Station</h3>
                    </div>
                </div>
            </div>

            <!-- Assigned Gate Details Badge Box -->
            <div class="p-3 sm:p-4 rounded-xl bg-slate-950/70 border border-white/10 flex items-center justify-between gap-2">
                <div class="text-xs sm:text-sm text-slate-300 min-w-0">
                    @if($user->assignedGate)
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-slate-400 font-medium">🔒 Assigned Gate:</span>
                            <span class="text-emerald-400 font-extrabold text-xs sm:text-sm">{{ $user->assignedGate->name }}</span>
                            @if($user->assignedGate->event)
                                <span class="text-slate-400 text-xs font-semibold">({{ $user->assignedGate->event->name }})</span>
                            @endif
                        </div>
                    @else
                        <span class="text-slate-400 italic">No gate explicitly assigned yet. Contact your Org Admin.</span>
                    @endif
                </div>
            </div>

            <!-- Launch QR Scanner Button (Full width on Mobile) -->
            @if($user->assignedGate && $user->assignedGate->event)
                <a href="{{ route('scanner.gate', ['eventUuid' => $user->assignedGate->event->uuid, 'gateUuid' => $user->assignedGate->uuid]) }}" class="w-full sm:w-auto py-3 px-6 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs sm:text-sm shadow-xl shadow-blue-500/25 flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
                    <svg class="w-4 h-4 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Launch QR Scanner ➔</span>
                </a>
            @endif
        </div>
    @endif

    <!-- Stat Cards Row (Dynamic Cols based on Role) -->
    <div class="grid grid-cols-2 {{ $isSecurity ? 'lg:grid-cols-3' : ($isSuperAdmin ? 'xl:grid-cols-5 md:grid-cols-3' : 'lg:grid-cols-4') }} gap-3 sm:gap-4">
        @if(!$isSecurity)
            <!-- Total Events Card -->
            <button type="button" wire:click="openBreakdown('events')" class="text-left w-full bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 hover:border-blue-500/50 hover:ring-2 hover:ring-blue-500/20 rounded-2xl shadow-sm dark:shadow-2xl p-3 sm:p-4 flex items-center justify-between animate-fadeInUp cursor-pointer transition-all hover:-translate-y-0.5 group" style="animation-delay: 0.2s" title="Click to view Breakdown">
                <div class="flex items-center min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 mr-2.5 sm:mr-3 group-hover:scale-110 transition-transform shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Events</p>
                        <h3 class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5" wire:key="stat-events">{{ $totalEvents }}</h3>
                        <span class="text-[9px] sm:text-[10px] font-extrabold text-blue-500 dark:text-blue-400 group-hover:underline mt-0.5 block truncate">{{ $breakdownBtnLabel }}</span>
                    </div>
                </div>
            </button>
        @endif
        
        <!-- Total Registrations Card -->
        <button type="button" wire:click="openBreakdown('registrations')" class="text-left w-full bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 hover:border-purple-500/50 hover:ring-2 hover:ring-purple-500/20 rounded-2xl shadow-sm dark:shadow-2xl p-3 sm:p-4 flex items-center justify-between animate-fadeInUp cursor-pointer transition-all hover:-translate-y-0.5 group" style="animation-delay: 0.3s" title="Click to view Breakdown">
            <div class="flex items-center min-w-0">
                <div class="p-2 sm:p-2.5 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 mr-2.5 sm:mr-3 group-hover:scale-110 transition-transform shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Registrations</p>
                    <h3 class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $totalRegistrations }}</h3>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-purple-500 dark:text-purple-400 group-hover:underline mt-0.5 block truncate">{{ $breakdownBtnLabel }}</span>
                </div>
            </div>
        </button>

        <!-- Verified Attendees Card -->
        <button type="button" wire:click="openBreakdown('verified')" class="text-left w-full bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 hover:border-emerald-500/50 hover:ring-2 hover:ring-emerald-500/20 rounded-2xl shadow-sm dark:shadow-2xl p-3 sm:p-4 flex items-center justify-between animate-fadeInUp cursor-pointer transition-all hover:-translate-y-0.5 group" style="animation-delay: 0.4s" title="Click to view Breakdown">
            <div class="flex items-center min-w-0">
                <div class="p-2 sm:p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mr-2.5 sm:mr-3 group-hover:scale-110 transition-transform shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Verified</p>
                    <h3 class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $verifiedAttendees }}</h3>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-emerald-500 dark:text-emerald-400 group-hover:underline mt-0.5 block truncate">{{ $breakdownBtnLabel }}</span>
                </div>
            </div>
        </button>

        <!-- Checked In Today Card -->
        <button type="button" wire:click="openBreakdown('checked_in')" class="text-left w-full bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 hover:border-amber-500/50 hover:ring-2 hover:ring-amber-500/20 rounded-2xl shadow-sm dark:shadow-2xl p-3 sm:p-4 flex items-center justify-between animate-fadeInUp cursor-pointer transition-all hover:-translate-y-0.5 group" style="animation-delay: 0.5s" title="Click to view Breakdown">
            <div class="flex items-center min-w-0">
                <div class="p-2 sm:p-2.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 mr-2.5 sm:mr-3 group-hover:scale-110 transition-transform shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Checked In</p>
                    <h3 class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $checkedInToday }}</h3>
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-amber-500 dark:text-amber-400 group-hover:underline mt-0.5 block truncate">{{ $breakdownBtnLabel }}</span>
                </div>
            </div>
        </button>

        @if($isSuperAdmin)
            <!-- VIP Pricing Waitlist Stat Card -->
            <div class="bg-gradient-to-br from-amber-500/10 to-orange-500/10 backdrop-blur-xl border border-amber-500/30 rounded-2xl shadow-sm dark:shadow-2xl p-3 sm:p-4 flex items-center justify-between animate-fadeInUp group" style="animation-delay: 0.55s">
                <div class="flex items-center min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-amber-500/20 text-amber-400 mr-2.5 sm:mr-3 group-hover:scale-110 transition-transform shrink-0 border border-amber-500/30">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] sm:text-[11px] font-bold text-amber-400 uppercase tracking-wider truncate">VIP Waitlist</p>
                        <h3 class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $pricingWaitlistCount }}</h3>
                        <span class="text-[9px] sm:text-[10px] font-extrabold text-amber-400/90 mt-0.5 block truncate">Early Access Signups</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Charts Section -->
    @php
        $chartData = $chart ?? ['labels' => [], 'data' => [], 'max' => 5];
        $labels = $chartData['labels'];
        $data = $chartData['data'];
        $maxVal = max($chartData['max'], 1);

        $totalReg = max($totalRegistrations, 1);
        $pctVerified = round(($verifiedAttendees / $totalReg) * 100);
        $pctPending = round(($pendingVerifications / $totalReg) * 100);
        $pctRejected = round(($rejectedCount / $totalReg) * 100);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @if(!$isSecurity)
            <!-- Registration Trend -->
            <div class="lg:col-span-2 bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl p-6 animate-fadeInUp" style="animation-delay: 0.6s">
                <div class="flex justify-between items-start sm:items-center gap-2 mb-6">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Registration Trend</h3>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">Daily registration activity (Last 15 Days)</p>
                    </div>
                    <span class="text-[10px] sm:text-xs font-semibold px-2.5 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-full border border-blue-500/20 whitespace-nowrap shrink-0">Live Activity</span>
                </div>

                <!-- Dynamic Bar & Trend Chart -->
                <div class="h-64 relative flex items-end justify-between gap-1 sm:gap-2 pt-8 pb-6 px-1 sm:px-2 border-b border-slate-200 dark:border-white/10 overflow-hidden">
                    @foreach($data as $idx => $val)
                        @php
                            $heightPct = min(100, max(8, round(($val / $maxVal) * 100)));
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative cursor-pointer min-w-0">
                            <!-- Tooltip -->
                            <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-xl pointer-events-none whitespace-nowrap z-20 border border-white/10">
                                {{ $labels[$idx] }}: {{ $val }} {{ Str::plural('reg', $val) }}
                            </div>
                            
                            <div class="w-full max-w-[28px] bg-gradient-to-t from-blue-600 to-indigo-500 group-hover:from-blue-500 group-hover:to-purple-500 rounded-t-lg transition-all shadow-md group-hover:shadow-blue-500/30" style="height: {{ $heightPct }}%"></div>
                            
                            <span class="text-[9px] font-medium text-slate-400 mt-2 text-center w-full flex flex-col items-center leading-tight">
                                <span class="text-[7.5px] text-slate-500 uppercase font-semibold hidden sm:block">{{ substr($labels[$idx], 0, 3) }}</span>
                                <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-300">{{ substr($labels[$idx], 4) }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-medium">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-gradient-to-r from-blue-600 to-indigo-500"></span>
                        <span>Daily Attendee Registrations</span>
                    </div>
                    <span>Peak: {{ max($data) }} registrations/day</span>
                </div>
            </div>
        @endif

        <!-- Verification Breakdown -->
        <div class="{{ $isSecurity ? 'lg:col-span-3' : '' }} bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl p-6 animate-fadeInUp flex flex-col justify-between" style="animation-delay: 0.7s">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Verification Status</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pre-event check status distribution</p>
                </div>
            </div>

            <!-- Donut Visual Ring -->
            <div class="relative flex items-center justify-center my-4">
                <div class="w-44 h-44 rounded-full flex items-center justify-center relative shadow-inner" style="background: conic-gradient(#10b981 0% {{ $pctVerified }}%, #f59e0b {{ $pctVerified }}% {{ $pctVerified + $pctPending }}%, #f43f5e {{ $pctVerified + $pctPending }}% 100%);">
                    <div class="w-32 h-32 rounded-full bg-white dark:bg-slate-900 flex flex-col items-center justify-center shadow-xl border border-slate-200 dark:border-white/10">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalRegistrations }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Attendees</span>
                    </div>
                </div>
            </div>

            <!-- Legend Status Cards -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>Verified</span>
                    </div>
                    <span>{{ number_format($verifiedAttendees) }} ({{ $pctVerified }}%)</span>
                </div>
                
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs font-bold text-amber-600 dark:text-amber-400">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span>Pending</span>
                    </div>
                    <span>{{ number_format($pendingVerifications) }} ({{ $pctPending }}%)</span>
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs font-bold text-rose-600 dark:text-rose-400">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span>Rejected</span>
                    </div>
                    <span>{{ number_format($rejectedCount) }} ({{ $pctRejected }}%)</span>
                </div>
            </div>
        </div>
    </div>

    @if(!$isSecurity)
        <!-- Upcoming Events Section -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl p-6 animate-fadeInUp" style="animation-delay: 0.8s">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Upcoming Events</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Overview of scheduled events</p>
                </div>
                <a href="{{ route('events.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-semibold">View All Events →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="py-3 px-4">Event Name</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Venue</th>
                            <th class="py-3 px-4">Capacity</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm text-slate-700 dark:text-slate-300">
                        @forelse($upcomingEvents ?? [] as $event)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">{{ $event->name }}</td>
                                <td class="py-3.5 px-4 text-xs font-medium">{{ $event->starts_at ? $event->starts_at->format('M j, Y g:i A') : 'TBD' }}</td>
                                <td class="py-3.5 px-4 text-xs font-medium">{{ $event->venue_name ?? 'Online' }}</td>
                                <td class="py-3.5 px-4 text-xs font-medium">{{ $event->capacity ?? 'Unlimited' }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        {{ ucfirst($event->status->value ?? 'Published') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('events.show', $event->uuid) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold">Manage →</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 font-medium">
                                    No upcoming events scheduled. Click "Create Event" to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($isSuperAdmin)
        <!-- Super Admin VIP Pricing Waitlist Section -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xl space-y-6 animate-fadeInUp">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center font-bold text-xl shrink-0">
                        ⭐
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">VIP Pricing Waitlist</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                {{ number_format($pricingWaitlistCount) }} Subscribers
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Early access interest submissions from the landing page.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <div class="relative min-w-[200px]">
                        <input type="text" 
                               wire:model.live.debounce.300ms="waitlistSearch" 
                               placeholder="Search subscriber email..." 
                               class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <select wire:model.live="waitlistStatusFilter" class="px-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-semibold outline-none cursor-pointer">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="notified">Notified</option>
                        <option value="converted">Converted</option>
                    </select>

                    <button type="button" wire:click="exportWaitlistCsv" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Export CSV</span>
                    </button>
                </div>
            </div>

            <!-- Waitlist Subscribers Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider bg-slate-50 dark:bg-slate-950/50">
                            <th class="py-3 px-4">Subscriber Email</th>
                            <th class="py-3 px-4">Date Joined</th>
                            <th class="py-3 px-4">IP Address</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($pricingWaitlistSubscribers as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-300 font-black text-xs flex items-center justify-center border border-amber-500/30">
                                        {{ strtoupper(substr($item->email, 0, 1)) }}
                                    </div>
                                    <span>{{ $item->email }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $item->created_at ? $item->created_at->format('M j, Y g:i A') : 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                    {{ $item->ip_address ?? '127.0.0.1' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($item->status === 'converted')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 uppercase">Converted</span>
                                    @elseif($item->status === 'notified')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/20 text-blue-400 border border-blue-500/30 uppercase">Notified</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-400 border border-amber-500/30 uppercase">Pending</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-1">
                                    <button type="button" wire:click="openSendVipEmailModal({{ $item->id }})" class="px-2.5 py-1 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-[10px] font-extrabold border border-amber-500/30 transition-all cursor-pointer inline-flex items-center gap-1 shadow-sm">
                                        ✉️ Send VIP Email
                                    </button>
                                    @if($item->status !== 'notified')
                                        <button type="button" wire:click="updateWaitlistStatus({{ $item->id }}, 'notified')" class="px-2 py-1 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 text-[10px] font-bold border border-blue-500/20 transition-all cursor-pointer">
                                            Mark Notified
                                        </button>
                                    @endif
                                    @if($item->status !== 'converted')
                                        <button type="button" wire:click="updateWaitlistStatus({{ $item->id }}, 'converted')" class="px-2 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/20 transition-all cursor-pointer">
                                            Mark Converted
                                        </button>
                                    @endif
                                    <button type="button" wire:click="deleteWaitlistEntry({{ $item->id }})" wire:confirm="Are you sure you want to remove this waitlist entry?" class="px-2 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-[10px] font-bold border border-rose-500/20 transition-all cursor-pointer" title="Delete Entry">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 font-medium italic">
                                    No VIP waitlist entries found. Submissions from the landing page will appear here!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Organization Admin Metric Breakdown Modal -->
    @if($showBreakdownModal)
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-3 sm:p-4 pt-8 sm:pt-4 overflow-y-auto bg-slate-950/80 backdrop-blur-md animate-fadeInUp">
            <div class="bg-slate-900 border border-white/10 rounded-2xl sm:rounded-3xl w-full max-w-2xl shadow-2xl p-4 sm:p-6 space-y-4 sm:space-y-5 my-4 sm:my-auto">
                <div class="flex items-start justify-between border-b border-white/10 pb-3.5 sm:pb-4 gap-3">
                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                        <div class="p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-blue-500/20 text-blue-400 border border-blue-500/30 shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase tracking-widest inline-block">
                                {{ $isSuperAdmin ? 'SUPER ADMIN BREAKDOWN' : 'WORKSPACE EVENT BREAKDOWN' }}
                            </span>
                            <h3 class="text-sm sm:text-lg font-black text-white mt-1 leading-snug truncate">{{ $breakdownTitle }}</h3>
                        </div>
                    </div>
                    <button type="button" wire:click="closeBreakdown" class="p-1.5 sm:p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white transition-colors cursor-pointer shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="max-h-80 sm:max-h-96 overflow-y-auto custom-scrollbar">
                    <table class="w-full text-left text-xs table-fixed">
                        <thead>
                            <tr class="bg-slate-950 border-b border-white/10 text-slate-400 font-extrabold uppercase tracking-wider text-[9px] sm:text-xs sticky top-0 z-10">
                                <th class="py-2.5 px-2 sm:px-4 w-[40%]">{{ $isSuperAdmin ? 'Organization Workspace' : 'Event Name' }}</th>
                                <th class="py-2.5 px-2 sm:px-4 w-[40%]">{{ $isSuperAdmin ? 'Organization Admin' : 'Venue & Schedule' }}</th>
                                <th class="py-2.5 px-2 sm:px-4 text-right w-[20%]">Metric</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($breakdownData as $row)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="py-3 px-2 sm:px-4">
                                        <div class="font-extrabold text-white text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2 min-w-0">
                                            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-purple-500/20 text-purple-300 font-black flex items-center justify-center text-[9px] sm:text-xs shrink-0">
                                                {{ strtoupper(substr($row['org_name'] ?? 'Org', 0, 2)) }}
                                            </div>
                                            <span class="truncate block text-xs sm:text-sm">{{ $row['org_name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-2 sm:px-4 min-w-0">
                                        <div class="font-bold text-slate-200 text-xs truncate block">{{ $row['admin_name'] }}</div>
                                        <div class="text-slate-400 text-[9px] sm:text-[11px] truncate block">{{ $row['admin_email'] }}</div>
                                    </td>
                                    <td class="py-3 px-2 sm:px-4 text-right font-black text-xs sm:text-sm text-blue-400 whitespace-nowrap">
                                        <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg sm:rounded-xl bg-blue-500/10 border border-blue-500/30">
                                            {{ number_format($row['count']) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-slate-500 italic text-xs">No organization admin data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-3 border-t border-white/10 flex justify-center sm:justify-end">
                    <button type="button" wire:click="closeBreakdown" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm cursor-pointer transition-all text-center">
                        Close Breakdown
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Send VIP Discount Email Modal -->
    @if($showSendVipEmailModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fadeInUp">
            <div class="bg-slate-900 border border-amber-500/30 rounded-3xl w-full max-w-xl shadow-2xl p-6 space-y-5 my-auto">
                <div class="flex items-start justify-between border-b border-white/10 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30">
                            ✉️
                        </div>
                        <div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase tracking-widest inline-block">
                                DIRECT EMAIL DISPATCH
                            </span>
                            <h3 class="text-lg font-black text-white mt-1">Send VIP Early Access &amp; Promo Code</h3>
                        </div>
                    </div>
                    <button type="button" wire:click="closeSendVipEmailModal" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white transition-colors cursor-pointer">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="sendVipEmail" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Recipient Email</label>
                        <input type="email" wire:model="emailTargetAddress" readonly class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 text-xs font-mono font-bold outline-none cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Email Subject</label>
                        <input type="text" wire:model="emailSubject" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-semibold focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">50% Off Promo Code</label>
                        <input type="text" wire:model="emailPromoCode" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-amber-500/40 text-amber-400 text-sm font-mono font-black tracking-widest focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Custom Invitation Message</label>
                        <textarea wire:model="emailCustomMessage" rows="4" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-normal focus:ring-2 focus:ring-amber-500 outline-none leading-relaxed" placeholder="Write custom note to subscriber..."></textarea>
                    </div>

                    <div class="pt-3 border-t border-white/10 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeSendVipEmailModal" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs cursor-pointer transition-all">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-slate-950 font-black text-xs shadow-lg transition-all flex items-center gap-2 cursor-pointer active:scale-95">
                            <span wire:loading.remove>🚀 Dispatch Direct VIP Email</span>
                            <span wire:loading class="flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Sending Mail...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
