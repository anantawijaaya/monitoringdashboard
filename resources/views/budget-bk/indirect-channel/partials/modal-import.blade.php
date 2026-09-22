<!-- MODAL IMPORT DATA EXCEL / CSV (TEMA SIMPEL PUTIH TANPA IKON) -->
<div id="importDataModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300">
    <div class="bg-white border border-slate-200 rounded-xl w-full max-w-lg p-6 shadow-2xl relative text-slate-800 animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Import Data Indirect Channel</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">.</p>
            </div>
        </div>

        <!-- Modal Form -->
        <form action="{{ route('budget-bk.indirect-channel.import') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            
            <!-- File Dropzone -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider"></label>
                <div id="importDropzone" onclick="document.getElementById('importFileInput').click()" class="border-2 border-slate-300 hover:border-[#ED1C24] bg-slate-50 hover:bg-slate-100/80 p-6 text-center cursor-pointer transition-all group">
                    
                    <div id="importFilePrompt" class="space-y-2">
                        <div class="text-xs font-semibold text-slate-300">
                            Klik dan Unggah file disini dengan format xlsx/csv (max 10mb)
                        </div>
                        <p class="text-[11px] text-slate-500"></p>
                    </div>

                    <div id="importFilePreview" class="hidden flex items-center justify-between bg-slate-100 p-3 rounded-xl border border-slate-200">
                        <div class="text-left truncate">
                            <div id="importFileNameDisplay" class="text-xs font-extrabold text-slate-800 truncate">nama_file.xlsx</div>
                            <div id="importFileSizeDisplay" class="text-[10px] text-slate-500">0 KB</div>
                        </div>
                        <button type="button" onclick="clearImportFile(event)" class="text-xs font-bold text-rose-600 hover:text-rose-700 px-2 py-1 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                            Hapus
                        </button>
                    </div>

                    <input type="file" id="importFileInput" name="file" accept=".xlsx,.xls,.csv,.zip" class="hidden" onchange="handleImportFileSelect(event)">
                </div>
            </div>

            <!-- Guidance Info Box (Simpel Tanpa Ikon) -->
            <div class="bg-slate-100 rounded-xl p-3.5 text-xs text-slate-600 font-medium">
              
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end border-t border-slate-100">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-extrabold rounded-xl shadow-sm transition-all cursor-pointer">
                    Unggah & Proses
                </button>
            </div>
        </form>
    </div>
</div>
