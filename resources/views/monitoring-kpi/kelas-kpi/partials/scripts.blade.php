<!-- JAVASCRIPT FOR SIDEBAR, VIEW MODE SWITCHING & PROFILE DROPDOWN -->
<script>
    let analisisChartInstance = null;
    const analisisMonthsList = @json($analisisMonths);
    const analisisDataMapObj = @json($analisisDataMap);
    const analisisPeriodesList = @json($analisisPeriodes);
    const analisisInitialDatasets = @json($analisisChartDatasets);


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
            setTimeout(initAnalisisChart, 100);
        }

        updateSwitcherButtons(mode);

        if (e && e.preventDefault) {
            e.preventDefault();
            const url = new URL(window.location.href);
            url.searchParams.set('view_mode', mode);
            window.history.pushState({}, '', url);
        }
    }

    const verticalHoverLinePlugin = {
        id: 'verticalHoverLine',
        beforeDraw: (chart) => {
            const activeElems = chart.getActiveElements ? chart.getActiveElements() : (chart.tooltip ? chart.tooltip._active : []);
            if (activeElems && activeElems.length) {
                const ctx = chart.ctx;
                const activePoint = activeElems[0];
                if (!activePoint || !activePoint.element) return;

                const x = activePoint.element.x;
                const topY = chart.scales.y ? chart.scales.y.top : 0;
                const bottomY = chart.scales.y ? chart.scales.y.bottom : chart.height;

                ctx.save();
                ctx.beginPath();
                ctx.moveTo(x, topY);
                ctx.lineTo(x, bottomY);
                ctx.lineWidth = 1.5;
                ctx.setLineDash([4, 4]);
                ctx.strokeStyle = 'rgba(237, 28, 36, 0.5)';
                ctx.stroke();
                ctx.restore();
            }
        }
    };

    function initAnalisisChart() {
        const ctx = document.getElementById('analisisLineChart');
        if (!ctx) return;

        if (ctx.clientWidth === 0 || ctx.clientHeight === 0) {
            setTimeout(initAnalisisChart, 100);
            return;
        }

        if (analisisChartInstance) {
            analisisChartInstance.destroy();
            analisisChartInstance = null;
        }

        const chartCanvasCtx = ctx.getContext('2d');

        analisisInitialDatasets.forEach((ds) => {
            const hex = ds.borderColor || '#ED1C24';
            
            let r = 237, g = 28, b = 36;
            let c = hex.replace('#', '');
            if (c.length === 3) c = c.split('').map(x => x + x).join('');
            if (c.length === 6) {
                const num = parseInt(c, 16);
                r = (num >> 16) & 255;
                g = (num >> 8) & 255;
                b = num & 255;
            }

            const gradient = chartCanvasCtx.createLinearGradient(0, 0, 0, 360);
            gradient.addColorStop(0, `rgba(${r}, ${g}, ${b}, 0.28)`);
            gradient.addColorStop(0.65, `rgba(${r}, ${g}, ${b}, 0.08)`);
            gradient.addColorStop(1, `rgba(${r}, ${g}, ${b}, 0.0)`);

            ds.fill = true;
            ds.backgroundColor = gradient;
            ds.tension = 0.2;
            ds.borderWidth = 3;
            ds.pointBackgroundColor = '#FFFFFF';
            ds.pointBorderColor = hex;
            ds.pointBorderWidth = 2.5;
            ds.pointRadius = 5;
            ds.pointHoverRadius = 9;
            ds.pointHitRadius = 35;
            ds.pointHoverBackgroundColor = '#FFFFFF';
            ds.pointHoverBorderColor = hex;
            ds.pointHoverBorderWidth = 3.5;
        });

        analisisChartInstance = new Chart(ctx, {
            type: 'line',
            plugins: [verticalHoverLinePlugin],
            data: {
                labels: analisisMonthsList,
                datasets: analisisInitialDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 15,
                        right: 45,
                        top: 20,
                        bottom: 5
                    }
                },
                events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
                hover: {
                    mode: 'index',
                    intersect: false
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                onHover: (event, activeElements) => {
                    const el = event.native ? event.native.target : (event.target || null);
                    if (el && el.style) {
                        el.style.cursor = activeElements && activeElements.length ? 'pointer' : 'default';
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        position: 'nearest',
                        backgroundColor: '#FFFFFF',
                        titleColor: '#64748B',
                        bodyColor: '#1E293B',
                        borderColor: 'rgba(226, 232, 240, 0.9)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                        usePointStyle: true,
                        boxWidth: 8,
                        boxHeight: 8,
                        boxPadding: 6,
                        filter: function(tooltipItem) {
                            if (!tooltipItem || !tooltipItem.dataset) return false;
                            if (tooltipItem.dataset.hidden) return false;
                            const val = tooltipItem.parsed ? tooltipItem.parsed.y : tooltipItem.raw;
                            return val !== null && val !== undefined && !isNaN(val);
                        },
                        callbacks: {
                            title: function(items) {
                                if (!items || !items.length) return '';
                                const idx = items[0].dataIndex;
                                if (analisisMonthsList && analisisMonthsList[idx]) {
                                    return analisisMonthsList[idx];
                                }
                                return items[0].label || '';
                            },
                            label: function(context) {
                                let val = context.parsed ? context.parsed.y : context.raw;
                                if (val === null || val === undefined || isNaN(val)) {
                                    return null;
                                }
                                let label = context.dataset.label || '';
                                return ` ${label}: ${Number(val).toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { 
                            color: 'rgba(241, 245, 249, 0.7)', 
                            drawBorder: false,
                            offset: false
                        },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10, weight: '600' },
                            color: '#475569',
                            autoSkip: false,
                            align: 'center',
                            maxRotation: 0,
                            minRotation: 0,
                            callback: function(value, index, values) {
                                const fullLabel = this.getLabelForValue(value);
                                if (!fullLabel) return '';
                                const monthsMap = {
                                    'januari': 'Jan', 'februari': 'Feb', 'maret': 'Mar', 'april': 'Apr',
                                    'mei': 'Mei', 'juni': 'Jun', 'juli': 'Jul', 'agustus': 'Agt',
                                    'september': 'Sep', 'oktober': 'Okt', 'november': 'Nov', 'desember': 'Des'
                                };
                                let str = String(fullLabel);
                                for (let k in monthsMap) {
                                    let r = new RegExp(k, 'gi');
                                    if (r.test(str)) {
                                        return str.replace(r, monthsMap[k]);
                                    }
                                }
                                return str;
                            }
                        }
                    },
                    y: {
                        min: 0,
                        max: 2.8,
                        grid: { color: 'rgba(241, 245, 249, 0.8)', drawBorder: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10.5, weight: '600' },
                            color: '#94A3B8',
                            stepSize: 0.5,
                            callback: function(value) {
                                return value.toFixed(1).replace('.', ',');
                            }
                        },
                        title: {
                            display: true,
                            text: 'TOTAL SKOR KPI',
                            font: { family: 'Plus Jakarta Sans', size: 10, weight: '800' },
                            color: '#334155'
                        }
                    }
                }
            }
        });

        // Immediately filter to top cluster (BALI BARAT) on chart load
        filterAnalisisChart();
    }

    function filterAnalisisChart() {
        if (!analisisChartInstance) return;

        const defaultCluster = (analisisInitialDatasets && analisisInitialDatasets.length) ? analisisInitialDatasets[0].cluster : 'BALI BARAT';
        const selectedPeriode = document.getElementById('analisisFilterPeriode')?.value || 'all';
        const selectedCluster = document.getElementById('analisisFilterCluster')?.value || defaultCluster;

        // 1. Filter Cluster: Show specific cluster
        analisisChartInstance.data.datasets.forEach(ds => {
            if (ds.cluster === selectedCluster) {
                ds.hidden = false;
            } else {
                ds.hidden = true;
            }
        });

        // 2. Filter Periode: Highlight selected periode point on X-axis if selected
        const pIndex = analisisPeriodesList.indexOf(selectedPeriode);

        analisisChartInstance.data.datasets.forEach(ds => {
            const hex = ds.borderColor || '#ED1C24';
            if (selectedPeriode !== 'all' && pIndex !== -1) {
                ds.pointRadius = ds.data.map((_, idx) => (idx === pIndex ? 8 : 4));
                ds.pointHoverRadius = ds.data.map((_, idx) => (idx === pIndex ? 11 : 7));
                ds.pointBorderWidth = ds.data.map((_, idx) => (idx === pIndex ? 4 : 2.5));
            } else {
                ds.pointRadius = 5;
                ds.pointHoverRadius = 9;
                ds.pointBorderWidth = 2.5;
            }
            ds.pointHitRadius = 35;
            ds.pointBackgroundColor = '#FFFFFF';
            ds.pointBorderColor = hex;
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
