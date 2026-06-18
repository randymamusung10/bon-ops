<x-modal id="addSupplierModal" title="Tambah Pemasok Baru" description="Masukkan detail pemasok (supplier) untuk didaftarkan ke sistem." size="lg">
    <form id="add-supplier-form">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label>Kode Pemasok</x-form.label>
                <x-form.input name="code" id="add-code" placeholder="Otomatis (Auto Generated)" disabled />
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Pemasok</x-form.label>
                <x-form.input name="name" id="add-name" placeholder="PT Contoh Supplier" required />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Nama Kontak (PIC)</x-form.label>
                <x-form.input name="contact_person_name" id="add-contact_person_name" placeholder="Nama orang yang dapat dihubungi" />
                <div class="invalid-feedback" id="add-contact_person_name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Telepon Kontak (PIC)</x-form.label>
                <x-form.input name="contact_person_phone" id="add-contact_person_phone" placeholder="0812..." />
                <div class="invalid-feedback" id="add-contact_person_phone-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Email Perusahaan</x-form.label>
                <x-form.input type="email" name="email" id="add-email" placeholder="email@contoh.com" />
                <div class="invalid-feedback" id="add-email-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Telepon Perusahaan</x-form.label>
                <x-form.input name="phone" id="add-phone" placeholder="021-..." />
                <div class="invalid-feedback" id="add-phone-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Kota</x-form.label>
                <x-form.input name="city" id="add-city" placeholder="Contoh: Jakarta" />
                <div class="invalid-feedback" id="add-city-error"></div>
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
                <x-form.label>Alamat Lengkap</x-form.label>
                <x-form.textarea name="address" id="add-address" rows="2" placeholder="Detail alamat..."></x-form.textarea>
                <div class="invalid-feedback" id="add-address-error"></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">
                Simpan Pemasok
            </x-button>
        </div>
    </form>
</x-modal>

<script>
    $('#add-status').select2({
        dropdownParent: $('#addSupplierModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });
</script>
