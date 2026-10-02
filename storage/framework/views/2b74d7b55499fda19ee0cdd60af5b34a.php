<!-- PROGRAM DIRECT SALES CARDS SECTION (3 WHITE CARDS GRID) -->
<div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
    <div>
        <h2 class="text-[19px] font-extrabold text-slate-900 tracking-tight">Program Direct Sales</h2>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola dan submit budget penyerapan anda sesuai ketentuan!</p>
    </div>

    <div class="flex flex-wrap items-start justify-start gap-7">
        <?php $__currentLoopData = $programCardsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $pStat = $card['stat']; ?>
            <div class="w-[320px] h-[260px] bg-[#121212] border border-gray-800 hover:border-gray-700 rounded-2xl p-7 text-center flex flex-col justify-between transition-all hover:shadow-xl group">
                
                <div>
                    <div class="w-12 h-12 rounded-full bg-[white]/10 text-[white] flex items-center justify-center mx-auto text-lg mb-3 group-hover:scale-110 transition-transform border border-[#ED1C24]/30">
                        <?php echo $card['icon_html']; ?>

                    </div>
                    <h3 class="text-[16px] font-bold text-white leading-snug h-8 flex items-center justify-center" title="<?php echo e($card['title']); ?>">
                        <?php echo e($card['title']); ?>

                    </h3>
                    
                    <div class="text-[10px] text-gray-400 font-bold mt-4 text-center">Realisasi</div>
                    <div class="text-xs font-black text-white text-center">
                        Rp <?php echo e(number_format($pStat['realisasi'], 0, ',', '.')); ?>

                    </div>

                    <div class="flex items-center gap-2 mt-2 mb-4">
                        <div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full bg-[#ED1C24] rounded-full transition-all duration-500" style="width: <?php echo e(min(100, $pStat['persen'])); ?>%"></div>
                        </div>
                        <span class="text-[10px] font-bold text-gray-300"><?php echo e($pStat['persen']); ?>%</span>
                    </div>
                </div>

                <button type="button" data-title="<?php echo e($card['title']); ?>" data-budget="<?php echo e($pStat['budget']); ?>" onclick="openSubmitBudgetModal(this.getAttribute('data-title'), parseFloat(this.getAttribute('data-budget')))" class="w-full py-2 px-3 bg-white hover:bg-slate-100 text-[#121212] font-black text-xs rounded-xl border border-white flex items-center justify-center gap-1.5 transition-all shadow-sm cursor-pointer">
                    <span>Kelola</span>
                </button>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\Users\Ananta Wijaya\OneDrive\Desktop\WEB PT 2\resources\views/budget-bk/direct-sales/partials/cards-grid.blade.php ENDPATH**/ ?>