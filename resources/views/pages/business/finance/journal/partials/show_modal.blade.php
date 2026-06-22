<x-modal id="showModal" title="Detail Jurnal Umum" description="Dokumen {{ $journal->journal_number }}" size="xl">
    <!-- Header Summary -->
    <div class="d-flex align-items-center justify-content-between p-3 rounded-4 mb-4" style="background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.05) 0%, rgba(var(--bs-primary-rgb), 0.01) 100%); border: 1px solid rgba(var(--bs-primary-rgb), 0.1);">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                <i class="bi bi-journal-text fs-4 text-primary"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1 text-heading">{{ $journal->journal_number }}</h5>
                <span class="text-muted" style="font-size: 12px;">Dibuat pada {{ \Carbon\Carbon::parse($journal->created_at)->format('d M Y, H:i') }} oleh {{ $journal->creator->name ?? 'Sistem' }}</span>
            </div>
        </div>
        <div class="text-end">
            <div class="text-muted fw-medium mb-1" style="font-size: 11px;">Status Dokumen</div>
            @if($journal->status === 'draft')
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill" style="font-size: 12px;"><i class="bi bi-file-earmark me-1"></i> Draft</span>
            @elseif($journal->status === 'submitted')
                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill" style="font-size: 12px;"><i class="bi bi-clock me-1"></i> Menunggu Persetujuan</span>
            @elseif($journal->status === 'approved')
                <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill" style="font-size: 12px;"><i class="bi bi-check me-1"></i> Disetujui</span>
            @elseif($journal->status === 'posted')
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill" style="font-size: 12px;"><i class="bi bi-check-circle-fill me-1"></i> Terposting</span>
            @elseif($journal->status === 'void')
                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill" style="font-size: 12px;"><i class="bi bi-x-circle me-1"></i> Dibatalkan</span>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Informasi Dokumen -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;"><i class="bi bi-info-circle me-2 text-primary"></i>Informasi Dokumen</h6>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted fw-medium mb-1" style="font-size: 11px;">Tanggal Jurnal</div>
                        <div class="fw-semibold text-heading" style="font-size: 13px;">{{ \Carbon\Carbon::parse($journal->date)->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fw-medium mb-1" style="font-size: 11px;">Tipe Referensi</div>
                        <div class="fw-semibold text-heading" style="font-size: 13px;">{{ $journal->reference_type ?: '-' }}</div>
                    </div>
                    <div class="col-sm-12">
                        <div class="text-muted fw-medium mb-1" style="font-size: 11px;">ID Referensi</div>
                        <div class="fw-semibold text-heading" style="font-size: 13px;">{{ $journal->reference_id ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Operasional -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;"><i class="bi bi-building me-2 text-primary"></i>Detail Operasional</h6>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted fw-medium mb-1" style="font-size: 11px;">Cabang</div>
                        <div class="fw-semibold text-heading" style="font-size: 13px;">{{ $journal->branch->name ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fw-medium mb-1" style="font-size: 11px;">Dibuat Oleh</div>
                        <div class="fw-semibold text-heading" style="font-size: 13px;">{{ $journal->creator->name ?? '-' }}</div>
                    </div>
                    <div class="col-sm-12">
                        <div class="text-muted fw-medium mb-1" style="font-size: 11px;">Mata Uang</div>
                        <div class="fw-bold text-heading" style="font-size: 13px;">IDR - Rupiah (Base)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Rincian -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Baris Jurnal</h6>
        </div>
        
        <div class="table-responsive rounded-4 overflow-hidden mb-4" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="5%" class="py-2 ps-3 text-center">No</th>
                        <th width="35%" class="py-2">Kode & Nama Akun (COA)</th>
                        <th width="30%" class="py-2">Keterangan Baris</th>
                        <th width="15%" class="text-end py-2">Debit (Rp)</th>
                        <th width="15%" class="text-end py-2 pe-4">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 text-heading">
                    @foreach($journal->items as $index => $item)
                    <tr>
                        <td class="py-2 ps-3 text-center text-muted">{{ $index + 1 }}</td>
                        <td class="py-2 fw-medium">
                            {{ $item->account->code ?? '' }} 
                            <br>
                            <span class="text-muted fw-normal" style="font-size: 11px;">{{ $item->account->name ?? '' }}</span>
                        </td>
                        <td class="py-2">{{ $item->description ?: '-' }}</td>
                        <td class="text-end py-2 fw-bold text-primary">{{ number_format($item->debit, 0, ',', '.') }}</td>
                        <td class="text-end py-2 pe-4 fw-bold text-primary">{{ number_format($item->credit, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                        <td colspan="3" class="py-1 ps-4 text-end fw-bold">Total Keseluruhan (Rp)</td>
                        <td class="py-1 text-end fw-bold text-primary fs-6">{{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                        <td class="py-1 pe-4 text-end fw-bold text-primary fs-6">{{ number_format($journal->total_credit, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="row g-4 mb-2">
        <div class="col-md-8">
            <h6 class="fw-bold mb-2 text-heading" style="font-size: 13px;"><i class="bi bi-chat-left-text me-2 text-primary"></i>Keterangan Tambahan</h6>
            <div class="p-3 rounded-4 h-100" style="background: rgba(var(--bs-primary-rgb), 0.03); border: 1px dashed rgba(var(--bs-primary-rgb), 0.2);">
                <p class="mb-0 text-heading" style="font-size: 13px; line-height: 1.6; white-space: pre-line;">{{ $journal->notes ?: '-' }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <h6 class="fw-bold mb-2 text-heading" style="font-size: 13px;"><i class="bi bi-paperclip me-2 text-primary"></i>Lampiran Dokumen</h6>
            <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-center align-items-center text-center" style="background: rgba(var(--bs-primary-rgb), 0.03); border: 1px dashed rgba(var(--bs-primary-rgb), 0.2);">
                @if($journal->attachment_path)
                <div class="w-100 text-start">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-white rounded p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                            <i class="bi bi-file-earmark-check text-primary fs-5"></i>
                        </div>
                        <div>
                            <span class="d-block fw-bold text-heading" style="font-size: 12px;">Dokumen Bukti</span>
                            <span class="text-muted" style="font-size: 11px;">Tersedia</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ Storage::url($journal->attachment_path) }}" target="_blank" class="btn btn-primary btn-sm flex-grow-1 rounded-3 d-flex justify-content-center align-items-center gap-2">
                            <i class="bi bi-eye"></i> Lihat
                        </a>
                        <a href="{{ Storage::url($journal->attachment_path) }}" target="_blank" class="btn btn-primary btn-sm rounded-3 px-3" title="Unduh File" download>
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
                @else
                    <i class="bi bi-file-earmark-x fs-3 text-muted mb-2" style="opacity: 0.5;"></i>
                    <span class="text-muted" style="font-size: 12px;">Tidak ada lampiran</span>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
        @if(in_array($journal->status, ['approved', 'posted']))
            <a href="{{ route('business.finance.journal.print', $journal->uuid) }}" target="_blank" class="btn btn-primary btn-sm rounded-3 px-3 me-auto">
                <i class="bi bi-printer me-1"></i> Cetak Jurnal Voucher
            </a>
        @endif
        
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
        
        @if($journal->status === 'draft')
            <x-button type="button" variant="primary" size="sm" class="btn-action-journal" data-uuid="{{ $journal->uuid }}" data-action="submit" icon="bi-send">
                Ajukan (Submit)
            </x-button>
        @elseif($journal->status === 'submitted')
            <x-button type="button" variant="primary" size="sm" class="btn-action-journal" data-uuid="{{ $journal->uuid }}" data-action="approve" icon="bi-check-lg">
                Setujui (Approve)
            </x-button>
        @elseif($journal->status === 'approved')
            <x-button type="button" variant="primary" size="sm" class="btn-action-journal" data-uuid="{{ $journal->uuid }}" data-action="post" icon="bi-check-circle-fill">
                Posting Jurnal
            </x-button>
        @endif
    </div>
</x-modal>

