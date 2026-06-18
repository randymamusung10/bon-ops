<x-modal id="editUnitModal" title="Edit Satuan Produk" description="Ubah data satuan di bawah ini." size="lg">
    <form id="edit-unit-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $unit->uuid }}">
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label>Kode Satuan</x-form.label>
                <x-form.input name="code" id="edit-code" value="{{ $unit->code }}" readonly />
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Satuan</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $unit->name }}" required />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-12">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="edit-status" class="form-select form-select-sm rounded-3">
                    <option value="active" {{ $unit->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $unit->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="edit-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label>Deskripsi Satuan</x-form.label>
                <x-form.textarea name="description" id="edit-description" rows="3">{{ $unit->description }}</x-form.textarea>
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
    $('#edit-status').select2({
        dropdownParent: $('#editUnitModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });
</script>
