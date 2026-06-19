<x-modal id="editAdjustmentModal" title="Edit Penyesuaian Stok" description="Ubah detail penyesuaian stok atau stok opname." size="xl">
    <form id="edit-adjustment-form" data-uuid="{{ $adjustment->uuid }}">
        @csrf
        @method('PUT')
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-form.label required>Tanggal</x-form.label>
                <x-form.input type="date" name="date" id="edit-date" value="{{ \Carbon\Carbon::parse($adjustment->date)->format('Y-m-d') }}" required />
                <div class="invalid-feedback" id="edit-date-error"></div>
            </div>
            <div class="col-md-4">
                <x-form.label required>Cabang</x-form.label>
                <select class="form-select" id="edit-branch_id" name="branch_id" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $adjustment->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="edit-branch_id-error"></div>
            </div>
            <div class="col-md-4">
                <x-form.label required>Gudang</x-form.label>
                <select class="form-select" id="edit-warehouse_id" name="warehouse_id" required>
                    <option value="">-- Pilih Gudang --</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ $adjustment->warehouse_id == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="edit-warehouse_id-error"></div>
            </div>
            <div class="col-12">
                <x-form.label>Catatan</x-form.label>
                <x-form.textarea name="notes" id="edit-notes" rows="2" placeholder="Alasan penyesuaian (opsional)">{{ $adjustment->notes }}</x-form.textarea>
                <div class="invalid-feedback" id="edit-notes-error"></div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3 mt-2">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Daftar Item Penyesuaian</h6>
        </div>
        <div class="table-responsive rounded-4 overflow-hidden mb-3" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table table-hover align-middle mb-0" id="items-table" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="40%" class="py-3 ps-4 border-0">Produk</th>
                        <th width="20%" class="py-3 border-0">Qty Fisik (Actual)</th>
                        <th width="30%" class="py-3 border-0">Alasan Selisih</th>
                        <th width="10%" class="text-center py-3 pe-4 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 text-heading">
                    @foreach($adjustment->items as $index => $item)
                    <tr>
                        <td class="py-3 ps-4">
                            <select class="form-select form-select-sm product-select" name="items[{{ $index }}][product_id]" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" {{ $item->product_id == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-3">
                            <input type="text" class="form-control form-control-sm format-number" name="items[{{ $index }}][actual_qty]" value="{{ number_format($item->actual_qty, 0, ',', '.') }}" required placeholder="0">
                        </td>
                        <td class="py-3">
                            <input type="text" class="form-control form-control-sm" name="items[{{ $index }}][reason]" value="{{ $item->reason }}" placeholder="Alasan (Opsional)">
                        </td>
                        <td class="text-center py-3 pe-4">
                            <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <x-button type="button" variant="ghost-primary" size="sm" id="btn-add-item-edit" icon="bi-plus">Tambah Item</x-button>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>

<!-- Hidden Template for new row -->
<template id="item-row-template-edit">
    <tr>
        <td class="py-3 ps-4">
            <select class="form-select form-select-sm product-select" name="items[__INDEX__][product_id]" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $prod)
                    <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm format-number" name="items[__INDEX__][actual_qty]" required placeholder="0">
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm" name="items[__INDEX__][reason]" placeholder="Alasan (Opsional)">
        </td>
        <td class="text-center py-3 pe-4">
            <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>
