<x-modal id="editCurrencyModal" title="Edit Mata Uang" description="Perbarui informasi mata uang." size="md">
    <form id="edit-currency-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $currency->uuid }}">
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label required>Kode Mata Uang</x-form.label>
                <x-form.input name="code" id="edit-code" value="{{ $currency->code }}" required />
                <div class="invalid-feedback" id="edit-code-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Mata Uang</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $currency->name }}" required />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Simbol</x-form.label>
                <x-form.input name="symbol" id="edit-symbol" value="{{ $currency->symbol }}" />
                <div class="invalid-feedback" id="edit-symbol-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="edit-status" class="form-select form-select-sm">
                    <option value="active" {{ $currency->status === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $currency->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="edit-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label required>Nilai Tukar (Exchange Rate)</x-form.label>
                <x-form.input name="exchange_rate" id="edit-exchange_rate" value="{{ (float)$currency->exchange_rate }}" required />
                <div class="form-text">Nilai konversi terhadap mata uang utama perusahaan.</div>
                <div class="invalid-feedback" id="edit-exchange_rate-error"></div>
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
        dropdownParent: $('#editCurrencyModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    // Format nilai tukar
    $('#edit-exchange_rate').on('keyup', function() {
        var val = $(this).val();
        var clean = val.replace(/[^0-9.,]/g, '');
        $(this).val(clean);
    });
</script>
