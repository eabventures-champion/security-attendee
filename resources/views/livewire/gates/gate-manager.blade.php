<div class="space-y-8 font-inter">
@if(!$event)
    <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-3xl p-12 text-center shadow-xl space-y-4 max-w-2xl mx-auto my-12">
        <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center mx-auto">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">No Event Found</h2>
        <p class="text-slate-600 dark:text-slate-400 text-sm">You must create at least one event before configuring gates and access points.</p>
        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Your First Event
        </a>
    </div>
@else
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mt-3 sm:mt-0 mb-2 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                    EVENT-SPECIFIC ACCESS POINT
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 truncate max-w-[200px]">
                    {{ $event->name }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Gate Management</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1.5 mb-3 sm:mb-0 font-medium text-xs sm:text-sm">Configure access points, allowed ticket roles, and scanners specifically for <strong class="text-slate-900 dark:text-white">{{ $event->name }}</strong>.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2.5 w-full md:w-auto md:ml-auto shrink-0 mb-4 sm:mb-0">
            <!-- Event Switcher Dropdown -->
            <div class="flex items-center gap-2 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 px-3.5 py-2.5 rounded-xl shadow-sm hover:border-blue-500/30 transition-all w-full sm:w-auto">
                <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <select @change="$wire.switchEvent($event.target.value)" class="bg-transparent text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none cursor-pointer pr-2 w-full">
                    @foreach($allEvents as $evt)
                        <option value="{{ $evt->uuid }}" {{ $evt->id === $event->id ? 'selected' : '' }} class="bg-slate-900 text-slate-100 font-medium">
                            Event: {{ $evt->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @php
                $user = auth()->user();
                $isSecurity = $user && $user->isSecurityPersonnel();
            @endphp

            @if(!$isSecurity)
                <!-- Add New Gate Button -->
                <button wire:click="createGate" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer text-xs sm:text-sm shrink-0 mb-1 sm:mb-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add New Gate
                </button>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-semibold text-sm flex items-center">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 font-semibold text-sm flex items-center">
            <svg class="w-5 h-5 mr-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Gates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($gates as $gate)
            <div class="bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dark:shadow-2xl p-6 flex flex-col justify-between hover:border-blue-500/40 transition-all group">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        @if(!$isSecurity)
                            <div class="flex space-x-1">
                                <button wire:click="editGate('{{ $gate->uuid }}')" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-500/10 transition-colors cursor-pointer" title="Edit Gate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button wire:click="deleteGate('{{ $gate->uuid }}')" wire:confirm="Are you sure you want to delete this gate?" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer" title="Delete Gate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500 dark:text-blue-400">
                            Bound Event: {{ $event->name }}
                        </span>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $gate->name }}</h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-4">{{ $gate->location ?? 'Location not specified' }}</p>

                    <div class="space-y-3 mb-4">
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Allowed Ticket Roles</div>
                        <div class="flex flex-wrap gap-1.5">
                            @php
                                $roles = is_array($gate->allowed_roles) ? $gate->allowed_roles : json_decode($gate->allowed_roles ?? '[]', true);
                            @endphp
                            @if(empty($roles))
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/10">All Access</span>
                            @else
                                @foreach($roles as $role)
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                        {{ $role === 'general_admission' ? 'General' : ($role === 'vip' ? 'Vip' : ucfirst(str_replace('_', ' ', $role))) }}
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Assigned Security Personnel -->
                    @php
                        $assignedUsers = $gate->assignedSecurityUsers ?? collect();
                        $assignedAttendees = $gate->assignedSecurityAttendees ?? collect();
                        $totalGuards = $assignedUsers->count() + $assignedAttendees->count();
                    @endphp
                    <div class="pt-3 border-t border-slate-100 dark:border-white/10 space-y-1.5 mb-2">
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center justify-between">
                            <span>Assigned Security Guards</span>
                            <span class="text-amber-500 font-extrabold">{{ $totalGuards }}</span>
                        </div>
                        @if($totalGuards > 0)
                            <div class="flex flex-wrap gap-1.5 pt-0.5">
                                @foreach($assignedUsers as $secGuard)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20" title="{{ $secGuard->email }}">
                                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        {{ $secGuard->name }}
                                    </span>
                                @endforeach
                                @foreach($assignedAttendees as $secGuard)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        {{ $secGuard->full_name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[11px] text-slate-400 font-medium italic">No security personnel assigned to this gate yet.</p>
                        @endif
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex justify-between items-center">
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $gate->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20' }}">
                        {{ $gate->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <a href="{{ route('scanner.gate', ['eventUuid' => $event->uuid, 'gateUuid' => $gate->uuid]) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-bold flex items-center gap-1">
                        Open Scanner 
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-white/5 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-12 text-center shadow-sm dark:shadow-2xl">
                <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No gates configured for {{ $event->name }}</h3>
                <p class="text-slate-600 dark:text-slate-400 mb-6 font-medium text-sm">Create gates to manage specific access points for {{ $event->name }}.</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="createDefaultGate" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm border border-white/10 cursor-pointer">Create Primary Gate</button>
                    <button wire:click="createGate" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold text-sm shadow-lg shadow-blue-500/25 cursor-pointer">Add New Custom Gate</button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modal Form for Add/Edit Gate -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-3 sm:p-4 pt-8 sm:pt-4 overflow-y-auto bg-slate-950/70 backdrop-blur-md animate-fadeInUp">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl sm:rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden my-4 sm:my-auto">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-white/10 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $isEditing ? 'Edit Gate' : 'Add New Gate' }}</h3>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveGate" class="p-6 space-y-4">
                    <div class="px-3.5 py-2.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-xs font-semibold text-blue-600 dark:text-blue-400 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Target Event: <strong>{{ $event->name }}</strong></span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Gate Name <span class="text-rose-500">*</span></label>
                        <input wire:model="name" type="text" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium" placeholder="e.g. VIP Entrance Gate 1">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Location / Description</label>
                        <input wire:model="location" type="text" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium" placeholder="e.g. North Hall Entrance">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Allowed Access Roles
                            </label>
                            <button type="button" wire:click="toggleAllRoles" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                                {{ count($allowed_roles) >= 11 ? 'Deselect All' : 'Select All Roles' }}
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl">
                            @foreach(['general_admission', 'vip', 'vvip', 'speaker', 'exhibitor', 'sponsor', 'staff', 'volunteer', 'media', 'organizer', 'security'] as $roleOption)
                                <label class="flex items-center space-x-2 text-xs text-slate-700 dark:text-slate-300 font-medium cursor-pointer">
                                    <input type="checkbox" wire:model="allowed_roles" value="{{ $roleOption }}" class="rounded border-slate-300 dark:border-white/20 text-blue-600 focus:ring-blue-500">
                                    <span>{{ $roleOption === 'general_admission' ? 'General' : ($roleOption === 'vip' ? 'Vip' : ($roleOption === 'vvip' ? 'VVIP' : ucfirst(str_replace('_', ' ', $roleOption)))) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center pt-2">
                        <input wire:model="is_active" type="checkbox" id="is_active_gate" class="w-4 h-4 rounded bg-slate-100 dark:bg-white/5 border-slate-300 dark:border-white/20 text-blue-600 focus:ring-blue-500/50 cursor-pointer">
                        <label for="is_active_gate" class="ml-2 text-sm text-slate-700 dark:text-slate-300 font-medium cursor-pointer">Gate is Active for Scanning</label>
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-white/10 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                        <button type="button" wire:click="closeModal" class="w-full sm:w-auto px-4 py-2.5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 font-medium cursor-pointer text-center">Cancel</button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 cursor-pointer text-center">
                            {{ $isEditing ? 'Update Gate' : 'Add Gate' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endif
</div>
