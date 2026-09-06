<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Revenue - Telkomsel Regional Bali Nusra</title>

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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
        
        .telkomsel-gradient {
            background: linear-gradient(135deg, #ED1C24 0%, #C8102E 50%, #9B0D23 100%);
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

        .shadow-card {
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.03);
        }

        .shadow-card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .shadow-card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.08), 0 4px 10px -2px rgba(0, 0, 0, 0.04);
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
<body class="h-full flex flex-col antialiased text-gray-800">

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

    <!-- MAIN BODY CONTAINER (Sidebar + Main Content) -->
    <div class="flex-1 flex min-h-0 overflow-hidden relative">

        @include('layouts.sidebar')

        <!-- MAIN SCROLLABLE CONTENT AREA -->
        <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- QUICK ACTIONS BAR -->
                <div class="flex flex-wrap items-center justify-end gap-2.5 bg-white p-4 rounded-3xl border border-gray-100 shadow-card">
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto justify-end">
                        @if(Auth::user()->isAdmin())
                            <!-- Button 1: Add Manual Data -->
                            <button type="button" onclick="openManualModal()" class="px-4 py-2.5 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-extrabold shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                                <i class="bi bi-plus-circle text-sm"></i>
                                <span>Tambah Data Manual</span>
                            </button>

                            <!-- Button 2: Import CSV / Excel -->
                            <button type="button" onclick="openImportModal()" class="px-4 py-2.5 rounded-xl bg-white hover:bg-gray-50 text-gray-800 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2 cursor-pointer">
                                <i class="bi bi-file-earmark-spreadsheet text-sm text-emerald-600"></i>
                                <span>Import CSV / Excel</span>
                            </button>
                        @endif

                        <!-- Button: Export CSV -->
                        <a href="{{ route('revenue.export', request()->query()) }}" class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2">
                            <i class="bi bi-file-earmark-arrow-down text-sm text-amber-600"></i>
                            <span>Export CSV</span>
                        </a>
                    </div>
                </div>

                <!-- 4 SUMMARY STATS KPI CARDS (ICON LEFT, TEXT CENTERED) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Total Kabupaten (RED BACKGROUND MATCHING IMAGE DESIGN) -->
                    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-4 sm:p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex flex-col justify-between group transition-all h-full">
                        <!-- Icon Left Translucent Box -->
                        <div class="absolute left-3.5 sm:left-4 top-1/2 -translate-y-1/2 w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/20 text-white flex items-center justify-center text-lg backdrop-blur-sm shadow-inner shrink-0">
                            <i class="bi bi-pin-map text-white"></i>
                        </div>

                        <!-- Top Title & Main Value Centered -->
                        <div class="w-full text-center flex flex-col items-center justify-center my-auto py-1">
                            <span class="text-xl sm:text-xl font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight mt-1">
                                TOTAL<br>KOTA/KABUPATEN
                            </span>
                            <h3 class="text-2xl sm:text-3xl lg:text-[34px] font-outfit font-black text-white mt-10 text-center tracking-tight">
                                {{ $totalRecords }} City
                            </h3>
                        </div>
                    </div>

                    <!-- Total Target Regional (RED BACKGROUND) -->
                    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex items-center gap-3.5 group transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-xl backdrop-blur-sm group-hover:scale-110 transition-transform shrink-0">
                            <i class="bi bi-bullseye text-white"></i>
                        </div>
                        <div class="flex-1 text-center flex flex-col items-center justify-center">
                            <span class="text-xl sm:text-xl font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight mt-2"> Target Revenue</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-white mt-14 text-center">Rp {{ number_format($totalTargetAll / 1000000000, 2, ',', '.') }} M</h3>
                            <span class="text-[13px] text-white/80 font-medium block text-center"></span>
                        </div>
                    </div>

                    <!-- Total MTD Realisasi (RED GRADIENT BACKGROUND MATCHING TARGET REVENUE BOX) -->
                    <div class="bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white p-4 sm:p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-red-600/60 relative overflow-hidden flex flex-col justify-between group transition-all h-full">
                        <!-- Icon Left Translucent Box -->
                        <div class="absolute left-3.5 top-3.5 w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-base backdrop-blur-sm shadow-inner shrink-0">
                            <i class="bi bi-cash-coin text-white"></i>
                        </div>

                        <!-- Top Title, Amount & Achieved Pill Badge -->
                        <div class="w-full text-center flex flex-col items-center justify-center pt-0.5">
                            <span class="text-xs sm:text-sm font-outfit font-black text-white uppercase tracking-wider block text-center leading-tight">ACHIEVEMENT REVENUE</span>
                            <h3 class="text-xl sm:text-2xl font-outfit font-black text-white mt-1 text-center tracking-tight">{{ \App\Models\ClusterRevenue::formatRevenueBm($totalMtdAll, true) }}</h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white text-[#ED1C24] text-[10px] font-black shadow-sm mt-1">
                                <i class="bi bi-check2-circle text-[10px]"></i>
                                {{ $avgAchAll }}% Achieved
                            </span>
                        </div>

                        <!-- Bottom 3 White Sub-cards (Broadband, Redeem PV, RGB) -->
                        <div class="grid grid-cols-3 gap-1 sm:gap-1.5 mt-3 w-full">
                            <!-- Sub-card 1: Broadband -->
                            <div class="bg-white rounded-xl p-1.5 sm:p-2 text-center flex flex-col items-center justify-between shadow-sm">
                                <span class="text-[8px] sm:text-[9px] font-outfit font-black text-[#ED1C24] sentencecase tracking-tighter leading-tight text-center">Revenue Broadband</span>
                                <span class="text-[10px] sm:text-xs font-outfit font-black text-[#ED1C24] mt-2 text-center">{{ \App\Models\ClusterRevenue::formatRevenueBm($totalMtdBroadband) }}</span>
                                <span class="text-[4px] sm:text-xl font-outfit font-black text-[#ED1C24] mt-1 text-center">{{ number_format($portionMtdBb, 1, ',', '.') }}%</span>
                            </div>

                            <!-- Sub-card 2: Redeem PV -->
                            <div class="bg-white rounded-xl p-1.5 sm:p-2 text-center flex flex-col items-center justify-between shadow-md">
                                <span class="text-[8px] sm:text-[9px] font-outfit font-black text-[#ED1C24] sentencecase tracking-tighter leading-tight text-center">Revenue Redeem PV</span>
                                <span class="text-[10px] sm:text-xs font-outfit font-black text-[#ED1C24] mt-2 text-center">{{ \App\Models\ClusterRevenue::formatRevenueBm($totalMtdRedeem) }}</span>
                                <span class="text-[4px] sm:text-xl font-outfit font-black text-[#ED1C24] mt-1 text-center">{{ number_format($portionMtdRedeem, 1, ',', '.') }}%</span>
                            </div>

                            <!-- Sub-card 3: RGB -->
                            <div class="bg-white rounded-xl p-1.5 sm:p-2 text-center flex flex-col items-center justify-between shadow-sm">
                                <span class="text-[8px] sm:text-[9px] font-outfit font-black text-[#ED1C24] sentencecase tracking-tighter leading-tight text-center">Revenue</span>
                                <span class="text-[8px] sm:text-[9px] font-outfit font-black text-[#ED1C24] sentencecase tracking-tighter leading-tight text-center">RGB</span>
                                <span class="text-[10px] sm:text-xs font-outfit font-black text-[#ED1C24] mt-2 text-center">{{ \App\Models\ClusterRevenue::formatRevenueBm($totalMtdRgb) }}</span>
                                <span class="text-[4px] sm:text-xl font-outfit font-black text-[#ED1C24] mt-1 text-center">{{ number_format($portionMtdRgb, 1, ',', '.') }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rata-rata MoM Growth (BLACK BACKGROUND) -->
                    <div class="bg-gradient-to-br from-[#121212] via-[#1A1A1E] to-[#26262B] text-white p-5 rounded-2xl shadow-card hover:shadow-card-hover border border-gray-800 relative overflow-hidden flex items-center gap-3.5 group transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl group-hover:scale-110 transition-transform shrink-0">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="flex-1 text-center flex flex-col items-center justify-center">
                            <span class="text-xs sm:text-sm font-outfit font-black text-gray-400 uppercase tracking-wider block text-center leading-tight">Rata-rata MoM Growth</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1 text-center">
                                {{ $avgGrowthMoM >= 0 ? '+' : '' }}{{ $avgGrowthMoM }}%
                            </h3>
                           
                        </div>
                    </div>
                </div>

                <!-- FILTER & SEARCH CONTROLS -->
                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-card">
                    <form method="GET" action="{{ route('revenue.manage') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                        
                        <!-- Search Keyword Input -->
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 mb-1">Cari Cluster / Kabupaten</label>
                            <div class="relative">
                                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik cluster atau kabupaten..." 
                                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                            </div>
                        </div>

                        <!-- Cluster Filter Dropdown -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Filter Cluster</label>
                            <select name="cluster" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                                <option value="all">Semua Cluster</option>
                                @foreach($availableClusters as $c)
                                    <option value="{{ $c }}" {{ $selectedCluster == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kabupaten Filter Dropdown -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Filter Kabupaten / Kota</label>
                            <select name="kabupaten" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                                <option value="all">Semua Kabupaten</option>
                                @foreach($availableKabupatens as $k)
                                    <option value="{{ $k }}" {{ ($selectedKabupaten ?? 'all') == $k ? 'selected' : '' }}>{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter Dropdown -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Status Pencapaian</label>
                            <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white cursor-pointer">
                                <option value="all" {{ $selectedStatus == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="melampaui_target" {{ $selectedStatus == 'melampaui_target' ? 'selected' : '' }}>Melampaui Target</option>
                                <option value="mencapai_target" {{ $selectedStatus == 'mencapai_target' ? 'selected' : '' }}>Mencapai Target</option>
                                <option value="tidak_mencapai_target" {{ $selectedStatus == 'tidak_mencapai_target' ? 'selected' : '' }}>Tidak Mencapai Target</option>
                            </select>
                        </div>

                        <!-- Sort By Dropdown -->
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Urutkan</label>
                            <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                                <option value="default" {{ $sortBy == 'default' ? 'selected' : '' }}>Cluster & Kab (A-Z)</option>
                                <option value="kabupaten_asc" {{ $sortBy == 'kabupaten_asc' ? 'selected' : '' }}>Nama Kabupaten (A-Z)</option>
                                <option value="ach_desc" {{ $sortBy == 'ach_desc' ? 'selected' : '' }}>Pencapaian Tertinggi</option>
                                <option value="ach_asc" {{ $sortBy == 'ach_asc' ? 'selected' : '' }}>Pencapaian Terendah</option>
                                <option value="growth_desc" {{ $sortBy == 'growth_desc' ? 'selected' : '' }}>Growth MoM Tertinggi</option>
                                <option value="growth_asc" {{ $sortBy == 'growth_asc' ? 'selected' : '' }}>Growth MoM Terendah</option>
                                <option value="mtd_desc" {{ $sortBy == 'mtd_desc' ? 'selected' : '' }}>Realisasi MTD Terbesar</option>
                            </select>
                        </div>

                    </form>
                </div>

                <!-- DETAILED DATA TABLE MATCHING 5 PARAMETERS -->
                <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-gray-900">Rincian Data Pendapatan </h3>
                        </div>
                        <span class="text-xs text-gray-500 font-medium">Menampilkan <strong>{{ $clusterRevenues->total() }}</strong> baris data</span>
                    </div>

                    <!-- Table -->
                    <div class="mt-4 overflow-x-auto rounded-2xl border border-gray-200 shadow-sm">
                        <table class="w-full text-center text-xs whitespace-nowrap">
                            <thead>
                                <!-- Top Group Header -->
                                <tr class="bg-[#ED1C24] text-white uppercase text-[11px] font-black tracking-wider select-none">
                                    <th rowspan="2" class="px-3 py-3 text-center w-8 bg-[#ED1C24] text-white font-black">NO</th>
                                    <th rowspan="2" class="px-4 py-3 min-w-[150px] text-center bg-[#ED1C24] text-white font-black">CLUSTER</th>
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
                                        <td class="px-3 py-3 text-center border-r border-slate-200">
                                            <span class="text-xs font-black text-gray-900 tracking-tight">{{ $clusterRevenues->firstItem() + $index }}</span>
                                        </td>
                                        
                                        <!-- 1. Cluster -->
                                        <td class="px-4 py-3 text-center border-r border-slate-200">
                                            <span class="text-xs font-black text-gray-900 tracking-tight uppercase">{{ $item->cluster_name }}</span>
                                        </td>

                                        <!-- Kabupaten / Kota -->
                                        <td class="px-4 py-3 text-center border-r border-slate-200">
                                            <span class="text-xs font-black text-gray-900 tracking-tight uppercase">{{ $item->kabupaten ?: '-' }}</span>
                                        </td>

                                        <!-- Periode -->
                                        <td class="px-3 py-3 text-center font-black text-gray-900 border-r border-slate-200">{{ $item->period_month }}</td>

                                        <!-- 2. REVENUE ALL -->
                                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                                            {{ number_format($targetAll, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                                            {{ number_format($mtdAll, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-left font-medium border-r border-slate-200">
                                            <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-black {{ $achAll >= 100 ? 'bg-emerald-600 text-white' : ($achAll >= 90 ? 'bg-yellow-400 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
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
                                            <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-black {{ $item->ach_broadband >= 100 ? 'bg-emerald-600 text-white' : ($item->ach_broadband >= 90 ? 'bg-yellow-400 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
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
                                            <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-black {{ $item->ach_redeem >= 100 ? 'bg-emerald-600 text-white' : ($item->ach_redeem >= 90 ? 'bg-yellow-400 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
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
                                            <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-black {{ $item->ach_rgb >= 100 ? 'bg-emerald-600 text-white' : ($item->ach_rgb >= 90 ? 'bg-yellow-400 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
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
                                            <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-black {{ $item->growth_mom >= 1.0 ? 'bg-emerald-600 text-white' : ($item->growth_mom >= 0 ? 'bg-yellow-400 text-gray-950' : 'bg-[#ED1C24] text-white') }}">
                                                {{ $item->growth_mom >= 0 ? '+' : '' }}{{ number_format($item->growth_mom, 1) }}%
                                            </span>
                                        </td>

                                        <!-- Notes / Status -->
                                        <td class="px-3 py-3 text-center">
                                            @php
                                                $catatanText = $item->catatan ?? ($item->notes ?: ($item->status ?: 'Mencapai Target'));
                                                if (str_contains(strtolower($catatanText), 'melampaui') || $item->growth_mom >= 1.0) {
                                                    $catatanText = 'Melampaui Target';
                                                    $catatanBadgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-300';
                                                } elseif (str_contains(strtolower($catatanText), 'tidak') || $item->growth_mom < 0) {
                                                    $catatanText = 'Tidak Mencapai Target';
                                                    $catatanBadgeClass = 'bg-red-100 text-red-800 border border-red-300';
                                                } else {
                                                    $catatanText = 'Mencapai Target';
                                                    $catatanBadgeClass = 'bg-amber-100 text-amber-900 border border-amber-300';
                                                }
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold inline-block {{ $catatanBadgeClass }}" title="{{ $catatanText }}">
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
                    <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                        <span>Menampilkan {{ $clusterRevenues->firstItem() ?? 0 }} - {{ $clusterRevenues->lastItem() ?? 0 }} dari {{ $clusterRevenues->total() }} data</span>
                        <div>{{ $clusterRevenues->links() }}</div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- BOTTOM FOOTER BAR -->
    <footer id="dashboardFooter" class="w-full h-8 bg-[#121212] text-gray-400 px-6 flex items-center justify-between text-[11px] font-medium shrink-0 border-t border-gray-800 select-none hidden opacity-0 transition-all duration-300 pointer-events-none">
        <span>©Telkomsel Bali Nusra 2026.</span>
        <span class="hidden sm:inline"></span>
    </footer>

    <!-- ==================== MODAL 1: TAMBAH DATA MANUAL ==================== -->
    <div id="manualModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-gray-100 relative animate-scale-up max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-xl shadow-sm">
                        <i class="bi bi-calculator-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900">Input Data Revenue Baru</h3>
                        <p class="text-xs text-gray-500">Persentase Target & MoM dihitung otomatis secara live</p>
                    </div>
                </div>
                <button onclick="closeManualModal()" class="text-gray-400 hover:text-gray-700 text-lg cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Error Banner -->
            <div id="manualErrorAlert" class="hidden mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0"></i>
                <span id="manualErrorMsg"></span>
            </div>

            <form action="{{ route('revenue.manual') }}" method="POST" class="mt-5 space-y-5" id="formRevenueManual" onsubmit="handleManualSubmit(event)">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ route('revenue.manage') }}">

                <!-- 1. CLUSTER & PERIODE -->
                <div class="p-4 rounded-2xl bg-gray-50/70 border border-gray-200/80 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-black text-gray-800 uppercase tracking-wide">
                        <span class="w-5 h-5 rounded-lg bg-gray-800 text-white flex items-center justify-center text-[10px]">1</span>
                        <span>INFORMASI CLUSTER, KABUPATEN & PERIODE</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Kabupaten / Kota <span class="text-red-500">*</span></label>
                            <input type="text" name="kabupaten" id="inKabupaten" onchange="autoFillClusterFromKabupaten()" oninput="autoFillClusterFromKabupaten()" list="kabupatenSuggestions" placeholder="Pilih / ketik kabupaten" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                            <datalist id="kabupatenSuggestions">
                                <option value="BULELENG"></option>
                                <option value="JEMBRANA"></option>
                                <option value="TABANAN"></option>
                                <option value="BADUNG"></option>
                                <option value="KOTA DENPASAR"></option>
                                <option value="BANGLI"></option>
                                <option value="GIANYAR"></option>
                                <option value="KARANG ASEM"></option>
                                <option value="KLUNGKUNG"></option>
                                <option value="ENDE"></option>
                                <option value="SIKKA"></option>
                                <option value="ALOR"></option>
                                <option value="FLORES TIMUR"></option>
                                <option value="LEMBATA"></option>
                                <option value="MANGGARAI"></option>
                                <option value="MANGGARAI BARAT"></option>
                                <option value="MANGGARAI TIMUR"></option>
                                <option value="NAGEKEO"></option>
                                <option value="NGADA"></option>
                                <option value="KOTA KUPANG"></option>
                                <option value="KUPANG"></option>
                                <option value="ROTE NDAO"></option>
                                <option value="BELU"></option>
                                <option value="MALAKA"></option>
                                <option value="TIMOR TENGAH SELATAN"></option>
                                <option value="TIMOR TENGAH UTARA"></option>
                                <option value="SABU RAIJUA"></option>
                                <option value="SUMBA BARAT"></option>
                                <option value="SUMBA BARAT DAYA"></option>
                                <option value="SUMBA TENGAH"></option>
                                <option value="SUMBA TIMUR"></option>
                                <option value="KOTA MATARAM"></option>
                                <option value="LOMBOK BARAT"></option>
                                <option value="LOMBOK TENGAH"></option>
                                <option value="LOMBOK TIMUR"></option>
                                <option value="LOMBOK UTARA"></option>
                                <option value="SUMBAWA"></option>
                                <option value="SUMBAWA BARAT"></option>
                                <option value="BIMA"></option>
                                <option value="KOTA BIMA"></option>
                                <option value="DOMPU"></option>
                            </datalist>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Cluster <span class="text-gray-400 font-normal">(Otomatis)</span></label>
                            <input type="text" name="cluster_name" id="inClusterName" list="clusterSuggestions" placeholder="Cluster otomatis terisi"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                            <datalist id="clusterSuggestions">
                                <option value="BALI BARAT"></option>
                                <option value="BALI TENGAH"></option>
                                <option value="BALI TIMUR"></option>
                                <option value="ENDE SIKKA"></option>
                                <option value="FLORES TIMUR"></option>
                                <option value="MANGGARAI"></option>
                                <option value="KUPANG ROTE"></option>
                                <option value="MALAKA TIMTIM BELU"></option>
                                <option value="SUMBA"></option>
                                <option value="LOMBOK"></option>
                                <option value="SUMBAWA"></option>
                                <option value="SUMBAWA TIMUR"></option>
                            </datalist>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Periode Bulan <span class="text-red-500">*</span></label>
                            <input type="text" name="period_month" value="Agustus 2026" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                            <input type="number" name="period_year" value="2026" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                        </div>
                    </div>
                </div>

                <!-- 2. REVENUE ALL -->
                <div class="p-4 rounded-2xl bg-red-50/40 border border-red-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-[#ED1C24] text-white flex items-center justify-center text-[10px]">2</span>
                            <span>REVENUE ALL</span>
                        </div>
                        <span id="badgeAchAll" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            Pencapaian: 0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Target Revenue All (Rp)</label>
                            <input type="number" name="target_revenue_all" id="inTargetAll" oninput="calcAutoPersentase()" placeholder="Contoh: 937000000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">MTD Revenue All (Rp)</label>
                            <input type="number" name="mtd_revenue_all" id="inMtdAll" oninput="calcAutoPersentase()" placeholder="Contoh: 980000000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- 3. REVENUE BROADBAND -->
                <div class="p-4 rounded-2xl bg-blue-50/40 border border-blue-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-blue-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-blue-600 text-white flex items-center justify-center text-[10px]">3</span>
                            <span>REVENUE BROADBAND</span>
                        </div>
                        <span id="badgeAchBb" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            Pencapaian: 0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Target Broadband (Rp)</label>
                            <input type="number" name="target_broadband" id="inTargetBb" oninput="calcAutoPersentase()" placeholder="Contoh: 535000000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">MTD Broadband (Rp)</label>
                            <input type="number" name="mtd_broadband" id="inMtdBb" oninput="calcAutoPersentase()" placeholder="Contoh: 560000000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- 4. REVENUE REDEEM PV -->
                <div class="p-4 rounded-2xl bg-amber-50/40 border border-amber-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-amber-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-amber-500 text-white flex items-center justify-center text-[10px]">4</span>
                            <span>REVENUE REDEEM (REDEEM PV)</span>
                        </div>
                        <span id="badgeAchRedeem" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            Pencapaian: 0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Target Redeem PV (Rp)</label>
                            <input type="number" name="target_redeem" id="inTargetRedeem" oninput="calcAutoPersentase()" placeholder="Contoh: 402000000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">MTD Redeem PV (Rp)</label>
                            <input type="number" name="mtd_redeem" id="inMtdRedeem" oninput="calcAutoPersentase()" placeholder="Contoh: 420000000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- 5. REVENUE RGB (Positioned before Growth Omzet) -->
                <div class="p-4 rounded-2xl bg-purple-50/40 border border-purple-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-purple-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-purple-600 text-white flex items-center justify-center text-[10px]">5</span>
                            <span>REVENUE RGB</span>
                        </div>
                        <span id="badgeAchRgb" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            Pencapaian: 0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Target RGB (Rp)</label>
                            <input type="number" name="target_rgb" id="inTargetRgb" oninput="calcAutoPersentase()" placeholder="Contoh: 350000000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">MTD RGB (Rp)</label>
                            <input type="number" name="mtd_rgb" id="inMtdRgb" oninput="calcAutoPersentase()" placeholder="Contoh: 365000000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- 5. GROWTH REVENUE (MoM) -->
                <div class="p-4 rounded-2xl bg-emerald-50/40 border border-emerald-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-emerald-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[10px]">5</span>
                            <span>GROWTH OMZET (MoM)</span>
                        </div>
                        <span id="badgeMoMGrowth" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            MoM: +0.0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Data Bln Sebelumnya (Rp)</label>
                            <input type="number" name="revenue_last_month" id="inLastMonth" oninput="calcAutoPersentase()" placeholder="Contoh: 949600000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Data Bulan Sekarang (Rp)</label>
                            <input type="number" name="revenue_current_month" id="inCurrentMonth" oninput="calcAutoPersentase()" placeholder="Contoh: 980000000"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- Catatan Opsional -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Catatan / Status (Opsional)</label>
                    <input type="text" name="notes" id="inNotes" placeholder="Otomatis terisi berdasarkan MoM atau ketik manual"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                </div>

                <!-- Submit Action Buttons -->
                <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
                    <button type="button" onclick="closeManualModal()" class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitManual" class="px-6 py-2.5 text-xs font-extrabold text-white bg-[#ED1C24] hover:bg-[#C8102E] rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-check2-circle text-base"></i>
                        <span>Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL 2: EDIT DATA CLUSTER ==================== -->
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-gray-100 relative animate-scale-up max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900">Edit Data Cluster Revenue</h3>
                        <p class="text-xs text-gray-500" id="editModalSubtitle">Perbarui target dan realisasi cluster</p>
                    </div>
                </div>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-700 text-lg cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Error Banner -->
            <div id="editErrorAlert" class="hidden mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0"></i>
                <span id="editErrorMsg"></span>
            </div>

            <form id="formRevenueEdit" onsubmit="handleEditSubmit(event)" class="mt-5 space-y-5">
                @csrf
                <input type="hidden" id="editRecordId">

                <!-- 1. CLUSTER & PERIODE -->
                <div class="p-4 rounded-2xl bg-gray-50/70 border border-gray-200/80 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-black text-gray-800 uppercase tracking-wide">
                        <span class="w-5 h-5 rounded-lg bg-gray-800 text-white flex items-center justify-center text-[10px]">1</span>
                        <span>INFORMASI CLUSTER, KABUPATEN & PERIODE</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Cluster</label>
                            <input type="text" name="cluster_name" id="editClusterName" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Kabupaten / Kota Cakupan</label>
                            <input type="text" name="kabupaten" id="editKabupaten" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Periode Bulan</label>
                            <input type="text" name="period_month" id="editPeriodMonth" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Tahun</label>
                            <input type="number" name="period_year" id="editPeriodYear" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                        </div>
                    </div>
                </div>

                <!-- 2. REVENUE ALL -->
                <div class="p-4 rounded-2xl bg-red-50/40 border border-red-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-red-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-[#ED1C24] text-white flex items-center justify-center text-[10px]">2</span>
                            <span>REVENUE ALL</span>
                        </div>
                        <span id="editBadgeAchAll" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            Pencapaian: 0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Target Revenue All (Rp)</label>
                            <input type="number" name="target_revenue_all" id="editTargetAll" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">MTD Revenue All (Rp)</label>
                            <input type="number" name="mtd_revenue_all" id="editMtdAll" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- 3. BROADBAND -->
                <div class="p-4 rounded-2xl bg-blue-50/40 border border-blue-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-blue-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-blue-600 text-white flex items-center justify-center text-[10px]">3</span>
                            <span>REVENUE BROADBAND</span>
                        </div>
                        <span id="editBadgeAchBb" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            Pencapaian: 0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Target Broadband (Rp)</label>
                            <input type="number" name="target_broadband" id="editTargetBb" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">MTD Broadband (Rp)</label>
                            <input type="number" name="mtd_broadband" id="editMtdBb" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- 4. REDEEM PV -->
                <div class="p-4 rounded-2xl bg-amber-50/40 border border-amber-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-amber-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-amber-500 text-white flex items-center justify-center text-[10px]">4</span>
                            <span>REVENUE REDEEM (PV)</span>
                        </div>
                        <span id="editBadgeAchRedeem" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            Pencapaian: 0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Target Redeem PV (Rp)</label>
                            <input type="number" name="target_redeem" id="editTargetRedeem" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">MTD Redeem PV (Rp)</label>
                            <input type="number" name="mtd_redeem" id="editMtdRedeem" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- 5. REVENUE RGB (Positioned before Growth Omzet) -->
                <div class="p-4 rounded-2xl bg-purple-50/40 border border-purple-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-purple-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-purple-600 text-white flex items-center justify-center text-[10px]">5</span>
                            <span>REVENUE RGB</span>
                        </div>
                        <span id="editBadgeAchRgb" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            Pencapaian: 0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Target RGB (Rp)</label>
                            <input type="number" name="target_rgb" id="editTargetRgb" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">MTD RGB (Rp)</label>
                            <input type="number" name="mtd_rgb" id="editMtdRgb" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- 5. GROWTH REVENUE -->
                <div class="p-4 rounded-2xl bg-emerald-50/40 border border-emerald-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-emerald-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[10px]">5</span>
                            <span>GROWTH OMZET (MoM)</span>
                        </div>
                        <span id="editBadgeMoMGrowth" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                            MoM: +0.0%
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Data Bln Sebelumnya (Rp)</label>
                            <input type="number" name="revenue_last_month" id="editLastMonth" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Data Bulan Sekarang (Rp)</label>
                            <input type="number" name="revenue_current_month" id="editCurrentMonth" oninput="calcEditAutoPersentase()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium bg-white">
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Catatan / Status</label>
                    <input type="text" name="notes" id="editNotes"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitEdit" class="px-6 py-2.5 text-xs font-extrabold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-check2-circle text-base"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL 3: IMPORT CSV / EXCEL ==================== -->
    <div id="importModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 relative animate-scale-up">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900">Import Data CSV / Excel</h3>
                        <p class="text-xs text-gray-500">Unggah berkas laporan revenue untuk diproses otomatis</p>
                    </div>
                </div>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-700 text-lg cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Error Banner -->
            <div id="importErrorAlert" class="hidden mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0"></i>
                <span id="importErrorMsg"></span>
            </div>

            <form action="{{ route('revenue.import') }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4" id="formRevenueImport" onsubmit="handleImportSubmit(event)">
                @csrf

                <!-- File Input Drop Zone -->
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-red-400 transition-colors bg-gray-50/50">
                    <i class="bi bi-cloud-arrow-up text-3xl text-gray-400 block mb-2"></i>
                    <p class="text-xs font-bold text-gray-700">Pilih file CSV atau Excel (.xlsx, .csv)</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Ukuran maksimal file: 10MB</p>
                    
                    <input type="file" name="file" id="inImportFile" accept=".csv,.xlsx,.xls,.txt" required class="mt-4 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#ED1C24] hover:file:bg-red-100 cursor-pointer">
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-all cursor-pointer">Batal</button>
                    <button type="submit" id="btnSubmitImport" class="px-5 py-2.5 text-xs font-bold text-white bg-[#ED1C24] hover:bg-[#C8102E] rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-upload"></i>
                        <span>Upload & Proses</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT & AJAX CONTROLLERS -->
    <script>
        // Auto-fill Cluster based on Kabupaten selection
        function autoFillClusterFromKabupaten() {
            const rawKab = document.getElementById('inKabupaten').value || '';
            const kab = rawKab.toUpperCase().replace(/^(KAB\.|KOTA|KABUPATEN)\s+/i, '').trim();
            const clusterInput = document.getElementById('inClusterName');
            if (!kab) return;

            // 1. BALI BARAT
            if (['BULELENG', 'JEMBRANA', 'TABANAN', 'SINGARAJA', 'NEGARA'].includes(kab)) {
                clusterInput.value = 'BALI BARAT';
            }
            // 2. BALI TENGAH
            else if (['BADUNG', 'DENPASAR', 'KOTA DENPASAR'].includes(kab)) {
                clusterInput.value = 'BALI TENGAH';
            }
            // 3. BALI TIMUR
            else if (['BANGLI', 'GIANYAR', 'KARANG ASEM', 'KARANGASEM', 'KLUNGKUNG', 'NUSA PENIDA'].includes(kab)) {
                clusterInput.value = 'BALI TIMUR';
            }
            // 4. ENDE SIKKA
            else if (['ENDE', 'SIKKA', 'MAUMERE'].includes(kab)) {
                clusterInput.value = 'ENDE SIKKA';
            }
            // 5. FLORES TIMUR
            else if (['ALOR', 'FLORES TIMUR', 'LEMBATA', 'LARANTUKA', 'KALABAHI', 'LEWOLEBA'].includes(kab)) {
                clusterInput.value = 'FLORES TIMUR';
            }
            // 6. MANGGARAI
            else if (['MANGGARAI', 'MANGGARAI BARAT', 'MANGGARAI TIMUR', 'NAGEKEO', 'NGADA', 'LABUAN BAJO', 'RUTENG', 'BORONG', 'MBAY', 'BAJAWA'].includes(kab)) {
                clusterInput.value = 'MANGGARAI';
            }
            // 7. KUPANG ROTE
            else if (['KUPANG', 'KOTA KUPANG', 'ROTE NDAO', 'ROTE', 'BAA', 'OELAMASI'].includes(kab)) {
                clusterInput.value = 'KUPANG ROTE';
            }
            // 8. MALAKA TIMTIM BELU
            else if (['BELU', 'MALAKA', 'TIMOR TENGAH SELATAN', 'TTS', 'TIMOR TENGAH UTARA', 'TTU', 'ATAMBUA', 'BETUN', 'SOE', 'KEFAMENANU'].includes(kab)) {
                clusterInput.value = 'MALAKA TIMTIM BELU';
            }
            // 9. SUMBA
            else if (['SABU RAIJUA', 'SUMBA BARAT', 'SUMBA BARAT DAYA', 'SUMBA TENGAH', 'SUMBA TIMUR', 'WAIKABUBAK', 'TAMBOLAKA', 'WAIBAKUL', 'WAINGAPU', 'MENIA'].includes(kab)) {
                clusterInput.value = 'SUMBA';
            }
            // 10. LOMBOK
            else if (['MATARAM', 'KOTA MATARAM', 'LOMBOK BARAT', 'LOMBOK TENGAH', 'LOMBOK TIMUR', 'LOMBOK UTARA', 'GERUNG', 'PRAYA', 'SELONG', 'TANJUNG'].includes(kab)) {
                clusterInput.value = 'LOMBOK';
            }
            // 11. SUMBAWA
            else if (['SUMBAWA', 'SUMBAWA BARAT', 'SUMBAWA BESAR', 'TALIWANG'].includes(kab)) {
                clusterInput.value = 'SUMBAWA';
            }
            // 12. SUMBAWA TIMUR
            else if (['BIMA', 'KOTA BIMA', 'DOMPU', 'WOHA', 'RABA'].includes(kab)) {
                clusterInput.value = 'SUMBAWA TIMUR';
            }
        }

        // Auto-fill Edit modal cluster based on kabupaten
        function autoFillClusterFromKabupatenEdit() {
            const rawKab = document.getElementById('editKabupaten').value || '';
            const kab = rawKab.toUpperCase().replace(/^(KAB\.|KOTA|KABUPATEN)\s+/i, '').trim();
            const clusterInput = document.getElementById('editClusterName');
            if (!kab) return;

            if (['BULELENG', 'JEMBRANA', 'TABANAN', 'SINGARAJA', 'NEGARA'].includes(kab)) clusterInput.value = 'BALI BARAT';
            else if (['BADUNG', 'DENPASAR', 'KOTA DENPASAR'].includes(kab)) clusterInput.value = 'BALI TENGAH';
            else if (['BANGLI', 'GIANYAR', 'KARANG ASEM', 'KARANGASEM', 'KLUNGKUNG'].includes(kab)) clusterInput.value = 'BALI TIMUR';
            else if (['ENDE', 'SIKKA'].includes(kab)) clusterInput.value = 'ENDE SIKKA';
            else if (['ALOR', 'FLORES TIMUR', 'LEMBATA'].includes(kab)) clusterInput.value = 'FLORES TIMUR';
            else if (['MANGGARAI', 'MANGGARAI BARAT', 'MANGGARAI TIMUR', 'NAGEKEO', 'NGADA'].includes(kab)) clusterInput.value = 'MANGGARAI';
            else if (['KUPANG', 'KOTA KUPANG', 'ROTE NDAO'].includes(kab)) clusterInput.value = 'KUPANG ROTE';
            else if (['BELU', 'MALAKA', 'TIMOR TENGAH SELATAN', 'TTS', 'TIMOR TENGAH UTARA', 'TTU'].includes(kab)) clusterInput.value = 'MALAKA TIMTIM BELU';
            else if (['SABU RAIJUA', 'SUMBA BARAT', 'SUMBA BARAT DAYA', 'SUMBA TENGAH', 'SUMBA TIMUR'].includes(kab)) clusterInput.value = 'SUMBA';
            else if (['MATARAM', 'KOTA MATARAM', 'LOMBOK BARAT', 'LOMBOK TENGAH', 'LOMBOK TIMUR', 'LOMBOK UTARA'].includes(kab)) clusterInput.value = 'LOMBOK';
            else if (['SUMBAWA', 'SUMBAWA BARAT'].includes(kab)) clusterInput.value = 'SUMBAWA';
            else if (['BIMA', 'KOTA BIMA', 'DOMPU'].includes(kab)) clusterInput.value = 'SUMBAWA TIMUR';
        }

        // Modal Controls: Manual Add
        function openManualModal() {
            const errAlert = document.getElementById('manualErrorAlert');
            if (errAlert) errAlert.classList.add('hidden');
            document.getElementById('manualModal').classList.remove('hidden');
        }
        function closeManualModal() {
            document.getElementById('manualModal').classList.add('hidden');
        }

        // Modal Controls: Edit Data
        function openEditModal(item) {
            const errAlert = document.getElementById('editErrorAlert');
            if (errAlert) errAlert.classList.add('hidden');

            document.getElementById('editRecordId').value = item.id;
            document.getElementById('editClusterName').value = item.cluster_name;
            document.getElementById('editKabupaten').value = item.kabupaten || '';
            document.getElementById('editPeriodMonth').value = item.period_month;
            document.getElementById('editPeriodYear').value = item.period_year;

            document.getElementById('editTargetAll').value = item.target_revenue_all || item.target_revenue || '';
            document.getElementById('editMtdAll').value = item.mtd_revenue_all || item.revenue_all || '';

            document.getElementById('editTargetBb').value = item.target_broadband || '';
            document.getElementById('editMtdBb').value = item.mtd_broadband || item.revenue_broadband || '';

            document.getElementById('editTargetRedeem').value = item.target_redeem || '';
            document.getElementById('editMtdRedeem').value = item.mtd_redeem || item.revenue_redeem_pv || '';

            document.getElementById('editTargetRgb').value = item.target_rgb || '';
            document.getElementById('editMtdRgb').value = item.mtd_rgb || item.revenue_rgb || '';

            document.getElementById('editLastMonth').value = item.revenue_last_month || '';
            document.getElementById('editCurrentMonth').value = item.revenue_current_month || (item.mtd_revenue_all || item.revenue_all || '');

            document.getElementById('editNotes').value = item.notes || item.status || '';

            document.getElementById('editModalSubtitle').innerText = `Cluster: ${item.cluster_name} (${item.period_month})`;

            calcEditAutoPersentase();
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Modal Controls: Import
        function openImportModal() {
            const errAlert = document.getElementById('importErrorAlert');
            if (errAlert) errAlert.classList.add('hidden');
            const modal = document.getElementById('importModal');
            if (modal) modal.classList.remove('hidden');
        }
        function closeImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) modal.classList.add('hidden');
        }

        // Helper to determine Catatan from MoM
        function getCatatanFromMoM(mom) {
            const val = parseFloat(mom);
            if (isNaN(val)) return 'Mencapai Target';
            if (val >= 1.0) {
                return 'Melampaui Target';
            } else if (val >= 0.0) {
                return 'Mencapai Target';
            } else {
                return 'Tidak Mencapai Target';
            }
        }

        // Live Auto-Calculation for Add Form
        function calcAutoPersentase() {
            const targetAll = parseFloat(document.getElementById('inTargetAll').value) || 0;
            const mtdAll = parseFloat(document.getElementById('inMtdAll').value) || 0;
            const badgeAchAll = document.getElementById('badgeAchAll');
            if (targetAll > 0) {
                const ach = ((mtdAll / targetAll) * 100).toFixed(1);
                badgeAchAll.innerText = `Pencapaian: ${ach}%`;
                badgeAchAll.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            } else {
                badgeAchAll.innerText = 'Pencapaian: 0%';
                badgeAchAll.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }

            const targetBb = parseFloat(document.getElementById('inTargetBb').value) || 0;
            const mtdBb = parseFloat(document.getElementById('inMtdBb').value) || 0;
            const badgeAchBb = document.getElementById('badgeAchBb');
            if (targetBb > 0) {
                const ach = ((mtdBb / targetBb) * 100).toFixed(1);
                badgeAchBb.innerText = `Pencapaian: ${ach}%`;
                badgeAchBb.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            } else {
                badgeAchBb.innerText = 'Pencapaian: 0%';
                badgeAchBb.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }

            const targetRedeem = parseFloat(document.getElementById('inTargetRedeem').value) || 0;
            const mtdRedeem = parseFloat(document.getElementById('inMtdRedeem').value) || 0;
            const badgeAchRedeem = document.getElementById('badgeAchRedeem');
            if (targetRedeem > 0) {
                const ach = ((mtdRedeem / targetRedeem) * 100).toFixed(1);
                badgeAchRedeem.innerText = `Pencapaian: ${ach}%`;
                badgeAchRedeem.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            } else {
                badgeAchRedeem.innerText = 'Pencapaian: 0%';
                badgeAchRedeem.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }

            const targetRgb = parseFloat(document.getElementById('inTargetRgb')?.value) || 0;
            const mtdRgb = parseFloat(document.getElementById('inMtdRgb')?.value) || 0;
            const badgeAchRgb = document.getElementById('badgeAchRgb');
            if (badgeAchRgb) {
                if (targetRgb > 0) {
                    const ach = ((mtdRgb / targetRgb) * 100).toFixed(1);
                    badgeAchRgb.innerText = `Pencapaian: ${ach}%`;
                    badgeAchRgb.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
                } else {
                    badgeAchRgb.innerText = 'Pencapaian: 0%';
                    badgeAchRgb.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
                }
            }

            const inCurr = document.getElementById('inCurrentMonth');
            if (!inCurr.value && mtdAll > 0) {
                inCurr.value = mtdAll;
            }

            const lastMonth = parseFloat(document.getElementById('inLastMonth').value) || 0;
            const currMonth = parseFloat(document.getElementById('inCurrentMonth').value) || 0;
            const badgeMoM = document.getElementById('badgeMoMGrowth');
            const inNotes = document.getElementById('inNotes');
            if (lastMonth > 0) {
                const mom = parseFloat((((currMonth - lastMonth) / lastMonth) * 100).toFixed(1));
                badgeMoM.innerText = `MoM: ${mom >= 0 ? '+' : ''}${mom.toFixed(1)}%`;
                badgeMoM.className = mom >= 1.0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (mom >= 0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
                if (inNotes && (!inNotes.value || ['Melampaui Target', 'Mencapai Target', 'Tidak Mencapai Target'].includes(inNotes.value))) {
                    inNotes.value = getCatatanFromMoM(mom);
                }
            } else {
                badgeMoM.innerText = 'MoM: +0.0%';
                badgeMoM.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }
        }

        // Live Auto-Calculation for Edit Form
        function calcEditAutoPersentase() {
            const targetAll = parseFloat(document.getElementById('editTargetAll').value) || 0;
            const mtdAll = parseFloat(document.getElementById('editMtdAll').value) || 0;
            const badgeAchAll = document.getElementById('editBadgeAchAll');
            if (targetAll > 0) {
                const ach = ((mtdAll / targetAll) * 100).toFixed(1);
                badgeAchAll.innerText = `Pencapaian: ${ach}%`;
                badgeAchAll.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            } else {
                badgeAchAll.innerText = 'Pencapaian: 0%';
                badgeAchAll.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }

            const targetBb = parseFloat(document.getElementById('editTargetBb').value) || 0;
            const mtdBb = parseFloat(document.getElementById('editMtdBb').value) || 0;
            const badgeAchBb = document.getElementById('editBadgeAchBb');
            if (targetBb > 0) {
                const ach = ((mtdBb / targetBb) * 100).toFixed(1);
                badgeAchBb.innerText = `Pencapaian: ${ach}%`;
                badgeAchBb.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            } else {
                badgeAchBb.innerText = 'Pencapaian: 0%';
                badgeAchBb.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }

            const targetRedeem = parseFloat(document.getElementById('editTargetRedeem').value) || 0;
            const mtdRedeem = parseFloat(document.getElementById('editMtdRedeem').value) || 0;
            const badgeAchRedeem = document.getElementById('editBadgeAchRedeem');
            if (targetRedeem > 0) {
                const ach = ((mtdRedeem / targetRedeem) * 100).toFixed(1);
                badgeAchRedeem.innerText = `Pencapaian: ${ach}%`;
                badgeAchRedeem.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            } else {
                badgeAchRedeem.innerText = 'Pencapaian: 0%';
                badgeAchRedeem.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }

            const targetRgb = parseFloat(document.getElementById('editTargetRgb')?.value) || 0;
            const mtdRgb = parseFloat(document.getElementById('editMtdRgb')?.value) || 0;
            const badgeAchRgb = document.getElementById('editBadgeAchRgb');
            if (badgeAchRgb) {
                if (targetRgb > 0) {
                    const ach = ((mtdRgb / targetRgb) * 100).toFixed(1);
                    badgeAchRgb.innerText = `Pencapaian: ${ach}%`;
                    badgeAchRgb.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
                } else {
                    badgeAchRgb.innerText = 'Pencapaian: 0%';
                    badgeAchRgb.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
                }
            }

            const lastMonth = parseFloat(document.getElementById('editLastMonth').value) || 0;
            const currMonth = parseFloat(document.getElementById('editCurrentMonth').value) || 0;
            const badgeMoM = document.getElementById('editBadgeMoMGrowth');
            const editNotes = document.getElementById('editNotes');
            if (lastMonth > 0) {
                const mom = parseFloat((((currMonth - lastMonth) / lastMonth) * 100).toFixed(1));
                badgeMoM.innerText = `MoM: ${mom >= 0 ? '+' : ''}${mom.toFixed(1)}%`;
                badgeMoM.className = mom >= 1.0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (mom >= 0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
                if (editNotes && (!editNotes.value || ['Melampaui Target', 'Mencapai Target', 'Tidak Mencapai Target', 'Optimal', 'Perlu Perhatian'].includes(editNotes.value))) {
                    editNotes.value = getCatatanFromMoM(mom);
                }
            } else {
                badgeMoM.innerText = 'MoM: +0.0%';
                badgeMoM.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }
        }

        // AJAX Handler: Manual Add Submit
        async function handleManualSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btnSubmitManual');
            const errAlert = document.getElementById('manualErrorAlert');
            const errMsg = document.getElementById('manualErrorMsg');
            
            errAlert.classList.add('hidden');
            errMsg.innerText = '';
            
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Menyimpan...</span>';

            try {
                const formData = new FormData(form);
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Tersimpan!</span>';
                    window.location.reload();
                } else {
                    errMsg.innerText = data.message || 'Terjadi kesalahan saat menyimpan data.';
                    errAlert.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            } catch (err) {
                errMsg.innerText = 'Gagal menghubungi server.';
                errAlert.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        }

        // AJAX Handler: Edit Data Submit
        async function handleEditSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('editRecordId').value;
            const btn = document.getElementById('btnSubmitEdit');
            const errAlert = document.getElementById('editErrorAlert');
            const errMsg = document.getElementById('editErrorMsg');
            
            errAlert.classList.add('hidden');
            errMsg.innerText = '';
            
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Memperbarui...</span>';

            try {
                const formData = new FormData(e.target);
                const payload = Object.fromEntries(formData.entries());
                payload._method = 'PUT';

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                const response = await fetch(`/revenue/${id}`, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Berhasil!</span>';
                    window.location.reload();
                } else {
                    errMsg.innerText = data.message || 'Gagal memperbarui data.';
                    errAlert.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            } catch (err) {
                errMsg.innerText = 'Gagal menghubungi server.';
                errAlert.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        }

        // AJAX Handler: Import File Submit
        async function handleImportSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btnSubmitImport');
            const errAlert = document.getElementById('importErrorAlert');
            const errMsg = document.getElementById('importErrorMsg');
            
            errAlert.classList.add('hidden');
            errMsg.innerText = '';
            
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Memproses Berkas...</span>';

            try {
                const formData = new FormData(form);
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Selesai!</span>';
                    window.location.reload();
                } else {
                    errMsg.innerText = data.message || 'Gagal memproses berkas import.';
                    errAlert.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            } catch (err) {
                errMsg.innerText = 'Terjadi kesalahan koneksi server.';
                errAlert.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        }

        // AJAX Handler: Delete Cluster
        async function handleDeleteCluster(e, url, name) {
            e.preventDefault();
            if (!confirm(`Hapus data cluster "${name}"?`)) return;

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                const data = await response.json().catch(() => ({}));
                if (response.ok && data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus data.');
                }
            } catch (err) {
                alert('Gagal menghubungi server.');
            }
        }

        // Dropdown Toggle
        function toggleUserDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            } else {
                menu.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('userDropdownContainer');
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        });

        // Close modal when clicking outside modal box (backdrop)
        ['manualModal', 'editModal', 'importModal'].forEach(id => {
            const modal = document.getElementById(id);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            }
        });

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
