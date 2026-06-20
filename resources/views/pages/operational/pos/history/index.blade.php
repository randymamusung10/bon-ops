@extends('layouts.app')

@section('page_title', 'Riwayat Penjualan')
@section('page_description', 'Kelola, cetak ulang struk, dan pantau seluruh transaksi penjualan kasir.')

@push('styles')
<style>
.receipt-container {
    width: 100%;
    max-width: 320px;
    margin: 0 auto;
    padding: 15px;
    background: #ffffff;
    color: #000000;
    font-family: 'Courier New', Courier, monospace;
    font-size: 12px;
    line-height: 1.4;
    border: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
}
.receipt-header {
    text-align: center;
    margin-bottom: 15px;
}
.receipt-title {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 2px;
}
.receipt-separator {
    border-top: 1px dashed #000000;
    margin: 8px 0;
}
.receipt-row {
    display: flex;
    justify-content-between;
}
.receipt-row-bold {
    display: flex;
    justify-content-between;
    font-weight: bold;
}
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none;
        box-shadow: none;
        padding: 0;
        margin: 0;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Data Table Card -->
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="history-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3" style="width: 15%;">Nomor Invoice</th>
                        <th class="py-3" style="width: 15%;">Tanggal & Waktu</th>
                        <th class="py-3" style="width: 12%;">Kasir</th>
                        <th class="py-3" style="width: 12%;">Pelanggan</th>
                        <th class="py-3" style="width: 8%;">Meja</th>
                        <th class="py-3" style="width: 10%;">Metode</th>
                        <th class="py-3" style="width: 10%;">Status Pembayaran</th>
                        <th class="py-3" style="width: 10%;">Status Order</th>
                        <th class="pe-4 text-end py-3" style="width: 13%;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px; color: var(--text-heading);">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Detail Transaksi -->
<x-modal id="detailModal" title="Detail Transaksi Penjualan">
    <div class="mb-4 p-3 rounded-4" style="background: rgba(226, 232, 240, 0.03); border: 1px solid rgba(226, 232, 240, 0.08);">
        <div class="row g-2" style="font-size: 13px;">
            <div class="col-6">
                <span class="text-muted d-block">Nomor Invoice</span>
                <strong class="text-heading" id="det-invoice">-</strong>
            </div>
            <div class="col-6">
                <span class="text-muted d-block">Kasir / Shift</span>
                <strong class="text-heading" id="det-cashier">-</strong>
            </div>
            <div class="col-6 mt-2">
                <span class="text-muted d-block">Waktu Transaksi</span>
                <strong class="text-heading" id="det-time">-</strong>
            </div>
            <div class="col-6 mt-2">
                <span class="text-muted d-block">Metode Pembayaran</span>
                <strong class="text-heading" id="det-payment-method">-</strong>
            </div>
            <div class="col-6 mt-2">
                <span class="text-muted d-block">Pelanggan / Meja</span>
                <strong class="text-heading" id="det-customer">-</strong>
            </div>
            <div class="col-6 mt-2">
                <span class="text-muted d-block">Catatan</span>
                <strong class="text-heading" id="det-notes">-</strong>
            </div>
        </div>
    </div>

    <h6 class="fw-bold text-heading mb-2">Item Pembelian</h6>
    <div class="table-responsive mb-4">
        <table class="table table-sm align-middle" style="--bs-table-bg: transparent; font-size: 13px;">
            <thead>
                <tr class="text-muted">
                    <th>Menu/Produk</th>
                    <th class="text-center" style="width: 15%;">Qty</th>
                    <th class="text-end" style="width: 25%;">Harga</th>
                    <th class="text-end" style="width: 25%;">Total</th>
                </tr>
            </thead>
            <tbody id="detail-items-body" class="text-heading">
                <!-- Items list dynamic -->
            </tbody>
        </table>
    </div>

    <hr style="opacity: 0.1;">
    <div class="d-flex justify-content-between mb-2" style="font-size: 13px;">
        <span class="text-muted">Subtotal</span>
        <span class="text-heading fw-medium" id="det-subtotal">Rp 0</span>
    </div>
    <div class="d-flex justify-content-between mb-2" style="font-size: 13px;">
        <span class="text-muted">Pajak (PPN 10%)</span>
        <span class="text-heading fw-medium" id="det-tax">Rp 0</span>
    </div>
    <div class="d-flex justify-content-between mb-0">
        <span class="fw-bold text-heading">Total Pembayaran</span>
        <span class="fw-bold text-primary" id="det-grandtotal" style="font-size: 15px;">Rp 0</span>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
    </div>
