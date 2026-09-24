<!-- LEFT SIDEBAR NAVIGATION (Dark #121212) -->
<style>
    /* Sleek Custom Dark Scrollbar for Sidebar */
    #mainSidebar .sidebar-scroll-container::-webkit-scrollbar {
        width: 4px;
    }
    #mainSidebar .sidebar-scroll-container::-webkit-scrollbar-track {
        background: transparent;
    }
    #mainSidebar .sidebar-scroll-container::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 9999px;
    }
    #mainSidebar .sidebar-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }
    #mainSidebar .sidebar-scroll-container {
        scrollbar-width: thin;
        scrollbar-color: #334155 transparent;
    }
</style>

<aside id="mainSidebar" class="w-60 bg-[#121212] text-gray-300 flex flex-col justify-between shrink-0 relative z-20 select-none border-r border-gray-800/80 transition-all duration-300 ease-in-out">
    
    <!-- Floating Red Circle Sidebar Toggle Button (Arrow) -->
    <button id="sidebarToggleBtn" 
            onclick="toggleSidebar()" 
            class="absolute -right-3.5 top-5 z-30 w-7 h-7 bg-[#ED1C24] hover:bg-[#C8102E] text-white rounded-full flex items-center justify-center shadow-lg border-2 border-white/20 hover:scale-110 transition-all cursor-pointer"
            title="Sembunyikan Sidebar">
        <i id="sidebarToggleIcon" class="bi bi-chevron-left text-xs transition-transform duration-300"></i>
    </button>
    
    <!-- Navigation Items Container -->
    <div class="sidebar-scroll-container py-4 px-3 space-y-3 relative z-10 overflow-y-auto flex-1">
        
        <!-- Home Menu -->
        <a href="{{ route('dashboard') }}" 
           class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10 font-semibold' }}">
            <i class="bi bi-house-door text-base {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
            <span>Home</span>
        </a>

        <!-- MAIN MENU 1: MONITORING KPI SBP (RED HEADER) -->
        <div class="pt-2 space-y-2">
            <button type="button" 
                    onclick="toggleKpiSbpMenu()" 
                    class="w-full px-1 py-1 flex items-center justify-between text-[11px] sm:text-xs font-black text-[#ED1C24] uppercase tracking-wider hover:text-red-400 transition-all cursor-pointer group">
                <span class="drop-shadow-[0_1px_2px_rgba(237,28,36,0.3)]">MONITORING KPI SBP</span>
                <i id="kpiSbpArrow" class="bi bi-chevron-right text-xs text-gray-400 transition-transform duration-300 group-hover:text-red-400 {{ request()->routeIs(['regional-map.*', 'revenue.*', 'hierarchy.*', 'ranking.*', 'kelas-kpi.*']) ? 'rotate-90' : '' }}"></i>
            </button>

            <!-- Submenus for Monitoring KPI SBP -->
            <div id="kpiSbpSubmenu" class="space-y-1 pt-1 pl-2 transition-all duration-300 {{ request()->routeIs(['regional-map.*', 'revenue.*', 'hierarchy.*', 'ranking.*', 'kelas-kpi.*']) ? '' : 'hidden' }}">
                <!-- Submenu 1: Peta Regional -->
                <a href="{{ route('regional-map.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('regional-map.*') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10 font-semibold' }}">
                    <i class="bi bi-geo-alt text-base {{ request()->routeIs('regional-map.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                    <span>Peta Regional</span>
                </a>

                <!-- Submenu 2: Pendapatan -->
                <a href="{{ route('revenue.manage') }}" 
                   class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('revenue.*') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10 font-semibold' }}">
                    <i class="bi bi-cash-stack text-base {{ request()->routeIs('revenue.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                    <span>Revenue</span>
                </a>

                <!-- Submenu 3: Hirarki -->
                <a href="{{ route('hierarchy.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('hierarchy.*') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10 font-semibold' }}">
                    <i class="bi bi-diagram-3 text-base {{ request()->routeIs('hierarchy.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                    <span>Hirarki</span>
                </a>

                <!-- Submenu 4: Peringkat -->
                <a href="{{ route('ranking.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('ranking.*') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10 font-semibold' }}">
                    <i class="bi bi-trophy text-base {{ request()->routeIs('ranking.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                    <span>Peringkat</span>
                </a>

                <!-- Submenu 5: Kelas KPI -->
                <a href="{{ route('kelas-kpi.index') }}"
                   class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('kelas-kpi.*') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10 font-semibold' }}">
                    <i class="bi bi-award text-base {{ request()->routeIs('kelas-kpi.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                    <span>Kelas KPI</span>
                </a>
            </div>
        </div>

        <!-- MAIN MENU 2: MONITORING BUDGET BK (RED HEADER) -->
        <div class="pt-2 space-y-2">
            <button type="button" 
                    onclick="toggleBudgetBkMenu()" 
                    class="w-full px-1 py-1 flex items-center justify-between text-[11px] sm:text-xs font-black text-[#ED1C24] uppercase tracking-wider hover:text-red-400 transition-all cursor-pointer group">
                <span class="drop-shadow-[0_1px_2px_rgba(237,28,36,0.3)]">MONITORING BUDGET BK</span>
                <i id="budgetBkArrow" class="bi bi-chevron-right text-xs text-gray-400 transition-transform duration-300 group-hover:text-red-400 {{ request()->routeIs('budget-bk.*') ? 'rotate-90' : '' }}"></i>
            </button>

            <!-- Submenus for Monitoring Budget BK -->
            <div id="budgetBkSubmenu" class="space-y-1 pt-1 pl-2 transition-all duration-300 {{ request()->routeIs('budget-bk.*') ? '' : 'hidden' }}">
                <!-- Submenu 1: Program Indirect Channel -->
                <a href="{{ route('budget-bk.indirect-channel.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('budget-bk.indirect-channel.*') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10 font-semibold' }}">
                    <i class="bi bi-diagram-2 text-base {{ request()->routeIs('budget-bk.indirect-channel.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                    <span>Program Indirect Channel</span>
                </a>

                <!-- Submenu 2: Program Direct Sales -->
                <a href="{{ route('budget-bk.direct-sales.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('budget-bk.direct-sales.*') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10 font-semibold' }}">
                    <i class="bi bi-bag-check text-base {{ request()->routeIs('budget-bk.direct-sales.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                    <span>Program Direct Sales</span>
                </a>

                <!-- Submenu 3: Culture Program -->
                <a href="{{ route('budget-bk.culture-program.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('budget-bk.culture-program.*') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-300 hover:text-white hover:bg-white/10 font-semibold' }}">
                    <i class="bi bi-people text-base {{ request()->routeIs('budget-bk.culture-program.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                    <span>Culture Program</span>
                </a>
            </div>
        </div>

        @if(Auth::user() && Auth::user()->isAdmin())
            <!-- SYSTEM MAINTENANCE & PROTECTION MENU -->
            <div class="pt-2 space-y-1 border-t border-gray-800/60">
                <a href="{{ route('admin.backups.index') }}" 
                   class="sidebar-item flex items-center gap-3 px-3 py-2 text-xs sm:text-sm rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.backups.*') ? 'active bg-[#ED1C24] text-white font-extrabold shadow-lg shadow-red-600/30' : 'text-gray-400 hover:text-white hover:bg-white/10 font-bold' }}">
                    <i class="bi bi-shield-check text-base {{ request()->routeIs('admin.backups.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors"></i>
                    <span>Backup & Restore Data</span>
                </a>
            </div>
        @endif

        <!-- Logout Link Form -->
        <div class="pt-4 border-t border-gray-800/80">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="sidebar-item w-full flex items-center gap-3 px-3 py-2 text-xs sm:text-sm font-bold text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200 text-left rounded-xl group cursor-pointer">
                    <i class="bi bi-box-arrow-right text-base text-gray-400 group-hover:text-white transition-colors"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>

    </div>
