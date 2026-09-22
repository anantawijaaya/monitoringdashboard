<!-- MODAL IMPOR DATA KELAS KPI (CSV / EXCEL) MATCHING RANKING DESIGN -->
<div id="importKpiModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative animate-scale-up space-y-5">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-50 text-[#ED1C24] flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="bi bi-file-earmark-arrow-up-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Impor Data Kelas KPI</h3>
                </div>
            </div>
            <button type="button" onclick="closeImportKpiModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold transition-colors cursor-pointer">&times;</button>
        </div>

        <!-- Download Template Box -->
        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div>
                    <p class="text-xs font-bold text-slate-800">Unduh Format Template</p>
                    <p class="text-[11px] text-slate-500">Sesuai nama kolom dengan format template</p>
                </div>
            </div>
            <a href="{{ route('kelas-kpi.template') }}" class="w-9 h-9 rounded-2xl bg-white text-emerald-600 flex items-center justify-center border border-slate-200 shadow-2xs hover:bg-slate-50 transition-all shrink-0" title="Unduh Template CSV">
                <i class="bi bi-download text-sm"></i>
            </a>
        </div>

        <!-- Modal Form -->
        <form action="{{ route('kelas-kpi.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Import Mode Selection -->
            <div class="space-y-2">
                <label class="text-xs font-extrabold text-slate-700 block">Metode Impor Data:</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-200 hover:border-red-500 cursor-pointer text-xs font-bold text-slate-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30 transition-all">
                        <input type="radio" name="mode" value="append" checked class="accent-red-600">
                        <span>Tambah/Update Data</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-200 hover:border-red-500 cursor-pointer text-xs font-bold text-slate-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30 transition-all">
                        <input type="radio" name="mode" value="replace" class="accent-red-600">
                        <span>Ganti Seluruh Data</span>
                    </label>
                </div>
            </div>

            <!-- File Upload Box -->
            <div class="space-y-2">
                <label class="text-xs font-extrabold text-slate-700 block">Pilih Berkas CSV / Excel:</label>
                <div class="border-2 border-dashed border-slate-200 hover:border-red-400 rounded-2xl p-6 text-center transition-colors cursor-pointer bg-slate-50/50" onclick="document.getElementById('kpiFileInput').click()">
                    <i class="bi bi-cloud-arrow-up-fill text-3xl text-slate-400 block mb-2"></i>
                    <span id="kpiFileNameDisplay" class="text-xs font-bold text-slate-700 block"></span>
                    <span class="text-[10px] text-slate-400 block mt-1">Format yang didukung : CSV, XLSX (Maks 10 MB)</span>
                    <input type="file" id="kpiFileInput" name="file" accept=".csv, .txt, .xls, .xlsx" class="hidden" required onchange="handleFileSelect(this)">
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeImportKpiModal()" class="px-4 py-2 rounded-xl text-xs font-extrabold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">Batal</button>
                <button type="submit" id="btnSubmitKpiImport" class="px-6 py-2.5 rounded-full bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-2 shadow-md cursor-pointer">
                    <span>Impor Data</span>
                </button>
            </div>
        </form>
    </div>
</div>
