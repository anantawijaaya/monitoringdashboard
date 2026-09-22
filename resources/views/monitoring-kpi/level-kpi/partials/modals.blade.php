<!-- MODAL ADD LEVEL KPI (ADMIN ONLY) -->
<div id="addModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl p-6 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="bi bi-plus-circle-fill text-red-500"></i>
                <span>Tambah Data Level KPI Cluster</span>
            </h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
        </div>

        <form action="{{ route('level-kpi.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Nama Cluster</label>
                    <input type="text" name="cluster_name" required placeholder="Contoh: DENPASAR" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Periode Bulan</label>
                    <input type="text" name="period_month" required value="Agustus 2026" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    <input type="hidden" name="period_year" value="2026">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Target Rev All (Rp)</label>
                    <input type="number" name="target_rev_all" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">MTD Rev All (Rp)</label>
                    <input type="number" name="mtd_rev_all" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Target Broadband (Rp)</label>
                    <input type="number" name="target_rev_bb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">MTD Broadband (Rp)</label>
                    <input type="number" name="mtd_rev_bb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Target Redeem PV (Rp)</label>
                    <input type="number" name="target_pv" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">MTD Redeem PV (Rp)</label>
                    <input type="number" name="mtd_pv" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Target Revenue RGB (Rp)</label>
                    <input type="number" name="target_rgb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">MTD Revenue RGB (Rp)</label>
                    <input type="number" name="mtd_rgb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Last Month Growth Rev (Rp)</label>
                    <input type="number" name="target_growth_rev" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">This Month Growth Rev (Rp)</label>
                    <input type="number" name="mtd_growth_rev" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Ratio PJP (%)</label>
                    <input type="number" name="ratio_pjp" required min="0" max="100" step="0.1" value="95.0" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Catatan / Status</label>
                    <input type="text" name="notes" placeholder="Contoh: Melampaui Target" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold shadow-lg shadow-red-600/30 transition-all">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT LEVEL KPI (ADMIN ONLY) -->
<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl p-6 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="bi bi-pencil-square text-blue-500"></i>
                <span>Edit Data Level KPI Cluster</span>
            </h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white"><i class="bi bi-x-lg"></i></button>
        </div>

        <form id="editForm" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Nama Cluster</label>
                    <input type="text" id="edit_cluster_name" name="cluster_name" required class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Periode Bulan</label>
                    <input type="text" id="edit_period_month" name="period_month" required class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                    <input type="hidden" id="edit_period_year" name="period_year" value="2026">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Target Rev All (Rp)</label>
                    <input type="number" id="edit_target_rev_all" name="target_rev_all" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">MTD Rev All (Rp)</label>
                    <input type="number" id="edit_mtd_rev_all" name="mtd_rev_all" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Target Broadband (Rp)</label>
                    <input type="number" id="edit_target_rev_bb" name="target_rev_bb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">MTD Broadband (Rp)</label>
                    <input type="number" id="edit_mtd_rev_bb" name="mtd_rev_bb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Target Redeem PV (Rp)</label>
                    <input type="number" id="edit_target_pv" name="target_pv" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">MTD Redeem PV (Rp)</label>
                    <input type="number" id="edit_mtd_pv" name="mtd_pv" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Target Revenue RGB (Rp)</label>
                    <input type="number" id="edit_target_rgb" name="target_rgb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">MTD Revenue RGB (Rp)</label>
                    <input type="number" id="edit_mtd_rgb" name="mtd_rgb" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Last Month Growth Rev (Rp)</label>
                    <input type="number" id="edit_target_growth_rev" name="target_growth_rev" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">This Month Growth Rev (Rp)</label>
                    <input type="number" id="edit_mtd_growth_rev" name="mtd_growth_rev" required min="0" step="1000" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Ratio PJP (%)</label>
                    <input type="number" id="edit_ratio_pjp" name="ratio_pjp" required min="0" max="100" step="0.1" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Catatan / Status</label>
                    <input type="text" id="edit_notes" name="notes" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white focus:outline-none focus:border-red-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold shadow-lg shadow-blue-600/30 transition-all">Update Data</button>
            </div>
        </form>
    </div>
</div>
