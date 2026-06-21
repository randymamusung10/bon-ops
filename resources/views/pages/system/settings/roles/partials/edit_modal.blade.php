<x-modal id="editRoleModal" title="Edit Role">
    <form id="edit-role-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-id" name="id" value="{{ $role->id }}">
        
        <div class="mb-3">
            <x-form.label required>Nama Role</x-form.label>
            <x-form.input type="text" name="name" id="edit-name" value="{{ $role->name }}" placeholder="Misal: Manager" />
            <div class="invalid-feedback" id="edit-name-error"></div>
        </div>
        <div class="mb-3">
            <x-form.label>Permissions</x-form.label>
            <select class="select2-permissions" name="permissions[]" multiple="multiple" id="edit-permissions" data-placeholder="Pilih Permission">
                @foreach($permissions as $permission)
                    <option value="{{ $permission->name }}" {{ $role->hasPermissionTo($permission->name) ? 'selected' : '' }}>
                        {{ $permission->name }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="edit-permissions-error"></div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>