</aside>

<!-- Shared Sidebar Script -->
<script>
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
            localStorage.setItem('sidebarCollapsed', 'false');

            if (toggleIcon) {
                toggleIcon.classList.remove('rotate-180');
            }
            if (toggleBtn) {
                toggleBtn.title = 'Sembunyikan Sidebar';
            }
            if (headerIcon) {
                headerIcon.className = 'bi bi-layout-sidebar-inset text-lg';
            }
        } else {
            sidebar.classList.add('-ml-60');
            document.documentElement.classList.add('sidebar-is-collapsed');
            localStorage.setItem('sidebarCollapsed', 'true');

            if (toggleIcon) {
                toggleIcon.classList.add('rotate-180');
            }
            if (toggleBtn) {
                toggleBtn.title = 'Tampilkan Sidebar';
            }
            if (headerIcon) {
                headerIcon.className = 'bi bi-layout-sidebar text-lg';
            }
        }

        window.dispatchEvent(new Event('resize'));
        setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
            if (typeof map !== 'undefined' && map && typeof map.invalidateSize === 'function') {
                map.invalidateSize();
            }
            if (typeof growthChart !== 'undefined' && growthChart && typeof growthChart.resize === 'function') {
                growthChart.resize();
            }
        }, 320);
    }
    window.toggleSidebar = toggleSidebar;

    if (typeof window.toggleKpiSbpMenu !== 'function') {
        window.toggleKpiSbpMenu = function() {
            const submenu = document.getElementById('kpiSbpSubmenu');
            const arrow = document.getElementById('kpiSbpArrow');
            if (submenu) {
                const isHidden = submenu.classList.toggle('hidden');
                if (arrow) {
                    if (isHidden) {
                        arrow.classList.remove('rotate-90');
                        arrow.style.transform = '';
                    } else {
                        arrow.classList.add('rotate-90');
                        arrow.style.transform = '';
                    }
                }
            }
        };
    }

    if (typeof window.toggleBudgetBkMenu !== 'function') {
        window.toggleBudgetBkMenu = function() {
            const submenu = document.getElementById('budgetBkSubmenu');
            const arrow = document.getElementById('budgetBkArrow');
            if (submenu) {
                const isHidden = submenu.classList.toggle('hidden');
                if (arrow) {
                    if (isHidden) {
                        arrow.classList.remove('rotate-90');
                        arrow.style.transform = '';
                    } else {
                        arrow.classList.add('rotate-90');
                        arrow.style.transform = '';
                    }
                }
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        const sidebar = document.getElementById('mainSidebar');
        const icon = document.getElementById('sidebarToggleIcon');
        const btn = document.getElementById('sidebarToggleBtn');
        const headerIcon = document.getElementById('headerSidebarToggleIcon');

        if (isCollapsed) {
            if (sidebar) sidebar.classList.add('-ml-60');
            document.documentElement.classList.add('sidebar-is-collapsed');
            if (icon) icon.classList.add('rotate-180');
            if (btn) btn.title = 'Tampilkan Sidebar';
            if (headerIcon) headerIcon.className = 'bi bi-layout-sidebar text-lg';
        } else {
            if (sidebar) sidebar.classList.remove('-ml-60');
            document.documentElement.classList.remove('sidebar-is-collapsed');
            if (icon) icon.classList.remove('rotate-180');
            if (btn) btn.title = 'Sembunyikan Sidebar';
            if (headerIcon) headerIcon.className = 'bi bi-layout-sidebar-inset text-lg';
        }
    });
</script>
