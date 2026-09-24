<!-- ==================== VIEW 3: ANALISIS DATA ==================== -->
<div id="viewAnalisis" class="{{ ($viewMode ?? 'ringkasan') === 'analisis' ? '' : 'hidden' }}">
    <div class="space-y-6">

        <!-- MAIN LINE CHART CONTAINER CARD -->
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 sm:p-8 space-y-5">
            <!-- Card Header & Toolbar Controls -->
            <div class="space-y-4">
                <!-- Top Row: Icon + Title & View Switcher -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-lg font-bold shrink-0">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Analisis Performance KPI Setiap Periode</h2>
                            <p class="text-xs text-slate-500 font-medium"></p>
                        </div>
                    </div>

                    <!-- View Mode Switcher -->
                    <div class="flex items-center gap-2 select-none shrink-0">
                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'ringkasan'])) }}" 
                           onclick="switchViewMode(event, 'ringkasan')"
                           class="tab-btn-ringkasan px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'ringkasan' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                            <i class="bi bi-grid-fill text-xs"></i>
                            <span>Ringkasan KPI</span>
                        </a>
                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'lengkap'])) }}" 
                           onclick="switchViewMode(event, 'lengkap')"
                           class="tab-btn-lengkap px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'lengkap' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                            <i class="bi bi-calculator-fill text-xs"></i>
                            <span>Perhitungan Lengkap KPI</span>
                        </a>
                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'analisis'])) }}" 
                           onclick="switchViewMode(event, 'analisis')"
                           class="tab-btn-analisis px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'analisis' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24]' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                            <i class="bi bi-graph-up-arrow text-xs"></i>
                            <span>Analisis Data</span>
                        </a>
                    </div>
                </div>

                <!-- Filter Controls (Periode & Cluster) -->
                <div class="flex items-center justify-end gap-2.5 sm:gap-3 pt-3 border-t border-slate-100">
                    <!-- Filter Periode Dropdown -->
                    <div class="relative min-w-[130px] z-30" id="analisisPeriodeDropdownContainer">
                        <input type="hidden" id="analisisFilterPeriode" value="all">
                        <button type="button" 
                                onclick="toggleAnalisisPeriodeMenu(event)"
                                class="w-full flex items-center justify-between gap-2 bg-white border border-slate-200/90 hover:bg-slate-50 rounded-full px-4 py-1.5 text-xs font-bold text-slate-700 shadow-xs focus:outline-none cursor-pointer">
                            <span id="analisisPeriodeBtnText">Periode</span>
                            <i id="analisisPeriodeArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                        </button>

                        <div id="analisisPeriodeMenu" 
                             class="hidden absolute top-full right-0 mt-1.5 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                            <a href="javascript:void(0)" onclick="selectAnalisisPeriode('all', 'Periode')"
                               class="block px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                                Periode (Semua)
                            </a>
                            @foreach($analisisPeriodes as $p)
                                <a href="javascript:void(0)" onclick="selectAnalisisPeriode('{{ $p }}', 'Periode {{ $p }}')"
                                   class="block px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                    Periode {{ $p }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Filter Cluster Dropdown -->
                    <div class="relative min-w-[140px] z-30" id="analisisClusterDropdownContainer">
                        <input type="hidden" id="analisisFilterCluster" value="{{ $analisisClusters[0] ?? 'BALI BARAT' }}">
                        <button type="button" 
                                onclick="toggleAnalisisClusterMenu(event)"
                                class="w-full flex items-center justify-between gap-2 bg-white border border-slate-200/90 hover:bg-slate-50 rounded-full px-4 py-1.5 text-xs font-bold text-slate-700 shadow-xs focus:outline-none cursor-pointer">
                            <span id="analisisClusterBtnText">{{ $analisisClusters[0] ?? 'BALI BARAT' }}</span>
                            <i id="analisisClusterArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                        </button>

                        <div id="analisisClusterMenu" 
                             class="hidden absolute top-full right-0 mt-1.5 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                            @foreach($analisisClusters as $c)
                                <a href="javascript:void(0)" onclick="selectAnalisisCluster('{{ $c }}', '{{ $c }}')"
                                   class="block px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                    {{ $c }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHART CANVAS CONTAINER -->
            <div class="relative w-full h-[460px] pt-2">
                <canvas id="analisisLineChart"></canvas>
            </div>
        </div>

    </div>
</div>
