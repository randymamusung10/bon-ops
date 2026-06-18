<x-modal id="editSupplierModal" title="Edit Data Pemasok" description="Perbarui detail pemasok di bawah ini." size="lg">
    <form id="edit-supplier-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $supplier->uuid }}">
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label>Kode Pemasok</x-form.label>
                <x-form.input name="code" id="edit-code" value="{{ $supplier->code }}" readonly />
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Pemasok</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $supplier->name }}" required />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Nama Kontak (PIC)</x-form.label>
                <x-form.input name="contact_person_name" id="edit-contact_person_name" value="{{ $supplier->contact_person_name }}" />
                <div class="invalid-feedback" id="edit-contact_person_name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Telepon Kontak (PIC)</x-form.label>
                <x-form.input name="contact_person_phone" id="edit-contact_person_phone" value="{{ $supplier->contact_person_phone }}" />
                <div class="invalid-feedback" id="edit-contact_person_phone-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Email Perusahaan</x-form.label>
                <x-form.input type="email" name="email" id="edit-email" value="{{ $supplier->email }}" />
                <div class="invalid-feedback" id="edit-email-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Telepon Perusahaan</x-form.label>
                <x-form.input name="phone" id="edit-phone" value="{{ $supplier->phone }}" />
                <div class="invalid-feedback" id="edit-phone-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Kota</x-form.label>
                <x-form.input name="city" id="edit-city" value="{{ $supplier->city }}" />
                <div class="invalid-feedback" id="edit-city-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="edit-status" class="form-select form-select-sm rounded-3">
                    <option value="active" {{ $supplier->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $supplier->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="edit-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label>Alamat Lengkap</x-form.label>
                <x-form.textarea name="address" id="edit-address" rows="2">{{ $supplier->address }}</x-form.textarea>
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
    $('#edit-status').select2({
        dropdownParent: $('#editSupplierModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });
</script>
