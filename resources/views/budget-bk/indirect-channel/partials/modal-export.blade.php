<!-- MODAL EXPORT DATA EXCEL / CSV (TEMA SIMPEL PUTIH TANPA IKON) -->
<div id="exportDataModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300">
    <div class="bg-white border border-slate-200 rounded-3xl w-full max-w-lg p-6 shadow-2xl relative text-slate-800 animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Export Data Indirect Channel</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5"></p>
            </div>
        </div>

        <!-- Modal Form -->
        <form action="{{ route('budget-bk.indirect-channel.export') }}" method="GET" class="space-y-5">
            
            <!-- Scope / Filter Fields -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Cluster Data</label>
                    <select name="cluster" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:border-[#ED1C24] focus:outline-none transition-colors">
                        <option value="all" {{ ($selectedCluster ?? 'all') === 'all' ? 'selected' : '' }}>Semua Cluster</option>
                        @if(isset($clusterMap) && is_array($clusterMap))
                            @foreach($clusterMap as $cKey => $cItem)
                                @if($cKey !== 'all')
                                    <option value="{{ $cKey }}" {{ ($selectedCluster ?? '') === $cKey ? 'selected' : '' }}>{{ $cItem }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Program</label>
                    <select name="program" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs font-semibold rounded-xl px-3.5 py-2.5 focus:border-[#ED1C24] focus:outline-none transition-colors">
                        <option value="all">Semua Program</option>
                        <option value="Digital Marketing">Digital Marketing</option>
                        <option value="CVM Program">CVM Program</option>
                        <option value="Engagement Outlet">Engagement Outlet</option>
                        <option value="Branding Outlet">Branding Outlet</option>
                        <option value="Program Sales Outlet">Program Sales Outlet</option>
                        <option value="Voucher Games Program">Voucher Games Program</option>
                    </select>
                </div>
            </div>

            <!-- Guidance Info Box (Simpel Tanpa Ikon) -->
            <div class="bg-slate-100 rounded-xl p-3.5 text-xs text-slate-600 font-medium">
              
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeExportModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-extrabold rounded-xl shadow-sm transition-all cursor-pointer">
                    Download Data
                </button>
            </div>
        </form>
    </div>
</div>
