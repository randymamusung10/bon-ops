<x-modal id="showUserModal" title="Detail User">
    <table class="table table-borderless table-sm mb-0">
        <tbody>
            <tr>
                <td class="text-muted" style="width: 130px;">ID</td>
                <td class="fw-semibold">{{ $user->id }}</td>
            </tr>
            <tr>
                <td class="text-muted">Nama Lengkap</td>
                <td class="fw-semibold">{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="text-muted">Email</td>
                <td class="fw-semibold">{{ $user->email }}</td>
            </tr>
            <tr>
                <td class="text-muted">Perusahaan</td>
                <td class="fw-semibold">{{ $user->company ? $user->company->name : '-' }}</td>
            </tr>
            <tr>
                <td class="text-muted align-top">Roles</td>
                <td>
                    @forelse($user->roles as $role)
                        <span class="badge bg-success-subtle text-success px-2 py-1 mb-1 me-1">{{ $role->name }}</span>
                    @empty
                        <span class="text-muted fst-italic">Belum ada role</span>
                    @endforelse
                </td>
            </tr>
        </tbody>
    </table>

    <x-slot name="footer">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
    </x-slot>
</x-modal>
