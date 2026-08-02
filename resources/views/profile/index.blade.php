@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div>
        <h1 class="text-2xl font-bold text-white">Account Profile</h1>
        <p class="text-slate-400 text-sm">Update your personal account details and security credentials.</p>
    </div>

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold border-4 border-slate-800">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ auth()->user()->name ?? 'Admin User' }}</h2>
                <p class="text-slate-400 text-sm">{{ auth()->user()->email ?? 'admin@attendflow.com' }}</p>
                <span class="inline-block mt-2 px-3 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-semibold rounded-full">
                    {{ auth()->user()->role_label }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-white/10">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Full Name</label>
                <input type="text" value="{{ auth()->user()->name ?? '' }}" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                <input type="email" value="{{ auth()->user()->email ?? '' }}" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="pt-4 border-t border-white/10 flex justify-end">
            <button type="button" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all">
                Update Profile
            </button>
        </div>
    </div>
</div>
@endsection
