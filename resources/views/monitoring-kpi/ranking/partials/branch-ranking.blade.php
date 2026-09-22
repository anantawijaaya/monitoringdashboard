<!-- ==================== VIEW 1: RANKING TOP 3 ATAS & BAWAH PER BRANCH ==================== -->
<div id="viewRanking" class="{{ ($viewMode ?? 'ranking') === 'ranking' ? '' : 'hidden' }} space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($branchRankings as $bCode => $bData)
            <div class="rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between">
                
                <!-- Branch Card Header -->
                <div class="bg-gradient-to-r from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-4 flex items-center justify-between border-b border-red-600/40">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/20 text-white flex items-center justify-center font-black text-sm shadow-xs backdrop-blur-xs">
                            <i class="bi bi-building-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black tracking-wide text-white uppercase">{{ $bData['name'] }}</h3>
                            <p class="text-[10px] font-black text-white/90">Manager: <span class="text-[11px] font-black text-white">{{ $bData['manager'] }}</span></p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-black/20 text-white text-[10px] font-extrabold border border-white/20 backdrop-blur-xs">
                        {{ $bData['total_cities'] }} City
                    </span>
                </div>

                <!-- Top 3 Atas Section -->
                <div class="p-4 space-y-3 bg-white">
                    <div class="flex items-center justify-between pb-1.5">
                        <span class="text-[14px] font-black text-emerald-800 flex items-center gap-1.5 uppercase tracking-wider">
                            Peringkat 3 Teratas 
                        </span>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-emerald-200/80">
                        <table class="w-full text-left text-xs border-collapse table-fixed">
                            <thead class="bg-[#ED1C24] text-white font-black uppercase text-[10px] tracking-wider">
                                <tr>
                                    <th class="py-2.5 px-3 text-center w-[15%]">RANK</th>
                                    <th class="py-2.5 px-3 w-[35%]">CITY</th>
                                    <th class="py-2.5 px-3 w-[30%]">CLUSTER</th>
                                    <th class="py-2.5 px-3 text-center w-[20%]">TOTAL SKOR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-50 font-medium text-slate-700 bg-white">
                                @forelse($bData['top3'] as $tIdx => $item)
                                    <tr class="hover:bg-emerald-50/40 transition-colors">
                                        <td class="py-2.5 px-3 text-center font-black text-xs">
                                            @php $tRank = $tIdx + 1; @endphp
                                            @if($tRank <= 9)
                                                <i class="bi bi-{{ $tRank }}-square-fill text-emerald-600 text-xl leading-none inline-block"></i>
                                            @else
                                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-black text-xs border border-emerald-200 gap-1">
                                                    <i class="bi bi-square-fill text-emerald-600 text-[10px]"></i> #{{ $tRank }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 font-black text-black/80 uppercase tracking-tight truncate">
                                            {{ $item->city ?? $item->kabupaten ?? '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 font-black text-black/80 uppercase tracking-tight truncate">
                                            {{ $item->cluster ?? $item->cluster_name ?? '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-black">
                                            <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 font-black text-xs border border-emerald-200">
                                                {{ number_format($item->total_score ?: $item->final_score, 2, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-slate-400 font-semibold text-xs">Tidak ada data 3 teratas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top 3 Terbawah Section -->
                <div class="p-4 pt-2 space-y-3 bg-white rounded-b-2xl border-t border-slate-100">
                    <div class="flex items-center justify-between pb-1.5">
                        <span class="text-[14px] font-black text-red-800 flex items-center gap-1.5 uppercase tracking-wider">
                            Peringkat 3 Terbawah
                        </span>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-rose-200/80">
                        <table class="w-full text-left text-xs border-collapse table-fixed">
                            <thead class="bg-[#ED1C24] text-white font-black uppercase text-[10px] tracking-wider">
                                <tr>
                                    <th class="py-2.5 px-3 text-center w-[15%]">POSISI</th>
                                    <th class="py-2.5 px-3 w-[35%]">CITY</th>
                                    <th class="py-2.5 px-3 w-[30%]">CLUSTER</th>
                                    <th class="py-2.5 px-3 text-center w-[20%]">TOTAL SKOR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rose-50 font-medium text-slate-700 bg-white">
                                @forelse($bData['bottom3'] as $bIdx => $item)
                                    <tr class="hover:bg-rose-50/40 transition-colors">
                                        <td class="py-2.5 px-3 text-center font-black text-xs">
                                            @php $bRank = $bIdx + 1; @endphp
                                            <i class="bi bi-{{ $bRank }}-square-fill text-red-600 text-xl leading-none inline-block"></i>
                                        </td>
                                        <td class="py-2.5 px-3 font-black text-black/80 uppercase tracking-tight truncate">
                                            {{ $item->city ?? $item->kabupaten ?? '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 font-black text-black/80 uppercase tracking-tight truncate">
                                            {{ $item->cluster ?? $item->cluster_name ?? '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-black">
                                            <span class="px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 font-black text-xs border border-rose-200">
                                                {{ number_format($item->total_score ?: $item->final_score, 2, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-slate-400 font-semibold text-xs">Tidak ada data 3 terbawah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
</div>
