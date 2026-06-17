<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - BonOps</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link href="{{ asset('vendor/css/google-fonts.css') }}" rel="stylesheet">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="{{ asset('vendor/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="{{ asset('vendor/css/bootstrap-icons.min.css') }}?v={{ filemtime(public_path('vendor/css/bootstrap-icons.min.css')) }}" rel="stylesheet">
    
    <!-- Theme + Dark Mode Preloader (sync, before render) -->
    <script>
        (function() {
            const root = document.documentElement;

            // Apply dark mode first to prevent flash
            if (localStorage.getItem('bonops-darkmode') === 'true') {
                root.classList.add('dark-mode');
            }

            // Apply accent color
            const savedTheme = localStorage.getItem('bonops-theme') || 'blue';
            const themes = {
                blue:   { '--primary-accent': '#3b82f6', '--primary-hover': '#1d4ed8' },
                green:  { '--primary-accent': '#10b981', '--primary-hover': '#059669' },
                teal:   { '--primary-accent': '#0d9488', '--primary-hover': '#0f766e' },
                orange: { '--primary-accent': '#f97316', '--primary-hover': '#ea580c' },
                purple: { '--primary-accent': '#8b5cf6', '--primary-hover': '#7c3aed' }
            };
            const colors = themes[savedTheme] || themes.blue;
            for (const [key, value] of Object.entries(colors)) {
                root.style.setProperty(key, value);
            }
        })();
    </script>
    
    <style>
        :root {
            --primary-accent: #3b82f6; /* Solid Royal Blue */
            --primary-hover: #1d4ed8; 
            --bg-light: #f8fafc; /* Crisp light slate background */
            --text-dark: #1e293b; /* Slate 800 */
            --text-muted: #64748b; /* Slate 500 */
            --border-color: rgba(0, 0, 0, 0.06);
            --primary-accent-rgb: 59, 130, 246;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Orbs Style (Light Pastel theme) */
        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            opacity: 0.5;
            filter: blur(100px);
        }
        .orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, color-mix(in srgb, var(--primary-accent) 15%, transparent) 0%, transparent 70%);
            top: -15%;
            left: -10%;
            animation: floatOrb1 24s linear infinite;
        }
        .orb-2 {
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.12) 0%, transparent 70%);
            bottom: -20%;
            right: -10%;
            animation: floatOrb2 30s linear infinite;
        }
        .orb-3 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.12) 0%, transparent 70%);
            top: 40%;
            left: 60%;
            animation: floatOrb3 28s linear infinite;
        }

        @keyframes floatOrb1 {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(40px, 60px) scale(1.1); }
            66% { transform: translate(-30px, 30px) scale(0.95); }
            100% { transform: translate(0, 0) scale(1); }
        }
        @keyframes floatOrb2 {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-60px, -40px) scale(0.92); }
            100% { transform: translate(0, 0) scale(1); }
        }
        @keyframes floatOrb3 {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-50px, 50px) scale(1.08); }
            66% { transform: translate(40px, -30px) scale(0.92); }
            100% { transform: translate(0, 0) scale(1); }
        }

        /* Immersive Centered Layout */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            z-index: 2;
        }

        /* Login Card Container (Glassmorphism Light) */
        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 44px 40px 36px;
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            box-shadow: 
                0 4px 6px rgba(148, 163, 184, 0.04),
                0 20px 60px rgba(148, 163, 184, 0.10),
                inset 0 1px 0 rgba(255, 255, 255, 1);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            text-align: left;
        }
        
        .login-card:hover {
            border-color: color-mix(in srgb, var(--primary-accent) 20%, transparent);
            box-shadow: 
                0 4px 6px rgba(148, 163, 184, 0.04),
                0 28px 70px color-mix(in srgb, var(--primary-accent) 6%, transparent),
                inset 0 1px 0 rgba(255, 255, 255, 1);
            transform: translateY(-3px);
        }

        /* Brand Elements */
        .brand-header-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
        }

        .brand-logo-circle {
            width: 56px;
            height: 56px;
            background-color: var(--primary-accent);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            font-size: 26px;
            box-shadow: 
                0 8px 20px color-mix(in srgb, var(--primary-accent) 28%, transparent),
                0 2px 4px color-mix(in srgb, var(--primary-accent) 15%, transparent);
            margin-bottom: 14px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .brand-logo-circle:hover {
            transform: scale(1.05) rotate(-5deg);
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .brand-name span {
            color: var(--primary-accent);
        }

        .welcome-desc {
            font-size: 13.5px;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 0;
            line-height: 1.6;
            max-width: 300px;
        }

        /* Form Custom Overrides */
        .form-label {
            font-size: 11.5px;
            font-weight: 700;
            color: #334155; /* Slate 700 */
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 7px;
            display: block;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-group-custom i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8; /* Slate 400 */
            font-size: 15px;
            z-index: 5;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-control-custom {
            width: 100%;
            padding: 11px 42px 11px 42px;
            font-size: 14px;
            color: var(--text-dark);
            background-color: rgba(255, 255, 255, 0.85) !important;
            border: 1.5px solid #e2e8f0; /* Slate 200 - softer */
            border-radius: 8px;
            transition: all 0.25s ease;
            line-height: 1.5;
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
        }

        .form-control-custom:focus {
            background-color: #ffffff !important;
            border-color: var(--primary-accent);
            border-width: 1.5px;
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--primary-accent) 20%, transparent);
            outline: none;
        }

        .form-control-custom:focus + i {
            color: var(--primary-accent);
        }

        .form-check-input {
            width: 16px;
            height: 16px;
            border: 1.5px solid #cbd5e1;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .form-check-input:checked {
            background-color: var(--primary-accent) !important;
            border-color: var(--primary-accent) !important;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--primary-accent) 20%, transparent) !important;
            border-color: var(--primary-accent) !important;
        }

        /* Solid Custom Button */
        .btn-primary-custom {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background-color: var(--primary-accent);
            color: #ffffff;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14.5px;
            transition: all 0.2s ease;
            cursor: pointer;
            letter-spacing: 0.2px;
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
        }

        .btn-primary-custom:active {
            transform: scale(0.97);
        }

        .btn-primary-custom:disabled {
            background-color: var(--primary-accent) !important;
            opacity: 0.7;
            cursor: wait;
            transform: none !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .alert-custom {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 10px;
            font-size: 13px;
            padding: 12px 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .footer-copyright {
            text-align: center;
            margin-top: 28px;
            font-size: 11.5px;
            color: var(--text-muted);
        }

        /* pulseLive for auth page (live dot indicators) */
        @keyframes pulseLive {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }

        /* Accessibility focus-visible for auth */
        :focus-visible {
            outline: 2px solid var(--primary-accent);
            outline-offset: 2px;
            border-radius: 6px;
        }
        .form-control-custom:focus-visible,
        .btn-primary-custom:focus-visible {
            outline: none;
        }

        /* ======================================
           AUTH DARK MODE
           ====================================== */
        html.dark-mode body {
            background-color: #0d1117;
        }

        html.dark-mode .orb-1 {
            background: radial-gradient(circle, color-mix(in srgb, var(--primary-accent) 8%, transparent) 0%, transparent 70%);
            opacity: 0.3;
        }

        html.dark-mode .orb-2 {
            background: radial-gradient(circle, rgba(139, 92, 246, 0.08) 0%, transparent 70%);
        }

        html.dark-mode .orb-3 {
            background: radial-gradient(circle, rgba(20, 184, 166, 0.07) 0%, transparent 70%);
        }

        html.dark-mode .login-card {
            background: rgba(22, 27, 34, 0.92) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow:
                0 4px 6px rgba(0, 0, 0, 0.3),
                0 20px 60px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.06) !important;
        }

        html.dark-mode .login-card:hover {
            border-color: color-mix(in srgb, var(--primary-accent) 30%, transparent) !important;
            box-shadow:
                0 4px 6px rgba(0, 0, 0, 0.4),
                0 28px 70px color-mix(in srgb, var(--primary-accent) 8%, transparent),
                inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        }

        /* Divider in dark mode */
        html.dark-mode .login-card > div[style*="background: linear-gradient"] {
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.08) 30%, rgba(255, 255, 255, 0.08) 70%, transparent 100%) !important;
        }

        html.dark-mode .brand-name {
            color: #f0f6fc;
        }

        html.dark-mode .welcome-desc {
            color: #8b949e;
        }

        html.dark-mode .form-label {
            color: #c9d1d9 !important;
        }

        html.dark-mode .form-control-custom {
            background-color: rgba(33, 38, 45, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #f0f6fc !important;
        }

        html.dark-mode .form-control-custom:focus {
            background-color: #21262d !important;
            border-color: var(--primary-accent) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-accent) 15%, transparent) !important;
        }

        html.dark-mode .form-control-custom::placeholder {
            color: #484f58;
        }

        html.dark-mode .input-group-custom i {
            color: #484f58;
        }

        html.dark-mode .input-group-custom:focus-within i {
            color: var(--primary-accent) !important;
        }

        html.dark-mode #togglePassword {
            color: #484f58 !important;
        }

        html.dark-mode .forgot-password-link {
            color: #8b949e !important;
        }

        html.dark-mode label[for="remember"] {
            color: #8b949e !important;
        }

        html.dark-mode .form-check-input {
            background-color: #21262d !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        html.dark-mode .demo-box {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        html.dark-mode .demo-box:hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
            border-color: color-mix(in srgb, var(--primary-accent) 30%, transparent) !important;
        }

        html.dark-mode .footer-copyright {
            color: #484f58;
        }

        html.dark-mode code {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: var(--primary-accent) !important;
        }

        html.dark-mode .fw-bold.text-dark,
        html.dark-mode .text-dark {
            color: #c9d1d9 !important;
        }

        html.dark-mode .text-muted {
            color: #8b949e !important;
        }
    </style>
