<!-- FILTER & SEARCH CONTROLS -->
<div class="bg-gray-50 rounded-2x1 p-5">
    <form id="revenueFilterForm" method="GET" action="{{ route('revenue.manage') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
        
        <!-- Search Keyword Input -->
        <div class="lg:col-span-2">
            <label class="block text-xs font-bold text-gray-600 mb-1">Cari Cluster / Kabupaten</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik cluster atau kabupaten..." 
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
            </div>
        </div>

        <!-- Cluster Filter Dropdown -->
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Filter Cluster</label>
            <input type="hidden" name="cluster" id="revClusterInput" value="{{ $selectedCluster }}">
            <div class="relative z-30" id="revClusterDropdownContainer">
                <button type="button" 
                        onclick="toggleRevClusterMenu(event)"
                        class="w-full flex items-center justify-between gap-2 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer">
                    <span>{{ $selectedCluster === 'all' ? 'Semua Cluster' : $selectedCluster }}</span>
                    <i id="revClusterArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="revClusterMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-full bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto min-w-[160px]">
                    <a href="javascript:void(0)" onclick="submitRevFilter('cluster', 'all')"
                       class="block px-4 py-2 text-xs font-bold {{ $selectedCluster === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                        Semua Cluster
                    </a>
                    @foreach($availableClusters as $c)
                        <a href="javascript:void(0)" onclick="submitRevFilter('cluster', '{{ $c }}')"
                           class="block px-4 py-2 text-xs font-semibold {{ $selectedCluster === $c ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                            {{ $c }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Kabupaten Filter Dropdown -->
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Filter Kabupaten / Kota</label>
            <input type="hidden" name="kabupaten" id="revKabupatenInput" value="{{ $selectedKabupaten ?? 'all' }}">
            <div class="relative z-30" id="revKabupatenDropdownContainer">
                <button type="button" 
                        onclick="toggleRevKabupatenMenu(event)"
                        class="w-full flex items-center justify-between gap-2 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer">
                    <span>{{ ($selectedKabupaten ?? 'all') === 'all' ? 'Semua Kabupaten' : $selectedKabupaten }}</span>
                    <i id="revKabupatenArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="revKabupatenMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-full bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto min-w-[160px]">
                    <a href="javascript:void(0)" onclick="submitRevFilter('kabupaten', 'all')"
                       class="block px-4 py-2 text-xs font-bold {{ ($selectedKabupaten ?? 'all') === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                        Semua Kabupaten
                    </a>
                    @foreach($availableKabupatens as $k)
                        <a href="javascript:void(0)" onclick="submitRevFilter('kabupaten', '{{ $k }}')"
                           class="block px-4 py-2 text-xs font-semibold {{ ($selectedKabupaten ?? 'all') === $k ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50' }} transition-colors">
                            {{ $k }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Status Filter Dropdown -->
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Status Pencapaian</label>
            <input type="hidden" name="status" id="revStatusInput" value="{{ $selectedStatus }}">
            @php
                $statusLabels = [
                    'all' => 'Semua Status',
                    'melampaui_target' => 'Melampaui Target',
                    'mencapai_target' => 'Mencapai Target',
                    'tidak_mencapai_target' => 'Tidak Mencapai Target',
                ];
                $currentStatusLabel = $statusLabels[$selectedStatus ?? 'all'] ?? 'Semua Status';
            @endphp
            <div class="relative z-30" id="revStatusDropdownContainer">
                <button type="button" 
                        onclick="toggleRevStatusMenu(event)"
                        class="w-full flex items-center justify-between gap-2 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer">
                    <span>{{ $currentStatusLabel }}</span>
                    <i id="revStatusArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="revStatusMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-full bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto min-w-[170px]">
                    @foreach($statusLabels as $sKey => $sLabel)
                        <a href="javascript:void(0)" onclick="submitRevFilter('status', '{{ $sKey }}')"
                           class="block px-4 py-2 text-xs {{ ($selectedStatus ?? 'all') === $sKey ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50 font-semibold' }} transition-colors">
                            {{ $sLabel }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sort By Dropdown -->
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Urutkan</label>
            <input type="hidden" name="sort" id="revSortInput" value="{{ $sortBy }}">
            @php
                $sortLabels = [
                    'default' => 'Cluster & Kab (A-Z)',
                    'kabupaten_asc' => 'Nama Kabupaten (A-Z)',
                    'ach_desc' => 'Pencapaian Tertinggi',
                    'ach_asc' => 'Pencapaian Terendah',
                    'growth_desc' => 'Growth MoM Tertinggi',
                    'growth_asc' => 'Growth MoM Terendah',
                    'mtd_desc' => 'Realisasi MTD Terbesar',
                ];
                $currentSortLabel = $sortLabels[$sortBy ?? 'default'] ?? 'Cluster & Kab (A-Z)';
            @endphp
            <div class="relative z-30" id="revSortDropdownContainer">
                <button type="button" 
                        onclick="toggleRevSortMenu(event)"
                        class="w-full flex items-center justify-between gap-2 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer">
                    <span>{{ $currentSortLabel }}</span>
                    <i id="revSortArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="revSortMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-full bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto min-w-[180px]">
                    @foreach($sortLabels as $soKey => $soLabel)
                        <a href="javascript:void(0)" onclick="submitRevFilter('sort', '{{ $soKey }}')"
                           class="block px-4 py-2 text-xs {{ ($sortBy ?? 'default') === $soKey ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50 font-semibold' }} transition-colors">
                            {{ $soLabel }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

    </form>
</div>
