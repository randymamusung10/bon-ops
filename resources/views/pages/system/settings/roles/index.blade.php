@extends('layouts.app')

@section('page_title', 'Manajemen Role')
@section('page_description', 'Kelola hak akses (roles) untuk pengguna sistem.')
@section('page_actions')
    <x-button id="btn-add-role" variant="primary" size="sm" icon="bi-plus-lg">
        Tambah Role
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Data Table Card -->
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="roles-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3" style="width: 45%;">Nama Role</th>
                        <th class="py-3" style="width: 25%;">Total Permissions</th>
                        <th class="pe-4 text-end py-3" style="width: 25%;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px; color: var(--text-heading);">
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <div class="modern-loader-spinner" style="width: 36px; height: 36px;">
                                    <div class="spinner-outer" style="border-width: 2.5px;"></div>
                                    <div class="spinner-inner" style="border-width: 1.5px;"></div>
                                    <div class="spinner-dot" style="width: 5px; height: 5px;"></div>
                                </div>
                                <span class="fw-semibold text-muted" style="font-size: 12px; letter-spacing: 0.2px;">Memuat Data...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('modals')
<div id="modal-container"></div>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#btn-add-role').on('click', function() {
        ERPLoader.loadModal("{{ route('system.settings.roles.create') }}", '#addRoleModal', {
            title: 'Tambah Role',
            errorMessage: 'Gagal memuat form tambah role.',
            onSuccess: function(modal) {
                $('.select2-permissions').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });
            }
        });
    });

    var table = $('#roles-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('system.settings.roles.data') }}"
        },
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        columns: [
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                class: 'ps-4 text-muted',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'name', 
                name: 'name',
                class: 'fw-semibold text-heading' 
            },
            { 
                data: 'permissions_count',
                name: 'permissions_count',
                render: function(data) {
                    return '<span class="badge bg-primary-subtle text-primary px-2.5 py-1.5 rounded-pill">' + data + ' Permissions</span>';
                }
            },
            { 
                data: 'id',
                orderable: false,
                searchable: false,
                class: 'pe-4 text-end text-nowrap',
                render: function(data, type, row) {
                    return '<div class="d-inline-flex gap-2">' +
                        '<button class="btn-icon-modern text-info show-btn" data-id="' + data + '" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
                        '<i class="bi bi-eye"></i>' +
                        '</button>' +
                        '<button class="btn-icon-modern text-primary edit-btn" data-id="' + data + '" title="Edit" style="background: color-mix(in srgb, var(--primary-accent) 12%, transparent);">' +
                        '<i class="bi bi-pencil-square"></i>' +
                        '</button>' +
                        '<button class="btn-icon-modern text-danger delete-btn" data-id="' + data + '" data-name="' + row.name + '" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">' +
                        '<i class="bi bi-trash3"></i>' +
                        '</button>' +
                        '</div>';
                }
            }
        ],
        language: {
            processing: `
                <div class="d-flex flex-column align-items-center gap-2">
                    <div class="modern-loader-spinner" style="width: 36px; height: 36px;">
                        <div class="spinner-outer" style="border-width: 2.5px;"></div>
                        <div class="spinner-inner" style="border-width: 1.5px;"></div>
                        <div class="spinner-dot" style="width: 5px; height: 5px;"></div>
                    </div>
                    <span class="fw-semibold text-muted" style="font-size: 12px; letter-spacing: 0.2px;">Memuat Data...</span>
                </div>
            `,
            search: '_INPUT_',
            searchPlaceholder: 'Cari role...',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 data',
            lengthMenu: 'Tampilkan _MENU_ entri',
            paginate: { previous: 'Prev', next: 'Next' }
        }
    });

    table.on('processing.dt', function(e, settings, processing) {
        if (processing) {
            $('.dataTables_wrapper').addClass('is-processing');
        } else {
            $('.dataTables_wrapper').removeClass('is-processing');
        }
    });

    $(document).on('submit', '#add-role-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ route('system.settings.roles.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addRoleModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Data Tersimpan!', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    AppAlert.warning('Validasi Gagal', 'Harap periksa kembali input Anda.');
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        $('#add-' + key.replace('.', '-')).addClass('is-invalid');
                        $('#add-' + key.replace('.', '-') + '-error').html(val[0]);
                    });
                } else {
                    AppAlert.error('Error!', 'Terjadi kesalahan.');
                }
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    $(document).on('click', '.show-btn', function() {
        var id = $(this).data('id');
        var url = "{{ url('system/settings/roles') }}/" + id;
        ERPLoader.loadModal(url, '#showRoleModal', {
            title: 'Detail Role',
            errorMessage: 'Gagal mengambil detail role.'
        });
    });

    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var url = "{{ url('system/settings/roles') }}/" + id + "/edit";
        ERPLoader.loadModal(url, '#editRoleModal', {
            title: 'Edit Role',
            errorMessage: 'Gagal mengambil data role.',
            onSuccess: function(modal) {
                $('.select2-permissions').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });
            }
        });
    });

    $(document).on('submit', '#edit-role-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var id = $('#edit-id').val();
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ url('system/settings/roles') }}/" + id,
            type: "POST", // Spoofed PUT
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editRoleModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Data Diperbarui!', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    AppAlert.warning('Validasi Gagal', 'Harap periksa kembali input Anda.');
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        $('#edit-' + key.replace('.', '-')).addClass('is-invalid');
                        $('#edit-' + key.replace('.', '-') + '-error').html(val[0]);
                    });
                } else {
                    AppAlert.error('Error!', 'Terjadi kesalahan.');
                }
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        AppAlert.confirmDelete('Hapus Role?', 'Apakah Anda yakin ingin menghapus role "' + name + '"?').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('system/settings/roles') }}/" + id,
                    type: "DELETE",
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Data Dihapus!', response.message);
                        }
                    },
                    error: function() {
                        AppAlert.error('Gagal!', 'Gagal menghapus role.');
                    }
                });
            }
        });
    });
});
</script>
@endpush