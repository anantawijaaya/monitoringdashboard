<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login to Account - Telkomsel Regional Bali Nusra</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
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
                            darkred: '#8B1D24',
                            deepred: '#990011',
                            black: '#18181B'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ED1C24;
            margin: 0;
            padding: 0;
        }

        .btn-hover-effect {
            transition: all 0.25s ease;
        }

        .btn-hover-effect:hover {
            opacity: 0.94;
            transform: translateY(-1.5px);
            box-shadow: 0 8px 25px -4px rgba(139, 29, 36, 0.4);
        }

        .btn-hover-effect:active {
            transform: translateY(0);
        }

        /* Enhanced Multi-Layered 3D Depth Card Shadow */
        .card-shadow {
            box-shadow: 
                0 30px 65px -12px rgba(0, 0, 0, 0.45),
                0 18px 36px -10px rgba(120, 0, 10, 0.4),
                0 0 50px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 bg-[#ED1C24] selection:bg-[#8B1D24] selection:text-white">

    <!-- Centered Login White Card Container with Enhanced 3D Shadow -->
    <div class="w-full max-w-md bg-white rounded-[28px] p-8 sm:p-10 card-shadow text-center relative z-10 my-auto">

        <!-- Header Title & Subtitle -->
        <div class="mb-7">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#18181B] tracking-tight">
                Login
            <p class="text-xs sm:text-sm text-gray-500 mt-2 font-medium">
        
            </p>
        </div>

        <!-- Success Flash Message -->
        @if (session('success'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold text-left">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold text-left">
                @if ($errors->count() == 1)
                    {{ $errors->first() }}
                @else
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login') }}" method="POST" class="space-y-5 text-left">
            @csrf

            <!-- Email / Username Address Field -->
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Email / Username:
                </label>
                <input id="email" 
                       name="email" 
                       type="text" 
                       autocomplete="username" 
                       required 
                       value="{{ old('email') }}"
                       class="w-full px-4 py-3 rounded-xl bg-[#F0F4F8] border border-gray-200 text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#8B1D24] focus:bg-white focus:shadow-md transition-all"
                       placeholder="">
            </div>

            <!-- Password Field -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-gray-700">
                        Password
                    </label>
                    <a href="#" class="text-xs text-gray-500 hover:text-[#8B1D24] transition-colors">
                        Forget Password?
                    </a>
                </div>
                <input id="password" 
                       name="password" 
                       type="password" 
                       required 
                       class="w-full px-4 py-3 rounded-xl bg-[#F0F4F8] border border-gray-200 text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#8B1D24] focus:bg-white focus:shadow-md transition-all"
                       placeholder="">
            </div>

            <!-- Remember Password Checkbox -->
            <div class="flex items-center pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" 
                           name="remember" 
                           class="w-4 h-4 rounded border-gray-300 text-[#8B1D24] focus:ring-[#8B1D24] accent-[#8B1D24]">
                    <span class="text-xs font-medium text-gray-600">Remember Password</span>
                </label>
            </div>

            <!-- Submit Sign In Button -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-xl bg-[#8B1D24] hover:bg-[#72171D] text-white font-bold text-sm btn-hover-effect shadow-lg focus:outline-none cursor-pointer">
                    Sign In
                </button>
            </div>
        </form>

        <!-- Divider -->
        <div class="relative my-5">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="bg-white px-3 text-gray-400 font-semibold tracking-wider uppercase">atau masuk dengan</span>
            </div>
        </div>

        <!-- Google OAuth Login Button -->
        <div>
            <a href="{{ route('auth.google.redirect') }}" 
               class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-bold text-sm btn-hover-effect shadow-sm focus:outline-none transition-all">
                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                Sign in with Google
            </a>
        </div>



    </div>

</body>
</html>
