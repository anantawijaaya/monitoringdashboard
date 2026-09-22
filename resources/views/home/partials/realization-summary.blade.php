<!-- SECTION: SUMMARY PENYERAPAN BUDGET REALISASI PER CLUSTER -->
@php
    $initAlokasi = 0;
    $initRealisasi = 0;
    if (isset($realizationSummary) && is_array($realizationSummary)) {
        foreach($realizationSummary as $row) {
            $initAlokasi += ($row['indirect_channel']['budget'] ?? 0) + ($row['direct_sales']['budget'] ?? 0) + ($row['culture_program']['budget'] ?? 0);
            $initRealisasi += ($row['indirect_channel']['realisasi'] ?? 0) + ($row['direct_sales']['realisasi'] ?? 0) + ($row['culture_program']['realisasi'] ?? 0);
        }
    }
    $initSisa = $initAlokasi - $initRealisasi;
    $initPersen = $initAlokasi > 0 ? round(($initRealisasi / $initAlokasi) * 100, 1) : 0;

    $clustersList = [
        'BALI BARAT', 'BALI TENGAH', 'BALI TIMUR', 
        'ENDE SIKKA', 'FLORES TIMUR', 'MANGGARAI', 
        'KUPANG ROTE', 'MALAKA TIMTIM BELU', 'SUMBA', 
        'LOMBOK', 'SUMBAWA BARAT', 'SUMBAWA TIMUR'
    ];
@endphp

