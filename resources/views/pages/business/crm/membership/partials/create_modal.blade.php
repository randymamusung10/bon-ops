<x-modal id="addMembershipModal" title="Tambah Membership" size="lg" description="Kelola detail membership pelanggan.">
    <form id="add-membership-form">
        @csrf
        <div class="mb-3">
            <x-form.label required>Nama Membership</x-form.label>
            <x-form.input name="name" id="add-name" placeholder="Contoh: Gold, Silver" required />
            <div class="invalid-feedback" id="add-name-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Minimum Spend (Total Belanja)</x-form.label>
            <x-form.input type="text" name="minimum_spend" id="add-minimum_spend" class="format-number" value="0" />
            <small class="text-muted" style="font-size: 11px;">Total akumulasi belanja untuk mencapai level ini.</small>
            <div class="invalid-feedback" id="add-minimum_spend-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Diskon (%)</x-form.label>
            <x-form.input type="text" name="discount_percentage" id="add-discount_percentage" class="format-number" value="0" />
            <div class="invalid-feedback" id="add-discount_percentage-error"></div>
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
