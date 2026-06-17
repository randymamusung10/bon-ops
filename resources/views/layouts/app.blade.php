<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - BonOps</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Accent Color Theme + Dark Mode Preloader (must be synchronous, before render) -->
    <script>
        (function() {
            const root = document.documentElement;

            // 1. Apply dark mode FIRST to prevent flash
            const isDark = localStorage.getItem('bonops-darkmode') === 'true';
            if (isDark) {
                root.classList.add('dark-mode');
            }

            // 2. Apply accent color theme
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

            // 3. Apply sidebar theme
            const savedSidebar = localStorage.getItem('bonops-sidebar-theme') || 'dark';
            if (savedSidebar === 'light') {
                root.classList.add('sidebar-light');
            } else {
                root.classList.remove('sidebar-light');
            }
        })();
    </script>
    
    <!-- CSS Global -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

    <!-- SIDEBAR -->
    @include('layouts.partials.sidebar')

    <!-- MAIN CONTENT WRAPPER -->
    <div id="content-wrapper">
        <!-- TOP NAVBAR -->
        @include('layouts.partials.navbar')

        <!-- Mobile Context Strip (visible only on small screens) -->
        <div class="d-flex d-lg-none align-items-center gap-2 px-4 py-2" style="background: rgba(255,255,255,0.6); border-bottom: 1px solid var(--border-color); font-size: 11.5px; backdrop-filter: blur(8px);">
            <i class="bi bi-building" style="color: var(--primary-accent); font-size: 12px;"></i>
            <span style="color: var(--text-muted);">PT Kopi Nusantara</span>
            <span style="color: var(--border-color);">•</span>
            <i class="bi bi-geo-alt" style="color: var(--secondary-accent); font-size: 12px;"></i>
            <span style="color: var(--text-muted);">Jakarta Outlet</span>
        </div>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- FOOTER -->
        @include('layouts.partials.footer')
    </div>

    <!-- Bootstrap 5.3.3 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JS Global -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>