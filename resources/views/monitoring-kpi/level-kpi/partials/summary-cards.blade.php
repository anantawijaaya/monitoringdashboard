<!-- 1. TOP KPI SUMMARY METRIC CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
    
    <!-- CARD 1: MTD REV ALL -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
        <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-bold uppercase tracking-wider">TOTAL MTD REV ALL</span>
            <i class="bi bi-currency-dollar text-red-400 text-base"></i>
        </div>
        <div class="mt-3">
            <h3 class="text-lg font-outfit font-black text-white">Rp {{ number_format($totalMtdRevAll / 1000000000, 2, ',', '.') }} B</h3>
            <div class="flex items-center gap-1.5 mt-1">
                <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded {{ $avgAchRevAll >= 100 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                    {{ $avgAchRevAll }}% Ach
                </span>
                <span class="text-[10px] text-slate-400 font-medium">Target Rev All</span>
            </div>
        </div>
    </div>

    <!-- CARD 2: MTD BROADBAND -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
        <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-bold uppercase tracking-wider">MTD BROADBAND</span>
            <i class="bi bi-wifi text-blue-400 text-base"></i>
        </div>
        <div class="mt-3">
            <h3 class="text-lg font-outfit font-black text-white">Rp {{ number_format($totalMtdBb / 1000000000, 2, ',', '.') }} B</h3>
            <div class="flex items-center gap-1.5 mt-1">
                <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-400">
                    {{ $avgAchBb }}% Ach
                </span>
                <span class="text-[10px] text-slate-400 font-medium">Broadband</span>
            </div>
        </div>
    </div>

    <!-- CARD 3: MTD REDEEM PV -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
        <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-bold uppercase tracking-wider">MTD REDEEM PV</span>
            <i class="bi bi-gift-fill text-purple-400 text-base"></i>
        </div>
        <div class="mt-3">
            <h3 class="text-lg font-outfit font-black text-white">Rp {{ number_format($totalMtdPv / 1000000000, 2, ',', '.') }} B</h3>
            <div class="flex items-center gap-1.5 mt-1">
                <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-400">
                    {{ $avgAchPv }}% Ach
                </span>
                <span class="text-[10px] text-slate-400 font-medium">Redeem PV</span>
            </div>
        </div>
    </div>

    <!-- CARD 4: MTD REVENUE RGB -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
        <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-bold uppercase tracking-wider">MTD REVENUE RGB</span>
            <i class="bi bi-arrow-up-right-circle-fill text-emerald-400 text-base"></i>
        </div>
        <div class="mt-3">
            <h3 class="text-lg font-outfit font-black text-white">Rp {{ number_format($totalMtdRgb / 1000000000, 2, ',', '.') }} B</h3>
            <div class="flex items-center gap-1.5 mt-1">
                <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">
                    {{ $avgAchRgb }}% Ach
                </span>
                <span class="text-[10px] text-slate-400 font-medium">RGB Revenue</span>
            </div>
        </div>
    </div>

    <!-- CARD 5: MOM GROWTH -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
        <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-bold uppercase tracking-wider">RATA-RATA MoM</span>
            <i class="bi bi-graph-up-arrow text-amber-400 text-base"></i>
        </div>
        <div class="mt-3">
            <h3 class="text-lg font-outfit font-black text-white">{{ $avgGrowthMoM > 0 ? '+' : '' }}{{ $avgGrowthMoM }}%</h3>
            <div class="flex items-center gap-1.5 mt-1">
                <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-400">
                    Growth Rev
                </span>
                <span class="text-[10px] text-slate-400 font-medium">MoM Performance</span>
            </div>
        </div>
    </div>

    <!-- CARD 6: RATIO PJP -->
    <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
        <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-bold uppercase tracking-wider">RATIO PJP</span>
            <i class="bi bi-pin-map-fill text-rose-400 text-base"></i>
        </div>
        <div class="mt-3">
            <h3 class="text-lg font-outfit font-black text-white">{{ $avgRatioPjp }}%</h3>
            <div class="flex items-center gap-1.5 mt-1">
                <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-400">
                    Kepatuhan PJP
                </span>
                <span class="text-[10px] text-slate-400 font-medium">Rata-rata</span>
            </div>
        </div>
    </div>

</div>
