<x-modal id="editCompanyModal" title="Edit Perusahaan" description="Ubah informasi detail perusahaan.">
    <form id="edit-company-form">
        @method('PUT')
        @csrf
        <input type="hidden" id="edit-uuid" value="{{ $company->uuid }}">
        <div class="mb-3">
            <x-form.label required>Nama Perusahaan</x-form.label>
            <x-form.input name="name" id="edit-name" value="{{ $company->name }}" required />
            <div class="invalid-feedback" id="edit-name-error"></div>
        </div>
        <div class="mb-4">
            <x-form.label required>Status</x-form.label>
            <select class="form-select" id="edit-status" name="status">
                <option value="active" {{ $company->status === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $company->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <div class="invalid-feedback" id="edit-status-error"></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-save">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>
