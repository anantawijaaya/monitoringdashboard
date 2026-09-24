<!-- JAVASCRIPT INTERACTIONS -->
<script>
    // Sidebar Toggle for Mobile / Desktop
    const sidebar = document.getElementById('mainSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('hidden');
        });
    }

    // Toggle Filter Box
    function toggleFilterBox() {
        const panel = document.getElementById('filterPanel');
        if (panel) {
            panel.classList.toggle('hidden');
        }
    }

    // User Profile Dropdown Toggle
    const userBtn = document.getElementById('userDropdownBtn');
    const userMenu = document.getElementById('userDropdownMenu');
    const arrow = document.getElementById('dropdownArrow');

    if (userBtn && userMenu) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
            arrow.style.transform = userMenu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    }

    document.addEventListener('click', (e) => {
        const container = document.getElementById('userDropdownContainer');
        const menu = document.getElementById('userDropdownMenu');
        const arrow = document.getElementById('dropdownArrow');
        if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    });

    // ==================== EDIT HIERARCHY MODAL FUNCTIONS ====================
    const editModal = document.getElementById('editHierarchyModal');

    function openEditHierarchyModal(data) {
        if (!data) return;

        document.getElementById('editOutletId').value = data.id || '';
        document.getElementById('editKabupaten').value = data.kabupaten || '';
        document.getElementById('editCluster').value = data.cluster || '';
        document.getElementById('editMitra').value = data.mitra || '';
        document.getElementById('editBranch').value = data.branch || '';
        document.getElementById('editJumlahOutlet').value = data.jumlah_outlet || '';
        document.getElementById('editManagerBranch').value = data.manager_branch || '';

        if (editModal) {
            editModal.classList.remove('hidden');
        }
    }

    function closeEditHierarchyModal() {
        if (editModal) {
            editModal.classList.add('hidden');
        }
    }

    if (editModal) {
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) {
                closeEditHierarchyModal();
            }
        });
    }

    // Submit Edit Form via AJAX
    async function submitEditHierarchy(event) {
        event.preventDefault();
        const form = document.getElementById('editHierarchyForm');
        const saveBtn = document.getElementById('saveEditBtn');
        const outletId = document.getElementById('editOutletId').value;

        if (!outletId) return;

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-sm"></i> <span>Menyimpan...</span>';

        const formData = new FormData(form);
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch(`/hierarchy/${outletId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                closeEditHierarchyModal();
                showToast(result.message || 'Data berhasil diperbarui!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(result.message || 'Gagal memperbarui data', 'error');
            }
        } catch (err) {
            showToast('Terjadi kesalahan koneksi server.', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check-circle-fill text-sm"></i> <span>Simpan Perubahan</span>';
        }
    }

    // ==================== IMPORT MODAL FUNCTIONS ====================
    const importModal = document.getElementById('importHierarchyModal');

    function openImportHierarchyModal() {
        if (importModal) {
            importModal.classList.remove('hidden');
        }
    }

    function closeImportHierarchyModal() {
        if (importModal) {
            importModal.classList.add('hidden');
        }
    }

    if (importModal) {
        importModal.addEventListener('click', (e) => {
            if (e.target === importModal) {
                closeImportHierarchyModal();
            }
        });
    }

    function updateFilePreview(input) {
        const fileNameSpan = document.getElementById('importFileName');
        if (input.files && input.files[0]) {
            fileNameSpan.textContent = input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
            fileNameSpan.classList.add('text-emerald-700');
        } else {
            fileNameSpan.textContent = 'Klik untuk memilih berkas';
            fileNameSpan.classList.remove('text-emerald-700');
        }
    }

    // Submit Import Form via AJAX
    async function submitImportHierarchy(event) {
        event.preventDefault();
        const form = document.getElementById('importHierarchyForm');
        const submitBtn = document.getElementById('submitImportBtn');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-sm"></i> <span>Mengimpor ke database...</span>';

        const formData = new FormData(form);
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch("{{ route('hierarchy.import') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                closeImportHierarchyModal();
                showToast(result.message || 'Data berhasil diimpor!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(result.message || 'Gagal mengimpor file', 'error');
            }
        } catch (err) {
            showToast('Terjadi kesalahan saat mengunggah berkas.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-upload text-sm"></i> <span>Mulai Impor ke Database</span>';
        }
    }

    // ==================== TOAST NOTIFICATION FUNCTION ====================
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toastNotification');
        const msgEl = document.getElementById('toastMessage');
        const iconEl = document.getElementById('toastIcon');

        if (!toast || !msgEl) return;

        msgEl.textContent = message;
        if (type === 'success') {
            iconEl.className = 'w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-base shrink-0';
            iconEl.innerHTML = '<i class="bi bi-check2-circle"></i>';
        } else {
            iconEl.className = 'w-8 h-8 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-base shrink-0';
            iconEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>';
        }

        toast.classList.remove('hidden');
        setTimeout(() => {
            hideToast();
        }, 4000);
    }

    function hideToast() {
        const toast = document.getElementById('toastNotification');
        if (toast) {
            toast.classList.add('hidden');
        }
    }

    function toggleKpiSbpMenu() {
        const submenu = document.getElementById('kpiSbpSubmenu');
        const arrow = document.getElementById('kpiSbpArrow');
        if (!submenu || !arrow) return;

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            arrow.classList.add('rotate-90');
            arrow.style.transform = '';
            localStorage.setItem('sidebarKpiSbpOpen', 'true');
        } else {
            submenu.classList.add('hidden');
            arrow.classList.remove('rotate-90');
            arrow.style.transform = '';
            localStorage.setItem('sidebarKpiSbpOpen', 'false');
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        const isOpened = localStorage.getItem('sidebarKpiSbpOpen');
        const submenu = document.getElementById('kpiSbpSubmenu');
        const arrow = document.getElementById('kpiSbpArrow');
        if (isOpened === 'false' && submenu && arrow) {
            submenu.classList.add('hidden');
            arrow.classList.remove('rotate-90');
            arrow.style.transform = '';
        } else if (isOpened === 'true' && submenu && arrow) {
            submenu.classList.remove('hidden');
            arrow.classList.add('rotate-90');
            arrow.style.transform = '';
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

    // Custom Dropdown Toggle Logic for Hierarchy Filters
    function submitHierFilter(type, val) {
        if (type === 'branch') document.getElementById('hierBranchInput').value = val;
        if (type === 'cluster') document.getElementById('hierClusterInput').value = val;
        document.getElementById('hierarchyFilterForm').submit();
    }

    function toggleHierBranchMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('hierBranchMenu');
        const arrow = document.getElementById('hierBranchArrow');
        const clusterMenu = document.getElementById('hierClusterMenu');
        if (clusterMenu) clusterMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleHierClusterMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('hierClusterMenu');
        const arrow = document.getElementById('hierClusterArrow');
        const branchMenu = document.getElementById('hierBranchMenu');
        if (branchMenu) branchMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const bContainer = document.getElementById('hierBranchDropdownContainer');
        const bMenu = document.getElementById('hierBranchMenu');
        const bArrow = document.getElementById('hierBranchArrow');
        if (bContainer && !bContainer.contains(e.target) && bMenu && !bMenu.classList.contains('hidden')) {
            bMenu.classList.add('hidden');
            if (bArrow) bArrow.classList.remove('rotate-180');
        }

        const cContainer = document.getElementById('hierClusterDropdownContainer');
        const cMenu = document.getElementById('hierClusterMenu');
        const cArrow = document.getElementById('hierClusterArrow');
        if (cContainer && !cContainer.contains(e.target) && cMenu && !cMenu.classList.contains('hidden')) {
            cMenu.classList.add('hidden');
            if (cArrow) cArrow.classList.remove('rotate-180');
        }
    });
</script>
