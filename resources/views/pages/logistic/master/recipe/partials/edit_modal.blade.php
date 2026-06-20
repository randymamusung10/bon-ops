<x-modal id="editRecipeModal" title="Edit Resep Produk" size="lg" description="Perbarui detail resep dan daftar bahan baku produk.">
    <form id="edit-recipe-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $recipe->uuid }}">
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label required>Produk Target (Menu/Jadi)</x-form.label>
                <select class="form-select select2-modal-edit" id="edit-product_id" name="product_id" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ $recipe->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->code }})</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="edit-product_id-error"></div>
            </div>
            <div class="col-md-6 mb-3">
                <x-form.label>Stasiun Produksi</x-form.label>
                <select class="form-select select2-modal-edit" id="edit-production_station_id" name="production_station_id">
                    <option value="">Pilih Stasiun (Opsional)</option>
                    @foreach($stations as $s)
                        <option value="{{ $s->id }}" {{ $recipe->production_station_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="edit-production_station_id-error"></div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8 mb-3">
                <x-form.label required>Nama Resep</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $recipe->name }}" required placeholder="Contoh: Resep Kopi Susu Aren Standard" />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-4 mb-3">
                <x-form.label required>Output Hasil (Porsi)</x-form.label>
                <x-form.input name="quantity" id="edit-quantity" class="text-end format-number" value="{{ number_format($recipe->quantity, 2, ',', '.') }}" required />
                <div class="invalid-feedback" id="edit-quantity-error"></div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label required>Status</x-form.label>
                <select class="form-select select2-modal-edit" id="edit-status" name="status">
                    <option value="draft" {{ $recipe->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ $recipe->status === 'active' ? 'selected' : '' }}>Aktif (Gunakan)</option>
                    <option value="inactive" {{ $recipe->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="edit-status-error"></div>
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" style="color: var(--text-heading); font-family: 'Outfit', sans-serif;">Bahan-Bahan / Komposisi</h6>
            <x-button type="button" variant="ghost-primary" size="sm" id="btn-edit-add-row" icon="bi-plus">Tambah Baris</x-button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="edit-recipe-items-table">
                <thead>
                    <tr style="font-size: 12px; color: var(--text-muted);">
                        <th style="width: 50%;">Bahan Baku / Ingredient <span class="text-danger">*</span></th>
                        <th style="width: 25%;" class="text-end">Jumlah Penggunaan <span class="text-danger">*</span></th>
                        <th style="width: 20%;">Satuan <span class="text-danger">*</span></th>
                        <th style="width: 5%;" class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recipe->items as $index => $item)
                        <tr class="edit-row-item" id="edit-row-{{ $index }}">
                            <td>
                                <select class="form-select select2-modal-edit" name="items[{{ $index }}][product_id]" required style="width: 100%;">
                                    <option value="">Pilih Bahan Baku</option>
                                    @foreach($ingredients as $ing)
                                        <option value="{{ $ing->id }}" {{ $item->product_id == $ing->id ? 'selected' : '' }} data-unit-id="{{ $ing->unit_id }}">{{ $ing->name }} ({{ $ing->code }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <x-form.input name="items[{{ $index }}][quantity]" class="text-end format-number" value="{{ number_format($item->quantity, 2, ',', '.') }}" placeholder="0" required />
                            </td>
                            <td>
                                <select class="form-select select2-modal-edit" name="items[{{ $index }}][unit_id]" required style="width: 100%;">
                                    <option value="">Satuan</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}" {{ $item->unit_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-icon-modern text-danger btn-remove-edit-row mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="invalid-feedback d-block" id="edit-items-error"></div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>
