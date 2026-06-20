@extends('layouts.app')

@section('page_title', 'Stasiun Produksi')
@section('page_description', 'Kelola daftar stasiun kerja produksi (misal: Kitchen, Bar, dll) untuk tenant Anda.')
@section('page_actions')
    <x-button id="btn-add-station" variant="primary" size="sm" icon="bi-plus-lg">
        Tambah Stasiun
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Data Table Card -->
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="stations-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3" style="width: 20%;">Kode</th>
                        <th class="py-3" style="width: 30%;">Nama Stasiun</th>
                        <th class="py-3" style="width: 25%;">Status</th>
                        <th class="pe-4 text-end py-3" style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px; color: var(--text-heading);">
                    <tr>
                        <td colspan="5" class="text-center py-5">
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

    $('#btn-add-station').on('click', function() {
        ERPLoader.loadModal("{{ route('logistic.master.station.create') }}", '#addStationModal', {
            title: 'Tambah Stasiun Produksi',
            errorMessage: 'Gagal memuat form tambah stasiun.',
            onSuccess: function(modal) {
                $('#add-status').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%',
                    minimumResultsForSearch: -1
                });
            }
        });
    });

    var table = $('#stations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('logistic.master.station.data') }}"
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
                data: 'code', 
                name: 'code',
                class: 'fw-mono text-heading'
            },
            { 
                data: 'name', 
                name: 'name',
                class: 'fw-semibold text-heading' 
            },
            { 
                data: 'status',
                name: 'status',
                render: function(data) {
                    if (data === 'active') {
                        return '<span class="badge bg-success-subtle text-success px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="bi bi-check-circle me-1"></i> Aktif</span>';
                    } else {
                        return '<span class="badge bg-danger-subtle text-danger px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="bi bi-x-circle me-1"></i> Nonaktif</span>';
                    }
                }
            },
            { 
                data: 'uuid',
                orderable: false,
                searchable: false,
                class: 'pe-4 text-end text-nowrap',
                render: function(data, type, row) {
                    return '<div class="d-inline-flex gap-2">' +
                        '<button class="btn-icon-modern text-primary edit-btn" data-uuid="' + data + '" title="Edit" style="background: color-mix(in srgb, var(--primary-accent) 12%, transparent);">' +
                        '<i class="bi bi-pencil-square"></i>' +
                        '</button>' +
                        '<button class="btn-icon-modern text-danger delete-btn" data-uuid="' + data + '" data-name="' + row.name + '" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">' +
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
            searchPlaceholder: 'Cari secara global...',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 data',
            lengthMenu: 'Tampilkan _MENU_ entri',
            paginate: { previous: 'Prev', next: 'Next' }
        }
    });

    $('.dataTables_length select').select2({ theme: 'bootstrap-5', width: '75px', minimumResultsForSearch: -1 });

    // Store Station
    $(document).on('submit', '#add-station-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ route('logistic.master.station.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addStationModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Data Tersimpan!', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        $('#add-' + key).addClass('is-invalid');
                        $('#add-' + key + '-error').html(val[0]);
                    });
                } else {
                    AppAlert.error('Error!', 'Terjadi kesalahan saat menyimpan data.');
                }
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    // Edit Station Modal Load
    $(document).on('click', '.edit-btn', function() {
        var uuid = $(this).data('uuid');
        var url = "{{ url('logistic/master/station') }}/" + uuid + "/edit";
        ERPLoader.loadModal(url, '#editStationModal', {
            title: 'Edit Stasiun Produksi',
            errorMessage: 'Gagal mengambil data stasiun produksi.',
            onSuccess: function(modal) {
                $('#edit-status').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%',
                    minimumResultsForSearch: -1
                });
            }
        });
    });

    // Update Station
    $(document).on('submit', '#edit-station-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var uuid = $('#edit-uuid').val();
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ url('logistic/master/station') }}/" + uuid,
            type: "POST", // method spoofing
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editStationModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Data Diperbarui!', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        $('#edit-' + key).addClass('is-invalid');
                        $('#edit-' + key + '-error').html(val[0]);
                    });
                } else {
                    AppAlert.error('Error!', 'Terjadi kesalahan saat memperbarui data.');
                }
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    // Delete Station
    $(document).on('click', '.delete-btn', function() {
        var uuid = $(this).data('uuid');
        var name = $(this).data('name');
        
        AppAlert.confirmDelete('Hapus Stasiun Produksi?', 'Apakah Anda yakin ingin menghapus stasiun "' + name + '"?').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('logistic/master/station') }}/" + uuid,
                    type: "DELETE",
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Data Dihapus!', response.message);
                        }
                    },
                    error: function() {
                        AppAlert.error('Gagal!', 'Gagal menghapus stasiun produksi.');
                    }
                });
            }
        });
    });
});
</script>
@endpush