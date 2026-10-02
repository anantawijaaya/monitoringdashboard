<!-- DAFTAR PENYERAPAN BUDGET TERBARU TABLE SECTION -->
<div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-30">
        <div>
            <h2 class="text-[19px] font-extrabold text-slate-900 tracking-tight">Riwayat Pengelolaan Budget</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5"></p>
        </div>

        <div class="flex flex-wrap items-center gap-3 relative z-30">
            <!-- Tombol Import Data -->
            <?php if(Auth::user() && !Auth::user()->isVisitor()): ?>
            <button type="button" 
                    onclick="openImportModal()"
                    class="flex items-center gap-2 bg-[#ED1C24] hover:bg-[#C8102E] text-white rounded-xl px-3.5 py-2 text-xs font-extrabold shadow-sm hover:shadow transition-all cursor-pointer">
                <span>Import Data</span>
            </button>
            <?php endif; ?>

            <!-- Custom Filter History Cluster (Guaranteed Opens Downward) -->
            <div class="relative z-30" id="historyClusterDropdownContainer">
                <button type="button" 
                        onclick="toggleHistoryClusterMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-sm cursor-pointer min-w-[145px]">
                    <span><?php echo e(($selectedHistoryCluster ?? 'all') === 'all' ? 'Semua Cluster' : ($clusterMap[$selectedHistoryCluster] ?? $selectedHistoryCluster)); ?></span>
                    <i id="historyClusterArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="historyClusterMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    <a href="?cluster=<?php echo e($selectedCluster ?? 'all'); ?>&history_cluster=all&program=<?php echo e($selectedProgram ?? 'all'); ?>&sort=<?php echo e($selectedSort ?? 'default'); ?>"
                       class="block px-4 py-2 text-xs font-bold <?php echo e(($selectedHistoryCluster ?? 'all') === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-slate-700 hover:bg-slate-50'); ?> transition-colors">
                        Semua Cluster
                    </a>
                    <?php $__currentLoopData = $clusterMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cKey => $cItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($cKey !== 'all'): ?>
                            <a href="?cluster=<?php echo e($selectedCluster ?? 'all'); ?>&history_cluster=<?php echo e(urlencode($cKey)); ?>&program=<?php echo e($selectedProgram ?? 'all'); ?>&sort=<?php echo e($selectedSort ?? 'default'); ?>"
                               class="block px-4 py-2 text-xs font-semibold <?php echo e(($selectedHistoryCluster ?? 'all') === $cKey ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-slate-700 hover:bg-slate-50'); ?> transition-colors">
                                <?php echo e($cItem); ?>

                            </a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Custom Filter Program (Guaranteed Opens Downward) -->
            <div class="relative z-30" id="historyProgramDropdownContainer">
                <?php
                    $progLabels = [
                        'all' => 'Semua Program',
                        'culture' => 'Culture Program',
                    ];
                    $currentProgLabel = $progLabels[$selectedProgram ?? 'all'] ?? 'Semua Program';
                ?>
                <button type="button" 
                        onclick="toggleHistoryProgramMenu(event)"
                        class="flex items-center justify-between gap-2.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-sm cursor-pointer min-w-[145px]">
                    <span><?php echo e($currentProgLabel); ?></span>
                    <i id="historyProgramArrow" class="bi bi-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="historyProgramMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                    <?php $__currentLoopData = $progLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pKey => $pLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="?cluster=<?php echo e($selectedCluster ?? 'all'); ?>&history_cluster=<?php echo e($selectedHistoryCluster ?? 'all'); ?>&program=<?php echo e($pKey); ?>&sort=<?php echo e($selectedSort ?? 'default'); ?>"
                           class="block px-4 py-2 text-xs <?php echo e(($selectedProgram ?? 'all') === $pKey ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-slate-700 hover:bg-slate-50 font-semibold'); ?> transition-colors">
                            <?php echo e($pLabel); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Display -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200/80">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-[12px] font-black uppercase text-slate-900 tracking-wider border-b border-slate-200/80">
                    <th class="py-3.5 px-4">Tanggal Upload</th>
                    <th class="py-3.5 px-4">Program</th>
                    <th class="py-3.5 px-4">Deskripsi</th>
                    <th class="py-3.5 px-4">Jumlah</th>
                    <th class="py-3.5 px-4">Bukti</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-semibold text-slate-700">
                                <?php echo e(\Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y')); ?>

                            </div>
                            <div class="text-[10px] text-slate-400">
                                <?php echo e(\Carbon\Carbon::parse($item->waktu)->format('H:i')); ?> WITA
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-semibold text-slate-700"><?php echo e($item->program_name); ?></div>
                            <?php if(!empty($item->cluster)): ?>
                                <div class="text-[10px] text-slate-400"><?php echo e($item->cluster); ?> <?php echo e($item->mitra ?? '-'); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 max-w-xs">
                            <div class="truncate text-slate-700 font-semibold" title="<?php echo e($item->deskripsi); ?>"><?php echo e($item->deskripsi); ?></div>
                            <?php if(!empty($item->note)): ?>
                                <div class="mt-1 text-[10px] text-slate-400 truncate" title="Note: <?php echo e($item->note); ?>">
                                    <span class="truncate"><strong>Note:</strong> <?php echo e($item->note); ?></span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 font-extrabold text-slate-900 whitespace-nowrap">
                            Rp <?php echo e(number_format($item->nominal_pengeluaran, 0, ',', '.')); ?>

                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <?php if($item->evidence_path): ?>
                                <a href="<?php echo e(asset('storage/' . $item->evidence_path)); ?>" target="_blank" class="inline-flex items-center gap-1.5 text-[#ED1C24] hover:text-[#C8102E] font-bold cursor-pointer">
                                    <i class="bi bi-file-earmark-pdf-fill text-sm"></i>
                                    <span><?php echo e($item->evidence_original_name ?? 'Invoice.pdf'); ?></span>
                                </a>
                            <?php else: ?>
                                <span class="text-slate-400 italic text-[11px]">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <?php if(Auth::check() && Auth::user()->isAdmin()): ?>
                                <div class="flex items-center gap-1.5">
                                    <form action="<?php echo e(route('budget-bk.culture-program.expense.status', $item->id)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="status" value="Disetujui">
                                        <button type="submit" title="Setujui Pengajuan" class="p-1.5 rounded-lg <?php echo e($item->status === 'Disetujui' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border border-emerald-200'); ?> transition-colors cursor-pointer flex items-center justify-center">
                                            <i class="bi bi-check-lg text-sm font-bold"></i>
                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('budget-bk.culture-program.expense.status', $item->id)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="status" value="Ditolak">
                                        <button type="submit" title="Tolak Pengajuan" class="p-1.5 rounded-lg <?php echo e($item->status === 'Ditolak' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200'); ?> transition-colors cursor-pointer flex items-center justify-center">
                                            <i class="bi bi-x-lg text-sm font-bold"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <?php if($item->status === 'Disetujui'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-black bg-emerald-600 text-white border border-emerald-200">
                                         Disetujui
                                    </span>
                                <?php elseif($item->status === 'Ditolak'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-black bg-rose-600 text-white border border-rose-200">
                                        Ditolak
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-black bg-amber-600 text-white border border-amber-200">
                                        Pending
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Edit Icon (Pulpen) - Accessible by USER, VISITOR, ADMIN -->
                                <button type="button" data-item="<?php echo e(json_encode($item)); ?>" onclick="openEditBudgetModal(JSON.parse(this.getAttribute('data-item')))" title="Edit Data" class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 transition-colors cursor-pointer flex items-center justify-center">
                                    <i class="bi bi-pencil-square text-sm"></i>
                                </button>

                                <?php if($item->evidence_path): ?>
                                    <!-- Download Icon - Accessible by USER, VISITOR, ADMIN -->
                                    <a href="<?php echo e(asset('storage/' . $item->evidence_path)); ?>" download title="Download Evidence" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 transition-colors flex items-center justify-center">
                                        <i class="bi bi-download text-sm"></i>
                                    </a>
                                    <!-- View PDF Icon - Accessible by USER, VISITOR, ADMIN -->
                                    <a href="<?php echo e(asset('storage/' . $item->evidence_path)); ?>" target="_blank" title="Lihat PDF" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 transition-colors flex items-center justify-center">
                                        <i class="bi bi-eye text-sm"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if(Auth::check() && Auth::user()->isAdmin()): ?>
                                    <!-- Delete Icon - For ADMIN -->
                                    <form action="<?php echo e(route('budget-bk.culture-program.expense.destroy', $item->id)); ?>" method="POST" onsubmit="return confirm('Hapus data pengajuan ini?')" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" title="Hapus Data" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-300 transition-colors cursor-pointer flex items-center justify-center">
                                            <i class="bi bi-trash-fill text-sm"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="py-10 text-center text-slate-400 font-medium">
                            <i class="bi bi-inbox text-3xl block mb-2 opacity-50"></i>
                            Belum ada data pengajuan penyerapan budget.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer (10 items per page) -->
    <?php if(isset($expenses) && method_exists($expenses, 'hasPages') && $expenses->hasPages()): ?>
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end text-xs font-semibold text-slate-600">
            <div class="flex items-center gap-1.5">
                
                <?php if($expenses->onFirstPage()): ?>
                    <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 font-bold text-xs cursor-not-allowed">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                <?php else: ?>
                    <a href="<?php echo e($expenses->previousPageUrl()); ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-colors">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                <?php endif; ?>

                
                <?php $__currentLoopData = $expenses->getUrlRange(1, $expenses->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $expenses->currentPage()): ?>
                        <span class="w-8 h-8 rounded-lg bg-[#ED1C24] text-white font-extrabold text-xs flex items-center justify-center shadow-sm">
                            <?php echo e($page); ?>

                        </span>
                    <?php else: ?>
                        <a href="<?php echo e($url); ?>" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center transition-colors">
                            <?php echo e($page); ?>

                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($expenses->hasMorePages()): ?>
                    <a href="<?php echo e($expenses->nextPageUrl()); ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-colors">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 font-bold text-xs cursor-not-allowed">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
    function toggleHistoryClusterMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('historyClusterMenu');
        const arrow = document.getElementById('historyClusterArrow');
        const progMenu = document.getElementById('historyProgramMenu');
        if (progMenu) progMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleHistoryProgramMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('historyProgramMenu');
        const arrow = document.getElementById('historyProgramArrow');
        const clusterMenu = document.getElementById('historyClusterMenu');
        if (clusterMenu) clusterMenu.classList.add('hidden');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const clusterContainer = document.getElementById('historyClusterDropdownContainer');
        const clusterMenu = document.getElementById('historyClusterMenu');
        const clusterArrow = document.getElementById('historyClusterArrow');
        if (clusterContainer && !clusterContainer.contains(e.target) && clusterMenu && !clusterMenu.classList.contains('hidden')) {
            clusterMenu.classList.add('hidden');
            if (clusterArrow) clusterArrow.classList.remove('rotate-180');
        }

        const progContainer = document.getElementById('historyProgramDropdownContainer');
        const progMenu = document.getElementById('historyProgramMenu');
        const progArrow = document.getElementById('historyProgramArrow');
        if (progContainer && !progContainer.contains(e.target) && progMenu && !progMenu.classList.contains('hidden')) {
            progMenu.classList.add('hidden');
            if (progArrow) progArrow.classList.remove('rotate-180');
        }
    });
</script>
<?php /**PATH C:\Users\Ananta Wijaya\OneDrive\Desktop\WEB PT 2\resources\views/budget-bk/culture-program/partials/table-history.blade.php ENDPATH**/ ?>