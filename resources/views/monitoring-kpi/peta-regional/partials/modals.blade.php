<!-- ========================================================================= -->
<!-- MODAL: OUTLET DETAIL DISPLAY (EXACT DESIGN MATCH) -->
<!-- ========================================================================= -->
<div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200">
    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 max-w-md w-full p-6 space-y-4 transform transition-all">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <!-- Circular Store Icon Badge -->
                <div id="modalColorBadge" class="w-12 h-12 rounded-2xl bg-[#ED1C24] text-white flex items-center justify-center shadow-sm shrink-0">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path d="M4 4h16l1.2 5H2.8L4 4zm-1.8 7h19.6a1 1 0 0 1 1 1.2l-1.5 7.5a2 2 0 0 1-2 1.3H5.7a2 2 0 0 1-2-1.3L2.2 12.2a1 1 0 0 1 1-1.2zm6.8 2a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0v-4a1 1 0 0 0-1-1zm6 0a1 1 0 0 0-1 1v4a1 1 0 0 0 2 0v-4a1 1 0 0 0-1-1z"/>
                    </svg>
                    
                </div>
                <div>
                    <span id="modalIdOutlet" class="text-lg font-black text-gray-900 tracking-tight"></span>
                </div>
            </div>

            <!-- Close "X" Button -->
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <!-- Financial Metric Cards (2 Columns) -->
        <div class="grid grid-cols-2 gap-3 pt-1">
            <!-- Total Omzet Card -->
            <div class="border border-gray-200/80 rounded-2xl p-4 bg-white shadow-sm">
                <span class="text-xs text-gray-500 font-medium block">Total Omzet</span>
                <span id="modalOmzet" class="text-[15px] font-medium mt-1 block tracking-tight"></span>
            </div>
            <!-- Flag Omzet Card -->
            <div class="border border-gray-200/80 rounded-2xl p-4 bg-white shadow-sm">
                <span class="text-xs text-gray-500 font-medium block">Flag Omzet</span>
                <span id="modalFlag" class="text-lg sm:text-xl font-extrabold text-red-600 mt-1 block tracking-tight"></span>
            </div>
        </div>

        <!-- Hierarchy & Region Details List -->
        <div class="space-y-0 text-sm border-t border-gray-100 pt-1">
            <!-- Branch -->
            <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                <span class="text-black-30 font-medium">Branch</span>
                <span id="modalBranch" class="font-medium tracking-wide text-[13px]"></span>
            </div>
            <!-- Cluster -->
            <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                <span class="text-black-30 font-medium">Cluster</span>
                <span id="modalCluster" class="font-medium tracking-wide text-[13px]"></span>
            </div>
            <!-- Kabupaten / Kota -->
            <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                <span class="text-black-30 font-medium">Kabupaten / Kota</span>
                <span id="modalKabupaten" class="font-medium tracking-wide text-[13px]"></span>
            </div>
            <!-- Koordinat GPS -->
            <div class="flex items-center justify-between py-2.5">
                <span class="text-black-30 font-medium">Koordinat GPS</span>
                <span id="modalCoords" class="font-medium text-[13px]"></span>
            </div>
        </div>

        <!-- Action Button: Buka Lokasi di Google Maps -->
        <div class="pt-1">
            <a id="modalGmapsLink" 
               href="#" 
               target="_blank" 
               class="w-full py-3 px-4 bg-white hover:bg-gray-50 text-red-600 font-bold text-xs sm:text-sm rounded-2xl flex items-center justify-center gap-2 border border-gray-200 shadow-sm transition-all">
                <i class="bi bi-geo-alt-fill text-red-600 text-base"></i>
                <span>Buka Lokasi di Google Maps</span>
                
            </a>
        </div>

        <!-- Modal Footer Button -->
        <div class="flex items-center justify-end pt-1">
            <button onclick="closeDetailModal()" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                Tutup
            </button>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: IMPORT DATA OUTLET (MATCHING NEW SCREENSHOT DESIGN) -->
