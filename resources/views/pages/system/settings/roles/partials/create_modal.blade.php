<x-modal id="addRoleModal" title="Tambah Role">
    <form id="add-role-form">
        @csrf
        <div class="mb-3">
            <x-form.label required>Nama Role</x-form.label>
            <x-form.input type="text" name="name" id="add-name" placeholder="Misal: Manager" />
            <div class="invalid-feedback" id="add-name-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Permissions</x-form.label>
            <select class="select2-permissions" name="permissions[]" multiple="multiple" id="add-permissions" data-placeholder="Pilih Permission">
                @foreach($permissions as $permission)
                    <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="add-permissions-error"></div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Role</x-button>
        </div>
    </form>
</x-modal>
