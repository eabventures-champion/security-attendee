<div x-data="{ dropdownOpen: false }" class="relative z-50">
    <!-- Notification Bell Button -->
    <button @click="dropdownOpen = !dropdownOpen" @click.outside="dropdownOpen = false" type="button" class="relative text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 p-2.5 rounded-full transition-all cursor-pointer focus:outline-none" title="Notifications">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full"></span>
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full animate-ping"></span>
            <span class="absolute -top-1 -right-1 bg-rose-600 text-white text-[10px] font-extrabold px-1.5 py-0.2 rounded-full shadow-md min-w-[18px] text-center border border-white dark:border-slate-900">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown Window -->
    <div x-show="dropdownOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute right-0 top-full mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-[9999] overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/80">
        
        <!-- Header -->
        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Notifications</h3>
                @if($unreadCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                        {{ $unreadCount }} New
                    </span>
                @endif
            </div>
            @if(count($notifications) > 0)
                <div class="flex items-center gap-2 text-xs">
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold cursor-pointer">
                            Mark all read
                        </button>
                    @endif
                    <button wire:click="clearAll" class="text-slate-400 hover:text-rose-500 font-semibold cursor-pointer">
                        Clear
                    </button>
                </div>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-white/5 custom-scrollbar">
            @forelse($notifications as $noti)
                @php
                    $isUnread = is_null($noti->read_at);
                    $type = $noti->data_array['type'] ?? 'info';
                    $link = $noti->data_array['link'] ?? null;
                @endphp
                <div wire:click="markAsRead('{{ $noti->id }}', {{ $link ? "'".$link."'" : 'null' }})" class="p-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer flex items-start gap-3 relative {{ $isUnread ? 'bg-blue-500/5 dark:bg-blue-500/10' : '' }}">
                    
                    <!-- Icon -->
                    <div class="p-2 rounded-xl shrink-0 
                        {{ $type === 'registration' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : '' }}
                        {{ $type === 'checkin' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : '' }}
                        {{ $type === 'info' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' : '' }}
                    ">
                        @if($type === 'registration')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        @elseif($type === 'checkin')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <span class="font-bold text-xs text-slate-900 dark:text-white truncate block">
                                {{ $noti->data_array['title'] ?? 'System Alert' }}
                            </span>
                            @if($isUnread)
                                <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0 ml-2 mt-1"></span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 line-clamp-2 leading-relaxed">
                            {{ $noti->data_array['message'] ?? '' }}
                        </p>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium block mt-1">
                            {{ $noti->formatted_time }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center space-y-2">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">No Notifications Yet</p>
                    <p class="text-[11px] text-slate-400 max-w-xs mx-auto">You'll receive alerts here when attendees register or check in at your event gates.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
