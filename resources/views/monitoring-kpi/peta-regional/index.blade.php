<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PETA PERSEBARAN OUTLET - Telkomsel Regional</title>

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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Leaflet Heat Plugin -->
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    <!-- Leaflet MarkerCluster Plugin -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        telkomsel: {
                            red: '#ED1C24',
                            darkred: '#C8102E',
                            deepred: '#8B1D24',
                            black: '#121212',
                            dark: '#18181B',
                            cardBg: '#FFFFFF',
                            softRed: '#FFF1F2'
                        }
                    },
                    boxShadow: {
                        'card': '0 2px 12px -2px rgba(0, 0, 0, 0.04), 0 1px 4px -1px rgba(0, 0, 0, 0.02)',
                        'card-hover': '0 8px 24px -4px rgba(0, 0, 0, 0.08)',
                    }
                }
            }
        }
    </script>

    <style>
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

        /* Map Container */
        #interactiveMap {
            height: 600px;
            width: 100%;
            border-radius: 1.25rem;
            z-index: 10;
            background: #121212;
        }

        .custom-cluster-badge {
            background: transparent;
            border: none;
        }

        /* Province Label on Map */
        .map-region-label {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 11px;
            color: #475569;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-shadow: 0 1px 4px rgba(255, 255, 255, 0.9), 0 0 10px #ffffff;
            pointer-events: none;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .map-region-label::before {
            display: none !important;
        }

        /* Tooltip styling */
        .leaflet-tooltip {
            background: #18181B !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 0.5rem !important;
            padding: 4px 8px !important;
            font-size: 11px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
        }

        .leaflet-tooltip::before {
            border-top-color: #18181B !important;
        }

        /* Custom Scrollbar */
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
    </style>
</head>
<body class="h-full flex flex-col antialiased text-gray-900 bg-[#F8FAFC]">

    <!-- TOP HEADER BAR (#121212) -->
    <header class="bg-[#121212] border-b border-gray-800/80 text-white h-16 px-4 sm:px-6 flex items-center justify-between shrink-0 relative z-30 select-none">
        <!-- Left: Logo PNG & Regional Title -->
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-balinusra.png') }}" alt="Bali Nusra Logo" class="h-10 sm:h-11 w-auto object-contain">
            <h1 class="text-sm sm:text-base font-black tracking-tight bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent drop-shadow-sm">
                Telkomsel Regional Bali Nusra
            </h1>
        </div>

        <!-- Right: Profile Pill Box & Dropdown -->
        <div class="relative" id="userDropdownContainer">
            <button id="userDropdownBtn" 
                    onclick="toggleUserDropdown(event)"
                    class="flex items-center gap-3 px-3.5 py-1.5 rounded-full bg-[#222225] hover:bg-[#2c2c30] border border-white/10 transition-all cursor-pointer">
                <div class="w-7 h-7 rounded-full bg-[#ED1C24] text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'V', 0, 1)) }}
                </div>
                <div class="flex flex-col text-left">
                    <span class="text-xs font-bold text-white leading-tight">
                        {{ Auth::user()->name ?? 'Visitor Telkomsel' }}
                    </span>
                    <span class="text-[9px] font-black uppercase text-gray-400 leading-tight tracking-wider">
                        {{ Auth::user()->role ?? 'VISITOR' }}
                    </span>
                </div>
                <i id="dropdownArrow" class="bi bi-chevron-down text-xs text-gray-400 ml-1 transition-transform duration-200"></i>
            </button>

            <!-- Profile Dropdown Menu -->
            <div id="userDropdownMenu" 
                 class="hidden absolute right-0 mt-2 w-52 bg-[#1a1a1d] border border-gray-800 rounded-2xl shadow-xl py-2 z-50 text-xs">
                <div class="px-4 py-2 border-b border-gray-800/80">
                    <p class="font-bold text-white truncate">{{ Auth::user()->name ?? 'Visitor Telkomsel' }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email ?? 'visitor@telkomsel.co.id' }}</p>
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

    <!-- MAIN BODY CONTAINER (Sidebar + Main Content) -->
    <div class="flex-1 flex min-h-0 overflow-hidden relative">

        @include('layouts.sidebar')

        <!-- MAIN SCROLLABLE CONTENT AREA -->

        <main class="flex-1 overflow-y-auto min-h-0 p-4 sm:p-6 lg:p-8 space-y-5">

            <!-- Toast Flash Messages -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-red-600 text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 cursor-pointer">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            <!-- 1. PAGE HEADER -->
            <div>
                <h2 class="text-xl font-black text-gray-900 tracking-tight">PETA SEBARAN OUTLET</h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Ringkasan Informasi Persebaran Outlet Regional Bali Nusa Tenggara</p>
            </div>

            @php
                $hasActiveFilter = !empty($search) || ($selectedBranch && $selectedBranch !== 'all') || ($selectedCluster && $selectedCluster !== 'all') || ($selectedFlag && $selectedFlag !== 'all');
            @endphp

            <!-- 2. TOP SECTION: 3 SUMMARY METRIC CARDS + 3 STACKED RED PILL ACTION BUTTONS (CENTER ALIGNED ON PAGE) -->
            <div class="flex flex-wrap items-center justify-center gap-3.5 sm:gap-4 w-full mx-auto">
                
                <!-- CARD 1: TOTAL OUTLET  -->
                <div class="h-[130px] w-[300px] bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white px-5 sm:px-6 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all shrink-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/20 text-white flex items-center justify-center text-xl sm:text-2xl backdrop-blur-sm group-hover:scale-110 transition-transform shrink-0">
                        <i class="bi bi-geo-alt-fill text-white"></i>
                    </div>
                    <div class="flex-1 text-center flex flex-col items-center justify-center">
                        <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">TOTAL OUTLET</span>
                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white mt-1.5 leading-tight tracking-tight text-center">{{ number_format($totalOutlets, 0, ',', '.') }}</h3>
                        <span class="text-xs text-gray-200 font-medium block mt-1 text-center">Tersebar di 12 Cluster</span>
                    </div>
                </div>

                <!-- CARD 2: DYNAMIC FLAG OMZET -->
                <div class="h-[130px] w-[320px] bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white px-5 sm:px-6 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all shrink-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/20 text-white flex items-center justify-center text-xl sm:text-2xl backdrop-blur-sm group-hover:scale-110 transition-transform shrink-0">
                        <i class="bi bi-bullseye text-white"></i>
                    </div>
                    <div class="flex-1 text-center flex flex-col items-center justify-center">
                        <span class="text-xs sm:text-sm font-black text-white/90 uppercase tracking-wider block truncate max-w-[210px] sm:max-w-[240px] text-center">{{ $flagBoxTitle ?? 'SEMUA KATEGORI FLAG' }}</span>
                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white mt-1.5 leading-tight tracking-tight text-center">{{ number_format($flagBoxCount ?? 0, 0, ',', '.') }}</h3>
                        <span class="text-xs text-gray-200 font-medium block mt-1 text-center">Jumlah outlet terfilter</span>
                    </div>
                </div>

                <!-- CARD 3: RATA-RATA OMZET  -->
                <div class="h-[130px] w-[350px] bg-gradient-to-br from-[#121212] via-[#1A1A1E] to-[#26262B] text-white px-5 sm:px-6 rounded-2xl shadow-card hover:shadow-card-hover border border-gray-800 relative overflow-hidden flex items-center gap-3.5 group transition-all shrink-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl sm:text-2xl backdrop-blur-sm group-hover:scale-110 transition-transform shrink-0">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="flex-1 text-center flex flex-col items-center justify-center">
                        <span class="text-xs sm:text-sm font-black text-white/90 uppercase tracking-wider block text-center">RATA-RATA OMZET</span>
                        <h3 class="text-xl sm:text-3xl lg:text-3xl font-black text-white mt-1.5 leading-tight tracking-tight text-center">{{ $formattedAvgOmzet }}</h3>
                        <span class="text-xs text-gray-400 font-medium block mt-1 text-center">Per Outlet</span>
                    </div>
                </div>

                <!-- COLUMN 4: 3 STACKED RED PILL ACTION BUTTONS -->
                <div class="h-[130px] w-[200px] flex flex-col justify-between gap-1.5 shrink-0">
                    <button type="button" 
                            id="btnModeCluster" 
                            onclick="setVisualMode('cluster')" 
                            class="flex-1 w-full px-4 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center cursor-pointer">
                        <span>Tampilan Titik Cluster</span>
                    </button>

                    <button type="button" 
                            id="btnModePoints" 
                            onclick="setVisualMode('points')" 
                            class="flex-1 w-full px-4 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center cursor-pointer">
                        <span>Tampilan Titik Outlet</span>
                    </button>

                    <button type="button" 
                            id="btnToggleFilterBar"
                            onclick="toggleFilterBar()" 
                            class="flex-1 w-full px-4 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="bi bi-funnel-fill text-xs"></i>
                        <span id="btnToggleFilterText">{{ $hasActiveFilter ? 'Sembunyikan Filter' : 'Tampilkan Filter' }}</span>
                    </button>
                </div>

            </div>

            <!-- 3. FILTER BAR WITH 4 INPUTS (HIDDEN BY DEFAULT WHEN MENU IS OPENED UNTIL "TAMPILKAN FILTER" IS CLICKED) -->
            <div id="filterBarContainer" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-gray-200/20 shadow-sm transition-all duration-300 {{ $hasActiveFilter ? '' : 'hidden' }}">
                <form id="filterForm" method="GET" action="{{ route('regional-map.index') }}" class="flex flex-wrap items-center justify-center gap-3.5 sm:gap-4 w-full mx-auto">
                    
                    <!-- Search Input -->
                    <div class="relative shrink-0">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari ID Outlet" 
                               class="w-90 pl-4 pr-10 py-2.5 text-xs font-medium rounded-xl border border-gray-200 focus:border-red-500 outline-none text-gray-800 bg-white">
                        <i class="bi bi-search absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>

                    <!-- Branch Dropdown -->
                    <div class="relative shrink-0">
                        <select name="branch" onchange="this.form.submit()" class="w 60 px-3.5 py-2.5 text-xs font-medium rounded-xl border border-gray-200 focus:border-red-500 outline-none text-gray-700 bg-white appearance-none pr-8 cursor-pointer">
                            <option value="all">Semua Branch</option>
                            @foreach($availableBranches as $b)
                                <option value="{{ $b }}" {{ $selectedBranch === $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>

                    <!-- Cluster Dropdown -->
                    <div class="relative shrink-0">
                        <select name="cluster" onchange="this.form.submit()" class="w-60 px-3.5 py-2.5 text-xs font-medium rounded-xl border border-gray-200 focus:border-red-500 outline-none text-gray-700 bg-white appearance-none pr-8 cursor-pointer">
                            <option value="all">Semua Cluster</option>
                            @foreach($availableClusters as $c)
                                <option value="{{ $c }}" {{ $selectedCluster === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>

                    <!-- Flag Category Dropdown -->
                    <div class="relative shrink-0">
                        <select name="flag" onchange="this.form.submit()" class="w-60 px-3.5 py-2.5 text-xs font-medium rounded-xl border border-gray-200 focus:border-red-500 outline-none text-gray-700 bg-white appearance-none pr-8 cursor-pointer">
                            <option value="all" {{ $selectedFlag === 'all' ? 'selected' : '' }}>Semua Kategori Flag</option>
                            <option value="black" {{ $selectedFlag === 'black' || $selectedFlag === 'white' ? 'selected' : '' }}>⚫ Flag < 0%</option>
                            <option value="red" {{ $selectedFlag === 'red' ? 'selected' : '' }}>🔴 Flag = 0%</option>
                            <option value="orange" {{ $selectedFlag === 'orange' || $selectedFlag === 'yellow' ? 'selected' : '' }}>🟠 Flag <= 3%</option>
                            <option value="green" {{ $selectedFlag === 'green' ? 'selected' : '' }}>🟢 Flag > 3%</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>

                </form>
            </div>

            <!-- 4. MAP VIEWPORT CANVAS CONTAINER (DEEP ZOOM GIS) -->
            <div class="bg-white p-4 rounded-3xl border border-gray-200/80 shadow-sm relative overflow-hidden space-y-3">
                
                <div class="relative w-full rounded-2xl overflow-hidden bg-[#121212] border border-gray-100">
                    <!-- LEAFLET MAP ELEMENT -->
                    <div id="interactiveMap"></div>

                    <!-- FLOATING MAP LEGEND (Bottom Left - EXACT MATCH) -->
                    <div class="absolute bottom-5 left-5 z-[500] bg-white/95 backdrop-blur-md text-gray-800 p-4 rounded-2xl border border-gray-200/80 shadow-lg space-y-2 pointer-events-auto min-w-[190px]">
                        <div class="text-[11px] font-extrabold uppercase tracking-wider text-gray-800 pb-1">
                            KLASIFIKASI FLAG OMZET
                        </div>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#18181B] inline-block shrink-0 shadow-sm"></span>
                                <span class="text-gray-700 font-medium text-[11px]">Flag < 0%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#ED1C24] inline-block shrink-0"></span>
                                <span class="text-gray-700 font-medium text-[11px]">Flag = 0%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#F97316] inline-block shrink-0"></span>
                                <span class="text-gray-700 font-medium text-[11px]">Flag <= 3%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#10B981] inline-block shrink-0"></span>
                                <span class="text-gray-700 font-medium text-[11px]">Flag > 3%</span>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL OUTLET BADGE (Bottom Right - EXACT MATCH) -->
                    <div class="absolute bottom-5 right-5 z-[500] bg-white/95 backdrop-blur-md text-gray-800 px-4 py-2 rounded-xl border border-gray-200/80 shadow-md text-xs font-extrabold flex items-center gap-2 pointer-events-auto">
                        <span id="markersCountLabel">Total Outlet: {{ number_format($totalOutlets, 0, ',', '.') }}</span>
                    </div>
                </div>

            </div>

            <!-- 5. DATA OUTLET TABLE & MANAGEMENT TOOLBAR -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
                
                <!-- Table Header & Action Toolbar -->
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900">Daftar Data Outlet Regional</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Menampilkan {{ $outlets->firstItem() ?? 0 }} - {{ $outlets->lastItem() ?? 0 }} dari total {{ $outlets->total() }} data outlet
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Export Button -->
                        <a href="{{ route('regional-map.export', request()->all()) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold transition-all shadow-xs">
                            <i class="bi bi-file-earmark-arrow-down text-emerald-600 text-sm"></i>
                            <span>Export CSV</span>
                        </a>

                        <!-- Template Button -->
                        <a href="{{ route('regional-map.template') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold transition-all shadow-xs">
                            <i class="bi bi-download text-blue-600 text-sm"></i>
                            <span>Template</span>
                        </a>

                        @if(!Auth::user()->isVisitor())
                            <!-- Import Modal Button -->
                            <button onclick="openImportModal()" 
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-bold transition-all shadow-sm cursor-pointer">
                                <i class="bi bi-cloud-arrow-up-fill text-sm"></i>
                                <span>Import Data</span>
                            </button>

                            <!-- Add Outlet Modal Button -->
                            <button onclick="openAddModal()" 
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold transition-all shadow-sm cursor-pointer">
                                <i class="bi bi-plus-lg text-sm"></i>
                                <span>Tambah</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Table Elements -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/75 border-b border-gray-100 text-[11px] font-extrabold text-gray-600 uppercase tracking-wider">
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4">ID Outlet</th>
                                <th class="py-3.5 px-4">Branch & Cluster</th>
                                <th class="py-3.5 px-4">Kabupaten</th>
                                <th class="py-3.5 px-4">Koordinat GPS</th>
                                <th class="py-3.5 px-4 text-right">Total Omzet</th>
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
                                    <td class="py-3.5 px-4 font-extrabold text-gray-900">
                                        <span>{{ $o->id_outlet }}</span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <p class="font-bold text-gray-900">{{ $o->branch }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $o->cluster }}</p>
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-800 font-medium">
                                        {{ $o->kabupaten }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-gray-600">
                                        {{ number_format($o->longitude, 4) }}, {{ number_format($o->latitude, 4) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-black text-gray-900">
                                        {{ $o->formatted_omzet }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
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
                                                        class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors cursor-pointer">
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

        </main>
    </div>

    <!-- BOTTOM FOOTER BAR -->
    <footer id="dashboardFooter" class="w-full h-8 bg-[#121212] text-gray-400 px-6 flex items-center justify-between text-[11px] font-medium shrink-0 border-t border-gray-800 select-none hidden opacity-0 transition-all duration-300 pointer-events-none">
        <span>© {{ date('Y') }} Telkomsel. All Rights Reserved.</span>
        <span class="hidden sm:inline">Monitoring Dashboard Regional Bali Nusra</span>
    </footer>

    <!-- ========================================================================= -->
    <!-- MODAL: OUTLET DETAIL DISPLAY (EXACT DESIGN MATCH) -->
    <!-- ========================================================================= -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 max-w-md w-full p-6 space-y-4 transform transition-all">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3.5">
                    <!-- Circular Store Icon Badge -->
                    <div id="modalColorBadge" class="w-12 h-12 rounded-full bg-[#ED1C24] text-white flex items-center justify-center shadow-sm shrink-0">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M4 4h16l1.2 5H2.8L4 4zm-1.8 7h19.6a1 1 0 0 1 1 1.2l-1.5 7.5a2 2 0 0 1-2 1.3H5.7a2 2 0 0 1-2-1.3L2.2 12.2a1 1 0 0 1 1-1.2zm6.8 2a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0v-4a1 1 0 0 0-1-1zm6 0a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0v-4a1 1 0 0 0-1-1z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span id="modalIdOutlet" class="text-lg font-black text-gray-900 tracking-tight"></span>
                            <span id="modalStatusBadge" class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-200"></span>
                        </div>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Outlet Regional Bali Nusra</p>
                    </div>
                </div>

                <!-- Close "X" Button -->
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <!-- Financial Metric Cards (2 Columns) -->
            <div class="grid grid-cols-2 gap-3 pt-1">
                <!-- Total Omzet Card -->
                <div class="border border-gray-200/80 rounded-2xl p-4 bg-white shadow-sm">
                    <span class="text-xs text-gray-500 font-medium block">Total Omzet</span>
                    <span id="modalOmzet" class="text-lg sm:text-xl font-extrabold text-gray-900 mt-1 block tracking-tight"></span>
                </div>
                <!-- Flag Omzet Card -->
                <div class="border border-gray-200/80 rounded-2xl p-4 bg-white shadow-sm">
                    <span class="text-xs text-gray-500 font-medium block">Flag Omzet</span>
                    <span id="modalFlag" class="text-lg sm:text-xl font-extrabold text-red-600 mt-1 block tracking-tight"></span>
                </div>
            </div>

            <!-- Hierarchy & Region Details List -->
            <div class="space-y-0 text-sm border-t border-gray-100 pt-1">
                <!-- Branch -->
                <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                    <span class="text-gray-500 font-medium text-xs sm:text-sm">Branch</span>
                    <span id="modalBranch" class="font-extrabold text-gray-900 tracking-wide uppercase text-xs sm:text-sm"></span>
                </div>
                <!-- Cluster -->
                <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                    <span class="text-gray-500 font-medium text-xs sm:text-sm">Cluster</span>
                    <span id="modalCluster" class="font-extrabold text-gray-900 tracking-wide uppercase text-xs sm:text-sm"></span>
                </div>
                <!-- Kabupaten / Kota -->
                <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                    <span class="text-gray-500 font-medium text-xs sm:text-sm">Kabupaten / Kota</span>
                    <span id="modalKabupaten" class="font-extrabold text-gray-900 tracking-wide uppercase text-xs sm:text-sm"></span>
                </div>
                <!-- Koordinat GPS -->
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-gray-500 font-medium text-xs sm:text-sm">Koordinat GPS</span>
                    <span id="modalCoords" class="font-mono font-bold text-gray-900 text-xs"></span>
                </div>
            </div>

            <!-- Action Button: Buka Lokasi di Google Maps -->
            <div class="pt-1">
                <a id="modalGmapsLink" 
                   href="#" 
                   target="_blank" 
                   class="w-full py-3 px-4 bg-white hover:bg-gray-50 text-red-600 font-bold text-xs sm:text-sm rounded-2xl flex items-center justify-center gap-2 border border-gray-200 shadow-sm transition-all">
                    <i class="bi bi-geo-alt-fill text-red-600 text-base"></i>
                    <span>Buka Lokasi di Google Maps</span>
                    <i class="bi bi-box-arrow-up-right text-gray-400 text-xs ml-0.5"></i>
                </a>
            </div>

            <!-- Modal Footer Button -->
            <div class="flex items-center justify-end pt-1">
                <button onclick="closeDetailModal()" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: IMPORT DATA OUTLET -->
    <!-- ========================================================================= -->
    @if(!Auth::user()->isVisitor())
    <div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-lg w-full overflow-hidden">
            
            <div class="bg-[#ED1C24] text-white p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-extrabold text-white">Import Data Peta Regional</h4>
                        <p class="text-xs text-white/80">Unggah berkas Excel (.xlsx) atau CSV (.csv)</p>
                    </div>
                </div>
                <button onclick="closeImportModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('regional-map.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700">Pilih Berkas (.xlsx / .csv)</label>
                    <input type="file" 
                           name="file" 
                           required 
                           accept=".xlsx,.xls,.csv" 
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 border border-gray-200 rounded-xl p-2 bg-gray-50/50">
                    <p class="text-[11px] text-gray-400">Ukuran berkas maksimal 50MB.</p>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-700">Mode Pengolahan Data</label>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <label class="flex items-start gap-2 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="import_mode" value="append" checked class="mt-0.5 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="font-bold text-gray-900 block">Update / Append (Rekomendasi)</span>
                                <span class="text-[11px] text-gray-500">Perbarui atau tambah data baru (Simpan data lama)</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-2 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="import_mode" value="replace" class="mt-0.5 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="font-bold text-gray-900 block">Replace All</span>
                                <span class="text-[11px] text-gray-500">Ganti seluruh data outlet lama</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-info-circle-fill text-blue-600 text-base"></i>
                        <span>Belum memiliki format template?</span>
                    </div>
                    <a href="{{ route('regional-map.template') }}" class="font-bold underline text-blue-700 hover:text-blue-900">
                        Unduh Template
                    </a>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-red-500/20 cursor-pointer">
                        Mulai Import Data
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: ADD / EDIT OUTLET -->
    <!-- ========================================================================= -->
    <div id="outletFormModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-lg w-full overflow-hidden">
            
            <div class="bg-gray-900 text-white p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                        <i class="bi bi-geo-fill text-lg text-red-500"></i>
                    </div>
                    <div>
                        <h4 id="formModalTitle" class="text-base font-extrabold text-white">Tambah Outlet Baru</h4>
                        <p class="text-xs text-gray-400">Lengkapi informasi koordinat dan performa outlet</p>
                    </div>
                </div>
                <button onclick="closeOutletFormModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form id="outletForm" method="POST" action="{{ route('regional-map.manual') }}" class="p-6 space-y-3.5 text-xs">
                @csrf
                <div id="methodContainer"></div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">ID Outlet *</label>
                    <input type="text" name="id_outlet" id="formIdOutlet" required placeholder="Contoh: OUT-DPS-100" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Longitude (X) *</label>
                        <input type="number" step="any" name="longitude" id="formLongitude" required placeholder="Contoh: 115.2126" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Latitude (Y) *</label>
                        <input type="number" step="any" name="latitude" id="formLatitude" required placeholder="Contoh: -8.6705" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Branch *</label>
                        <input type="text" name="branch" id="formBranch" required placeholder="Branch Denpasar" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Cluster *</label>
                        <input type="text" name="cluster" id="formCluster" required placeholder="Cluster Denpasar Kota" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Kabupaten / Kota *</label>
                        <input type="text" name="kabupaten" id="formKabupaten" required placeholder="Kota Denpasar" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Total Omzet (Rp) *</label>
                        <input type="number" step="any" name="total_omzet" id="formTotalOmzet" required placeholder="150000000" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Flag Omzet (%) *</label>
                        <input type="number" step="0.01" name="flag_omzet" id="formFlagOmzet" required placeholder="Contoh: 3.50 (atau -1.2)" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeOutletFormModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-gray-900 hover:bg-black text-white font-bold rounded-xl transition-all shadow-md cursor-pointer">
                        Simpan Data Outlet
                    </button>
                </div>
            </form>

        </div>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- JAVASCRIPT: ADVANCED CLUSTER & GIS REGIONAL MAP ENGINE -->
    <!-- ========================================================================= -->
    <script>
        let rawMarkers = [];
        let map = null;
        let clusterLayer = null;
        let pointsLayer = null;
        let heatmapLayer = null;
        let streetLayer = null;
        let satelliteLayer = null;
        let lightLayer = null;
        let darkLayer = null;
        let currentVisualMode = 'points'; // 'cluster' | 'points' | 'heatmap' | 'street' | 'satellite'

        // Tile layer definitions (Esri ArcGIS Online - Clean, No API Key Required, No Watermark)
        const tileProviders = {
            dark: {
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}',
                options: {
                    attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
                    maxZoom: 19
                }
            },
            light: {
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
                options: {
                    attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
                    maxZoom: 19
                }
            },
            street: {
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
                options: {
                    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ',
                    maxZoom: 19
                }
            },
            satellite: {
                url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                options: {
                    attribution: 'Tiles &copy; Esri',
                    maxZoom: 19
                }
            }
        };

        // Toggle Filter Bar Visibility
        function toggleFilterBar() {
            const container = document.getElementById('filterBarContainer');
            const btnText = document.getElementById('btnToggleFilterText');
            if (container) {
                if (container.classList.contains('hidden')) {
                    container.classList.remove('hidden');
                    if (btnText) btnText.innerText = 'Sembunyikan Filter';
                } else {
                    container.classList.add('hidden');
                    if (btnText) btnText.innerText = 'Tampilkan Filter';
                }
            }
        }

        // User Profile Dropdown
        function toggleUserDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                menu.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userDropdownMenu');
            const btn = document.getElementById('userDropdownBtn');
            const arrow = document.getElementById('dropdownArrow');
            if (menu && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('hidden');
                if (arrow) arrow.classList.remove('rotate-180');
            }
        });

        // Helpers
        function formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        function getFlagInfo(val) {
            const num = parseFloat(val || 0);
            let color = 'black';
            let hex = '#18181B';
            let label = 'Flag < 0%';

            if (num < 0) {
                color = 'black';
                hex = '#18181B';
                label = 'Flag < 0%';
            } else if (num === 0.0) {
                color = 'red';
                hex = '#ED1C24';
                label = 'Flag = 0%';
            } else if (num <= 3.0) {
                color = 'orange';
                hex = '#F97316';
                label = 'Flag <= 3%';
            } else {
                color = 'green';
                hex = '#10B981';
                label = 'Flag > 3%';
            }

            const prefix = num > 0 ? '+' : '';
            const formatted = prefix + num.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%';

            return { color, hex, label, formatted };
        }

        // =====================================================================
        // INITIALIZE MAP & REGIONAL LABELS
        // =====================================================================
        function initInteractiveMap() {
            if (map) return;

            // Define Bali, NTB, NTT Regional Bounding Box (Strict Lock)
            const baliNusaBounds = L.latLngBounds(
                L.latLng(-11.0, 114.0), // South-West corner (South of Rote/Timor & West Bali)
                L.latLng(-7.2, 125.6)   // North-East corner (North of Flores/Alor & Bali)
            );

            map = L.map('interactiveMap', {
                preferCanvas: true,
                center: [-8.60, 119.20],
                zoom: 8,
                minZoom: 7,
                maxZoom: 19,
                maxBounds: baliNusaBounds,
                maxBoundsViscosity: 1.0, // 100% Locked: prevents dragging off-screen outside Bali Nusra
                zoomControl: true
            });

            // Automatically fit exact Bali & Nusa Tenggara region bounds on initial render
            map.fitBounds(baliNusaBounds, { padding: [10, 10] });

            // Base Tile Layer (Dark Matter for Points Mode, Light for Others)
            if (currentVisualMode === 'points') {
                darkLayer = L.tileLayer(tileProviders.dark.url, tileProviders.dark.options).addTo(map);
            } else {
                lightLayer = L.tileLayer(tileProviders.light.url, tileProviders.light.options).addTo(map);
            }

            // Add Permanent Province / Island Text Overlays (High-Contrast for Dark/Light Maps)
            const provinceLabels = [
                { name: 'BALI', lat: -8.35, lng: 115.15 },
                { name: 'NUSA TENGGARA<br>BARAT', lat: -8.28, lng: 117.45 },
                { name: 'NUSA TENGGARA<br>TIMUR', lat: -8.35, lng: 122.35 }
            ];

            provinceLabels.forEach(prov => {
                const labelIcon = L.divIcon({
                    className: 'map-region-label',
                    html: `<div style="text-align:center; font-weight:800; font-size:11px; color:#cbd5e1; letter-spacing:0.08em; text-transform:uppercase; text-shadow:0 1px 4px rgba(0,0,0,0.9), 0 0 10px #000000;">${prov.name}</div>`,
                    iconSize: [140, 30],
                    iconAnchor: [70, 15]
                });
                L.marker([prov.lat, prov.lng], { icon: labelIcon, interactive: false }).addTo(map);
            });

            // Re-render radii on deep zoom in scatter mode
            map.on('zoomend', () => {
                if (currentVisualMode === 'points' || currentVisualMode === 'street' || currentVisualMode === 'satellite') {
                    updatePointsRadius();
                }
            });
        }

        function flyToLocation(lat, lng, zoom) {
            if (map) {
                map.flyTo([lat, lng], zoom, {
                    animate: true,
                    duration: 1.2
                });
            }
        }

        // =====================================================================
        // SWITCH VISUALIZATION MODES (Cluster, Points, Satellite)
        // =====================================================================
        function setVisualMode(mode) {
            currentVisualMode = mode;
            const btnCluster = document.getElementById('btnModeCluster');
            const btnPoints = document.getElementById('btnModePoints');

            const allBtns = [btnCluster, btnPoints];
            allBtns.forEach(b => {
                if (b) b.className = 'w-full px-5 py-2.5 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center cursor-pointer';
            });

            const activeBtn = mode === 'cluster' ? btnCluster : btnPoints;
            if (activeBtn) activeBtn.className = 'w-full px-5 py-2.5 rounded-xl bg-[#C8102E] text-white font-extrabold text-xs shadow-md ring-2 ring-white/60 transition-all flex items-center justify-center cursor-pointer';

            // Tile layer adjustment
            if (darkLayer && map.hasLayer(darkLayer)) map.removeLayer(darkLayer);
            if (lightLayer && map.hasLayer(lightLayer)) map.removeLayer(lightLayer);
            if (satelliteLayer && map.hasLayer(satelliteLayer)) map.removeLayer(satelliteLayer);

            if (mode === 'points') {
                darkLayer = L.tileLayer(tileProviders.dark.url, tileProviders.dark.options).addTo(map);
            } else {
                lightLayer = L.tileLayer(tileProviders.light.url, tileProviders.light.options).addTo(map);
            }

            renderMapLayers();
        }

        function getAdaptiveRadius() {
            if (!map) return 4;
            const z = map.getZoom();
            if (z <= 8) return 3.5;
            if (z <= 11) return 4.5;
            if (z <= 14) return 6.0;
            if (z <= 16) return 7.5;
            return 9.0;
        }

        function updatePointsRadius() {
            if (!pointsLayer) return;
            const r = getAdaptiveRadius();
            pointsLayer.eachLayer(layer => {
                if (layer.setRadius) {
                    layer.setRadius(r);
                }
            });
        }

        // =====================================================================
        // RENDER CLUSTERS, SCATTER POINTS & HEATMAP (ROBUST & FAIL-SAFE)
        // =====================================================================
        function renderMapLayers() {
            if (!map || !rawMarkers || rawMarkers.length === 0) return;

            try {
                if (clusterLayer && map.hasLayer(clusterLayer)) map.removeLayer(clusterLayer);
            } catch(e) {}
            try {
                if (pointsLayer && map.hasLayer(pointsLayer)) map.removeLayer(pointsLayer);
            } catch(e) {}
            try {
                if (heatmapLayer && map.hasLayer(heatmapLayer)) map.removeLayer(heatmapLayer);
            } catch(e) {}

            const radius = getAdaptiveRadius();

            // 1. THERMAL HEATMAP MODE
            if (currentVisualMode === 'heatmap') {
                if (typeof L.heatLayer !== 'undefined') {
                    const heatPoints = [];
                    for (let i = 0; i < rawMarkers.length; i++) {
                        const m = rawMarkers[i];
                        const lat = parseFloat(m.latitude);
                        const lng = parseFloat(m.longitude);
                        if (isNaN(lat) || isNaN(lng) || lat > -6.0 || lat < -12.0 || lng < 113.0 || lng > 127.0) continue;
                        heatPoints.push([lat, lng, 0.7]);
                    }
                    heatmapLayer = L.heatLayer(heatPoints, {
                        radius: 20,
                        blur: 15,
                        maxZoom: 16,
                        max: 1.0,
                        gradient: {
                            0.2: '#3B82F6',
                            0.4: '#10B981',
                            0.6: '#F59E0B',
                            0.8: '#ED1C24',
                            1.0: '#FFFFFF'
                        }
                    }).addTo(map);
                    return;
                }
            }

            // 2. SCATTER POINTS / STREET / SATELLITE MODE (Direct Canvas Rendering)
            if (currentVisualMode === 'points' || currentVisualMode === 'street' || currentVisualMode === 'satellite' || typeof L.markerClusterGroup === 'undefined') {
                pointsLayer = L.layerGroup();

                for (let i = 0; i < rawMarkers.length; i++) {
                    const m = rawMarkers[i];
                    const lat = parseFloat(m.latitude);
                    const lng = parseFloat(m.longitude);
                    if (isNaN(lat) || isNaN(lng) || lat > -6.0 || lat < -12.0 || lng < 113.0 || lng > 127.0) continue;

                    const flagInfo = getFlagInfo(m.flag_omzet);
                    const marker = L.circleMarker([lat, lng], {
                        radius: radius,
                        fillColor: flagInfo.hex,
                        color: '#FFFFFF',
                        weight: 1.2,
                        opacity: 1.0,
                        fillOpacity: 0.95
                    });

                    marker.bindTooltip(`<b>${m.id_outlet}</b><br><span style="color:${flagInfo.hex}">${flagInfo.formatted}</span>`, {
                        direction: 'top',
                        offset: [0, -4],
                        opacity: 0.95
                    });

                    marker.on('click', (e) => {
                        L.DomEvent.stopPropagation(e);
                        showOutletDetail(m);
                    });

                    pointsLayer.addLayer(marker);
                }

                pointsLayer.addTo(map);
                return;
            }

            // 3. CLUSTER MODE (MarkerClusterGroup with Fallback)
            try {
                clusterLayer = L.markerClusterGroup({
                    chunkedLoading: true,
                    maxClusterRadius: 45,
                    spiderfyOnMaxZoom: true,
                    showCoverageOnHover: false,
                    zoomToBoundsOnClick: true,
                    disableClusteringAtZoom: 14,
                    iconCreateFunction: function(cluster) {
                        const count = cluster.getChildCount();
                        let bgColor = '#ED1C24';
                        if (count < 100) bgColor = '#F59E0B';
                        else bgColor = '#ED1C24';

                        const size = count >= 1000 ? 44 : (count >= 100 ? 36 : 28);
                        const formattedCount = count.toLocaleString('id-ID');

                        return L.divIcon({
                            html: `<div style="
                                background-color: ${bgColor};
                                width: ${size}px;
                                height: ${size}px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                border-radius: 50%;
                                color: #ffffff;
                                font-family: 'Plus Jakarta Sans', sans-serif;
                                font-weight: 800;
                                font-size: ${size >= 40 ? '11px' : (size >= 32 ? '10px' : '9px')};
                                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25), 0 0 0 2.5px rgba(255, 255, 255, 0.9);
                                cursor: pointer;
                            ">${formattedCount}</div>`,
                            className: 'custom-cluster-badge',
                            iconSize: L.point(size, size),
                            iconAnchor: [size / 2, size / 2]
                        });
                    }
                });

                for (let i = 0; i < rawMarkers.length; i++) {
                    const m = rawMarkers[i];
                    const lat = parseFloat(m.latitude);
                    const lng = parseFloat(m.longitude);
                    if (isNaN(lat) || isNaN(lng) || lat > -6.0 || lat < -12.0 || lng < 113.0 || lng > 127.0) continue;

                    const flagInfo = getFlagInfo(m.flag_omzet);
                    const marker = L.circleMarker([lat, lng], {
                        radius: 5.0,
                        fillColor: flagInfo.hex,
                        color: '#FFFFFF',
                        weight: 1.5,
                        opacity: 1.0,
                        fillOpacity: 0.95
                    });

                    marker.bindTooltip(`<b>${m.id_outlet}</b><br><span style="color:${flagInfo.hex}">${flagInfo.formatted}</span>`, {
                        direction: 'top',
                        offset: [0, -4],
                        opacity: 0.95
                    });

                    marker.on('click', (e) => {
                        L.DomEvent.stopPropagation(e);
                        showOutletDetail(m);
                    });

                    clusterLayer.addLayer(marker);
                }

                clusterLayer.addTo(map);

            } catch (err) {
                console.error("Cluster mode error, falling back to direct scatter:", err);
                pointsLayer = L.layerGroup();
                for (let i = 0; i < rawMarkers.length; i++) {
                    const m = rawMarkers[i];
                    const lat = parseFloat(m.latitude);
                    const lng = parseFloat(m.longitude);
                    if (isNaN(lat) || isNaN(lng) || lat > -6.0 || lat < -12.0 || lng < 113.0 || lng > 127.0) continue;

                    const flagInfo = getFlagInfo(m.flag_omzet);
                    const marker = L.circleMarker([lat, lng], {
                        radius: radius,
                        fillColor: flagInfo.hex,
                        color: '#FFFFFF',
                        weight: 1.2,
                        opacity: 1.0,
                        fillOpacity: 0.95
                    });

                    marker.bindTooltip(`<b>${m.id_outlet}</b><br><span style="color:${flagInfo.hex}">${flagInfo.formatted}</span>`, {
                        direction: 'top',
                        offset: [0, -4],
                        opacity: 0.95
                    });

                    marker.on('click', (e) => {
                        L.DomEvent.stopPropagation(e);
                        showOutletDetail(m);
                    });

                    pointsLayer.addLayer(marker);
                }
                pointsLayer.addTo(map);
            }
        }

        // =====================================================================
        // ASYNC DATA LOADER
        // =====================================================================
        function loadMarkersAsync() {
            const searchParams = new URLSearchParams(window.location.search);
            const url = `{{ route('regional-map.markers') }}?${searchParams.toString()}`;

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    if (res && res.data) {
                        rawMarkers = res.data;

                        const countLabel = document.getElementById('markersCountLabel');
                        if (countLabel) {
                            countLabel.textContent = `Total Outlet: ${rawMarkers.length.toLocaleString('id-ID')}`;
                        }

                        initInteractiveMap();
                        renderMapLayers();
                        autoFocusFilteredTerritory();
                    }
                })
                .catch(err => {
                    console.error("Gagal memuat markers:", err);
                });
        }

        // =====================================================================
        // AUTO FOCUS TERRITORY / ISLAND ON FILTER SELECTION
        // =====================================================================
        function autoFocusFilteredTerritory() {
            if (!map) return;

            const urlParams = new URLSearchParams(window.location.search);
            const branch = urlParams.get('branch') || 'all';
            const cluster = urlParams.get('cluster') || 'all';

            // 1. Direct Cluster Exact Matching Dictionary (All 12 Regional Clusters)
            const clusterPresets = {
                'BALI BARAT': [[-8.55, 114.43], [-8.10, 115.15]],
                'BALI TENGAH': [[-8.85, 115.08], [-8.40, 115.35]],
                'BALI TIMUR': [[-8.68, 115.30], [-8.10, 115.75]],
                'LOMBOK': [[-8.92, 115.95], [-8.22, 116.75]],
                'SUMBAWA BARAT': [[-9.12, 116.70], [-8.35, 117.80]],
                'SUMBAWA TIMUR': [[-8.95, 117.75], [-8.15, 119.30]],
                'MANGGARAI': [[-8.95, 119.70], [-8.25, 120.95]],
                'ENDE SIKKA': [[-8.95, 120.90], [-8.35, 122.65]],
                'FLORES TIMUR': [[-8.60, 122.50], [-8.15, 124.00]],
                'SUMBA': [[-10.35, 118.90], [-9.20, 120.90]],
                'KUPANG ROTE': [[-10.95, 122.70], [-9.80, 124.20]],
                'MALAKA TIMTIM BELU': [[-10.15, 124.00], [-8.90, 125.25]]
            };

            const targetClusterUpper = cluster.toUpperCase().trim();
            if (cluster !== 'all' && clusterPresets[targetClusterUpper]) {
                map.flyToBounds(clusterPresets[targetClusterUpper], {
                    padding: [35, 35],
                    maxZoom: 12,
                    duration: 1.4
                });
                return;
            }

            // 2. Fallback Sub-string Match for Clusters & Branches
            const targetRegion = (cluster !== 'all' ? cluster : branch).toUpperCase().trim();
            const islandPresets = {
                // BALI
                'BALI': [[-8.88, 114.43], [-8.06, 115.71]],
                'DENPASAR': [[-8.73, 115.15], [-8.55, 115.28]],
                'BADUNG': [[-8.82, 115.12], [-8.42, 115.26]],
                'TABANAN': [[-8.68, 114.95], [-8.28, 115.18]],
                'SINGARAJA': [[-8.32, 114.65], [-8.08, 115.42]],
                'BULELENG': [[-8.32, 114.65], [-8.08, 115.42]],
                'GIANYAR': [[-8.62, 115.24], [-8.42, 115.35]],
                'KLUNGKUNG': [[-8.78, 115.35], [-8.48, 115.55]],
                'KARANGASEM': [[-8.58, 115.48], [-8.28, 115.72]],
                'BANGLI': [[-8.55, 115.30], [-8.15, 115.45]],
                'JEMBRANA': [[-8.45, 114.43], [-8.15, 114.90]],

                // NTB
                'LOMBOK': [[-8.92, 115.95], [-8.22, 116.75]],
                'MATARAM': [[-8.65, 116.05], [-8.53, 116.18]],
                'SUMBAWA': [[-9.10, 116.70], [-8.30, 119.30]],
                'BIMA': [[-8.90, 118.40], [-8.20, 119.30]],
                'DOMPU': [[-8.85, 118.10], [-8.25, 118.60]],

                // NTT
                'FLORES': [[-8.90, 119.70], [-8.15, 124.00]],
                'MANGGARAI': [[-8.95, 119.70], [-8.25, 120.95]],
                'ENDE': [[-8.95, 120.90], [-8.35, 122.65]],
                'SIKKA': [[-8.80, 122.00], [-8.45, 122.65]],
                'KUPANG': [[-10.40, 123.40], [-9.80, 124.20]],
                'TIMOR': [[-10.40, 123.40], [-8.90, 125.25]],
                'BELU': [[-9.30, 124.70], [-8.90, 125.25]],
                'MALAKA': [[-9.75, 124.70], [-9.20, 125.10]],
                'SUMBA': [[-10.35, 118.90], [-9.20, 120.90]],
                'ROTE': [[-10.90, 122.80], [-10.35, 123.40]],
                'ALOR': [[-8.50, 124.00], [-8.00, 125.20]]
            };

            for (const key in islandPresets) {
                if (targetRegion.includes(key)) {
                    map.flyToBounds(islandPresets[key], {
                        padding: [30, 30],
                        maxZoom: 12,
                        duration: 1.4
                    });
                    return;
                }
            }

            // 3. Fallback: Calculate bounds from filtered raw markers (with outlier filtering)
            if ((branch !== 'all' || cluster !== 'all') && rawMarkers && rawMarkers.length > 0) {
                let minLat = 90, maxLat = -90, minLng = 180, maxLng = -180;
                let validCount = 0;

                for (let i = 0; i < rawMarkers.length; i++) {
                    const lat = parseFloat(rawMarkers[i].latitude);
                    const lng = parseFloat(rawMarkers[i].longitude);
                    if (!isNaN(lat) && !isNaN(lng) && lat >= -11.5 && lat <= -7.0 && lng >= 113.0 && lng <= 126.0) {
                        if (lat < minLat) minLat = lat;
                        if (lat > maxLat) maxLat = lat;
                        if (lng < minLng) minLng = lng;
                        if (lng > maxLng) maxLng = lng;
                        validCount++;
                    }
                }

                if (validCount > 0) {
                    const bounds = L.latLngBounds([minLat, minLng], [maxLat, maxLng]);
                    map.flyToBounds(bounds, {
                        padding: [35, 35],
                        maxZoom: 12,
                        duration: 1.4
                    });
                    return;
                }
            }

            // 4. Default: Fit Entire Bali Nusra Bounding Box
            const baliNusaBounds = L.latLngBounds(
                L.latLng(-11.0, 114.0),
                L.latLng(-7.2, 125.6)
            );
            map.flyToBounds(baliNusaBounds, { padding: [10, 10], duration: 1.4 });
        }

        // =====================================================================
        // DETAIL MODAL (EXACT MATCHING USER DESIGN)
        // =====================================================================
        function showOutletDetail(outlet) {
            document.getElementById('modalIdOutlet').textContent = outlet.id_outlet;
            document.getElementById('modalOmzet').textContent = outlet.formatted_omzet || formatRupiah(outlet.total_omzet);
            
            const flagInfo = getFlagInfo(outlet.flag_omzet);
            const flagEl = document.getElementById('modalFlag');
            flagEl.textContent = outlet.formatted_flag || flagInfo.formatted;
            flagEl.style.color = flagInfo.hex;

            const badgeEl = document.getElementById('modalStatusBadge');
            badgeEl.textContent = flagInfo.label;
            badgeEl.style.borderColor = flagInfo.hex + '35';
            badgeEl.style.color = flagInfo.hex;
            badgeEl.style.backgroundColor = flagInfo.hex + '12';

            const iconBadge = document.getElementById('modalColorBadge');
            iconBadge.style.backgroundColor = flagInfo.hex;

            document.getElementById('modalBranch').textContent = outlet.branch || '-';
            document.getElementById('modalCluster').textContent = outlet.cluster || '-';
            document.getElementById('modalKabupaten').textContent = outlet.kabupaten || '-';
            
            const lngFormatted = Number(outlet.longitude || 0).toFixed(4);
            const latFormatted = Number(outlet.latitude || 0).toFixed(7);
            document.getElementById('modalCoords').textContent = `${lngFormatted}, ${latFormatted}`;

            document.getElementById('modalGmapsLink').href = `https://www.google.com/maps?q=${outlet.latitude},${outlet.longitude}`;

            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // =====================================================================
        // IMPORT & CRUD MODALS
        // =====================================================================
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }
        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        function openAddModal() {
            const form = document.getElementById('outletForm');
            form.action = "{{ route('regional-map.manual') }}";
            document.getElementById('methodContainer').innerHTML = '';
            document.getElementById('formModalTitle').textContent = 'Tambah Outlet Baru';

            document.getElementById('formIdOutlet').value = '';
            document.getElementById('formLongitude').value = '';
            document.getElementById('formLatitude').value = '';
            document.getElementById('formBranch').value = '';
            document.getElementById('formCluster').value = '';
            document.getElementById('formKabupaten').value = '';
            document.getElementById('formTotalOmzet').value = '';
            document.getElementById('formFlagOmzet').value = '';

            document.getElementById('outletFormModal').classList.remove('hidden');
        }

        function openEditModal(outlet) {
            const form = document.getElementById('outletForm');
            form.action = `/regional-map/${outlet.id}`;
            document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('formModalTitle').textContent = `Edit Outlet: ${outlet.id_outlet}`;

            document.getElementById('formIdOutlet').value = outlet.id_outlet;
            document.getElementById('formLongitude').value = outlet.longitude;
            document.getElementById('formLatitude').value = outlet.latitude;
            document.getElementById('formBranch').value = outlet.branch;
            document.getElementById('formCluster').value = outlet.cluster;
            document.getElementById('formKabupaten').value = outlet.kabupaten;
            document.getElementById('formTotalOmzet').value = outlet.total_omzet;
            document.getElementById('formFlagOmzet').value = outlet.flag_omzet;

            document.getElementById('outletFormModal').classList.remove('hidden');
        }

        function closeOutletFormModal() {
            document.getElementById('outletFormModal').classList.add('hidden');
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

            // Invalidate Leaflet map size after animation if map is present
            setTimeout(() => {
                if (typeof map !== 'undefined' && map) {
                    map.invalidateSize();
                }
            }, 320);
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            initInteractiveMap();
            loadMarkersAsync();

            // Restore KPI SBP Submenu state
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
