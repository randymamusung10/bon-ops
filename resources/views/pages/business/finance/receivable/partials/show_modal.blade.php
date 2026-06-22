<x-modal id="show-modal-receivable" title="Detail Piutang" description="Informasi lengkap mengenai tagihan dan riwayat pembayaran." size="xl">
    
    <!-- Header / Title Section -->
    <div class="d-flex align-items-center mb-4 p-3 rounded-4" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1);">
        <div class="d-flex justify-content-center align-items-center rounded-circle" style="width: 60px; height: 60px; background: color-mix(in srgb, var(--primary-accent) 15%, transparent); color: var(--primary-accent); font-size: 24px;">
            <i class="bi bi-receipt"></i>
        </div>
        <div class="ms-3">
            <h5 class="mb-1 fw-bold" style="color: var(--text-heading);">Order: {{ $order->order_number }}</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</span>
                @if($order->payment_status === 'paid')
                    <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                @elseif($order->payment_status === 'partial')
                    <span class="badge bg-warning-subtle text-warning px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Dibayar Sebagian</span>
                @else
                    <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-exclamation-circle me-1"></i>Belum Dibayar</span>
                @endif
                <span class="badge bg-info-subtle text-info px-2 py-1">
                    <i class="bi bi-person me-1"></i>{{ $order->customer_name ?: 'Pelanggan Umum' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Data Grid -->
    <div class="row g-4">
        <!-- Kolom Kiri: Detail Informasi -->
        <div class="col-md-7">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="p-3 rounded-3" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                        <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500; letter-spacing: 0.2px;">
                            <i class="bi bi-shop me-1"></i> Tipe Order
                        </div>
                        <div class="fw-semibold text-heading" style="font-size: 14px; text-transform: capitalize;">
                            {{ str_replace('_', ' ', $order->order_type) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 rounded-3" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                        <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500; letter-spacing: 0.2px;">
                            <i class="bi bi-ui-radios-grid me-1"></i> Nomor Meja
                        </div>
                        <div class="fw-semibold text-heading" style="font-size: 14px;">
                            {{ $order->table_number ?: '-' }}
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 rounded-3 h-100" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                        <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500; letter-spacing: 0.2px;">
                            <i class="bi bi-card-text me-1"></i> Catatan Transaksi
                        </div>
                        <div class="fw-medium text-heading" style="font-size: 14px; line-height: 1.5;">
                            {{ $order->notes ?: 'Tidak ada catatan' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Tagihan -->
        <div class="col-md-5">
            @php
                $isPaid = $order->payment_status === 'paid';
                $sisa = max(0, $order->grand_total - $order->paid_amount);
                $gradientBox = $isPaid 
                    ? 'linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.15)); border: 1px solid rgba(16, 185, 129, 0.2);'
                    : 'linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(239, 68, 68, 0.15)); border: 1px solid rgba(239, 68, 68, 0.2);';
                $textColor = $isPaid ? 'text-success' : 'text-danger';
            @endphp
            <div class="p-4 rounded-4 text-center h-100" style="background: {{ $gradientBox }}">
                <div class="mb-3">
                    <div class="{{ $textColor }} fw-bold mb-1" style="font-size: 13px; letter-spacing: 0.5px; text-transform: uppercase;">
                        Sisa Tagihan
                    </div>
                    <div class="fw-bolder" style="font-size: 28px; color: var(--text-heading);">
                        Rp {{ number_format($sisa, 0, ',', '.') }}
                    </div>
                </div>
                <hr style="border-color: rgba(0, 0, 0, 0.1);">
                <div class="row mt-3">
                    <div class="col-6 border-end" style="border-color: rgba(0, 0, 0, 0.1) !important;">
                        <div class="text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">
                            Total Tagihan
                        </div>
                        <div class="fw-bold text-heading" style="font-size: 16px;">
                            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">
                            Sudah Dibayar
                        </div>
                        <div class="fw-bold text-success" style="font-size: 16px;">
                            Rp {{ number_format($order->paid_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Item -->
    <div class="mt-4 pt-4" style="border-top: 1px dashed var(--border-color);">
        <h6 class="mb-3 fw-bold" style="color: var(--text-heading);"><i class="bi bi-box me-2 text-primary"></i>Detail Item Pesanan</h6>
        
        @if($order->items->count() > 0)
            <div class="table-responsive rounded-4" style="border: 1px solid rgba(226, 232, 240, 0.6);">
                <table class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                    <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                        <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                            <th class="py-2 ps-4" style="width: 40%; font-weight: 600;">Produk</th>
                            <th class="py-2 text-center" style="width: 15%; font-weight: 600;">Kuantitas</th>
                            <th class="py-2 text-end" style="width: 20%; font-weight: 600;">Harga Satuan</th>
                            <th class="py-2 text-end pe-4" style="width: 25%; font-weight: 600;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13px; color: var(--text-heading);">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $item->product->name ?? $item->product_name ?? 'Item' }}</td>
                                <td class="text-center">{{ (float) $item->qty }}</td>
                                <td class="text-end text-muted">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-end pe-4 fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="font-size: 13px; color: var(--text-heading); border-top: 2px solid rgba(226, 232, 240, 0.8);">
                        <tr>
                            <td colspan="3" class="text-end text-muted fw-medium py-1">Subtotal Item:</td>
                            <td class="text-end pe-4 fw-medium py-1">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($order->tax_amount > 0)
                        <tr>
                            <td colspan="3" class="text-end text-muted fw-medium py-1">Pajak (Tax):</td>
                            <td class="text-end pe-4 fw-medium py-1 text-danger">+ Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="3" class="text-end text-muted fw-medium py-1">Diskon:</td>
                            <td class="text-end pe-4 fw-medium py-1 text-success">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr style="background-color: rgba(14, 165, 233, 0.05);">
                            <td colspan="3" class="text-end text-primary fw-bold py-1" style="font-size: 14px;">Total Akhir:</td>
                            <td class="text-end pe-4 text-primary fw-bold py-1" style="font-size: 14px;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="alert alert-light text-center py-3 mb-0" style="border: 1px dashed var(--border-color); background: var(--bg-dark-secondary);">
                <i class="bi bi-info-circle text-muted mb-2 d-block" style="font-size: 20px;"></i>
                <span class="text-muted" style="font-size: 13px;">Tidak ada detail item yang tercatat.</span>
            </div>
        @endif
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="mt-4 pt-4" style="border-top: 1px dashed var(--border-color);">
        <h6 class="mb-3 fw-bold" style="color: var(--text-heading);"><i class="bi bi-clock-history me-2 text-warning"></i>Riwayat Pembayaran</h6>
        
        @if($order->payments->count() > 0)
            <div class="table-responsive rounded-4" style="border: 1px solid rgba(226, 232, 240, 0.6);">
                <table class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                    <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                        <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                            <th class="py-2 ps-4" style="width: 15%; font-weight: 600;">Tanggal</th>
                            <th class="py-2" style="width: 15%; font-weight: 600;">Metode</th>
                            <th class="py-2" style="width: 20%; font-weight: 600;">Penerima</th>
                            <th class="py-2" style="width: 30%; font-weight: 600;">Catatan & Lampiran</th>
                            <th class="py-2 text-end pe-4" style="width: 20%; font-weight: 600;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13px; color: var(--text-heading);">
                        @foreach($order->payments as $payment)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="text-transform: capitalize;">
                                        <i class="bi bi-credit-card me-1"></i>{{ $payment->payment_method }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                                            {{ substr($payment->creator ? $payment->creator->name : 'S', 0, 1) }}
                                        </div>
                                        <span class="text-muted">{{ $payment->creator ? $payment->creator->name : 'Sistem' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($payment->notes)
                                            <div class="text-muted" style="font-size: 12px;"><i class="bi bi-chat-text me-1"></i>{{ $payment->notes }}</div>
                                        @endif
                                        @if($payment->attachment_path)
                                            <div>
                                                <button type="button" class="badge bg-info-subtle text-info border-0 btn-view-attachment" data-url="{{ asset($payment->attachment_path) }}">
                                                    <i class="bi bi-paperclip me-1"></i>Lihat Bukti Lampiran
                                                </button>
                                            </div>
                                        @else
                                            @if(!$payment->notes) <span class="text-muted fst-italic" style="font-size: 12px;">Tidak ada catatan/lampiran</span> @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end pe-4 fw-bold text-success">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-light text-center py-3 mb-0" style="border: 1px dashed var(--border-color); background: var(--bg-dark-secondary);">
                <i class="bi bi-info-circle text-muted mb-2 d-block" style="font-size: 20px;"></i>
                <span class="text-muted" style="font-size: 13px;">Belum ada riwayat pembayaran untuk tagihan ini.</span>
            </div>
        @endif
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px dashed var(--border-color);">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
            Tutup
        </x-button>
        @if($order->payment_status !== 'paid')
            <x-button type="button" variant="success" size="sm" class="btn-pay-from-show" data-uuid="{{ $order->uuid }}" icon="bi-check2-circle">
                Lunasi Piutang
            </x-button>
        @endif
    </div>
</x-modal>

<!-- Fullscreen Lightbox Overlay -->
<div id="custom-lightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.85); z-index: 99999; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
    <button type="button" class="btn-close btn-close-white" id="close-lightbox" style="position: absolute; top: 25px; right: 30px; font-size: 20px; opacity: 1; cursor: pointer;"></button>
    <div style="width: 90%; height: 90%; display: flex; justify-content: center; align-items: center;">
        <img id="lightbox-img" src="" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <iframe id="lightbox-iframe" src="" style="width: 100%; height: 100%; border: none; display: none; background: white; border-radius: 8px;"></iframe>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-pay-from-show').on('click', function() {
        var uuid = $(this).data('uuid');
        $('#show-modal-receivable').modal('hide');
        
        setTimeout(function() {
            var url = "{{ url('business/finance/receivable') }}/" + uuid + "/payment-modal";
            ERPLoader.loadModal(url, '#modal-payment', {
                title: 'Pelunasan Piutang'
            });
        }, 300); // Wait for modal to hide
    });

    $('.btn-view-attachment').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var ext = url.split('.').pop().toLowerCase();
        
        $('#custom-lightbox').fadeIn(200).css('display', 'flex');
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            $('#lightbox-iframe').hide();
            $('#lightbox-img').attr('src', url).show();
        } else {
            $('#lightbox-img').hide();
            $('#lightbox-iframe').attr('src', url).show();
        }
    });

    $('#close-lightbox, #custom-lightbox').on('click', function(e) {
        if (e.target !== this && e.target.id !== 'close-lightbox') return;
        $('#custom-lightbox').fadeOut(200, function() {
            $('#lightbox-iframe').attr('src', '');
            $('#lightbox-img').attr('src', '');
        });
    });
});
</script>
