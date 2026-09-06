<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peringkat Revenue - Telkomsel Regional Bali Nusra</title>

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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

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
                            black: '#121212',
                            dark: '#1E1E1E',
                            gray: '#2C2C2C',
                        }
                    },
                    boxShadow: {
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'card-hover': '0 10px 25px -5px rgba(0, 0, 0, 0.1)',
                        'gold': '0 10px 30px -5px rgba(234, 179, 8, 0.3)',
                        'silver': '0 10px 30px -5px rgba(148, 163, 184, 0.3)',
                        'bronze': '0 10px 30px -5px rgba(249, 115, 22, 0.3)',
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

        /* Custom Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }

        .animate-float {
            animation: floatSlow 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-gray-800 antialiased min-h-screen flex flex-col">

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

    <!-- ==================== MAIN BODY (Sidebar + Content) ==================== -->
    <div class="flex-1 flex min-h-0 overflow-hidden relative">

        @include('layouts.sidebar')

        <!-- MAIN SCROLLABLE CONTENT AREA -->
        <main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- HEADER TITLE & BREADCRUMB -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                            
                        </h1>
                    </div>

                    <!-- Breadcrumb & Actions (HANYA MUNCUL PADA VIEW KALKULASI LENGKAP) -->
                    <div id="rankingHeaderActions" class="flex items-center gap-2 {{ ($viewMode ?? 'peringkat') === 'lengkap' ? '' : 'hidden' }}">
                        @if(Auth::user() && !Auth::user()->isVisitor())
                            <!-- Impor Data Button -->
                            <button type="button" onclick="openImportModal()" 
                                    class="px-4 py-2 rounded-xl bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-2 shadow-sm cursor-pointer hover:shadow-md">
                                <i class="bi bi-file-earmark-arrow-up-fill text-xs"></i>
                                <span>Impor Data</span>
                            </button>
                        @endif

                        <!-- Export Button -->
                        <a href="{{ route('ranking.export', request()->query()) }}" 
                           class="px-3.5 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2">
                            <i class="bi bi-file-earmark-arrow-down text-sm text-amber-600"></i>
                            <span>Export</span>
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-2xl p-4 flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-emerald-500 text-base"></i>
                            <span class="font-bold">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-sm cursor-pointer">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 text-xs rounded-2xl p-4 flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-red-500 text-base"></i>
                            <span class="font-bold">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold text-sm cursor-pointer">&times;</button>
                    </div>
                @endif

                <!-- ==================== LEADERBOARD TABLE & VIEW SWITCHER ==================== -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 space-y-5 w-full">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-base font-bold shrink-0">
                                <i id="rankingHeaderIcon" class="bi {{ ($viewMode ?? 'ranking') === 'lengkap' ? 'bi-calculator-fill' : (($viewMode ?? 'ranking') === 'ringkasan' ? 'bi-grid-fill' : 'bi-trophy-fill') }}"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900" id="rankingHeaderTitle">
                                    {{ ($viewMode ?? 'ranking') === 'lengkap' ? 'Perhitungan Lengkap KPI Setiap City' : (($viewMode ?? 'ranking') === 'ringkasan' ? 'Ringkasan Performance KPI Setiap City' : 'City Ranking Berdasarkan Branch') }}
                                </h2>
                            </div>
                        </div>

                        <!-- Filter Periode & View Mode Switcher Inline -->
                        <div class="flex flex-wrap items-center gap-3 select-none">
                            <!-- Filter Periode Bulan Dropdown -->
                            <form method="GET" action="{{ route('ranking.index') }}" class="flex items-center" id="filterPeriodeForm">
                                <input type="hidden" name="view_mode" value="{{ $viewMode ?? 'ranking' }}">
                                <div class="relative min-w-[150px]">
                                    <select name="period" onchange="this.form.submit()" 
                                            class="w-full appearance-none bg-white border border-slate-200/80 rounded-full px-4 py-2 pr-8 text-xs font-bold text-slate-700 shadow-xs focus:outline-none focus:border-red-500 cursor-pointer">
                                        <option value="all">Semua Periode</option>
                                        @foreach($availablePeriods as $p)
                                            <option value="{{ $p }}" {{ $selectedPeriod == $p ? 'selected' : '' }}>Periode {{ $p }}</option>
                                        @endforeach
                                    </select>
                                    <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                </div>
                            </form>

                            <!-- View Mode Switcher (2 Views: Ranking Branch, Perhitungan Lengkap KPI) -->
                            <div class="flex flex-wrap items-center gap-2 select-none">
                                <!-- View 1: Ranking Branch -->
                                <button type="button" id="tabBtnRanking" 
                                        onclick="switchViewMode(event, 'ranking')"
                                        class="{{ ($viewMode ?? 'ranking') === 'ranking' ? 'px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] flex items-center gap-1.5' : 'px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent flex items-center gap-1.5' }}">
                                    <i class="bi bi-trophy-fill text-xs"></i>
                                    <span>Ranking City</span>
                                </button>

                                <!-- View 2: Perhitungan Lengkap KPI -->
                                <button type="button" id="tabBtnLengkap" 
                                        onclick="switchViewMode(event, 'lengkap')"
                                        class="{{ ($viewMode ?? 'ranking') === 'lengkap' ? 'px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] flex items-center gap-1.5' : 'px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent flex items-center gap-1.5' }}">
                                    <i class="bi bi-calculator-fill text-xs"></i>
                                    <span>Perhitungan Lengkap KPI</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== VIEW 1: RANKING TOP 3 ATAS & BAWAH PER BRANCH ==================== -->
                    <div id="viewRanking" class="{{ ($viewMode ?? 'ranking') === 'ranking' ? '' : 'hidden' }} space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($branchRankings as $bCode => $bData)
                                <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between">
                                    
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
                                        <div class="flex items-center justify-between pb-1.5 border-b border-emerald-100">
                                            <span class="text-[14px] font-black text-emerald-800 flex items-center gap-1.5 uppercase tracking-wider">
                                                <i class=""></i>
                                                Peringkat 3 Teratas 
                                            </span>
                                            <span class="text-[10px] font-extrabold text-emerald-700 border-emerald-200"></span>
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
                                                            <td class="py-2.5 px-3 font-black text-slate-700 uppercase tracking-tight truncate">
                                                                {{ $item->city ?? $item->kabupaten ?? '-' }}
                                                            </td>
                                                            <td class="py-2.5 px-3 font-black text-slate-700 uppercase tracking-tight truncate">
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
                                        <div class="flex items-center justify-between pb-1.5 border-b border-rose-100">
                                            <span class="text-[14px] font-black text-red-800 flex items-center gap-1.5 uppercase tracking-wider">
                                                <i class=""></i>
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
                                                            <td class="py-2.5 px-3 font-black text-slate-700 uppercase tracking-tight truncate">
                                                                {{ $item->city ?? $item->kabupaten ?? '-' }}
                                                            </td>
                                                            <td class="py-2.5 px-3 font-black text-slate-700 uppercase tracking-tight truncate">
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



                    <!-- ==================== VIEW 2: KALKULASI LENGKAP (FULL MATRIX TABLE) ==================== -->
                    <div id="viewLengkap" class="{{ ($viewMode ?? 'peringkat') === 'lengkap' ? '' : 'hidden' }} overflow-x-auto border border-slate-200/80 rounded-t-2xl shadow-xs overflow-hidden">
                        <table class="w-full text-left text-[11px] border-collapse min-w-[2800px]">
                            <thead>
                                <!-- Super Headers -->
                                <tr class="bg-[#ED1C24] text-white font-black text-[11px] uppercase tracking-wider text-center border-b border-red-600">
                                    <th rowspan="2" class="py-3 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white sticky left-0 z-10 font-black min-w-[180px] rounded-tl-2xl">City / Cluster</th>
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
                                        <td class="py-2.5 px-4 font-extrabold text-slate-900 border-r border-slate-200 bg-slate-50/80 sticky left-0 z-10">
                                            <span class="block text-xs uppercase tracking-tight text-slate-900">{{ $item->city ?? $item->kabupaten ?? '-' }}</span>
                                            <span class="block text-[10px] font-semibold text-slate-500 uppercase">{{ $item->cluster ?? $item->cluster_name ?? '-' }}</span>
                                        </td>

                                        <!-- Periode -->
                                        <td class="py-2.5 px-3 text-center font-bold text-slate-700 border-r border-slate-200 bg-slate-50/50">
                                            {{ $item->period_month }} {{ $item->period_year }}
                                        </td>

                                        <!-- Type -->
                                        <td class="py-2.5 px-3 text-center font-black border-r border-slate-200">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ ($item->type ?? 'HIGH') === 'VERY HIGH' ? '' : (($item->type ?? 'HIGH') === 'LOW' ? '' : '') }}">
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

                </div>

                <!-- JavaScript Switch View Mode -->
                <script>
                    function switchViewMode(e, mode) {
                        const viewRanking = document.getElementById('viewRanking');
                        const viewLengkap = document.getElementById('viewLengkap');

                        const btnRanking = document.getElementById('tabBtnRanking');
                        const btnLengkap = document.getElementById('tabBtnLengkap');

                        const headerTitle = document.getElementById('rankingHeaderTitle');
                        const headerIcon = document.getElementById('rankingHeaderIcon');
                        const headerActions = document.getElementById('rankingHeaderActions');

                        const activeClass = "px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] flex items-center gap-1.5";
                        const inactiveClass = "px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent flex items-center gap-1.5";

                        if (mode === 'lengkap') {
                            if (viewRanking) viewRanking.classList.add('hidden');
                            if (viewLengkap) viewLengkap.classList.remove('hidden');

                            if (btnLengkap) btnLengkap.className = activeClass;
                            if (btnRanking) btnRanking.className = inactiveClass;

                            if (headerTitle) headerTitle.innerText = "Perhitungan Lengkap KPI Setiap City";
                            if (headerIcon) headerIcon.className = "bi bi-calculator-fill";
                            if (headerActions) headerActions.classList.remove('hidden');
                        } else {
                            if (viewRanking) viewRanking.classList.remove('hidden');
                            if (viewLengkap) viewLengkap.classList.add('hidden');

                            if (btnRanking) btnRanking.className = activeClass;
                            if (btnLengkap) btnLengkap.className = inactiveClass;

                            if (headerTitle) headerTitle.innerText = "City Ranking Berdasarkan Branch";
                            if (headerIcon) headerIcon.className = "bi bi-trophy-fill";
                            if (headerActions) headerActions.classList.add('hidden');
                        }

                        const hiddenInput = document.querySelector('#filterPeriodeForm input[name="view_mode"]');
                        if (hiddenInput) hiddenInput.value = mode;

                        if (e && e.preventDefault) {
                            e.preventDefault();
                            const url = new URL(window.location.href);
                            url.searchParams.set('view_mode', mode);
                            window.history.pushState({}, '', url);
                        }
                    }
                </script>

            </div>
        </main>
    </div>

    <!-- BOTTOM FOOTER BAR -->
    <footer id="dashboardFooter" class="w-full h-8 bg-[#121212] text-gray-400 px-6 flex items-center justify-between text-[11px] font-medium shrink-0 border-t border-gray-800 select-none hidden opacity-0 transition-all duration-300 pointer-events-none">
        <span>© {{ date('Y') }} Telkomsel. All Rights Reserved.</span>
        <span class="hidden sm:inline">Monitoring Dashboard Regional Bali Nusra</span>
    </footer>

    <!-- ==================== MODAL IMPOR DATA PERINGKAT ==================== -->
    <div id="importRankingModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-200">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full mx-4 shadow-2xl border border-slate-100 relative space-y-6">
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-xl font-bold">
                        <i class="bi bi-file-earmark-arrow-up-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Impor Data Peringkat Revenue</h3>
                        <p class="text-xs text-slate-500 font-medium">Unggah berkas CSV atau Excel (.xls, .xlsx)</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold transition-colors cursor-pointer">&times;</button>
            </div>

            <!-- Download Template Box -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-2xl text-emerald-600"></i>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Unduh Format Template</p>
                        <p class="text-[11px] text-slate-500">Sesuaikan nama kolom dengan format template</p>
                    </div>
                </div>
                <a href="{{ route('ranking.template') }}" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-extrabold border border-slate-200 shadow-2xs transition-all flex items-center gap-1.5">
                    <i class="bi bi-download text-xs text-emerald-600"></i>
                    <span>Template</span>
                </a>
            </div>

            <!-- Form Upload -->
            <form id="importRankingForm" action="{{ route('ranking.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <!-- Opsi Mode Impor -->
                <div class="space-y-2">
                    <label class="text-xs font-extrabold text-slate-700 block">Metode Impor Data:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:border-red-500 cursor-pointer text-xs font-bold text-slate-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30 transition-all">
                            <input type="radio" name="mode" value="append" checked class="accent-red-600">
                            <span>Tambah / Update (Append)</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:border-red-500 cursor-pointer text-xs font-bold text-slate-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30 transition-all">
                            <input type="radio" name="mode" value="replace" class="accent-red-600">
                            <span>Ganti Seluruh Data (Replace)</span>
                        </label>
                    </div>
                </div>

                <!-- Input File -->
                <div class="space-y-2">
                    <label class="text-xs font-extrabold text-slate-700 block">Pilih Berkas CSV / Excel:</label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-red-400 rounded-2xl p-6 text-center transition-colors cursor-pointer bg-slate-50/50" onclick="document.getElementById('rankingFileInput').click()">
                        <i class="bi bi-cloud-arrow-up-fill text-3xl text-slate-400 block mb-2"></i>
                        <span id="rankingFileNameDisplay" class="text-xs font-bold text-slate-700 block">Klik di sini untuk memilih berkas</span>
                        <span class="text-[10px] text-slate-400 block mt-1">Mendukung format .csv, .xls, .xlsx (Maks 10 MB)</span>
                        <input type="file" id="rankingFileInput" name="file" accept=".csv, .txt, .xls, .xlsx" class="hidden" onchange="updateRankingFileName(this)">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 rounded-xl text-xs font-extrabold text-slate-600 hover:bg-slate-100 transition-colors">Batal</button>
                    <button type="submit" id="btnSubmitRankingImport" class="px-5 py-2.5 rounded-xl bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-2 shadow-md cursor-pointer">
                        <i class="bi bi-file-earmark-arrow-up-fill"></i>
                        <span>Mulai Impor Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openImportModal() {
            const modal = document.getElementById('importRankingModal');
            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                }, 10);
            }
        }

        function closeImportModal() {
            const modal = document.getElementById('importRankingModal');
            if (modal) {
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }
        }

        function updateRankingFileName(input) {
            const display = document.getElementById('rankingFileNameDisplay');
            if (input.files && input.files[0]) {
                display.textContent = input.files[0].name;
                display.classList.add('text-red-600');
            } else {
                display.textContent = 'Klik di sini untuk memilih berkas';
                display.classList.remove('text-red-600');
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
