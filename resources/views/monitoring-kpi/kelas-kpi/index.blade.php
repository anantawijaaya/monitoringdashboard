<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelas KPI - Telkomsel Regional Bali Nusra</title>
    
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

        <!-- 2. MAIN CONTENT AREA (LIGHT MODERN DASHBOARD UI) -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#F8FAFC]">
            
            <!-- MAIN SCROLLABLE DASHBOARD CANVAS -->
            <main class="flex-1 overflow-y-auto p-6 sm:p-8 space-y-6">

                <!-- TOP HEADER BAR -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div id="kpiHeaderTitleBlock" class="{{ ($viewMode ?? 'ringkasan') === 'ringkasan' ? '' : 'hidden' }}">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                            
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 font-medium">
                            
                        </p>
                    </div>

                    <!-- Right Header Controls: Impor Data & Export (HANYA MUNCUL PADA VIEW PERHITUNGAN LENGKAP KPI) -->
                    <div id="kpiHeaderActions" class="flex flex-wrap items-center gap-3 ml-auto {{ ($viewMode ?? 'ringkasan') === 'lengkap' ? '' : 'hidden' }}">
                        @if(Auth::user() && !Auth::user()->isVisitor())
                            <!-- Impor Data Button -->
                            <button type="button" onclick="openImportKpiModal()" class="px-4 py-2 rounded-full bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-1.5 shadow-sm cursor-pointer hover:shadow-md">
                                <i class="bi bi-file-earmark-arrow-up-fill text-xs"></i>
                                <span>Impor Data</span>
                            </button>
                        @endif

                        <!-- Export Button -->
                        <a href="{{ route('kelas-kpi.export', request()->query()) }}" class="px-4 py-2 rounded-full border border-slate-200/80 bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs transition-all flex items-center gap-1.5 shadow-xs">
                            <i class="bi bi-download text-xs"></i>
                            <span>Export</span>
                        </a>
                    </div>
                </div>

                <!-- ==================== VIEW 1: RINGKASAN KPI ==================== -->
                <div id="viewRingkasan" class="{{ ($viewMode ?? 'ringkasan') === 'ringkasan' ? '' : 'hidden' }}">
                    <div class="bg-white border border-slate-200/80 rounded-full shadow-sm p-6 space-y-5 w-full">
                        <!-- Toolbar Controls -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-2.5">`
                                <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-base font-bold">
                                    <i class="bi bi-grid-fill"></i>
                                </div>
                                <div>
                                    <h2 class="text-base font-extrabold text-slate-900">Ringkasan KPI Performance Cluster</h2>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <!-- Filter Periode Dropdown -->
                                <form method="GET" action="{{ route('kelas-kpi.index') }}" class="flex items-center gap-2" id="filterPeriodeFormRingkasan">
                                    <input type="hidden" name="view_mode" value="ringkasan">
                                    <div class="relative select-none">
                                        <select name="periode" onchange="this.form.submit()" class="appearance-none bg-white border border-slate-200/80 rounded-full px-4 py-2 pr-8 text-xs font-bold text-slate-700 shadow-xs focus:outline-none focus:border-red-500 cursor-pointer">
                                            <option value="all">Semua Periode</option>
                                            @foreach($availablePeriodes as $p)
                                                <option value="{{ $p }}" {{ ($selectedPeriode ?? 'all') === $p ? 'selected' : '' }}>Periode {{ $p }}</option>
                                            @endforeach
                                        </select>
                                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                    </div>
                                </form>

                                <!-- View Mode Switcher -->
                                <div class="flex items-center gap-2 select-none">
                                    <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'ringkasan'])) }}" 
                                       onclick="switchViewMode(event, 'ringkasan')"
                                       class="tab-btn-ringkasan px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'ringkasan' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24]' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                                        <i class="bi bi-grid-fill text-xs"></i>
                                        <span>Ringkasan KPI</span>
                                    </a>
                                    <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'lengkap'])) }}" 
                                       onclick="switchViewMode(event, 'lengkap')"
                                       class="tab-btn-lengkap px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'lengkap' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                                        <i class="bi bi-calculator-fill text-xs"></i>
                                        <span>Perhitungan Lengkap KPI</span>
                                    </a>
                                    <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'analisis'])) }}" 
                                       onclick="switchViewMode(event, 'analisis')"
                                       class="tab-btn-analisis px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'analisis' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                                        <i class="bi bi-graph-up-arrow text-xs"></i>
                                        <span>Analisis Data</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- RINGKASAN TABLE MATCHING EXACT USER MOCKUP -->
                        <div class="overflow-x-auto rounded-t-2xl border border-slate-200/80 shadow-xs">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-[#ED1C24] text-white font-black uppercase text-[11px] tracking-wider">
                                    <tr class="bg-[#ED1C24] text-white">
                                        <th class="py-4 px-4 text-center w-[5%] rounded-tl-2xl">NO</th>
                                        <th class="py-3.5 px-4 text-center w-[10%]">PERIODE</th>
                                        <th class="py-3.5 px-4 w-[20%]">CLUSTER</th>
                                        <th class="py-3.5 px-4 w-[30%]">MITRA</th>
                                        <th class="py-3.5 px-8 text-center w-[20%]">TIPE CLUSTER</th>
                                        <th class="py-3.5 px-4 text-center w-[11%]">TOTAL SKOR</th>
                                        <th class="py-3.5 px-4 text-center w-[11%] rounded-tr-2xl">KELAS KPI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium text-slate-700 bg-white">
                                    @forelse($kelasKpis as $index => $row)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-3.5 px-4 text-center font-bold text-slate-400 font-semibold">
                                                {{ $kelasKpis->firstItem() + $index }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center font-black text-slate-600 text-xs">
                                                {{ $row->periode ?: '2026-08' }}
                                            </td>
                                            <td class="py-3.5 px-4 font-black text-slate-900 tracking-tight">
                                                {{ $row->cluster ?: $row->new_cluster }}
                                            </td>
                                            <td class="py-3.5 px-4 font-bold text-slate-600 uppercase">
                                                {{ $row->mitra ?: 'TELKOMSEL REGIONAL BALI NUSRA' }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center font-extrabold text-slate-700 uppercase tracking-wide">
                                                {{ $row->type }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center font-bold text-blue-600 bg-blue-50/40 text-sm">
                                                 {{ number_format($row->final_score ?: $row->total_score, 2, ',', '.') }}
                                             </td>
                                            <td class="py-3.5 px-4 text-center">
                                                @if($row->class === 'PLATINUM')
                                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-sky-600 text-white shadow-xs tracking-wider uppercase min-w-[85px]">PLATINUM</span>
                                                @elseif($row->class === 'GOLD')
                                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-[#EAB308] text-white shadow-xs tracking-wider uppercase min-w-[85px]">GOLD</span>
                                                @elseif($row->class === 'SILVER')
                                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-[#94A3B8] text-white shadow-xs tracking-wider uppercase min-w-[85px]">SILVER</span>
                                                @elseif($row->class === 'BRONZE')
                                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-[#B45309] text-white shadow-xs tracking-wider uppercase min-w-[85px]">BRONZE</span>
                                                @else
                                                    <span class="inline-block px-4 py-1.5 rounded-md text-[10px] font-black bg-slate-900 text-white shadow-xs tracking-wider uppercase min-w-[85px]">BLACK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-10 text-center text-slate-400 font-medium">
                                                Belum ada data cluster yang sesuai.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Pagination -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 text-xs text-slate-500 font-medium">
                            <div>Menampilkan {{ $kelasKpis->firstItem() ?? 1 }}–{{ $kelasKpis->lastItem() ?? 12 }} dari {{ $kelasKpis->total() }} cluster</div>
                            <div>{{ $kelasKpis->links() }}</div>
                        </div>
                    </div>
                </div>

                <!-- ==================== VIEW 2: PERHITUNGAN LENGKAP KPI ==================== -->
                <div id="viewLengkap" class="{{ ($viewMode ?? 'ringkasan') === 'lengkap' ? '' : 'hidden' }}">
                    <div class="space-y-5">


                        <!-- FULL PERHITUNGAN LENGKAP TABLE CARD -->
                        <div class="bg-white border border-slate 100/80 rounded-2xl shadow-sm p-8 space-y-7">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-base font-bold">
                                        <i class="bi bi-calculator-fill"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900">Perhitungan Lengkap KPI Perfomance Cluster</h2>
                                        <p class="text-xs text-slate-500"> </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <!-- Filter Periode Dropdown -->
                                    <form method="GET" action="{{ route('kelas-kpi.index') }}" class="flex items-center gap-2" id="filterPeriodeFormLengkap">
                                        <input type="hidden" name="view_mode" value="lengkap">
                                        <div class="relative select-none">
                                            <select name="periode" onchange="this.form.submit()" class="appearance-none bg-white border border-slate-200/80 rounded-full px-4 py-2 pr-8 text-xs font-bold text-slate-700 shadow-xs focus:outline-none focus:border-red-500 cursor-pointer">
                                                <option value="all">Semua Periode</option>
                                                @foreach($availablePeriodes as $p)
                                                    <option value="{{ $p }}" {{ ($selectedPeriode ?? 'all') === $p ? 'selected' : '' }}>Periode {{ $p }}</option>
                                                @endforeach
                                            </select>
                                            <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                        </div>
                                    </form>

                                    <!-- View Mode Switcher -->
                                    <div class="flex items-center gap-2 select-none">
                                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'ringkasan'])) }}" 
                                           onclick="switchViewMode(event, 'ringkasan')"
                                           class="tab-btn-ringkasan px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'ringkasan' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                                            <i class="bi bi-grid-fill text-xs"></i>
                                            <span>Ringkasan KPI</span>
                                        </a>
                                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'lengkap'])) }}" 
                                           onclick="switchViewMode(event, 'lengkap')"
                                           class="tab-btn-lengkap px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'lengkap' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24]' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                                            <i class="bi bi-calculator-fill text-xs"></i>
                                            <span>Perhitungan Lengkap KPI</span>
                                        </a>
                                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'analisis'])) }}" 
                                           onclick="switchViewMode(event, 'analisis')"
                                           class="tab-btn-analisis px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'analisis' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                                            <i class="bi bi-graph-up-arrow text-xs"></i>
                                            <span>Analisis Data</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            @if(session('success'))
                                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl p-4 flex items-center justify-between shadow-sm">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-emerald-500 text-base"></i>
                                        <span class="font-bold">{{ session('success') }}</span>
                                    </div>
                                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-sm cursor-pointer">&times;</button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl p-4 flex items-center justify-between shadow-sm">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-exclamation-triangle-fill text-red-500 text-base"></i>
                                        <span class="font-bold">{{ session('error') }}</span>
                                    </div>
                                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold text-sm cursor-pointer">&times;</button>
                                </div>
                            @endif

                             <!-- MAIN FULL CALCULATION MATRIX TABLE -->
                            <div class="overflow-x-auto border border-slate-200 rounded-t-2xl shadow-xs overflow-hidden">
                                <table class="w-full text-left text-xs whitespace-nowrap border-collapse">
                                    <thead>
                                        <!-- Section Super Headers (Uniform Telkomsel Red #ED1C24) -->
                                        <tr class="bg-[#ED1C24] text-white font-black text-[11px] uppercase tracking-wider text-center border-b border-red-600">
                                            <th rowspan="2" class="py-3 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white sticky left-0 z-10 font-black rounded-tl-2xl">Cluster</th>
                                            <th rowspan="2" class="py-3 px-3 border-r border-red-600/80 bg-[#ED1C24] text-white font-black">Periode</th>
                                            <th colspan="6" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">Revenue All</th>
                                            <th colspan="6" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">Revenue Broadband</th>
                                            <th colspan="6" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">Revenue Redeem PV</th>
                                            <th colspan="6" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">Revenue RGB</th>
                                            <th colspan="14" class="py-2.5 px-4 border-r border-red-600/80 bg-[#ED1C24] text-white font-extrabold">Omzet Outlet</th>
                                            <th colspan="2" class="py-2.5 px-4 bg-[#ED1C24] text-white font-black rounded-tr-2xl">Hasil Akhir KPI</th>
                                        </tr>
                                        <!-- Column Metric Sub Headers -->
                                        <tr class="bg-[#ED1C24] text-white font-bold text-[10px] uppercase tracking-tight border-b border-red-600 text-center">
                                            <!-- Rev ALL -->
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">TARGET</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">actual</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">ach%</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">score</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">weight</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E]">final score</th>

                                            <!-- Rev BB -->
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">target</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">actual</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">ach%</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">score</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">weight</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E]">final score</th>

                                            <!-- Rev PV -->
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">target</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">actual</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">ach%</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">score</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">weight</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E]">final score</th>

                                            <!-- Rev RGB -->
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">target</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">actual</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">ach%</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">score</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">weight</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E]">final score</th>

                                            <!-- Growth -->
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">omzet m1</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">mtd m1</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">tgt 3%</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">mtd</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">growth%</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">growth tgt</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">rev growth ach%</th>

                                            <!-- Outlet PJP -->
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">outlet pjp</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">pjp growth</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">ratio pjp</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">omzet outlet ach%</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white font-black">score</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 text-white">weight</th>
                                            <th class="py-2 px-3 border-r border-red-600/80 font-black text-white bg-[#C8102E]">final score</th>

                                            <!-- Final Result -->
                                            <th class="py-2 px-4 border-r border-red-600/80 font-black text-white bg-[#B91C1C]">total final score</th>
                                            <th class="py-2 px-4 font-black text-white bg-[#B91C1C]">Kelas KPI</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                        @forelse($kelasKpis as $index => $row)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <!-- Cluster -->
                                                <td class="py-3 px-4 font-bold text-slate-900 border-r border-slate-200 bg-slate-50/80 sticky left-0 z-10">
                                                    {{ $row->cluster ?: $row->new_cluster }}
                                                </td>

                                                <!-- Periode -->
                                                <td class="py-3 px-3 text-center font-bold text-slate-900 border-r border-slate-200 bg-slate-50/50">
                                                    {{ $row->periode ?: '2026-08' }}
                                                </td>

                                                <!-- Rev ALL -->
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->target_rev_all, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->actual_rev_all, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ach_rev_all ?: $row->ach_percent, 1, ',', '.') }}%</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_rev_all ?: $row->score, 0) }},0</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">15%</td>
                                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_rev_all, 2, ',', '.') }}</td>

                                                <!-- Rev BB -->
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->target_rev_bb, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->actual_rev_bb, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ach_rev_bb, 1, ',', '.') }}%</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_rev_bb, 0) }},0</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->weight_rev_bb ?: (strtoupper($row->type) === 'LOW' ? 20 : 15), 0) }}%</td>
                                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_rev_bb, 2, ',', '.') }}</td>

                                                <!-- Rev PV -->
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->target_rev_pv, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->actual_rev_pv, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ach_rev_pv, 1, ',', '.') }}%</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_rev_pv, 0) }},0</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->weight_rev_pv ?: (strtoupper($row->type) === 'LOW' ? 20 : 25), 0) }}%</td>
                                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_rev_pv, 2, ',', '.') }}</td>

                                                <!-- Rev RGB -->
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->target_rgb, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->actual_rgb, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ach_rgb, 1, ',', '.') }}%</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_rgb, 0) }},0</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">20%</td>
                                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_rgb, 2, ',', '.') }}</td>

                                                <!-- Growth Revenue -->
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->omzet_rev_m1, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->mtd_m1, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->tgt_3_percent, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->mtd, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->growth, 0, ',', '.') }}%</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->growth_tgt, 0, ',', '.') }}%</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->rev_growth_ach_percent, 1, ',', '.') }}%</td>

                                                <!-- Outlet PJP & Omzet Outlet -->
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->outlet_pjp, 0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->outlet_pjp_growth,0, ',', '.') }}</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->ratio_outlet_pjp, 1, ',', '.') }}%</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->omzet_outlet_ach, 1, ',', '.') }}%</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">{{ number_format($row->score_omzet ?: $row->omzet_outlet_ach_score, 0) }},0</td>
                                                <td class="py-3 px-3 text-left font-medium border-r border-slate-200">25%</td>
                                                <td class="py-3 px-3 text-center font-bold text-blue-600 bg-blue-50/40 border-r border-slate-200">{{ number_format($row->final_score_omzet, 2, ',', '.') }}</td>

                                                <!-- Total Final Score & Class -->
                                                <td class="py-3 px-4 text-center font-black text-amber-600 bg-amber-50/80 text-sm border-r border-slate-200">
                                                    {{ number_format($row->final_score ?: $row->total_score, 2, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    @php
                                                        $cls = strtoupper($row->class ?: 'BRONZE');
                                                        $clsClass = match($cls) {
                                                            'PLATINUM' => 'bg-sky-600 text-white shadow-xs',
                                                            'GOLD' => 'bg-amber-500 text-white shadow-xs',
                                                            'SILVER' => 'bg-slate-400 text-white shadow-xs',
                                                            'BRONZE' => 'bg-amber-800 text-white shadow-xs',
                                                            default => 'bg-slate-900 text-white shadow-xs'
                                                        };
                                                    @endphp
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide {{ $clsClass }}">
                                                        {{ $cls }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="41" class="py-10 text-center text-slate-400">
                                                    Belum ada data cluster yang sesuai.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer Pagination -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 text-xs text-slate-500 font-medium">
                                <div>Menampilkan {{ $kelasKpis->firstItem() ?? 1 }}–{{ $kelasKpis->lastItem() ?? 12 }} dari {{ $kelasKpis->total() }} cluster</div>
                                <div>{{ $kelasKpis->links() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== VIEW 3: ANALISIS DATA ==================== -->
                <div id="viewAnalisis" class="{{ ($viewMode ?? 'ringkasan') === 'analisis' ? '' : 'hidden' }}">
                    <div class="space-y-6">

                        <!-- MAIN LINE CHART CONTAINER CARD -->
                        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 sm:p-8 space-y-5">
                            <!-- Card Header & Toolbar Controls -->
                            <div class="space-y-4">
                                <!-- Top Row: Icon + Title & View Switcher -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-lg font-bold shrink-0">
                                            <i class="bi bi-graph-up-arrow"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-base font-extrabold text-slate-900">Analisis Performance KPI Setiap Periode</h2>
                                            <p class="text-xs text-slate-500 font-medium">Grafik Tren SKOR KPI 12 Bulan Regional Bali Nusra</p>
                                        </div>
                                    </div>

                                    <!-- View Mode Switcher -->
                                    <div class="flex items-center gap-2 select-none shrink-0">
                                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'ringkasan'])) }}" 
                                           onclick="switchViewMode(event, 'ringkasan')"
                                           class="tab-btn-ringkasan px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'ringkasan' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                                            <i class="bi bi-grid-fill text-xs"></i>
                                            <span>Ringkasan KPI</span>
                                        </a>
                                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'lengkap'])) }}" 
                                           onclick="switchViewMode(event, 'lengkap')"
                                           class="tab-btn-lengkap px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'lengkap' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] font-extrabold' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                                            <i class="bi bi-calculator-fill text-xs"></i>
                                            <span>Perhitungan Lengkap KPI</span>
                                        </a>
                                        <a href="{{ route('kelas-kpi.index', array_merge(request()->query(), ['view_mode' => 'analisis'])) }}" 
                                           onclick="switchViewMode(event, 'analisis')"
                                           class="tab-btn-analisis px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer {{ ($viewMode ?? 'ringkasan') === 'analisis' ? 'shadow-md bg-[#ED1C24] text-white border border-[#ED1C24]' : 'text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent' }} flex items-center gap-1.5">
                                            <i class="bi bi-graph-up-arrow text-xs"></i>
                                            <span>Analisis Data</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Filter Controls (Periode & Cluster) Aligned to the Right Corner Below View Switcher -->
                                <div class="flex items-center justify-end gap-2.5 sm:gap-3 pt-3 border-t border-slate-100">
                                    <!-- Filter Periode Dropdown -->
                                    <div class="relative min-w-[130px]">
                                        <select id="analisisFilterPeriode" onchange="filterAnalisisChart()" 
                                                class="w-full appearance-none bg-white border border-slate-200/90 rounded-full px-4 py-1.5 pr-8 text-xs font-bold text-slate-700 shadow-xs focus:outline-none focus:border-red-500 cursor-pointer hover:border-slate-300 transition-colors">
                                            <option value="all">Periode</option>
                                            @foreach($analisisPeriodes as $p)
                                                <option value="{{ $p }}">Periode {{ $p }}</option>
                                            @endforeach
                                        </select>
                                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                    </div>

                                    <!-- Filter Cluster Dropdown -->
                                    <div class="relative min-w-[140px]">
                                        <select id="analisisFilterCluster" onchange="filterAnalisisChart()" 
                                                class="w-full appearance-none bg-white border border-slate-200/90 rounded-full px-4 py-1.5 pr-8 text-xs font-bold text-slate-700 shadow-xs focus:outline-none focus:border-red-500 cursor-pointer hover:border-slate-300 transition-colors">
                                            <option value="all">Semua Cluster</option>
                                            @foreach($analisisClusters as $c)
                                                <option value="{{ $c }}">{{ $c }}</option>
                                            @endforeach
                                        </select>
                                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- CHART CANVAS CONTAINER -->
                            <div class="relative w-full h-[460px] pt-2">
                                <canvas id="analisisLineChart"></canvas>
                            </div>
                        </div>

                    </div>
                </div>

            </main>

        </div>
    </div>

    <!-- JAVASCRIPT FOR SIDEBAR, VIEW MODE SWITCHING & PROFILE DROPDOWN -->
    <script>
        let analisisChartInstance = null;
        const analisisMonthsList = @json($analisisMonths);
        const analisisDataMapObj = @json($analisisDataMap);
        const analisisPeriodesList = @json($analisisPeriodes);

        function updateSwitcherButtons(mode) {
            const activeClass = "px-3.5 py-1.5 rounded-full text-xs font-extrabold transition-all cursor-pointer shadow-xs bg-[#ED1C24] text-white flex items-center gap-1.5";
            const inactiveClass = "px-3.5 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-[#ED1C24] flex items-center gap-1.5";

            document.querySelectorAll('.tab-btn-ringkasan').forEach(el => el.className = (mode === 'ringkasan' ? activeClass : inactiveClass) + ' tab-btn-ringkasan');
            document.querySelectorAll('.tab-btn-lengkap').forEach(el => el.className = (mode === 'lengkap' ? activeClass : inactiveClass) + ' tab-btn-lengkap');
            document.querySelectorAll('.tab-btn-analisis').forEach(el => el.className = (mode === 'analisis' ? activeClass : inactiveClass) + ' tab-btn-analisis');
        }

        function switchViewMode(e, mode) {
            const viewRingkasan = document.getElementById('viewRingkasan');
            const viewLengkap = document.getElementById('viewLengkap');
            const viewAnalisis = document.getElementById('viewAnalisis');
            const formViewMode = document.getElementById('formViewMode');
            const titleBlock = document.getElementById('kpiHeaderTitleBlock');
            const headerActions = document.getElementById('kpiHeaderActions');

            if (formViewMode) formViewMode.value = mode;

            if (mode === 'ringkasan') {
                if (viewRingkasan) viewRingkasan.classList.remove('hidden');
                if (viewLengkap) viewLengkap.classList.add('hidden');
                if (viewAnalisis) viewAnalisis.classList.add('hidden');
                if (titleBlock) titleBlock.classList.remove('hidden');
                if (headerActions) headerActions.classList.add('hidden');
            } else if (mode === 'lengkap') {
                if (viewRingkasan) viewRingkasan.classList.add('hidden');
                if (viewLengkap) viewLengkap.classList.remove('hidden');
                if (viewAnalisis) viewAnalisis.classList.add('hidden');
                if (titleBlock) titleBlock.classList.add('hidden');
                if (headerActions) headerActions.classList.remove('hidden');
            } else if (mode === 'analisis') {
                if (viewRingkasan) viewRingkasan.classList.add('hidden');
                if (viewLengkap) viewLengkap.classList.add('hidden');
                if (viewAnalisis) viewAnalisis.classList.remove('hidden');
                if (titleBlock) titleBlock.classList.add('hidden');
                if (headerActions) headerActions.classList.add('hidden');
                setTimeout(initAnalisisChart, 50);
            }

            updateSwitcherButtons(mode);

            if (e && e.preventDefault) {
                e.preventDefault();
                const url = new URL(window.location.href);
                url.searchParams.set('view_mode', mode);
                window.history.pushState({}, '', url);
            }
        }

        function initAnalisisChart() {
            const ctx = document.getElementById('analisisLineChart');
            if (!ctx) return;

            if (analisisChartInstance) {
                analisisChartInstance.resize();
                return;
            }

            const canvasCtx = ctx.getContext('2d');
            const colorPalette = ['#F59E0B', '#10B981', '#ED1C24', '#2563EB', '#8B5CF6', '#06B6D4'];

            const chartDatasets = analisisPeriodesList.map((pVal, idx) => {
                const color = colorPalette[idx % colorPalette.length];
                const dataArr = (analisisDataMapObj['all'] && analisisDataMapObj['all'][pVal]) 
                    ? analisisDataMapObj['all'][pVal] 
                    : [2.0, 2.5, 2.0, 2.1, 2.1, 2.0, 2.0, 2.3, 2.1, 2.1, 1.85, 1.65];

                let bgFill = 'transparent';
                if (idx === 0) {
                    const grad = canvasCtx.createLinearGradient(0, 0, 0, 420);
                    grad.addColorStop(0, 'rgba(245, 158, 11, 0.20)');
                    grad.addColorStop(0.7, 'rgba(245, 158, 11, 0.04)');
                    grad.addColorStop(1, 'rgba(245, 158, 11, 0.00)');
                    bgFill = grad;
                }

                return {
                    label: 'Periode ' + pVal,
                    periode: pVal,
                    data: dataArr,
                    borderColor: color,
                    borderWidth: 3.5,
                    tension: 0.45,
                    fill: idx === 0 ? 'origin' : false,
                    backgroundColor: bgFill,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: color,
                    pointBorderWidth: 2.5,
                    pointRadius: 4.5,
                    pointHoverRadius: 8,
                    pointHoverBorderWidth: 3,
                };
            });

            analisisChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: analisisMonthsList,
                    datasets: chartDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'center',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 20,
                                font: {
                                    family: 'Plus Jakarta Sans',
                                    size: 11,
                                    weight: 'bold'
                                },
                                color: '#334155'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                            padding: 12,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(context) {
                                    let val = context.parsed.y;
                                    let label = context.dataset.label || '';
                                    return `${label}: ${val.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' },
                                color: function(context) {
                                    return context.index === 8 ? '#0284C7' : '#334155';
                                }
                            }
                        },
                        y: {
                            min: 0,
                            max: 3.2,
                            grid: { color: '#F1F5F9', drawBorder: false },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 10, weight: 'extrabold' },
                                color: '#334155',
                                stepSize: 0.5,
                                callback: function(value) {
                                    return value.toFixed(1).replace('.', ',');
                                }
                            },
                            title: {
                                display: true,
                                text: 'TOTAL SKOR KPI',
                                font: { family: 'Plus Jakarta Sans', size: 10, weight: 'extrabold' },
                                color: '#0F172A'
                            }
                        }
                    }
                }
            });
        }

        function filterAnalisisChart() {
            if (!analisisChartInstance) return;

            const selectedPeriode = document.getElementById('analisisFilterPeriode')?.value || 'all';
            const selectedCluster = document.getElementById('analisisFilterCluster')?.value || 'all';

            analisisChartInstance.data.datasets.forEach(ds => {
                const pVal = ds.periode;
                
                if (selectedPeriode === 'all' || selectedPeriode === pVal) {
                    ds.hidden = false;
                } else {
                    ds.hidden = true;
                }

                const clusterMap = analisisDataMapObj[selectedCluster] || analisisDataMapObj['all'];
                if (clusterMap && clusterMap[pVal]) {
                    ds.data = clusterMap[pVal];
                }
            });

            analisisChartInstance.update();
        }

        document.addEventListener('DOMContentLoaded', function() {
            if ("{{ $viewMode ?? 'ringkasan' }}" === 'analisis') {
                setTimeout(initAnalisisChart, 100);
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const icon = document.getElementById('sidebarToggleIcon');
            if (sidebar) {
                const isCollapsed = sidebar.classList.toggle('-ml-60');
                localStorage.setItem('sidebarCollapsed', isCollapsed ? 'true' : 'false');
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
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            if (menu) {
                menu.classList.toggle('hidden');
                if (arrow) {
                    arrow.classList.toggle('rotate-180');
                }
            }
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('userDropdownContainer');
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            if (container && !container.contains(e.target)) {
                if (menu && !menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                    if (arrow) {
                        arrow.classList.remove('rotate-180');
                    }
                }
            }
        });

        // ==================== IMPOR DATA KPI MODAL JS ====================
        function openImportKpiModal() {
            const modal = document.getElementById('importKpiModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeImportKpiModal() {
            const modal = document.getElementById('importKpiModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function handleFileSelect(input) {
            const fileInfo = document.getElementById('selectedFileInfo');
            const fileNameSpan = document.getElementById('selectedFileName');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (fileNameSpan) fileNameSpan.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                if (fileInfo) fileInfo.classList.remove('hidden');
            } else {
                if (fileInfo) fileInfo.classList.add('hidden');
            }
        }
    </script>

    <!-- MODAL IMPOR DATA KELAS KPI (CSV / EXCEL) -->
    <div id="importKpiModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-100 transform transition-all duration-200">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-6 py-5 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-500/20 border border-red-500/30 text-red-400 flex items-center justify-center text-lg">
                        <i class="bi bi-file-earmark-arrow-up-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white"> </h3>
                        <p class="text-xs text-slate-300"> </p>
                    </div>
                </div>
                <button type="button" onclick="closeImportKpiModal()" class="text-slate-400 hover:text-white text-xl font-bold p-1 cursor-pointer">&times;</button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('kelas-kpi.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <!-- Download Template Banner -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-file-earmark-code text-red-500 text-lg"></i>
                        <div>
                            <p class="text-xs font-bold text-slate-800"> </p>
                            <p class="text-[11px] text-slate-500">Cukup input data sesuai CSV </p>
                            <p class="text-[11px] text-slate-500">Data lainnya terhitung dengan otomatis oleh sistem </p>
                        </div>
                    </div>
                    <a href="{{ route('kelas-kpi.template') }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-1.5 shadow-xs transition-all shrink-0">
                        <i class="bi bi-download"></i>
                        <span>Template CSV</span>
                    </a>
                </div>

                <!-- File Upload Box -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Pilih Berkas CSV / Excel</label>
                    <div class="relative border-2 border-dashed border-slate-200 hover:border-red-400 rounded-2xl p-6 text-center transition-all bg-slate-50/50 hover:bg-red-50/20 group cursor-pointer" onclick="document.getElementById('kpiFileInput').click()">
                        <input type="file" name="file" id="kpiFileInput" accept=".csv,.xlsx,.xls,.txt" onchange="handleFileSelect(this)" class="hidden" required>
                        
                        <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-red-500 flex items-center justify-center text-2xl mx-auto shadow-xs group-hover:scale-105 transition-transform">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-800 mt-3">Klik untuk mengunggah atau drag & drop</p>
                        <p class="text-[11px] text-slate-400 mt-1">Berkas CSV, XLSX, XLS hingga 10MB</p>

                        <!-- Selected File Info -->
                        <div id="selectedFileInfo" class="hidden mt-3 pt-3 border-t border-slate-200/60 text-xs font-bold text-emerald-600 flex items-center justify-center gap-1.5">
                            <i class="bi bi-check-circle-fill"></i>
                            <span id="selectedFileName">filename.csv</span>
                        </div>
                    </div>
                </div>

                <!-- Import Mode Selection -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Mode Impor Data</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="border border-slate-200 rounded-2xl p-3.5 flex items-start gap-2.5 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30">
                            <input type="radio" name="mode" value="append" checked class="mt-0.5 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="block text-xs font-bold text-slate-900">Gabung / Simpan Riwayat</span>
                                <span class="block text-[10px] text-slate-500 mt-0.5">Tambah data/periode baru & simpan data lama </span>
                            </div>
                        </label>
                        <label class="border border-slate-200 rounded-2xl p-3.5 flex items-start gap-2.5 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30">
                            <input type="radio" name="mode" value="replace" class="mt-0.5 text-red-600 focus:ring-red-500">
                            <div>
                                <span class="block text-xs font-bold text-slate-900">Ganti Seluruh Data</span>
                                <span class="block text-[10px] text-slate-500 mt-0.5">Hapus seluruh isi database & ganti dengan data baru</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeImportKpiModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md shadow-red-500/20 transition-all flex items-center gap-2 cursor-pointer">
                        <i class="bi bi-upload"></i>
                        <span>Mulai Impor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
