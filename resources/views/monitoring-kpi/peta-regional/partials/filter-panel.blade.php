@php
    $hasActiveFilter = !empty($search) || ($selectedBranch && $selectedBranch !== 'all') || ($selectedCluster && $selectedCluster !== 'all') || ($selectedFlag && $selectedFlag !== 'all');
@endphp

<!-- 3. FILTER BAR WITH 4 INPUTS (HIDDEN BY DEFAULT WHEN MENU IS OPENED UNTIL "TAMPILKAN FILTER" IS CLICKED) -->
<div id="filterBarContainer" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-gray-200/20 shadow-sm transition-all duration-300 {{ $hasActiveFilter ? '' : 'hidden' }}">
    <form id="filterForm" method="GET" action="{{ route('regional-map.index') }}" class="flex flex-wrap items-center justify-center gap-3.5 sm:gap-4 w-full mx-auto">
        
        <!-- Search Input -->
        <div class="relative shrink-0">
            <input type="text" 
                   name="search" 
                   value="{{ $search }}" 
                   placeholder="Cari ID Outlet" 
                   class="w-90 pl-4 pr-10 py-2.5 text-xs font-medium rounded-xl border border-gray-200 focus:border-red-500 outline-none text-gray-800 bg-white">
            <i class="bi bi-search absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        <!-- Custom Branch Dropdown (Guaranteed Opens Downward) -->
        <div class="relative shrink-0 z-30" id="mapBranchDropdownContainer">
            <input type="hidden" name="branch" id="mapBranchInput" value="{{ $selectedBranch }}">
            <button type="button" 
                    onclick="toggleMapBranchMenu(event)"
                    class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer min-w-[160px]">
                <span>{{ $selectedBranch === 'all' ? 'Semua Branch' : $selectedBranch }}</span>
                <i id="mapBranchArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
            </button>

            <div id="mapBranchMenu" 
                 class="hidden absolute top-full left-0 mt-1.5 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                <a href="javascript:void(0)" onclick="submitMapFilter('branch', 'all')"
                   class="block px-4 py-2 text-xs font-bold {{ $selectedBranch === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                    Semua Branch
                </a>
                @foreach($availableBranches as $b)
                    <a href="javascript:void(0)" onclick="submitMapFilter('branch', '{{ $b }}')"
                       class="block px-4 py-2 text-xs font-semibold {{ $selectedBranch === $b ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                        {{ $b }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Custom Cluster Dropdown (Guaranteed Opens Downward) -->
        <div class="relative shrink-0 z-30" id="mapClusterDropdownContainer">
            <input type="hidden" name="cluster" id="mapClusterInput" value="{{ $selectedCluster }}">
            <button type="button" 
                    onclick="toggleMapClusterMenu(event)"
                    class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer min-w-[160px]">
                <span>{{ $selectedCluster === 'all' ? 'Semua Cluster' : $selectedCluster }}</span>
                <i id="mapClusterArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
            </button>

            <div id="mapClusterMenu" 
                 class="hidden absolute top-full left-0 mt-1.5 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                <a href="javascript:void(0)" onclick="submitMapFilter('cluster', 'all')"
                   class="block px-4 py-2 text-xs font-bold {{ $selectedCluster === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                    Semua Cluster
                </a>
                @foreach($availableClusters as $c)
                    <a href="javascript:void(0)" onclick="submitMapFilter('cluster', '{{ $c }}')"
                       class="block px-4 py-2 text-xs font-semibold {{ $selectedCluster === $c ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                        {{ $c }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Custom Flag Category Dropdown (Guaranteed Opens Downward) -->
        <div class="relative shrink-0 z-30" id="mapFlagDropdownContainer">
            <input type="hidden" name="flag" id="mapFlagInput" value="{{ $selectedFlag }}">
            @php
                $flagLabels = [
                    'all' => 'Semua Kategori Flag',
                    'black' => 'Flag < 0%',
                    'red' => 'Flag = 0%',
                    'orange' => 'Flag <= 3%',
                    'green' => 'Flag > 3%',
                ];
                $currentFlagLabel = $flagLabels[$selectedFlag ?? 'all'] ?? 'Semua Kategori Flag';
            @endphp
            <button type="button" 
                    onclick="toggleMapFlagMenu(event)"
                    class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer min-w-[170px]">
                <span>{{ $currentFlagLabel }}</span>
                <i id="mapFlagArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
            </button>

            <div id="mapFlagMenu" 
                 class="hidden absolute top-full left-0 mt-1.5 w-60 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                @foreach($flagLabels as $fKey => $fLabel)
                    <a href="javascript:void(0)" onclick="submitMapFilter('flag', '{{ $fKey }}')"
                       class="block px-4 py-2 text-xs {{ ($selectedFlag ?? 'all') === $fKey ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50 font-semibold' }} transition-colors">
                        {{ $fLabel }}
                    </a>
                @endforeach
            </div>
        </div>

    </form>
</div>
