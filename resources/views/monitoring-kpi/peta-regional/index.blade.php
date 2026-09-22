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

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1 shadow-sm">
                    <div class="flex items-center justify-between font-bold text-sm text-red-900 mb-1">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-red-600 text-lg"></i>
                            <span>Terjadi kesalahan validasi pengunggahan:</span>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 cursor-pointer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <ul class="list-disc list-inside space-y-1 pl-6">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. PAGE HEADER -->
            <div>
                <h2 class="text-xl font-black text-gray-900 tracking-tight"></h2>
                <p class="text-xs text-gray-500 font-medium mt-0.5"></p>
            </div>

            <!-- 2. SUMMARY METRIC CARDS -->
            @include('monitoring-kpi.peta-regional.partials.summary-cards')

            <!-- 3. FILTER PANEL -->
            @include('monitoring-kpi.peta-regional.partials.filter-panel')

            <!-- 4. MAP VIEWPORT CANVAS CONTAINER -->
            @include('monitoring-kpi.peta-regional.partials.map-container')

            <!-- 5. DATA OUTLET TABLE & MANAGEMENT TOOLBAR -->
            @include('monitoring-kpi.peta-regional.partials.data-table')

        </main>
    </div>

    <!-- BOTTOM FOOTER BAR -->
    <footer id="dashboardFooter" class="fixed bottom-0 left-0 right-0 z-40 h-8 bg-[#121212] text-gray-400 px-6 flex items-center justify-between text-[11px] font-medium border-t border-gray-800 select-none transition-all duration-300 transform translate-y-full opacity-0 pointer-events-none">
        <span>© {{ date('Y') }} Telkomsel. All Rights Reserved.</span>
        <span class="hidden sm:inline">Monitoring Dashboard Regional Bali Nusra</span>
    </footer>

    <!-- MODALS -->
    @include('monitoring-kpi.peta-regional.partials.modals')

    <!-- JAVASCRIPT ENGINE -->
    @include('monitoring-kpi.peta-regional.partials.scripts')

</body>
</html>
