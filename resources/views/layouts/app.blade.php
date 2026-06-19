<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page_title', 'Dashboard') - BonOps</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link href="{{ asset('vendor/css/google-fonts.css') }}" rel="stylesheet">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="{{ asset('vendor/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="{{ asset('vendor/css/bootstrap-icons.min.css') }}?v={{ filemtime(public_path('vendor/css/bootstrap-icons.min.css')) }}" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="{{ asset('vendor/js/chart.umd.js') }}"></script>
    
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

            // Fix native select option hover color in Chromium
            const style = document.createElement('style');
            style.id = 'dynamic-theme-styles';
            style.textContent = `
                select option:checked,
                select option:hover {
                    background-color: ${colors['--primary-accent']} !important;
                    color: #ffffff !important;
                }
            `;
            document.head.appendChild(style);

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
    <link href="{{ asset('vendor/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <!-- DataTables Buttons CSS -->
    <link href="{{ asset('vendor/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <!-- Select2 & Bootstrap 5 Theme CSS -->
    <link href="{{ asset('vendor/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="{{ asset('vendor/css/sweetalert2.min.css') }}" rel="stylesheet">
    
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
            @hasSection('page_title')
            <div class="container-fluid px-0">
                <div class="row align-items-center mb-4">
                    <div class="col-12 col-md-7">
                        {{ Breadcrumbs::render() }}
                        <h1 class="h4 fw-bold mb-1" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">@yield('page_title')</h1>
                        @hasSection('page_description')
                        <p class="mb-0" style="color: var(--text-light); font-size: 13.5px;">@yield('page_description')</p>
                        @endif
                    </div>
                    <div class="col-12 col-md-5 text-md-end mt-3 mt-md-0 d-flex flex-wrap justify-content-md-end gap-2">
                        @yield('page_actions')
                    </div>
                </div>
            </div>
            @endif

            @yield('content')
        </main>

        <!-- FOOTER -->
        @include('layouts.partials.footer')
    </div>

    <!-- Modals Stack -->
    @stack('modals')

    <!-- jQuery & Bootstrap 5.3.3 Bundle with Popper -->
    <script src="{{ asset('vendor/js/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Select2 & SweetAlert2 JS -->
    <script src="{{ asset('vendor/js/select2.min.js') }}"></script>
    <script src="{{ asset('vendor/js/sweetalert2.min.js') }}"></script>
    
    <!-- DataTables Core & Bootstrap 5 Integration JS -->
    <script src="{{ asset('vendor/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- JS Zip (Excel) & PDF Make (PDF) -->
    <script src="{{ asset('vendor/js/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/js/vfs_fonts.js') }}"></script>

    <!-- DataTables Buttons JS -->
    <script src="{{ asset('vendor/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendor/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/js/buttons.print.min.js') }}"></script>

    <!-- JS Global -->
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
    
    <!-- Scripts Stack -->
    @stack('scripts')
</body>
</html>