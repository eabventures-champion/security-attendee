@props(['title' => '', 'maxWidth' => '2xl'])

@php
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-show="show"
    style="display: none;"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-50 overflow-y-auto"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"
        @click="show = false"
    ></div>

    <!-- Modal Panel -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-xl glassmorphism border border-slate-700/50 text-left shadow-xl transition-all sm:my-8 sm:w-full {{ $maxWidthClass }}"
        >
            @if($title)
            <div class="px-6 py-4 border-b border-slate-700/50 flex justify-between items-center">
                <h3 class="text-lg font-medium leading-6 text-white">{{ $title }}</h3>
                <button @click="show = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @endif
            
            <div class="px-6 py-4">
                {{ $slot }}
            </div>
            
            @if(isset($footer))
            <div class="px-6 py-4 border-t border-slate-700/50 bg-slate-800/30 flex justify-end space-x-3">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>
