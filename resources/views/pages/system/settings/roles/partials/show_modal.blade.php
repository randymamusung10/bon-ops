<x-modal id="showRoleModal" title="Detail Role">
    <table class="table table-borderless table-sm mb-0">
        <tbody>
            <tr>
                <td class="text-muted" style="width: 130px; font-size: 13.5px;">ID</td>
                <td class="fw-semibold text-heading" style="font-size: 13.5px;">{{ $role->id }}</td>
            </tr>
            <tr>
                <td class="text-muted" style="font-size: 13.5px;">Nama Role</td>
                <td class="fw-semibold text-heading" style="font-size: 13.5px;">{{ $role->name }}</td>
            </tr>
            <tr>
                <td class="text-muted align-top" style="font-size: 13.5px; padding-top: 8px;">Permissions</td>
                <td style="padding-top: 6px;">
                    <div class="d-flex flex-wrap gap-1">
                        @forelse($role->permissions as $permission)
                            <span class="badge bg-primary-subtle text-primary px-2.5 py-1.5 rounded-pill" style="font-size: 11.5px; font-weight: 500;">
                                {{ $permission->name }}
                            </span>
                        @empty
                            <span class="text-muted fst-italic" style="font-size: 13px;">Belum ada permission</span>
                        @endforelse
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <x-slot name="footer">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
    </x-slot>
</x-modal>
