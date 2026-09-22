<!-- ==================== MODAL IMPOR DATA PERINGKAT ==================== -->
<div id="importRankingModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-200">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full mx-4 shadow-2xl border border-slate-100 relative space-y-6">
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-xl font-bold">
                    
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Impor Data Peringkat Revenue</h3>
                    <p class="text-xs text-slate-500 font-medium"></p>
                </div>
            </div>
            <button type="button" onclick="closeImportModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold transition-colors cursor-pointer">&times;</button>
        </div>

        <!-- Download Template Box -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                
                <div>
                    <p class="text-xs font-bold text-slate-800 ">Unduh Format Template</p>
                    <p class="text-[11px] text-slate-500">Sesuaikan nama kolom dengan format template</p>
                </div>
            </div>
            <a href="{{ route('ranking.template') }}" class="px-3.5 py-1.5 rounded-xl bg-white text-slate-700 text-xs font-extrabold border border-slate-200 shadow-2xs transition-all flex items-center gap-1.5">
                <i class="bi bi-download text-xs text-emerald-600"></i>
            </a>
        </div>

        <!-- Form Upload -->
        <form id="importRankingForm" action="{{ route('ranking.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <!-- Opsi Mode Impor -->
            <div class="space-y-2">
                <label class="text-xs font-extrabold text-slate-700 block">Metode Impor Data:</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:border-red-500 cursor-pointer text-xs font-bold text-slate-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30 transition-all">
                        <input type="radio" name="mode" value="append" checked class="accent-red-600">
                        <span>Tambah/Update Data</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:border-red-500 cursor-pointer text-xs font-bold text-slate-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30 transition-all">
                        <input type="radio" name="mode" value="replace" class="accent-red-600">
                        <span>Ganti Seluruh Data</span>
                    </label>
                </div>
            </div>

            <!-- Input File -->
            <div class="space-y-2">
                <label class="text-xs font-extrabold text-slate-700 block">Pilih Berkas CSV / Excel:</label>
                <div class="border-2 border-dashed border-slate-200 hover:border-red-400 rounded-2xl p-6 text-center transition-colors cursor-pointer bg-slate-50/50" onclick="document.getElementById('rankingFileInput').click()">
                    <i class="bi bi-cloud-arrow-up-fill text-3xl text-slate-400 block mb-2"></i>
                    <span id="rankingFileNameDisplay" class="text-xs font-bold text-slate-700 block"></span>
                    <span class="text-[10px] text-slate-400 block mt-1">Format yang didukung : CSV, XLSX (Maks 10 MB)</span>
                    <input type="file" id="rankingFileInput" name="file" accept=".csv, .txt, .xls, .xlsx" class="hidden" onchange="updateRankingFileName(this)">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 rounded-xl text-xs font-extrabold text-slate-600 hover:bg-slate-100 transition-colors">Batal</button>
                <button type="submit" id="btnSubmitRankingImport" class="px-5 py-2.5 rounded-xl bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-2 shadow-md cursor-pointer">
                    <span>Impor Data</span>
                </button>
            </div>
        </form>
    </div>
</div>
