<!-- TWO BOXES SECTION: TOP 3 RANKING ATAS & TOP 3 RANKING BAWAH (AUTO CALCULATED RANKINGS) -->
<div id="rankingSection" class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">

    <!-- BOX 1: TOP 3 RANKING ATAS (BEST PERFORMERS) -->
    <div class="p-5 sm:p-6 relative overflow-hidden flex flex-col justify-between">
        <div class="absolute top-0 left-0 right-0 h-1.5"></div>

        <div>
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-gray-900">TOP 3 RANKING ATAS</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List of Top 3 Items -->
            <div class="mt-4 space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $topRankings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $ach = $item->ach_revenue_all ?: $item->achievement_rate;
                    ?>
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm relative hover:border-emerald-200 transition-all">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <!-- Rank Number Badge -->
                                <div class="w-7 h-7 rounded-lg bg-emerald-500 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5">
                                    <?php echo e($index + 1); ?>

                                </div>
                                
                                <div>
                                    <!-- Cluster Name -->
                                    <h4 class="text-sm font-black text-gray-900 leading-snug uppercase"><?php echo e($item->cluster_name); ?></h4>
                                    
                                    <!-- Kabupaten -->
                                    <?php if($item->kabupaten): ?>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <i class="bi bi-geo-alt-fill text-red-500 text-[14px]"></i>
                                            <span class="text-xs font-medium border-r border-slate-200 uppercase"><?php echo e($item->kabupaten); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Target & Pencapaian -->
                                    <div class="mt-1 space-y-0.5 text-xs text-gray-400 font-medium">
                                        <div>
                                            Target: <span class="text-gray-700 font-bold"><?php echo e($item->formatted_target); ?></span>
                                        </div>
                                        <div>
                                            Pencapaian: <span class="text-red-600 font-bold"><?php echo e($item->formatted_revenue_all); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ach Percentage on the Right -->
                            <div class="text-right shrink-0">
                                <span class="text-xs sm:text-sm font-black text-black-500 uppercase tracking-wider block leading-tight">ach (%)</span>
                                <span class="text-2xl sm:text-3xl lg:text-[26px] font-extrabold block mt-1 leading-none tracking-tight"><?php echo e(number_format($ach, 2)); ?>%</span>
                            </div>
                        </div>
                        
                        <!-- Bottom Green Line -->
                        <div class="w-full h-2 bg-emerald-500 mt-3"></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="py-8 text-center text-gray-400 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                        <i class="bi bi-inbox text-3xl block mb-1"></i>
                        <p class="text-xs font-semibold">Belum ada data cluster yang dimasukkan.</p>
                        <a href="<?php echo e(route('revenue.manage')); ?>" class="mt-2 text-xs text-[#ED1C24] font-bold hover:underline inline-block">Kelola Data Revenue</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- BOX 2: TOP 3 RANKING BAWAH (NEEDS ATTENTION) -->
    <div class="p-5 sm:p-6 relative overflow-hidden flex flex-col justify-between">
        <div class="absolute top-0 left-0 right-0 h-1.5"></div>

        <div>
            <!-- Header -->
            <div class="flex items-center justify-between pb-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="bg-red-50 text-red-600 flex items-center justify-center text-lg shrink-0">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-gray-900">TOP 3 RANKING BAWAH</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List of Bottom 3 Items -->
            <div class="mt-4 space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $bottomRankings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $ach = $item->ach_revenue_all ?: $item->achievement_rate;
                        $cardBorder = ($index === 2) ? 'border-red-200' : 'border-gray-100';
                    ?>
                    <div class="bg-white rounded-2xl p-4 border <?php echo e($cardBorder); ?> shadow-sm relative hover:border-red-200 transition-all">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <!-- Rank Number Badge (All Red) -->
                                <div class="w-7 h-7 rounded-lg bg-red-600 text-white font-black text-sm flex items-center justify-center shrink-0 mt-0.5">
                                    <?php echo e($index + 1); ?>

                                </div>
                                
                                <div>
                                    <!-- Cluster Name -->
                                    <h4 class="text-sm font-black text-gray-900 leading-snug uppercase"><?php echo e($item->cluster_name); ?></h4>
                                    
                                    <!-- Kabupaten -->
                                    <?php if($item->kabupaten): ?>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <i class="bi bi-geo-alt-fill text-red-500 text-[11px]"></i>
                                            <span class="text-xs font-medium border-r border-slate-200 uppercase"><?php echo e($item->kabupaten); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Target & Pencapaian -->
                                    <div class="mt-1 space-y-0.5 text-xs text-gray-400 font-medium">
                                        <div>
                                            Target: <span class="text-gray-700 font-bold"><?php echo e($item->formatted_target); ?></span>
                                        </div>
                                        <div>
                                            Pencapaian: <span class="text-red-600 font-bold"><?php echo e($item->formatted_revenue_all); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ach Percentage on the Right -->
                            <div class="text-right shrink-0">
                                <span class="text-xs sm:text-sm font-black text-black-500 uppercase tracking-wider block leading-tight">ach (%)</span>
                                <span class="text-2xl sm:text-3xl lg:text-[26px] font-extrabold block mt-2 leading-none tracking-tight"><?php echo e(number_format($ach, 2)); ?>%</span>
                            </div>
                        </div>
                        
                        <!-- Bottom Red Line -->
                        <div class="w-full h-2 bg-red-500 mt-4"></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="py-8 text-center text-gray-400 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                        <i class="bi bi-inbox text-3xl block mb-1"></i>
                        <p class="text-xs font-semibold">Belum ada data cluster yang dimasukkan.</p>
                        <a href="<?php echo e(route('revenue.manage')); ?>" class="mt-2 text-xs text-[#ED1C24] font-bold hover:underline inline-block">Kelola Data Revenue</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?php /**PATH C:\Users\Ananta Wijaya\OneDrive\Desktop\WEB PT 2\resources\views/home/partials/ranking-cards.blade.php ENDPATH**/ ?>