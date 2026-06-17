@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-7">
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0" style="font-size: 12px; font-weight: 500;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Master Data</a></li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-accent);">Cabang</li>
                </ol>
            </nav>
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">Manajemen Cabang</h1>
            <p class="mb-0" style="color: var(--text-light); font-size: 13.5px;">Kelola daftar cabang atau outlet untuk tenant Anda.</p>
        </div>
        <div class="col-12 col-md-5 text-md-end mt-3 mt-md-0 d-flex flex-wrap justify-content-md-end gap-2">
            <x-button id="btn-export-excel" variant="ghost-success" size="sm" icon="bi-file-earmark-excel">
                Excel
            </x-button>
            <x-button id="btn-export-pdf" variant="ghost-danger" size="sm" icon="bi-file-earmark-pdf">
                PDF
            </x-button>
            <x-button id="btn-add-branch" variant="primary" size="sm" icon="bi-plus-lg">
                Tambah Cabang
            </x-button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="branches-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3" style="width: 10%;">Kode</th>
                        <th class="py-3" style="width: 20%;">Nama Cabang</th>
                        <th class="py-3" style="width: 15%;">Perusahaan</th>
                        <th class="py-3" style="width: 15%;">Kota / Wilayah</th>
                        <th class="py-3" style="width: 20%;">Alamat Lengkap</th>
                        <th class="py-3" style="width: 10%;">Status</th>
                        <th class="pe-4 text-end py-3" style="width: 12%;">Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <th class="ps-4 pb-3"></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Kode..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Cabang..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Perusahaan..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Kota..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Alamat..."></th>
                        <th class="pb-3">
                            <select class="form-select form-select-sm column-filter">
                                <option value="">Semua</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </th>
                        <th class="pe-4 pb-3"></th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px; color: var(--text-heading);">
                    <tr>
                        <td colspan="8" class="text-center py-5">
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
    // CSRF Token Setup untuk AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load & Tampilkan Modal Tambah Cabang
    $('#btn-add-branch').on('click', function() {
        ERPLoader.loadModal("{{ route('logistic.master.branch.create') }}", '#addBranchModal', {
            title: 'Tambah Cabang',
            errorMessage: 'Gagal memuat form tambah cabang.'
        });
    });

    // Inisialisasi DataTable Server-Side
    var table = $('#branches-table').DataTable({
        processing: true,
        serverSide: true,
        orderCellsTop: true,
        ajax: {
            url: "{{ route('logistic.master.branch.data') }}"
        },
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lBf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: 'Data_Cabang_Export',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'pdfHtml5',
                className: 'buttons-pdf d-none',
                title: 'Data_Cabang_Export',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }
        ],
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
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
                render: function(data) {
                    return '<code class="bg-secondary-subtle text-secondary px-2 py-1 rounded" style="font-size: 11.5px; font-weight: 500;">' + data + '</code>';
                }
            },
            { 
                data: 'name', 
                name: 'name',
                class: 'fw-semibold text-heading' 
            },
            { 
                data: 'company.name', 
                name: 'company.name',
                class: 'text-heading',
                defaultContent: '-'
            },
            { 
                data: 'city',
                name: 'city',
                class: 'text-heading',
                render: function(data) {
                    return data ? '<i class="bi bi-geo-alt me-1.5 text-muted"></i>' + data : '-';
                }
            },
            { 
                data: 'address',
                name: 'address',
                class: 'text-heading',
                render: function(data) {
                    return data ? '<span title="' + data + '" class="d-inline-block text-truncate" style="max-width: 250px; vertical-align: bottom;">' + data + '</span>' : '-';
                }
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
            paginate: {
                previous: 'Prev',
                next: 'Next'
            }
        }
    });

    // Processing overlay indicator
    table.on('processing.dt', function(e, settings, processing) {
        if (processing) {
            $('.dataTables_wrapper').addClass('is-processing');
        } else {
            $('.dataTables_wrapper').removeClass('is-processing');
        }
    });

    // Initialize Select2 on DataTable length menu select
    $('.dataTables_length select').select2({
        theme: 'bootstrap-5',
        width: '75px',
        minimumResultsForSearch: -1
    });

    // Initialize Select2 on column filter select
    $('#branches-table thead tr.filter-row select.column-filter').select2({
        theme: 'bootstrap-5',
        width: '100%',
        minimumResultsForSearch: -1
    });

    // Cegah sorting ketika klik di input filter kolom
    $('#branches-table thead tr.filter-row').on('click mousedown keydown', function(e) {
        e.stopPropagation();
    });

    // Event handler filter per kolom
    $('#branches-table thead tr.filter-row .column-filter').on('keyup change clear', function() {
        var th = $(this).closest('th');
        var index = th.index();
        if (table.column(index).search() !== this.value) {
            table.column(index).search(this.value).draw();
        }
    });

    // Custom Export Excel Trigger
    $('#btn-export-excel').on('click', function(e) {
        e.preventDefault();
        table.button('.buttons-excel').trigger();
    });

    // Custom Export PDF Trigger
    $('#btn-export-pdf').on('click', function(e) {
        e.preventDefault();
        table.button('.buttons-pdf').trigger();
    });

    // Form Tambah Cabang AJAX dengan Delegasi Event
    $(document).on('submit', '#add-branch-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        // Clear error states
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ route('logistic.master.branch.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addBranchModal').modal('hide');
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
                    AppAlert.error('Error!', 'Terjadi kesalahan. Silakan coba lagi.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Edit Button Click (Load Edit Modal secara Dinamis)
    $(document).on('click', '.edit-btn', function() {
        var uuid = $(this).data('uuid');
        var url = "{{ url('logistic/master/branch') }}/" + uuid + "/edit";
        
        ERPLoader.loadModal(url, '#editBranchModal', {
            title: 'Edit Cabang',
            errorMessage: 'Gagal mengambil data cabang.',
            onSuccess: function(modal) {
                // Inisialisasi Select2 setelah HTML diinjeksikan ke DOM
                $('#edit-status').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%',
                    minimumResultsForSearch: -1 // Sembunyikan pencarian
                });
            }
        });
    });

    // Form Edit Cabang AJAX dengan Delegasi Event
    $(document).on('submit', '#edit-branch-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var uuid = $('#edit-uuid').val();
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ url('logistic/master/branch') }}/" + uuid,
            type: "POST", // Memakai POST dengan _method PUT untuk spoofing laravel
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editBranchModal').modal('hide');
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
                    AppAlert.error('Error!', 'Terjadi kesalahan. Silakan coba lagi.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Delete Button Click via SweetAlert2
    $(document).on('click', '.delete-btn', function() {
        var uuid = $(this).data('uuid');
        var name = $(this).data('name');
        
        AppAlert.confirmDelete('Hapus Cabang?', 'Apakah Anda yakin ingin menghapus cabang "' + name + '"?').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('logistic/master/branch') }}/" + uuid,
                    type: "DELETE",
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Data Dihapus!', response.message);
                        }
                    },
                    error: function() {
                        AppAlert.error('Gagal!', 'Gagal menghapus cabang.');
                    }
                });
            }
        });
    });
});
</script>
@endpush