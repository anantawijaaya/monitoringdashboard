<!-- ==================== VIEW 2: KALKULASI LENGKAP (FULL MATRIX TABLE) ==================== -->
<div id="viewLengkap" class="{{ ($viewMode ?? 'peringkat') === 'lengkap' ? '' : 'hidden' }} overflow-x-auto border border-slate-200/80 rounded-t-2xl shadow-xs overflow-hidden">
    <table class="w-full text-left text-[11px] border-collapse min-w-[2800px]">
        <thead>
            <!-- Super Headers -->
            <tr class="bg-[#ED1C24] text-white font-black text-[11px] uppercase tracking-wider text-center border-b border-red-600">
                <th rowspan="2" class="py-3 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white sticky left-0 z-10 font-black min-w-[180px] rounded-tl-2xl">City & Cluster</th>
                <th rowspan="2" class="py-3 px-3 border-r border-red-600/80 bg-[#ED1C24] text-white font-black min-w-[90px]">Periode</th>
                <th rowspan="2" class="py-3 px-3 border-r border-red-600/80 bg-[#ED1C24] text-white font-black min-w-[110px]">Type</th>
                <th colspan="6" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">Revenue All Weight</th>
                <th colspan="6" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">Revenue Broadband</th>
                <th colspan="6" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">Revenue Redeem PV</th>
                <th colspan="6" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">Revenue RGB</th>
                <th colspan="14" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">OMZET OUTLET</th>
                <th colspan="1" class="py-2.5 px-4 bg-[#ED1C24] text-white font-black min-w-[140px] rounded-tr-2xl">Hasil Akhir</th>
            </tr>

            <!-- Sub Headers Metric Columns -->
            <tr class="bg-[#ED1C24] text-white font-bold text-[10px] uppercase tracking-tight border-b border-red-600 text-center">
                <!-- Revenue All -->
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">TARGET</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">ACTUAL</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[70px]">ACH%</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[60px]">SCORE</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[60px]">WEIGHT</th>
                <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E] min-w-[85px]">FINAL SCORE</th>

                <!-- Revenue Broadband -->
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">TARGET</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">ACTUAL</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[70px]">ACH%</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[60px]">SCORE</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[60px]">WEIGHT</th>
                <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E] min-w-[85px]">FINAL SCORE</th>

                <!-- Revenue Redeem PV -->
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">TARGET</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">ACTUAL</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[70px]">ACH%</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[60px]">SCORE</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[60px]">WEIGHT</th>
                <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E] min-w-[85px]">FINAL SCORE</th>

                <!-- Revenue RGB -->
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">TARGET</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">ACTUAL</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[70px]">ACH%</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[60px]">SCORE</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[60px]">WEIGHT</th>
                <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E] min-w-[85px]">FINAL SCORE</th>

                <!-- Growth Revenue -->
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">OMZET M1</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">MTD  M1</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">TGT  3%</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[110px]">MTD</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[75px]">GROWTH%</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[75px]">GROWTH TGT</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[95px]">REV GROWTH ACH%</th>

                <!-- Outlet PJP -->
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[80px]">OUTLET PJP</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[80px]">PJP GROWTH</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[80px]">RATIO PJP</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[100px]">OMZET OUTLET ACH%</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white font-black min-w-[60px]">SCORE</th>
                <th class="py-2 px-3 border-r border-red-600/80 text-white min-w-[60px]">WEIGHT</th>
                <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E] min-w-[85px]">FINAL SCORE</th>

                <!-- Final Score -->
                <th class="py-2 px-4 font-black text-white bg-[#B91C1C] min-w-[120px]">TOTAL FINAL SCORE</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-200/70 font-medium text-slate-800 bg-white">
            @forelse($rankedData as $item)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <!-- City / Cluster -->
                    <td class="py-2.5 px-4 font-extrabold text-slate-900 border-r border-slate-200 bg-white sticky left-0 z-10">
                        <span class="font-extrabold text-black uppercase tracking-tight text-[13px]">{{ $item->city ?? $item->kabupaten ?? '-' }}</span>
                        <span class="block text-[10px] font-semibold text-black/50 uppercase">{{ $item->cluster ?? $item->cluster_name ?? '-' }}</span>
                    </td>

                    <!-- Periode -->
                    <td class="py-2.5 px-3 text-center font-bold text-black/90 text-[12px] border-r border-slate-200">
                        {{ $item->period_month }} {{ $item->period_year }}
                    </td>

                    <!-- Type -->
                    <td class="py-2.5 px-3 text-center font-bold text-black/90 border-r border-slate-200">
                        <span class="px-2 py-0.5 rounded text-[11px] font- uppercase">
                            {{ $item->type ?? 'HIGH' }}
                        </span>
                    </td>

                    <!-- Revenue All -->
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->target_rev_all ?: $item->target_revenue_all, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->actual_rev_all ?: $item->mtd_revenue_all, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-center font-medium  border-r border-slate-200 {{ ($item->ach_rev_all ?: $item->ach_revenue_all) >= 100 ? 'text-emerald-700 bg-emerald-50/30' : 'text-slate-700' }}">{{ number_format($item->ach_rev_all ?: $item->ach_revenue_all, 1, ',', '.') }}%</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">{{ number_format($item->score_rev_all ?: 2, 0) }},0</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">15%</td>
                    <td class="py-2.5 px-3 text-center font-extrabold border-r border-slate-200 bg-blue-50/40 text-blue-700">{{ number_format($item->final_score_rev_all ?: 0.3, 2, ',', '.') }}</td>

                    <!-- Revenue Broadband -->
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->target_rev_bb ?: $item->target_broadband, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->actual_rev_bb ?: $item->mtd_broadband, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200 {{ ($item->ach_rev_bb ?: $item->ach_broadband) >= 100 ? 'text-emerald-700 bg-emerald-50/30' : 'text-slate-700' }}">{{ number_format($item->ach_rev_bb ?: $item->ach_broadband, 1, ',', '.') }}%</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">{{ number_format($item->score_rev_bb ?: 2, 0) }},0</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">{{ number_format($item->weight_rev_bb ?: 15, 0) }}%</td>
                    <td class="py-2.5 px-3 text-center font-extrabold border-r border-slate-200 bg-blue-50/40 text-blue-700">{{ number_format($item->final_score_rev_bb ?: 0.3, 2, ',', '.') }}</td>

                    <!-- Revenue Redeem PV -->
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->target_rev_pv ?: $item->target_redeem, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->actual_rev_pv ?: $item->mtd_redeem, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200 {{ ($item->ach_rev_pv ?: $item->ach_redeem) >= 100 ? 'text-emerald-700 bg-emerald-50/30' : 'text-slate-700' }}">{{ number_format($item->ach_rev_pv ?: $item->ach_redeem, 1, ',', '.') }}%</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">{{ number_format($item->score_rev_pv ?: 2, 0) }},0</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">{{ number_format($item->weight_rev_pv ?: 25, 0) }}%</td>
                    <td class="py-2.5 px-3 text-center font-extrabold border-r border-slate-200 bg-blue-50/40 text-blue-700">{{ number_format($item->final_score_rev_pv ?: 0.5, 2, ',', '.') }}</td>

                    <!-- Revenue RGB -->
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->target_rgb, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->actual_rgb ?: $item->mtd_rgb, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200 {{ $item->ach_rgb >= 100 ? 'text-emerald-700 bg-emerald-50/30' : 'text-slate-700' }}">{{ number_format($item->ach_rgb, 1, ',', '.') }}%</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">{{ number_format($item->score_rgb ?: 2, 0) }},0</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">20%</td>
                    <td class="py-2.5 px-3 text-center font-extrabold border-r border-slate-200 bg-blue-50/40 text-blue-700">{{ number_format($item->final_score_rgb ?: 0.4, 2, ',', '.') }}</td>

                    <!-- Growth Revenue -->
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->omzet_rev_m1 ?: $item->revenue_last_month, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->mtd_m1 ?: $item->revenue_last_month, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200 text-slate-700">{{ number_format($item->tgt_3_percent, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->mtd ?: $item->actual_rev_all, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200 {{ ($item->growth ?: $item->growth_mom) >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($item->growth ?: $item->growth_mom, 1, ',', '.') }}%</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200 text-slate-600">3,0%</td>
                    <td class="py-2.5 px-3 text-center font-extrabold border-r border-slate-200 text-slate-900">{{ number_format($item->rev_growth_ach_percent, 1, ',', '.') }}%</td>

                    <!-- Outlet PJP -->
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->outlet_pjp, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-right font-medium border-r border-slate-200">{{ number_format($item->outlet_pjp_growth, 0, ',', '.') }}</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">{{ number_format($item->ratio_outlet_pjp, 1, ',', '.') }}%</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200 text-slate-900">{{ number_format($item->omzet_outlet_ach, 1, ',', '.') }}%</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">{{ number_format($item->score_omzet ?: 2, 0) }},0</td>
                    <td class="py-2.5 px-3 text-center font-medium border-r border-slate-200">25%</td>
                    <td class="py-2.5 px-3 text-center font-extrabold border-r border-slate-200 bg-blue-50/40 text-blue-700">{{ number_format($item->final_score_omzet ?: 0.5, 2, ',', '.') }}</td>

                    <!-- Final Result -->
                    <td class="py-2.5 px-4 text-center font-black bg-amber-50/90 text-amber-700 text-xs">
                        {{ number_format($item->total_score ?: $item->final_score, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="40" class="py-10 text-center text-slate-400 font-medium">
                        <i class="bi bi-trophy text-4xl block mb-2 text-slate-300"></i>
                        <p class="font-bold text-sm text-slate-600">Tidak ada data kalkulasi yang sesuai dengan filter.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
