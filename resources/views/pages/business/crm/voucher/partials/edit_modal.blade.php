<x-modal id="editVoucherModal" title="Edit Voucher" size="lg" description="Perbarui detail voucher promo pelanggan.">
    <form id="edit-voucher-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-id" value="{{ $voucher->id }}">
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label required>Kode Voucher</x-form.label>
                <x-form.input name="code" id="edit-code" class="text-uppercase" value="{{ $voucher->code }}" required />
                <div class="invalid-feedback" id="edit-code-error"></div>
            </div>
            <div class="col-md-6 mb-3">
                <x-form.label required>Nama Voucher</x-form.label>
                <x-form.input name="name" id="edit-name" value="{{ $voucher->name }}" required />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label required>Tipe Potongan</x-form.label>
                <x-form.select name="type" id="edit-type" required onchange="toggleValueLabel(this, '#edit-value-label', '#edit-max-discount-container')">
                    <option value="nominal" {{ $voucher->type == 'nominal' ? 'selected' : '' }}>Nominal Rupiah</option>
                    <option value="percentage" {{ $voucher->type == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                </x-form.select>
                <div class="invalid-feedback" id="edit-type-error"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="edit-value" id="edit-value-label" class="form-label" style="font-size: 13px; font-weight: 500;">
                    {!! $voucher->type == 'percentage' ? 'Nilai Persentase (%) <span class="text-danger">*</span>' : 'Nilai Rupiah (Rp) <span class="text-danger">*</span>' !!}
                </label>
                <x-form.input type="text" name="value" id="edit-value" class="format-number" required value="{{ $voucher->type == 'percentage' ? $voucher->value : number_format($voucher->value, 0, '', '') }}" />
                <div class="invalid-feedback" id="edit-value-error"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.label>Minimum Transaksi</x-form.label>
                <x-form.input type="text" name="minimum_spend" id="edit-minimum_spend" class="format-number" value="{{ number_format($voucher->minimum_spend, 0, '', '') }}" />
                <div class="invalid-feedback" id="edit-minimum_spend-error"></div>
            </div>
            <div class="col-md-6 mb-3" id="edit-max-discount-container" style="{{ $voucher->type == 'percentage' ? '' : 'display: none;' }}">
                <x-form.label>Maksimal Diskon (Rupiah)</x-form.label>
                <x-form.input type="text" name="maximum_discount" id="edit-maximum_discount" class="format-number" value="{{ number_format($voucher->maximum_discount, 0, '', '') }}" />
                <div class="invalid-feedback" id="edit-maximum_discount-error"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <x-form.label>Batas Kuota</x-form.label>
                <x-form.input type="number" name="quota" id="edit-quota" min="1" value="{{ $voucher->quota }}" placeholder="Kosong = Unlimited" />
                <div class="invalid-feedback" id="edit-quota-error"></div>
            </div>
            <div class="col-md-4 mb-3">
                <x-form.label>Berlaku Dari</x-form.label>
                <x-form.input type="date" name="valid_from" id="edit-valid_from" value="{{ $voucher->valid_from }}" />
                <div class="invalid-feedback" id="edit-valid_from-error"></div>
            </div>
            <div class="col-md-4 mb-3">
                <x-form.label>Berlaku Sampai</x-form.label>
                <x-form.input type="date" name="valid_until" id="edit-valid_until" value="{{ $voucher->valid_until }}" />
                <div class="invalid-feedback" id="edit-valid_until-error"></div>
            </div>
        </div>

        <div class="mb-4">
            <x-form.label required>Status</x-form.label>
            <x-form.select name="status" id="edit-status" required>
                <option value="active" {{ $voucher->status == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $voucher->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </x-form.select>
            <div class="invalid-feedback" id="edit-status-error"></div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">
                Perbarui
            </x-button>
        </div>
    </form>
</x-modal>
