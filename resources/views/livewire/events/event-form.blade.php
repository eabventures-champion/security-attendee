<div class="space-y-8 max-w-4xl mx-auto font-inter">
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
    <div class="mb-6 sm:mb-8 text-center animate-fadeInUp">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $eventUuid ? 'Edit Event' : 'Create New Event' }}</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-1.5 font-medium text-xs sm:text-sm">Fill in the details to configure your event parameters.</p>
    </div>

    <!-- Step Indicators -->
    <div class="mb-10 relative animate-fadeInUp" style="animation-delay: 0.1s">
        <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-200 dark:bg-white/10 -translate-y-1/2 rounded-full"></div>
        <div class="absolute top-1/2 left-0 h-1 bg-gradient-to-r from-blue-600 to-emerald-500 -translate-y-1/2 rounded-full transition-all duration-500" style="width: {{ (($currentStep - 1) / ($totalSteps - 1)) * 100 }}%"></div>
        
        <div class="relative flex justify-between">
            @for ($i = 1; $i <= $totalSteps; $i++)
                <button type="button" wire:click="setStep({{ $i }})" class="flex flex-col items-center focus:outline-none group cursor-pointer">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $currentStep >= $i ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30' : 'bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/20 text-slate-500 dark:text-slate-400 group-hover:border-blue-400' }}">
                        {{ $i }}
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $currentStep >= $i ? 'text-blue-600 dark:text-blue-400' : 'text-slate-500 dark:text-slate-400' }}">
                        @if($i == 1) Basic Info
                        @elseif($i == 2) Schedule
                        @elseif($i == 3) Venue
                        @else Settings @endif
                    </span>
                </button>
            @endfor
        </div>
    </div>

    <!-- Restored Draft Banner Card -->
    @if($isRestoredDraft)
        <div class="mb-8 p-5 sm:p-6 rounded-2xl bg-blue-500/10 dark:bg-blue-500/10 border border-blue-500/30 backdrop-blur-xl shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-fadeIn">
            <div class="flex items-center gap-3">
                <span class="flex h-3 w-3 relative shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
                <div>
                    <h4 class="text-xs font-bold text-blue-400 uppercase tracking-wider">Draft Restored & Auto-Saved</h4>
                    <p class="text-xs font-semibold text-slate-200 mt-0.5">
                        Restored your event draft from previous session (Step {{ $currentStep }}). Auto-saved!
                    </p>
                </div>
            </div>
            <button wire:click="startFresh" type="button" class="shrink-0 px-4 py-2 rounded-xl bg-slate-800/80 hover:bg-rose-500/20 text-slate-300 hover:text-rose-400 border border-slate-700 hover:border-rose-500/30 text-xs font-bold transition-all cursor-pointer">
                Discard Draft & Start New
            </button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl p-8 relative overflow-hidden animate-fadeInUp" style="animation-delay: 0.2s">
        
        <form wire:submit.prevent="save">
            
            <!-- Step 1: Basic Info -->
            @if($currentStep === 1)
                <div class="space-y-6 animate-fadeInUp">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Basic Information</h3>
                    
                    <!-- Cover Image Upload Box -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Event Cover Image</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                            <div class="w-full sm:w-48 h-32 rounded-xl bg-slate-200 dark:bg-slate-800 overflow-hidden relative group shrink-0 border border-slate-300 dark:border-white/10 flex items-center justify-center">
                                <!-- Loading Spinner during upload -->
                                <div wire:loading wire:target="cover_image" class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center gap-2 p-2">
                                    <svg class="animate-spin h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-[10px] text-blue-300 font-bold uppercase tracking-wider">Uploading...</span>
                                </div>

                                @if ($cover_image)
                                    @php
                                        $tempUrl = null;
                                        try {
                                            $tempUrl = $cover_image->temporaryUrl();
                                        } catch (\Exception $e) {
                                            $tempUrl = null;
                                        }
                                    @endphp
                                    @if ($tempUrl)
                                        <img src="{{ $tempUrl }}" onerror="this.onerror=null; this.src='https://placehold.co/600x400?text=Preview+Error';" class="w-full h-full object-cover rounded-xl">
                                    @else
                                        <div class="text-center p-3">
                                            <span class="text-xl">🖼️</span>
                                            <span class="text-[11px] text-emerald-400 font-bold block mt-1">Image Selected</span>
                                            <span class="text-[9px] text-slate-400 block font-medium">Ready to save</span>
                                        </div>
                                    @endif
                                @elseif ($existing_cover_image_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($existing_cover_image_path) }}" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');" class="w-full h-full object-cover">
                                    <div class="hidden text-center p-3">
                                        <svg class="w-8 h-8 text-slate-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-[11px] text-slate-400 font-medium">No Image Uploaded</span>
                                    </div>
                                @else
                                    <div class="text-center p-3">
                                        <svg class="w-8 h-8 text-slate-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-[11px] text-slate-400 font-medium">No Image Uploaded</span>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-2 flex-1">
                                <input type="file" wire:model="cover_image" accept="image/*" id="cover_image_input" class="hidden">
                                <label for="cover_image_input" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-xs shadow-md shadow-blue-500/25 transition-all inline-flex items-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Choose Cover Image
                                </label>
                                <p class="text-xs text-slate-500 dark:text-slate-400">PNG, JPG, or WEBP up to 3MB. Recommended resolution: 1200x630.</p>
                                <div wire:loading wire:target="cover_image" class="text-xs text-blue-400 font-medium animate-pulse">Uploading image preview...</div>
                                @error('cover_image') <span class="text-rose-500 text-xs block font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Event Name <span class="text-rose-500">*</span></label>
                        <input wire:model.live.debounce.300ms="name" type="text" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium" placeholder="e.g. Annual Tech Conference 2026 or Sam weds Tabitha">
                        @error('name') <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Typography / Font Selection -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Event Title Typography (Font Style)
                                </label>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Select a font style to match your event theme (Weddings, Corporate, Tech, Galas, etc.)</p>
                            </div>
                            @if($this->isSuperAdmin)
                                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center gap-1">
                                    👑 Super Admin Access: All 5 Fonts Unlocked
                                </span>
                            @elseif($this->hasFullFontAccess)
                                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                                    ✨ Premium Typography Pack Active: All 5 Fonts Unlocked
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-500/10 text-blue-400 border border-blue-500/30 flex items-center gap-1">
                                    ⚡ Standard Tier: 2 Free Fonts Unlocked
                                </span>
                            @endif
                        </div>

                        @if(session()->has('subscription_request'))
                            <div class="p-3.5 rounded-xl bg-purple-500/20 border border-purple-500/40 text-purple-200 text-xs font-bold animate-fadeIn">
                                {{ session('subscription_request') }}
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <!-- Alex Brush (FREE) -->
                            <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all {{ $title_font === 'Alex Brush' ? 'bg-blue-500/10 border-blue-500 ring-2 ring-blue-500/20' : 'bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20' }}">
                                <input type="radio" wire:model.live="title_font" value="Alex Brush" class="sr-only">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Alex Brush</span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-pink-500/10 text-pink-400 border border-pink-500/20">Script (Standard)</span>
                                </div>
                                <span class="text-2xl text-blue-400 font-normal mt-1 line-clamp-1" style="font-family: 'Alex Brush', cursive;">
                                    {{ $name ?: 'Sam weds Tabitha' }}
                                </span>
                                <span class="text-[10px] text-slate-400 mt-2">Weddings, Galas, Special Invitations</span>
                            </label>

                            <!-- Outfit (FREE) -->
                            <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all {{ $title_font === 'Outfit' ? 'bg-blue-500/10 border-blue-500 ring-2 ring-blue-500/20' : 'bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20' }}">
                                <input type="radio" wire:model.live="title_font" value="Outfit" class="sr-only">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Outfit</span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">Modern Tech (Standard)</span>
                                </div>
                                <span class="text-lg text-blue-400 font-bold tracking-tight mt-1 line-clamp-1" style="font-family: 'Outfit', sans-serif;">
                                    {{ $name ?: 'Tech Summit 2026' }}
                                </span>
                                <span class="text-[10px] text-slate-400 mt-2">Startups, Tech, Corporate Expos</span>
                            </label>

                            <!-- Playfair Display (PREMIUM) -->
                            @if($this->hasFullFontAccess)
                                <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all {{ $title_font === 'Playfair Display' ? 'bg-blue-500/10 border-blue-500 ring-2 ring-blue-500/20' : 'bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20' }}">
                                    <input type="radio" wire:model.live="title_font" value="Playfair Display" class="sr-only">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">Playfair Display</span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Luxury Serif</span>
                                    </div>
                                    <span class="text-lg text-blue-400 font-bold italic mt-1 line-clamp-1" style="font-family: 'Playfair Display', serif;">
                                        {{ $name ?: 'Executive Leadership' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 mt-2">Executive Seminars & Conferences</span>
                                </label>
                            @else
                                <div wire:click="requestFontSubscription('Playfair Display')" class="relative flex flex-col p-4 rounded-xl border border-amber-500/30 bg-slate-900/60 opacity-80 hover:opacity-100 hover:border-amber-500 transition-all cursor-pointer group shadow-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-300">Playfair Display</span>
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1">
                                            🔒 PRO Subscription
                                        </span>
                                    </div>
                                    <span class="text-lg text-slate-400 font-bold italic mt-1 line-clamp-1" style="font-family: 'Playfair Display', serif;">
                                        {{ $name ?: 'Executive Leadership' }}
                                    </span>
                                    <div class="mt-2 pt-2 border-t border-white/5 flex items-center justify-between">
                                        <span class="text-[10px] text-amber-400 font-bold">Request Premium Access</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-amber-500 text-slate-950 font-extrabold">Upgrade</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Cinzel (PREMIUM) -->
                            @if($this->hasFullFontAccess)
                                <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all {{ $title_font === 'Cinzel' ? 'bg-blue-500/10 border-blue-500 ring-2 ring-blue-500/20' : 'bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20' }}">
                                    <input type="radio" wire:model.live="title_font" value="Cinzel" class="sr-only">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">Cinzel</span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">Classic Roman</span>
                                    </div>
                                    <span class="text-base text-blue-400 font-bold uppercase tracking-wider mt-1 line-clamp-1" style="font-family: 'Cinzel', serif;">
                                        {{ $name ?: 'Grand Awards Gala' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 mt-2">Formal Ceremonies, Awards, Banquets</span>
                                </label>
                            @else
                                <div wire:click="requestFontSubscription('Cinzel')" class="relative flex flex-col p-4 rounded-xl border border-amber-500/30 bg-slate-900/60 opacity-80 hover:opacity-100 hover:border-amber-500 transition-all cursor-pointer group shadow-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-300">Cinzel</span>
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1">
                                            🔒 PRO Subscription
                                        </span>
                                    </div>
                                    <span class="text-base text-slate-400 font-bold uppercase tracking-wider mt-1 line-clamp-1" style="font-family: 'Cinzel', serif;">
                                        {{ $name ?: 'Grand Awards Gala' }}
                                    </span>
                                    <div class="mt-2 pt-2 border-t border-white/5 flex items-center justify-between">
                                        <span class="text-[10px] text-amber-400 font-bold">Request Premium Access</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-amber-500 text-slate-950 font-extrabold">Upgrade</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Space Grotesk (PREMIUM) -->
                            @if($this->hasFullFontAccess)
                                <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all {{ $title_font === 'Space Grotesk' ? 'bg-blue-500/10 border-blue-500 ring-2 ring-blue-500/20' : 'bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20' }}">
                                    <input type="radio" wire:model.live="title_font" value="Space Grotesk" class="sr-only">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">Space Grotesk</span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20">Futuristic</span>
                                    </div>
                                    <span class="text-lg text-blue-400 font-bold mt-1 line-clamp-1" style="font-family: 'Space Grotesk', sans-serif;">
                                        {{ $name ?: 'Hackathon & Design' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 mt-2">Hackathons, Creative Arts, Youth</span>
                                </label>
                            @else
                                <div wire:click="requestFontSubscription('Space Grotesk')" class="relative flex flex-col p-4 rounded-xl border border-amber-500/30 bg-slate-900/60 opacity-80 hover:opacity-100 hover:border-amber-500 transition-all cursor-pointer group shadow-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-300">Space Grotesk</span>
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1">
                                            🔒 PRO Subscription
                                        </span>
                                    </div>
                                    <span class="text-lg text-slate-400 font-bold mt-1 line-clamp-1" style="font-family: 'Space Grotesk', sans-serif;">
                                        {{ $name ?: 'Hackathon & Design' }}
                                    </span>
                                    <div class="mt-2 pt-2 border-t border-white/5 flex items-center justify-between">
                                        <span class="text-[10px] text-amber-400 font-bold">Request Premium Access</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-amber-500 text-slate-950 font-extrabold">Upgrade</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Description</label>
                        <textarea wire:model.live.debounce.300ms="description" rows="5" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium custom-scrollbar" placeholder="Describe your event..."></textarea>
                    </div>
                </div>
            @endif

            <!-- Step 2: Schedule -->
            @if($currentStep === 2)
                <div class="space-y-6 animate-fadeInUp">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Date & Time</h3>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input wire:model.live="is_multi_day" type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded border-slate-300 dark:border-white/20 bg-slate-100 dark:bg-white/5 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <span class="text-slate-700 dark:text-slate-300 font-semibold">This is a multi-day event</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Starts At <span class="text-rose-500">*</span></label>
                            <input wire:model.live="starts_at" type="datetime-local" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                            @error('starts_at') <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Ends At</label>
                            <input wire:model.live="ends_at" type="datetime-local" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                            @error('ends_at') <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 3: Venue -->
            @if($currentStep === 3)
                <div class="space-y-6 animate-fadeInUp">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Venue Details</h3>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Venue Name</label>
                        <input wire:model.live="venue_name" type="text" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium" placeholder="e.g. Grand Convention Center or Online">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Address</label>
                        <input wire:model.live="venue_address" type="text" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium" placeholder="Street address">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">City</label>
                            <input wire:model.live="venue_city" type="text" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Country</label>
                            <input wire:model.live="venue_country" type="text" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 4: Settings -->
            @if($currentStep === 4)
                <div class="space-y-6 animate-fadeInUp">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Event Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Total Capacity</label>
                            <input wire:model.live="capacity" type="number" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium" placeholder="Leave blank for unlimited">
                            @error('capacity') <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Registration Deadline</label>
                            <input wire:model.live="registration_deadline" type="datetime-local" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-6 p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input wire:model.live="is_free" type="radio" value="1" class="form-radio h-5 w-5 text-blue-600 border-slate-300 dark:border-white/20 focus:ring-blue-500">
                            <span class="text-slate-700 dark:text-slate-300 font-semibold">Free Event</span>
                        </label>
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input wire:model.live="is_free" type="radio" value="0" class="form-radio h-5 w-5 text-blue-600 border-slate-300 dark:border-white/20 focus:ring-blue-500">
                            <span class="text-slate-700 dark:text-slate-300 font-semibold">Paid Event</span>
                        </label>
                    </div>

                    <!-- Event Privacy Selection -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Event Visibility & Privacy</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 transition-all cursor-pointer {{ !$is_private ? 'border-blue-500 bg-blue-500/5 dark:bg-blue-500/10' : 'border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                        Public Event
                                    </span>
                                    <input type="radio" wire:model.live="is_private" :value="false" class="form-radio text-blue-600 focus:ring-blue-500">
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Visible on public website catalog (/public-events). Open to public registration.</p>
                            </label>

                            <label class="relative flex flex-col p-4 rounded-2xl border-2 transition-all cursor-pointer {{ $is_private ? 'border-purple-500 bg-purple-500/5 dark:bg-purple-500/10' : 'border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Private Event
                                    </span>
                                    <input type="radio" wire:model.live="is_private" :value="true" class="form-radio text-purple-600 focus:ring-purple-500">
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Hidden from public website catalog. Accessible only via private invite link.</p>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Initial Status</label>
                        <select wire:model.live="status" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                            <option value="draft">Draft (Internal Setup Only — Inactive Link)</option>
                            <option value="published">Published (Active & Ready for RSVPs / Invites)</option>
                        </select>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">
                            For private events, publishing activates the private invitation link so invited guests can confirm RSVPs.
                        </p>
                    </div>

                    <!-- Invitation Customization (Badge Title & Subtitle Message) -->
                    <div class="pt-6 border-t border-slate-200 dark:border-white/10 space-y-4">
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Invitation Page Customization</h4>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-blue-500/10 text-blue-500 border border-blue-500/20">Custom Branding</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Customize the highlighted badge title and message description shown on your event invitation page for guests.</p>

                        <!-- Invitation Heading Title / Badge -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Invitation Badge / Heading Title
                            </label>
                            <input type="text"
                                   wire:model.live="invitation_title"
                                   placeholder="e.g. PRIVATE VVIP INVITATION (Default: 🔒 PRIVATE VVIP INVITATION)"
                                   class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium text-sm">
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Replaces the highlighted badge title above the invitation poster card.</p>
                        </div>

                        <!-- Invitation Subtitle / Message -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Invitation Subtitle / Description Message
                            </label>
                            <textarea wire:model.live="invitation_description"
                                      rows="3"
                                      placeholder="e.g. You have received an exclusive private VVIP invitation directly from the event organizers..."
                                      class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium text-sm"></textarea>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Leave empty to use default invitation welcome text.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Navigation & Save Buttons -->
            <div class="mt-10 pt-6 border-t border-slate-100 dark:border-white/10 flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center space-x-3">
                    @if($currentStep > 1)
                        <button type="button" wire:click="previousStep" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-white/20 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-semibold text-sm transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Previous Step
                        </button>
                    @else
                        <a href="{{ route('events.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-white/20 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-semibold text-sm transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Back to Events
                        </a>
                    @endif
                </div>

                <div class="flex items-center space-x-3">
                    @if($eventUuid)
                        <!-- Edit mode: Quick Save Changes button available at any step -->
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span wire:loading.remove>Save Changes</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Saving...
                            </span>
                        </button>
                    @endif

                    @if($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 cursor-pointer">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    @else
                        <!-- Create mode or Final Step: Save Event button -->
                        @if(!$eventUuid)
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2 cursor-pointer">
                                <span wire:loading.remove>Save Event</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Saving...
                                </span>
                            </button>
                        @endif
                    @endif
                </div>
            </div>

        </form>
    </div>
</div>
