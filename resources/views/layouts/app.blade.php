
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal') - Monitoring Mobile Channel</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN for instant rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                        accent: {
                            cyan: '#06b6d4',
                            emerald: '#10b981',
                            violet: '#8b5cf6',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            overflow-x: hidden;
        }

        .heading-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphism card effect */
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            transition: all 0.25s ease;
        }

        .glass-input:focus {
            background: rgba(15, 23, 42, 0.85);
            border-color: #6366f1;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.25);
            outline: none;
        }

        /* Gradient Glowing Background Blobs */
        .bg-blob-1 {
            position: fixed;
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.2) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
            animation: pulse-slow 8s infinite alternate;
        }

        .bg-blob-2 {
            position: fixed;
            bottom: -15%;
            right: -10%;
            width: 60vw;
            height: 60vw;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.18) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
            animation: pulse-slow 10s infinite alternate-reverse;
        }

        @keyframes pulse-slow {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.15) translate(20px, -20px); }
        }
    </style>
</head>
<body class="min-h-screen relative flex flex-col justify-between antialiased selection:bg-brand-500 selection:text-white">
    <!-- Ambient Background Lighting -->
    <div class="bg-blob-1"></div>
    <div class="bg-blob-2"></div>

    <div class="relative z-10 flex-grow flex flex-col">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
