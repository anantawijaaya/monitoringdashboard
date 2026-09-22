<!-- JAVASCRIPT INTERACTIONS -->
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

    // Alur Logika Sistem: Footer HANYA muncul saat scrolling menyentuh bagian paling bawah (Fixed Position, Bebas Crash)
    document.addEventListener('DOMContentLoaded', () => {
        const mainCanvas = document.querySelector('main');
        const dashboardFooter = document.getElementById('dashboardFooter');

        if (mainCanvas && dashboardFooter) {
            const checkFooterVisibility = () => {
                const isAtBottom = (mainCanvas.scrollTop + mainCanvas.clientHeight) >= (mainCanvas.scrollHeight - 25);
                if (isAtBottom) {
                    dashboardFooter.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
                    dashboardFooter.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                } else {
                    dashboardFooter.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                    dashboardFooter.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
                }
            };

            checkFooterVisibility();
            mainCanvas.addEventListener('scroll', checkFooterVisibility, { passive: true });
            window.addEventListener('resize', checkFooterVisibility, { passive: true });
        }
    });

    // Custom Dropdown Toggle Logic for Ranking Period
    function submitRankingPeriod(val) {
        document.getElementById('rankingPeriodInput').value = val;
        document.getElementById('filterPeriodeForm').submit();
    }

    function toggleRankingPeriodMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('rankingPeriodMenu');
        const arrow = document.getElementById('rankingPeriodArrow');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('rankingPeriodDropdownContainer');
        const menu = document.getElementById('rankingPeriodMenu');
        const arrow = document.getElementById('rankingPeriodArrow');
        if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });
</script>
