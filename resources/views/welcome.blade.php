<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Parameter KPI - Telkomsel Regional Bali Nusra</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                            black: '#0A0A0C',
                            gray: '#4B5563'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        html, body {
            height: 100vh;
            max-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8F9FA;
            overflow: hidden;
        }

        /* Subtle dot grid pattern background */
        .bg-dot-pattern {
            background-image: radial-gradient(#D1D5DB 1.5px, transparent 1.5px);
            background-size: 24px 24px;
        }

        .btn-hover-effect {
            transition: all 0.2s ease-in-out;
        }
        .btn-hover-effect:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(237, 28, 36, 0.25);
        }

        /* 3D Red Recoloring & Floating Island Drop Shadow Filter */
        .stunning-3d-red-map {
            filter: 
                url(#telkomselRedFilter)
                drop-shadow(8px 18px 16px rgba(0, 0, 0, 0.42))
                drop-shadow(0px 25px 45px rgba(237, 28, 36, 0.35));
            transition: filter 0.3s ease;
        }

        /* Gentle floating animation for map */
        @keyframes floatMap {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(0.3deg); }
        }
        .animate-float {
            animation: floatMap 7s ease-in-out infinite;
        }
    </style>
</head>
<body class="antialiased text-gray-900 h-screen max-h-screen flex flex-col justify-between overflow-hidden selection:bg-[#ED1C24] selection:text-white relative bg-[#F8F9FA]">

    <!-- Hidden SVG Filter to recolor map image into vibrant Telkomsel Red (#ED1C24) -->
    <svg width="0" height="0" class="absolute pointer-events-none opacity-0">
        <filter id="telkomselRedFilter" color-interpolation-filters="sRGB">
            <feColorMatrix type="matrix" values="
                0 0 0 0 0.929
                0 0 0 0 0.110
                0 0 0 0 0.141
                0 0 0 1 0" />
        </filter>
    </svg>

    <!-- Dot Grid Pattern Background -->
    <div class="absolute inset-0 bg-dot-pattern opacity-40 pointer-events-none z-0"></div>

    <!-- Header Navigation Bar -->
    <header class="w-full shrink-0 px-6 sm:px-10 py-3 flex items-center justify-between relative z-40 bg-transparent">
        
        <!-- Left Side: Logo Bali Nusra + Title -->
        <div class="flex items-center gap-3 sm:gap-4">
            <img src="{{ asset('images/logo-balinusra.png') }}" 
                 alt="Logo Bali Nusra" 
                 class="h-12 sm:h-14 w-auto object-contain" />
            <span class="font-extrabold text-gray-900 text-sm sm:text-base lg:text-lg tracking-tight">
                Telkomsel Regional Bali Nusra
            </span>
        </div>

        <!-- Right Side: Auth Buttons & Telkomsel Logo Badge -->
        <div class="flex items-center gap-3 sm:gap-4">
            @auth
                <a href="{{ route('dashboard') }}" 
                   class="btn-hover-effect px-7 py-2 bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs sm:text-sm rounded-full shadow-sm uppercase tracking-wider">
                    DASHBOARD
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="btn-hover-effect px-5 py-2 bg-[#ED1C24] hover:bg-red-700 text-white font-extrabold text-xs sm:text-sm rounded-full shadow-sm sentencecase tracking-wider">
                    Login
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" 
                       class="btn-hover-effect px-6 py-2 bg-black hover:bg-gray-900 text-white font-extrabold text-xs sm:text-sm rounded-full shadow-sm sentencecase tracking-wider">
                        Registrasi
                    </a>
                @endif
            @endauth

            <!-- Telkomsel Logo Badge on Far Right Corner -->
            <div class="ml-5 w-14 h-14 sm:w-30 sm:h-30 flex items-center justify-center border-b border-x border-gray-100 shrink-0">
                <img src="{{ asset('images/logo telkomsel api.svg') }}" 
                     alt="Telkomsel Logo" 
                     class="w-full h-full object-contain" />
            </div>
        </div>
    </header>

    <!-- Main Hero Content Area -->
    <main class="flex-1 min-h-0 relative z-20 px-6 sm:px-12 lg:px-16 flex items-center justify-center overflow-hidden py-2">
        
        <!-- Background Ultra-Wide Spanning 3D Red Map (Fills the entire background of welcoming page) -->
        <div class="absolute inset-0 z-0 pointer-events-none flex items-center justify-center overflow-hidden">
            <!-- Red Ambient Glow Sphere behind Map -->
            <div class="absolute w-[95vw] h-[65vh] bg-radial from-[#ED1C24]/28 via-[#ED1C24]/10 to-transparent blur-3xl pointer-events-none"></div>

            <!-- Massive 3D Red Map spanning full background -->
            <div class="w-[130vw] sm:w-[150vw] lg:w-[180vw] max-w-none h-full flex items-center justify-center relative transform translate-x-16 sm:translate-x-28 lg:translate-x-44 ml-[200px]">
                <img src="{{ asset('images/bali-nusra-map.svg') }}" 
                     alt="Peta 3D Regional Bali Nusra" 
                     class="w-full h-auto max-h-[145%] object-contain select-none stunning-3d-red-map animate-float" />
            </div>
        </div>

        <!-- Foreground Hero Content Grid -->
        <div class="w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">

            <!-- LEFT COLUMN: Headline & Paragraph Card (Overlaid cleanly on top left) -->
            <div class="lg:col-span-6 space-y-3 relative z-20 transform -translate-x-[20px]">
                
                <!-- Main Title: SELAMAT DATANG -->
                <div class="leading-none tracking-tight">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-[#0A0A0C] uppercase tracking-tight leading-tight">
                        SELAMAT
                    </h1>
                    <h1 class="text-4xl sm:text-6xl lg:text-6xl font-black text-[#ED1C24] uppercase tracking-tight leading-tight mt-0.5">
                        DATANG
                    </h1>
                </div>

                <!-- Subtitles -->
                <div class="pt-2">
                    <p class="text-gray-700 font-semibold text-base sm:text-lg lg:text-xl">
                        di Monitoring Dashboard KPI
                    </p>
                    <p class="text-gray-900 font-extrabold text-lg sm:text-xl lg:text-2xl mt-0.5">
                        Telkomsel Regional Bali Nusra
                    </p>
                </div>

                <!-- Red Accent Underline Bar -->
                <div class="w-20 h-1.5 bg-[#ED1C24] rounded-full my-3"></div>

                <!-- Paragraph Text -->
                <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed max-w-md pt-0.5">
                    Pantau performa penjualan, analisis data pendapatan, dan dapatkan insight terbaik untuk pengambilan keputusan yang tepat
                </p>
            </div>

            <!-- RIGHT SPACER FOR MAP BACKGROUND -->
            <div class="hidden lg:block lg:col-span-6 pointer-events-none"></div>

        </div>

    </main>

    <!-- Bottom Curved Waves Graphic -->
    <footer class="w-full shrink-0 relative z-10 pointer-events-none">
        <svg viewBox="0 0 1440 200" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-24 sm:h-32 md:h-40 lg:h-48 block">
            <!-- Red Curved Accent Line -->
            <path d="M 0 100 C 480 170, 960 130, 1440 30 L 1440 200 L 0 200 Z" fill="#ED1C24" />
            <!-- Dark Black Footer Wave -->
            <path d="M 0 110 C 480 180, 960 140, 1440 40 L 1440 200 L 0 200 Z" fill="#0A0A0C" />
        </svg>
    </footer>

</body>
</html>

