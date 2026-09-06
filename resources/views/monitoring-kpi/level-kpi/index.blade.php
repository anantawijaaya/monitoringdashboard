<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monitoring Level KPI - Telkomsel Regional Bali Nusra</title>
    
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

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        telkomsel: {
                            red: '#ED1C24',
                            darkRed: '#C8102E',
                            dark: '#121212',
                            card: '#1E293B'
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        outfit: ['"Outfit"', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0B0F19;
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

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #1E293B;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="h-full flex flex-col text-slate-100 font-sans antialiased bg-[#0B0F19] selection:bg-red-500 selection:text-white overflow-hidden">

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

    <!-- 2. MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#0B0F19]">
        
        <!-- TOP HEADER BAR -->
        <header class="h-20 px-8 bg-[#0F172A]/80 border-b border-slate-800/80 flex items-center justify-between shrink-0 backdrop-blur-md z-20">
            <div>
                <h2 class="text-xl font-outfit font-black text-white tracking-tight flex items-center gap-2.5">
                    <i class="bi bi-speedometer2 text-red-500"></i>
                    <span>MONITORING LEVEL KPI</span>
                </h2>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Database Capaian Key Performance Indicators Cluster Regional Bali Nusa Tenggara</p>
            </div>

            <!-- Profile & Logout Dropdown -->
            <div class="relative">
                <button onclick="toggleUserDropdown(event)" class="flex items-center gap-3 p-1.5 pr-3 rounded-xl bg-slate-800/60 border border-slate-700/60 hover:bg-slate-700/60 transition-all cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-red-600 to-red-500 flex items-center justify-center font-bold text-white shadow-md">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="text-left hidden sm:block">
                        <div class="text-xs font-bold text-white leading-tight">{{ $user->name ?? 'User' }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">{{ Auth::user()->isVisitor() ? 'Visitor' : 'Administrator' }}</div>
                    </div>
                    <i class="bi bi-chevron-down text-xs text-slate-400"></i>
                </button>

                <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-48 rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl py-2 z-50">
                    <div class="px-4 py-2 border-b border-slate-800">
                        <p class="text-xs font-semibold text-white">{{ $user->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-red-400 hover:bg-red-500/10 flex items-center gap-2 transition-colors">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Keluar Aplikasi</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- MAIN SCROLLABLE DASHBOARD CANVAS -->
        <main class="flex-1 overflow-y-auto p-6 sm:p-8 space-y-6">

            <!-- ALERT NOTIFICATIONS -->
            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-5 py-3.5 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-lg">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-check-circle-fill text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
                </div>
            @endif

            <!-- 1. TOP KPI SUMMARY METRIC CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                
                <!-- CARD 1: MTD REV ALL -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-[11px] font-bold uppercase tracking-wider">TOTAL MTD REV ALL</span>
                        <i class="bi bi-currency-dollar text-red-400 text-base"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg font-outfit font-black text-white">Rp {{ number_format($totalMtdRevAll / 1000000000, 2, ',', '.') }} B</h3>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded {{ $avgAchRevAll >= 100 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                                {{ $avgAchRevAll }}% Ach
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">Target Rev All</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: MTD BROADBAND -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-[11px] font-bold uppercase tracking-wider">MTD BROADBAND</span>
                        <i class="bi bi-wifi text-blue-400 text-base"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg font-outfit font-black text-white">Rp {{ number_format($totalMtdBb / 1000000000, 2, ',', '.') }} B</h3>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-400">
                                {{ $avgAchBb }}% Ach
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">Broadband</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: MTD REDEEM PV -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-[11px] font-bold uppercase tracking-wider">MTD REDEEM PV</span>
                        <i class="bi bi-gift-fill text-purple-400 text-base"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg font-outfit font-black text-white">Rp {{ number_format($totalMtdPv / 1000000000, 2, ',', '.') }} B</h3>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-400">
                                {{ $avgAchPv }}% Ach
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">Redeem PV</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: MTD REVENUE RGB -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-[11px] font-bold uppercase tracking-wider">MTD REVENUE RGB</span>
                        <i class="bi bi-arrow-up-right-circle-fill text-emerald-400 text-base"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg font-outfit font-black text-white">Rp {{ number_format($totalMtdRgb / 1000000000, 2, ',', '.') }} B</h3>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400">
                                {{ $avgAchRgb }}% Ach
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">RGB Revenue</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 5: MOM GROWTH -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-[11px] font-bold uppercase tracking-wider">RATA-RATA MoM</span>
                        <i class="bi bi-graph-up-arrow text-amber-400 text-base"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg font-outfit font-black text-white">{{ $avgGrowthMoM > 0 ? '+' : '' }}{{ $avgGrowthMoM }}%</h3>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-400">
                                Growth Rev
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">MoM Performance</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 6: RATIO PJP -->
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between hover:border-red-500/50 transition-all">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-[11px] font-bold uppercase tracking-wider">RATIO PJP</span>
                        <i class="bi bi-pin-map-fill text-rose-400 text-base"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="text-lg font-outfit font-black text-white">{{ $avgRatioPjp }}%</h3>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[11px] font-extrabold px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-400">
                                Kepatuhan PJP
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">Rata-rata</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. FILTER & ACTION BAR -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-xl space-y-4">
                <form method="GET" action="{{ route('level-kpi.index') }}" class="flex flex-wrap items-center justify-between gap-3">
                    
                    <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
                        <!-- Search Box -->
                        <div class="relative w-full sm:w-64">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="Cari nama Cluster..." 
                                   class="w-full pl-9 pr-4 py-2 text-xs font-medium rounded-xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 focus:outline-none focus:border-red-500 transition-all">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        </div>

                        <!-- Cluster Dropdown -->
                        <select name="cluster" onchange="this.form.submit()" class="py-2 px-3 text-xs font-medium rounded-xl bg-slate-800/90 border border-slate-700 text-slate-200 focus:outline-none focus:border-red-500 cursor-pointer">
                            <option value="all">Semua Cluster</option>
                            @foreach($availableClusters as $c)
                                <option value="{{ $c }}" {{ $selectedCluster === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>

                        <!-- Period Dropdown -->
                        <select name="period" onchange="this.form.submit()" class="py-2 px-3 text-xs font-medium rounded-xl bg-slate-800/90 border border-slate-700 text-slate-200 focus:outline-none focus:border-red-500 cursor-pointer">
                            <option value="all">Semua Periode</option>
                            @foreach($availablePeriods as $p)
                                <option value="{{ $p }}" {{ $selectedPeriod === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>

                        <!-- Status Dropdown -->
                        <select name="status" onchange="this.form.submit()" class="py-2 px-3 text-xs font-medium rounded-xl bg-slate-800/90 border border-slate-700 text-slate-200 focus:outline-none focus:border-red-500 cursor-pointer">
                            <option value="all">Semua Status Target</option>
                            <option value="melampaui_target" {{ $selectedStatus === 'melampaui_target' ? 'selected' : '' }}>🟢 Melampaui (>= 100%)</option>
                            <option value="mencapai_target" {{ $selectedStatus === 'mencapai_target' ? 'selected' : '' }}>🟠 Mencapai (90% - 99%)</option>
                            <option value="tidak_mencapai_target" {{ $selectedStatus === 'tidak_mencapai_target' ? 'selected' : '' }}>🔴 Tidak Mencapai (< 90%)</option>
                        </select>

                        <!-- Sort Dropdown -->
                        <select name="sort" onchange="this.form.submit()" class="py-2 px-3 text-xs font-medium rounded-xl bg-slate-800/90 border border-slate-700 text-slate-200 focus:outline-none focus:border-red-500 cursor-pointer">
                            <option value="name_asc" {{ $sortBy === 'name_asc' ? 'selected' : '' }}>Urutan: Nama Cluster A-Z</option>
                            <option value="ach_desc" {{ $sortBy === 'ach_desc' ? 'selected' : '' }}>Urutan: Ach Rev All Tertinggi</option>
                            <option value="ach_asc" {{ $sortBy === 'ach_asc' ? 'selected' : '' }}>Urutan: Ach Rev All Terendah</option>
                            <option value="growth_desc" {{ $sortBy === 'growth_desc' ? 'selected' : '' }}>Urutan: Growth MoM Tertinggi</option>
                        </select>

                        @if($search || $selectedCluster !== 'all' || $selectedPeriod !== 'all' || $selectedStatus !== 'all' || $sortBy !== 'name_asc')
                            <a href="{{ route('level-kpi.index') }}" class="py-2 px-3 text-xs font-semibold rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 transition-all flex items-center gap-1.5">
                                <i class="bi bi-x-circle-fill"></i> Reset
                            </a>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('level-kpi.export', request()->query()) }}" class="py-2 px-4 rounded-xl bg-emerald-600/90 hover:bg-emerald-600 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-1.5">
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            <span>Export CSV</span>
                        </a>

                        @if(!Auth::user()->isVisitor())
                            <button type="button" onclick="openAddModal()" class="py-2 px-4 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs shadow-lg shadow-red-600/30 transition-all flex items-center gap-1.5 cursor-pointer">
                                <i class="bi bi-plus-circle-fill"></i>
                                <span>Tambah Data KPI</span>
                            </button>
                        @endif
                    </div>

                </form>
            </div>

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

        </main>

        <!-- FOOTER -->
        <footer class="h-10 bg-[#0F172A] border-t border-slate-800 px-6 flex items-center justify-between text-[11px] text-slate-500 shrink-0">
            <span>&copy;Telkomsel Bali Nusra 2026.</span>
            <span>Key Performance Indicator Management System</span>
        </footer>

    </div>
</div>

    <!-- MODAL ADD LEVEL KPI (ADMIN ONLY) -->
    <div id="addModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl p-6 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="bi bi-plus-circle-fill text-red-500"></i>
                    <span>Tambah Data Level KPI Cluster</span>
                </h3>
                <button onclick="closeAddModal()" class="text-slate-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
            </div>

            <form action="{{ route('level-kpi.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Cluster</label>
                        <input type="text" name="cluster_name" required placeholder="Contoh: DENPASAR" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Periode Bulan</label>
                        <input type="text" name="period_month" required value="Agustus 2026" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                        <input type="hidden" name="period_year" value="2026">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Target Rev All (Rp)</label>
                        <input type="number" name="target_rev_all" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">MTD Rev All (Rp)</label>
                        <input type="number" name="mtd_rev_all" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Target Broadband (Rp)</label>
                        <input type="number" name="target_rev_bb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">MTD Broadband (Rp)</label>
                        <input type="number" name="mtd_rev_bb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Target Redeem PV (Rp)</label>
                        <input type="number" name="target_pv" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">MTD Redeem PV (Rp)</label>
                        <input type="number" name="mtd_pv" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Target Revenue RGB (Rp)</label>
                        <input type="number" name="target_rgb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">MTD Revenue RGB (Rp)</label>
                        <input type="number" name="mtd_rgb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Last Month Growth Rev (Rp)</label>
                        <input type="number" name="target_growth_rev" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">This Month Growth Rev (Rp)</label>
                        <input type="number" name="mtd_growth_rev" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Ratio PJP (%)</label>
                        <input type="number" name="ratio_pjp" required min="0" max="100" step="0.1" value="95.0" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Catatan / Status</label>
                        <input type="text" name="notes" placeholder="Contoh: Melampaui Target" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold shadow-lg shadow-red-600/30 transition-all">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT LEVEL KPI (ADMIN ONLY) -->
    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl p-6 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="bi bi-pencil-square text-blue-500"></i>
                    <span>Edit Data Level KPI Cluster</span>
                </h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
            </div>

            <form id="editForm" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Cluster</label>
                        <input type="text" id="edit_cluster_name" name="cluster_name" required class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Periode Bulan</label>
                        <input type="text" id="edit_period_month" name="period_month" required class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                        <input type="hidden" id="edit_period_year" name="period_year" value="2026">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Target Rev All (Rp)</label>
                        <input type="number" id="edit_target_rev_all" name="target_rev_all" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">MTD Rev All (Rp)</label>
                        <input type="number" id="edit_mtd_rev_all" name="mtd_rev_all" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Target Broadband (Rp)</label>
                        <input type="number" id="edit_target_rev_bb" name="target_rev_bb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">MTD Broadband (Rp)</label>
                        <input type="number" id="edit_mtd_rev_bb" name="mtd_rev_bb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Target Redeem PV (Rp)</label>
                        <input type="number" id="edit_target_pv" name="target_pv" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">MTD Redeem PV (Rp)</label>
                        <input type="number" id="edit_mtd_pv" name="mtd_pv" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Target Revenue RGB (Rp)</label>
                        <input type="number" id="edit_target_rgb" name="target_rgb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">MTD Revenue RGB (Rp)</label>
                        <input type="number" id="edit_mtd_rgb" name="mtd_rgb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Last Month Growth Rev (Rp)</label>
                        <input type="number" id="edit_target_growth_rev" name="target_growth_rev" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">This Month Growth Rev (Rp)</label>
                        <input type="number" id="edit_mtd_growth_rev" name="mtd_growth_rev" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Ratio PJP (%)</label>
                        <input type="number" id="edit_ratio_pjp" name="ratio_pjp" required min="0" max="100" step="0.1" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Catatan / Status</label>
                        <input type="text" id="edit_notes" name="notes" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold shadow-lg shadow-blue-600/30 transition-all">Update Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT FOR MODALS & UI INTERACTION -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const icon = document.getElementById('sidebarToggleIcon');
            if (sidebar) {
                sidebar.classList.toggle('-ml-60');
                if (icon) {
                    icon.classList.toggle('rotate-180');
                }
            }
        }

        function toggleKpiSbpMenu() {
            const submenu = document.getElementById('kpiSbpSubmenu');
            const arrow = document.getElementById('kpiSbpArrow');
            if (submenu) {
                submenu.classList.toggle('hidden');
                if (arrow) {
                    arrow.classList.toggle('rotate-180');
                }
            }
        }

        function toggleUserDropdown(e) {
            e.stopPropagation();
            const m = document.getElementById('userDropdownMenu');
            m.classList.toggle('hidden');
        }

        document.addEventListener('click', function() {
            const m = document.getElementById('userDropdownMenu');
            if (m && !m.classList.contains('hidden')) {
                m.classList.add('hidden');
            }
        });

        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(row) {
            document.getElementById('editForm').action = "/level-kpi/" + row.id;
            document.getElementById('edit_cluster_name').value = row.cluster_name;
            document.getElementById('edit_period_month').value = row.period_month;
            document.getElementById('edit_period_year').value = row.period_year;
            document.getElementById('edit_target_rev_all').value = row.target_rev_all;
            document.getElementById('edit_mtd_rev_all').value = row.mtd_rev_all;
            document.getElementById('edit_target_rev_bb').value = row.target_rev_bb;
            document.getElementById('edit_mtd_rev_bb').value = row.mtd_rev_bb;
            document.getElementById('edit_target_pv').value = row.target_pv;
            document.getElementById('edit_mtd_pv').value = row.mtd_pv;
            document.getElementById('edit_target_rgb').value = row.target_rgb;
            document.getElementById('edit_mtd_rgb').value = row.mtd_rgb;
            document.getElementById('edit_target_growth_rev').value = row.target_growth_rev;
            document.getElementById('edit_mtd_growth_rev').value = row.mtd_growth_rev;
            document.getElementById('edit_ratio_pjp').value = row.ratio_pjp;
            document.getElementById('edit_notes').value = row.notes ?? '';

            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>
