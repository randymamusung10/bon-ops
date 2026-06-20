@extends('layouts.app')

@section('page_title', 'Refund Transaksi (Void)')
@section('page_description', 'Batalkan transaksi POS yang telah dibayar dan kembalikan stok produk otomatis.')

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
                    <h5 class="fw-bold text-heading mb-0" style="font-family: 'Outfit', sans-serif;">Detail Invoice</h5>
                    <div id="badge-container"></div>
                </div>

                <div class="p-3 rounded-4 mb-4" style="background: var(--bg-dark-tertiary); border: 1px solid var(--border-color);">
                    <div class="row g-2" style="font-size: 13px;">
                        <div class="col-6">
                            <span class="text-muted d-block">Nomor Invoice</span>
                            <strong class="text-heading" id="res-invoice">-</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Kasir Pembuat</span>
                            <strong class="text-heading" id="res-cashier">-</strong>
                        </div>
                        <div class="col-6 mt-2">
                            <span class="text-muted d-block">Waktu Transaksi</span>
                            <strong class="text-heading" id="res-time">-</strong>
                        </div>
                        <div class="col-6 mt-2">
                            <span class="text-muted d-block">Metode Pembayaran</span>
                            <strong class="text-heading text-uppercase" id="res-payment-method">-</strong>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-heading mb-2">Daftar Item Menu</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm align-middle" style="--bs-table-bg: transparent; font-size: 13px;">
                        <thead>
                            <tr class="text-muted">
                                <th>Menu/Produk</th>
                                <th class="text-center" style="width: 15%;">Qty</th>
                                <th class="text-end" style="width: 25%;">Harga</th>
                                <th class="text-end" style="width: 25%;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="res-items-body" class="text-heading">
                            <!-- Items go here -->
                        </tbody>
                    </table>
                </div>

                <hr style="opacity: 0.1;">
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold text-heading">Total Pembayaran</span>
                    <span class="fw-bold text-primary" id="res-grandtotal" style="font-size: 16px;">Rp 0</span>
                </div>

                <!-- Refund Button trigger -->
                <div class="d-flex gap-2 justify-content-end pt-3" style="border-top: 1px solid var(--border-color);" id="action-container">
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
                    $('#res-payment-method').text(order.payment_method);
                    $('#res-grandtotal').text('Rp ' + formatMoney(order.grand_total));
                    
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
                            <tr>
                                <td>${item.product?.name}</td>
                                <td class="text-center">${parseInt(item.qty)}</td>
                                <td class="text-end">Rp ${formatMoney(item.price)}</td>
                                <td class="text-end">Rp ${formatMoney(item.subtotal)}</td>
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