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
    <header class="bg-[#121212] border-b border-gray-800/80 text-white h-16 px-4 sm:px-6 flex items-center justify-between shrink-0 relative z-[100] select-none">
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

                <!-- HEADER TITLE & ACTIONS -->
                @include('monitoring-kpi.ranking.partials.header')

                <!-- LEADERBOARD TABLE & VIEW SWITCHER -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 space-y-5 w-full">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-base font-bold shrink-0">
                                <i id="rankingHeaderIcon" class="bi {{ ($viewMode ?? 'ranking') === 'lengkap' ? 'bi-calculator-fill' : (($viewMode ?? 'ranking') === 'ringkasan' ? 'bi-grid-fill' : 'bi-trophy-fill') }}"></i>
                            </div>
                            <div>
                                <h2 class="text-[15px] font-black text-slate-900" id="rankingHeaderTitle">
                                    {{ ($viewMode ?? 'ranking') === 'lengkap' ? 'Perhitungan Lengkap KPI Setiap City' : (($viewMode ?? 'ranking') === 'ringkasan' ? 'Ringkasan Performance KPI Setiap City' : 'City Ranking Berdasarkan Branch') }}
                                </h2>
                            </div>
                        </div>

                        <!-- Filter Periode & View Mode Switcher Inline -->
                        <div class="flex flex-wrap items-center gap-3 select-none">
                            <!-- Custom Filter Periode Bulan Dropdown -->
                            <form method="GET" action="{{ route('ranking.index') }}" class="flex items-center" id="filterPeriodeForm">
                                <input type="hidden" name="view_mode" value="{{ $viewMode ?? 'ranking' }}">
                                <input type="hidden" name="period" id="rankingPeriodInput" value="{{ $selectedPeriod ?? 'all' }}">
                                <div class="relative z-30" id="rankingPeriodDropdownContainer">
                                    <button type="button" 
                                            onclick="toggleRankingPeriodMenu(event)"
                                            class="flex items-center justify-between gap-2.5 bg-white border border-slate-200/80 hover:bg-slate-50 rounded-full px-4 py-2 text-xs font-bold text-slate-700 shadow-xs focus:outline-none cursor-pointer min-w-[150px]">
                                        <span>{{ ($selectedPeriod ?? 'all') === 'all' ? 'Semua Periode' : 'Periode ' . $selectedPeriod }}</span>
                                        <i id="rankingPeriodArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                                    </button>

                                    <div id="rankingPeriodMenu" 
                                         class="hidden absolute top-full left-0 mt-1.5 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                                        <a href="javascript:void(0)" onclick="submitRankingPeriod('all')"
                                           class="block px-4 py-2 text-xs font-bold {{ ($selectedPeriod ?? 'all') === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }} transition-colors">
                                            Semua Periode
                                        </a>
                                        @foreach($availablePeriods as $p)
                                            <a href="javascript:void(0)" onclick="submitRankingPeriod('{{ $p }}')"
                                               class="block px-4 py-2 text-xs font-semibold {{ ($selectedPeriod ?? 'all') == $p ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }} transition-colors">
                                                Periode {{ $p }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </form>

                            <!-- View Mode Switcher -->
                            <div class="flex flex-wrap items-center gap-2 select-none">
                                <button type="button" id="tabBtnRanking" 
                                        onclick="switchViewMode(event, 'ranking')"
                                        class="{{ ($viewMode ?? 'ranking') === 'ranking' ? 'px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] flex items-center gap-1.5' : 'px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent flex items-center gap-1.5' }}">
                                    <i class="bi bi-trophy-fill text-xs"></i>
                                    <span>Ranking City</span>
                                </button>

                                <button type="button" id="tabBtnLengkap" 
                                        onclick="switchViewMode(event, 'lengkap')"
                                        class="{{ ($viewMode ?? 'ranking') === 'lengkap' ? 'px-5 py-2 rounded-full text-xs font-extrabold transition-all cursor-pointer shadow-md bg-[#ED1C24] text-white border border-[#ED1C24] flex items-center gap-1.5' : 'px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-[#ED1C24] hover:bg-red-50/50 border border-transparent flex items-center gap-1.5' }}">
                                    <i class="bi bi-calculator-fill text-xs"></i>
                                    <span>Perhitungan Lengkap KPI</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- VIEW 1: RANKING TOP 3 ATAS & BAWAH PER BRANCH -->
                    @include('monitoring-kpi.ranking.partials.branch-ranking')

                    <!-- VIEW 2: KALKULASI LENGKAP MATRIX TABLE -->
                    @include('monitoring-kpi.ranking.partials.full-matrix')

                </div>

            </div>
        </main>
    </div>

    <!-- BOTTOM FOOTER BAR -->
    <footer id="dashboardFooter" class="fixed bottom-0 left-0 right-0 z-40 h-8 bg-[#121212] text-gray-400 px-6 flex items-center justify-between text-[11px] font-medium border-t border-gray-800 select-none transition-all duration-300 transform translate-y-full opacity-0 pointer-events-none">
        <span>© {{ date('Y') }} Telkomsel. All Rights Reserved.</span>
        <span class="hidden sm:inline">Monitoring Dashboard Regional Bali Nusra</span>
    </footer>

    <!-- MODALS -->
    @include('monitoring-kpi.ranking.partials.modals')

    <!-- JAVASCRIPT INTERACTIONS -->
    @include('monitoring-kpi.ranking.partials.scripts')

</body>
</html>
