<x-modal id="showRecipeModal" title="Detail Resep Produk" size="lg" description="Formula resep dan perhitungan cost (HPP) menu.">
    <div class="row g-3 mb-4">
        <!-- Info Box 1: Informasi Resep -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Resep
                </h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Kode Resep</span>
                        <span class="fw-mono text-heading fw-semibold" style="font-size: 13px;">{{ $recipe->code }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Nama Resep</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $recipe->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Status</span>
                        <span>
                            @if($recipe->status === 'active')
                                <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                                    <i class="bi bi-check-circle me-1"></i> Aktif
                                </span>
                            @elseif($recipe->status === 'draft')
                                <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                                    <i class="bi bi-info-circle me-1"></i> Draft
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                                    <i class="bi bi-x-circle me-1"></i> Nonaktif
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Box 2: Target & Output -->
        <div class="col-md-6">
            <div class="p-3 rounded-4 h-100" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                <h6 class="fw-bold mb-3 text-heading" style="font-size: 13px;">
                    <i class="bi bi-egg-fried me-2 text-primary"></i>Target & Hasil Produksi
                </h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Produk Target</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $recipe->product->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Stasiun Kerja</span>
                        <span class="text-heading fw-semibold" style="font-size: 13px;">{{ $recipe->station->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Output Hasil</span>
                        <span class="text-heading fw-bold text-success" style="font-size: 13px;">{{ number_format($recipe->quantity, 2, ',', '.') }} Porsi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Section for Items -->
    <div class="d-flex align-items-center mb-3">
        <div class="bg-primary rounded px-2 py-1 me-2">
            <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
        </div>
        <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Bahan-Bahan / Komposisi</h6>
    </div>

    <!-- Table Composition -->
    <div class="table-responsive rounded-4 overflow-hidden mb-4" style="border: 1px solid rgba(226, 232, 240, 0.2);">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
            <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                <tr class="text-muted" style="letter-spacing: 0.2px;">
                    <th width="5%" class="text-center py-3 ps-4 border-0">No</th>
                    <th width="20%" class="py-3 border-0">Kode Bahan</th>
                    <th width="40%" class="py-3 border-0">Nama Bahan Baku</th>
                    <th width="15%" class="text-end py-3 border-0">Jumlah</th>
                    <th width="20%" class="text-end py-3 pe-4 border-0">Cost Terhitung</th>
                </tr>
            </thead>
            <tbody class="border-top-0 text-heading">
                @php $totalCost = 0; @endphp
                @forelse($recipe->items as $idx => $item)
                    @php $totalCost += $item->cost; @endphp
                    <tr>
                        <td class="text-center py-3 ps-4">{{ $idx + 1 }}</td>
                        <td class="fw-mono text-muted py-3">{{ $item->product->code ?? '-' }}</td>
                        <td class="py-3 fw-medium">{{ $item->product->name ?? '-' }}</td>
                        <td class="text-end py-3 text-muted">{{ number_format($item->quantity, 4, ',', '.') }} {{ $item->unit->name ?? '' }}</td>
                        <td class="text-end py-3 pe-4 fw-semibold">Rp {{ number_format($item->cost, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted border-0">Tidak ada bahan baku yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <td colspan="4" class="py-2.5 text-end fw-bold">Total Estimasi Cost Resep</td>
                    <td class="py-2.5 pe-4 text-end fw-bold text-primary">Rp {{ number_format($totalCost, 2, ',', '.') }}</td>
                </tr>
                <tr style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <td colspan="4" class="py-2.5 text-end fw-bold">Estimasi Cost (HPP) Per Porsi</td>
                    @php $costPerPortion = $recipe->quantity > 0 ? $totalCost / $recipe->quantity : 0; @endphp
                    <td class="py-2.5 pe-4 text-end fw-bold text-success">Rp {{ number_format($costPerPortion, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Footer Actions -->
    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
    </div>
</x-modal>
