<x-modal id="editCustomerModal" title="Edit Data Pelanggan" description="Perbarui detail pelanggan di bawah ini." size="lg">
    <form id="edit-customer-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $customer->uuid }}">
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label>Kode Pelanggan</x-form.label>
                <x-form.input name="code" id="edit-code" value="{{ $customer->code }}" readonly />
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Pelanggan</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $customer->name }}" required />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Email</x-form.label>
                <x-form.input type="email" name="email" id="edit-email" value="{{ $customer->email }}" />
                <div class="invalid-feedback" id="edit-email-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Telepon</x-form.label>
                <x-form.input name="phone" id="edit-phone" value="{{ $customer->phone }}" />
                <div class="invalid-feedback" id="edit-phone-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="edit-status" class="form-select form-select-sm rounded-3">
                    <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $customer->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="edit-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label>Alamat Lengkap</x-form.label>
                <x-form.textarea name="address" id="edit-address" rows="2">{{ $customer->address }}</x-form.textarea>
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
