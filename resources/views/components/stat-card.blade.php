@props([
    'title', 
    'value', 
    'change' => null, 
    'changeType' => 'increase', 
    'icon' => null, 
    'color' => 'blue'
])

@php
$colorMap = [
    'blue' => ['bg' => 'bg-blue-500/10', 'border' => 'border-blue-500/30', 'text' => 'text-blue-400', 'iconBg' => 'bg-gradient-to-br from-blue-500 to-blue-600'],
    'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/30', 'text' => 'text-emerald-400', 'iconBg' => 'bg-gradient-to-br from-emerald-500 to-emerald-600'],
    'amber' => ['bg' => 'bg-amber-500/10', 'border' => 'border-amber-500/30', 'text' => 'text-amber-400', 'iconBg' => 'bg-gradient-to-br from-amber-500 to-amber-600'],
    'rose' => ['bg' => 'bg-rose-500/10', 'border' => 'border-rose-500/30', 'text' => 'text-rose-400', 'iconBg' => 'bg-gradient-to-br from-rose-500 to-rose-600'],
    'purple' => ['bg' => 'bg-purple-500/10', 'border' => 'border-purple-500/30', 'text' => 'text-purple-400', 'iconBg' => 'bg-gradient-to-br from-purple-500 to-purple-600'],
];

$theme = $colorMap[$color] ?? $colorMap['blue'];
@endphp

<div class="glassmorphism rounded-2xl p-6 relative overflow-hidden group hover:-translate-y-1 transition-all duration-300 border border-slate-700/50 hover:{{ $theme['border'] }} hover:shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:shadow-{{ $color }}-500/20">
    <!-- Background Glow Effect -->
    <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full {{ $theme['bg'] }} blur-2xl opacity-50 group-hover:opacity-100 transition-opacity duration-500"></div>

    <div class="flex justify-between items-start relative z-10">
        <div>
            <p class="text-sm font-medium text-slate-400 mb-1">{{ $title }}</p>
            <h3 class="text-3xl font-bold text-white tracking-tight animate-count-up">{{ $value }}</h3>
            
            @if($change)
                <div class="flex items-center mt-2 text-sm">
                    @if($changeType === 'increase')
                        <span class="text-emerald-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                            {{ $change }}
                        </span>
                    @else
                        <span class="text-rose-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" /></svg>
                            {{ $change }}
                        </span>
                    @endif
                    <span class="text-slate-500 ml-2">vs last month</span>
                </div>
            @endif
        </div>
        
        @if($icon)
            <div class="w-12 h-12 rounded-xl {{ $theme['iconBg'] }} flex items-center justify-center text-white shadow-lg shrink-0">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
