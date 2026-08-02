@extends('layouts.guest')

@section('title', 'Sign In — AttendFlow')

@section('content')
<div class="w-full max-w-md">
    <!-- Login Card -->
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-2xl font-bold text-white mb-1">Welcome back</h2>
        <p class="text-slate-400 text-sm mb-6">Sign in to your account to continue</p>

        @if($errors->any())
            <div class="mb-4 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl">
                <ul class="text-rose-400 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('status'))
            <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                <p class="text-emerald-400 text-sm">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required 
                    autofocus
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                    placeholder="you@company.com"
                >
            </div>

            <!-- Password -->
            <div x-data="{ showPassword: false }">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">Forgot password?</a>
                </div>
                <div class="relative">
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-3 pr-12 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                        placeholder="••••••••"
                    >
                    <button 
                        type="button" 
                        @click="showPassword = !showPassword" 
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition-colors focus:outline-none"
                        title="Toggle password visibility"
                    >
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.557 4.127M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                    class="w-4 h-4 rounded bg-white/5 border-white/20 text-blue-500 focus:ring-blue-500/50 focus:ring-offset-0"
                >
                <label for="remember" class="ml-2 text-sm text-slate-400">Remember me</label>
            </div>

            <!-- Submit -->
            <button 
                type="submit"
                class="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500/50 cursor-pointer"
            >
                Sign In
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-medium transition-colors">Create one</a>
        </p>
    </div>
</div>
@endsection
