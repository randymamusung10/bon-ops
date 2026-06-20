<x-modal id="showWasteModal" title="Detail Stock Waste" description="Dokumen {{ $waste->document_number }}" size="xl">
    <div class="row g-3 mb-4">
        <!-- Info Box 1: Informasi Dokumen -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Dokumen</h6>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Tanggal</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $waste->date->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Status</span>
                        <span>
                            @if($waste->status === 'draft')
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-file-earmark-text me-1"></i> Draft</span>
                            @elseif($waste->status === 'submitted')
                                <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-clock me-1"></i> Submitted</span>
                            @elseif($waste->status === 'approved')
                                <span class="badge bg-info-subtle text-info px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-check me-1"></i> Approved</span>
                            @elseif($waste->status === 'posted')
                                <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-check-circle-fill me-1"></i> Posted</span>
                            @else
                                <span class="badge bg-dark px-2.5 py-1 rounded-pill" style="font-size: 11px;">{{ strtoupper($waste->status) }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Dibuat Oleh</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $waste->creator->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Box 2: Lokasi & Posting -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;"><i class="bi bi-geo-alt me-2 text-primary"></i>Lokasi & Penanggung Jawab</h6>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Cabang</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $waste->branch->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Gudang</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $waste->warehouse->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="text-muted fw-medium mt-1" style="font-size: 12px;">Diposting Oleh</span>
                        <span class="text-heading fw-semibold text-end" style="font-size: 13px;">
                            {{ $waste->poster->name ?? '-' }} 
                            @if($waste->posted_at)
                                <br><span class="text-muted fw-normal" style="font-size: 11px;">({{ $waste->posted_at->format('d/m/Y H:i') }})</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        @if($waste->notes)
        <div class="col-12">
            <div class="p-3 rounded-4 mt-1" style="background: rgba(14, 165, 233, 0.05); border: 1px dashed rgba(14, 165, 233, 0.3);">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                        <i class="bi bi-chat-text"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted fw-medium" style="font-size: 12px;">Catatan Waste:</p>
                        <p class="mb-0 text-heading fw-semibold" style="font-size: 13px; line-height: 1.5;">{{ $waste->notes }}</p>
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
        <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Daftar Item Pembuangan</h6>
    </div>

    <div class="table-responsive rounded-4 overflow-hidden mb-4" style="border: 1px solid rgba(226, 232, 240, 0.2);">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
            <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                <tr class="text-muted" style="letter-spacing: 0.2px;">
                    <th width="5%" class="text-center py-3 ps-4 border-0">No</th>
                    <th width="35%" class="py-3 border-0">Produk</th>
                    <th width="15%" class="text-end py-3 border-0">Qty Waste</th>
                    <th width="20%" class="text-end py-3 border-0">Harga Pokok (Cost)</th>
                    <th width="25%" class="text-end py-3 pe-4 border-0">Estimasi Kerugian</th>
                </tr>
            </thead>
            <tbody class="border-top-0 text-heading">
                @php $totalLoss = 0; @endphp
                @foreach($waste->items as $idx => $item)
                @php 
                    $itemLoss = $item->qty * $item->cost; 
                    $totalLoss += $itemLoss;
                @endphp
                <tr>
                    <td class="text-center py-3 ps-4">{{ $idx + 1 }}</td>
                    <td class="py-3 fw-medium">
                        {{ $item->product->name }} 
                        <span class="badge bg-danger-subtle text-danger ms-2 px-2 py-0.5 rounded-pill" style="font-size: 10px;">{{ $item->reason }}</span>
                    </td>
                    <td class="text-end py-3 fw-bold text-danger">{{ number_format($item->qty, 2, ',', '.') }} {{ $item->product->unit->name ?? 'pcs' }}</td>
                    <td class="text-end py-3">Rp {{ number_format($item->cost, 2, ',', '.') }}</td>
                    <td class="text-end py-3 pe-4 fw-semibold text-danger">Rp {{ number_format($itemLoss, 2, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="table-light fw-bold" style="border-top: 2px solid rgba(226, 232, 240, 0.4);">
                    <td colspan="4" class="text-end py-3 ps-4 border-0">Total Estimasi Kerugian:</td>
                    <td class="text-end py-3 pe-4 text-danger border-0">Rp {{ number_format($totalLoss, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
        
        @if($waste->status === 'draft')
        <x-button type="button" variant="warning" size="sm" class="btn-action-waste" data-uuid="{{ $waste->uuid }}" data-action="submit" icon="bi-send">Ajukan (Submit)</x-button>
        @endif

        @if($waste->status === 'submitted')
        <x-button type="button" variant="primary" size="sm" class="btn-action-waste" data-uuid="{{ $waste->uuid }}" data-action="approve" icon="bi-check-lg">Setujui (Approve)</x-button>
        @endif

        @if($waste->status === 'approved')
        <x-button type="button" variant="success" size="sm" class="btn-action-waste" data-uuid="{{ $waste->uuid }}" data-action="post" icon="bi-check-circle-fill">Posting Waste</x-button>
        @endif
    </div>
</x-modal>
