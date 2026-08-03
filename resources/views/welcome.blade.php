<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AttendFlow - Enterprise Event Attendance Management & Secure QR Check-in Platform. Manage events, verify attendees, and streamline check-ins with military-grade QR security.">
    <title>AttendFlow — Smart Event Attendance Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
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
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--navy-950);
            color: var(--navy-200);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Animated gradient background */
        .hero-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(59, 130, 246, 0.15), transparent),
                        radial-gradient(ellipse 60% 40% at 80% 50%, rgba(139, 92, 246, 0.1), transparent),
                        radial-gradient(ellipse 60% 40% at 20% 80%, rgba(16, 185, 129, 0.08), transparent);
            animation: bgShift 15s ease-in-out infinite;
        }
        @keyframes bgShift {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Grid pattern overlay */
        .grid-pattern {
            position: fixed;
            inset: 0;
            z-index: 1;
            background-image: 
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .content { position: relative; z-index: 2; }

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
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 4rem;
        }
        .hero-content { max-width: 900px; }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 100px;
            font-size: 0.85rem;
            color: var(--blue-400);
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease forwards;
        }
        .hero-badge .dot {
            width: 6px; height: 6px;
            background: var(--emerald-500);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.5); }
        }
        .hero-title-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .hero-side-card {
            position: absolute;
            top: 50%;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.75rem 1.35rem;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            border-radius: 18px;
            z-index: 10;
            white-space: nowrap;
            text-align: left;
            overflow: hidden;
        }
        .card-left {
            left: -95px;
            border: 1px solid rgba(16, 185, 129, 0.45);
            animation: floatGlowLeft 4s ease-in-out infinite alternate;
        }
        .card-right {
            right: -95px;
            border: 1px solid rgba(59, 130, 246, 0.45);
            animation: floatGlowRight 4.5s ease-in-out infinite alternate;
        }
        @keyframes floatGlowLeft {
            0% {
                transform: translateY(-50%) translateX(0px) rotate(0deg);
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.3), inset 0 0 10px rgba(16, 185, 129, 0.15);
            }
            50% {
                box-shadow: 0 0 40px rgba(16, 185, 129, 0.7), 0 0 20px rgba(52, 211, 153, 0.9), inset 0 0 25px rgba(16, 185, 129, 0.3);
            }
            100% {
                transform: translateY(-68%) translateX(-14px) rotate(-3deg);
                box-shadow: 0 0 25px rgba(16, 185, 129, 0.45), inset 0 0 15px rgba(16, 185, 129, 0.2);
            }
        }
        @keyframes floatGlowRight {
            0% {
                transform: translateY(-50%) translateX(0px) rotate(0deg);
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), inset 0 0 10px rgba(59, 130, 246, 0.15);
            }
            50% {
                box-shadow: 0 0 40px rgba(59, 130, 246, 0.7), 0 0 20px rgba(96, 165, 250, 0.9), inset 0 0 25px rgba(59, 130, 246, 0.3);
            }
            100% {
                transform: translateY(-32%) translateX(14px) rotate(3deg);
                box-shadow: 0 0 25px rgba(59, 130, 246, 0.45), inset 0 0 15px rgba(59, 130, 246, 0.2);
            }
        }
        .card-left::after {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(52, 211, 153, 0.3), transparent);
            animation: cardScanSweep 2.8s infinite;
        }
        .card-right::after {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.3), transparent);
            animation: cardScanSweep 3.2s infinite 0.5s;
        }
        @keyframes cardScanSweep {
            0% { left: -100%; }
            50%, 100% { left: 200%; }
        }
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .card-icon.emerald {
            background: rgba(16, 185, 129, 0.25);
            color: #34d399;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
            animation: iconPulse 2s infinite alternate;
        }
        .card-icon.blue {
            background: rgba(59, 130, 246, 0.25);
            color: #60a5fa;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
            animation: iconPulse 2s infinite alternate 0.5s;
        }
        @keyframes iconPulse {
            0% { transform: scale(1); }
            100% { transform: scale(1.12); }
        }
        .card-title {
            font-size: 0.88rem;
            font-weight: 700;
            color: white;
            line-height: 1.2;
        }
        .card-sub {
            font-size: 0.73rem;
            color: var(--navy-400);
        }
        @media (max-width: 1100px) {
            .hero-side-card { display: none; }
        }
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: white;
            animation: fadeInUp 0.8s ease 0.1s forwards;
            opacity: 0;
        }
        .hero h1 .gradient-text {
            background: linear-gradient(135deg, var(--blue-400), var(--purple-400), var(--emerald-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: 1.2rem;
            color: var(--navy-400);
            max-width: 650px;
            margin: 0 auto 2.5rem;
            animation: fadeInUp 0.8s ease 0.2s forwards;
            opacity: 0;
        }
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease 0.3s forwards;
            opacity: 0;
        }
        .hero-buttons .btn-primary {
            padding: 0.8rem 2rem;
            font-size: 1rem;
        }
        /* Animated QR Hero Showcase */
        .qr-hero-card {
            position: relative;
            width: 280px;
            height: 280px;
            margin: 3rem auto 1.5rem;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(59, 130, 246, 0.35);
            border-radius: 24px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 50px rgba(2, 6, 23, 0.8), 0 0 35px rgba(59, 130, 246, 0.25);
            animation: qrFloat 6s ease-in-out infinite, fadeInUp 0.8s ease 0.35s forwards;
            opacity: 0;
        }

        @keyframes qrFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-14px) rotate(1deg); }
        }

        .qr-code-wrapper {
            position: relative;
            width: 170px;
            height: 170px;
            background: #020617;
            border-radius: 16px;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            overflow: hidden;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.8);
        }

        .qr-code-wrapper svg {
            width: 100%;
            height: 100%;
        }

        /* Scanning laser beam */
        .scan-line {
            position: absolute;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3b82f6, #60a5fa, #10b981, transparent);
            box-shadow: 0 0 15px #3b82f6, 0 0 8px #10b981;
            animation: scanMove 2.5s ease-in-out infinite alternate;
            z-index: 10;
        }

        @keyframes scanMove {
            0% { top: 6%; opacity: 0.3; }
            50% { opacity: 1; }
            100% { top: 88%; opacity: 0.3; }
        }

        /* Floating status pills around QR card */
        .qr-floating-badge {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.6);
            white-space: nowrap;
            z-index: 20;
        }

        .badge-left {
            left: -45px;
            top: 25%;
            animation: badgeFloatLeft 4.5s ease-in-out infinite alternate;
        }

        .badge-right {
            right: -45px;
            bottom: 25%;
            animation: badgeFloatRight 4.5s ease-in-out infinite alternate;
        }

        @keyframes badgeFloatLeft {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-10px, -8px); }
        }

        @keyframes badgeFloatRight {
            0% { transform: translate(0, 0); }
            100% { transform: translate(10px, 8px); }
        }

        .badge-dot-green {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: pulse 1.5s infinite;
        }

        .badge-dot-blue {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #3b82f6;
            box-shadow: 0 0 8px #3b82f6;
            animation: pulse 1.5s infinite;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stats Bar */
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 4rem;
            padding: 3rem 2rem;
            margin-top: 2rem;
            animation: fadeInUp 0.8s ease 0.4s forwards;
            opacity: 0;
        }
        .stat-item { text-align: center; }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, white, var(--navy-300));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-label {
            font-size: 0.85rem;
            color: var(--navy-400);
            margin-top: 0.25rem;
        }

        /* Features Section */
        .features {
            padding: 6rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1rem;
        }
        .section-header p {
            color: var(--navy-400);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        .feature-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--blue-500), transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }
        .feature-card:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.1);
            transform: translateY(-4px);
        }
        .feature-card:hover::before { opacity: 1; }
        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .feature-icon.blue { background: rgba(59, 130, 246, 0.15); color: var(--blue-400); }
        .feature-icon.emerald { background: rgba(16, 185, 129, 0.15); color: var(--emerald-400); }
        .feature-icon.purple { background: rgba(139, 92, 246, 0.15); color: var(--purple-400); }
        .feature-icon.amber { background: rgba(245, 158, 11, 0.15); color: var(--amber-500); }
        .feature-icon.rose { background: rgba(244, 63, 94, 0.15); color: var(--rose-500); }
        .feature-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.75rem;
        }
        .feature-card p {
            color: var(--navy-400);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* Pricing Section - Premium Coming Soon Package */
        .pricing {
            padding: 6rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }
        .pricing-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.45rem 1.25rem;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.35);
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #fbbf24;
            margin-bottom: 1.25rem;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.15);
        }
        .pulse-dot-amber {
            width: 8px;
            height: 8px;
            background: #fbbf24;
            border-radius: 50%;
            box-shadow: 0 0 10px #fbbf24;
            animation: pulse 1.8s infinite;
        }
        .pricing-coming-soon-container {
            position: relative;
            max-width: 960px;
            margin: 0 auto;
        }
        .pricing-glow-aura {
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            width: 85%;
            height: 380px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, rgba(59, 130, 246, 0.12) 45%, transparent 70%);
            filter: blur(50px);
            pointer-events: none;
            z-index: 0;
        }
        .premium-package-card {
            position: relative;
            z-index: 2;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(245, 158, 11, 0.4);
            border-radius: 28px;
            padding: 3.5rem 3rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 0 45px rgba(245, 158, 11, 0.15);
            overflow: hidden;
            transition: all 0.4s ease;
        }
        .premium-package-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #f59e0b, #3b82f6, #8b5cf6, #10b981, #f59e0b);
            background-size: 300% 100%;
            animation: borderGradient 6s ease infinite;
        }
        @keyframes borderGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .package-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .package-badge-vip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 1rem;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(217, 119, 6, 0.35));
            border: 1px solid rgba(245, 158, 11, 0.5);
            border-radius: 100px;
            font-size: 0.78rem;
            font-weight: 800;
            color: #fef08a;
            letter-spacing: 0.05em;
            margin-bottom: 1.25rem;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.25);
        }
        .package-header h3 {
            font-size: 2.3rem;
            font-weight: 900;
            color: white;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }
        .package-tagline {
            color: var(--navy-300);
            font-size: 1.05rem;
            max-width: 680px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .package-status-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 1.25rem 2rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .status-price .price-val {
            font-size: 1.8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #ffffff, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .status-price .price-period {
            font-size: 0.9rem;
            color: var(--navy-400);
            margin-left: 0.5rem;
        }
        .status-pill {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            padding: 0.45rem 1.15rem;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #34d399;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.2);
        }
        .dot-gold {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: pulse 1.5s infinite;
        }
        .package-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 1.25rem;
            margin-bottom: 3rem;
        }
        .feature-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 1.25rem 1.5rem;
            border-radius: 18px;
            transition: all 0.3s ease;
        }
        .feature-item:hover {
            background: rgba(255, 255, 255, 0.045);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-3px);
        }
        .feature-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .feature-icon-box.blue { background: rgba(59, 130, 246, 0.2); color: #60a5fa; box-shadow: 0 0 12px rgba(59, 130, 246, 0.2); }
        .feature-icon-box.emerald { background: rgba(16, 185, 129, 0.2); color: #34d399; box-shadow: 0 0 12px rgba(16, 185, 129, 0.2); }
        .feature-icon-box.purple { background: rgba(139, 92, 246, 0.2); color: #c084fc; box-shadow: 0 0 12px rgba(139, 92, 246, 0.2); }
        .feature-icon-box.amber { background: rgba(245, 158, 11, 0.2); color: #fbbf24; box-shadow: 0 0 12px rgba(245, 158, 11, 0.2); }

        .feature-item h4 {
            font-size: 1.05rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.25rem;
        }
        .feature-item p {
            font-size: 0.88rem;
            color: var(--navy-400);
            line-height: 1.5;
        }
        .waitlist-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.95));
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 22px;
            padding: 2.25rem 2rem;
            text-align: center;
            position: relative;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }
        .waitlist-header h4 {
            font-size: 1.35rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.4rem;
        }
        .waitlist-header p {
            font-size: 0.92rem;
            color: var(--navy-300);
            margin-bottom: 1.5rem;
        }
        .waitlist-form {
            max-width: 550px;
            margin: 0 auto 1.25rem;
        }
        .waitlist-form .input-group {
            display: flex;
            gap: 0.5rem;
            background: rgba(2, 6, 23, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.4rem;
            border-radius: 14px;
            transition: all 0.3s ease;
        }
        .waitlist-form .input-group:focus-within {
            border-color: #fbbf24;
            box-shadow: 0 0 18px rgba(245, 158, 11, 0.3);
        }
        .waitlist-input {
            flex: 1;
            background: transparent;
            border: none;
            padding: 0.75rem 1rem;
            color: white;
            font-size: 0.95rem;
            outline: none;
        }
        .waitlist-input::placeholder {
            color: var(--navy-500);
        }
        .btn-waitlist {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #0f172a;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.92rem;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        .btn-waitlist:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5);
            filter: brightness(1.1);
        }
        .waitlist-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
            padding: 0.9rem 1.25rem;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            margin: 1rem auto 1.25rem;
            max-width: 550px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);
        }
        .waitlist-counter {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: var(--navy-400);
        }
        .avatar-group {
            display: flex;
            align-items: center;
        }
        .avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 2px solid #0f172a;
            margin-left: -8px;
            font-size: 0.65rem;
            font-weight: 800;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar:first-child { margin-left: 0; }

        /* Preview Tiers (Blurred Teaser Cards) */
        .preview-tiers {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-top: 2rem;
            opacity: 0.8;
        }
        .tier-teaser {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
        }
        .tier-teaser:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.18);
        }
        .tier-teaser.featured {
            border-color: rgba(245, 158, 11, 0.35);
            background: rgba(245, 158, 11, 0.04);
        }
        .teaser-lock-badge {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--navy-300);
            background: rgba(255,255,255,0.08);
            padding: 0.25rem 0.7rem;
            border-radius: 100px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-bottom: 0.75rem;
        }
        .teaser-lock-badge.gold {
            color: #fbbf24;
            background: rgba(245, 158, 11, 0.18);
            border: 1px solid rgba(245, 158, 11, 0.35);
        }
        .tier-teaser h4 {
            font-size: 1.15rem;
            color: white;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .teaser-price {
            font-size: 1.6rem;
            font-weight: 800;
            color: white;
            margin: 0.4rem 0;
        }
        .teaser-price span {
            font-size: 0.85rem;
            color: var(--navy-400);
            font-weight: 400;
        }
        .tier-teaser p {
            font-size: 0.84rem;
            color: var(--navy-400);
        }

        @media (max-width: 768px) {
            .premium-package-card {
                padding: 2.25rem 1.5rem;
            }
            .package-header h3 {
                font-size: 1.7rem;
            }
            .package-grid {
                grid-template-columns: 1fr;
            }
            .package-status-box {
                flex-direction: column;
                align-items: flex-start;
            }
            .waitlist-form .input-group {
                flex-direction: column;
                background: transparent;
                border: none;
                padding: 0;
            }
            .waitlist-input {
                background: rgba(2, 6, 23, 0.85);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 12px;
            }
            .btn-waitlist {
                width: 100%;
                justify-content: center;
                border-radius: 12px;
            }
        }

        /* Footer */
        footer {
            border-top: 1px solid rgba(255,255,255,0.05);
            padding: 3rem 2rem;
            text-align: center;
            color: var(--navy-600);
            font-size: 0.85rem;
        }
        footer a { color: var(--blue-400); text-decoration: none; }

        /* Responsive Mobile Typography (< 768px) */
        @media (max-width: 768px) {
            .hero {
                padding: 5.5rem 1.25rem 1.5rem;
            }
            .hero h1 {
                font-size: clamp(1.55rem, 6.5vw, 2rem) !important;
                line-height: 1.2;
                margin-bottom: 1rem;
            }
            .hero p {
                font-size: 0.95rem !important;
                line-height: 1.55;
                margin-bottom: 1.75rem;
                padding: 0 0.5rem;
            }
            .stats-bar {
                gap: 1.25rem;
                flex-wrap: wrap;
                padding: 1.5rem 1rem 0.5rem;
                margin-top: 1rem;
            }
            .stat-value { font-size: 1.6rem; }
            .features {
                padding: 2rem 1.25rem 3rem;
            }
            .pricing {
                padding: 2rem 1.25rem 3rem;
            }
            .section-header {
                margin-bottom: 2rem;
            }
            .section-header h2 {
                font-size: 1.75rem !important;
            }
            .section-header p {
                font-size: 0.95rem !important;
            }
            .features-grid { grid-template-columns: 1fr; gap: 1rem; }
            .pricing-grid { grid-template-columns: 1fr; gap: 1rem; }
        }
    </style>
</head>
<body>
    <div class="hero-bg"></div>
    <div class="grid-pattern"></div>
    
    <div class="content">
        <!-- Navigation -->
        <nav class="homepage-nav">
            <a href="/" class="logo">
                <div class="logo-icon">AF</div>
                <span class="logo-text">AttendFlow</span>
            </a>

            <!-- Desktop Links -->
            <div class="nav-desktop-links">
                <a href="/public-events" class="{{ request()->is('public-events*') ? 'active' : '' }}">Explore Events</a>
                <a href="#features">Features</a>
                <a href="#pricing">Pricing</a>
                <a href="#security">Security</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary">Dashboard →</a>
                @else
                    <a href="/login" class="btn-secondary">Sign In</a>
                    <a href="/register" class="btn-primary">Get Started</a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle Button -->
            <button type="button" class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Toggle navigation menu">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="hamburgerIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Mobile Drawer Overlay -->
            <div class="mobile-nav-drawer" id="mobileNavDrawer">
                <a href="/public-events" onclick="closeMobileNav()">Explore Events</a>
                <a href="#features" onclick="closeMobileNav()">Features</a>
                <a href="#pricing" onclick="closeMobileNav()">Pricing</a>
                <a href="#security" onclick="closeMobileNav()">Security</a>
                
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
                const btn = document.getElementById('mobileMenuBtn');
                const drawer = document.getElementById('mobileNavDrawer');
                const icon = document.getElementById('hamburgerIcon');

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

            function closeMobileNav() {
                const drawer = document.getElementById('mobileNavDrawer');
                const icon = document.getElementById('hamburgerIcon');
                if (drawer) drawer.classList.remove('open');
                if (icon) icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
            }

            async function handleWaitlistSubmit(event) {
                event.preventDefault();
                const form = document.getElementById('pricingWaitlistForm');
                const emailInput = document.getElementById('waitlistEmail');
                const successMsg = document.getElementById('waitlistSuccess');
                const submitBtn = form ? form.querySelector('button[type="submit"]') : null;

                if (!emailInput || !emailInput.value) return;

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.7';
                }

                try {
                    const formData = new FormData();
                    formData.append('email', emailInput.value);

                    const response = await fetch('/pricing-waitlist', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        if (form && successMsg) {
                            form.style.display = 'none';
                            successMsg.style.display = 'flex';
                        }
                    } else {
                        alert(data.message || 'Unable to join waitlist. Please verify your email.');
                        if (submitBtn) submitBtn.disabled = false;
                    }
                } catch (err) {
                    console.error('Waitlist submission error:', err);
                    alert('Submission failed. Please try again.');
                    if (submitBtn) submitBtn.disabled = false;
                }
            }
        </script>

        <!-- Hero -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="dot"></span>
                    Enterprise-grade event management
                </div>
                <div class="hero-title-container">
                    <!-- Floating Animated Security Card Left -->
                    <div class="hero-side-card card-left">
                        <div class="card-icon emerald">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <div class="card-title">AES-256 Encrypted</div>
                            <div class="card-sub">HMAC Signed QR</div>
                        </div>
                    </div>

                    <h1>
                        Effortless Event<br>
                        <span class="gradient-text">Attendance Management</span>
                    </h1>

                    <!-- Floating Animated Security Card Right -->
                    <div class="hero-side-card card-right">
                        <div class="card-icon blue">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <div class="card-title">Pre-Verified Pass</div>
                            <div class="card-sub">Anti-Fraud Gate Shield</div>
                        </div>
                    </div>
                </div>
                <p>
                    Secure QR check-ins, real-time dashboards, role-based access control, and multi-gate management. 
                    Everything you need to run events of any scale.
                </p>
                <div class="hero-buttons">
                    <a href="/public-events" class="btn-primary" style="background: linear-gradient(135deg, #2563eb, #7c3aed);">Browse Public Events →</a>
                    <a href="/register" class="btn-secondary">Start Free Trial</a>
                </div>

                <!-- Animated QR Showcase -->
                <div class="qr-hero-card">
                    <!-- Floating Badge Left -->
                    <div class="qr-floating-badge badge-left">
                        <span class="badge-dot-green"></span>
                        <span>Pass Verified ✓</span>
                    </div>

                    <!-- Floating Badge Right -->
                    <div class="qr-floating-badge badge-right">
                        <span class="badge-dot-blue"></span>
                        <span>AES-256 Encrypted</span>
                    </div>

                    <!-- QR Wrapper with Scan Line -->
                    <div class="qr-code-wrapper">
                        <div class="scan-line"></div>
                        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Top-Left Finder -->
                            <rect x="5" y="5" width="26" height="26" rx="5" fill="#3b82f6" fill-opacity="0.15" stroke="#3b82f6" stroke-width="2"/>
                            <rect x="11" y="11" width="14" height="14" rx="3" fill="#60a5fa"/>
                            
                            <!-- Top-Right Finder -->
                            <rect x="69" y="5" width="26" height="26" rx="5" fill="#3b82f6" fill-opacity="0.15" stroke="#3b82f6" stroke-width="2"/>
                            <rect x="75" y="11" width="14" height="14" rx="3" fill="#60a5fa"/>
                            
                            <!-- Bottom-Left Finder -->
                            <rect x="5" y="69" width="26" height="26" rx="5" fill="#3b82f6" fill-opacity="0.15" stroke="#3b82f6" stroke-width="2"/>
                            <rect x="11" y="75" width="14" height="14" rx="3" fill="#60a5fa"/>
                            
                            <!-- Data Modules -->
                            <rect x="36" y="8" width="6" height="6" rx="2" fill="#38bdf8"/>
                            <rect x="46" y="8" width="6" height="6" rx="2" fill="#818cf8"/>
                            <rect x="56" y="8" width="6" height="6" rx="2" fill="#38bdf8"/>
                            
                            <rect x="36" y="18" width="6" height="6" rx="2" fill="#818cf8"/>
                            <rect x="46" y="18" width="16" height="6" rx="2" fill="#10b981"/>
                            
                            <rect x="8" y="36" width="6" height="6" rx="2" fill="#38bdf8"/>
                            <rect x="18" y="36" width="6" height="6" rx="2" fill="#818cf8"/>
                            <rect x="36" y="36" width="12" height="12" rx="3" fill="#60a5fa"/>
                            <rect x="52" y="36" width="6" height="6" rx="2" fill="#34d399"/>
                            <rect x="62" y="36" width="12" height="6" rx="2" fill="#38bdf8"/>
                            <rect x="78" y="36" width="14" height="6" rx="2" fill="#a78bfa"/>
                            
                            <rect x="8" y="46" width="16" height="6" rx="2" fill="#a78bfa"/>
                            <rect x="52" y="52" width="16" height="6" rx="2" fill="#60a5fa"/>
                            <rect x="72" y="46" width="6" height="12" rx="2" fill="#34d399"/>
                            <rect x="82" y="46" width="10" height="6" rx="2" fill="#818cf8"/>
                            
                            <rect x="36" y="62" width="6" height="12" rx="2" fill="#38bdf8"/>
                            <rect x="46" y="68" width="12" height="6" rx="2" fill="#10b981"/>
                            <rect x="62" y="62" width="10" height="6" rx="2" fill="#60a5fa"/>
                            <rect x="76" y="62" width="16" height="6" rx="2" fill="#a78bfa"/>
                            
                            <rect x="36" y="78" width="16" height="6" rx="2" fill="#a78bfa"/>
                            <rect x="56" y="78" width="8" height="8" rx="2" fill="#34d399"/>
                            <rect x="68" y="74" width="6" height="14" rx="2" fill="#38bdf8"/>
                            <rect x="78" y="78" width="14" height="14" rx="3" fill="#60a5fa"/>
                        </svg>
                    </div>
                </div>
                <div class="stats-bar">
                    <div class="stat-item">
                        <div class="stat-value">10K+</div>
                        <div class="stat-label">Events Managed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">2M+</div>
                        <div class="stat-label">Attendees Checked In</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">99.9%</div>
                        <div class="stat-label">Uptime SLA</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">50ms</div>
                        <div class="stat-label">Avg. Scan Time</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="features" id="features">
            <div class="section-header">
                <h2>Everything You Need</h2>
                <p>Powerful features designed for events of every size, from intimate workshops to massive conferences.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card" id="security">
                    <div class="feature-icon blue">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <h3>Encrypted QR Codes</h3>
                    <p>Military-grade AES-256 encryption with HMAC signatures. One-time scan, expiration, revocation, and tamper detection built in.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon emerald">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3>Pre-Event Verification</h3>
                    <p>Email and OTP verification eliminates fake registrations. Only verified attendees receive QR codes and event passes.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon purple">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3>Role-Based Access</h3>
                    <p>VIP, Speaker, Staff, Media — assign roles and control access to specific gates and areas with granular permissions.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon amber">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3>Real-Time Dashboard</h3>
                    <p>Live attendance tracking, gate activity monitoring, capacity management, and peak hour analytics — all updating in real-time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon rose">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3>Multi-Gate Management</h3>
                    <p>Configure unlimited entry points. Main entrance, VIP gate, speaker entrance — each with its own authorized roles and real-time stats.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3>Reports & Exports</h3>
                    <p>Attendance, registration, verification, and gate reports. Export to PDF, Excel, or CSV with one click.</p>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section class="pricing" id="pricing">
            <div class="section-header">
                <div class="pricing-badge">
                    <span class="pulse-dot-amber"></span>
                    <span>PREMIUM PACKAGE &bull; COMING SOON</span>
                </div>
                <h2>Simple, Transparent Pricing</h2>
                <p>We're finalizing our flexible subscription packages &amp; enterprise tiers. Join the early access waitlist to lock in exclusive launch perks!</p>
            </div>

            <div class="pricing-coming-soon-container">
                <div class="pricing-glow-aura"></div>

                <div class="premium-package-card">
                    <div class="package-header">
                        <div class="package-badge-vip">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            VIP EARLY ACCESS SUITE
                        </div>
                        <h3>AttendFlow All-Access Premium Package</h3>
                        <p class="package-tagline">Everything you need for seamless event check-ins, encrypted QR ticketing, multi-gate security, and real-time attendance intelligence.</p>
                    </div>

                    <div class="package-status-box">
                        <div class="status-price">
                            <span class="price-val">Free VIP Early Access</span>
                            <span class="price-period">/ Beta &amp; Launch Period</span>
                        </div>
                        <div class="status-pill">
                            <span class="dot-gold"></span> Packages Launching Soon
                        </div>
                    </div>

                    <div class="package-grid">
                        <div class="feature-item">
                            <div class="feature-icon-box blue">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div>
                                <h4>Unlimited Events &amp; Registrations</h4>
                                <p>Host unlimited small or mega events with high-volume ticket scanning capability.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon-box emerald">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <h4>Encrypted &amp; Dynamic QR Codes</h4>
                                <p>Prevent ticket forgery with cryptographic QR signatures and instant validation.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon-box purple">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <h4>Real-Time Multi-Gate Analytics</h4>
                                <p>Monitor live gate throughput, peak entry times, and gate keeper activity in real-time.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon-box amber">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h4>White Label &amp; Custom Domain</h4>
                                <p>Custom branding, personalized email receipts, PDF tickets, and organizational subdomains.</p>
                            </div>
                        </div>
                    </div>

                    <div class="waitlist-card">
                        <div class="waitlist-header">
                            <h4>Get Notified &amp; Claim 50% Off Lifetime Discount</h4>
                            <p>Be the first to know when full subscription packages launch. Zero commitment required.</p>
                        </div>
                        <form class="waitlist-form" id="pricingWaitlistForm" onsubmit="handleWaitlistSubmit(event)">
                            <div class="input-group">
                                <input type="email" id="waitlistEmail" placeholder="Enter your work email..." required class="waitlist-input">
                                <button type="submit" class="btn-waitlist">
                                    <span>Join VIP Waitlist</span>
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </form>
                        <div id="waitlistSuccess" class="waitlist-success" style="display: none;">
                            <span class="check-icon">✓</span> You're on the VIP list! We'll send your launch invitation and 50% discount code first.
                        </div>
                        <div class="waitlist-counter">
                            <div class="avatar-group">
                                <span class="avatar" style="background:#3b82f6;">JD</span>
                                <span class="avatar" style="background:#10b981;">SK</span>
                                <span class="avatar" style="background:#8b5cf6;">AM</span>
                                <span class="avatar" style="background:#f59e0b;">+</span>
                            </div>
                            <span>Over <strong>1,420+</strong> event organizers on the early access waitlist</span>
                        </div>
                    </div>
                </div>

                <div class="preview-tiers">
                    <div class="tier-teaser">
                        <span class="teaser-lock-badge">🔒 Launching Soon</span>
                        <h4>Starter Package</h4>
                        <div class="teaser-price">$29<span>/month</span></div>
                        <p>10 Events &bull; 2,000 Registrations</p>
                    </div>
                    <div class="tier-teaser featured">
                        <span class="teaser-lock-badge gold">⭐ Most Popular</span>
                        <h4>Business Package</h4>
                        <div class="teaser-price">$99<span>/month</span></div>
                        <p>Unlimited Events &bull; Multi-Gate Access</p>
                    </div>
                    <div class="tier-teaser">
                        <span class="teaser-lock-badge">🔒 Launching Soon</span>
                        <h4>Enterprise Package</h4>
                        <div class="teaser-price">Custom</div>
                        <p>White Label &bull; Dedicated Support</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <p>&copy; {{ date('Y') }} AttendFlow. Built with Laravel. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
