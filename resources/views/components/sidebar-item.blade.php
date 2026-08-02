@props(['route', 'params' => [], 'icon', 'label', 'active' => false])

@php
$active = $active || request()->routeIs($route . '*');
try {
    $url = Route::has($route) ? route($route, $params) : '#';
} catch (\Throwable $e) {
    $url = '#';
}
@endphp

<a href="{{ $url }}" 
   @click="if (window.innerWidth < 1024) sidebarOpen = false"
   class="group relative flex items-center px-3 py-2.5 rounded-lg transition-all duration-300 {{ $active ? 'bg-gradient-to-r from-blue-500/20 to-transparent text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' }}"
   :class="{ 'justify-center': !sidebarOpen }"
>
    <!-- Left Active Border Indicator -->
    @if($active)
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 rounded-r-full shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
    @endif
    
    <div class="flex items-center shrink-0" :class="{ 'mr-3': sidebarOpen }">
        {!! $icon !!}
    </div>
    
    <span class="font-medium whitespace-nowrap overflow-hidden transition-all duration-300"
          x-show="sidebarOpen"
          x-transition:enter="transition-opacity duration-300 delay-100"
          x-transition:enter-start="opacity-0"
          x-transition:enter-end="opacity-100"
          x-transition:leave="transition-opacity duration-100"
          x-transition:leave-start="opacity-100"
          x-transition:leave-end="opacity-0">
        {{ $label }}
    </span>

    <!-- Tooltip for collapsed state -->
    <div x-show="!sidebarOpen" 
         class="absolute left-full ml-4 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-lg border border-slate-700">
        {{ $label }}
        <div class="absolute left-[-4px] top-1/2 -translate-y-1/2 w-2 h-2 bg-slate-800 transform rotate-45 border-l border-b border-slate-700"></div>
    </div>
</a>
