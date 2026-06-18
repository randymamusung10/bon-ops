<x-modal id="editProductModal" title="Edit Produk" description="Perbarui informasi produk atau layanan." size="xl">
    <form id="edit-product-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-uuid" value="{{ $product->uuid }}">
        <div class="row g-4">
            <!-- Informasi Utama -->
            <div class="col-md-8">
                <div class="p-4 rounded-4" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                    <h6 class="mb-3 fw-bold" style="color: var(--text-heading);"><i class="bi bi-box-seam me-2 text-primary"></i>Informasi Utama</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-form.label required>Nama Produk</x-form.label>
                            <x-form.input name="name" id="edit-name" value="{{ $product->name }}" required />
                            <div class="invalid-feedback" id="edit-name-error"></div>
                        </div>
                        <div class="col-md-6">
                            <x-form.label>Kode Produk</x-form.label>
                            <x-form.input name="code" id="edit-code" value="{{ $product->code }}" disabled />
                        </div>
                        <div class="col-md-6">
                            <x-form.label required>Kategori</x-form.label>
                            <select name="product_category_id" id="edit-category" class="form-select form-select-sm" required>
                                @if($product->category)
                                    <option value="{{ $product->category->id }}" selected>
                                        [{{ $product->category->code }}] {{ $product->category->name }}
                                    </option>
                                @endif
                            </select>
                            <div class="invalid-feedback" id="edit-product_category_id-error"></div>
                        </div>
                        <div class="col-md-6">
                            <x-form.label required>Satuan</x-form.label>
                            <select name="unit_id" id="edit-unit" class="form-select form-select-sm" required>
                                @if($product->unit)
                                    <option value="{{ $product->unit->id }}" selected>
                                        [{{ $product->unit->code }}] {{ $product->unit->name }}
                                    </option>
                                @endif
                            </select>
                            <div class="invalid-feedback" id="edit-unit_id-error"></div>
                        </div>
                        <div class="col-md-6">
                            <x-form.label required>Tipe Produk</x-form.label>
                            <select name="type" id="edit-type" class="form-select form-select-sm">
                                <option value="finished_good" {{ $product->type == 'finished_good' ? 'selected' : '' }}>Barang Jadi (Finished Good)</option>
                                <option value="raw_material" {{ $product->type == 'raw_material' ? 'selected' : '' }}>Bahan Baku (Raw Material)</option>
                                <option value="service" {{ $product->type == 'service' ? 'selected' : '' }}>Jasa / Layanan</option>
                            </select>
                            <div class="invalid-feedback" id="edit-type-error"></div>
                        </div>
                        <div class="col-md-6">
                            <x-form.label required>Status</x-form.label>
                            <select name="status" id="edit-status" class="form-select form-select-sm">
                                <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            <div class="invalid-feedback" id="edit-status-error"></div>
                        </div>
                        <div class="col-12">
                            <x-form.label>Deskripsi</x-form.label>
                            <x-form.textarea name="description" id="edit-description" rows="2">{{ $product->description }}</x-form.textarea>
                            <div class="invalid-feedback" id="edit-description-error"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Harga & Biaya -->
            <div class="col-md-4">
                <div class="p-4 rounded-4" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color); height: 100%;">
                    <h6 class="mb-3 fw-bold" style="color: var(--text-heading);"><i class="bi bi-tag me-2 text-success"></i>Harga & Biaya</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <x-form.label>Harga Jual (Price)</x-form.label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted border-end-0">Rp</span>
                                <input type="text" class="form-control border-start-0 ps-0 text-end" name="price" id="edit-price" value="{{ number_format($product->price, 0, ',', '.') }}">
                            </div>
                            <div class="invalid-feedback" id="edit-price-error"></div>
                        </div>
                        <div class="col-12">
                            <x-form.label>Harga Modal (Cost)</x-form.label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted border-end-0">Rp</span>
                                <input type="text" class="form-control border-start-0 ps-0 text-end" name="cost" id="edit-cost" value="{{ number_format($product->cost, 0, ',', '.') }}">
                            </div>
                            <div class="invalid-feedback" id="edit-cost-error"></div>
                        </div>
                        <div class="col-12 mt-4 pt-3" style="border-top: 1px dashed var(--border-color);">
                            <h6 class="mb-3 fw-bold" style="color: var(--text-heading); font-size: 13px;"><i class="bi bi-percent me-2 text-warning"></i>Pajak Default</h6>
                            <x-form.label>Pajak</x-form.label>
                            <select name="tax_id" id="edit-tax" class="form-select form-select-sm">
                                @if($product->tax)
                                    <option value="{{ $product->tax->id }}" selected>[{{ $product->tax->code }}] {{ $product->tax->name }}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback" id="edit-tax_id-error"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pemetaan Akun (COA) -->
            <div class="col-12">
                <div class="p-4 rounded-4" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                    <h6 class="mb-3 fw-bold" style="color: var(--text-heading);"><i class="bi bi-wallet2 me-2 text-info"></i>Pemetaan Akun (GL)</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <x-form.label>Akun Persediaan (Asset)</x-form.label>
                            <select name="inventory_account_id" id="edit-inventory-account" class="form-select form-select-sm">
                                @if($product->inventoryAccount)
                                    <option value="{{ $product->inventoryAccount->id }}" selected>[{{ $product->inventoryAccount->code }}] {{ $product->inventoryAccount->name }}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback" id="edit-inventory_account_id-error"></div>
                        </div>
                        <div class="col-md-4">
                            <x-form.label>Akun HPP (COGS)</x-form.label>
                            <select name="cogs_account_id" id="edit-cogs-account" class="form-select form-select-sm">
                                @if($product->cogsAccount)
                                    <option value="{{ $product->cogsAccount->id }}" selected>[{{ $product->cogsAccount->code }}] {{ $product->cogsAccount->name }}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback" id="edit-cogs_account_id-error"></div>
                        </div>
                        <div class="col-md-4">
                            <x-form.label>Akun Pendapatan (Revenue)</x-form.label>
                            <select name="income_account_id" id="edit-income-account" class="form-select form-select-sm">
                                @if($product->incomeAccount)
                                    <option value="{{ $product->incomeAccount->id }}" selected>[{{ $product->incomeAccount->code }}] {{ $product->incomeAccount->name }}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback" id="edit-income_account_id-error"></div>
                        </div>
                    </div>
                </div>
            </div>
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

