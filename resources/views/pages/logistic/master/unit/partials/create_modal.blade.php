<x-modal id="addUnitModal" title="Tambah Satuan Produk" description="Buat satuan/UOM produk baru." size="lg">
    <form id="add-unit-form">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label>Kode Satuan</x-form.label>
                <x-form.input name="code" id="add-code" placeholder="Otomatis (Auto Generated)" disabled />
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Satuan</x-form.label>
                <x-form.input name="name" id="add-name" placeholder="Misal: Kg, Liter, Box" required />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
            <div class="col-md-12">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="add-status" class="form-select form-select-sm rounded-3">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="add-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label>Deskripsi Satuan</x-form.label>
                <x-form.textarea name="description" id="add-description" rows="3" placeholder="Deskripsi singkat..."></x-form.textarea>
                <div class="invalid-feedback" id="add-description-error"></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">
                Simpan Satuan
            </x-button>
        </div>
    </form>
</x-modal>

<script>
    $('#add-status').select2({
        dropdownParent: $('#addUnitModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });
</script>
