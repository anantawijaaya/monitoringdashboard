<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Program Direct Sales - Monitoring Budget BK</title>

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

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
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
        
        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#F8FAFC]">
            
            <main class="flex-1 overflow-y-auto p-6 sm:p-8 space-y-6">
                
                <!-- Page Title Header Card -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="p-3 bg-red-50 text-telkomsel-red rounded-2xl border border-red-100">
                            <i class="bi bi-bag-check-fill text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Program Direct Sales</h1>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Monitoring Budget BK &bull; Realisasi Anggaran Direct Sales</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active Period 2026
                        </span>
                    </div>
                </div>

                <!-- Overview Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="glass-card p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between text-slate-500 mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider">Total Alokasi Budget</span>
                            <i class="bi bi-wallet2 text-telkomsel-red text-lg"></i>
                        </div>
                        <div class="text-2xl font-black text-slate-900">Rp 0</div>
                        <p class="text-xs text-slate-500 mt-1">Program Direct Sales</p>
                    </div>

                    <div class="glass-card p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between text-slate-500 mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider">Total Realisasi</span>
                            <i class="bi bi-arrow-up-right-circle text-emerald-600 text-lg"></i>
                        </div>
                        <div class="text-2xl font-black text-slate-900">Rp 0</div>
                        <p class="text-xs text-emerald-600 font-medium mt-1">0% dari Alokasi</p>
                    </div>

                    <div class="glass-card p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between text-slate-500 mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider">Sisa Budget</span>
                            <i class="bi bi-piggy-bank text-amber-500 text-lg"></i>
                        </div>
                        <div class="text-2xl font-black text-slate-900">Rp 0</div>
                        <p class="text-xs text-slate-500 mt-1">Siap Dialokasikan</p>
                    </div>
                </div>

                <!-- Main Section Placeholder -->
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-sm">
                    <div class="max-w-md mx-auto space-y-4">
                        <div class="w-16 h-16 bg-red-50 text-telkomsel-red rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-inner">
                            <i class="bi bi-bag-check"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">Modul Program Direct Sales</h2>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Direktori untuk sub menu <strong>Program Direct Sales</strong> telah berhasil disiapkan pada: <br>
                            <code class="text-xs bg-slate-100 text-telkomsel-red px-2 py-1 rounded font-mono border border-slate-200">resources/views/budget-bk/direct-sales/index.blade.php</code>
                        </p>
                        <div class="pt-2">
                            <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white telkomsel-gradient shadow-md hover:opacity-95 transition-all">
                                <i class="bi bi-plus-lg"></i>
                                Tambah Program Direct Sales
                            </button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- User Profile Dropdown Script -->
    <script>
        function toggleUserDropdown(event) {
            event.stopPropagation();
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
            if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                if (arrow) {
                    arrow.classList.remove('rotate-180');
                }
            }
        });
    </script>
</body>
</html>
