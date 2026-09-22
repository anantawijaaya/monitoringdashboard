<!-- DETAILED DATA TABLE MATCHING 5 PARAMETERS -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-card p-5 sm:p-6">
    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <h3 class="text-base font-extrabold text-gray-900">Rincian Data Pendapatan </h3>
        </div>
        
    </div>

    <!-- Table -->
    <div class="mt-4 overflow-x-auto rounded-2xl border border-gray-200 shadow-sm">
        <table class="w-full text-center text-xs whitespace-nowrap">
            <thead>
                <!-- Top Group Header -->
                <tr class="bg-[#ED1C24] text-white uppercase text-[11px] font-black tracking-wider select-none">
                    <th rowspan="2" class="px-3 py-3 text-center w-8 bg-[#ED1C24] text-white font-black">NO</th>
                    <th rowspan="2" class="px-4 py-3 min-w-[150px] text-center bg-[#ED1C24] text-white font-black ">CLUSTER</th>
                    <th rowspan="2" class="px-4 py-3 min-w-[220px] text-center bg-[#ED1C24] text-white font-black">KABUPATEN / KOTA</th>
                    <th rowspan="2" class="px-3 py-3 text-center min-w-[90px] bg-[#ED1C24] text-white font-black">PERIODE</th>
                    <th colspan="3" class="px-3 py-2.5 text-center bg-[#ED1C24] text-white font-black">REVENUE ALL</th>
                    <th colspan="3" class="px-3 py-2.5 text-center bg-[#ED1C24] text-white font-black">REVENUE BROADBAND</th>
                    <th colspan="3" class="px-3 py-2.5 text-center bg-[#ED1C24] text-white font-black">REVENUE REDEEM PV</th>
                    <th colspan="3" class="px-3 py-2.5 text-center bg-[#ED1C24] text-white font-black">REVENUE RGB</th>
                    <th colspan="3" class="px-3 py-2.5 text-center bg-[#ED1C24] text-white font-black">GROWTH OMZET</th>
                    <th rowspan="2" class="px-3 py-3 text-center min-w-[110px] bg-[#ED1C24] text-white font-black">CATATAN</th>
                    @if(Auth::user()->isAdmin())
                        <th rowspan="2" class="px-3 py-3 text-center w-20 bg-[#ED1C24] text-white font-black">AKSI</th>
                    @endif
                </tr>
                <!-- Sub Header Columns -->
                <tr class="text-[10px] text-white font-bold bg-[#C8102E] select-none">
                    <!-- 2. Revenue All -->
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-red-100 font-bold">Target (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">MTD (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">Ach (%)</th>

                    <!-- 3. Broadband -->
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-red-100 font-bold">Target (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">MTD (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">Ach (%)</th>

                    <!-- 4. Redeem PV -->
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-red-100 font-bold">Target (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">MTD (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">Ach (%)</th>

                    <!-- 5. Revenue RGB (Positioned to the left of Growth Omzet) -->
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-red-100 font-bold">Target (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">MTD (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">Ach (%)</th>

                    <!-- 6. Growth -->
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-red-100 font-bold">Bulan Lalu (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">Bulan Ini (Rp)</th>
                    <th class="px-3 py-2 text-center bg-[#C8102E] text-white font-black">MoM (%)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($clusterRevenues as $index => $item)
                    @php
                        $achAll = $item->ach_revenue_all ?: $item->achievement_rate;
                        $targetAll = $item->target_revenue_all ?: $item->target_revenue;
                        $mtdAll = $item->mtd_revenue_all ?: $item->revenue_all;
                        $lastMonth = $item->revenue_last_month;
                        $currMonth = $item->revenue_current_month ?: $mtdAll;
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <!-- No -->
                        <td class="px-3 py-3 text-center border-r border-slate-200 bg-white sticky left-0 z-20">
                            <span class="text-xs font-semibold tracking-tight">{{ $clusterRevenues->firstItem() + $index }}</span>
                        </td>
                        
                        <!-- 1. Cluster -->
                        <td class="px-4 py-3 text-left border-r border-slate-200 bg-white sticky left-9 z-10">
                            <span class="text-xs font-semibold tracking-tight uppercase">{{ $item->cluster_name }}</span>
                        </td>

                        <!-- Kabupaten / Kota -->
                        <td class="px-4 py-3 text-center border-r border-slate-200">
                            <span class="text-xs font-semibold tracking-tight uppercase">{{ $item->kabupaten ?: '-' }}</span>
                        </td>

                        <!-- Periode -->
                        <td class="px-3 py-3 text-center font-semibold tracking-tight uppercase">{{ $item->period_month }}</td>

                        <!-- 2. REVENUE ALL -->
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($targetAll, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($mtdAll, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            <span class="inline-block px-2.5 py-1 text-[11px] font-black {{ $achAll >= 100 ? 'bg-emerald-400 text-white' : ($achAll >= 90 ? 'bg-yellow-200 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
                                {{ number_format($achAll, 1) }}%
                            </span>
                        </td>

                        <!-- 3. BROADBAND -->
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($item->target_broadband, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($item->mtd_broadband ?: $item->revenue_broadband, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            <span class="inline-block px-2.5 py-1 text-[11px] font-black {{ $item->ach_broadband >= 100 ? 'bg-emerald-400 text-white' : ($item->ach_broadband >= 90 ? 'bg-yellow-200 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
                                {{ number_format($item->ach_broadband, 1) }}%
                            </span>
                        </td>

                        <!-- 4. REDEEM PV -->
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($item->target_redeem, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($item->mtd_redeem ?: $item->revenue_redeem_pv, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            <span class="inline-block px-2.5 py-1 text-[11px] font-black {{ $item->ach_redeem >= 100 ? 'bg-emerald-400 text-white' : ($item->ach_redeem >= 90 ? 'bg-yellow-200 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
                                {{ number_format($item->ach_redeem, 1) }}%
                            </span>
                        </td>

                        <!-- 5. REVENUE RGB (Positioned to the left of Growth / Revenue Bln Lalu) -->
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($item->target_rgb, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($item->mtd_rgb ?: $item->revenue_rgb, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            <span class="inline-block px-2.5 py-1 text-[11px] font-black {{ $item->ach_rgb >= 100 ? 'bg-emerald-400 text-white' : ($item->ach_rgb >= 90 ? 'bg-yellow-200 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
                                {{ number_format($item->ach_rgb, 1) }}%
                            </span>
                        </td>

                        <!-- 5. GROWTH REVENUE -->
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($lastMonth, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            {{ number_format($currMonth, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                            <span class="inline-block px-2.5 py-1 text-[11px] font-black {{ $item->growth_mom >= 1.0 ? 'bg-emerald-400 text-white' : ($item->growth_mom >= 0 ? 'bg-yellow-200 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
                                {{ $item->growth_mom >= 0 ? '+' : '' }}{{ number_format($item->growth_mom, 1) }}%
                            </span>
                        </td>

                        <!-- Notes / Status -->
                        <td class="px-3 py-3 text-left">
                            @php
                                $catatanText = $item->catatan ?? ($item->notes ?: ($item->status ?: 'Mencapai Target'));
                                if (str_contains(strtolower($catatanText), 'melampaui') || $item->growth_mom >= 1.0) {
                                    $catatanText = 'Melampaui Target';
                                    $catatanBadgeClass = 'font-semibold';
                                } elseif (str_contains(strtolower($catatanText), 'tidak') || $item->growth_mom < 0) {
                                    $catatanText = 'Tidak Mencapai Target';
                                    $catatanBadgeClass = 'font-semibold';
                                } else {
                                    $catatanText = 'Mencapai Target';
                                    $catatanBadgeClass = 'font-semibold';
                                }
                            @endphp
                            <span class="text-[11px] font-extrabold {{ $catatanBadgeClass }}" title="{{ $catatanText }}">
                                {{ $catatanText }}
                            </span>
                        </td>

                        <!-- Actions: Edit & Delete (Admin Only) -->
                        @if(Auth::user()->isAdmin())
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Edit Button -->
                                    <button type="button" 
                                            onclick="openEditModal({{ json_encode($item) }})" 
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer" 
                                            title="Edit Data Cluster">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" 
                                            onclick="handleDeleteCluster(event, '{{ route('revenue.destroy', $item->id) }}', '{{ addslashes($item->cluster_name) }}')" 
                                            class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors cursor-pointer" 
                                            title="Hapus Data">
                                        <i class="bi bi-trash3 text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="16" class="px-4 py-12 text-center text-gray-400">
                            <i class="bi bi-folder2-open text-4xl block mb-2 text-gray-300"></i>
                            <p class="font-bold text-gray-600">Tidak ada data revenue yang sesuai filter.</p>
                            <p class="text-xs text-gray-400 mt-1">Gunakan tombol "Tambah Data Manual" di atas untuk menambahkan data baru.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-end text-xs text-gray-500 [&_p]:hidden">
        <div>{{ $clusterRevenues->links() }}</div>
    </div>
</div>
