<x-modal id="addTaxModal" title="Tambah Pajak" description="Masukkan data persentase pajak baru." size="md">
    <form id="add-tax-form">
        @csrf
        <div class="row g-3">
            <div class="col-md-12">
                <x-form.label required>Nama Pajak</x-form.label>
                <x-form.input name="name" id="add-name" placeholder="Cth: PPN 11%" required />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Kode Pajak</x-form.label>
                <x-form.input name="code" id="add-code" placeholder="Cth: TAX-PPN" required />
                <div class="invalid-feedback" id="add-code-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="add-status" class="form-select form-select-sm">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="add-status-error"></div>
            </div>
            <div class="col-12">
                <x-form.label required>Persentase (%)</x-form.label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control text-end" name="rate_percentage" id="add-rate_percentage" placeholder="0" value="0" required>
                    <span class="input-group-text bg-light text-muted border-start-0">%</span>
                </div>
                <div class="form-text">Contoh: 11 untuk 11%, atau 2.5 untuk 2,5%.</div>
                <div class="invalid-feedback" id="add-rate_percentage-error"></div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px dashed var(--border-color);">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">
                Simpan
            </x-button>
        </div>
    </form>
</x-modal>

<script>
    $('#add-status').select2({
        dropdownParent: $('#addTaxModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    // Format nilai
    $('#add-rate_percentage').on('keyup', function() {
        var val = $(this).val();
        var clean = val.replace(/[^0-9.,]/g, '');
        $(this).val(clean);
    });
</script>
