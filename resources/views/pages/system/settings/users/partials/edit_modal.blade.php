<x-modal id="editUserModal" title="Edit User" size="lg">
    <form id="edit-user-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-id" name="id" value="{{ $user->id }}">
        
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label required>Nama Lengkap</x-form.label>
                <x-form.input type="text" name="name" id="edit-name" value="{{ $user->name }}" placeholder="Misal: John Doe" />
                <div class="invalid-feedback" id="edit-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Email</x-form.label>
                <x-form.input type="email" name="email" id="edit-email" value="{{ $user->email }}" placeholder="Misal: john@example.com" />
                <div class="invalid-feedback" id="edit-email-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Password Baru</x-form.label>
                <x-form.input type="password" name="password" id="edit-password" placeholder="Biarkan kosong jika tidak ingin mengubah" />
                <div class="invalid-feedback" id="edit-password-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Konfirmasi Password Baru</x-form.label>
                <x-form.input type="password" name="password_confirmation" id="edit-password_confirmation" placeholder="Ketik ulang password baru" />
            </div>
            <div class="col-md-6">
                <x-form.label>Perusahaan (Company)</x-form.label>
                <select class="select2-company" name="company_id" id="edit-company_id" data-placeholder="Pilih Perusahaan">
                    <option value="">Tanpa Perusahaan</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ $user->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="edit-company_id-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Roles</x-form.label>
                <select class="select2-roles" name="roles[]" multiple="multiple" id="edit-roles" data-placeholder="Pilih Roles">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="edit-roles-error"></div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>
