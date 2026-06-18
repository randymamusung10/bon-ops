<x-modal id="showProductModal" title="Detail Produk" description="Informasi lengkap mengenai produk." size="xl">
    
    <!-- Header / Title Section -->
    <div class="d-flex align-items-center mb-4 p-3 rounded-4" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1);">
        <div class="d-flex justify-content-center align-items-center rounded-circle" style="width: 60px; height: 60px; background: color-mix(in srgb, var(--primary-accent) 15%, transparent); color: var(--primary-accent); font-size: 24px;">
            <i class="bi bi-box-seam"></i>
        </div>
        <div class="ms-3">
            <h5 class="mb-1 fw-bold" style="color: var(--text-heading);">{{ $product->name }}</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="bi bi-upc-scan me-1"></i>{{ $product->code }}</span>
                @if($product->status === 'active')
                    <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                @else
                    <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                @endif
                <span class="badge bg-info-subtle text-info px-2 py-1">
                    @if($product->type == 'finished_good') Barang Jadi
                    @elseif($product->type == 'raw_material') Bahan Baku
                    @else Jasa
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Data Grid -->
    <div class="row g-4">
        <!-- Kolom Kiri: Detail -->
        <div class="col-md-7">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="p-3 rounded-3" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                        <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500; letter-spacing: 0.2px;">
                            <i class="bi bi-tags me-1"></i> Kategori
                        </div>
                        <div class="fw-semibold text-heading" style="font-size: 14px;">
                            {{ $product->category ? $product->category->name : '-' }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 rounded-3" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                        <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500; letter-spacing: 0.2px;">
                            <i class="bi bi-rulers me-1"></i> Satuan Dasar
                        </div>
                        <div class="fw-semibold text-heading" style="font-size: 14px;">
                            {{ $product->unit ? $product->unit->name : '-' }}
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 rounded-3" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                        <div class="text-muted mb-1" style="font-size: 13px; font-weight: 500; letter-spacing: 0.2px;">
                            <i class="bi bi-card-text me-1"></i> Deskripsi Produk
                        </div>
                        <div class="fw-medium text-heading" style="font-size: 14px; line-height: 1.5;">
                            {{ $product->description ?: 'Tidak ada deskripsi' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Harga -->
        <div class="col-md-5">
            <div class="p-4 rounded-4 text-center h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.15)); border: 1px solid rgba(16, 185, 129, 0.2);">
                <div class="mb-4">
                    <div class="text-success fw-bold mb-1" style="font-size: 13px; letter-spacing: 0.5px; text-transform: uppercase;">
                        Harga Jual (Price)
                    </div>
                    <div class="fw-bolder" style="font-size: 28px; color: var(--text-heading);">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                </div>
                <hr style="border-color: rgba(16, 185, 129, 0.2);">
                <div class="mt-4">
                    <div class="text-muted fw-bold mb-1" style="font-size: 13px; letter-spacing: 0.5px; text-transform: uppercase;">
                        Harga Modal (Cost)
                    </div>
                    <div class="fw-bold" style="font-size: 20px; color: var(--text-muted);">
                        Rp {{ number_format($product->cost, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Harga -->
    <div class="mt-4 pt-4" style="border-top: 1px dashed var(--border-color);">
        <h6 class="mb-3 fw-bold" style="color: var(--text-heading);"><i class="bi bi-clock-history me-2 text-warning"></i>Riwayat Perubahan Harga</h6>
        
        @if($product->priceHistories->count() > 0)
            <div class="table-responsive rounded-3" style="border: 1px solid var(--border-color);">
                <table class="table align-middle mb-0" style="font-size: 13px;">
                    <thead style="background: var(--bg-dark-secondary);">
                        <tr style="color: var(--text-muted);">
                            <th class="py-2 ps-3" style="width: 20%;">Tanggal</th>
                            <th class="py-2" style="width: 20%;">Pengguna</th>
                            <th class="py-2 text-end" style="width: 20%;">Harga Jual (Baru)</th>
                            <th class="py-2 text-end" style="width: 20%;">Harga Modal (Baru)</th>
                            <th class="py-2 pe-3" style="width: 20%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->priceHistories as $history)
                            <tr>
                                <td class="ps-3 text-heading">{{ $history->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                        <i class="bi bi-person me-1"></i>{{ $history->creator ? $history->creator->name : 'Sistem' }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-success">
                                    Rp {{ number_format($history->new_price, 0, ',', '.') }}
                                    @if($history->new_price > $history->old_price)
                                        <i class="bi bi-arrow-up-right text-success ms-1" style="font-size: 11px;"></i>
                                    @elseif($history->new_price < $history->old_price)
                                        <i class="bi bi-arrow-down-right text-danger ms-1" style="font-size: 11px;"></i>
                                    @endif
                                </td>
                                <td class="text-end fw-medium text-muted">
                                    Rp {{ number_format($history->new_cost, 0, ',', '.') }}
                                    @if($history->new_cost > $history->old_cost)
                                        <i class="bi bi-arrow-up-right text-danger ms-1" style="font-size: 11px;"></i>
                                    @elseif($history->new_cost < $history->old_cost)
                                        <i class="bi bi-arrow-down-right text-success ms-1" style="font-size: 11px;"></i>
                                    @endif
                                </td>
                                <td class="pe-3 text-muted" style="font-size: 12px;">{{ $history->reason }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-light text-center py-3 mb-0" style="border: 1px dashed var(--border-color); background: var(--bg-dark-secondary);">
                <i class="bi bi-info-circle text-muted mb-2 d-block" style="font-size: 20px;"></i>
                <span class="text-muted" style="font-size: 13px;">Belum ada riwayat perubahan harga untuk produk ini.</span>
            </div>
        @endif
    </div>

    <div class="d-flex justify-content-end mt-4 pt-3" style="border-top: 1px dashed var(--border-color);">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
            Tutup
        </x-button>
    </div>
</x-modal>
