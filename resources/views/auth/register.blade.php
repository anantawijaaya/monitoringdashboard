<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create an Account - Telkomsel Regional Bali Nusra</title>
    
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

    <!-- Centered Register White Card Container with 3D Shadow -->
    <div class="w-full max-w-md bg-white rounded-[28px] p-8 sm:p-10 card-shadow text-center relative z-10 my-auto">

        <!-- Header Title & Subtitle -->
        <div class="mb-7">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#18181B] tracking-tight">
                Create an Account
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-2 font-medium">
                Create a account to continue
            </p>
        </div>

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold text-left">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Register Form -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4 text-left">
            @csrf

            <!-- Email Address Field -->
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Email address:
                </label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       autocomplete="email" 
                       required 
                       value="{{ old('email') }}"
                       class="w-full px-4 py-3 rounded-xl bg-[#F0F4F8] border border-gray-200 text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#8B1D24] focus:bg-white focus:shadow-md transition-all"
                       placeholder="esteban_schiller@gmail.com">
            </div>

            <!-- Username Field -->
            <div>
                <label for="name" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Username
                </label>
                <input id="name" 
                       name="name" 
                       type="text" 
                       required 
                       value="{{ old('name') }}"
                       class="w-full px-4 py-3 rounded-xl bg-[#F0F4F8] border border-gray-200 text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#8B1D24] focus:bg-white focus:shadow-md transition-all"
                       placeholder="Username">
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
                       placeholder="••••••••">
                <!-- Hidden matching password_confirmation to satisfy Laravel validation rules -->
                <input type="hidden" name="password_confirmation" id="password_confirmation">
            </div>

            <!-- Terms & Conditions Checkbox -->
            <div class="flex items-center pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" 
                           required
                           class="w-4 h-4 rounded border-gray-300 text-[#8B1D24] focus:ring-[#8B1D24] accent-[#8B1D24]">
                    <span class="text-xs font-medium text-gray-600">I accept terms and conditions</span>
                </label>
            </div>

            <!-- Submit Sign Up Button -->
            <div class="pt-3">
                <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-xl bg-[#8B1D24] hover:bg-[#72171D] text-white font-bold text-sm btn-hover-effect shadow-lg focus:outline-none">
                    Sign Up
                </button>
            </div>
        </form>

        <!-- Footer Login Link -->
        <div class="mt-6 text-xs text-gray-600 font-medium">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-[#8B1D24] underline hover:text-[#72171D] transition-colors">
                Login
            </a>
        </div>

    </div>

    <script>
        // Sync password field to hidden password_confirmation
        const pwdInput = document.getElementById('password');
        const pwdConfirm = document.getElementById('password_confirmation');
        if (pwdInput && pwdConfirm) {
            pwdInput.addEventListener('input', function() {
                pwdConfirm.value = this.value;
            });
        }
    </script>

</body>
</html>
