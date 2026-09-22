<!-- ==================== EDIT HIERARCHY MODAL ==================== -->
<div id="editHierarchyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 max-w-lg w-full overflow-hidden animate-fade-in relative">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-[#ED1C24] to-[#C8102E] text-white p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl backdrop-blur-sm shadow-sm">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h3 class="text-base font-black tracking-tight leading-tight">Edit Data Cluster & Outlet</h3>
                    <p class="text-xs text-white/80 mt-0.5">Ubah informasi kabupaten, mitra, branch & jumlah outlet</p>
                </div>
            </div>
            <button type="button" onclick="closeEditHierarchyModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form id="editHierarchyForm" onsubmit="submitEditHierarchy(event)" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editOutletId" name="id">

            <!-- Kabupaten / Kota -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Kabupaten / Kota <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="bi bi-geo-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="editKabupaten" name="kabupaten" required
                           class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-bold uppercase">
                </div>
            </div>

            <!-- Cluster & Branch Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Cluster -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Cluster <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="bi bi-building absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="editCluster" name="cluster" required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-bold uppercase">
                    </div>
                </div>

                <!-- Branch -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="bi bi-diagram-3 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="editBranch" name="branch" required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-bold uppercase">
                    </div>
                </div>
            </div>

            <!-- Mitra -->
            <div>
                <label class="block text-xs font-black text-slate-700 mb-1">Nama Mitra <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="bi bi-person-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" id="editMitra" name="mitra" required
                           class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-semibold uppercase">
                </div>
            </div>

            <!-- Jumlah Outlet & Manager Branch Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Jumlah Outlet -->
                <div>
                    <label class="block text-xs font-black text-slate-700 mb-1">Jumlah Outlet <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="bi bi-shop absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="editJumlahOutlet" name="jumlah_outlet" required placeholder="Contoh: 1.139"
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-black">
                    </div>
                </div>

                <!-- Manager Branch -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Manager Branch <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="bi bi-person-check absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="editManagerBranch" name="manager_branch" required
                               class="w-full pl-9 pr-3 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-bold uppercase">
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeEditHierarchyModal()" 
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="saveEditBtn"
                        class="px-5 py-2.5 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-extrabold shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                    <i class="bi bi-check-circle-fill text-sm"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== IMPORT CSV / EXCEL MODAL ==================== -->
<div id="importHierarchyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="rounded-2xl bg-white border border-gray-100 max-w-lg w-full overflow-hidden animate-fade-in relative">
        
        <!-- Modal Header -->
        <div class="rounded-2xl text-white flex items-center justify-between border-gray-900">
            <div class="flex items-center gap-3">
                <div class="bg-black text-white flex items-center justify-center text-xl">
                  
                </div>
                <div>
                    <h3 class="text-base font-black tracking-tight leading-tight"></h3>
                </div>
            </div>
            <button type="button" onclick="closeImportHierarchyModal()" class="w-8 h-8 rounded-xl bg-black/50 text-white flex items-center justify-center transition-all cursor-pointer">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form id="importHierarchyForm" onsubmit="submitImportHierarchy(event)" class="p-6 space-y-4" enctype="multipart/form-data">
            @csrf

            <!-- File Dropzone -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Drop File Dibawah Ini<span class="text-red-500">!</span></label>
                <div class="relative border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-2xl p-6 text-center transition-all bg-gray-50/50 hover:bg-emerald-50/20 cursor-pointer" onclick="document.getElementById('importFile').click()">
                    <input type="file" id="importFile" name="file" accept=".csv,.txt,.xlsx,.xls" required class="hidden" onchange="updateFilePreview(this)">
                    
                    <div class="flex flex-col items-center justify-center space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center text-2xl">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-800" id="importFileName"></span>
                            <p class="text-[11px] text-gray-700 mt-0.5">Format yang didukung : CSV, XLSX (Maks 10 MB)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Mode Selection -->
            <div class="space-y-2 pt-1">
                <label class="block text-xs font-bold text-gray-700">Mode Impor Data</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/50 cursor-pointer text-xs font-semibold text-gray-800 hover:bg-emerald-50">
                        <input type="radio" name="mode" value="append" checked class="text-emerald-600 focus:ring-emerald-500">
                        <span>Tambahkan & Simpan Data Lama</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-gray-200 bg-white cursor-pointer text-xs font-semibold text-gray-800 hover:bg-gray-50">
                        <input type="radio" name="mode" value="replace" class="text-emerald-600 focus:ring-emerald-500">
                        <span>Ganti Semua Data</span>
                    </label>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeImportHierarchyModal()" 
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="submitImportBtn"
                        class="px-5 py-2.5 rounded-xl bg-[#ED1C24] text-white text-xs font-extrabold shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                    <span>Import Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TOAST NOTIFICATION CONTAINER -->
<div id="toastNotification" class="hidden fixed bottom-6 right-6 z-50 max-w-md bg-gray-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-gray-800 flex items-center gap-3 animate-slide-up">
    <div id="toastIcon" class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-base shrink-0">
        <i class="bi bi-check2-circle"></i>
    </div>
    <div class="flex-1">
        <p id="toastMessage" class="text-xs font-bold leading-tight">Data berhasil diproses!</p>
    </div>
    <button type="button" onclick="hideToast()" class="text-gray-400 hover:text-white text-sm">
        <i class="bi bi-x"></i>
    </button>
</div>
