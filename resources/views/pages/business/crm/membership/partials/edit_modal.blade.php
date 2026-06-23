<x-modal id="editMembershipModal" title="Edit Membership" size="lg" description="Perbarui detail membership pelanggan.">
    <form id="edit-membership-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-id" value="{{ $membership->id }}">
        
        <div class="mb-3">
            <x-form.label required>Nama Membership</x-form.label>
            <x-form.input name="name" id="edit-name" value="{{ $membership->name }}" required />
            <div class="invalid-feedback" id="edit-name-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Minimum Spend (Total Belanja)</x-form.label>
            <x-form.input type="text" name="minimum_spend" id="edit-minimum_spend" class="format-number" value="{{ number_format($membership->minimum_spend, 0, '', '') }}" />
            <div class="invalid-feedback" id="edit-minimum_spend-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Diskon (%)</x-form.label>
            <x-form.input type="text" name="discount_percentage" id="edit-discount_percentage" class="format-number" value="{{ number_format($membership->discount_percentage, 0, '', '') }}" />
            <div class="invalid-feedback" id="edit-discount_percentage-error"></div>
        </div>
        <div class="mb-4">
            <x-form.label required>Status</x-form.label>
            <x-form.select name="status" id="edit-status" required>
                <option value="active" {{ $membership->status == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $membership->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
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
