<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.11/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables Buttons CSS -->
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 & Bootstrap 5 Theme CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- CSS Global (Loaded last to ensure custom overrides win) -->
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
    @stack('styles')
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
            <span style="color: var(--text-muted);">{{ Session::get('tenant_name', 'Perusahaan Anda') }}</span>
            <span style="color: var(--border-color);">•</span>
            <i class="bi bi-geo-alt" style="color: var(--secondary-accent); font-size: 12px;"></i>
            <span style="color: var(--text-muted);">{{ Session::get('branch_name', 'Semua Cabang') }}</span>
        </div>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- FOOTER -->
        @include('layouts.partials.footer')
    </div>

    <!-- Modals Stack -->
    @stack('modals')

    <!-- jQuery & Bootstrap 5.3.3 Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Select2 & SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- DataTables Core & Bootstrap 5 Integration JS -->
    <script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.11/js/dataTables.bootstrap5.min.js"></script>

    <!-- JS Zip (Excel) & PDF Make (PDF) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- JS Global -->
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
    
    <!-- Scripts Stack -->
    @stack('scripts')
</body>
</html>