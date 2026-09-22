<!-- TOP RED SUMMARY CARDS & RIGHT CONTROL PILLS -->
<div class="flex flex-wrap lg:flex-nowrap items-stretch justify-center gap-4 max-w-7xl mx-auto relative z-40">
    
    <!-- Card 1: Total Alokasi Budget -->
    <div class="bg-[#ED1C24] rounded-3xl p-5 text-white shadow-md relative overflow-hidden h-full flex flex-col items-center justify-center text-center w-80">
        <div class="flex items-center justify-center gap-2 text-white/90 w-full mb-3">
            <span class="text-[17px] uppercase font-extrabold tracking-wider">TOTAL ALOKASI BUDGET</span>
        </div>
        <div class="text-2xl font-black text-white my-1">
            Rp {{ number_format($totalAlokasi, 0, ',', '.') }}
        </div>
        <p class="text-xs text-white/80 font-medium">Program Direct Sales</p>
    </div>

    <!-- Card 2: Total Realisasi -->
    <div class="bg-[#ED1C24] rounded-3xl p-5 text-white shadow-md relative overflow-hidden h-full flex flex-col items-center justify-center text-center w-80">
        <div class="flex items-center justify-center gap-2 text-white/90 w-full mb-3">
            <span class="text-[17px] uppercase font-extrabold tracking-wider">TOTAL REALISASI</span>
        </div>
        <div class="text-2xl font-black text-white my-1">
            Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
        </div>
        <p class="text-xs text-white/80 font-medium">{{ $totalRealisasiPersen }}% dari Alokasi</p>
    </div>

    <!-- Card 3: Sisa Budget -->
    <div class="bg-[#ED1C24] rounded-3xl p-5 text-white shadow-md relative overflow-hidden h-full flex flex-col items-center justify-center text-center w-80">
        <div class="flex items-center justify-center gap-2 text-white/90 w-full mb-3">
            <span class="text-[17px] uppercase font-extrabold tracking-wider">SISA BUDGET</span>
        </div>
        <div class="text-2xl font-black text-white my-1">
            Rp {{ number_format($sisaBudget, 0, ',', '.') }}
        </div>
        <p class="text-xs text-white/80 font-medium">Siap Dialokasikan</p>
    </div>

    <!-- Right Stacked Control Buttons (Red Pills) -->
    <div class="flex flex-col items-center justify-start gap-2.5 w-auto relative z-40">
        
        <!-- Pill 1: Custom Filter 12 Cluster (Centered Text, Opens Downward) -->
        <div class="relative z-50" id="topClusterDropdownContainer">
            <button type="button" 
                    onclick="toggleTopClusterMenu(event)"
                    class="w-48 h-9 bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-extrabold rounded-xl shadow-sm hover:shadow transition-all relative flex items-center justify-center px-4 cursor-pointer">
                <span class="truncate text-center w-full pr-3">{{ ($selectedCluster ?? 'all') === 'all' ? 'Semua Cluster' : ($clusterMap[$selectedCluster] ?? $selectedCluster) }}</span>
                <i id="topClusterArrow" class="bi bi-chevron-down text-white text-xs transition-transform duration-200 absolute right-3.5 top-1/2 -translate-y-1/2"></i>
            </button>

            <!-- Exact w-48 width & z-50 centered list items -->
            <div id="topClusterMenu" 
                 class="hidden absolute top-full left-0 mt-1 w-48 bg-white border border-slate-200/90 rounded-2xl shadow-2xl py-1.5 z-50 max-h-60 overflow-y-auto text-center">
                <a href="?cluster=all&history_cluster={{ $selectedHistoryCluster ?? 'all' }}&program={{ $selectedProgram ?? 'all' }}&sort={{ $selectedSort ?? 'default' }}"
                   class="block px-4 py-2 text-xs font-bold text-center {{ ($selectedCluster ?? 'all') === 'all' ? 'text-red-600 bg-red-50/60 font-extrabold' : 'text-slate-800 hover:bg-slate-50' }} transition-colors">
                    Semua Cluster
                </a>
                @foreach($clusterMap as $cKey => $cItem)
                    @if($cKey !== 'all')
                        <a href="?cluster={{ urlencode($cKey) }}&history_cluster={{ $selectedHistoryCluster ?? 'all' }}&program={{ $selectedProgram ?? 'all' }}&sort={{ $selectedSort ?? 'default' }}"
                           class="block px-4 py-2 text-xs font-semibold text-center {{ ($selectedCluster ?? 'all') === $cKey ? 'text-red-600 bg-red-50/60 font-extrabold' : 'text-slate-800 hover:bg-slate-50' }} transition-colors">
                            {{ $cItem }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>


        <!-- Pill 3: Tombol Export Data -->
        <div class="relative z-10">
            <button type="button" onclick="openExportModal()" class="w-48 h-9 bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-extrabold rounded-xl shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Export Data</span>
            </button>
        </div>
    </div>

</div>

<script>
    function toggleTopClusterMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('topClusterMenu');
        const arrow = document.getElementById('topClusterArrow');
        const sortMenu = document.getElementById('topSortMenu');
        if (sortMenu) sortMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleTopSortMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('topSortMenu');
        const arrow = document.getElementById('topSortArrow');
        const clusterMenu = document.getElementById('topClusterMenu');
        if (clusterMenu) clusterMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const clusterContainer = document.getElementById('topClusterDropdownContainer');
        const clusterMenu = document.getElementById('topClusterMenu');
        const clusterArrow = document.getElementById('topClusterArrow');
        if (clusterContainer && !clusterContainer.contains(e.target) && clusterMenu && !clusterMenu.classList.contains('hidden')) {
            clusterMenu.classList.add('hidden');
            if (clusterArrow) clusterArrow.classList.remove('rotate-180');
        }

        const sortContainer = document.getElementById('topSortDropdownContainer');
        const sortMenu = document.getElementById('topSortMenu');
        const sortArrow = document.getElementById('topSortArrow');
        if (sortContainer && !sortContainer.contains(e.target) && sortMenu && !sortMenu.classList.contains('hidden')) {
            sortMenu.classList.add('hidden');
            if (sortArrow) sortArrow.classList.remove('rotate-180');
        }
    });
</script>
