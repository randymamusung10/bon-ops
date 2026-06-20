<x-modal id="addStationModal" title="Tambah Stasiun Produksi" description="Masukkan detail stasiun kerja produksi baru.">
    <form id="add-station-form">
        @csrf
        <div class="mb-3">
            <x-form.label required>Nama Stasiun</x-form.label>
            <x-form.input name="name" id="add-name" required placeholder="Contoh: Kitchen Utama, Barista Counter" />
            <div class="invalid-feedback" id="add-name-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Deskripsi</x-form.label>
            <x-form.textarea name="description" id="add-description" rows="3" placeholder="Tuliskan keterangan mengenai stasiun kerja ini..."></x-form.textarea>
            <div class="invalid-feedback" id="add-description-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Status</x-form.label>
            <select class="form-select" id="add-status" name="status">
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
            <div class="invalid-feedback" id="add-status-error"></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan</x-button>
        </div>
    </form>
</x-modal>
