<div class="space-y-6 font-inter">
    @php
        $statusStr = is_object($event->status) ? $event->status->value : (string)$event->status;
    @endphp

    <!-- Back to Events Button -->
    <div class="animate-fadeInUp">
        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/10 text-sm font-semibold transition-all group shadow-sm dark:shadow-2xl">
            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Events
        </a>
    </div>

    <!-- Header -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-900/95 to-indigo-950/40 border border-slate-200 dark:border-white/10 rounded-3xl p-5 sm:p-8 relative overflow-hidden animate-fadeInUp shadow-2xl space-y-5">
        <div class="absolute right-0 top-0 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
            <div class="space-y-3 w-full md:w-auto">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border
                        {{ $statusStr === 'published' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : ($statusStr === 'cancelled' ? 'bg-rose-500/20 text-rose-300 border-rose-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30') }}">
                        ● {{ strtoupper($statusStr) }}
                    </span>
                    @if($event->is_private)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase tracking-wider">
                            🔒 PRIVATE EVENT
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase tracking-wider">
                            🌐 PUBLIC EVENT
                        </span>
                    @endif
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-800 text-slate-300 border border-slate-700 uppercase tracking-wider">
                        {{ $event->is_free ? 'FREE' : 'PAID' }}
                    </span>
                </div>

                <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-white tracking-tight leading-tight" style="font-family: {{ $event->title_css_font_family }};">
                    {{ $event->name }}
                </h1>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-5 text-xs sm:text-sm text-slate-300 font-medium">
                    <span class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> 
                        </div>
                        <span>{{ $event->starts_at ? $event->starts_at->format('M d, Y • g:i A') : 'Date TBA' }}</span>
                    </span>
                    <span class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg> 
                        </div>
                        <span>{{ $event->venue_name ?? 'Online / Venue TBD' }}</span>
                    </span>
                </div>
            </div>

            <!-- Mobile-Friendly Action Buttons -->
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2.5 w-full md:w-auto pt-2 md:pt-0">
                <a href="{{ route('reports.index', $event->uuid) }}" class="px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800/80 hover:bg-slate-700 text-white text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> 
                    <span>Export Reports</span>
                </a>
                <a href="{{ route('events.edit', $event->uuid) }}" class="px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800/80 hover:bg-slate-700 text-white text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> 
                    <span>Edit Event</span>
                </a>
                @if($statusStr === 'archived')
                    <button wire:click="unarchiveEvent" class="px-3.5 py-2.5 rounded-xl border border-amber-500/30 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Unarchive</span>
                    </button>
                @else
                    <button wire:click="archiveEvent" class="px-3.5 py-2.5 rounded-xl border border-amber-500/30 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span>Archive</span>
                    </button>
                @endif
                <button wire:click="deleteEvent" wire:confirm="Are you sure you want to delete this event?" class="px-3.5 py-2.5 rounded-xl border border-rose-500/30 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Delete</span>
                </button>
                @if($statusStr !== 'published')
                    <button wire:click="publishEvent" class="col-span-2 sm:col-span-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs shadow-lg shadow-blue-500/30 transition-all cursor-pointer text-center">
                        🚀 Publish Event
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 sm:p-6 shadow-sm dark:shadow-2xl animate-fadeInUp" style="animation-delay: 0.1s">
            <p class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Registrations</p>
            <h3 class="text-xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1 sm:mt-2">{{ $event->attendees()->count() }}</h3>
            <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-slate-100 dark:border-white/10">
                <div class="flex justify-between text-[10px] sm:text-xs font-semibold mb-1">
                    <span class="text-slate-500 dark:text-slate-400">Capacity</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $event->capacity ? round(($event->attendees()->count() / $event->capacity) * 100) . '%' : 'Unlimited' }}</span>
                </div>
                @if($event->capacity)
                <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600 rounded-full" style="width: {{ min(100, ($event->attendees()->count() / $event->capacity) * 100) }}%"></div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 sm:p-6 shadow-sm dark:shadow-2xl animate-fadeInUp" style="animation-delay: 0.2s">
            <p class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Verified Attendees</p>
            <h3 class="text-xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1 sm:mt-2">{{ $event->attendees()->where('verification_status', \App\Enums\VerificationStatus::Verified)->count() }}</h3>
            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1.5 font-medium truncate">Pre-event verified</p>
        </div>

        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 sm:p-6 shadow-sm dark:shadow-2xl animate-fadeInUp" style="animation-delay: 0.3s">
            <p class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Checked In</p>
            <h3 class="text-xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mt-1 sm:mt-2">{{ $event->checkIns()->where('scan_result', \App\Enums\ScanResult::Granted)->count() }}</h3>
            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1.5 font-medium truncate">Current attendance</p>
        </div>

        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 sm:p-6 shadow-sm dark:shadow-2xl animate-fadeInUp" style="animation-delay: 0.4s">
            <p class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Configured Gates</p>
            <h3 class="text-xl sm:text-3xl font-black text-purple-600 dark:text-purple-400 mt-1 sm:mt-2">{{ $event->gates()->count() }}</h3>
            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1.5 font-medium truncate">Entry checkpoints</p>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fadeInUp" style="animation-delay: 0.5s">
        <!-- Quick Access links -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Invitation & Access Links -->
            <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Invitation Links</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 font-medium">Share public registration or special private invitation links with attendees.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Public Invitation Link -->
                    <div class="p-4 sm:p-5 rounded-2xl border border-blue-500/20 bg-blue-500/5 dark:bg-blue-500/10 space-y-3" x-data="{ copied: false }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                            <span class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span>🌐</span>
                                <span>Public Invitation Link</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/20 text-blue-600 dark:text-blue-300 border border-blue-500/30 w-max">
                                STANDARD ACCESS
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                            Public invitation link for general attendees. Displays invitation layout and confirms pass.
                        </p>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                            <input type="text" readonly value="{{ route('events.public.invite', $event->slug) }}" class="w-full sm:flex-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-3.5 py-2 text-xs text-slate-700 dark:text-slate-300 font-mono select-all truncate">
                            <button @click="navigator.clipboard.writeText('{{ route('events.public.invite', $event->slug) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-md shrink-0 flex items-center justify-center gap-1 cursor-pointer">
                                <span x-show="!copied">Copy Link</span>
                                <span x-show="copied" x-cloak class="text-emerald-300">Copied! ✓</span>
                            </button>
                        </div>
                    </div>

                    <!-- Secret Private VIP Invitation Link -->
                    <div class="p-4 sm:p-5 rounded-2xl border border-purple-500/30 bg-purple-500/10 space-y-3" x-data="{ copied: false }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                            <span class="text-xs font-black text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span>🔒</span>
                                <span>Secret Private Invitation</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30 w-max">
                                VVIP RSVP
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 font-medium leading-relaxed">
                            Secret private VVIP invitation link for special guests. Grants VVIP access privileges & pre-verified pass.
                        </p>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                            <input type="text" readonly value="{{ route('events.public.invite', ['event_slug' => $event->slug, 'vip' => 1]) }}" class="w-full sm:flex-1 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-purple-300 font-mono select-all truncate">
                            <button @click="navigator.clipboard.writeText('{{ route('events.public.invite', ['event_slug' => $event->slug, 'vip' => 1]) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition-all shadow-md shrink-0 flex items-center justify-center gap-1 cursor-pointer">
                                <span x-show="!copied">Copy Link</span>
                                <span x-show="copied" x-cloak class="text-emerald-300">Copied! ✓</span>
                            </button>
                        </div>
                    </div>

                    <!-- Single-Use Secure Token Invitation Card -->
                    <div class="md:col-span-2 p-4 sm:p-5 rounded-2xl border border-amber-500/30 bg-amber-500/10 space-y-3" x-data="{ copied: false }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                            <span class="text-xs font-black text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>Single-Use Token Invitation Link</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/40 w-max">
                                1-TIME PASS TOKEN
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 font-medium leading-relaxed">
                            Generates a unique 1-time token link (for WhatsApp or Personal sharing). First person to register gets auto-verified; forwarded copies downgrade to Pending Verification.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                            <button wire:click="generateSingleUseToken('vvip')" type="button" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-xs shadow-md transition-all cursor-pointer text-center">
                                Generate 1-Time VVIP Link
                            </button>
                            <button wire:click="generateSingleUseToken('general_admission')" type="button" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer text-center">
                                Generate 1-Time General Link
                            </button>
                        </div>

                        @if($generatedTokenLink)
                            <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-2 animate-fadeIn">
                                <input type="text" readonly value="{{ $generatedTokenLink }}" class="w-full sm:flex-1 bg-slate-900 border border-amber-500/50 rounded-xl px-3.5 py-2 text-xs text-amber-300 font-mono select-all truncate">
                                <button @click="navigator.clipboard.writeText('{{ $generatedTokenLink }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shrink-0 flex items-center justify-center gap-1 cursor-pointer">
                                    <span x-show="!copied">Copy Token Link</span>
                                    <span x-show="copied" x-cloak class="text-emerald-200">Copied! ✓</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Management Modules</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('attendees.index', $event->uuid) }}" class="p-4 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">Attendees</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Manage registrations & verifications</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('gates.index', $event->uuid) }}" class="p-4 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">Gates & Access</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Configure entry checkpoints</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('scanner.index', $event->uuid) }}" class="p-4 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">Scanner Dashboard</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Launch QR checking interface</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('reports.index', $event->uuid) }}" class="p-4 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">Reports & Export</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Download CSV and PDF reports</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar Activity -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Recent Gate Activity</h3>
            <div class="space-y-4">
                <div class="text-center py-10 text-slate-400 font-medium text-sm">
                    <p>No scans recorded yet.</p>
                </div>
            </div>
        </div>
    </div>
</div>
