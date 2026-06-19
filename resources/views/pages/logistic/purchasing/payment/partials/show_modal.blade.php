<x-modal id="showModal" title="Detail Pembayaran Supplier" description="Dokumen {{ $payment->document_number }}" size="lg">
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
