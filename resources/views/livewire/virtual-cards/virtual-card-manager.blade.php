<div class="space-y-8 font-inter">
    <!-- Header -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 pb-2">
        <div class="space-y-1.5">
            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Virtual ID Cards</h1>
                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-sm">
                    {{ number_format($totalCount) }} Generated
                </span>
                <button wire:click="openInstitutionsModal" 
                        class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-orange-500/10 hover:bg-orange-500/20 text-orange-600 dark:text-orange-400 border border-orange-500/30 shadow-sm transition-all flex items-center gap-1.5 cursor-pointer active:scale-95"
                        title="Manage and upload institution/faculty names for dropdown">
                    <span>🏛️</span>
                    <span>{{ count($institutionList) }} Institutions</span>
                </button>
                @if(count($selectedMembers) > 0)
                    <button wire:click="triggerBulkCardsDownload(true)" 
                            wire:loading.attr="disabled"
                            class="px-3 py-1 rounded-full border border-teal-500/30 bg-teal-500/10 hover:bg-teal-500/20 text-teal-600 dark:text-teal-400 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer active:scale-95 shadow-sm"
                            title="Download full Student ID Cards for selected members in a ZIP package">
                        <svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Download Cards ({{ count($selectedMembers) }})</span>
                    </button>
                    <button wire:click="bulkDelete" 
                            wire:confirm="Are you sure you want to delete {{ count($selectedMembers) }} selected virtual ID card(s)?"
                            class="px-2.5 py-1 rounded-full border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>Delete ({{ count($selectedMembers) }})</span>
                    </button>
                @endif
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-medium text-xs sm:text-sm">
                Manage members, customize card fields &amp; logo, and auto-generate digital ID credentials.
            </p>
        </div>

        <!-- Action Toolbar -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            
            <!-- 1. Fields & Logo -->
            <button wire:click="$set('showFieldCustomizerModal', true)" 
                    class="h-10 px-3 sm:px-3.5 rounded-xl border border-purple-500/30 bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95 whitespace-nowrap" 
                    title="Customize card fields, add custom fields, and upload institution logo">
                <svg class="w-4 h-4 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Fields &amp; Logo</span>
            </button>

            <!-- 2. ID CARDS ZIP -->
            <button wire:click="triggerBulkCardsDownload(false)" 
                    wire:loading.attr="disabled"
                    class="h-10 px-3 sm:px-3.5 rounded-xl border border-teal-500/30 bg-teal-500/10 hover:bg-teal-500/20 text-teal-600 dark:text-teal-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95 whitespace-nowrap" 
                    title="Render and download high-resolution ID cards for all members in a ZIP package">
                <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span wire:loading.remove wire:target="triggerBulkCardsDownload(false)">ID Cards (.ZIP)</span>
                <span wire:loading wire:target="triggerBulkCardsDownload(false)" class="flex items-center gap-1">
                    <svg class="animate-spin h-3.5 w-3.5 text-teal-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Preparing...</span>
                </span>
            </button>

            <!-- 3. PHOTOS ZIP -->
            <button wire:click="downloadAllPhotos" 
                    wire:loading.attr="disabled"
                    class="h-10 px-3 sm:px-3.5 rounded-xl border border-orange-500/30 bg-orange-500/10 hover:bg-orange-500/20 text-orange-600 dark:text-orange-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95 whitespace-nowrap" 
                    title="Download ZIP package of all member profile photos">
                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span wire:loading.remove wire:target="downloadAllPhotos">Photos (.ZIP)</span>
                <span wire:loading wire:target="downloadAllPhotos" class="flex items-center gap-1">
                    <svg class="animate-spin h-3.5 w-3.5 text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Zipping...</span>
                </span>
            </button>

            <!-- 4. SHARE LINK -->
            <button wire:click="openShareLinkModal" 
                    class="h-10 px-3 sm:px-3.5 rounded-xl border border-amber-500/30 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95 whitespace-nowrap" 
                    title="Generate and copy shareable member registration & card generation link">
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                <span>Share Link</span>
            </button>

            <!-- 5. IMPORT -->
            <button wire:click="openUploadModal" 
                    class="h-10 px-3 sm:px-3.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95 whitespace-nowrap" 
                    title="Batch upload members from Excel or CSV spreadsheet">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path>
                </svg>
                <span>Import</span>
            </button>

            <!-- 6. ADD MEMBER -->
            <button wire:click="openAddModal" 
                    class="h-10 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg> 
                <span>Add Member</span>
            </button>
        </div>
    </div>

    <!-- Summary Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 animate-fadeInUp">
        <!-- Total Cards -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/5 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-black uppercase tracking-wider">Total Members</span>
                <div class="p-2 rounded-xl bg-blue-500/10 text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalCount) }}</div>
        </div>

        <!-- Active Cards -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/5 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Active IDs</span>
                <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($activeCount) }}</div>
        </div>

        <!-- Institutions Stat Card (Click to View) -->
        <button type="button" 
                wire:click="openInstitutionsModal('active')" 
                class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/5 shadow-sm space-y-2 text-left cursor-pointer hover:border-purple-500/50 hover:shadow-lg hover:shadow-purple-500/5 transition-all active:scale-[0.98] group w-full"
                title="Click to view institutions & members breakdown">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-black uppercase tracking-wider text-purple-600 dark:text-purple-400">
                    Institutions
                </span>
                <div class="p-2 rounded-xl bg-purple-500/10 text-purple-500 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ count($institutions) }}</div>
                <div class="text-xs font-semibold text-slate-400">Active ({{ count($institutionList) }} in list)</div>
            </div>
        </button>

        <!-- Dispatch Channels -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/5 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">Dispatch Channels</span>
                <div class="p-2 rounded-xl bg-amber-500/10 text-amber-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2 pt-1.5">
                <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-500 font-extrabold">✉️ Email</span>
                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-500 font-extrabold">📱 WhatsApp</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/5 shadow-sm">
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-2.5">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, email, ID, faculty, designation..." class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <!-- Designation Filter -->
            <div class="w-full md:w-44 shrink-0">
                <select wire:model.live="designationFilter" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Designations</option>
                    <option value="member">Regular Members</option>
                    <option value="executive">Executives Only</option>
                </select>
            </div>

            <!-- Institution Filter -->
            <div class="w-full md:w-56 shrink-0">
                <select wire:model.live="institutionFilter" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Institutions ({{ count($institutions) }})</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst }}">{{ $inst }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-36 shrink-0">
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Bulk Selection Bar -->
    @if(!empty($selectedMembers))
        <div class="p-3.5 px-5 rounded-2xl bg-slate-900 border border-blue-500/40 shadow-xl flex flex-wrap items-center justify-between gap-3 animate-fadeIn text-xs">
            <div class="flex items-center gap-2 font-bold text-white">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-400 font-mono text-xs border border-blue-500/30">
                    {{ count($selectedMembers) }}
                </span>
                <span>member(s) selected</span>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="downloadSelectedPhotos" 
                        wire:loading.attr="disabled"
                        class="px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-orange-500/20 transition-all cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span wire:loading.remove wire:target="downloadSelectedPhotos">Download Selected Photos (.ZIP)</span>
                    <span wire:loading wire:target="downloadSelectedPhotos">Zipping...</span>
                </button>
                <button wire:click="$set('selectedMembers', [])" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white font-bold text-xs transition-all cursor-pointer">
                    Deselect All
                </button>
            </div>
        </div>
    @endif

    <!-- Members & Virtual ID Cards Table -->
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-white/5 text-slate-400 font-extrabold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-10">
                            <input type="checkbox" wire:model.live="selectAll" class="form-checkbox h-4 w-4 text-blue-600 rounded border-slate-300 dark:border-slate-700">
                        </th>
                        <th class="py-3.5 px-4">Member</th>
                        <th class="py-3.5 px-4">Designation &amp; Institution</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Card Actions &amp; Dispatch</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 font-medium text-slate-700 dark:text-slate-200">
                    @forelse($members as $m)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="py-3.5 px-4">
                                <input type="checkbox" wire:model.live="selectedMembers" value="{{ (string)$m->id }}" class="form-checkbox h-4 w-4 text-blue-600 rounded border-slate-300 dark:border-slate-700">
                            </td>
                            
                            <!-- Member Photo & Name -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <!-- Photo or Silhouette -->
                                    <div class="w-10 h-12 rounded-lg border border-slate-200 dark:border-white/10 overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 flex items-center justify-center shadow-sm">
                                        @if($m->photo_url)
                                            <img src="{{ $m->photo_url }}" alt="{{ $m->full_name }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-black text-slate-900 dark:text-white text-sm truncate flex items-center gap-1.5">
                                            <span>{{ $m->full_name }}</span>
                                            <span class="text-blue-500" title="Digitally signed ID">
                                                <svg class="w-3.5 h-3.5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Merged Designation & Institution Column (Clickable to reveal details) -->
                            <td class="py-3.5 px-4">
                                <div class="relative inline-block" x-data="{ showDetails: false }" @click.outside="showDetails = false">
                                    @if($m->isExecutive())
                                        <button @click="showDetails = !showDetails" 
                                                type="button" 
                                                class="group inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30 text-xs font-black uppercase tracking-wider transition-all cursor-pointer shadow-sm active:scale-95 text-left">
                                            <span>⭐ Executive</span>
                                            @if(!empty($m->position))
                                                <span class="text-slate-900 dark:text-white font-bold normal-case text-xs truncate max-w-[140px]">• {{ $m->position }}</span>
                                            @endif
                                            <svg class="w-3 h-3 text-amber-500/70 group-hover:text-amber-400 transition-transform duration-200" :class="{ 'rotate-180': showDetails }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <!-- Executive Details Popover -->
                                        <div x-show="showDetails"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             style="display: none;"
                                             class="absolute left-0 mt-2 w-72 rounded-2xl bg-slate-900 border border-amber-500/30 shadow-2xl p-3.5 z-40 space-y-2.5 text-left">
                                            
                                            <div class="flex items-center justify-between border-b border-white/10 pb-2">
                                                <span class="text-[10px] font-black uppercase tracking-wider text-amber-400">Executive Details</span>
                                                <span class="text-[10px] font-mono font-bold text-slate-400">{{ $m->member_id_number }}</span>
                                            </div>

                                            <div class="space-y-2 text-xs">
                                                <div>
                                                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block">Position / Role</span>
                                                    <span class="font-black text-amber-300 text-xs">{{ $m->position ?: 'Executive Officer' }}</span>
                                                </div>

                                                <div>
                                                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block">Institution / Faculty</span>
                                                    <span class="font-semibold text-slate-200 block text-xs leading-snug">{{ $m->institution ?: 'Federation of African Law Students (FALAS)' }}</span>
                                                </div>

                                                @if($m->admission_year || $m->completion_year)
                                                    <div>
                                                        <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block">Academic Period</span>
                                                        <span class="font-mono text-slate-300 font-bold text-xs">{{ $m->admission_year ?: 'N/A' }} — {{ $m->completion_year ?: 'Present' }}</span>
                                                    </div>
                                                @endif

                                                <!-- Contact Details -->
                                                <div class="pt-2 border-t border-white/10 space-y-1.5">
                                                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block">Contact Details</span>
                                                    <div class="space-y-1 text-xs">
                                                        <div class="flex items-center gap-2 text-slate-300">
                                                            <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                            <span class="truncate font-medium">{{ $m->email ?: 'No email configured' }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2 text-slate-300">
                                                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.97.581 1.936.945 2.795.945 3.179 0 5.766-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm10.009 5.766c-.002 5.51-4.484 9.991-9.996 9.991-1.748 0-3.377-.45-4.814-1.242L2 22l1.523-5.568C2.693 14.972 2.21 13.35 2.21 11.938c.002-5.51 4.484-9.992 9.997-9.992 5.513 0 9.995 4.482 9.996 9.992z"/></svg>
                                                            <span class="font-mono text-[11px]">{{ $m->phone ?: 'No phone configured' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <button @click="showDetails = !showDetails" 
                                                type="button" 
                                                class="group inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800/80 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-white/10 text-xs font-bold transition-all cursor-pointer shadow-sm active:scale-95 text-left">
                                            <span>👤 Member</span>
                                            <svg class="w-3 h-3 text-slate-400 group-hover:text-slate-200 transition-transform duration-200" :class="{ 'rotate-180': showDetails }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <!-- Member Details Popover -->
                                        <div x-show="showDetails"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             style="display: none;"
                                             class="absolute left-0 mt-2 w-72 rounded-2xl bg-slate-900 border border-blue-500/30 shadow-2xl p-3.5 z-40 space-y-2.5 text-left">
                                            
                                            <div class="flex items-center justify-between border-b border-white/10 pb-2">
                                                <span class="text-[10px] font-black uppercase tracking-wider text-blue-400">Member Details</span>
                                                <span class="text-[10px] font-mono font-bold text-slate-400">{{ $m->member_id_number }}</span>
                                            </div>

                                            <div class="space-y-2 text-xs">
                                                <div>
                                                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block">Designation</span>
                                                    <span class="font-bold text-white text-xs">General Member</span>
                                                </div>

                                                <div>
                                                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block">Institution / Faculty</span>
                                                    <span class="font-semibold text-slate-200 block text-xs leading-snug">{{ $m->institution ?: 'Federation of African Law Students (FALAS)' }}</span>
                                                </div>

                                                @if($m->admission_year || $m->completion_year)
                                                    <div>
                                                        <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block">Academic Period</span>
                                                        <span class="font-mono text-slate-300 font-bold text-xs">{{ $m->admission_year ?: 'N/A' }} — {{ $m->completion_year ?: 'Present' }}</span>
                                                    </div>
                                                @endif

                                                <!-- Contact Details -->
                                                <div class="pt-2 border-t border-white/10 space-y-1.5">
                                                    <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block">Contact Details</span>
                                                    <div class="space-y-1 text-xs">
                                                        <div class="flex items-center gap-2 text-slate-300">
                                                            <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                            <span class="truncate font-medium">{{ $m->email ?: 'No email configured' }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2 text-slate-300">
                                                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.97.581 1.936.945 2.795.945 3.179 0 5.766-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm10.009 5.766c-.002 5.51-4.484 9.991-9.996 9.991-1.748 0-3.377-.45-4.814-1.242L2 22l1.523-5.568C2.693 14.972 2.21 13.35 2.21 11.938c.002-5.51 4.484-9.992 9.997-9.992 5.513 0 9.995 4.482 9.996 9.992z"/></svg>
                                                            <span class="font-mono text-[11px]">{{ $m->phone ?: 'No phone configured' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $m->status === 'active' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border border-rose-500/20' }}">
                                    {{ $m->status }}
                                </span>
                            </td>

                            <!-- Actions & Dispatch -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    <!-- Download Member Photo (If uploaded) -->
                                    @if($m->photo_path || $m->photo_url)
                                        <button wire:click="downloadPhoto({{ $m->id }})" class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 font-bold transition-all cursor-pointer active:scale-95" title="Download Member Profile Photo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    @endif

                                    <!-- Live Card Preview -->
                                    <button wire:click="openCardPreview({{ $m->id }})" class="p-2 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-bold transition-all cursor-pointer active:scale-95" title="View Virtual ID Card Pass">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    <!-- Compact Dispatch Dropdown Menu (Email & WhatsApp) -->
                                    <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                        <button @click="open = !open" 
                                                type="button" 
                                                class="p-2 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 border border-purple-500/20 font-bold transition-all cursor-pointer flex items-center gap-1 active:scale-95" 
                                                title="Dispatch Virtual ID via Email or WhatsApp">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            <svg class="w-2.5 h-2.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             style="display: none;"
                                             class="absolute right-0 mt-1.5 w-52 rounded-2xl bg-slate-900 border border-white/10 shadow-2xl p-1.5 z-40 space-y-1 text-left">
                                            
                                            <div class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-white/5">
                                                Dispatch Virtual ID
                                            </div>

                                            <!-- Send via Email -->
                                            <button type="button"
                                                    @click="open = false"
                                                    wire:click="sendCardEmail({{ $m->id }})" 
                                                    class="w-full px-2.5 py-2 rounded-xl text-left font-bold text-xs text-purple-400 hover:bg-purple-500/15 transition-all flex items-center gap-2.5 cursor-pointer group">
                                                <div class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400 group-hover:bg-purple-500/20 shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <span class="block text-white text-xs font-bold">Send via Email</span>
                                                    <span class="block text-[10px] text-slate-400 font-normal truncate">{{ $m->email ?: 'No email configured' }}</span>
                                                </div>
                                            </button>

                                            <!-- Send via WhatsApp -->
                                            <button type="button"
                                                    @click="open = false"
                                                    wire:click="sendCardWhatsApp({{ $m->id }})" 
                                                    class="w-full px-2.5 py-2 rounded-xl text-left font-bold text-xs text-emerald-400 hover:bg-emerald-500/15 transition-all flex items-center gap-2.5 cursor-pointer group">
                                                <div class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20 shrink-0">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.97.581 1.936.945 2.795.945 3.179 0 5.766-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm10.009 5.766c-.002 5.51-4.484 9.991-9.996 9.991-1.748 0-3.377-.45-4.814-1.242L2 22l1.523-5.568C2.693 14.972 2.21 13.35 2.21 11.938c.002-5.51 4.484-9.992 9.997-9.992 5.513 0 9.995 4.482 9.996 9.992z"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <span class="block text-white text-xs font-bold">Send via WhatsApp</span>
                                                    <span class="block text-[10px] text-slate-400 font-normal truncate">{{ $m->phone ?: 'No phone configured' }}</span>
                                                </div>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Edit -->
                                    <button wire:click="openEditModal({{ $m->id }})" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/10 text-slate-500 dark:text-slate-300 font-bold transition-all cursor-pointer active:scale-95" title="Edit Member">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    <!-- Delete -->
                                    <button wire:click="deleteMember({{ $m->id }})" wire:confirm="Are you sure you want to delete this virtual ID card?" class="p-2 rounded-xl hover:bg-rose-500/10 text-rose-500 font-bold transition-all cursor-pointer active:scale-95" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-900 dark:text-white">No Virtual ID Cards Found</h3>
                                    <p class="text-xs text-slate-500">Get started by adding a member manually or importing from an Excel/CSV spreadsheet.</p>
                                    <div class="pt-2 flex justify-center gap-2">
                                        <button wire:click="openAddModal" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md">
                                            + Add First Member
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-white/5">
                {{ $members->links() }}
            </div>
        @endif
    </div>

    <!-- ==================== MODAL 1: ADD / EDIT MEMBER ==================== -->
    @if($showMemberModal)
        @teleport('body')
        <div class="fixed inset-0 z-[9999] w-screen h-screen flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 sm:p-7 max-w-xl w-full shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto custom-scrollbar">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div class="space-y-0.5">
                        <h3 class="text-lg font-black text-white">
                            {{ $editingMemberId ? 'Edit Member Virtual ID' : 'Add New Member Virtual ID' }}
                        </h3>
                        <p class="text-xs text-slate-400">Enter member credentials to auto-generate their digital ID pass.</p>
                    </div>
                    <button wire:click="closeMemberModal" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveMember" class="space-y-4 text-xs">
                    
                    <!-- Photo Upload with Silhouette Live Preview -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-800/80 border border-white/10 text-white">
                        <div class="w-16 h-20 rounded-xl border-2 border-blue-500/40 bg-slate-900 overflow-hidden shrink-0 flex items-center justify-center shadow">
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                            @elseif($existing_photo_path)
                                <img src="{{ asset('storage/' . $existing_photo_path) }}" alt="Preview" class="w-full h-full object-cover">
                            @else
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                    <span class="text-[8px] font-bold">Silhouette</span>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1.5 flex-1">
                            <label class="block font-bold text-slate-200">Profile Photo</label>
                            <input type="file" wire:model="photo" accept="image/*" class="text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
                            <p class="text-[11px] text-slate-400">Leave blank to automatically use a corporate silhouette on the ID card.</p>
                        </div>
                    </div>

                    <!-- Member Full Name -->
                    <div>
                        <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Full Name *</label>
                        <input type="text" wire:model="full_name" required placeholder="e.g. Kwame Mensah" class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                        @error('full_name') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Designation Selection (Member / Executive) -->
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Designation *</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border {{ $designation === 'member' ? 'border-blue-500 bg-blue-500/15 text-white ring-1 ring-blue-500/40' : 'border-white/10 bg-slate-800/80 text-slate-300 hover:border-white/20' }} cursor-pointer transition-all">
                                <input type="radio" wire:model.live="designation" value="member" class="text-blue-600 focus:ring-blue-500 h-4 w-4">
                                <div>
                                    <span class="font-black text-xs block">👤 Member</span>
                                    <span class="text-[10px] text-slate-400">Regular member</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border {{ $designation === 'executive' ? 'border-amber-500 bg-amber-500/15 text-white ring-1 ring-amber-500/40' : 'border-white/10 bg-slate-800/80 text-slate-300 hover:border-white/20' }} cursor-pointer transition-all">
                                <input type="radio" wire:model.live="designation" value="executive" class="text-amber-500 focus:ring-amber-500 h-4 w-4">
                                <div>
                                    <span class="font-black text-xs block text-amber-400">⭐ Executive</span>
                                    <span class="text-[10px] text-slate-400">Leadership / Officer</span>
                                </div>
                            </label>
                        </div>
                        @error('designation') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Executive Exact Position Field (Only when Executive is selected) -->
                    @if($designation === 'executive')
                        <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-1.5 animate-fadeIn">
                            <label class="block font-bold text-amber-400 uppercase tracking-wider text-[11px]">Executive Position / Title *</label>
                            <input type="text" 
                                   wire:model="position" 
                                   placeholder="e.g. President, Vice President, General Secretary, PRO, Organizer..." 
                                   class="w-full bg-slate-800 border border-amber-500/30 rounded-xl px-4 py-2.5 text-white font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none placeholder-slate-500">
                            <p class="text-[10.5px] text-amber-300/70">Specify the official portfolio or executive role held by the member.</p>
                            @error('position') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- ID Number & Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Member ID Number</label>
                                <span class="text-[10px] text-blue-400 font-bold bg-blue-500/10 px-2 py-0.5 rounded-md border border-blue-500/20">
                                    🔒 Auto-Generated
                                </span>
                            </div>
                            <div class="relative">
                                <input type="text" 
                                       wire:model="member_id_number" 
                                       readonly 
                                       tabindex="-1"
                                       class="w-full bg-slate-800/60 border border-white/10 rounded-xl px-4 py-2.5 text-blue-300 font-mono font-bold focus:outline-none cursor-not-allowed select-all" 
                                       title="System Generated ID Number">
                                <div class="absolute right-3.5 top-3 text-slate-500 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                            </div>
                            @error('member_id_number') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Email (for Card Dispatch)</label>
                                @if($isDuplicateEmail)
                                    <span class="text-[10.5px] font-bold text-rose-400 flex items-center gap-1 animate-pulse">
                                        ⚠️ Already Registered
                                    </span>
                                @elseif($email && filter_var($email, FILTER_VALIDATE_EMAIL))
                                    <span class="text-[10.5px] font-bold text-emerald-400 flex items-center gap-1">
                                        ✓ Available
                                    </span>
                                @endif
                            </div>
                            <div class="relative">
                                <input type="email" 
                                       wire:model.live.debounce.300ms="email" 
                                       placeholder="member@example.com" 
                                       class="w-full bg-slate-800 border {{ $isDuplicateEmail ? 'border-rose-500 ring-2 ring-rose-500/30' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                                <div wire:loading wire:target="email" class="absolute right-3 top-3 text-slate-400 text-xs">
                                    <svg class="animate-spin h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                </div>
                            </div>
                            @if($duplicateEmailWarning)
                                <div class="mt-1.5 p-2.5 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-[11px] font-semibold flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>{{ $duplicateEmailWarning }}</span>
                                </div>
                            @endif
                            @error('email') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Phone Number & Institution/Faculty of Law -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Phone (for WhatsApp Dispatch)</label>
                                @if($isDuplicatePhone)
                                    <span class="text-[10.5px] font-bold text-rose-400 flex items-center gap-1 animate-pulse">
                                        ⚠️ Already Registered
                                    </span>
                                @elseif($phone && strlen(preg_replace('/[^0-9]/', '', $phone)) >= 6)
                                    <span class="text-[10.5px] font-bold text-emerald-400 flex items-center gap-1">
                                        ✓ Available
                                    </span>
                                @endif
                            </div>
                            <div class="relative">
                                <input type="text" 
                                       wire:model.live.debounce.300ms="phone" 
                                       placeholder="+233..." 
                                       class="w-full bg-slate-800 border {{ $isDuplicatePhone ? 'border-rose-500 ring-2 ring-rose-500/30' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                                <div wire:loading wire:target="phone" class="absolute right-3 top-3 text-slate-400 text-xs">
                                    <svg class="animate-spin h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                </div>
                            </div>
                            @if($duplicatePhoneWarning)
                                <div class="mt-1.5 p-2.5 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-[11px] font-semibold flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>{{ $duplicatePhoneWarning }}</span>
                                </div>
                            @endif
                            @error('phone') <span class="text-rose-400 text-[11px] block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Faculty of Law</label>
                                <button type="button" wire:click="openInstitutionsModal" class="text-orange-400 hover:text-orange-300 text-[10px] font-bold hover:underline cursor-pointer flex items-center gap-0.5">
                                    <span>+ Manage List</span>
                                </button>
                            </div>
                            <select wire:model="institution" class="w-full bg-slate-800 border border-white/10 rounded-xl px-3.5 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-semibold text-xs">
                                <option value="">-- Select Institution / Faculty --</option>
                                @foreach($institutionList as $inst)
                                    <option value="{{ $inst }}">{{ $inst }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Admission & Completion Years -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Admission Year</label>
                            <input type="text" wire:model="admission_year" placeholder="e.g. 2023" class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Completion Year</label>
                            <input type="text" wire:model="completion_year" placeholder="e.g. 2026" class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                        </div>
                    </div>

                    <!-- Dynamic Custom Fields (If Any) -->
                    @if(!empty($customFieldDefs))
                        <div class="pt-2 border-t border-white/10 space-y-3">
                            <span class="font-extrabold text-purple-400 block uppercase tracking-wider text-[11px]">
                                Custom Card Fields
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($customFieldDefs as $cf)
                                    <div>
                                        <label class="block font-bold text-slate-300 mb-1">{{ $cf['label'] }}</label>
                                        @if(($cf['type'] ?? '') === 'select' || !empty($cf['options']))
                                            <select wire:model="member_custom_fields.{{ $cf['key'] }}" class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-purple-500 focus:outline-none text-xs font-semibold">
                                                <option value="">-- Select {{ $cf['label'] }} --</option>
                                                @foreach($cf['options'] ?? ['Yes', 'No'] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="{{ $cf['type'] ?? 'text' }}" wire:model="member_custom_fields.{{ $cf['key'] }}" placeholder="Value for {{ $cf['label'] }}" class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:outline-none placeholder-slate-500">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Modal Actions -->
                    <div class="pt-4 flex justify-end gap-3 border-t border-white/10">
                        <button type="button" wire:click="closeMemberModal" class="px-5 py-2.5 rounded-xl border border-white/20 text-slate-300 font-semibold text-xs hover:bg-white/5 transition-all cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all cursor-pointer">
                            <span wire:loading.remove>{{ $editingMemberId ? 'Save Changes' : 'Generate Virtual ID Card' }}</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endteleport
    @endif

    <!-- ==================== MODAL 2: FIELDS & LOGO CUSTOMIZER ==================== -->
    @if($showFieldCustomizerModal)
        @teleport('body')
        <div class="fixed inset-0 z-[9999] w-screen h-screen flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 sm:p-7 max-w-xl w-full shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto custom-scrollbar">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div class="space-y-0.5">
                        <h3 class="text-lg font-black text-white">Customize ID Card Fields &amp; Logo</h3>
                        <p class="text-xs text-slate-400">Upload institution branding logo and configure standard &amp; custom fields.</p>
                    </div>
                    <button wire:click="$set('showFieldCustomizerModal', false)" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Organization / Association Workspace Name -->
                <div class="p-3.5 rounded-2xl bg-blue-500/10 border border-blue-500/20 space-y-1.5">
                    <label class="block font-bold text-blue-300 uppercase tracking-wider text-[11px] flex items-center justify-between">
                        <span>Organization / Association Name</span>
                        <span class="text-[10px] text-blue-400 font-medium">Top-left Brand &amp; ID Header</span>
                    </label>
                    <input type="text" 
                           wire:model="organization_name" 
                           placeholder="e.g. Federation of African Law Students (FALAS)" 
                           class="w-full bg-slate-800 border border-white/10 rounded-xl px-3.5 py-2 text-xs text-white font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                    <p class="text-[10px] text-slate-400">
                        This name updates the brand text shown in the top-left sidebar and across member ID cards.
                    </p>
                </div>

                <!-- 1. Institution Branding Logos (Main Logo & Association Logo) -->
                <div class="space-y-3">
                    <span class="font-extrabold text-purple-400 text-xs uppercase tracking-wider block">Institution &amp; Association Branding Logos</span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- 1. Main Logo -->
                        <div class="p-3.5 rounded-2xl bg-purple-500/10 border border-purple-500/20 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-purple-300 text-[11px] uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🏛️</span>
                                    <span>1. Main Logo</span>
                                </span>
                                @if($existing_main_logo_path)
                                    <button type="button" wire:click="removeMainLogo" class="text-rose-400 hover:underline text-[10px] font-bold">Remove</button>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl border border-purple-500/30 bg-slate-800 flex items-center justify-center p-1.5 overflow-hidden shrink-0">
                                    @if($main_logo)
                                        <img src="{{ $main_logo->temporaryUrl() }}" alt="Main Logo" class="w-full h-full object-contain">
                                    @elseif($existing_main_logo_path)
                                        <img src="{{ asset('storage/' . $existing_main_logo_path) }}" alt="Main Logo" class="w-full h-full object-contain">
                                    @else
                                        <svg class="w-7 h-7 text-purple-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    @endif
                                </div>
                                <div class="space-y-1 flex-1 min-w-0">
                                    <input type="file" wire:model="main_logo" accept="image/*" class="text-[11px] text-slate-300 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10.5px] file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-500 cursor-pointer w-full">
                                    <p class="text-[10px] text-slate-400">University / Institution Emblem</p>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Association Logo -->
                        <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-300 text-[11px] uppercase tracking-wider flex items-center gap-1.5">
                                    <span>⚖️</span>
                                    <span>2. Association Logo</span>
                                </span>
                                @if($existing_association_logo_path)
                                    <button type="button" wire:click="removeAssociationLogo" class="text-rose-400 hover:underline text-[10px] font-bold">Remove</button>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl border border-amber-500/30 bg-slate-800 flex items-center justify-center p-1.5 overflow-hidden shrink-0">
                                    @if($association_logo)
                                        <img src="{{ $association_logo->temporaryUrl() }}" alt="Association Logo" class="w-full h-full object-contain">
                                    @elseif($existing_association_logo_path)
                                        <img src="{{ asset('storage/' . $existing_association_logo_path) }}" alt="Association Logo" class="w-full h-full object-contain">
                                    @else
                                        <svg class="w-7 h-7 text-amber-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                    @endif
                                </div>
                                <div class="space-y-1 flex-1 min-w-0">
                                    <input type="file" wire:model="association_logo" accept="image/*" class="text-[11px] text-slate-300 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10.5px] file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-500 cursor-pointer w-full">
                                    <p class="text-[10px] text-slate-400">FALAS / Association Crest</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Default Fields Management -->
                <div class="space-y-2">
                    <span class="font-extrabold text-slate-300 text-xs uppercase tracking-wider block">Standard ID Fields</span>
                    <div class="space-y-1.5">
                        @foreach($defaultFieldDefs as $idx => $df)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-800 border border-white/10 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <input type="text" wire:model="defaultFieldDefs.{{ $idx }}.label" class="bg-transparent font-bold text-white border-b border-dashed border-slate-500 focus:outline-none text-xs">
                                    <span class="text-[10px] text-slate-400 font-mono">({{ $df['key'] }})</span>
                                </div>
                                <button type="button" wire:click="removeDefaultField({{ $idx }})" class="text-rose-400 hover:text-rose-500 p-1" title="Hide/Delete field">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Dynamic Custom Fields -->
                <div class="space-y-3 pt-2 border-t border-white/10">
                    <span class="font-extrabold text-purple-400 text-xs uppercase tracking-wider block">Add Dynamic Custom Field</span>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model="newFieldLabel" placeholder="e.g. Blood Group, Chapter, Alumni Status..." class="flex-1 bg-slate-800 border border-white/10 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <select wire:model.live="newFieldType" class="bg-slate-800 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-bold">
                                <option value="text">Text Input</option>
                                <option value="number">Number Input</option>
                                <option value="date">Date Picker</option>
                                <option value="yes_no">Yes / No Dropdown</option>
                                <option value="dropdown">Custom Dropdown (Select)</option>
                            </select>
                            <button type="button" wire:click="addCustomField" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow cursor-pointer shrink-0">
                                + Add
                            </button>
                        </div>

                        <!-- Dropdown Options input when Custom Dropdown is selected -->
                        @if($newFieldType === 'dropdown')
                            <div class="p-2.5 rounded-xl bg-purple-500/10 border border-purple-500/20 space-y-1">
                                <label class="block font-bold text-purple-300 text-[11px]">Dropdown Options (comma-separated):</label>
                                <input type="text" wire:model="newFieldOptions" placeholder="e.g. Option 1, Option 2, Option 3" class="w-full bg-slate-800 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        @endif
                    </div>

                    @if(!empty($customFieldDefs))
                        <div class="space-y-1.5 pt-1">
                            @foreach($customFieldDefs as $cIdx => $cf)
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-purple-500/10 border border-purple-500/20 text-xs">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                            <input type="text" wire:model="customFieldDefs.{{ $cIdx }}.label" class="bg-transparent font-bold text-purple-300 border-b border-dashed border-purple-400 focus:outline-none text-xs">
                                            <span class="px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 font-mono text-[10px] uppercase font-bold">{{ $cf['type'] ?? 'text' }}</span>
                                        </div>
                                        @if(!empty($cf['options']))
                                            <div class="text-[10px] text-slate-400 pl-4">
                                                Options: <span class="text-purple-300 font-mono">{{ implode(', ', $cf['options']) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" wire:click="removeCustomField({{ $cIdx }})" class="text-rose-400 hover:text-rose-500 p-1" title="Delete custom field">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-white/10">
                    <button type="button" wire:click="$set('showFieldCustomizerModal', false)" class="px-5 py-2.5 rounded-xl border border-white/20 text-slate-300 font-semibold text-xs hover:bg-white/5 transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveFieldDefinitions" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs shadow-lg shadow-purple-500/25 transition-all cursor-pointer">
                        Save Field Settings &amp; Logo
                    </button>
                </div>
            </div>
        </div>
        @endteleport
    @endif

    <!-- ==================== MODAL 3: EXCEL / CSV BATCH UPLOAD ==================== -->
    @if($showUploadModal)
        @php
            $expectedFields = $this->getExpectedImportFields();
        @endphp
        @teleport('body')
        <div class="fixed inset-0 z-[9999] w-screen h-screen flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 sm:p-7 max-w-2xl w-full shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto custom-scrollbar">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div class="space-y-0.5">
                        <h3 class="text-lg font-black text-white">Import Members from Spreadsheet</h3>
                        <p class="text-xs text-slate-400">Upload Excel (.xlsx, .xls) or CSV (.csv) containing your member records.</p>
                    </div>
                    <button wire:click="closeUploadModal" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Dropzone with Template Download Action -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-300 uppercase tracking-wider text-[11px]">Upload Spreadsheet</span>
                            <button type="button" wire:click="downloadSampleCsv" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 hover:underline cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Download Matching Sample (.CSV)</span>
                            </button>
                        </div>

                        <label class="border-2 border-dashed border-slate-700 hover:border-emerald-500 rounded-2xl p-6 flex flex-col items-center justify-center text-center cursor-pointer transition-all bg-white/[0.02] hover:bg-emerald-500/5 group">
                            <div class="p-3 rounded-2xl bg-emerald-500/10 text-emerald-400 mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-200">
                                Click or Drag Spreadsheet Here (.xlsx, .xls, .csv)
                            </span>
                            <p class="text-[11px] text-slate-400 mt-1">
                                System auto-detects columns and maps them to your active field settings.
                            </p>
                            <input type="file" wire:model="excel_file" accept=".xlsx,.xls,.csv,.txt" class="hidden">
                        </label>
                    </div>

                    <!-- Expected Spreadsheet Columns Requirements (Synchronized with Field Customizer) -->
                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-200 uppercase tracking-wider text-[11px]">
                                Expected Column Headers in Spreadsheet:
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">Matched automatically</span>
                        </div>

                        <div class="flex flex-wrap gap-1.5">
                            @foreach($expectedFields as $ef)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ ($ef['type'] ?? '') === 'custom' || ($ef['type'] ?? '') === 'select' ? 'bg-purple-500/15 border border-purple-500/30 text-purple-300' : 'bg-slate-700/60 text-slate-200 border border-white/10' }} text-[11px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full {{ ($ef['type'] ?? '') === 'custom' || ($ef['type'] ?? '') === 'select' ? 'bg-purple-400' : 'bg-blue-400' }}"></span>
                                    <span>{{ $ef['label'] }}</span>
                                    @if($ef['required'])
                                        <span class="text-rose-400 font-bold">*</span>
                                    @endif
                                    @if(($ef['type'] ?? '') === 'custom' || ($ef['type'] ?? '') === 'select')
                                        <span class="text-[9px] uppercase px-1 rounded bg-purple-500/20 text-purple-300">Custom</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <p class="text-[10.5px] text-slate-400 pt-1 border-t border-white/10">
                            💡 <strong>Uniform Auto-Generation:</strong> <em>Member ID Numbers</em>, <em>Branding Logos</em>, and <em>Cryptographic QR Tokens</em> are generated and applied uniformly by the system upon entry.
                        </p>
                    </div>

                    <!-- Parsed Rows Preview -->
                    @if($uploadedCount > 0)
                        <div class="space-y-2 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/25">
                            <div class="flex items-center justify-between font-bold text-emerald-400">
                                <span>✨ {{ $uploadedCount }} Member Record(s) Ready to Import</span>
                            </div>
                            <div class="max-h-48 overflow-y-auto space-y-1.5 font-mono text-[11px] bg-slate-900/50 p-2.5 rounded-xl border border-emerald-500/20 custom-scrollbar">
                                @foreach(array_slice($uploadPreview, 0, 5) as $uIdx => $uRow)
                                    <div class="p-2 rounded bg-white/5 space-y-1">
                                        <div class="flex items-center justify-between font-bold text-slate-200">
                                            <span>{{ $uIdx + 1 }}. {{ $uRow['full_name'] }}</span>
                                            <span class="text-slate-400 text-[10px]">{{ $uRow['institution'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-[10px] text-slate-400">
                                            <span>Email: {{ $uRow['email'] ?: '—' }}</span>
                                            <span>Phone: {{ $uRow['phone'] ?: '—' }}</span>
                                            <span>Adm: {{ $uRow['admission_year'] }} - {{ $uRow['completion_year'] }}</span>
                                        </div>
                                        @if(!empty($uRow['custom_fields']))
                                            <div class="flex flex-wrap gap-1 text-[9px] pt-1">
                                                @foreach($uRow['custom_fields'] as $ck => $cv)
                                                    <span class="px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 font-sans font-semibold">
                                                        {{ ucwords(str_replace('_', ' ', $ck)) }}: {{ $cv }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                @if($uploadedCount > 5)
                                    <div class="text-center text-[10px] text-slate-500 pt-1">+ {{ $uploadedCount - 5 }} more records ready to import...</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-white/10">
                    <button type="button" wire:click="closeUploadModal" class="px-5 py-2.5 rounded-xl border border-white/20 text-slate-300 font-semibold text-xs hover:bg-white/5 transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" 
                            wire:click="importUploadedMembers" 
                            @if($uploadedCount === 0) disabled @endif
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        Import {{ $uploadedCount }} Members
                    </button>
                </div>
            </div>
        </div>
        @endteleport
    @endif

    <!-- ==================== MODAL 4: LIVE CARD PREVIEW & SNAPSHOT ==================== -->
    @if($showCardPreviewModal && $previewCard)
        @teleport('body')
        <div class="fixed inset-0 z-[9999] w-screen h-screen flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl p-6 sm:p-7 max-w-2xl w-full shadow-2xl space-y-5 max-h-[95vh] overflow-y-auto custom-scrollbar">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/10 pb-3">
                    <div class="space-y-0.5">
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Virtual ID Card Pass</h3>
                        <p class="text-xs text-slate-500">{{ $previewCard->full_name }} ({{ $previewCard->member_id_number }})</p>
                    </div>
                    <button wire:click="closeCardPreview" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Digital Card Snapshot Canvas Target with Main Logo Green & White Theme + Pinch of Yellow -->
                <div id="admin-preview-card-canvas" class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-[#064e3b] via-[#033b2c] to-[#02241b] border-2 border-emerald-400/40 ring-1 ring-white/20 p-5 sm:p-6 space-y-5 text-white shadow-2xl">
                    
                    <!-- Background Institution Logo / Law Watermark -->
                    <div class="absolute inset-0 opacity-[0.06] pointer-events-none flex items-center justify-center overflow-hidden p-6 select-none">
                        @if($previewCard->main_logo_url)
                            <img src="{{ $previewCard->main_logo_url }}" crossorigin="anonymous" class="w-72 h-72 object-contain filter grayscale contrast-200 invert">
                        @else
                            <svg class="w-72 h-72 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 10h2v4h-2zm0 6h2v2h-2z"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Header: Dual Logos & Centered Organization Details -->
                    <div class="relative z-10 flex items-center justify-between gap-3 border-b border-white/20 pb-4">
                        <!-- 1. Main Logo (Left) -->
                        <div class="w-12 h-12 sm:w-14 sm:h-14 min-w-[48px] min-h-[48px] max-w-[56px] max-h-[56px] rounded-xl bg-white p-1 flex items-center justify-center shrink-0 shadow-md border-2 border-white/90 overflow-hidden">
                            @if($previewCard->main_logo_url)
                                <img src="{{ $previewCard->main_logo_url }}" crossorigin="anonymous" alt="Main Logo" class="max-w-full max-h-full object-contain" title="Main Logo">
                            @else
                                <div class="w-full h-full rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black text-base shrink-0 shadow-inner">
                                    🏛️
                                </div>
                            @endif
                        </div>

                        <!-- Center: Organization Title & Subtitle in Green & White with Pinch of Yellow -->
                        <div class="text-center flex-1 min-w-0 space-y-0.5 px-1">
                            @php
                                $rawOrgName = $previewCard->organization ? $previewCard->organization->name : 'Federation of African Law Students';
                                if (stripos($rawOrgName, 'IDENTITY CARD') !== false) {
                                    $orgTitle = trim(preg_replace('/[-–—]?\s*identity\s*card/i', '', $rawOrgName));
                                    $hasIdCardSuffix = true;
                                } else {
                                    $orgTitle = $rawOrgName;
                                    $hasIdCardSuffix = false;
                                }
                            @endphp
                            <div class="text-xs sm:text-sm font-black uppercase tracking-wider text-white font-sans leading-tight">
                                {{ $orgTitle }}
                            </div>
                            @if($hasIdCardSuffix)
                                <div class="text-[10px] sm:text-[11px] font-black uppercase tracking-widest text-yellow-300 font-sans leading-tight">
                                    IDENTITY CARD
                                </div>
                            @endif
                            <div class="text-[8.5px] sm:text-[9.5px] font-extrabold uppercase tracking-[0.18em] text-emerald-100/80 font-sans pt-0.5">
                                Official Student Pass
                            </div>
                        </div>

                        <!-- 2. Association Logo (Right) -->
                        <div class="w-12 h-12 sm:w-14 sm:h-14 min-w-[48px] min-h-[48px] max-w-[56px] max-h-[56px] rounded-xl bg-white p-1 flex items-center justify-center shrink-0 shadow-md border-2 border-white/90 overflow-hidden">
                            @if($previewCard->association_logo_url)
                                <img src="{{ $previewCard->association_logo_url }}" crossorigin="anonymous" alt="Association Logo" class="max-w-full max-h-full object-contain" title="Association Logo">
                            @else
                                <div class="w-full h-full rounded-lg bg-emerald-700 flex items-center justify-center text-white font-black text-xs">
                                    FALAS
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Main Body: Framed Portrait & Credentials Spotlight -->
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-5 relative z-10 py-1">
                        <!-- Framed Photo or Silhouette in White-Emerald-Gold Gradient -->
                        <div class="relative shrink-0">
                            <div class="w-28 h-36 sm:w-32 sm:h-40 rounded-2xl p-1 bg-gradient-to-b from-white via-emerald-300 to-yellow-400/80 shadow-xl shadow-black/30">
                                <div class="w-full h-full rounded-[14px] overflow-hidden bg-slate-900 flex items-center justify-center relative group">
                                    @if($previewCard->photo_url)
                                        <img src="{{ $previewCard->photo_url }}" crossorigin="anonymous" alt="{{ $previewCard->full_name }}" class="w-full h-full object-cover">
                                        <!-- Interactive Hover to Download Photo directly -->
                                        <button type="button" 
                                                wire:click="downloadPhoto({{ $previewCard->id }})" 
                                                title="Click to Download Profile Photo"
                                                class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white transition-all cursor-pointer">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            <span class="text-[9px] font-bold mt-1">Download</span>
                                        </button>
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-b from-slate-800 to-slate-950 text-slate-500">
                                            <svg class="w-14 h-14 text-slate-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                            <span class="text-[8px] font-bold uppercase tracking-wider text-slate-500 mt-1">Photo on File</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="absolute -bottom-1 -right-1 p-1 rounded-full bg-emerald-500 border-2 border-white text-white shadow">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>

                        <!-- Cardholder Details -->
                        <div class="flex-1 text-center sm:text-left space-y-2.5 w-full">
                            <div class="space-y-1.5">
                                <div>
                                    <span class="text-[9.5px] font-extrabold uppercase tracking-widest text-emerald-200/90 block">Cardholder Name</span>
                                    <h4 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-snug">{{ $previewCard->full_name }}</h4>
                                </div>
                                
                                <!-- Member ID Number Badge under Name in Crisp White/Green -->
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-white/15 border border-white/30 text-white font-mono text-[11px] font-bold shadow-sm tracking-wider">
                                        {{ $previewCard->member_id_number }}
                                    </span>
                                </div>

                                <!-- Institution under Name -->
                                @if($previewCard->institution)
                                    <div class="text-[11px] sm:text-xs text-white font-medium leading-snug pt-0.5">
                                        {{ $previewCard->institution }}
                                    </div>
                                @endif

                            </div>

                            <!-- 3-Pill Tier: Admission | Country Director / Executive Role | Completion -->
                            <div class="flex items-stretch gap-1.5 pt-2">
                                <!-- 1. Admission -->
                                <div class="bg-white/10 border border-white/20 rounded-xl py-1.5 px-2.5 text-center shrink-0 min-w-[65px] sm:min-w-[75px] flex flex-col justify-center">
                                    <span class="text-[7.5px] sm:text-[8px] text-emerald-200 block font-extrabold uppercase tracking-wider leading-none">Admission</span>
                                    <span class="font-bold text-white font-mono text-[10.5px] sm:text-[11px] block mt-1 leading-tight">{{ $previewCard->admission_year ?: 'N/A' }}</span>
                                </div>

                                <!-- 2. Role / Designation (In Between - Pinch of Yellow Accent) -->
                                <div class="flex-1 rounded-xl py-1.5 px-2 text-center flex items-center justify-center shadow-sm min-w-0 {{ $previewCard->isExecutive() ? 'bg-gradient-to-b from-yellow-500/20 via-yellow-500/10 to-transparent border border-yellow-400/60 text-yellow-300 shadow-yellow-500/10' : 'bg-white/15 border border-white/30 text-white' }}">
                                    <span class="font-black text-[9px] sm:text-[10px] leading-tight tracking-tight whitespace-nowrap {{ $previewCard->isExecutive() ? 'text-yellow-300' : 'text-white' }}">
                                        {{ $previewCard->isExecutive() ? '⭐ ' . (!empty($previewCard->position) ? strtoupper($previewCard->position) : 'EXECUTIVE') : '👤 MEMBER' }}
                                    </span>
                                </div>

                                <!-- 3. Completion -->
                                <div class="bg-white/10 border border-white/20 rounded-xl py-1.5 px-2.5 text-center shrink-0 min-w-[65px] sm:min-w-[75px] flex flex-col justify-center">
                                    <span class="text-[7.5px] sm:text-[8px] text-emerald-200 block font-extrabold uppercase tracking-wider leading-none">Completion</span>
                                    <span class="font-bold text-white font-mono text-[10.5px] sm:text-[11px] block mt-1 leading-tight">{{ $previewCard->completion_year ?: 'Present' }}</span>
                                </div>
                            </div>

                            @if(!empty($previewCard->custom_fields))
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    @foreach($previewCard->custom_fields as $cfKey => $cfVal)
                                        @if(!empty($cfVal))
                                            <div class="bg-white/10 border border-white/20 rounded-xl p-2 text-center sm:text-left">
                                                <span class="text-[9px] text-emerald-200 block font-bold uppercase tracking-wider truncate">{{ ucwords(str_replace('_', ' ', $cfKey)) }}</span>
                                                <span class="font-bold text-white truncate block text-[11px]">{{ $cfVal }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer Bar with QR Code -->
                    <div class="pt-3.5 border-t border-white/20 flex items-center justify-between gap-3 relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="p-1 rounded-xl bg-white shadow shrink-0 flex items-center justify-center">
                                <img src="{{ $previewCard->qr_code_url }}" crossorigin="anonymous" alt="QR" class="w-12 h-12 sm:w-14 sm:h-14 rounded block">
                            </div>
                            <div class="space-y-0.5 text-left">
                                <div class="text-[10.5px] font-black uppercase tracking-wide text-white flex items-center gap-1.5 whitespace-nowrap">
                                    <span class="text-emerald-400 font-bold">✓</span>
                                    <span>Digitally Verified</span>
                                </div>
                                <div class="text-[9.5px] text-emerald-100 font-mono tracking-tight whitespace-nowrap">Token: {{ $previewCard->qr_token }}</div>
                                <div class="text-[8.5px] text-emerald-200/80 whitespace-nowrap">Scan code with camera to verify</div>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider whitespace-nowrap {{ $previewCard->status === 'active' ? 'bg-white/20 text-white border border-white/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                                <span class="w-2 h-2 rounded-full shrink-0 {{ $previewCard->status === 'active' ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400' }}"></span>
                                <span class="whitespace-nowrap">{{ strtoupper($previewCard->status) }} ID</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="space-y-2.5 pt-2 text-xs">
                    <!-- Primary Action: Download Full Student ID Card Pass -->
                    <button type="button" 
                            id="admin-download-card-btn" 
                            onclick="downloadAdminCardImage()" 
                            class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Save / Download Student ID Card (PNG)</span>
                    </button>

                    <!-- Secondary Dispatch & Photo Actions -->
                    <div class="grid grid-cols-1 {{ ($previewCard->photo_path || $previewCard->photo_url) ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }} gap-2">
                        @if($previewCard->photo_path || $previewCard->photo_url)
                            <button type="button" 
                                    wire:click="downloadPhoto({{ $previewCard->id }})" 
                                    class="py-2.5 px-3 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-500 dark:text-amber-400 border border-amber-500/20 font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer active:scale-95" 
                                    title="Download Profile Picture only">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Profile Photo</span>
                            </button>
                        @endif

                        <button type="button" wire:click="sendCardEmail({{ $previewCard->id }})" class="py-2.5 px-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold flex items-center justify-center gap-1.5 shadow transition-all cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>Send Email</span>
                        </button>
                        <button type="button" wire:click="sendCardWhatsApp({{ $previewCard->id }})" class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold flex items-center justify-center gap-1.5 shadow transition-all cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.97.581 1.936.945 2.795.945 3.179 0 5.766-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm10.009 5.766c-.002 5.51-4.484 9.991-9.996 9.991-1.748 0-3.377-.45-4.814-1.242L2 22l1.523-5.568C2.693 14.972 2.21 13.35 2.21 11.938c.002-5.51 4.484-9.992 9.997-9.992 5.513 0 9.995 4.482 9.996 9.992z"/></svg>
                            <span>Send WhatsApp</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========    <!-- ==================== MODAL: SHARE REGISTRATION LINK ==================== -->
    @if($showShareLinkModal)
        @php
            $shareUrl = $this->getShareableApplicationUrl();
            $waShareText = "Hello! Please click the link below to register and receive your official Digital Virtual ID Card:\n\n" . $shareUrl;
            $waShareUrl = "https://api.whatsapp.com/send?text=" . rawurlencode($waShareText);
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($shareUrl);
        @endphp
        @teleport('body')
        <div class="fixed inset-0 z-[9999] w-screen h-screen min-h-screen flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto custom-scrollbar">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div class="space-y-0.5">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-black uppercase tracking-wider border border-amber-500/20">
                            Public Registration Link
                        </div>
                        <h3 class="text-lg font-black text-white">Share Member Registration Link</h3>
                    </div>
                    <button wire:click="closeShareLinkModal" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <p class="text-slate-300">
                        Share this public registration link with your members or students. Once they submit their details, their record is added directly to your database and their official Virtual ID Card is instantly generated.
                    </p>

                    <!-- Copyable Link Input with Copy & Open Buttons -->
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Direct Registration URL</label>
                        <div class="flex items-center gap-2">
                            <input id="shareable-app-url-input" type="text" readonly value="{{ $shareUrl }}" class="flex-1 bg-slate-800 border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-blue-400 font-mono font-bold focus:outline-none select-all cursor-pointer" onclick="this.select()">
                            
                            <button type="button" 
                                    onclick="copyShareUrl()" 
                                    id="copy-share-url-btn"
                                    class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow transition-all cursor-pointer shrink-0 active:scale-95 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                <span>Copy</span>
                            </button>

                            <a href="{{ $shareUrl }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-slate-200 font-bold text-xs shadow transition-all cursor-pointer shrink-0 flex items-center gap-1" title="Open registration form in new tab">
                                <span>Open</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Direct WhatsApp Share Button -->
                    <div>
                        <a href="{{ $waShareUrl }}" target="_blank" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.97.581 1.936.945 2.795.945 3.179 0 5.766-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm10.009 5.766c-.002 5.51-4.484 9.991-9.996 9.991-1.748 0-3.377-.45-4.814-1.242L2 22l1.523-5.568C2.693 14.972 2.21 13.35 2.21 11.938c.002-5.51 4.484-9.992 9.997-9.992 5.513 0 9.995 4.482 9.996 9.992z"/></svg>
                            <span>Share Registration Link on WhatsApp</span>
                        </a>
                    </div>

                    <!-- QR Code to Scan & Apply on Mobile -->
                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 flex items-center gap-4">
                        <div class="p-2 rounded-xl bg-white shadow shrink-0">
                            <img src="{{ $qrUrl }}" alt="QR Application" class="w-20 h-20 rounded-lg">
                        </div>
                        <div class="space-y-1">
                            <span class="font-extrabold text-white block">Scan to Apply on Mobile</span>
                            <p class="text-[11px] text-slate-400">Project or print this QR code at admission booths, lecture halls, or orientation events.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-3 flex justify-end border-t border-white/10">
                    <button type="button" wire:click="closeShareLinkModal" class="px-5 py-2.5 rounded-xl border border-white/20 text-slate-300 font-semibold text-xs hover:bg-white/5 transition-all cursor-pointer">
                        Done
                    </button>
                </div>
            </div>
        </div>
        @endteleport
    @endif

    <!-- ==================== MODAL 5: MANAGE INSTITUTIONS ==================== -->
    @if($showInstitutionsModal)
        @teleport('body')
        <div class="fixed inset-0 z-[9999] w-screen h-screen min-h-screen flex items-center justify-center p-3 sm:p-6 lg:p-8 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl w-full max-w-5xl shadow-2xl p-5 sm:p-7 space-y-4 max-h-[92vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-white/10 pb-3.5 shrink-0">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-purple-500/10 text-purple-400 text-[10px] font-black uppercase tracking-wider border border-purple-500/20">
                            Institutions &amp; Faculties Directory
                        </div>
                        <h3 class="text-xl font-black text-white">🏛️ Institutions &amp; Faculties</h3>
                    </div>
                    <button wire:click="closeInstitutionsModal" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Tabs Switcher -->
                <div class="flex items-center gap-2 p-1 rounded-2xl bg-slate-800/80 border border-white/10 shrink-0">
                    <button type="button" 
                            wire:click="$set('institutionModalTab', 'active')" 
                            class="flex-1 py-2 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-2 {{ $institutionModalTab === 'active' ? 'bg-purple-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <span>🏛️ Active Breakdown</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $institutionModalTab === 'active' ? 'bg-white/20 text-white' : 'bg-slate-700 text-slate-300' }}">
                            {{ count($institutions) }}
                        </span>
                    </button>
                    <button type="button" 
                            wire:click="$set('institutionModalTab', 'manage')" 
                            class="flex-1 py-2 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-2 {{ $institutionModalTab === 'manage' ? 'bg-orange-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <span>⚙️ Configure Dropdown List</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $institutionModalTab === 'manage' ? 'bg-white/20 text-white' : 'bg-slate-700 text-slate-300' }}">
                            {{ count($institutionList) }}
                        </span>
                    </button>
                </div>

                <!-- Scrollable Body Content -->
                <div class="flex-1 overflow-y-auto pr-1 custom-scrollbar space-y-4">

                <!-- ════════ TAB 1: ACTIVE INSTITUTIONS BREAKDOWN ════════ -->
                @if($institutionModalTab === 'active')
                    <div class="space-y-4 animate-fadeIn">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                            <p class="text-xs text-slate-400">
                                Real-time breakdown of registered members and leadership across institutions.
                            </p>
                            @if(!empty($institutionFilter))
                                <button type="button" 
                                        wire:click="filterByInstitution('')" 
                                        class="text-xs font-bold text-rose-400 hover:underline flex items-center gap-1 cursor-pointer">
                                    <span>✕ Clear Current Filter ({{ $institutionFilter }})</span>
                                </button>
                            @endif
                        </div>

                        <!-- Search Filter -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" 
                                   wire:model.live.debounce.200ms="institutionSearch" 
                                   placeholder="Filter institutions..." 
                                   class="w-full pl-10 pr-4 py-2 bg-slate-800 border border-white/10 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>

                        <!-- Active Institutions Cards Grid / List -->
                        <div class="space-y-2.5 max-h-[60vh] overflow-y-auto pr-1 custom-scrollbar">
                            @php
                                $filteredBreakdown = $institutionBreakdown->filter(function($item) {
                                    if (empty($this->institutionSearch)) return true;
                                    return stripos($item->institution, $this->institutionSearch) !== false;
                                });
                            @endphp

                            @forelse($filteredBreakdown as $ib)
                                <div class="rounded-2xl bg-slate-800/90 border border-white/10 hover:border-purple-500/40 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 transition-all shadow-sm">
                                    <div class="space-y-1 min-w-0 flex-1">
                                        <div class="font-bold text-white text-xs flex items-center gap-2">
                                            <span class="text-purple-400 shrink-0 text-sm">🏛️</span>
                                            <span class="truncate">{{ $ib->institution }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] pt-0.5">
                                            <span class="px-2.5 py-0.5 rounded-full bg-blue-500/15 text-blue-300 font-bold border border-blue-500/20">
                                                👥 {{ $ib->member_count }} {{ Str::plural('Member', $ib->member_count) }}
                                            </span>
                                            @if($ib->executive_count > 0)
                                                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-300 font-bold border border-amber-500/20">
                                                    ⭐ {{ $ib->executive_count }} {{ Str::plural('Executive', $ib->executive_count) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" 
                                                wire:click="openInstitutionMembersPopup('{{ addslashes($ib->institution) }}')" 
                                                class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-black transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-md shadow-purple-500/20 active:scale-95">
                                            <span>Preview Members</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-xs bg-slate-800/40 rounded-2xl border border-white/5 space-y-1">
                                    <p class="font-bold text-slate-300">No matching active institutions found.</p>
                                    <p class="text-[11px]">Switch to the 'Configure Dropdown List' tab to see all {{ count($institutionList) }} available faculties.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                <!-- ════════ TAB 2: CONFIGURE DROPDOWN LIST ════════ -->
                @else
                    <div class="space-y-4 animate-fadeIn">
                        <p class="text-xs text-slate-400">
                            These institutions populate the <strong>Institution/Faculty of Law</strong> dropdown for manual entry, spreadsheet imports, and public self-registration forms.
                        </p>

                        <!-- Add Single Institution -->
                        <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 space-y-2">
                            <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Add Single Institution / Faculty</label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       wire:model="newInstitutionName" 
                                       wire:keydown.enter="addInstitution"
                                       placeholder="e.g. University of Ghana, School of Law" 
                                       class="flex-1 bg-slate-800 border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:ring-2 focus:ring-orange-500 focus:outline-none placeholder-slate-500">
                                <button type="button" 
                                        wire:click="addInstitution" 
                                        class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer">
                                    Add
                                </button>
                            </div>
                        </div>

                        <!-- Bulk Add Textarea -->
                        <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 space-y-2">
                            <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Paste Bulk Names (One Per Line)</label>
                            <textarea wire:model="bulkInstitutionsText" 
                                      rows="3" 
                                      placeholder="University of Ghana, School of Law&#10;KNUST Faculty of Law&#10;GIMPA Faculty of Law"
                                      class="w-full bg-slate-800 border border-white/10 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-orange-500 focus:outline-none placeholder-slate-500 font-mono"></textarea>
                            <div class="flex justify-end">
                                <button type="button" 
                                        wire:click="addBulkInstitutions" 
                                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs border border-white/10 transition-all cursor-pointer">
                                    Process Bulk Paste
                                </button>
                            </div>
                        </div>

                        <!-- Restore Defaults & List Count Toolbar -->
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs font-bold text-slate-300">Available List ({{ count($institutionList) }})</span>
                            <button type="button" 
                                    wire:click="restoreDefaultInstitutions" 
                                    wire:confirm="Reset the list back to the 19 standard Ghanaian law faculties?"
                                    class="text-[11px] font-bold text-orange-400 hover:underline cursor-pointer">
                                ↺ Restore 19 Defaults
                            </button>
                        </div>

                        <!-- Current Configured Institutions List -->
                        <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1 custom-scrollbar">
                            @forelse($institutionList as $idx => $instName)
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-800/50 border border-white/5 text-xs hover:border-white/15 transition-all">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-500 font-mono text-[10px]">{{ $idx + 1 }}.</span>
                                        <span class="font-semibold text-slate-200">{{ $instName }}</span>
                                    </div>
                                    <button type="button" 
                                            wire:click="removeInstitution({{ $idx }})" 
                                            class="p-1 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer"
                                            title="Remove from list">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">No institutions configured. Click 'Restore 19 Defaults' above.</div>
                            @endforelse
                        </div>
                    </div>
                @endif
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 flex justify-end border-t border-white/10 shrink-0">
                    <button type="button" wire:click="closeInstitutionsModal" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-white/10 transition-all cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
        @endteleport
    @endif

    <!-- ==================== MODAL 6: PREVIEW INSTITUTION MEMBERS POPUP ==================== -->
    @if($previewInstitutionName)
        @php
            $popupMembers = ($institutionMembers[$previewInstitutionName] ?? collect([]))->filter(function($m) {
                if (empty($this->institutionMemberSearch)) return true;
                $s = strtolower($this->institutionMemberSearch);
                return str_contains(strtolower($m->full_name), $s)
                    || str_contains(strtolower($m->member_id_number ?? ''), $s)
                    || str_contains(strtolower($m->email ?? ''), $s)
                    || str_contains(strtolower($m->phone ?? ''), $s)
                    || str_contains(strtolower($m->position ?? ''), $s);
            });
            $totalCount = ($institutionMembers[$previewInstitutionName] ?? collect([]))->count();
            $execCount = ($institutionMembers[$previewInstitutionName] ?? collect([]))->where('designation', 'executive')->count();
        @endphp
        @teleport('body')
        <div class="fixed inset-0 z-[10000] w-screen h-screen min-h-screen flex items-center justify-center p-3 sm:p-6 lg:p-8 bg-slate-950/90 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl w-full max-w-4xl shadow-2xl p-5 sm:p-7 space-y-4 max-h-[92vh] flex flex-col">
                
                <!-- Popup Header -->
                <div class="flex items-center justify-between border-b border-white/10 pb-3.5 shrink-0">
                    <div class="space-y-1 min-w-0 flex-1 pr-4">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-purple-500/10 text-purple-400 text-[10px] font-black uppercase tracking-wider border border-purple-500/20">
                            Faculty Members Directory
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-white truncate flex items-center gap-2">
                            <span>🏛️</span>
                            <span class="truncate">{{ $previewInstitutionName }}</span>
                        </h3>
                        <div class="flex items-center gap-2 text-[11px] pt-0.5">
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-500/15 text-blue-300 font-bold border border-blue-500/20">
                                👥 {{ $totalCount }} {{ Str::plural('Member', $totalCount) }}
                            </span>
                            @if($execCount > 0)
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-300 font-bold border border-amber-500/20">
                                    ⭐ {{ $execCount }} {{ Str::plural('Executive', $execCount) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <button wire:click="closeInstitutionMembersPopup" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition-colors cursor-pointer shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Search and Action Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" 
                               wire:model.live.debounce.150ms="institutionMemberSearch" 
                               placeholder="Search members by name, ID number, email, role..." 
                               class="w-full pl-10 pr-4 py-2 bg-slate-800 border border-white/10 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <button type="button" 
                            wire:click="filterByInstitution('{{ addslashes($previewInstitutionName) }}')" 
                            class="px-3.5 py-2 rounded-xl bg-purple-600/20 hover:bg-purple-600 text-purple-300 hover:text-white border border-purple-500/30 text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shrink-0">
                        <span>Open in Main Table →</span>
                    </button>
                </div>

                <!-- Members Cards List -->
                <div class="flex-1 overflow-y-auto pr-1.5 custom-scrollbar space-y-2.5">
                    @forelse($popupMembers as $m)
                        <div class="p-3.5 rounded-2xl bg-slate-800/90 border border-white/5 hover:border-purple-500/30 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                            
                            <!-- Member Info -->
                            <div class="flex items-center gap-3.5 min-w-0 flex-1">
                                <!-- Avatar Photo -->
                                <div class="w-12 h-12 rounded-2xl bg-slate-700 border border-white/10 overflow-hidden shrink-0 flex items-center justify-center shadow-md">
                                    @if($m->photo_url)
                                        <img src="{{ $m->photo_url }}" alt="{{ $m->full_name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="font-black text-sm text-slate-200">
                                            {{ strtoupper(mb_substr($m->full_name, 0, 2)) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="space-y-1 min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-black text-white text-sm truncate">{{ $m->full_name }}</span>
                                        <span class="font-mono text-[10.5px] font-bold px-2 py-0.5 rounded-md bg-slate-900/80 text-emerald-400 border border-emerald-500/20">
                                            {{ $m->member_id_number }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap text-xs">
                                        <!-- Designation Badge -->
                                        @if(strtolower($m->designation) === 'executive')
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold text-[10.5px]">
                                                ⭐ {{ $m->position ? "Executive • {$m->position}" : 'Executive' }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full bg-blue-500/15 text-blue-300 border border-blue-500/25 font-semibold text-[10.5px]">
                                                👤 Member
                                            </span>
                                        @endif

                                        <!-- Admission Year -->
                                        @if($m->admission_year)
                                            <span class="text-slate-400 text-xs">
                                                Class of {{ $m->admission_year }}
                                            </span>
                                        @endif

                                        <!-- Contact details -->
                                        @if($m->phone)
                                            <span class="text-slate-400 text-xs flex items-center gap-1">
                                                <span>📱</span> {{ $m->phone }}
                                            </span>
                                        @endif
                                        @if($m->email)
                                            <span class="text-slate-400 text-xs flex items-center gap-1">
                                                <span>✉️</span> {{ $m->email }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                <button type="button" 
                                        wire:click="openCardPreview({{ $m->id }})" 
                                        class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-md shadow-indigo-500/20 active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>View ID</span>
                                </button>
                                
                                <button type="button" 
                                        wire:click="openEditModal({{ $m->id }})" 
                                        class="p-2 rounded-xl bg-slate-700/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-white/10 transition-colors cursor-pointer"
                                        title="Edit Member Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs bg-slate-800/40 rounded-2xl border border-white/5 space-y-1">
                            <p class="font-bold text-slate-300">No members found matching your search.</p>
                            <p class="text-[11px]">Try searching with a different name or keyword.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Popup Footer -->
                <div class="pt-3 flex items-center justify-between border-t border-white/10 shrink-0">
                    <button type="button" 
                            wire:click="closeInstitutionMembersPopup" 
                            class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-white/10 transition-all cursor-pointer flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Back to Directory</span>
                    </button>
                    <button type="button" 
                            wire:click="closeInstitutionMembersPopup" 
                            class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-white/10 transition-all cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
        @endteleport
    @endif

    <!-- ==================== BULK ID CARD DOWNLOAD PROGRESS MODAL & HIDDEN STAGE ==================== -->
    <div id="bulk-download-progress-modal" class="fixed inset-0 z-[99999] hidden items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-5 text-center">
            <div class="w-14 h-14 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center mx-auto text-2xl shadow-inner">
                <svg class="w-7 h-7 animate-bounce text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </div>
            
            <div class="space-y-1">
                <h3 class="text-base font-black text-slate-900 dark:text-white">Generating Student ID Cards (.ZIP)</h3>
                <p id="bulk-progress-member" class="text-xs text-teal-400 font-semibold truncate px-2">Preparing generation queue...</p>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200 dark:border-white/10">
                    <div id="bulk-progress-bar" class="bg-gradient-to-r from-teal-500 via-emerald-500 to-teal-400 h-full rounded-full transition-all duration-150" style="width: 0%;"></div>
                </div>
                <div class="flex justify-between text-[11px] font-bold text-slate-400">
                    <span id="bulk-progress-text">0% Completed</span>
                    <span id="bulk-progress-count">0 / 0</span>
                </div>
            </div>

            <button type="button" onclick="cancelBulkDownload()" class="w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-all cursor-pointer">
                Cancel
            </button>
        </div>
    </div>

    <!-- Hidden Offscreen Render Stage for Bulk Processing -->
    <div style="position: fixed; top: 0; left: 0; width: 620px; z-index: 99998; pointer-events: none; opacity: 0.01;" aria-hidden="true">
        <div id="bulk-card-stage" class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-[#064e3b] via-[#033b2c] to-[#02241b] border-2 border-emerald-400/40 p-6 space-y-5 text-white shadow-2xl" style="width: 620px;">
            
            <!-- Header: Dual Logos & Centered Org Details -->
            <div class="relative z-10 flex items-center justify-between gap-3 border-b border-white/20 pb-4">
                <div class="w-14 h-14 min-w-[56px] min-h-[56px] max-w-[56px] max-h-[56px] rounded-xl bg-white p-1 flex items-center justify-center shrink-0 shadow-md border-2 border-white/90 overflow-hidden">
                    <img id="b-card-main-logo" src="" crossorigin="anonymous" alt="Main Logo" class="max-w-full max-h-full object-contain">
                </div>
                <div class="text-center flex-1 min-w-0 space-y-0.5 px-1">
                    <div id="b-card-org-title" class="text-sm font-black uppercase tracking-wider text-white font-sans leading-tight"></div>
                    <div id="b-card-id-suffix" class="text-[11px] font-black uppercase tracking-widest text-yellow-300 font-sans leading-tight">IDENTITY CARD</div>
                    <div class="text-[9.5px] font-extrabold uppercase tracking-[0.18em] text-emerald-100/80 font-sans pt-0.5">Official Student Pass</div>
                </div>
                <div class="w-14 h-14 min-w-[56px] min-h-[56px] max-w-[56px] max-h-[56px] rounded-xl bg-white p-1 flex items-center justify-center shrink-0 shadow-md border-2 border-white/90 overflow-hidden">
                    <img id="b-card-assoc-logo" src="" crossorigin="anonymous" alt="Association Logo" class="max-w-full max-h-full object-contain">
                </div>
            </div>

            <!-- Main Body: Framed Portrait & Credentials -->
            <div class="flex items-center gap-5 relative z-10 py-1">
                <div class="relative shrink-0">
                    <div class="w-32 h-40 rounded-2xl p-1 bg-gradient-to-b from-white via-emerald-300 to-yellow-400/80 shadow-xl">
                        <div class="w-full h-full rounded-[14px] overflow-hidden bg-slate-900 flex items-center justify-center">
                            <img id="b-card-photo" src="" crossorigin="anonymous" alt="Photo" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div class="absolute -bottom-1 -right-1 p-1 rounded-full bg-emerald-500 border-2 border-white text-white shadow">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>

                <div class="flex-1 text-left space-y-2.5 w-full min-w-0">
                    <div class="space-y-1.5">
                        <div>
                            <span class="text-[9.5px] font-extrabold uppercase tracking-widest text-emerald-200/90 block">Cardholder Name</span>
                            <h4 id="b-card-name" class="text-2xl font-black text-white tracking-tight leading-snug truncate"></h4>
                        </div>
                        <div>
                            <span id="b-card-member-id" class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-white/15 border border-white/30 text-white font-mono text-[11px] font-bold shadow-sm tracking-wider"></span>
                        </div>
                        <div id="b-card-institution" class="text-xs text-white font-medium leading-snug pt-0.5 truncate"></div>
                    </div>

                    <div class="flex items-stretch gap-1.5 pt-2">
                        <div class="bg-white/10 border border-white/20 rounded-xl py-1.5 px-3 text-center shrink-0 min-w-[75px] flex flex-col justify-center">
                            <span class="text-[8px] text-emerald-200 block font-extrabold uppercase tracking-wider leading-none">Admission</span>
                            <span id="b-card-admission" class="font-bold text-white font-mono text-[11px] block mt-1 leading-tight"></span>
                        </div>
                        <div id="b-card-role-pill" class="flex-1 rounded-xl py-1.5 px-2 text-center flex items-center justify-center shadow-sm min-w-0">
                            <span id="b-card-role-text" class="font-black text-[10px] leading-tight tracking-tight whitespace-nowrap"></span>
                        </div>
                        <div class="bg-white/10 border border-white/20 rounded-xl py-1.5 px-3 text-center shrink-0 min-w-[75px] flex flex-col justify-center">
                            <span class="text-[8px] text-emerald-200 block font-extrabold uppercase tracking-wider leading-none">Completion</span>
                            <span id="b-card-completion" class="font-bold text-white font-mono text-[11px] block mt-1 leading-tight"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bar with QR Code -->
            <div class="pt-3.5 border-t border-white/20 flex items-center justify-between gap-3 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="p-1 rounded-xl bg-white shadow shrink-0 flex items-center justify-center">
                        <img id="b-card-qr" src="" crossorigin="anonymous" alt="QR" class="w-14 h-14 rounded block">
                    </div>
                    <div class="space-y-0.5 text-left">
                        <div class="text-[10.5px] font-black uppercase tracking-wide text-white flex items-center gap-1.5 whitespace-nowrap">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Digitally Verified</span>
                        </div>
                        <div id="b-card-token" class="text-[9.5px] text-emerald-100 font-mono tracking-tight whitespace-nowrap"></div>
                        <div class="text-[8.5px] text-emerald-200/80 whitespace-nowrap">Scan code with camera to verify</div>
                    </div>
                </div>
                <div class="shrink-0 flex items-center">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider whitespace-nowrap bg-white/20 text-white border border-white/30">
                        <span class="w-2 h-2 rounded-full shrink-0 bg-emerald-400 animate-pulse"></span>
                        <span id="b-card-status-text" class="whitespace-nowrap">ACTIVE ID</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <script>
            function loadSnapshotLibraries(callback) {
                if (window.html2canvas && window.JSZip && window.saveAs) {
                    callback();
                    return;
                }
                let pending = 0;
                const checkDone = () => {
                    pending--;
                    if (pending <= 0) callback();
                };

                if (!window.html2canvas) {
                    pending++;
                    const s = document.createElement('script');
                    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                    s.onload = checkDone;
                    s.onerror = checkDone;
                    document.head.appendChild(s);
                }
                if (!window.JSZip) {
                    pending++;
                    const s = document.createElement('script');
                    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
                    s.onload = checkDone;
                    s.onerror = checkDone;
                    document.head.appendChild(s);
                }
                if (!window.saveAs) {
                    pending++;
                    const s = document.createElement('script');
                    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js';
                    s.onload = checkDone;
                    s.onerror = checkDone;
                    document.head.appendChild(s);
                }
                if (pending === 0) callback();
            }

            let bulkCancelled = false;
            function cancelBulkDownload() {
                bulkCancelled = true;
                const modal = document.getElementById('bulk-download-progress-modal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            window.addEventListener('start-bulk-cards-zip', (event) => {
                const cards = (event.detail && event.detail.cards) ? event.detail.cards : (event.detail && event.detail[0] ? event.detail[0].cards : []);
                const zipName = (event.detail && event.detail.zipName) ? event.detail.zipName : ((event.detail && event.detail[0]) ? event.detail[0].zipName : 'Student_ID_Cards.zip');

                if (!cards || !cards.length) {
                    alert('No card records found to package.');
                    return;
                }

                loadSnapshotLibraries(async () => {
                    bulkCancelled = false;
                    const modal = document.getElementById('bulk-download-progress-modal');
                    const progressBar = document.getElementById('bulk-progress-bar');
                    const progressText = document.getElementById('bulk-progress-text');
                    const progressCount = document.getElementById('bulk-progress-count');
                    const progressMember = document.getElementById('bulk-progress-member');
                    const stage = document.getElementById('bulk-card-stage');

                    if (!modal || !stage) {
                        alert('Could not initialize rendering stage.');
                        return;
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    const zip = new JSZip();
                    let processed = 0;
                    const total = cards.length;

                    for (let i = 0; i < total; i++) {
                        if (bulkCancelled) return;

                        const card = cards[i];
                        const percent = Math.round(((i + 1) / total) * 100);

                        if (progressBar) progressBar.style.width = percent + '%';
                        if (progressText) progressText.innerText = `${percent}% Completed`;
                        if (progressCount) progressCount.innerText = `${i + 1} / ${total}`;
                        if (progressMember) progressMember.innerText = `Card ${i + 1} of ${total}: ${card.full_name} (${card.member_id_number})`;

                        // Inject card data into stage
                        document.getElementById('b-card-org-title').innerText = card.org_title || 'Federation of African Law Students';
                        const idSuffix = document.getElementById('b-card-id-suffix');
                        if (idSuffix) idSuffix.style.display = card.has_id_card_suffix ? 'block' : 'none';

                        const mainLogo = document.getElementById('b-card-main-logo');
                        const assocLogo = document.getElementById('b-card-assoc-logo');
                        const photoImg = document.getElementById('b-card-photo');
                        const qrImg = document.getElementById('b-card-qr');

                        document.getElementById('b-card-name').innerText = card.full_name || '';
                        document.getElementById('b-card-member-id').innerText = card.member_id_number || '';
                        document.getElementById('b-card-institution').innerText = card.institution || '';
                        document.getElementById('b-card-admission').innerText = card.admission_year || 'N/A';
                        document.getElementById('b-card-completion').innerText = card.completion_year || 'Present';

                        const rolePill = document.getElementById('b-card-role-pill');
                        const roleText = document.getElementById('b-card-role-text');
                        if (card.is_executive) {
                            rolePill.className = 'flex-1 rounded-xl py-1.5 px-2 text-center flex items-center justify-center shadow-sm min-w-0 bg-gradient-to-b from-yellow-500/20 via-yellow-500/10 to-transparent border border-yellow-400/60 text-yellow-300 shadow-yellow-500/10';
                            roleText.className = 'font-black text-[10px] leading-tight tracking-tight whitespace-nowrap text-yellow-300';
                            roleText.innerText = '⭐ ' + (card.position ? card.position.toUpperCase() : 'EXECUTIVE');
                        } else {
                            rolePill.className = 'flex-1 rounded-xl py-1.5 px-2 text-center flex items-center justify-center shadow-sm min-w-0 bg-white/15 border border-white/30 text-white';
                            roleText.className = 'font-black text-[10px] leading-tight tracking-tight whitespace-nowrap text-white';
                            roleText.innerText = '👤 MEMBER';
                        }

                        document.getElementById('b-card-token').innerText = 'Token: ' + (card.qr_token || '');
                        document.getElementById('b-card-status-text').innerText = (card.status || 'ACTIVE').toUpperCase() + ' ID';

                        // Preload and decode images
                        const loadImg = (imgEl, src) => {
                            if (!src) {
                                imgEl.style.display = 'none';
                                return Promise.resolve();
                            }
                            imgEl.style.display = 'block';
                            if (imgEl.src === src && imgEl.complete) {
                                return Promise.resolve();
                            }
                            return new Promise(resolve => {
                                imgEl.onload = () => resolve();
                                imgEl.onerror = () => resolve();
                                imgEl.src = src;
                                setTimeout(resolve, 300);
                            });
                        };

                        await Promise.all([
                            loadImg(mainLogo, card.main_logo_url),
                            loadImg(assocLogo, card.association_logo_url),
                            loadImg(photoImg, card.photo_url || 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="%2364748b"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>'),
                            loadImg(qrImg, card.qr_code_url)
                        ]);

                        let blob = null;
                        try {
                            const canvas = await html2canvas(stage, {
                                scale: 2,
                                useCORS: true,
                                allowTaint: false,
                                backgroundColor: '#064e3b',
                                logging: false,
                                scrollX: 0,
                                scrollY: 0,
                            });
                            blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
                        } catch (renderErr) {
                            console.warn('html2canvas error, attempting htmlToImage fallback for:', card.full_name, renderErr);
                            if (window.htmlToImage) {
                                try {
                                    blob = await window.htmlToImage.toBlob(stage, {
                                        pixelRatio: 2,
                                        backgroundColor: '#064e3b',
                                        skipFonts: true,
                                    });
                                } catch (h2iErr) {
                                    console.error('Snapshot completely failed for:', card.full_name, h2iErr);
                                }
                            }
                        }

                        if (blob) {
                            const cleanName = (card.full_name || 'member').toLowerCase().replace(/[^a-z0-9]+/g, '_');
                            const idNum = (card.member_id_number || '').replace(/[^a-z0-9]+/gi, '_');
                            zip.file(`Virtual_ID_${cleanName}_${idNum}.png`, blob);
                            processed++;
                        }
                    }

                    if (bulkCancelled) return;

                    if (processed === 0) {
                        alert('Could not render card images. Please check your browser connection.');
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        return;
                    }

                    if (progressMember) progressMember.innerText = `Compressing ${processed} ID Cards into ZIP archive...`;
                    if (progressText) progressText.innerText = `Generating ZIP file...`;

                    const zipBlob = await zip.generateAsync({
                        type: 'blob',
                        compression: 'DEFLATE',
                        compressionOptions: { level: 6 }
                    });

                    saveAs(zipBlob, zipName);

                    if (progressMember) progressMember.innerText = `✓ Successfully created ${zipName}!`;
                    if (progressText) progressText.innerText = `Download starting...`;

                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 1500);
                });
            });

            function downloadAdminCardImage() {
                const btn = document.getElementById('admin-download-card-btn');
                if (!btn) return;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<svg class="animate-spin w-4 h-4 text-white inline-block mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Rendering Card...</span>';
                btn.disabled = true;

                loadSnapshotLibraries(() => {
                    const element = document.getElementById('admin-preview-card-canvas');
                    if (!element) {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                        return;
                    }

                    const filename = 'Virtual_ID_Card_{{ $previewCard ? Str::slug($previewCard->full_name) : "pass" }}_{{ $previewCard ? $previewCard->member_id_number : "card" }}.png';

                    const onComplete = (dataUrl) => {
                        const link = document.createElement('a');
                        link.download = filename;
                        link.href = dataUrl;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        btn.innerHTML = '<span class="text-emerald-300 font-black">✓ Downloaded!</span>';
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }, 2000);
                    };

                    const executeCapture = () => {
                        if (window.html2canvas) {
                            html2canvas(element, {
                                scale: 3,
                                useCORS: true,
                                allowTaint: false,
                                backgroundColor: '#064e3b',
                                logging: false,
                                scrollX: 0,
                                scrollY: 0,
                                ignoreElements: (el) => el.tagName === 'BUTTON',
                            }).then(canvas => {
                                try {
                                    onComplete(canvas.toDataURL('image/png'));
                                } catch (e) {
                                    console.warn('toDataURL failed, attempting blob:', e);
                                    canvas.toBlob((blob) => {
                                        if (blob) {
                                            const url = URL.createObjectURL(blob);
                                            onComplete(url);
                                        } else {
                                            fallbackToHtmlToImage();
                                        }
                                    }, 'image/png');
                                }
                            }).catch(err => {
                                console.warn('html2canvas failed, falling back:', err);
                                fallbackToHtmlToImage();
                            });
                        } else {
                            fallbackToHtmlToImage();
                        }
                    };

                    function fallbackToHtmlToImage() {
                        if (window.htmlToImage) {
                            window.htmlToImage.toPng(element, {
                                pixelRatio: 3,
                                backgroundColor: '#064e3b',
                                cacheBust: true,
                                skipFonts: true,
                                filter: (node) => node.tagName !== 'BUTTON',
                            }).then(onComplete).catch(finalErr => {
                                console.error('Snapshot failed:', finalErr);
                                btn.innerHTML = originalHtml;
                                btn.disabled = false;
                                @if($previewCard)
                                    window.open('{{ route("virtual-cards.public.view", ["uuid" => $previewCard->uuid]) }}', '_blank');
                                @else
                                    alert('Could not render image snapshot automatically. Please use the Print option.');
                                @endif
                            });
                        } else {
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                            @if($previewCard)
                                window.open('{{ route("virtual-cards.public.view", ["uuid" => $previewCard->uuid]) }}', '_blank');
                            @else
                                alert('Snapshot library not available.');
                            @endif
                        }
                    }

                    if (document.fonts && document.fonts.ready) {
                        document.fonts.ready.then(executeCapture).catch(executeCapture);
                    } else {
                        executeCapture();
                    }
                });
            }

            function copyShareUrl() {
                const input = document.getElementById('shareable-app-url-input');
                if (!input) return;

                input.focus();
                input.select();
                input.setSelectionRange(0, 99999);

                let copied = false;
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(input.value).then(() => {
                        showCopySuccess();
                    }).catch(() => {
                        fallbackCopy(input);
                    });
                } else {
                    fallbackCopy(input);
                }
            }

            function fallbackCopy(input) {
                try {
                    document.execCommand('copy');
                    showCopySuccess();
                } catch (err) {
                    alert('Please select and copy the text manually.');
                }
            }

            function showCopySuccess() {
                const btn = document.getElementById('copy-share-url-btn');
                if (!btn) return;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<span class="text-emerald-300 font-black">✓ Copied!</span>';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 2500);
            }
        </script>
</div>
