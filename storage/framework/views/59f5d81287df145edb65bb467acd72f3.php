<!-- Expandable Filter Panel -->
<div id="filterPanel" class="<?php echo e((!empty($search) || $selectedBranch !== 'all' || $selectedCluster !== 'all' || $selectedMitra !== 'all') ? '' : 'hidden'); ?> p-4 rounded-2xl bg-gray-50/80 border border-gray-200 transition-all">
    <form id="hierarchyFilterForm" method="GET" action="<?php echo e(route('hierarchy.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Search -->
        <div class="lg:col-span-2">
            <label class="block text-xs font-bold text-gray-600 mb-1"></label>
            <div class="relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-600 text-xs"></i>
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="cari kabupaten, cluster, mitra, manager disini..." 
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium bg-white">
            </div>
        </div>

        <!-- Filter Branch -->
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Filter Branch</label>
            <input type="hidden" name="branch" id="hierBranchInput" value="<?php echo e($selectedBranch); ?>">
            <div class="relative z-30" id="hierBranchDropdownContainer">
                <button type="button" 
                        onclick="toggleHierBranchMenu(event)"
                        class="w-full flex items-center justify-between gap-2 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer">
                    <span><?php echo e($selectedBranch === 'all' ? 'Semua Branch' : $selectedBranch); ?></span>
                    <i id="hierBranchArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="hierBranchMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-full bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto min-w-[160px]">
                    <a href="javascript:void(0)" onclick="submitHierFilter('branch', 'all')"
                       class="block px-4 py-2 text-xs font-bold <?php echo e($selectedBranch === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50'); ?> transition-colors">
                        Semua Branch
                    </a>
                    <?php $__currentLoopData = $availableBranches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="javascript:void(0)" onclick="submitHierFilter('branch', '<?php echo e($b); ?>')"
                           class="block px-4 py-2 text-xs font-semibold <?php echo e($selectedBranch === $b ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50'); ?> transition-colors">
                            <?php echo e($b); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <!-- Filter Cluster -->
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">Filter Cluster</label>
            <input type="hidden" name="cluster" id="hierClusterInput" value="<?php echo e($selectedCluster); ?>">
            <div class="relative z-30" id="hierClusterDropdownContainer">
                <button type="button" 
                        onclick="toggleHierClusterMenu(event)"
                        class="w-full flex items-center justify-between gap-2 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer">
                    <span><?php echo e($selectedCluster === 'all' ? 'Semua Cluster' : $selectedCluster); ?></span>
                    <i id="hierClusterArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
                </button>

                <div id="hierClusterMenu" 
                     class="hidden absolute top-full left-0 mt-1.5 w-full bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto min-w-[160px]">
                    <a href="javascript:void(0)" onclick="submitHierFilter('cluster', 'all')"
                       class="block px-4 py-2 text-xs font-bold <?php echo e($selectedCluster === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50'); ?> transition-colors">
                        Semua Cluster
                    </a>
                    <?php $__currentLoopData = $availableClusters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="javascript:void(0)" onclick="submitHierFilter('cluster', '<?php echo e($c); ?>')"
                           class="block px-4 py-2 text-xs font-semibold <?php echo e($selectedCluster === $c ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50'); ?> transition-colors">
                            <?php echo e($c); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </form>
</div>
<?php /**PATH C:\Users\Ananta Wijaya\OneDrive\Desktop\WEB PT 2\resources\views/monitoring-kpi/hierarchy/partials/filter-panel.blade.php ENDPATH**/ ?>