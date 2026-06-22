<x-modal id="showAdjustmentModal" title="Detail Penyesuaian Stok" description="Dokumen {{ $adjustment->document_number }}" size="xl">
    <div class="row g-3 mb-4">
        <!-- Info Box 1: Informasi Dokumen -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Dokumen</h6>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Tanggal</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $adjustment->date->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Status</span>
                        <span>
                            @if($adjustment->status === 'draft')
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-file-earmark-text me-1"></i> Draft</span>
                            @elseif($adjustment->status === 'submitted')
                                <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-clock me-1"></i> Submitted</span>
                            @elseif($adjustment->status === 'approved')
                                <span class="badge bg-info-subtle text-info px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-check me-1"></i> Approved</span>
                            @elseif($adjustment->status === 'posted')
                                <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill" style="font-size: 11px;"><i class="bi bi-check-circle-fill me-1"></i> Posted</span>
                            @else
                                <span class="badge bg-dark px-2.5 py-1 rounded-pill" style="font-size: 11px;">{{ strtoupper($adjustment->status) }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Dibuat Oleh</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $adjustment->creator->name ?? '-' }}</span>
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
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $adjustment->branch->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Gudang</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $adjustment->warehouse->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="text-muted fw-medium mt-1" style="font-size: 12px;">Diposting Oleh</span>
                        <span class="text-heading fw-semibold text-end" style="font-size: 13px;">
                            {{ $adjustment->poster->name ?? '-' }} 
                            @if($adjustment->posted_at)
                                <br><span class="text-muted fw-normal" style="font-size: 11px;">({{ $adjustment->posted_at->format('d/m/Y H:i') }})</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        @if($adjustment->notes)
        <div class="col-12">
            <div class="p-3 rounded-4 mt-1" style="background: rgba(14, 165, 233, 0.05); border: 1px dashed rgba(14, 165, 233, 0.3);">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                        <i class="bi bi-chat-text"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted fw-medium" style="font-size: 12px;">Catatan Penyesuaian:</p>
                        <p class="mb-0 text-heading fw-semibold" style="font-size: 13px; line-height: 1.5;">{{ $adjustment->notes }}</p>
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
        <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Daftar Item Penyesuaian</h6>
    </div>

    <div class="table-responsive rounded-4 overflow-hidden mb-4" style="border: 1px solid rgba(226, 232, 240, 0.2);">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
            <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                <tr class="text-muted" style="letter-spacing: 0.2px;">
                    <th width="5%" class="text-center py-2 ps-4 border-0">No</th>
                    <th width="35%" class="py-2 border-0">Produk</th>
                    <th width="15%" class="text-end py-2 border-0">Stok Sistem</th>
                    <th width="15%" class="text-end py-2 border-0">Stok Fisik</th>
                    <th width="15%" class="text-end py-2 border-0">Selisih</th>
                    <th width="15%" class="py-2 pe-4 border-0">Alasan</th>
                </tr>
            </thead>
            <tbody class="border-top-0 text-heading">
                @foreach($adjustment->items as $idx => $item)
                <tr>
                    <td class="text-center py-3 ps-4">{{ $idx + 1 }}</td>
                    <td class="py-3 fw-medium">{{ $item->product->name }} <span class="text-muted ms-1" style="font-size: 12px;">({{ $item->product->code ?? '-' }})</span></td>
                    <td class="text-end py-3">{{ number_format($item->system_qty, 2, ',', '.') }}</td>
                    <td class="text-end py-3 fw-bold text-primary">{{ number_format($item->actual_qty, 2, ',', '.') }}</td>
                    <td class="text-end py-3 fw-semibold {{ $item->difference < 0 ? 'text-danger' : ($item->difference > 0 ? 'text-success' : 'text-muted') }}">
                        @if($item->difference > 0)
                            <i class="bi bi-arrow-up-right me-1" style="font-size: 10px;"></i>+{{ number_format($item->difference, 2, ',', '.') }}
                        @elseif($item->difference < 0)
                            <i class="bi bi-arrow-down-right me-1" style="font-size: 10px;"></i>{{ number_format($item->difference, 2, ',', '.') }}
                        @else
                            0,00
                        @endif
                    </td>
                    <td class="py-3 pe-4 text-muted">{{ $item->reason ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
        
        @if($adjustment->status === 'draft')
        <x-button type="button" variant="warning" size="sm" class="btn-action-adjustment" data-uuid="{{ $adjustment->uuid }}" data-action="submit" icon="bi-send">Ajukan (Submit)</x-button>
        @endif

        @if($adjustment->status === 'submitted')
        <x-button type="button" variant="primary" size="sm" class="btn-action-adjustment" data-uuid="{{ $adjustment->uuid }}" data-action="approve" icon="bi-check-lg">Setujui (Approve)</x-button>
        @endif

        @if($adjustment->status === 'approved')
        <x-button type="button" variant="success" size="sm" class="btn-action-adjustment" data-uuid="{{ $adjustment->uuid }}" data-action="post" icon="bi-check-circle-fill">Posting Penyesuaian</x-button>
        @endif
    </div>
</x-modal>
