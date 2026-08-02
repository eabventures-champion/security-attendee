<div class="space-y-8 font-inter">
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fadeInUp">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-emerald-400">
                    Resource Center & Support
                </h1>
                @if($isSuperAdmin)
                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase tracking-widest">
                        SUPER ADMIN MANAGEMENT
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase tracking-widest">
                        WORKSPACE RESOURCES
                    </span>
                @endif
            </div>
            <p class="text-slate-600 dark:text-slate-400 mt-1 text-sm font-medium">
                Access system updates, workspace guides, and communicate directly with system administration.
            </p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            @if($isSuperAdmin)
                <button type="button" wire:click="openResourceModal" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Publish Update / Resource</span>
                </button>
            @else
                <button type="button" wire:click="openFeedbackModal" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>Send Feedback / Request</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Flash Alert -->
    @if(session()->has('resource_success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-bold flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('resource_success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-400 hover:text-white"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    @endif

    <!-- Navigation Tabs & Search Controls -->
    <div class="bg-white dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-6 shadow-xl space-y-6 animate-fadeInUp">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 gap-4">
            <div class="flex items-center gap-2 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700/60 self-start">
                <button type="button" wire:click="setTab('updates')" class="px-4 py-2 rounded-xl text-xs font-black transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'updates' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span>System Updates & Guides</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $activeTab === 'updates' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ count($resources) }}
                    </span>
                </button>

                <button type="button" wire:click="setTab('feedbacks')" class="px-4 py-2 rounded-xl text-xs font-black transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'feedbacks' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>{{ $isSuperAdmin ? 'Feedback & Complaints (Admin Review)' : 'My Feedback & Requests' }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $activeTab === 'feedbacks' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ count($feedbacks) }}
                    </span>
                </button>
            </div>
        </div>

        @if($activeTab === 'updates')
            <!-- Updates Filtering Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto custom-scrollbar pb-2 sm:pb-0">
                    <button type="button" wire:click="$set('selectedCategory', 'all')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border whitespace-nowrap {{ $selectedCategory === 'all' ? 'bg-blue-500/20 text-blue-400 border-blue-500/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 border-slate-700 hover:text-white' }}">
                        All Updates
                    </button>
                    <button type="button" wire:click="$set('selectedCategory', 'guide')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border whitespace-nowrap {{ $selectedCategory === 'guide' ? 'bg-purple-500/20 text-purple-400 border-purple-500/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 border-slate-700 hover:text-white' }}">
                        📖 Workspace Guide
                    </button>
                    <button type="button" wire:click="$set('selectedCategory', 'update')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border whitespace-nowrap {{ $selectedCategory === 'update' ? 'bg-cyan-500/20 text-cyan-400 border-cyan-500/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 border-slate-700 hover:text-white' }}">
                        📢 Updates
                    </button>
                    <button type="button" wire:click="$set('selectedCategory', 'announcement')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border whitespace-nowrap {{ $selectedCategory === 'announcement' ? 'bg-amber-500/20 text-amber-400 border-amber-500/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 border-slate-700 hover:text-white' }}">
                        🔔 Announcements
                    </button>
                    <button type="button" wire:click="$set('selectedCategory', 'feature')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all border whitespace-nowrap {{ $selectedCategory === 'feature' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 border-slate-700 hover:text-white' }}">
                        ✨ Features
                    </button>
                </div>

                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.300ms="searchResource" placeholder="Search updates..." class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-1.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- Resources Cards Grid -->
            <div class="grid grid-cols-1 gap-6">
                @forelse($resources as $res)
                    <div class="bg-slate-50 dark:bg-slate-900/90 border {{ $res->pinned ? 'border-blue-500/60 ring-1 ring-blue-500/30' : 'border-slate-200 dark:border-slate-800' }} rounded-3xl p-6 shadow-xl space-y-4 transition-all hover:border-blue-500/40">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800/80 pb-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($res->pinned)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/20 text-blue-400 border border-blue-500/40 uppercase tracking-widest flex items-center gap-1">
                                        📌 PINNED
                                    </span>
                                @endif

                                @php
                                    $catColor = match($res->category) {
                                        'guide' => 'purple',
                                        'announcement' => 'amber',
                                        'feature' => 'emerald',
                                        'maintenance' => 'rose',
                                        default => 'cyan'
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-{{ $catColor }}-500/20 text-{{ $catColor }}-400 border border-{{ $catColor }}-500/40 uppercase tracking-widest">
                                    {{ strtoupper($res->category) }}
                                </span>

                                @if($res->priority === 'high' || $res->priority === 'important')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-400 border border-rose-500/40 uppercase tracking-widest">
                                        🔥 {{ strtoupper($res->priority) }}
                                    </span>
                                @endif

                                @if($isSuperAdmin && !$res->is_published)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-500/20 text-slate-400 border border-slate-500/40 uppercase tracking-widest">
                                        DRAFT (UNPUBLISHED)
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                                <span>Published {{ $res->created_at->format('M j, Y • g:i A') }}</span>
                                @if($isSuperAdmin)
                                    <div class="flex items-center gap-1.5 ml-2 border-l border-slate-700 pl-3">
                                        <button type="button" wire:click="toggleResourcePin({{ $res->id }})" class="p-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors" title="Toggle Pin">
                                            {{ $res->pinned ? '📌' : '📍' }}
                                        </button>
                                        <button type="button" wire:click="openResourceModal({{ $res->id }})" class="p-1 rounded bg-slate-800 hover:bg-slate-700 text-blue-400 transition-colors" title="Edit Update">
                                            ✏️
                                        </button>
                                        <button type="button" wire:click="deleteResource({{ $res->id }})" wire:confirm="Are you sure you want to delete this resource?" class="p-1 rounded bg-slate-800 hover:bg-rose-900/50 text-rose-400 transition-colors" title="Delete">
                                            🗑️
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-3">{{ $res->title }}</h3>
                            <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line font-medium space-y-2">
                                {!! nl2br(e($res->content)) !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center border-2 border-dashed border-slate-300 dark:border-slate-800 rounded-3xl text-slate-400 font-medium">
                        No system updates or guides found matching your selection.
                    </div>
                @endforelse
            </div>

        @elseif($activeTab === 'feedbacks')
            <!-- Feedbacks Filtering Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto custom-scrollbar pb-2 sm:pb-0">
                    <select wire:model.live="selectedType" class="bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-900 dark:text-white font-bold focus:outline-none">
                        <option value="all">All Types</option>
                        <option value="feedback">💬 Feedback</option>
                        <option value="suggestion">💡 Suggestion</option>
                        <option value="request">🚀 Feature Request</option>
                        <option value="complaint">⚠️ Complaint</option>
                    </select>

                    <select wire:model.live="selectedStatus" class="bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-900 dark:text-white font-bold focus:outline-none">
                        <option value="all">All Statuses</option>
                        <option value="pending">⏳ Pending Review</option>
                        <option value="under_review">🔍 Under Review</option>
                        <option value="resolved">✅ Resolved</option>
                        <option value="dismissed">❌ Dismissed</option>
                    </select>
                </div>

                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.300ms="searchFeedback" placeholder="Search feedback subject..." class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-1.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- Feedbacks List Grid -->
            <div class="grid grid-cols-1 gap-4">
                @forelse($feedbacks as $fb)
                    <div class="bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-lg space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                @php
                                    $typeBadge = match($fb->type) {
                                        'complaint' => ['bg' => 'rose', 'icon' => '⚠️', 'label' => 'COMPLAINT'],
                                        'suggestion' => ['bg' => 'purple', 'icon' => '💡', 'label' => 'SUGGESTION'],
                                        'request' => ['bg' => 'blue', 'icon' => '🚀', 'label' => 'FEATURE REQUEST'],
                                        default => ['bg' => 'emerald', 'icon' => '💬', 'label' => 'FEEDBACK']
                                    };

                                    $statusBadge = match($fb->status) {
                                        'resolved' => ['bg' => 'emerald', 'label' => 'RESOLVED'],
                                        'under_review' => ['bg' => 'blue', 'label' => 'UNDER REVIEW'],
                                        'dismissed' => ['bg' => 'slate', 'label' => 'DISMISSED'],
                                        default => ['bg' => 'amber', 'label' => 'PENDING REVIEW']
                                    };
                                @endphp

                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-{{ $typeBadge['bg'] }}-500/20 text-{{ $typeBadge['bg'] }}-400 border border-{{ $typeBadge['bg'] }}-500/30 uppercase tracking-wider flex items-center gap-1">
                                    <span>{{ $typeBadge['icon'] }}</span>
                                    <span>{{ $typeBadge['label'] }}</span>
                                </span>

                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-{{ $statusBadge['bg'] }}-500/20 text-{{ $statusBadge['bg'] }}-400 border border-{{ $statusBadge['bg'] }}-500/30 uppercase tracking-wider">
                                    {{ $statusBadge['label'] }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-slate-400 font-medium">
                                <span>Submitted {{ $fb->created_at->format('M j, Y • g:i A') }}</span>
                                @if($isSuperAdmin)
                                    <button type="button" wire:click="openResponseModal({{ $fb->id }})" class="px-3 py-1 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition-all cursor-pointer shadow">
                                        Review & Respond ➔
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <h4 class="font-extrabold text-slate-900 dark:text-white text-base">{{ $fb->subject }}</h4>
                                @if($isSuperAdmin)
                                    <span class="text-xs text-blue-400 font-bold">
                                        Org: {{ $fb->organization->name ?? 'System' }} ({{ $fb->user->name ?? 'User' }})
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium whitespace-pre-line">
                                {{ $fb->message }}
                            </p>
                        </div>

                        <!-- Super Admin Response Section if present -->
                        @if($fb->admin_response)
                            <div class="p-4 rounded-xl bg-blue-950/40 border border-blue-500/30 space-y-1.5 mt-3">
                                <div class="flex items-center justify-between text-[11px] font-extrabold text-blue-400">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Super Admin Official Response:
                                    </span>
                                    <span>{{ $fb->responded_at ? $fb->responded_at->format('M j, Y • g:i A') : '' }}</span>
                                </div>
                                <p class="text-xs text-blue-100 font-medium whitespace-pre-line">
                                    {{ $fb->admin_response }}
                                </p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-12 text-center border-2 border-dashed border-slate-300 dark:border-slate-800 rounded-3xl text-slate-400 font-medium">
                        No feedback items or requests found matching your filter criteria.
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    <!-- MODAL 1: Organization Admin Submit Feedback -->
    @if($showFeedbackModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fadeInUp">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl w-full max-w-lg shadow-2xl p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-white">Send Feedback or System Request</h3>
                    </div>
                    <button type="button" wire:click="closeFeedbackModal" class="text-slate-400 hover:text-white"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                <form wire:submit.prevent="submitFeedback" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Feedback Category</label>
                        <select wire:model="feedbackType" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="feedback">💬 General Feedback</option>
                            <option value="suggestion">💡 Feature Suggestion</option>
                            <option value="request">🚀 Custom System Request</option>
                            <option value="complaint">⚠️ Issue or Complaint</option>
                        </select>
                        @error('feedbackType') <span class="text-xs text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Subject</label>
                        <input type="text" wire:model="feedbackSubject" placeholder="Brief title summarizing your request or complaint..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('feedbackSubject') <span class="text-xs text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Message / Details</label>
                        <textarea wire:model="feedbackMessage" rows="5" placeholder="Provide full details, steps to reproduce, or requirements..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                        @error('feedbackMessage') <span class="text-xs text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-end gap-3">
                        <button type="button" wire:click="closeFeedbackModal" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 2: Super Admin Create / Edit Resource Update -->
    @if($showResourceModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fadeInUp">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl w-full max-w-xl shadow-2xl p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-purple-500/20 text-purple-400 border border-purple-500/30">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-white">{{ $editingResourceId ? 'Edit Resource / System Update' : 'Publish New System Resource & Update' }}</h3>
                    </div>
                    <button type="button" wire:click="closeResourceModal" class="text-slate-400 hover:text-white"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                <form wire:submit.prevent="saveResource" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Title</label>
                        <input type="text" wire:model="resourceTitle" placeholder="Update title..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('resourceTitle') <span class="text-xs text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Category</label>
                            <select wire:model="resourceCategory" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="guide">📖 Workspace Guide</option>
                                <option value="update">📢 System Update</option>
                                <option value="announcement">🔔 Announcement</option>
                                <option value="feature">✨ New Feature</option>
                                <option value="maintenance">🛠️ Maintenance</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Priority</label>
                            <select wire:model="resourcePriority" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="normal">Normal</option>
                                <option value="important">Important</option>
                                <option value="high">High Priority</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Content / Announcement Details</label>
                        <textarea wire:model="resourceContent" rows="6" placeholder="Write update details or instructions..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                        @error('resourceContent') <span class="text-xs text-rose-400 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-6 pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-300">
                            <input type="checkbox" wire:model="resourceIsPublished" class="rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-blue-500">
                            <span>Publish Immediately</span>
                        </label>

                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-300">
                            <input type="checkbox" wire:model="resourcePinned" class="rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-blue-500">
                            <span>Pin to Top of Resource Page</span>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-end gap-3">
                        <button type="button" wire:click="closeResourceModal" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg">Save & Publish</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL 3: Super Admin Respond to Feedback -->
    @if($showResponseModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fadeInUp">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl w-full max-w-lg shadow-2xl p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-white">Review & Respond to Feedback</h3>
                    </div>
                    <button type="button" wire:click="closeResponseModal" class="text-slate-400 hover:text-white"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                <form wire:submit.prevent="submitAdminResponse" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Update Status</label>
                        <select wire:model="adminResponseStatus" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs font-bold text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="pending">⏳ Pending Review</option>
                            <option value="under_review">🔍 Under Review</option>
                            <option value="resolved">✅ Resolved / Completed</option>
                            <option value="dismissed">❌ Dismissed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Super Admin Official Response Note</label>
                        <textarea wire:model="adminResponseText" rows="4" placeholder="Write feedback response or resolution notes to the organization admin..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-end gap-3">
                        <button type="button" wire:click="closeResponseModal" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg">Save Response</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
