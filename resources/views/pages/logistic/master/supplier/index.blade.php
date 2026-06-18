@extends('layouts.app')

@section('page_title', 'Manajemen Pemasok (Supplier)')
@section('page_description', 'Kelola daftar pemasok untuk tenant Anda.')
@section('page_actions')
    <x-button id="btn-export-excel" variant="ghost-success" size="sm" icon="bi-file-earmark-excel">
        Excel
    </x-button>
    <x-button id="btn-export-pdf" variant="ghost-danger" size="sm" icon="bi-file-earmark-pdf">
        PDF
    </x-button>
    <x-button id="btn-add-supplier" variant="primary" size="sm" icon="bi-plus-lg">
        Tambah Pemasok
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="suppliers-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3" style="width: 15%;">Kode</th>
                        <th class="py-3" style="width: 25%;">Nama Pemasok</th>
                        <th class="py-3" style="width: 15%;">Telepon</th>
                        <th class="py-3" style="width: 15%;">Kota</th>
                        <th class="py-3" style="width: 10%;">Status</th>
                        <th class="pe-4 text-end py-3" style="width: 15%;">Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <th class="ps-4 pb-3"></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Kode..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Pemasok..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Telepon..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Kota..."></th>
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
                        <td colspan="7" class="text-center py-5">
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

    $('#btn-add-supplier').on('click', function() {
        ERPLoader.loadModal("{{ route('logistic.master.supplier.create') }}", '#addSupplierModal', {
            title: 'Tambah Pemasok',
            errorMessage: 'Gagal memuat form tambah pemasok.'
        });
    });

    var table = $('#suppliers-table').DataTable({
        processing: true,
        serverSide: true,
        orderCellsTop: true,
        ajax: {
            url: "{{ route('logistic.master.supplier.data') }}"
        },
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lBf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: 'Data_Pemasok_Export',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            },
            {
                extend: 'pdfHtml5',
                className: 'buttons-pdf d-none',
                title: 'Data_Pemasok_Export',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
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
                data: 'phone', 
                name: 'phone',
                class: 'text-heading',
                defaultContent: '-'
            },
            { 
                data: 'city',
                name: 'city',
                class: 'text-heading',
                defaultContent: '-'
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
                        '<button class="btn-icon-modern text-info show-btn" data-uuid="' + data + '" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
                        '<i class="bi bi-eye"></i>' +
                        '</button>' +
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

    table.on('processing.dt', function(e, settings, processing) {
        if (processing) {
            $('.dataTables_wrapper').addClass('is-processing');
        } else {
            $('.dataTables_wrapper').removeClass('is-processing');
        }
    });

    $('.dataTables_length select').select2({ theme: 'bootstrap-5', width: '75px', minimumResultsForSearch: -1 });
    $('#suppliers-table thead tr.filter-row select.column-filter').select2({ theme: 'bootstrap-5', width: '100%', minimumResultsForSearch: -1 });
    $('#suppliers-table thead tr.filter-row').on('click mousedown keydown', function(e) { e.stopPropagation(); });

    $('#suppliers-table thead tr.filter-row .column-filter').on('keyup change clear', function() {
        var th = $(this).closest('th');
        var index = th.index();
        if (table.column(index).search() !== this.value) {
            table.column(index).search(this.value).draw();
        }
    });

    $('#btn-export-excel').on('click', function(e) { e.preventDefault(); table.button('.buttons-excel').trigger(); });
    $('#btn-export-pdf').on('click', function(e) { e.preventDefault(); table.button('.buttons-pdf').trigger(); });

    $(document).on('submit', '#add-supplier-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ route('logistic.master.supplier.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addSupplierModal').modal('hide');
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
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    $(document).on('click', '.show-btn', function() {
        var uuid = $(this).data('uuid');
        var url = "{{ url('logistic/master/supplier') }}/" + uuid;
        ERPLoader.loadModal(url, '#showSupplierModal', { title: 'Detail Pemasok', errorMessage: 'Gagal mengambil data detail pemasok.' });
    });

    $(document).on('click', '.edit-btn', function() {
        var uuid = $(this).data('uuid');
        var url = "{{ url('logistic/master/supplier') }}/" + uuid + "/edit";
        ERPLoader.loadModal(url, '#editSupplierModal', { title: 'Edit Pemasok', errorMessage: 'Gagal mengambil data pemasok.' });
    });

    $(document).on('submit', '#edit-supplier-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var uuid = $('#edit-uuid').val();
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ url('logistic/master/supplier') }}/" + uuid,
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editSupplierModal').modal('hide');
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
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        var uuid = $(this).data('uuid');
        var name = $(this).data('name');
        
        AppAlert.confirmDelete('Hapus Pemasok?', 'Apakah Anda yakin ingin menghapus pemasok "' + name + '"?').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('logistic/master/supplier') }}/" + uuid,
                    type: "DELETE",
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Data Dihapus!', response.message);
                        }
                    },
                    error: function() { AppAlert.error('Gagal!', 'Gagal menghapus pemasok.'); }
                });
            }
        });
    });
});
</script>
@endpush