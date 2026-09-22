<!-- 4 SUMMARY STATS KPI CARDS (ICON LEFT - TEXT CENTERED) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
    
    <!-- CARD 1: TOTAL CLUSTER -->
    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">
        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shrink-0 backdrop-blur-sm">
            <i class="bi bi-building"></i>
        </div>
        <div class="flex-1 text-center flex flex-col items-center justify-center">
            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">Total Cluster</span>
            <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5 text-center">{{ $totalClusterCount }}</h3>
            <span class="text-[11px] text-white font-medium block text-center">Cluster Aktif</span>
        </div>
    </div>

    <!-- CARD 2: TOTAL MITRA -->
    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">
        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shrink-0 backdrop-blur-sm">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="flex-1 text-center flex flex-col items-center justify-center">
            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">Total Mitra</span>
            <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5 text-center">{{ $totalMitraCount }}</h3>
            <span class="text-[11px] text-white font-medium block text-center">Mitra Aktif</span>
        </div>
    </div>

    <!-- CARD 3: TOTAL OUTLET -->
    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">
        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shrink-0 backdrop-blur-sm">
            <i class="bi bi-shop"></i>
        </div>
        <div class="flex-1 text-center flex flex-col items-center justify-center">
            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">Total Outlet</span>
            <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5 text-center">{{ $totalOutletDisplay }}</h3>
            <span class="text-[11px] text-white font-medium block text-center">Sebaran {{ $totalKabupatenCount }} Kabupaten</span>
        </div>
    </div>

    <!-- CARD 4: TOTAL BRANCH -->
    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">
        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shrink-0 backdrop-blur-sm">
            <i class="bi bi-diagram-3-fill"></i>
        </div>
        <div class="flex-1 text-center flex flex-col items-center justify-center">
            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">Total Branch</span>
            <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5 text-center">{{ $totalBranchCount }}</h3>
            <span class="text-[11px] text-white font-medium block text-center">Branch Aktif</span>
        </div>
    </div>

</div>
