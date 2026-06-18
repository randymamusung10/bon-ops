<x-modal id="addCurrencyModal" title="Tambah Mata Uang" description="Masukkan data mata uang baru." size="md">
    <form id="add-currency-form">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label required>Kode Mata Uang</x-form.label>
                <x-form.input name="code" id="add-code" placeholder="Cth: IDR" required />
                <div class="invalid-feedback" id="add-code-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Mata Uang</x-form.label>
                <x-form.input name="name" id="add-name" placeholder="Cth: Rupiah" required />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Simbol</x-form.label>
                <x-form.input name="symbol" id="add-symbol" placeholder="Cth: Rp" />
                <div class="invalid-feedback" id="add-symbol-error"></div>
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
                <x-form.label required>Nilai Tukar (Exchange Rate)</x-form.label>
                <x-form.input name="exchange_rate" id="add-exchange_rate" placeholder="Cth: 1" value="1" required />
                <div class="form-text">Nilai konversi terhadap mata uang utama perusahaan.</div>
                <div class="invalid-feedback" id="add-exchange_rate-error"></div>
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
        dropdownParent: $('#addCurrencyModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    // Format nilai tukar
    $('#add-exchange_rate').on('keyup', function() {
        var val = $(this).val();
        // Regex untuk mengizinkan angka dan satu buah titik/koma desimal
        var clean = val.replace(/[^0-9.,]/g, '');
        $(this).val(clean);
    });
</script>
