<div class="w-full max-w-xl mx-auto space-y-6 font-inter">
    <!-- Top Event Header Branding -->
    <div class="text-center space-y-1 py-2">
        <a href="/" class="inline-block group">
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-normal tracking-normal bg-clip-text text-transparent bg-gradient-to-r from-blue-300 via-purple-300 to-amber-200 drop-shadow-xl" style="font-family: {{ $event->title_css_font_family }};">
                {{ $event->name }}
            </h1>
        </a>
    </div>

    <!-- Invitation Card -->
    <div class="{{ $isVip ? 'bg-slate-800/90 backdrop-blur-2xl border border-amber-500/40 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-amber-500/10' : 'bg-slate-800/90 backdrop-blur-2xl border border-blue-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl' }} relative overflow-hidden">
        <!-- Glow & Cover Image Backdrop Accents -->
        @if($event->cover_image_path)
            <div class="absolute inset-0 bg-cover bg-center filter blur-3xl opacity-25 scale-110 pointer-events-none" style="background-image: url('{{ \Illuminate\Support\Facades\Storage::url($event->cover_image_path) }}');"></div>
            <div class="absolute inset-0 bg-slate-900/80 pointer-events-none"></div>
        @else
            @if($isVip)
                <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/15 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>
            @else
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>
            @endif
        @endif

        <div class="relative z-10 space-y-6">
            @if(!$isSuccess)
                <!-- Premium Event Cover Hero Image Banner (Contained & Uncropped) -->
                @if($event->cover_image_path)
                    <div class="relative w-full rounded-2xl overflow-hidden shadow-2xl border border-white/10 bg-slate-950/90 mb-4 flex items-center justify-center min-h-[220px] max-h-[450px]">
                        <!-- Ambient Blur Background to fill letterbox areas seamlessly -->
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($event->cover_image_path) }}" class="absolute inset-0 w-full h-full object-cover filter blur-2xl opacity-40 scale-110 pointer-events-none">
                        <div class="absolute inset-0 bg-slate-950/40 pointer-events-none"></div>

                        <!-- Contained Uncropped Image -->
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($event->cover_image_path) }}" alt="{{ $event->name }}" class="relative z-10 w-full max-h-[450px] object-contain rounded-2xl shadow-xl">

                        <!-- Floating Badge on Top Left -->
                        <div class="absolute top-3 left-3 z-20">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full backdrop-blur-md {{ $isVip ? 'bg-amber-500/90 text-white border border-amber-400/50' : 'bg-blue-600/90 text-white border border-blue-400/50' }} text-xs font-bold uppercase tracking-wider shadow-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                {{ $event->invitation_title ?? ($isVip ? '🔒 PRIVATE VVIP INVITATION' : 'EVENT INVITATION') }}
                            </span>
                        </div>

                        <!-- Floating Details at Bottom of Banner -->
                        <div class="absolute bottom-0 left-0 right-0 z-20 p-4 bg-gradient-to-t from-slate-950 via-slate-950/80 to-transparent space-y-1">
                            @if(!empty($event->description))
                                <p class="text-xs sm:text-sm font-semibold text-orange-400 drop-shadow line-clamp-2">{{ $event->description }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-200 font-semibold drop-shadow pt-0.5">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $event->starts_at ? $event->starts_at->format('M j, Y — g:i A') : 'TBA' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ $event->venue_name ?? 'Special Venue' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form Header -->
                <div class="text-center space-y-2 mb-6">
                    @if(!$event->cover_image_path)
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full {{ $isVip ? 'bg-amber-500/15 border border-amber-500/40 text-amber-300' : 'bg-blue-500/10 border border-blue-500/30 text-blue-300' }} text-xs font-bold uppercase tracking-wider">
                            <svg class="w-4 h-4 {{ $isVip ? 'text-amber-400' : 'text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            {{ $event->invitation_title ?? ($isVip ? '🔒 PRIVATE VVIP INVITATION' : 'EVENT INVITATION') }}
                        </span>
                    @endif

                    <p class="text-slate-300 text-xs sm:text-sm font-medium leading-relaxed">
                        @if(!empty($event->invitation_description))
                            {{ $event->invitation_description }}
                        @elseif($isVip)
                            You have received an <strong class="text-amber-400">exclusive private VVIP invitation</strong> directly from the event organizers. Confirming your attendance grants you <strong class="text-amber-400">VVIP access privileges</strong> and a pre-verified digital pass.
                        @else
                            You are invited to join us for this special event. Confirming your attendance secures your entry pass.
                        @endif
                    </p>
                </div>

                <!-- Event Details Card (When cover image is not present) -->
                @if(!$event->cover_image_path)
                    <div class="bg-slate-900/80 border border-slate-700/60 rounded-2xl p-5 mb-6 space-y-3">
                        <h2 class="text-lg font-bold text-white">{{ $event->name }}</h2>
                        @if(!empty($event->description))
                            <p class="text-xs sm:text-sm font-semibold text-orange-400 line-clamp-2">{{ $event->description }}</p>
                        @endif
                        <p class="text-slate-400 text-xs line-clamp-2">{{ $event->description ?? 'Event details & scheduling.' }}</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800 text-xs text-slate-300 font-medium">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $event->starts_at ? $event->starts_at->format('M j, Y — g:i A') : 'TBA' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                <span>{{ $event->venue_name ?? 'Special Venue' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form with Auto-Save & Draft Persistence -->
                <form wire:submit.prevent="confirmRsvp"
                      x-data="{
                          eventId: '{{ $event->id }}',
                          init() {
                              let savedName = localStorage.getItem('draft_inv_name_' + this.eventId);
                              let savedEmail = localStorage.getItem('draft_inv_email_' + this.eventId);
                              let savedPhone = localStorage.getItem('draft_inv_phone_' + this.eventId);
                              let savedCompany = localStorage.getItem('draft_inv_company_' + this.eventId);
                              let savedJob = localStorage.getItem('draft_inv_job_' + this.eventId);
                              if (savedName || savedEmail || savedPhone || savedCompany || savedJob) {
                                  $wire.restoreDraftValues(savedName || '', savedEmail || '', savedPhone || '', savedCompany || '', savedJob || '');
                              }
                          },
                          saveDraft(field, val) {
                              if (val) {
                                  localStorage.setItem('draft_inv_' + field + '_' + this.eventId, val);
                              }
                          },
                          clearDraft() {
                              localStorage.removeItem('draft_inv_name_' + this.eventId);
                              localStorage.removeItem('draft_inv_email_' + this.eventId);
                              localStorage.removeItem('draft_inv_phone_' + this.eventId);
                              localStorage.removeItem('draft_inv_company_' + this.eventId);
                              localStorage.removeItem('draft_inv_job_' + this.eventId);
                          }
                      }"
                      @submit="clearDraft()"
                      class="space-y-4">
                    @if($isTokenConsumed)
                        <div class="p-4 rounded-2xl bg-amber-500/15 border border-amber-500/40 backdrop-blur-md animate-fadeIn space-y-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider">Single-Use Link Security Notice</h4>
                            </div>
                            <p class="text-xs text-slate-300 font-medium">
                                {{ $tokenNotice }}
                            </p>

                            <!-- Compulsory Reason Textbox -->
                            <div class="pt-2 border-t border-amber-500/30">
                                <label class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                    <span>Reason for Requesting Attendance <span class="text-rose-400 font-bold">*</span></span>
                                    <span class="text-[10px] text-amber-400 font-semibold uppercase">Fill to Unlock Form</span>
                                </label>
                                <textarea wire:model.live.blur="registration_reason"
                                          rows="2"
                                          placeholder="Compulsory: Please state why you are filling this form / how you received this link before filling lower fields..."
                                          class="w-full bg-black/40 border border-amber-500/50 rounded-xl px-4 py-2.5 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium text-xs"></textarea>
                                @error('registration_reason') <span class="text-rose-400 text-xs mt-1 block font-semibold flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</span> @enderror
                            </div>
                        </div>
                    @elseif($hasValidToken)
                        <div class="p-3.5 rounded-2xl bg-blue-500/15 border border-blue-500/30 backdrop-blur-md animate-fadeIn flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                                <span class="text-xs font-bold text-blue-300">Verified Personal Single-Use Pass</span>
                            </div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider bg-blue-500/30 text-blue-300 px-2 py-0.5 rounded-full border border-blue-400/40">Token Active</span>
                        </div>
                    @endif

                    @if($isRecognized)
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-500/20 via-teal-500/20 to-purple-500/20 border border-emerald-500/40 backdrop-blur-md animate-fadeIn space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="flex h-2.5 w-2.5 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                </span>
                                <h4 class="text-xs font-black text-emerald-400 uppercase tracking-wider">Welcome Back, {{ $recognizedName }}!</h4>
                            </div>
                            <p class="text-xs text-slate-300 font-medium">
                                Recognized profile from <strong>{{ $recognizedPastEvent }}</strong>. Your info is pre-filled for quick attendance confirmation.
                            </p>
                        </div>
                    @endif

                    @php
                        $isLocked = $isTokenConsumed && (empty(trim($registration_reason)) || strlen(trim($registration_reason)) < 5);
                    @endphp

                    @if($isLocked)
                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-semibold flex items-center gap-2 animate-pulse">
                            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>Please fill out your Reason for Requesting Attendance above to unlock the form fields.</span>
                        </div>
                    @endif

                    <div class="{{ $isLocked ? 'opacity-40 pointer-events-none transition-all duration-300' : 'transition-all duration-300' }}">
                        <!-- Personal Details -->
                        <div class="space-y-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Personal Details</span>

                            <div>
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input wire:model.live.blur="full_name"
                                           @blur="saveDraft('name', $el.value)"
                                           type="text"
                                           {{ $isLocked ? 'disabled' : '' }}
                                           placeholder="Your full name"
                                           class="w-full bg-slate-900/80 border {{ $errors->has('full_name') ? 'border-rose-500 focus:ring-rose-500' : (!empty($full_name) ? 'border-emerald-500/60 focus:ring-emerald-500' : 'border-slate-700 focus:ring-blue-500') }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 {{ $isVip ? 'focus:ring-amber-500' : 'focus:ring-blue-500' }} transition-all font-medium text-sm pr-10">
                                    @if(!empty($full_name) && !$errors->has('full_name'))
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-emerald-400 animate-fadeIn">
                                            <div class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('full_name') <span class="text-rose-400 text-xs mt-1.5 block font-semibold flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input wire:model.live.blur="email"
                                           @blur="saveDraft('email', $el.value)"
                                           type="email"
                                           {{ $isLocked ? 'disabled' : '' }}
                                           placeholder="name@example.com"
                                           class="w-full bg-slate-900/80 border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : (!empty($email) ? 'border-emerald-500/60 focus:ring-emerald-500' : 'border-slate-700 focus:ring-blue-500') }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 {{ $isVip ? 'focus:ring-amber-500' : 'focus:ring-blue-500' }} transition-all font-medium text-sm pr-10">
                                    @if(!empty($email) && !$errors->has('email'))
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-emerald-400 animate-fadeIn">
                                            <div class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('email') <span class="text-rose-400 text-xs mt-1.5 block font-semibold flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Phone Number <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input wire:model.live.blur="phone"
                                           @blur="saveDraft('phone', $el.value)"
                                           type="tel"
                                           maxlength="10"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                           {{ $isLocked ? 'disabled' : '' }}
                                           placeholder="0246345698 (10 digits)"
                                           class="w-full bg-slate-900/80 border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : (!empty($phone) ? 'border-emerald-500/60 focus:ring-emerald-500' : 'border-slate-700 focus:ring-blue-500') }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 {{ $isVip ? 'focus:ring-amber-500' : 'focus:ring-blue-500' }} transition-all font-medium text-sm pr-10">
                                    @if(!empty($phone) && !$errors->has('phone'))
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-emerald-400 animate-fadeIn">
                                            <div class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('phone') <span class="text-rose-400 text-xs mt-1.5 block font-semibold flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Optional Professional Details: Company & Job Title -->
                        <div class="pt-4 border-t border-slate-700/60 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Professional Details</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-orange-500/20 text-orange-400 border border-orange-500/40 shadow-sm shadow-orange-500/10">For Networking</span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-700/50 border border-slate-600/50">Optional</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Company -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Company / Org <span class="text-slate-500 font-normal text-[10px]">(Optional)</span></label>
                                    <div class="relative">
                                        <input wire:model.live.blur="company"
                                               @blur="saveDraft('company', $el.value)"
                                               type="text"
                                               {{ $isLocked ? 'disabled' : '' }}
                                               placeholder="Company Name (Optional)"
                                               class="w-full bg-slate-900/80 border {{ !empty($company) ? 'border-emerald-500/60 focus:ring-emerald-500' : 'border-slate-700 focus:ring-blue-500' }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 {{ $isVip ? 'focus:ring-amber-500' : 'focus:ring-blue-500' }} transition-all font-medium text-sm pr-10">
                                        @if(!empty($company) && !$errors->has('company'))
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-emerald-400 animate-fadeIn">
                                                <div class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Job Title -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Job Title <span class="text-slate-500 font-normal text-[10px]">(Optional)</span></label>
                                    <div class="relative">
                                        <input wire:model.live.blur="job_title"
                                               @blur="saveDraft('job', $el.value)"
                                               type="text"
                                               {{ $isLocked ? 'disabled' : '' }}
                                               placeholder="Position (Optional)"
                                               class="w-full bg-slate-900/80 border {{ !empty($job_title) ? 'border-emerald-500/60 focus:ring-emerald-500' : 'border-slate-700 focus:ring-blue-500' }} rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 {{ $isVip ? 'focus:ring-amber-500' : 'focus:ring-blue-500' }} transition-all font-medium text-sm pr-10">
                                        @if(!empty($job_title) && !$errors->has('job_title'))
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-emerald-400 animate-fadeIn">
                                                <div class="w-5 h-5 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input wire:model="consent" {{ $isLocked ? 'disabled' : '' }} type="checkbox" class="mt-1 form-checkbox h-4 w-4 {{ $isVip ? 'text-amber-600' : 'text-blue-600' }} rounded border-slate-700 bg-slate-900 {{ $isVip ? 'focus:ring-amber-500' : 'focus:ring-blue-500' }}">
                                <span class="text-xs text-slate-400 font-medium">I confirm my attendance for this event and agree to receive digital pass communications.</span>
                            </label>
                            @error('consent') <span class="text-rose-400 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        @if($isVip && !$isTokenConsumed)
                            <button type="submit" {{ $isLocked ? 'disabled' : '' }} wire:loading.attr="disabled" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-amber-600 via-yellow-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-extrabold text-sm shadow-xl shadow-amber-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer mt-4">
                                <span wire:loading.remove>Confirm VVIP Attendance →</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Confirming VVIP Attendance...
                                </span>
                            </button>
                        @elseif($isTokenConsumed)
                            <button type="submit" {{ $isLocked ? 'disabled' : '' }} wire:loading.attr="disabled" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-extrabold text-sm shadow-xl shadow-amber-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer mt-4">
                                <span wire:loading.remove>Submit Request for Administrator Review →</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Submitting Request...
                                </span>
                            </button>
                        @else
                            <button type="submit" {{ $isLocked ? 'disabled' : '' }} wire:loading.attr="disabled" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-sm shadow-xl shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer mt-4">
                                <span wire:loading.remove>Confirm Attendance →</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Confirming Attendance...
                                </span>
                            </button>
                        @endif
                    </div>
                </form>
            @else
                <!-- Success View -->
                <div class="text-center space-y-6 animate-fadeInUp">
                    @if(!empty($qrToken))
                        <!-- Verified Pass Issued Screen -->
                        <div class="w-16 h-16 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center mx-auto shadow-xl shadow-emerald-500/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>

                        <div class="space-y-2">
                            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                VERIFIED PASS ISSUED
                            </span>
                            <h2 class="text-2xl font-extrabold text-white">Attendance Confirmed!</h2>
                            <p class="text-slate-300 text-xs font-medium max-w-md mx-auto">
                                Welcome, <strong class="text-white">{{ $full_name }}</strong>! Your private invitation has been confirmed and pre-verified.
                            </p>
                        </div>

                        <!-- Digital QR Pass Card -->
                        <div class="bg-slate-900 border border-emerald-500/30 rounded-2xl p-6 text-center space-y-4 shadow-inner">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Digital Entry QR Code Token</p>
                            
                            <div class="w-36 h-36 bg-white rounded-xl p-2 mx-auto flex items-center justify-center shadow-lg">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($qrToken) }}" alt="QR Pass" class="w-full h-full object-contain">
                            </div>

                            <div class="bg-slate-800 rounded-lg p-2 font-mono text-[11px] text-emerald-400 break-all border border-slate-700">
                                {{ $qrToken }}
                            </div>

                            <!-- Download Button -->
                            @php
                                $qrDownloadUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=" . urlencode($qrToken);
                                $downloadFileName = \Illuminate\Support\Str::slug($full_name ?: 'attendee') . "-qr-pass.png";
                            @endphp
                            <div class="pt-1">
                                <a href="{{ $qrDownloadUrl }}" 
                                   target="_blank" 
                                   download="{{ $downloadFileName }}"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Download QR Pass (PNG)
                                </a>
                            </div>

                            <!-- Single-Use Personal Security Caution Box -->
                            <div class="p-4 rounded-xl bg-rose-500/15 border border-rose-500/40 text-left space-y-2 animate-fadeIn mt-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4.5 h-4.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <h4 class="text-xs font-black text-rose-400 uppercase tracking-wider">⚠️ Security Caution: Single-Use Pass</h4>
                                </div>
                                <p class="text-[11px] text-slate-300 font-medium leading-relaxed">
                                    Scanning of this entry QR pass is <strong>strictly single-use only (1-time check-in)</strong>. Pass duplication, screenshots, or code sharing is strictly prohibited.
                                </p>
                                <p class="text-[11px] text-rose-300 font-semibold leading-relaxed pt-1 border-t border-rose-500/20">
                                    If this code is shared with anyone, the first person to arrive at the event checkpoint will consume the pass, and duplicate entries will be automatically rejected. <strong>Sharing this code is at your own risk.</strong>
                                </p>
                            </div>

                            <p class="text-[11px] text-slate-400 pt-1">Present this QR code at entry checkpoints for seamless access.</p>
                        </div>
                    @else
                        <!-- Pending Approval Confirmation Screen (NO QR CODE GENERATED) -->
                        <div class="w-16 h-16 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center mx-auto shadow-xl shadow-amber-500/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>

                        <div class="space-y-2">
                            <span class="inline-block px-3.5 py-1 rounded-full text-xs font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                STATUS: PENDING ADMINISTRATOR REVIEW
                            </span>
                            <h2 class="text-2xl font-extrabold text-white mt-4">Request Submitted for Approval</h2>
                            <p class="text-slate-300 text-xs font-medium max-w-md mx-auto">
                                Thank you, <strong class="text-white">{{ $full_name }}</strong>! Because this single-use link was previously redeemed, your attendance request has been routed to event administrators for verification.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-left space-y-2 text-xs">
                            <span class="font-bold text-amber-400 uppercase tracking-wider block flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Approval & Pass Delivery Process
                            </span>
                            <p class="text-slate-300 font-medium">
                                🔒 <strong>No QR Code Pass is generated at this stage.</strong> Once an event administrator reviews your request and reason note in the dashboard, your entry pass will be generated and emailed directly to <strong class="text-white">{{ $email }}</strong>.
                            </p>
                        </div>
                    @endif

                    <a href="/public-events" class="inline-block text-xs font-bold text-purple-400 hover:text-purple-300 hover:underline">
                        ← Explore More Events
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes titleGlow {
        0%, 100% {
            background-size: 200% 200%;
            background-position: left center;
            transform: translateY(0px);
        }
        50% {
            background-size: 200% 200%;
            background-position: right center;
            transform: translateY(-3px);
        }
    }
    .animate-title-shimmer {
        background-size: 200% auto;
        animation: titleGlow 4s ease-in-out infinite;
    }
</style>
