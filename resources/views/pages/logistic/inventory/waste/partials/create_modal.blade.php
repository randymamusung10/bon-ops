<x-modal id="addWasteModal" title="Buat Stock Waste" description="Masukkan detail pembuangan stok (waste) produk/bahan baku." size="xl">
    <form id="add-waste-form">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-form.label required>Tanggal</x-form.label>
                <x-form.input type="date" name="date" id="add-date" value="{{ date('Y-m-d') }}" required />
                <div class="invalid-feedback" id="add-date-error"></div>
            </div>
            <div class="col-md-4">
                <x-form.label required>Cabang</x-form.label>
                <select class="form-select" id="add-branch_id" name="branch_id" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="add-branch_id-error"></div>
            </div>
            <div class="col-md-4">
                <x-form.label required>Gudang</x-form.label>
                <select class="form-select" id="add-warehouse_id" name="warehouse_id" required>
                    <option value="">-- Pilih Gudang --</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="add-warehouse_id-error"></div>
            </div>
            <div class="col-12">
                <x-form.label>Catatan</x-form.label>
                <x-form.textarea name="notes" id="add-notes" rows="2" placeholder="Catatan umum mengenai pembuangan stok ini"></x-form.textarea>
                <div class="invalid-feedback" id="add-notes-error"></div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3 mt-2">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Daftar Item Pembuangan</h6>
        </div>

        <div class="table-responsive rounded-4 overflow-hidden mb-3" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table table-hover align-middle mb-0" id="items-table" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="40%" class="py-3 ps-4 border-0">Produk</th>
                        <th width="20%" class="py-3 border-0">Qty Waste</th>
                        <th width="30%" class="py-3 border-0">Alasan Pembuangan</th>
                        <th width="10%" class="text-center py-3 pe-4 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 text-heading">
                    <!-- Rows will be added here via JS -->
                </tbody>
            </table>
        </div>
        <div>
            <x-button type="button" variant="ghost-primary" size="sm" id="btn-add-item" icon="bi-plus">Tambah Item</x-button>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Draft</x-button>
        </div>
    </form>
</x-modal>

<!-- Hidden Template for new row -->
<template id="item-row-template">
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
            <input type="text" class="form-control form-control-sm format-number" name="items[__INDEX__][qty]" required placeholder="0">
        </td>
        <td class="py-3">
            <select class="form-select form-select-sm" name="items[__INDEX__][reason]" required>
                <option value="Expired">Expired (Kedaluwarsa)</option>
                <option value="Spoiled">Spoiled (Busuk/Basi)</option>
                <option value="Damaged">Damaged (Rusak/Pecah)</option>
                <option value="Sample">Sample (Uji Coba/Trial)</option>
                <option value="Other">Lainnya</option>
            </select>
        </td>
        <td class="text-center py-3 pe-4">
            <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>
