<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Cluster, Mitra, Branch & Jumlah Outlet - Telkomsel Regional Bali Nusra</title>

    <!-- Prevent Sidebar Flash/Glitch on Page Load & Navigation -->
    <script>
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.documentElement.classList.add('sidebar-is-collapsed');
        }
    </script>
    <style>
        html.sidebar-is-collapsed #mainSidebar {
            margin-left: -15rem !important;
            transition: none !important;
        }
    </style>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            zoom: 0.9;
            -moz-transform: scale(0.9);
            -moz-transform-origin: top center;
        }

        /* Custom subtle scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        /* Custom soft shadows */
        .shadow-card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.03);
        }
        .shadow-card-hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            zoom: 0.9;
            -moz-transform: scale(0.9);
            -moz-transform-origin: top center;
        }

        .sidebar-item {
            transition: all 0.2s ease-in-out;
            background-color: transparent;
        }

        .sidebar-item:hover {
            background-color: #ED1C24 !important;
            color: #FFFFFF !important;
        }

        .sidebar-item:hover i,
        .sidebar-item:hover svg {
            color: #FFFFFF !important;
        }

        .sidebar-item.active {
            background-color: #ED1C24 !important;
            color: #FFFFFF !important;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(237, 28, 36, 0.4);
        }

        .sidebar-item.active i,
        .sidebar-item.active svg {
            color: #FFFFFF !important;
        }
    </style>
