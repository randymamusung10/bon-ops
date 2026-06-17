<!-- Modal Edit Cabang -->
<x-modal id="editBranchModal" title="Edit Data Cabang" description="Perbarui detail cabang di bawah ini.">
    <form id="edit-branch-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $branch->uuid }}">
        <div class="mb-3">
            <x-form.label required>Kode Cabang</x-form.label>
            <x-form.input name="code" id="edit-code" value="{{ $branch->code }}" placeholder="Misal: JKT-02" required />
            <div class="invalid-feedback" id="edit-code-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label required>Nama Cabang</x-form.label>
            <x-form.input name="name" id="edit-name" value="{{ $branch->name }}" placeholder="Masukkan nama cabang" required />
            <div class="invalid-feedback" id="edit-name-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Kota / Wilayah</x-form.label>
            <x-form.input name="city" id="edit-city" value="{{ $branch->city }}" placeholder="Contoh: Jakarta Selatan" />
            <div class="invalid-feedback" id="edit-city-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Status Cabang</x-form.label>
            <select name="status" id="edit-status" class="form-select form-select-sm rounded-3">
                <option value="active" {{ $branch->status == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $branch->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <div class="invalid-feedback" id="edit-status-error"></div>
        </div>
        <div class="mb-4">
            <x-form.label>Alamat Lengkap</x-form.label>
            <x-form.textarea name="address" id="edit-address" rows="3" placeholder="Detail alamat...">{{ $branch->address }}</x-form.textarea>
            <div class="invalid-feedback" id="edit-address-error"></div>
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
