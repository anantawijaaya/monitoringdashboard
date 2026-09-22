<!-- ==================== VIEW 1: RINGKASAN KPI ==================== -->
<div id="viewRingkasan" class="{{ ($viewMode ?? 'ringkasan') === 'ringkasan' ? '' : 'hidden' }}">
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 space-y-5 w-full">
        <!-- Toolbar Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-base font-bold">
                    <i class="bi bi-grid-fill"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Ringkasan KPI Performance Cluster</h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Custom Filter Periode Dropdown -->
                <form method="GET" action="{{ route('kelas-kpi.index') }}" class="flex items-center gap-2" id="filterPeriodeFormRingkasan">
                    <input type="hidden" name="view_mode" value="ringkasan">
                    <input type="hidden" name="periode" id="ringkasanPeriodeInput" value="{{ $selectedPeriode ?? 'all' }}">
                    <div class="relative z-30" id="ringkasanPeriodeDropdownContainer">
                        <button type="button" 
                                onclick="toggleRingkasanPeriodeMenu(event)"
                                class="flex items-center justify-between gap-2.5 bg-white border border-slate-200/80 hover:bg-slate-50 rounded-full px-4 py-2 text-xs font-bold text-slate-700 shadow-xs focus:outline-none cursor-pointer min-w-[145px]">
                            <span>{{ ($selectedPeriode ?? 'all') === 'all' ? 'Semua Periode' : 'Periode ' . $selectedPeriode }}</span>
                            <i id="ringkasanPeriodeArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                        </button>

                        <div id="ringkasanPeriodeMenu" 
                             class="hidden absolute top-full left-0 mt-1.5 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                            <a href="javascript:void(0)" onclick="submitRingkasanPeriode('all')"
                               class="block px-4 py-2 text-xs font-bold {{ ($selectedPeriode ?? 'all') === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }} transition-colors">
                                Semua Periode
                            </a>
                            @foreach($availablePeriodes as $p)
                                <a href="javascript:void(0)" onclick="submitRingkasanPeriode('{{ $p }}')"
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
                       class="tab-btn-ringkasan px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'ringkasan' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24]' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
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
                       class="tab-btn-analisis px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'analisis' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                        <i class="bi bi-graph-up-arrow text-xs"></i>
                        <span>Analisis Data</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- RINGKASAN TABLE -->
        <div class="overflow-x-auto rounded-t-2xl border border-slate-200/80 shadow-xs">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#ED1C24] text-white font-black uppercase text-[11px] tracking-wider">
                    <tr class="bg-[#ED1C24] text-white">
                        <th class="py-4 px-4 text-center w-[5%] rounded-tl-2xl">NO</th>
                        <th class="py-3.5 px-4 text-center w-[10%]">PERIODE</th>
                        <th class="py-3.5 px-4 w-[20%]">CLUSTER</th>
                        <th class="py-3.5 px-4 w-[30%]">MITRA</th>
                        <th class="py-3.5 px-8 text-center w-[20%]">TIPE CLUSTER</th>
                        <th class="py-3.5 px-4 text-center w-[11%]">TOTAL SKOR</th>
                        <th class="py-3.5 px-4 text-center w-[11%] rounded-tr-2xl">KELAS KPI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700 bg-white">
                    @forelse($kelasKpis as $index => $row)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 text-center font-semibold border-r border-slate-200">
                                {{ $kelasKpis->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold border-r border-slate-200">
                                {{ $row->periode ?: '2026-08' }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold uppercase border-r border-slate-200">
                                {{ $row->cluster ?: $row->new_cluster }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold uppercase border-r border-slate-200">
                                {{ $row->mitra ?: 'TELKOMSEL REGIONAL BALI NUSRA' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold tracking-wide border-r border-slate-200">
                                {{ $row->type }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-blue-600 bg-blue-50/40 text-sm border-r border-slate-200">
                                 {{ number_format($row->final_score ?: $row->total_score, 2, ',', '.') }}
                             </td>
                            <td class="py-3.5 px-4 text-center border-r border-slate-200">
                                @if($row->class === 'PLATINUM')
                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-sky-600 text-white shadow-xs tracking-wider uppercase min-w-[85px]">PLATINUM</span>
                                @elseif($row->class === 'GOLD')
                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-[#EAB308] text-white shadow-xs tracking-wider uppercase min-w-[85px]">GOLD</span>
                                @elseif($row->class === 'SILVER')
                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-[#94A3B8] text-white shadow-xs tracking-wider uppercase min-w-[85px]">SILVER</span>
                                @elseif($row->class === 'BRONZE')
                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-[#B45309] text-white shadow-xs tracking-wider uppercase min-w-[85px]">BRONZE</span>
                                @else
                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-slate-900 text-white shadow-xs tracking-wider uppercase min-w-[85px]">BLACK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400 font-medium">
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
