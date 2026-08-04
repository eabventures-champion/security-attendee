<div class="w-full max-w-7xl mx-auto space-y-6 sm:space-y-10 font-inter pt-2 sm:pt-4">
    <!-- Hero Title -->
    <div class="text-center space-y-3 sm:space-y-4 max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Public Invitation Portal
        </div>
        <h1 class="text-2xl sm:text-3xl md:text-5xl font-black text-white tracking-tight">
            Explore & Attend <span class="bg-gradient-to-r from-blue-400 via-indigo-400 to-emerald-400 bg-clip-text text-transparent">Upcoming Events</span>
        </h1>
        <p class="text-slate-400 text-xs sm:text-base md:text-lg font-medium">
            Browse verified public events, request your attendee pass, and receive your secure QR entry ticket upon organization approval.
        </p>
    </div>

    <!-- Filters & Search -->
    <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-2xl p-4 shadow-2xl flex flex-col md:flex-row gap-4 justify-between items-center">
        <!-- Search Input -->
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search events by title, venue, or keyword..." class="w-full pl-10 pr-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
        </div>

        <!-- Filter Pills -->
        <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
            <button wire:click="$set('typeFilter', '')" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer {{ $typeFilter === '' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-slate-900/60 text-slate-400 border border-slate-700 hover:text-white' }}">
                All Events
            </button>
            <button wire:click="$set('typeFilter', 'free')" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer {{ $typeFilter === 'free' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-900/60 text-slate-400 border border-slate-700 hover:text-white' }}">
                Free Access
            </button>
            <button wire:click="$set('typeFilter', 'paid')" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer {{ $typeFilter === 'paid' ? 'bg-purple-600 text-white shadow-md shadow-purple-500/30' : 'bg-slate-900/60 text-slate-400 border border-slate-700 hover:text-white' }}">
                Paid Events
            </button>
        </div>
    </div>

    <!-- Notice Banner -->
    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-start gap-3 text-slate-300 text-xs font-medium">
        <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <strong class="text-amber-400 font-semibold">How Public Invitations Work:</strong> Interested attendees can submit a registration for any event. Once submitted, your registration undergoes verification by the organization admin team. You will receive email updates and your digital QR pass upon approval.
        </div>
    </div>

    <!-- Event Catalog Grid -->
    <div wire:loading.delay class="w-full flex justify-center py-12">
        <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
    </div>

    <div wire:loading.remove.delay>
        @if($events->isEmpty())
            <div class="bg-slate-800/50 border border-slate-700/60 rounded-2xl p-12 text-center shadow-xl">
                <svg class="w-16 h-16 mx-auto text-slate-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                @if(!empty($search) || !empty($typeFilter))
                    <h3 class="text-xl font-bold text-white mb-2">No public events match your search</h3>
                    <p class="text-slate-400 text-sm max-w-md mx-auto">Try adjusting your search keyword or switching your filter preference.</p>
                @else
                    <h3 class="text-xl font-bold text-white mb-2">No Public Events Available Yet</h3>
                    <p class="text-slate-400 text-sm max-w-md mx-auto">There are currently no public events published in the system. Check back soon for newly published events!</p>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($events as $evt)
                    @php
                        $registeredCount = $evt->attendees()->count();
                        $isFull = $evt->capacity && $registeredCount >= $evt->capacity;
                    @endphp
                    <div class="bg-slate-800/60 backdrop-blur-xl border border-slate-700/60 hover:border-blue-500/50 rounded-2xl overflow-hidden flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 group shadow-xl">
                        <!-- Cover Image Banner -->
                        <div class="h-36 w-full bg-slate-900 overflow-hidden relative border-b border-slate-700/40">
                            @if($evt->cover_image_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($evt->cover_image_path) }}" alt="{{ $evt->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-900/40 via-indigo-900/30 to-purple-900/40 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif

                            <div class="absolute top-2.5 left-2.5 right-2.5 flex justify-between items-start pointer-events-none">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider backdrop-blur-md shadow-md {{ $evt->is_free ? 'bg-emerald-500/80 text-white' : 'bg-purple-500/80 text-white' }}">
                                    {{ $evt->is_free ? 'Free Event' : 'Paid Event' }}
                                </span>

                                @if(($evt->settings['default_entry_mode'] ?? 'details') === 'no_details')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-600/90 text-white border border-purple-400/40 backdrop-blur-md shadow-md" title="Direct Claim Pass Mode (No form details required)">
                                        ⚡ Direct Claim
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-600/90 text-white border border-amber-400/40 backdrop-blur-md shadow-md" title="Form Entry Mode (Name, Email & Phone required)">
                                        📋 Form Entry
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Top Info -->
                        <div class="p-4 space-y-3">
                            <div>
                                <h3 class="text-base font-bold text-white group-hover:text-blue-400 transition-colors line-clamp-1">
                                    {{ $evt->name }}
                                </h3>
                                <p class="text-slate-400 text-xs font-medium line-clamp-2 mt-1">
                                    {{ $evt->description ?? 'No description provided.' }}
                                </p>
                            </div>

                            <!-- Meta Details -->
                            <div class="space-y-1.5 pt-2 border-t border-slate-700/40 text-[11px] text-slate-300 font-medium">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>{{ $evt->starts_at ? $evt->starts_at->format('M j, Y — g:i A') : 'Date TBA' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    <span class="line-clamp-1">{{ $evt->venue_name ? $evt->venue_name . ($evt->venue_city ? ', ' . $evt->venue_city : '') : 'Online Event' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action & Progress Footer -->
                        <div class="px-4 py-3 bg-slate-900/60 border-t border-slate-700/40 space-y-2.5">
                            <!-- Capacity progress -->
                            <div class="flex justify-between items-center text-[11px] font-semibold">
                                <span class="text-slate-400">Capacity</span>
                                <span class="{{ $isFull ? 'text-rose-400' : 'text-slate-300' }}">
                                    {{ $registeredCount }} / {{ $evt->capacity ? $evt->capacity : 'Unlimited' }}
                                </span>
                            </div>

                            @if($isFull)
                                <button disabled class="w-full py-2 rounded-lg bg-slate-800 text-slate-500 font-semibold text-xs text-center cursor-not-allowed">
                                    Event Fully Booked
                                </button>
                            @else
                                <a href="{{ route('events.public.invite', $evt->slug) }}" class="block w-full py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs text-center shadow-md shadow-blue-500/20 transition-all">
                                    Confirm Attendance →
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($events->hasPages())
                <div class="mt-8">
                    {{ $events->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        @endif
    </div>
</div>
