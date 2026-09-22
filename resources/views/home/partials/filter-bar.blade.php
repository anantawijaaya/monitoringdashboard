<!-- HEADER & ACTION BAR -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between p-3 sm:p-4 rounded-xl">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold text-red-600 uppercase tracking-wider">
            <span class="inline-block w-2 h-2 rounded-full bg-red-600"></span>
            DATA OVERVIEW
        </div>
    </div>

    <!-- Filter Form -->
    <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2.5" id="dashboardFilterForm">
            <input type="hidden" name="cluster" id="dashClusterInput" value="{{ $selectedCluster }}">
            <input type="hidden" name="period" id="dashPeriodInput" value="{{ $selectedPeriod }}">

            <!-- Custom Cluster Dropdown -->
            <div class="relative z-30" id="dashClusterDropdownContainer">
                <button type="button" 
                        onclick="toggleDashClusterMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-sm cursor-pointer min-w-[150px]">
                    <span>{{ $selectedCluster === 'all' ? 'Semua Cluster' : $selectedCluster }}</span>
                    <i id="dashClusterArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="dashClusterMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    <a href="javascript:void(0)" onclick="submitDashFilter('cluster', 'all')"
                       class="block px-4 py-2 text-xs font-bold {{ $selectedCluster === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                        Semua Cluster
                    </a>
                    @foreach($availableClusters as $c)
                        <a href="javascript:void(0)" onclick="submitDashFilter('cluster', '{{ $c }}')"
                           class="block px-4 py-2 text-xs font-semibold {{ $selectedCluster === $c ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                            {{ $c }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Custom Period Dropdown -->
            <div class="relative z-30" id="dashPeriodDropdownContainer">
                <button type="button" 
                        onclick="toggleDashPeriodMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-sm cursor-pointer min-w-[150px]">
                    <span>{{ $selectedPeriod === 'all' ? 'Semua Periode' : 'Periode ' . $selectedPeriod }}</span>
                    <i id="dashPeriodArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="dashPeriodMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    <a href="javascript:void(0)" onclick="submitDashFilter('period', 'all')"
                       class="block px-4 py-2 text-xs font-bold {{ $selectedPeriod === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                        Semua Periode
                    </a>
                    @foreach($availablePeriods as $p)
                        <a href="javascript:void(0)" onclick="submitDashFilter('period', '{{ $p }}')"
                           class="block px-4 py-2 text-xs font-semibold {{ $selectedPeriod == $p ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                            Periode {{ $p }}
                        </a>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</div>
