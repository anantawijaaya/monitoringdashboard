<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Home Dashboard - Telkomsel Regional Bali Nusra</title>
    
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

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js CDN for interactive line charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        telkomsel: {
                            red: '#ED1C24',
                            darkred: '#C8102E',
                            deepred: '#8B1D24',
                            dark: '#121212',
                            sidebar: '#18181B',
                            softRed: '#FFF1F2',
                            cardBg: '#FFFFFF'
                        }
                    },
                    boxShadow: {
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.02)',
                        'card-hover': '0 12px 30px -4px rgba(237, 28, 36, 0.12), 0 4px 12px -2px rgba(0, 0, 0, 0.04)',
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

        .metric-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .metric-card:hover {
            transform: translateY(-3px);
        }

        .rank-card {
            transition: all 0.2s ease;
        }

        .rank-card:hover {
            transform: translateX(4px);
        }

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

        <!-- MAIN CONTENT CANVAS -->
        <main class="flex-1 p-5 sm:p-7 overflow-y-auto bg-[#F8FAFC]">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- FLASH SUCCESS / ERROR ALERTS -->
                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-check-circle-fill text-xl text-emerald-600"></i>
                            <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-exclamation-triangle-fill text-xl text-red-600"></i>
                            <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-800">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif                <!-- HEADER & ACTION BAR -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-600 uppercase tracking-wider">
                            <span class="inline-block w-2 h-2 rounded-full bg-red-600"></span>
                            DATA OVERVIEW
                        </div>
                    </div>

                    <!-- Filter Form -->
                    <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                            <div class="relative">
                                <select name="cluster" onchange="this.form.submit()" class="text-xs font-medium bg-white text-gray-700 border border-gray-200 rounded-2xl px-3.5 py-2.5 pr-8 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm cursor-pointer">
                                    <option value="all" {{ $selectedCluster == 'all' ? 'selected' : '' }}>Semua Cluster</option>
                                    @foreach($availableClusters as $c)
                                        <option value="{{ $c }}" {{ $selectedCluster == $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative">
                                <select name="period" onchange="this.form.submit()" class="text-xs font-medium bg-white text-gray-700 border border-gray-200 rounded-2xl px-3.5 py-2.5 pr-8 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm cursor-pointer">
                                    <option value="all" {{ $selectedPeriod == 'all' ? 'selected' : '' }}>Semua Periode</option>
                                    @foreach($availablePeriods as $p)
                                        <option value="{{ $p }}" {{ $selectedPeriod == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- EMPTY STATE ONBOARDING BANNER (WHEN 0 RECORDS IN DATABASE) -->
                @if(!$hasData)
                    <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-r from-red-50 via-white to-amber-50 border border-red-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4 animate-fade-in">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-red-100 text-[#ED1C24] flex items-center justify-center text-2xl shrink-0 shadow-sm">
                                <i class="bi bi-database-add"></i>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-extrabold text-gray-900">Database Masih Bersih (0 Data)</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Belum ada data revenue cluster. Masukkan data parameter untuk menghitung persentase pencapaian dan perangkingan secara otomatis.</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                            <a href="{{ route('revenue.manage') }}" class="px-4 py-2.5 text-xs font-bold text-white bg-[#ED1C24] hover:bg-[#C8102E] rounded-xl shadow transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>Buka Menu Kelola Revenue</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- 5 MAIN REVENUE BOXES (DATA-DRIVEN FROM DATABASE) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5">

                    <!-- BOX 1: REVENUE ALL (MTD) - RED BACKGROUND -->
                    <div class="metric-card bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
                        <div>
                            <!-- Header Label -->
                            <div>
                                <span class="text-xs sm:text-sm font-black text-white/90 uppercase tracking-wider block">REVENUE ALL (MTD)</span>
                            </div>

                            <!-- Big Main Value with extra generous gap -->
                            <div class="mt-8 sm:mt-10 flex items-baseline gap-2">
                                <span class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-none">{{ $revenueData['all']['value'] }}</span>
                                <span class="text-sm sm:text-base font-bold text-white/80">Miliar</span>
                            </div>

                            <!-- Target & Pill Row -->
                            <div class="flex items-center justify-between text-xs sm:text-sm mt-3.5">
                                <span class="text-white/90 font-medium">Target: <strong class="text-white font-black text-xs sm:text-sm">{{ $revenueData['all']['target'] }} M</strong></span>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs sm:text-sm font-black bg-white text-[#ED1C24] shadow-sm">{{ $revenueData['all']['achievement'] }}%</span>
                            </div>

                            <!-- White Progress Bar -->
                            <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden mt-2">
                                <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ min(100, $revenueData['all']['achievement']) }}%;"></div>
                            </div>
                        </div>

                        <!-- Bottom Stats Row -->
                        <div class="flex items-center justify-between text-xs sm:text-[13px] pt-3 mt-3.5 border-t border-white/15">
                            <span class="flex items-center gap-1.5 text-white font-bold">
                                <i class="bi bi-graph-up-arrow text-emerald-300"></i>
                                {{ $revenueData['all']['growth'] }} MoM
                            </span>
                        </div>
                    </div>

                    <!-- BOX 2: REVENUE BROADBAND - RED BACKGROUND -->
                    <div class="metric-card bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
                        <div>
                            <!-- Header Label -->
                            <div>
                                <span class="text-xs sm:text-sm font-black text-white/90 uppercase tracking-wider block">REVENUE BROADBAND</span>
                            </div>

                            <!-- Big Main Value with extra generous gap -->
                            <div class="mt-8 sm:mt-10 flex items-baseline gap-2">
                                <span class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-none">{{ $revenueData['broadband']['value'] }}</span>
                                <span class="text-sm sm:text-base font-bold text-white/80">Miliar</span>
                            </div>

                            <!-- Target & Pill Row -->
                            <div class="flex items-center justify-between text-xs sm:text-sm mt-3.5">
                                <span class="text-white/90 font-medium">Target: <strong class="text-white font-black text-xs sm:text-sm">{{ $revenueData['broadband']['target'] }} M</strong></span>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-white text-[#ED1C24] shadow-sm">{{ $revenueData['broadband']['achievement'] }}%</span>
                            </div>

                            <!-- White Progress Bar -->
                            <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden mt-2">
                                <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ min(100, $revenueData['broadband']['achievement']) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- BOX 3: REVENUE REDEEM PV - RED BACKGROUND -->
                    <div class="metric-card bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
                        <div>
                            <!-- Header Label -->
                            <div>
                                <span class="text-xs sm:text-sm font-black text-white/90 uppercase tracking-wider block">REVENUE REDEEM PV</span>
                            </div>

                            <!-- Big Main Value with extra generous gap -->
                            <div class="mt-8 sm:mt-10 flex items-baseline gap-2">
                                <span class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-none">{{ $revenueData['redeem_pv']['value'] }}</span>
                                <span class="text-sm sm:text-base font-bold text-white/80">Miliar</span>
                            </div>

                            <!-- Target & Pill Row -->
                            <div class="flex items-center justify-between text-xs sm:text-sm mt-3.5">
                                <span class="text-white/90 font-medium">Target: <strong class="text-white font-black text-xs sm:text-sm">{{ $revenueData['redeem_pv']['target'] }} M</strong></span>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-white text-[#ED1C24] shadow-sm">{{ $revenueData['redeem_pv']['achievement'] }}%</span>
                            </div>

                            <!-- White Progress Bar -->
                            <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden mt-2">
                                <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ min(100, $revenueData['redeem_pv']['achievement']) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- BOX 4: REVENUE RGB ALL - RED BACKGROUND -->
                    <div class="metric-card bg-gradient-to-br from-[#ED1C24] via-[#DC2626] to-[#B91C1C] text-white rounded-3xl p-5 sm:p-6 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
                        <div>
                            <!-- Header Label -->
                            <div>
                                <span class="text-xs sm:text-sm font-black text-white/90 uppercase tracking-wider block">REVENUE RGB ALL</span>
                            </div>

                            <!-- Big Main Value with extra generous gap -->
                            <div class="mt-8 sm:mt-10 flex items-baseline gap-2">
                                <span class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-none">{{ $revenueData['rgb']['value'] }}</span>
                                <span class="text-sm sm:text-base font-bold text-white/80">Miliar</span>
                            </div>

                            <!-- Target & Pill Row -->
                            <div class="flex items-center justify-between text-xs sm:text-sm mt-3.5">
                                <span class="text-white/90 font-medium">Target: <strong class="text-white font-black text-xs sm:text-sm">{{ $revenueData['rgb']['target'] }} M</strong></span>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-white text-[#ED1C24] shadow-sm">{{ $revenueData['rgb']['achievement'] }}%</span>
                            </div>

                            <!-- White Progress Bar -->
                            <div class="w-full h-2 bg-black/20 rounded-full overflow-hidden mt-2">
                                <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ min(100, $revenueData['rgb']['achievement']) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- BOX 4: AVG GROWTH OMZET (CALCULATED FROM LINE CHART) -->
                    <div class="metric-card bg-gradient-to-br from-[#121212] via-[#1A1A1E] to-[#26262B] text-white rounded-3xl p-5 sm:p-6 border border-gray-800 shadow-card hover:shadow-card-hover relative overflow-hidden flex flex-col justify-between group">
                        <!-- Header Top Row -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs sm:text-sm font-black text-gray-300 uppercase tracking-wider block">AVG GROWTH OMZET</span>
                            <div class="w-8 h-8 rounded-xl {{ $revenueData['growth']['is_positive'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }} flex items-center justify-center text-sm font-bold shrink-0">
                                <i class="bi {{ $revenueData['growth']['is_positive'] ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }}"></i>
                            </div>
                        </div>

                        <!-- Big Main Value (Clean & Perfectly Centered) -->
                        <div class="my-auto py-6 sm:py-8 flex items-center justify-center text-center">
                            <span class="text-4xl sm:text-5xl font-black {{ $revenueData['growth']['is_positive'] ? 'text-emerald-400' : 'text-rose-400' }} tracking-tight leading-none text-center">
                                {{ $revenueData['growth']['rate'] }}
                            </span>
                        </div>
                    </div>

                </div>


                <!-- TWO BOXES SECTION: TOP 3 RANKING ATAS & TOP 3 RANKING BAWAH (AUTO CALCULATED RANKINGS) -->
                <div id="rankingSection" class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">

                    <!-- BOX 1: TOP 3 RANKING ATAS (BEST PERFORMERS) -->
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-card relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-emerald-500"></div>

                        <div>
                            <!-- Header -->
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                                            <i class="bi bi-trophy-fill"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm sm:text-base font-black text-gray-900">TOP 3 RANKING ATAS</h3>
                                        </div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Leaderboard
                                </span>
                            </div>

                            <!-- List of Top 3 Items -->
                            <div class="mt-4 space-y-3">
                                @forelse($topRankings as $index => $item)
                                    @php
                                        $ach = $item->ach_revenue_all ?: $item->achievement_rate;
                                    @endphp
                                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm relative hover:border-emerald-200 transition-all">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3">
                                                <!-- Rank Number Badge -->
                                                <div class="w-7 h-7 rounded-lg bg-emerald-500 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5">
                                                    {{ $index + 1 }}
                                                </div>
                                                
                                                <div>
                                                    <!-- Cluster Name -->
                                                    <h4 class="text-sm font-black text-gray-900 leading-snug uppercase">{{ $item->cluster_name }}</h4>
                                                    
                                                    <!-- Kabupaten -->
                                                    @if($item->kabupaten)
                                                        <div class="flex items-center gap-1 mt-0.5">
                                                            <i class="bi bi-geo-alt-fill text-red-500 text-[14px]"></i>
                                                            <span class="text-xs font-medium border-r border-slate-200 uppercase">{{ $item->kabupaten }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Target & Pencapaian -->
                                                    <div class="mt-1 space-y-0.5 text-xs text-gray-400 font-medium">
                                                        <div>
                                                            Target: <span class="text-gray-700 font-bold">{{ $item->formatted_target }}</span>
                                                        </div>
                                                        <div>
                                                            Pencapaian: <span class="text-red-600 font-bold">{{ $item->formatted_revenue_all }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Ach Percentage on the Right -->
                                            <div class="text-right shrink-0">
                                                <span class="text-xs sm:text-sm font-black text-black-500 uppercase tracking-wider block leading-tight">ach (%)</span>
                                                <span class="text-2xl sm:text-3xl lg:text-[26px] font-extrabold block mt-1 leading-none tracking-tight">{{ number_format($ach, 2) }}%</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Bottom Green Line -->
                                        <div class="w-full h-2 bg-emerald-500 rounded-full mt-3"></div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-gray-400 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                        <i class="bi bi-inbox text-3xl block mb-1"></i>
                                        <p class="text-xs font-semibold">Belum ada data cluster yang dimasukkan.</p>
                                        <a href="{{ route('revenue.manage') }}" class="mt-2 text-xs text-[#ED1C24] font-bold hover:underline inline-block">Kelola Data Revenue</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- BOX 2: TOP 3 RANKING BAWAH (NEEDS ATTENTION) -->
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-card relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-red-500"></div>

                        <div>
                            <!-- Header -->
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div>
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-lg shrink-0">
                                            <i class="bi bi-trophy-fill"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm sm:text-base font-black text-gray-900">TOP 3 RANKING BAWAH</h3>
                                        </div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                    Perlu Evaluasi
                                </span>
                            </div>

                            <!-- List of Bottom 3 Items -->
                            <div class="mt-4 space-y-3">
                                @forelse($bottomRankings as $index => $item)
                                    @php
                                        $ach = $item->ach_revenue_all ?: $item->achievement_rate;
                                        $cardBorder = ($index === 2) ? 'border-red-200' : 'border-gray-100';
                                    @endphp
                                    <div class="bg-white rounded-2xl p-4 border {{ $cardBorder }} shadow-sm relative hover:border-red-200 transition-all">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3">
                                                <!-- Rank Number Badge (All Red) -->
                                                <div class="w-7 h-7 rounded-lg bg-red-600 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5">
                                                    {{ $index + 1 }}
                                                </div>
                                                
                                                <div>
                                                    <!-- Cluster Name -->
                                                    <h4 class="text-sm font-black text-gray-900 leading-snug uppercase">{{ $item->cluster_name }}</h4>
                                                    
                                                    <!-- Kabupaten -->
                                                    @if($item->kabupaten)
                                                        <div class="flex items-center gap-1 mt-0.5">
                                                            <i class="bi bi-geo-alt-fill text-red-500 text-[11px]"></i>
                                                            <span class="text-xs font-medium border-r border-slate-200 uppercase">{{ $item->kabupaten }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Target & Pencapaian -->
                                                    <div class="mt-1 space-y-0.5 text-xs text-gray-400 font-medium">
                                                        <div>
                                                            Target: <span class="text-gray-700 font-bold">{{ $item->formatted_target }}</span>
                                                        </div>
                                                        <div>
                                                            Pencapaian: <span class="text-red-600 font-bold">{{ $item->formatted_revenue_all }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Ach Percentage on the Right -->
                                            <div class="text-right shrink-0">
                                                <span class="text-xs sm:text-sm font-black text-black-500 uppercase tracking-wider block leading-tight">ach (%)</span>
                                                <span class="text-2xl sm:text-3xl lg:text-[26px] font-extrabold block mt-2 leading-none tracking-tight">{{ number_format($ach, 2) }}%</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Bottom Red Line -->
                                        <div class="w-full h-2 bg-red-500 rounded-full mt-4"></div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-gray-400 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                        <i class="bi bi-inbox text-3xl block mb-1"></i>
                                        <p class="text-xs font-semibold">Belum ada data cluster yang dimasukkan.</p>
                                        <a href="{{ route('revenue.manage') }}" class="mt-2 text-xs text-[#ED1C24] font-bold hover:underline inline-block">Kelola Data Revenue</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

                <!-- LINE CHART SECTION: GRAFIK PERBANDINGAN BULAN SEBELUMNYA & BULAN SEKARANG -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-100 shadow-card">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 shadow-sm">
                                <i class="bi bi-bar-chart-line-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-black text-gray-900 uppercase tracking-tight">GRAFIK PERBANDINGAN PENDAPATAN CLUSTER</h3>
                            </div>
                        </div>

                        <!-- Right Control Actions: Import Button (Admin Only) -->
                        @if(Auth::user()->isAdmin())
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" onclick="openGrowthImportModal()" class="px-4 py-2 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-bold shadow-sm hover:shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                                    <i class="bi bi-file-earmark-spreadsheet text-sm"></i>
                                    <span>Import Excel Grafik</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Series Toggle Chips / Legend -->
                    <div id="chartLegendContainer" class="flex flex-wrap items-center gap-2 sm:gap-3 pt-4 pb-2 text-xs font-semibold">
                        <button type="button" onclick="toggleDataset(0)" id="legendBtn-0" class="flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-50 text-yellow-800 border border-yellow-300 transition-all cursor-pointer">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#EAB308]"></span>
                            <span id="legendTitle-0">Bulan Sebelumnya (M)</span>
                        </button>
                        <button type="button" onclick="toggleDataset(1)" id="legendBtn-1" class="flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 transition-all cursor-pointer">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#059669]"></span>
                            <span id="legendTitle-1">Bulan Sekarang (M)</span>
                        </button>
                    </div>

                    <!-- Chart Container -->
                    <div class="relative w-full h-[320px] sm:h-[370px] mt-3">
                        <canvas id="revenueGrowthChart"></canvas>
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

    <!-- MODAL 1: FORM INPUT SESUAI SPESIFIKASI DENGAN KALKULASI OTOMATIS PERSENTASE -->
    <div id="manualModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-gray-100 relative animate-scale-up max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-xl shadow-sm">
                        <i class="bi bi-calculator-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900">Input Data Revenue & Kalkulasi Otomatis</h3>
                        <p class="text-xs text-gray-500">Persentase Target dan Pertumbuhan MoM dihitung otomatis secara live</p>
                    </div>
                </div>
                <button onclick="closeManualModal()" class="text-gray-400 hover:text-gray-700 text-lg cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Error banner inside modal -->
            <div id="manualErrorAlert" class="hidden mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0"></i>
                <span id="manualErrorMsg"></span>
            </div>

            <form action="{{ route('revenue.manual') }}" method="POST" class="mt-5 space-y-5" id="formRevenueManual" onsubmit="handleManualSubmit(event)">
                @csrf

                <!-- SECTION 1: CLUSTER & PERIODE -->
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

                <!-- SECTION 2: REVENUE ALL (TARGET & MTD) -->
                <div class="p-4 rounded-2xl bg-red-50/40 border border-red-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-red-700 uppercase tracking-wide">
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

                <!-- SECTION 3: REVENUE BROADBAND (TARGET & MTD) -->
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

                <!-- SECTION 4: REVENUE REDEEM PV (TARGET & MTD) -->
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

                <!-- SECTION 5: GROWTH REVENUE (DATA BLN SEBELUMNYA & DATA BULAN SEKARANG -> MoM OTOMATIS) -->
                <div class="p-4 rounded-2xl bg-emerald-50/40 border border-emerald-200/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-black text-emerald-700 uppercase tracking-wide">
                            <span class="w-5 h-5 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[10px]">5</span>
                            <span>GROWTH REVENUE (MoM)</span>
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
                    <input type="text" name="notes" placeholder="Contoh: Melampaui Target, Optimal, Perlu Peningkatan"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                </div>

                <!-- Submit Action Buttons -->
                <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
                    <button type="button" onclick="closeManualModal()" class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitManual" class="px-6 py-2.5 text-xs font-extrabold text-white bg-[#ED1C24] hover:bg-[#C8102E] rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-check2-circle text-base"></i>
                        <span>Simpan & Perangkingan Otomatis</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: IMPORT CSV / EXCEL -->
    <div id="importModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 relative animate-scale-up">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900">Import Data CSV / Excel</h3>
                        <p class="text-xs text-gray-500">Unggah file laporan revenue untuk diproses otomatis</p>
                    </div>
                </div>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-700 text-lg cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Error banner inside import modal -->
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

    <!-- ==================== MODAL 3: IMPORT EXCEL DATA GRAFIK GROWTH ==================== -->
    <div id="growthImportModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 relative animate-scale-up">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-xl">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900">Import Excel Data Grafik Growth</h3>
                        <p class="text-xs text-gray-500">Unggah berkas untuk membandingkan pendapatan bulan lalu vs bulan ini</p>
                    </div>
                </div>
                <button onclick="closeGrowthImportModal()" class="text-gray-400 hover:text-gray-700 text-lg cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Error Banner -->
            <div id="growthImportErrorAlert" class="hidden mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0"></i>
                <span id="growthImportErrorMsg"></span>
            </div>

            <form action="{{ route('growth.import') }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4" id="formGrowthImport" onsubmit="handleGrowthImportSubmit(event)">
                @csrf

                <!-- File Input Drop Zone -->
                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-red-400 transition-colors bg-gray-50/50">
                    <i class="bi bi-cloud-arrow-up text-3xl text-gray-400 block mb-2"></i>
                    <p class="text-xs font-bold text-gray-700">Pilih file Excel (.xlsx) atau CSV (.csv)</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Format 4 Kolom: Cluster, Kabupaten, Pendapatan Bulan Lalu, Pendapatan Bulan Ini</p>
                    
                    <input type="file" name="file" id="inGrowthFile" accept=".csv,.xlsx,.xls,.txt" required class="mt-4 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#ED1C24] hover:file:bg-red-100 cursor-pointer">
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100">
                    <button type="button" onclick="closeGrowthImportModal()" class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-all cursor-pointer">Batal</button>
                    <button type="submit" id="btnSubmitGrowthImport" class="px-5 py-2.5 text-xs font-bold text-white bg-[#ED1C24] hover:bg-[#C8102E] rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-upload"></i>
                        <span>Upload & Terapkan Grafik</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- INTERACTIVE JAVASCRIPT & LIVE FORM AUTO-CALCULATION -->
    <script>
        // Auto-fill Cluster based on Kabupaten selection
        function autoFillClusterFromKabupaten() {
            const rawKab = document.getElementById('inKabupaten').value || '';
            const kab = rawKab.toUpperCase().replace(/^(KAB\.|KOTA|KABUPATEN)\s+/i, '').trim();
            const clusterInput = document.getElementById('inClusterName');
            if (!kab) return;

            if (['BULELENG', 'JEMBRANA', 'TABANAN', 'SINGARAJA', 'NEGARA'].includes(kab)) {
                clusterInput.value = 'BALI BARAT';
            } else if (['BADUNG', 'DENPASAR', 'KOTA DENPASAR'].includes(kab)) {
                clusterInput.value = 'BALI TENGAH';
            } else if (['BANGLI', 'GIANYAR', 'KARANG ASEM', 'KARANGASEM', 'KLUNGKUNG', 'NUSA PENIDA'].includes(kab)) {
                clusterInput.value = 'BALI TIMUR';
            } else if (['ENDE', 'SIKKA', 'MAUMERE'].includes(kab)) {
                clusterInput.value = 'ENDE SIKKA';
            } else if (['ALOR', 'FLORES TIMUR', 'LEMBATA', 'LARANTUKA', 'KALABAHI', 'LEWOLEBA'].includes(kab)) {
                clusterInput.value = 'FLORES TIMUR';
            } else if (['MANGGARAI', 'MANGGARAI BARAT', 'MANGGARAI TIMUR', 'NAGEKEO', 'NGADA', 'LABUAN BAJO', 'RUTENG', 'BORONG', 'MBAY', 'BAJAWA'].includes(kab)) {
                clusterInput.value = 'MANGGARAI';
            } else if (['KUPANG', 'KOTA KUPANG', 'ROTE NDAO', 'ROTE', 'BAA', 'OELAMASI'].includes(kab)) {
                clusterInput.value = 'KUPANG ROTE';
            } else if (['BELU', 'MALAKA', 'TIMOR TENGAH SELATAN', 'TTS', 'TIMOR TENGAH UTARA', 'TTU', 'ATAMBUA', 'BETUN', 'SOE', 'KEFAMENANU'].includes(kab)) {
                clusterInput.value = 'MALAKA TIMTIM BELU';
            } else if (['SABU RAIJUA', 'SUMBA BARAT', 'SUMBA BARAT DAYA', 'SUMBA TENGAH', 'SUMBA TIMUR', 'WAIKABUBAK', 'TAMBOLAKA', 'WAIBAKUL', 'WAINGAPU', 'MENIA'].includes(kab)) {
                clusterInput.value = 'SUMBA';
            } else if (['MATARAM', 'KOTA MATARAM', 'LOMBOK BARAT', 'LOMBOK TENGAH', 'LOMBOK TIMUR', 'LOMBOK UTARA', 'GERUNG', 'PRAYA', 'SELONG', 'TANJUNG'].includes(kab)) {
                clusterInput.value = 'LOMBOK';
            } else if (['SUMBAWA', 'SUMBAWA BARAT', 'SUMBAWA BESAR', 'TALIWANG'].includes(kab)) {
                clusterInput.value = 'SUMBAWA';
            } else if (['BIMA', 'KOTA BIMA', 'DOMPU', 'WOHA', 'RABA'].includes(kab)) {
                clusterInput.value = 'SUMBAWA TIMUR';
            }
        }

        // Modal Controls
        function openManualModal() {
            const errAlert = document.getElementById('manualErrorAlert');
            if (errAlert) errAlert.classList.add('hidden');
            document.getElementById('manualModal').classList.remove('hidden');
        }
        function closeManualModal() {
            document.getElementById('manualModal').classList.add('hidden');
        }

        function openImportModal() {
            const errAlert = document.getElementById('importErrorAlert');
            if (errAlert) errAlert.classList.add('hidden');
            document.getElementById('importModal').classList.remove('hidden');
        }
        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        function openGrowthImportModal() {
            const errAlert = document.getElementById('growthImportErrorAlert');
            if (errAlert) errAlert.classList.add('hidden');
            document.getElementById('growthImportModal').classList.remove('hidden');
        }
        function closeGrowthImportModal() {
            document.getElementById('growthImportModal').classList.add('hidden');
        }

        // Seamless AJAX Manual Form Submit
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
                    window.location.href = "{{ route('dashboard') }}";
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

        // Seamless AJAX Import Form Submit
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
                    window.location.href = "{{ route('dashboard') }}";
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

        // Seamless AJAX Growth Chart Import Submit
        async function handleGrowthImportSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btnSubmitGrowthImport');
            const errAlert = document.getElementById('growthImportErrorAlert');
            const errMsg = document.getElementById('growthImportErrorMsg');
            
            errAlert.classList.add('hidden');
            errMsg.innerText = '';
            
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Memproses Data...</span>';

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
                    }
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success) {
                    btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Berhasil!</span>';
                    window.location.reload();
                } else {
                    errMsg.innerText = data.message || 'Gagal mengimpor file data grafik.';
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

        // Reset Growth Data
        async function resetGrowthData() {
            if (!confirm('Apakah Anda yakin ingin mereset data grafik khusus dan kembali ke sinkronisasi database revenue utama?')) {
                return;
            }

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            try {
                const response = await fetch('{{ route("growth.reset") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    }
                });
                const data = await response.json().catch(() => ({}));
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal mereset data.');
                }
            } catch (e) {
                alert('Gagal menghubungi server.');
            }
        }

        // Seamless AJAX Delete Cluster Record
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
                    window.location.href = "{{ route('dashboard') }}";
                } else {
                    alert(data.message || 'Gagal menghapus data.');
                }
            } catch (err) {
                alert('Gagal menghubungi server.');
            }
        }

        // Live Auto-Calculation of Percentages in Manual Input Form
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

            const inCurr = document.getElementById('inCurrentMonth');
            if (!inCurr.value && mtdAll > 0) {
                inCurr.value = mtdAll;
            }

            const lastMonth = parseFloat(document.getElementById('inLastMonth').value) || 0;
            const currMonth = parseFloat(document.getElementById('inCurrentMonth').value) || 0;
            const badgeMoM = document.getElementById('badgeMoMGrowth');
            if (lastMonth > 0) {
                const mom = (((currMonth - lastMonth) / lastMonth) * 100).toFixed(1);
                badgeMoM.innerText = `MoM: ${mom >= 0 ? '+' : ''}${mom}%`;
                badgeMoM.className = mom >= 2.0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (mom >= 0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            } else {
                badgeMoM.innerText = 'MoM: +0.0%';
                badgeMoM.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
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

        // Chart.js initialization for Perbandingan Pendapatan Cluster (Bulan Sebelumnya vs Bulan Sekarang)
        let growthChart;

        const chartClusterLabels = @json($chartData['labels'] ?? []);
        const chartKabupatens = @json($chartData['kabupatens'] ?? []);
        const chartClusters = @json($chartData['clusters'] ?? []);
        const datasetValLastMonth = @json($chartData['val_last_month'] ?? []);
        const datasetValCurrentMonth = @json($chartData['val_current_month'] ?? []);

        function initChart() {
            const chartCanvas = document.getElementById('revenueGrowthChart');
            if (!chartCanvas) return;
            const ctx = chartCanvas.getContext('2d');

            const gradientGreen = ctx.createLinearGradient(0, 0, 0, 320);
            gradientGreen.addColorStop(0, 'rgba(5, 150, 105, 0.25)');
            gradientGreen.addColorStop(1, 'rgba(5, 150, 105, 0.0)');

            const gradientYellow = ctx.createLinearGradient(0, 0, 0, 320);
            gradientYellow.addColorStop(0, 'rgba(234, 179, 8, 0.25)');
            gradientYellow.addColorStop(1, 'rgba(234, 179, 8, 0.0)');

            growthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartClusterLabels,
                    datasets: [
                        {
                            label: 'Bulan Sebelumnya (M)',
                            data: datasetValLastMonth,
                            borderColor: '#EAB308',
                            backgroundColor: gradientYellow,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#EAB308',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Bulan Sekarang (M)',
                            data: datasetValCurrentMonth,
                            borderColor: '#059669',
                            backgroundColor: gradientGreen,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#18181B',
                            titleColor: '#FFFFFF',
                            bodyColor: '#F3F4F6',
                            padding: 12,
                            borderRadius: 12,
                            usePointStyle: true,
                            callbacks: {
                                title: function(items) {
                                    if (!items.length) return '';
                                    const idx = items[0].dataIndex;
                                    const cluster = chartClusters[idx] || items[0].label;
                                    const kab = chartKabupatens[idx] || '';
                                    return (kab && kab !== cluster && kab !== '-') ? `${cluster} (${kab})` : cluster;
                                },
                                label: function(context) {
                                    const val = context.parsed.y;
                                    const datasetLabel = context.dataset.label || '';
                                    return `${datasetLabel}: Rp ${val.toFixed(2).replace('.', ',')} Miliar`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '700' }, color: '#334155' }
                        },
                        y: {
                            grid: { color: '#F1F5F9', drawBorder: false },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
                                color: '#64748B',
                                callback: function(value) {
                                    return 'Rp ' + value + ' M';
                                }
                            }
                        }
                    }
                }
            });
        }

        function toggleDataset(index) {
            if (!growthChart) return;
            const isVisible = growthChart.isDatasetVisible(index);
            const btn = document.getElementById(`legendBtn-${index}`);
            if (isVisible) {
                growthChart.hide(index);
                if (btn) btn.classList.add('opacity-40', 'line-through');
            } else {
                growthChart.show(index);
                if (btn) btn.classList.remove('opacity-40', 'line-through');
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

            // Invalidate Chart size after animation if Chart is present
            setTimeout(() => {
                if (typeof growthChart !== 'undefined' && growthChart) {
                    growthChart.resize();
                }
            }, 320);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initChart();
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
