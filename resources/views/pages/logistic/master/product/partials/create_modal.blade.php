<x-modal id="addProductModal" title="Tambah Produk" description="Tambahkan data barang atau layanan baru." size="xl">
    <form id="add-product-form">
        @csrf
        <div class="row g-4">
            <!-- Informasi Utama -->
            <div class="col-md-8">
                <div class="p-4 rounded-4" style="background: var(--bg-dark-secondary); border: 1px solid var(--border-color);">
                    <h6 class="mb-3 fw-bold" style="color: var(--text-heading);"><i class="bi bi-box-seam me-2 text-primary"></i>Informasi Utama</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-form.label required>Nama Produk</x-form.label>
                            <x-form.input name="name" id="add-name" placeholder="Cth: Meja Kayu Jati" required />
                            <div class="invalid-feedback" id="add-name-error"></div>
                        </div>
                        <div class="col-md-6">
                            <x-form.label>Kode Produk</x-form.label>
                            <x-form.input name="code" id="add-code" placeholder="Otomatis" disabled />
                        </div>
                        <div class="col-md-6">
                            <x-form.label required>Kategori</x-form.label>
                            <select name="product_category_id" id="add-category" class="form-select form-select-sm" required></select>
                            <div class="invalid-feedback" id="add-product_category_id-error"></div>
                        </div>
                        <div class="col-md-6">
                            <x-form.label required>Satuan</x-form.label>
                            <select name="unit_id" id="add-unit" class="form-select form-select-sm" required></select>
                            <div class="invalid-feedback" id="add-unit_id-error"></div>
                        </div>
                        <div class="col-md-6">
                            <x-form.label required>Tipe Produk</x-form.label>
                            <select name="type" id="add-type" class="form-select form-select-sm">
                                <option value="finished_good">Barang Jadi (Finished Good)</option>
                                <option value="raw_material">Bahan Baku (Raw Material)</option>
                                <option value="service">Jasa / Layanan</option>
                            </select>
                            <div class="invalid-feedback" id="add-type-error"></div>
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
                            <x-form.label>Deskripsi</x-form.label>
                            <x-form.textarea name="description" id="add-description" rows="2" placeholder="Detail spesifikasi produk..."></x-form.textarea>
                            <div class="invalid-feedback" id="add-description-error"></div>
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
                                <input type="text" class="form-control border-start-0 ps-0 text-end" name="price" id="add-price" placeholder="0" value="0">
                            </div>
                            <div class="invalid-feedback" id="add-price-error"></div>
                        </div>
                        <div class="col-12">
                            <x-form.label>Harga Modal (Cost)</x-form.label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted border-end-0">Rp</span>
                                <input type="text" class="form-control border-start-0 ps-0 text-end" name="cost" id="add-cost" placeholder="0" value="0">
                            </div>
                            <div class="invalid-feedback" id="add-cost-error"></div>
                        </div>
                        <div class="col-12 mt-4 pt-3" style="border-top: 1px dashed var(--border-color);">
                            <h6 class="mb-3 fw-bold" style="color: var(--text-heading); font-size: 13px;"><i class="bi bi-percent me-2 text-warning"></i>Pajak Default</h6>
                            <x-form.label>Pajak</x-form.label>
                            <select name="tax_id" id="add-tax" class="form-select form-select-sm"></select>
                            <div class="invalid-feedback" id="add-tax_id-error"></div>
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
                            <select name="inventory_account_id" id="add-inventory-account" class="form-select form-select-sm"></select>
                            <div class="invalid-feedback" id="add-inventory_account_id-error"></div>
                        </div>
                        <div class="col-md-4">
                            <x-form.label>Akun HPP (COGS)</x-form.label>
                            <select name="cogs_account_id" id="add-cogs-account" class="form-select form-select-sm"></select>
                            <div class="invalid-feedback" id="add-cogs_account_id-error"></div>
                        </div>
                        <div class="col-md-4">
                            <x-form.label>Akun Pendapatan (Revenue)</x-form.label>
                            <select name="income_account_id" id="add-income-account" class="form-select form-select-sm"></select>
                            <div class="invalid-feedback" id="add-income_account_id-error"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">
                Simpan Produk
            </x-button>
        </div>
    </form>
</x-modal>

<script>
    $('#add-type, #add-status').select2({
        dropdownParent: $('#addProductModal'),
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    // Format mata uang (ribuan)
    $('#add-price, #add-cost').on('keyup', function() {
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
    $('#add-category').select2({
        dropdownParent: $('#addProductModal'),
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
    $('#add-unit').select2({
        dropdownParent: $('#addProductModal'),
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
    $('#add-tax').select2({
        dropdownParent: $('#addProductModal'),
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
            dropdownParent: $('#addProductModal'),
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

    initCoaSelect2('#add-inventory-account', 'Pilih Akun Persediaan...', 'asset');
    initCoaSelect2('#add-cogs-account', 'Pilih Akun HPP...', 'expense');
    initCoaSelect2('#add-income-account', 'Pilih Akun Pendapatan...', 'revenue');
</script>
