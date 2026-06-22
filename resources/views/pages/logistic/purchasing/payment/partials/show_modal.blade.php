<x-modal id="showModal" title="Detail Pembayaran Supplier" description="Dokumen {{ $payment->document_number }}" size="xl">
    <div class="row g-3 mb-4">
        <!-- Info Box 1: Informasi Pembayaran -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Pembayaran</h6>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Tanggal Bayar</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Metode</span>
                        <span class="text-heading fw-semibold text-capitalize" style="font-size: 13px;">{{ $payment->payment_method }}</span>
                    </div>
                    @if($payment->bank_name)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Bank</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $payment->bank_name }}</span>
                    </div>
                    @endif
                    @if($payment->bank_account_number)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">No. Rekening</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $payment->bank_account_number }}</span>
                    </div>
                    @endif
                    @if($payment->bank_reference)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">No. Referensi</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $payment->bank_reference }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Status</span>
                        <span>
                            @if($payment->status === 'draft')
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Draft</span>
                            @elseif($payment->status === 'submitted')
                                <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Submitted</span>
                            @elseif($payment->status === 'approved')
                                <span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill">Approved</span>
                            @elseif($payment->status === 'posted')
                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Posted / Lunas</span>
                            @else
                                <span class="badge bg-dark px-2 py-1 rounded-pill">{{ $payment->status }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Box 2: Referensi & Workflow -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;"><i class="bi bi-diagram-3 me-2 text-primary"></i>Referensi & Workflow</h6>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Supplier</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $payment->supplier->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Ref. Faktur (Invoice)</span>
                        <span class="text-primary fw-semibold" style="font-size: 13px;">{{ $payment->supplierInvoice->document_number ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Total Tagihan Faktur</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">Rp {{ number_format($payment->invoice_amount, 2, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="text-muted fw-medium mt-1" style="font-size: 12px;">Workflow</span>
                        <span class="text-heading fw-semibold text-end" style="font-size: 13px;">
                            Dibuat: {{ $payment->creator->name ?? '-' }}<br>
                            @if($payment->approver)
                                Disetujui: {{ $payment->approver->name }}
                            @endif
                            @if($payment->poster)
                                <br>Diposting: {{ $payment->poster->name }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($payment->notes)
        <div class="col-12">
            <div class="p-3 rounded-4 mt-1" style="background: rgba(14, 165, 233, 0.05); border: 1px dashed rgba(14, 165, 233, 0.3);">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                        <i class="bi bi-chat-text"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted fw-medium" style="font-size: 12px;">Catatan Pembayaran:</p>
                        <p class="mb-0 text-heading fw-semibold" style="font-size: 13px; line-height: 1.5;">{{ $payment->notes }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($payment->attachment_path)
        <div class="col-12">
            <div class="p-3 rounded-4 mt-1" style="background: rgba(14, 165, 233, 0.05); border: 1px dashed rgba(14, 165, 233, 0.3);">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                            <i class="bi bi-paperclip"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-heading fw-semibold" style="font-size: 13px;">Lampiran Bukti Pembayaran</p>
                            <p class="mb-0 text-muted" style="font-size: 11px;">Tersedia dokumen terlampir.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill btn-view-attachment px-3" style="font-size: 11px;" data-url="{{ asset($payment->attachment_path) }}">
                        <i class="bi bi-eye me-1"></i>Lihat
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Summary Pembayaran -->
        <div class="col-12">
            <div class="p-3 rounded-4" style="background: color-mix(in srgb, var(--primary-accent) 6%, transparent); border: 1px solid rgba(226, 232, 240, 0.15);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p class="mb-1 text-muted fw-medium" style="font-size: 12px;">Jumlah Pembayaran Ini</p>
                        <p class="mb-0 fw-bold text-primary" style="font-size: 22px;">Rp {{ number_format($payment->payment_amount, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        @if($payment->payment_amount >= $payment->invoice_amount)
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill" style="font-size: 12px;"><i class="bi bi-check-circle-fill me-1"></i> Lunas Penuh</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill" style="font-size: 12px;"><i class="bi bi-exclamation-triangle me-1"></i> Bayar Sebagian</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Item Invoice yang Dibayar -->
    @if($payment->supplierInvoice)
    <div class="mt-4 pt-4" style="border-top: 1px dashed var(--border-color);">
        <h6 class="mb-3 fw-bold" style="color: var(--text-heading);"><i class="bi bi-box me-2 text-primary"></i>Detail Item Faktur ({{ $payment->supplierInvoice->document_number }})</h6>
        
        @if($payment->supplierInvoice->items && $payment->supplierInvoice->items->count() > 0)
            <div class="table-responsive rounded-4" style="border: 1px solid rgba(226, 232, 240, 0.6);">
                <table class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                    <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                        <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                            <th class="py-2 ps-4" style="width: 40%; font-weight: 600;">Produk / Layanan</th>
                            <th class="py-2 text-center" style="width: 15%; font-weight: 600;">Kuantitas</th>
                            <th class="py-2 text-end" style="width: 20%; font-weight: 600;">Harga Satuan</th>
                            <th class="py-2 text-end pe-4" style="width: 25%; font-weight: 600;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13px; color: var(--text-heading);">
                        @foreach($payment->supplierInvoice->items as $item)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $item->product->name ?? $item->item_name ?? 'Item' }}</td>
                                <td class="text-center">{{ (float) $item->quantity }}</td>
                                <td class="text-end text-muted">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-end pe-4 fw-semibold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="font-size: 13px; color: var(--text-heading); border-top: 2px solid rgba(226, 232, 240, 0.8);">
                        <tr style="background-color: rgba(14, 165, 233, 0.05);">
                            <td colspan="3" class="text-end text-primary fw-bold py-1" style="font-size: 14px;">Total Tagihan:</td>
                            <td class="text-end pe-4 text-primary fw-bold py-1" style="font-size: 14px;">Rp {{ number_format($payment->supplierInvoice->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="alert alert-light text-center py-3 mb-0" style="border: 1px dashed var(--border-color); background: var(--bg-dark-secondary);">
                <i class="bi bi-info-circle text-muted mb-2 d-block" style="font-size: 20px;"></i>
                <span class="text-muted" style="font-size: 13px;">Tidak ada detail item yang tercatat pada invoice ini.</span>
            </div>
        @endif
    </div>
    
    <!-- Riwayat Pembayaran Invoice Tersebut -->
    <div class="mt-4 pt-4" style="border-top: 1px dashed var(--border-color);">
        <h6 class="mb-3 fw-bold" style="color: var(--text-heading);"><i class="bi bi-clock-history me-2 text-warning"></i>Riwayat Pembayaran Faktur</h6>
        
        @if($payment->supplierInvoice->payments && $payment->supplierInvoice->payments->count() > 0)
            <div class="table-responsive rounded-4" style="border: 1px solid rgba(226, 232, 240, 0.6);">
                <table class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                    <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                        <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                            <th class="py-2 ps-4" style="width: 15%; font-weight: 600;">Tanggal</th>
                            <th class="py-2" style="width: 15%; font-weight: 600;">Metode</th>
                            <th class="py-2" style="width: 20%; font-weight: 600;">No. Dokumen / Status</th>
                            <th class="py-2" style="width: 30%; font-weight: 600;">Catatan & Lampiran</th>
                            <th class="py-2 text-end pe-4" style="width: 20%; font-weight: 600;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13px; color: var(--text-heading);">
                        @foreach($payment->supplierInvoice->payments as $histPayment)
                            <tr @if($histPayment->id === $payment->id) style="background-color: rgba(16, 185, 129, 0.05);" @endif>
                                <td class="ps-4 fw-medium">{{ \Carbon\Carbon::parse($histPayment->payment_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="text-transform: capitalize;">
                                        <i class="bi bi-credit-card me-1"></i>{{ $histPayment->payment_method }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-medium text-heading mb-1">{{ $histPayment->document_number }} @if($histPayment->id === $payment->id) <span class="badge bg-success ms-1" style="font-size: 9px;">Ini</span> @endif</div>
                                    @if($histPayment->status === 'posted')
                                        <span class="badge bg-success-subtle text-success" style="font-size: 10px;">Posted</span>
                                    @elseif($histPayment->status === 'approved')
                                        <span class="badge bg-info-subtle text-info" style="font-size: 10px;">Approved</span>
                                    @elseif($histPayment->status === 'submitted')
                                        <span class="badge bg-warning-subtle text-warning" style="font-size: 10px;">Submitted</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($histPayment->notes)
                                            <div class="text-muted" style="font-size: 12px;"><i class="bi bi-chat-text me-1"></i>{{ $histPayment->notes }}</div>
                                        @endif
                                        @if($histPayment->attachment_path)
                                            <div>
                                                <button type="button" class="badge bg-info-subtle text-info border-0 btn-view-attachment" data-url="{{ asset($histPayment->attachment_path) }}">
                                                    <i class="bi bi-paperclip me-1"></i>Lihat Bukti Lampiran
                                                </button>
                                            </div>
                                        @else
                                            @if(!$histPayment->notes) <span class="text-muted fst-italic" style="font-size: 12px;">Tidak ada catatan/lampiran</span> @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end pe-4 fw-bold @if($histPayment->id === $payment->id) text-success @endif">
                                    Rp {{ number_format($histPayment->payment_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif


    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>

        @if($payment->status === 'draft')
        <x-button type="button" variant="warning" size="sm" class="btn-action-payment" data-uuid="{{ $payment->uuid }}" data-action="submit" icon="bi-send">Ajukan (Submit)</x-button>
        @endif

        @if($payment->status === 'submitted')
        <x-button type="button" variant="primary" size="sm" class="btn-action-payment" data-uuid="{{ $payment->uuid }}" data-action="approve" icon="bi-check-lg">Setujui (Approve)</x-button>
        @endif

        @if($payment->status === 'approved')
        <x-button type="button" variant="success" size="sm" class="btn-action-payment" data-uuid="{{ $payment->uuid }}" data-action="post" icon="bi-check-circle-fill">Posting (Lunaskan Hutang)</x-button>
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
