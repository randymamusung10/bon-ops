<!-- TOP NAVBAR -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="context-badge">
                    <i class="bi bi-building"></i>
                    <span>Tenant: <b>PT Kopi Nusantara</b></span>
                    <div class="vr bg-secondary opacity-20"></div>
                    <i class="bi bi-geo-alt"></i>
                    <span>Cabang: <b>Jakarta Outlet</b></span>
                </div>
                <!-- Live Server KPI status -->
                <div class="d-none d-lg-flex align-items-center gap-2 px-3 py-1.5 rounded-pill border" style="background: rgba(16, 185, 129, 0.03); border-color: rgba(16, 185, 129, 0.15); font-size: 11px;">
                    <div class="rounded-circle bg-success" style="width: 6px; height: 6px; box-shadow: 0 0 8px #10b981; animation: pulseLive 1.5s infinite;"></div>
                    <span class="text-muted">Status: </span>
                    <span class="fw-semibold text-dark" id="systemMetricsText">CPU 8% | Ping 24ms</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="navbar-icon-btn" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute bg-danger border border-white rounded-circle" style="width: 8px; height: 8px; top: 8px; right: 8px;"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2 border shadow-sm rounded-3 dropdown-menu-custom mt-2" aria-labelledby="notificationsDropdown" style="min-width: 320px;">
                        <li>
                            <div class="d-flex justify-content-between align-items-center px-2 py-1 mb-2">
                                <span class="fw-bold text-dark" style="font-size: 13.5px;">Notifikasi</span>
                                <span class="badge bg-danger text-white fw-normal" style="font-size: 10px; border-radius: 20px;">3 Baru</span>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        
                        <!-- Notification Item 1 -->
                        <li>
                            <a class="dropdown-item dropdown-item-custom p-2 rounded d-flex align-items-start gap-2 mb-1" href="#" style="background: none;">
                                <div class="p-1.5 bg-warning-subtle text-warning rounded-circle" style="font-size: 14px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div class="d-flex flex-column overflow-hidden w-100">
                                    <span class="fw-semibold text-dark text-wrap" style="font-size: 12px; line-height: 1.3;">Stok Kopi Arabika menipis di bawah minimal!</span>
                                    <span class="text-muted mt-0.5" style="font-size: 9.5px;">5 menit yang lalu</span>
                                </div>
                            </a>
                        </li>
                        
                        <!-- Notification Item 2 -->
                        <li>
                            <a class="dropdown-item dropdown-item-custom p-2 rounded d-flex align-items-start gap-2 mb-1" href="#" style="background: none;">
                                <div class="p-1.5 bg-success-subtle text-success rounded-circle" style="font-size: 14px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="d-flex flex-column overflow-hidden w-100">
                                    <span class="fw-semibold text-dark text-wrap" style="font-size: 12px; line-height: 1.3;">Penerimaan PT Makmur Jaya diverifikasi.</span>
                                    <span class="text-muted mt-0.5" style="font-size: 9.5px;">1 jam yang lalu</span>
                                </div>
                            </a>
                        </li>
                        
                        <!-- Notification Item 3 -->
                        <li>
                            <a class="dropdown-item dropdown-item-custom p-2 rounded d-flex align-items-start gap-2 mb-1" href="#" style="background: none;">
                                <div class="p-1.5 bg-info-subtle text-info rounded-circle" style="font-size: 14px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="bi bi-info-circle-fill"></i>
                                </div>
                                <div class="d-flex flex-column overflow-hidden w-100">
                                    <span class="fw-semibold text-dark text-wrap" style="font-size: 12px; line-height: 1.3;">Buka shift baru disetujui Supervisor.</span>
                                    <span class="text-muted mt-0.5" style="font-size: 9.5px;">3 jam yang lalu</span>
                                </div>
                            </a>
                        </li>
                        
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item text-center fw-semibold text-primary py-1.5" href="#" style="font-size: 12px; background: none;">
                                Tandai Semua Telah Dibaca
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Downloads Dropdown -->
                <div class="dropdown">
                    <button class="navbar-icon-btn" type="button" id="downloadsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Daftar Unduhan">
                        <i class="bi bi-download"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2 border shadow-sm rounded-3 dropdown-menu-custom mt-2" aria-labelledby="downloadsDropdown" style="min-width: 320px;">
                        <li>
                            <div class="d-flex justify-content-between align-items-center px-2 py-1 mb-2">
                                <span class="fw-bold text-dark" style="font-size: 13.5px;">Daftar Unduhan</span>
                                <span class="badge bg-light text-muted fw-normal" style="font-size: 10px; border: 1px solid #e2e8f0;">3 File</span>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        
                        <!-- Download Item 1 -->
                        <li>
                            <a class="dropdown-item dropdown-item-custom p-2 rounded d-flex align-items-start gap-2 mb-1" href="#" style="background: none;">
                                <div class="p-2 bg-danger-subtle text-danger rounded" style="font-size: 16px; flex-shrink: 0;">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="d-flex flex-column overflow-hidden w-100">
                                    <span class="fw-semibold text-dark text-truncate" style="font-size: 12px;">Laporan Penjualan Mei 2026.pdf</span>
                                    <span class="text-muted" style="font-size: 10px;">2.4 MB • Selesai • 2 jam yang lalu</span>
                                </div>
                            </a>
                        </li>
                        
                        <!-- Download Item 2 -->
                        <li>
                            <a class="dropdown-item dropdown-item-custom p-2 rounded d-flex align-items-start gap-2 mb-1" href="#" style="background: none;">
                                <div class="p-2 bg-success-subtle text-success rounded" style="font-size: 16px; flex-shrink: 0;">
                                    <i class="bi bi-file-earmark-excel-fill"></i>
                                </div>
                                <div class="d-flex flex-column overflow-hidden w-100">
                                    <span class="fw-semibold text-dark text-truncate" style="font-size: 12px;">Stok Awal Cabang Jakarta.xlsx</span>
                                    <span class="text-muted" style="font-size: 10px;">450 KB • Selesai • Kemarin</span>
                                </div>
                            </a>
                        </li>
                        
                        <!-- Download Item 3 -->
                        <li>
                            <a class="dropdown-item dropdown-item-custom p-2 rounded d-flex align-items-start gap-2 mb-1" href="#" style="background: none;">
                                <div class="p-2 bg-danger-subtle text-danger rounded" style="font-size: 16px; flex-shrink: 0;">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="d-flex flex-column overflow-hidden w-100">
                                    <span class="fw-semibold text-dark text-truncate" style="font-size: 12px;">Faktur Supplier PT Makmur.pdf</span>
                                    <span class="text-muted" style="font-size: 10px;">1.2 MB • Selesai • 3 hari yang lalu</span>
                                </div>
                            </a>
                        </li>
                        
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item text-center fw-semibold text-primary py-1.5" href="#" style="font-size: 12px; background: none;">
                                Lihat Semua Unduhan
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Fullscreen Toggle -->
                <button class="navbar-icon-btn" id="fullscreenBtn" onclick="toggleFullscreen()" title="Fullscreen">
                    <i class="bi bi-fullscreen"></i>
                </button>

                <!-- Dark Mode Toggle -->
                <button class="navbar-icon-btn" id="darkModeToggle" onclick="toggleDarkMode()" title="Toggle Dark Mode" style="position: relative;">
                    <i class="bi bi-moon-stars" id="darkModeIcon" style="transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.2s ease;"></i>
                </button>

                <!-- Accent Color Switcher -->
                <div class="dropdown">
                    <button class="navbar-icon-btn" type="button" id="themeSwitcher" data-bs-toggle="dropdown" aria-expanded="false" title="Pilih Warna Aksen">
                        <i class="bi bi-palette"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2 border shadow-sm rounded-3 dropdown-menu-custom mt-2" aria-labelledby="themeSwitcher" style="min-width: 140px;">
                        <li><div class="dropdown-header text-dark fw-bold px-2 py-1" style="font-size: 11px;">Warna Aksen ERP</div></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 border-0 bg-transparent w-100 text-start" onclick="changeTheme('blue')">
                                <span class="rounded-circle" style="width: 12px; height: 12px; background: #3b82f6; display: inline-block;"></span>
                                <span style="font-size: 12.5px;">Royal Blue</span>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 border-0 bg-transparent w-100 text-start" onclick="changeTheme('green')">
                                <span class="rounded-circle" style="width: 12px; height: 12px; background: #10b981; display: inline-block;"></span>
                                <span style="font-size: 12.5px;">Emerald Green</span>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 border-0 bg-transparent w-100 text-start" onclick="changeTheme('teal')">
                                <span class="rounded-circle" style="width: 12px; height: 12px; background: #0d9488; display: inline-block;"></span>
                                <span style="font-size: 12.5px;">Teal Sage</span>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 border-0 bg-transparent w-100 text-start" onclick="changeTheme('orange')">
                                <span class="rounded-circle" style="width: 12px; height: 12px; background: #f97316; display: inline-block;"></span>
                                <span style="font-size: 12.5px;">Sunset Orange</span>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 border-0 bg-transparent w-100 text-start" onclick="changeTheme('purple')">
                                <span class="rounded-circle" style="width: 12px; height: 12px; background: #8b5cf6; display: inline-block;"></span>
                                <span style="font-size: 12.5px;">Lilac Purple</span>
                            </button>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><div class="dropdown-header text-dark fw-bold px-2 py-1" style="font-size: 11px;">Tema Sidebar</div></li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 border-0 bg-transparent w-100 text-start" onclick="changeSidebarTheme('dark')">
                                <i class="bi bi-moon-stars-fill text-muted" style="font-size: 13px;"></i>
                                <span style="font-size: 12.5px;">Gelap (Default)</span>
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item d-flex align-items-center gap-2 rounded px-2 py-1.5 border-0 bg-transparent w-100 text-start" onclick="changeSidebarTheme('light')">
                                <i class="bi bi-sun-fill text-warning" style="font-size: 13px;"></i>
                                <span style="font-size: 12.5px;">Putih (Minimalis)</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="dropdown">
                    @php
                        $userName = Auth::user()->name ?? 'Dev Admin';
                        $userEmail = Auth::user()->email ?? 'admin@bonops.com';
                        $userInitials = collect(explode(' ', $userName))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                    @endphp
                    <button class="profile-dropdown-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-avatar">{{ $userInitials }}</div>
                        <span class="d-none d-md-inline-block">{{ $userName }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom mt-2 border-0">
                        <!-- User Card Header -->
                        <li>
                            <div class="dropdown-user-header d-flex align-items-center gap-3 mb-3 rounded-3">
                                <div class="d-flex align-items-center justify-content-center text-white fw-bold" style="width: 38px; height: 38px; background-color: var(--primary-accent); border-radius: 50%; font-size: 14px; box-shadow: 0 2px 6px color-mix(in srgb, var(--primary-accent) 25%, transparent); flex-shrink: 0;">{{ $userInitials }}</div>
                                <div class="d-flex flex-column overflow-hidden">
                                    <span class="fw-bold text-dark lh-sm text-truncate" style="font-size: 13.5px;">{{ $userName }}</span>
                                    <span class="text-muted text-truncate" style="font-size: 11px;">{{ $userEmail }}</span>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider" style="border-color: var(--border-color); opacity: 0.5; margin: 12px 0;"></li>
                        <!-- Dropdown Items -->
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="dropdown-icon-wrapper"><i class="bi bi-person-circle"></i></div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">Profil Saya</span>
                                        <span style="font-size: 10.5px; color: var(--text-muted); margin-top: 1px;">Lihat & edit info akun</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="{{ route('system.settings.branch_config') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="dropdown-icon-wrapper"><i class="bi bi-sliders"></i></div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">Pengaturan Sistem</span>
                                        <span style="font-size: 10.5px; color: var(--text-muted); margin-top: 1px;">Konfigurasi cabang & akun</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="{{ route('system.settings.users.index') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="dropdown-icon-wrapper"><i class="bi bi-shield-lock"></i></div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">Manajemen User</span>
                                        <span style="font-size: 10.5px; color: var(--text-muted); margin-top: 1px;">Kelola user & hak akses</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider" style="border-color: var(--border-color); opacity: 0.5; margin: 12px 0;"></li>
                        <!-- Logout Item -->
                        <li>
                            <a class="dropdown-item dropdown-item-custom text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-navbar').submit();">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="dropdown-icon-wrapper text-danger bg-danger-subtle"><i class="bi bi-box-arrow-right"></i></div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">Keluar (Logout)</span>
                                        <span style="font-size: 10.5px; margin-top: 1px;">Akhiri sesi ini</span>
                                    </div>
                                </div>
                            </a>
                            <form id="logout-form-navbar" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- ===== MODAL PROFIL SAYA ===== -->
        <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="margin-top: 3.5rem;">
                <div class="modal-content" style="border-radius: 20px; border: 1px solid var(--border-color); background: var(--bg-dark-secondary); box-shadow: 0 24px 64px rgba(0,0,0,0.12);">

                    <!-- Modal Header -->
                    <div class="modal-header" style="border-bottom: 1px solid var(--border-color); padding: 20px 28px;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 36px; height: 36px; background: color-mix(in srgb, var(--primary-accent) 10%, transparent); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-circle" style="color: var(--primary-accent); font-size: 17px;"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" id="profileModalLabel" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; font-size: 16px;">Profil Saya</h5>
                                <p class="mb-0" style="font-size: 12px; color: var(--text-muted);">Informasi akun & pengaturan personal</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body" style="padding: 28px;">
                        <div class="row g-4">

                            <!-- LEFT: Avatar & Info -->
                            <div class="col-12 col-md-4">
                                <div class="d-flex flex-column align-items-center text-center p-4 rounded-4" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid color-mix(in srgb, var(--primary-accent) 10%, transparent);">
                                    <!-- Avatar -->
                                    <div class="position-relative mb-3">
                                        <div id="profileAvatarLarge" style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-accent), var(--primary-hover)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; color: white; font-size: 28px; font-family: 'Outfit', sans-serif; box-shadow: 0 8px 24px color-mix(in srgb, var(--primary-accent) 30%, transparent);">{{ $userInitials }}</div>
                                        <div style="position: absolute; bottom: 2px; right: 2px; width: 20px; height: 20px; background: #10b981; border-radius: 50%; border: 2px solid var(--bg-dark-secondary); display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-check" style="font-size: 10px; color: white; font-weight: 900;"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="color: var(--text-heading); font-size: 15px;">{{ $userName }}</h6>
                                    <span class="text-muted mb-3" style="font-size: 12px;">{{ $userEmail }}</span>
                                    <!-- Role Badge -->
                                    <span class="badge px-3 py-1.5 mb-3 rounded-pill" style="background: color-mix(in srgb, var(--primary-accent) 10%, transparent); color: var(--primary-accent); font-size: 11px; font-weight: 600;"><i class="bi bi-shield-check me-1"></i>System Admin</span>
                                    <!-- Status -->
                                    <div class="d-flex align-items-center gap-2" style="font-size: 12px; color: #10b981;">
                                        <div style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 6px #10b981;"></div>
                                        <span class="fw-semibold">Aktif & Online</span>
                                    </div>
                                    <!-- Quick Stats -->
                                    <div class="w-100 mt-4 pt-3" style="border-top: 1px solid color-mix(in srgb, var(--primary-accent) 12%, transparent);">
                                        <div class="d-flex justify-content-around text-center">
                                            <div>
                                                <div class="fw-bold" style="color: var(--text-heading); font-size: 18px; font-family: 'Outfit', sans-serif;">124</div>
                                                <div style="font-size: 10.5px; color: var(--text-muted);">Tenant</div>
                                            </div>
                                            <div style="width: 1px; background: color-mix(in srgb, var(--primary-accent) 12%, transparent);"></div>
                                            <div>
                                                <div class="fw-bold" style="color: var(--text-heading); font-size: 18px; font-family: 'Outfit', sans-serif;">512</div>
                                                <div style="font-size: 10.5px; color: var(--text-muted);">Cabang</div>
                                            </div>
                                            <div style="width: 1px; background: color-mix(in srgb, var(--primary-accent) 12%, transparent);"></div>
                                            <div>
                                                <div class="fw-bold" style="color: var(--text-heading); font-size: 18px; font-family: 'Outfit', sans-serif;">8</div>
                                                <div style="font-size: 10.5px; color: var(--text-muted);">Hari</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT: Form Edit Profil -->
                            <div class="col-12 col-md-8">
                                <!-- Tabs -->
                                <ul class="nav nav-pills mb-4" style="gap: 4px; background: var(--bg-dark-tertiary); padding: 4px; border-radius: 10px;" id="profileTabs">
                                    <li class="nav-item flex-fill">
                                        <button class="nav-link active w-100" style="font-size: 12.5px; border-radius: 8px; padding: 7px 0; font-weight: 600;" data-bs-toggle="pill" data-bs-target="#tabInfo"><i class="bi bi-person me-1"></i>Info Pribadi</button>
                                    </li>
                                    <li class="nav-item flex-fill">
                                        <button class="nav-link w-100" style="font-size: 12.5px; border-radius: 8px; padding: 7px 0; font-weight: 600;" data-bs-toggle="pill" data-bs-target="#tabPassword"><i class="bi bi-lock me-1"></i>Keamanan</button>
                                    </li>
                                    <li class="nav-item flex-fill">
                                        <button class="nav-link w-100" style="font-size: 12.5px; border-radius: 8px; padding: 7px 0; font-weight: 600;" data-bs-toggle="pill" data-bs-target="#tabActivity"><i class="bi bi-activity me-1"></i>Aktivitas</button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Tab: Info Pribadi -->
                                    <div class="tab-pane fade show active" id="tabInfo">
                                        <div class="row g-3">
                                            <div class="col-12 col-sm-6">
                                                <x-form.label>Nama Lengkap</x-form.label>
                                                <x-form.input value="{{ $userName }}" placeholder="Nama lengkap Anda" />
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <x-form.label>Username</x-form.label>
                                                <x-form.input value="{{ strtolower(str_replace(' ', '.', $userName)) }}" placeholder="username" />
                                            </div>
                                            <div class="col-12">
                                                <x-form.label>Alamat Email</x-form.label>
                                                <div class="position-relative">
                                                    <x-form.input type="email" value="{{ $userEmail }}" placeholder="email@bonops.com" style="padding-left: 40px;" />
                                                    <i class="bi bi-envelope position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px;"></i>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <x-form.label>Telepon</x-form.label>
                                                <x-form.input type="tel" value="+62 812-XXXX-XXXX" placeholder="+62 8xx" />
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <x-form.label>Zona Waktu</x-form.label>
                                                <select class="form-select custom-form-control">
                                                    <option selected>WIB (UTC+7)</option>
                                                    <option>WITA (UTC+8)</option>
                                                    <option>WIT (UTC+9)</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <x-form.label>Tentang Saya</x-form.label>
                                                <x-form.textarea rows="2" placeholder="Deskripsi singkat peran Anda..." style="resize: none;">System Administrator BonOps Multi-Tenant ERP.</x-form.textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab: Keamanan -->
                                    <div class="tab-pane fade" id="tabPassword">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-2" style="background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.15);">
                                                    <i class="bi bi-shield-fill-check text-success" style="font-size: 16px;"></i>
                                                    <div>
                                                        <div class="fw-semibold" style="font-size: 12.5px; color: var(--text-heading);">Akun Anda Aman</div>
                                                        <div style="font-size: 11px; color: var(--text-muted);">Password terakhir diubah 30 hari lalu</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <x-form.label>Password Saat Ini</x-form.label>
                                                <div class="position-relative">
                                                    <x-form.input type="password" id="currentPwdModal" placeholder="••••••••" style="padding-right: 42px;" />
                                                    <button type="button" onclick="togglePwd('currentPwdModal', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px;"><i class="bi bi-eye-slash"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <x-form.label>Password Baru</x-form.label>
                                                <div class="position-relative">
                                                    <x-form.input type="password" id="newPwdModal" placeholder="Min. 8 karakter" style="padding-right: 42px;" />
                                                    <button type="button" onclick="togglePwd('newPwdModal', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px;"><i class="bi bi-eye-slash"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <x-form.label>Konfirmasi Password</x-form.label>
                                                <div class="position-relative">
                                                    <x-form.input type="password" id="confirmPwdModal" placeholder="Ulangi password baru" style="padding-right: 42px;" />
                                                    <button type="button" onclick="togglePwd('confirmPwdModal', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px;"><i class="bi bi-eye-slash"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 rounded-3" style="background: var(--bg-dark-tertiary); border: 1px solid var(--border-color);">
                                                    <div class="fw-semibold mb-2" style="font-size: 12px; color: var(--text-heading);"><i class="bi bi-info-circle me-1 text-primary"></i>Syarat Password</div>
                                                    <div class="d-flex flex-column gap-1">
                                                        <div style="font-size: 11.5px; color: var(--text-muted);"><i class="bi bi-check-circle text-success me-1"></i>Minimal 8 karakter</div>
                                                        <div style="font-size: 11.5px; color: var(--text-muted);"><i class="bi bi-check-circle text-success me-1"></i>Kombinasi huruf & angka</div>
                                                        <div style="font-size: 11.5px; color: var(--text-muted);"><i class="bi bi-circle me-1" style="opacity:0.4;"></i>Karakter spesial (disarankan)</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab: Aktivitas -->
                                    <div class="tab-pane fade" id="tabActivity">
                                        <div class="d-flex flex-column gap-2">
                                            @php
                                                $activities = [
                                                    ['icon'=>'bi-box-arrow-in-right','color'=>'#10b981','bg'=>'rgba(16,185,129,0.08)','title'=>'Login berhasil','desc'=>'Chrome • Windows 11 • Jakarta','time'=>'Baru saja'],
                                                    ['icon'=>'bi-pencil-square','color'=>'var(--primary-accent)','bg'=>'color-mix(in srgb, var(--primary-accent) 8%, transparent)','title'=>'Mengedit konfigurasi cabang','desc'=>'Jakarta Outlet — Branch Config','time'=>'2 jam lalu'],
                                                    ['icon'=>'bi-download','color'=>'#f59e0b','bg'=>'rgba(245,158,11,0.08)','title'=>'Unduh laporan penjualan','desc'=>'Laporan Penjualan Mei 2026.pdf','time'=>'5 jam lalu'],
                                                    ['icon'=>'bi-person-plus','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,0.08)','title'=>'Menambahkan user baru','desc'=>'Staff Kasir — Jakarta Outlet','time'=>'Kemarin'],
                                                    ['icon'=>'bi-box-arrow-right','color'=>'#ef4444','bg'=>'rgba(239,68,68,0.08)','title'=>'Logout sesi sebelumnya','desc'=>'Safari • MacOS — Bandung','time'=>'2 hari lalu'],
                                                ];
                                            @endphp
                                            @foreach($activities as $act)
                                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: var(--bg-dark-tertiary); border: 1px solid var(--border-color);">
                                                <div style="width: 34px; height: 34px; border-radius: 9px; background: {{ $act['bg'] }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <i class="bi {{ $act['icon'] }}" style="font-size: 14px; color: {{ $act['color'] }};"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="fw-semibold text-truncate" style="font-size: 12.5px; color: var(--text-heading);">{{ $act['title'] }}</div>
                                                    <div class="text-truncate" style="font-size: 11px; color: var(--text-muted);">{{ $act['desc'] }}</div>
                                                </div>
                                                <span style="font-size: 10.5px; color: var(--text-muted); white-space: nowrap; flex-shrink: 0;">{{ $act['time'] }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 16px 28px; background: color-mix(in srgb, var(--primary-accent) 2%, transparent); border-radius: 0 0 20px 20px;">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <span style="font-size: 11.5px; color: var(--text-muted);"><i class="bi bi-clock me-1"></i>Sesi aktif sejak: <b>{{ now()->format('d M Y, H:i') }} WIB</b></span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm custom-btn" data-bs-dismiss="modal" style="background: var(--bg-dark-tertiary); border: 1px solid var(--border-color); color: var(--text-light); border-radius: 8px; padding: 7px 18px; font-size: 13px;">Batal</button>
                                <button type="button" class="btn btn-sm btn-primary custom-btn" onclick="saveProfile()" style="border-radius: 8px; padding: 7px 20px; font-size: 13px; font-weight: 600;"><i class="bi bi-check2 me-1"></i>Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MODAL PROFIL -->

        <script>
            function togglePwd(inputId, btn) {
                const input = document.getElementById(inputId);
                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                }
            }
            function saveProfile() {
                const btn = event.currentTarget;
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:12px;height:12px;"></span>Menyimpan...';
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Tersimpan!';
                    btn.style.background = '#10b981';
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.style.background = 'var(--primary-accent)';
                    }, 2000);
                }, 1000);
            }
        </script>