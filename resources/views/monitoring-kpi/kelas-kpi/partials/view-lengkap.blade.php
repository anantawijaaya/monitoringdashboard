<!-- ==================== VIEW 2: PERHITUNGAN LENGKAP KPI ==================== -->
<div id="viewLengkap" class="{{ ($viewMode ?? 'ringkasan') === 'lengkap' ? '' : 'hidden' }}">
    <div class="space-y-5">

        <!-- FULL PERHITUNGAN LENGKAP TABLE CARD -->
        <div class="bg-white border border-slate 100/80 rounded-2xl shadow-sm p-8 space-y-7">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-base font-bold">
                        <i class="bi bi-calculator-fill"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Perhitungan Lengkap KPI Perfomance Cluster</h2>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                     <!-- Custom Filter Periode Dropdown -->
                     <form method="GET" action="{{ route('kelas-kpi.index') }}" class="flex items-center gap-2" id="filterPeriodeFormLengkap">
                         <input type="hidden" name="view_mode" value="lengkap">
                         <input type="hidden" name="periode" id="lengkapPeriodeInput" value="{{ $selectedPeriode ?? 'all' }}">
                         <div class="relative z-30" id="lengkapPeriodeDropdownContainer">
                             <button type="button" 
                                     onclick="toggleLengkapPeriodeMenu(event)"
                                     class="flex items-center justify-between gap-2.5 bg-white border border-slate-200/80 hover:bg-slate-50 rounded-full px-4 py-2 text-xs font-bold text-slate-700 shadow-xs focus:outline-none cursor-pointer min-w-[145px]">
                                 <span>{{ ($selectedPeriode ?? 'all') === 'all' ? 'Semua Periode' : 'Periode ' . $selectedPeriode }}</span>
                                 <i id="lengkapPeriodeArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                             </button>

                             <div id="lengkapPeriodeMenu" 
                                  class="hidden absolute top-full left-0 mt-1.5 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                                 <a href="javascript:void(0)" onclick="submitLengkapPeriode('all')"
                                    class="block px-4 py-2 text-xs font-bold {{ ($selectedPeriode ?? 'all') === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }} transition-colors">
                                     Semua Periode
                                 </a>
                                 @foreach($availablePeriodes as $p)
                                     <a href="javascript:void(0)" onclick="submitLengkapPeriode('{{ $p }}')"
                                        class="block px-4 py-2 text-xs font-semibold {{ ($selectedPeriode ?? 'all') === $p ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }} transition-colors">
                                         Periode {{ $p }}
                                     </a>
                                 @endforeach
                             </div>
                         </div>
                     </form>

                    <!-- View Mode Switcher -->
                    <div class="flex items-center gap-2 select-none">
                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'ringkasan'])) }}" 
                           onclick="switchViewMode(event, 'ringkasan')"
                           class="tab-btn-ringkasan px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'ringkasan' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                            <i class="bi bi-grid-fill text-xs"></i>
                            <span>Ringkasan KPI</span>
                        </a>
                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'lengkap'])) }}" 
                           onclick="switchViewMode(event, 'lengkap')"
                           class="tab-btn-lengkap px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'lengkap' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24]' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                            <i class="bi bi-calculator-fill text-xs"></i>
                            <span>Perhitungan Lengkap KPI</span>
                        </a>
                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'analisis'])) }}" 
                           onclick="switchViewMode(event, 'analisis')"
                           class="tab-btn-analisis px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'analisis' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                            <i class="bi bi-graph-up-arrow text-xs"></i>
                            <span>Analisis Data</span>
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-4 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-emerald-500 text-base"></i>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-sm cursor-pointer">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl p-4 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-red-500 text-base"></i>
                        <span class="font-bold">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold text-sm cursor-pointer">&times;</button>
                </div>
            @endif

             <!-- MAIN FULL CALCULATION MATRIX TABLE -->
            <div class="overflow-x-auto border border-slate-200 rounded-t-2xl shadow-xs overflow-hidden">
                <table class="w-full text-left text-xs whitespace-nowrap border-collapse">
                    <thead>
                        <!-- Section Super Headers (Uniform Telkomsel Red #ED1C24) -->
                        <tr class="bg-[#ED1C24] text-white font-black text-[11px] uppercase tracking-wider text-center border-b border-red-600">
                            <th rowspan="2" class="py-3 px-4 text-left font-semibold">Cluster</th>
                            <th rowspan="2" class="py-3 px-3 text-center font-semibold">Periode</th>
                            <th colspan="6" class="py-2.5 px-4 bg-[#ED1C24] text-white font-extrabold">Revenue All</th>
                            <th colspan="6" class="py-2.5 px-4 bg-[#ED1C24] text-white font-extrabold">Revenue Broadband</th>
                            <th colspan="6" class="py-2.5 px-4 bg-[#ED1C24] text-white font-extrabold">Revenue Redeem PV</th>
                            <th colspan="6" class="py-2.5 px-4 bg-[#ED1C24] text-white font-extrabold">Revenue RGB</th>
                            <th colspan="14" class="py-2.5 px-4 bg-[#ED1C24] text-white font-extrabold">Omzet Outlet</th>
                            <th colspan="2" class="py-2.5 px-4 bg-[#ED1C24] text-white font-black rounded-tr-2xl">Hasil Akhir KPI</th>
                        </tr>
                        <!-- Column Metric Sub Headers -->
                        <tr class="bg-[#ED1C24] text-white font-bold text-[10px] uppercase tracking-tight text-center">
                            <!-- Rev ALL -->
                            <th class="py-2 px-3 text-white">TARGET</th>
                            <th class="py-2 px-3 text-white">actual</th>
                            <th class="py-2 px-3 text-white font-black">ach%</th>
                            <th class="py-2 px-3 text-white font-black">score</th>
                            <th class="py-2 px-3 text-white">weight</th>
                            <th class="py-2 px-3 font-black text-white bg-[#C8102E]">final score</th>

                            <!-- Rev BB -->
                            <th class="py-2 px-3 text-white">target</th>
                            <th class="py-2 px-3 text-white">actual</th>
                            <th class="py-2 px-3 text-white font-black">ach%</th>
                            <th class="py-2 px-3 text-white font-black">score</th>
                            <th class="py-2 px-3 text-white">weight</th>
                            <th class="py-2 px-3 font-black text-white bg-[#C8102E]">final score</th>

                            <!-- Rev PV -->
                            <th class="py-2 px-3 text-white">target</th>
                            <th class="py-2 px-3 text-white">actual</th>
                            <th class="py-2 px-3 text-white font-black">ach%</th>
                            <th class="py-2 px-3 text-white font-black">score</th>
                            <th class="py-2 px-3 text-white">weight</th>
                            <th class="py-2 px-3 font-black text-white bg-[#C8102E]">final score</th>

                            <!-- Rev RGB -->
                            <th class="py-2 px-3 text-white">target</th>
                            <th class="py-2 px-3 text-white">actual</th>
                            <th class="py-2 px-3 text-white font-black">ach%</th>
                            <th class="py-2 px-3 text-white font-black">score</th>
                            <th class="py-2 px-3 text-white">weight</th>
                            <th class="py-2 px-3 font-black text-white bg-[#C8102E]">final score</th>

                            <!-- Growth -->
                            <th class="py-2 px-3 text-white">omzet m1</th>
                            <th class="py-2 px-3 text-white">mtd m1</th>
                            <th class="py-2 px-3 text-white">tgt 3%</th>
                            <th class="py-2 px-3 text-white">mtd</th>
                            <th class="py-2 px-3 text-white">growth%</th>
                            <th class="py-2 px-3 text-white">growth tgt</th>
                            <th class="py-2 px-3 text-white font-black">rev growth ach%</th>

                            <!-- Outlet PJP -->
                            <th class="py-2 px-3 text-white">outlet pjp</th>
                            <th class="py-2 px-3 text-white">pjp growth</th>
                            <th class="py-2 px-3 text-white">ratio pjp</th>
                            <th class="py-2 px-3 text-white font-black">omzet outlet ach%</th>
                            <th class="py-2 px-3 text-white font-black">score</th>
                            <th class="py-2 px-3 text-white">weight</th>
                            <th class="py-2 px-3 font-black text-white bg-[#C8102E]">final score</th>

                            <!-- Final Result -->
                            <th class="py-2 px-4 font-black text-white bg-[#B91C1C]">total final score</th>
                            <th class="py-2 px-4 font-black text-white bg-[#B91C1C]">Kelas KPI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($kelasKpis as $index => $row)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <!-- Cluster -->
                                <td class="py-3 px-4 font-bold text-black/90 border-r border-slate-200 bg-white sticky left-0 z-10">
                                    {{ $row->cluster ?: $row->new_cluster }}
                                </td>

                                <!-- Periode -->
                                <td class="py-3 px-3 text-center font-bold text-black/90">
                                    {{ $row->periode ?: '2026-08' }}
                                </td>

                                <!-- Rev ALL -->
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->target_rev_all, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->actual_rev_all, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ach_rev_all ?: $row->ach_percent, 1, ',', '.') }}%</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_rev_all ?: $row->score, 0) }},0</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">15%</td>
                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_rev_all, 2, ',', '.') }}</td>

                                <!-- Rev BB -->
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->target_rev_bb, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->actual_rev_bb, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ach_rev_bb, 1, ',', '.') }}%</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_rev_bb, 0) }},0</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->weight_rev_bb ?: (strtoupper($row->type) === 'LOW' ? 20 : 15), 0) }}%</td>
                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_rev_bb, 2, ',', '.') }}</td>

                                <!-- Rev PV -->
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->target_rev_pv, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->actual_rev_pv, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ach_rev_pv, 1, ',', '.') }}%</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_rev_pv, 0) }},0</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->weight_rev_pv ?: (strtoupper($row->type) === 'LOW' ? 20 : 25), 0) }}%</td>
                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_rev_pv, 2, ',', '.') }}</td>

                                <!-- Rev RGB -->
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->target_rgb, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->actual_rgb, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ach_rgb, 1, ',', '.') }}%</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_rgb, 0) }},0</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">20%</td>
                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_rgb, 2, ',', '.') }}</td>

                                <!-- Growth Revenue -->
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->omzet_rev_m1, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->mtd_m1, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->tgt_3_percent, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->mtd, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->growth, 0, ',', '.') }}%</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->growth_tgt, 0, ',', '.') }}%</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->rev_growth_ach_percent, 1, ',', '.') }}%</td>

                                <!-- Outlet PJP & Omzet Outlet -->
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->outlet_pjp, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->outlet_pjp_growth,0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ratio_outlet_pjp, 1, ',', '.') }}%</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->omzet_outlet_ach, 1, ',', '.') }}%</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_omzet ?: $row->omzet_outlet_ach_score, 0) }},0</td>
                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">25%</td>
                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_omzet, 2, ',', '.') }}</td>

                                <!-- Total Final Score & Class -->
                                <td class="py-3 px-4 text-center font-black text-amber-600 bg-amber-50/80 text-sm border-r border-slate-200">
                                    {{ number_format($row->final_score ?: $row->total_score, 2, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @php
                                        $cls = strtoupper($row->class ?: 'BRONZE');
                                        $clsClass = match($cls) {
                                            'PLATINUM' => 'bg-sky-600 text-white shadow-xs',
                                            'GOLD' => 'bg-amber-500 text-white shadow-xs',
                                            'SILVER' => 'bg-slate-400 text-white shadow-xs',
                                            'BRONZE' => 'bg-amber-800 text-white shadow-xs',
                                            default => 'bg-slate-900 text-white shadow-xs'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide {{ $clsClass }}">
                                        {{ $cls }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="41" class="py-10 text-center text-slate-400">
                                    Belum ada data cluster yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-end gap-4 pt-2 text-xs text-slate-500 font-medium [&_p]:hidden">
                <div>{{ $kelasKpis->links() }}</div>
            </div>
        </div>
    </div>
</div>
