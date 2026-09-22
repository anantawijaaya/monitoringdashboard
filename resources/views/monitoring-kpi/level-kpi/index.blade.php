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

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#0B0F19]">
            
            <!-- SUB HEADER BAR -->
            <header class="h-20 px-8 bg-[#0F172A]/80 border-b border-slate-800/80 flex items-center justify-between shrink-0 backdrop-blur-md z-20">
                <div>
                    <h2 class="text-xl font-outfit font-black text-white tracking-tight flex items-center gap-2.5">
                        <i class="bi bi-speedometer2 text-red-500"></i>
                        <span>MONITORING LEVEL KPI</span>
                    </h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Database Capaian Key Performance Indicators Cluster Regional Bali Nusa Tenggara</p>
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
                @include('monitoring-kpi.level-kpi.partials.summary-cards')

                <!-- 2. FILTER & ACTION BAR -->
                @include('monitoring-kpi.level-kpi.partials.filter-bar')

                <!-- 3. MAIN DATA TABLE -->
                @include('monitoring-kpi.level-kpi.partials.data-table')

            </main>

            <!-- FOOTER -->
            <footer class="h-10 bg-[#0F172A] border-t border-slate-800 px-6 flex items-center justify-between text-[11px] text-slate-500 shrink-0">
                <span>&copy;Telkomsel Bali Nusra 2026.</span>
                <span>Key Performance Indicator Management System</span>
            </footer>

        </div>
    </div>

    <!-- MODALS -->
    @include('monitoring-kpi.level-kpi.partials.modals')

    <!-- JAVASCRIPT INTERACTIONS -->
    @include('monitoring-kpi.level-kpi.partials.scripts')

</body>
</html>
