<div class="space-y-8 font-inter">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-1">
        <div class="space-y-1">
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
                    <button wire:click="bulkDelete" 
                            wire:confirm="Are you sure you want to delete {{ count($selectedMembers) }} selected virtual ID card(s)?"
                            class="px-2.5 py-1 rounded-full border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <span>Delete Selected ({{ count($selectedMembers) }})</span>
                    </button>
                @endif
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-medium text-xs sm:text-sm whitespace-normal sm:whitespace-nowrap">
                Manage members, customize card fields &amp; logo, auto-generate digital ID cards,<br> and dispatch via Email &amp; WhatsApp.
            </p>
        </div>

        <!-- Right-Aligned Action Toolbar -->
        <div class="flex flex-wrap items-center justify-start lg:justify-end gap-2 sm:gap-2.5 shrink-0">
            
            <!-- Customize Fields & Logo -->
            <button wire:click="$set('showFieldCustomizerModal', true)" 
                    class="h-10 px-3.5 rounded-xl border border-purple-500/30 bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95" 
                    title="Customize card fields, add custom fields, and upload institution logo">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Fields &amp; Logo</span>
            </button>

            <!-- Share Application Link -->
            <button wire:click="openShareLinkModal" 
                    class="h-10 px-3.5 rounded-xl border border-amber-500/30 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95" 
                    title="Generate and copy shareable member registration & card generation link">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                <span>Share Link</span>
            </button>

            <!-- Download All Photos (.ZIP) -->
            <button wire:click="downloadAllPhotos" 
                    wire:loading.attr="disabled"
                    class="h-10 px-3.5 rounded-xl border border-orange-500/30 bg-orange-500/10 hover:bg-orange-500/20 text-orange-600 dark:text-orange-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95" 
                    title="Download ZIP package of all member profile photos">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span wire:loading.remove wire:target="downloadAllPhotos">Download All Photos (.ZIP)</span>
                <span wire:loading wire:target="downloadAllPhotos" class="flex items-center gap-1">
                    <svg class="animate-spin h-3.5 w-3.5 text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Zipping Photos...</span>
                </span>
            </button>

            <!-- Import Excel / CSV -->
            <button wire:click="openUploadModal" 
                    class="h-10 px-3.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm hover:shadow active:scale-95" 
                    title="Batch upload members from Excel or CSV spreadsheet">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m4-8l-4-4m0 0L13 8m4-4v12"></path></svg>
                <span>Import</span>
            </button>

            <!-- Add Member -->
            <button wire:click="openAddModal" 
                    class="h-10 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg> 
                <span>Add</span>
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

        <!-- Institutions -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/5 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-black uppercase tracking-wider text-purple-600 dark:text-purple-400">Institutions</span>
                <div class="p-2 rounded-xl bg-purple-500/10 text-purple-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ count($institutions) }}</div>
        </div>

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
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/5 shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative lg:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, email, ID number, faculty..." class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <!-- Institution Filter -->
            <div>
                <select wire:model.live="institutionFilter" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Institutions ({{ count($institutions) }})</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst }}">{{ $inst }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
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
                        <th class="py-3.5 px-4">Member &amp; ID Number</th>
                        <th class="py-3.5 px-4">Institution &amp; Faculty</th>
                        <th class="py-3.5 px-4">Years</th>
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
                                    <div class="space-y-0.5 min-w-0">
                                        <div class="font-black text-slate-900 dark:text-white text-sm truncate flex items-center gap-1.5">
                                            <span>{{ $m->full_name }}</span>
                                            <span class="text-blue-500" title="Digitally signed ID">
                                                <svg class="w-3.5 h-3.5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            </span>
                                        </div>
                                        <div class="text-[11px] font-mono text-blue-600 dark:text-blue-400 font-bold">
                                            {{ $m->member_id_number }}
                                        </div>
                                        @if($m->email)
                                            <div class="text-[11px] text-slate-400 truncate">{{ $m->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Institution & Faculty -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-slate-200">{{ $m->institution ?: 'FALAS' }}</div>
                            </td>

                            <!-- Admission / Completion Years -->
                            <td class="py-3.5 px-4 font-mono text-[11px]">
                                <span class="text-slate-400">{{ $m->admission_year ?: 'N/A' }}</span>
                                <span class="text-slate-500">—</span>
                                <span class="text-slate-200 font-bold">{{ $m->completion_year ?: 'Present' }}</span>
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
                                        <button wire:click="downloadPhoto({{ $m->id }})" class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 font-bold transition-all cursor-pointer" title="Download Member Profile Photo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    @endif

                                    <!-- Live Card Preview -->
                                    <button wire:click="openCardPreview({{ $m->id }})" class="p-2 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-bold transition-all cursor-pointer" title="View Virtual ID Card Pass">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    <!-- Send Email -->
                                    <button wire:click="sendCardEmail({{ $m->id }})" class="p-2 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 border border-purple-500/20 font-bold transition-all cursor-pointer" title="Dispatch Virtual ID via Email">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </button>

                                    <!-- Send WhatsApp -->
                                    <button wire:click="sendCardWhatsApp({{ $m->id }})" class="p-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold transition-all cursor-pointer" title="Dispatch Virtual ID via WhatsApp">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.97.581 1.936.945 2.795.945 3.179 0 5.766-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm10.009 5.766c-.002 5.51-4.484 9.991-9.996 9.991-1.748 0-3.377-.45-4.814-1.242L2 22l1.523-5.568C2.693 14.972 2.21 13.35 2.21 11.938c.002-5.51 4.484-9.992 9.997-9.992 5.513 0 9.995 4.482 9.996 9.992z"/></svg>
                                    </button>

                                    <!-- Edit -->
                                    <button wire:click="openEditModal({{ $m->id }})" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/10 text-slate-500 dark:text-slate-300 font-bold transition-all cursor-pointer" title="Edit Member">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    <!-- Delete -->
                                    <button wire:click="deleteMember({{ $m->id }})" wire:confirm="Are you sure you want to delete this virtual ID card?" class="p-2 rounded-xl hover:bg-rose-500/10 text-rose-500 font-bold transition-all cursor-pointer" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 sm:p-7 max-w-xl w-full shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto">
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

                    <!-- ID Number & Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Member ID Number *</label>
                            <input type="text" wire:model="member_id_number" required placeholder="e.g. FALAS-2026-001" class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
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
                            <label class="block font-bold text-slate-300 uppercase tracking-wider mb-1">Phone (for WhatsApp Dispatch)</label>
                            <input type="text" wire:model="phone" placeholder="+233..." class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-500">
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">Institution/Faculty of Law</label>
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
    @endif

    <!-- ==================== MODAL 2: FIELDS & LOGO CUSTOMIZER ==================== -->
    @if($showFieldCustomizerModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 sm:p-7 max-w-xl w-full shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div class="space-y-0.5">
                        <h3 class="text-lg font-black text-white">Customize ID Card Fields &amp; Logo</h3>
                        <p class="text-xs text-slate-400">Upload institution branding logo and configure standard &amp; custom fields.</p>
                    </div>
                    <button wire:click="$set('showFieldCustomizerModal', false)" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- 1. Institution Logo Upload -->
                <div class="p-4 rounded-2xl bg-purple-500/10 border border-purple-500/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-purple-400 text-xs uppercase tracking-wider">Institution Branding Logo</span>
                        @if($existing_institution_logo_path)
                            <button type="button" wire:click="removeInstitutionLogo" class="text-rose-400 hover:underline text-[11px] font-bold">Remove Logo</button>
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl border border-purple-500/30 bg-slate-800 flex items-center justify-center p-2 overflow-hidden shrink-0">
                            @if($institution_logo)
                                <img src="{{ $institution_logo->temporaryUrl() }}" alt="Logo" class="w-full h-full object-contain">
                            @elseif($existing_institution_logo_path)
                                <img src="{{ asset('storage/' . $existing_institution_logo_path) }}" alt="Logo" class="w-full h-full object-contain">
                            @else
                                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            @endif
                        </div>
                        <div class="space-y-1 flex-1">
                            <input type="file" wire:model="institution_logo" accept="image/*" class="text-xs text-slate-300 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-500 cursor-pointer">
                            <p class="text-[11px] text-slate-400">Logo is rendered at the top of the ID card badge.</p>
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
    @endif

    <!-- ==================== MODAL 3: EXCEL / CSV BATCH UPLOAD ==================== -->
    @if($showUploadModal)
        @php
            $expectedFields = $this->getExpectedImportFields();
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 sm:p-7 max-w-2xl w-full shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto">
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
                            <div class="max-h-48 overflow-y-auto space-y-1.5 font-mono text-[11px] bg-slate-900/50 p-2.5 rounded-xl border border-emerald-500/20">
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
    @endif

    <!-- ==================== MODAL 4: LIVE CARD PREVIEW & SNAPSHOT ==================== -->
    @if($showCardPreviewModal && $previewCard)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl space-y-5 max-h-[95vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/10 pb-3">
                    <div class="space-y-0.5">
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Virtual ID Card Pass</h3>
                        <p class="text-xs text-slate-500">{{ $previewCard->full_name }} ({{ $previewCard->member_id_number }})</p>
                    </div>
                    <button wire:click="closeCardPreview" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Digital Card Snapshot Canvas Target -->
                <div id="admin-preview-card-canvas" class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border-2 border-blue-500/40 p-5 sm:p-6 space-y-5 text-white shadow-2xl">
                    
                    <!-- Background Institution Logo / Law Watermark -->
                    <div class="absolute inset-0 opacity-[0.07] pointer-events-none flex items-center justify-center overflow-hidden p-6 select-none">
                        @if($previewCard->institution_logo_url)
                            <img src="{{ $previewCard->institution_logo_url }}" alt="Watermark" class="w-64 h-64 object-contain filter grayscale contrast-150">
                        @else
                            <svg class="w-64 h-64 text-white" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m0-18l-8 4m8-4l8 4M4 7l-2 6h8L8 7m8 0l-2 6h8l-2-6M6 21h12"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Card Top Header with Institution Logo -->
                    <div class="flex items-center justify-between border-b border-white/10 pb-3.5 relative z-10 gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            @if($previewCard->institution_logo_url)
                                <img src="{{ $previewCard->institution_logo_url }}" alt="Logo" class="w-8 h-8 object-contain rounded-lg bg-white/5 p-0.5 shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-black text-xs shrink-0">
                                    🏛️
                                </div>
                            @endif
                            <div class="space-y-0.5 min-w-0">
                                <div class="text-[11px] font-black uppercase tracking-wider text-blue-400 font-sans leading-tight">
                                    Federation of African Law Students (FALAS)
                                </div>
                                <div class="text-[10.5px] font-bold text-orange-400 tracking-wide font-sans">
                                    {{ $previewCard->institution ?: 'University of Ghana, School of Law' }}
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="inline-block px-2.5 py-1 rounded-lg bg-blue-500/20 text-blue-300 font-mono text-[11px] font-bold whitespace-nowrap border border-blue-500/30">
                                {{ $previewCard->member_id_number }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="flex items-start gap-4 relative z-10">
                        <!-- Photo or Silhouette -->
                        <div class="relative group w-24 h-32 rounded-xl border-2 border-blue-400/50 bg-slate-800 overflow-hidden flex items-center justify-center shrink-0 shadow">
                            @if($previewCard->photo_url)
                                <img src="{{ $previewCard->photo_url }}" alt="{{ $previewCard->full_name }}" class="w-full h-full object-cover">
                                <!-- Interactive Hover to Download Photo directly -->
                                <button type="button" 
                                        wire:click="downloadPhoto({{ $previewCard->id }})" 
                                        title="Click to Download Profile Photo"
                                        class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white transition-all cursor-pointer">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span class="text-[9px] font-bold mt-1">Download</span>
                                </button>
                            @else
                                <div class="flex flex-col items-center justify-center text-slate-500">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                    <span class="text-[8px] font-bold uppercase mt-1">Photo on File</span>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 space-y-2 text-xs">
                            <div>
                                <span class="text-[9px] font-extrabold uppercase text-slate-400 block">Cardholder</span>
                                <h4 class="text-base font-black text-white leading-tight">{{ $previewCard->full_name }}</h4>
                            </div>

                            <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                                <div class="p-1.5 rounded-lg bg-white/5">
                                    <span class="text-[9px] text-slate-400 block uppercase">Admission</span>
                                    <span class="font-bold text-slate-200">{{ $previewCard->admission_year ?: 'N/A' }}</span>
                                </div>
                                <div class="p-1.5 rounded-lg bg-white/5">
                                    <span class="text-[9px] text-slate-400 block uppercase">Completion</span>
                                    <span class="font-bold text-slate-200">{{ $previewCard->completion_year ?: 'Present' }}</span>
                                </div>
                            </div>

                            @if(!empty($previewCard->custom_fields))
                                <div class="grid grid-cols-2 gap-1.5 text-[10px]">
                                    @foreach($previewCard->custom_fields as $cfKey => $cfVal)
                                        @if(!empty($cfVal))
                                            <div class="p-1 rounded bg-white/5">
                                                <span class="text-slate-400 block uppercase">{{ ucwords(str_replace('_', ' ', $cfKey)) }}</span>
                                                <span class="font-bold text-slate-200 truncate block">{{ $cfVal }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer Bar with QR Code -->
                    <div class="pt-3 border-t border-white/10 flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="p-1 rounded-lg bg-white shadow shrink-0">
                                <img src="{{ $previewCard->qr_code_url }}" alt="QR" class="w-12 h-12 rounded">
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-[9px] font-black uppercase text-emerald-400 block">Digitally Verified</span>
                                <span class="text-[9px] text-slate-400 font-mono">{{ $previewCard->qr_token }}</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            Active ID
                        </span>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="space-y-2 pt-2 text-xs">
                    @if($previewCard->photo_path || $previewCard->photo_url)
                        <!-- Download Profile Photo (Primary Button) -->
                        <button type="button" 
                                wire:click="downloadPhoto({{ $previewCard->id }})" 
                                class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold flex items-center justify-center gap-2 shadow-lg shadow-blue-500/25 transition-all cursor-pointer active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Download Profile Photo</span>
                        </button>
                    @else
                        <div class="p-2.5 rounded-xl bg-slate-800/80 border border-white/10 text-center text-slate-400 text-xs">
                            <span>No profile photo uploaded for this member</span>
                        </div>
                    @endif

                    <!-- Send Email & Send WhatsApp Grid -->
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" wire:click="sendCardEmail({{ $previewCard->id }})" class="py-2.5 px-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold flex items-center justify-center gap-1.5 shadow transition-all cursor-pointer active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>Send Email</span>
                        </button>
                        <button type="button" wire:click="sendCardWhatsApp({{ $previewCard->id }})" class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold flex items-center justify-center gap-1.5 shadow transition-all cursor-pointer active:scale-95">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.97.581 1.936.945 2.795.945 3.179 0 5.766-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm10.009 5.766c-.002 5.51-4.484 9.991-9.996 9.991-1.748 0-3.377-.45-4.814-1.242L2 22l1.523-5.568C2.693 14.972 2.21 13.35 2.21 11.938c.002-5.51 4.484-9.992 9.997-9.992 5.513 0 9.995 4.482 9.996 9.992z"/></svg>
                            <span>Send WhatsApp</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== MODAL 4: SHARE APPLICATION LINK ==================== -->
    @if($showShareLinkModal)
        @php
            $shareUrl = $this->getShareableApplicationUrl();
            $waShareText = "Hello! Please click the link below to register and receive your official Digital Virtual ID Card:\n\n" . $shareUrl;
            $waShareUrl = "https://api.whatsapp.com/send?text=" . rawurlencode($waShareText);
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($shareUrl);
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto">
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
    @endif

    <!-- ==================== MODAL 5: MANAGE INSTITUTIONS ==================== -->
    @if($showInstitutionsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fadeIn">
            <div class="bg-slate-900 border border-white/10 rounded-3xl w-full max-w-2xl shadow-2xl p-6 space-y-5 max-h-[92vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-orange-500/10 text-orange-400 text-[10px] font-black uppercase tracking-wider border border-orange-500/20">
                            Dropdown Configuration
                        </div>
                        <h3 class="text-lg font-black text-white">🏛️ Manage Institution &amp; Faculty Names</h3>
                    </div>
                    <button wire:click="closeInstitutionsModal" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

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
                                class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow transition-all cursor-pointer shrink-0 active:scale-95">
                            + Add
                        </button>
                    </div>
                </div>

                <!-- Upload List or Reset -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Upload CSV/TXT File -->
                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 space-y-2.5 flex flex-col justify-between">
                        <div>
                            <span class="font-bold text-slate-300 uppercase tracking-wider text-[11px] block">📁 Upload File (.TXT or .CSV)</span>
                            <p class="text-[11px] text-slate-400 mt-1">Upload a text file with one institution name per line.</p>
                        </div>
                        <div class="space-y-2">
                            <input type="file" wire:model="institutions_file" accept=".txt,.csv" class="block w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-500/20 file:text-orange-400 hover:file:bg-orange-500/30 cursor-pointer">
                            @if($institutions_file)
                                <button type="button" wire:click="uploadInstitutionsFile" class="w-full py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition-all cursor-pointer">
                                    Import Uploaded File
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Reset to Default 19 Standard Law Faculties -->
                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 space-y-2.5 flex flex-col justify-between">
                        <div>
                            <span class="font-bold text-slate-300 uppercase tracking-wider text-[11px] block">🔄 Reset Defaults</span>
                            <p class="text-[11px] text-slate-400 mt-1">Restore the 19 accredited Ghanaian Universities and Law Faculties list.</p>
                        </div>
                        <button type="button" 
                                wire:click="resetDefaultInstitutions" 
                                wire:confirm="Are you sure you want to reset to the default 19 standard law faculties list?"
                                class="w-full py-2 rounded-xl border border-white/10 hover:bg-white/10 text-slate-300 font-bold text-xs transition-all cursor-pointer">
                            Restore 19 Defaults
                        </button>
                    </div>
                </div>

                <!-- Current Institutions List Table -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px]">
                            Configured Institutions ({{ count($institutionList) }})
                        </label>
                    </div>

                    <div class="border border-white/10 rounded-2xl overflow-hidden divide-y divide-white/5 max-h-60 overflow-y-auto bg-slate-800/50">
                        @forelse($institutionList as $idx => $instName)
                            <div class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-800 transition-colors text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-slate-400 text-[10px] w-5 text-right">{{ $idx + 1 }}.</span>
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

                <div class="pt-3 flex justify-end border-t border-white/10">
                    <button type="button" wire:click="closeInstitutionsModal" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow transition-all cursor-pointer">
                        Done
                    </button>
                </div>
            </div>
        </div>
    @endif

        <script>
            function loadHtmlToImage(callback) {
                if (window.htmlToImage) {
                    callback();
                    return;
                }
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js';
                script.onload = callback;
                script.onerror = () => {
                    alert('Failed to load image generation library. Please check your internet connection.');
                };
                document.head.appendChild(script);
            }

            function downloadAdminCardImage() {
                const btn = document.getElementById('admin-download-card-btn');
                if (!btn) return;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<svg class="animate-spin w-4 h-4 text-white inline-block mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Rendering High-Res Image...</span>';
                btn.disabled = true;

                loadHtmlToImage(() => {
                    const element = document.getElementById('admin-preview-card-canvas');
                    if (!element) {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                        return;
                    }

                    window.htmlToImage.toPng(element, {
                        pixelRatio: 3,
                        backgroundColor: '#090d16',
                        cacheBust: true,
                    }).then(dataUrl => {
                        const link = document.createElement('a');
                        link.download = 'Virtual_ID_Card_{{ $previewCard ? Str::slug($previewCard->full_name) : "pass" }}_{{ $previewCard ? $previewCard->member_id_number : "card" }}.png';
                        link.href = dataUrl;
                        link.click();
                        btn.innerHTML = '<span class="text-emerald-300 font-black">✓ Downloaded!</span>';
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }, 2000);
                    }).catch(err => {
                        console.warn('PNG capture fallback to JPEG:', err);
                        window.htmlToImage.toJpeg(element, {
                            pixelRatio: 2,
                            backgroundColor: '#090d16',
                            quality: 0.95
                        }).then(dataUrl => {
                            const link = document.createElement('a');
                            link.download = 'Virtual_ID_Card_{{ $previewCard ? Str::slug($previewCard->full_name) : "pass" }}_{{ $previewCard ? $previewCard->member_id_number : "card" }}.jpg';
                            link.href = dataUrl;
                            link.click();
                            btn.innerHTML = '<span class="text-emerald-300 font-black">✓ Downloaded!</span>';
                            setTimeout(() => {
                                btn.innerHTML = originalHtml;
                                btn.disabled = false;
                            }, 2000);
                        }).catch(finalErr => {
                            console.error('Snapshot failed completely:', finalErr);
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                            alert('Could not render image snapshot: ' + (finalErr.message || 'Rendering error'));
                        });
                    });
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
