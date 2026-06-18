<x-modal id="showCategoryModal" title="Detail Kategori Produk" description="Informasi lengkap hierarki kategori." size="lg">
    
    <!-- Header / Title Section -->
    <div class="d-flex align-items-center mb-4 p-3 rounded-4" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1);">
        <div class="d-flex justify-content-center align-items-center rounded-circle" style="width: 60px; height: 60px; background: color-mix(in srgb, var(--primary-accent) 15%, transparent); color: var(--primary-accent); font-size: 24px;">
            <i class="bi bi-tags"></i>
        </div>
        <div class="ms-3">
            <h5 class="mb-1 fw-bold" style="color: var(--text-heading);">{{ $category->name }}</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="bi bi-upc-scan me-1"></i>{{ $category->code }}</span>
                @if($category->status === 'active')
                    <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                @else
                    <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Data Grid -->
    <div class="row g-3">
        <!-- Kotak Info 1 -->
        <div class="col-md-12">
            <div class="p-3 rounded-3 h-100" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500; letter-spacing: 0.2px;">
                    <i class="bi bi-diagram-3 me-1"></i> Kategori Induk (Parent)
                </div>
                <div class="fw-medium text-heading" style="font-size: 14px;">
                    @if($category->parent)
                        <span class="text-primary"><i class="bi bi-arrow-return-right me-1"></i> [{{ $category->parent->code }}] {{ $category->parent->name }}</span>
                    @else
                        <span class="text-muted"><i class="bi bi-dash me-1"></i>Kategori Level Teratas (Root)</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kotak Info 2 (Full Width) -->
        <div class="col-12">
            <div class="p-3 rounded-3" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500; letter-spacing: 0.2px;">
                    <i class="bi bi-card-text me-1"></i> Deskripsi
                </div>
                <div class="fw-medium text-heading" style="font-size: 14px; line-height: 1.5;">
                    {{ $category->description ?: 'Tidak ada deskripsi' }}
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
