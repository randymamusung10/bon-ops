<x-modal id="addCategoryModal" title="Tambah Kategori Produk" description="Buat klasifikasi produk baru." size="lg">
    <form id="add-category-form">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label>Kode Kategori</x-form.label>
                <x-form.input name="code" id="add-code" placeholder="Otomatis (Auto Generated)" disabled />
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Kategori</x-form.label>
                <x-form.input name="name" id="add-name" placeholder="Misal: Bahan Baku, Kemasan" required />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Kategori Induk (Opsional)</x-form.label>
                <select name="parent_id" id="add-parent_id" class="form-select form-select-sm rounded-3">
                    <option value="">-- Tidak Memiliki Induk --</option>
                </select>
                <div class="invalid-feedback" id="add-parent_id-error"></div>
                <small class="text-muted" style="font-size: 11px;">Jika kosong, kategori akan menjadi level teratas.</small>
            </div>
            <div class="col-md-6">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="add-status" class="form-select form-select-sm rounded-3">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="add-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label>Deskripsi Kategori</x-form.label>
                <x-form.textarea name="description" id="add-description" rows="3" placeholder="Deskripsi singkat..."></x-form.textarea>
                <div class="invalid-feedback" id="add-description-error"></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">
                Simpan Kategori
            </x-button>
        </div>
    </form>
</x-modal>

<script>
    $('#add-parent_id').select2({
        dropdownParent: $('#addCategoryModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Tidak Memiliki Induk --',
        allowClear: true,
        ajax: {
            url: "{{ route('logistic.master.category.select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });
    $('#add-status').select2({
        dropdownParent: $('#addCategoryModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });
</script>
