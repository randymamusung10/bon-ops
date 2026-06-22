<x-modal id="showModal" title="Detail Purchase Order" description="Dokumen {{ $order->po_number }}" size="xl">
    <div class="row g-3 mb-4">
        <!-- Info Box 1: Informasi Dokumen -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Dokumen</h6>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Tanggal PO</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Estimasi Diterima</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $order->expected_date ? \Carbon\Carbon::parse($order->expected_date)->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Status</span>
                        <span>
                            @if($order->status === 'draft') 
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-file-earmark me-1"></i> Draft</span>
                            @elseif($order->status === 'submitted') 
                                <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-clock me-1"></i> Submitted</span>
                            @elseif($order->status === 'approved') 
                                <span class="badge bg-info-subtle text-info px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-check me-1"></i> Approved</span>
                            @elseif($order->status === 'posted') 
                                <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-check-circle-fill me-1"></i> Posted</span>
                            @elseif($order->status === 'closed') 
                                <span class="badge bg-dark-subtle text-dark px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-lock-fill me-1"></i> Closed</span>
                            @else 
                                <span class="badge bg-dark px-2.5 py-1 rounded-pill" style="font-size: 11px;">{{ $order->status }}</span> 
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Box 2: Pihak Terlibat & Approval -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;"><i class="bi bi-people me-2 text-primary"></i>Pihak Terkait & Workflow</h6>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Cabang Pemesan</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $order->branch->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Supplier</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $order->supplier->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="text-muted fw-medium mt-1" style="font-size: 12px;">Workflow</span>
                        <span class="text-heading fw-semibold text-end" style="font-size: 13px;">
                            Dibuat: {{ $order->creator->name ?? '-' }}<br>
                            @if($order->approver)
                                Disetujui: {{ $order->approver->name }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        @if($order->notes)
        <div class="col-12">
            <div class="p-3 rounded-4 mt-1" style="background: rgba(14, 165, 233, 0.05); border: 1px dashed rgba(14, 165, 233, 0.3);">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                        <i class="bi bi-chat-text"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted fw-medium" style="font-size: 12px;">Catatan PO:</p>
                        <p class="mb-0 text-heading fw-semibold" style="font-size: 13px; line-height: 1.5;">{{ $order->notes }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="d-flex align-items-center mb-3">
        <div class="bg-primary rounded px-2 py-1 me-2">
            <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
        </div>
        <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Daftar Item Pemesanan</h6>
    </div>

    <div class="table-responsive rounded-4 overflow-hidden mb-4" style="border: 1px solid rgba(226, 232, 240, 0.2);">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
            <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                <tr class="text-muted" style="letter-spacing: 0.2px;">
                    <th width="5%" class="text-center py-2 ps-4 border-0">No</th>
                    <th width="35%" class="py-2 border-0">Produk</th>
                    <th width="15%" class="py-2 border-0">Satuan</th>
                    <th width="15%" class="text-end py-2 border-0">Kuantitas</th>
                    <th width="15%" class="text-end py-2 border-0">Harga Satuan</th>
                    <th width="15%" class="text-end py-2 pe-4 border-0">Total Harga</th>
                </tr>
            </thead>
            <tbody class="border-top-0 text-heading">
                @foreach($order->items as $idx => $item)
                <tr>
                    <td class="text-center py-3 ps-4">{{ $idx + 1 }}</td>
                    <td class="py-3 fw-medium">{{ $item->product->name }} <span class="text-muted ms-1" style="font-size: 12px;">({{ $item->product->code ?? '-' }})</span></td>
                    <td class="py-3">{{ $item->unit->name }}</td>
                    <td class="text-end py-3 fw-bold text-primary">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                    <td class="text-end py-3 text-muted">Rp {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="text-end py-3 pe-4 fw-semibold">Rp {{ number_format($item->total_price, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <td colspan="5" class="py-1 text-end fw-bold">Total Keseluruhan</td>
                    <td class="py-1 pe-4 text-end fw-bold text-primary">Rp {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
        
        @if($order->status === 'draft')
        <x-button type="button" variant="warning" size="sm" class="btn-action-po" data-uuid="{{ $order->uuid }}" data-action="submit" icon="bi-send">Ajukan (Submit)</x-button>
        @endif

        @if($order->status === 'submitted')
        <x-button type="button" variant="primary" size="sm" class="btn-action-po" data-uuid="{{ $order->uuid }}" data-action="approve" icon="bi-check-lg">Setujui (Approve)</x-button>
        @endif

        @if($order->status === 'approved')
        <x-button type="button" variant="success" size="sm" class="btn-action-po" data-uuid="{{ $order->uuid }}" data-action="post" icon="bi-check-circle-fill">Posting PO</x-button>
        @endif
    </div>
</x-modal>
