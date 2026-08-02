@extends('layouts.app')

@section('title', 'Organization Settings')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div>
        <h1 class="text-2xl font-bold text-white">Organization Settings</h1>
        <p class="text-slate-400 text-sm">Manage your brand preferences, domain settings, and API access.</p>
    </div>

    <!-- Settings Form -->
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-xl space-y-6">
        <h2 class="text-lg font-semibold text-white">General Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Organization Name</label>
                <input type="text" value="{{ auth()->user()->organization->name ?? 'TechConf Global' }}" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Primary Brand Color</label>
                <div class="flex items-center gap-3">
                    <input type="color" value="{{ auth()->user()->organization->brand_color ?? '#3b82f6' }}" class="w-10 h-10 rounded-lg bg-transparent border border-white/10 cursor-pointer">
                    <input type="text" value="{{ auth()->user()->organization->brand_color ?? '#3b82f6' }}" class="flex-1 px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-white/10 flex justify-end">
            <button type="button" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 transition-all">
                Save Changes
            </button>
        </div>
    </div>
</div>
@endsection
