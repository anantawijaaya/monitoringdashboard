<!-- BAR CHART SECTION: GRAFIK PERBANDINGAN BULAN SEBELUMNYA & BULAN SEKARANG -->
<div class="p-5 sm:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-100">
        <div class="flex items-center gap-1">
            <div>
                <h3 class="text-sm sm:text-base font-black text-gray-900 uppercase tracking-tight">GRAFIK PERBANDINGAN PENDAPATAN CLUSTER</h3>
            </div>
        </div>

        <!-- Right Control Actions: Import Button (Admin Only) -->
        @if(Auth::user()->isAdmin())
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="openGrowthImportModal()" class="px-4 py-2 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-bold shadow-sm hover:shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="bi bi-file-earmark-spreadsheet text-sm"></i>
                    <span>Import Excel Grafik</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Series Toggle Chips / Legend -->
    <div id="chartLegendContainer" class="flex flex-wrap items-center gap-2 sm:gap-3 pt-4 pb-2 text-xs font-semibold">
        <button type="button" onclick="toggleDataset(0)" id="legendBtn-0" class="flex items-center gap-1 transition-all cursor-pointer">
            <span class="w-2.5 h-2.5 rounded-full bg-[#EAB308]"></span>
            <span id="legendTitle-0">Bulan Sebelumnya (M)</span>
        </button>
        <button type="button" onclick="toggleDataset(1)" id="legendBtn-1" class="flex items-center gap-1 transition-all cursor-pointer">
            <span class="w-2.5 h-2.5 rounded-full bg-[#059669]"></span>
            <span id="legendTitle-1">Bulan Sekarang (M)</span>
        </button>
    </div>

    <!-- Chart Container -->
    <div class="relative w-full h-[320px] sm:h-[370px] mt-3">
        <canvas id="revenueGrowthChart"></canvas>
    </div>
</div>
