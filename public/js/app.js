// Accent theme switcher logic
        const themesColors = {
            blue: { '--primary-accent': '#3b82f6', '--primary-hover': '#1d4ed8' },
            green: { '--primary-accent': '#10b981', '--primary-hover': '#059669' },
            teal: { '--primary-accent': '#0d9488', '--primary-hover': '#0f766e' },
            orange: { '--primary-accent': '#f97316', '--primary-hover': '#ea580c' },
            purple: { '--primary-accent': '#8b5cf6', '--primary-hover': '#7c3aed' }
        };

        window.changeTheme = function(themeName) {
            const root = document.documentElement;
            const colors = themesColors[themeName] || themesColors.blue;
            for (const [key, value] of Object.entries(colors)) {
                root.style.setProperty(key, value);
            }
            localStorage.setItem('bonops-theme', themeName);
            
            // Dispatch dynamic event to allow widgets (e.g., Chart.js) to adapt
            window.dispatchEvent(new CustomEvent('accentThemeChanged', { detail: { theme: themeName, colors } }));
        };

        // Dark Mode toggle logic
        window.toggleDarkMode = function() {
            const root = document.documentElement;
            const icon = document.getElementById('darkModeIcon');
            const isDark = root.classList.toggle('dark-mode');
            
            localStorage.setItem('bonops-darkmode', isDark ? 'true' : 'false');

            // Animate icon swap
            if (icon) {
                icon.style.transform = 'rotate(360deg) scale(0.5)';
                icon.style.opacity = '0';
                setTimeout(() => {
                    icon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars';
                    icon.style.transform = 'rotate(0deg) scale(1)';
                    icon.style.opacity = '1';
                }, 200);
            }

            // Update tooltip
            const btn = document.getElementById('darkModeToggle');
            if (btn) {
                btn.title = isDark ? 'Mode Terang' : 'Mode Gelap';
            }

            // Update Chart.js grid colors if charts exist
            window.dispatchEvent(new CustomEvent('darkModeChanged', { detail: { isDark } }));
        };

        // Fullscreen toggle logic
        window.toggleFullscreen = function() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error(`Error attempting to enable full-screen mode: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        };

        document.addEventListener('fullscreenchange', function() {
            const btnIcon = document.querySelector('#fullscreenBtn i');
            if (btnIcon) {
                if (document.fullscreenElement) {
                    btnIcon.className = 'bi bi-fullscreen-exit';
                } else {
                    btnIcon.className = 'bi bi-fullscreen';
                }
            }
        });
        // Sidebar theme switcher logic
        window.changeSidebarTheme = function(themeName) {
            const root = document.documentElement;
            if (themeName === 'light') {
                root.classList.add('sidebar-light');
            } else {
                root.classList.remove('sidebar-light');
            }
            localStorage.setItem('bonops-sidebar-theme', themeName);
        };
        // Fluctuating server status logic
        setInterval(() => {
            const cpu = Math.floor(Math.random() * 11) + 4; // 4 - 14%
            const ping = Math.floor(Math.random() * 15) + 18; // 18 - 32ms
            const el = document.getElementById('systemMetricsText');
            if (el) {
                el.innerText = `CPU ${cpu}% | Ping ${ping}ms`;
            }
        }, 3000);

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const body = document.body;

            // ── Sync dark mode icon on load ──────────────────────
            const isDarkOnLoad = document.documentElement.classList.contains('dark-mode');
            const darkIcon = document.getElementById('darkModeIcon');
            const darkBtn  = document.getElementById('darkModeToggle');
            if (darkIcon) {
                darkIcon.className = isDarkOnLoad ? 'bi bi-sun-fill' : 'bi bi-moon-stars';
            }
            if (darkBtn) {
                darkBtn.title = isDarkOnLoad ? 'Mode Terang' : 'Mode Gelap';
            }
            // ─────────────────────────────────────────────────────

            // Create sidebar backdrop for mobile
            const backdrop = document.createElement('div');
            backdrop.className = 'sidebar-backdrop';
            backdrop.id = 'sidebarBackdrop';
            document.body.appendChild(backdrop);

            // Set initial state for mobile
            if (sidebarToggle && window.innerWidth < 992) {
                sidebarToggle.classList.add('is-collapsed');
            }

            function openSidebar() {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
                sidebarToggle.classList.remove('is-collapsed');
                body.style.overflow = 'hidden'; // prevent scroll behind
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                if (sidebarToggle) sidebarToggle.classList.add('is-collapsed');
                body.style.overflow = '';
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (window.innerWidth >= 992) {
                        body.classList.toggle('sidebar-collapsed');
                        sidebarToggle.classList.toggle('is-collapsed', body.classList.contains('sidebar-collapsed'));
                    } else {
                        if (sidebar.classList.contains('show')) {
                            closeSidebar();
                        } else {
                            openSidebar();
                        }
                    }
                });
            }

            // Close sidebar on backdrop click
            backdrop.addEventListener('click', function() {
                closeSidebar();
            });

            // Close sidebar on resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                    body.style.overflow = '';
                }
            });
        });