<?php
    $hasActiveFilter = !empty($search) || ($selectedBranch && $selectedBranch !== 'all') || ($selectedCluster && $selectedCluster !== 'all') || ($selectedFlag && $selectedFlag !== 'all');
?>

<!-- 3. FILTER BAR WITH 4 INPUTS (HIDDEN BY DEFAULT WHEN MENU IS OPENED UNTIL "TAMPILKAN FILTER" IS CLICKED) -->
<div id="filterBarContainer" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-gray-200/20 shadow-sm transition-all duration-300 <?php echo e($hasActiveFilter ? '' : 'hidden'); ?>">
    <form id="filterForm" method="GET" action="<?php echo e(route('regional-map.index')); ?>" class="flex flex-wrap items-center justify-center gap-3.5 sm:gap-4 w-full mx-auto">
        
        <!-- Search Input -->
        <div class="relative shrink-0">
            <input type="text" 
                   name="search" 
                   value="<?php echo e($search); ?>" 
                   placeholder="Cari ID Outlet" 
                   class="w-90 pl-4 pr-10 py-2.5 text-xs font-medium rounded-xl border border-gray-200 focus:border-red-500 outline-none text-gray-800 bg-white">
            <i class="bi bi-search absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        <!-- Custom Branch Dropdown (Guaranteed Opens Downward) -->
        <div class="relative shrink-0 z-30" id="mapBranchDropdownContainer">
            <input type="hidden" name="branch" id="mapBranchInput" value="<?php echo e($selectedBranch); ?>">
            <button type="button" 
                    onclick="toggleMapBranchMenu(event)"
                    class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer min-w-[160px]">
                <span><?php echo e($selectedBranch === 'all' ? 'Semua Branch' : $selectedBranch); ?></span>
                <i id="mapBranchArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
            </button>

            <div id="mapBranchMenu" 
                 class="hidden absolute top-full left-0 mt-1.5 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                <a href="javascript:void(0)" onclick="submitMapFilter('branch', 'all')"
                   class="block px-4 py-2 text-xs font-bold <?php echo e($selectedBranch === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50'); ?> transition-colors">
                    Semua Branch
                </a>
                <?php $__currentLoopData = $availableBranches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="javascript:void(0)" onclick="submitMapFilter('branch', '<?php echo e($b); ?>')"
                       class="block px-4 py-2 text-xs font-semibold <?php echo e($selectedBranch === $b ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50'); ?> transition-colors">
                        <?php echo e($b); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Custom Cluster Dropdown (Guaranteed Opens Downward) -->
        <div class="relative shrink-0 z-30" id="mapClusterDropdownContainer">
            <input type="hidden" name="cluster" id="mapClusterInput" value="<?php echo e($selectedCluster); ?>">
            <button type="button" 
                    onclick="toggleMapClusterMenu(event)"
                    class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer min-w-[160px]">
                <span><?php echo e($selectedCluster === 'all' ? 'Semua Cluster' : $selectedCluster); ?></span>
                <i id="mapClusterArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
            </button>

            <div id="mapClusterMenu" 
                 class="hidden absolute top-full left-0 mt-1.5 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                <a href="javascript:void(0)" onclick="submitMapFilter('cluster', 'all')"
                   class="block px-4 py-2 text-xs font-bold <?php echo e($selectedCluster === 'all' ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50'); ?> transition-colors">
                    Semua Cluster
                </a>
                <?php $__currentLoopData = $availableClusters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="javascript:void(0)" onclick="submitMapFilter('cluster', '<?php echo e($c); ?>')"
                       class="block px-4 py-2 text-xs font-semibold <?php echo e($selectedCluster === $c ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50'); ?> transition-colors">
                        <?php echo e($c); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Custom Flag Category Dropdown (Guaranteed Opens Downward) -->
        <div class="relative shrink-0 z-30" id="mapFlagDropdownContainer">
            <input type="hidden" name="flag" id="mapFlagInput" value="<?php echo e($selectedFlag); ?>">
            <?php
                $flagLabels = [
                    'all' => 'Semua Kategori Flag',
                    'black' => 'Flag < 0%',
                    'red' => 'Flag = 0%',
                    'orange' => 'Flag <= 3%',
                    'green' => 'Flag > 3%',
                ];
                $currentFlagLabel = $flagLabels[$selectedFlag ?? 'all'] ?? 'Semua Kategori Flag';
            ?>
            <button type="button" 
                    onclick="toggleMapFlagMenu(event)"
                    class="flex items-center justify-between gap-2.5 bg-white border border-gray-200 hover:bg-slate-50 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 shadow-xs cursor-pointer min-w-[170px]">
                <span><?php echo e($currentFlagLabel); ?></span>
                <i id="mapFlagArrow" class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200"></i>
            </button>

            <div id="mapFlagMenu" 
                 class="hidden absolute top-full left-0 mt-1.5 w-60 bg-white border border-gray-200 rounded-2xl shadow-xl py-1.5 z-50 max-h-60 overflow-y-auto">
                <?php $__currentLoopData = $flagLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fKey => $fLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="javascript:void(0)" onclick="submitMapFilter('flag', '<?php echo e($fKey); ?>')"
                       class="block px-4 py-2 text-xs <?php echo e(($selectedFlag ?? 'all') === $fKey ? 'text-red-600 bg-red-50/50 font-extrabold' : 'text-gray-700 hover:bg-slate-50 font-semibold'); ?> transition-colors">
                        <?php echo e($fLabel); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

    </form>
</div>
<?php /**PATH C:\Users\Ananta Wijaya\OneDrive\Desktop\WEB PT 2\resources\views/monitoring-kpi/peta-regional/partials/filter-panel.blade.php ENDPATH**/ ?>