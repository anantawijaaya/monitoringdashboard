<!-- DATA LENGKAP TABLE CONTAINER -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-card p-5 sm:p-6 space-y-4">
    
    <!-- Table Section Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-100">
        <h2 class="text-[15px] font-black text-gray-900">
            Tabel Hirarki Regional Bali Nusra
        </h2>

        <!-- Action Buttons: Import CSV/Excel, Template, Export Excel & Filter -->
        <div class="flex flex-wrap items-center gap-2.5">
            
            <!-- Button 1: Import CSV / Excel (Admin Only) -->
            @if(Auth::user()->isAdmin())
                <button type="button" onclick="openImportHierarchyModal()" 
                        class="px-4 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-800 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2 cursor-pointer">
                    <span>Import Data</span>
                </button>
            @endif  

            <!-- Button 2: Export Excel -->
            <a href="{{ route('hierarchy.export', request()->query()) }}" 
               class="px-3.5 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2">
                <span>Export Data</span>
            </a>

            <!-- Button 4: Filter Toggle Button (Red) -->
            <button type="button" onclick="toggleFilterBox()" id="filterToggleBtn"
                    class="px-4 py-2 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer">
                <i class="bi bi-funnel-fill text-xs"></i>
                <span>Filter</span>
            </button>
        </div>
    </div>

    @include('monitoring-kpi.hierarchy.partials.filter-panel')

    <!-- THE DATA TABLE -->
    <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm">
        <table class="w-full text-left text-xs whitespace-nowrap">
            <!-- Solid Red Header (#ED1C24) -->
            <thead>
                <tr class="bg-[#ED1C24] text-white uppercase text-[11px] font-black tracking-wider select-none">
                    <th class="px-3.5 py-3.5 text-center w-12">NO</th>
                    <th class="px-4 py-3.5">KABUPATEN / KOTA</th>
                    <th class="px-4 py-3.5">CLUSTER</th>
                    <th class="px-4 py-3.5">MITRA</th>
                    <th class="px-4 py-3.5">BRANCH</th>
                    <th class="px-4 py-3.5 text-center">JUMLAH OUTLET</th>
                    <th class="px-4 py-3.5">MANAGER BRANCH</th>
                    @if(Auth::user()->isAdmin())
                        <th class="px-4 py-3.5 text-center">AKSI</th>
                    @endif
                </tr>
            </thead>
            
            <!-- Table Body with Clean White Background -->
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800">
                @forelse($paginatedData as $row)
                    <tr id="row-{{ $row->id }}" class="bg-white hover:bg-gray-50/80 transition-colors">
                        <!-- NO -->
                        <td class="px-3.5 py-3 text-center font-bold text-gray-600 border-r border-slate-200 ">
                            {{ $loop->iteration + ($paginatedData->firstItem() ? $paginatedData->firstItem() - 1 : 0) }}
                        </td>

                        <!-- KABUPATEN / KOTA -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            {{ $row->kabupaten }}
                        </td>

                        <!-- CLUSTER -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            {{ $row->cluster }}
                        </td>

                        <!-- MITRA -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            {{ $row->mitra }}
                        </td>

                        <!-- BRANCH -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            {{ $row->branch }}
                        </td>

                        <!-- JUMLAH OUTLET -->
                        <td class="px-4 py-3 text-center font-medium border-r border-slate-200">
                            {{ $row->jumlah_outlet }}
                        </td>

                        <!-- MANAGER BRANCH -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            {{ $row->manager_branch }}
                        </td>

                        <!-- AKSI (EDIT BUTTON - Admin Only) -->
                        @if(Auth::user()->isAdmin())
                            <td class="px-4 py-3 text-center">
                                <button type="button" 
                                        onclick='openEditHierarchyModal(@json($row))'
                                        class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-red-50 text-gray-700 hover:text-[#ED1C24] border border-gray-200 hover:border-red-300 shadow-sm transition-all text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer"
                                        title="Edit Data {{ $row->kabupaten }}">
                                    <i class="bi bi-pencil-square text-[#ED1C24]"></i>
                                    <span>Edit</span>
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400 bg-white">
                            <i class="bi bi-inbox text-3xl block mb-2 text-gray-300"></i>
                            <p class="font-bold text-sm text-gray-600">Tidak ada data yang sesuai dengan pencarian / filter.</p>
                            <a href="{{ route('hierarchy.index') }}" class="text-xs text-red-600 font-bold hover:underline mt-1 inline-block">Reset Filter</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- FOOTER CONTROLS & PAGINATION -->
    <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-600 font-medium">
        
        <!-- Left: Info Data Count -->
        <div>

        </div>

        <!-- Right: Per-Page Dropdown & Page Navigation Buttons -->
        <div class="flex items-center gap-3">
            <!-- Per Page Selector Form -->
            <form method="GET" action="{{ route('hierarchy.index') }}" class="flex items-center gap-1.5">
                @if(!empty($search)) <input type="hidden" name="search" value="{{ $search }}"> @endif
                @if($selectedBranch !== 'all') <input type="hidden" name="branch" value="{{ $selectedBranch }}"> @endif
                @if($selectedCluster !== 'all') <input type="hidden" name="cluster" value="{{ $selectedCluster }}"> @endif
                

            </form>

            <!-- Pagination Buttons Matching Screenshot (< [1] [2] [3] [4] >) -->
            <div class="flex items-center gap-1">
                <!-- Previous Page -->
                @if($paginatedData->onFirstPage())
                    <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-300 flex items-center justify-center text-xs cursor-not-allowed">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $paginatedData->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 flex items-center justify-center text-xs font-bold transition-colors">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                @endif

                <!-- Page Numbers -->
                @for($p = 1; $p <= $paginatedData->lastPage(); $p++)
                    @if($p == $paginatedData->currentPage())
                        <span class="w-8 h-8 rounded-lg bg-[#ED1C24] text-white flex items-center justify-center text-xs font-black shadow-sm">
                            {{ $p }}
                        </span>
                    @else
                        <a href="{{ $paginatedData->url($p) }}" class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 flex items-center justify-center text-xs font-bold transition-colors">
                            {{ $p }}
                        </a>
                    @endif
                @endfor

                <!-- Next Page -->
                @if($paginatedData->hasMorePages())
                    <a href="{{ $paginatedData->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 flex items-center justify-center text-xs font-bold transition-colors">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-300 flex items-center justify-center text-xs cursor-not-allowed">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>

    </div>

</div>
