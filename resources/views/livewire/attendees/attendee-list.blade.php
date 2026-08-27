<div class="space-y-8 font-inter">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-1">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Attendees</h1>
                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-sm">
                    {{ number_format($totalCount) }} Registered
                </span>
                @if(($totalCount ?? 0) > 0)
                    <!-- Delete All next to count badge -->
                    <button wire:click="deleteAllFilteredAttendees" 
                            wire:confirm="⚠️ PERMANENT DATABASE DELETION: Are you sure you want to permanently delete ALL {{ number_format($totalCount) }} attendee(s) currently shown in this table? This will permanently delete their records, QR passes, and check-ins from the database. This action CANNOT be undone." 
                            class="px-2.5 py-1 rounded-full border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer shadow-sm hover:shadow active:scale-95" 
                            title="Permanently delete all attendees currently shown in this table from the database">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>Delete All ({{ number_format($totalCount) }})</span>
                    </button>
                @endif
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-medium text-xs sm:text-sm whitespace-normal sm:whitespace-nowrap">
                Manage registrations, security passes, verifications, and access control.
            </p>
        </div>

        <!-- Right-Aligned Premium Action Toolbar -->
        <div class="flex flex-wrap items-center justify-start lg:justify-end gap-2 sm:gap-2.5 shrink-0">
            <!-- 1. Import & Export Dropdown -->
            <div class="relative" x-data="{ openCsvMenu: false }">
                <button @click="openCsvMenu = !openCsvMenu" 
                        type="button" 
                        class="h-10 px-3.5 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 hover:bg-slate-50 dark:hover:bg-white/10 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span>Import / Export</span>
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Dropdown options -->
                <div x-show="openCsvMenu" 
                     @click.away="openCsvMenu = false" 
                     x-transition 
                     x-cloak
                     class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 p-2 z-50 space-y-1">
                    
                    <!-- Import CSV -->
                    <button type="button"
                            wire:click="openImportCsvModal"
                            @click="openCsvMenu = false"
                            class="w-full text-left p-2.5 rounded-xl hover:bg-emerald-500/10 transition-colors cursor-pointer group">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-500">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span>Import CSV</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 ml-6">Upload and import attendees via spreadsheet.</p>
                    </button>

                    <!-- Export CSV -->
                    <button type="button"
                            wire:click="export"
                            wire:loading.attr="disabled"
                            @click="openCsvMenu = false"
                            class="w-full text-left p-2.5 rounded-xl hover:bg-blue-500/10 transition-colors cursor-pointer group border-t border-slate-100 dark:border-white/5">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-500">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Export CSV</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 ml-6">Download current attendees to CSV.</p>
                    </button>
                </div>
            </div>

            <!-- 2. Invitations Dropdown (Secure Link & Bulk Invite) -->
            <div class="relative" x-data="{ openInviteMenu: false }">
                <button @click="openInviteMenu = !openInviteMenu" 
                        type="button" 
                        class="h-10 px-3.5 rounded-xl border border-purple-500/30 bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Invitations</span>
                    <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Dropdown options -->
                <div x-show="openInviteMenu" 
                     @click.away="openInviteMenu = false" 
                     x-transition 
                     x-cloak
                     class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 p-2 z-50 space-y-1">
                    
                    <!-- Bulk Invite -->
                    <button type="button"
                            wire:click="openBulkInviteModal"
                            @click="openInviteMenu = false"
                            class="w-full text-left p-2.5 rounded-xl hover:bg-purple-500/10 transition-colors cursor-pointer group">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-purple-500">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>Bulk Invitations</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 ml-6">Send invitation emails to attendee lists.</p>
                    </button>

                    <!-- Secure Single-Use Link -->
                    <button type="button"
                            wire:click="openLinkGeneratorModal"
                            @click="openInviteMenu = false"
                            class="w-full text-left p-2.5 rounded-xl hover:bg-amber-500/10 transition-colors cursor-pointer group border-t border-slate-100 dark:border-white/5">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-amber-500">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            <span>Secure Single-Use Link</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 ml-6">Generate private single-use registration link.</p>
                    </button>
                </div>
            </div>

            <!-- Reset Logs Dropdown -->
            <div class="relative" x-data="{ openResetMenu: false }">
                <button @click="openResetMenu = !openResetMenu" 
                        type="button" 
                        class="h-10 px-3.5 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 hover:bg-slate-50 dark:hover:bg-white/10 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95" 
                        title="Reset delivery logs or reset attendees for testing">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Reset</span>
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Dropdown options -->
                <div x-show="openResetMenu" 
                     @click.away="openResetMenu = false" 
                     x-transition 
                     x-cloak
                     class="absolute right-0 mt-2 w-72 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 p-2 z-50 space-y-1">
                    
                    <!-- Reset 1: Clean Log Only -->
                    <button type="button"
                            wire:click="clearDeliveryLogsOnly"
                            wire:confirm="🧹 Clear all email delivery logs? Attendee verification statuses and QR passes will NOT be modified."
                            @click="openResetMenu = false"
                            class="w-full text-left p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer group">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-500">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span>Reset Delivery Log Only</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 ml-6">Clears log records. Keeps attendee verification and QR passes intact.</p>
                    </button>

                    <!-- Reset 2: Full Reset (Logs + Attendee Status & QR Codes) -->
                    <button type="button"
                            wire:click="fullResetLogsAndAttendeeStatus"
                            wire:confirm="🔄 FULL RESET WARNING: This will clear all delivery logs, remove generated QR passes, and reset all attendees to 'Pending' so you can freshly test 'Approve All' or bulk passes. Continue?"
                            @click="openResetMenu = false"
                            class="w-full text-left p-2.5 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors cursor-pointer group border-t border-slate-100 dark:border-white/5">
                        <div class="flex items-center gap-2 text-xs font-bold text-rose-600 dark:text-rose-400">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span>Full Reset (Logs + QR Pass Status)</span>
                        </div>
                        <p class="text-[10px] text-rose-500/80 dark:text-rose-400/80 mt-0.5 ml-6">Clears logs AND resets attendees to Pending for fresh bulk re-testing.</p>
                    </button>
                </div>
            </div>

            <!-- Add Attendee -->
            <button wire:click="openAddModal" class="h-10 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg> 
                Add
            </button>
        </div>
    </div>

    <!-- Summary Stat Cards (2 Cols on Mobile, 4 Cols on Large Screens) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 animate-fadeInUp">
        <!-- Total Attendees Card -->
        <button type="button" wire:click="$set('statusFilter', '')" class="text-left p-3.5 sm:p-5 rounded-2xl bg-white dark:bg-white/5 backdrop-blur-xl border transition-all cursor-pointer group shadow-sm dark:shadow-xl {{ $statusFilter === '' ? 'border-blue-500 ring-2 ring-blue-500/20 bg-blue-500/5' : 'border-slate-200 dark:border-white/10 hover:border-blue-500/40' }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Attendees</span>
                <div class="p-2 sm:p-2.5 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 flex items-baseline justify-between">
                <span class="text-xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($totalCount) }}</span>
                <span class="text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300">Total</span>
            </div>
        </button>

        <!-- Verified Attendees Card -->
        <button type="button" wire:click="$set('statusFilter', 'verified')" class="text-left p-3.5 sm:p-5 rounded-2xl bg-white dark:bg-white/5 backdrop-blur-xl border transition-all cursor-pointer group shadow-sm dark:shadow-xl {{ $statusFilter === 'verified' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-500/5' : 'border-slate-200 dark:border-white/10 hover:border-emerald-500/40' }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider truncate">Verified</span>
                <div class="p-2 sm:p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 flex items-baseline justify-between">
                <span class="text-xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($verifiedCount) }}</span>
                <span class="text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    {{ $totalCount > 0 ? round(($verifiedCount / $totalCount) * 100) . '%' : '0%' }}
                </span>
            </div>
        </button>

        <!-- Pending Attendees Card -->
        <button type="button" wire:click="$set('statusFilter', 'pending')" class="text-left p-3.5 sm:p-5 rounded-2xl bg-white dark:bg-white/5 backdrop-blur-xl border transition-all cursor-pointer group shadow-sm dark:shadow-xl {{ $statusFilter === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-500/5' : 'border-slate-200 dark:border-white/10 hover:border-amber-500/40' }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider truncate">Pending</span>
                <div class="p-2 sm:p-2.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 flex items-baseline justify-between">
                <span class="text-xl sm:text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($pendingCount) }}</span>
                <span class="text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    {{ $totalCount > 0 ? round(($pendingCount / $totalCount) * 100) . '%' : '0%' }}
                </span>
            </div>
        </button>

        <!-- Rejected Attendees Card -->
        <button type="button" wire:click="$set('statusFilter', 'rejected')" class="text-left p-3.5 sm:p-5 rounded-2xl bg-white dark:bg-white/5 backdrop-blur-xl border transition-all cursor-pointer group shadow-sm dark:shadow-xl {{ $statusFilter === 'rejected' ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-500/5' : 'border-slate-200 dark:border-white/10 hover:border-rose-500/40' }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider truncate">Rejected</span>
                <div class="p-2 sm:p-2.5 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 flex items-baseline justify-between">
                <span class="text-xl sm:text-3xl font-extrabold text-rose-600 dark:text-rose-400">{{ number_format($rejectedCount) }}</span>
                <span class="text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-600 dark:text-rose-400">
                    {{ $totalCount > 0 ? round(($rejectedCount / $totalCount) * 100) . '%' : '0%' }}
                </span>
            </div>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 sm:p-5 shadow-sm dark:shadow-xl space-y-3 sm:space-y-4">
        <!-- Search Input Row -->
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-4 py-2.5 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm font-medium transition-colors" placeholder="Search by name, email, or QR code...">
        </div>

        <!-- Dropdowns 4-Column Grid Row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 w-full">
            <select wire:model.live="eventUuid" class="block w-full min-w-0 px-3 py-2.5 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm font-medium truncate cursor-pointer">
                <option value="" class="bg-slate-900 text-slate-100">All Events</option>
                @foreach($events as $evt)
                    <option value="{{ $evt->uuid }}" class="bg-slate-900 text-slate-100">{{ $evt->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="statusFilter" class="block w-full min-w-0 px-3 py-2.5 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm font-medium truncate cursor-pointer">
                <option value="" class="bg-slate-900 text-slate-100">All Statuses ({{ $totalCount }})</option>
                <option value="verified" class="bg-slate-900 text-slate-100">Verified ({{ $verifiedCount }})</option>
                <option value="pending" class="bg-slate-900 text-slate-100">Pending ({{ $pendingCount }})</option>
                <option value="rejected" class="bg-slate-900 text-slate-100">Rejected ({{ $rejectedCount }})</option>
            </select>
            <select wire:model.live="roleFilter" class="block w-full min-w-0 px-3 py-2.5 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm font-medium truncate cursor-pointer">
                <option value="" class="bg-slate-900 text-slate-100">All Roles</option>
                @foreach(\App\Enums\AccessRole::attendeeCases() as $role)
                    <option value="{{ $role->value }}" class="bg-slate-900 text-slate-100">{{ $role->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="categoryFilter" class="block w-full min-w-0 px-3 py-2.5 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm font-medium truncate cursor-pointer">
                <option value="" class="bg-slate-900 text-slate-100">All Categories</option>
                <option value="details" class="bg-slate-900 text-slate-100">📋 Form Details</option>
                <option value="no_details" class="bg-slate-900 text-slate-100">⚡ No Details (Direct Claim)</option>
            </select>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    @if(count($selectedAttendees ?? []) > 0)
        <div class="bg-slate-900/95 dark:bg-slate-900/95 border border-blue-500/30 dark:border-blue-500/40 rounded-2xl p-4 shadow-xl backdrop-blur-xl flex flex-col xl:flex-row items-start xl:items-center justify-between gap-4 animate-fadeIn">
            
            <!-- Left: Selection Info & Quick Selector -->
            <div class="flex items-center gap-3.5">
                <div class="p-2.5 rounded-xl bg-blue-500/20 text-blue-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-extrabold text-white">
                            {{ number_format(count($selectedAttendees)) }} <span class="font-normal text-slate-300">attendee(s) selected</span>
                        </span>
                        <span class="text-slate-600 dark:text-slate-500">•</span>
                        <button type="button" 
                                wire:click="$set('selectedAttendees', [])" 
                                class="text-xs font-semibold text-rose-400 hover:text-rose-300 transition-colors flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Clear
                        </button>
                    </div>
                    @if(count($selectedAttendees) < ($totalCount ?? 0))
                        <p class="text-xs text-slate-400">
                            {{ count($selectedAttendees) }} on this page. 
                            <button type="button" 
                                    wire:click="selectAllFilteredAttendees" 
                                    class="text-blue-400 hover:text-blue-300 font-bold hover:underline cursor-pointer">
                                Select all {{ number_format($totalCount) }} across all pages →
                            </button>
                        </p>
                    @else
                        <p class="text-xs font-bold text-purple-400 flex items-center gap-1">
                            ✨ All {{ number_format(count($selectedAttendees)) }} attendees across all pages are selected.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Right: Action Buttons Row -->
            <div class="flex items-center gap-2.5 flex-wrap xl:flex-nowrap w-full xl:w-auto justify-start xl:justify-end">
                
                <!-- 1. Approve Selected -->
                <button wire:click="bulkApproveAttendees" 
                        wire:confirm="Approve and issue digital QR passes to {{ count($selectedAttendees) }} selected attendee(s)? An email delivery report will be shown after completion." 
                        wire:loading.attr="disabled"
                        wire:target="bulkApproveAttendees,approveAllFilteredAttendees"
                        class="h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold transition-all shadow-md shadow-emerald-500/20 flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-wait shrink-0">
                    <svg wire:loading.remove wire:target="bulkApproveAttendees" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <svg wire:loading wire:target="bulkApproveAttendees" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span wire:loading.remove wire:target="bulkApproveAttendees">Approve &amp; Issue ({{ count($selectedAttendees) }})</span>
                    <span wire:loading wire:target="bulkApproveAttendees">Processing...</span>
                </button>

                <!-- 2. Approve All Filtered (when not all selected) -->
                @if(count($selectedAttendees) < ($totalCount ?? 0))
                    <button wire:click="approveAllFilteredAttendees" 
                            wire:confirm="🚀 Approve ALL {{ number_format($totalCount) }} attendee(s) matching the current filters and send QR passes via email? An email delivery report will be shown after completion."
                            wire:loading.attr="disabled"
                            wire:target="bulkApproveAttendees,approveAllFilteredAttendees"
                            class="h-10 px-4 rounded-xl bg-gradient-to-r from-emerald-600/80 to-teal-600/80 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold transition-all shadow-md shadow-emerald-500/10 flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-wait border border-emerald-400/30 shrink-0">
                        <svg wire:loading.remove wire:target="approveAllFilteredAttendees" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <svg wire:loading wire:target="approveAllFilteredAttendees" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span wire:loading.remove wire:target="approveAllFilteredAttendees">Approve All ({{ number_format($totalCount) }})</span>
                        <span wire:loading wire:target="approveAllFilteredAttendees">Processing...</span>
                    </button>
                @endif

                <!-- 3. Change Role Dropdown -->
                <div x-data="{ open: false }" class="relative shrink-0">
                    <button @click="open = !open" 
                            type="button" 
                            class="h-10 px-3.5 rounded-xl border border-indigo-500/30 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        <span>Change Role</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-slate-800 border border-white/10 rounded-xl shadow-2xl z-50 py-1 overflow-hidden">
                        @foreach(\App\Enums\AccessRole::attendeeCases() as $role)
                            <button wire:click="bulkChangeRole('{{ $role->value }}')" @click="open = false" class="block w-full text-left px-4 py-2 text-xs font-medium text-slate-300 hover:bg-white/10 hover:text-white transition-colors cursor-pointer">
                                {{ $role->label() }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Delete Selected -->
                <button wire:click="bulkDeleteAttendees" 
                        wire:confirm="⚠️ PERMANENT DATABASE DELETION: Are you sure you want to permanently delete {{ count($selectedAttendees) }} selected attendee(s) from the database? This action will remove all passes, check-ins, and data, and CANNOT be undone." 
                        class="h-10 px-3.5 rounded-xl bg-rose-600/90 hover:bg-rose-600 text-white text-xs font-bold transition-all shadow-md shadow-rose-500/20 flex items-center gap-1.5 cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Delete ({{ count($selectedAttendees) }})</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-semibold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- View Switcher Bar for Super Admin -->
    @if($isSuperAdmin)
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 sm:p-5 shadow-sm dark:shadow-xl space-y-4">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 shrink-0 shadow-md">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white">Super Admin Hierarchy View</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Toggle between Workspace-Encased Event Tree and Flat Attendee List</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 bg-slate-100 dark:bg-slate-900/80 p-1.5 rounded-2xl border border-slate-200 dark:border-white/10 w-full md:w-auto">
                    <button type="button" wire:click="$set('groupedView', true)" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer text-center flex items-center justify-center gap-1.5 {{ $groupedView ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-lg shadow-purple-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>🏢</span>
                        <span>Encased Tree</span>
                    </button>
                    <button type="button" wire:click="$set('groupedView', false)" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer text-center flex items-center justify-center gap-1.5 {{ !$groupedView ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-lg shadow-purple-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>📋</span>
                        <span>All Attendees</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($isSuperAdmin && $groupedView)
        <!-- Encased Organization & Event Hierarchy View -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl overflow-hidden">
            <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 uppercase tracking-widest w-max">
                    ORGANIZATION & EVENT HIERARCHY
                </span>
                <span class="text-xs text-slate-600 dark:text-slate-400 font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    {{ $organizationsTree->count() }} Organization {{ Str::plural('Workspace', $organizationsTree->count()) }}
                </span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse($organizationsTree as $org)
                    @php
                        $orgAdmin = $org->users->firstWhere(fn($u) => $u->hasRole('organization_admin')) ?? $org->users->first();
                        $isOrgExpanded = in_array($org->id, $expandedOrgs);
                        $totalOrgAttendees = $org->events->sum('attendees_count');
                    @endphp
                    <div class="p-5 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md shrink-0">
                                    {{ strtoupper(substr($org->name ?? 'Org', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-slate-900 dark:text-white text-base">{{ $org->name ?? 'Organization Workspace' }}</h4>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 uppercase tracking-wider">
                                            ORG ID: {{ $org->id }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                                        Admin: <strong class="text-slate-700 dark:text-slate-200">{{ $orgAdmin->name ?? 'Unassigned Admin' }}</strong> ({{ $orgAdmin->email ?? 'N/A' }})
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $org->events->count() }} {{ Str::plural('Event', $org->events->count()) }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    {{ $totalOrgAttendees }} {{ Str::plural('Attendee', $totalOrgAttendees) }}
                                </span>

                                <!-- Desktop View Events Toggle -->
                                <button type="button" wire:click="toggleExpandOrg({{ $org->id }})" class="hidden sm:inline-flex px-4 py-2 rounded-xl text-xs font-bold bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition-all cursor-pointer items-center gap-1.5">
                                    @if($isOrgExpanded)
                                        <span>Hide Events</span>
                                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @else
                                        <span>View Events ({{ $org->events->count() }})</span>
                                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @endif
                                </button>

                                <!-- Mobile View Events Modal Trigger -->
                                <button type="button" wire:click="openMobileOrgModal({{ $org->id }})" class="sm:hidden inline-flex px-4 py-2 rounded-xl text-xs font-bold bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/40 transition-all cursor-pointer items-center gap-1.5 shadow-md">
                                    <span>View Events ({{ $org->events->count() }})</span>
                                    <svg class="w-4 h-4 text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        @if($isOrgExpanded)
                            <!-- Expanded Events Sub-List -->
                            <div class="mt-4 pl-4 sm:pl-8 border-l-2 border-purple-500/30 space-y-3">
                                @forelse($org->events as $evt)
                                    @php
                                        $isEvtExpanded = in_array($evt->id, $expandedEvents);
                                    @endphp
                                    <div class="bg-slate-900/80 rounded-2xl border border-white/10 p-4 space-y-3 shadow-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div>
                                                    <h5 class="text-sm font-extrabold text-white">{{ $evt->name }}</h5>
                                                    <p class="text-[11px] text-slate-400">Status: <span class="capitalize text-emerald-400 font-semibold">{{ $evt->status }}</span></p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <span class="px-3 py-1 rounded-xl text-xs font-extrabold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                                    👥 {{ $evt->attendees_count }} Registered
                                                </span>
                                                <!-- Desktop View Attendees Toggle -->
                                                <button type="button" wire:click="toggleExpandEvent({{ $evt->id }})" class="hidden sm:inline-flex px-3.5 py-1.5 rounded-xl text-xs font-bold bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 transition-all cursor-pointer items-center gap-1.5">
                                                    @if($isEvtExpanded)
                                                        <span>Hide Attendees</span>
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                                    @else
                                                        <span>View Attendees ({{ $evt->attendees_count }})</span>
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                    @endif
                                                </button>

                                                <!-- Mobile View Attendees Button (Pops up Modal) -->
                                                <button type="button" wire:click="openMobileAttendeesModal({{ $evt->id }})" class="sm:hidden inline-flex px-3.5 py-1.5 rounded-xl text-xs font-bold bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 transition-all cursor-pointer items-center gap-1.5">
                                                    <span>View Attendees ({{ $evt->attendees_count }})</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                </button>
                                            </div>
                                        </div>

                                        @if($isEvtExpanded)
                                            <!-- Inline Attendees Sub-Table -->
                                            @php
                                                $evtAttendees = \App\Models\Attendee::where('event_id', $evt->id)->with(['qrCode', 'assignedGate', 'latestCheckIn.gate'])->latest()->get();
                                            @endphp
                                            <div class="pt-3 border-t border-white/10 overflow-x-auto">
                                                <table class="w-full text-left text-xs">
                                                    <thead>
                                                        <tr class="text-slate-400 font-extrabold uppercase border-b border-white/10 pb-2">
                                                            <th class="py-2.5 px-3">Attendee</th>
                                                            <th class="py-2.5 px-3">Email</th>
                                                            <th class="py-2.5 px-3">Role</th>
                                                            <th class="py-2.5 px-3">Check-In</th>
                                                            <th class="py-2.5 px-3">Status</th>
                                                            <th class="py-2.5 px-3 text-right">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-white/5">
                                                        @forelse($evtAttendees as $att)
                                                            <tr class="hover:bg-white/5">
                                                                <td class="py-2.5 px-3 font-bold text-white flex items-center gap-2">
                                                                    <div class="w-7 h-7 rounded-xl bg-blue-500/20 text-blue-300 font-extrabold flex items-center justify-center text-xs">
                                                                        {{ strtoupper(substr($att->full_name, 0, 1)) }}
                                                                    </div>
                                                                    {{ $att->full_name }}
                                                                </td>
                                                                <td class="py-2.5 px-3 text-slate-300">{{ $att->email }}</td>
                                                                <td class="py-2.5 px-3">
                                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase">
                                                                        {{ is_object($att->access_role) ? $att->access_role->label() : ucfirst($att->access_role) }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-2.5 px-3">
                                                                    @php
                                                                        $attCheckIn = $att->latestCheckIn;
                                                                        $attIsCheckedIn = $attCheckIn && ($attCheckIn->scan_result === \App\Enums\ScanResult::Granted || $attCheckIn->scan_result === 'granted');
                                                                    @endphp
                                                                    @if($attIsCheckedIn)
                                                                        <div class="space-y-0.5">
                                                                            <div x-data="{ openScannerInfo: false }" class="relative inline-block">
                                                                                <button @click="openScannerInfo = !openScannerInfo" type="button" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/30 inline-flex items-center gap-1 cursor-pointer transition-all" title="Click to view Security Officer details">
                                                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                                                                                    <span>Checked In</span>
                                                                                    <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                                                </button>

                                                                                <!-- Dropdown with Security Officer info -->
                                                                                <div x-show="openScannerInfo" @click.outside="openScannerInfo = false" x-cloak
                                                                                     class="absolute left-0 mt-1 w-56 bg-slate-900 border border-blue-500/30 rounded-2xl shadow-2xl z-50 p-3 space-y-2 text-xs">
                                                                                    <div class="flex items-center justify-between border-b border-white/10 pb-1.5">
                                                                                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400">Scanned By Security</span>
                                                                                        <span class="text-[10px] text-slate-400 font-mono">{{ $attCheckIn->scanned_at ? $attCheckIn->scanned_at->format('H:i:s') : '' }}</span>
                                                                                    </div>
                                                                                    @php
                                                                                        $attScanner = $attCheckIn->scanner;
                                                                                    @endphp
                                                                                    @if($attScanner)
                                                                                        <div class="space-y-1">
                                                                                            <div class="flex items-center gap-2">
                                                                                                <div class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-300 font-extrabold flex items-center justify-center text-[10px] shrink-0">🛡️</div>
                                                                                                <div class="min-w-0">
                                                                                                    <div class="font-bold text-white text-xs truncate">{{ $attScanner->name }}</div>
                                                                                                    <div class="text-[10px] text-slate-400">Gate: <span class="text-blue-300 font-semibold">{{ $attCheckIn->gate ? $attCheckIn->gate->name : 'Gate' }}</span></div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="pt-1.5 border-t border-white/5 space-y-0.5 text-[11px] text-slate-300">
                                                                                                <div class="flex items-center gap-1.5">
                                                                                                    <svg class="w-3 h-3 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                                                                    <span class="truncate">{{ $attScanner->email }}</span>
                                                                                                </div>
                                                                                                @if($attScanner->phone)
                                                                                                    <div class="flex items-center gap-1.5">
                                                                                                        <svg class="w-3 h-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                                                                        <span>{{ $attScanner->phone }}</span>
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="text-[11px] text-slate-400 italic">
                                                                                            Scanned at: <strong class="text-white">{{ $attCheckIn->gate ? $attCheckIn->gate->name : 'Gate' }}</strong>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            @if($attCheckIn->gate)
                                                                                <div class="text-[9px] font-medium text-slate-400">
                                                                                    {{ $attCheckIn->gate->name }} • {{ $attCheckIn->scanned_at ? $attCheckIn->scanned_at->format('H:i') : '' }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @else
                                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-white/5 text-slate-400 border border-white/10">
                                                                            Not Checked In
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="py-2.5 px-3">
                                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $att->verification_status->badgeClass() }}">
                                                                        {{ $att->verification_status->label() }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-2.5 px-3 text-right">
                                                                    <button wire:click="viewAttendeeDetails('{{ $att->uuid }}')" class="px-2.5 py-1 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 rounded-lg text-[11px] font-bold cursor-pointer">
                                                                        Details
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="py-4 text-center text-slate-500 italic">No registered attendees for this event yet.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-slate-500 text-xs italic">No events created under this organization workspace yet.</div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">No organization workspaces found.</div>
                @endforelse
            </div>
        </div>
    @else
        <!-- Flat Attendees Table -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden shadow-sm dark:shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-white/10 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-4 px-4 w-12">
                            <input type="checkbox" wire:model.live="selectAllOnPage" class="form-checkbox h-4 w-4 text-blue-600 rounded border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="py-4 px-6">Attendee & Contact</th>
                        <th class="py-4 px-6">Role & Event</th>
                        <th class="py-4 px-6">Check-In</th>
                        <th class="py-4 px-6">QR Code</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm text-slate-700 dark:text-slate-300">
                    @forelse($attendees as $attendee)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors {{ in_array($attendee->uuid, $selectedAttendees) ? 'bg-blue-500/5 dark:bg-blue-500/10' : '' }}">
                            <td class="py-4 px-4">
                                <input type="checkbox" wire:model.live="selectedAttendees" value="{{ $attendee->uuid }}" class="form-checkbox h-4 w-4 text-blue-600 rounded border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:ring-blue-500 cursor-pointer">
                            </td>
                            <td class="py-4 px-6">
                                <div wire:click="viewAttendeeDetails('{{ $attendee->uuid }}')" class="flex items-center gap-3.5 cursor-pointer group" title="Click to view full details">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md group-hover:scale-105 transition-transform shrink-0">
                                        {{ strtoupper(substr($attendee->full_name, 0, 1)) }}
                                    </div>
                                    <div class="space-y-1">
                                        <div class="font-bold text-slate-900 dark:text-white group-hover:text-blue-500 transition-colors text-sm">{{ $attendee->full_name }}</div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10">
                                                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                {{ $attendee->email }}
                                            </span>
                                            @if($attendee->phone)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                    {{ $attendee->phone }}
                                                </span>
                                            @endif

                                            @if(str_contains($attendee->email, '@attendflow.pass') || str_starts_with($attendee->phone, '000') || str_contains($attendee->full_name, 'Guest Pass'))
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase tracking-wider" title="Claimed pass directly by clicking poster image (No form entry required)">
                                                    ⚡ No Details
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase tracking-wider" title="Registered by filling in Name, Email & Phone form">
                                                    📋 Form Details
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $attendee->event->name ?? 'General Event' }}</div>
                                <div class="flex flex-col items-start gap-1.5 mt-1">
                                    <!-- Inline Role Toggle -->
                                    <div x-data="{ open: false }" class="relative inline-block">
                                        <button @click="open = !open" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border cursor-pointer hover:opacity-80 transition-opacity
                                            @if(is_object($attendee->access_role))
                                                @switch($attendee->access_role)
                                                    @case(\App\Enums\AccessRole::Vvip)
                                                        bg-amber-500/15 text-amber-500 dark:text-amber-400 border-amber-500/30
                                                        @break
                                                    @case(\App\Enums\AccessRole::Vip)
                                                        bg-purple-500/15 text-purple-600 dark:text-purple-400 border-purple-500/30
                                                        @break
                                                    @case(\App\Enums\AccessRole::Speaker)
                                                        bg-blue-500/15 text-blue-600 dark:text-blue-400 border-blue-500/30
                                                        @break
                                                    @default
                                                        bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20
                                                @endswitch
                                            @else
                                                bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20
                                            @endif
                                        " title="Click to change role">
                                            @if(is_object($attendee->access_role) && $attendee->access_role === \App\Enums\AccessRole::Vvip)
                                                ⭐ {{ $attendee->access_role->label() }}
                                            @elseif(is_object($attendee->access_role) && $attendee->access_role === \App\Enums\AccessRole::Vip)
                                                👑 {{ $attendee->access_role->label() }}
                                            @else
                                                {{ is_object($attendee->access_role) ? $attendee->access_role->label() : ucfirst($attendee->access_role) }}
                                            @endif
                                            <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        <!-- Role Dropdown -->
                                        <div x-show="open" @click.outside="open = false" x-cloak
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             class="absolute left-0 mt-1 w-44 bg-slate-800 border border-white/10 rounded-xl shadow-2xl z-50 py-1 overflow-hidden">
                                            @foreach(\App\Enums\AccessRole::attendeeCases() as $role)
                                                <button
                                                    wire:click="toggleAttendeeRole('{{ $attendee->uuid }}', '{{ $role->value }}')"
                                                    @click="open = false"
                                                    class="block w-full text-left px-3 py-1.5 text-xs font-medium transition-colors cursor-pointer
                                                        {{ is_object($attendee->access_role) && $attendee->access_role->value === $role->value ? 'bg-blue-500/20 text-blue-300' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}
                                                    ">
                                                    @if($role === \App\Enums\AccessRole::Vvip) ⭐ @elseif($role === \App\Enums\AccessRole::Vip) 👑 @endif
                                                    {{ $role->label() }}
                                                    @if(is_object($attendee->access_role) && $attendee->access_role->value === $role->value)
                                                        <span class="float-right text-blue-400">✓</span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if((is_object($attendee->access_role) && $attendee->access_role === \App\Enums\AccessRole::Security) || $attendee->access_role === 'security')
                                        <div x-data="{ openGate: false }" class="relative inline-block">
                                            <button @click="openGate = !openGate" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30 hover:bg-amber-500/25 transition-all cursor-pointer">
                                                <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                {{ $attendee->assignedGate ? 'Gate: ' . $attendee->assignedGate->name : '⚠️ Assign Gate' }}
                                                <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                            <div x-show="openGate" @click.outside="openGate = false" x-cloak class="absolute left-0 mt-1 w-52 bg-slate-800 border border-amber-500/30 rounded-xl shadow-2xl z-50 py-1 overflow-hidden">
                                                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-amber-400 border-b border-slate-700">Assign Security Gate</div>
                                                <button wire:click="assignGateToAttendee('{{ $attendee->uuid }}', null)" @click="openGate = false" class="block w-full text-left px-3 py-1.5 text-xs text-slate-400 hover:bg-slate-700 hover:text-white transition-colors cursor-pointer">
                                                    -- Unassigned --
                                                </button>
                                                @foreach($availableGates as $g)
                                                    @if($g->event_id == $attendee->event_id)
                                                        <button wire:click="assignGateToAttendee('{{ $attendee->uuid }}', '{{ $g->id }}')" @click="openGate = false" class="block w-full text-left px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-amber-500/20 hover:text-amber-300 transition-colors cursor-pointer flex items-center justify-between">
                                                            <span>{{ $g->name }}</span>
                                                            @if($attendee->assigned_gate_id == $g->id) <span class="text-amber-400">✓</span> @endif
                                                        </button>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $checkIn = $attendee->latestCheckIn;
                                    $isCheckedIn = $checkIn && ($checkIn->scan_result === \App\Enums\ScanResult::Granted || $checkIn->scan_result === 'granted');
                                    $vStatus = $attendee->verification_status;
                                @endphp

                                @if(is_object($vStatus) && $vStatus === \App\Enums\VerificationStatus::Pending)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        Pending Verification
                                    </span>
                                @elseif(is_object($vStatus) && $vStatus === \App\Enums\VerificationStatus::Rejected)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        Rejected
                                    </span>
                                @elseif($isCheckedIn)
                                    <div class="space-y-1">
                                        <div x-data="{ openScannerInfo: false }" class="relative inline-block">
                                            <button @click="openScannerInfo = !openScannerInfo" type="button" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20 cursor-pointer transition-all" title="Click to view Security Officer details">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                                <span>Checked In</span>
                                                <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>

                                            <!-- Dropdown with Security Personnel Name & Contact -->
                                            <div x-show="openScannerInfo" @click.outside="openScannerInfo = false" x-cloak
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 class="absolute left-0 mt-1 w-60 bg-slate-900 border border-blue-500/30 rounded-2xl shadow-2xl z-50 p-3 space-y-2 text-xs">
                                                
                                                <div class="flex items-center justify-between border-b border-white/10 pb-1.5">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400">Scanned By Security</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">{{ $checkIn->scanned_at ? $checkIn->scanned_at->format('H:i:s') : '' }}</span>
                                                </div>

                                                @php
                                                    $scannerUser = $checkIn->scanner;
                                                @endphp

                                                @if($scannerUser)
                                                    <div class="space-y-1">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-300 font-extrabold flex items-center justify-center text-[10px] shrink-0">
                                                                🛡️
                                                            </div>
                                                            <div class="min-w-0">
                                                                <div class="font-bold text-white text-xs truncate">{{ $scannerUser->name }}</div>
                                                                <div class="text-[10px] text-slate-400">Gate: <span class="text-blue-300 font-semibold">{{ $checkIn->gate ? $checkIn->gate->name : 'Gate' }}</span></div>
                                                            </div>
                                                        </div>

                                                        <div class="pt-1.5 border-t border-white/5 space-y-0.5 text-[11px] text-slate-300">
                                                            <div class="flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                                <span class="truncate">{{ $scannerUser->email }}</span>
                                                            </div>
                                                            @if($scannerUser->phone)
                                                                <div class="flex items-center gap-1.5">
                                                                    <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                                    <span>{{ $scannerUser->phone }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-[11px] text-slate-400 italic">
                                                        Scanned at: <strong class="text-white">{{ $checkIn->gate ? $checkIn->gate->name : 'Gate' }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if($checkIn->gate)
                                            <div class="text-[10px] font-medium text-slate-500 dark:text-slate-400">
                                                {{ $checkIn->gate->name }} • {{ $checkIn->scanned_at ? $checkIn->scanned_at->format('H:i') : '' }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/10">
                                        Not Checked In
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($attendee->qrCode)
                                    <div class="flex flex-col items-start gap-1.5">
                                        <!-- Pass Generated Badge -->
                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Generated
                                        </span>

                                        <!-- Email Delivery Status -->
                                        @php
                                            $emailLogs = $attendee->notificationLogs ? $attendee->notificationLogs->where('channel', \App\Enums\NotificationChannel::Email) : collect();
                                            $hasDeliveredEmail = $emailLogs->whereIn('status', ['delivered', 'sent'])->isNotEmpty();
                                            $latestEmailLog = $emailLogs->sortByDesc('created_at')->first();
                                            $emailStatus = $hasDeliveredEmail ? 'delivered' : ($latestEmailLog ? $latestEmailLog->status : null);
                                            $displayLog = $hasDeliveredEmail ? $emailLogs->whereIn('status', ['delivered', 'sent'])->sortByDesc('created_at')->first() : $latestEmailLog;
                                        @endphp

                                        @if($emailStatus === 'delivered' || $emailStatus === 'sent')
                                            <div class="inline-flex items-center gap-1.5 bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400" title="{{ ($displayLog && $displayLog->sent_at) ? 'Email pass sent ' . $displayLog->sent_at->diffForHumans() : 'Email pass delivered' }}">
                                                    <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span>Email Sent</span>
                                                </span>
                                                <button type="button" wire:click="resendPassEmail('{{ $attendee->uuid }}')" title="Resend QR pass via email" class="text-[10px] text-slate-400 hover:text-emerald-500 hover:bg-emerald-500/10 px-1 py-0.5 rounded font-semibold transition-colors cursor-pointer border-l border-emerald-500/20 pl-1.5">
                                                    Resend
                                                </button>
                                            </div>
                                        @elseif($emailStatus === 'failed')
                                            <div class="inline-flex items-center gap-1.5 bg-rose-500/10 px-2 py-0.5 rounded-lg border border-rose-500/20">
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500 dark:text-rose-400" title="{{ ($displayLog && $displayLog->error_message) ? $displayLog->error_message : 'Email delivery failed' }}">
                                                    <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span>Email Failed</span>
                                                </span>
                                                <button type="button" wire:click="resendPassEmail('{{ $attendee->uuid }}')" title="Retry sending QR pass email" class="text-[10px] text-rose-400 hover:text-white hover:bg-rose-600 px-1.5 py-0.5 rounded font-bold transition-colors cursor-pointer border-l border-rose-500/20 pl-1.5">
                                                    Retry
                                                </button>
                                            </div>
                                        @else
                                            <button type="button" wire:click="resendPassEmail('{{ $attendee->uuid }}')" title="Send QR pass to attendee via email" class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-400 hover:text-blue-500 transition-colors cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                <span>Send Email</span>
                                            </button>
                                        @endif

                                        <!-- WhatsApp Delivery Status -->
                                        @php
                                            $waLog = $attendee->notificationLogs ? $attendee->notificationLogs->where('channel', \App\Enums\NotificationChannel::WhatsApp)->sortByDesc('created_at')->first() : null;
                                            $waStatus = $waLog ? $waLog->status : null;
                                        @endphp

                                        @if($waStatus === 'delivered' || $waStatus === 'sent')
                                            <div class="inline-flex items-center gap-1.5 bg-blue-500/10 px-2 py-0.5 rounded-lg border border-blue-500/20">
                                                <button type="button" wire:click="sendWhatsAppPass('{{ $attendee->uuid }}')" title="WhatsApp Pass Dispatched! Click to open chat again" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-500 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span>WhatsApp Sent</span>
                                                </button>
                                                <button type="button" wire:click="markWhatsAppFailed('{{ $attendee->uuid }}')" title="Not on WhatsApp? Click to mark as Failed" class="text-[10px] text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 px-1 py-0.5 rounded font-semibold transition-colors cursor-pointer border-l border-blue-500/20 pl-1.5">
                                                    ✕ Not on WA
                                                </button>
                                            </div>
                                        @elseif($waStatus === 'failed')
                                            <div class="inline-flex items-center gap-1.5 bg-rose-500/10 px-2 py-0.5 rounded-lg border border-rose-500/20">
                                                <button type="button" wire:click="sendWhatsAppPass('{{ $attendee->uuid }}')" title="{{ $waLog->error_message ?? 'Number is not on WhatsApp or invalid' }}. Click to retry" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500 dark:text-rose-400 hover:text-rose-600 dark:hover:text-rose-300 transition-colors cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-rose-500 dark:text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span>WhatsApp Failed</span>
                                                </button>
                                                <button type="button" wire:click="markWhatsAppSent('{{ $attendee->uuid }}')" title="Confirmed delivered? Click to mark as Sent" class="text-[10px] text-slate-400 hover:text-emerald-500 hover:bg-emerald-500/10 px-1 py-0.5 rounded font-semibold transition-colors cursor-pointer border-l border-rose-500/20 pl-1.5">
                                                    ✓ Mark Sent
                                                </button>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" wire:click="sendWhatsAppPass('{{ $attendee->uuid }}')" title="Click to auto-send QR Pass to attendee via WhatsApp" class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-400 hover:text-emerald-500 transition-colors cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 fill-current text-slate-400" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                    <span>Send WhatsApp</span>
                                                </button>
                                                <button type="button" wire:click="markWhatsAppFailed('{{ $attendee->uuid }}')" title="Not on WhatsApp" class="text-[10px] text-slate-500 hover:text-rose-400 transition-colors cursor-pointer">
                                                    ✕
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs font-medium text-slate-400">Not Generated</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" wire:click="viewAttendeeDetails('{{ $attendee->uuid }}')" class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-blue-500/10 transition-colors cursor-pointer" title="View Full Details">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    @if(($attendee->verification_status->value ?? $attendee->verification_status) === 'pending')
                                        <button type="button" wire:click="verifyAttendee('{{ $attendee->uuid }}')" class="px-2.5 py-1 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg transition-colors cursor-pointer flex items-center gap-1 shadow-md shadow-emerald-500/20" title="Approve Attendee &amp; Issue QR Pass">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Approve &amp; Issue Pass</span>
                                        </button>
                                    @else
                                        <button type="button" wire:click="verifyAttendee('{{ $attendee->uuid }}')" class="p-1.5 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg hover:bg-emerald-500/10 transition-colors cursor-pointer" title="Re-Verify Pass">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    @endif
                                    <button type="button" wire:click="deleteAttendee('{{ $attendee->uuid }}')" wire:confirm="Remove this attendee?" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-500/10 transition-colors cursor-pointer" title="Delete">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                                No attendees found matching your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendees->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-white/10">
                {{ $attendees->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
    @endif

    <!-- Add Attendee Modal -->
    @if($showAddModal)
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-3 sm:p-4 pt-8 sm:pt-4 overflow-y-auto bg-slate-950/80 backdrop-blur-md animate-fadeInUp">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl sm:rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative my-4 sm:my-auto max-h-[85vh] sm:max-h-[90vh] overflow-y-auto custom-scrollbar">
                <!-- Close Button -->
                <button wire:click="closeAddModal" type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-3 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Manually Add Attendee</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Direct admin registration with instant verification & QR pass generation.</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveAttendee" class="space-y-4">
                    <!-- Event Selection -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Select Event <span class="text-rose-500">*</span></label>
                        <select wire:model.live="new_event_id" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                            <option value="" class="bg-slate-900 text-slate-100">-- Choose Target Event --</option>
                            @foreach($events as $evt)
                                <option value="{{ $evt->id }}" class="bg-slate-900 text-slate-100">{{ $evt->name }}</option>
                            @endforeach
                        </select>
                        @error('new_event_id') <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                        <input wire:model.live="new_full_name" type="text" placeholder="e.g. Alexander Vance" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        @error('new_full_name') <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                        <input wire:model.live="new_email" type="email" placeholder="name@example.com" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        @error('new_email') <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Phone Number (Optional)</label>
                        <input wire:model.live.debounce.300ms="new_phone" type="text" maxlength="10" placeholder="0246345698 (10 digits)" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        @error('new_phone') <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Grid for Role & Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Access Role</label>
                            <select wire:model.live="new_access_role" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                                @foreach(\App\Enums\AccessRole::attendeeCases() as $role)
                                    <option value="{{ $role->value }}" class="bg-slate-900 text-slate-100">{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Verification Status</label>
                            <select wire:model.live="new_verification_status" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                                <option value="verified" class="bg-slate-900 text-slate-100">Verified (Pre-approved)</option>
                                <option value="pending" class="bg-slate-900 text-slate-100">Pending Review</option>
                                <option value="rejected" class="bg-slate-900 text-slate-100">Rejected</option>
                            </select>
                        </div>
                    </div>

                    @if($new_access_role === 'security')
                        <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 space-y-2 animate-fadeIn">
                            <label class="block text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Assign Security Personnel To Gate
                            </label>
                            <select wire:model="new_assigned_gate_id" class="w-full bg-slate-900 border border-amber-500/40 rounded-xl px-3 py-2.5 text-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-xs font-medium">
                                <option value="">-- Select Gate (Optional) --</option>
                                @foreach($availableGates as $gt)
                                    <option value="{{ $gt->id }}">{{ $gt->name }} ({{ $gt->event->name ?? 'Event' }})</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400">Assigning a gate will auto-provision a Security User login account (Default password: <strong class="text-amber-300 font-mono">Security@123</strong>).</p>
                        </div>
                    @endif

                    <!-- Auto-Generate QR Code Checkbox -->
                    <div class="pt-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input wire:model.live="auto_generate_qr" type="checkbox" class="form-checkbox h-4 w-4 text-blue-600 rounded border-slate-300 dark:border-white/20 bg-slate-100 dark:bg-white/5 focus:ring-blue-500">
                            <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold">Issue Digital Entry QR Pass immediately</span>
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-white/10">
                        <button wire:click="closeAddModal" type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-white/20 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-semibold text-xs transition-all cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 cursor-pointer">
                            <span wire:loading.remove>Register Attendee</span>
                            <span wire:loading class="flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Registering...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- View Attendee Details Modal -->
    @if($showDetailsModal && $selectedAttendee)
        @php
            $selStatusStr = is_object($selectedAttendee->verification_status) ? $selectedAttendee->verification_status->value : (string)$selectedAttendee->verification_status;
        @endphp
        <div class="fixed inset-0 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn" style="z-index: 99999;">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-2xl max-w-xl w-full p-6 space-y-6 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <!-- Modal Header -->
                <div class="flex justify-between items-start pb-4 border-b border-slate-100 dark:border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-blue-500/25 shrink-0">
                            {{ strtoupper(substr($selectedAttendee->full_name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $selectedAttendee->full_name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                    {{ is_object($selectedAttendee->access_role) ? $selectedAttendee->access_role->label() : ucfirst($selectedAttendee->access_role) }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border 
                                    {{ $selStatusStr === 'verified' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : '' }}
                                    {{ $selStatusStr === 'pending' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20' : '' }}
                                    {{ $selStatusStr === 'rejected' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20' : '' }}
                                ">
                                    {{ is_object($selectedAttendee->verification_status) ? $selectedAttendee->verification_status->label() : ucfirst($selectedAttendee->verification_status) }}
                                </span>
                                @php
                                    $selCheckIn = $selectedAttendee->latestCheckIn;
                                    $selIsCheckedIn = $selCheckIn && ($selCheckIn->scan_result === \App\Enums\ScanResult::Granted || $selCheckIn->scan_result === 'granted');
                                @endphp
                                @if($selIsCheckedIn)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                        ✓ Checked In @if($selCheckIn->gate) ({{ $selCheckIn->gate->name }}) @endif
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/10">
                                        Not Checked In
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button wire:click="closeDetailsModal" class="p-2 rounded-xl text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Professional Details Highlight Section -->
                <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-500/10 via-indigo-500/10 to-purple-500/10 border border-blue-500/20">
                    <h4 class="text-xs font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Professional Background
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 font-semibold block mb-1">Company / Organization</span>
                            <span class="font-bold text-slate-900 dark:text-white text-sm">
                                {{ $selectedAttendee->company ? $selectedAttendee->company : 'None (Ordinary Individual)' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 font-semibold block mb-1">Job Title / Occupation</span>
                            <span class="font-bold text-slate-900 dark:text-white text-sm">
                                {{ $selectedAttendee->job_title ? $selectedAttendee->job_title : 'None Specified' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Personal Contact Details -->
                <div class="space-y-2">
                    <h4 class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Contact & Event Profile</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-slate-50 dark:bg-white/5 p-4 rounded-2xl border border-slate-200 dark:border-white/10">
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 font-semibold block mb-0.5">Email Address</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $selectedAttendee->email }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 font-semibold block mb-0.5">Phone Contact</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $selectedAttendee->phone ?? 'Not Provided' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 font-semibold block mb-0.5">Target Event</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $selectedAttendee->event->name ?? 'General Event' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 font-semibold block mb-0.5">Registered On</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $selectedAttendee->created_at ? $selectedAttendee->created_at->format('M j, Y @ g:i A') : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Registration Reason / Access Note (If Provided) -->
                @if(!empty($selectedAttendee->registration_reason))
                    <div class="space-y-2">
                        <h4 class="text-xs font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Reason for Requesting Attendance (Applicant Note)
                        </h4>
                        <div class="bg-amber-500/10 p-4 rounded-2xl border border-amber-500/20 text-xs font-medium text-slate-800 dark:text-amber-200">
                            {{ $selectedAttendee->registration_reason }}
                        </div>
                    </div>
                @endif

                <!-- Custom Extra Fields Answers (If Submitted) -->
                @php
                    $customAnswers = is_array($selectedAttendee->metadata) && isset($selectedAttendee->metadata['custom_fields'])
                        ? $selectedAttendee->metadata['custom_fields']
                        : [];
                    $customFieldsConfig = $selectedAttendee->event ? ($selectedAttendee->event->form_fields_config['custom_fields'] ?? []) : [];
                @endphp

                @if(!empty($customAnswers))
                    <div class="space-y-2">
                        <h4 class="text-xs font-extrabold text-purple-600 dark:text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Custom Extra Field Responses
                        </h4>
                        <div class="bg-purple-500/10 p-4 rounded-2xl border border-purple-500/20 text-xs font-medium space-y-2">
                            @foreach($customFieldsConfig as $cConf)
                                @php
                                    $cId = $cConf['id'] ?? '';
                                    $ans = $customAnswers[$cId] ?? null;
                                @endphp
                                @if($ans !== null && $ans !== '')
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-purple-500/10 pb-1.5 last:border-b-0 last:pb-0 gap-1">
                                        <span class="text-purple-300 font-bold">{{ $cConf['label'] ?? 'Custom Field' }}:</span>
                                        <span class="text-white font-extrabold">{{ is_array($ans) ? implode(', ', $ans) : (is_bool($ans) ? ($ans ? 'Yes' : 'No') : $ans) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Security & Pass Details -->
                <div class="space-y-2">
                    <h4 class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pass & QR Security Token</h4>
                    <div class="bg-slate-50 dark:bg-white/5 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-white/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs">
                        <div class="space-y-2 flex-1">
                            <div>
                                <span class="text-slate-500 dark:text-slate-400 font-semibold block mb-0.5">Pass Token ID</span>
                                <span class="font-mono text-slate-900 dark:text-white font-bold text-[11px] truncate max-w-[280px] block">{{ $selectedAttendee->qrCode->secure_token ?? 'No Pass Issued Yet' }}</span>
                            </div>
                            @if($selectedAttendee->qrCode)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Pass Active & Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                    Pass Inactive / Pending Approval
                                </span>
                            @endif
                        </div>

                        <!-- Visual QR Code Graphic -->
                        @if($selectedAttendee->qrCode)
                            <div class="p-3 bg-white rounded-2xl border border-slate-200 dark:border-white/20 shadow-lg flex flex-col items-center gap-1.5 shrink-0 self-center sm:self-auto">
                                <span class="text-[11px] font-black text-slate-900 uppercase tracking-wider text-center max-w-[140px] truncate">{{ $selectedAttendee->full_name }}</span>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($selectedAttendee->qrCode->secure_token) }}" 
                                     alt="Attendee Entry Pass QR Code" 
                                     class="w-32 h-32 object-contain rounded-lg">
                                <span class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider">Entry Pass QR</span>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Reshare Actions -->
                    @if($selectedAttendee->qrCode)
                        @php
                            $cleanPhone = preg_replace('/[^0-9]/', '', $selectedAttendee->phone ?? '');
                            if (!empty($cleanPhone) && str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '233' . substr($cleanPhone, 1);
                            }
                            $qrPassImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=" . urlencode($selectedAttendee->qrCode->secure_token);
                            $whatsappMessage = rawurlencode("Hello {$selectedAttendee->full_name},\n\nHere is your official digital entry pass for *{$selectedAttendee->event->name}*:\n\n🎟️ *Pass Token ID:* {$selectedAttendee->qrCode->secure_token}\n\n📷 *View/Download Your Entry Pass QR Code:* \n{$qrPassImageUrl}\n\nPlease present this QR code at check-in.");
                            $whatsappUrl = !empty($cleanPhone) 
                                ? "https://api.whatsapp.com/send?phone={$cleanPhone}&text={$whatsappMessage}" 
                                : "https://api.whatsapp.com/send?text={$whatsappMessage}";
                            $qrImageUrl = $qrPassImageUrl;
                        @endphp
                        <div class="p-3.5 rounded-2xl bg-blue-500/10 border border-blue-500/20 space-y-3 mt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider block">Reshare Pass Options</span>
                                @php
                                    $modalWaLog = $selectedAttendee->notificationLogs ? $selectedAttendee->notificationLogs->where('channel', \App\Enums\NotificationChannel::WhatsApp)->sortByDesc('created_at')->first() : null;
                                    $modalWaStatus = $modalWaLog ? $modalWaLog->status : null;
                                @endphp
                                @if($modalWaStatus === 'delivered' || $modalWaStatus === 'sent')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-500 bg-blue-500/20 px-2 py-0.5 rounded-md">
                                        ✓ WhatsApp Sent
                                    </span>
                                @elseif($modalWaStatus === 'failed')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500 bg-rose-500/20 px-2 py-0.5 rounded-md" title="{{ $modalWaLog->error_message ?? '' }}">
                                        ✕ WhatsApp Failed
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <!-- Resend via Email -->
                                <button type="button" wire:click="resendPassEmail('{{ $selectedAttendee->uuid }}')" wire:loading.attr="disabled" class="px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span wire:loading.remove wire:target="resendPassEmail('{{ $selectedAttendee->uuid }}')">Email Pass</span>
                                    <span wire:loading wire:target="resendPassEmail('{{ $selectedAttendee->uuid }}')">Sending Email...</span>
                                </button>

                                <!-- Share via WhatsApp -->
                                @if(!empty($selectedAttendee->phone))
                                    <button type="button" wire:click="sendWhatsAppPass('{{ $selectedAttendee->uuid }}')" class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                        Send on WhatsApp
                                    </button>
                                @endif

                                <!-- Download QR Image -->
                                <a href="{{ $qrImageUrl }}" target="_blank" download="{{ \Illuminate\Support\Str::slug($selectedAttendee->full_name) }}-qr-pass.png" class="px-3 py-2 rounded-xl bg-slate-200 dark:bg-white/10 hover:bg-slate-300 dark:hover:bg-white/20 text-slate-800 dark:text-white font-semibold text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Download Image
                                </a>
                            </div>

                            @if($modalWaStatus === 'delivered' || $modalWaStatus === 'sent')
                                <div class="pt-2 border-t border-blue-500/20 flex justify-between items-center text-[11px]">
                                    <span class="text-slate-400">Did WhatsApp report the number is not registered?</span>
                                    <button type="button" wire:click="markWhatsAppFailed('{{ $selectedAttendee->uuid }}')" class="text-rose-400 hover:text-rose-300 font-bold underline cursor-pointer">
                                        Mark as Not on WhatsApp
                                    </button>
                                </div>
                            @elseif($modalWaStatus === 'failed')
                                <div class="pt-2 border-t border-rose-500/20 flex justify-between items-center text-[11px]">
                                    <span class="text-rose-400 font-medium">{{ $modalWaLog->error_message ?? 'Number not registered on WhatsApp' }}</span>
                                    <button type="button" wire:click="markWhatsAppSent('{{ $selectedAttendee->uuid }}')" class="text-emerald-400 hover:text-emerald-300 font-bold underline cursor-pointer">
                                        Mark as Sent
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex justify-between items-center">
                    <div>
                        @if($selectedAttendee->verification_status !== \App\Enums\VerificationStatus::Verified)
                            <button type="button" wire:click="verifyAttendee('{{ $selectedAttendee->uuid }}'); closeDetailsModal();" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Verify & Approve Attendee
                            </button>
                        @endif
                    </div>
                    <button type="button" wire:click="closeDetailsModal" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/20 font-bold text-xs transition-colors cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Send Invitations Modal -->
    @if($showBulkInviteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/80 backdrop-blur-md animate-fadeIn">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-2xl max-w-xl w-full p-6 sm:p-7 space-y-5 max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-4 border-b border-slate-100 dark:border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Bulk Send Invitations</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Issue private invitations with customizable confirmation flows.</p>
                        </div>
                    </div>
                    <button wire:click="closeBulkInviteModal" class="p-2 rounded-xl text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="sendBulkInvitations" class="space-y-4">
                    <!-- Target Event Selection -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Target Event</label>
                        <select wire:model.live="new_event_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-purple-500">
                            @foreach($events as $ev)
                                <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Flow Option Selector (Option A vs Option B) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Invitation Flow &amp; Confirmation Type
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Option A -->
                            <button type="button" 
                                    wire:click="$set('bulk_invite_type', 'form')" 
                                    class="p-3.5 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $bulk_invite_type === 'form' ? 'border-purple-500 bg-purple-500/10 text-white font-extrabold shadow-md' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
                                <div class="flex items-center gap-1.5 text-xs font-extrabold text-purple-600 dark:text-purple-400">
                                    <span>📋</span>
                                    <span>Option A: Form RSVP Entry</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1 leading-snug">
                                    Recipient clicks <strong>"Access &amp; Confirm Pass"</strong> and fills/confirms registration details before receiving QR code.
                                </p>
                            </button>

                            <!-- Option B -->
                            <button type="button" 
                                    wire:click="$set('bulk_invite_type', 'direct')" 
                                    class="p-3.5 rounded-2xl border-2 text-left transition-all cursor-pointer {{ $bulk_invite_type === 'direct' ? 'border-purple-500 bg-purple-500/10 text-white font-extrabold shadow-md' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
                                <div class="flex items-center gap-1.5 text-xs font-extrabold text-purple-600 dark:text-purple-400">
                                    <span>⚡</span>
                                    <span>Option B: 1-Click Instant Pass</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1 leading-snug">
                                    Recipient clicks <strong>"Access &amp; Confirm Invitation"</strong> and directly receives their official digital QR pass without any form.
                                </p>
                            </button>
                        </div>
                    </div>

                    <!-- Input Format Selector (1. Only Emails vs 2. Names & Respective Emails vs 3. Import Excel / CSV) -->
                    <div class="space-y-2.5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Recipient Input Format
                            </label>
                            <div class="flex rounded-xl bg-slate-100 dark:bg-slate-800 p-1 border border-slate-200 dark:border-white/10 shrink-0">
                                <button type="button" 
                                        wire:click="$set('bulk_input_mode', 'emails_only')" 
                                        class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition-all cursor-pointer {{ $bulk_input_mode === 'emails_only' ? 'bg-purple-600 text-white shadow' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                    1. Only Emails
                                </button>
                                <button type="button" 
                                        wire:click="$set('bulk_input_mode', 'names_and_emails')" 
                                        class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition-all cursor-pointer {{ $bulk_input_mode === 'names_and_emails' ? 'bg-purple-600 text-white shadow' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                                    2. Names &amp; Emails
                                </button>
                                <button type="button" 
                                        wire:click="$set('bulk_input_mode', 'excel_import')" 
                                        class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition-all cursor-pointer flex items-center gap-1 {{ $bulk_input_mode === 'excel_import' ? 'bg-emerald-600 text-white shadow' : 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>3. Excel / CSV</span>
                                </button>
                            </div>
                        </div>

                        <!-- Active Uploaded File Banner (if a file was imported) -->
                        @if($bulk_uploaded_file_name)
                            <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-between text-xs animate-fadeIn">
                                <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300 font-bold truncate">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="truncate">Imported from <code>{{ $bulk_uploaded_file_name }}</code> ({{ $bulk_imported_count }} records)</span>
                                </div>
                                <button type="button" wire:click="clearBulkUploadedFile" class="text-xs text-rose-500 hover:text-rose-600 font-bold px-2 py-0.5 rounded hover:bg-rose-500/10 transition-colors cursor-pointer shrink-0" title="Clear file and inputs">
                                    ✕ Clear
                                </button>
                            </div>
                        @endif

                        @if($bulk_input_mode === 'emails_only')
                            <div class="space-y-1.5">
                                <div class="relative">
                                    <textarea wire:model.live.debounce.300ms="bulk_emails" 
                                              rows="4" 
                                              class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl p-3.5 text-slate-900 dark:text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-purple-500" 
                                              placeholder="Enter comma or line separated emails e.g.:
david@example.com
sarah@company.com, alex@tech.org"></textarea>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Enter one or more email addresses separated by commas or newlines. Leave blank to re-send to all attendees.
                                </p>
                            </div>
                        @elseif($bulk_input_mode === 'names_and_emails')
                            <div class="space-y-1.5">
                                <div class="relative">
                                    <textarea wire:model.live.debounce.300ms="bulk_names_emails" 
                                              rows="4" 
                                              class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl p-3.5 text-slate-900 dark:text-white text-xs font-mono focus:outline-none focus:ring-2 focus:ring-purple-500" 
                                              placeholder="Enter full name with email per line e.g.:
John Doe, john@example.com
Sarah Connor <sarah@company.com>
Alex Johnson - alex@tech.org"></textarea>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Supports <code>Name, email</code>, <code>Name &lt;email&gt;</code>, <code>Name - email</code>, or copy-pasting 2 columns directly from Excel.
                                </p>
                            </div>
                        @else
                            <!-- Dedicated Excel / CSV Upload Dropzone -->
                            <div class="space-y-3">
                                <label class="border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 dark:hover:border-emerald-400 rounded-2xl p-5 flex flex-col items-center justify-center text-center cursor-pointer transition-all bg-slate-50/50 dark:bg-white/[0.02] hover:bg-emerald-500/5 group">
                                    <div class="p-3 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mb-2 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                        Click to Upload Excel (.xlsx, .xls) or CSV (.csv) File
                                    </span>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        Spreadsheet with columns <code>Name</code> and <code>Email</code> (or email-only column).
                                    </p>
                                    <input type="file" wire:model="bulk_excel_file" accept=".xlsx,.xls,.csv,.txt" class="hidden">
                                </label>

                                <div wire:loading wire:target="bulk_excel_file" class="w-full text-center py-2 text-xs font-bold text-emerald-500 flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>Reading and extracting spreadsheet data...</span>
                                </div>
                            </div>
                        @endif

                        <!-- Live Parsed Recipients Analysis & Duplicate Detection -->
                        @php
                            $bulkAnalysis = $this->getBulkRecipientsAnalysis();
                        @endphp
                        @if($bulkAnalysis['total'] > 0)
                            <div class="space-y-2.5 animate-fadeIn">
                                @if($bulkAnalysis['existing'] === 0)
                                    <!-- 100% New (No Duplicates) -->
                                    <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            <span>{{ $bulkAnalysis['total'] }} new recipient{{ $bulkAnalysis['total'] > 1 ? 's' : '' }} ready to invite (0 duplicates)</span>
                                        </div>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono truncate max-w-[200px]">
                                            {{ $bulkAnalysis['new_recipients'][0]['name'] }} ({{ $bulkAnalysis['new_recipients'][0]['email'] }}){{ $bulkAnalysis['total'] > 1 ? ', +' . ($bulkAnalysis['total'] - 1) . ' more' : '' }}
                                        </span>
                                    </div>
                                @elseif($bulkAnalysis['new'] === 0)
                                    <!-- 100% Duplicates -->
                                    <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-xs space-y-2.5">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-center gap-2 font-black text-amber-600 dark:text-amber-400">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <span>Duplicate Notice: All {{ $bulkAnalysis['total'] }} recipient(s) are already registered in this event.</span>
                                            </div>
                                            <button type="button" wire:click="toggleDuplicateDetails" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline cursor-pointer shrink-0">
                                                {{ $bulk_show_duplicate_details ? '▲ Hide List' : '▼ View Duplicates (' . $bulkAnalysis['existing'] . ')' }}
                                            </button>
                                        </div>

                                        <p class="text-[11px] text-slate-600 dark:text-slate-400">
                                            There are <strong>0 new recipients</strong> in this list. By default, duplicate records are skipped.
                                        </p>

                                        <!-- Duplicate Attendees List Drawer -->
                                        @if($bulk_show_duplicate_details)
                                            <div class="mt-2 max-h-48 overflow-y-auto rounded-xl border border-amber-500/20 bg-slate-900/50 p-2 space-y-1.5 font-mono text-[11px]">
                                                @foreach($bulkAnalysis['existing_recipients'] as $idx => $dup)
                                                    <div class="flex items-center justify-between p-1.5 rounded-lg bg-white/5 border border-white/5">
                                                        <div class="truncate mr-2">
                                                            <span class="text-slate-400">{{ $idx + 1 }}.</span>
                                                            <span class="font-bold text-slate-200">{{ $dup['db_name'] ?? $dup['name'] }}</span>
                                                            <span class="text-amber-400/90">&lt;{{ $dup['email'] }}&gt;</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5 shrink-0 text-[10px]">
                                                            <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold uppercase">{{ $dup['db_role'] ?? 'General' }}</span>
                                                            <span class="text-slate-500">{{ $dup['db_registered_at'] ?? '' }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <label class="flex items-center space-x-2 pt-2 border-t border-amber-500/20 cursor-pointer">
                                            <input wire:model.live="bulk_resend_to_existing" type="checkbox" class="form-checkbox h-4 w-4 text-amber-600 rounded border-slate-300">
                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                                Re-send invitations &amp; update passes for these {{ $bulkAnalysis['existing'] }} existing attendees
                                            </span>
                                        </label>
                                    </div>
                                @else
                                    <!-- Mixed: Some New, Some Duplicates -->
                                    <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-xs space-y-2.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-black">
                                                    ✨ {{ $bulkAnalysis['new'] }} Non-Duplicate (New)
                                                </span>
                                                <span class="px-2 py-0.5 rounded-lg bg-amber-500/20 text-amber-600 dark:text-amber-400 font-black">
                                                    ⚠️ {{ $bulkAnalysis['existing'] }} Duplicate (Already in DB)
                                                </span>
                                            </div>
                                            <button type="button" wire:click="toggleDuplicateDetails" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline cursor-pointer shrink-0">
                                                {{ $bulk_show_duplicate_details ? '▲ Hide List' : '▼ View Duplicates (' . $bulkAnalysis['existing'] . ')' }}
                                            </button>
                                        </div>

                                        <p class="text-[11px] text-slate-600 dark:text-slate-400">
                                            Clicking <strong>Dispatch Bulk Invitations</strong> will send invitations <strong>only to the {{ $bulkAnalysis['new'] }} new recipients</strong>.
                                        </p>

                                        <!-- Duplicate Attendees List Drawer -->
                                        @if($bulk_show_duplicate_details)
                                            <div class="mt-2 max-h-48 overflow-y-auto rounded-xl border border-amber-500/20 bg-slate-900/50 p-2 space-y-1.5 font-mono text-[11px]">
                                                @foreach($bulkAnalysis['existing_recipients'] as $idx => $dup)
                                                    <div class="flex items-center justify-between p-1.5 rounded-lg bg-white/5 border border-white/5">
                                                        <div class="truncate mr-2">
                                                            <span class="text-slate-400">{{ $idx + 1 }}.</span>
                                                            <span class="font-bold text-slate-200">{{ $dup['db_name'] ?? $dup['name'] }}</span>
                                                            <span class="text-amber-400/90">&lt;{{ $dup['email'] }}&gt;</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5 shrink-0 text-[10px]">
                                                            <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold uppercase">{{ $dup['db_role'] ?? 'General' }}</span>
                                                            <span class="text-slate-500">{{ $dup['db_registered_at'] ?? '' }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <label class="flex items-center space-x-2 pt-2 border-t border-amber-500/20 cursor-pointer">
                                            <input wire:model.live="bulk_resend_to_existing" type="checkbox" class="form-checkbox h-4 w-4 text-amber-600 rounded border-slate-300">
                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                                Also re-send passes to the {{ $bulkAnalysis['existing'] }} existing duplicate attendees
                                            </span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Access Role & Verification -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Access Role</label>
                            <select wire:model="bulk_access_role" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-purple-500">
                                @foreach(\App\Enums\AccessRole::cases() as $role)
                                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center pt-4 sm:pt-6">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input wire:model="bulk_auto_verify" type="checkbox" class="form-checkbox h-4 w-4 text-purple-600 rounded border-slate-300 dark:border-white/20">
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold">Pre-verify &amp; Approve Pass</span>
                            </label>
                        </div>
                    </div>

                    <!-- Flow Information Box -->
                    <div class="p-3.5 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-xs space-y-1">
                        <span class="font-extrabold text-purple-600 dark:text-purple-400 block flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            {{ $bulk_invite_type === 'direct' ? 'Option B: Direct Pass Confirmation' : 'Option A: Form RSVP Confirmation' }}
                        </span>
                        <p class="text-slate-600 dark:text-slate-400">
                            @if($bulk_invite_type === 'direct')
                                Each recipient receives an email with <strong>"Access &amp; Confirm Invitation"</strong>. Clicking it confirms their pass instantly and renders their official QR code.
                            @else
                                Each recipient receives an email with <strong>"Access &amp; Confirm Pass"</strong>. Clicking it opens the registration form with pre-filled {{ $bulk_input_mode === 'names_and_emails' ? 'Name & Email' : 'Email' }}, and QR pass is issued upon submission.
                            @endif
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 flex justify-end gap-3 border-t border-slate-100 dark:border-white/10">
                        <button wire:click="closeBulkInviteModal" type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-white/20 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-semibold text-xs transition-all cursor-pointer">
                            Cancel
                        </button>
                        
                        @php
                            $canDispatch = ($bulkAnalysis['total'] === 0) || ($bulkAnalysis['new'] > 0) || $bulk_resend_to_existing;
                        @endphp
                        
                        <button type="submit" 
                                wire:loading.attr="disabled" 
                                @if(!$canDispatch) disabled @endif
                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs shadow-lg shadow-purple-500/25 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove>
                                @if($bulkAnalysis['total'] === 0)
                                    Re-send Passes to All Event Attendees
                                @elseif($bulkAnalysis['new'] > 0 && !$bulk_resend_to_existing)
                                    Dispatch to {{ $bulkAnalysis['new'] }} Non-Duplicate Recipient{{ $bulkAnalysis['new'] > 1 ? 's' : '' }}
                                @elseif($bulkAnalysis['new'] > 0 && $bulk_resend_to_existing)
                                    Dispatch to All {{ $bulkAnalysis['total'] }} Recipients ({{ $bulkAnalysis['new'] }} New + {{ $bulkAnalysis['existing'] }} Existing)
                                @elseif($bulk_resend_to_existing)
                                    Re-dispatch Passes to {{ $bulkAnalysis['existing'] }} Existing Attendees
                                @else
                                    All {{ $bulkAnalysis['existing'] }} Recipients Already Registered (0 New)
                                @endif
                            </span>
                            <span wire:loading class="flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Sending Invitations...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Secure Single-Use Link Generator Modal -->
    @if($showLinkGeneratorModal)
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-3 sm:p-4 pt-8 sm:pt-4 overflow-y-auto bg-slate-950/80 backdrop-blur-md animate-fadeIn">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl sm:rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl relative my-4 sm:my-auto max-h-[85vh] sm:max-h-[90vh] overflow-y-auto custom-scrollbar">
                <!-- Close Button -->
                <button wire:click="closeLinkGeneratorModal" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-bold uppercase tracking-wider border border-amber-500/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Security Architecture
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Generate Personal Link</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Generate a single-use token URL for specific VIPs or attendees. Once used, shared link attempts will downgrade to Pending Verification.</p>
                </div>

                <div class="space-y-4">
                    <!-- Event Selector -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Select Event <span class="text-red-500">*</span></label>
                        <select wire:model.live="gen_event_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">— Choose an event —</option>
                            @foreach($events as $evt)
                                <option value="{{ $evt->id }}">{{ $evt->name }}</option>
                            @endforeach
                        </select>
                        @if(empty($gen_event_id))
                            <p class="text-[10px] text-amber-500 font-medium mt-1">Please select which event this secure link is for.</p>
                        @endif
                    </div>

                    <!-- Category Selector (Details vs No Details) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Invitation Category / Entry Mode</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" wire:click="$set('gen_category', 'details')" class="p-3 rounded-xl border-2 text-left transition-all cursor-pointer {{ $gen_category === 'details' ? 'border-amber-500 bg-amber-500/10 text-white font-extrabold shadow-md' : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                                <div class="text-xs font-bold flex items-center gap-1">
                                    <span>📋</span>
                                    <span>Details (Form Entry)</span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium mt-1">Invitee fills in Name, Email &amp; Phone to claim pass.</p>
                            </button>

                            <button type="button" wire:click="$set('gen_category', 'no_details')" class="p-3 rounded-xl border-2 text-left transition-all cursor-pointer {{ $gen_category === 'no_details' ? 'border-amber-500 bg-amber-500/10 text-white font-extrabold shadow-md' : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                                <div class="text-xs font-bold flex items-center gap-1">
                                    <span>⚡</span>
                                    <span>No Details (Direct Claim)</span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium mt-1">Form bypassed. Click poster image to instantly claim pass.</p>
                            </button>
                        </div>

                        <!-- Dropdown Field Selection / Form Controls (Shown when Details / Form Entry mode is selected) -->
                        @if($gen_category === 'details')
                            @php
                                $stdLabels = [
                                    'full_name' => 'Full Name',
                                    'email' => 'Email Address',
                                    'phone' => 'Phone Number',
                                    'company' => 'Company / Organization',
                                    'job_title' => 'Job Title',
                                    'country' => 'Country',
                                    'gender' => 'Gender',
                                    'emergency_contact_name' => 'Emergency Contact Name',
                                    'emergency_contact_phone' => 'Emergency Contact Phone',
                                    'dietary_preferences' => 'Dietary Preferences',
                                    'accessibility_needs' => 'Accessibility Needs',
                                    'registration_reason' => 'Reason for Attendance',
                                ];
                            @endphp

                            <div class="mt-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/90 border border-amber-500/30 space-y-4 animate-fadeIn max-h-[360px] overflow-y-auto pr-1">
                                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700/80 pb-2.5 flex-wrap gap-2">
                                    <div>
                                        <h4 class="text-xs font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                            <span>⚙️</span>
                                            <span>Form Controls & Input Customization</span>
                                        </h4>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Configure which input fields appear on RSVP forms and add custom extra questions.</p>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30 shrink-0">Category 1: Get Details</span>
                                </div>

                                <!-- Standard Input Fields Configuration Grid -->
                                <div class="space-y-2">
                                    <h5 class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Standard Input Fields
                                    </h5>

                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($stdLabels as $key => $label)
                                            @php $currSt = $gen_standard_fields[$key] ?? 'disabled'; @endphp
                                            <div class="p-2.5 px-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 shadow-xs">
                                                <span class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate flex-1">{{ $label }}</span>
                                                <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 dark:border-white/10 shrink-0 shadow-inner">
                                                    <button type="button" 
                                                            wire:click="setGenFieldStatus('{{ $key }}', 'disabled')" 
                                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all cursor-pointer {{ $currSt === 'disabled' ? 'bg-slate-800 text-slate-200 shadow-sm border border-slate-700 font-extrabold' : 'text-slate-400 hover:text-slate-200' }}">
                                                        Disabled
                                                    </button>
                                                    <button type="button" 
                                                            wire:click="setGenFieldStatus('{{ $key }}', 'optional')" 
                                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all cursor-pointer {{ $currSt === 'optional' ? 'bg-blue-600 text-white font-black shadow-md shadow-blue-500/30 border border-blue-400' : 'text-slate-400 hover:text-blue-300' }}">
                                                        Optional
                                                    </button>
                                                    <button type="button" 
                                                            wire:click="setGenFieldStatus('{{ $key }}', 'required')" 
                                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all cursor-pointer {{ $currSt === 'required' ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/30 border border-amber-400' : 'text-slate-400 hover:text-amber-300' }}">
                                                        Required
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Custom Extra Fields Builder Section -->
                                <div class="pt-3 border-t border-slate-200 dark:border-slate-700 space-y-2.5">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <h5 class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            Custom Extra Fields
                                        </h5>
                                        <button type="button" wire:click="addGenCustomField" class="px-2.5 py-1 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 border border-purple-500/30 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                            + Add Custom Field
                                        </button>
                                    </div>

                                    @if(empty($gen_custom_fields))
                                        <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-dashed border-slate-300 dark:border-slate-700 text-center text-xs text-slate-400 italic">
                                            No custom extra fields added yet.
                                        </div>
                                    @else
                                        <div class="space-y-2">
                                             @foreach($gen_custom_fields as $index => $field)
                                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 space-y-2">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-black text-purple-600 dark:text-purple-400 uppercase">Question #{{ $index + 1 }}</span>
                                                        <button type="button" wire:click="removeGenCustomField({{ $index }})" class="text-xs text-rose-500 hover:text-rose-600 font-bold cursor-pointer">Remove</button>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                                        <div class="sm:col-span-2">
                                                            <input type="text" wire:model.blur="gen_custom_fields.{{ $index }}.label" wire:change="persistGenFormFields" placeholder="Question label / question text..." class="w-full px-3 py-1.5 text-xs rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white font-medium outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500">
                                                        </div>
                                                        <div>
                                                            <select wire:model.live="gen_custom_fields.{{ $index }}.type" wire:change="persistGenFormFields" class="w-full px-2.5 py-1.5 text-xs rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white font-medium outline-none cursor-pointer">
                                                                <option value="text" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">Short Text</option>
                                                                <option value="number" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">Number</option>
                                                                <option value="textarea" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">Paragraph</option>
                                                                <option value="select" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">Dropdown</option>
                                                                <option value="checkbox" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">Checkbox</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="mt-2 p-2.5 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-300 text-xs font-medium flex items-center gap-2">
                                <span>⚡</span>
                                <span>Direct Claim mode active. Form entry is bypassed so guests claim passes instantly without filling input fields.</span>
                            </div>
                        @endif
                    </div>

                    <!-- Target Role -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Access Role / VIP Category</label>
                        <select wire:model="gen_access_role" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            @foreach(\App\Enums\AccessRole::cases() as $role)
                                <option value="{{ $role->value }}">{{ $role->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Optional Recipient Email -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Recipient Email <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                        <input wire:model="gen_email" type="email" placeholder="e.g. vipguest@example.com (optional)" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <!-- Max Uses -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Max Link Uses (Default: 1 Single-Use)</label>
                        <input wire:model="gen_max_uses" type="number" min="1" max="100" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <button wire:click="generateSingleUseLink" type="button" class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-sm shadow-lg shadow-amber-500/25 transition-all cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        Generate Secure Token Link
                    </button>

                    <!-- Generated Link Output -->
                    @if($generated_invite_url)
                        <div x-data="{ copied: false }" class="p-4 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-amber-500/30 space-y-2.5 animate-fadeIn">
                            <label class="block text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Single-Use Link Ready!</label>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                <input readonly type="text" value="{{ $generated_invite_url }}" class="w-full sm:flex-1 px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-mono select-all truncate">
                                <button @click="navigator.clipboard.writeText('{{ $generated_invite_url }}'); copied = true; setTimeout(() => copied = false, 2000)" type="button" class="px-3.5 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center justify-center gap-1">
                                    <span x-show="!copied">Copy</span>
                                    <span x-show="copied" class="text-emerald-200">Copied! ✓</span>
                                </button>
                                @if($generated_whatsapp_url)
                                    <a href="{{ $generated_whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center justify-center gap-1.5 shadow-md">
                                        <svg class="w-4 h-4 fill-current text-white" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.754zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        <span>Share WhatsApp</span>
                                    </a>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Share this link directly with your guest. Once redeemed, forwarded copies will require admin approval.</p>
                        </div>
                    @endif
                </div>

                <div class="pt-4 flex justify-end border-t border-slate-100 dark:border-white/10">
                    <button wire:click="closeLinkGeneratorModal" type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-white/20 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-semibold text-xs transition-all cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Mobile Organization Workspace Modal -->
    @if($showMobileOrgModal && $mobileOrg)
        @php
            $mOrgAdmin = $mobileOrg->users->firstWhere(fn($u) => $u->hasRole('organization_admin')) ?? $mobileOrg->users->first();
            $mTotalAttendees = $mobileOrg->events->sum('attendees_count');
        @endphp
        <div class="fixed inset-0 bg-slate-950/95 backdrop-blur-md flex items-center justify-center p-0 sm:p-4 animate-fadeIn" style="z-index: 9999;">
            <div class="bg-slate-900 border-0 sm:border border-white/10 rounded-none sm:rounded-3xl w-full h-full sm:h-[88vh] max-w-none sm:max-w-xl flex flex-col overflow-hidden shadow-2xl">
                <!-- Modal Header -->
                <div class="p-5 border-b border-white/10 flex items-center justify-between bg-slate-900/90 backdrop-blur-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md shrink-0">
                            {{ strtoupper(substr($mobileOrg->name ?? 'Org', 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-extrabold text-white line-clamp-1">{{ $mobileOrg->name ?? 'Organization Workspace' }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                    ID: {{ $mobileOrg->id }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">Admin: <strong class="text-slate-200">{{ $mOrgAdmin->name ?? 'Admin' }}</strong></p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeMobileOrgModal" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white border border-white/10 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Sub-Header Stats -->
                <div class="px-5 py-3 bg-slate-950/60 border-b border-white/5 flex items-center justify-between">
                    <span class="text-xs text-blue-400 font-extrabold">📅 {{ $mobileOrg->events->count() }} {{ Str::plural('Event', $mobileOrg->events->count()) }}</span>
                    <span class="text-xs text-emerald-400 font-extrabold">👥 {{ $mTotalAttendees }} Total {{ Str::plural('Attendee', $mTotalAttendees) }}</span>
                </div>

                <!-- Modal Body: Events List -->
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 space-y-4">
                    @forelse($mobileOrg->events as $mEvt)
                        @php
                            $isMSelected = ($mobileSelectedEventId === $mEvt->id);
                        @endphp
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20 shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-extrabold text-white line-clamp-1">{{ $mEvt->name }}</h4>
                                        <p class="text-[11px] text-slate-400">Status: <span class="capitalize text-emerald-400 font-bold">{{ $mEvt->status }}</span></p>
                                    </div>
                                </div>

                                <button type="button" wire:click="selectMobileEvent({{ $mEvt->id }})" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white border border-blue-500/40 transition-all cursor-pointer flex items-center gap-1.5 shrink-0">
                                    <span>{{ $isMSelected ? 'Hide' : 'View Attendees ('.$mEvt->attendees_count.')' }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isMSelected ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                                </button>
                            </div>

                            @if($isMSelected)
                                <!-- Event Attendees Details List inside Mobile Org Modal -->
                                @php
                                    $mEvtAttendees = \App\Models\Attendee::where('event_id', $mEvt->id)->with(['qrCode', 'assignedGate'])->latest()->get();
                                @endphp
                                <div class="pt-3 border-t border-white/10 space-y-3 animate-fadeIn">
                                    <div class="text-xs font-extrabold text-purple-300 uppercase tracking-wider">
                                        Attendees for {{ $mEvt->name }} ({{ count($mEvtAttendees) }})
                                    </div>

                                    @forelse($mEvtAttendees as $mAtt)
                                        @php
                                            $mStatusStr = is_object($mAtt->verification_status) ? $mAtt->verification_status->value : (string)$mAtt->verification_status;
                                            $mStatusLabel = is_object($mAtt->verification_status) ? $mAtt->verification_status->label() : ucfirst($mAtt->verification_status);
                                        @endphp
                                        <div class="p-3.5 rounded-xl bg-slate-900/90 border border-white/10 space-y-2.5">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <h5 class="text-xs font-extrabold text-white flex items-center gap-1.5">
                                                        <span class="w-5 h-5 rounded-lg bg-blue-500/20 text-blue-300 text-[10px] flex items-center justify-center font-black">
                                                            {{ strtoupper(substr($mAtt->full_name, 0, 1)) }}
                                                        </span>
                                                        {{ $mAtt->full_name }}
                                                    </h5>
                                                    <p class="text-[11px] text-slate-400 font-medium pl-6">{{ $mAtt->email }}</p>
                                                </div>

                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $mStatusStr === 'verified' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($mStatusStr === 'rejected' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40') }}">
                                                    {{ $mStatusLabel }}
                                                </span>
                                            </div>

                                            <div class="flex items-center flex-wrap gap-1.5 text-[10px]">
                                                <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-300 font-bold border border-blue-500/20">
                                                    Role: {{ is_object($mAtt->access_role) ? $mAtt->access_role->label() : ucfirst($mAtt->access_role) }}
                                                </span>
                                                @if($mAtt->assignedGate)
                                                    <span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 font-bold border border-purple-500/20">
                                                        Gate: {{ $mAtt->assignedGate->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-end gap-1.5 pt-1.5 border-t border-white/5">
                                                <button type="button" wire:click="openDetailsModal({{ $mAtt->id }})" class="px-2.5 py-1 rounded-lg bg-blue-500/20 text-blue-300 text-[10px] font-bold hover:bg-blue-500 hover:text-white transition-all cursor-pointer">
                                                    View Pass Details
                                                </button>
                                                <button type="button" wire:click="resendTicket({{ $mAtt->id }})" class="px-2 py-1 rounded-lg bg-amber-500/20 text-amber-300 text-[10px] font-bold hover:bg-amber-500 hover:text-slate-950 transition-all cursor-pointer">
                                                    Resend Email
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400 italic">No registered attendees yet.</p>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-6">No events registered for this workspace.</p>
                    @endforelse
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-white/10 bg-slate-900/90 backdrop-blur-xl flex justify-end">
                    <button type="button" wire:click="closeMobileOrgModal" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition-colors cursor-pointer">
                        Done
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Mobile View Attendees Modal -->
    @if($showMobileAttendeesModal && $mobileEvent)
        <div class="fixed inset-0 bg-slate-950/95 backdrop-blur-md flex items-center justify-center p-0 sm:p-4 animate-fadeIn" style="z-index: 9999;">
            <div class="bg-slate-900 border-0 sm:border border-white/10 rounded-none sm:rounded-3xl w-full h-full sm:h-[88vh] max-w-none sm:max-w-xl flex flex-col overflow-hidden shadow-2xl">
                <!-- Modal Header -->
                <div class="p-5 border-b border-white/10 flex items-center justify-between bg-slate-900/90 backdrop-blur-xl">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase tracking-wider">
                                {{ $mobileEvent->organization->name ?? 'Workspace' }}
                            </span>
                            <span class="text-xs text-slate-400 font-bold">👥 {{ count($mobileAttendees) }} Registered</span>
                        </div>
                        <h3 class="text-base font-extrabold text-white line-clamp-1">{{ $mobileEvent->name }}</h3>
                    </div>
                    <button type="button" wire:click="closeMobileAttendeesModal" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white border border-white/10 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body: Scrollable Attendees List -->
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 space-y-3">
                    @forelse($mobileAttendees as $att)
                        @php
                            $attStatusStr = is_object($att->verification_status) ? $att->verification_status->value : (string)$att->verification_status;
                            $attStatusLabel = is_object($att->verification_status) ? $att->verification_status->label() : ucfirst($att->verification_status);
                        @endphp
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white font-black flex items-center justify-center text-sm shadow-md shrink-0">
                                        {{ strtoupper(substr($att->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-extrabold text-white">{{ $att->full_name }}</h4>
                                        <p class="text-xs text-slate-400 font-medium">{{ $att->email }}</p>
                                        @if($att->phone)
                                            <p class="text-[11px] text-slate-500 font-medium">{{ $att->phone }}</p>
                                        @endif
                                    </div>
                                </div>

                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $attStatusStr === 'verified' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($attStatusStr === 'rejected' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40') }}">
                                    {{ $attStatusLabel }}
                                </span>
                            </div>

                            <div class="flex items-center flex-wrap gap-2 pt-2 border-t border-white/5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-500/15 text-blue-300 border border-blue-500/25">
                                    Role: {{ is_object($att->access_role) ? $att->access_role->label() : ucfirst($att->access_role) }}
                                </span>
                                @if($att->assignedGate)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-purple-500/15 text-purple-300 border border-purple-500/25">
                                        Gate: {{ $att->assignedGate->name }}
                                    </span>
                                @endif
                                @if($att->qrCode)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold bg-slate-800 text-slate-300 border border-white/10">
                                        Pass #: {{ substr($att->qrCode->code, 0, 10) }}...
                                    </span>
                                @endif
                            </div>

                            <!-- Mobile Card Quick Actions -->
                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/5">
                                <button type="button" wire:click="openDetailsModal({{ $att->id }})" class="px-3 py-1.5 rounded-xl bg-blue-500/20 hover:bg-blue-500 text-blue-300 hover:text-white border border-blue-500/30 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View Pass
                                </button>
                                <button type="button" wire:click="resendTicket({{ $att->id }})" class="p-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 border border-amber-500/30 text-xs font-bold transition-all cursor-pointer" title="Resend Pass Email">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </button>
                                <button type="button" wire:click="deleteAttendee({{ $att->id }})" wire:confirm="Delete this attendee?" class="p-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white border border-rose-500/30 text-xs font-bold transition-all cursor-pointer" title="Delete Attendee">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <p class="text-sm font-semibold">No registered attendees found for this event.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-white/10 bg-slate-900/90 backdrop-blur-xl flex justify-end">
                    <button type="button" wire:click="closeMobileAttendeesModal" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition-colors cursor-pointer">
                        Done
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ═══ Import CSV Modal ═══ -->
    @if($showImportCsvModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fadeIn">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-2xl max-w-2xl w-full p-6 space-y-6 max-h-[90vh] overflow-y-auto custom-scrollbar">

                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-4 border-b border-slate-100 dark:border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m4-8l-4-4m0 0L13 8m4-4v12"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Import Attendees from CSV</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Upload a CSV file to bulk-register attendees for an event.</p>
                        </div>
                    </div>
                    <button wire:click="closeImportCsvModal" class="p-2 rounded-xl text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Step 1: Select Event -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Target Event</label>
                    <select wire:model.live="import_event_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">— Select an event —</option>
                        @foreach($events as $ev)
                            <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Step 2: Field Preview & Template Download -->
                @if(!empty($import_event_id) && !empty($importEventFields))
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">CSV Template Fields</label>
                            <button wire:click="downloadCsvTemplate" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download Template
                            </button>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                            <div class="flex flex-wrap gap-2">
                                @foreach($importEventFields as $field)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold
                                        {{ $field['status'] === 'required'
                                            ? 'bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20'
                                            : 'bg-slate-200/60 dark:bg-white/10 text-slate-600 dark:text-slate-400 border border-slate-300/40 dark:border-white/10' }}">
                                        {{ $field['label'] }}
                                        @if($field['status'] === 'required')
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </span>
                                @endforeach
                                {{-- Always-present system columns --}}
                                <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">Role</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">Verification Status</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-3"><span class="text-red-500 font-bold">*</span> = Required field. Download the template to get the exact column headers with example data.</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5"><span class="text-emerald-500 font-bold">💡 Phone Helper:</span> Contacts (e.g. <code class="text-[10px] bg-slate-200/80 dark:bg-white/10 px-1 py-0.5 rounded">0547977840</code> or Excel-trimmed <code class="text-[10px] bg-slate-200/80 dark:bg-white/10 px-1 py-0.5 rounded">547977840</code>) are automatically normalized to 10 digits and formatted for WhatsApp pass delivery.</p>
                        </div>
                    </div>

                    <!-- Step 3: Upload CSV -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Upload CSV File</label>
                        <div class="relative">
                            <input type="file" wire:model="csv_file" accept=".csv" class="block w-full text-xs text-slate-500 dark:text-slate-400
                                file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0
                                file:text-xs file:font-bold
                                file:bg-emerald-50 dark:file:bg-emerald-500/10 file:text-emerald-700 dark:file:text-emerald-400
                                hover:file:bg-emerald-100 dark:hover:file:bg-emerald-500/20
                                file:cursor-pointer file:transition-all
                                border border-dashed border-slate-300 dark:border-white/20 rounded-xl p-3" />
                            <div wire:loading wire:target="csv_file" class="absolute inset-0 bg-white/80 dark:bg-slate-900/80 rounded-xl flex items-center justify-center">
                                <div class="flex items-center gap-2 text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Uploading file...
                                </div>
                            </div>
                        </div>
                        @error('csv_file') <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Import Results -->
                    @if(!empty($importResults))
                        <div class="space-y-3">
                            <!-- Summary Stats -->
                            <div class="grid grid-cols-3 gap-3">
                                <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $importResults['imported'] ?? 0 }}</div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-500 mt-0.5">Imported</div>
                                </div>
                                <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-center">
                                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $importResults['skipped'] ?? 0 }}</div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-500 mt-0.5">Skipped</div>
                                </div>
                                <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-center">
                                    <div class="text-2xl font-black text-red-600 dark:text-red-400">{{ count($importResults['errors'] ?? []) }}</div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-red-600 dark:text-red-500 mt-0.5">Errors</div>
                                </div>
                            </div>

                            <!-- Skipped Details (Duplicates) -->
                            @if(!empty($importResults['skip_reasons']))
                                <div class="p-4 rounded-2xl bg-amber-500/5 border border-amber-500/20 max-h-40 overflow-y-auto custom-scrollbar">
                                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400 mb-2 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Skipped (Duplicates Prevention)
                                    </p>
                                    <ul class="space-y-1">
                                        @foreach($importResults['skip_reasons'] as $reason)
                                            <li class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">• {{ $reason }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Error Details -->
                            @if(!empty($importResults['errors']))
                                <div class="p-4 rounded-2xl bg-red-500/5 border border-red-500/20 max-h-40 overflow-y-auto custom-scrollbar">
                                    <p class="text-xs font-bold text-red-600 dark:text-red-400 mb-2 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Import Errors
                                    </p>
                                    <ul class="space-y-1">
                                        @foreach($importResults['errors'] as $error)
                                            <li class="text-[11px] text-red-600 dark:text-red-400 font-medium">• {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif

                <!-- Action Buttons -->
                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-white/10">
                    <button wire:click="closeImportCsvModal" type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-white/20 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-semibold text-xs transition-all cursor-pointer">
                        {{ !empty($importResults) ? 'Done' : 'Cancel' }}
                    </button>
                    @if(!empty($import_event_id) && empty($importResults))
                        <button wire:click="importCsv" wire:loading.attr="disabled" type="button" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed" {{ !$csv_file ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="importCsv">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m4-8l-4-4m0 0L13 8m4-4v12"></path></svg>
                                Import Attendees
                            </span>
                            <span wire:loading wire:target="importCsv" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Importing...
                            </span>
                        </button>
                    @endif
                </div>

            </div>
        </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- EMAIL DELIVERY REPORT MODAL                                            -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- EMAIL DELIVERY REPORT MODAL (WITH LIVE PROGRESSIVE BATCHING)            -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if($showEmailReportModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" 
             x-data 
             x-init="document.body.classList.add('overflow-hidden')" 
             x-on:remove.window="document.body.classList.remove('overflow-hidden')"
             @if($isProcessingBatch) wire:poll.300ms="processNextEmailChunk" @endif>
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @if(!$isProcessingBatch) wire:click="closeEmailReportModal" @endif></div>

            <!-- Modal Content -->
            <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden animate-fadeIn" @if(!$isProcessingBatch) x-on:keydown.escape.window="$wire.closeEmailReportModal()" @endif>

                <!-- Header -->
                <div class="p-6 pb-4 border-b border-slate-100 dark:border-white/10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-2xl {{ $isProcessingBatch ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 animate-pulse' : ($emailFailedCount > 0 ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400') }}">
                                @if($isProcessingBatch)
                                    <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                @elseif($emailFailedCount > 0)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">
                                    {{ $isProcessingBatch ? 'Dispatching Passes in Progress...' : 'Email Delivery Report' }}
                                </h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                    @if($isProcessingBatch)
                                        Processing chunk batches without timeouts ({{ number_format($batchProcessedCount) }} / {{ number_format($batchTotalCount) }})
                                    @else
                                        Bulk approval completed for {{ number_format($approvedTotalCount) }} attendee(s)
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if(!$isProcessingBatch)
                            <button wire:click="closeEmailReportModal" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/10 transition-colors cursor-pointer">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Processing Banner (shown while batch dispatching) -->
                @if($isProcessingBatch)
                    <div class="px-6 pt-4">
                        <div class="p-3 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-ping"></span>
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">Paced Batch Delivery Active</span>
                            </div>
                            <span class="text-xs font-mono font-bold text-blue-500">
                                {{ $batchTotalCount > 0 ? round(($batchProcessedCount / $batchTotalCount) * 100) : 0 }}% Complete
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Summary Stats -->
                <div class="px-6 pt-4 pb-2">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/20 text-center">
                            <div class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($approvedTotalCount) }}</div>
                            <div class="text-[10px] font-bold text-blue-500 dark:text-blue-400/70 uppercase tracking-wider mt-0.5">Approved</div>
                        </div>
                        <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($emailSuccessCount) }}</div>
                            <div class="text-[10px] font-bold text-emerald-500 dark:text-emerald-400/70 uppercase tracking-wider mt-0.5">Emails Sent</div>
                        </div>
                        <div class="p-3 rounded-xl {{ $emailFailedCount > 0 ? 'bg-rose-500/10 border-rose-500/20' : 'bg-slate-500/10 border-slate-500/20' }} border text-center">
                            <div class="text-2xl font-extrabold {{ $emailFailedCount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 dark:text-slate-400' }}">{{ number_format($emailFailedCount) }}</div>
                            <div class="text-[10px] font-bold {{ $emailFailedCount > 0 ? 'text-rose-500 dark:text-rose-400/70' : 'text-slate-400' }} uppercase tracking-wider mt-0.5">Failed</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-3">
                        @php 
                            $percent = $batchTotalCount > 0 ? round(($batchProcessedCount / $batchTotalCount) * 100) : ($approvedTotalCount > 0 ? round(($emailSuccessCount / $approvedTotalCount) * 100) : 0);
                        @endphp
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                {{ $isProcessingBatch ? 'Dispatch Progress' : 'Delivery Rate' }}
                            </span>
                            <span class="text-xs font-extrabold {{ $percent === 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400' }}">{{ $percent }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-200 dark:bg-white/10 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300 {{ $percent === 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500' }}" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Results Lists -->
                <div class="px-6 pb-4 max-h-[40vh] overflow-y-auto space-y-3" style="scrollbar-width: thin;">

                    <!-- Failed Emails (shown first, always expanded if any) -->
                    @if($emailFailedCount > 0)
                        <div x-data="{ showFailed: true }">
                            <button @click="showFailed = !showFailed" class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 cursor-pointer hover:bg-rose-500/15 transition-colors">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">Failed Deliveries ({{ $emailFailedCount }})</span>
                                </div>
                                <svg class="w-4 h-4 text-rose-400 transition-transform" :class="{ 'rotate-180': showFailed }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="showFailed" x-collapse class="mt-2 space-y-1.5">
                                @foreach(collect($emailDeliveryResults)->where('status', 'failed') as $result)
                                    <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl bg-rose-500/5 border border-rose-500/10">
                                        <div class="p-1 rounded-full bg-rose-500/20 mt-0.5 shrink-0">
                                            <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $result['name'] }}</span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $result['email'] }}</p>
                                            @if(!empty($result['error']))
                                                <p class="text-[10px] text-rose-500 dark:text-rose-400 mt-0.5 line-clamp-2">{{ Str::limit($result['error'], 120) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Successful Emails (collapsible) -->
                    @if($emailSuccessCount > 0)
                        <div x-data="{ showSuccess: {{ $emailFailedCount === 0 ? 'true' : 'false' }} }">
                            <button @click="showSuccess = !showSuccess" class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 cursor-pointer hover:bg-emerald-500/15 transition-colors">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Emails Delivered Successfully ({{ $emailSuccessCount }})</span>
                                </div>
                                <svg class="w-4 h-4 text-emerald-400 transition-transform" :class="{ 'rotate-180': showSuccess }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="showSuccess" x-collapse class="mt-2 space-y-1">
                                @foreach(collect($emailDeliveryResults)->where('status', 'success') as $result)
                                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-emerald-500/5 border border-emerald-500/10">
                                        <div class="p-1 rounded-full bg-emerald-500/20 shrink-0">
                                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-xs font-semibold text-slate-900 dark:text-white truncate block">{{ $result['name'] }}</span>
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate block">{{ $result['email'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/10 flex items-center justify-between gap-3 bg-slate-50 dark:bg-slate-900/60">
                    @if($isProcessingBatch)
                        <div class="flex items-center gap-2 text-xs font-bold text-blue-600 dark:text-blue-400">
                            <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Processing chunk batch ({{ $batchProcessedCount }}/{{ $batchTotalCount }})...</span>
                        </div>
                        <span class="text-[11px] text-slate-400">Please keep this window open</span>
                    @else
                        @if($emailFailedCount > 0)
                            <button wire:click="retryFailedEmails" 
                                    wire:loading.attr="disabled"
                                    wire:target="retryFailedEmails"
                                    class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition-all shadow-md shadow-amber-500/20 flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-wait">
                                <svg wire:loading.remove wire:target="retryFailedEmails" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <svg wire:loading wire:target="retryFailedEmails" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span wire:loading.remove wire:target="retryFailedEmails">Retry Failed ({{ $emailFailedCount }})</span>
                                <span wire:loading wire:target="retryFailedEmails">Retrying...</span>
                            </button>
                        @else
                            <div></div>
                        @endif
                        <button wire:click="closeEmailReportModal" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-white/20 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-semibold text-xs transition-all cursor-pointer">
                            Close
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
