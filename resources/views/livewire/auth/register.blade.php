<div class="w-full max-w-md">
    <!-- Register Card -->
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl">
        @if($registeredPending)
            <div class="text-center space-y-5 animate-fadeIn">
                <div class="w-16 h-16 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-amber-500/20 animate-bounce">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <div>
                    <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-500/15 text-amber-400 border border-amber-500/30 tracking-wider uppercase">
                        Super Admin Approval Required
                    </span>
                    <h2 class="text-2xl font-extrabold text-white mt-3">Registration Submitted!</h2>
                    <p class="text-slate-400 text-sm mt-2 leading-relaxed">
                        Your organization account <strong class="text-blue-400">{{ $organization_name }}</strong> has been successfully submitted for Super Admin review.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-900/80 border border-white/10 text-left space-y-2 text-xs">
                    <div class="flex justify-between border-b border-white/5 pb-2">
                        <span class="text-slate-500 font-medium">Administrator Email</span>
                        <span class="font-bold text-slate-200">{{ $email }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 pb-2">
                        <span class="text-slate-500 font-medium">Organization</span>
                        <span class="font-bold text-blue-400">{{ $organization_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Approval Status</span>
                        <span class="font-bold text-amber-400 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                            Pending Super Admin Review
                        </span>
                    </div>
                </div>

                <p class="text-xs text-slate-400 leading-relaxed">
                    An approval notification with direct confirmation links has been dispatched to the Super Admin. You will receive an automated notification as soon as your workspace is activated.
                </p>

                <a href="{{ route('login') }}" class="block w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all text-sm">
                    Return to Sign In
                </a>
            </div>
        @else
            <h2 class="text-2xl font-bold text-white mb-1">Create your account</h2>
            <p class="text-slate-400 text-sm mb-6">Start managing events in minutes</p>

        <form wire:submit.prevent="register" class="space-y-4">

            <!-- Full Name -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="name" class="block text-sm font-medium text-slate-300">Full Name</label>
                    @if(!$errors->has('name') && strlen($name) >= 3)
                        <span class="text-xs text-emerald-400 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Valid
                        </span>
                    @endif
                </div>
                <input 
                    type="text" 
                    id="name" 
                    wire:model.live.debounce.300ms="name"
                    class="w-full px-4 py-3 bg-white/5 border rounded-xl text-white placeholder-slate-500 focus:outline-none transition-all {{ $errors->has('name') ? 'border-rose-500/80 focus:ring-2 focus:ring-rose-500/50' : (strlen($name) >= 3 ? 'border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/30' : 'border-white/10 focus:ring-2 focus:ring-blue-500/50') }}"
                    placeholder="John Doe"
                >
                @error('name')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="email" class="block text-sm font-medium text-slate-300">Email Address</label>
                    @if(!$errors->has('email') && filter_var($email, FILTER_VALIDATE_EMAIL))
                        <span class="text-xs text-emerald-400 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Available
                        </span>
                    @endif
                </div>
                <input 
                    type="email" 
                    id="email" 
                    wire:model.live.debounce.300ms="email"
                    class="w-full px-4 py-3 bg-white/5 border rounded-xl text-white placeholder-slate-500 focus:outline-none transition-all {{ $errors->has('email') ? 'border-rose-500/80 focus:ring-2 focus:ring-rose-500/50' : (filter_var($email, FILTER_VALIDATE_EMAIL) ? 'border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/30' : 'border-white/10 focus:ring-2 focus:ring-blue-500/50') }}"
                    placeholder="you@company.com"
                >
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Organization Name -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="organization_name" class="block text-sm font-medium text-slate-300">Organization Name</label>
                    @if(!$errors->has('organization_name') && strlen($organization_name) >= 2)
                        <span class="text-xs text-emerald-400 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Valid
                        </span>
                    @endif
                </div>
                <input 
                    type="text" 
                    id="organization_name" 
                    wire:model.live.debounce.300ms="organization_name"
                    class="w-full px-4 py-3 bg-white/5 border rounded-xl text-white placeholder-slate-500 focus:outline-none transition-all {{ $errors->has('organization_name') ? 'border-rose-500/80 focus:ring-2 focus:ring-rose-500/50' : (strlen($organization_name) >= 2 ? 'border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/30' : 'border-white/10 focus:ring-2 focus:ring-blue-500/50') }}"
                    placeholder="Your Company"
                >
                @error('organization_name')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password -->
            <div x-data="{ showPassword: false }">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                    @if(strlen($password) >= 8)
                        <span class="text-xs text-emerald-400 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> 8+ Chars
                        </span>
                    @elseif(strlen($password) > 0)
                        <span class="text-xs text-amber-400 font-medium">
                            {{ 8 - strlen($password) }} more chars needed
                        </span>
                    @endif
                </div>
                <div class="relative">
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        id="password" 
                        wire:model.live.debounce.300ms="password"
                        class="w-full px-4 py-3 pr-12 bg-white/5 border rounded-xl text-white placeholder-slate-500 focus:outline-none transition-all {{ $errors->has('password') ? 'border-rose-500/80 focus:ring-2 focus:ring-rose-500/50' : (strlen($password) >= 8 ? 'border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/30' : 'border-white/10 focus:ring-2 focus:ring-blue-500/50') }}"
                        placeholder="••••••••"
                    >
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition-colors focus:outline-none">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.557 4.127M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" /></svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div x-data="{ showConfirmPassword: false }">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300">Confirm Password</label>
                    @if(strlen($password_confirmation) > 0 && $password === $password_confirmation)
                        <span class="text-xs text-emerald-400 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Passwords Match
                        </span>
                    @endif
                </div>
                <div class="relative">
                    <input 
                        :type="showConfirmPassword ? 'text' : 'password'" 
                        id="password_confirmation" 
                        wire:model.live.debounce.300ms="password_confirmation"
                        class="w-full px-4 py-3 pr-12 bg-white/5 border rounded-xl text-white placeholder-slate-500 focus:outline-none transition-all {{ $errors->has('password_confirmation') ? 'border-rose-500/80 focus:ring-2 focus:ring-rose-500/50' : (strlen($password_confirmation) > 0 && $password === $password_confirmation ? 'border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/30' : 'border-white/10 focus:ring-2 focus:ring-blue-500/50') }}"
                        placeholder="••••••••"
                    >
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition-colors focus:outline-none">
                        <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.557 4.127M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" /></svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Terms -->
            <div>
                <div class="flex items-start">
                    <input 
                        type="checkbox" 
                        id="terms" 
                        wire:model.live="terms"
                        class="w-4 h-4 mt-0.5 rounded bg-white/5 border-white/20 text-blue-500 focus:ring-blue-500/50 focus:ring-offset-0 cursor-pointer"
                    >
                    <label for="terms" class="ml-2 text-sm text-slate-400 cursor-pointer">
                        I agree to the <a href="#" class="text-blue-400 hover:text-blue-300 font-medium">Terms of Service</a> and 
                        <a href="#" class="text-blue-400 hover:text-blue-300 font-medium">Privacy Policy</a>
                    </label>
                </div>
                @error('terms')
                    <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <button 
                type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500/50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
                <span wire:loading.remove>Create Account</span>
                <span wire:loading class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating Account...
                </span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium transition-colors">Sign in</a>
        </p>
        @endif
    </div>
</div>
