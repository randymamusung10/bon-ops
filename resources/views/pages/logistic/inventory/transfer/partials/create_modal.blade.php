<x-modal id="createTransferModal" title="Buat Mutasi Stok" size="xl">
    <form id="form-create-transfer">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-form.label required>Tanggal</x-form.label>
                <x-form.input type="date" name="date" required value="{{ date('Y-m-d') }}" />
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <x-form.label required>Cabang Asal</x-form.label>
                <select class="form-select" name="source_branch_id" required>
                    <option value="">Pilih Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <x-form.label required>Gudang Asal</x-form.label>
                <select class="form-select" name="source_warehouse_id" required>
                    <option value="">Pilih Gudang</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-md-4 offset-md-4">
                <x-form.label required>Cabang Tujuan</x-form.label>
                <select class="form-select" name="destination_branch_id" required>
                    <option value="">Pilih Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <x-form.label required>Gudang Tujuan</x-form.label>
                <select class="form-select" name="destination_warehouse_id" required>
                    <option value="">Pilih Gudang</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Catatan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Tujuan mutasi (opsional)"></x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3 mt-2">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Daftar Item Mutasi</h6>
        </div>

        <div class="table-responsive rounded-4 overflow-hidden mb-3" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table table-hover align-middle mb-0" id="table-items" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="40%" class="py-3 ps-4 border-0">Produk</th>
                        <th width="20%" class="py-3 border-0">Qty Mutasi</th>
                        <th width="30%" class="py-3 border-0">Catatan Item</th>
                        <th width="10%" class="text-center py-3 pe-4 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 text-heading">
                    <!-- Items inserted here via JS -->
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
<template id="transfer-item-template">
    <tr>
        <td class="py-3 ps-4">
            <select class="form-select form-select-sm select2-product" name="items[__INDEX__][product_id]" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="py-3">
            <input type="number" class="form-control form-control-sm text-end" name="items[__INDEX__][qty]" step="0.01" min="0.01" required placeholder="0.00">
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm" name="items[__INDEX__][notes]" placeholder="Catatan (Opsional)">
        </td>
        <td class="text-center py-3 pe-4">
            <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>
