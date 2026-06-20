<x-modal id="addRecipeModal" title="Tambah Resep Baru" size="lg" description="Tentukan menu target beserta komposisi bahan baku penyusunnya.">
    <form id="add-recipe-form">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label required>Produk Target (Menu/Jadi)</x-form.label>
                <select class="form-select select2-modal" id="add-product_id" name="product_id" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="add-product_id-error"></div>
            </div>
            <div class="col-md-6 mb-3">
                <x-form.label>Stasiun Produksi</x-form.label>
                <select class="form-select select2-modal" id="add-production_station_id" name="production_station_id">
                    <option value="">Pilih Stasiun (Opsional)</option>
                    @foreach($stations as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="add-production_station_id-error"></div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8 mb-3">
                <x-form.label required>Nama Resep</x-form.label>
                <x-form.input name="name" id="add-name" required placeholder="Contoh: Resep Kopi Susu Aren Standard" />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
            <div class="col-md-4 mb-3">
                <x-form.label required>Output Hasil (Porsi)</x-form.label>
                <x-form.input name="quantity" id="add-quantity" class="text-end format-number" value="1" required />
                <div class="invalid-feedback" id="add-quantity-error"></div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label required>Status</x-form.label>
                <select class="form-select select2-modal" id="add-status" name="status">
                    <option value="draft">Draft</option>
                    <option value="active">Aktif (Gunakan)</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="add-status-error"></div>
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" style="color: var(--text-heading); font-family: 'Outfit', sans-serif;">Bahan-Bahan / Komposisi</h6>
            <x-button type="button" variant="ghost-primary" size="sm" id="btn-add-row" icon="bi-plus">Tambah Baris</x-button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="recipe-items-table">
                <thead>
                    <tr style="font-size: 12px; color: var(--text-muted);">
                        <th style="width: 50%;">Bahan Baku / Ingredient <span class="text-danger">*</span></th>
                        <th style="width: 25%;" class="text-end">Jumlah Penggunaan <span class="text-danger">*</span></th>
                        <th style="width: 20%;">Satuan <span class="text-danger">*</span></th>
                        <th style="width: 5%;" class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dinamis ditambahkan via JS -->
                </tbody>
            </table>
        </div>
        <div class="invalid-feedback d-block" id="add-items-error"></div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Resep</x-button>
        </div>
    </form>
</x-modal>
