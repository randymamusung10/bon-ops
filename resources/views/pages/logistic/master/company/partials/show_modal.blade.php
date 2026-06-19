<x-modal id="showCompanyModal" title="Detail Perusahaan" description="Informasi lengkap mengenai perusahaan.">
    <div class="row g-3">
        <div class="col-12">
            <x-form.label>Nama Perusahaan</x-form.label>
            <div class="text-heading fw-medium fs-6">{{ $company->name }}</div>
        </div>
        <div class="col-12">
            <x-form.label>Status</x-form.label>
            <div>
                @if($company->status === 'active')
                    <span class="badge bg-success-subtle text-success px-2.5 py-1.5 rounded-pill" style="font-size: 12px; font-weight: 600;"><i class="bi bi-check-circle me-1"></i> Aktif</span>
                @else
                    <span class="badge bg-danger-subtle text-danger px-2.5 py-1.5 rounded-pill" style="font-size: 12px; font-weight: 600;"><i class="bi bi-x-circle me-1"></i> Nonaktif</span>
                @endif
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-4">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
    </div>
</x-modal>
