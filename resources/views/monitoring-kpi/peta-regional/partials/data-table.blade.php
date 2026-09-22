<!-- 5. DATA OUTLET TABLE & MANAGEMENT TOOLBAR -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
    
    <!-- Table Header & Action Toolbar -->
    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h3 class="text-base font-extrabold text-gray-900">Daftar Data Lengkap Outlet Regional Bali Nusra</h3>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Export Button -->
            <a href="{{ route('regional-map.export', request()->all()) }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold transition-all shadow-xs">
                <span>Export Data</span>
            </a>

            @if(!Auth::user()->isVisitor())
                <!-- Import Modal Button -->
                <button onclick="openImportModal()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-bold transition-all shadow-sm cursor-pointer">
                    <span>Import Data</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Table Elements -->
    <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#ED1C24] text-[12px] font-extrabold text-white uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-12">No</th>
                    <th class="py-3.5 px-4">ID Outlet</th>
                    <th class="py-3.5 px-4">Branch & Cluster</th>
                    <th class="py-3.5 px-4">Kabupaten</th>
                    <th class="py-3.5 px-4">Koordinat GPS</th>
                    <th class="py-3.5 px-4 text-center">Total Omzet</th>
                    <th class="py-3.5 px-4 text-center">Flag Omzet</th>
                    <th class="py-3.5 px-4 text-center">Status</th>
                    <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-700">
                @forelse($outlets as $index => $o)
                    <tr class="hover:bg-red-50/30 transition-colors group">
                        <td class="py-3.5 px-4 text-center text-gray-400 font-bold">
                             {{ $outlets->firstItem() + $index }}
                        </td>
                        <td class="py-3.5 px-4 font-medium">
                            <span>{{ $o->id_outlet }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="text-xs font-semibold text-black/70 tracking-tight uppercase">{{ $o->branch }}</p>
                            <p class="text-[11px] text-gray-400">{{ $o->cluster }}</p>
                        </td>
                        <td class="text-xs font-semibold text-black/70 tracking-tight uppercase">
                            {{ $o->kabupaten }}
                        </td>
                        <td class="py-3.5 px-4 font-medium">
                            {{ number_format($o->longitude, 4) }}, {{ number_format($o->latitude, 4) }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-medium">
                            {{ $o->formatted_omzet }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-medium">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black {{ $o->flag_badge_class }}">
                                {{ $o->formatted_flag }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-gray-700">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $o->flag_color_hex }}"></span>
                                <span>{{ ucfirst($o->flag_color) }}</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="showOutletDetail({{ json_encode($o) }})" 
                                        title="Lihat Detail"
                                        class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors cursor-pointer">
                                    <i class="bi bi-eye-fill"></i>
                                </button>

                                @if(!Auth::user()->isVisitor())
                                    <button onclick="openEditModal({{ json_encode($o) }})" 
                                            title="Edit Data"
                                            class="p-1.5 hover:bg-blue-100 text-black transition-colors cursor-pointer">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <form action="{{ route('regional-map.destroy', $o->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus outlet {{ $o->id_outlet }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Outlet"
                                                class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors cursor-pointer">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-gray-500">
                            <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                <i class="bi bi-geo-alt-slash text-2xl"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-700">Tidak ada data outlet yang sesuai</p>
                            <p class="text-xs text-gray-400 mt-1">Coba sesuaikan filter pencarian atau impor data baru.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Pagination -->
    <div class="p-4 border-t border-gray-100 flex items-center justify-between">
        <div class="text-xs text-gray-500 font-medium">
            Halaman {{ $outlets->currentPage() }} dari {{ $outlets->lastPage() }}
        </div>
        <div>
            {{ $outlets->links() }}
        </div>
    </div>

</div>
