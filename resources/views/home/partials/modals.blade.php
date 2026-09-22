<!-- MODAL 1: FORM INPUT SESUAI SPESIFIKASI DENGAN KALKULASI OTOMATIS PERSENTASE -->
<div id="manualModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-gray-100 relative animate-scale-up max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-xl shadow-sm">
                    <i class="bi bi-calculator-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Input Data Revenue & Kalkulasi Otomatis</h3>
                    <p class="text-xs text-gray-500">Persentase Target dan Pertumbuhan MoM dihitung otomatis secara live</p>
                </div>
            </div>
            <button onclick="closeManualModal()" class="text-gray-400 hover:text-gray-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Error banner inside modal -->
        <div id="manualErrorAlert" class="hidden mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0"></i>
            <span id="manualErrorMsg"></span>
        </div>

        <form action="{{ route('revenue.manual') }}" method="POST" class="mt-5 space-y-5" id="formRevenueManual" onsubmit="handleManualSubmit(event)">
            @csrf

            <!-- SECTION 1: CLUSTER & PERIODE -->
            <div class="p-4 rounded-2xl bg-gray-50/70 border border-gray-200/80 space-y-3">
                <div class="flex items-center gap-2 text-xs font-black text-gray-800 uppercase tracking-wide">
                    <span class="w-5 h-5 rounded-lg bg-gray-800 text-white flex items-center justify-center text-[10px]">1</span>
                    <span>INFORMASI CLUSTER, KABUPATEN & PERIODE</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kabupaten / Kota <span class="text-red-500">*</span></label>
                        <input type="text" name="kabupaten" id="inKabupaten" onchange="autoFillClusterFromKabupaten()" oninput="autoFillClusterFromKabupaten()" list="kabupatenSuggestions" placeholder="Pilih / ketik kabupaten" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                        <datalist id="kabupatenSuggestions">
                            <option value="BULELENG"></option>
                            <option value="JEMBRANA"></option>
                            <option value="TABANAN"></option>
                            <option value="BADUNG"></option>
                            <option value="KOTA DENPASAR"></option>
                            <option value="BANGLI"></option>
                            <option value="GIANYAR"></option>
                            <option value="KARANG ASEM"></option>
                            <option value="KLUNGKUNG"></option>
                            <option value="ENDE"></option>
                            <option value="SIKKA"></option>
                            <option value="ALOR"></option>
                            <option value="FLORES TIMUR"></option>
                            <option value="LEMBATA"></option>
                            <option value="MANGGARAI"></option>
                            <option value="MANGGARAI BARAT"></option>
                            <option value="MANGGARAI TIMUR"></option>
                            <option value="NAGEKEO"></option>
                            <option value="NGADA"></option>
                            <option value="KOTA KUPANG"></option>
                            <option value="KUPANG"></option>
                            <option value="ROTE NDAO"></option>
                            <option value="BELU"></option>
                            <option value="MALAKA"></option>
                            <option value="TIMOR TENGAH SELATAN"></option>
                            <option value="TIMOR TENGAH UTARA"></option>
                            <option value="SABU RAIJUA"></option>
                            <option value="SUMBA BARAT"></option>
                            <option value="SUMBA BARAT DAYA"></option>
                            <option value="SUMBA TENGAH"></option>
                            <option value="SUMBA TIMUR"></option>
                            <option value="KOTA MATARAM"></option>
                            <option value="LOMBOK BARAT"></option>
                            <option value="LOMBOK TENGAH"></option>
                            <option value="LOMBOK TIMUR"></option>
                            <option value="LOMBOK UTARA"></option>
                            <option value="SUMBAWA"></option>
                            <option value="SUMBAWA BARAT"></option>
                            <option value="BIMA"></option>
                            <option value="KOTA BIMA"></option>
                            <option value="DOMPU"></option>
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nama Cluster <span class="text-gray-400 font-normal">(Otomatis)</span></label>
                        <input type="text" name="cluster_name" id="inClusterName" list="clusterSuggestions" placeholder="Cluster otomatis terisi"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                        <datalist id="clusterSuggestions">
                            <option value="BALI BARAT"></option>
                            <option value="BALI TENGAH"></option>
                            <option value="BALI TIMUR"></option>
                            <option value="ENDE SIKKA"></option>
                            <option value="FLORES TIMUR"></option>
                            <option value="MANGGARAI"></option>
                            <option value="KUPANG ROTE"></option>
                            <option value="MALAKA TIMTIM BELU"></option>
                            <option value="SUMBA"></option>
                            <option value="LOMBOK"></option>
                            <option value="SUMBAWA"></option>
                            <option value="SUMBAWA TIMUR"></option>
                        </datalist>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Periode Bulan <span class="text-red-500">*</span></label>
                        <input type="text" name="period_month" value="Agustus 2026" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                        <input type="number" name="period_year" value="2026" required
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: REVENUE ALL (TARGET & MTD) -->
            <div class="p-4 rounded-2xl bg-red-50/40 border border-red-200/70 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-black text-red-700 uppercase tracking-wide">
                        <span class="w-5 h-5 rounded-lg bg-[#ED1C24] text-white flex items-center justify-center text-[10px]">2</span>
                        <span>REVENUE ALL</span>
                    </div>
                    <span id="badgeAchAll" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                        Pencapaian: 0%
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Target Revenue All (Rp)</label>
                        <input type="number" name="target_revenue_all" id="inTargetAll" oninput="calcAutoPersentase()" placeholder="Contoh: 937000000"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">MTD Revenue All (Rp)</label>
                        <input type="number" name="mtd_revenue_all" id="inMtdAll" oninput="calcAutoPersentase()" placeholder="Contoh: 980000000"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: REVENUE BROADBAND (TARGET & MTD) -->
            <div class="p-4 rounded-2xl bg-blue-50/40 border border-blue-200/70 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-black text-blue-700 uppercase tracking-wide">
                        <span class="w-5 h-5 rounded-lg bg-blue-600 text-white flex items-center justify-center text-[10px]">3</span>
                        <span>REVENUE BROADBAND</span>
                    </div>
                    <span id="badgeAchBb" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                        Pencapaian: 0%
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Target Broadband (Rp)</label>
                        <input type="number" name="target_broadband" id="inTargetBb" oninput="calcAutoPersentase()" placeholder="Contoh: 535000000"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">MTD Broadband (Rp)</label>
                        <input type="number" name="mtd_broadband" id="inMtdBb" oninput="calcAutoPersentase()" placeholder="Contoh: 560000000"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium bg-white">
                    </div>
                </div>
            </div>

            <!-- SECTION 4: REVENUE REDEEM PV (TARGET & MTD) -->
            <div class="p-4 rounded-2xl bg-amber-50/40 border border-amber-200/70 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-black text-amber-700 uppercase tracking-wide">
                        <span class="w-5 h-5 rounded-lg bg-amber-500 text-white flex items-center justify-center text-[10px]">4</span>
                        <span>REVENUE REDEEM (REDEEM PV)</span>
                    </div>
                    <span id="badgeAchRedeem" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                        Pencapaian: 0%
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Target Redeem PV (Rp)</label>
                        <input type="number" name="target_redeem" id="inTargetRedeem" oninput="calcAutoPersentase()" placeholder="Contoh: 402000000"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">MTD Redeem PV (Rp)</label>
                        <input type="number" name="mtd_redeem" id="inMtdRedeem" oninput="calcAutoPersentase()" placeholder="Contoh: 420000000"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium bg-white">
                    </div>
                </div>
            </div>

            <!-- SECTION 5: GROWTH REVENUE (DATA BLN SEBELUMNYA & DATA BULAN SEKARANG -> MoM OTOMATIS) -->
            <div class="p-4 rounded-2xl bg-emerald-50/40 border border-emerald-200/70 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-black text-emerald-700 uppercase tracking-wide">
                        <span class="w-5 h-5 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[10px]">5</span>
                        <span>GROWTH REVENUE (MoM)</span>
                    </div>
                    <span id="badgeMoMGrowth" class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600">
                        MoM: +0.0%
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Data Bln Sebelumnya (Rp)</label>
                        <input type="number" name="revenue_last_month" id="inLastMonth" oninput="calcAutoPersentase()" placeholder="Contoh: 949600000"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Data Bulan Sekarang (Rp)</label>
                        <input type="number" name="revenue_current_month" id="inCurrentMonth" oninput="calcAutoPersentase()" placeholder="Contoh: 980000000"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium bg-white">
                    </div>
                </div>
            </div>

            <!-- Catatan Opsional -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Catatan / Status (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Melampaui Target, Optimal, Perlu Peningkatan"
                       class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium">
            </div>

            <!-- Submit Action Buttons -->
            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
                <button type="button" onclick="closeManualModal()" class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitManual" class="px-6 py-2.5 text-xs font-extrabold text-white bg-[#ED1C24] hover:bg-[#C8102E] rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                    <i class="bi bi-check2-circle text-base"></i>
                    <span>Simpan & Perangkingan Otomatis</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: IMPORT CSV / EXCEL -->
<div id="importModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 relative animate-scale-up">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Import Data CSV / Excel</h3>
                    <p class="text-xs text-gray-500">Unggah file laporan revenue untuk diproses otomatis</p>
                </div>
            </div>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Error banner inside import modal -->
        <div id="importErrorAlert" class="hidden mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0"></i>
            <span id="importErrorMsg"></span>
        </div>

        <form action="{{ route('revenue.import') }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4" id="formRevenueImport" onsubmit="handleImportSubmit(event)">
            @csrf

            <!-- Mode Import Options -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Mode Import Data:</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-start gap-2 p-2.5 rounded-xl border border-gray-200 bg-slate-50 hover:bg-slate-100 cursor-pointer transition-colors">
                        <input type="radio" name="import_mode" value="replace" checked class="mt-0.5 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="font-bold text-gray-800 block text-xs leading-tight">Ganti Data (Replace)</span>
                            <span class="text-[10px] text-gray-500 block">Hapus data lama, gantikan penuh dengan file baru.</span>
                        </div>
                    </label>
                    <label class="flex items-start gap-2 p-2.5 rounded-xl border border-gray-200 bg-slate-50 hover:bg-slate-100 cursor-pointer transition-colors">
                        <input type="radio" name="import_mode" value="append" class="mt-0.5 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="font-bold text-gray-800 block text-xs leading-tight">Tambah / Update (Update)</span>
                            <span class="text-[10px] text-gray-500 block">Perbarui data yang cocok, tambahkan data baru.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- File Input Drop Zone -->
            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-red-400 transition-colors bg-gray-50/50">
                <i class="bi bi-cloud-arrow-up text-3xl text-gray-400 block mb-2"></i>
                <p class="text-xs font-bold text-gray-700">Pilih file CSV atau Excel (.xlsx, .csv)</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Ukuran maksimal file: 10MB</p>
                
                <input type="file" name="file" id="inImportFile" accept=".csv,.xlsx,.xls,.txt" required class="mt-4 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#ED1C24] hover:file:bg-red-100 cursor-pointer">
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-all cursor-pointer">Batal</button>
                <button type="submit" id="btnSubmitImport" class="px-5 py-2.5 text-xs font-bold text-white bg-[#ED1C24] hover:bg-[#C8102E] rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                    <i class="bi bi-upload"></i>
                    <span>Upload & Proses</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: IMPORT EXCEL DATA GRAFIK GROWTH -->
<div id="growthImportModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-1xl max-w-lg w-full p-6">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-xl">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Import Excel Data Grafik Growth</h3>
                    <p class="text-xs text-gray-500">Unggah berkas untuk membandingkan pendapatan bulan lalu vs bulan ini</p>
                </div>
            </div>
            <button onclick="closeGrowthImportModal()" class="text-gray-400 hover:text-gray-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Error Banner -->
        <div id="growthImportErrorAlert" class="hidden mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0"></i>
            <span id="growthImportErrorMsg"></span>
        </div>

        <form action="{{ route('growth.import') }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4" id="formGrowthImport" onsubmit="handleGrowthImportSubmit(event)">
            @csrf

            <!-- File Input Drop Zone -->
            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-red-400 transition-colors bg-gray-50/50">
                <i class="bi bi-cloud-arrow-up text-3xl text-gray-400 block mb-2"></i>
                <p class="text-xs font-bold text-gray-700">Pilih file Excel (.xlsx) atau CSV (.csv)</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Format 4 Kolom: Cluster, Kabupaten, Pendapatan Bulan Lalu, Pendapatan Bulan Ini</p>
                
                <input type="file" name="file" id="inGrowthFile" accept=".csv,.xlsx,.xls,.txt" required class="mt-4 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#ED1C24] hover:file:bg-red-100 cursor-pointer">
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="closeGrowthImportModal()" class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-all cursor-pointer">Batal</button>
                <button type="submit" id="btnSubmitGrowthImport" class="px-5 py-2.5 text-xs font-bold text-white bg-[#ED1C24] hover:bg-[#C8102E] rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                    <i class="bi bi-upload"></i>
                    <span>Upload & Terapkan Grafik</span>
                </button>
            </div>
        </form>
    </div>
</div>
