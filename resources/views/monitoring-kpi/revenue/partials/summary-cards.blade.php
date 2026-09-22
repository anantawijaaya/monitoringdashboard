<!-- 4 SUMMARY STATS KPI CARDS (ICON LEFT, TEXT CENTERED) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Total Kabupaten (RED BACKGROUND MATCHING IMAGE DESIGN) -->
    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-4 sm:p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex flex-col justify-between group transition-all h-full">
        <!-- Icon Left Translucent Box -->


        <!-- Top Title & Main Value Centered -->
        <div class="w-full text-center flex flex-col items-center justify-center my-auto py-1">
            <span class="text-xl sm:text-xl font-black text-white uppercase tracking-wider block text-center leading-tight mt-1">
                TOTAL<br>KOTA/KABUPATEN
            </span>
            <h3 class="text-2xl sm:text-3xl lg:text-[34px] font-black text-white mt-10 text-center tracking-tight">
                {{ $totalRecords }} City
            </h3>
        </div>
    </div>

    <!-- Total Target Regional (RED BACKGROUND) -->
    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">

        <div class="flex-1 text-center flex flex-col items-center justify-center">
            <span class="text-xl sm:text-xl font-black text-white uppercase tracking-wider block text-center leading-tight mt-3"> Target Revenue</span>
            <h3 class="text-[32px] font-black text-white mt-14 text-center">Rp {{ number_format($totalTargetAll / 1000000000, 2, ',', '.') }} M</h3>
            <span class="text-[13px] text-white/80 font-medium block text-center"></span>
        </div>
    </div>

    <!-- Total MTD Realisasi (RED GRADIENT BACKGROUND MATCHING TARGET REVENUE BOX) -->
    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-4 sm:p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex flex-col justify-between group transition-all h-full">
        <!-- Icon Left Translucent Box -->


        <!-- Top Title, Amount & Achieved Pill Badge -->
        <div class="w-full text-center flex flex-col items-center justify-center pt-0.5">
            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">ACHIEVEMENT REVENUE</span>
            <h3 class="text-xl sm:text-2xl font-outfit font-black text-white mt-1 text-center tracking-tight">{{ \App\Models\ClusterRevenue::formatRevenueBm($totalMtdAll, true) }}</h3>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white text-[#ED1C24] text-[10px] font-black shadow-sm mt-1">
                <i class="bi bi-check2-circle text-[10px]"></i>
                {{ $avgAchAll }}% Achieved
            </span>
        </div>

        <!-- Bottom 3 White Sub-cards (Broadband, Redeem PV, RGB) -->
        <div class="grid grid-cols-3 gap-1 sm:gap-1.5 mt-3 w-full">
            <!-- Sub-card 1: Broadband -->
            <div class="bg-white rounded-xl p-1.5 sm:p-2 text-center flex flex-col items-center justify-between shadow-sm">
                <span class="text-[8px] sm:text-[9px] font-outfit font-black text-[#ED1C24] sentencecase tracking-tighter leading-tight text-center">Revenue Broadband</span>
                <span class="text-[10px] sm:text-xs font-outfit font-black text-[#ED1C24] mt-2 text-center">{{ \App\Models\ClusterRevenue::formatRevenueBm($totalMtdBroadband) }}</span>
                <span class="text-[4px] sm:text-xl font-outfit font-black text-[#ED1C24] mt-1 text-center">{{ number_format($portionMtdBb, 1, ',', '.') }}%</span>
            </div>

            <!-- Sub-card 2: Redeem PV -->
            <div class="bg-white rounded-xl p-1.5 sm:p-2 text-center flex flex-col items-center justify-between shadow-md">
                <span class="text-[8px] sm:text-[9px] font-outfit font-black text-[#ED1C24] sentencecase tracking-tighter leading-tight text-center">Revenue Redeem PV</span>
                <span class="text-[10px] sm:text-xs font-outfit font-black text-[#ED1C24] mt-2 text-center">{{ \App\Models\ClusterRevenue::formatRevenueBm($totalMtdRedeem) }}</span>
                <span class="text-[4px] sm:text-xl font-outfit font-black text-[#ED1C24] mt-1 text-center">{{ number_format($portionMtdRedeem, 1, ',', '.') }}%</span>
            </div>

            <!-- Sub-card 3: RGB -->
            <div class="bg-white rounded-xl p-1.5 sm:p-2 text-center flex flex-col items-center justify-between shadow-sm">
                <span class="text-[8px] sm:text-[9px] font-outfit font-black text-[#ED1C24] sentencecase tracking-tighter leading-tight text-center">Revenue</span>
                <span class="text-[8px] sm:text-[9px] font-outfit font-black text-[#ED1C24] sentencecase tracking-tighter leading-tight text-center">RGB</span>
                <span class="text-[10px] sm:text-xs font-outfit font-black text-[#ED1C24] mt-2 text-center">{{ \App\Models\ClusterRevenue::formatRevenueBm($totalMtdRgb) }}</span>
                <span class="text-[4px] sm:text-xl font-outfit font-black text-[#ED1C24] mt-1 text-center">{{ number_format($portionMtdRgb, 1, ',', '.') }}%</span>
            </div>
        </div>
    </div>

    <!-- Rata-rata MoM Growth (BLACK BACKGROUND) -->
    <div class="bg-gradient-to-br from-[#121212] via-[#1A1A1E] to-[#26262B] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border relative overflow-hidden flex items-center gap-3.5 group transition-all">
        <div class="w-12 h-12 rounded-2xl {{ $avgGrowthMoM >= 0 ? 'text-white' : 'text-white bg-white/5' }} flex items-center justify-center text-xl group-hover:scale-110 transition-transform shrink-0">
            <i class="bi {{ $avgGrowthMoM >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }}"></i>
        </div>
        <div class="flex-1 text-center flex flex-col items-center justify-center">
            <span class="text-[18px] font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">AVG MoM Growth</span>
            <h3 class="text-2xl sm:text-3xl font-black {{ $avgGrowthMoM >= 0 ? 'text-white' : 'text-white' }} mt-9 text-center">
                {{ $avgGrowthMoM >= 0 ? '+' : '' }}{{ $avgGrowthMoM }}%
            </h3>
        </div>
    </div>
</div>
