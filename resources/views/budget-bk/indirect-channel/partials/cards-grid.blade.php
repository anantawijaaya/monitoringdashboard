<!-- PROGRAM INDIRECT CHANNEL CARDS SECTION (6 WHITE CARDS GRID) -->
<div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
    <div>
        <h2 class="text-[19px] font-extrabold text-slate-900 tracking-tight">Program Indirect Channel</h2>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola dan submit budget penyerapan anda sesuai ketentuan!</p>
    </div>

    @php
        $cardMap = collect($programCardsList)->keyBy('key');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        
        <!-- ========================================== -->
        <!-- BOX 1: DIGITAL MARKETING -->
        <!-- ========================================== -->
        @php $cardDM = $cardMap['dm'] ?? null; $pStatDM = $cardDM['stat'] ?? ['realisasi' => 0, 'budget' => 0, 'persen' => 0]; @endphp
        <div class="bg-[#121212] border border-gray-800 hover:border-gray-700 rounded-2xl p-4 text-center flex flex-col justify-between transition-all hover:shadow-xl group">
            <div>
                <div class="w-12 h-12 rounded-full bg-[white]/10 text-[white] flex items-center justify-center mx-auto text-lg mb-3 group-hover:scale-110 transition-transform border border-[#ED1C24]/30">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <h3 class="text-[14px] font-bold text-white leading-snug h-8 flex items-center justify-center" title="Digital Marketing">
                    Digital Marketing
                </h3>
                
                <div class="text-[10px] text-gray-400 font-bold mt-4 text-center">Realisasi</div>
                <div class="text-xs font-black text-white text-center">
                    Rp {{ number_format($pStatDM['realisasi'], 0, ',', '.') }}
                </div>

                <div class="flex items-center gap-2 mt-2 mb-4">
                    <div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-[#ED1C24] rounded-full transition-all duration-500" style="width: {{ min(100, $pStatDM['persen']) }}%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-300">{{ $pStatDM['persen'] }}%</span>
                </div>
            </div>

            <button type="button" data-title="Digital Marketing" data-budget="{{ $pStatDM['budget'] }}" onclick="openSubmitBudgetModal(this.getAttribute('data-title'), parseFloat(this.getAttribute('data-budget')))" class="w-full py-2 px-3 bg-white hover:bg-slate-100 text-[#121212] font-black text-xs rounded-xl border border-white flex items-center justify-center gap-1.5 transition-all shadow-sm cursor-pointer">
                <span>Kelola</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- BOX 2: CVM PROGRAM -->
        <!-- ========================================== -->
        @php $cardCVM = $cardMap['cvm'] ?? null; $pStatCVM = $cardCVM['stat'] ?? ['realisasi' => 0, 'budget' => 0, 'persen' => 0]; @endphp
        <div class="bg-[#121212] border border-gray-800 hover:border-gray-700 rounded-2xl p-4 text-center flex flex-col justify-between transition-all hover:shadow-xl group">
            <div>
                <div class="w-12 h-12 rounded-full bg-[white]/10 text-[white] flex items-center justify-center mx-auto text-lg mb-3 group-hover:scale-110 transition-transform border border-[#ED1C24]/30">
                    <i class="fa-solid fa-people-line"></i>
                </div>
                <h3 class="text-[14px] font-bold text-white leading-snug h-8 flex items-center justify-center" title="CVM Program">
                    CVM Program
                </h3>
                
                <div class="text-[10px] text-gray-400 font-bold mt-4 text-center">Realisasi</div>
                <div class="text-xs font-black text-white text-center">
                    Rp {{ number_format($pStatCVM['realisasi'], 0, ',', '.') }}
                </div>

                <div class="flex items-center gap-2 mt-2 mb-4">
                    <div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-[#ED1C24] rounded-full transition-all duration-500" style="width: {{ min(100, $pStatCVM['persen']) }}%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-300">{{ $pStatCVM['persen'] }}%</span>
                </div>
            </div>

            <button type="button" data-title="CVM Program" data-budget="{{ $pStatCVM['budget'] }}" onclick="openSubmitBudgetModal(this.getAttribute('data-title'), parseFloat(this.getAttribute('data-budget')))" class="w-full py-2 px-3 bg-white hover:bg-slate-100 text-[#121212] font-black text-xs rounded-xl border border-white flex items-center justify-center gap-1.5 transition-all shadow-sm cursor-pointer">
                <span>Kelola</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- BOX 3: ENGAGEMENT OUTLET -->
        <!-- ========================================== -->
        @php $cardEO = $cardMap['eo'] ?? null; $pStatEO = $cardEO['stat'] ?? ['realisasi' => 0, 'budget' => 0, 'persen' => 0]; @endphp
        <div class="bg-[#121212] border border-gray-800 hover:border-gray-700 rounded-2xl p-4 text-center flex flex-col justify-between transition-all hover:shadow-xl group">
            <div>
                <div class="w-12 h-12 rounded-full bg-[white]/10 text-[white] flex items-center justify-center mx-auto text-lg mb-3 group-hover:scale-110 transition-transform border border-[#ED1C24]/30">
                    <i class="bi bi-shop"></i>
                </div>
                <h3 class="text-[14px] font-bold text-white leading-snug h-8 flex items-center justify-center" title="Engagement Outlet">
                    Engagement Outlet
                </h3>
                
                <div class="text-[10px] text-gray-400 font-bold mt-4 text-center">Realisasi</div>
                <div class="text-xs font-black text-white text-center">
                    Rp {{ number_format($pStatEO['realisasi'], 0, ',', '.') }}
                </div>

                <div class="flex items-center gap-2 mt-2 mb-4">
                    <div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-[#ED1C24] rounded-full transition-all duration-500" style="width: {{ min(100, $pStatEO['persen']) }}%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-300">{{ $pStatEO['persen'] }}%</span>
                </div>
            </div>

            <button type="button" data-title="Engagement Outlet" data-budget="{{ $pStatEO['budget'] }}" onclick="openSubmitBudgetModal(this.getAttribute('data-title'), parseFloat(this.getAttribute('data-budget')))" class="w-full py-2 px-3 bg-white hover:bg-slate-100 text-[#121212] font-black text-xs rounded-xl border border-white flex items-center justify-center gap-1.5 transition-all shadow-sm cursor-pointer">
                <span>Kelola</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- BOX 4: BRANDING OUTLET -->
        <!-- ========================================== -->
        @php $cardBO = $cardMap['bo'] ?? null; $pStatBO = $cardBO['stat'] ?? ['realisasi' => 0, 'budget' => 0, 'persen' => 0]; @endphp
        <div class="bg-[#121212] border border-gray-800 hover:border-gray-700 rounded-2xl p-4 text-center flex flex-col justify-between transition-all hover:shadow-xl group">
            <div>
                <div class="w-12 h-12 rounded-full bg-[white]/10 text-[white] flex items-center justify-center mx-auto text-lg mb-3 group-hover:scale-110 transition-transform border border-[#ED1C24]/30">
                    <i class="bi bi-award-fill"></i>
                </div>
                <h3 class="text-[14px] font-bold text-white leading-snug h-8 flex items-center justify-center" title="Branding Outlet">
                    Branding Outlet
                </h3>
                
                <div class="text-[10px] text-gray-400 font-bold mt-4 text-center">Realisasi</div>
                <div class="text-xs font-black text-white text-center">
                    Rp {{ number_format($pStatBO['realisasi'], 0, ',', '.') }}
                </div>

                <div class="flex items-center gap-2 mt-2 mb-4">
                    <div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-[#ED1C24] rounded-full transition-all duration-500" style="width: {{ min(100, $pStatBO['persen']) }}%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-300">{{ $pStatBO['persen'] }}%</span>
                </div>
            </div>

            <button type="button" data-title="Branding Outlet" data-budget="{{ $pStatBO['budget'] }}" onclick="openSubmitBudgetModal(this.getAttribute('data-title'), parseFloat(this.getAttribute('data-budget')))" class="w-full py-2 px-3 bg-white hover:bg-slate-100 text-[#121212] font-black text-xs rounded-xl border border-white flex items-center justify-center gap-1.5 transition-all shadow-sm cursor-pointer">
                <span>Kelola</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- BOX 5: PROGRAM SALES OUTLET -->
        <!-- ========================================== -->
        @php $cardPSO = $cardMap['pso'] ?? null; $pStatPSO = $cardPSO['stat'] ?? ['realisasi' => 0, 'budget' => 0, 'persen' => 0]; @endphp
        <div class="bg-[#121212] border border-gray-800 hover:border-gray-700 rounded-2xl p-4 text-center flex flex-col justify-between transition-all hover:shadow-xl group">
            <div>
                <div class="w-12 h-12 rounded-full bg-[white]/10 text-[white] flex items-center justify-center mx-auto text-lg mb-3 group-hover:scale-110 transition-transform border border-[#ED1C24]/30">
                    <i class="bi bi-tag-fill"></i>
                </div>
                <h3 class="text-[14px] font-bold text-white leading-snug h-8 flex items-center justify-center" title="Program Sales Outlet">
                    Program Sales Outlet
                </h3>
                
                <div class="text-[10px] text-gray-400 font-bold mt-4 text-center">Realisasi</div>
                <div class="text-xs font-black text-white text-center">
                    Rp {{ number_format($pStatPSO['realisasi'], 0, ',', '.') }}
                </div>

                <div class="flex items-center gap-2 mt-2 mb-4">
                    <div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-[#ED1C24] rounded-full transition-all duration-500" style="width: {{ min(100, $pStatPSO['persen']) }}%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-300">{{ $pStatPSO['persen'] }}%</span>
                </div>
            </div>

            <button type="button" data-title="Program Sales Outlet" data-budget="{{ $pStatPSO['budget'] }}" onclick="openSubmitBudgetModal(this.getAttribute('data-title'), parseFloat(this.getAttribute('data-budget')))" class="w-full py-2 px-3 bg-white hover:bg-slate-100 text-[#121212] font-black text-xs rounded-xl border border-white flex items-center justify-center gap-1.5 transition-all shadow-sm cursor-pointer">
                <span>Kelola</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- BOX 6: VOUCHER GAMES PROGRAM -->
        <!-- ========================================== -->
        @php $cardVG = $cardMap['vg'] ?? null; $pStatVG = $cardVG['stat'] ?? ['realisasi' => 0, 'budget' => 0, 'persen' => 0]; @endphp
        <div class="bg-[#121212] border border-gray-800 hover:border-gray-700 rounded-2xl p-4 text-center flex flex-col justify-between transition-all hover:shadow-xl group">
            <div>
                <div class="w-12 h-12 rounded-full bg-[white]/10 text-[white] flex items-center justify-center mx-auto text-lg mb-3 group-hover:scale-110 transition-transform border border-[#ED1C24]/30">
                    <i class="bi bi-controller"></i>
                </div>
                <h3 class="text-[14px] font-bold text-white leading-snug h-8 flex items-center justify-center" title="Voucher Games Program">
                    Voucher Games Program
                </h3>
                
                <div class="text-[10px] text-gray-400 font-bold mt-4 text-center">Realisasi</div>
                <div class="text-xs font-black text-white text-center">
                    Rp {{ number_format($pStatVG['realisasi'], 0, ',', '.') }}
                </div>

                <div class="flex items-center gap-2 mt-2 mb-4">
                    <div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-[#ED1C24] rounded-full transition-all duration-500" style="width: {{ min(100, $pStatVG['persen']) }}%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-gray-300">{{ $pStatVG['persen'] }}%</span>
                </div>
            </div>

            <button type="button" data-title="Voucher Games Program" data-budget="{{ $pStatVG['budget'] }}" onclick="openSubmitBudgetModal(this.getAttribute('data-title'), parseFloat(this.getAttribute('data-budget')))" class="w-full py-2 px-3 bg-white hover:bg-slate-100 text-[#121212] font-black text-xs rounded-xl border border-white flex items-center justify-center gap-1.5 transition-all shadow-sm cursor-pointer">
                <span>Kelola</span>
            </button>
        </div>

    </div>
</div>
