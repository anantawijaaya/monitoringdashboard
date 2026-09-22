@php
    $hasActiveFilter = !empty($search) || ($selectedBranch && $selectedBranch !== 'all') || ($selectedCluster && $selectedCluster !== 'all') || ($selectedFlag && $selectedFlag !== 'all');
@endphp

<!-- 2. TOP SECTION: 3 SUMMARY METRIC CARDS + 3 STACKED RED PILL ACTION BUTTONS (CENTER ALIGNED ON PAGE) -->
<div class="flex flex-wrap items-center justify-center gap-3.5 sm:gap-4 w-full mx-auto">
    
    <!-- CARD 1: TOTAL OUTLET  -->
    <div class="h-[130px] w-[300px] bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white px-5 sm:px-6 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all shrink-0">
        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/20 text-white flex items-center justify-center text-xl sm:text-2xl backdrop-blur-sm group-hover:scale-110 transition-transform shrink-0">
            <i class="bi bi-geo-alt-fill text-white"></i>
        </div>
        <div class="flex-1 text-center flex flex-col items-center justify-center">
            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">TOTAL OUTLET</span>
            <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white mt-1.5 leading-tight tracking-tight text-center">{{ number_format($totalOutlets, 0, ',', '.') }}</h3>
            <span class="text-xs text-gray-200 font-medium block mt-1 text-center">Tersebar di 12 Cluster</span>
        </div>
    </div>

    <!-- CARD 2: DYNAMIC FLAG OMZET -->
    <div class="h-[130px] w-[320px] bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white px-5 sm:px-6 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all shrink-0">
        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/20 text-white flex items-center justify-center text-xl sm:text-2xl backdrop-blur-sm group-hover:scale-110 transition-transform shrink-0">
            <i class="bi bi-bullseye text-white"></i>
        </div>
        <div class="flex-1 text-center flex flex-col items-center justify-center">
            <span class="text-xs sm:text-sm font-black text-white/90 uppercase tracking-wider block truncate max-w-[210px] sm:max-w-[240px] text-center">{{ $flagBoxTitle ?? 'SEMUA KATEGORI FLAG' }}</span>
            <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white mt-1.5 leading-tight tracking-tight text-center">{{ number_format($flagBoxCount ?? 0, 0, ',', '.') }}</h3>
            <span class="text-xs text-gray-200 font-medium block mt-1 text-center">Jumlah outlet terfilter</span>
        </div>
    </div>

    <!-- CARD 3: RATA-RATA OMZET  -->
    <div class="h-[130px] w-[350px] bg-gradient-to-br from-[#121212] via-[#1A1A1E] to-[#26262B] text-white px-5 sm:px-6 rounded-2xl shadow-card hover:shadow-card-hover border border-gray-800 relative overflow-hidden flex items-center gap-3.5 group transition-all shrink-0">
        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl sm:text-2xl backdrop-blur-sm group-hover:scale-110 transition-transform shrink-0">
            <i class="bi bi-cash-coin"></i>
        </div>
        <div class="flex-1 text-center flex flex-col items-center justify-center">
            <span class="text-xs sm:text-sm font-black text-white/90 uppercase tracking-wider block text-center">RATA-RATA OMZET</span>
            <h3 class="text-xl sm:text-3xl lg:text-3xl font-black text-white mt-1.5 leading-tight tracking-tight text-center">{{ $formattedAvgOmzet }}</h3>
            <span class="text-xs text-gray-400 font-medium block mt-1 text-center">Per Outlet</span>
        </div>
    </div>

    <!-- COLUMN 4: 3 STACKED RED PILL ACTION BUTTONS -->
    <div class="h-[130px] w-[200px] flex flex-col justify-between gap-1.5 shrink-0">
        <button type="button" 
                id="btnModeCluster" 
                onclick="setVisualMode('cluster')" 
                class="flex-1 w-full px-4 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center cursor-pointer">
            <span>Tampilan Titik Cluster</span>
        </button>

        <button type="button" 
                id="btnModePoints" 
                onclick="setVisualMode('points')" 
                class="flex-1 w-full px-4 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center cursor-pointer">
            <span>Tampilan Titik Outlet</span>
        </button>

        <button type="button" 
                id="btnToggleFilterBar"
                onclick="toggleFilterBar()" 
                class="flex-1 w-full px-4 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <i class="bi bi-funnel-fill text-xs"></i>
            <span id="btnToggleFilterText">{{ $hasActiveFilter ? 'Sembunyikan Filter' : 'Tampilkan Filter' }}</span>
        </button>
    </div>

</div>
