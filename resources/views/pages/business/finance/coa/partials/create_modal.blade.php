<x-modal id="addCoaModal" title="Tambah Akun" description="Masukkan data Chart of Account baru." size="md">
    <form id="add-coa-form">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label required>Kode Akun</x-form.label>
                <x-form.input name="code" id="add-code" placeholder="Cth: 1-1000" required />
                <div class="invalid-feedback" id="add-code-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Tipe Akun</x-form.label>
                <select name="account_type" id="add-account_type" class="form-select form-select-sm" required>
                    <option value="">Pilih Tipe</option>
                    <option value="asset">Aset (Asset)</option>
                    <option value="liability">Kewajiban (Liability)</option>
                    <option value="equity">Ekuitas (Equity)</option>
                    <option value="revenue">Pendapatan (Revenue)</option>
                    <option value="expense">Beban (Expense)</option>
                </select>
                <div class="invalid-feedback" id="add-account_type-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Akun</x-form.label>
                <x-form.input name="name" id="add-name" placeholder="Cth: Kas Kecil" required />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="add-status" class="form-select form-select-sm">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="add-status-error"></div>
            </div>
            <div class="col-12 mt-3 pt-3" style="border-top: 1px dashed var(--border-color);">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="add-is_header" name="is_header" value="1">
                    <label class="form-check-label fw-bold" for="add-is_header">Jadikan Akun Induk (Header/Folder)</label>
                    <div class="form-text mt-0">Aktifkan jika akun ini hanya digunakan untuk mengelompokkan akun lain (tidak bisa dijurnal).</div>
                </div>
            </div>
            <div class="col-md-12" id="add-parent-container">
                <x-form.label>Pilih Induk Akun (Parent)</x-form.label>
                <select name="parent_id" id="add-parent_id" class="form-select form-select-sm"></select>
                <div class="form-text">Kosongkan jika ini adalah akun level teratas.</div>
                <div class="invalid-feedback" id="add-parent_id-error"></div>
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
    $('#add-status, #add-account_type').select2({
        dropdownParent: $('#addCoaModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    $('#add-parent_id').select2({
        dropdownParent: $('#addCoaModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Akun Induk...',
        allowClear: true,
        ajax: {
            url: "{{ route('business.finance.coa.select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var accountType = $('#add-account_type').val();
                return { q: params.term, type: accountType, only_header: true };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });

    // Clear parent if account_type changes to prevent mismatched parents
    $('#add-account_type').on('change', function() {
        $('#add-parent_id').val(null).trigger('change');
    });

    // Toggle parent container
    $('#add-is_header').on('change', function() {
        if($(this).is(':checked')) {
            $('#add-parent-container').slideUp();
            $('#add-parent_id').val(null).trigger('change');
        } else {
            $('#add-parent-container').slideDown();
        }
    });
</script>
