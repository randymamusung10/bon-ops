<x-modal id="editTaxModal" title="Edit Pajak" description="Perbarui informasi persentase pajak." size="md">
    <form id="edit-tax-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $tax->uuid }}">
        <div class="row g-3">
            <div class="col-md-12">
                <x-form.label required>Nama Pajak</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $tax->name }}" required />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Kode Pajak</x-form.label>
                <x-form.input name="code" id="edit-code" value="{{ $tax->code }}" required />
                <div class="invalid-feedback" id="edit-code-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="edit-status" class="form-select form-select-sm">
                    <option value="active" {{ $tax->status === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $tax->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="edit-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label required>Persentase (%)</x-form.label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control text-end" name="rate_percentage" id="edit-rate_percentage" value="{{ (float)$tax->rate_percentage }}" required>
                    <span class="input-group-text bg-light text-muted border-start-0">%</span>
                </div>
                <div class="form-text">Contoh: 11 untuk 11%, atau 2.5 untuk 2,5%.</div>
                <div class="invalid-feedback" id="edit-rate_percentage-error"></div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px dashed var(--border-color);">
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
        dropdownParent: $('#editTaxModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    // Format nilai
    $('#edit-rate_percentage').on('keyup', function() {
        var val = $(this).val();
        var clean = val.replace(/[^0-9.,]/g, '');
        $(this).val(clean);
    });
</script>
