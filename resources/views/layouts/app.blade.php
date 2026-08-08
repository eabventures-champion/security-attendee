<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          darkMode: localStorage.getItem('theme') !== 'light',
          sidebarOpen: window.innerWidth >= 1024 ? (localStorage.getItem('sidebarOpen') !== 'false') : false 
      }" 
      x-init="
          $watch('darkMode', val => {
              localStorage.setItem('theme', val ? 'dark' : 'light');
              if (val) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          });
          $watch('sidebarOpen', val => {
              if (window.innerWidth >= 1024) {
                  localStorage.setItem('sidebarOpen', val);
              }
          });
      "
      :class="{ 'dark': darkMode }"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Anti-flicker Theme Initialization -->
    <script>
        if (localStorage.getItem('theme') !== 'light') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <title>{{ config('app.name', 'AttendFlow') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cinzel:wght@400;600;700;800&family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    
    <style>
        [x-cloak] { display: none !important; }
        /* Global dropdown option dark mode visibility fix */
        .dark select option, select option {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-300">
    @if(session()->has('impersonator_id'))
        <div class="bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white text-xs font-bold py-2.5 px-6 shadow-2xl z-50 flex items-center justify-between border-b border-amber-400/40 sticky top-0">
            <div class="flex items-center gap-2.5">
                <span class="px-2.5 py-0.5 rounded-full bg-black/40 border border-white/20 uppercase tracking-widest text-[10px] text-amber-200">Super Admin Impersonation Mode</span>
                <span>⚡ Currently managing workspace for <strong class="text-amber-100 underline decoration-amber-300/50">{{ auth()->user()->organization?->name ?? 'Organization' }}</strong> (Admin: <strong>{{ auth()->user()->name }}</strong>)</span>
            </div>
            <form action="{{ route('superadmin.stop_impersonate') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-3.5 py-1 bg-white text-slate-950 hover:bg-amber-100 font-extrabold rounded-lg shadow transition-all cursor-pointer text-xs flex items-center gap-1.5 border border-white/40">
                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Exit Impersonation
                </button>
            </form>
        </div>
    @endif

    <!-- Background Pattern -->
    <div class="fixed inset-0 z-[-1] pointer-events-none opacity-20 dark:opacity-[0.03] bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:24px_24px]"></div>

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar overlay for mobile -->
        <div x-show="sidebarOpen" 
             class="fixed inset-0 z-20 bg-slate-900/80 backdrop-blur-sm lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0 w-72' : '-translate-x-full lg:translate-x-0 lg:w-20'" 
               class="fixed inset-y-0 left-0 z-30 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-all duration-300 shadow-xl">
            
            <!-- Logo area -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-slate-200 dark:border-slate-800">
                <a href="{{ route('dashboard') ?? '#' }}" @click="if (window.innerWidth < 1024) sidebarOpen = false" class="flex items-center overflow-hidden">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shrink-0 shadow-md tracking-tight">
                        BS
                    </div>
                    <span x-show="sidebarOpen" 
                          x-transition.opacity.duration.300ms
                          class="ml-3 text-xl font-bold text-slate-800 dark:text-white whitespace-nowrap">
                        Built Studios
                    </span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation -->
            @php
            $defaultEventUuid = request()->route('eventUuid') 
                ?? request()->route('uuid') 
                ?? \App\Models\Event::latest()->value('uuid');
            $eventParams = $defaultEventUuid ? ['eventUuid' => $defaultEventUuid] : [];
            $isSecurityUser = auth()->check() && auth()->user()->isSecurityPersonnel();
            @endphp
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
                @if(!$isSecurityUser)
                    <x-sidebar-item route="dashboard" label="Dashboard">
                        <x-slot:icon>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </x-slot:icon>
                    </x-sidebar-item>
                    
                    <x-sidebar-item route="events.index" label="Events">
                        <x-slot:icon>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </x-slot:icon>
                    </x-sidebar-item>

                    <x-sidebar-item route="attendees.index" label="Attendees">
                        <x-slot:icon>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </x-slot:icon>
                    </x-sidebar-item>
                @endif

                <x-sidebar-item route="gates.index" :params="$eventParams" label="Gates">
                    <x-slot:icon>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </x-slot:icon>
                </x-sidebar-item>

                <x-sidebar-item route="scanner.index" :params="$eventParams" label="Scanner">
                    <x-slot:icon>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </x-slot:icon>
                </x-sidebar-item>

                @if(!$isSecurityUser)
                    <x-sidebar-item route="reports.index" :params="$eventParams" label="Reports">
                        <x-slot:icon>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </x-slot:icon>
                    </x-sidebar-item>

                    <x-sidebar-item route="resources.index" label="Resource">
                        <x-slot:icon>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </x-slot:icon>
                    </x-sidebar-item>

                    <div class="pt-4 mt-4 border-t border-slate-800">
                        <x-sidebar-item route="users.index" label="Team & Roles">
                            <x-slot:icon>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </x-slot:icon>
                        </x-sidebar-item>

                        <x-sidebar-item route="settings.index" label="Settings">
                            <x-slot:icon>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </x-slot:icon>
                        </x-sidebar-item>

                        <x-sidebar-item route="profile.index" label="Profile">
                            <x-slot:icon>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </x-slot:icon>
                        </x-sidebar-item>
                    </div>
                @else
                    <div class="pt-4 mt-4 border-t border-slate-800">
                        <x-sidebar-item route="profile.index" label="Profile">
                            <x-slot:icon>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </x-slot:icon>
                        </x-sidebar-item>
                    </div>
                @endif
            </nav>
            
            <!-- Sidebar Footer Toggle -->
            <div class="p-4 border-t border-slate-800 flex justify-center lg:justify-end">
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex items-center justify-center w-8 h-8 rounded bg-slate-800 text-slate-400 hover:text-white transition-colors">
                    <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300" 
             :class="sidebarOpen ? 'lg:ml-72' : 'lg:ml-20'">
            
            <!-- Top Navbar -->
            <header class="h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 z-40 sticky top-0">
                <div class="flex items-center flex-1">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 dark:text-slate-400 mr-4">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="text-xl font-semibold text-slate-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors truncate" title="Go to Dashboard">
                        @yield('title', 'Dashboard')
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="hidden sm:flex relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" placeholder="Search..." class="bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg pl-10 pr-12 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64 transition-all">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                            <span class="text-xs text-slate-400 border border-slate-300 dark:border-slate-600 rounded px-1.5 py-0.5">Ctrl K</span>
                        </div>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <button type="button" @click="darkMode = !darkMode" class="text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 p-2 rounded-full transition-colors cursor-pointer" :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                        <svg x-show="!darkMode" class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <svg x-show="darkMode" class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>

                    <!-- Notifications Dropdown Component -->
                    <livewire:header-notifications />

                    <!-- Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative z-50">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center space-x-2 focus:outline-none p-1 rounded-full hover:ring-2 hover:ring-blue-500/50 transition-all">
                            <img class="w-9 h-9 rounded-full border-2 border-blue-500/40 object-cover shadow-md" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin User') }}&background=3b82f6&color=fff&bold=true" alt="User avatar">
                        </button>
                        
                        <div x-show="open" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="absolute right-0 top-full mt-2 w-56 bg-slate-800/95 backdrop-blur-xl rounded-xl shadow-2xl py-2 border border-slate-700/80 z-[9999] overflow-hidden divide-y divide-slate-700/60"
                        >
                            <!-- User Header Info -->
                            <div class="px-4 py-2.5">
                                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                                <p class="text-xs text-slate-400 truncate mb-1.5">{{ auth()->user()->email ?? 'admin@attendflow.com' }}</p>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    {{ auth()->user()->role_label }}
                                </span>
                            </div>

                            <!-- Menu Links -->
                            <div class="py-1">
                                <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-blue-600/20 transition-colors">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    My Profile
                                </a>
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-blue-600/20 transition-colors">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Settings
                                </a>
                            </div>

                            <!-- Logout -->
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2.5 w-full text-left px-4 py-2 text-sm text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-6 lg:p-8 custom-scrollbar">
                @if(auth()->check() && !auth()->user()->isSuperAdmin() && auth()->user()->hasRole('organization_admin') && auth()->user()->invitation_status !== 'confirmed')
                    <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg animate-pulse">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-amber-500/20 text-amber-400 shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="text-xs sm:text-sm">
                                <span class="font-extrabold text-amber-200">Workspace Pending Email Confirmation:</span>
                                Please check your email inbox (<span class="font-bold underline text-amber-100">{{ auth()->user()->email }}</span>) and click <strong>"Confirm Receipt & Accept Workspace"</strong> to unlock event creation & team management.
                            </div>
                        </div>
                    </div>
                @endif

                @if(session()->has('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 flex items-center justify-between gap-4 shadow-lg">
                        <div class="flex items-center gap-3 text-xs sm:text-sm font-semibold">
                            <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
