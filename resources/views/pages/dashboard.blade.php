@extends('layouts.app')

@section('content')
<style>
    @keyframes refreshPulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(0.98); opacity: 0.85; }
        100% { transform: scale(1); opacity: 1; }
    }
    .metrics-refresh-pulse {
        animation: refreshPulse 0.5s ease-in-out;
    }
    .activity-item {
        animation: slideInActivity 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes slideInActivity {
        from { opacity: 0; transform: translateY(-12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Premium Light Theme Glassmorphism Card Override */
    .card {
        background: rgba(255, 255, 255, 0.72) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04) !important;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.08) !important;
    }

    /* High contrast table styles */
    .table th {
        font-weight: 700 !important;
        color: var(--text-heading) !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
    }
    .table td {
        border-bottom: 1px solid rgba(226, 232, 240, 0.6) !important;
    }
</style>
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6">
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">Dashboard Utama</h1>
            <p class="mb-0" style="color: var(--text-light); font-size: 13.5px;">Selamat datang kembali, <b class="text-primary">{{ Auth::user()->name ?? 'Dev Admin' }}</b>. Berikut performa sistem BonOps Anda hari ini.</p>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-outline-primary btn-sm rounded-3 me-2 px-3 py-2 border-light-subtle" id="refreshDataBtn" style="background-color: rgba(255, 255, 255, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(226, 232, 240, 0.8); color: var(--primary-accent); font-size: 13px;">
                <i class="bi bi-arrow-repeat me-1" id="refreshIcon"></i>
                <span id="refreshText">Segarkan Data</span>
            </button>
            <button class="btn btn-primary btn-sm rounded-3 px-3 py-2" id="addTenantBtn" style="background-color: var(--primary-accent); border: none; font-size: 13px; font-weight: 500;">
                <i class="bi bi-plus-lg me-1" id="addTenantIcon"></i>
                <span id="addTenantText">Tambah Tenant</span>
            </button>
        </div>
    </div>

    <!-- METRICS CARDS WITH SPARKLINES -->
    <div class="row g-4 mb-4">
        <!-- Metric: Total Tenant -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 rounded-4" style="padding: 18px 20px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-semibold" style="color: var(--text-muted); font-size: 12.5px; letter-spacing: 0.2px;">Total Tenant</span>
                    <div class="metric-icon-box" style="background-color: rgba(59, 130, 246, 0.08);">
                        <i class="bi bi-building" style="font-size: 16px; color: var(--primary-accent);"></i>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1 count-up" data-target="124" style="color: var(--text-heading); font-size: 26px; font-family: 'Outfit', sans-serif; letter-spacing: -0.5px; line-height: 1;">0</h3>
                        <span class="text-success fw-semibold d-flex align-items-center gap-1" style="font-size: 11.5px;">
                            <i class="bi bi-arrow-up-right"></i>+12% bulan ini
                        </span>
                    </div>
                    <!-- Sparkline SVG (Royal Blue CSS variable mapped) -->
                    <div class="mb-1">
                        <svg width="60" height="28" viewBox="0 0 60 28" style="overflow: visible;">
                            <polyline fill="none" style="stroke: var(--primary-accent);" stroke-width="2" points="0,24 10,20 20,24 30,12 40,16 50,6 60,10" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric: Total Cabang -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 rounded-4" style="padding: 18px 20px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-semibold" style="color: var(--text-muted); font-size: 12.5px; letter-spacing: 0.2px;">Cabang Aktif</span>
                    <div class="metric-icon-box" style="background-color: rgba(13, 148, 136, 0.08);">
                        <i class="bi bi-geo-alt" style="font-size: 16px; color: var(--secondary-accent);"></i>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1 count-up" data-target="512" style="color: var(--text-heading); font-size: 26px; font-family: 'Outfit', sans-serif; letter-spacing: -0.5px; line-height: 1;">0</h3>
                        <span class="text-success fw-semibold d-flex align-items-center gap-1" style="font-size: 11.5px;">
                            <i class="bi bi-arrow-up-right"></i>+8% minggu ini
                        </span>
                    </div>
                    <!-- Sparkline SVG (Teal) -->
                    <div class="mb-1">
                        <svg width="60" height="28" viewBox="0 0 60 28" style="overflow: visible;">
                            <polyline fill="none" style="stroke: var(--secondary-accent);" stroke-width="2" points="0,22 10,16 20,20 30,10 40,9 50,4 60,6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric: Transaksi Hari Ini -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 rounded-4" style="padding: 18px 20px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-semibold" style="color: var(--text-muted); font-size: 12.5px; letter-spacing: 0.2px;">Transaksi POS</span>
                    <div class="metric-icon-box" style="background-color: rgba(16, 185, 129, 0.08);">
                        <i class="bi bi-cart-check" style="font-size: 16px; color: #10b981;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1 count-up" data-target="8432" data-format="comma" style="color: var(--text-heading); font-size: 26px; font-family: 'Outfit', sans-serif; letter-spacing: -0.5px; line-height: 1;">0</h3>
                        <span class="text-success fw-semibold d-flex align-items-center gap-1" style="font-size: 11.5px;">
                            <i class="bi bi-arrow-up-right"></i>+18.3% vs kemarin
                        </span>
                    </div>
                    <!-- Sparkline SVG (Green) -->
                    <div class="mb-1">
                        <svg width="60" height="28" viewBox="0 0 60 28" style="overflow: visible;">
                            <polyline fill="none" stroke="#10b981" stroke-width="2" points="0,26 10,20 20,24 30,14 40,18 50,8 60,2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric: GMV Harian -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 rounded-4" style="padding: 18px 20px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-semibold" style="color: var(--text-muted); font-size: 12.5px; letter-spacing: 0.2px;">Estimasi GMV</span>
                    <div class="metric-icon-box" style="background-color: rgba(245, 158, 11, 0.08);">
                        <i class="bi bi-wallet2" style="font-size: 16px; color: #f59e0b;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1 count-up-decimal" data-target="348.5" data-prefix="Rp " data-suffix=" M" style="color: var(--text-heading); font-size: 24px; font-family: 'Outfit', sans-serif; letter-spacing: -0.5px; line-height: 1;">Rp 0 M</h3>
                        <span class="text-success fw-semibold d-flex align-items-center gap-1" style="font-size: 11.5px;">
                            <i class="bi bi-arrow-up-right"></i>+4.2% bulan ini
                        </span>
                    </div>
                    <!-- Sparkline SVG (Orange) -->
                    <div class="mb-1">
                        <svg width="60" height="28" viewBox="0 0 60 28" style="overflow: visible;">
                            <polyline fill="none" stroke="#f59e0b" stroke-width="2" points="0,24 10,18 20,26 30,13 40,17 50,11 60,6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MIDDLE ROW: CHARTS (FLAT SOLID COLORS - NO GRADIENTS) -->
    <div class="row g-4 mb-4">
        <!-- Sales Performance Line Chart (65% width) -->
        <div class="col-12 col-lg-8">
            <div class="card rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--text-heading);">Kinerja Penjualan Bulanan (Omnichannel)</h6>
                        <p class="mb-0" style="color: var(--text-light); font-size: 12px;">Gabungan transaksi POS & QR Ordering (Nilai dalam Ribu USD)</p>
                    </div>
                    <span class="badge px-2.5 py-1.5" style="background-color: rgba(59, 130, 246, 0.08); color: var(--primary-accent); font-size: 11px; font-weight: 600;">Realisasi Target</span>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Warehouse Transaction Bar Chart (35% width) -->
        <div class="col-12 col-lg-4">
            <div class="card rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--text-heading);">Distribusi Transaksi Gudang</h6>
                        <p class="mb-0" style="color: var(--text-light); font-size: 12px;">Volume pasokan bahan baku antar cabang</p>
                    </div>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="branchChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- LOWER ROW: FEATURES DISCOVERY & AGENDA STACK -->
    <div class="row g-4">
        <!-- Feature Roadmap Tracker (Left Column) -->
        <div class="col-12 col-xl-8">
            <div class="card rounded-4 p-4">
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-heading); font-size: 15px; font-family: 'Outfit', sans-serif;">Daftar Fitur & Modul BonOps</h5>
                        <p class="mb-0" style="color: var(--text-light); font-size: 12.5px;">Status integrasi modul POS + ERP Multi Tenant BonOps.</p>
                    </div>
                </div>

                <!-- Custom Flat Filters Row -->
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4 p-3 rounded-3" style="background-color: rgba(255, 255, 255, 0.4); border: 1px solid rgba(255, 255, 255, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                    <div class="d-flex flex-wrap gap-1.5 align-items-center">
                        <button class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 12px; background-color: var(--primary-accent); color: #ffffff; border: none;">Semua</button>
                        <button class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 12px; background-color: rgba(255, 255, 255, 0.6); color: var(--text-light); border: 1px solid rgba(226, 232, 240, 0.8);">Phase 1</button>
                        <button class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 12px; background-color: rgba(255, 255, 255, 0.6); color: var(--text-light); border: 1px solid rgba(226, 232, 240, 0.8);">Phase 2</button>
                        <button class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 12px; background-color: rgba(255, 255, 255, 0.6); color: var(--text-light); border: 1px solid rgba(226, 232, 240, 0.8);">Phase 3</button>
                    </div>

                    <div class="d-flex align-items-center gap-2 mt-2 mt-md-0" style="flex-grow: 1; max-width: 400px; justify-content: flex-end;">
                        <div class="position-relative" style="width: 150px;">
                            <select class="form-select" style="font-size: 12px; height: 36px; padding: 0 24px 0 12px; border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 8px; background-color: rgba(255, 255, 255, 0.6); cursor: pointer; transition: all 0.2s;" onfocus="this.style.borderColor='var(--primary-accent)';" onblur="this.style.borderColor='rgba(226, 232, 240, 0.8)';">
                                <option>Semua Status</option>
                                <option>Selesai</option>
                                <option>Proses</option>
                                <option>Terkunci</option>
                            </select>
                        </div>
                        <div class="position-relative" style="width: 160px;">
                            <input type="text" placeholder="Cari modul..." style="font-size: 12px; height: 36px; width: 100%; border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 8px; padding: 0 12px 0 32px; background-color: rgba(255, 255, 255, 0.6); transition: all 0.2s;" onfocus="this.style.borderColor='var(--primary-accent)';" onblur="this.style.borderColor='rgba(226, 232, 240, 0.8)';">
                            <i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 9px; font-size: 12px; opacity: 0.5;"></i>
                        </div>
                        <button class="btn btn-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border: none; border-radius: 8px; background-color: var(--primary-accent);">
                            <i class="bi bi-search" style="font-size: 12px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Minimalist Table -->
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-border-color: var(--border-color);">
                        <thead>
                            <tr style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                                <th class="border-0 ps-2 py-3">Nama Modul</th>
                                <th class="border-0 py-3">Kategori</th>
                                <th class="border-0 py-3">Detail Sub-Fitur</th>
                                <th class="border-0 py-3">Status</th>
                                <th class="border-0 pe-2 text-end py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13px; color: var(--text-light);">
                            <!-- ROW 1: Multi Tenant Core -->
                            <tr>
                                <td class="ps-2 py-2.5 fw-semibold" style="color: var(--text-heading);"><i class="bi bi-box me-2 text-primary"></i>Multi Tenant & Core</td>
                                <td><span class="badge px-2 py-1" style="background-color: rgba(59, 130, 246, 0.08); color: var(--primary-accent); font-size: 11px;">Phase 1</span></td>
                                <td class="text-muted">Data Isolation (tenant_id), Company, Branch & Warehouse Management</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <div class="spinner-grow spinner-grow-sm text-warning" role="status" style="width: 8px; height: 8px;"></div>
                                        <span class="text-warning fw-medium" style="font-size: 12px;">Sedang Berjalan</span>
                                    </div>
                                </td>
                                <td class="pe-2 text-end"><a href="#" class="btn btn-sm btn-outline-primary rounded-2 px-2.5 py-1" style="font-size: 11.5px; border-color: rgba(59, 130, 246, 0.2);">Kelola</a></td>
                            </tr>
                            <!-- ROW 2: User Management -->
                            <tr>
                                <td class="ps-2 py-2.5 fw-semibold" style="color: var(--text-heading);"><i class="bi bi-shield-lock me-2 text-primary"></i>User Management</td>
                                <td><span class="badge px-2 py-1" style="background-color: rgba(59, 130, 246, 0.08); color: var(--primary-accent); font-size: 11px;">Phase 1</span></td>
                                <td class="text-muted">User, Role Matrix (System Owner, Cashier, dll) & Permission Policy</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 13px;"></i>
                                        <span class="text-success fw-medium" style="font-size: 12px;">Selesai (Seeded)</span>
                                    </div>
                                </td>
                                <td class="pe-2 text-end"><a href="#" class="btn btn-sm btn-outline-primary rounded-2 px-2.5 py-1" style="font-size: 11.5px; border-color: rgba(59, 130, 246, 0.2);">Kelola</a></td>
                            </tr>
                            <!-- ROW 3: Product & Recipe -->
                            <tr>
                                <td class="ps-2 py-2.5 fw-semibold" style="color: var(--text-heading);"><i class="bi bi-egg-fried me-2 text-primary"></i>Product & Recipe</td>
                                <td><span class="badge px-2 py-1" style="background-color: rgba(59, 130, 246, 0.08); color: var(--primary-accent); font-size: 11px;">Phase 1</span></td>
                                <td class="text-muted">Bahan Baku, Resep Menu, Pengurangan Stok Otomatis</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-dash-circle text-muted" style="font-size: 13px;"></i>
                                        <span class="text-muted" style="font-size: 12px;">Belum Dimulai</span>
                                    </div>
                                </td>
                                <td class="pe-2 text-end"><button class="btn btn-sm btn-light text-muted rounded-2 px-2.5 py-1 border-0" style="font-size: 11.5px; background-color: var(--bg-dark-tertiary);" disabled>Kelola</button></td>
                            </tr>
                            <!-- ROW 4: POS & Kitchen Display -->
                            <tr>
                                <td class="ps-2 py-2.5 fw-semibold" style="color: var(--text-heading);"><i class="bi bi-pc-display-horizontal me-2 text-primary"></i>POS & Kitchen</td>
                                <td><span class="badge px-2 py-1" style="background-color: rgba(59, 130, 246, 0.08); color: var(--primary-accent); font-size: 11px;">Phase 1</span></td>
                                <td class="text-muted">Sales Flow, Kitchen Display, Receipt Printer & Open/Close Shift</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-dash-circle text-muted" style="font-size: 13px;"></i>
                                        <span class="text-muted" style="font-size: 12px;">Belum Dimulai</span>
                                    </div>
                                </td>
                                <td class="pe-2 text-end"><button class="btn btn-sm btn-light text-muted rounded-2 px-2.5 py-1 border-0" style="font-size: 11.5px; background-color: var(--bg-dark-tertiary);" disabled>Kelola</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Combined Right Column: Setup wizard on top, Agenda below -->
        <div class="col-12 col-xl-4">
            <div class="d-flex flex-column gap-4">
                
                <!-- Setup Wizard Card -->
                <div class="card rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: var(--text-heading);">Setup Toko BonOps</h6>
                            <p class="text-muted mb-0" style="font-size: 12px;">Panduan konfigurasi awal tenant</p>
                        </div>
                        <span class="badge px-2 py-1" style="background-color: rgba(59, 130, 246, 0.08); color: var(--primary-accent); font-size: 11px; font-weight: 600;">3 / 5 Selesai</span>
                    </div>
                    
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2.5 p-2.5 rounded-3" style="background-color: rgba(255, 255, 255, 0.4); border: 1px solid rgba(255, 255, 255, 0.6); font-size: 12.5px;">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 15px;"></i>
                            <span class="text-muted text-decoration-line-through">Buat Perusahaan (PT Kopi Nusantara)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2.5 p-2.5 rounded-3" style="background-color: rgba(255, 255, 255, 0.4); border: 1px solid rgba(255, 255, 255, 0.6); font-size: 12.5px;">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 15px;"></i>
                            <span class="text-muted text-decoration-line-through">Daftarkan Cabang (Jakarta Outlet)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2.5 p-2.5 rounded-3" style="background-color: rgba(255, 255, 255, 0.4); border: 1px solid rgba(255, 255, 255, 0.6); font-size: 12.5px;">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 15px;"></i>
                            <span class="text-muted text-decoration-line-through">Daftarkan Gudang Utama</span>
                        </div>
                        <div class="d-flex align-items-center gap-2.5 p-2.5 rounded-3" style="font-size: 12.5px; background-color: color-mix(in srgb, var(--primary-accent) 6%, transparent); border: 1px solid color-mix(in srgb, var(--primary-accent) 15%, transparent);">
                            <div class="spinner-grow spinner-grow-sm text-primary" role="status" style="width: 12px; height: 12px;"></div>
                            <span class="fw-bold" style="color: var(--text-heading);">Input Bahan Baku & Resep Menu</span>
                        </div>
                        <div class="d-flex align-items-center gap-2.5 p-2.5 rounded-3" style="font-size: 12.5px; opacity: 0.5;">
                            <i class="bi bi-circle text-muted" style="font-size: 15px;"></i>
                            <span class="text-muted">Hubungkan POS Terminal Kasir</span>
                        </div>
                    </div>
                </div>

                <!-- Live Operations Log Card -->
                <div class="card rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: var(--text-heading);">Log Aktivitas ERP Live</h6>
                            <p class="text-muted mb-0" style="font-size: 12px;">Pemantauan POS & Logistik Multi-Tenant</p>
                        </div>
                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill" style="font-size: 10.5px; font-weight: 600; animation: pulseLive 2s infinite; display: inline-flex; align-items: center; gap: 4px;">
                            <span class="rounded-circle bg-success" style="width: 5px; height: 5px;"></span>
                            Live Feed
                        </span>
                    </div>

                    <!-- Live Feed Stack -->
                    <div class="d-flex flex-column gap-2" id="liveActivityFeed" style="max-height: 290px; overflow: hidden; position: relative;">
                        <!-- Activity Item 1 -->
                        <div class="activity-item d-flex align-items-center justify-content-between p-2.5 rounded-3 border-start border-4 border-primary" style="background-color: rgba(255, 255, 255, 0.4); border: 1px solid rgba(255, 255, 255, 0.6); transition: all 0.3s ease;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded p-1.5 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; color: var(--primary-accent); background-color: rgba(59, 130, 246, 0.08);">
                                    <i class="bi bi-cart-check-fill" style="font-size: 13px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 12px;">Kasir Jakarta 1: Transaksi #2910</div>
                                    <div class="text-muted mt-0.5" style="font-size: 10.5px;">Baru saja &bull; PT Kopi Nusantara</div>
                                </div>
                            </div>
                            <span class="fw-bold text-success" style="font-size: 11.5px;">+Rp 85.000</span>
                        </div>
                        <!-- Activity Item 2 -->
                        <div class="activity-item d-flex align-items-center justify-content-between p-2.5 rounded-3 border-start border-4" style="background-color: rgba(255, 255, 255, 0.4); border: 1px solid rgba(255, 255, 255, 0.6); border-left-color: #0d9488 !important; transition: all 0.3s ease;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded p-1.5 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; color: #0d9488; background-color: rgba(13, 148, 136, 0.08);">
                                    <i class="bi bi-box-seam-fill" style="font-size: 13px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 12px;">Gudang Pusat: Pengiriman Bahan</div>
                                    <div class="text-muted mt-0.5" style="font-size: 10.5px;">4 menit lalu &bull; Jakarta Outlet</div>
                                </div>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">PO #9102</span>
                        </div>
                        <!-- Activity Item 3 -->
                        <div class="activity-item d-flex align-items-center justify-content-between p-2.5 rounded-3 border-start border-4" style="background-color: rgba(255, 255, 255, 0.4); border: 1px solid rgba(255, 255, 255, 0.6); border-left-color: #f59e0b !important; transition: all 0.3s ease;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded p-1.5 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; color: #f59e0b; background-color: rgba(245, 158, 11, 0.08);">
                                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 13px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 12px;">Stok Kopi Arabika Menipis</div>
                                    <div class="text-muted mt-0.5" style="font-size: 10.5px;">10 menit lalu &bull; Gudang Utama</div>
                                </div>
                            </div>
                            <span class="text-danger fw-semibold" style="font-size: 11px;">&lt; 5kg</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refreshBtn = document.getElementById('refreshDataBtn');
        const refreshIcon = document.getElementById('refreshIcon');
        const refreshText = document.getElementById('refreshText');

        const addTenantBtn = document.getElementById('addTenantBtn');
        const addTenantIcon = document.getElementById('addTenantIcon');
        const addTenantText = document.getElementById('addTenantText');

        // Chart.js - Left Solid Line Chart (Omnichannel Sales) - No gradients
        const ctxSales = document.getElementById('salesChart').getContext('2d');
        const salesChartObj = new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [
                    {
                        label: 'POS Kasir',
                        data: [41500, 49800, 56400, 67300, 48200, 51300],
                        borderColor: '#3b82f6', // Solid Royal Blue
                        borderWidth: 2.5,
                        backgroundColor: '#3b82f6',
                        fill: false,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'QR Ordering',
                        data: [37200, 42400, 51600, 63600, 39500, 45200],
                        borderColor: '#0d9488', // Solid Sage Teal
                        borderWidth: 2.5,
                        backgroundColor: '#0d9488',
                        fill: false,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 6,
                            boxHeight: 6,
                            font: { family: 'Inter', size: 11, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: 'rgba(0, 0, 0, 0.02)' }, // Ultra-faint gridlines
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#64748b',
                            stepSize: 20000,
                            callback: function(value) { 
                                return value >= 1000 ? (value/1000) + 'k' : value; 
                            }
                        },
                        border: { display: false }
                    }
                }
            }
        });

        // Chart.js - Right Rounded Bar Chart (Branch Distribution) - No gradients
        const ctxBranch = document.getElementById('branchChart').getContext('2d');
        const branchChartObj = new Chart(ctxBranch, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Volume Order',
                    data: [80400, 70500, 80500, 79100, 68700, 75000],
                    backgroundColor: '#0d9488', // Solid Sage Teal
                    borderRadius: 4,
                    borderSkipped: false,
                    barPercentage: 0.45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: 'rgba(0, 0, 0, 0.02)' }, // Ultra-faint gridlines
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#64748b',
                            stepSize: 20000,
                            callback: function(value) { 
                                return value >= 1000 ? (value/1000) + 'k' : value; 
                            }
                        },
                        border: { display: false }
                    }
                }
            }
        });

        // Reusable Count-up animation functions
        function animateValue(obj, start, end, duration, commaFormat) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const val = Math.floor(progress * (end - start) + start);
                obj.innerText = commaFormat ? val.toLocaleString('en-US') : val;
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        function animateValueDecimal(obj, start, end, duration, prefix, suffix) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const val = (progress * (end - start) + start).toFixed(1);
                obj.innerText = `${prefix}${val}${suffix}`;
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        function initCountUp() {
            document.querySelectorAll('.count-up').forEach(el => {
                const target = parseInt(el.getAttribute('data-target'), 10);
                const format = el.getAttribute('data-format') === 'comma';
                animateValue(el, 0, target, 1200, format);
            });
            document.querySelectorAll('.count-up-decimal').forEach(el => {
                const target = parseFloat(el.getAttribute('data-target'));
                const prefix = el.getAttribute('data-prefix') || '';
                const suffix = el.getAttribute('data-suffix') || '';
                animateValueDecimal(el, 0, target, 1200, prefix, suffix);
            });
        }

        // Initialize Count-Up on load
        initCountUp();

        // Listen for accent theme changes to update charts
        window.addEventListener('accentThemeChanged', function(e) {
            const colors = e.detail.colors;
            const accentColor = colors['--primary-accent'];
            
            // Update salesChartObj colors
            salesChartObj.data.datasets[0].borderColor = accentColor;
            salesChartObj.data.datasets[0].backgroundColor = accentColor;
            salesChartObj.update();
        });
        
        // Apply initial accent theme color to chart if customized
        const currentTheme = localStorage.getItem('bonops-theme') || 'blue';
        if (currentTheme !== 'blue') {
            const rootStyle = getComputedStyle(document.documentElement);
            const primaryAccentColor = rootStyle.getPropertyValue('--primary-accent').trim();
            if (primaryAccentColor) {
                salesChartObj.data.datasets[0].borderColor = primaryAccentColor;
                salesChartObj.data.datasets[0].backgroundColor = primaryAccentColor;
                salesChartObj.update();
            }
        }

        // Live Activities Log simulation
        const mockActivities = [
            {
                title: "Kasir Bandung 2: Transaksi #2911",
                desc: "Baru saja &bull; PT Kopi Nusantara",
                badge: "+Rp 112.000",
                isSuccess: true,
                icon: "bi-cart-check-fill",
                color: "var(--primary-accent)",
                colorClass: "border-primary"
            },
            {
                title: "Sistem: Backup Database Bulanan",
                desc: "Baru saja &bull; Cloud Engine",
                badge: "Selesai",
                isSuccess: false,
                icon: "bi-database-fill-check",
                color: "#10b981",
                colorClass: "border-success"
            },
            {
                title: "Gudang Utama: Supplier PO #8022",
                desc: "1 menit lalu &bull; Jakarta Outlet",
                badge: "Diterima",
                isSuccess: false,
                icon: "bi-truck",
                color: "#0d9488",
                colorClass: "border-info"
            },
            {
                title: "Kasir Jakarta 2: Refund Transaksi #2890",
                desc: "2 menit lalu &bull; PT Kopi Nusantara",
                badge: "-Rp 45.000",
                isSuccess: false,
                isDanger: true,
                icon: "bi-arrow-counterclockwise",
                color: "#ef4444",
                colorClass: "border-danger"
            },
            {
                title: "Tenant Baru: PT Kopi Malabar",
                desc: "3 menit lalu &bull; Sistem Core",
                badge: "Aktif",
                isSuccess: true,
                icon: "bi-building-fill-add",
                color: "var(--primary-accent)",
                colorClass: "border-primary"
            }
        ];

        function insertNewActivity() {
            const container = document.getElementById('liveActivityFeed');
            if (!container) return;
            
            const activity = mockActivities[Math.floor(Math.random() * mockActivities.length)];
            
            const item = document.createElement('div');
            item.className = `activity-item d-flex align-items-center justify-content-between p-2.5 rounded-3 border-start border-4 ${activity.colorClass}`;
            item.style.backgroundColor = "rgba(255, 255, 255, 0.4)";
            item.style.border = "1px solid rgba(255, 255, 255, 0.6)";
            item.style.borderLeftWidth = "4px";
            if (activity.colorClass === 'border-success') {
                item.style.borderLeftColor = "#10b981";
            } else if (activity.colorClass === 'border-info') {
                item.style.borderLeftColor = "#0d9488";
            } else if (activity.colorClass === 'border-danger') {
                item.style.borderLeftColor = "#ef4444";
            }
            
            let badgeHtml = '';
            if (activity.isSuccess) {
                badgeHtml = `<span class="fw-bold text-success" style="font-size: 11.5px;">${activity.badge}</span>`;
            } else if (activity.isDanger) {
                badgeHtml = `<span class="fw-bold text-danger" style="font-size: 11.5px;">${activity.badge}</span>`;
            } else {
                badgeHtml = `<span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">${activity.badge}</span>`;
            }

            item.innerHTML = `
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded p-1.5 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; color: ${activity.color}; background-color: rgba(59, 130, 246, 0.08);">
                        <i class="bi ${activity.icon}" style="font-size: 13px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 12px;">${activity.title}</div>
                        <div class="text-muted mt-0.5" style="font-size: 10.5px;">${activity.desc}</div>
                    </div>
                </div>
                ${badgeHtml}
            `;

            container.insertBefore(item, container.firstChild);
            
            if (container.children.length > 3) {
                container.removeChild(container.lastChild);
            }
        }

        // Poll for live activities
        setInterval(insertNewActivity, 5000);

        // Handle Refresh Action
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (refreshBtn.disabled) return;

                refreshBtn.disabled = true;
                refreshIcon.classList.remove('bi-arrow-repeat');
                refreshIcon.classList.add('spinner-border', 'spinner-border-sm');
                refreshIcon.style.width = '12px';
                refreshIcon.style.height = '12px';
                refreshText.innerText = 'Menyegarkan...';

                // Add card refresh pulses
                document.querySelectorAll('.card').forEach(card => {
                    card.classList.add('metrics-refresh-pulse');
                });

                // Simulate Ajax Refresh
                setTimeout(() => {
                    refreshBtn.disabled = false;
                    refreshIcon.classList.remove('spinner-border', 'spinner-border-sm');
                    refreshIcon.classList.add('bi-arrow-repeat');
                    refreshIcon.style.width = '';
                    refreshIcon.style.height = '';
                    refreshText.innerText = 'Segarkan Data';
                    
                    // Remove card refresh pulses
                    document.querySelectorAll('.card').forEach(card => {
                        card.classList.remove('metrics-refresh-pulse');
                    });
                    
                    // Count-up metrics values with random deviations
                    const newTenant = Math.floor(120 + Math.random() * 15);
                    const newBranches = Math.floor(500 + Math.random() * 25);
                    const newTrans = Math.floor(8300 + Math.random() * 400);
                    const newGmv = (340 + Math.random() * 20).toFixed(1);

                    const tenantEl = document.querySelector('.count-up[data-target]');
                    const branchEl = document.querySelectorAll('.count-up')[1];
                    const transEl = document.querySelectorAll('.count-up')[2];
                    const gmvEl = document.querySelector('.count-up-decimal');

                    if (tenantEl) {
                        const cur = parseInt(tenantEl.innerText, 10) || 0;
                        tenantEl.setAttribute('data-target', newTenant);
                        animateValue(tenantEl, cur, newTenant, 1000, false);
                    }
                    if (branchEl) {
                        const cur = parseInt(branchEl.innerText, 10) || 0;
                        branchEl.setAttribute('data-target', newBranches);
                        animateValue(branchEl, cur, newBranches, 1000, false);
                    }
                    if (transEl) {
                        const cur = parseInt(transEl.innerText.replace(/,/g, ''), 10) || 0;
                        transEl.setAttribute('data-target', newTrans);
                        animateValue(transEl, cur, newTrans, 1000, true);
                    }
                    if (gmvEl) {
                        const cur = parseFloat(gmvEl.innerText.replace(/[^\d.]/g, '')) || 0;
                        gmvEl.setAttribute('data-target', newGmv);
                        animateValueDecimal(gmvEl, cur, parseFloat(newGmv), 1000, 'Rp ', ' M');
                    }

                    // Randomize chart data to look live
                    salesChartObj.data.datasets[0].data = salesChartObj.data.datasets[0].data.map(val => Math.floor(val * (0.9 + Math.random() * 0.2)));
                    salesChartObj.data.datasets[1].data = salesChartObj.data.datasets[1].data.map(val => Math.floor(val * (0.9 + Math.random() * 0.2)));
                    salesChartObj.update();

                    branchChartObj.data.datasets[0].data = branchChartObj.data.datasets[0].data.map(val => Math.floor(val * (0.9 + Math.random() * 0.2)));
                    branchChartObj.update();
                }, 1000);
            });
        }

        // Handle Add Tenant Action
        if (addTenantBtn) {
            addTenantBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (addTenantBtn.disabled) return;

                addTenantBtn.disabled = true;
                addTenantIcon.classList.remove('bi-plus-lg');
                addTenantIcon.classList.add('spinner-border', 'spinner-border-sm');
                addTenantIcon.style.width = '12px';
                addTenantIcon.style.height = '12px';
                addTenantText.innerText = 'Memproses...';

                // Simulate tenant creation
                setTimeout(() => {
                    addTenantBtn.disabled = false;
                    addTenantIcon.classList.remove('spinner-border', 'spinner-border-sm');
                    addTenantIcon.classList.add('bi-plus-lg');
                    addTenantIcon.style.width = '';
                    addTenantIcon.style.height = '';
                    addTenantText.innerText = 'Tambah Tenant';

                    const tenantName = prompt('Masukkan nama Tenant Baru Anda:');
                    if (tenantName) {
                        alert(`Tenant "${tenantName}" berhasil didaftarkan ke jaringan multi-tenant BonOps!`);
                    }
                }, 600);
            });
        }
    });
</script>
@endsection
