@extends('layouts.app')

@section('page_title', 'Refund Transaksi (Void)')
@section('page_description', 'Batalkan transaksi POS yang telah dibayar dan kembalikan stok produk otomatis.')

@push('styles')
<style>
/* Autocomplete Hover Effect */
.autocomplete-item {
    transition: all 0.2s ease;
    position: relative;
}
.autocomplete-item:hover, .autocomplete-item:focus {
    background-color: color-mix(in srgb, var(--primary-accent) 5%, transparent) !important;
    box-shadow: 0 4px 15px color-mix(in srgb, var(--primary-accent) 15%, transparent);
    z-index: 2;
    transform: translateY(-1px);
    border-radius: 8px;
    margin: 0 4px;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4">
        <!-- Left Column: Search & Information -->
        <div class="col-lg-5">
            <div class="card rounded-4 border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold text-heading mb-3" style="font-family: 'Outfit', sans-serif;">Cari Transaksi</h5>
                <form id="search-order-form">
                    @csrf
                    <div class="mb-3 position-relative">
                        <x-form.label required>Nomor Invoice / Order</x-form.label>
                        <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid var(--border-color);">
                            <input type="text" class="form-control border-0" id="order_number" name="order_number" placeholder="Ketik sebagian Nomor Invoice..." required style="background: var(--bg-dark-tertiary); color: var(--text-heading); font-size: 14px; padding: 12px 16px;" autocomplete="off" />
                            <button type="submit" class="btn btn-primary px-4 fw-semibold" id="btn-search-submit" style="border-radius: 0;">
                                <i class="bi bi-search me-1"></i> Cari
                            </button>
                        </div>
                        <div id="autocomplete-dropdown" class="position-absolute w-100 shadow-lg rounded-3 border d-none" style="z-index: 1050; top: 100%; left: 0; margin-top: 5px; max-height: 300px; overflow-y: auto; background: var(--bg-dark-secondary); border-color: var(--border-color) !important;">
                            <!-- items go here -->
                        </div>
                    </div>
                </form>
            </div>

            <!-- ERP Rules Card -->
            <div class="card rounded-4 border-0 shadow-sm p-4">
                <h6 class="fw-bold text-heading mb-3" style="font-family: 'Outfit', sans-serif;"><i class="bi bi-shield-lock-fill text-warning me-2"></i> Ketentuan Refund ERP</h6>
                <ul class="text-muted ps-3 mb-0" style="font-size: 12.5px; line-height: 1.6;">
                    <li class="mb-2">Transaksi yang dibatalkan harus berstatus pembayaran <strong class="text-success">Paid</strong>.</li>
                    <li class="mb-2">Hanya pesanan yang <strong class="text-info">Belum Diproses</strong> oleh Kitchen/Barista yang dapat di-refund. Pesanan yang sudah <strong class="text-warning">Diproses Kitchen</strong> atau <strong class="text-success">Completed</strong> tidak dapat dibatalkan.</li>
                    <li class="mb-2">Sistem akan secara otomatis **mengembalikan saldo stok** bahan baku yang terpotong ke gudang asal.</li>
                    <li class="mb-2">Alasan pembatalan wajib diisi dengan jelas untuk tujuan pelaporan audit keuangan.</li>
                    <li>Semua aktivitas refund/void dicatat dalam log sistem beserta kasir pemroses.</li>
                </ul>
            </div>
        </div>

        <!-- Right Column: Detail & Actions -->
        <div class="col-lg-7">
            <!-- Alert Placeholder -->
            <div class="card rounded-4 border-0 shadow-sm p-5 text-center text-muted h-100 d-flex flex-column justify-content-center align-items-center" id="empty-state">
                <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px; background: var(--bg-dark-tertiary);">
                    <i class="bi bi-receipt-cutoff text-primary" style="font-size: 32px;"></i>
                </div>
                <h5 class="fw-bold text-heading mb-2" style="font-family: 'Outfit', sans-serif;">Detail Transaksi Penjualan</h5>
                <p class="text-muted mb-0" style="font-size: 14px; max-width: 320px;">Silakan masukkan Nomor Invoice pada panel di sebelah kiri untuk menampilkan data transaksi.</p>
            </div>

            <!-- Detail Result Panel (Hidden by default) -->
            <div class="card rounded-4 border-0 shadow-sm p-4 d-none" id="detail-panel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-heading mb-0 d-flex align-items-center gap-2" style="font-family: 'Outfit', sans-serif;">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-receipt"></i>
                        </div>
                        Detail Invoice
                    </h5>
                    <div id="badge-container" class="d-flex gap-1 flex-wrap justify-content-end"></div>
                </div>

                <div class="p-3 rounded-4 mb-4" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                    <div class="row gy-3 gx-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-hash text-muted"></i>
                                <span class="text-muted" style="font-size: 12px; font-weight: 500;">Nomor Invoice</span>
                            </div>
                            <strong class="text-heading ms-4 d-block" id="res-invoice" style="font-size: 14px;">-</strong>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-person text-muted"></i>
                                <span class="text-muted" style="font-size: 12px; font-weight: 500;">Kasir Pembuat</span>
                            </div>
                            <strong class="text-heading ms-4 d-block" id="res-cashier" style="font-size: 14px;">-</strong>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-clock text-muted"></i>
                                <span class="text-muted" style="font-size: 12px; font-weight: 500;">Waktu Transaksi</span>
                            </div>
                            <strong class="text-heading ms-4 d-block" id="res-time" style="font-size: 14px;">-</strong>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-wallet2 text-muted"></i>
                                <span class="text-muted" style="font-size: 12px; font-weight: 500;">Metode Pembayaran</span>
                            </div>
                            <strong class="text-heading ms-4 d-block text-uppercase" id="res-payment-method" style="font-size: 14px;">-</strong>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-shop text-muted"></i>
                                <span class="text-muted" style="font-size: 12px; font-weight: 500;">Cabang</span>
                            </div>
                            <strong class="text-heading ms-4 d-block" id="res-branch" style="font-size: 14px;">-</strong>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-person-badge text-muted"></i>
                                <span class="text-muted" style="font-size: 12px; font-weight: 500;">Nama Pelanggan</span>
                            </div>
                            <strong class="text-heading ms-4 d-block" id="res-customer" style="font-size: 14px;">-</strong>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-display text-muted"></i>
                                <span class="text-muted" style="font-size: 12px; font-weight: 500;">Nomor Meja</span>
                            </div>
                            <strong class="text-heading ms-4 d-block" id="res-table" style="font-size: 14px;">-</strong>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-journal-text text-muted"></i>
                                <span class="text-muted" style="font-size: 12px; font-weight: 500;">Catatan Pesanan</span>
                            </div>
                            <strong class="text-heading ms-4 d-block" id="res-notes" style="font-size: 14px;">-</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-cart3 text-primary fs-5"></i>
                    <h6 class="fw-bold text-heading mb-0" style="font-family: 'Outfit', sans-serif;">Daftar Item Menu</h6>
                </div>

                <div class="table-responsive mb-4 rounded-4 overflow-hidden border" style="border-color: rgba(226, 232, 240, 0.2) !important;">
                    <table class="table table-hover table-borderless align-middle mb-0" style="--bs-table-bg: transparent; font-size: 13px; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                        <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                            <tr class="text-muted">
                                <th class="py-3 px-3 fw-semibold">Menu/Produk</th>
                                <th class="text-center py-3 fw-semibold" style="width: 15%;">Qty</th>
                                <th class="text-end py-3 fw-semibold" style="width: 25%;">Harga</th>
                                <th class="text-end py-3 px-3 fw-semibold" style="width: 25%;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="res-items-body" class="text-heading">
                            <!-- Items go here -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-column gap-1 mb-3 px-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted" style="font-size: 13px;">Subtotal</span>
                        <strong class="text-heading" id="res-subtotal" style="font-size: 13px;">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted" style="font-size: 13px;">Diskon</span>
                        <strong class="text-danger" id="res-discount" style="font-size: 13px;">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted" style="font-size: 13px;">Pajak (Tax)</span>
                        <strong class="text-heading" id="res-tax" style="font-size: 13px;">Rp 0</strong>
                    </div>
                </div>

                <div class="p-3 rounded-4 mb-4 d-flex justify-content-between align-items-center" style="background: color-mix(in srgb, var(--primary-accent) 6%, transparent); border: 1px solid rgba(226, 232, 240, 0.15);">
                    <span class="fw-bold text-heading" style="font-family: 'Outfit', sans-serif; font-size: 15px;">Grand Total</span>
                    <span class="fw-bold text-primary" id="res-grandtotal" style="font-size: 20px;">Rp 0</span>
                </div>

                <!-- Refund Button trigger -->
                <div id="action-container" class="mt-2">
                    <!-- Void button will be generated here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Proses Refund -->
<x-modal id="refundModal" title="Alasan Batalkan Transaksi (Refund)">
    <form id="refund-process-form">
        @csrf
        <div class="mb-3">
            <x-form.label required>Alasan Refund / Pembatalan</x-form.label>
            <x-form.textarea name="notes" id="refund-notes" rows="3" required placeholder="Contoh: Pelanggan membatalkan pesanan, salah ketik item menu, atau lainnya..."></x-form.textarea>
            <div class="invalid-feedback">Wajib memasukkan alasan pembatalan!</div>
            <small class="text-muted mt-1 d-block">Alasan ini akan dicatat dalam histori jurnal audit pergudangan & kas.</small>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="danger" size="sm" icon="bi-arrow-counterclockwise" id="btn-submit-refund">Proses Refund</x-button>
        </div>
    </form>
</x-modal>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    var activeOrderUuid = '';

    function formatMoney(amount) {
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(amount);
    }

    // Autocomplete Logic
    var autocompleteTimer;
    $('#order_number').on('input', function() {
        clearTimeout(autocompleteTimer);
        var q = $(this).val();
        
        if (q.length < 3) {
            $('#autocomplete-dropdown').addClass('d-none');
            return;
        }

        autocompleteTimer = setTimeout(function() {
            $.ajax({
                url: "{{ route('operational.pos.refund.autocomplete') }}",
                type: "GET",
                data: { q: q },
                success: function(res) {
                    if (res.length > 0) {
                        var html = '<div class="list-group list-group-flush">';
                        res.forEach(function(item) {
                            var statusBadge = '';
                            if (item.status === 'pending') {
                                statusBadge = '<span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill" style="font-size: 10px;">Belum Diproses</span>';
                            } else if (item.status === 'completed') {
                                statusBadge = '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill" style="font-size: 10px;">Completed</span>';
                            } else if (item.status === 'cancelled') {
                                statusBadge = '<span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill" style="font-size: 10px;">Cancelled</span>';
                            } else {
                                statusBadge = '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill" style="font-size: 10px;">Diproses Kitchen</span>';
                            }
                            
                            html += '<button type="button" class="list-group-item list-group-item-action autocomplete-item p-3 border-0 d-flex flex-column" data-id="' + item.id + '" style="background: transparent; color: var(--text-heading); border-bottom: 1px solid var(--border-color) !important;">';
                            html += '<div class="d-flex justify-content-between align-items-center w-100 mb-1"><strong style="font-size: 13px;">' + item.id + '</strong>' + statusBadge + '</div>';
                            html += '<div class="d-flex justify-content-between align-items-center w-100 text-muted" style="font-size: 12px;"><span>' + item.date + '</span><span class="fw-semibold text-primary">Rp ' + formatMoney(item.grand_total) + '</span></div>';
                            html += '</button>';
                        });
                        html += '</div>';
                        $('#autocomplete-dropdown').html(html).removeClass('d-none');
                    } else {
                        $('#autocomplete-dropdown').html('<div class="p-3 text-center text-muted" style="font-size: 13px;">Transaksi tidak ditemukan</div>').removeClass('d-none');
                    }
                }
            });
        }, 300); // 300ms debounce
    });

    // Handle click on autocomplete item
    $(document).on('click', '.autocomplete-item', function() {
        var id = $(this).data('id');
        $('#order_number').val(id);
        $('#autocomplete-dropdown').addClass('d-none');
        $('#search-order-form').submit(); // auto submit
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#order_number, #autocomplete-dropdown').length) {
            $('#autocomplete-dropdown').addClass('d-none');
        }
    });

    // Search Order Form Submit
    $('#search-order-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var searchBtn = $('#btn-search-submit');
        
        searchBtn.prop('disabled', true);
        
        $.ajax({
            url: "{{ route('operational.pos.refund.search') }}",
            type: "GET",
            data: form.serialize(),
            success: function(res) {
                if (res.success) {
                    var order = res.order;
                    activeOrderUuid = order.uuid;
                    
                    // Show result panel
                    $('#empty-state').addClass('d-none');
                    $('#detail-panel').removeClass('d-none');
                    
                    // Populate details
                    $('#res-invoice').text(order.order_number);
                    $('#res-cashier').text(order.creator?.name || 'Kasir');
                    $('#res-time').text(new Date(order.created_at).toLocaleString('id-ID'));
                    $('#res-payment-method').text(order.payment_method || '-');
                    $('#res-branch').text(order.branch?.name || '-');
                    $('#res-customer').text(order.customer_name || 'Pelanggan Umum');
                    $('#res-table').text(order.table_number || '-');
                    $('#res-notes').text(order.notes || '-');
                    
                    $('#res-subtotal').text('Rp ' + formatMoney(order.total_amount || 0));
                    $('#res-discount').text('- Rp ' + formatMoney(order.discount_amount || 0));
                    $('#res-tax').text('Rp ' + formatMoney(order.tax_amount || 0));
                    $('#res-grandtotal').text('Rp ' + formatMoney(order.grand_total || 0));
                    
                    // Payment Status Badge
                    var badgeHtml = '';
                    if (order.payment_status === 'paid') {
                        badgeHtml += '<span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill me-1" style="font-size: 11px; font-weight:600;"><i class="bi bi-check-circle-fill me-1"></i> Paid</span>';
                    } else if (order.payment_status === 'refunded') {
                        badgeHtml += '<span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill me-1" style="font-size: 11px; font-weight:600;"><i class="bi bi-arrow-counterclockwise me-1"></i> Refunded</span>';
                    } else {
                        badgeHtml += '<span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-pill me-1" style="font-size: 11px; font-weight:600;"><i class="bi bi-exclamation-circle-fill me-1"></i> Unpaid</span>';
                    }
                    
                    // Order Status Badge
                    if (order.status === 'completed') {
                        badgeHtml += '<span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-check-circle-fill me-1"></i> Completed</span>';
                    } else if (order.status === 'cancelled') {
                        badgeHtml += '<span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-x-circle-fill me-1"></i> Cancelled</span>';
                    } else if (order.status === 'processing') {
                        badgeHtml += '<span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-fire me-1"></i> Diproses Kitchen</span>';
                    } else if (order.status === 'pending') {
                        badgeHtml += '<span class="badge bg-info-subtle text-info px-2.5 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-hourglass-split me-1"></i> Belum Diproses</span>';
                    }
                    $('#badge-container').html(badgeHtml);
                    
                    // Populate Items Table
                    var itemsHtml = '';
                    $.each(order.items, function(index, item) {
                        itemsHtml += `
                            <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                                <td class="px-3 py-2 fw-medium">${item.product?.name}</td>
                                <td class="text-center py-2"><span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">${parseInt(item.qty)}x</span></td>
                                <td class="text-end py-2 text-muted">Rp ${formatMoney(item.price)}</td>
                                <td class="text-end px-3 py-2 fw-semibold text-heading">Rp ${formatMoney(item.subtotal)}</td>
                            </tr>
                        `;
                    });
                    $('#res-items-body').html(itemsHtml);
                    
                    // Actions — use is_eligible from server response
                    var actionHtml = '';
                    if (res.is_eligible) {
                        actionHtml = '<button type="button" class="btn custom-btn btn-size-md btn-variant-danger position-relative overflow-hidden w-100 shadow-sm rounded-3" id="btn-trigger-refund-modal">' +
                            '<span class="btn-content d-flex align-items-center justify-content-center gap-2" style="position: relative; z-index: 2;">' +
                            '<i class="bi bi-arrow-counterclockwise fs-5"></i>' +
                            '<span style="font-weight: 600; font-size: 14px;">Void / Refund Transaksi</span>' +
                            '</span></button>';
                    } else {
                        actionHtml = '<div class="alert alert-danger d-flex align-items-center mb-0 w-100 p-3 rounded-3 border-0 shadow-sm" role="alert" style="font-size: 13px;">' +
                            '<i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>' +
                            '<div>' + res.ineligible_reason + '</div></div>';
                    }
                    $('#action-container').html(actionHtml);
                }
            },
            error: function(xhr) {
                $('#empty-state').removeClass('d-none');
                $('#detail-panel').addClass('d-none');
                AppAlert.error('Tidak Ditemukan', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
            },
            complete: function() {
                searchBtn.prop('disabled', false);
            }
        });
    });

    // Trigger Refund Modal
    $(document).on('click', '#btn-trigger-refund-modal', function() {
        $('#refund-notes').val('');
        $('#refundModal').modal('show');
    });

    // Submit Refund Process
    $('#refund-process-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#btn-submit-refund');
        
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: `/operational/pos/refund/process/${activeOrderUuid}`,
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                if (res.success) {
                    $('#refundModal').modal('hide');
                    AppAlert.success('Refund Berhasil!', res.message);
                    
                    // Trigger search submit to refresh state
                    $('#search-order-form').submit();
                }
            },
            error: function(xhr) {
                AppAlert.error('Refund Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush