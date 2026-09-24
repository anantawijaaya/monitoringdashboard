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
    <header class="bg-[#121212] border-b border-gray-800/80 text-white h-16 px-4 sm:px-6 flex items-center justify-between shrink-0 relative z-[100] select-none">
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
                    <div class="py-2 px-1 text-emerald-700 flex items-center justify-between animate-fade-in">
                        <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 cursor-pointer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="py-2 px-1 text-red-700 flex items-center justify-between animate-fade-in">
                        <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 cursor-pointer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                <!-- HEADER & ACTION BAR -->
                @include('home.partials.filter-bar')

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

                <!-- 5 MAIN REVENUE KPI CARDS -->
                @include('home.partials.kpi-cards')

                <!-- TOP 3 RANKING ATAS & BAWAH -->
                @include('home.partials.ranking-cards')

                <!-- GRAFIK PERBANDINGAN PENDAPATAN CLUSTER -->
                @include('home.partials.growth-chart')

                <!-- SUMMARY PENYERAPAN BUDGET MARKETING PER CLUSTER -->
                @include('home.partials.realization-summary')

            </div>
        </main>

    </div>

    <!-- BOTTOM FOOTER BAR -->
    <footer id="dashboardFooter" class="fixed bottom-0 left-0 right-0 z-40 h-8 bg-[#121212] text-gray-400 px-6 flex items-center justify-between text-[11px] font-medium border-t border-gray-800 select-none transition-all duration-300 transform translate-y-full opacity-0 pointer-events-none">
        <span>© {{ date('Y') }} Telkomsel. All Rights Reserved.</span>
        <span class="hidden sm:inline">Monitoring Dashboard Regional Bali Nusra</span>
    </footer>

    <!-- MODALS SECTION -->
    @include('home.partials.modals')

    <!-- SCRIPTS SECTION -->
    @include('home.partials.scripts')

</body>
</html>
