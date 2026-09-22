<!-- JAVASCRIPT FOR MODALS & UI INTERACTION -->
<script>
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
        const m = document.getElementById('userDropdownMenu');
        m.classList.toggle('hidden');
    }

    document.addEventListener('click', function() {
        const m = document.getElementById('userDropdownMenu');
        if (m && !m.classList.contains('hidden')) {
            m.classList.add('hidden');
        }
    });

    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function openEditModal(row) {
        document.getElementById('editForm').action = "/level-kpi/" + row.id;
        document.getElementById('edit_cluster_name').value = row.cluster_name;
        document.getElementById('edit_period_month').value = row.period_month;
        document.getElementById('edit_period_year').value = row.period_year;
        document.getElementById('edit_target_rev_all').value = row.target_rev_all;
        document.getElementById('edit_mtd_rev_all').value = row.mtd_rev_all;
        document.getElementById('edit_target_rev_bb').value = row.target_rev_bb;
        document.getElementById('edit_mtd_rev_bb').value = row.mtd_rev_bb;
        document.getElementById('edit_target_pv').value = row.target_pv;
        document.getElementById('edit_mtd_pv').value = row.mtd_pv;
        document.getElementById('edit_target_rgb').value = row.target_rgb;
        document.getElementById('edit_mtd_rgb').value = row.mtd_rgb;
        document.getElementById('edit_target_growth_rev').value = row.target_growth_rev;
        document.getElementById('edit_mtd_growth_rev').value = row.mtd_growth_rev;
        document.getElementById('edit_ratio_pjp').value = row.ratio_pjp;
        document.getElementById('edit_notes').value = row.notes ?? '';

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Custom Dropdown Toggle Logic for Level KPI Filters
    function submitLevelFilter(type, val) {
        if (type === 'cluster') document.getElementById('levelClusterInput').value = val;
        if (type === 'period') document.getElementById('levelPeriodInput').value = val;
        if (type === 'status') document.getElementById('levelStatusInput').value = val;
        if (type === 'sort') document.getElementById('levelSortInput').value = val;
        document.getElementById('levelKpiFilterForm').submit();
    }

    function toggleLevelClusterMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('levelClusterMenu');
        const arrow = document.getElementById('levelClusterArrow');
        closeAllLevelMenusExcept('levelClusterMenu');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleLevelPeriodMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('levelPeriodMenu');
        const arrow = document.getElementById('levelPeriodArrow');
        closeAllLevelMenusExcept('levelPeriodMenu');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleLevelStatusMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('levelStatusMenu');
        const arrow = document.getElementById('levelStatusArrow');
        closeAllLevelMenusExcept('levelStatusMenu');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleLevelSortMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('levelSortMenu');
        const arrow = document.getElementById('levelSortArrow');
        closeAllLevelMenusExcept('levelSortMenu');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function closeAllLevelMenusExcept(exceptId) {
        ['levelClusterMenu', 'levelPeriodMenu', 'levelStatusMenu', 'levelSortMenu'].forEach(id => {
            if (id !== exceptId) {
                const m = document.getElementById(id);
                if (m) m.classList.add('hidden');
            }
        });
        ['levelClusterArrow', 'levelPeriodArrow', 'levelStatusArrow', 'levelSortArrow'].forEach(id => {
            const arrId = id;
            const menuId = id.replace('Arrow', 'Menu');
            if (menuId !== exceptId) {
                const a = document.getElementById(arrId);
                if (a) a.classList.remove('rotate-180');
            }
        });
    }

    document.addEventListener('click', function(e) {
        const ids = [
            { container: 'levelClusterDropdownContainer', menu: 'levelClusterMenu', arrow: 'levelClusterArrow' },
            { container: 'levelPeriodDropdownContainer', menu: 'levelPeriodMenu', arrow: 'levelPeriodArrow' },
            { container: 'levelStatusDropdownContainer', menu: 'levelStatusMenu', arrow: 'levelStatusArrow' },
            { container: 'levelSortDropdownContainer', menu: 'levelSortMenu', arrow: 'levelSortArrow' },
        ];

        ids.forEach(item => {
            const c = document.getElementById(item.container);
            const m = document.getElementById(item.menu);
            const a = document.getElementById(item.arrow);
            if (c && !c.contains(e.target) && m && !m.classList.contains('hidden')) {
                m.classList.add('hidden');
                if (a) a.classList.remove('rotate-180');
            }
        });
    });
</script>
