<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Backup & Restore Data - Telkomsel Regional Bali Nusra</title>
    
    <!-- Prevent Sidebar Flash/Glitch on Page Load -->
    <script>
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.documentElement.classList.add('sidebar-is-collapsed');
        }
    </script>
    <style>
        html.sidebar-is-collapsed #mainSidebar {
            margin-left: -15rem !important;
            transition: none !important;
        }
    </style>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        telkomsel: {
                            red: '#ED1C24',
                            darkred: '#C8102E',
                            deepred: '#8B1D24',
                            dark: '#121212',
                            sidebar: '#18181B',
                            softRed: '#FFF1F2',
                            cardBg: '#FFFFFF'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            zoom: 0.9;
            -moz-transform: scale(0.9);
            -moz-transform-origin: top center;
        }

        .sidebar-item {
            transition: all 0.2s ease-in-out;
            background-color: transparent;
        }

        .sidebar-item:hover {
            background-color: #ED1C24 !important;
            color: #FFFFFF !important;
        }

        .sidebar-item:hover i,
        .sidebar-item:hover svg {
            color: #FFFFFF !important;
        }

        .sidebar-item.active {
            background-color: #ED1C24 !important;
            color: #FFFFFF !important;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(237, 28, 36, 0.4);
        }

        .sidebar-item.active i,
        .sidebar-item.active svg {
            color: #FFFFFF !important;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>
</head>
<body class="h-full flex flex-col antialiased text-gray-900 bg-[#F8FAFC]">

    <!-- TOP HEADER BAR (#121212) -->
    <header class="bg-[#121212] border-b border-gray-800/80 text-white h-16 px-4 sm:px-6 flex items-center justify-between shrink-0 relative z-[100] select-none">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-balinusra.png') }}" alt="Bali Nusra Logo" class="h-10 sm:h-11 w-auto object-contain">
            <h1 class="text-sm sm:text-base font-black tracking-tight bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent drop-shadow-sm">
                Telkomsel Regional Bali Nusra
            </h1>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative" id="userDropdownContainer">
            <button id="userDropdownBtn" 
                    onclick="toggleUserDropdown(event)"
                    class="flex items-center gap-3 px-3.5 py-1.5 rounded-full bg-[#222225] hover:bg-[#2c2c30] border border-white/10 transition-all cursor-pointer">
                <div class="w-7 h-7 rounded-full bg-[#ED1C24] text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex flex-col text-left">
                    <span class="text-xs font-bold text-white leading-tight">
                        {{ Auth::user()->name ?? 'Admin Telkomsel' }}
                    </span>
                    <span class="text-[9px] font-black uppercase text-red-400 leading-tight tracking-wider">
                        {{ Auth::user()->role ?? 'ADMIN' }}
                    </span>
                </div>
                <i id="dropdownArrow" class="bi bi-chevron-down text-xs text-gray-400 ml-1 transition-transform duration-200"></i>
            </button>

            <div id="userDropdownMenu" 
                 class="hidden absolute right-0 mt-2 w-52 bg-[#1a1a1d] border border-gray-800 rounded-2xl shadow-xl py-2 z-50 text-xs">
                <div class="px-4 py-2 border-b border-gray-800/80">
                    <p class="font-bold text-white truncate">{{ Auth::user()->name ?? 'Admin Telkomsel' }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email ?? 'admin@telkomsel.co.id' }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-red-400 hover:bg-white/5 transition-colors font-bold text-left cursor-pointer">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- MAIN BODY CONTAINER -->
    <div class="flex-1 flex min-h-0 overflow-hidden relative">

        @include('layouts.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#F8FAFC]">
            
            <main class="flex-1 overflow-y-auto p-6 sm:p-8 space-y-6">

                <!-- ALERTS -->
                @if(session('success'))
                    <div class="py-2 px-1 text-emerald-700 flex items-center justify-between animate-fade-in">
                        <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 cursor-pointer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="py-2 px-1 text-red-700 flex items-center justify-between animate-fade-in">
                        <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 cursor-pointer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                <!-- HEADER TITLE BLOCK -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-[17px] font-extrabold text-red-600 uppercase tracking-wider mb-1">
                            
                    
                        </div>
                        <p class="text-[13px] text-slate-500 mt-0.5 font-medium">
                           
                        </p>
                    </div>

                    <!-- Instant Backup & Restore Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Button 1: Back Up Data Baru -->
                        <form action="{{ route('admin.backups.create') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 rounded-full bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-2 shadow-md hover:shadow-lg cursor-pointer">
                                <span>Back Up Data Baru</span>
                            </button>
                        </form>

                        <!-- Button 2: Tampilan Data (Opens Modal Upload & Restore) -->
                        <button type="button" 
                                onclick="openRestoreModal()"
                                class="px-5 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition-all flex items-center gap-2 shadow-md hover:shadow-lg cursor-pointer">
                            <i class="bi bi-folder2-open text-sm"></i>
                            <span>Restore Data</span>
                        </button>
                    </div>
                </div>

                <!-- 4 SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <!-- Card 1: Total File Backup -->
                    <div class="bg-[#ED1C24] border-[#ED1C24] rounded-2xl p-5 shadow-xs space-y-2">
                        <div class="flex items-center justify-between text-white text-[15px] font-extrabold uppercase tracking-wider">
                            <span>Total File Backup</span>
                        </div>
                        <div class="text-[25px] font-black text-white">{{ $totalBackups }} File</div>
                        <p class="text-[11px] text-white/80 font-medium">Tersimpan di server aplikasi</p>
                    </div>

                    <!-- Card 2: Ukuran Storage -->
                    <div class="bg-[#ED1C24] border-[#ED1C24] rounded-2xl p-5 shadow-xs space-y-2">
                        <div class="flex items-center justify-between text-white text-[15px] font-extrabold uppercase tracking-wider">
                            <span>Total Ukuran Storage</span>
                        </div>
                        <div class="text-[25px] font-black text-white">{{ $totalSizeFormatted }}</div>
                        <p class="text-[11px] text-white/80 font-medium">Alokasi ruang file backup</p>
                    </div>

                    <!-- Card 3: Backup Terakhir -->
                    <div class="bg-[#ED1C24] border-[#ED1C24] rounded-2xl p-5 shadow-xs space-y-2">
                        <div class="flex items-center justify-between text-white text-[15px] font-extrabold uppercase tracking-wider">
                            <span>Backup Terakhir</span>
                        </div>
                        <div class="text-[23px] font-extrabold text-white truncate" title="{{ $latestBackupDate }}">
                            {{ $latestBackupDate }}
                        </div>
                        <p class="text-[11px] text-white/80 font-medium">Waktu snapshot terbaru</p>
                    </div>

                    <!-- Card 4: Proteksi Sistem -->
                    <div class="bg-[#ED1C24] border-[#ED1C24] rounded-2xl p-5 shadow-xs space-y-2 bg-gradient-to-br from-[#ED1C24] to-[#ED1C24]">
                        <div class="flex items-center justify-between text-white text-[15px] font-extrabold uppercase tracking-wider">
                            <span>Status Proteksi Data</span>
                        </div>
                        <div class="text-[21px] font-extrabold text-white">AKTIF</div>
                        <p class="text-[11px] text-white/80 font-medium">Terproteksi dari reset otomatis</p>
                    </div>
                </div>

                <!-- RIWAYAT FILE BACKUP TABLE -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden space-y-4 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i class="bi bi-card-list text-lg text-slate-700"></i>
                            <h2 class="text-sm font-extrabold text-slate-900">Daftar Riwayat File Backup (.SQL)</h2>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">{{ count($backups) }} file tersedia</span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-slate-700 font-extrabold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4">Nama File Backup</th>
                                    <th class="py-3.5 px-4 text-center">Tanggal Dibuat</th>
                                    <th class="py-3.5 px-4 text-center">Ukuran File</th>
                                    <th class="py-3.5 px-4 text-center w-48">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                @forelse($backups as $index => $b)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2">
                                            <i class="bi bi-file-earmark-code text-red-500 text-base"></i>
                                            <span>{{ $b['fileName'] }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-semibold text-slate-600">
                                            {{ $b['createdAt'] }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="px-2.5 py-1 rounded-full bg-slate-100 font-bold text-slate-700 text-[10px]">
                                                {{ $b['size'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Download Button -->
                                                <a href="{{ route('admin.backups.download', $b['fileName']) }}" 
                                                   class="p-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all font-bold text-xs" 
                                                   title="Unduh File Backup ke Laptop">
                                                    <i class="bi bi-download"></i>
                                                </a>

                                                <!-- Restore Button -->
                                                <form action="{{ route('admin.backups.restore') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="file_name" value="{{ $b['fileName'] }}">
                                                    <button type="submit" 
                                                            onclick="return confirm('Apakah Anda yakin ingin memulihkan (restore) data dari file {{ $b['fileName'] }}? Data saat ini akan diperbarui sesuai kondisi file backup ini.')"
                                                            class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-[11px] transition-all flex items-center gap-1 cursor-pointer"
                                                            title="Pulihkan Data dari File Ini">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                        <span>Restore</span>
                                                    </button>
                                                </form>

                                                <!-- Delete Button -->
                                                <form action="{{ route('admin.backups.destroy', $b['fileName']) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus file backup {{ $b['fileName'] }} ini?')"
                                                            class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition-all font-bold text-xs cursor-pointer" 
                                                            title="Hapus File Backup">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 font-semibold">
                                            Belum ada file backup yang tersimpan. Klik tombol <strong class="text-red-600">"Buat Backup Baru Sekarang"</strong> di atas untuk membuat file cadangan instan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- POPUP MODAL: UPLOAD & RESTORE FILE BACKUP -->
    <div id="restoreFileModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-all duration-300 opacity-0">
        <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-all duration-300">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-red-50 text-[#ED1C24] flex items-center justify-center text-sm font-bold shrink-0">
                        <i class="bi bi-cloud-upload"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Upload & Restore File Backup</h3>
                        
                    </div>
                </div>
                <button onclick="closeRestoreModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form action="{{ route('admin.backups.restore') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Pilih Berkas Backup (.sql)</label>
                    <input type="file" name="backup_file" accept=".sql,.txt" required
                           class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-extrabold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer border border-slate-200 rounded-2xl p-1.5 bg-slate-50 focus:outline-none focus:border-red-500">
                    <p class="text-[10px] text-slate-400 font-medium"> <strong></strong></p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeRestoreModal()" class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            onclick="return confirm('Apakah Anda yakin ingin memulihkan (restore) data dari file ini? Data saat ini akan diperbarui sesuai isi file backup.')"
                            class="px-5 py-2 rounded-full bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs transition-all flex items-center gap-1.5 shadow-md cursor-pointer">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Upload & Restore Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dropdown Toggle & Modal Scripts -->
    <script>
        function openRestoreModal() {
            const modal = document.getElementById('restoreFileModal');
            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    if (modal.firstElementChild) modal.firstElementChild.classList.remove('scale-95');
                }, 10);
            }
        }

        function closeRestoreModal() {
            const modal = document.getElementById('restoreFileModal');
            if (modal) {
                if (modal.firstElementChild) modal.firstElementChild.classList.add('scale-95');
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }
        }

        function toggleUserDropdown(e) {
            e.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            if (menu) {
                menu.classList.toggle('hidden');
                if (arrow) arrow.classList.toggle('rotate-180');
            }
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('userDropdownContainer');
            const menu = document.getElementById('userDropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            if (container && !container.contains(e.target)) {
                if (menu && !menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                    if (arrow) arrow.classList.remove('rotate-180');
                }
            }
        });
    </script>
</body>
</html>