</head>
<body>

    <!-- Full-screen Ambient Background Glows -->
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>
    <div class="ambient-orb orb-3"></div>

    <!-- Full-screen Interactive Particles Canvas -->
    <canvas id="login-particle-canvas" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; pointer-events: none; z-index: 1; opacity: 0.5;"></canvas>

    <!-- Main Content Wrapper -->
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Brand header -->
            <div class="brand-header-center">
                <div class="brand-logo-circle">B</div>
                <div class="brand-name">Bon<span>Ops</span></div>
                <div class="welcome-desc">Masuk ke akun Anda untuk mengelola<br>operasional bisnis multi-tenant.</div>
            </div>

            <!-- Visual Divider -->
            <div style="height: 1px; background: linear-gradient(90deg, transparent 0%, rgba(226, 232, 240, 0.8) 30%, rgba(226, 232, 240, 0.8) 70%, transparent 100%); margin: 24px 0;"></div>

            @yield('content')
            
            <div class="footer-copyright">
                &copy; 2026 <b>BonOps Multi-Tenant</b>. Hak Cipta Dilindungi.
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 Bundle with Popper -->
    <script src="{{ asset('vendor/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Canvas Particle Mesh Script (FullScreen) -->
    <script>
        (function() {
            const canvas = document.getElementById('login-particle-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            
            let width, height;
            
            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }
            resize();
            
            window.addEventListener('resize', resize);
            
            const particles = [];
            const particleCount = 60; 
            const maxDistance = 125;
            
            const mouse = {
                x: null,
                y: null,
                radius: 170
            };
            
            window.addEventListener('mousemove', (e) => {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
            });
            
            window.addEventListener('mouseleave', () => {
                mouse.x = null;
                mouse.y = null;
            });
            
            class Particle {
                constructor() {
                    this.reset();
                    this.x = Math.random() * width;
                    this.y = Math.random() * height;
                }
                
                reset() {
                    this.x = Math.random() * width;
                    this.y = Math.random() * height;
                    this.vx = (Math.random() - 0.5) * 0.3;
                    this.vy = (Math.random() - 0.5) * 0.3;
                    this.radius = Math.random() * 1.8 + 1;
                }
                
                update() {
                    this.x += this.vx;
                    this.y += this.vy;
                    
                    if (this.x < 0 || this.x > width) this.vx *= -1;
                    if (this.y < 0 || this.y > height) this.vy *= -1;
                    
                    // Mouse interaction (gentle attraction)
                    if (mouse.x !== null && mouse.y !== null) {
                        const dx = mouse.x - this.x;
                        const dy = mouse.y - this.y;
                        const dist = Math.hypot(dx, dy);
                        if (dist < mouse.radius) {
                            const force = (mouse.radius - dist) / mouse.radius;
                            this.x += (dx / dist) * force * 0.4;
                            this.y += (dy / dist) * force * 0.4;
                        }
                    }
                }
                
                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                    const themeColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-accent').trim() || '#3b82f6';
                    ctx.globalAlpha = 0.2;
                    ctx.fillStyle = themeColor;
                    ctx.fill();
                    ctx.globalAlpha = 1.0;
                }
            }
            
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }
            
            function animate() {
                ctx.clearRect(0, 0, width, height);
                
                const themeColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-accent').trim() || '#3b82f6';
                
                for (let i = 0; i < particles.length; i++) {
                    particles[i].update();
                    particles[i].draw();
                }
                
                // Draw connecting lines
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.hypot(dx, dy);
                        
                        if (dist < maxDistance) {
                            const alpha = (maxDistance - dist) / maxDistance * 0.15;
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = themeColor;
                            ctx.globalAlpha = alpha;
                            ctx.lineWidth = 0.6;
                            ctx.stroke();
                            ctx.globalAlpha = 1.0;
                        }
                    }
                }
                
                requestAnimationFrame(animate);
            }
            
            animate();
        })();
    </script>
</body>
</html>
