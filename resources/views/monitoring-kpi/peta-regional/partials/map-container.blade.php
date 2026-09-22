<!-- 4. MAP VIEWPORT CANVAS CONTAINER (DEEP ZOOM GIS) -->
<div class="relative overflow-hidden space-y-3">
    
    <div class="relative w-full overflow-hidden bg-white border border-gray-100">
        <!-- LEAFLET MAP ELEMENT -->
        <div id="interactiveMap"></div>

        <!-- FLOATING MAP LEGEND (Bottom Left - EXACT MATCH) -->
        <div class="absolute bottom-5 left-5 z-[500] bg-white/95 backdrop-blur-md text-gray-800 p-4 rounded-2xl border border-gray-200/80 shadow-lg space-y-2 pointer-events-auto min-w-[190px]">
            <div class="text-[11px] font-extrabold uppercase tracking-wider text-gray-800 pb-1">
                KLASIFIKASI FLAG OMZET
            </div>
            <div class="space-y-1.5 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#18181B] inline-block shrink-0 shadow-sm"></span>
                    <span class="text-gray-700 font-medium text-[11px]">Flag < 0%</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#ED1C24] inline-block shrink-0"></span>
                    <span class="text-gray-700 font-medium text-[11px]">Flag = 0%</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#F97316] inline-block shrink-0"></span>
                    <span class="text-gray-700 font-medium text-[11px]">Flag <= 3%</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#10B981] inline-block shrink-0"></span>
                    <span class="text-gray-700 font-medium text-[11px]">Flag > 3%</span>
                </div>
            </div>
        </div>

        <!-- TOTAL OUTLET BADGE (Bottom Right - EXACT MATCH) -->
        <div class="absolute bottom-5 right-5 z-[500] bg-white/95 backdrop-blur-md text-gray-800 px-4 py-2 rounded-xl border border-gray-200/80 shadow-md text-xs font-extrabold flex items-center gap-2 pointer-events-auto">
            <span id="markersCountLabel">Total Outlet: {{ number_format($totalOutlets, 0, ',', '.') }}</span>
        </div>
    </div>

</div>