<!-- ========================================================================= -->
@if(!Auth::user()->isVisitor())
<div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative animate-scale-up space-y-5">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-50 text-[#ED1C24] flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="bi bi-file-earmark-arrow-up-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Import Data Peta Regional</h3>
                </div>
            </div>
            <button type="button" onclick="closeImportModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold transition-colors cursor-pointer">&times;</button>
        </div>

        <!-- Download Template Box -->
        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div>
                    <p class="text-xs font-bold text-slate-800">Unduh Format Template</p>
                    <p class="text-[11px] text-slate-500">Sesuai nama kolom dengan format template</p>
                </div>
            </div>
            <a href="{{ route('regional-map.template') }}" class="w-9 h-9 rounded-2xl bg-white text-emerald-600 flex items-center justify-center border border-slate-200 shadow-2xs hover:bg-slate-50 transition-all shrink-0" title="Unduh Template CSV">
                <i class="bi bi-download text-sm"></i>
            </a>
        </div>

        <!-- Modal Form -->
        <form action="{{ route('regional-map.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Import Mode Selection -->
            <div class="space-y-2">
                <label class="text-xs font-extrabold text-slate-700 block">Metode Impor Data:</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-200 hover:border-red-500 cursor-pointer text-xs font-bold text-slate-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30 transition-all">
                        <input type="radio" name="import_mode" value="append" checked class="accent-red-600">
                        <span>Tambah/Update Data</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-200 hover:border-red-500 cursor-pointer text-xs font-bold text-slate-700 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30 transition-all">
                        <input type="radio" name="import_mode" value="replace" class="accent-red-600">
                        <span>Ganti Seluruh Data</span>
                    </label>
                </div>
            </div>

            <!-- File Upload Box -->
            <div class="space-y-2">
                <label class="text-xs font-extrabold text-slate-700 block">Pilih Berkas CSV / Excel:</label>
                <div class="border-2 border-dashed border-slate-200 hover:border-red-400 rounded-2xl p-6 text-center transition-colors cursor-pointer bg-slate-50/50" onclick="document.getElementById('petaFileInput').click()">
                    <i class="bi bi-cloud-arrow-up-fill text-3xl text-slate-400 block mb-2"></i>
                    <span id="petaFileNameDisplay" class="text-xs font-bold text-slate-700 block"></span>
                    <span class="text-[10px] text-slate-400 block mt-1">Format yang didukung : CSV, XLSX (Maks 10 MB)</span>
                    <input type="file" id="petaFileInput" name="file" accept=".csv, .txt, .xls, .xlsx" class="hidden" required onchange="handlePetaFileSelect(this)">
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 rounded-xl text-xs font-extrabold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">Batal</button>
                <button type="submit" id="btnSubmitPetaImport" class="px-6 py-2.5 rounded-full bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-2 shadow-md cursor-pointer">
                    <span>Impor Data</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: ADD / EDIT DATA OUTLET FORM -->
<!-- ========================================================================= -->
<div id="outletFormModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-100 max-w-lg w-full overflow-hidden">
        
        <div class="text-black p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                    <i class="bi bi-geo-fill text-lg text-red-500"></i>
                </div>
                <div>
                    <h4 id="formModalTitle" class="text-base font-extrabold text-black">Tambah Outlet Baru</h4>
                </div>
            </div>
            <button onclick="closeOutletFormModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-black flex items-center justify-center cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="outletForm" method="POST" action="{{ route('regional-map.manual') }}" class="p-6 space-y-3.5 text-xs">
            @csrf
            <div id="methodContainer"></div>

            <div>
                <label class="font-semibold">ID Outlet</label>
                <input type="text" name="id_outlet" id="formIdOutlet" required placeholder="Contoh: OUT-DPS-100" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold">Longitude</label>
                    <input type="number" step="any" name="longitude" id="formLongitude" required placeholder="Contoh: 115.2126" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                </div>

                <div>
                    <label class="font-semibold">Latitude</label>
                    <input type="number" step="any" name="latitude" id="formLatitude" required placeholder="Contoh: -8.6705" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="font-semibold">Branch</label>
                    <input type="text" name="branch" id="formBranch" required placeholder="Branch Denpasar" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                </div>

                <div>
                    <label class="font-semibold">Cluster</label>
                    <input type="text" name="cluster" id="formCluster" required placeholder="Cluster Denpasar Kota" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                </div>

                <div>
                    <label class="font-semibold">Kabupaten / Kota</label>
                    <input type="text" name="kabupaten" id="formKabupaten" required placeholder="Kota Denpasar" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold">Total Omzet (Rp)</label>
                    <input type="number" step="any" name="total_omzet" id="formTotalOmzet" required placeholder="150000000" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                </div>

                <div>
                    <label class="font-semibold">Flag Omzet (%)</label>
                    <input type="number" step="0.01" name="flag_omzet" id="formFlagOmzet" required placeholder="Contoh: 3.50 (atau -1.2)" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-red-500 outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeOutletFormModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-gray-900 hover:bg-[#ED1C24] text-white font-bold rounded-xl transition-all shadow-md cursor-pointer">
                    Simpan Data Outlet
                </button>
            </div>
        </form>

    </div>
</div>
@endif
