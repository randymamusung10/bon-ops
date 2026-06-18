<x-modal id="showCurrencyModal" title="Detail Mata Uang" description="Informasi lengkap mata uang." size="md">
    <div class="d-flex align-items-center mb-4 p-3 rounded-4" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1);">
        <div class="d-flex justify-content-center align-items-center rounded-circle" style="width: 60px; height: 60px; background: color-mix(in srgb, var(--primary-accent) 15%, transparent); color: var(--primary-accent); font-size: 24px;">
            <i class="bi bi-cash-coin"></i>
        </div>
        <div class="ms-3">
            <h5 class="mb-1 fw-bold" style="color: var(--text-heading);">{{ $currency->name }}</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="bi bi-upc-scan me-1"></i>{{ $currency->code }}</span>
                @if($currency->status === 'active')
                    <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                @else
                    <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-sm-6">
            <div class="p-3 rounded-3" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500;">Simbol</div>
                <div class="fw-semibold text-heading" style="font-size: 16px;">
                    {{ $currency->symbol ?: '-' }}
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="p-3 rounded-3 text-center" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.15)); border: 1px solid rgba(16, 185, 129, 0.2);">
                <div class="text-success fw-bold mb-1" style="font-size: 13px; text-transform: uppercase;">Nilai Tukar Base</div>
                <div class="fw-bolder" style="font-size: 20px; color: var(--text-heading);">
                    {{ (float)$currency->exchange_rate }}
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4 pt-3" style="border-top: 1px dashed var(--border-color);">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
            Tutup
        </x-button>
    </div>
</x-modal>
