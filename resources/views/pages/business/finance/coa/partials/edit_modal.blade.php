<x-modal id="editCoaModal" title="Edit Akun" description="Perbarui informasi Chart of Account." size="md">
    <form id="edit-coa-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $coa->uuid }}">
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label required>Kode Akun</x-form.label>
                <x-form.input name="code" id="edit-code" value="{{ $coa->code }}" required />
                <div class="invalid-feedback" id="edit-code-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Tipe Akun</x-form.label>
                <select name="account_type" id="edit-account_type" class="form-select form-select-sm" required>
                    <option value="asset" {{ $coa->account_type === 'asset' ? 'selected' : '' }}>Aset (Asset)</option>
                    <option value="liability" {{ $coa->account_type === 'liability' ? 'selected' : '' }}>Kewajiban (Liability)</option>
                    <option value="equity" {{ $coa->account_type === 'equity' ? 'selected' : '' }}>Ekuitas (Equity)</option>
                    <option value="revenue" {{ $coa->account_type === 'revenue' ? 'selected' : '' }}>Pendapatan (Revenue)</option>
                    <option value="expense" {{ $coa->account_type === 'expense' ? 'selected' : '' }}>Beban (Expense)</option>
                </select>
                <div class="invalid-feedback" id="edit-account_type-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Nama Akun</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $coa->name }}" required />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Status</x-form.label>
                <select name="status" id="edit-status" class="form-select form-select-sm">
                    <option value="active" {{ $coa->status === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ $coa->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="invalid-feedback" id="edit-status-error"></div>
            </div>
            <div class="col-12 mt-3 pt-3" style="border-top: 1px dashed var(--border-color);">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="edit-is_header" name="is_header" value="1" {{ $coa->is_header ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="edit-is_header">Jadikan Akun Induk (Header/Folder)</label>
                    <div class="form-text mt-0">Aktifkan jika akun ini hanya digunakan untuk mengelompokkan akun lain (tidak bisa dijurnal).</div>
                </div>
            </div>
            <div class="col-md-12" id="edit-parent-container" style="{{ $coa->is_header ? 'display: none;' : '' }}">
                <x-form.label>Pilih Induk Akun (Parent)</x-form.label>
                <select name="parent_id" id="edit-parent_id" class="form-select form-select-sm">
                    @if($coa->parent)
                        <option value="{{ $coa->parent->id }}" selected>[{{ $coa->parent->code }}] {{ $coa->parent->name }}</option>
                    @endif
                </select>
                <div class="form-text">Kosongkan jika ini adalah akun level teratas.</div>
                <div class="invalid-feedback" id="edit-parent_id-error"></div>
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
    $('#edit-status, #edit-account_type').select2({
        dropdownParent: $('#editCoaModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    $('#edit-parent_id').select2({
        dropdownParent: $('#editCoaModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Akun Induk...',
        allowClear: true,
        ajax: {
            url: "{{ route('business.finance.coa.select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var accountType = $('#edit-account_type').val();
                return { q: params.term, type: accountType, only_header: true };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });

    // Clear parent if account_type changes to prevent mismatched parents
    $('#edit-account_type').on('change', function() {
        $('#edit-parent_id').val(null).trigger('change');
    });

    // Toggle parent container
    $('#edit-is_header').on('change', function() {
        if($(this).is(':checked')) {
            $('#edit-parent-container').slideUp();
            $('#edit-parent_id').val(null).trigger('change');
        } else {
            $('#edit-parent-container').slideDown();
        }
    });
</script>
