<div class="glassmorphism rounded-xl overflow-hidden shadow-lg border border-slate-700/50">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="text-xs uppercase bg-slate-800/80 text-slate-400 border-b border-slate-700/50">
                <tr>
                    {{ $thead }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                {{ $tbody ?? $slot }}
            </tbody>
        </table>
    </div>
</div>
