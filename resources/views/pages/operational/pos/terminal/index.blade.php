@extends('layouts.app')

@section('page_title', 'POS Terminal')
@section('page_description', 'Kasir Penjualan Cepat ERP BonOps.')

@push('styles')
<style>
#category-tabs::-webkit-scrollbar {
    display: none;
}
.category-tab-btn {
    font-weight: 600;
    border-radius: 8px;
    background: transparent;
    border: none;
    color: var(--text-light);
    transition: all 0.2s ease-in-out;
}
.category-tab-btn:hover {
    background: rgba(226, 232, 240, 0.08);
    color: var(--text-heading);
}
.category-tab-btn.active {
    background: var(--primary-accent) !important;
    color: #ffffff !important;
}
.payment-method-btn {
    border: 1px solid rgba(226, 232, 240, 0.1) !important;
    background: rgba(226, 232, 240, 0.02) !important;
    color: var(--text-light) !important;
    font-weight: 500;
    font-size: 12px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease-in-out;
}
.payment-method-btn:hover {
    background: rgba(226, 232, 240, 0.08) !important;
    color: var(--text-heading) !important;
    border-color: rgba(226, 232, 240, 0.2) !important;
}
.btn-check:checked + .payment-method-btn {
    background: var(--primary-accent) !important;
    border-color: var(--primary-accent) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4">
        <!-- Left Side: Product catalog -->
        <div class="col-lg-8">
            <!-- Search & Filters -->
            <div class="card rounded-4 border-0 shadow-sm p-3 mb-4" style="background: var(--bg-dark-secondary);">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 14px; z-index: 10;"></i>
                            <x-form.input type="text" id="search-product" class="ps-5" placeholder="Cari menu atau kode produk..." style="border-radius: 10px;" />
                        </div>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="d-flex gap-2 overflow-x-auto p-1 align-items-center" id="category-tabs" style="background: rgba(226, 232, 240, 0.05); border-radius: 10px; scrollbar-width: none; -ms-overflow-style: none;">
                            <button type="button" class="btn btn-sm category-tab-btn active px-3 py-2 text-nowrap flex-shrink-0" data-category-id="all">
                                Semua
                            </button>
                            @foreach($categories as $cat)
                                <button type="button" class="btn btn-sm category-tab-btn px-3 py-2 text-nowrap flex-shrink-0" data-category-id="{{ $cat->id }}">
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="row g-3" id="product-grid" style="max-height: 65vh; overflow-y: auto;">
                @foreach($products as $prod)
                    <div class="col-md-4 col-sm-6 product-card-item" data-category-id="{{ $prod->product_category_id }}" data-name="{{ strtolower($prod->name) }}" data-code="{{ strtolower($prod->code) }}">
                        <div class="card h-100 rounded-4 border-0 shadow-sm overflow-hidden position-relative product-btn" 
                             data-id="{{ $prod->id }}" 
                             data-name="{{ $prod->name }}" 
                             data-price="{{ (float)$prod->price }}"
                             style="background: var(--bg-dark-secondary); cursor: pointer; transition: transform 0.2s; border: 1px solid rgba(226, 232, 240, 0.1) !important;">
                            
                            <!-- Header visual or initials -->
                            <div class="d-flex align-items-center justify-content-center text-white fw-bold" style="height: 120px; background: linear-gradient(135deg, var(--primary-accent), var(--primary-hover)); font-size: 24px;">
                                {{ strtoupper(substr($prod->name, 0, 2)) }}
                            </div>
                            
                            <div class="p-3">
                                <span class="text-muted d-block" style="font-size: 11px;">{{ $prod->code }}</span>
                                <h6 class="fw-bold mb-1 text-heading mt-1" style="font-family: 'Outfit', sans-serif;">{{ $prod->name }}</h6>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="fw-bold text-primary" style="font-size: 14px;">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 10px; font-weight: 600;">{{ $prod->unit->name ?? 'pcs' }}</span>
                                        @if($prod->type === 'raw_material')
                                            <span class="text-muted mt-1" style="font-size: 10.5px;">Stok: <strong class="text-heading">{{ (float)$prod->inventoryBalances->sum('qty') }}</strong></span>
                                        @else
                                            <span class="text-muted mt-1" style="font-size: 10.5px;">Stok: <strong class="text-info" data-bs-toggle="tooltip" title="BTO (Built To Order): Menu ini dibuat langsung saat ada pesanan, sehingga tidak memiliki stok fisik instan.">Menu (BTO) <i class="bi bi-info-circle ms-1"></i></strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side: Cart & Invoice Summary -->
        <div class="col-lg-4">
            <div class="card rounded-4 border-0 shadow-sm h-100 d-flex flex-column" style="background: var(--bg-dark-secondary); min-height: 75vh; border: 1px solid rgba(226, 232, 240, 0.1) !important;">
                <!-- Header -->
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color: rgba(226, 232, 240, 0.1) !important;">
                    <h6 class="fw-bold mb-0 text-heading"><i class="bi bi-cart3 me-2 text-primary"></i>Keranjang Belanja</h6>
                    <x-button type="button" variant="ghost-danger" size="sm" class="rounded-3" id="clear-cart" data-bs-toggle="tooltip" title="Hapus semua menu yang ada di keranjang belanja">
                        <i class="bi bi-trash me-1"></i>Reset
                    </x-button>
                </div>

                <!-- Customer info inputs -->
                <div class="p-3 border-bottom bg-light-subtle" style="border-color: rgba(226, 232, 240, 0.1) !important;">
                    <div class="row g-2">
                        <div class="col-6">
                            <x-form.input type="text" id="customer_name" placeholder="Nama Pelanggan" style="font-size: 12px; border-radius: 8px;" />
                        </div>
                        <div class="col-6">
                            <x-form.input type="text" id="table_number" placeholder="No. Meja" style="font-size: 12px; border-radius: 8px;" />
                        </div>
                    </div>
                </div>

                <!-- Cart Items List -->
                <div class="flex-grow-1 p-3 overflow-y-auto" id="cart-items-wrapper" style="max-height: 35vh;">
                    <!-- Empty State -->
                    <div class="text-center py-5" id="cart-empty-state">
                        <div class="text-muted mb-2"><i class="bi bi-cart-x" style="font-size: 40px;"></i></div>
                        <p class="text-muted mb-0" style="font-size: 12px;">Keranjang kosong.</p>
                    </div>
                    <!-- Dynamic cart items will load here -->
                    <div class="d-flex flex-column gap-3" id="cart-items-container"></div>
                </div>

                <!-- Pricing & Payment Selector -->
                <div class="p-3 border-top" style="border-color: rgba(226, 232, 240, 0.1) !important; background-color: color-mix(in srgb, var(--primary-accent) 2%, transparent);">
                    <!-- Summary -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size: 12px;">Subtotal</span>
                        <span class="text-heading fw-medium" id="summary-subtotal" style="font-size: 13px;">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size: 12px;">Pajak (PPN 10%)</span>
                        <span class="text-heading fw-medium" id="summary-tax" style="font-size: 13px;">Rp 0</span>
                    </div>
                    <hr style="opacity: 0.1; margin: 10px 0;">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold text-heading" style="font-size: 14px;">Total Bayar</span>
                        <span class="fw-bold text-primary" id="summary-grandtotal" style="font-size: 16px;">Rp 0</span>
                    </div>

                    <!-- Payment Mode -->
                    <div class="mb-3">
                        <x-form.label class="mb-2" style="font-size: 11px;">Metode Pembayaran</x-form.label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="payment_method" id="pay-cash" value="cash" checked autocomplete="off">
                            <label class="btn payment-method-btn py-2" for="pay-cash" data-bs-toggle="tooltip" title="Pembayaran langsung dengan uang tunai fisik"><i class="bi bi-cash me-1"></i> Cash</label>
                            
                            <input type="radio" class="btn-check" name="payment_method" id="pay-debit" value="debit" autocomplete="off">
                            <label class="btn payment-method-btn py-2" for="pay-debit" data-bs-toggle="tooltip" title="Pembayaran potong saldo langsung via Kartu ATM/Debit"><i class="bi bi-credit-card me-1"></i> Debit</label>

                            <input type="radio" class="btn-check" name="payment_method" id="pay-credit" value="credit" autocomplete="off">
                            <label class="btn payment-method-btn py-2" for="pay-credit" data-bs-toggle="tooltip" title="Pembayaran tagihan via Kartu Kredit"><i class="bi bi-credit-card-2-front me-1"></i> Credit</label>

                            <input type="radio" class="btn-check" name="payment_method" id="pay-qris" value="qris" autocomplete="off">
                            <label class="btn payment-method-btn py-2" for="pay-qris" data-bs-toggle="tooltip" title="QRIS (Quick Response Code): Pembayaran digital dengan scan barcode lewat aplikasi E-Wallet/M-Banking (Gopay, Ovo, Dana, dll)"><i class="bi bi-qr-code me-1"></i> QRIS</label>
                        </div>
                    </div>

                    <!-- Bank Selection (Hidden by default, shown for Debit/Credit) -->
                    <div class="mb-3 d-none" id="bank-selection-container">
                        <x-form.label class="mb-2" style="font-size: 11px;">Pilih Bank / Mesin EDC <i class="bi bi-question-circle text-muted ms-1" style="cursor: help;" data-bs-toggle="tooltip" title="EDC (Electronic Data Capture): Mesin gesek kartu yang disediakan bank untuk menerima pembayaran Debit/Kredit"></i></x-form.label>
                        <x-form.select id="bank-selector" style="font-size: 12px; border-radius: 8px;">
                            <option value="bca">BCA</option>
                            <option value="mandiri">Mandiri</option>
                            <option value="bni">BNI</option>
                            <option value="bri">BRI</option>
                            <option value="other">Bank Lainnya</option>
                        </x-form.select>
                    </div>

                    <!-- Checkout Button -->
                    <x-button type="button" variant="primary" class="w-100 py-2.5 rounded-3 fw-bold" id="btn-checkout" size="md">
                        Bayar Sekarang <i class="bi bi-arrow-right-short ms-1" style="font-size: 18px;"></i>
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Pembayaran Cash -->
<x-modal id="paymentModal" title="Pembayaran Tunai">
    <div class="text-center mb-4 p-3 rounded-4" style="background: rgba(59, 130, 246, 0.05);">
        <span class="text-muted d-block mb-1" style="font-size: 12px;">Tagihan Total</span>
        <h3 class="fw-bold text-primary mb-0" id="payment-bill-amount">Rp 0</h3>
    </div>
    <div class="mb-3">
        <x-form.label required>Uang Diterima (Rp)</x-form.label>
        <x-form.input type="text" id="cash-received" class="format-number" required placeholder="Masukkan jumlah uang tunai" />
        <div class="invalid-feedback" id="cash-error">Jumlah uang diterima kurang!</div>
    </div>

    <!-- Shortcut Buttons -->
    <div class="d-flex flex-wrap gap-2 mb-3" id="cash-shortcuts">
        <x-button type="button" variant="light" size="sm" class="cash-shortcut-btn" data-value="10000">10k</x-button>
        <x-button type="button" variant="light" size="sm" class="cash-shortcut-btn" data-value="20000">20k</x-button>
        <x-button type="button" variant="light" size="sm" class="cash-shortcut-btn" data-value="50000">50k</x-button>
        <x-button type="button" variant="light" size="sm" class="cash-shortcut-btn" data-value="100000">100k</x-button>
        <x-button type="button" variant="light" size="sm" id="btn-exact-cash">Uang Pas</x-button>
    </div>

    <!-- Detailed Breakdown -->
    <div class="p-3 rounded-4 mb-3" style="background: rgba(226, 232, 240, 0.05); border: 1px solid rgba(226, 232, 240, 0.1);">
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted" style="font-size: 12px;">Total Tagihan:</span>
            <span class="text-heading fw-bold" id="detail-total-bill" style="font-size: 12px;">Rp 0</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted" style="font-size: 12px;">Uang Diterima:</span>
            <span class="text-heading fw-bold" id="detail-cash-received" style="font-size: 12px;">Rp 0</span>
        </div>
        <hr style="opacity: 0.1; margin: 8px 0;">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold" id="status-label" style="font-size: 13px; color: var(--text-muted);">Uang Kembalian:</span>
            <h4 class="fw-bold mb-0" id="payment-change-amount" style="font-size: 16px; color: var(--text-heading);">Rp 0</h4>
        </div>
    </div>

    <x-slot name="footer">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
        <x-button type="button" variant="primary" size="sm" id="btn-submit-payment" icon="bi-check2">Selesaikan Transaksi</x-button>
    </x-slot>
</x-modal>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 on Bank selector
    $('#bank-selector').select2({
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    var cart = [];
    var grandTotal = 0;
    var taxAmount = 0;
    var subtotal = 0;

    // Search Product
    $('#search-product').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.product-card-item').each(function() {
            var name = $(this).data('name');
            var code = $(this).data('code');
            if (name.indexOf(value) > -1 || code.indexOf(value) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Category Filters
    $(document).on('click', '.category-tab-btn', function() {
        $('.category-tab-btn').removeClass('active');
        $(this).addClass('active');

        var catId = $(this).data('category-id');
        if (catId === 'all') {
            $('.product-card-item').show();
        } else {
            $('.product-card-item').each(function() {
                if ($(this).data('category-id') == catId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Toggle Bank Selection container
    $('input[name="payment_method"]').on('change', function() {
        var val = $(this).val();
        if (val === 'debit' || val === 'credit') {
            $('#bank-selection-container').removeClass('d-none');
        } else {
            $('#bank-selection-container').addClass('d-none');
        }
    });

    // Add Product to Cart
    $('.product-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var price = parseFloat($(this).data('price'));

        var existing = cart.find(x => x.product_id === id);
        if (existing) {
            existing.qty++;
        } else {
            cart.push({
                product_id: id,
                name: name,
                price: price,
                qty: 1,
                notes: ''
            });
        }
        updateCart();
    });

    // Update Cart UI
    function updateCart() {
        var container = $('#cart-items-container');
        container.empty();

        if (cart.length === 0) {
            $('#cart-empty-state').show();
            subtotal = 0;
            taxAmount = 0;
            grandTotal = 0;
        } else {
            $('#cart-empty-state').hide();
            subtotal = 0;

            cart.forEach(function(item, index) {
                var itemSubtotal = item.price * item.qty;
                subtotal += itemSubtotal;

                var row = `
                    <div class="p-3 rounded-4" style="background: color-mix(in srgb, var(--primary-accent) 3%, transparent); border: 1px solid rgba(226, 232, 240, 0.15);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="fw-semibold text-heading d-block" style="font-size: 13px;">${item.name}</span>
                                <span class="text-primary fw-medium" style="font-size: 12px;">Rp ${formatMoney(item.price)}</span>
                            </div>
                            <span class="fw-bold text-heading" style="font-size: 13px;">Rp ${formatMoney(itemSubtotal)}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="text" class="form-control form-control-sm item-notes-input" data-index="${index}" value="${item.notes}" placeholder="Notes (es batu, dll)" style="max-width: 150px; font-size: 11px; height: 26px; border-radius: 6px;">
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-icon-modern btn-minus bg-danger-subtle text-danger border-0 d-flex align-items-center justify-content-center" data-index="${index}" style="width: 26px; height: 26px; font-size: 14px; border-radius: 6px;"><i class="bi bi-dash"></i></button>
                                <span class="fw-bold text-heading text-center" style="font-size: 14px; width: 20px;">${item.qty}</span>
                                <button class="btn btn-icon-modern btn-plus bg-primary-subtle text-primary border-0 d-flex align-items-center justify-content-center" data-index="${index}" style="width: 26px; height: 26px; font-size: 14px; border-radius: 6px;"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                container.append(row);
            });

            taxAmount = subtotal * 0.10;
            grandTotal = subtotal + taxAmount;
        }

        $('#summary-subtotal').html('Rp ' + formatMoney(subtotal));
        $('#summary-tax').html('Rp ' + formatMoney(taxAmount));
        $('#summary-grandtotal').html('Rp ' + formatMoney(grandTotal));
    }

    // Money Helper
    function formatMoney(amount) {
        return amount.toLocaleString('id-ID');
    }

    // Plus Qty
    $(document).on('click', '.btn-plus', function() {
        var idx = $(this).data('index');
        cart[idx].qty++;
        updateCart();
    });

    // Minus Qty
    $(document).on('click', '.btn-minus', function() {
        var idx = $(this).data('index');
        if (cart[idx].qty > 1) {
            cart[idx].qty--;
        } else {
            cart.splice(idx, 1);
        }
        updateCart();
    });

    // Notes change
    $(document).on('keyup', '.item-notes-input', function() {
        var idx = $(this).data('index');
        cart[idx].notes = $(this).val();
    });

    // Clear Cart
    $('#clear-cart').on('click', function() {
        cart = [];
        updateCart();
    });

    // Checkout Button
    $('#btn-checkout').on('click', function() {
        if (cart.length === 0) {
            AppAlert.error('Peringatan', 'Keranjang belanja masih kosong.');
            return;
        }

        var method = $('input[name="payment_method"]:checked').val();
        if (method === 'cash') {
            $('#payment-bill-amount').html('Rp ' + formatMoney(grandTotal));
            $('#cash-received').val('').removeClass('is-invalid');
            $('#detail-total-bill').html('Rp ' + formatMoney(grandTotal));
            $('#detail-cash-received').html('Rp 0');
            $('#status-label').text('Kekurangan:').css('color', 'var(--bs-danger)');
            $('#payment-change-amount').html('(Rp ' + formatMoney(grandTotal) + ')').addClass('text-danger').removeClass('text-success');
            $('#paymentModal').modal('show');
        } else {
            if (method === 'debit' || method === 'credit') {
                var bank = $('#bank-selector').val();
                method = method + '_' + bank;
            }
            submitOrder(method);
        }
    });

    // Cash Received input change
    $('#cash-received').on('keyup change input', function() {
        var rawVal = window.AppFormat.unmaskNumber($(this).val());
        var val = parseFloat(rawVal) || 0;
        
        $('#detail-total-bill').html('Rp ' + formatMoney(grandTotal));
        $('#detail-cash-received').html('Rp ' + formatMoney(val));

        var diff = val - grandTotal;
        if (diff >= 0) {
            $('#status-label').text('Uang Kembalian:').css('color', 'var(--bs-success)');
            $('#payment-change-amount').html('Rp ' + formatMoney(diff)).removeClass('text-danger').addClass('text-success');
            $(this).removeClass('is-invalid');
            $('#cash-error').hide();
        } else {
            var shortage = Math.abs(diff);
            $('#status-label').text('Kekurangan:').css('color', 'var(--bs-danger)');
            $('#payment-change-amount').html('(Rp ' + formatMoney(shortage) + ')').removeClass('text-success').addClass('text-danger');
        }
    });

    // Shortcuts for cash
    $(document).on('click', '.cash-shortcut-btn', function() {
        var val = parseFloat($(this).data('value'));
        $('#cash-received').val(window.AppFormat.formatRupiah(val)).trigger('change');
    });

    $('#btn-exact-cash').on('click', function() {
        $('#cash-received').val(window.AppFormat.formatRupiah(grandTotal)).trigger('change');
    });

    // Submit Cash Payment
    $('#btn-submit-payment').on('click', function() {
        var rawVal = window.AppFormat.unmaskNumber($('#cash-received').val());
        var cash = parseFloat(rawVal) || 0;
        if (cash < grandTotal) {
            $('#cash-received').addClass('is-invalid');
            $('#cash-error').show();
            return;
        }
        submitOrder('cash');
    });

    // General Submit Order Function
    function submitOrder(method) {
        var itemsData = cart.map(x => {
            return {
                product_id: x.product_id,
                qty: x.qty,
                price: x.price,
                notes: x.notes
            };
        });

        $('#btn-checkout, #btn-submit-payment').prop('disabled', true);

        $.ajax({
            url: "{{ route('operational.pos.terminal.store') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                customer_name: $('#customer_name').val(),
                table_number: $('#table_number').val(),
                payment_method: method,
                items: itemsData
            },
            success: function(res) {
                $('#paymentModal').modal('hide');
                AppAlert.success('Sukses!', 'Transaksi berhasil diproses.');
                cart = [];
                updateCart();
                $('#customer_name, #table_number').val('');
            },
            error: function(xhr) {
                AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan pemrosesan transaksi.');
            },
            complete: function() {
                $('#btn-checkout, #btn-submit-payment').prop('disabled', false);
            }
        });
    }
});
</script>
@endpush