</head>
<body class="h-full flex flex-col bg-[#F8FAFC] text-slate-800 antialiased selection:bg-red-500 selection:text-white">

    <!-- TOP HEADER BAR (#121212) -->
    <header class="bg-[#121212] border-b border-gray-800/80 text-white h-16 px-4 sm:px-6 flex items-center justify-between shrink-0 relative z-30 select-none">
        <!-- Left: Logo PNG & Regional Title -->
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-balinusra.png') }}" alt="Bali Nusra Logo" class="h-10 sm:h-11 w-auto object-contain">
            <h1 class="text-sm sm:text-base font-extrabold text-white tracking-wide">Telkomsel Regional Bali Nusra</h1>
        </div>

        <!-- Right: Profile Pill Box & Dropdown -->
        <div class="relative" id="userDropdownContainer">
            <button id="userDropdownBtn" 
                    onclick="toggleUserDropdown(event)"
                    class="flex items-center gap-3 px-3.5 py-1.5 rounded-full bg-[#222225] hover:bg-[#2c2c30] border border-white/10 transition-all cursor-pointer">
                <div class="w-7 h-7 rounded-full bg-[#ED1C24] text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                    {{ strtoupper(substr($user->name ?? 'V', 0, 1)) }}
                </div>
                <div class="flex flex-col text-left">
                    <span class="text-xs font-bold text-white leading-tight">
                        {{ $user->name ?? 'Visitor Telkomsel' }}
                    </span>
                    <span class="text-[9px] font-black uppercase text-gray-400 leading-tight tracking-wider">
                        {{ $user->role ?? 'VISITOR' }}
                    </span>
                </div>
                <i id="dropdownArrow" class="bi bi-chevron-down text-xs text-gray-400 ml-1 transition-transform duration-200"></i>
            </button>

            <!-- Profile Dropdown Menu -->
            <div id="userDropdownMenu" 
                 class="hidden absolute right-0 mt-2 w-52 bg-[#1a1a1d] border border-gray-800 rounded-2xl shadow-xl py-2 z-50 text-xs">
                <div class="px-4 py-2 border-b border-gray-800/80">
                    <p class="font-bold text-white truncate">{{ $user->name ?? 'Visitor Telkomsel' }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ $user->email ?? 'visitor@telkomsel.co.id' }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-red-400 hover:bg-white/5 transition-colors font-bold text-left cursor-pointer">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- ==================== MAIN BODY CONTAINER (Sidebar + Main Content) ==================== -->
    <div class="flex-1 flex min-h-0 overflow-hidden relative">

        @include('layouts.sidebar')

        <!-- MAIN SCROLLABLE CONTENT AREA -->
        <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- HEADER TITLE -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h1 class="text-xl sm:text-xl font-black text-gray-900 tracking-tight">
                            HIRARKI REGIONAL BALI NUSRA
                        </h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
                            Informasi Lengkap Mengenai Data Cluster, Mitra, Branch, Jumlah Outlet dan Manager Branch
                        </p>
                    </div>
                </div>

                <!-- 4 SUMMARY STATS KPI CARDS (ICON LEFT - TEXT CENTERED) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    
                    <!-- CARD 1: TOTAL CLUSTER -->
                    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shrink-0 backdrop-blur-sm">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="flex-1 text-center flex flex-col items-center justify-center">
                            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">Total Cluster</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5 text-center">{{ $totalClusterCount }}</h3>
                            <span class="text-[11px] text-white font-medium block text-center">Cluster Aktif</span>
                        </div>
                    </div>

                    <!-- CARD 2: TOTAL MITRA -->
                    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shrink-0 backdrop-blur-sm">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="flex-1 text-center flex flex-col items-center justify-center">
                            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">Total Mitra</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5 text-center">{{ $totalMitraCount }}</h3>
                            <span class="text-[11px] text-white font-medium block text-center">Mitra Aktif</span>
                        </div>
                    </div>

                    <!-- CARD 3: TOTAL OUTLET -->
                    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shrink-0 backdrop-blur-sm">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div class="flex-1 text-center flex flex-col items-center justify-center">
                            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">Total Outlet</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5 text-center">{{ $totalOutletDisplay }}</h3>
                            <span class="text-[11px] text-white font-medium block text-center">Sebaran {{ $totalKabupatenCount }} Kabupaten</span>
                        </div>
                    </div>

                    <!-- CARD 4: TOTAL BRANCH -->
                    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shrink-0 backdrop-blur-sm">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div class="flex-1 text-center flex flex-col items-center justify-center">
                            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">Total Branch</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5 text-center">{{ $totalBranchCount }}</h3>
                            <span class="text-[11px] text-white font-medium block text-center">Branch Aktif</span>
                        </div>
                    </div>

                </div>

                <!-- DATA LENGKAP TABLE CONTAINER -->
                <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-5 sm:p-6 space-y-4">
                    
                    <!-- Table Section Header & Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-gray-100">
                        <h2 class="text-base sm:text-lg font-black text-gray-900">
                            Data Lengkap
                        </h2>

                        <!-- Action Buttons: Import CSV/Excel, Template, Export Excel & Filter -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            
                            <!-- Button 1: Import CSV / Excel (Admin Only) -->
                            @if(Auth::user()->isAdmin())
                                <button type="button" onclick="openImportHierarchyModal()" 
                                        class="px-4 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-800 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2 cursor-pointer">
                                    <i class="bi bi-file-earmark-spreadsheet text-sm text-emerald-600"></i>
                                    <span>Import CSV / Excel</span>
                                </button>
                            @endif

                            <!-- Button 2: Export Excel -->
                            <a href="{{ route('hierarchy.export', request()->query()) }}" 
                               class="px-3.5 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2">
                                <i class="bi bi-file-earmark-arrow-down text-sm text-amber-600"></i>
                                <span>Export Excel</span>
                            </a>

                            <!-- Button 4: Filter Toggle Button (Red) -->
                            <button type="button" onclick="toggleFilterBox()" id="filterToggleBtn"
                                    class="px-4 py-2 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer">
                                <i class="bi bi-funnel-fill text-xs"></i>
                                <span>Filter</span>
                            </button>
                        </div>
                    </div>

                    <!-- Expandable Filter Panel -->
                    <div id="filterPanel" class="{{ (!empty($search) || $selectedBranch !== 'all' || $selectedCluster !== 'all' || $selectedMitra !== 'all') ? '' : 'hidden' }} p-4 rounded-2xl bg-gray-50/80 border border-gray-200 transition-all">
                        <form method="GET" action="{{ route('hierarchy.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                            <!-- Search -->
                            <div class="lg:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 mb-1">Cari Wilayah / Cluster / Mitra</label>
                                <div class="relative">
                                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kabupaten, cluster, mitra, manager..." 
                                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                                </div>
                            </div>

                            <!-- Filter Branch -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Filter Branch</label>
                                <select name="branch" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                                    <option value="all">Semua Branch</option>
                                    @foreach($availableBranches as $b)
                                        <option value="{{ $b }}" {{ $selectedBranch === $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Cluster -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Filter Cluster</label>
                                <select name="cluster" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                                    <option value="all">Semua Cluster</option>
                                    @foreach($availableClusters as $c)
                                        <option value="{{ $c }}" {{ $selectedCluster === $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Reset / Submit -->
                            <div class="flex items-end gap-2">
                                <button type="submit" class="flex-1 px-3 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-black transition-all">
                                    Terapkan
                                </button>
                                <a href="{{ route('hierarchy.index') }}" class="px-3 py-2 rounded-xl bg-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-300 transition-all text-center">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>

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
                                        <td class="px-3.5 py-3 text-center font-bold text-gray-600">
                                            {{ $loop->iteration + ($paginatedData->firstItem() ? $paginatedData->firstItem() - 1 : 0) }}
                                        </td>

                                        <!-- KABUPATEN / KOTA -->
                                        <td class="px-4 py-3 font-black text-slate-700 tracking-tight">
                                            {{ $row->kabupaten }}
                                        </td>

                                        <!-- CLUSTER -->
                                        <td class="px-4 py-3 font-black text-slate-700">
                                            {{ $row->cluster }}
                                        </td>

                                        <!-- MITRA -->
                                        <td class="px-4 py-3 font-black text-slate-700">
                                            {{ $row->mitra }}
                                        </td>

                                        <!-- BRANCH -->
                                        <td class="px-4 py-3 font-bold text-slate-700">
                                            {{ $row->branch }}
                                        </td>

                                        <!-- JUMLAH OUTLET -->
                                        <td class="px-4 py-3 text-center font-black text-slate-700">
                                            {{ $row->jumlah_outlet }}
                                        </td>

                                        <!-- MANAGER BRANCH -->
                                        <td class="px-4 py-3 font-black text-slate-700">
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

                    <!-- Subtle Indicator (e.g. "... dan 25 data lainnya" when paginated) -->
                    @if($paginatedData->hasMorePages())
                        <div class="text-center text-xs text-gray-400 font-semibold py-1">
                            ... dan {{ $totalFilteredCount - $paginatedData->lastItem() }} data lainnya
                        </div>
                    @endif

                    <!-- FOOTER CONTROLS & PAGINATION -->
                    <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-600 font-medium">
                        
                        <!-- Left: Info Data Count -->
                        <div>
                            Menampilkan <strong>{{ $paginatedData->firstItem() ?? 0 }} - {{ $paginatedData->lastItem() ?? 0 }}</strong> dari <strong>{{ $totalFilteredCount }}</strong> data
                        </div>

                        <!-- Right: Per-Page Dropdown & Page Navigation Buttons -->
                        <div class="flex items-center gap-3">
                            <!-- Per Page Selector Form -->
                            <form method="GET" action="{{ route('hierarchy.index') }}" class="flex items-center gap-1.5">
                                @if(!empty($search)) <input type="hidden" name="search" value="{{ $search }}"> @endif
                                @if($selectedBranch !== 'all') <input type="hidden" name="branch" value="{{ $selectedBranch }}"> @endif
                                @if($selectedCluster !== 'all') <input type="hidden" name="cluster" value="{{ $selectedCluster }}"> @endif
                                
                                <select name="per_page" onchange="this.form.submit()" class="px-2.5 py-1 rounded-lg border border-gray-200 bg-white text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-red-500 cursor-pointer">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 / halaman</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 / halaman</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 / halaman</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>Semua data</option>
                                </select>
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

            </div>
        </main>
    </div>

    <!-- BOTTOM FOOTER BAR -->
    <footer id="dashboardFooter" class="w-full h-8 bg-[#121212] text-gray-400 px-6 flex items-center justify-between text-[11px] font-medium shrink-0 border-t border-gray-800 select-none hidden opacity-0 transition-all duration-300 pointer-events-none">
        <span>© {{ date('Y') }} Telkomsel. All Rights Reserved.</span>
        <span class="hidden sm:inline">Monitoring Dashboard Regional Bali Nusra</span>
    </footer>

    <!-- ==================== EDIT HIERARCHY MODAL ==================== -->
    <div id="editHierarchyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 max-w-lg w-full overflow-hidden animate-fade-in relative">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-[#ED1C24] to-[#C8102E] text-white p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl backdrop-blur-sm shadow-sm">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black tracking-tight leading-tight">Edit Data Cluster & Outlet</h3>
                        <p class="text-xs text-white/80 mt-0.5">Ubah informasi kabupaten, mitra, branch & jumlah outlet</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditHierarchyModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form id="editHierarchyForm" onsubmit="submitEditHierarchy(event)" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="editOutletId" name="id">

                <!-- Kabupaten / Kota -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kabupaten / Kota <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="bi bi-geo-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="editKabupaten" name="kabupaten" required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-bold uppercase">
                    </div>
                </div>

                <!-- Cluster & Branch Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Cluster -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Cluster <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="bi bi-building absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="editCluster" name="cluster" required
                                   class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-bold uppercase">
                        </div>
                    </div>

                    <!-- Branch -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="bi bi-diagram-3 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="editBranch" name="branch" required
                                   class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-bold uppercase">
                        </div>
                    </div>
                </div>

                <!-- Mitra -->
                <div>
                    <label class="block text-xs font-black text-slate-700 mb-1">Nama Mitra <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="bi bi-person-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="editMitra" name="mitra" required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-semibold uppercase">
                    </div>
                </div>

                <!-- Jumlah Outlet & Manager Branch Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Jumlah Outlet -->
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Jumlah Outlet <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="bi bi-shop absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="editJumlahOutlet" name="jumlah_outlet" required placeholder="Contoh: 1.139"
                                   class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-black">
                        </div>
                    </div>

                    <!-- Manager Branch -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Manager Branch <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="bi bi-person-check absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="editManagerBranch" name="manager_branch" required
                                   class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-bold uppercase">
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeEditHierarchyModal()" 
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="saveEditBtn"
                            class="px-5 py-2.5 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-extrabold shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-check-circle-fill text-sm"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== IMPORT CSV / EXCEL MODAL ==================== -->
    <div id="importHierarchyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 max-w-lg w-full overflow-hidden animate-fade-in relative">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-[#121212] via-[#1A1A1E] to-[#121212] text-white p-5 flex items-center justify-between border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl shadow-sm">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black tracking-tight leading-tight">Import Data Cluster & Outlet</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Unggah berkas CSV atau Excel langsung ke basis data</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportHierarchyModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form id="importHierarchyForm" onsubmit="submitImportHierarchy(event)" class="p-6 space-y-4" enctype="multipart/form-data">
                @csrf

                <!-- File Dropzone -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Pilih Berkas CSV / Excel <span class="text-red-500">*</span></label>
                    <div class="relative border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-2xl p-6 text-center transition-all bg-gray-50/50 hover:bg-emerald-50/20 cursor-pointer" onclick="document.getElementById('importFile').click()">
                        <input type="file" id="importFile" name="file" accept=".csv,.txt,.xlsx,.xls" required class="hidden" onchange="updateFilePreview(this)">
                        
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-gray-800" id="importFileName">Klik untuk memilih berkas</span>
                                <p class="text-[11px] text-gray-400 mt-0.5">Format yang didukung: .CSV, .XLSX (Maks 10 MB)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Import Mode Selection -->
                <div class="space-y-2 pt-1">
                    <label class="block text-xs font-bold text-gray-700">Mode Impor Data</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/50 cursor-pointer text-xs font-semibold text-gray-800 hover:bg-emerald-50">
                            <input type="radio" name="mode" value="append" checked class="text-emerald-600 focus:ring-emerald-500">
                            <span>Tambahkan & Simpan Data Lama (Append)</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-gray-200 bg-white cursor-pointer text-xs font-semibold text-gray-800 hover:bg-gray-50">
                            <input type="radio" name="mode" value="replace" class="text-emerald-600 focus:ring-emerald-500">
                            <span>Ganti Semua (Replace)</span>
                        </label>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeImportHierarchyModal()" 
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="submitImportBtn"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-upload text-sm"></i>
                        <span>Mulai Impor ke Database</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toastNotification" class="hidden fixed bottom-6 right-6 z-50 max-w-md bg-gray-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-gray-800 flex items-center gap-3 animate-slide-up">
        <div id="toastIcon" class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-base shrink-0">
            <i class="bi bi-check2-circle"></i>
        </div>
        <div class="flex-1">
            <p id="toastMessage" class="text-xs font-bold leading-tight">Data berhasil diproses!</p>
        </div>
        <button type="button" onclick="hideToast()" class="text-gray-400 hover:text-white text-sm">
            <i class="bi bi-x"></i>
        </button>
    </div>

    <!-- JAVASCRIPT INTERACTIONS -->
    <script>
        // Sidebar Toggle for Mobile / Desktop
        const sidebar = document.getElementById('mainSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                sidebar.classList.toggle('hidden');
            });
        }

        // Toggle Filter Box
        function toggleFilterBox() {
            const panel = document.getElementById('filterPanel');
            if (panel) {
                panel.classList.toggle('hidden');
            }
        }

        // User Profile Dropdown Toggle
        const userBtn = document.getElementById('userDropdownBtn');
        const userMenu = document.getElementById('userDropdownMenu');
        const arrow = document.getElementById('dropdownArrow');

        if (userBtn && userMenu) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
                arrow.style.transform = userMenu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        }

        document.addEventListener('click', (e) => {
            const container = document.getElementById('userDropdownContainer');
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        });

        // ==================== EDIT HIERARCHY MODAL FUNCTIONS ====================
        const editModal = document.getElementById('editHierarchyModal');

        function openEditHierarchyModal(data) {
            if (!data) return;

            document.getElementById('editOutletId').value = data.id || '';
            document.getElementById('editKabupaten').value = data.kabupaten || '';
            document.getElementById('editCluster').value = data.cluster || '';
            document.getElementById('editMitra').value = data.mitra || '';
            document.getElementById('editBranch').value = data.branch || '';
            document.getElementById('editJumlahOutlet').value = data.jumlah_outlet || '';
            document.getElementById('editManagerBranch').value = data.manager_branch || '';

            if (editModal) {
                editModal.classList.remove('hidden');
            }
        }

        function closeEditHierarchyModal() {
            if (editModal) {
                editModal.classList.add('hidden');
            }
        }

        if (editModal) {
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal) {
                    closeEditHierarchyModal();
                }
            });
        }

        // Submit Edit Form via AJAX
        async function submitEditHierarchy(event) {
            event.preventDefault();
            const form = document.getElementById('editHierarchyForm');
            const saveBtn = document.getElementById('saveEditBtn');
            const outletId = document.getElementById('editOutletId').value;

            if (!outletId) return;

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-sm"></i> <span>Menyimpan...</span>';

            const formData = new FormData(form);
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`/hierarchy/${outletId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    closeEditHierarchyModal();
                    showToast(result.message || 'Data berhasil diperbarui!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    showToast(result.message || 'Gagal memperbarui data', 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan koneksi server.', 'error');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-check-circle-fill text-sm"></i> <span>Simpan Perubahan</span>';
            }
        }

        // ==================== IMPORT MODAL FUNCTIONS ====================
        const importModal = document.getElementById('importHierarchyModal');

        function openImportHierarchyModal() {
            if (importModal) {
                importModal.classList.remove('hidden');
            }
        }

        function closeImportHierarchyModal() {
            if (importModal) {
                importModal.classList.add('hidden');
            }
        }

        if (importModal) {
            importModal.addEventListener('click', (e) => {
                if (e.target === importModal) {
                    closeImportHierarchyModal();
                }
            });
        }

        function updateFilePreview(input) {
            const fileNameSpan = document.getElementById('importFileName');
            if (input.files && input.files[0]) {
                fileNameSpan.textContent = input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
                fileNameSpan.classList.add('text-emerald-700');
            } else {
                fileNameSpan.textContent = 'Klik untuk memilih berkas';
                fileNameSpan.classList.remove('text-emerald-700');
            }
        }

        // Submit Import Form via AJAX
        async function submitImportHierarchy(event) {
            event.preventDefault();
            const form = document.getElementById('importHierarchyForm');
            const submitBtn = document.getElementById('submitImportBtn');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-sm"></i> <span>Mengimpor ke database...</span>';

            const formData = new FormData(form);
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch("{{ route('hierarchy.import') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    closeImportHierarchyModal();
                    showToast(result.message || 'Data berhasil diimpor!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    showToast(result.message || 'Gagal mengimpor file', 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan saat mengunggah berkas.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-upload text-sm"></i> <span>Mulai Impor ke Database</span>';
            }
        }

        // ==================== TOAST NOTIFICATION FUNCTION ====================
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const msgEl = document.getElementById('toastMessage');
            const iconEl = document.getElementById('toastIcon');

            if (!toast || !msgEl) return;

            msgEl.textContent = message;
            if (type === 'success') {
                iconEl.className = 'w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-base shrink-0';
                iconEl.innerHTML = '<i class="bi bi-check2-circle"></i>';
            } else {
                iconEl.className = 'w-8 h-8 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-base shrink-0';
                iconEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>';
            }

            toast.classList.remove('hidden');
            setTimeout(() => {
                hideToast();
            }, 4000);
        }

        function hideToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) {
                toast.classList.add('hidden');
            }
        }

        function toggleKpiSbpMenu() {
            const submenu = document.getElementById('kpiSbpSubmenu');
            const arrow = document.getElementById('kpiSbpArrow');
            if (!submenu || !arrow) return;

            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                arrow.style.transform = 'rotate(0deg)';
                localStorage.setItem('sidebarKpiSbpOpen', 'true');
            } else {
                submenu.classList.add('hidden');
                arrow.style.transform = 'rotate(-90deg)';
                localStorage.setItem('sidebarKpiSbpOpen', 'false');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const headerIcon = document.getElementById('headerSidebarToggleIcon');
            if (!sidebar) return;

            const isCollapsed = sidebar.classList.contains('-ml-60') || document.documentElement.classList.contains('sidebar-is-collapsed');
            if (isCollapsed) {
                sidebar.classList.remove('-ml-60');
                document.documentElement.classList.remove('sidebar-is-collapsed');
                if (toggleIcon) toggleIcon.className = 'bi bi-chevron-left text-xs transition-transform duration-300';
                if (toggleBtn) toggleBtn.title = 'Sembunyikan Sidebar';
                if (headerIcon) headerIcon.className = 'bi bi-layout-sidebar-inset text-lg';
                localStorage.setItem('sidebarCollapsed', 'false');
            } else {
                sidebar.classList.add('-ml-60');
                document.documentElement.classList.add('sidebar-is-collapsed');
                if (toggleIcon) toggleIcon.className = 'bi bi-chevron-right text-xs transition-transform duration-300';
                if (toggleBtn) toggleBtn.title = 'Tampilkan Sidebar';
                if (headerIcon) headerIcon.className = 'bi bi-layout-sidebar text-lg';
                localStorage.setItem('sidebarCollapsed', 'true');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isOpened = localStorage.getItem('sidebarKpiSbpOpen');
            const submenu = document.getElementById('kpiSbpSubmenu');
            const arrow = document.getElementById('kpiSbpArrow');
            if (isOpened === 'false' && submenu && arrow) {
                submenu.classList.add('hidden');
                arrow.style.transform = 'rotate(-90deg)';
            }

            // Restore Sidebar collapse state
            const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isSidebarCollapsed) {
                const sidebar = document.getElementById('mainSidebar');
                const toggleIcon = document.getElementById('sidebarToggleIcon');
                const toggleBtn = document.getElementById('sidebarToggleBtn');
                const headerIcon = document.getElementById('headerSidebarToggleIcon');
                document.documentElement.classList.add('sidebar-is-collapsed');
                if (sidebar) sidebar.classList.add('-ml-60');
                if (toggleIcon) toggleIcon.className = 'bi bi-chevron-right text-xs transition-transform duration-300';
                if (toggleBtn) toggleBtn.title = 'Tampilkan Sidebar';
                if (headerIcon) headerIcon.className = 'bi bi-layout-sidebar text-lg';
            }
        });

        // Alur Logika Sistem: Footer HANYA muncul saat scrolling menyentuh bagian paling bawah
        document.addEventListener('DOMContentLoaded', () => {
            const mainCanvas = document.querySelector('main');
            const dashboardFooter = document.getElementById('dashboardFooter');

            if (mainCanvas && dashboardFooter) {
                const checkFooterVisibility = () => {
                    const isAtBottom = (mainCanvas.scrollTop + mainCanvas.clientHeight) >= (mainCanvas.scrollHeight - 15);
                    if (isAtBottom) {
                        dashboardFooter.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
                        dashboardFooter.classList.add('opacity-100', 'pointer-events-auto');
                    } else {
                        dashboardFooter.classList.remove('opacity-100', 'pointer-events-auto');
                        dashboardFooter.classList.add('hidden', 'opacity-0', 'pointer-events-none');
                    }
                };

                checkFooterVisibility();
                mainCanvas.addEventListener('scroll', checkFooterVisibility, { passive: true });
                window.addEventListener('resize', checkFooterVisibility, { passive: true });
            }
        });
    </script>

</body>
</html>
