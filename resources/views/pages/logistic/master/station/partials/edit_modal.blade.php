<x-modal id="editStationModal" title="Edit Stasiun Produksi" description="Perbarui detail stasiun kerja produksi.">
    <form id="edit-station-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $station->uuid }}">
        
        <div class="mb-3">
            <x-form.label>Kode Stasiun</x-form.label>
            <x-form.input id="edit-code" value="{{ $station->code }}" disabled readonly />
        </div>
        
        <div class="mb-3">
            <x-form.label required>Nama Stasiun</x-form.label>
            <x-form.input name="name" id="edit-name" value="{{ $station->name }}" required placeholder="Contoh: Kitchen Utama, Barista Counter" />
            <div class="invalid-feedback" id="edit-name-error"></div>
        </div>
        
        <div class="mb-3">
            <x-form.label>Deskripsi</x-form.label>
            <x-form.textarea name="description" id="edit-description" rows="3" placeholder="Tuliskan keterangan mengenai stasiun kerja ini...">{{ $station->description }}</x-form.textarea>
            <div class="invalid-feedback" id="edit-description-error"></div>
        </div>
        
        <div class="mb-3">
            <x-form.label>Status</x-form.label>
            <select class="form-select" id="edit-status" name="status">
                <option value="active" {{ $station->status === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $station->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <div class="invalid-feedback" id="edit-status-error"></div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>
