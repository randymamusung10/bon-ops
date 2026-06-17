<!-- Modal Tambah Cabang -->
<x-modal id="addBranchModal" title="Tambah Cabang Baru" description="Masukkan detail cabang untuk didaftarkan ke sistem.">
    <form id="add-branch-form">
        @csrf
        <div class="mb-3">
            <x-form.label required>Kode Cabang</x-form.label>
            <x-form.input name="code" id="add-code" placeholder="Misal: JKT-02" required />
            <div class="invalid-feedback" id="add-code-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label required>Nama Cabang</x-form.label>
            <x-form.input name="name" id="add-name" placeholder="Masukkan nama cabang" required />
            <div class="invalid-feedback" id="add-name-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Kota / Wilayah</x-form.label>
            <x-form.input name="city" id="add-city" placeholder="Contoh: Jakarta Selatan" />
            <div class="invalid-feedback" id="add-city-error"></div>
        </div>
        <div class="mb-4">
            <x-form.label>Alamat Lengkap</x-form.label>
            <x-form.textarea name="address" id="add-address" rows="3" placeholder="Detail alamat..."></x-form.textarea>
            <div class="invalid-feedback" id="add-address-error"></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">
                Simpan Cabang
            </x-button>
        </div>
    </form>
</x-modal>
