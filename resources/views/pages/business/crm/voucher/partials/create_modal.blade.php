<x-modal id="addVoucherModal" title="Tambah Voucher" size="lg" description="Buat voucher promo baru untuk pelanggan.">
    <form id="add-voucher-form">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label required>Kode Voucher</x-form.label>
                <x-form.input name="code" id="add-code" class="text-uppercase" placeholder="Contoh: PROMO2026" required />
                <div class="invalid-feedback" id="add-code-error"></div>
            </div>
            <div class="col-md-6 mb-3">
                <x-form.label required>Nama Voucher</x-form.label>
                <x-form.input name="name" id="add-name" placeholder="Contoh: Promo Akhir Tahun" required />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label required>Tipe Potongan</x-form.label>
                <x-form.select name="type" id="add-type" required onchange="toggleValueLabel(this, '#add-value-label', '#add-max-discount-container')">
                    <option value="nominal">Nominal Rupiah</option>
                    <option value="percentage">Persentase (%)</option>
                </x-form.select>
                <div class="invalid-feedback" id="add-type-error"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="add-value" id="add-value-label" class="form-label" style="font-size: 13px; font-weight: 500;">Nilai Rupiah (Rp) <span class="text-danger">*</span></label>
                <x-form.input type="text" name="value" id="add-value" class="format-number" required />
                <div class="invalid-feedback" id="add-value-error"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label>Minimum Transaksi</x-form.label>
                <x-form.input type="text" name="minimum_spend" id="add-minimum_spend" class="format-number" value="0" />
                <div class="invalid-feedback" id="add-minimum_spend-error"></div>
            </div>
            <div class="col-md-6 mb-3" id="add-max-discount-container" style="display: none;">
                <x-form.label>Maksimal Diskon (Rupiah)</x-form.label>
                <x-form.input type="text" name="maximum_discount" id="add-maximum_discount" class="format-number" />
                <div class="invalid-feedback" id="add-maximum_discount-error"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <x-form.label>Batas Kuota</x-form.label>
                <x-form.input type="number" name="quota" id="add-quota" min="1" placeholder="Kosong = Unlimited" />
                <div class="invalid-feedback" id="add-quota-error"></div>
            </div>
            <div class="col-md-4 mb-3">
                <x-form.label>Berlaku Dari</x-form.label>
                <x-form.input type="date" name="valid_from" id="add-valid_from" />
                <div class="invalid-feedback" id="add-valid_from-error"></div>
            </div>
            <div class="col-md-4 mb-3">
                <x-form.label>Berlaku Sampai</x-form.label>
                <x-form.input type="date" name="valid_until" id="add-valid_until" />
                <div class="invalid-feedback" id="add-valid_until-error"></div>
            </div>
        </div>

        <div class="mb-4">
            <x-form.label required>Status</x-form.label>
            <x-form.select name="status" id="add-status" required>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </x-form.select>
            <div class="invalid-feedback" id="add-status-error"></div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">
                Simpan
            </x-button>
        </div>
    </form>
</x-modal>
