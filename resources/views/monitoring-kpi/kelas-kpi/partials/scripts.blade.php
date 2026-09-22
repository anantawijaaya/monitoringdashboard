<!-- JAVASCRIPT FOR SIDEBAR, VIEW MODE SWITCHING & PROFILE DROPDOWN -->
<script>
    let analisisChartInstance = null;
    const analisisMonthsList = @json($analisisMonths);
    const analisisDataMapObj = @json($analisisDataMap);
    const analisisPeriodesList = @json($analisisPeriodes);

    function updateSwitcherButtons(mode) {
        const activeClass = "px-3.5 py-1.5 rounded-full text-xs font-extrabold transition-all cursor-pointer shadow-xs bg-[#ED1C24] text-white flex items-center gap-1.5";
        const inactiveClass = "px-3.5 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-[#ED1C24] flex items-center gap-1.5";

        document.querySelectorAll('.tab-btn-ringkasan').forEach(el => el.className = (mode === 'ringkasan' ? activeClass : inactiveClass) + ' tab-btn-ringkasan');
        document.querySelectorAll('.tab-btn-lengkap').forEach(el => el.className = (mode === 'lengkap' ? activeClass : inactiveClass) + ' tab-btn-lengkap');
        document.querySelectorAll('.tab-btn-analisis').forEach(el => el.className = (mode === 'analisis' ? activeClass : inactiveClass) + ' tab-btn-analisis');
    }

    function switchViewMode(e, mode) {
        const viewRingkasan = document.getElementById('viewRingkasan');
        const viewLengkap = document.getElementById('viewLengkap');
        const viewAnalisis = document.getElementById('viewAnalisis');
        const formViewMode = document.getElementById('formViewMode');
        const titleBlock = document.getElementById('kpiHeaderTitleBlock');
        const headerActions = document.getElementById('kpiHeaderActions');

        if (formViewMode) formViewMode.value = mode;

        if (mode === 'ringkasan') {
            if (viewRingkasan) viewRingkasan.classList.remove('hidden');
            if (viewLengkap) viewLengkap.classList.add('hidden');
            if (viewAnalisis) viewAnalisis.classList.add('hidden');
            if (titleBlock) titleBlock.classList.remove('hidden');
            if (headerActions) headerActions.classList.add('hidden');
        } else if (mode === 'lengkap') {
            if (viewRingkasan) viewRingkasan.classList.add('hidden');
            if (viewLengkap) viewLengkap.classList.remove('hidden');
            if (viewAnalisis) viewAnalisis.classList.add('hidden');
            if (titleBlock) titleBlock.classList.add('hidden');
            if (headerActions) headerActions.classList.remove('hidden');
        } else if (mode === 'analisis') {
            if (viewRingkasan) viewRingkasan.classList.add('hidden');
            if (viewLengkap) viewLengkap.classList.add('hidden');
            if (viewAnalisis) viewAnalisis.classList.remove('hidden');
            if (titleBlock) titleBlock.classList.add('hidden');
            if (headerActions) headerActions.classList.add('hidden');
            setTimeout(initAnalisisChart, 50);
        }

        updateSwitcherButtons(mode);

        if (e && e.preventDefault) {
            e.preventDefault();
            const url = new URL(window.location.href);
            url.searchParams.set('view_mode', mode);
            window.history.pushState({}, '', url);
        }
    }

    function initAnalisisChart() {
        const ctx = document.getElementById('analisisLineChart');
        if (!ctx) return;

        if (analisisChartInstance) {
            analisisChartInstance.resize();
            return;
        }

        const canvasCtx = ctx.getContext('2d');
        const colorPalette = ['#F59E0B', '#10B981', '#ED1C24', '#2563EB', '#8B5CF6', '#06B6D4'];

        const chartDatasets = analisisPeriodesList.map((pVal, idx) => {
            const color = colorPalette[idx % colorPalette.length];
            const dataArr = (analisisDataMapObj['all'] && analisisDataMapObj['all'][pVal]) 
                ? analisisDataMapObj['all'][pVal] 
                : [2.0, 2.5, 2.0, 2.1, 2.1, 2.0, 2.0, 2.3, 2.1, 2.1, 1.85, 1.65];

            let bgFill = 'transparent';
            if (idx === 0) {
                const grad = canvasCtx.createLinearGradient(0, 0, 0, 420);
                grad.addColorStop(0, 'rgba(245, 158, 11, 0.20)');
                grad.addColorStop(0.7, 'rgba(245, 158, 11, 0.04)');
                grad.addColorStop(1, 'rgba(245, 158, 11, 0.00)');
                bgFill = grad;
            }

            return {
                label: 'Periode ' + pVal,
                periode: pVal,
                data: dataArr,
                borderColor: color,
                borderWidth: 3.5,
                tension: 0.45,
                fill: idx === 0 ? 'origin' : false,
                backgroundColor: bgFill,
                pointBackgroundColor: '#FFFFFF',
                pointBorderColor: color,
                pointBorderWidth: 2.5,
                pointRadius: 4.5,
                pointHoverRadius: 8,
                pointHoverBorderWidth: 3,
            };
        });

        analisisChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: analisisMonthsList,
                datasets: chartDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'center',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            padding: 20,
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 11,
                                weight: 'bold'
                            },
                            color: '#334155'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                let val = context.parsed.y;
                                let label = context.dataset.label || '';
                                return `${label}: ${val.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' },
                            color: function(context) {
                                return context.index === 8 ? '#0284C7' : '#334155';
                            }
                        }
                    },
                    y: {
                        min: 0,
                        max: 3.2,
                        grid: { color: '#F1F5F9', drawBorder: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10, weight: 'extrabold' },
                            color: '#334155',
                            stepSize: 0.5,
                            callback: function(value) {
                                return value.toFixed(1).replace('.', ',');
                            }
                        },
                        title: {
                            display: true,
                            text: 'TOTAL SKOR KPI',
                            font: { family: 'Plus Jakarta Sans', size: 10, weight: 'extrabold' },
                            color: '#0F172A'
                        }
                    }
                }
            }
        });
    }

    function filterAnalisisChart() {
        if (!analisisChartInstance) return;

        const selectedPeriode = document.getElementById('analisisFilterPeriode')?.value || 'all';
        const selectedCluster = document.getElementById('analisisFilterCluster')?.value || 'all';

        analisisChartInstance.data.datasets.forEach(ds => {
            const pVal = ds.periode;
            
            if (selectedPeriode === 'all' || selectedPeriode === pVal) {
                ds.hidden = false;
            } else {
                ds.hidden = true;
            }

            const clusterMap = analisisDataMapObj[selectedCluster] || analisisDataMapObj['all'];
            if (clusterMap && clusterMap[pVal]) {
                ds.data = clusterMap[pVal];
            }
        });

        analisisChartInstance.update();
    }

    document.addEventListener('DOMContentLoaded', function() {
        if ("{{ $viewMode ?? 'ringkasan' }}" === 'analisis') {
            setTimeout(initAnalisisChart, 100);
        }
    });
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
        if (container && !container.contains(e.target)) {
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                if (arrow) {
                    arrow.classList.remove('rotate-180');
                }
            }
        }
    });

    // ==================== IMPOR DATA KPI MODAL JS ====================
    function openImportKpiModal() {
        const modal = document.getElementById('importKpiModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeImportKpiModal() {
        const modal = document.getElementById('importKpiModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function handleFileSelect(input) {
        const fileNameDisplay = document.getElementById('kpiFileNameDisplay');
        const fileInfo = document.getElementById('selectedFileInfo');
        const fileNameSpan = document.getElementById('selectedFileName');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const text = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            if (fileNameDisplay) fileNameDisplay.textContent = text;
            if (fileNameSpan) fileNameSpan.textContent = text;
            if (fileInfo) fileInfo.classList.remove('hidden');
        } else {
            if (fileNameDisplay) fileNameDisplay.textContent = '';
            if (fileInfo) fileInfo.classList.add('hidden');
        }
    }

    // Custom Dropdown Toggle Logic for Kelas KPI
    function submitRingkasanPeriode(val) {
        document.getElementById('ringkasanPeriodeInput').value = val;
        document.getElementById('filterPeriodeFormRingkasan').submit();
    }

    function toggleRingkasanPeriodeMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('ringkasanPeriodeMenu');
        const arrow = document.getElementById('ringkasanPeriodeArrow');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function submitLengkapPeriode(val) {
        document.getElementById('lengkapPeriodeInput').value = val;
        document.getElementById('filterPeriodeFormLengkap').submit();
    }

    function toggleLengkapPeriodeMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('lengkapPeriodeMenu');
        const arrow = document.getElementById('lengkapPeriodeArrow');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleAnalisisPeriodeMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('analisisPeriodeMenu');
        const arrow = document.getElementById('analisisPeriodeArrow');
        const clusterMenu = document.getElementById('analisisClusterMenu');
        if (clusterMenu) clusterMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function selectAnalisisPeriode(val, label) {
        document.getElementById('analisisFilterPeriode').value = val;
        document.getElementById('analisisPeriodeBtnText').textContent = label;
        document.getElementById('analisisPeriodeMenu').classList.add('hidden');
        const arrow = document.getElementById('analisisPeriodeArrow');
        if (arrow) arrow.classList.remove('rotate-180');
        filterAnalisisChart();
    }

    function toggleAnalisisClusterMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('analisisClusterMenu');
        const arrow = document.getElementById('analisisClusterArrow');
        const periodeMenu = document.getElementById('analisisPeriodeMenu');
        if (periodeMenu) periodeMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function selectAnalisisCluster(val, label) {
        document.getElementById('analisisFilterCluster').value = val;
        document.getElementById('analisisClusterBtnText').textContent = label;
        document.getElementById('analisisClusterMenu').classList.add('hidden');
        const arrow = document.getElementById('analisisClusterArrow');
        if (arrow) arrow.classList.remove('rotate-180');
        filterAnalisisChart();
    }

    document.addEventListener('click', function(e) {
        const rContainer = document.getElementById('ringkasanPeriodeDropdownContainer');
        const rMenu = document.getElementById('ringkasanPeriodeMenu');
        const rArrow = document.getElementById('ringkasanPeriodeArrow');
        if (rContainer && !rContainer.contains(e.target) && rMenu && !rMenu.classList.contains('hidden')) {
            rMenu.classList.add('hidden');
            if (rArrow) rArrow.classList.remove('rotate-180');
        }

        const lContainer = document.getElementById('lengkapPeriodeDropdownContainer');
        const lMenu = document.getElementById('lengkapPeriodeMenu');
        const lArrow = document.getElementById('lengkapPeriodeArrow');
        if (lContainer && !lContainer.contains(e.target) && lMenu && !lMenu.classList.contains('hidden')) {
            lMenu.classList.add('hidden');
            if (lArrow) lArrow.classList.remove('rotate-180');
        }

        const apContainer = document.getElementById('analisisPeriodeDropdownContainer');
        const apMenu = document.getElementById('analisisPeriodeMenu');
        const apArrow = document.getElementById('analisisPeriodeArrow');
        if (apContainer && !apContainer.contains(e.target) && apMenu && !apMenu.classList.contains('hidden')) {
            apMenu.classList.add('hidden');
            if (apArrow) apArrow.classList.remove('rotate-180');
        }

        const acContainer = document.getElementById('analisisClusterDropdownContainer');
        const acMenu = document.getElementById('analisisClusterMenu');
        const acArrow = document.getElementById('analisisClusterArrow');
        if (acContainer && !acContainer.contains(e.target) && acMenu && !acMenu.classList.contains('hidden')) {
            acMenu.classList.add('hidden');
            if (acArrow) acArrow.classList.remove('rotate-180');
        }
    });
</script>
