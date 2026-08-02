@extends('layouts.guest')

@section('title', 'Reset Password — AttendFlow')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-2xl font-bold text-white mb-1">Reset your password</h2>
        <p class="text-slate-400 text-sm mb-6">Enter your email and we'll send you a password reset link.</p>

        @if(session('status'))
            <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                <p class="text-emerald-400 text-sm font-semibold">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                    placeholder="you@company.com">
                @error('email')
                    <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-0.5 transition-all duration-300">
                Send Reset Link
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-400">
            Remember your password? <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium">Sign in</a>
        </p>
    </div>
</div>
@endsection
