@extends('layouts.guest')

@section('title', 'Create Account — AttendFlow')

@section('content')
<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <a href="/" class="inline-flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center font-black text-white text-lg shadow-lg shadow-blue-500/30">
                AF
            </div>
            <span class="text-2xl font-extrabold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                AttendFlow
            </span>
        </a>
    </div>

    <!-- Register Card -->
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-2xl font-bold text-white mb-1">Create your account</h2>
        <p class="text-slate-400 text-sm mb-6">Start managing events in minutes</p>

        @if($errors->any())
            <div class="mb-4 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl">
                <ul class="text-rose-400 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                    placeholder="John Doe">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                    placeholder="you@company.com">
            </div>

            <!-- Organization Name -->
            <div>
                <label for="organization_name" class="block text-sm font-medium text-slate-300 mb-1.5">Organization Name</label>
                <input type="text" id="organization_name" name="organization_name" value="{{ old('organization_name') }}" required
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                    placeholder="Your Company">
            </div>

            <!-- Password -->
            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                        class="w-full px-4 py-3 pr-12 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                        placeholder="••••••••">
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition-colors focus:outline-none">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.557 4.127M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" /></svg>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div x-data="{ showConfirmPassword: false }">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1.5">Confirm Password</label>
                <div class="relative">
                    <input :type="showConfirmPassword ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-3 pr-12 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                        placeholder="••••••••">
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition-colors focus:outline-none">
                        <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.557 4.127M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" /></svg>
                    </button>
                </div>
            </div>

            <!-- Terms -->
            <div class="flex items-start">
                <input type="checkbox" id="terms" name="terms" required
                    class="w-4 h-4 mt-0.5 rounded bg-white/5 border-white/20 text-blue-500 focus:ring-blue-500/50 focus:ring-offset-0">
                <label for="terms" class="ml-2 text-sm text-slate-400">
                    I agree to the <a href="#" class="text-blue-400 hover:text-blue-300">Terms of Service</a> and 
                    <a href="#" class="text-blue-400 hover:text-blue-300">Privacy Policy</a>
                </label>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500/50 cursor-pointer">
                Create Account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium transition-colors">Sign in</a>
        </p>
    </div>
</div>
@endsection
