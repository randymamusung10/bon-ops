<x-modal id="editCategoryModal" title="Edit Kategori Produk" description="Ubah data kategori di bawah ini." size="lg">
    <form id="edit-category-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $category->uuid }}">
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label>Kode Kategori</x-form.label>
                <x-form.input name="code" id="edit-code" value="{{ $category->code }}" readonly />
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Kategori</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $category->name }}" required />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Kategori Induk (Opsional)</x-form.label>
                <select name="parent_id" id="edit-parent_id" class="form-select form-select-sm rounded-3">
                    <option value="">-- Tidak Memiliki Induk --</option>
                    @if($category->parent_id && $category->parent)
                        <option value="{{ $category->parent_id }}" selected>[{{ $category->parent->code }}] {{ $category->parent->name }}</option>
                    @endif
                </select>
                <div class="invalid-feedback" id="edit-parent_id-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="edit-status" class="form-select form-select-sm rounded-3">
                    <option value="active" {{ $category->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $category->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="edit-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label>Deskripsi Kategori</x-form.label>
                <x-form.textarea name="description" id="edit-description" rows="3">{{ $category->description }}</x-form.textarea>
                <div class="invalid-feedback" id="edit-description-error"></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="warning" size="sm" icon="bi-check2">
                Simpan Perubahan
            </x-button>
        </div>
    </form>
</x-modal>

<script>
    $('#edit-parent_id').select2({
        dropdownParent: $('#editCategoryModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Tidak Memiliki Induk --',
        allowClear: true,
        ajax: {
            url: "{{ route('logistic.master.category.select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    q: params.term,
                    exclude_uuid: '{{ $category->uuid }}' // prevent category from selecting itself as parent
                };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });
    $('#edit-status').select2({
        dropdownParent: $('#editCategoryModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });
</script>
