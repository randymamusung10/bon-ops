<!-- FOOTER -->
        <footer class="dashboard-footer mt-auto">
            <div class="container-fluid px-4">
                <div class="row align-items-center gy-3">

                    <!-- LEFT: Brand & Copyright -->
                    <div class="col-12 col-lg-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="footer-brand-icon">
                                <i class="bi bi-stack"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="footer-brand-name">Bon<span>Ops</span></span>
                                    <span class="footer-version-badge">v1.0.0-beta</span>
                                </div>
                                <div class="footer-copyright">&copy; 2026 BonOps Multi-Tenant ERP. Hak Cipta Dilindungi.</div>
                            </div>
                        </div>
                    </div>

                    <!-- CENTER: System Status -->
                    <div class="col-12 col-lg-4 d-flex justify-content-lg-center">
                        <div class="footer-status-row">
                            <div class="footer-status-item">
                                <div class="footer-status-dot footer-status-online"></div>
                                <span>Sistem Online</span>
                            </div>
                            <div class="footer-status-divider"></div>
                            <div class="footer-status-item">
                                <i class="bi bi-building" style="font-size: 11px; color: var(--primary-accent);"></i>
                                <span>PT Kopi Nusantara</span>
                            </div>
                            <div class="footer-status-divider"></div>
                            <div class="footer-status-item">
                                <i class="bi bi-geo-alt" style="font-size: 11px; color: var(--secondary-accent);"></i>
                                <span>Jakarta Outlet</span>
                            </div>
                            <div class="footer-status-divider d-none d-md-block"></div>
                            <div class="footer-status-item d-none d-md-flex">
                                <i class="bi bi-clock" style="font-size: 11px; color: var(--text-muted);"></i>
                                <span id="footerClock">--:--:--</span>
                                <span style="color: var(--text-muted); opacity: 0.6;">WIB</span>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: Links -->
                    <div class="col-12 col-lg-4 d-flex justify-content-lg-end">
                        <div class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-lg-end">
                            <a href="#" class="footer-link"><i class="bi bi-file-earmark-text me-1"></i>Dokumentasi</a>
                            <span class="footer-link-sep">•</span>
                            <a href="#" class="footer-link"><i class="bi bi-shield-check me-1"></i>Privasi</a>
                            <span class="footer-link-sep">•</span>
                            <a href="#" class="footer-link"><i class="bi bi-question-circle me-1"></i>Bantuan</a>
                            <span class="footer-link-sep">•</span>
                            <a href="#" class="footer-link footer-changelog-link" data-bs-toggle="tooltip" title="Lihat riwayat perubahan versi">
                                <i class="bi bi-journal-code me-1"></i>Changelog
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </footer>

        <script>
            // Footer live clock
            (function() {
                function updateClock() {
                    const el = document.getElementById('footerClock');
                    if (!el) return;
                    const now = new Date();
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    const s = String(now.getSeconds()).padStart(2, '0');
                    el.textContent = `${h}:${m}:${s}`;
                }
                updateClock();
                setInterval(updateClock, 1000);

                // Init tooltips in footer
                document.addEventListener('DOMContentLoaded', function () {
                    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                    tooltipEls.forEach(el => new bootstrap.Tooltip(el, { placement: 'top', trigger: 'hover' }));
                });
            })();
        </script>