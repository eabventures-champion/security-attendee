<div class="space-y-8 font-inter">
@if(!$event)
    <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-3xl p-12 text-center shadow-xl space-y-4 max-w-2xl mx-auto my-12">
        <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center mx-auto">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">No Reports Available</h2>
        <p class="text-slate-600 dark:text-slate-400 text-sm">Create an event to start generating attendance analytics and scan reports.</p>
        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition-all">
            Go to Events
        </a>
    </div>
@else
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('events.show', $event->uuid) }}" class="p-2 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer shrink-0" title="Back to Event Dashboard">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Reports & Gate Audit Logs</h1>
            </div>
            <p class="text-slate-600 dark:text-slate-400 mt-1 font-medium text-xs sm:text-sm sm:ml-10">Export reports and analyze live gate check-in logs for <strong class="text-slate-900 dark:text-white">{{ $event->name }}</strong>.</p>
        </div>

        <!-- Organization Workspace & Event Switcher -->
        @if(isset($organizationsTree) && $organizationsTree->isNotEmpty())
            <div class="flex items-center gap-2 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3.5 py-2.5 rounded-xl shadow-sm hover:border-blue-500/30 transition-all w-full md:w-auto shrink-0">
                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <select @change="$wire.switchEvent($event.target.value)" class="bg-transparent text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none cursor-pointer pr-2 w-full">
                    @foreach($organizationsTree as $org)
                        @php
                            $adminUser = $org->users->first();
                        @endphp
                        <optgroup label="🏢 {{ $org->name }} (Admin: {{ $adminUser ? $adminUser->name : 'Unassigned' }})" class="bg-slate-900 text-purple-400 font-extrabold">
                            @foreach($org->events as $evt)
                                <option value="{{ $evt->uuid }}" {{ $event && $evt->id === $event->id ? 'selected' : '' }} class="bg-slate-900 text-slate-100 font-medium">
                                    📅 {{ $evt->name }} ({{ number_format($evt->total_scans_count ?? 0) }} Scans)
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <!-- Organization Workspaces & Events Audit Hierarchy Card for Super Admin -->
    @if($isSuperAdmin && isset($organizationsTree) && $organizationsTree->isNotEmpty())
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl overflow-hidden">
            <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 uppercase tracking-widest w-max">
                    ORGANIZATION WORKSPACES & AUDIT LOGS
                </span>
                <span class="text-xs text-slate-600 dark:text-slate-400 font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    {{ $organizationsTree->count() }} Organization {{ Str::plural('Workspace', $organizationsTree->count()) }}
                </span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach($organizationsTree as $org)
                    @php
                        $adminUser = $org->users->first();
                        $isExpanded = in_array($org->id, $expandedOrgs);
                    @endphp
                    <div class="p-4 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-sm shadow-md shadow-purple-500/20 shrink-0">
                                    {{ strtoupper(substr($org->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $org->name }}</h4>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-400">ORG ID: {{ $org->id }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                        Admin: <strong class="text-slate-700 dark:text-slate-300">{{ $adminUser ? $adminUser->name : 'Unassigned Admin' }}</strong>
                                        @if($adminUser)<span class="text-slate-400">({{ $adminUser->email }})</span>@endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-xl text-xs font-extrabold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                    📊 {{ $org->events->count() }} {{ Str::plural('Event', $org->events->count()) }}
                                </span>
                                <button type="button" wire:click="toggleExpandOrg({{ $org->id }})" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10 transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>View Audit Events ({{ $org->events->count() }})</span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isExpanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                        </div>

                        @if($isExpanded)
                            <div class="mt-4 pl-0 sm:pl-8 border-l-0 sm:border-l-2 border-purple-500/30 space-y-3 pt-2">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @forelse($org->events as $evt)
                                        <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl p-4 space-y-3 shadow-md">
                                            <div class="flex items-center justify-between">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $evt->status->badgeClass() }}">
                                                    {{ $evt->status->label() }}
                                                </span>
                                                <span class="text-xs text-slate-400 font-medium">
                                                    {{ number_format($evt->total_scans_count ?? 0) }} Scans
                                                </span>
                                            </div>

                                            <h5 class="text-sm font-extrabold text-slate-900 dark:text-white truncate">{{ $evt->name }}</h5>
                                            
                                            <div class="pt-2 border-t border-slate-200 dark:border-white/10 flex items-center justify-between">
                                                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                                    🚪 {{ $evt->gates_count ?? 0 }} Gates
                                                </span>
                                                <button type="button" wire:click="switchEvent('{{ $evt->uuid }}')" class="px-3 py-1 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-xs shadow-md hover:from-blue-700 hover:to-indigo-700 transition-all cursor-pointer">
                                                    View Audit Log ➔
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-span-full p-4 rounded-xl bg-slate-900/40 text-center text-slate-500 text-xs italic">
                                            No events created under this Organization Workspace yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Stat Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="p-3.5 sm:p-5 rounded-2xl bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 shadow-sm dark:shadow-xl">
            <span class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Gate Scans</span>
            <div class="mt-1 sm:mt-2 text-xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($totalScans) }}</div>
            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 font-medium">All check-in attempts</p>
        </div>

        <div class="p-3.5 sm:p-5 rounded-2xl bg-white dark:bg-white/5 backdrop-blur-xl border border-emerald-500/20 bg-emerald-500/5 shadow-sm dark:shadow-xl">
            <span class="text-[10px] sm:text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Granted Entry</span>
            <div class="mt-1 sm:mt-2 text-xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($grantedScans) }}</div>
            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 font-medium">Successful check-ins</p>
        </div>

        <div class="p-3.5 sm:p-5 rounded-2xl bg-white dark:bg-white/5 backdrop-blur-xl border border-rose-500/20 bg-rose-500/5 shadow-sm dark:shadow-xl col-span-2 sm:col-span-1">
            <span class="text-[10px] sm:text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Denied Attempts</span>
            <div class="mt-1 sm:mt-2 text-xl sm:text-3xl font-extrabold text-rose-600 dark:text-rose-400">{{ number_format($deniedScans) }}</div>
            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 sm:mt-1 font-medium">Invalid or revoked passes</p>
        </div>
    </div>

    <!-- Report Export Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Attendance Summary Report Card -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl hover:border-blue-500/40 transition-all group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Attendance Summary</h3>
                <p class="text-slate-600 dark:text-slate-400 text-xs font-medium leading-relaxed mb-6">Complete breakdown of verified attendees vs. check-in totals across all gates.</p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex gap-2">
                <button wire:click="exportCsv('attendance')" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                    Export CSV
                </button>
            </div>
        </div>

        <!-- Verification Report Card -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl hover:border-emerald-500/40 transition-all group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Pre-Event Verification</h3>
                <p class="text-slate-600 dark:text-slate-400 text-xs font-medium leading-relaxed mb-6">Audit log of email verifications, pending approvals, and attendee registrations.</p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex gap-2">
                <button wire:click="exportCsv('verification')" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-500/20 transition-all cursor-pointer">
                    Export CSV
                </button>
            </div>
        </div>

        <!-- Gate Activity Report Card -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm dark:shadow-2xl hover:border-purple-500/40 transition-all group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Gate Activity & Scans</h3>
                <p class="text-slate-600 dark:text-slate-400 text-xs font-medium leading-relaxed mb-6">Detailed scan logs including authorized access grants and denied attempts.</p>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex gap-2">
                <button wire:click="exportCsv('gate')" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-purple-500/20 transition-all cursor-pointer">
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Live Gate Check-ins & Audit Logs Table -->
    <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl overflow-hidden space-y-4 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-100 dark:border-white/10">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Gate Check-ins Audit Log
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Real-time log of every scan attempt during and after the meeting.</p>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by attendee name or email..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <select wire:model.live="gateFilter" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All Gates</option>
                    @foreach($gates as $gt)
                        <option value="{{ $gt->id }}">{{ $gt->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="resultFilter" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All Scan Results</option>
                    <option value="granted">Granted Only</option>
                    <option value="denied">Denied Only</option>
                </select>
            </div>
        </div>

        <!-- Scan Logs Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-white/10 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3 px-4">Attendee</th>
                        <th class="py-3 px-4">Gate Checkpoint</th>
                        <th class="py-3 px-4">Scan Result</th>
                        <th class="py-3 px-4">Scanned At</th>
                        <th class="py-3 px-4 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-xs text-slate-700 dark:text-slate-300 font-medium">
                    @forelse($scanLogs as $log)
                        @php
                            $isGranted = is_object($log->scan_result) ? $log->scan_result->value === 'granted' : $log->scan_result === 'granted';
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $log->attendee->full_name ?? 'Unknown / Unregistered' }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $log->attendee->email ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $log->gate->name ?? 'Main Entrance' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($isGranted)
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        Granted Access
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        Access Denied
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-800 dark:text-slate-200 font-mono">
                                {{ $log->scanned_at ? $log->scanned_at->format('M j, Y @ g:i:s A') : $log->created_at->format('M j, Y @ g:i:s A') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-500 dark:text-slate-400 text-[11px]">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 font-medium">
                                No check-in scan records found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($scanLogs->hasPages())
            <div class="pt-4 border-t border-slate-100 dark:border-white/10">
                {{ $scanLogs->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
@endif
</div>
