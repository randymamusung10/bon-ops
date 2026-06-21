<!-- SIDEBAR -->
    <div id="sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon-wrapper">
                <i class="bi bi-stack logo-icon"></i>
            </div>
            <span>Bon<span>Ops</span></span>
        </div>
        
        <!-- SEARCH BAR (Clean Light styling) -->
        <div class="px-3 mb-2 mt-3">
            <div class="position-relative d-flex align-items-center">
                <i class="bi bi-search position-absolute text-muted" style="left: 14px; font-size: 13px; pointer-events: none; opacity: 0.6;"></i>
                <input type="text" class="form-control sidebar-search-input" placeholder="Cari menu...">
            </div>
        </div>
        
        <div class="sidebar-menu-wrapper">
            <!-- DASHBOARD -->
            <a href="{{ route('dashboard') }}" class="nav-link-custom {{ Route::is('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <!-- CATEGORY: OPERASIONAL -->
            <div class="menu-category-title">Operasional POS</div>

            <!-- POS -->
            <div>
                <a class="nav-link-custom submenu-btn {{ Request::is('operational/pos*') ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#posMenu" role="button" aria-expanded="{{ Request::is('operational/pos*') ? 'true' : 'false' }}" aria-controls="posMenu">
                    <i class="bi bi-cpu"></i>
                    <span>POS Terminal</span>
                    
                    <i class="bi bi-chevron-right chevron-icon"></i>
                </a>
                <div class="collapse submenu-list-wrapper {{ Request::is('operational/pos*') ? 'show' : '' }}" id="posMenu">
                    <div class="submenu-list">
                        <a href="{{ route('operational.pos.terminal') }}" class="submenu-link {{ Route::is('operational.pos.terminal') ? 'active' : '' }}"><i class="bi bi-pc-display"></i><span>POS Terminal</span></a>
                        <a href="{{ route('operational.pos.history') }}" class="submenu-link {{ Route::is('operational.pos.history') ? 'active' : '' }}"><i class="bi bi-receipt"></i><span>Riwayat Penjualan</span></a>
                        <a href="{{ route('operational.pos.shift') }}" class="submenu-link {{ Route::is('operational.pos.shift') ? 'active' : '' }}"><i class="bi bi-unlock"></i><span>Buka/Tutup Shift</span></a>
                        <a href="{{ route('operational.pos.refund') }}" class="submenu-link {{ Route::is('operational.pos.refund') ? 'active' : '' }}"><i class="bi bi-arrow-counterclockwise"></i><span>Refund</span></a>
                    </div>
                </div>
            </div>

            <!-- Restaurant -->
            <div>
                <a class="nav-link-custom submenu-btn {{ Request::is('operational/restaurant*') ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#restaurantMenu" role="button" aria-expanded="{{ Request::is('operational/restaurant*') ? 'true' : 'false' }}" aria-controls="restaurantMenu">
                    <i class="bi bi-cup-hot"></i>
                    <span>Restoran</span>
                    
                    <i class="bi bi-chevron-right chevron-icon"></i>
                </a>
                <div class="collapse submenu-list-wrapper {{ Request::is('operational/restaurant*') ? 'show' : '' }}" id="restaurantMenu">
                    <div class="submenu-list">
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('operational.restaurant.tables') ? 'active' : '' }}"><i class="bi bi-layout-text-window-reverse"></i><span>Manajemen Meja</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('operational.restaurant.reservations') ? 'active' : '' }}"><i class="bi bi-calendar-event"></i><span>Reservasi</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="{{ route('operational.restaurant.kitchen') }}" class="submenu-link {{ Route::is('operational.restaurant.kitchen') ? 'active' : '' }}"><i class="bi bi-fire"></i><span>Kitchen Display</span></a>
                        <a href="{{ route('operational.restaurant.barista') }}" class="submenu-link {{ Route::is('operational.restaurant.barista') ? 'active' : '' }}"><i class="bi bi-cup-hot"></i><span>Barista Display</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('operational.restaurant.queue') ? 'active' : '' }}"><i class="bi bi-list-ol"></i><span>Antrean Pesanan</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                    </div>
                </div>
            </div>

            <!-- CATEGORY: LOGISTIK & MASTER -->
            <div class="menu-category-title">Logistik & Master</div>

            <!-- Master Data -->
            <div>
                <a class="nav-link-custom submenu-btn {{ Request::is('logistic/master*') ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#masterMenu" role="button" aria-expanded="{{ Request::is('logistic/master*') ? 'true' : 'false' }}" aria-controls="masterMenu">
                    <i class="bi bi-database-fill-gear"></i>
                    <span>Master Data</span>
                    <i class="bi bi-chevron-right chevron-icon"></i>
                </a>
                <div class="collapse submenu-list-wrapper {{ Request::is('logistic/master*') ? 'show' : '' }}" id="masterMenu">
                    <div class="submenu-list">
                        <a href="{{ route('logistic.master.company') }}" class="submenu-link {{ Route::is('logistic.master.company') ? 'active' : '' }}"><i class="bi bi-building"></i><span>Perusahaan (Company)</span></a>
                        <a href="{{ route('logistic.master.branch') }}" class="submenu-link {{ Route::is('logistic.master.branch') ? 'active' : '' }}"><i class="bi bi-geo-alt"></i><span>Cabang (Branch)</span></a>
                        <a href="{{ route('logistic.master.warehouse') }}" class="submenu-link {{ Route::is('logistic.master.warehouse') ? 'active' : '' }}"><i class="bi bi-house-gear"></i><span>Gudang (Warehouse)</span></a>
                        <a href="{{ route('logistic.master.customer') }}" class="submenu-link {{ Route::is('logistic.master.customer') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Pelanggan (Customer)</span></a>
                        <a href="{{ route('logistic.master.supplier') }}" class="submenu-link {{ Route::is('logistic.master.supplier') ? 'active' : '' }}"><i class="bi bi-truck"></i><span>Supplier</span></a>
                        <a href="{{ route('logistic.master.category') }}" class="submenu-link {{ Route::is('logistic.master.category') ? 'active' : '' }}"><i class="bi bi-tags"></i><span>Kategori Produk</span></a>
                        <a href="{{ route('logistic.master.product') }}" class="submenu-link {{ Route::is('logistic.master.product') ? 'active' : '' }}"><i class="bi bi-box"></i><span>Produk</span></a>
                        <a href="{{ route('logistic.master.unit') }}" class="submenu-link {{ Route::is('logistic.master.unit') ? 'active' : '' }}"><i class="bi bi-rulers"></i><span>Satuan (Unit)</span></a>
                        <a href="{{ route('logistic.master.recipe') }}" class="submenu-link {{ Route::is('logistic.master.recipe') ? 'active' : '' }}"><i class="bi bi-journal-text"></i><span>Resep (Recipe)</span></a>
                        <a href="{{ route('logistic.master.station') }}" class="submenu-link {{ Route::is('logistic.master.station') ? 'active' : '' }}"><i class="bi bi-cpu"></i><span>Stasiun Produksi</span></a>
                    </div>
                </div>
            </div>

            <!-- Inventory -->
            <div>
                <a class="nav-link-custom submenu-btn {{ Request::is('logistic/inventory*') ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#inventoryMenu" role="button" aria-expanded="{{ Request::is('logistic/inventory*') ? 'true' : 'false' }}" aria-controls="inventoryMenu">
                    <i class="bi bi-box-seam"></i>
                    <span>Inventaris</span>
                    
                    <i class="bi bi-chevron-right chevron-icon"></i>
                </a>
                <div class="collapse submenu-list-wrapper {{ Request::is('logistic/inventory*') ? 'show' : '' }}" id="inventoryMenu">
                    <div class="submenu-list">
                        <a href="{{ route('logistic.inventory.balance') }}" class="submenu-link {{ Route::is('logistic.inventory.balance') ? 'active' : '' }}"><i class="bi bi-boxes"></i><span>Saldo Stok</span></a>
                        <a href="{{ route('logistic.inventory.card') }}" class="submenu-link {{ Route::is('logistic.inventory.card') ? 'active' : '' }}"><i class="bi bi-card-list"></i><span>Kartu Stok</span></a>
                        <a href="{{ route('logistic.inventory.adjustment') }}" class="submenu-link {{ Route::is('logistic.inventory.adjustment') ? 'active' : '' }}"><i class="bi bi-sliders2"></i><span>Penyesuaian Stok</span></a>
                        <a href="{{ route('logistic.inventory.transfer') }}" class="submenu-link {{ Route::is('logistic.inventory.transfer') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i><span>Transfer Stok</span></a>
                        <a href="{{ route('logistic.inventory.opname') }}" class="submenu-link {{ Route::is('logistic.inventory.opname') ? 'active' : '' }}"><i class="bi bi-clipboard-check"></i><span>Stock Opname</span></a>
                        <a href="{{ route('logistic.inventory.waste') }}" class="submenu-link {{ Route::is('logistic.inventory.waste') ? 'active' : '' }}"><i class="bi bi-trash3"></i><span>Waste (Pembuangan)</span></a>
                    </div>
                </div>
            </div>

            <!-- Purchasing -->
            <div>
                <a class="nav-link-custom submenu-btn {{ Request::is('logistic/purchasing*') ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#purchasingMenu" role="button" aria-expanded="{{ Request::is('logistic/purchasing*') ? 'true' : 'false' }}" aria-controls="purchasingMenu">
                    <i class="bi bi-cart3"></i>
                    <span>Pembelian (Purchasing)</span>
                    
                    <i class="bi bi-chevron-right chevron-icon"></i>
                </a>
                <div class="collapse submenu-list-wrapper {{ Request::is('logistic/purchasing*') ? 'show' : '' }}" id="purchasingMenu">
                    <div class="submenu-list">
                        <a href="{{ route('logistic.purchasing.request') }}" class="submenu-link {{ Route::is('logistic.purchasing.request*') ? 'active' : '' }}"><i class="bi bi-file-earmark-plus"></i><span>Permintaan (PR)</span></a>
                        <a href="{{ route('logistic.purchasing.order') }}" class="submenu-link {{ Route::is('logistic.purchasing.order') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i><span>Pesanan (PO)</span></a>
                        <a href="{{ route('logistic.purchasing.receipt') }}" class="submenu-link {{ Route::is('logistic.purchasing.receipt') ? 'active' : '' }}"><i class="bi bi-box-arrow-in-down"></i><span>Penerimaan Barang</span></a>
                        <a href="{{ route('logistic.purchasing.invoice') }}" class="submenu-link {{ Route::is('logistic.purchasing.invoice') ? 'active' : '' }}"><i class="bi bi-calculator"></i><span>Faktur Supplier</span></a>
                        <a href="{{ route('logistic.purchasing.payment') }}" class="submenu-link {{ Route::is('logistic.purchasing.payment') ? 'active' : '' }}"><i class="bi bi-credit-card"></i><span>Pembayaran Supplier</span></a>
                    </div>
                </div>
            </div>

            <!-- CATEGORY: BISNIS & FINANSIAL -->
            <div class="menu-category-title">Bisnis & Finansial</div>

            <!-- CRM -->
            <div>
                <a class="nav-link-custom submenu-btn {{ Request::is('business/crm*') ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#crmMenu" role="button" aria-expanded="{{ Request::is('business/crm*') ? 'true' : 'false' }}" aria-controls="crmMenu">
                    <i class="bi bi-people"></i>
                    <span>CRM & Loyalitas</span>
                    <i class="bi bi-chevron-right chevron-icon"></i>
                </a>
                <div class="collapse submenu-list-wrapper {{ Request::is('business/crm*') ? 'show' : '' }}" id="crmMenu">
                    <div class="submenu-list">
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.crm.customer') ? 'active' : '' }}"><i class="bi bi-person-badge"></i><span>Pelanggan</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.crm.membership') ? 'active' : '' }}"><i class="bi bi-patch-check"></i><span>Membership</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.crm.loyalty') ? 'active' : '' }}"><i class="bi bi-award"></i><span>Poin Loyalitas</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.crm.voucher') ? 'active' : '' }}"><i class="bi bi-ticket-perforated"></i><span>Voucher</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                    </div>
                </div>
            </div>

            <!-- Finance -->
            <div>
                <a class="nav-link-custom submenu-btn {{ Request::is('business/finance*') ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#financeMenu" role="button" aria-expanded="{{ Request::is('business/finance*') ? 'true' : 'false' }}" aria-controls="financeMenu">
                    <i class="bi bi-cash-stack"></i>
                    <span>Keuangan (Finance)</span>
                    <i class="bi bi-chevron-right chevron-icon"></i>
                </a>
                <div class="collapse submenu-list-wrapper {{ Request::is('business/finance*') ? 'show' : '' }}" id="financeMenu">
                    <div class="submenu-list">
                        <a href="{{ route('business.finance.currency') }}" class="submenu-link {{ Route::is('business.finance.currency') ? 'active' : '' }}"><i class="bi bi-currency-exchange"></i><span>Mata Uang</span></a>
                        <a href="{{ route('business.finance.tax') }}" class="submenu-link {{ Route::is('business.finance.tax') ? 'active' : '' }}"><i class="bi bi-percent"></i><span>Pajak (Tax)</span></a>
                        <a href="{{ route('business.finance.coa') }}" class="submenu-link {{ Route::is('business.finance.coa') ? 'active' : '' }}"><i class="bi bi-list-stars"></i><span>Bagan Akun (COA)</span></a>
                        <a href="{{ route('business.finance.journal') }}" class="submenu-link {{ Route::is('business.finance.journal') ? 'active' : '' }}"><i class="bi bi-book"></i><span>Jurnal Umum</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.finance.payable') ? 'active' : '' }}"><i class="bi bi-box-arrow-right"></i><span>Hutang (AP)</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.finance.receivable') ? 'active' : '' }}"><i class="bi bi-box-arrow-in-left"></i><span>Piutang (AR)</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="{{ route('business.finance.ledger') }}" class="submenu-link {{ Route::is('business.finance.ledger') ? 'active' : '' }}"><i class="bi bi-journal-bookmark"></i><span>Buku Besar</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.finance.profit_loss') ? 'active' : '' }}"><i class="bi bi-graph-down"></i><span>Laba Rugi</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.finance.balance_sheet') ? 'active' : '' }}"><i class="bi bi-journal-check"></i><span>Neraca (Balance Sheet)</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                    </div>
                </div>
            </div>

            <!-- Reports -->
            <div>
                <a class="nav-link-custom submenu-btn {{ Request::is('business/reports*') ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#reportsMenu" role="button" aria-expanded="{{ Request::is('business/reports*') ? 'true' : 'false' }}" aria-controls="reportsMenu">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Laporan (Reports)</span>
                    <i class="bi bi-chevron-right chevron-icon"></i>
                </a>
                <div class="collapse submenu-list-wrapper {{ Request::is('business/reports*') ? 'show' : '' }}" id="reportsMenu">
                    <div class="submenu-list">
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.reports.sales') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i><span>Laporan Penjualan</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.reports.stock') ? 'active' : '' }}"><i class="bi bi-graph-up"></i><span>Laporan Stok</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.reports.food_cost') ? 'active' : '' }}"><i class="bi bi-pie-chart"></i><span>Laporan Food Cost</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.reports.purchase') ? 'active' : '' }}"><i class="bi bi-cart-check"></i><span>Laporan Pembelian</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('business.reports.executive') ? 'active' : '' }}"><i class="bi bi-shield-check"></i><span>Dashboard Eksekutif</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                    </div>
                </div>
            </div>

            <!-- GRADIENT DIVIDER -->
            <div class="sidebar-divider"></div>

            <!-- CATEGORY: SETTING -->
            <div class="menu-category-title">Sistem & Pengaturan</div>

            <!-- Settings -->
            <div>
                <a class="nav-link-custom submenu-btn {{ Request::is('system/settings*') ? 'active' : 'collapsed' }}" data-bs-toggle="collapse" href="#settingsMenu" role="button" aria-expanded="{{ Request::is('system/settings*') ? 'true' : 'false' }}" aria-controls="settingsMenu">
                    <i class="bi bi-gear"></i>
                    <span>Pengaturan</span>
                    
                    <i class="bi bi-chevron-right chevron-icon"></i>
                </a>
                <div class="collapse submenu-list-wrapper {{ Request::is('system/settings*') ? 'show' : '' }}" id="settingsMenu">
                    <div class="submenu-list">
                        <a href="{{ route('system.settings.users.index') }}" class="submenu-link {{ Route::is('system.settings.users.*') ? 'active' : '' }}"><i class="bi bi-person-gear"></i><span>Manajemen User</span></a>
                        <a href="{{ route('system.settings.roles.index') }}" class="submenu-link {{ Route::is('system.settings.roles.*') ? 'active' : '' }}"><i class="bi bi-briefcase"></i><span>Role & Jabatan</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('system.settings.permissions') ? 'active' : '' }}"><i class="bi bi-key"></i><span>Hak Akses (Permissions)</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                        <a href="javascript:void(0)" onclick="showDevAlert()" class="submenu-link {{ Route::is('system.settings.branch_config') ? 'active' : '' }}"><i class="bi bi-sliders"></i><span>Konfigurasi Cabang</span><span class="sidebar-sub-badge bg-secondary bg-opacity-25 text-secondary ms-auto" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;"><i class="bi bi-tools me-1"></i>Dev</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    function showDevAlert() {
        if(typeof AppAlert !== 'undefined') {
            AppAlert.info('Tahap Pengembangan', 'Fitur ini masih dalam tahap pengembangan dan akan segera hadir.');
        } else {
            alert('Fitur ini masih dalam tahap pengembangan.');
        }
    }
</script>
