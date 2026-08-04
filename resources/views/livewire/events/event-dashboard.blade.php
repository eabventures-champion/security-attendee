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
                    <div class="p-4 sm:p-5 rounded-2xl border border-blue-500/20 bg-blue-500/5 dark:bg-blue-500/10 space-y-3.5" x-data="{ copied: false, activeCat: 'get_details' }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 border-b border-blue-500/20 pb-3">
                            <span class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span>🌐</span>
                                <span>Public Invitation Link</span>
                            </span>
                            
                            <!-- Category Filter Tabs -->
                            <div class="flex items-center bg-slate-900/80 p-1 rounded-xl border border-blue-500/30">
                                <button type="button" @click="activeCat = 'get_details'" :class="activeCat === 'get_details' ? 'bg-blue-600 text-white font-black' : 'text-blue-300 hover:text-white font-bold'" class="px-2.5 py-1 rounded-lg text-[9px] uppercase tracking-wider transition-all cursor-pointer">
                                    📋 GET DETAILS
                                </button>
                                <button type="button" @click="activeCat = 'no_details'" :class="activeCat === 'no_details' ? 'bg-blue-600 text-white font-black' : 'text-blue-300 hover:text-white font-bold'" class="px-2.5 py-1 rounded-lg text-[9px] uppercase tracking-wider transition-all cursor-pointer">
                                    ⚡ NO DETAILS
                                </button>
                            </div>
                        </div>

                        <!-- Category 1: GET DETAILS View -->
                        <div x-show="activeCat === 'get_details'" class="space-y-3">
                            <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                                <strong class="text-blue-400">Category 1 (GET DETAILS):</strong> Public invitation link for general attendees. Invitees fill in their Name, Email, and Phone number before receiving their pass.
                            </p>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                                <input type="text" readonly value="{{ route('events.public.invite', $event->slug) }}" class="w-full sm:flex-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-3.5 py-2 text-xs text-slate-700 dark:text-slate-300 font-mono select-all truncate">
                                <button @click="navigator.clipboard.writeText('{{ route('events.public.invite', $event->slug) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-md shrink-0 flex items-center justify-center gap-1 cursor-pointer">
                                    <span x-show="!copied">Copy Link</span>
                                    <span x-show="copied" x-cloak class="text-emerald-300">Copied! ✓</span>
                                </button>
                            </div>
                        </div>

                        <!-- Category 2: NO DETAILS View -->
                        <div x-show="activeCat === 'no_details'" class="space-y-3" x-cloak>
                            <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                                <strong class="text-blue-400">Category 2 (NO DETAILS):</strong> Public direct entry link. Form inputs are bypassed so invitees click to instantly claim &amp; download their pass.
                            </p>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                                <input type="text" readonly value="{{ route('events.public.invite', ['event_slug' => $event->slug, 'no_details' => 1]) }}" class="w-full sm:flex-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-3.5 py-2 text-xs text-slate-700 dark:text-slate-300 font-mono select-all truncate">
                                <button @click="navigator.clipboard.writeText('{{ route('events.public.invite', ['event_slug' => $event->slug, 'no_details' => 1]) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-md shrink-0 flex items-center justify-center gap-1 cursor-pointer">
                                    <span x-show="!copied">Copy Link</span>
                                    <span x-show="copied" x-cloak class="text-emerald-300">Copied! ✓</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Secret Private VIP Invitation Link -->
                    <div class="p-4 sm:p-5 rounded-2xl border border-purple-500/30 bg-purple-500/10 space-y-3.5" x-data="{ copied: false, activeCat: 'get_details' }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 border-b border-purple-500/20 pb-3">
                            <span class="text-xs font-black text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span>🔒</span>
                                <span>Secret Private Invitation</span>
                            </span>
                            
                            <!-- Category Filter Tabs -->
                            <div class="flex items-center bg-slate-900/80 p-1 rounded-xl border border-purple-500/30">
                                <button type="button" @click="activeCat = 'get_details'" :class="activeCat === 'get_details' ? 'bg-purple-600 text-white font-black' : 'text-purple-300 hover:text-white font-bold'" class="px-2.5 py-1 rounded-lg text-[9px] uppercase tracking-wider transition-all cursor-pointer">
                                    📋 GET DETAILS
                                </button>
                                <button type="button" @click="activeCat = 'no_details'" :class="activeCat === 'no_details' ? 'bg-purple-600 text-white font-black' : 'text-purple-300 hover:text-white font-bold'" class="px-2.5 py-1 rounded-lg text-[9px] uppercase tracking-wider transition-all cursor-pointer">
                                    ⚡ NO DETAILS
                                </button>
                            </div>
                        </div>

                        <!-- Category 1: GET DETAILS View -->
                        <div x-show="activeCat === 'get_details'" class="space-y-3">
                            <p class="text-xs text-slate-300 font-medium leading-relaxed">
                                <strong class="text-purple-300">Category 1 (GET DETAILS):</strong> Secret private VVIP invitation link. Special guests enter their Name, Email, and Phone before obtaining VVIP pass privileges.
                            </p>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                                <input type="text" readonly value="{{ route('events.public.invite', ['event_slug' => $event->slug, 'vip' => 1]) }}" class="w-full sm:flex-1 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-purple-300 font-mono select-all truncate">
                                <button @click="navigator.clipboard.writeText('{{ route('events.public.invite', ['event_slug' => $event->slug, 'vip' => 1]) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition-all shadow-md shrink-0 flex items-center justify-center gap-1 cursor-pointer">
                                    <span x-show="!copied">Copy Link</span>
                                    <span x-show="copied" x-cloak class="text-emerald-300">Copied! ✓</span>
                                </button>
                            </div>
                        </div>

                        <!-- Category 2: NO DETAILS View -->
                        <div x-show="activeCat === 'no_details'" class="space-y-3" x-cloak>
                            <p class="text-xs text-slate-300 font-medium leading-relaxed">
                                <strong class="text-purple-300">Category 2 (NO DETAILS):</strong> Secret private VVIP direct link. Form inputs are bypassed for instant VVIP pass claiming &amp; download.
                            </p>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                                <input type="text" readonly value="{{ route('events.public.invite', ['event_slug' => $event->slug, 'vip' => 1, 'no_details' => 1]) }}" class="w-full sm:flex-1 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-purple-300 font-mono select-all truncate">
                                <button @click="navigator.clipboard.writeText('{{ route('events.public.invite', ['event_slug' => $event->slug, 'vip' => 1, 'no_details' => 1]) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition-all shadow-md shrink-0 flex items-center justify-center gap-1 cursor-pointer">
                                    <span x-show="!copied">Copy Link</span>
                                    <span x-show="copied" x-cloak class="text-emerald-300">Copied! ✓</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Single-Use Secure Token Invitation Card -->
                    <div class="md:col-span-2 p-4 sm:p-5 rounded-2xl border border-amber-500/30 bg-amber-500/10 space-y-4" x-data="{ copied: false, activeCat: 'get_details' }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 border-b border-amber-500/20 pb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span class="text-xs font-black text-amber-400 uppercase tracking-wider">Single-Use Token Invitation Links</span>
                            </div>
                            
                            <!-- Category Filter Tabs -->
                            <div class="flex items-center bg-slate-900/80 p-1 rounded-xl border border-amber-500/30">
                                <button type="button" @click="activeCat = 'get_details'" :class="activeCat === 'get_details' ? 'bg-amber-500 text-slate-950 font-black' : 'text-amber-300 hover:text-white font-bold'" class="px-3 py-1 rounded-lg text-[10px] uppercase tracking-wider transition-all cursor-pointer">
                                    📋 Category 1: GET DETAILS
                                </button>
                                <button type="button" @click="activeCat = 'no_details'" :class="activeCat === 'no_details' ? 'bg-amber-500 text-slate-950 font-black' : 'text-amber-300 hover:text-white font-bold'" class="px-3 py-1 rounded-lg text-[10px] uppercase tracking-wider transition-all cursor-pointer">
                                    ⚡ Category 2: NO DETAILS
                                </button>
                            </div>
                        </div>

                        <!-- Category 1: GET DETAILS Description & Generators -->
                        <div x-show="activeCat === 'get_details'" class="space-y-3">
                            <p class="text-xs text-slate-300 font-medium leading-relaxed">
                                <strong class="text-amber-300">GET DETAILS Category:</strong> Generates an interactive RSVP link. Invitees fill in their Name, Email, and Phone number before receiving their auto-verified digital pass.
                            </p>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                                <button wire:click="generateSingleUseToken('vvip', false)" type="button" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-xs shadow-md transition-all cursor-pointer text-center flex items-center justify-center gap-1.5">
                                    <span>👑 Generate 1-Time VVIP Link</span>
                                    <span class="text-[9px] bg-black/20 px-1.5 py-0.5 rounded uppercase">(Get Details)</span>
                                </button>
                                <button wire:click="generateSingleUseToken('general_admission', false)" type="button" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer text-center flex items-center justify-center gap-1.5">
                                    <span>🎫 Generate 1-Time General Link</span>
                                    <span class="text-[9px] bg-black/20 px-1.5 py-0.5 rounded uppercase">(Get Details)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Category 2: NO DETAILS Description & Generators -->
                        <div x-show="activeCat === 'no_details'" class="space-y-3" x-cloak>
                            <p class="text-xs text-slate-300 font-medium leading-relaxed">
                                <strong class="text-amber-300">NO DETAILS Category (Direct QR Claim):</strong> Personal details form inputs are completely eliminated. Invitees click the poster image to instantly claim &amp; download their QR pass. Forwarded or repeated attempts are denied.
                            </p>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                                <button wire:click="generateSingleUseToken('vvip', true)" type="button" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-xs shadow-md transition-all cursor-pointer text-center flex items-center justify-center gap-1.5">
                                    <span>⚡ Generate 1-Time VVIP Pass</span>
                                    <span class="text-[9px] bg-black/30 px-1.5 py-0.5 rounded uppercase font-black text-amber-200">(No Details)</span>
                                </button>
                                <button wire:click="generateSingleUseToken('general_admission', true)" type="button" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer text-center flex items-center justify-center gap-1.5">
                                    <span>⚡ Generate 1-Time General Pass</span>
                                    <span class="text-[9px] bg-black/30 px-1.5 py-0.5 rounded uppercase font-black text-slate-400">(No Details)</span>
                                </button>
                            </div>
                        </div>

                        @if($generatedTokenLink)
                            <div class="pt-3 border-t border-amber-500/20 flex flex-col sm:flex-row items-stretch sm:items-center gap-2 animate-fadeIn">
                                <div class="shrink-0 text-[10px] font-black uppercase px-2 py-1 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                    {{ $generatedTokenType ?: 'Generated Link' }}
                                </div>
                                <input type="text" readonly value="{{ $generatedTokenLink }}" class="w-full sm:flex-1 bg-slate-900 border border-amber-500/50 rounded-xl px-3.5 py-2 text-xs text-amber-300 font-mono select-all truncate">
                                
                                <button type="button" @click="navigator.clipboard.writeText('{{ $generatedTokenLink }}'); copied = true; setTimeout(() => copied = false, 2000)" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs transition-all shrink-0 flex items-center justify-center gap-1 cursor-pointer">
                                    <span x-show="!copied">Copy Link</span>
                                    <span x-show="copied" x-cloak class="text-slate-950">Copied! ✓</span>
                                </button>

                                <a href="{{ $generatedTokenWhatsappUrl }}" target="_blank" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition-all shrink-0 flex items-center justify-center gap-1.5 cursor-pointer shadow-md">
                                    <svg class="w-4 h-4 fill-current text-white" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.754zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    <span>Share WhatsApp</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- BATCH BULK WHATSAPP LINK DISPATCHER (FOR MANY PEOPLE) -->
                    <div class="md:col-span-2 p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-emerald-500/30 bg-gradient-to-br from-emerald-950/40 via-slate-900/90 to-teal-950/40 backdrop-blur-xl shadow-2xl space-y-5" x-data="{ batchCopied: false }">
                        
                        <!-- Header Section -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-emerald-500/20 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm sm:text-base font-black text-white tracking-wide">Batch WhatsApp Link Dispatcher</h3>
                                    <p class="text-[11px] text-slate-400 font-medium">Generate &amp; send individual 1-time single-use links to multiple guests via WhatsApp</p>
                                </div>
                            </div>
                            <span class="self-start md:self-auto text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-3 py-1 rounded-full uppercase tracking-wider shrink-0">
                                🛡️ Single-Use Protection
                            </span>
                        </div>

                        <!-- Config Controls Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 items-end bg-slate-950/60 p-4 rounded-2xl border border-white/5">
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-300 uppercase tracking-wider mb-1.5">Quantity (Links)</label>
                                <input type="number" wire:model.live="batchQuantity" min="1" max="50" class="w-full bg-slate-900 border border-emerald-500/40 rounded-xl px-3.5 py-2.5 text-xs text-emerald-300 font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-300 uppercase tracking-wider mb-1.5">Pass Category</label>
                                <select wire:model.live="batchCategory" class="w-full bg-slate-900 border border-emerald-500/40 rounded-xl px-3.5 py-2.5 text-xs text-emerald-300 font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all">
                                    <option value="no_details">⚡ Category 2: NO DETAILS (Direct Pass)</option>
                                    <option value="get_details">📋 Category 1: GET DETAILS (RSVP Form)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-300 uppercase tracking-wider mb-1.5">Access Role</label>
                                <select wire:model.live="batchRole" class="w-full bg-slate-900 border border-emerald-500/40 rounded-xl px-3.5 py-2.5 text-xs text-emerald-300 font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all">
                                    <option value="vvip">👑 VVIP Pass</option>
                                    <option value="general_admission">🎫 General Admission</option>
                                </select>
                            </div>
                            <div>
                                <button wire:click="generateBatchTokens" type="button" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition-all cursor-pointer flex items-center justify-center gap-1.5 active:scale-95">
                                    <svg class="w-4 h-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Generate {{ $batchQuantity }} Links</span>
                                </button>
                            </div>
                        </div>

                        <!-- Multi-Contact WhatsApp Dispatch Card -->
                        @if(count($batchLinks) > 0)
                            <div class="pt-4 border-t border-emerald-500/20 space-y-4 animate-fadeIn">
                                <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-emerald-950/90 via-slate-950 to-teal-950/90 border border-emerald-500/50 shadow-2xl space-y-3.5">
                                    
                                    <!-- Header Row (Single neat flex line on mobile) -->
                                    <div class="flex items-center justify-between gap-2 border-b border-emerald-500/20 pb-3">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center shrink-0 shadow-md">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current text-emerald-400" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.754zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="text-xs sm:text-sm font-extrabold text-white tracking-wide truncate">WhatsApp Dispatch Ready</h4>
                                                <p class="text-[10px] sm:text-xs text-emerald-300/80 font-medium truncate">{{ count($batchLinks) }} Unique Passes</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-wider text-emerald-300 bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-500/40">
                                                ✓ {{ count($batchLinks) }} Generated
                                            </span>
                                            <button type="button" wire:click="clearBatchLinks" class="p-1 rounded-lg text-rose-400 hover:text-rose-300 transition-colors cursor-pointer" title="Clear Batch">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <p class="text-[11px] sm:text-xs text-slate-300 font-medium leading-relaxed">
                                        Opens WhatsApp with all <strong class="text-white font-bold">{{ count($batchLinks) }} unique 1-time pass links</strong> pre-loaded. Select your contacts or group to send in 1 click!
                                    </p>

                                    <!-- Action Buttons Grid (Full width & clean text on Mobile) -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                        <button type="button" @click="navigator.clipboard.writeText(`{{ $batchBulkMessageText }}`); batchCopied = true; setTimeout(() => batchCopied = false, 2000)" class="w-full py-2.5 px-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 text-xs font-extrabold uppercase tracking-wider border border-emerald-500/40 shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                                            <span x-show="!batchCopied">📋 Copy All {{ count($batchLinks) }} Passes (Bulk)</span>
                                            <span x-show="batchCopied" x-cloak class="text-emerald-400">Copied All! ✓</span>
                                        </button>

                                        <a href="{{ $batchWhatsappBulkUrl }}" target="_blank" class="w-full py-2.5 px-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-1.5 cursor-pointer transition-all active:scale-95">
                                            <svg class="w-4 h-4 fill-current text-white shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.754zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            <span>💬 Share All in 1 Message</span>
                                        </a>
                                    </div>

                                    <!-- Individual Pass Links Section (Send 1-by-1 to Separate Contacts) -->
                                    <div class="pt-4 border-t border-emerald-500/20 space-y-3" x-data="{ showIndividual: true }">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-black text-emerald-300 uppercase tracking-wider flex items-center gap-1.5">
                                                <span>📲</span>
                                                <span>Send Links Individually (1-by-1 to 5 Contacts)</span>
                                            </span>
                                            <button type="button" @click="showIndividual = !showIndividual" class="text-[10px] text-emerald-400 hover:text-emerald-300 underline font-bold cursor-pointer">
                                                <span x-show="!showIndividual">Show List ▼</span>
                                                <span x-show="showIndividual">Hide List ▲</span>
                                            </button>
                                        </div>
                                        <p class="text-[11px] text-slate-400 font-medium">To send each contact <strong>only their own 1 link</strong> (without seeing other guests' links), click <strong>"Send to Contact"</strong> next to each pass line one at a time:</p>

                                        <div x-show="showIndividual" class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                                            @foreach($batchLinks as $bLink)
                                                <div class="p-3 rounded-xl bg-slate-900/90 border border-emerald-500/30 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2" x-data="{ copiedItem: false }">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shrink-0">
                                                            Pass #{{ $bLink['id'] }}
                                                        </span>
                                                        <input type="text" readonly value="{{ $bLink['link'] }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1 text-[11px] text-slate-300 font-mono select-all truncate">
                                                    </div>
                                                    <div class="flex items-center gap-1.5 shrink-0 justify-end">
                                                        <button type="button" @click="navigator.clipboard.writeText('{{ $bLink['link'] }}'); copiedItem = true; setTimeout(() => copiedItem = false, 2000)" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold transition-all border border-slate-700 cursor-pointer">
                                                            <span x-show="!copiedItem">Copy</span>
                                                            <span x-show="copiedItem" x-cloak class="text-emerald-400">Copied!</span>
                                                        </button>
                                                        <a href="{{ $bLink['whatsapp_url'] }}" target="_blank" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-extrabold transition-all shadow flex items-center gap-1 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 fill-current text-white shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.754zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                            <span>Send to Contact</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
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
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-white/10 pb-3">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📡</span>
                        <span>Recent Gate Activity</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Real-time scan logs across all gates</p>
                </div>
                @if(count($recentScans) > 0)
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shrink-0 flex items-center gap-1 animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>LIVE</span>
                    </span>
                @endif
            </div>

            <div class="space-y-3">
                @forelse($recentScans as $scan)
                    @php
                        $scanResultStr = is_object($scan->scan_result) ? $scan->scan_result->value : (string)$scan->scan_result;
                        $isGranted = strtolower($scanResultStr) === 'granted';
                        $att = $scan->attendee;
                        $roleStr = $att && is_object($att->access_role) ? $att->access_role->value : ($att->access_role ?? 'general_admission');
                        $isVipRole = in_array(strtolower((string)$roleStr), ['vvip', 'vip']);
                    @endphp
                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-slate-900/80 space-y-2 hover:border-blue-500/30 transition-all shadow-sm">
                        <!-- Top Row: Attendee Name & Result Badge -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <p class="text-xs sm:text-sm font-black text-slate-900 dark:text-white truncate">
                                        {{ $att->full_name ?? 'Anonymous Guest' }}
                                    </p>
                                    @if($isVipRole)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase">
                                            👑 VVIP
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase">
                                            🎫 PASS
                                        </span>
                                    @endif
                                </div>
                                @if($att && ($att->email || $att->phone))
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono truncate">
                                        {{ $att->phone ?: $att->email }}
                                    </p>
                                @endif
                            </div>

                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider shrink-0 flex items-center gap-1 {{ $isGranted ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                                <span>{{ $isGranted ? '✓' : '⛔' }}</span>
                                <span>{{ ucfirst($scanResultStr) }}</span>
                            </span>
                        </div>

                        <!-- Bottom Row: Gate Name, Scanner Operator & Timestamp -->
                        <div class="pt-2 border-t border-slate-200/50 dark:border-white/5 flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400">
                            <span class="truncate font-semibold text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                <span>{{ $scan->gate->name ?? 'Main Checkpoint' }}</span>
                            </span>

                            <span class="font-mono text-slate-400 shrink-0" title="{{ $scan->scanned_at ? $scan->scanned_at->format('M d, Y g:i:s A') : '' }}">
                                {{ $scan->scanned_at ? $scan->scanned_at->format('g:i A') : ($scan->created_at ? $scan->created_at->format('g:i A') : '') }}
                                @if($scan->scanned_at)
                                    ({{ $scan->scanned_at->diffForHumans(null, true) }} ago)
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-400 font-medium text-xs space-y-1">
                        <p class="font-bold text-slate-300">No scans recorded yet</p>
                        <p class="text-[11px] text-slate-500">Live gate activity will appear here as passes are scanned.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
