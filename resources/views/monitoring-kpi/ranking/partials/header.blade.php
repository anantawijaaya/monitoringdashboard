<!-- HEADER TITLE & BREADCRUMB -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-[19px] font-black text-gray-900 tracking-tight">
            
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium">
            
        </p>
    </div>

    <!-- Breadcrumb & Actions (HANYA MUNCUL PADA VIEW KALKULASI LENGKAP) -->
    <div id="rankingHeaderActions" class="flex items-center gap-2 {{ ($viewMode ?? 'peringkat') === 'lengkap' ? '' : 'hidden' }}">
        @if(Auth::user() && !Auth::user()->isVisitor())
            <!-- Impor Data Button -->
            <button type="button" onclick="openImportModal()" 
                    class="px-4 py-2 rounded-xl bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-2 shadow-sm cursor-pointer hover:shadow-md">
                
                <span>Impor Data</span>
            </button>
        @endif

        <!-- Export Button -->
        <a href="{{ route('ranking.export', request()->query()) }}" 
           class="px-3.5 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold border border-gray-200 shadow-sm transition-all flex items-center gap-2">
           
            <span>Export Data</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-2xl p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-500 text-base"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-sm cursor-pointer">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 text-xs rounded-2xl p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-red-500 text-base"></i>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold text-sm cursor-pointer">&times;</button>
    </div>
@endif
