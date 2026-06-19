<x-modal id="addCompanyModal" title="Tambah Perusahaan" description="Masukkan detail perusahaan untuk didaftarkan ke sistem.">
    <form id="add-company-form">
        @csrf
        <div class="mb-3">
            <x-form.label required>Nama Perusahaan</x-form.label>
            <x-form.input name="name" id="add-name" placeholder="Contoh: PT. Maju Bersama" required />
            <div class="invalid-feedback" id="add-name-error"></div>
        </div>
        <div class="mb-4">
            <x-form.label required>Status</x-form.label>
            <select class="form-select" id="add-status" name="status">
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
            <div class="invalid-feedback" id="add-status-error"></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Perusahaan</x-button>
        </div>
    </form>
</x-modal>
