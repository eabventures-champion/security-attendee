<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AttendFlow') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cinzel:wght@400;600;700;800&family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --navy-950: #020617;
            --navy-900: #0f172a;
            --navy-800: #1e293b;
            --navy-700: #334155;
            --navy-600: #475569;
            --navy-400: #94a3b8;
            --navy-300: #cbd5e1;
            --navy-200: #e2e8f0;
            --navy-100: #f1f5f9;
            --blue-500: #3b82f6;
            --blue-600: #2563eb;
            --blue-400: #60a5fa;
            --emerald-500: #10b981;
            --emerald-400: #34d399;
            --amber-500: #f59e0b;
            --rose-500: #f43f5e;
            --purple-500: #8b5cf6;
            --purple-400: #a78bfa;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Navigation Bar Styles */
        nav, nav.homepage-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 72px;
            z-index: 100;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(20px);
            background: rgba(15, 23, 42, 0.85);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            box-sizing: border-box;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: white;
            z-index: 101;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--blue-500), var(--purple-500));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.1rem;
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, var(--navy-300) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Desktop Nav Links */
        .nav-desktop-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-desktop-links a {
            color: var(--navy-400);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .nav-desktop-links a:hover, .nav-desktop-links a.active {
            color: white;
        }

        /* Mobile Hamburger Toggle Button (Hidden on Desktop) */
        .mobile-toggle-btn {
            display: none;
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: white;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            z-index: 101;
            transition: all 0.2s ease;
        }
        .mobile-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        /* Mobile Drawer Menu Overlay (Ultra-smooth transition & 100% solid high-opacity background) */
        .mobile-nav-drawer {
            display: flex;
            position: fixed;
            top: 72px;
            left: 0;
            right: 0;
            background: #020617; /* Solid 100% Opaque Background */
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0 1.5rem;
            flex-direction: column;
            gap: 0.75rem;
            z-index: 99;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.95);
            max-height: 0;
            opacity: 0;
            transform: translateY(-12px);
            overflow: hidden;
            pointer-events: none;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                        opacity 0.35s ease-in-out, 
                        transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        padding 0.35s ease;
        }
        .mobile-nav-drawer.open {
            max-height: 500px;
            opacity: 1;
            padding: 1.5rem;
            transform: translateY(0);
            pointer-events: auto;
        }
        .mobile-nav-drawer a {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            transition: background 0.2s, color 0.2s;
        }
        .mobile-nav-drawer a:hover {
            background: rgba(255,255,255,0.06);
            color: white;
        }
        .mobile-nav-drawer .mobile-cta-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 0.5rem;
        }

        /* Responsive Breakpoint for Mobile Nav (< 768px) */
        @media (max-width: 768px) {
            .nav-desktop-links {
                display: none !important;
            }
            .mobile-toggle-btn {
                display: flex !important;
            }
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--blue-500), var(--purple-500));
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            border: none;
            cursor: pointer;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        .btn-secondary {
            background: rgba(255,255,255,0.05);
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col items-center justify-start relative overflow-x-hidden overflow-y-auto">
    
    <!-- Navigation Bar -->
    <nav class="homepage-nav">
        <a href="/" class="logo">
            <div class="logo-icon">BS</div>
            <span class="logo-text">Built Studios</span>
        </a>

        <!-- Desktop Links -->
        <div class="nav-desktop-links">
            <a href="/public-events" class="{{ request()->is('public-events*') ? 'active' : '' }}">Explore Events</a>
            <a href="/#features">Features</a>
            <a href="/#pricing">Pricing</a>
            <a href="/#security">Security</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-primary">Dashboard →</a>
            @else
                <a href="/login" class="btn-secondary">Sign In</a>
                <a href="/register" class="btn-primary">Get Started</a>
            @endauth
        </div>

        <!-- Mobile Menu Toggle Button -->
        <button type="button" class="mobile-toggle-btn" id="guestMobileMenuBtn" aria-label="Toggle navigation menu">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path id="guestHamburgerIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Mobile Drawer Overlay -->
        <div class="mobile-nav-drawer" id="guestMobileNavDrawer">
            <a href="/public-events" onclick="closeGuestMobileNav()">Explore Events</a>
            <a href="/#features" onclick="closeGuestMobileNav()">Features</a>
            <a href="/#pricing" onclick="closeGuestMobileNav()">Pricing</a>
            <a href="/#security" onclick="closeGuestMobileNav()">Security</a>
            
            <div class="mobile-cta-group">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary" style="text-align: center;">Dashboard →</a>
                @else
                    <a href="/login" class="btn-secondary" style="text-align: center;">Sign In</a>
                    <a href="/register" class="btn-primary" style="text-align: center;">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('guestMobileMenuBtn');
            const drawer = document.getElementById('guestMobileNavDrawer');
            const icon = document.getElementById('guestHamburgerIcon');

            if (btn && drawer) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = drawer.classList.toggle('open');
                    if (icon) {
                        if (isOpen) {
                            icon.setAttribute('d', 'M6 18L18 6M6 6l12 12');
                        } else {
                            icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
                        }
                    }
                });

                document.addEventListener('click', function(e) {
                    if (drawer.classList.contains('open') && !drawer.contains(e.target) && !btn.contains(e.target)) {
                        drawer.classList.remove('open');
                        if (icon) icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
                    }
                });
            }
        });

        function closeGuestMobileNav() {
            const drawer = document.getElementById('guestMobileNavDrawer');
            const icon = document.getElementById('guestHamburgerIcon');
            if (drawer) drawer.classList.remove('open');
            if (icon) icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
        }
    </script>

    <!-- Background Decor -->
    <div class="fixed top-[-10%] left-[-10%] w-96 h-96 bg-blue-600/20 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-600/20 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="fixed inset-0 z-[-1] pointer-events-none opacity-[0.03] bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:24px_24px]"></div>

    <!-- Main Content Area -->
    <div class="w-full flex items-center justify-center px-4 sm:px-8 relative z-10 min-h-screen pt-28 sm:pt-32 pb-12">
        @yield('content')
        {{ $slot ?? '' }}
    </div>

    @livewireScripts
</body>
</html>
