<div class="space-y-8 font-inter">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Events</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1 font-medium text-sm">Manage all your upcoming and past events.</p>
        </div>
            @php
                $isOrgAdminUnconfirmed = auth()->check() && !auth()->user()->isSuperAdmin() && auth()->user()->hasRole('organization_admin') && auth()->user()->invitation_status !== 'confirmed';
            @endphp
            <button type="button" wire:click="createEvent" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 cursor-pointer {{ $isOrgAdminUnconfirmed ? 'opacity-60' : '' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Event
                @if($isOrgAdminUnconfirmed)
                    <svg class="w-4 h-4 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Requires Email Confirmation"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                @endif
            </button>
    </div>

    <!-- Summary Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- Total Events -->
        <div wire:click="$set('statusFilter', '')" class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 p-4 sm:p-5 rounded-2xl cursor-pointer hover:border-blue-500/50 transition-all shadow-sm group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</span>
                <div class="p-2 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $totalCount }}</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300">Total</span>
            </div>
        </div>

        <!-- Published Events -->
        <div wire:click="$set('statusFilter', 'published')" class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 p-4 sm:p-5 rounded-2xl cursor-pointer hover:border-emerald-500/50 transition-all shadow-sm group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Published</span>
                <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $publishedCount }}</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    {{ $totalCount > 0 ? round(($publishedCount / $totalCount) * 100) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Draft Events -->
        <div wire:click="$set('statusFilter', 'draft')" class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 p-4 sm:p-5 rounded-2xl cursor-pointer hover:border-slate-400 transition-all shadow-sm group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Draft</span>
                <div class="p-2 rounded-xl bg-slate-500/10 text-slate-600 dark:text-slate-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $draftCount }}</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-500/10 text-slate-600 dark:text-slate-400">
                    {{ $totalCount > 0 ? round(($draftCount / $totalCount) * 100) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Cancelled Events -->
        <div wire:click="$set('statusFilter', 'cancelled')" class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 p-4 sm:p-5 rounded-2xl cursor-pointer hover:border-rose-500/50 transition-all shadow-sm group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Cancelled</span>
                <div class="p-2 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $cancelledCount }}</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400">
                    {{ $totalCount > 0 ? round(($cancelledCount / $totalCount) * 100) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Completed Events -->
        <div wire:click="$set('statusFilter', 'completed')" class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 p-4 sm:p-5 rounded-2xl cursor-pointer hover:border-purple-500/50 transition-all shadow-sm group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Completed</span>
                <div class="p-2 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $completedCount }}</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-purple-500/10 text-purple-600 dark:text-purple-400">
                    {{ $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Archived Events -->
        <div wire:click="$set('statusFilter', 'archived')" class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 p-4 sm:p-5 rounded-2xl cursor-pointer hover:border-amber-500/50 transition-all shadow-sm group">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Archived</span>
                <div class="p-2 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $archivedCount }}</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    {{ $totalCount > 0 ? round(($archivedCount / $totalCount) * 100) : 0 }}%
                </span>
            </div>
        </div>
    </div>

    <!-- Filters & Controls -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-4 shadow-sm dark:shadow-xl">
        <div class="w-full md:w-auto flex-1 flex flex-col sm:flex-row items-center gap-3">
            <div class="relative w-full max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm font-medium transition-colors" placeholder="Search events...">
            </div>

            <!-- Status Select -->
            <select wire:model.live="statusFilter" class="block w-full sm:w-52 px-3 py-2.5 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm font-medium transition-colors">
                <option value="" class="bg-slate-900 text-slate-100">All Statuses ({{ $totalCount }})</option>
                <option value="published" class="bg-slate-900 text-slate-100">Published ({{ $publishedCount }})</option>
                <option value="draft" class="bg-slate-900 text-slate-100">Draft ({{ $draftCount }})</option>
                <option value="cancelled" class="bg-slate-900 text-slate-100">Cancelled ({{ $cancelledCount }})</option>
                <option value="completed" class="bg-slate-900 text-slate-100">Completed ({{ $completedCount }})</option>
                <option value="archived" class="bg-slate-900 text-slate-100">Archived ({{ $archivedCount }})</option>
            </select>
        </div>

        <!-- View Toggle Icons (Far Right) -->
        <div class="flex items-center gap-1.5 shrink-0 justify-end w-full md:w-auto">
            <button type="button" wire:click="setGridMode" class="p-2.5 rounded-xl border {{ $viewMode === 'grid' ? 'bg-blue-500/10 border-blue-500/30 text-blue-600 dark:text-blue-400 font-bold' : 'border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition-colors cursor-pointer" title="Grid View">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </button>
            <button type="button" wire:click="setTableMode" class="p-2.5 rounded-xl border {{ $viewMode === 'table' ? 'bg-blue-500/10 border-blue-500/30 text-blue-600 dark:text-blue-400 font-bold' : 'border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition-colors cursor-pointer" title="Table View">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-semibold text-sm flex items-center">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white">Organization Workspace Hierarchy</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Categorize events under their respective Organization Admin Workspaces</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 bg-slate-100 dark:bg-slate-900/80 p-1.5 rounded-2xl border border-slate-200 dark:border-white/10 w-full md:w-auto">
                    <button type="button" wire:click="$set('groupedView', true)" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer text-center flex items-center justify-center gap-1.5 {{ $groupedView ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-lg shadow-purple-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>🏢</span>
                        <span>Categorized Org</span>
                    </button>
                    <button type="button" wire:click="$set('groupedView', false)" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer text-center flex items-center justify-center gap-1.5 {{ !$groupedView ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-lg shadow-purple-500/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>📅</span>
                        <span>All Events List</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($isSuperAdmin && $groupedView)
        <!-- Super Admin Organization-Categorized Events Tree -->
        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl overflow-hidden">
            <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 uppercase tracking-widest w-max">
                    ORGANIZATION WORKSPACES & EVENTS
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
                    @endphp
                    <div class="p-5 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md shrink-0">
                                    {{ strtoupper(substr($org->name ?? 'Org', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-extrabold text-slate-900 dark:text-white text-base">{{ $org->name ?? 'Organization Workspace' }}</h4>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 uppercase tracking-widest">
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
                                    📅 {{ $org->events->count() }} {{ Str::plural('Event', $org->events->count()) }}
                                </span>
                                <button type="button" wire:click="toggleExpandOrg({{ $org->id }})" class="px-4 py-2 rounded-xl text-xs font-bold bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition-all cursor-pointer flex items-center gap-1.5">
                                    @if($isOrgExpanded)
                                        <span>Hide Events</span>
                                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @else
                                        <span>View Events ({{ $org->events->count() }})</span>
                                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @endif
                                </button>
                                <button type="button" wire:click="deleteOrganization({{ $org->id }})" wire:confirm="Are you sure you want to delete this organization workspace and all its events?" class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/30 transition-all shadow-sm cursor-pointer" title="Delete Organization Workspace">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>

                        @if($isOrgExpanded)
                            <!-- Expanded Events List for this Organization -->
                            <div class="mt-4 pl-2 sm:pl-6 border-l-2 border-purple-500/30 space-y-3">
                                @if($viewMode === 'grid')
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
                                        @forelse($org->events as $evt)
                                            <div class="bg-slate-900 border border-white/10 rounded-2xl p-5 space-y-3 shadow-xl hover:border-blue-500/40 transition-all">
                                                <div class="flex items-center justify-between">
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $evt->status->badgeClass() }}">
                                                        {{ $evt->status->label() }}
                                                    </span>
                                                    <span class="text-xs text-slate-400 font-medium">
                                                        {{ $evt->starts_at ? $evt->starts_at->format('M d, Y') : 'Date TBD' }}
                                                    </span>
                                                </div>

                                                <div>
                                                    <h5 class="text-base font-extrabold text-white">{{ $evt->name }}</h5>
                                                    <p class="text-xs text-slate-400 line-clamp-1 mt-0.5">{{ $evt->location ?? 'Online / Main Venue' }}</p>
                                                </div>

                                                <div class="pt-3 border-t border-white/10 flex items-center justify-between text-xs text-slate-300">
                                                    <span>👥 {{ $evt->total_registrations_count ?? 0 }} Registered</span>
                                                    <span>🚪 {{ $evt->gates_count ?? 0 }} Gates</span>
                                                </div>

                                                <div class="pt-2 flex items-center justify-between flex-wrap gap-2">
                                                    <div class="flex items-center gap-1.5">
                                                        <a href="{{ route('events.show', $evt->uuid) }}" class="px-3 py-1.5 rounded-xl bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 font-bold text-xs flex items-center gap-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                            <span>Manage</span>
                                                        </a>
                                                        <a href="{{ route('events.edit', $evt->uuid) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs flex items-center gap-1" title="Edit Event">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                            <span>Edit</span>
                                                        </a>
                                                    </div>
                                                    <div class="flex items-center gap-1.5">
                                                        @if(($evt->status->value ?? $evt->status) === 'archived')
                                                            <button wire:click="unarchiveEvent('{{ $evt->uuid }}')" class="p-1.5 rounded-xl bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/40 text-xs font-bold transition-all cursor-pointer" title="Unarchive Event (Move to Draft)">
                                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                            </button>
                                                        @else
                                                            <button wire:click="archiveEvent('{{ $evt->uuid }}')" class="p-1.5 rounded-xl bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/40 text-xs font-bold transition-all cursor-pointer" title="Archive Event">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                                            </button>
                                                        @endif
                                                        <button wire:click="deleteEvent('{{ $evt->uuid }}')" wire:confirm="Are you sure you want to delete this event?" class="p-1.5 rounded-xl bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 border border-rose-500/40 text-xs font-bold transition-all cursor-pointer" title="Delete Event">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-span-full p-6 text-center text-slate-500 text-xs italic bg-slate-900/50 rounded-2xl border border-white/5">
                                                No events created under this organization workspace yet.
                                            </div>
                                        @endforelse
                                    </div>
                                @else
                                    <!-- Table View for Organization Workspace Events -->
                                    <div class="bg-slate-900 border border-white/10 rounded-2xl shadow-xl overflow-hidden mt-2">
                                        <div class="overflow-x-auto custom-scrollbar">
                                            <table class="w-full min-w-[700px] text-left border-collapse">
                                                <thead>
                                                    <tr class="bg-slate-950/80 border-b border-white/10 text-xs font-bold uppercase tracking-wider text-slate-400">
                                                        <th class="py-3.5 px-5 whitespace-nowrap">Event Name</th>
                                                        <th class="py-3.5 px-5 whitespace-nowrap">Date</th>
                                                        <th class="py-3.5 px-5 whitespace-nowrap">Venue</th>
                                                        <th class="py-3.5 px-5 whitespace-nowrap">Registrations / Verified</th>
                                                        <th class="py-3.5 px-5 whitespace-nowrap">Check-ins / Gates</th>
                                                        <th class="py-3.5 px-5 whitespace-nowrap">Status</th>
                                                        <th class="py-3.5 px-5 text-right whitespace-nowrap">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-white/5 text-sm text-slate-300">
                                                    @forelse($org->events as $evt)
                                                        <tr class="hover:bg-white/5 transition-colors">
                                                            <td class="py-3.5 px-5 font-bold text-white whitespace-nowrap">{{ $evt->name }}</td>
                                                            <td class="py-3.5 px-5 text-xs font-medium whitespace-nowrap">{{ $evt->starts_at ? $evt->starts_at->format('M j, Y g:i A') : 'TBD' }}</td>
                                                            <td class="py-3.5 px-5 text-xs font-medium whitespace-nowrap">{{ $evt->venue_name ?? 'Online' }}</td>
                                                            <td class="py-3.5 px-5 text-xs font-medium whitespace-nowrap">
                                                                <span class="text-blue-400 font-bold">{{ $evt->total_registrations_count ?? 0 }} Regs</span>
                                                                <span class="text-slate-500">/</span>
                                                                <span class="text-emerald-400 font-bold">{{ $evt->verified_attendees_count ?? 0 }} Verified</span>
                                                            </td>
                                                            <td class="py-3.5 px-5 text-xs font-medium whitespace-nowrap">
                                                                <span class="text-purple-400 font-bold">{{ $evt->checked_in_count ?? 0 }} In</span>
                                                                <span class="text-slate-500">/</span>
                                                                <span class="text-amber-400 font-bold">{{ $evt->gates_count ?? 0 }} Gates</span>
                                                            </td>
                                                            <td class="py-3.5 px-5 whitespace-nowrap">
                                                                <div class="flex flex-col items-start gap-1">
                                                                    @php
                                                                        $statusName = $evt->status->value ?? $evt->status ?? 'published';
                                                                        $statusBadgeStyle = match($statusName) {
                                                                            'published' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 dot-emerald-500',
                                                                            'draft' => 'bg-slate-500/10 text-slate-400 border-slate-500/20 dot-slate-400',
                                                                            'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/20 dot-rose-500',
                                                                            'completed' => 'bg-purple-500/10 text-purple-400 border-purple-500/20 dot-purple-500',
                                                                            'archived' => 'bg-amber-500/10 text-amber-400 border-amber-500/20 dot-amber-500',
                                                                            default => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 dot-emerald-500'
                                                                        };
                                                                    @endphp
                                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusBadgeStyle }}">
                                                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                                        <span>{{ ucfirst($statusName) }}</span>
                                                                    </span>
                                                                    @if($evt->is_private)
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                                                            🔒 Private
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                                                            🌐 Public
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                                                <div class="flex items-center justify-end gap-1.5">
                                                                    <a href="{{ route('events.show', $evt->uuid) }}" class="px-2.5 py-1.5 rounded-xl bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 font-bold text-xs flex items-center gap-1" title="Manage Event">
                                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                                        <span>Manage</span>
                                                                    </a>
                                                                    <a href="{{ route('events.edit', $evt->uuid) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs flex items-center gap-1" title="Edit Event">
                                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                    @if(($evt->status->value ?? $evt->status) === 'archived')
                                                                        <button wire:click="unarchiveEvent('{{ $evt->uuid }}')" class="p-1.5 rounded-xl bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/40 text-xs font-bold transition-all cursor-pointer" title="Unarchive Event (Move to Draft)">
                                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                                        </button>
                                                                    @else
                                                                        <button wire:click="archiveEvent('{{ $evt->uuid }}')" class="p-1.5 rounded-xl bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/40 text-xs font-bold transition-all cursor-pointer" title="Archive Event">
                                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                                                        </button>
                                                                    @endif
                                                                    <button wire:click="deleteEvent('{{ $evt->uuid }}')" wire:confirm="Are you sure you want to delete this event?" class="p-1.5 rounded-xl bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 border border-rose-500/40 text-xs font-bold transition-all cursor-pointer" title="Delete Event">
                                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="p-6 text-center text-slate-500 text-xs italic">
                                                                No events created under this organization workspace yet.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">No organization workspaces found.</div>
                @endforelse
            </div>
        </div>
    @else
    <div wire:loading.remove.delay>
        @if($events->isEmpty())
            <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-12 text-center shadow-sm dark:shadow-2xl">
                <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No events found</h3>
                <p class="text-slate-600 dark:text-slate-400 mb-6 font-medium text-sm">Get started by creating your first event.</p>
                <a href="{{ route('events.create') }}" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-sm inline-block shadow-lg shadow-blue-500/25">Create Event</a>
            </div>
        @else
            @if($viewMode === 'grid')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl overflow-hidden hover:border-blue-500/40 transition-all flex flex-col justify-between group">
                            <!-- Cover Image Header Banner -->
                            <div class="h-40 w-full bg-slate-900 overflow-hidden relative border-b border-slate-200 dark:border-white/10">
                                @if($event->cover_image_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($event->cover_image_path) }}" alt="{{ $event->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-900/40 via-indigo-900/30 to-purple-900/40 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-slate-600 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif

                                <div class="absolute top-3 left-3 right-3 flex justify-between items-start pointer-events-none">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-md shadow-sm
                                            {{ $event->status === \App\Enums\EventStatus::Published ? 'bg-emerald-500/80 text-white' : '' }}
                                            {{ $event->status === \App\Enums\EventStatus::Draft ? 'bg-slate-700/80 text-white' : '' }}
                                            {{ $event->status === \App\Enums\EventStatus::Cancelled ? 'bg-rose-500/80 text-white' : '' }}
                                        ">
                                            {{ ucfirst($event->status->value ?? 'Draft') }}
                                        </span>

                                        @if($event->is_private)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-900/90 text-purple-200 border border-purple-500/30 backdrop-blur-md shadow-sm">
                                                🔒 Private
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-900/90 text-blue-200 border border-blue-500/30 backdrop-blur-md shadow-sm">
                                                🌐 Public
                                            </span>
                                        @endif
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-900/80 backdrop-blur-md text-slate-200 border border-white/10 shadow-sm">{{ $event->is_free ? 'Free' : 'Paid' }}</span>
                                </div>
                            </div>

                            <div class="p-6">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $event->name }}</h3>
                                <p class="text-slate-600 dark:text-slate-400 text-xs font-medium line-clamp-2 mb-4">{{ $event->description ?? 'No description provided.' }}</p>
                                
                                <div class="space-y-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $event->starts_at ? $event->starts_at->format('M j, Y g:i A') : 'Date TBD' }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ $event->venue_name ?? 'Online / TBD' }}
                                    </div>
                                </div>

                                <!-- At-A-Glance Metric Badges Grid -->
                                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-white/10 grid grid-cols-2 gap-2 text-[11px] font-semibold">
                                    <div class="flex items-center gap-1.5 text-blue-600 dark:text-blue-400 bg-blue-500/10 dark:bg-blue-500/10 border border-blue-500/20 rounded-xl px-2.5 py-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>{{ number_format($event->total_registrations_count ?? $event->attendees_count ?? 0) }} Regs</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 dark:bg-emerald-500/10 border border-emerald-500/20 rounded-xl px-2.5 py-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ number_format($event->verified_attendees_count ?? 0) }} Verified</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-purple-600 dark:text-purple-400 bg-purple-500/10 dark:bg-purple-500/10 border border-purple-500/20 rounded-xl px-2.5 py-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                        <span>{{ number_format($event->checked_in_count ?? 0) }} Checked In</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400 bg-amber-500/10 dark:bg-amber-500/10 border border-amber-500/20 rounded-xl px-2.5 py-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        <span>{{ number_format($event->gates_count ?? 0) }} Gates</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-white/10 flex justify-between items-center flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('events.show', $event->uuid) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 text-xs font-bold transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Manage</span>
                                    </a>
                                    <a href="{{ route('events.edit', $event->uuid) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-500/10 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-500/20 border border-slate-500/20 text-xs font-bold transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </a>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if(($event->status->value ?? $event->status) === 'archived')
                                        <button wire:click="unarchiveEvent('{{ $event->uuid }}')" class="p-2 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 text-xs font-bold transition-all shadow-sm cursor-pointer" title="Unarchive Event (Move to Draft)">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        </button>
                                    @else
                                        <button wire:click="archiveEvent('{{ $event->uuid }}')" class="p-2 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 text-xs font-bold transition-all shadow-sm cursor-pointer" title="Archive Event">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                        </button>
                                    @endif
                                    <button wire:click="deleteEvent('{{ $event->uuid }}')" wire:confirm="Are you sure you want to delete this event?" class="p-2 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 text-xs font-bold transition-all shadow-sm cursor-pointer" title="Delete Event">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[700px] text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-white/10 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="py-4 px-6 whitespace-nowrap">Event Name</th>
                                    <th class="py-4 px-6 whitespace-nowrap">Date</th>
                                    <th class="py-4 px-6 whitespace-nowrap">Venue</th>
                                    <th class="py-4 px-6 whitespace-nowrap">Registrations / Verified</th>
                                    <th class="py-4 px-6 whitespace-nowrap">Check-ins / Gates</th>
                                    <th class="py-4 px-6 whitespace-nowrap">Status</th>
                                    <th class="py-4 px-6 text-right whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm text-slate-700 dark:text-slate-300">
                                @foreach($events as $event)
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors">
                                        <td class="py-4 px-6 font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ $event->name }}</td>
                                        <td class="py-4 px-6 text-xs font-medium whitespace-nowrap">{{ $event->starts_at ? $event->starts_at->format('M j, Y g:i A') : 'TBD' }}</td>
                                        <td class="py-4 px-6 text-xs font-medium whitespace-nowrap">{{ $event->venue_name ?? 'Online' }}</td>
                                        <td class="py-4 px-6 text-xs font-medium whitespace-nowrap">
                                            <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $event->total_registrations_count ?? 0 }} Regs</span>
                                            <span class="text-slate-400 dark:text-slate-500">/</span>
                                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $event->verified_attendees_count ?? 0 }} Verified</span>
                                        </td>
                                        <td class="py-4 px-6 text-xs font-medium whitespace-nowrap">
                                            <span class="text-purple-600 dark:text-purple-400 font-bold">{{ $event->checked_in_count ?? 0 }} In</span>
                                            <span class="text-slate-400 dark:text-slate-500">/</span>
                                            <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $event->gates_count ?? 0 }} Gates</span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="flex flex-col items-start gap-1">
                                                @php
                                                    $statusName = $event->status->value ?? $event->status ?? 'published';
                                                    $statusBadgeStyle = match($statusName) {
                                                        'published' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20 dot-emerald-500',
                                                        'draft' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20 dot-slate-400',
                                                        'cancelled' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20 dot-rose-500',
                                                        'completed' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20 dot-purple-500',
                                                        'archived' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20 dot-amber-500',
                                                        default => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20 dot-emerald-500'
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusBadgeStyle }}">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                    <span>{{ ucfirst($statusName) }}</span>
                                                </span>
                                                @if($event->is_private)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-300 border border-purple-500/20">
                                                        🔒 Private Event
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                                        🌐 Public Event
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('events.show', $event->uuid) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 text-xs font-bold transition-all shadow-sm" title="Manage Event">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span>Manage</span>
                                                </a>
                                                <a href="{{ route('events.edit', $event->uuid) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-500/10 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-500/20 border border-slate-500/20 text-xs font-bold transition-all shadow-sm" title="Edit Event">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Edit</span>
                                                </a>
                                                @if(($event->status->value ?? $event->status) === 'archived')
                                                    <button wire:click="unarchiveEvent('{{ $event->uuid }}')" class="p-1.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 text-xs font-bold transition-all shadow-sm cursor-pointer" title="Unarchive Event (Move to Draft)">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                    </button>
                                                @else
                                                    <button wire:click="archiveEvent('{{ $event->uuid }}')" class="p-1.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 text-xs font-bold transition-all shadow-sm cursor-pointer" title="Archive Event">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                                    </button>
                                                @endif
                                                <button wire:click="deleteEvent('{{ $event->uuid }}')" wire:confirm="Are you sure you want to delete this event?" class="p-1.5 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 text-xs font-bold transition-all shadow-sm cursor-pointer" title="Delete Event">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($events->hasPages())
                <div class="mt-6">
                    {{ $events->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        @endif
    </div>
    @endif
</div>
