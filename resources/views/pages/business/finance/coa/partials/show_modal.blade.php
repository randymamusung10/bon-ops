<x-modal id="showCoaModal" title="Detail Akun" description="Informasi lengkap Chart of Account." size="md">
    <div class="d-flex align-items-center mb-4 p-3 rounded-4" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1);">
        <div class="d-flex justify-content-center align-items-center rounded-circle" style="width: 60px; height: 60px; background: color-mix(in srgb, var(--primary-accent) 15%, transparent); color: var(--primary-accent); font-size: 24px;">
            <i class="bi bi-wallet2"></i>
        </div>
        <div class="ms-3">
            <h5 class="mb-1 fw-bold" style="color: var(--text-heading);">{{ $coa->name }}</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="bi bi-upc-scan me-1"></i>{{ $coa->code }}</span>
                @if($coa->status === 'active')
                    <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                @else
                    <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-sm-6">
            @php
                $bgClass = 'bg-secondary-subtle';
                $textClass = 'text-secondary';
                $borderClass = 'border-secondary';
                $typeText = $coa->account_type;

                if ($coa->account_type === 'asset') { $bgClass = 'bg-primary-subtle'; $textClass = 'text-primary'; $borderClass = 'border-primary'; $typeText = 'Aset (Asset)'; }
                elseif ($coa->account_type === 'liability') { $bgClass = 'bg-warning-subtle'; $textClass = 'text-warning'; $borderClass = 'border-warning'; $typeText = 'Kewajiban (Liability)'; }
                elseif ($coa->account_type === 'equity') { $bgClass = 'bg-info-subtle'; $textClass = 'text-info'; $borderClass = 'border-info'; $typeText = 'Ekuitas (Equity)'; }
                elseif ($coa->account_type === 'revenue') { $bgClass = 'bg-success-subtle'; $textClass = 'text-success'; $borderClass = 'border-success'; $typeText = 'Pendapatan (Revenue)'; }
                elseif ($coa->account_type === 'expense') { $bgClass = 'bg-danger-subtle'; $textClass = 'text-danger'; $borderClass = 'border-danger'; $typeText = 'Beban (Expense)'; }
            @endphp
            <div class="p-3 rounded-3 text-center {{ $bgClass }} border" style="--bs-border-opacity: .2;">
                <div class="{{ $textClass }} fw-bold mb-1" style="font-size: 13px; text-transform: uppercase;">Tipe Akun</div>
                <div class="fw-bolder" style="font-size: 18px; color: var(--text-heading);">
                    {{ $typeText }}
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="p-3 rounded-3 text-center bg-light border" style="--bs-border-opacity: .2;">
                <div class="text-muted fw-bold mb-1" style="font-size: 13px; text-transform: uppercase;">Sifat Akun</div>
                <div class="fw-bolder" style="font-size: 18px; color: var(--text-heading);">
                    @if($coa->is_header)
                        <span class="text-dark"><i class="bi bi-folder-fill text-warning me-2"></i>Header (Induk)</span>
                    @else
                        <span class="text-dark"><i class="bi bi-file-earmark-text-fill text-info me-2"></i>Detail (Transaksi)</span>
                    @endif
                </div>
            </div>
        </div>
        @if($coa->parent)
        <div class="col-sm-12">
            <div class="p-3 rounded-3" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                <div class="text-muted fw-bold mb-1" style="font-size: 13px; text-transform: uppercase;">Akun Induk (Parent)</div>
                <div class="fw-semibold" style="color: var(--text-heading);">
                    [{{ $coa->parent->code }}] {{ $coa->parent->name }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="d-flex justify-content-end mt-4 pt-3" style="border-top: 1px dashed var(--border-color);">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
            Tutup
        </x-button>
    </div>
</x-modal>
