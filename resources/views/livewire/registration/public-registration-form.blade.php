<div class="min-h-screen bg-slate-950 font-inter py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden flex items-center justify-center">
    
    <!-- Background Accents -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-[100px]"></div>
        <div class="absolute top-40 right-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-40 left-1/3 w-[600px] h-[600px] bg-emerald-600/10 rounded-full blur-[150px]"></div>
    </div>

    <div class="w-full max-w-3xl relative z-10">
        
        @if(!$event)
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-12 text-center text-white">
                <h1 class="text-2xl font-bold mb-2">Event Not Found</h1>
                <p class="text-slate-400">The event you are looking for does not exist or has been removed.</p>
            </div>
        @elseif($isSuccess)
            <!-- Success State -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 md:p-12 text-center text-white animate-bounce-in shadow-2xl shadow-blue-900/20">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(16,185,129,0.3)]">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-3 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/70">You're Registered!</h2>
                <p class="text-sm md:text-base text-slate-300 mb-6 max-w-md mx-auto">
                    Thank you for registering for <span class="font-semibold text-white">{{ $event->name }}</span>. Your ticket and QR pass have been issued to <span class="text-blue-400 font-semibold">{{ $email }}</span>.
                </p>

                @if(!empty($qrToken))
                    <!-- Digital Entry QR Code Card -->
                    <div class="bg-slate-900/90 border border-emerald-500/30 rounded-2xl p-6 max-w-sm mx-auto mb-8 text-center space-y-4 shadow-xl">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">🎟️ OFFICIAL DIGITAL QR ENTRY PASS</p>
                            @if(!empty($full_name))
                                <h3 class="text-xl font-extrabold text-white drop-shadow">{{ $full_name }}</h3>
                            @endif
                        </div>
                        
                        <div class="w-36 h-36 bg-white rounded-xl p-2 mx-auto flex items-center justify-center shadow-lg">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($qrToken) }}" alt="QR Pass" class="w-full h-full object-contain">
                        </div>

                        <div class="bg-slate-800 rounded-lg p-2 font-mono text-[11px] text-emerald-400 break-all border border-slate-700">
                            {{ $qrToken }}
                        </div>

                        @php
                            $publicQrDownloadUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=" . urlencode($qrToken);
                            $publicDownloadFileName = \Illuminate\Support\Str::slug($full_name ?: 'attendee') . "-qr-pass.png";
                        @endphp
                        <div>
                            <a href="{{ $publicQrDownloadUrl }}" 
                               target="_blank" 
                               download="{{ $publicDownloadFileName }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download QR Pass (PNG)
                            </a>
                        </div>
                    </div>
                @endif

                <div class="p-5 bg-black/30 rounded-2xl border border-white/5 max-w-sm mx-auto mb-8 text-left backdrop-blur-md">
                    <p class="text-xs text-slate-500 uppercase tracking-wider mb-2">Event Details</p>
                    <p class="font-medium text-white text-sm">{{ $event->starts_at ? $event->starts_at->format('l, F j, Y @ g:i A') : 'Date TBA' }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $event->full_venue_location }}</p>
                </div>

                <a href="/public-events" class="text-blue-400 hover:text-blue-300 transition-colors text-xs font-bold uppercase tracking-wider">← Explore More Events</a>
            </div>
        @else
            <!-- Registration Form -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl shadow-black/50 overflow-hidden">
                
                <!-- Event Header -->
                <div class="p-8 md:p-10 border-b border-white/10 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20"></div>
                    <div class="relative z-10 text-center">
                        <span class="inline-block px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-medium text-blue-300 tracking-wider uppercase mb-4 backdrop-blur-md">
                            {{ $event->is_free ? 'Free Registration' : 'Ticketed Event' }}
                        </span>
                        <h1 class="text-4xl md:text-6xl font-normal text-white mb-2 tracking-normal drop-shadow-lg" style="font-family: {{ $event->title_css_font_family }};">{{ $event->name }}</h1>
                        @if(!empty($event->description))
                            <p class="text-sm md:text-base font-semibold text-orange-400 mb-4 max-w-xl mx-auto drop-shadow line-clamp-3">{{ $event->description }}</p>
                        @endif
                        <div class="flex flex-col md:flex-row items-center justify-center space-y-2 md:space-y-0 md:space-x-6 text-slate-300">
                            <span class="flex items-center"><svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ $event->starts_at ? $event->starts_at->format('M d, Y @ H:i') : 'Date TBA' }}</span>
                            <span class="flex items-center"><svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg> {{ $event->full_venue_location }}</span>
                        </div>
                    </div>
                </div>

                <!-- Warnings -->
                @if($event->is_registration_closed)
                    <div class="bg-rose-500/20 border-b border-rose-500/30 p-6 text-center space-y-3 animate-fadeIn">
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-rose-500/30 text-rose-300 border border-rose-500/50 uppercase tracking-wider inline-flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                            REGISTRATION CLOSED
                        </span>
                        <h3 class="text-xl font-bold text-white">Registration Deadline Passed</h3>
                        <p class="text-xs text-rose-200 font-medium max-w-md mx-auto leading-relaxed">
                            Registration for this event officially closed on <strong>{{ $event->registration_deadline ? $event->registration_deadline->format('F j, Y @ g:i A') : '' }}</strong>. New submissions are disabled.
                        </p>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-rose-500/20 border-b border-rose-500/30 p-4 text-center text-rose-300 font-medium">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if ($event->capacity && $event->attendees_count >= ($event->capacity * 0.9))
                    <div class="bg-amber-500/10 border-b border-amber-500/20 p-3 text-center text-amber-400 text-sm">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Almost full! Only a few spots left.
                        </span>
                    </div>
                @endif

                <form wire:submit.prevent="register"
                      x-data="{
                          eventId: '{{ $event->id }}',
                          init() {
                              let savedName = localStorage.getItem('draft_reg_name_' + this.eventId);
                              let savedEmail = localStorage.getItem('draft_reg_email_' + this.eventId);
                              let savedPhone = localStorage.getItem('draft_reg_phone_' + this.eventId);
                              let savedCompany = localStorage.getItem('draft_reg_company_' + this.eventId);
                              let savedJob = localStorage.getItem('draft_reg_job_' + this.eventId);
                              if (savedName || savedEmail || savedPhone || savedCompany || savedJob) {
                                  $wire.restoreDraftValues(savedName || '', savedEmail || '', savedPhone || '', savedCompany || '', savedJob || '');
                              }
                          },
                          saveDraft(field, val) {
                              if (val) {
                                  localStorage.setItem('draft_reg_' + field + '_' + this.eventId, val);
                              }
                          },
                          clearDraft() {
                              localStorage.removeItem('draft_reg_name_' + this.eventId);
                              localStorage.removeItem('draft_reg_email_' + this.eventId);
                              localStorage.removeItem('draft_reg_phone_' + this.eventId);
                              localStorage.removeItem('draft_reg_company_' + this.eventId);
                              localStorage.removeItem('draft_reg_job_' + this.eventId);
                          }
                      }"
                      @submit="clearDraft()"
                      class="p-8 md:p-10">
                    
                    @if($isRecognized)
                        <div class="mb-8 p-5 rounded-2xl bg-gradient-to-r from-emerald-500/20 via-teal-500/20 to-blue-500/20 border border-emerald-500/40 backdrop-blur-md animate-fadeIn space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                <h4 class="text-sm font-black text-emerald-400 tracking-wide uppercase">Welcome Back, {{ $recognizedName }}!</h4>
                            </div>
                            <p class="text-xs text-slate-200 leading-relaxed font-medium">
                                We recognized your profile from <strong>{{ $recognizedPastEvent }}</strong> under <strong>{{ $recognizedOrganization }}</strong>. Your details have been automatically pre-filled below for quick 1-click registration.
                            </p>
                        </div>
                    @endif

                    @php
                        $formConfig = $event->form_fields_config;
                        $stdConfig = $formConfig['standard_fields'];
                        $customConfig = $formConfig['custom_fields'];
                    @endphp

                    <!-- Section 1: Attendee Details -->
                    <div class="mb-10">
                        <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                            <span class="w-6 h-6 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center text-xs mr-3">1</span>
                            Attendee Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            @if(($stdConfig['full_name'] ?? 'required') !== 'disabled')
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Full Name 
                                        @if(($stdConfig['full_name'] ?? 'required') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="full_name" type="text" class="w-full bg-black/20 border {{ $errors->has('full_name') ? 'border-rose-500 focus:ring-rose-500' : 'border-white/10 focus:ring-blue-500' }} rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="John Doe">
                                    @error('full_name') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Email Address -->
                            @if(($stdConfig['email'] ?? 'required') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Email Address 
                                        @if(($stdConfig['email'] ?? 'required') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="email" type="email" class="w-full bg-black/20 border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-white/10 focus:ring-blue-500' }} rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="john@example.com">
                                    @error('email') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Phone Number -->
                            @if(($stdConfig['phone'] ?? 'required') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Phone Number 
                                        @if(($stdConfig['phone'] ?? 'required') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="phone" type="tel" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" class="w-full bg-black/20 border {{ $errors->has('phone') ? 'border-rose-500 focus:ring-rose-500' : 'border-white/10 focus:ring-blue-500' }} rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="e.g. 0240303609 (10 digits)">
                                    @error('phone') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Company -->
                            @if(($stdConfig['company'] ?? 'disabled') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Company / Organization 
                                        @if(($stdConfig['company'] ?? '') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="company" type="text" class="w-full bg-black/20 border border-white/10 focus:ring-blue-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="Company Name">
                                    @error('company') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Job Title -->
                            @if(($stdConfig['job_title'] ?? 'disabled') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Job Title 
                                        @if(($stdConfig['job_title'] ?? '') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="job_title" type="text" class="w-full bg-black/20 border border-white/10 focus:ring-blue-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="e.g. Software Engineer">
                                    @error('job_title') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Country -->
                            @if(($stdConfig['country'] ?? 'disabled') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Country 
                                        @if(($stdConfig['country'] ?? '') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="country" type="text" class="w-full bg-black/20 border border-white/10 focus:ring-blue-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="e.g. Ghana">
                                    @error('country') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Gender -->
                            @if(($stdConfig['gender'] ?? 'disabled') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Gender 
                                        @if(($stdConfig['gender'] ?? '') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <select wire:model.live="gender" class="w-full bg-black/20 border border-white/10 focus:ring-blue-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium cursor-pointer">
                                        <option value="" class="bg-slate-900">Select Gender</option>
                                        <option value="Male" class="bg-slate-900">Male</option>
                                        <option value="Female" class="bg-slate-900">Female</option>
                                        <option value="Other" class="bg-slate-900">Other / Prefer not to say</option>
                                    </select>
                                    @error('gender') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Emergency Contact Name -->
                            @if(($stdConfig['emergency_contact_name'] ?? 'disabled') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Emergency Contact Name 
                                        @if(($stdConfig['emergency_contact_name'] ?? '') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="emergency_contact_name" type="text" class="w-full bg-black/20 border border-white/10 focus:ring-blue-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="Contact Name">
                                    @error('emergency_contact_name') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Emergency Contact Phone -->
                            @if(($stdConfig['emergency_contact_phone'] ?? 'disabled') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Emergency Contact Phone 
                                        @if(($stdConfig['emergency_contact_phone'] ?? '') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="emergency_contact_phone" type="tel" class="w-full bg-black/20 border border-white/10 focus:ring-blue-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="Contact Phone">
                                    @error('emergency_contact_phone') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Dietary Preferences -->
                            @if(($stdConfig['dietary_preferences'] ?? 'disabled') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Dietary Preferences 
                                        @if(($stdConfig['dietary_preferences'] ?? '') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="dietary_preferences" type="text" class="w-full bg-black/20 border border-white/10 focus:ring-blue-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="e.g. Vegetarian, Halal, Kosher">
                                    @error('dietary_preferences') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Accessibility Needs -->
                            @if(($stdConfig['accessibility_needs'] ?? 'disabled') !== 'disabled')
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Accessibility Needs 
                                        @if(($stdConfig['accessibility_needs'] ?? '') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <input wire:model.live.blur="accessibility_needs" type="text" class="w-full bg-black/20 border border-white/10 focus:ring-blue-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="e.g. Wheelchair access, Sign language">
                                    @error('accessibility_needs') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <!-- Registration Reason -->
                            @if(($stdConfig['registration_reason'] ?? 'disabled') !== 'disabled')
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Reason for Attendance 
                                        @if(($stdConfig['registration_reason'] ?? '') === 'required')
                                            <span class="text-rose-500">*</span>
                                        @else
                                            <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                        @endif
                                    </label>
                                    <textarea wire:model.live.blur="registration_reason" rows="2" class="w-full bg-black/20 border border-white/10 focus:ring-blue-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="Brief note..."></textarea>
                                    @error('registration_reason') <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Custom Extra Questions Section -->
                    @if(!empty($customConfig))
                        <div class="mb-10 pt-8 border-t border-white/10">
                            <h3 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center text-xs">2</span>
                                Additional Event Questions
                            </h3>
                            <div class="space-y-6">
                                @foreach($customConfig as $cField)
                                    @php
                                        $cId = $cField['id'] ?? '';
                                        $cLabel = $cField['label'] ?? 'Question';
                                        $cType = $cField['type'] ?? 'text';
                                        $cReq = !empty($cField['required']);
                                        $cOpts = array_filter(array_map('trim', explode(',', $cField['options'] ?? '')));
                                    @endphp

                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">
                                            {{ $cLabel }}
                                            @if($cReq)
                                                <span class="text-rose-500">*</span>
                                            @else
                                                <span class="text-slate-500 text-xs font-normal">(Optional)</span>
                                            @endif
                                        </label>

                                        @if($cType === 'text')
                                            <input type="text" wire:model.live="custom_answers.{{ $cId }}" class="w-full bg-black/20 border border-white/10 focus:ring-purple-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="Your answer...">
                                        @elseif($cType === 'number')
                                            <input type="number" wire:model.live="custom_answers.{{ $cId }}" class="w-full bg-black/20 border border-white/10 focus:ring-purple-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="0">
                                        @elseif($cType === 'textarea')
                                            <textarea wire:model.live="custom_answers.{{ $cId }}" rows="3" class="w-full bg-black/20 border border-white/10 focus:ring-purple-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium" placeholder="Your answer..."></textarea>
                                        @elseif($cType === 'select')
                                            <select wire:model.live="custom_answers.{{ $cId }}" class="w-full bg-black/20 border border-white/10 focus:ring-purple-500 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:bg-white/5 transition-all font-medium cursor-pointer">
                                                <option value="" class="bg-slate-900">Select an option</option>
                                                @foreach($cOpts as $opt)
                                                    <option value="{{ $opt }}" class="bg-slate-900">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($cType === 'checkbox')
                                            <label class="flex items-center space-x-3 cursor-pointer p-3 rounded-xl bg-black/20 border border-white/10">
                                                <input type="checkbox" wire:model.live="custom_answers.{{ $cId }}" class="form-checkbox h-5 w-5 text-purple-600 rounded border-slate-600 bg-slate-900 focus:ring-purple-500">
                                                <span class="text-sm text-slate-300 font-semibold">{{ $cLabel }}</span>
                                            </label>
                                        @endif

                                        @error("custom_answers.{$cId}") <span class="text-rose-400 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Consent -->
                    <div class="mb-10 pt-8 border-t border-white/10">
                        <label class="flex items-start space-x-4 cursor-pointer p-4 rounded-xl hover:bg-white/5 transition-colors border border-transparent hover:border-white/5">
                            <div class="flex-shrink-0 mt-1">
                                <input wire:model.live="consent" type="checkbox" class="form-checkbox h-5 w-5 text-blue-500 rounded border-slate-600 bg-slate-900 focus:ring-blue-500 focus:ring-offset-slate-900">
                            </div>
                            <div>
                                <p class="text-sm text-slate-300">I agree to the <a href="#" class="text-blue-400 hover:underline">Terms & Conditions</a> and <a href="#" class="text-blue-400 hover:underline">Privacy Policy</a>. I consent to my data being processed for the purpose of organizing this event.</p>
                                @error('consent') <span class="text-rose-400 text-xs mt-1.5 block font-semibold flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</span> @enderror
                            </div>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="mt-8">
                        <button type="submit" {{ $event->is_registration_closed ? 'disabled' : '' }} class="w-full py-4 rounded-xl {{ $event->is_registration_closed ? 'bg-rose-950/60 text-rose-300 border border-rose-500/30 cursor-not-allowed' : 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5' }} font-bold text-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-blue-500 relative overflow-hidden group">
                            @if($event->is_registration_closed)
                                <span class="flex items-center justify-center gap-2">
                                    <span>⛔</span>
                                    <span>Registration Closed (Deadline Passed)</span>
                                </span>
                            @else
                                <span wire:loading.remove>Complete Registration</span>
                                <span wire:loading class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                                <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                            @endif
                        </button>
                    </div>

                </form>
            </div>
        @endif
    </div>
</div>

<style>
    @keyframes bounce-in {
        0% { transform: scale(0.9); opacity: 0; }
        60% { transform: scale(1.02); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-bounce-in {
        animation: bounce-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>

@script
<script>
    $wire.on('registration-successful', () => {
        // Confetti could be triggered here
        if (typeof confetti === 'function') {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#3b82f6', '#10b981', '#8b5cf6']
            });
        }
    });
</script>
@endscript
