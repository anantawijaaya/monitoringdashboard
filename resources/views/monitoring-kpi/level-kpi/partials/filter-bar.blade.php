<!-- FILTER & ACTION BAR -->
<div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-xl space-y-4">
    <form id="levelKpiFilterForm" method="GET" action="{{ route('level-kpi.index') }}" class="flex flex-wrap items-center justify-between gap-3">
        
        <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
            <!-- Search Box -->
            <div class="relative w-full sm:w-64">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama Cluster..." 
                       class="w-full pl-9 pr-4 py-2 text-xs font-medium rounded-xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 focus:outline-none focus:border-red-500 transition-all">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <!-- Custom Cluster Dropdown -->
            <div class="relative shrink-0 z-30" id="levelClusterDropdownContainer">
                <input type="hidden" name="cluster" id="levelClusterInput" value="{{ $selectedCluster }}">
                <button type="button" 
                        onclick="toggleLevelClusterMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-slate-800/90 border border-slate-700 hover:bg-slate-800 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-200 focus:outline-none focus:border-red-500 cursor-pointer min-w-[145px]">
                    <span>{{ $selectedCluster === 'all' ? 'Semua Cluster' : $selectedCluster }}</span>
                    <i id="levelClusterArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="levelClusterMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-52 bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    <a href="javascript:void(0)" onclick="submitLevelFilter('cluster', 'all')"
                       class="block px-4 py-2 text-xs font-bold {{ $selectedCluster === 'all' ? 'text-red-400 bg-red-950/40 font-extrabold' : 'text-slate-200 hover:bg-slate-700/60' }} transition-colors">
                        Semua Cluster
                    </a>
                    @foreach($availableClusters as $c)
                        <a href="javascript:void(0)" onclick="submitLevelFilter('cluster', '{{ $c }}')"
                           class="block px-4 py-2 text-xs font-semibold {{ $selectedCluster === $c ? 'text-red-400 bg-red-950/40 font-extrabold' : 'text-slate-200 hover:bg-slate-700/60' }} transition-colors">
                            {{ $c }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Custom Period Dropdown -->
            <div class="relative shrink-0 z-30" id="levelPeriodDropdownContainer">
                <input type="hidden" name="period" id="levelPeriodInput" value="{{ $selectedPeriod }}">
                <button type="button" 
                        onclick="toggleLevelPeriodMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-slate-800/90 border border-slate-700 hover:bg-slate-800 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-200 focus:outline-none focus:border-red-500 cursor-pointer min-w-[145px]">
                    <span>{{ $selectedPeriod === 'all' ? 'Semua Periode' : 'Periode ' . $selectedPeriod }}</span>
                    <i id="levelPeriodArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="levelPeriodMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-52 bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    <a href="javascript:void(0)" onclick="submitLevelFilter('period', 'all')"
                       class="block px-4 py-2 text-xs font-bold {{ $selectedPeriod === 'all' ? 'text-red-400 bg-red-950/40 font-extrabold' : 'text-slate-200 hover:bg-slate-700/60' }} transition-colors">
                        Semua Periode
                    </a>
                    @foreach($availablePeriods as $p)
                        <a href="javascript:void(0)" onclick="submitLevelFilter('period', '{{ $p }}')"
                           class="block px-4 py-2 text-xs font-semibold {{ $selectedPeriod === $p ? 'text-red-400 bg-red-950/40 font-extrabold' : 'text-slate-200 hover:bg-slate-700/60' }} transition-colors">
                            Periode {{ $p }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Custom Status Dropdown -->
            <div class="relative shrink-0 z-30" id="levelStatusDropdownContainer">
                <input type="hidden" name="status" id="levelStatusInput" value="{{ $selectedStatus }}">
                @php
                    $levelStatusLabels = [
                        'all' => 'Semua Status Target',
                        'melampaui_target' => '🟢 Melampaui (>= 100%)',
                        'mencapai_target' => '🟠 Mencapai (90% - 99%)',
                        'tidak_mencapai_target' => '🔴 Tidak Mencapai (< 90%)',
                    ];
                    $currentLevelStatusLabel = $levelStatusLabels[$selectedStatus ?? 'all'] ?? 'Semua Status Target';
                @endphp
                <button type="button" 
                        onclick="toggleLevelStatusMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-slate-800/90 border border-slate-700 hover:bg-slate-800 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-200 focus:outline-none focus:border-red-500 cursor-pointer min-w-[170px]">
                    <span>{{ $currentLevelStatusLabel }}</span>
                    <i id="levelStatusArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="levelStatusMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-60 bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    @foreach($levelStatusLabels as $lsKey => $lsLabel)
                        <a href="javascript:void(0)" onclick="submitLevelFilter('status', '{{ $lsKey }}')"
                           class="block px-4 py-2 text-xs {{ ($selectedStatus ?? 'all') === $lsKey ? 'text-red-400 bg-red-950/40 font-extrabold' : 'text-slate-200 hover:bg-slate-700/60 font-semibold' }} transition-colors">
                            {{ $lsLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Custom Sort Dropdown -->
            <div class="relative shrink-0 z-30" id="levelSortDropdownContainer">
                <input type="hidden" name="sort" id="levelSortInput" value="{{ $sortBy }}">
                @php
                    $levelSortLabels = [
                        'name_asc' => 'Urutan: Nama Cluster A-Z',
                        'ach_desc' => 'Urutan: Ach Rev All Tertinggi',
                        'ach_asc' => 'Urutan: Ach Rev All Terendah',
                        'growth_desc' => 'Urutan: Growth MoM Tertinggi',
                    ];
                    $currentLevelSortLabel = $levelSortLabels[$sortBy ?? 'name_asc'] ?? 'Urutan: Nama Cluster A-Z';
                @endphp
                <button type="button" 
                        onclick="toggleLevelSortMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-slate-800/90 border border-slate-700 hover:bg-slate-800 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-200 focus:outline-none focus:border-red-500 cursor-pointer min-w-[180px]">
                    <span>{{ $currentLevelSortLabel }}</span>
                    <i id="levelSortArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="levelSortMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-60 bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    @foreach($levelSortLabels as $lsoKey => $lsoLabel)
                        <a href="javascript:void(0)" onclick="submitLevelFilter('sort', '{{ $lsoKey }}')"
                           class="block px-4 py-2 text-xs {{ ($sortBy ?? 'name_asc') === $lsoKey ? 'text-red-400 bg-red-950/40 font-extrabold' : 'text-slate-200 hover:bg-slate-700/60 font-semibold' }} transition-colors">
                            {{ $lsoLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if($search || $selectedCluster !== 'all' || $selectedPeriod !== 'all' || $selectedStatus !== 'all' || $sortBy !== 'name_asc')
                <a href="{{ route('level-kpi.index') }}" class="py-2 px-3 text-xs font-semibold rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 transition-all flex items-center gap-1.5">
                    <i class="bi bi-x-circle-fill"></i> Reset
                </a>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <a href="{{ route('level-kpi.export', request()->query()) }}" class="py-2 px-4 rounded-xl bg-emerald-600/90 hover:bg-emerald-600 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-1.5">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                <span>Export CSV</span>
            </a>

            @if(!Auth::user()->isVisitor())
                <button type="button" onclick="openAddModal()" class="py-2 px-4 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs shadow-lg shadow-red-600/30 transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Tambah Data KPI</span>
                </button>
            @endif
        </div>

    </form>
</div>
