@props(['type' => 'info', 'text', 'pulse' => false])

@php
    $colors = [
        'success' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.2)]',
        'warning' => 'bg-amber-500/10 text-amber-400 border-amber-500/20 shadow-[0_0_10px_rgba(245,158,11,0.2)]',
        'danger' => 'bg-rose-500/10 text-rose-400 border-rose-500/20 shadow-[0_0_10px_rgba(244,63,94,0.2)]',
        'info' => 'bg-blue-500/10 text-blue-400 border-blue-500/20 shadow-[0_0_10px_rgba(59,130,246,0.2)]',
        'gray' => 'bg-slate-500/10 text-slate-400 border-slate-500/20 shadow-[0_0_10px_rgba(100,116,139,0.2)]',
    ];
    $colorClass = $colors[$type] ?? $colors['info'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border $colorClass"]) }}>
    @if($pulse)
        <span class="flex h-2 w-2 relative mr-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ str_replace('text-', 'bg-', explode(' ', $colorClass)[1]) }}"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 {{ str_replace('text-', 'bg-', explode(' ', $colorClass)[1]) }}"></span>
        </span>
    @endif
    {{ $text ?? $slot }}
</span>