</x-modal>

<!-- Modal Cetak Ulang Struk -->
<x-modal id="printModal" title="Cetak Ulang Struk">
    <div class="d-flex justify-content-center p-3 rounded-4 mb-3" style="background: var(--bg-dark-secondary);">
        <div class="receipt-container" id="print-area">
            <div class="receipt-header">
                <div class="receipt-title" id="receipt-tenant-name">BONOPS RESTO</div>
                <div style="font-size: 10px;" id="receipt-branch-name">Cabang Bandung</div>
                <div style="font-size: 9px; color: #555;" id="receipt-branch-address">Jl. Dipatiukur No. 100, Bandung</div>
            </div>
            
            <div class="receipt-separator"></div>
            
            <div class="receipt-row">
                <span>Invoice:</span>
                <span id="receipt-invoice-no">POS-12345</span>
            </div>
            <div class="receipt-row">
                <span>Tanggal:</span>
                <span id="receipt-date">20/06/2026 12:00</span>
            </div>
            <div class="receipt-row">
                <span>Kasir:</span>
                <span id="receipt-cashier">Cashier Admin</span>
            </div>
            <div class="receipt-row">
                <span>Pelanggan:</span>
                <span id="receipt-customer">Umum</span>
            </div>
            
            <div class="receipt-separator"></div>
            
            <div id="receipt-items">
                <!-- Receipt items will go here -->
            </div>
            
            <div class="receipt-separator"></div>
            
            <div class="receipt-row">
                <span>Subtotal</span>
                <span id="receipt-subtotal">Rp 0</span>
            </div>
            <div class="receipt-row">
                <span>Pajak (PPN 10%)</span>
                <span id="receipt-tax">Rp 0</span>
            </div>
            <div class="receipt-row-bold">
                <span>Total</span>
                <span id="receipt-grandtotal">Rp 0</span>
            </div>
            
            <div class="receipt-separator"></div>
            
            <div class="receipt-row">
                <span>Metode Bayar</span>
                <span id="receipt-payment-method">CASH</span>
            </div>
            
            <div class="receipt-header mt-3">
                <div style="font-size: 10px; font-weight: bold;">Terima Kasih</div>
                <div style="font-size: 9px; color: #555;">Silakan berkunjung kembali!</div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
        <x-button type="button" variant="primary" size="sm" icon="bi-printer" id="btn-do-print">Cetak Struk</x-button>
    </div>