<script>
    $('#edit-type, #edit-status').select2({
        dropdownParent: $('#editProductModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    // Format mata uang (ribuan)
    $('#edit-price, #edit-cost').on('keyup', function() {
        var value = $(this).val().replace(/[^,\d]/g, '');
        var split = value.split(',');
        var sisa = split[0].length % 3;
        var rupiah = split[0].substr(0, sisa);
        var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if(ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        $(this).val(rupiah);
    });

    // Select2 Kategori
    $('#edit-category').select2({
        dropdownParent: $('#editProductModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Kategori...',
        ajax: {
            url: "{{ route('logistic.master.category.select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });

    // Select2 Satuan
    $('#edit-unit').select2({
        dropdownParent: $('#editProductModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Satuan...',
        ajax: {
            url: "{{ route('logistic.master.unit.select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });

    // Select2 Pajak
    $('#edit-tax').select2({
        dropdownParent: $('#editProductModal'),
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Pajak...',
        allowClear: true,
        ajax: {
            url: "{{ route('business.finance.tax.select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });

    function initCoaSelect2(selector, placeholder, typeFilter) {
        $(selector).select2({
            dropdownParent: $('#editProductModal'),
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholder,
            allowClear: true,
            ajax: {
                url: "{{ route('business.finance.coa.select2') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, type: typeFilter, only_detail: true };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        });
    }

    initCoaSelect2('#edit-inventory-account', 'Pilih Akun Persediaan...', 'asset');
    initCoaSelect2('#edit-cogs-account', 'Pilih Akun HPP...', 'expense');
    initCoaSelect2('#edit-income-account', 'Pilih Akun Pendapatan...', 'revenue');
</script>
