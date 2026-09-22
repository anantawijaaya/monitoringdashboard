<!-- INTERACTIVE JAVASCRIPT & LIVE FORM AUTO-CALCULATION -->
<script>
    // Auto-fill Cluster based on Kabupaten selection
    function autoFillClusterFromKabupaten() {
        const rawKab = document.getElementById('inKabupaten').value || '';
        const kab = rawKab.toUpperCase().replace(/^(KAB\.|KOTA|KABUPATEN)\s+/i, '').trim();
        const clusterInput = document.getElementById('inClusterName');
        if (!kab) return;

        if (['BULELENG', 'JEMBRANA', 'TABANAN', 'SINGARAJA', 'NEGARA'].includes(kab)) {
            clusterInput.value = 'BALI BARAT';
        } else if (['BADUNG', 'DENPASAR', 'KOTA DENPASAR'].includes(kab)) {
            clusterInput.value = 'BALI TENGAH';
        } else if (['BANGLI', 'GIANYAR', 'KARANG ASEM', 'KARANGASEM', 'KLUNGKUNG', 'NUSA PENIDA'].includes(kab)) {
            clusterInput.value = 'BALI TIMUR';
        } else if (['ENDE', 'SIKKA', 'MAUMERE'].includes(kab)) {
            clusterInput.value = 'ENDE SIKKA';
        } else if (['ALOR', 'FLORES TIMUR', 'LEMBATA', 'LARANTUKA', 'KALABAHI', 'LEWOLEBA'].includes(kab)) {
            clusterInput.value = 'FLORES TIMUR';
        } else if (['MANGGARAI', 'MANGGARAI BARAT', 'MANGGARAI TIMUR', 'NAGEKEO', 'NGADA', 'LABUAN BAJO', 'RUTENG', 'BORONG', 'MBAY', 'BAJAWA'].includes(kab)) {
            clusterInput.value = 'MANGGARAI';
        } else if (['KUPANG', 'KOTA KUPANG', 'ROTE NDAO', 'ROTE', 'BAA', 'OELAMASI'].includes(kab)) {
            clusterInput.value = 'KUPANG ROTE';
        } else if (['BELU', 'MALAKA', 'TIMOR TENGAH SELATAN', 'TTS', 'TIMOR TENGAH UTARA', 'TTU', 'ATAMBUA', 'BETUN', 'SOE', 'KEFAMENANU'].includes(kab)) {
            clusterInput.value = 'MALAKA TIMTIM BELU';
        } else if (['SABU RAIJUA', 'SUMBA BARAT', 'SUMBA BARAT DAYA', 'SUMBA TENGAH', 'SUMBA TIMUR', 'WAIKABUBAK', 'TAMBOLAKA', 'WAIBAKUL', 'WAINGAPU', 'MENIA'].includes(kab)) {
            clusterInput.value = 'SUMBA';
        } else if (['MATARAM', 'KOTA MATARAM', 'LOMBOK BARAT', 'LOMBOK TENGAH', 'LOMBOK TIMUR', 'LOMBOK UTARA', 'GERUNG', 'PRAYA', 'SELONG', 'TANJUNG'].includes(kab)) {
            clusterInput.value = 'LOMBOK';
        } else if (['SUMBAWA', 'SUMBAWA BARAT', 'SUMBAWA BESAR', 'TALIWANG'].includes(kab)) {
            clusterInput.value = 'SUMBAWA';
        } else if (['BIMA', 'KOTA BIMA', 'DOMPU', 'WOHA', 'RABA'].includes(kab)) {
            clusterInput.value = 'SUMBAWA TIMUR';
        }
    }

    // Modal Controls
    function openManualModal() {
        const errAlert = document.getElementById('manualErrorAlert');
        if (errAlert) errAlert.classList.add('hidden');
        document.getElementById('manualModal').classList.remove('hidden');
    }
    function closeManualModal() {
        document.getElementById('manualModal').classList.add('hidden');
    }

    function openImportModal() {
        const errAlert = document.getElementById('importErrorAlert');
        if (errAlert) errAlert.classList.add('hidden');
        document.getElementById('importModal').classList.remove('hidden');
    }
    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function openGrowthImportModal() {
        const errAlert = document.getElementById('growthImportErrorAlert');
        if (errAlert) errAlert.classList.add('hidden');
        document.getElementById('growthImportModal').classList.remove('hidden');
    }
    function closeGrowthImportModal() {
        document.getElementById('growthImportModal').classList.add('hidden');
    }

    // Seamless AJAX Manual Form Submit
    async function handleManualSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitManual');
        const errAlert = document.getElementById('manualErrorAlert');
        const errMsg = document.getElementById('manualErrorMsg');
        
        errAlert.classList.add('hidden');
        errMsg.innerText = '';
        
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Menyimpan...</span>';

        try {
            const formData = new FormData(form);
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success) {
                btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Tersimpan!</span>';
                window.location.href = "{{ route('dashboard') }}";
            } else {
                errMsg.innerText = data.message || 'Terjadi kesalahan saat menyimpan data.';
                errAlert.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        } catch (err) {
            errMsg.innerText = 'Gagal menghubungi server.';
            errAlert.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    // Seamless AJAX Import Form Submit
    async function handleImportSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitImport');
        const errAlert = document.getElementById('importErrorAlert');
        const errMsg = document.getElementById('importErrorMsg');
        
        errAlert.classList.add('hidden');
        errMsg.innerText = '';
        
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Memproses Berkas...</span>';

        try {
            const formData = new FormData(form);
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success) {
                btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Selesai!</span>';
                window.location.href = "{{ route('dashboard') }}";
            } else {
                errMsg.innerText = data.message || 'Gagal memproses berkas import.';
                errAlert.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        } catch (err) {
            errMsg.innerText = 'Terjadi kesalahan koneksi server.';
            errAlert.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    // Seamless AJAX Growth Chart Import Submit
    async function handleGrowthImportSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitGrowthImport');
        const errAlert = document.getElementById('growthImportErrorAlert');
        const errMsg = document.getElementById('growthImportErrorMsg');
        
        errAlert.classList.add('hidden');
        errMsg.innerText = '';
        
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Memproses Data...</span>';

        try {
            const formData = new FormData(form);
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success) {
                btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Berhasil!</span>';
                window.location.reload();
            } else {
                errMsg.innerText = data.message || 'Gagal mengimpor file data grafik.';
                errAlert.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        } catch (err) {
            errMsg.innerText = 'Gagal menghubungi server.';
            errAlert.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    // Reset Growth Data
    async function resetGrowthData() {
        if (!confirm('Apakah Anda yakin ingin mereset data grafik khusus dan kembali ke sinkronisasi database revenue utama?')) {
            return;
        }

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        try {
            const response = await fetch('{{ route("growth.reset") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                }
            });
            const data = await response.json().catch(() => ({}));
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal mereset data.');
            }
        } catch (e) {
            alert('Gagal menghubungi server.');
        }
    }

    // Seamless AJAX Delete Cluster Record
    async function handleDeleteCluster(e, url, name) {
        e.preventDefault();
        if (!confirm(`Hapus data cluster "${name}"?`)) return;

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ _method: 'DELETE' })
            });

            const data = await response.json().catch(() => ({}));
            if (response.ok && data.success) {
                window.location.href = "{{ route('dashboard') }}";
            } else {
                alert(data.message || 'Gagal menghapus data.');
            }
        } catch (err) {
            alert('Gagal menghubungi server.');
        }
    }

    // Live Auto-Calculation of Percentages in Manual Input Form
    function calcAutoPersentase() {
        const targetAll = parseFloat(document.getElementById('inTargetAll').value) || 0;
        const mtdAll = parseFloat(document.getElementById('inMtdAll').value) || 0;
        const badgeAchAll = document.getElementById('badgeAchAll');
        if (targetAll > 0) {
            const ach = ((mtdAll / targetAll) * 100).toFixed(1);
            badgeAchAll.innerText = `Pencapaian: ${ach}%`;
            badgeAchAll.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeAchAll.innerText = 'Pencapaian: 0%';
            badgeAchAll.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }

        const targetBb = parseFloat(document.getElementById('inTargetBb').value) || 0;
        const mtdBb = parseFloat(document.getElementById('inMtdBb').value) || 0;
        const badgeAchBb = document.getElementById('badgeAchBb');
        if (targetBb > 0) {
            const ach = ((mtdBb / targetBb) * 100).toFixed(1);
            badgeAchBb.innerText = `Pencapaian: ${ach}%`;
            badgeAchBb.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeAchBb.innerText = 'Pencapaian: 0%';
            badgeAchBb.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }

        const targetRedeem = parseFloat(document.getElementById('inTargetRedeem').value) || 0;
        const mtdRedeem = parseFloat(document.getElementById('inMtdRedeem').value) || 0;
        const badgeAchRedeem = document.getElementById('badgeAchRedeem');
        if (targetRedeem > 0) {
            const ach = ((mtdRedeem / targetRedeem) * 100).toFixed(1);
            badgeAchRedeem.innerText = `Pencapaian: ${ach}%`;
            badgeAchRedeem.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeAchRedeem.innerText = 'Pencapaian: 0%';
            badgeAchRedeem.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }

        const inCurr = document.getElementById('inCurrentMonth');
        if (!inCurr.value && mtdAll > 0) {
            inCurr.value = mtdAll;
        }

        const lastMonth = parseFloat(document.getElementById('inLastMonth').value) || 0;
        const currMonth = parseFloat(document.getElementById('inCurrentMonth').value) || 0;
        const badgeMoM = document.getElementById('badgeMoMGrowth');
        if (lastMonth > 0) {
            const mom = (((currMonth - lastMonth) / lastMonth) * 100).toFixed(1);
            badgeMoM.innerText = `MoM: ${mom >= 0 ? '+' : ''}${mom}%`;
            badgeMoM.className = mom >= 2.0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (mom >= 0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeMoM.innerText = 'MoM: +0.0%';
            badgeMoM.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }
    }

    // Dropdown Toggle
    function toggleUserDropdown(event) {
        event.stopPropagation();
        const menu = document.getElementById('userDropdownMenu');
        const arrow = document.getElementById('dropdownArrow');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            menu.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('userDropdownContainer');
        const menu = document.getElementById('userDropdownMenu');
        const arrow = document.getElementById('dropdownArrow');
        if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    });

    // Chart.js initialization for Perbandingan Pendapatan Cluster (Bulan Sebelumnya vs Bulan Sekarang)
    let growthChart;

    const chartClusterLabels = @json($chartData['labels'] ?? []);
    const chartKabupatens = @json($chartData['kabupatens'] ?? []);
    const chartClusters = @json($chartData['clusters'] ?? []);
    const datasetValLastMonth = @json($chartData['val_last_month'] ?? []);
    const datasetValCurrentMonth = @json($chartData['val_current_month'] ?? []);

    function initChart() {
        const chartCanvas = document.getElementById('revenueGrowthChart');
        if (!chartCanvas) return;
        const ctx = chartCanvas.getContext('2d');

        growthChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartClusterLabels,
                datasets: [
                    {
                        label: 'Bulan Sebelumnya',
                        data: datasetValLastMonth,
                        backgroundColor: 'grey',
                        hoverBackgroundColor: 'grey',
                        borderRadius: 0,
                        borderSkipped: false,
                        barPercentage: 0.9,
                        categoryPercentage: 0.8,
                    },
                    {
                        label: 'Bulan Sekarang',
                        data: datasetValCurrentMonth,
                        backgroundColor: 'red',
                        hoverBackgroundColor: 'red',
                        borderRadius: 0,
                        borderSkipped: false,
                        barPercentage: 0.9,
                        categoryPercentage: 0.8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#18181B',
                        titleColor: '#FFFFFF',
                        bodyColor: '#F3F4F6',
                        padding: 12,
                        borderRadius: 12,
                        usePointStyle: true,
                        callbacks: {
                            title: function(items) {
                                if (!items.length) return '';
                                const idx = items[0].dataIndex;
                                const cluster = chartClusters[idx] || items[0].label;
                                const kab = chartKabupatens[idx] || '';
                                return (kab && kab !== cluster && kab !== '-') ? `${cluster} (${kab})` : cluster;
                            },
                            label: function(context) {
                                const val = context.parsed.y;
                                const datasetLabel = context.dataset.label || '';
                                return `${datasetLabel}: Rp ${val.toFixed(2).replace('.', ',')} Miliar`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 8.5, weight: '700' },
                            color: '#334155',
                            autoSkip: false,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: {
                        grid: { color: '#F1F5F9', drawBorder: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
                            color: '#64748B',
                            callback: function(value) {
                                return 'Rp ' + value + ' M';
                            }
                        }
                    }
                }
            }
        });
    }

    function toggleDataset(index) {
        if (!growthChart) return;
        const isVisible = growthChart.isDatasetVisible(index);
        const btn = document.getElementById(`legendBtn-${index}`);
        if (isVisible) {
            growthChart.hide(index);
            if (btn) btn.classList.add('opacity-40', 'line-through');
        } else {
            growthChart.show(index);
            if (btn) btn.classList.remove('opacity-40', 'line-through');
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
        initChart();
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

    // Custom Dropdown Toggle Logic for Dashboard Filters
    function submitDashFilter(type, val) {
        if (type === 'cluster') document.getElementById('dashClusterInput').value = val;
        if (type === 'period') document.getElementById('dashPeriodInput').value = val;
        document.getElementById('dashboardFilterForm').submit();
    }

    function toggleDashClusterMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('dashClusterMenu');
        const arrow = document.getElementById('dashClusterArrow');
        const periodMenu = document.getElementById('dashPeriodMenu');
        if (periodMenu) periodMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleDashPeriodMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('dashPeriodMenu');
        const arrow = document.getElementById('dashPeriodArrow');
        const clusterMenu = document.getElementById('dashClusterMenu');
        if (clusterMenu) clusterMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const cContainer = document.getElementById('dashClusterDropdownContainer');
        const cMenu = document.getElementById('dashClusterMenu');
        const cArrow = document.getElementById('dashClusterArrow');
        if (cContainer && !cContainer.contains(e.target) && cMenu && !cMenu.classList.contains('hidden')) {
            cMenu.classList.add('hidden');
            if (cArrow) cArrow.classList.remove('rotate-180');
        }

        const pContainer = document.getElementById('dashPeriodDropdownContainer');
        const pMenu = document.getElementById('dashPeriodMenu');
        const pArrow = document.getElementById('dashPeriodArrow');
        if (pContainer && !pContainer.contains(e.target) && pMenu && !pMenu.classList.contains('hidden')) {
            pMenu.classList.add('hidden');
            if (pArrow) pArrow.classList.remove('rotate-180');
        }
    });
</script>