</x-modal>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    function formatMoney(amount) {
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(amount);
    }

    var table = $('#history-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('operational.pos.history.data') }}",
        order: [[2, 'desc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        columns: [
            { data: null, searchable: false, orderable: false, class: 'ps-4 text-muted', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
            { data: 'order_number', name: 'order_number', class: 'fw-semibold text-heading' },
            { data: 'date', name: 'created_at' },
            { data: 'creator.name', name: 'creator.name' },
            { data: 'customer_name', name: 'customer_name', defaultContent: 'Umum' },
            { data: 'table_number', name: 'table_number', defaultContent: '-' },
            { 
                data: 'payment_method', 
                name: 'payment_method',
                render: function(data) {
                    return '<span class="text-uppercase fw-semibold">' + data + '</span>';
                }
            },
            { data: 'payment_status_badge', name: 'payment_status' },
            { data: 'status_badge', name: 'status' },
            { 
                data: 'uuid', 
                orderable: false, 
                searchable: false, 
                class: 'pe-4 text-end text-nowrap',
                render: function(data, type, row) {
                    return '<div class="d-inline-flex gap-2">' +
                        '<button class="btn-icon-modern text-info btn-detail" data-uuid="' + data + '" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
                        '<i class="bi bi-eye"></i>' +
                        '</button>' +
                        '<button class="btn-icon-modern text-primary btn-reprint" data-uuid="' + data + '" title="Cetak Struk" style="background: color-mix(in srgb, var(--primary-accent) 12%, transparent);">' +
                        '<i class="bi bi-printer"></i>' +
                        '</button>' +
                        '</div>';
                }
            }
        ],
        language: {
            processing: `
                <div class="d-flex flex-column align-items-center gap-2">
                    <div class="modern-loader-spinner" style="width: 36px; height: 36px;">
                        <div class="spinner-outer" style="border-width: 2.5px;"></div>
                        <div class="spinner-inner" style="border-width: 1.5px;"></div>
                        <div class="spinner-dot" style="width: 5px; height: 5px;"></div>
                    </div>
                    <span class="fw-semibold text-muted" style="font-size: 12px; letter-spacing: 0.2px;">Memuat Data...</span>
                </div>
            `,
            search: '_INPUT_',
            searchPlaceholder: 'Cari invoice...',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 data',
            lengthMenu: 'Tampilkan _MENU_ entri',
            paginate: { previous: 'Prev', next: 'Next' }
        }
    });

    table.on('processing.dt', function(e, settings, processing) {
        if (processing) {
            $('.dataTables_wrapper').addClass('is-processing');
        } else {
            $('.dataTables_wrapper').removeClass('is-processing');
        }
    });

    $('.dataTables_length select').select2({
        theme: 'bootstrap-5',
        width: '75px',
        minimumResultsForSearch: -1
    });

    // View Details Event
    $(document).on('click', '.btn-detail', function() {
        var uuid = $(this).data('uuid');
        $.ajax({
            url: `/operational/pos/history/${uuid}`,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    var order = res.order;
                    $('#det-invoice').text(order.order_number);
                    $('#det-cashier').text(order.creator?.name || 'Kasir');
                    $('#det-time').text(new Date(order.created_at).toLocaleString('id-ID'));
                    $('#det-payment-method').text(order.payment_method.toUpperCase());
                    $('#det-customer').text((order.customer_name || 'Umum') + ' / Meja: ' + (order.table_number || '-'));
                    $('#det-notes').text(order.notes || '-');
                    
                    // Populate Items Table
                    var itemsHtml = '';
                    $.each(order.items, function(index, item) {
                        itemsHtml += `
                            <tr>
                                <td>${item.product?.name}</td>
                                <td class="text-center">${parseInt(item.qty)}</td>
                                <td class="text-end">Rp ${formatMoney(item.price)}</td>
                                <td class="text-end">Rp ${formatMoney(item.subtotal)}</td>
                            </tr>
                        `;
                    });
                    $('#detail-items-body').html(itemsHtml);
                    
                    $('#det-subtotal').text('Rp ' + formatMoney(order.total_amount));
                    $('#det-tax').text('Rp ' + formatMoney(order.tax_amount));
                    $('#det-grandtotal').text('Rp ' + formatMoney(order.grand_total));
                    
                    $('#detailModal').modal('show');
                }
            },
            error: function() {
                AppAlert.error('Gagal!', 'Tidak dapat mengambil detail transaksi.');
            }
        });
    });

    // Reprint Receipt Modal Trigger
    $(document).on('click', '.btn-reprint', function() {
        var uuid = $(this).data('uuid');
        $.ajax({
            url: `/operational/pos/history/${uuid}`,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    var order = res.order;
                    $('#receipt-tenant-name').text(order.branch?.company?.name || 'BONOPS RESTO');
                    $('#receipt-branch-name').text(order.branch?.name || 'Cabang');
                    $('#receipt-branch-address').text(order.branch?.address || '-');
                    
                    $('#receipt-invoice-no').text(order.order_number);
                    $('#receipt-date').text(new Date(order.created_at).toLocaleString('id-ID'));
                    $('#receipt-cashier').text(order.creator?.name || 'Kasir');
                    $('#receipt-customer').text(order.customer_name || 'Umum');
                    
                    // Render Items
                    var itemsHtml = '';
                    $.each(order.items, function(index, item) {
                        itemsHtml += `
                            <div class="receipt-row">
                                <span>${item.product?.name}</span>
                            </div>
                            <div class="receipt-row mb-1" style="font-size: 11px; padding-left: 10px;">
                                <span>${parseInt(item.qty)} x Rp ${formatMoney(item.price)}</span>
                                <span>Rp ${formatMoney(item.subtotal)}</span>
                            </div>
                        `;
                    });
                    $('#receipt-items').html(itemsHtml);
                    
                    $('#receipt-subtotal').text('Rp ' + formatMoney(order.total_amount));
                    $('#receipt-tax').text('Rp ' + formatMoney(order.tax_amount));
                    $('#receipt-grandtotal').text('Rp ' + formatMoney(order.grand_total));
                    $('#receipt-payment-method').text(order.payment_method.toUpperCase());
                    
                    $('#printModal').modal('show');
                }
            },
            error: function() {
                AppAlert.error('Gagal!', 'Tidak dapat memuat detail struk.');
            }
        });
    });

    // Native Browser Print trigger
    $('#btn-do-print').on('click', function() {
        window.print();
    });
});
</script>
@endpush