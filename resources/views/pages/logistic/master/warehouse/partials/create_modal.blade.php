<x-modal id="addWarehouseModal" title="Tambah Gudang Baru" description="Masukkan detail gudang untuk didaftarkan ke sistem." size="lg">
    <form id="add-warehouse-form">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label>Kode Gudang</x-form.label>
                <x-form.input name="code" id="add-code" placeholder="Otomatis (Auto Generated)" disabled />
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Gudang</x-form.label>
                <x-form.input name="name" id="add-name" placeholder="Gudang Utama" required />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Cabang Terkait</x-form.label>
                <select name="branch_id" id="add-branch_id" class="form-select form-select-sm rounded-3">
                    <option value="">-- Tidak Terikat Cabang --</option>
                </select>
                <div class="invalid-feedback" id="add-branch_id-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Kota / Wilayah</x-form.label>
                <x-form.input name="city" id="add-city" placeholder="Contoh: Jakarta Barat" />
                <div class="invalid-feedback" id="add-city-error"></div>
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
                <x-form.label>Alamat Lengkap Gudang</x-form.label>
                <x-form.textarea name="address" id="add-address" rows="3" placeholder="Detail alamat gudang..."></x-form.textarea>
                <div class="invalid-feedback" id="add-address-error"></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">
                Simpan Gudang
            </x-button>
        </div>
    </form>
</x-modal>

<script>
    $('#add-branch_id').select2({
        dropdownParent: $('#addWarehouseModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Tidak Terikat Cabang --',
        allowClear: true,
        ajax: {
            url: "{{ route('logistic.master.branch.select2') }}",
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
        dropdownParent: $('#addWarehouseModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });
</script>
