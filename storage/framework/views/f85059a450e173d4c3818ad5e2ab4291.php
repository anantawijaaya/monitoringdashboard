<!-- DATA LENGKAP TABLE CONTAINER -->
<div class="bg-white rounded-3xl border border-gray-100 shadow-card p-5 sm:p-6 space-y-4">
    
    <!-- Table Section Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-100">
        <h2 class="text-[15px] font-black text-gray-900">
            Tabel Hirarki Regional Bali Nusra
        </h2>

        <!-- Action Buttons: Import CSV/Excel, Template, Export Excel & Filter -->
        <div class="flex flex-wrap items-center gap-2.5">
            
            <!-- Button 1: Import CSV / Excel (Admin Only) -->
            <?php if(Auth::user()->isAdmin()): ?>
                <button type="button" onclick="openImportHierarchyModal()" 
                        class="px-4 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-800 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2 cursor-pointer">
                    <span>Import Data</span>
                </button>
            <?php endif; ?>  

            <!-- Button 2: Export Excel -->
            <a href="<?php echo e(route('hierarchy.export', request()->query())); ?>" 
               class="px-3.5 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2">
                <span>Export Data</span>
            </a>

            <!-- Button 4: Filter Toggle Button (Red) -->
            <button type="button" onclick="toggleFilterBox()" id="filterToggleBtn"
                    class="px-4 py-2 rounded-xl bg-[#ED1C24] hover:bg-[#C8102E] text-white text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer">
                <i class="bi bi-funnel-fill text-xs"></i>
                <span>Filter</span>
            </button>
        </div>
    </div>

    <?php echo $__env->make('monitoring-kpi.hierarchy.partials.filter-panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- THE DATA TABLE -->
    <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm">
        <table class="w-full text-left text-xs whitespace-nowrap">
            <!-- Solid Red Header (#ED1C24) -->
            <thead>
                <tr class="bg-[#ED1C24] text-white uppercase text-[11px] font-black tracking-wider select-none">
                    <th class="px-3.5 py-3.5 text-center w-12">NO</th>
                    <th class="px-4 py-3.5">KABUPATEN / KOTA</th>
                    <th class="px-4 py-3.5">CLUSTER</th>
                    <th class="px-4 py-3.5">MITRA</th>
                    <th class="px-4 py-3.5">BRANCH</th>
                    <th class="px-4 py-3.5 text-center">JUMLAH OUTLET</th>
                    <th class="px-4 py-3.5">MANAGER BRANCH</th>
                    <?php if(Auth::user()->isAdmin()): ?>
                        <th class="px-4 py-3.5 text-center">AKSI</th>
                    <?php endif; ?>
                </tr>
            </thead>
            
            <!-- Table Body with Clean White Background -->
            <tbody class="divide-y divide-gray-100 bg-white text-gray-800">
                <?php $__empty_1 = true; $__currentLoopData = $paginatedData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr id="row-<?php echo e($row->id); ?>" class="bg-white hover:bg-gray-50/80 transition-colors">
                        <!-- NO -->
                        <td class="px-3.5 py-3 text-center font-bold text-gray-600 border-r border-slate-200 ">
                            <?php echo e($loop->iteration + ($paginatedData->firstItem() ? $paginatedData->firstItem() - 1 : 0)); ?>

                        </td>

                        <!-- KABUPATEN / KOTA -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            <?php echo e($row->kabupaten); ?>

                        </td>

                        <!-- CLUSTER -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            <?php echo e($row->cluster); ?>

                        </td>

                        <!-- MITRA -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            <?php echo e($row->mitra); ?>

                        </td>

                        <!-- BRANCH -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            <?php echo e($row->branch); ?>

                        </td>

                        <!-- JUMLAH OUTLET -->
                        <td class="px-4 py-3 text-center font-medium border-r border-slate-200">
                            <?php echo e($row->jumlah_outlet); ?>

                        </td>

                        <!-- MANAGER BRANCH -->
                        <td class="px-4 py-3 font-semibold border-r border-slate-200">
                            <?php echo e($row->manager_branch); ?>

                        </td>

                        <!-- AKSI (EDIT BUTTON - Admin Only) -->
                        <?php if(Auth::user()->isAdmin()): ?>
                            <td class="px-4 py-3 text-center">
                                <button type="button" 
                                        onclick='openEditHierarchyModal(<?php echo json_encode($row, 15, 512) ?>)'
                                        class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-red-50 text-gray-700 hover:text-[#ED1C24] border border-gray-200 hover:border-red-300 shadow-sm transition-all text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer"
                                        title="Edit Data <?php echo e($row->kabupaten); ?>">
                                    <i class="bi bi-pencil-square text-[#ED1C24]"></i>
                                    <span>Edit</span>
                                </button>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400 bg-white">
                            <i class="bi bi-inbox text-3xl block mb-2 text-gray-300"></i>
                            <p class="font-bold text-sm text-gray-600">Tidak ada data yang sesuai dengan pencarian / filter.</p>
                            <a href="<?php echo e(route('hierarchy.index')); ?>" class="text-xs text-red-600 font-bold hover:underline mt-1 inline-block">Reset Filter</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- FOOTER CONTROLS & PAGINATION -->
    <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-600 font-medium">
        
        <!-- Left: Info Data Count -->
        <div>

        </div>

        <!-- Right: Per-Page Dropdown & Page Navigation Buttons -->
        <div class="flex items-center gap-3">
            <!-- Per Page Selector Form -->
            <form method="GET" action="<?php echo e(route('hierarchy.index')); ?>" class="flex items-center gap-1.5">
                <?php if(!empty($search)): ?> <input type="hidden" name="search" value="<?php echo e($search); ?>"> <?php endif; ?>
                <?php if($selectedBranch !== 'all'): ?> <input type="hidden" name="branch" value="<?php echo e($selectedBranch); ?>"> <?php endif; ?>
                <?php if($selectedCluster !== 'all'): ?> <input type="hidden" name="cluster" value="<?php echo e($selectedCluster); ?>"> <?php endif; ?>
                

            </form>

            <!-- Pagination Buttons Matching Screenshot (< [1] [2] [3] [4] >) -->
            <div class="flex items-center gap-1">
                <!-- Previous Page -->
                <?php if($paginatedData->onFirstPage()): ?>
                    <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-300 flex items-center justify-center text-xs cursor-not-allowed">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                <?php else: ?>
                    <a href="<?php echo e($paginatedData->previousPageUrl()); ?>" class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 flex items-center justify-center text-xs font-bold transition-colors">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php for($p = 1; $p <= $paginatedData->lastPage(); $p++): ?>
                    <?php if($p == $paginatedData->currentPage()): ?>
                        <span class="w-8 h-8 rounded-lg bg-[#ED1C24] text-white flex items-center justify-center text-xs font-black shadow-sm">
                            <?php echo e($p); ?>

                        </span>
                    <?php else: ?>
                        <a href="<?php echo e($paginatedData->url($p)); ?>" class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 flex items-center justify-center text-xs font-bold transition-colors">
                            <?php echo e($p); ?>

                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next Page -->
                <?php if($paginatedData->hasMorePages()): ?>
                    <a href="<?php echo e($paginatedData->nextPageUrl()); ?>" class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 flex items-center justify-center text-xs font-bold transition-colors">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-300 flex items-center justify-center text-xs cursor-not-allowed">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>
<?php /**PATH C:\Users\Ananta Wijaya\OneDrive\Desktop\WEB PT 2\resources\views/monitoring-kpi/hierarchy/partials/data-table.blade.php ENDPATH**/ ?>