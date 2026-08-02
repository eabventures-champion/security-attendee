<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Team & Role Management</h1>
            <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm mt-1">Add users, assign RBAC roles, and manage permissions across your organization.</p>
        </div>
            <button wire:click="openCreateModal" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer {{ auth()->check() && auth()->user()->invitation_status !== 'confirmed' ? 'opacity-60' : '' }}" title="{{ auth()->check() && auth()->user()->invitation_status !== 'confirmed' ? 'Requires Email Confirmation' : 'Add Team Member' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Add Team Member
                @if(auth()->check() && auth()->user()->invitation_status !== 'confirmed')
                    <svg class="w-4 h-4 text-amber-300 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                @endif
            </button>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-600 dark:text-emerald-400 text-sm font-medium flex items-center justify-between shadow-sm">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                {{ session('message') }}
            </span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-600 dark:text-rose-400 text-sm font-medium flex items-center justify-between shadow-sm">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </span>
        </div>
    @endif

    @if($isSuperAdmin && isset($pendingTypographyRequests) && count($pendingTypographyRequests) > 0)
        <div class="p-5 rounded-2xl bg-gradient-to-r from-purple-900/40 via-indigo-900/30 to-slate-900 border border-purple-500/40 shadow-xl space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        🎨
                    </span>
                    <h3 class="text-sm font-extrabold text-white">Pending Typography Subscription Requests ({{ count($pendingTypographyRequests) }})</h3>
                </div>
                <a href="{{ route('resources.index') }}" class="text-xs text-purple-400 hover:text-white font-bold underline">View in Resource Center →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($pendingTypographyRequests as $req)
                    <div class="p-3.5 rounded-xl bg-slate-900/80 border border-white/10 flex items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <div class="text-xs font-bold text-white">{{ $req->organization->name ?? 'Workspace' }}</div>
                            <div class="text-[11px] text-purple-300 font-semibold">{{ $req->subject }}</div>
                            <div class="text-[10px] text-slate-500">{{ $req->created_at->diffForHumans() }}</div>
                        </div>
                        <button type="button" wire:click="approveTypographyRequest({{ $req->id }}, {{ $req->organization_id }})" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs shadow-md cursor-pointer shrink-0">
                            Approve & Unlock Pack
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 p-4 rounded-2xl shadow-sm dark:shadow-xl backdrop-blur-xl">
        <div class="relative w-full sm:w-80">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search team members by name or email..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-sm transition-all">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <label class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Filter by Role:</label>
            <select wire:model.live="roleFilter" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                <option value="">All Roles</option>
                <option value="organization_admin">Organization Admin</option>
                <option value="event_manager">Event Manager</option>
                <option value="security_officer">Security Officer</option>
                <option value="volunteer">Volunteer</option>
                <option value="scanner_operator">Scanner Operator</option>
            </select>
        </div>
    </div>

    <!-- Bulk Action Bar -->
    @if(count($selectedUsers) > 0)
        <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-2xl flex items-center justify-between animate-fadeInUp">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-xl bg-red-500/20 text-red-400 border border-red-500/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white">{{ count($selectedUsers) }} {{ Str::plural('Workspace', count($selectedUsers)) }} Selected</h4>
                    <p class="text-xs text-red-300">Selected workspaces and all associated events, attendees, and team members will be permanently deleted.</p>
                </div>
            </div>
            <button type="button" wire:click="deleteSelected" wire:confirm="Permanently delete all {{ count($selectedUsers) }} selected workspace(s) and ALL associated events, attendees, and team members?" class="px-5 py-2.5 text-xs font-extrabold text-white bg-red-600 hover:bg-red-500 border border-red-500/40 rounded-xl transition-all shadow-lg shadow-red-600/30 cursor-pointer flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Delete Selected ({{ count($selectedUsers) }})</span>
            </button>
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl overflow-hidden backdrop-blur-xl">
        <div class="overflow-x-auto">
            @if($isSuperAdmin)
                <!-- Super Admin View: Encased Organization & Admin Table -->
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-white/10 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="py-4 px-4 w-12 text-center">
                                <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-slate-700 text-blue-600 focus:ring-blue-500 bg-slate-900 cursor-pointer" title="Select All Workspaces">
                            </th>
                            <th class="py-4 px-6">Organization & Admin</th>
                            <th class="py-4 px-6">Super Admin Approval</th>
                            <th class="py-4 px-6">Workspace Team</th>
                            <th class="py-4 px-6 text-right">Workspace Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm text-slate-700 dark:text-slate-300">
                        @forelse($users as $user)
                            @php
                                $org = $user->organization;
                                $teamMembers = $org ? $org->users->reject(fn($u) => $u->hasRole('super_admin')) : collect([$user]);
                                $isExpanded = $org && in_array($org->id, $expandedOrgs);
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 w-12 text-center">
                                    <input type="checkbox" wire:model.live="selectedUsers" value="{{ $user->id }}" class="w-4 h-4 rounded border-slate-700 text-blue-600 focus:ring-blue-500 bg-slate-900 cursor-pointer">
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-base shadow-md shadow-blue-500/20">
                                            {{ strtoupper(substr($org->name ?? $user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-slate-900 dark:text-white text-base flex items-center gap-2">
                                                {{ $org->name ?? 'Personal Workspace' }}
                                                <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                                    Org ID: {{ $org->id ?? 1 }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @if($user->approval_status === 'approved' && $user->is_active)
                                        <div class="flex flex-col gap-0.5">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 w-max">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                Approved & Active
                                            </span>
                                            @if($user->approved_at)
                                                <span class="text-[10px] text-slate-400 font-medium ml-1">
                                                    Approved {{ $user->approved_at->format('M j, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif($user->approval_status === 'suspended' || !$user->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-500/15 text-rose-500 border border-rose-500/30">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            Suspended
                                        </span>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30 animate-pulse">
                                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Pending Review
                                            </span>
                                            <button type="button" wire:click="approveOrgAdmin({{ $user->id }})" class="p-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 border border-emerald-500/30 rounded-xl transition-all shadow-md cursor-pointer flex items-center justify-center shrink-0" title="Approve & Activate Workspace">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10">
                                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        {{ $teamMembers->count() }} Team {{ Str::plural('Member', $teamMembers->count()) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Impersonate Icon Button -->
                                        <a href="{{ route('superadmin.impersonate', $user->id) }}" class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500 text-amber-400 hover:text-slate-950 border border-amber-500/30 transition-all shadow-sm cursor-pointer shrink-0" title="Impersonate Workspace">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        </a>

                                        <!-- Premium Typography Pack Subscription Toggle -->
                                        @if($org)
                                            <button type="button" wire:click="togglePremiumTypography({{ $org->id }})" class="p-2 rounded-xl {{ $org->has_premium_typography ? 'bg-purple-500/20 text-purple-300 border-purple-500/40' : 'bg-slate-800 text-slate-400 border-white/10 hover:text-purple-300' }} border transition-all shadow-sm cursor-pointer shrink-0" title="{{ $org->has_premium_typography ? 'Premium Typography Subscription Active (Click to Revoke)' : 'Unlock Premium Typography Pack for this Workspace' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                            </button>
                                        @endif

                                        <!-- Disable / Re-Activate Icon Button -->
                                        @if($user->approval_status === 'approved' && $user->is_active)
                                            <button type="button" wire:click="disableOrgAdmin({{ $user->id }})" wire:confirm="Suspend this Organization Admin and ALL associated team members?" class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/30 transition-all shadow-sm cursor-pointer shrink-0" title="Disable Workspace & Suspend Team Members">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </button>
                                        @else
                                            <button type="button" wire:click="approveOrgAdmin({{ $user->id }})" class="p-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-500/30 transition-all shadow-sm cursor-pointer shrink-0" title="Re-Activate Workspace & Team Members">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        @endif

                                        <!-- Delete Workspace Icon Button -->
                                        <button type="button" wire:click="deleteOrgWorkspace({{ $user->id }})" wire:confirm="Permanently delete this organization workspace and ALL associated events, attendees, and team members?" class="p-2 rounded-xl bg-red-500/10 hover:bg-red-600 text-red-400 hover:text-white border border-red-500/30 transition-all shadow-sm cursor-pointer shrink-0" title="Delete Organization Workspace">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>

                                        <!-- View Team Button -->
                                        @if($org && $teamMembers->count() > 0)
                                            <button type="button" wire:click="toggleExpandOrg({{ $org->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition-all cursor-pointer whitespace-nowrap">
                                                @if($isExpanded)
                                                    <span>Collapse Team</span>
                                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                @else
                                                    <span>View Team ({{ $teamMembers->count() }})</span>
                                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                @endif
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Expanded Sub-Table: Nested Team Members under Organization Admin -->
                            @if($isExpanded)
                                <tr class="bg-slate-900/90 dark:bg-slate-950/80 border-t border-b border-blue-500/20">
                                    <td colspan="5" class="p-4 pl-12">
                                        <div class="p-4 rounded-2xl bg-slate-900 border border-white/10 shadow-inner space-y-3">
                                            <div class="flex items-center justify-between border-b border-white/5 pb-2">
                                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-blue-400 flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                    Encased Team Members under {{ $org->name }}
                                                </h4>
                                                <span class="text-[11px] text-slate-400 font-medium">Created & Managed by Organization Admin ({{ $user->name }})</span>
                                            </div>

                                            <table class="w-full text-left text-xs border-collapse">
                                                <thead>
                                                    <tr class="text-slate-500 border-b border-white/5 font-bold uppercase tracking-wider">
                                                        <th class="py-2.5 px-3">Team Member</th>
                                                        <th class="py-2.5 px-3">Role & Access</th>
                                                        <th class="py-2.5 px-3">Confirmation Receipt</th>
                                                        <th class="py-2.5 px-3">Status</th>
                                                        <th class="py-2.5 px-3 text-right">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-white/5 text-slate-300">
                                                    @foreach($teamMembers as $member)
                                                        <tr class="hover:bg-white/5 transition-colors">
                                                            <td class="py-3 px-3">
                                                                <div class="flex items-center gap-2.5">
                                                                    <img class="w-8 h-8 rounded-full border border-blue-500/30 object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=1e293b&color=60a5fa&bold=true" alt="{{ $member->name }}">
                                                                    <div>
                                                                        <div class="font-bold text-white">{{ $member->name }}</div>
                                                                        <div class="text-[11px] text-slate-400 font-medium">{{ $member->email }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="py-3 px-3">
                                                                <div class="flex flex-col gap-1 items-start">
                                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold 
                                                                        {{ $member->role_color === 'purple' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : '' }}
                                                                        {{ $member->role_color === 'emerald' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                                                        {{ $member->role_color === 'blue' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                                                        {{ $member->role_color === 'amber' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                                                        {{ $member->role_color === 'slate' ? 'bg-slate-500/10 text-slate-400 border border-slate-500/20' : '' }}
                                                                    ">
                                                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                                        {{ $member->role_label }}
                                                                    </span>
                                                                    @if($member->assignedGate)
                                                                         <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                                                             Gate: {{ $member->assignedGate->name }}{{ $member->assignedGate->event ? ' ('.$member->assignedGate->event->name.')' : '' }}
                                                                         </span>
                                                                     @endif
                                                                </div>
                                                            </td>
                                                            <td class="py-3 px-3">
                                                                 @if($member->invitation_status === 'confirmed')
                                                                     <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                                         Confirmed
                                                                     </span>
                                                                @else
                                                                    <div class="flex items-center gap-1.5">
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30 animate-pulse">
                                                                            Pending Receipt
                                                                        </span>
                                                                        <button type="button" 
                                                                                wire:click="resendInvitation({{ $member->id }})" 
                                                                                wire:loading.attr="disabled"
                                                                                wire:target="resendInvitation({{ $member->id }})"
                                                                                class="px-2 py-0.5 text-[10px] font-bold text-blue-400 hover:text-white bg-blue-500/10 hover:bg-blue-600 border border-blue-500/20 rounded-md transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                                                            <span wire:loading.remove wire:target="resendInvitation({{ $member->id }})">
                                                                                @if($resentUserId === $member->id)
                                                                                    <span class="text-emerald-400 flex items-center gap-0.5"><svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg> Sent!</span>
                                                                                @else
                                                                                    Resend
                                                                                @endif
                                                                            </span>
                                                                            <span wire:loading wire:target="resendInvitation({{ $member->id }})" class="flex items-center gap-1 text-blue-400">
                                                                                <svg class="animate-spin w-3 h-3 text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                                                Sending...
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="py-3 px-3">
                                                                @if($member->approval_status === 'approved' && $member->is_active)
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                                        Active
                                                                    </span>
                                                                @elseif($member->invitation_status === 'pending')
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                                                        Pending Invite
                                                                    </span>
                                                                @elseif($member->is_active)
                                                                    <span class="text-emerald-400 font-medium text-xs">Active</span>
                                                                @else
                                                                    <span class="text-rose-400 font-medium text-xs">Disabled</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-3 px-3 whitespace-nowrap">
                                                                <div class="flex items-center justify-end gap-1">
                                                                    <button type="button" wire:click="resendInvitation({{ $member->id }})" class="p-1 text-slate-400 hover:text-indigo-400 cursor-pointer" title="Resend Activation / Invitation Email">
                                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                                    </button>
                                                                    <button type="button" wire:click="openEditModal({{ $member->id }})" class="p-1 text-slate-400 hover:text-blue-400 cursor-pointer" title="Edit Member">
                                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                    </button>
                                                                    <button type="button" wire:click="toggleUserStatus({{ $member->id }})" class="p-1 text-slate-400 hover:text-amber-400 cursor-pointer" title="Toggle Status">
                                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-400">
                                    No organization administrators found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <!-- Organization Admin View: Standard Team Member Table -->
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-white/10 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="py-4 px-6">User & Role</th>
                            <th class="py-4 px-6">Confirmation Receipt</th>
                            <th class="py-4 px-6">Account Status</th>
                            <th class="py-4 px-6">Last Active</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm text-slate-700 dark:text-slate-300">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <img class="w-10 h-10 rounded-full border border-blue-500/30 object-cover shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=3b82f6&color=fff&bold=true" alt="{{ $user->name }}">
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $user->email }}</div>
                                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold 
                                                    {{ $user->role_color === 'purple' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' : '' }}
                                                    {{ $user->role_color === 'emerald' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : '' }}
                                                    {{ $user->role_color === 'blue' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : '' }}
                                                    {{ $user->role_color === 'amber' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : '' }}
                                                    {{ $user->role_color === 'slate' ? 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20' : '' }}
                                                ">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                    {{ $user->role_label }}
                                                </span>
                                                @if($user->assignedGate)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                        Gate: {{ $user->assignedGate->name }}{{ $user->assignedGate->event ? ' ('.$user->assignedGate->event->name.')' : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @if($user->invitation_status === 'confirmed')
                                        <div class="flex flex-col gap-0.5" title="Confirmed {{ $user->invitation_accepted_at ? $user->invitation_accepted_at->format('M j, Y @ g:i A') : '' }}">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 w-max">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                Confirmed
                                            </span>
                                            @if($user->invitation_accepted_at)
                                                <span class="text-[10px] text-slate-400 font-medium ml-1">
                                                    {{ $user->invitation_accepted_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30 animate-pulse">
                                                <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Pending Receipt
                                            </span>
                                            <button type="button" 
                                                    wire:click="resendInvitation({{ $user->id }})" 
                                                    wire:loading.attr="disabled"
                                                    wire:target="resendInvitation({{ $user->id }})"
                                                    class="px-2.5 py-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:text-white bg-blue-500/10 hover:bg-blue-600 border border-blue-500/20 rounded-lg transition-all cursor-pointer flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed" 
                                                    title="Resend Email Invitation Link">
                                                <span wire:loading.remove wire:target="resendInvitation({{ $user->id }})" class="flex items-center gap-1">
                                                    @if($resentUserId === $user->id)
                                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sent!</span>
                                                    @else
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                        Resend Invite
                                                    @endif
                                                </span>
                                                <span wire:loading wire:target="resendInvitation({{ $user->id }})" class="flex items-center gap-1 text-blue-400">
                                                    <svg class="animate-spin w-3 h-3 text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    Sending...
                                                </span>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if($user->approval_status === 'approved' && $user->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            Active
                                        </span>
                                    @elseif($user->invitation_status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                            Pending Invite
                                        </span>
                                    @elseif($user->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            Disabled
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-500 dark:text-slate-400 text-xs font-medium">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1 sm:gap-2">
                                        <button type="button" wire:click="resendInvitation({{ $user->id }})" class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-500/10 rounded-lg transition-colors cursor-pointer" title="Resend Activation / Invitation Email">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </button>
                                        <button type="button" wire:click="openEditModal({{ $user->id }})" class="p-2 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition-colors cursor-pointer" title="Edit User">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button type="button" wire:click="toggleUserStatus({{ $user->id }})" class="p-2 text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-500/10 rounded-lg transition-colors cursor-pointer" title="Toggle Status">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <button type="button" wire:click="deleteUser({{ $user->id }})" wire:confirm="Are you sure you want to remove this user?" class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors cursor-pointer" title="Delete User">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    No team members found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-white/10">
                {{ $users->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>

    <!-- Modal Form for Add/Edit User -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-3 sm:p-4 pt-8 sm:pt-4 overflow-y-auto bg-slate-950/70 backdrop-blur-md animate-fadeInUp">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl sm:rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden my-4 sm:my-auto">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-white/10 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $editingUserId ? 'Edit Team Member' : 'Add New Team Member' }}</h3>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveUser" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                        <input wire:model="name" type="text" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="e.g. Sarah Jenkins">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input wire:model="email" type="email" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="sarah@company.com">
                        @error('email') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number (Optional)</label>
                        <input wire:model="phone" type="text" maxlength="10" placeholder="0246345698 (10 digits)" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('phone') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Password {{ $editingUserId ? '(Leave blank to keep current)' : '*' }}</label>
                        <div x-data="{ showPassword: false }" class="relative">
                            <input wire:model="password" :type="showPassword ? 'text' : 'password'" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none pr-10" placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-200 transition-colors cursor-pointer focus:outline-none" :title="showPassword ? 'Hide Password' : 'Show Password'">
                                <svg x-show="showPassword" x-cloak class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="!showPassword" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.04 10.04 0 013.122-.463c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.163 3.592m-4.417 4.417A3 3 0 0112 15a3 3 0 01-2.122-.878m-2.122-2.122A3 3 0 019 12c0-.424.088-.828.246-1.192"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            An invitation email with access credentials & dashboard link will be dispatched automatically.
                        </p>
                        @error('password') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Assign Role <span class="text-rose-500">*</span></label>
                        <select wire:model.live="selectedRole" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
                            <option value="organization_admin">Organization Admin (Full Access)</option>
                            <option value="event_manager">Event Manager (Events, Attendees, Gates)</option>
                            <option value="security_officer">Security Officer (QR Scanner & Check-in)</option>
                            <option value="volunteer">Volunteer (Basic Check-in)</option>
                            <option value="scanner_operator">Scanner Operator (Scan Only)</option>
                        </select>
                        @error('selectedRole') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    @if(in_array($selectedRole, ['security_officer', 'gate_staff', 'security', 'gate-staff', 'gate-security']))
                        <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 space-y-2 animate-fadeIn">
                            <label class="block text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Assign Security Officer To Gate
                            </label>
                            <select wire:model="assigned_gate_id" class="w-full bg-slate-900 border border-amber-500/40 rounded-xl px-3 py-2.5 text-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-500 text-xs font-medium">
                                <option value="">-- Select Gate (Optional) --</option>
                                @foreach($gates as $gt)
                                    <option value="{{ $gt->id }}">{{ $gt->name }} ({{ $gt->event->name ?? 'Event' }})</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400">Security personnel will be restricted to viewing and scanning only this specific gate when logged in.</p>
                        </div>
                    @endif

                    <div class="flex items-center pt-2">
                        <input wire:model="is_active" type="checkbox" id="is_active_check" class="w-4 h-4 rounded bg-slate-100 dark:bg-white/5 border-slate-300 dark:border-white/20 text-blue-600 focus:ring-blue-500/50 cursor-pointer">
                        <label for="is_active_check" class="ml-2 text-sm text-slate-700 dark:text-slate-300 font-medium cursor-pointer">Account is Active & Allowed to Sign In</label>
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-white/10 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                        <button type="button" wire:click="closeModal" class="w-full sm:w-auto px-4 py-2.5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-medium cursor-pointer text-center">Cancel</button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 cursor-pointer text-center">
                            {{ $editingUserId ? 'Update Member' : 'Add Member' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
