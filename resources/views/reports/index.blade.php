@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="space-y-8 font-inter">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Reports & Analytics</h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1 font-medium">Download and analyze event performance reports</p>
        </div>
    </div>

    <!-- Report Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Attendance Report -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl hover:border-blue-500/40 transition-all group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Attendance Summary</h3>
                <p class="text-slate-600 dark:text-slate-400 text-xs font-medium leading-relaxed mb-6">Complete breakdown of verified attendees vs. check-in totals across all gates.</p>
            </div>
            <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-white/10">
                <button class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-blue-500/20 transition-all cursor-pointer">Export CSV</button>
                <button class="px-4 py-2.5 bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white text-xs font-semibold rounded-xl transition-all cursor-pointer">Export PDF</button>
            </div>
        </div>

        <!-- Verification Report -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl hover:border-emerald-500/40 transition-all group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Pre-Event Verification</h3>
                <p class="text-slate-600 dark:text-slate-400 text-xs font-medium leading-relaxed mb-6">Audit log of email/OTP verifications, pending approvals, and rejected registrations.</p>
            </div>
            <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-white/10">
                <button class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-emerald-500/20 transition-all cursor-pointer">Export CSV</button>
                <button class="px-4 py-2.5 bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white text-xs font-semibold rounded-xl transition-all cursor-pointer">Export PDF</button>
            </div>
        </div>

        <!-- Gate Activity Report -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl hover:border-purple-500/40 transition-all group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Gate Activity & Scans</h3>
                <p class="text-slate-600 dark:text-slate-400 text-xs font-medium leading-relaxed mb-6">Detailed scan logs including authorized access grants and denied attempts.</p>
            </div>
            <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-white/10">
                <button class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-purple-500/20 transition-all cursor-pointer">Export CSV</button>
                <button class="px-4 py-2.5 bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white text-xs font-semibold rounded-xl transition-all cursor-pointer">Export PDF</button>
            </div>
        </div>
    </div>
</div>
@endsection
