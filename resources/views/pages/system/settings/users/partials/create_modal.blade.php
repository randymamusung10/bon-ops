<x-modal id="addUserModal" title="Tambah User" size="lg">
    <form id="add-user-form">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <x-form.label required>Nama Lengkap</x-form.label>
                <x-form.input type="text" name="name" id="add-name" placeholder="Misal: John Doe" />
                <div class="invalid-feedback" id="add-name-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Email</x-form.label>
                <x-form.input type="email" name="email" id="add-email" placeholder="Misal: john@example.com" />
                <div class="invalid-feedback" id="add-email-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Password</x-form.label>
                <x-form.input type="password" name="password" id="add-password" placeholder="Minimal 8 karakter" />
                <div class="invalid-feedback" id="add-password-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Konfirmasi Password</x-form.label>
                <x-form.input type="password" name="password_confirmation" id="add-password_confirmation" placeholder="Ketik ulang password" />
            </div>
            <div class="col-md-6">
                <x-form.label>Perusahaan (Company)</x-form.label>
                <select class="select2-company" name="company_id" id="add-company_id" data-placeholder="Pilih Perusahaan">
                    <option value="">Tanpa Perusahaan</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="add-company_id-error"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Roles</x-form.label>
                <select class="select2-roles" name="roles[]" multiple="multiple" id="add-roles" data-placeholder="Pilih Roles">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="add-roles-error"></div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan User</x-button>
        </div>
    </form>
</x-modal>
