<!-- 5 MAIN REVENUE BOXES (DATA-DRIVEN FROM DATABASE) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5">

    <!-- BOX 1: REVENUE ALL (MTD) - RED BACKGROUND -->
    <div class="metric-card bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
        <div>
            <!-- Header Label -->
            <div>
                <span class="text-xs sm:text-sm font-black text-white/90 text-center uppercase tracking-wider block">REVENUE ALL (MTD)</span>
            </div>

            <!-- Big Main Value with extra generous gap -->
            <div class="mt-8 sm:mt-10 flex items-baseline gap-2">
                <span class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-none">{{ $revenueData['all']['value'] }}</span>
                <span class="text-sm sm:text-base font-bold text-white/80">Miliar</span>
            </div>

            <!-- Target & Pill Row -->
            <div class="flex items-center justify-between text-xs sm:text-sm mt-3.5">
                <span class="text-white/90 font-medium">Target: <strong class="text-white font-black text-xs sm:text-sm">{{ $revenueData['all']['target'] }} M</strong></span>
                <span class="px-2.5 py-0.5 rounded-lg text-xs sm:text-sm font-black bg-white text-[#ED1C24] shadow-sm">{{ $revenueData['all']['achievement'] }}%</span>
            </div>

            <!-- White Progress Bar -->
            <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden mt-2">
                <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ min(100, $revenueData['all']['achievement']) }}%;"></div>
            </div>
        </div>

        <!-- Bottom Stats Row -->
        <div class="flex items-center justify-between text-xs sm:text-[13px] pt-3 mt-3.5 border-white/15">
            <span class="flex items-center gap-1.5 text-white font-bold">
            </span>
        </div>
    </div>

    <!-- BOX 2: REVENUE BROADBAND - RED BACKGROUND -->
    <div class="metric-card bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
        <div>
            <!-- Header Label -->
            <div>
                <span class="text-xs sm:text-sm font-black text-white/90 uppercase text-center tracking-wider block">REVENUE BROADBAND</span>
            </div>

            <!-- Big Main Value with extra generous gap -->
            <div class="mt-8 sm:mt-10 flex items-baseline gap-2">
                <span class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-none">{{ $revenueData['broadband']['value'] }}</span>
                <span class="text-sm sm:text-base font-bold text-white/80">Miliar</span>
            </div>

            <!-- Target & Pill Row -->
            <div class="flex items-center justify-between text-xs sm:text-sm mt-3.5">
                <span class="text-white/90 font-medium">Target: <strong class="text-white font-black text-xs sm:text-sm">{{ $revenueData['broadband']['target'] }} M</strong></span>
                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-white text-[#ED1C24] shadow-sm">{{ $revenueData['broadband']['achievement'] }}%</span>
            </div>

            <!-- White Progress Bar -->
            <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden mt-2">
                <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ min(100, $revenueData['broadband']['achievement']) }}%;"></div>
            </div>
        </div>
    </div>

    <!-- BOX 3: REVENUE REDEEM PV - RED BACKGROUND -->
    <div class="metric-card bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
        <div>
            <!-- Header Label -->
            <div>
                <span class="text-xs sm:text-sm font-black text-white/90 uppercase text-center tracking-wider block">REVENUE REDEEM PV</span>
            </div>

            <!-- Big Main Value with extra generous gap -->
            <div class="mt-8 sm:mt-10 flex items-baseline gap-2">
                <span class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-none">{{ $revenueData['redeem_pv']['value'] }}</span>
                <span class="text-sm sm:text-base font-bold text-white/80">Miliar</span>
            </div>

            <!-- Target & Pill Row -->
            <div class="flex items-center justify-between text-xs sm:text-sm mt-3.5">
                <span class="text-white/90 font-medium">Target: <strong class="text-white font-black text-xs sm:text-sm">{{ $revenueData['redeem_pv']['target'] }} M</strong></span>
                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-white text-[#ED1C24] shadow-sm">{{ $revenueData['redeem_pv']['achievement'] }}%</span>
            </div>

            <!-- White Progress Bar -->
            <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden mt-2">
                <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ min(100, $revenueData['redeem_pv']['achievement']) }}%;"></div>
            </div>
        </div>
    </div>

    <!-- BOX 4: REVENUE RGB ALL - RED BACKGROUND -->
    <div class="metric-card bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
        <div>
            <!-- Header Label -->
            <div>
                <span class="text-xs sm:text-sm font-black text-white/90 uppercase text-center tracking-wider block">REVENUE RGB ALL</span>
            </div>

            <!-- Big Main Value with extra generous gap -->
            <div class="mt-8 sm:mt-10 flex items-baseline gap-2">
                <span class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-none">{{ $revenueData['rgb']['value'] }}</span>
                <span class="text-sm sm:text-base font-bold text-white/80">Miliar</span>
            </div>

            <!-- Target & Pill Row -->
            <div class="flex items-center justify-between text-xs sm:text-sm mt-3.5">
                <span class="text-white/90 font-medium">Target: <strong class="text-white font-black text-xs sm:text-sm">{{ $revenueData['rgb']['target'] }} M</strong></span>
                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-white text-[#ED1C24] shadow-sm">{{ $revenueData['rgb']['achievement'] }}%</span>
            </div>

            <!-- White Progress Bar -->
            <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden mt-2">
                <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ min(100, $revenueData['rgb']['achievement']) }}%;"></div>
            </div>
        </div>
    </div>

    <!-- BOX 5: AVG GROWTH OMZET (CALCULATED FROM LINE CHART) -->
    <div class="metric-card bg-gradient-to-br from-[#121212] via-[#1A1A1E] to-[#26262B] text-white rounded-3xl p-5 sm:p-6 border border-gray-800 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
        <!-- Header Top Row -->
        <div class="flex items-center justify-between">
            <span class="text-xs sm:text-sm font-black text-white/90 uppercase tracking-wider block">AVG GROWTH OMZET</span>
            <div class="w-10 h-10 rounded-xl {{ $revenueData['growth']['is_positive'] ? 'text-white' : 'bg-white/5 text-white' }} flex items-center justify-center text-sm font-bold shrink-0">
                <i class="bi {{ $revenueData['growth']['is_positive'] ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }}"></i>
            </div>
        </div>

        <!-- Big Main Value (Clean & Perfectly Centered) -->
        <div class="my-auto py-6 sm:py-8 flex items-center justify-center text-center">
            <span class="text-4xl sm:text-5xl font-black {{ $revenueData['growth']['is_positive'] ? 'text-emerald-400' : 'text-rose-400' }} tracking-tight leading-none text-center">
                {{ $revenueData['growth']['rate'] }}
            </span>
        </div>
    </div>

</div>
