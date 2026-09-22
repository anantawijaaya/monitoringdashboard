<!-- MODAL FORM INPUT / EDIT BUDGETING -->
<div id="submitBudgetModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4 transition-all">
    <div class="bg-white rounded-1xl shadow-2xl border border-slate-100 max-w-2xl w-full overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="bg-[#121212] px-6 py-4 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white">
                    <i class="bi bi-file-earmark-plus-fill text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">FORM EDIT BUDGETING</p>
                    <h3 id="modalProgramTitle" class="text-base font-extrabold text-white">Culture Program</h3>
                </div>
            </div>
            <button type="button" onclick="closeSubmitBudgetModal()" class="text-gray-400 hover:text-white transition-colors cursor-pointer p-1">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form id="submitBudgetForm" action="{{ route('budget-bk.culture-program.expense.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateBudgetForm(event)" class="flex-1 overflow-y-auto p-6 space-y-5">
            @csrf
            <input type="hidden" id="inputProgramName" name="program_name" value="Culture Program">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilihan Cluster</label>
                        <select id="inputCluster" name="cluster" onchange="updateModalBudgetByCluster()" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            @foreach($clusterMap as $cKey => $cItem)
                                @if($cKey !== 'all')
                                    <option value="{{ $cKey }}">{{ $cItem }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span>Nama Mitra</span>
                            <i class="bi bi-lock-fill text-slate-400 text-xs" title="Terkunci (Tidak dapat diedit)"></i>
                        </label>
                        <input type="text" id="inputMitra" name="mitra" value="PT AKAR DAYA" readonly class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 focus:outline-none cursor-not-allowed select-none" title="Nama Mitra terkunci secara sistem">
                    </div>

                    <div class="grid grid-cols-2 gap-3 items-end">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between h-7">
                                <span class="whitespace-nowrap">Tanggal Upload</span>
                            </label>
                            <input type="date" id="inputTanggal" name="tanggal" value="{{ date('Y-m-d') }}" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 focus:outline-none cursor-not-allowed select-none" title="Tanggal otomatis secara realtime">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between h-7">
                                <span class="whitespace-nowrap">Waktu Upload</span>
                                <i class="bi bi-lock-fill text-slate-400 text-xs shrink-0" title="Terkunci (Tidak dapat diedit)"></i>
                            </label>
                            <input type="time" id="inputWaktu" name="waktu" step="1" value="{{ date('H:i:s') }}" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 focus:outline-none cursor-not-allowed select-none" title="Waktu otomatis secara realtime">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Kegiatan</label>
                        <textarea id="inputDeskripsi" name="deskripsi" rows="3" placeholder="Tuliskan detail deskripsi kegiatan pengeluaran..." required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 resize-none h-[88px]"></textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                            <span>Budget Program</span>
                            <i class="bi bi-lock-fill text-slate-400 text-xs"></i>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="text" id="displayBudgetProgram" readonly value="0" class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-black text-slate-800">
                            <input type="hidden" id="inputBudgetProgram" name="budget_program" value="0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Pengeluaran</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="text" id="displayNominalPengeluaran" placeholder="0" required oninput="handleNominalInput(this)" class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            <input type="hidden" id="inputNominalPengeluaran" name="nominal_pengeluaran" value="0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Sisa Budget Program</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-emerald-600">Rp</span>
                            <input type="text" id="displaySisaBudgetProgram" readonly value="0" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-black text-emerald-600">
                            <input type="hidden" id="inputSisaBudgetProgram" name="sisa_budget_program" value="0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Evidence Invoice <span class="text-[#ED1C24]">*</span></label>
                        <div class="border-2 border-dashed border-slate-200 hover:border-red-400 rounded-2xl p-4 text-center transition-all bg-slate-50/50">
                            <input type="file" id="evidenceFileInput" name="evidence" accept=".pdf,.jpg,.jpeg,.png" required onchange="handleFileSelect(event)" class="hidden">
                            
                            <div id="fileUploadPrompt" class="cursor-pointer" onclick="document.getElementById('evidenceFileInput').click()">
                                <i class="bi bi-cloud-arrow-up text-2xl text-slate-400 block mb-1"></i>
                                <p class="text-xs font-bold text-slate-700">Upload Dokumen Invoice</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Format: PDF, JPG, PNG (Max 10MB)</p>
                            </div>

                            <div id="fileUploadPreview" class="hidden flex items-center justify-between bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#ED1C24] flex items-center justify-center shrink-0">
                                        <i class="bi bi-file-earmark-check-fill text-lg"></i>
                                    </div>
                                    <div class="text-left overflow-hidden">
                                        <p id="fileNameDisplay" class="text-xs font-bold text-slate-800 truncate max-w-[180px]">invoice.pdf</p>
                                        <p id="fileSizeDisplay" class="text-[10px] text-slate-400">0 KB</p>
                                    </div>
                                </div>
                                <button type="button" onclick="clearSelectedFile(event)" class="text-slate-400 hover:text-rose-600 p-1">
                                    <i class="bi bi-x-circle-fill text-base"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Note / Catatan Perbaikan Field -->
            @if(Auth::check() && in_array(strtolower(Auth::user()->role ?? ''), ['user', 'admin']))
                <div class="pt-2">
                    <label for="inputNote" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Note</span>
                    </label>
                    <textarea id="inputNote" name="note" rows="2" placeholder="Tuliskan catatan perbaikan jika terdapat data pengajuan yang salah..." class="w-full px-3.5 py-2.5 bg-black-50/40 border border-black-200/90 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-black-500 focus:ring-2 focus:ring-black-500/20 outline-none transition-all resize-none h-[64px]"></textarea>
                </div>
            @endif

            <!-- Modal Actions -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeSubmitBudgetModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-extrabold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                    <i class="bi bi-check-lg text-sm"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>

    </div>
</div>
