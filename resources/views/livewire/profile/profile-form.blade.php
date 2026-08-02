<div class="space-y-8 max-w-5xl font-inter">
    <!-- Header -->
    <div>
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Account Profile & Security</h1>
        <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm mt-1 font-medium">Manage your personal credentials, contact details, and security password.</p>
    </div>

    <!-- User Overview Card -->
    <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 sm:p-6 shadow-sm dark:shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6 relative z-10">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white text-2xl sm:text-3xl font-extrabold shadow-lg shadow-blue-500/30 border-2 border-white/20 shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <h2 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</h2>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ auth()->user()->role_label }}
                    </span>
                </div>
                <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm font-medium mt-1 truncate">{{ auth()->user()->email ?? 'admin@attendflow.com' }}</p>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 mt-3 text-xs text-slate-500 dark:text-slate-400 font-medium">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Org: <strong class="text-slate-800 dark:text-slate-200 ml-1">{{ auth()->user()->organization->name ?? 'AttendFlow SaaS' }}</strong>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Last Login: <strong class="text-slate-800 dark:text-slate-200 ml-1">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Just now' }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- 1. Personal Information Form -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 sm:p-6 shadow-sm dark:shadow-2xl flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-white/10">
                    <div class="p-2.5 bg-blue-500/10 rounded-xl text-blue-600 dark:text-blue-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Personal Information</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Update your account name and email address</p>
                    </div>
                </div>

                @if (session()->has('profile_success'))
                    <div class="mb-4 p-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-600 dark:text-emerald-400 text-xs font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ session('profile_success') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Full Name</label>
                        <input wire:model="name" type="text" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all placeholder-slate-400 font-medium">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input wire:model="email" type="email" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all placeholder-slate-400 font-medium">
                        @error('email') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Phone Number</label>
                        <input wire:model="phone" type="text" maxlength="10" placeholder="0246345698 (10 digits)" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all placeholder-slate-400 font-medium">
                        @error('phone') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex justify-end">
                        <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <span wire:loading.remove>Save Changes</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Change Password & Security -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 sm:p-6 shadow-sm dark:shadow-2xl flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-white/10">
                    <div class="p-2.5 bg-purple-500/10 rounded-xl text-purple-600 dark:text-purple-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Change Password</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Update your security password to protect your account</p>
                    </div>
                </div>

                @if (session()->has('password_success'))
                    <div class="mb-4 p-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-600 dark:text-emerald-400 text-xs font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ session('password_success') }}
                    </div>
                @endif

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <!-- Current Password -->
                    <div x-data="{ show: false }">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Current Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" wire:model="current_password" class="w-full px-4 py-2.5 pr-12 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all placeholder-slate-400 font-medium" placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors focus:outline-none cursor-pointer">
                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.557 4.127M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('current_password') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- New Password -->
                    <div x-data="{ show: false }">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">New Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" wire:model="new_password" class="w-full px-4 py-2.5 pr-12 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all placeholder-slate-400 font-medium" placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors focus:outline-none cursor-pointer">
                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.557 4.127M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('new_password') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div x-data="{ show: false }">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" wire:model="new_password_confirmation" class="w-full px-4 py-2.5 pr-12 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all placeholder-slate-400 font-medium" placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors focus:outline-none cursor-pointer">
                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.557 4.127M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex justify-end">
                        <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-purple-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <span wire:loading.remove>Update Password</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
