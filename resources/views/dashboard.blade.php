@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">
    <!-- Row 1: Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stagger-1">
            <x-stat-card 
                title="Total Events" 
                value="{{ $totalEvents ?? '24' }}" 
                change="12%" 
                changeType="increase"
                color="blue"
            >
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </x-slot:icon>
            </x-stat-card>
        </div>
        <div class="stagger-2">
            <x-stat-card 
                title="Total Registrations" 
                value="{{ $totalRegistrations ?? '8,439' }}" 
                change="5.4%" 
                changeType="increase"
                color="purple"
            >
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </x-slot:icon>
            </x-stat-card>
        </div>
        <div class="stagger-3">
            <x-stat-card 
                title="Verified Attendees" 
                value="{{ $verifiedAttendees ?? '6,210' }}" 
                change="2.1%" 
                changeType="decrease"
                color="emerald"
            >
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:icon>
            </x-stat-card>
        </div>
        <div class="stagger-4">
            <x-stat-card 
                title="Checked In Today" 
                value="{{ $checkedInToday ?? '1,245' }}" 
                change="18%" 
                changeType="increase"
                color="amber"
            >
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </x-slot:icon>
            </x-stat-card>
        </div>
    </div>

    <!-- Row 2: Charts and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Area -->
        <div class="lg:col-span-2 glassmorphism rounded-2xl border border-slate-700/50 p-6 stagger-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-white">Registration Trends</h3>
                <select class="bg-slate-800 border border-slate-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 text-white">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>This Year</option>
                </select>
            </div>
            <!-- Placeholder for Chart -->
            <div class="w-full h-64 rounded-xl border border-dashed border-slate-600 bg-slate-800/30 flex items-center justify-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-purple-500/5 opacity-50"></div>
                <div class="flex flex-col items-center">
                    <svg class="w-10 h-10 text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    <span class="text-slate-400">Chart rendering area</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glassmorphism rounded-2xl border border-slate-700/50 p-6 stagger-6">
            <h3 class="text-lg font-medium text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 gap-3">
                <button class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-medium transition-all shadow-[0_0_15px_rgba(59,130,246,0.3)] hover:shadow-[0_0_25px_rgba(59,130,246,0.5)] hover:-translate-y-0.5">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Event
                    </div>
                    <svg class="w-4 h-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                
                <button class="flex items-center justify-between p-4 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-medium transition-all hover:-translate-y-0.5">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        Scan QR Code
                    </div>
                </button>

                <button class="flex items-center justify-between p-4 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-medium transition-all hover:-translate-y-0.5">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        View Reports
                    </div>
                </button>

                <button class="flex items-center justify-between p-4 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-medium transition-all hover:-translate-y-0.5">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export Data
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Row 3: Activity & Live Gates -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity Timeline -->
        <div class="glassmorphism rounded-2xl border border-slate-700/50 p-6 stagger-7">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-white">Recent Activity</h3>
                <a href="#" class="text-sm text-blue-400 hover:text-blue-300">View all</a>
            </div>
            
            <div class="relative border-l border-slate-700 ml-3 space-y-6">
                <!-- Activity Item 1 -->
                <div class="relative pl-6 animate-slide-in-left" style="animation-delay: 0.1s; animation-fill-mode: both;">
                    <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] border-2 border-slate-900"></span>
                    <div class="flex flex-col">
                        <p class="text-sm text-slate-300"><span class="font-medium text-white">Sarah Jenkins</span> checked in at <span class="text-blue-400">Main Gate A</span></p>
                        <span class="text-xs text-slate-500 mt-1">2 mins ago</span>
                    </div>
                </div>

                <!-- Activity Item 2 -->
                <div class="relative pl-6 animate-slide-in-left" style="animation-delay: 0.2s; animation-fill-mode: both;">
                    <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)] border-2 border-slate-900"></span>
                    <div class="flex flex-col">
                        <p class="text-sm text-slate-300">New registration for <span class="font-medium text-white">Tech Summit 2024</span></p>
                        <span class="text-xs text-slate-500 mt-1">15 mins ago</span>
                    </div>
                </div>
                
                <!-- Activity Item 3 -->
                <div class="relative pl-6 animate-slide-in-left" style="animation-delay: 0.3s; animation-fill-mode: both;">
                    <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)] border-2 border-slate-900"></span>
                    <div class="flex flex-col">
                        <p class="text-sm text-slate-300">Invalid ticket attempt at <span class="text-blue-400">VIP Entrance</span></p>
                        <span class="text-xs text-slate-500 mt-1">45 mins ago</span>
                    </div>
                </div>
                
                <!-- Activity Item 4 -->
                <div class="relative pl-6 animate-slide-in-left" style="animation-delay: 0.4s; animation-fill-mode: both;">
                    <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)] border-2 border-slate-900"></span>
                    <div class="flex flex-col">
                        <p class="text-sm text-slate-300"><span class="font-medium text-white">System Admin</span> updated gate configuration</p>
                        <span class="text-xs text-slate-500 mt-1">2 hours ago</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Gate Activity -->
        <div class="glassmorphism rounded-2xl border border-slate-700/50 p-6 flex flex-col stagger-8 h-[400px]">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center">
                    <h3 class="text-lg font-medium text-white mr-3">Live Scanner Feed</h3>
                    <x-badge type="danger" text="LIVE" pulse="true" />
                </div>
                <span class="text-xs text-slate-400">Auto-scrolling</span>
            </div>
            
            <!-- Feed stream -->
            <div class="flex-1 overflow-hidden relative rounded-xl bg-slate-800/30 border border-slate-700/50 p-2">
                <div class="absolute inset-x-0 top-0 h-8 bg-gradient-to-b from-slate-800/80 to-transparent z-10 pointer-events-none"></div>
                <div class="absolute inset-x-0 bottom-0 h-8 bg-gradient-to-t from-slate-800/80 to-transparent z-10 pointer-events-none"></div>
                
                <div class="space-y-2 h-full overflow-y-auto custom-scrollbar p-2 pb-8">
                    <!-- Stream items -->
                    <div class="p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center mr-3 text-emerald-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white font-medium">TKT-8942-A</p>
                            <p class="text-xs text-slate-400">Gate 2 • Just now</p>
                        </div>
                        <span class="text-xs text-emerald-400 bg-emerald-500/20 px-2 py-1 rounded">Granted</span>
                    </div>

                    <div class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-rose-500/20 flex items-center justify-center mr-3 text-rose-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white font-medium">TKT-1123-X</p>
                            <p class="text-xs text-slate-400">Gate 1 • 12s ago</p>
                        </div>
                        <span class="text-xs text-rose-400 bg-rose-500/20 px-2 py-1 rounded">Denied: Already Scanned</span>
                    </div>

                    <div class="p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center mr-3 text-emerald-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white font-medium">TKT-9931-B</p>
                            <p class="text-xs text-slate-400">VIP Gate • 45s ago</p>
                        </div>
                        <span class="text-xs text-emerald-400 bg-emerald-500/20 px-2 py-1 rounded">Granted</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Upcoming Events Table -->
    <div class="stagger-8">
        <h3 class="text-lg font-medium text-white mb-4 mt-2">Upcoming Events</h3>
        <x-data-table>
            <x-slot:thead>
                <th scope="col" class="px-6 py-4 font-medium">Event Name</th>
                <th scope="col" class="px-6 py-4 font-medium">Date</th>
                <th scope="col" class="px-6 py-4 font-medium">Status</th>
                <th scope="col" class="px-6 py-4 font-medium">Capacity</th>
                <th scope="col" class="px-6 py-4 font-medium text-right">Actions</th>
            </x-slot:thead>
            
            <!-- Row 1 -->
            <tr class="hover:bg-slate-800/50 transition-colors group">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 mr-3 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">Global Tech Summit 2024</p>
                            <p class="text-xs text-slate-400">Main Conference Hall</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                    Tomorrow, 09:00 AM
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <x-badge type="info" text="Upcoming" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="w-full bg-slate-700 rounded-full h-1.5 mb-1">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: 85%"></div>
                    </div>
                    <p class="text-xs text-slate-400">850 / 1000</p>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button class="text-slate-400 hover:text-white transition-colors p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <button class="text-slate-400 hover:text-white transition-colors p-1 ml-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                    </button>
                </td>
            </tr>

            <!-- Row 2 -->
            <tr class="hover:bg-slate-800/50 transition-colors group">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 mr-3 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">Marketing Workshop</p>
                            <p class="text-xs text-slate-400">Room 304</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                    Oct 24, 14:00 PM
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <x-badge type="success" text="Live Now" pulse="true" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="w-full bg-slate-700 rounded-full h-1.5 mb-1">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 40%"></div>
                    </div>
                    <p class="text-xs text-slate-400">40 / 100</p>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button class="text-slate-400 hover:text-white transition-colors p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <button class="text-slate-400 hover:text-white transition-colors p-1 ml-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                    </button>
                </td>
            </tr>
        </x-data-table>
    </div>
</div>
@endsection
