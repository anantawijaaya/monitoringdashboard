<!-- 3. MAIN DATA TABLE (GROUPED HEADERS & ALL PARAMETERS WITH ACH COLUMN) -->
<div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <!-- TABLE HEADERS -->
            <thead class="bg-[#151D2A] text-slate-300 font-bold uppercase tracking-wider text-[11px] border-b border-slate-800">
                <tr>
                    <th rowspan="2" class="py-4 px-4 text-center border-r border-slate-800/80 w-12">NO</th>
                    <th rowspan="2" class="py-4 px-4 border-r border-slate-800/80 min-w-[140px]">CLUSTER</th>
                    <th rowspan="2" class="py-4 px-4 border-r border-slate-800/80 min-w-[120px] text-center">PERIODE</th>
                    
                    <!-- REVENUE ALL -->
                    <th colspan="3" class="py-2.5 px-4 text-center border-r border-slate-800/80 bg-red-950/40 text-red-300">
                        REVENUE ALL
                    </th>

                    <!-- BROADBAND -->
                    <th colspan="3" class="py-2.5 px-4 text-center border-r border-slate-800/80 bg-blue-950/40 text-blue-300">
                        REVENUE BROADBAND
                    </th>

                    <!-- REDEEM PV -->
                    <th colspan="3" class="py-2.5 px-4 text-center border-r border-slate-800/80 bg-purple-950/40 text-purple-300">
                        REDEEM PV
                    </th>

                    <!-- REVENUE RGB -->
                    <th colspan="3" class="py-2.5 px-4 text-center border-r border-slate-800/80 bg-emerald-950/40 text-emerald-300">
                        REVENUE RGB
                    </th>

                    <!-- GROWTH REVENUE -->
                    <th colspan="3" class="py-2.5 px-4 text-center border-r border-slate-800/80 bg-amber-950/40 text-amber-300">
                        GROWTH REVENUE (MoM)
                    </th>

                    <!-- RATIO PJP -->
                    <th colspan="2" class="py-2.5 px-4 text-center border-r border-slate-800/80 bg-rose-950/40 text-rose-300">
                        RATIO PJP
                    </th>

                    <th rowspan="2" class="py-4 px-4 text-center min-w-[90px]">AKSI</th>
                </tr>
                <tr class="border-t border-slate-800 text-[10px] text-slate-400">
                    <!-- Rev All -->
                    <th class="py-2 px-3 text-right bg-red-950/20 border-r border-slate-800/60">TARGET</th>
                    <th class="py-2 px-3 text-right bg-red-950/20 border-r border-slate-800/60">MTD</th>
                    <th class="py-2 px-3 text-center bg-red-950/30 border-r border-slate-800/80 text-red-400 font-extrabold">ACH (%)</th>

                    <!-- Broadband -->
                    <th class="py-2 px-3 text-right bg-blue-950/20 border-r border-slate-800/60">TARGET</th>
                    <th class="py-2 px-3 text-right bg-blue-950/20 border-r border-slate-800/60">MTD</th>
                    <th class="py-2 px-3 text-center bg-blue-950/30 border-r border-slate-800/80 text-blue-400 font-extrabold">ACH (%)</th>

                    <!-- Redeem PV -->
                    <th class="py-2 px-3 text-right bg-purple-950/20 border-r border-slate-800/60">TARGET</th>
                    <th class="py-2 px-3 text-right bg-purple-950/20 border-r border-slate-800/60">MTD</th>
                    <th class="py-2 px-3 text-center bg-purple-950/30 border-r border-slate-800/80 text-purple-400 font-extrabold">ACH (%)</th>

                    <!-- RGB -->
                    <th class="py-2 px-3 text-right bg-emerald-950/20 border-r border-slate-800/60">TARGET</th>
                    <th class="py-2 px-3 text-right bg-emerald-950/20 border-r border-slate-800/60">MTD</th>
                    <th class="py-2 px-3 text-center bg-emerald-950/30 border-r border-slate-800/80 text-emerald-400 font-extrabold">ACH (%)</th>

                    <!-- Growth Rev -->
                    <th class="py-2 px-3 text-right bg-amber-950/20 border-r border-slate-800/60">LAST M.</th>
                    <th class="py-2 px-3 text-right bg-amber-950/20 border-r border-slate-800/60">THIS M.</th>
                    <th class="py-2 px-3 text-center bg-amber-950/30 border-r border-slate-800/80 text-amber-400 font-extrabold">ACH MoM (%)</th>

                    <!-- Ratio PJP -->
                    <th class="py-2 px-3 text-center bg-rose-950/20 border-r border-slate-800/60">RATIO</th>
                    <th class="py-2 px-3 text-center bg-rose-950/30 border-r border-slate-800/80 text-rose-400 font-extrabold">ACH (%)</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800/60 font-medium text-slate-200">
                @forelse($kpiLevels as $index => $row)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <!-- Index -->
                        <td class="py-3 px-4 text-center text-slate-400 border-r border-slate-800/60 font-mono">
                            {{ $kpiLevels->firstItem() + $index }}
                        </td>

                        <!-- Cluster -->
                        <td class="py-3 px-4 font-bold text-white border-r border-slate-800/60">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                <span>{{ $row->cluster_name }}</span>
                            </div>
                        </td>

                        <!-- Periode -->
                        <td class="py-3 px-4 text-center text-slate-300 border-r border-slate-800/60">
                            {{ $row->period_month }}
                        </td>

                        <!-- REVENUE ALL -->
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-slate-300">
                            {{ number_format($row->target_rev_all, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-white font-bold">
                            {{ number_format($row->mtd_rev_all, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-center border-r border-slate-800/80 font-bold">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $row->ach_rev_all >= 100 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($row->ach_rev_all >= 90 ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30') }}">
                                {{ number_format($row->ach_rev_all, 1, ',', '.') }}%
                            </span>
                        </td>

                        <!-- BROADBAND -->
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-slate-300">
                            {{ number_format($row->target_rev_bb, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-white font-bold">
                            {{ number_format($row->mtd_rev_bb, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-center border-r border-slate-800/80 font-bold">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $row->ach_rev_bb >= 100 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-blue-500/20 text-blue-400' }}">
                                {{ number_format($row->ach_rev_bb, 1, ',', '.') }}%
                            </span>
                        </td>

                        <!-- REDEEM PV -->
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-slate-300">
                            {{ number_format($row->target_pv, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-white font-bold">
                            {{ number_format($row->mtd_pv, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-center border-r border-slate-800/80 font-bold">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $row->ach_pv >= 100 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-purple-500/20 text-purple-400' }}">
                                {{ number_format($row->ach_pv, 1, ',', '.') }}%
                            </span>
                        </td>

                        <!-- REVENUE RGB -->
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-slate-300">
                            {{ number_format($row->target_rgb, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-white font-bold">
                            {{ number_format($row->mtd_rgb, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-center border-r border-slate-800/80 font-bold">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $row->ach_rgb >= 100 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                                {{ number_format($row->ach_rgb, 1, ',', '.') }}%
                            </span>
                        </td>

                        <!-- GROWTH REVENUE -->
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-slate-300">
                            {{ number_format($row->target_growth_rev, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right border-r border-slate-800/60 font-mono text-white font-bold">
                            {{ number_format($row->mtd_growth_rev, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-center border-r border-slate-800/80 font-bold">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $row->ach_growth_rev >= 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                {{ $row->ach_growth_rev > 0 ? '+' : '' }}{{ number_format($row->ach_growth_rev, 1, ',', '.') }}%
                            </span>
                        </td>

                        <!-- RATIO PJP -->
                        <td class="py-3 px-3 text-center border-r border-slate-800/60 font-mono text-white font-bold">
                            {{ number_format($row->ratio_pjp, 1, ',', '.') }}%
                        </td>
                        <td class="py-3 px-3 text-center border-r border-slate-800/80 font-bold">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $row->ach_pjp >= 90 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                {{ number_format($row->ach_pjp, 1, ',', '.') }}%
                            </span>
                        </td>

                        <!-- AKSI -->
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if(!Auth::user()->isVisitor())
                                    <button onclick="openEditModal({{ json_encode($row) }})" 
                                            title="Edit Data KPI" 
                                            class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-blue-400 hover:text-blue-300 transition-colors cursor-pointer">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <form action="{{ route('level-kpi.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data KPI {{ $row->cluster_name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Data KPI" 
                                                class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-red-400 hover:text-red-300 transition-colors cursor-pointer">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-500 text-xs">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="19" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-slate-800/80 flex items-center justify-center text-slate-500 text-xl">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <p class="text-sm font-semibold">Belum ada data Level KPI yang sesuai dengan filter.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION & FOOTER -->
    <div class="p-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-400 bg-[#121824]">
        <div>
            Menampilkan <span class="font-bold text-white">{{ $kpiLevels->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-white">{{ $kpiLevels->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-white">{{ $kpiLevels->total() }}</span> data Level KPI Cluster
        </div>
        <div>
            {{ $kpiLevels->links() }}
        </div>
    </div>
</div>
