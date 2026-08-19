<div class="min-h-screen bg-slate-950 text-slate-100 py-10 px-4 font-inter selection:bg-blue-500 selection:text-white">
    <div class="max-w-xl mx-auto w-full space-y-6">
        
        <!-- Header & Branding -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center gap-3 p-2 rounded-2xl bg-white/10 border border-white/20 shadow-lg mb-1">
                @if(!empty($main_logo_url))
                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shrink-0 shadow-md border border-white/80 overflow-hidden">
                        <img src="{{ $main_logo_url }}" alt="Main Logo" class="w-full h-full object-cover" title="Main Logo">
                    </div>
                @endif
                @if(!empty($association_logo_url))
                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shrink-0 shadow-md border border-white/80 overflow-hidden">
                        <img src="{{ $association_logo_url }}" alt="Association Logo" class="w-full h-full object-cover" title="Association Logo">
                    </div>
                @endif
                @if(empty($main_logo_url) && empty($association_logo_url))
                    <div class="w-12 h-12 rounded-xl bg-emerald-600/20 text-emerald-400 flex items-center justify-center font-black text-xl">
                        🪪
                    </div>
                @endif
            </div>

            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[11px] font-black tracking-wider uppercase border border-blue-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Official Membership Registration
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    {{ $organization->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 max-w-md mx-auto">
                    Fill out your credentials below to generate and receive your official verified Digital Virtual ID Card.
                </p>
            </div>
        </div>

        @if(!$submitted)
            <!-- Registration Form Card -->
            <div class="rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-8 shadow-2xl shadow-black/60 space-y-6">
                
                <form wire:submit.prevent="submitApplication" class="space-y-5 text-xs">
                    
                    <!-- Profile Photo with Silhouette Preview -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-800/60 border border-white/5">
                        <div class="w-16 h-20 rounded-xl border-2 border-blue-500/40 bg-slate-950 overflow-hidden shrink-0 flex items-center justify-center shadow-md">
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                            @else
                                <div class="flex flex-col items-center justify-center text-slate-500">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                    <span class="text-[8px] font-bold uppercase mt-0.5">Silhouette</span>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1 flex-1">
                            <label class="block font-bold text-slate-200">Upload Profile Photo</label>
                            <input type="file" wire:model="photo" accept="image/*" class="text-xs text-slate-400 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
                            <p class="text-[11px] text-slate-500">Optional. A modern profile silhouette will be used if left blank.</p>
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Full Legal Name *</label>
                        <input type="text" wire:model="full_name" required placeholder="e.g. Kwame Mensah" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                        @error('full_name') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Designation Selection (Member / Executive) -->
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Designation *</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border {{ $designation === 'member' ? 'border-blue-500 bg-blue-500/15 text-white ring-1 ring-blue-500/40' : 'border-white/10 bg-slate-800/80 text-slate-300 hover:border-white/20' }} cursor-pointer transition-all">
                                <input type="radio" wire:model.live="designation" value="member" class="text-blue-600 focus:ring-blue-500 h-4 w-4">
                                <div>
                                    <span class="font-black text-xs block">👤 Member</span>
                                    <span class="text-[10px] text-slate-400">Regular member</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border {{ $designation === 'executive' ? 'border-amber-500 bg-amber-500/15 text-white ring-1 ring-amber-500/40' : 'border-white/10 bg-slate-800/80 text-slate-300 hover:border-white/20' }} cursor-pointer transition-all">
                                <input type="radio" wire:model.live="designation" value="executive" class="text-amber-500 focus:ring-amber-500 h-4 w-4">
                                <div>
                                    <span class="font-black text-xs block text-amber-400">⭐ Executive</span>
                                    <span class="text-[10px] text-slate-400">Leadership / Officer</span>
                                </div>
                            </label>
                        </div>
                        @error('designation') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Executive Exact Position Field (Shown only if Executive is chosen) -->
                    @if($designation === 'executive')
                        <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-1.5 animate-fadeIn">
                            <label class="block font-bold text-amber-400 uppercase tracking-wider text-[11px]">Executive Position / Title *</label>
                            <input type="text" 
                                   wire:model="position" 
                                   required
                                   placeholder="e.g. President, Vice President, General Secretary, PRO..." 
                                   class="w-full bg-slate-800/90 border border-amber-500/30 rounded-xl px-4 py-2.5 text-white font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none placeholder-slate-500">
                            <p class="text-[10.5px] text-amber-300/70">Specify your executive leadership portfolio or office.</p>
                            @error('position') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Email & Phone -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Email Address *</label>
                                @if($isDuplicateEmail)
                                    <span class="text-[10px] font-bold text-rose-400 flex items-center gap-1 animate-pulse">
                                        ⚠️ Already Registered
                                    </span>
                                @elseif($email && filter_var($email, FILTER_VALIDATE_EMAIL))
                                    <span class="text-[10px] font-bold text-emerald-400 flex items-center gap-1">
                                        ✓ Available
                                    </span>
                                @endif
                            </div>
                            <div class="relative">
                                <input type="email" 
                                       wire:model.live.debounce.300ms="email" 
                                       required 
                                       placeholder="your.name@example.com" 
                                       class="w-full bg-slate-800/80 border {{ $isDuplicateEmail ? 'border-rose-500 ring-2 ring-rose-500/30' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                                <div wire:loading wire:target="email" class="absolute right-3 top-3 text-slate-400 text-xs">
                                    <svg class="animate-spin h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                </div>
                            </div>
                            @if($duplicateEmailWarning)
                                <div class="mt-1.5 p-2 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-[10.5px] font-semibold">
                                    {{ $duplicateEmailWarning }}
                                </div>
                            @endif
                            @error('email') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">WhatsApp / Phone</label>
                                @if($isDuplicatePhone)
                                    <span class="text-[10px] font-bold text-rose-400 flex items-center gap-1 animate-pulse">
                                        ⚠️ Already Registered
                                    </span>
                                @elseif($phone && strlen(preg_replace('/[^0-9]/', '', $phone)) >= 6)
                                    <span class="text-[10px] font-bold text-emerald-400 flex items-center gap-1">
                                        ✓ Available
                                    </span>
                                @endif
                            </div>
                            <div class="relative">
                                <input type="text" 
                                       wire:model.live.debounce.300ms="phone" 
                                       placeholder="+233..." 
                                       class="w-full bg-slate-800/80 border {{ $isDuplicatePhone ? 'border-rose-500 ring-2 ring-rose-500/30' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                                <div wire:loading wire:target="phone" class="absolute right-3 top-3 text-slate-400 text-xs">
                                    <svg class="animate-spin h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                </div>
                            </div>
                            @if($duplicatePhoneWarning)
                                <div class="mt-1.5 p-2 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-[10.5px] font-semibold">
                                    {{ $duplicatePhoneWarning }}
                                </div>
                            @endif
                            @error('phone') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Institution/Faculty of Law Dropdown -->
                    <div>
                        <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Faculty of Law</label>
                        <select wire:model="institution" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-semibold text-xs">
                            <option value="">-- Select Institution / Faculty --</option>
                            @foreach($institutionList as $inst)
                                <option value="{{ $inst }}">{{ $inst }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Admission & Completion Years -->
                    <div class="grid grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Admission Year</label>
                            <input type="text" wire:model="admission_year" placeholder="e.g. 2023" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Completion Year</label>
                            <input type="text" wire:model="completion_year" placeholder="e.g. 2026" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                        </div>
                    </div>

                    <!-- Dynamic Custom Fields -->
                    @if(!empty($customFieldDefs))
                        <div class="pt-2 border-t border-white/10 space-y-3">
                            <span class="font-extrabold text-purple-400 block uppercase tracking-wider text-[11px]">
                                Additional Information
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                @foreach($customFieldDefs as $cf)
                                    <div>
                                        <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">{{ $cf['label'] }}</label>
                                        @if(($cf['type'] ?? '') === 'select' || !empty($cf['options']))
                                            <select wire:model="custom_field_values.{{ $cf['key'] }}" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-white font-semibold focus:ring-2 focus:ring-purple-500 focus:outline-none text-xs">
                                                <option value="">-- Select {{ $cf['label'] }} --</option>
                                                @foreach($cf['options'] ?? ['Yes', 'No'] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="{{ $cf['type'] ?? 'text' }}" wire:model="custom_field_values.{{ $cf['key'] }}" placeholder="Enter {{ $cf['label'] }}" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-purple-500 focus:outline-none placeholder-slate-500 text-xs">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                            <span wire:loading.remove>Generate &amp; Activate Virtual ID Card</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Generating Your ID Card...
                            </span>
                        </button>
                    </div>
                </form>

            </div>
        @else
            <!-- Success / ID Card Presentation Screen -->
            <div class="space-y-6 animate-fadeIn">
                
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-center text-xs space-y-1">
                    <span class="font-extrabold text-sm block">🎉 Your Virtual ID Card is Active!</span>
                    <p class="text-slate-300">An official copy has also been dispatched to <strong>{{ $generatedCard->email }}</strong>.</p>
                </div>

                <!-- Digital Card Canvas -->
                <div id="virtual-id-card-element" class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-[#0e1628] via-[#0a0f1d] to-[#060a14] border border-blue-500/30 ring-1 ring-white/10 p-5 sm:p-6 space-y-5 text-white shadow-2xl">
                    
                    <!-- Background Institution Logo / Law Watermark -->
                    <div class="absolute inset-0 opacity-[0.06] pointer-events-none flex items-center justify-center overflow-hidden p-6 select-none">
                        @if($generatedCard->institution_logo_url)
                            <img src="{{ $generatedCard->institution_logo_url }}" alt="Institution Emblem" class="w-72 h-72 object-contain filter grayscale contrast-150">
                        @else
                            <svg class="w-72 h-72 text-white" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18l-8 4m8-4l8 4M4 7l-2 6h8L8 7m8 0l-2 6h8l-2-6M6 21h12"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Card Top Header with Main Logo, Association Logo & Member ID Badge -->
                    <div class="flex items-start justify-between border-b border-white/10 pb-3.5 relative z-10 gap-2.5">
                        <div class="flex items-start gap-2.5 min-w-0 flex-1">
                            <!-- 1. Main Logo -->
                            @if($generatedCard->main_logo_url)
                                <img src="{{ $generatedCard->main_logo_url }}" alt="Main Logo" class="w-10 h-10 object-cover rounded-xl border border-white/10 shrink-0 shadow-sm mt-0.5 overflow-hidden" title="Institution Main Logo">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600/30 to-indigo-500/30 border border-blue-400/30 text-blue-300 flex items-center justify-center font-black text-sm shrink-0 shadow-inner mt-0.5">
                                    🏛️
                                </div>
                            @endif
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-blue-400 font-sans leading-tight">
                                    {{ $generatedCard->organization ? $generatedCard->organization->name : 'Federation of African Law Students' }}
                                </div>
                                <div class="text-[10.5px] sm:text-xs font-bold text-amber-400/95 tracking-normal font-sans leading-snug">
                                    {{ $generatedCard->institution ?: 'University of Ghana, School of Law' }}
                                </div>
                            </div>
                        </div>

                        <!-- Right: Association Logo & Member ID Badge -->
                        <div class="shrink-0 flex items-center gap-2">
                            @if($generatedCard->association_logo_url)
                                <img src="{{ $generatedCard->association_logo_url }}" alt="Association Logo" class="w-9 h-9 object-cover rounded-xl border border-amber-500/30 shadow-sm overflow-hidden" title="Association Logo">
                            @endif
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-blue-500/15 border border-blue-400/30 text-blue-300 font-mono text-[10.5px] font-bold shadow-sm whitespace-nowrap">
                                {{ $generatedCard->member_id_number }}
                            </span>
                        </div>
                    </div>

                    <!-- Main Body: Framed Portrait & Credentials Spotlight -->
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-5 relative z-10 py-1">
                        
                        <!-- Framed Photo or Silhouette -->
                        <div class="relative shrink-0">
                            <div class="w-28 h-36 sm:w-32 sm:h-40 rounded-2xl p-1 bg-gradient-to-b from-blue-400/50 via-indigo-500/30 to-blue-600/40 shadow-xl shadow-blue-500/10">
                                <div class="w-full h-full rounded-[14px] overflow-hidden bg-slate-900 flex items-center justify-center relative">
                                    @if($generatedCard->photo_url)
                                        <img src="{{ $generatedCard->photo_url }}" alt="{{ $generatedCard->full_name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-b from-slate-800 to-slate-950 text-slate-500">
                                            <svg class="w-14 h-14 text-slate-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                            <span class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mt-1">Photo on File</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="absolute -bottom-1 -right-1 p-1 rounded-full bg-blue-600 border-2 border-slate-900 text-white shadow">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>

                        <!-- Cardholder Details -->
                        <div class="flex-1 text-center sm:text-left space-y-2.5 w-full">
                            <div class="space-y-1">
                                <span class="text-[9.5px] font-extrabold uppercase tracking-widest text-slate-400 block">Cardholder Name</span>
                                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-snug">{{ $generatedCard->full_name }}</h2>
                                <div>
                                    @if($generatedCard->isExecutive())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 border border-amber-400/40 text-[10px] font-black uppercase tracking-wider shadow-sm">
                                            <span>⭐ EXECUTIVE</span>
                                            @if(!empty($generatedCard->position))
                                                <span>• {{ strtoupper($generatedCard->position) }}</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-500/15 text-blue-300 border border-blue-400/30 text-[9.5px] font-bold uppercase tracking-wider">
                                            <span>MEMBER</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Clean Frosted Chips Grid -->
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="bg-white/[0.04] border border-white/10 rounded-xl p-2 text-center sm:text-left">
                                    <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wider">Admission</span>
                                    <span class="font-bold text-slate-200 font-mono text-[11px]">{{ $generatedCard->admission_year ?: 'N/A' }}</span>
                                </div>
                                <div class="bg-white/[0.04] border border-white/10 rounded-xl p-2 text-center sm:text-left">
                                    <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wider">Completion</span>
                                    <span class="font-bold text-slate-200 font-mono text-[11px]">{{ $generatedCard->completion_year ?: 'Present' }}</span>
                                </div>
                            </div>

                            @if(!empty($generatedCard->custom_fields))
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    @foreach($generatedCard->custom_fields as $cfKey => $cfVal)
                                        @if(!empty($cfVal))
                                            <div class="bg-white/[0.04] border border-white/10 rounded-xl p-2 text-center sm:text-left">
                                                <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wider truncate">{{ ucwords(str_replace('_', ' ', $cfKey)) }}</span>
                                                <span class="font-bold text-slate-200 truncate block text-[11px]">{{ $cfVal }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Bar with QR Code -->
                    <div class="pt-3.5 border-t border-white/10 flex items-center justify-between gap-3 relative z-10">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="p-1 rounded-xl bg-white shadow shrink-0">
                                <img src="{{ $generatedCard->qr_code_url }}" alt="QR" class="w-12 h-12 sm:w-14 sm:h-14 rounded">
                            </div>
                            <div class="space-y-0.5 text-left min-w-0">
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span>Digitally Verified</span>
                                </span>
                                <div class="text-[9.5px] text-slate-400 font-mono truncate">Token: {{ $generatedCard->qr_token }}</div>
                                <div class="text-[8.5px] text-slate-500">Scan code with camera to verify</div>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Active ID</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Download Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button id="download-png-btn" onclick="downloadMemberCardImage()" class="flex-1 py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Save / Download ID Card (PNG)</span>
                    </button>
                    <a href="{{ route('virtual-cards.public.apply', ['org_slug' => $org_slug]) }}" class="py-3.5 px-5 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 text-slate-200 font-bold text-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                        Register Another Member
                    </a>
                </div>

            </div>
        @endif

    <!-- iOS / Safari Long-Press Save Modal Fallback -->
    <div id="ios-save-modal" class="hidden fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md p-4 flex items-center justify-center animate-fadeIn">
        <div class="bg-slate-900 border border-blue-500/30 rounded-3xl p-5 sm:p-6 max-w-md w-full text-center space-y-4 shadow-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-black uppercase">
                📱 iPhone / iPad Save Guide
            </div>
            <h3 class="text-lg font-black text-white">Save ID Card to Photos</h3>
            <div class="rounded-2xl overflow-hidden border border-white/10 shadow-lg max-h-[50vh] flex items-center justify-center bg-slate-950">
                <img id="ios-modal-img" src="" alt="Virtual ID Card" class="max-w-full max-h-[50vh] object-contain rounded-xl select-all">
            </div>
            <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-300 text-xs text-left space-y-1">
                <div class="font-extrabold flex items-center gap-1.5">
                    <span>👆</span> <span>How to save on iPhone:</span>
                </div>
                <p class="text-slate-300 text-[11px] leading-relaxed">
                    <strong>Press and hold (long-press)</strong> the card image above, then tap <strong>"Save to Photos"</strong> (or "Share" &rarr; "Save Image").
                </p>
            </div>
            <button onclick="closeIOSModal()" class="w-full py-3 px-4 rounded-xl bg-white/10 hover:bg-white/15 text-white font-bold text-xs transition cursor-pointer">
                Done / Close
            </button>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-xs text-slate-600 pt-6">
        &copy; {{ date('Y') }} {{ $organization->name }}. All rights reserved.
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js"></script>
    <script>
        function dataURLtoBlob(dataUrl) {
            const arr = dataUrl.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new Blob([u8arr], { type: mime });
        }

        function showIOSPreviewModal(dataUrl) {
            const modal = document.getElementById('ios-save-modal');
            const img = document.getElementById('ios-modal-img');
            img.src = dataUrl;
            modal.classList.remove('hidden');
        }

        function closeIOSModal() {
            const modal = document.getElementById('ios-save-modal');
            modal.classList.add('hidden');
        }

        async function downloadMemberCardImage() {
            const btn = document.getElementById('download-png-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span>Rendering Image...</span>';
            btn.disabled = true;

            const element = document.getElementById('virtual-id-card-element');
            const filename = 'Virtual_ID_Card_{{ $generatedCard ? Str::slug($generatedCard->full_name) : "pass" }}_{{ $generatedCard ? $generatedCard->member_id_number : "card" }}.png';

            window.htmlToImage.toPng(element, {
                pixelRatio: 3,
                backgroundColor: '#090d16',
                cacheBust: true,
            }).then(async dataUrl => {
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
                
                try {
                    const blob = dataURLtoBlob(dataUrl);
                    const file = new File([blob], filename, { type: 'image/png' });

                    // 1. Web Share API (Primary for iPhone / iOS to directly save to Photos or Files)
                    if (navigator.canShare && navigator.canShare({ files: [file] })) {
                        try {
                            await navigator.share({
                                files: [file],
                                title: 'Official Virtual ID Card',
                                text: 'Virtual ID Card - {{ $generatedCard ? $generatedCard->full_name : "" }}'
                            });
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                            return;
                        } catch (shareErr) {
                            if (shareErr.name === 'AbortError') {
                                btn.innerHTML = originalText;
                                btn.disabled = false;
                                return;
                            }
                            console.warn('Share API error, falling back:', shareErr);
                        }
                    }

                    // 2. Standard Blob Download for Android / Desktop
                    const blobUrl = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.download = filename;
                    link.href = blobUrl;
                    document.body.appendChild(link);
                    link.click();

                    setTimeout(() => {
                        document.body.removeChild(link);
                        URL.revokeObjectURL(blobUrl);
                    }, 100);

                    // 3. If on iOS and browser did not open download/share, show preview modal
                    if (isIOS) {
                        showIOSPreviewModal(dataUrl);
                    }
                } catch (e) {
                    console.error('Download handling error:', e);
                    if (isIOS) {
                        showIOSPreviewModal(dataUrl);
                    } else {
                        const link = document.createElement('a');
                        link.download = filename;
                        link.href = dataUrl;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                } finally {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }).catch(err => {
                console.error(err);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Could not generate image snapshot. Please take a screenshot.');
            });
        }
    </script>
</div>
