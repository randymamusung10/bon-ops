<x-modal id="editWarehouseModal" title="Edit Data Gudang" description="Perbarui detail gudang di bawah ini." size="lg">
    <form id="edit-warehouse-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $warehouse->uuid }}">
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label>Kode Gudang</x-form.label>
                <x-form.input name="code" id="edit-code" value="{{ $warehouse->code }}" readonly />
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Gudang</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $warehouse->name }}" required />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Cabang Terkait</x-form.label>
                <select name="branch_id" id="edit-branch_id" class="form-select form-select-sm rounded-3">
                    <option value="">-- Tidak Terikat Cabang --</option>
                    @if($warehouse->branch_id && $warehouse->branch)
                        <option value="{{ $warehouse->branch_id }}" selected>[{{ $warehouse->branch->code }}] {{ $warehouse->branch->name }}</option>
                    @endif
                </select>
                <div class="invalid-feedback" id="edit-branch_id-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Kota / Wilayah</x-form.label>
                <x-form.input name="city" id="edit-city" value="{{ $warehouse->city }}" />
                <div class="invalid-feedback" id="edit-city-error"></div>
            </div>
            <div class="col-md-12">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="edit-status" class="form-select form-select-sm rounded-3">
                    <option value="active" {{ $warehouse->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $warehouse->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="edit-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label>Alamat Lengkap Gudang</x-form.label>
                <x-form.textarea name="address" id="edit-address" rows="3">{{ $warehouse->address }}</x-form.textarea>
                <div class="invalid-feedback" id="edit-address-error"></div>
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
    $('#edit-branch_id').select2({
        dropdownParent: $('#editWarehouseModal'),
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
    $('#edit-status').select2({
        dropdownParent: $('#editWarehouseModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });
</script>
