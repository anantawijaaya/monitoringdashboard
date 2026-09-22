<!-- ========================================================================= -->
<!-- JAVASCRIPT: ADVANCED CLUSTER & GIS REGIONAL MAP ENGINE -->
<!-- ========================================================================= -->
<script>
    let rawMarkers = [];
    let map = null;
    let clusterLayer = null;
    let pointsLayer = null;
    let heatmapLayer = null;
    let streetLayer = null;
    let satelliteLayer = null;
    let lightLayer = null;
    let darkLayer = null;
    let currentVisualMode = 'points'; // 'cluster' | 'points' | 'heatmap' | 'street' | 'satellite'

    // Tile layer definitions (Esri ArcGIS Online - Clean, No API Key Required, No Watermark)
    const tileProviders = {
        dark: {
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}',
            options: {
                attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
                maxZoom: 19
            }
        },
        light: {
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
            options: {
                attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
                maxZoom: 19
            }
        },
        street: {
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
            options: {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ',
                maxZoom: 19
            }
        },
        satellite: {
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            options: {
                attribution: 'Tiles &copy; Esri',
                maxZoom: 19
            }
        }
    };

    // Toggle Filter Bar Visibility
    function toggleFilterBar() {
        const container = document.getElementById('filterBarContainer');
        const btnText = document.getElementById('btnToggleFilterText');
        if (container) {
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                if (btnText) btnText.innerText = 'Sembunyikan Filter';
            } else {
                container.classList.add('hidden');
                if (btnText) btnText.innerText = 'Tampilkan Filter';
            }
        }
    }

    // User Profile Dropdown
    function toggleUserDropdown(event) {
        event.stopPropagation();
        const menu = document.getElementById('userDropdownMenu');
        const arrow = document.getElementById('dropdownArrow');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            arrow.classList.add('rotate-180');
        } else {
            menu.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('userDropdownMenu');
        const btn = document.getElementById('userDropdownBtn');
        const arrow = document.getElementById('dropdownArrow');
        if (menu && !menu.contains(e.target) && !btn.contains(e.target)) {
            menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });

    // Helpers
    function formatRupiah(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    }

    function getFlagInfo(val) {
        const num = parseFloat(val || 0);
        let color = 'black';
        let hex = '#18181B';
        let label = 'Flag < 0%';

        if (num < 0) {
            color = 'black';
            hex = '#18181B';
            label = 'Flag < 0%';
        } else if (num === 0.0) {
            color = 'red';
            hex = '#ED1C24';
            label = 'Flag = 0%';
        } else if (num <= 3.0) {
            color = 'orange';
            hex = '#F97316';
            label = 'Flag <= 3%';
        } else {
            color = 'green';
            hex = '#10B981';
            label = 'Flag > 3%';
        }

        const prefix = num > 0 ? '+' : '';
        const formatted = prefix + num.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%';

        return { color, hex, label, formatted };
    }

    // =====================================================================
    // INITIALIZE MAP & REGIONAL LABELS
    // =====================================================================
    function initInteractiveMap() {
        if (map) return;

        // Define Bali, NTB, NTT Regional Bounding Box (Strict Lock)
        const baliNusaBounds = L.latLngBounds(
            L.latLng(-11.0, 114.0), // South-West corner (South of Rote/Timor & West Bali)
            L.latLng(-7.2, 125.6)   // North-East corner (North of Flores/Alor & Bali)
        );

        map = L.map('interactiveMap', {
            preferCanvas: true,
            center: [-8.60, 119.20],
            zoom: 8,
            minZoom: 7,
            maxZoom: 19,
            maxBounds: baliNusaBounds,
            maxBoundsViscosity: 1.0, // 100% Locked: prevents dragging off-screen outside Bali Nusra
            zoomControl: true
        });

        // Automatically fit exact Bali & Nusa Tenggara region bounds on initial render
        map.fitBounds(baliNusaBounds, { padding: [10, 10] });

        // Base Tile Layer (Dark Matter for Points Mode, Light for Others)
        if (currentVisualMode === 'points') {
            darkLayer = L.tileLayer(tileProviders.dark.url, tileProviders.dark.options).addTo(map);
        } else {
            lightLayer = L.tileLayer(tileProviders.light.url, tileProviders.light.options).addTo(map);
        }

        // Add Permanent Province / Island Text Overlays (High-Contrast for Dark/Light Maps)
        const provinceLabels = [
            { name: 'BALI', lat: -8.35, lng: 115.15 },
            { name: 'NUSA TENGGARA<br>BARAT', lat: -8.28, lng: 117.45 },
            { name: 'NUSA TENGGARA<br>TIMUR', lat: -8.35, lng: 122.35 }
        ];

        provinceLabels.forEach(prov => {
            const labelIcon = L.divIcon({
                className: 'map-region-label',
                html: `<div style="text-align:center; font-weight:800; font-size:11px; color:#cbd5e1; letter-spacing:0.08em; text-transform:uppercase; text-shadow:0 1px 4px rgba(0,0,0,0.9), 0 0 10px #000000;">${prov.name}</div>`,
                iconSize: [140, 30],
                iconAnchor: [70, 15]
            });
            L.marker([prov.lat, prov.lng], { icon: labelIcon, interactive: false }).addTo(map);
        });

        // Re-render radii on deep zoom in scatter mode
        map.on('zoomend', () => {
            if (currentVisualMode === 'points' || currentVisualMode === 'street' || currentVisualMode === 'satellite') {
                updatePointsRadius();
            }
        });
    }

    function flyToLocation(lat, lng, zoom) {
        if (map) {
            map.flyTo([lat, lng], zoom, {
                animate: true,
                duration: 1.2
            });
        }
    }

    // =====================================================================
    // SWITCH VISUALIZATION MODES (Cluster, Points, Satellite)
    // =====================================================================
    function setVisualMode(mode) {
        currentVisualMode = mode;
        const btnCluster = document.getElementById('btnModeCluster');
        const btnPoints = document.getElementById('btnModePoints');

        const allBtns = [btnCluster, btnPoints];
        allBtns.forEach(b => {
            if (b) b.className = 'w-full px-5 py-2.5 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center cursor-pointer';
        });

        const activeBtn = mode === 'cluster' ? btnCluster : btnPoints;
        if (activeBtn) activeBtn.className = 'w-full px-5 py-2.5 rounded-xl bg-[#C8102E] text-white font-extrabold text-xs shadow-md ring-2 ring-white/60 transition-all flex items-center justify-center cursor-pointer';

        // Tile layer adjustment
        if (darkLayer && map.hasLayer(darkLayer)) map.removeLayer(darkLayer);
        if (lightLayer && map.hasLayer(lightLayer)) map.removeLayer(lightLayer);
        if (satelliteLayer && map.hasLayer(satelliteLayer)) map.removeLayer(satelliteLayer);

        if (mode === 'points') {
            darkLayer = L.tileLayer(tileProviders.dark.url, tileProviders.dark.options).addTo(map);
        } else {
            lightLayer = L.tileLayer(tileProviders.light.url, tileProviders.light.options).addTo(map);
        }

        renderMapLayers();
    }

    function getAdaptiveRadius() {
        if (!map) return 4;
        const z = map.getZoom();
        if (z <= 8) return 3.5;
        if (z <= 11) return 4.5;
        if (z <= 14) return 6.0;
        if (z <= 16) return 7.5;
        return 9.0;
    }

    function updatePointsRadius() {
        if (!pointsLayer) return;
        const r = getAdaptiveRadius();
        pointsLayer.eachLayer(layer => {
            if (layer.setRadius) {
                layer.setRadius(r);
            }
        });
    }

    // =====================================================================
    // RENDER CLUSTERS, SCATTER POINTS & HEATMAP (ROBUST & FAIL-SAFE)
    // =====================================================================
    function renderMapLayers() {
        if (!map || !rawMarkers || rawMarkers.length === 0) return;

        try {
            if (clusterLayer && map.hasLayer(clusterLayer)) map.removeLayer(clusterLayer);
        } catch(e) {}
        try {
            if (pointsLayer && map.hasLayer(pointsLayer)) map.removeLayer(pointsLayer);
        } catch(e) {}
        try {
            if (heatmapLayer && map.hasLayer(heatmapLayer)) map.removeLayer(heatmapLayer);
        } catch(e) {}

        const radius = getAdaptiveRadius();

        // 1. THERMAL HEATMAP MODE
        if (currentVisualMode === 'heatmap') {
            if (typeof L.heatLayer !== 'undefined') {
                const heatPoints = [];
                for (let i = 0; i < rawMarkers.length; i++) {
                    const m = rawMarkers[i];
                    const lat = parseFloat(m.latitude);
                    const lng = parseFloat(m.longitude);
                    if (isNaN(lat) || isNaN(lng) || lat > -6.0 || lat < -12.0 || lng < 113.0 || lng > 127.0) continue;
                    heatPoints.push([lat, lng, 0.7]);
                }
                heatmapLayer = L.heatLayer(heatPoints, {
                    radius: 20,
                    blur: 15,
                    maxZoom: 16,
                    max: 1.0,
                    gradient: {
                        0.2: '#3B82F6',
                        0.4: '#10B981',
                        0.6: '#F59E0B',
                        0.8: '#ED1C24',
                        1.0: '#FFFFFF'
                    }
                }).addTo(map);
                return;
            }
        }

        // 2. SCATTER POINTS / STREET / SATELLITE MODE (Direct Canvas Rendering)
        if (currentVisualMode === 'points' || currentVisualMode === 'street' || currentVisualMode === 'satellite' || typeof L.markerClusterGroup === 'undefined') {
            pointsLayer = L.layerGroup();

            for (let i = 0; i < rawMarkers.length; i++) {
                const m = rawMarkers[i];
                const lat = parseFloat(m.latitude);
                const lng = parseFloat(m.longitude);
                if (isNaN(lat) || isNaN(lng) || lat > -6.0 || lat < -12.0 || lng < 113.0 || lng > 127.0) continue;

                const flagInfo = getFlagInfo(m.flag_omzet);
                const marker = L.circleMarker([lat, lng], {
                    radius: radius,
                    fillColor: flagInfo.hex,
                    color: '#FFFFFF',
                    weight: 1.2,
                    opacity: 1.0,
                    fillOpacity: 0.95
                });

                marker.bindTooltip(`<b>${m.id_outlet}</b><br><span style="color:${flagInfo.hex}">${flagInfo.formatted}</span>`, {
                    direction: 'top',
                    offset: [0, -4],
                    opacity: 0.95
                });

                marker.on('click', (e) => {
                    L.DomEvent.stopPropagation(e);
                    showOutletDetail(m);
                });

                pointsLayer.addLayer(marker);
            }

            pointsLayer.addTo(map);
            return;
        }

        // 3. CLUSTER MODE (MarkerClusterGroup with Fallback)
        try {
            clusterLayer = L.markerClusterGroup({
                chunkedLoading: true,
                maxClusterRadius: 45,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                disableClusteringAtZoom: 14,
                iconCreateFunction: function(cluster) {
                    const count = cluster.getChildCount();
                    let bgColor = '#ED1C24';
                    if (count < 100) bgColor = '#F59E0B';
                    else bgColor = '#ED1C24';

                    const size = count >= 1000 ? 44 : (count >= 100 ? 36 : 28);
                    const formattedCount = count.toLocaleString('id-ID');

                    return L.divIcon({
                        html: `<div style="
                            background-color: ${bgColor};
                            width: ${size}px;
                            height: ${size}px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border-radius: 50%;
                            color: #ffffff;
                            font-family: 'Plus Jakarta Sans', sans-serif;
                            font-weight: 800;
                            font-size: ${size >= 40 ? '11px' : (size >= 32 ? '10px' : '9px')};
                            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25), 0 0 0 2.5px rgba(255, 255, 255, 0.9);
                            cursor: pointer;
                        ">${formattedCount}</div>`,
                        className: 'custom-cluster-badge',
                        iconSize: L.point(size, size),
                        iconAnchor: [size / 2, size / 2]
                    });
                }
            });

            for (let i = 0; i < rawMarkers.length; i++) {
                const m = rawMarkers[i];
                const lat = parseFloat(m.latitude);
                const lng = parseFloat(m.longitude);
                if (isNaN(lat) || isNaN(lng) || lat > -6.0 || lat < -12.0 || lng < 113.0 || lng > 127.0) continue;

                const flagInfo = getFlagInfo(m.flag_omzet);
                const marker = L.circleMarker([lat, lng], {
                    radius: 5.0,
                    fillColor: flagInfo.hex,
                    color: '#FFFFFF',
                    weight: 1.5,
                    opacity: 1.0,
                    fillOpacity: 0.95
                });

                marker.bindTooltip(`<b>${m.id_outlet}</b><br><span style="color:${flagInfo.hex}">${flagInfo.formatted}</span>`, {
                    direction: 'top',
                    offset: [0, -4],
                    opacity: 0.95
                });

                marker.on('click', (e) => {
                    L.DomEvent.stopPropagation(e);
                    showOutletDetail(m);
                });

                clusterLayer.addLayer(marker);
            }

            clusterLayer.addTo(map);

        } catch (err) {
            console.error("Cluster mode error, falling back to direct scatter:", err);
            pointsLayer = L.layerGroup();
            for (let i = 0; i < rawMarkers.length; i++) {
                const m = rawMarkers[i];
                const lat = parseFloat(m.latitude);
                const lng = parseFloat(m.longitude);
                if (isNaN(lat) || isNaN(lng) || lat > -6.0 || lat < -12.0 || lng < 113.0 || lng > 127.0) continue;

                const flagInfo = getFlagInfo(m.flag_omzet);
                const marker = L.circleMarker([lat, lng], {
                    radius: radius,
                    fillColor: flagInfo.hex,
                    color: '#FFFFFF',
                    weight: 1.2,
                    opacity: 1.0,
                    fillOpacity: 0.95
                });

                marker.bindTooltip(`<b>${m.id_outlet}</b><br><span style="color:${flagInfo.hex}">${flagInfo.formatted}</span>`, {
                    direction: 'top',
                    offset: [0, -4],
                    opacity: 0.95
                });

                marker.on('click', (e) => {
                    L.DomEvent.stopPropagation(e);
                    showOutletDetail(m);
                });

                pointsLayer.addLayer(marker);
            }
            pointsLayer.addTo(map);
        }
    }

    // =====================================================================
    // ASYNC DATA LOADER
    // =====================================================================
    function loadMarkersAsync() {
        const searchParams = new URLSearchParams(window.location.search);
        const url = `{{ route('regional-map.markers') }}?${searchParams.toString()}`;

        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res && res.data) {
                    rawMarkers = res.data;

                    const countLabel = document.getElementById('markersCountLabel');
                    if (countLabel) {
                        countLabel.textContent = `Total Outlet: ${rawMarkers.length.toLocaleString('id-ID')}`;
                    }

                    initInteractiveMap();
                    renderMapLayers();
                    autoFocusFilteredTerritory();
                }
            })
            .catch(err => {
                console.error("Gagal memuat markers:", err);
            });
    }

    // =====================================================================
    // AUTO FOCUS TERRITORY / ISLAND ON FILTER SELECTION
    // =====================================================================
    function autoFocusFilteredTerritory() {
        if (!map) return;

        const urlParams = new URLSearchParams(window.location.search);
        const branch = urlParams.get('branch') || 'all';
        const cluster = urlParams.get('cluster') || 'all';

        // 1. Direct Cluster Exact Matching Dictionary (All 12 Regional Clusters)
        const clusterPresets = {
            'BALI BARAT': [[-8.55, 114.43], [-8.10, 115.15]],
            'BALI TENGAH': [[-8.85, 115.08], [-8.40, 115.35]],
            'BALI TIMUR': [[-8.68, 115.30], [-8.10, 115.75]],
            'LOMBOK': [[-8.92, 115.95], [-8.22, 116.75]],
            'SUMBAWA BARAT': [[-9.12, 116.70], [-8.35, 117.80]],
            'SUMBAWA TIMUR': [[-8.95, 117.75], [-8.15, 119.30]],
            'MANGGARAI': [[-8.95, 119.70], [-8.25, 120.95]],
            'ENDE SIKKA': [[-8.95, 120.90], [-8.35, 122.65]],
            'FLORES TIMUR': [[-8.60, 122.50], [-8.15, 124.00]],
            'SUMBA': [[-10.35, 118.90], [-9.20, 120.90]],
            'KUPANG ROTE': [[-10.95, 122.70], [-9.80, 124.20]],
            'MALAKA TIMTIM BELU': [[-10.15, 124.00], [-8.90, 125.25]]
        };

        const targetClusterUpper = cluster.toUpperCase().trim();
        if (cluster !== 'all' && clusterPresets[targetClusterUpper]) {
            map.flyToBounds(clusterPresets[targetClusterUpper], {
                padding: [35, 35],
                maxZoom: 12,
                duration: 1.4
            });
            return;
        }

        // 2. Fallback Sub-string Match for Clusters & Branches
        const targetRegion = (cluster !== 'all' ? cluster : branch).toUpperCase().trim();
        const islandPresets = {
            // BALI
            'BALI': [[-8.88, 114.43], [-8.06, 115.71]],
            'DENPASAR': [[-8.73, 115.15], [-8.55, 115.28]],
            'BADUNG': [[-8.82, 115.12], [-8.42, 115.26]],
            'TABANAN': [[-8.68, 114.95], [-8.28, 115.18]],
            'SINGARAJA': [[-8.32, 114.65], [-8.08, 115.42]],
            'BULELENG': [[-8.32, 114.65], [-8.08, 115.42]],
            'GIANYAR': [[-8.62, 115.24], [-8.42, 115.35]],
            'KLUNGKUNG': [[-8.78, 115.35], [-8.48, 115.55]],
            'KARANGASEM': [[-8.58, 115.48], [-8.28, 115.72]],
            'BANGLI': [[-8.55, 115.30], [-8.15, 115.45]],
            'JEMBRANA': [[-8.45, 114.43], [-8.15, 114.90]],

            // NTB
            'LOMBOK': [[-8.92, 115.95], [-8.22, 116.75]],
            'MATARAM': [[-8.65, 116.05], [-8.53, 116.18]],
            'SUMBAWA': [[-9.10, 116.70], [-8.30, 119.30]],
            'BIMA': [[-8.90, 118.40], [-8.20, 119.30]],
            'DOMPU': [[-8.85, 118.10], [-8.25, 118.60]],

            // NTT
            'FLORES': [[-8.90, 119.70], [-8.15, 124.00]],
            'MANGGARAI': [[-8.95, 119.70], [-8.25, 120.95]],
            'ENDE': [[-8.95, 120.90], [-8.35, 122.65]],
            'SIKKA': [[-8.80, 122.00], [-8.45, 122.65]],
            'KUPANG': [[-10.40, 123.40], [-9.80, 124.20]],
            'TIMOR': [[-10.40, 123.40], [-8.90, 125.25]],
            'BELU': [[-9.30, 124.70], [-8.90, 125.25]],
            'MALAKA': [[-9.75, 124.70], [-9.20, 125.10]],
            'SUMBA': [[-10.35, 118.90], [-9.20, 120.90]],
            'ROTE': [[-10.90, 122.80], [-10.35, 123.40]],
            'ALOR': [[-8.50, 124.00], [-8.00, 125.20]]
        };

        for (const key in islandPresets) {
            if (targetRegion.includes(key)) {
                map.flyToBounds(islandPresets[key], {
                    padding: [30, 30],
                    maxZoom: 12,
                    duration: 1.4
                });
                return;
            }
        }

        // 3. Fallback: Calculate bounds from filtered raw markers (with outlier filtering)
        if ((branch !== 'all' || cluster !== 'all') && rawMarkers && rawMarkers.length > 0) {
            let minLat = 90, maxLat = -90, minLng = 180, maxLng = -180;
            let validCount = 0;

            for (let i = 0; i < rawMarkers.length; i++) {
                const lat = parseFloat(rawMarkers[i].latitude);
                const lng = parseFloat(rawMarkers[i].longitude);
                if (!isNaN(lat) && !isNaN(lng) && lat >= -11.5 && lat <= -7.0 && lng >= 113.0 && lng <= 126.0) {
                    if (lat < minLat) minLat = lat;
                    if (lat > maxLat) maxLat = lat;
                    if (lng < minLng) minLng = lng;
                    if (lng > maxLng) maxLng = lng;
                    validCount++;
                }
            }

            if (validCount > 0) {
                const bounds = L.latLngBounds([minLat, minLng], [maxLat, maxLng]);
                map.flyToBounds(bounds, {
                    padding: [35, 35],
                    maxZoom: 12,
                    duration: 1.4
                });
                return;
            }
        }

        // 4. Default: Fit Entire Bali Nusra Bounding Box
        const baliNusaBounds = L.latLngBounds(
            L.latLng(-11.0, 114.0),
            L.latLng(-7.2, 125.6)
        );
        map.flyToBounds(baliNusaBounds, { padding: [10, 10], duration: 1.4 });
    }

    // =====================================================================
    // DETAIL MODAL (EXACT MATCHING USER DESIGN)
    // =====================================================================
    function showOutletDetail(outlet) {
        document.getElementById('modalIdOutlet').textContent = outlet.id_outlet;
        document.getElementById('modalOmzet').textContent = outlet.formatted_omzet || formatRupiah(outlet.total_omzet);
        
        const flagInfo = getFlagInfo(outlet.flag_omzet);
        const flagEl = document.getElementById('modalFlag');
        flagEl.textContent = outlet.formatted_flag || flagInfo.formatted;
        flagEl.style.color = flagInfo.hex;

        const badgeEl = document.getElementById('modalStatusBadge');
        if (badgeEl) {
            badgeEl.textContent = flagInfo.label;
            badgeEl.style.borderColor = flagInfo.hex + '35';
            badgeEl.style.color = flagInfo.hex;
            badgeEl.style.backgroundColor = flagInfo.hex + '12';
        }

        const iconBadge = document.getElementById('modalColorBadge');
        iconBadge.style.backgroundColor = flagInfo.hex;

        document.getElementById('modalBranch').textContent = outlet.branch || '-';
        document.getElementById('modalCluster').textContent = outlet.cluster || '-';
        document.getElementById('modalKabupaten').textContent = outlet.kabupaten || '-';
        
        const lngFormatted = Number(outlet.longitude || 0).toFixed(4);
        const latFormatted = Number(outlet.latitude || 0).toFixed(7);
        document.getElementById('modalCoords').textContent = `${lngFormatted}, ${latFormatted}`;

        document.getElementById('modalGmapsLink').href = `https://www.google.com/maps?q=${outlet.latitude},${outlet.longitude}`;

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // =====================================================================
    // IMPORT & CRUD MODALS
    // =====================================================================
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }
    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handlePetaFileSelect(input) {
        const fileNameDisplay = document.getElementById('petaFileNameDisplay');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const text = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            if (fileNameDisplay) fileNameDisplay.textContent = text;
        } else {
            if (fileNameDisplay) fileNameDisplay.textContent = '';
        }
    }

    function openAddModal() {
        const form = document.getElementById('outletForm');
        form.action = "{{ route('regional-map.manual') }}";
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('formModalTitle').textContent = 'Tambah Outlet Baru';

        document.getElementById('formIdOutlet').value = '';
        document.getElementById('formLongitude').value = '';
        document.getElementById('formLatitude').value = '';
        document.getElementById('formBranch').value = '';
        document.getElementById('formCluster').value = '';
        document.getElementById('formKabupaten').value = '';
        document.getElementById('formTotalOmzet').value = '';
        document.getElementById('formFlagOmzet').value = '';

        document.getElementById('outletFormModal').classList.remove('hidden');
    }

    function openEditModal(outlet) {
        const form = document.getElementById('outletForm');
        form.action = `/regional-map/${outlet.id}`;
        document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('formModalTitle').textContent = `Edit Outlet: ${outlet.id_outlet}`;

        document.getElementById('formIdOutlet').value = outlet.id_outlet;
        document.getElementById('formLongitude').value = outlet.longitude;
        document.getElementById('formLatitude').value = outlet.latitude;
        document.getElementById('formBranch').value = outlet.branch;
        document.getElementById('formCluster').value = outlet.cluster;
        document.getElementById('formKabupaten').value = outlet.kabupaten;
        document.getElementById('formTotalOmzet').value = outlet.total_omzet;
        document.getElementById('formFlagOmzet').value = outlet.flag_omzet;

        document.getElementById('outletFormModal').classList.remove('hidden');
    }

    function closeOutletFormModal() {
        document.getElementById('outletFormModal').classList.add('hidden');
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
    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        initInteractiveMap();
        loadMarkersAsync();

        // Restore KPI SBP Submenu state
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

    // Custom Dropdown Toggle Logic for Peta Regional Filter Bar
    function submitMapFilter(type, val) {
        if (type === 'branch') document.getElementById('mapBranchInput').value = val;
        if (type === 'cluster') document.getElementById('mapClusterInput').value = val;
        if (type === 'flag') document.getElementById('mapFlagInput').value = val;
        document.getElementById('filterForm').submit();
    }

    function toggleMapBranchMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('mapBranchMenu');
        const arrow = document.getElementById('mapBranchArrow');
        const clusterMenu = document.getElementById('mapClusterMenu');
        const flagMenu = document.getElementById('mapFlagMenu');
        if (clusterMenu) clusterMenu.classList.add('hidden');
        if (flagMenu) flagMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleMapClusterMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('mapClusterMenu');
        const arrow = document.getElementById('mapClusterArrow');
        const branchMenu = document.getElementById('mapBranchMenu');
        const flagMenu = document.getElementById('mapFlagMenu');
        if (branchMenu) branchMenu.classList.add('hidden');
        if (flagMenu) flagMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleMapFlagMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('mapFlagMenu');
        const arrow = document.getElementById('mapFlagArrow');
        const branchMenu = document.getElementById('mapBranchMenu');
        const clusterMenu = document.getElementById('mapClusterMenu');
        if (branchMenu) branchMenu.classList.add('hidden');
        if (clusterMenu) clusterMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const bContainer = document.getElementById('mapBranchDropdownContainer');
        const bMenu = document.getElementById('mapBranchMenu');
        const bArrow = document.getElementById('mapBranchArrow');
        if (bContainer && !bContainer.contains(e.target) && bMenu && !bMenu.classList.contains('hidden')) {
            bMenu.classList.add('hidden');
            if (bArrow) bArrow.classList.remove('rotate-180');
        }

        const cContainer = document.getElementById('mapClusterDropdownContainer');
        const cMenu = document.getElementById('mapClusterMenu');
        const cArrow = document.getElementById('mapClusterArrow');
        if (cContainer && !cContainer.contains(e.target) && cMenu && !cMenu.classList.contains('hidden')) {
            cMenu.classList.add('hidden');
            if (cArrow) cArrow.classList.remove('rotate-180');
        }

        const fContainer = document.getElementById('mapFlagDropdownContainer');
        const fMenu = document.getElementById('mapFlagMenu');
        const fArrow = document.getElementById('mapFlagArrow');
        if (fContainer && !fContainer.contains(e.target) && fMenu && !fMenu.classList.contains('hidden')) {
            fMenu.classList.add('hidden');
            if (fArrow) fArrow.classList.remove('rotate-180');
        }
    });
</script>
