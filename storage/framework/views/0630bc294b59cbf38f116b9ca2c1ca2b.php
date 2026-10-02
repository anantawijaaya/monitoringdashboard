<!-- HEADER TITLE & BREADCRUMB -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-[19px] font-black text-gray-900 tracking-tight">
            
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
            
        </p>
    </div>

    <!-- Breadcrumb & Actions (HANYA MUNCUL PADA VIEW KALKULASI LENGKAP) -->
    <div id="rankingHeaderActions" class="flex items-center gap-2 <?php echo e(($viewMode ?? 'peringkat') === 'lengkap' ? '' : 'hidden'); ?>">
        <?php if(Auth::user() && !Auth::user()->isVisitor()): ?>
            <!-- Impor Data Button -->
            <button type="button" onclick="openImportModal()" 
                    class="px-4 py-2 rounded-xl bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-2 shadow-sm cursor-pointer hover:shadow-md">
                
                <span>Impor Data</span>
            </button>
        <?php endif; ?>

        <!-- Export Button -->
        <a href="<?php echo e(route('ranking.export', request()->query())); ?>" 
           class="px-3.5 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2">
           
            <span>Export Data</span>
        </a>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="py-2 px-1 text-emerald-700 text-xs font-bold flex items-center justify-between">
        <span><?php echo e(session('success')); ?></span>
        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold text-sm cursor-pointer">&times;</button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="py-2 px-1 text-red-700 text-xs font-bold flex items-center justify-between">
        <span><?php echo e(session('error')); ?></span>
        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 font-bold text-sm cursor-pointer">&times;</button>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\Ananta Wijaya\OneDrive\Desktop\WEB PT 2\resources\views/monitoring-kpi/ranking/partials/header.blade.php ENDPATH**/ ?>