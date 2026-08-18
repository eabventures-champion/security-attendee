<div class="min-h-screen bg-slate-950 text-slate-100 py-10 px-4 font-inter selection:bg-blue-500 selection:text-white">
    <div class="max-w-xl mx-auto w-full space-y-6">
        
        <!-- Header & Branding -->
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-blue-500/10 border border-blue-500/20 shadow-lg shadow-blue-500/10 mb-1">
                @if($institution_logo_url)
                    <img src="{{ $institution_logo_url }}" alt="Logo" class="w-12 h-12 object-contain rounded-xl">
                @else
                    <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center font-black text-xl">
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
                            <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">WhatsApp / Phone</label>
                            <input type="text" wire:model="phone" placeholder="+233..." class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                        </div>
                    </div>

                    <!-- Institution/Faculty of Law Dropdown -->
                    <div>
                        <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Institution/Faculty of Law</label>
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
                <div id="virtual-id-card-element" class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border-2 border-blue-500/40 p-6 sm:p-7 space-y-6 text-white shadow-2xl">
                    
                    <!-- Background Institution Logo / Law Watermark -->
                    <div class="absolute inset-0 opacity-[0.07] pointer-events-none flex items-center justify-center overflow-hidden p-6 select-none">
                        @if($generatedCard->institution_logo_url)
                            <img src="{{ $generatedCard->institution_logo_url }}" alt="Institution Emblem" class="w-80 h-80 object-contain filter grayscale contrast-150">
                        @else
                            <svg class="w-80 h-80 text-white" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18l-8 4m8-4l8 4M4 7l-2 6h8L8 7m8 0l-2 6h8l-2-6M6 21h12"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Card Top Header with Institution Logo -->
                    <div class="flex items-center justify-between border-b border-white/10 pb-4 relative z-10 gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($generatedCard->institution_logo_url)
                                <img src="{{ $generatedCard->institution_logo_url }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl bg-white/5 p-1 border border-white/10 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center font-black text-sm shrink-0">
                                    🏛️
                                </div>
                            @endif
                            <div class="space-y-0.5 min-w-0">
                                <div class="text-xs font-black uppercase tracking-wider text-blue-400 font-sans leading-tight">
                                    Federation of African Law Students (FALAS)
                                </div>
                                <div class="text-xs font-bold text-orange-400 tracking-wide font-sans">
                                    {{ $generatedCard->institution ?: 'University of Ghana, School of Law' }}
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="inline-block px-2.5 py-1 rounded-lg bg-blue-500/20 border border-blue-500/30 text-blue-300 font-mono text-xs font-black tracking-wider whitespace-nowrap">
                                {{ $generatedCard->member_id_number }}
                            </span>
                        </div>
                    </div>

                    <!-- Main Body: Photo & Credentials -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 relative z-10">
                        
                        <!-- Photo or Silhouette -->
                        <div class="w-28 h-36 sm:w-32 sm:h-40 rounded-2xl border-2 border-blue-400/50 bg-slate-800/80 overflow-hidden flex items-center justify-center shrink-0 shadow-lg relative">
                            @if($generatedCard->photo_url)
                                <img src="{{ $generatedCard->photo_url }}" alt="{{ $generatedCard->full_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-b from-slate-800 to-slate-950 text-slate-500">
                                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                    <span class="text-[9px] font-bold uppercase tracking-wider mt-1">Photo on File</span>
                                </div>
                            @endif
                            <div class="absolute bottom-1 right-1 p-1 rounded-full bg-blue-600 text-white shadow">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 text-center sm:text-left space-y-3 w-full">
                            <div>
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Cardholder Name</div>
                                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $generatedCard->full_name }}</h2>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="bg-white/5 border border-white/5 rounded-xl p-2">
                                    <span class="text-[10px] text-slate-400 block font-semibold uppercase">Admission</span>
                                    <span class="font-bold text-slate-200">{{ $generatedCard->admission_year ?: 'N/A' }}</span>
                                </div>
                                <div class="bg-white/5 border border-white/5 rounded-xl p-2">
                                    <span class="text-[10px] text-slate-400 block font-semibold uppercase">Completion</span>
                                    <span class="font-bold text-slate-200">{{ $generatedCard->completion_year ?: 'Present' }}</span>
                                </div>
                            </div>

                            @if(!empty($generatedCard->custom_fields))
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    @foreach($generatedCard->custom_fields as $cfKey => $cfVal)
                                        @if(!empty($cfVal))
                                            <div class="bg-white/5 border border-white/5 rounded-xl p-2">
                                                <span class="text-[10px] text-slate-400 block font-semibold uppercase">{{ ucwords(str_replace('_', ' ', $cfKey)) }}</span>
                                                <span class="font-bold text-slate-200 truncate block">{{ $cfVal }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Bar with QR Code -->
                    <div class="pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
                        <div class="flex items-center gap-3.5">
                            <div class="p-1.5 rounded-xl bg-white shadow-md shrink-0">
                                <img src="{{ $generatedCard->qr_code_url }}" alt="QR" class="w-16 h-16 rounded-lg">
                            </div>
                            <div class="space-y-0.5 text-left">
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Digitally Verified ID
                                </span>
                                <div class="text-[10px] text-slate-400 font-mono">Token: {{ $generatedCard->qr_token }}</div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                Active ID
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

        <!-- Footer -->
        <div class="text-center text-xs text-slate-600 pt-6">
            &copy; {{ date('Y') }} {{ $organization->name }}. All rights reserved.
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js"></script>
    <script>
        function downloadMemberCardImage() {
            const btn = document.getElementById('download-png-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span>Rendering Image...</span>';
            btn.disabled = true;

            const element = document.getElementById('virtual-id-card-element');
            window.htmlToImage.toPng(element, {
                pixelRatio: 3,
                backgroundColor: '#090d16',
                cacheBust: true,
            }).then(dataUrl => {
                const link = document.createElement('a');
                link.download = 'Virtual_ID_Card_{{ $generatedCard ? Str::slug($generatedCard->full_name) : "pass" }}_{{ $generatedCard ? $generatedCard->member_id_number : "card" }}.png';
                link.href = dataUrl;
                link.click();
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(err => {
                console.error(err);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Could not generate image snapshot. Please take a screenshot.');
            });
        }
    </script>
</div>