<div class="p-5 sm:p-6 space-y-4 bg-white rounded-3xl border border-gray-100 shadow-sm">
    <!-- Header Section with Title & 2 Interactive Custom Dropdown Filters -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-2 border-b border-gray-100">
        <div>
            <h3 class="text-[17px] font-black text-gray-900 uppercase tracking-tight flex items-center gap-2">
                SUMMARY PENYERAPAN BUDGET MARKETING PER CLUSTER
            </h3>
        </div>

        <!-- 2 Interactive Custom Dropdown Filters (Matching Main Filter Bar Style) -->
        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
            
            <!-- Custom Cluster Dropdown -->
            <div class="relative z-30" id="summaryClusterDropdownContainer">
                <button type="button" 
                        onclick="toggleSummaryClusterMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-sm cursor-pointer min-w-[160px]">
                    <span id="summaryClusterBtnLabel">Semua Cluster</span>
                    <i id="summaryClusterArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="summaryClusterMenu" 
                     class="hidden absolute top-full right-0 mt-1.5 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    <a href="javascript:void(0)" onclick="selectSummaryCluster('all', 'Semua Cluster')"
                       id="opt-cluster-all"
                       class="summary-cluster-item block px-4 py-2 text-xs font-bold text-red-600 bg-red-50/50 font-extrabold transition-colors">
                        Semua Cluster
                    </a>
                    @foreach($clustersList as $cName)
                        <a href="javascript:void(0)" onclick="selectSummaryCluster('{{ $cName }}', '{{ $cName }}')"
                           id="opt-cluster-{{ Str::slug($cName) }}"
                           class="summary-cluster-item block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-slate-50 transition-colors">
                            {{ $cName }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Custom Program Dropdown -->
            <div class="relative z-30" id="summaryProgramDropdownContainer">
                <button type="button" 
                        onclick="toggleSummaryProgramMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-2xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-sm cursor-pointer min-w-[160px]">
                    <span id="summaryProgramBtnLabel">Semua Program</span>
                    <i id="summaryProgramArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="summaryProgramMenu" 
                     class="hidden absolute top-full right-0 mt-1.5 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    <a href="javascript:void(0)" onclick="selectSummaryProgram('all', 'Semua Program')"
                       id="opt-program-all"
                       class="summary-program-item block px-4 py-2 text-xs font-bold text-red-600 bg-red-50/50 font-extrabold transition-colors">
                        Semua Program
                    </a>
                    <a href="javascript:void(0)" onclick="selectSummaryProgram('indirect_channel', 'Indirect Channel')"
                       id="opt-program-indirect_channel"
                       class="summary-program-item block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-slate-50 transition-colors">
                        Indirect Channel
                    </a>
                    <a href="javascript:void(0)" onclick="selectSummaryProgram('direct_sales', 'Direct Sales')"
                       id="opt-program-direct_sales"
                       class="summary-program-item block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-slate-50 transition-colors">
                        Direct Sales
                    </a>
                    <a href="javascript:void(0)" onclick="selectSummaryProgram('culture_program', 'Culture Program')"
                       id="opt-program-culture_program"
                       class="summary-program-item block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-slate-50 transition-colors">
                        Culture Program
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- 3 RED METRIC BOXES (CENTERED) -->
    <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-8 my-4">

        <!-- BOX 1: TOTAL ALOKASI BUDGET -->
        <div class="metric-card w-[300px] h-[180px] bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
            <div>
                <div class="flex items-center text-center justify-center">
                    <span class="text-[15px] font-black text-white/90 uppercase tracking-wider block mt-3">TOTAL ALOKASI BUDGET</span>
                </div>

                <div class="mt-6 sm:mt-8 flex items-baseline gap-2 justify-center">
                    <span id="summaryTotalAlokasi" class="text-[26px] font-black text-white tracking-tight leading-none mt-5">
                        Rp {{ number_format($initAlokasi, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="mt-4 text-xs text-white/80 font-medium text-center">
                
            </div>
        </div>

        <!-- BOX 2: TOTAL REALISASI -->
        <div class="metric-card w-[300px] h-[180px] bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
            <div>
                <div class="flex items-center text-center justify-center">
                    <span class="text-[15px] font-black text-white/90 uppercase tracking-wider block">TOTAL REALISASI</span>
                </div>

                <div class="mt-6 sm:mt-8 flex items-baseline gap-2 justify-center">
                    <span id="summaryTotalRealisasi" class="text-[26px] font-black text-white tracking-tight leading-none">
                        Rp {{ number_format($initRealisasi, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Target & Pill Row -->
                <div class="flex items-center justify-between text-xs sm:text-sm mt-4">
                    <span class="text-white/90 text-[12px] font-medium">% Penyerapan:</span>
                    <span id="summaryPersenRealisasi" class="px-2.5 py-0.5 rounded-lg text-[12px] font-black bg-white text-[#ED1C24] shadow-sm">
                        {{ $initPersen }}%
                    </span>
                </div>

                <!-- White Progress Bar -->
                <div class="w-full h-2 bg-black/20 rounded-lg overflow-hidden mt-3">
                    <div id="summaryProgressBar" class="h-full bg-white rounded-lg transition-all duration-700" style="width: {{ min(100, $initPersen) }}%;"></div>
                </div>
            </div>
        </div>

        <!-- BOX 3: SISA BUDGET -->
        <div class="metric-card w-[300px] h-[180px] bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
            <div>
                <div class="flex items-center justify-center">
                    <span class="text-[17px] font-black text-white/90 uppercase tracking-wider block mt-3">SISA BUDGET</span>
                </div>

                <div class="mt-6 sm:mt-8 flex items-baseline gap-2 justify-center">
                    <span id="summarySisaBudget" class="text-[26px] font-black text-white tracking-tight leading-none mt-5">
                        Rp {{ number_format($initSisa, 0, ',', '.') }}
                    </span>
                </div>

                <div class="mt-8 text-xs text-white/80 font-medium text-center">
                    
                </div>
            </div>
        </div>

    </div>

    <!-- Summary Table Container -->    
    <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm mt-4">
        <table class="w-full h-[40px] text-left text-xs border-collapse">
            <thead>
                <tr class="bg-[#ED1C24] text-white font-extrabold uppercase text-[13px] tracking-wider">
                    <th class="py-2.5 px-3 text-center w-1 whitespace-nowrap border-slate-800">No</th>
                    <th class="py-2.5 px-3 border-slate-800 text-center w-1 whitespace-nowrap">Cluster</th>
                    <th class="py-2.5 px-3 border-slate-800 text-center w-1 whitespace-nowrap">Mitra</th>
                    <th class="py-2.5 px-3 border-slate-800 text-center w-64">Indirect Channel</th>
                    <th class="py-2.5 px-3 border-slate-800 text-center w-64">Direct Sales</th>
                    <th class="py-2.5 px-3 border-slate-800 text-center w-64">Culture Program</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($realizationSummary as $index => $row)
                    <tr class="summary-table-row hover:bg-slate-50/80 transition-colors font-medium" data-cluster="{{ $row['cluster'] }}">
                        <!-- No -->
                        <td class="py-2 px-3 text-center font-semibold whitespace-nowrap border-r border-slate-200">
                            {{ $index + 1 }}
                        </td>
                        
                        <!-- Cluster -->
                        <td class="py-2 px-3 font-semibold whitespace-nowrap border-r border-slate-200">
                            {{ $row['cluster'] }}
                        </td>

                        <!-- Mitra -->
                        <td class="py-2 px-3 font-semibold whitespace-nowrap border-r border-slate-200">
                            {{ $row['mitra'] }}
                        </td>

                        <!-- Indirect Channel -->
                        <td class="py-2 px-3 whitespace-nowrap border-r border-slate-200">
                            <div class="space-y-1 min-w-[170px]">
                                <div class="flex items-center justify-between text-xs gap-3">
                                    <span class="text-[14px] font-semibold">Rp {{ number_format($row['indirect_channel']['realisasi'], 0, ',', '.') }}</span>
                                    <span class="font-extrabold text-black-600 bg-black-50 px-1.5 py-0.5 rounded text-[14px]">{{ $row['indirect_channel']['persen'] }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-100 overflow-hidden">
                                    <div class="h-full bg-red-500 transition-all duration-500" style="width: {{ min(100, $row['indirect_channel']['persen']) }}%;"></div>
                                </div>
                                <span class="text-[11px] text-gray-400 block">Rp {{ number_format($row['indirect_channel']['budget'], 0, ',', '.') }}</span>
                            </div>
                        </td>

                        <!-- Direct Sales -->
                        <td class="py-2 px-3 whitespace-nowrap border-r border-slate-200">
                            <div class="space-y-1 min-w-[170px]">
                                <div class="flex items-center justify-between text-xs gap-3">
                                    <span class="text-[14px] font-semibold">Rp {{ number_format($row['direct_sales']['realisasi'], 0, ',', '.') }}</span>
                                    <span class="font-extrabold text-black-600 bg-black-50 px-1.5 py-0.5 rounded text-[14px]">{{ $row['direct_sales']['persen'] }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-100 overflow-hidden">
                                    <div class="h-full bg-red-500 transition-all duration-500" style="width: {{ min(100, $row['direct_sales']['persen']) }}%;"></div>
                                </div>
                                <span class="text-[11px] text-gray-400 block">Rp {{ number_format($row['direct_sales']['budget'], 0, ',', '.') }}</span>
                            </div>
                        </td>

                        <!-- Culture Program -->
                        <td class="py-2 px-3 whitespace-nowrap border-r border-slate-200">
                            <div class="space-y-1 min-w-[170px]">
                                <div class="flex items-center justify-between text-xs gap-3">
                                    <span class="text-[14px] font-semibold">Rp {{ number_format($row['culture_program']['realisasi'], 0, ',', '.') }}</span>
                                    <span class="font-extrabold text-black-600 bg-black-50 px-1.5 py-0.5 rounded text-[14px]">{{ $row['culture_program']['persen'] }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-100 overflow-hidden">
                                    <div class="h-full bg-red-500 transition-all duration-500" style="width: {{ min(100, $row['culture_program']['persen']) }}%;"></div>
                                </div>
                                <span class="text-[11px] text-gray-400 block">Rp {{ number_format($row['culture_program']['budget'], 0, ',', '.') }}</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400 bg-gray-50">
                            <i class="bi bi-inbox text-3xl block mb-1"></i>
                            <p class="text-xs font-semibold">Belum ada data realisasi penyerapan budget.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Custom Interactive JavaScript for Dropdowns & Calculations -->
<script>
    window.realizationSummaryData = @json($realizationSummary ?? []);
    let activeSummaryCluster = 'all';
    let activeSummaryProgram = 'all';

    function toggleSummaryClusterMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('summaryClusterMenu');
        const arrow = document.getElementById('summaryClusterArrow');
        const programMenu = document.getElementById('summaryProgramMenu');
        const programArrow = document.getElementById('summaryProgramArrow');
        
        if (programMenu) programMenu.classList.add('hidden');
        if (programArrow) programArrow.classList.remove('rotate-180');

        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleSummaryProgramMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('summaryProgramMenu');
        const arrow = document.getElementById('summaryProgramArrow');
        const clusterMenu = document.getElementById('summaryClusterMenu');
        const clusterArrow = document.getElementById('summaryClusterArrow');

        if (clusterMenu) clusterMenu.classList.add('hidden');
        if (clusterArrow) clusterArrow.classList.remove('rotate-180');

        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function selectSummaryCluster(val, label) {
        activeSummaryCluster = val;
        const btnLabel = document.getElementById('summaryClusterBtnLabel');
        if (btnLabel) btnLabel.innerText = label;

        // Reset highlight styling for cluster menu options
        document.querySelectorAll('.summary-cluster-item').forEach(el => {
            el.className = 'summary-cluster-item block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-slate-50 transition-colors';
        });

        // Set active item styling
        const slug = val === 'all' ? 'all' : val.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        const activeItem = document.getElementById('opt-cluster-' + slug);
        if (activeItem) {
            activeItem.className = 'summary-cluster-item block px-4 py-2 text-xs font-bold text-red-600 bg-red-50/50 font-extrabold transition-colors';
        }

        // Close dropdown menu
        const menu = document.getElementById('summaryClusterMenu');
        const arrow = document.getElementById('summaryClusterArrow');
        if (menu) menu.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');

        updateSummaryBudgetCards();
    }

    function selectSummaryProgram(val, label) {
        activeSummaryProgram = val;
        const btnLabel = document.getElementById('summaryProgramBtnLabel');
        if (btnLabel) btnLabel.innerText = label;

        // Reset highlight styling for program menu options
        document.querySelectorAll('.summary-program-item').forEach(el => {
            el.className = 'summary-program-item block px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-slate-50 transition-colors';
        });

        // Set active item styling
        const activeItem = document.getElementById('opt-program-' + val);
        if (activeItem) {
            activeItem.className = 'summary-program-item block px-4 py-2 text-xs font-bold text-red-600 bg-red-50/50 font-extrabold transition-colors';
        }

        // Close dropdown menu
        const menu = document.getElementById('summaryProgramMenu');
        const arrow = document.getElementById('summaryProgramArrow');
        if (menu) menu.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');

        updateSummaryBudgetCards();
    }

    document.addEventListener('click', function(e) {
        const cContainer = document.getElementById('summaryClusterDropdownContainer');
        const cMenu = document.getElementById('summaryClusterMenu');
        const cArrow = document.getElementById('summaryClusterArrow');
        if (cContainer && !cContainer.contains(e.target) && cMenu && !cMenu.classList.contains('hidden')) {
            cMenu.classList.add('hidden');
            if (cArrow) cArrow.classList.remove('rotate-180');
        }

        const pContainer = document.getElementById('summaryProgramDropdownContainer');
        const pMenu = document.getElementById('summaryProgramMenu');
        const pArrow = document.getElementById('summaryProgramArrow');
        if (pContainer && !pContainer.contains(e.target) && pMenu && !pMenu.classList.contains('hidden')) {
            pMenu.classList.add('hidden');
            if (pArrow) pArrow.classList.remove('rotate-180');
        }
    });

    function updateSummaryBudgetCards() {
        const selectedCluster = activeSummaryCluster;
        const selectedProgram = activeSummaryProgram;

        let totalAlokasi = 0;
        let totalRealisasi = 0;

        if (Array.isArray(window.realizationSummaryData)) {
            window.realizationSummaryData.forEach(row => {
                const matchCluster = (selectedCluster === 'all') || 
                    (row.cluster && row.cluster.trim().toUpperCase() === selectedCluster.trim().toUpperCase());

                if (!matchCluster) return;

                if (selectedProgram === 'all') {
                    totalAlokasi += (row.indirect_channel?.budget || 0) + (row.direct_sales?.budget || 0) + (row.culture_program?.budget || 0);
                    totalRealisasi += (row.indirect_channel?.realisasi || 0) + (row.direct_sales?.realisasi || 0) + (row.culture_program?.realisasi || 0);
                } else if (selectedProgram === 'indirect_channel') {
                    totalAlokasi += (row.indirect_channel?.budget || 0);
                    totalRealisasi += (row.indirect_channel?.realisasi || 0);
                } else if (selectedProgram === 'direct_sales') {
                    totalAlokasi += (row.direct_sales?.budget || 0);
                    totalRealisasi += (row.direct_sales?.realisasi || 0);
                } else if (selectedProgram === 'culture_program') {
                    totalAlokasi += (row.culture_program?.budget || 0);
                    totalRealisasi += (row.culture_program?.realisasi || 0);
                }
            });
        }

        const sisaBudget = totalAlokasi - totalRealisasi;
        const persen = totalAlokasi > 0 ? ((totalRealisasi / totalAlokasi) * 100).toFixed(1) : '0.0';

        const elAlokasi = document.getElementById('summaryTotalAlokasi');
        const elRealisasi = document.getElementById('summaryTotalRealisasi');
        const elSisa = document.getElementById('summarySisaBudget');
        const elPersen = document.getElementById('summaryPersenRealisasi');
        const elBar = document.getElementById('summaryProgressBar');

        if (elAlokasi) elAlokasi.innerText = 'Rp ' + formatRupiahSummary(totalAlokasi);
        if (elRealisasi) elRealisasi.innerText = 'Rp ' + formatRupiahSummary(totalRealisasi);
        if (elSisa) elSisa.innerText = 'Rp ' + formatRupiahSummary(sisaBudget);
        if (elPersen) elPersen.innerText = persen + '%';
        if (elBar) elBar.style.width = Math.min(100, Math.max(0, parseFloat(persen))) + '%';

        // Filter table rows
        const rows = document.querySelectorAll('.summary-table-row');
        rows.forEach(tr => {
            const rowCluster = tr.getAttribute('data-cluster') || '';
            if (selectedCluster === 'all' || rowCluster.trim().toUpperCase() === selectedCluster.trim().toUpperCase()) {
                tr.style.display = '';
            } else {
                tr.style.display = 'none';
            }
        });
    }

    function formatRupiahSummary(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    }
</script>
