@extends('layouts.app')

@section('page_title', 'Voucher & Promo')
@section('page_description', 'Kelola voucher promo dan diskon untuk pelanggan.')
@section('page_actions')
    <x-button id="btn-add-voucher" variant="primary" size="sm" icon="bi-plus-lg">
        Tambah Voucher
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Data Table Card -->
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="voucher-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3" style="width: 15%;">Kode</th>
                        <th class="py-3" style="width: 20%;">Nama Voucher</th>
                        <th class="py-3" style="width: 15%;">Tipe</th>
                        <th class="py-3" style="width: 15%;">Nilai</th>
                        <th class="py-3" style="width: 10%;">Kuota</th>
                        <th class="py-3" style="width: 10%;">Status</th>
                        <th class="pe-4 text-end py-3" style="width: 10%;">Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <th class="ps-4 pb-3"></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Kode..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Nama..."></th>
                        <th class="pb-3">
                            <select class="form-select form-select-sm column-filter">
                                <option value="">Semua</option>
                                <option value="nominal">Nominal</option>
                                <option value="percentage">Persentase</option>
                            </select>
                        </th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Nilai..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Kuota..."></th>
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
    let table;

    function toggleValueLabel(selectElem, labelId, maxDiscountContainerId) {
        let type = $(selectElem).val();
        if(type === 'percentage') {
            $(labelId).html('Nilai Persentase (%) <span class="text-danger">*</span>');
            $(maxDiscountContainerId).show();
        } else {
            $(labelId).html('Nilai Rupiah (Rp) <span class="text-danger">*</span>');
            $(maxDiscountContainerId).hide();
            $(maxDiscountContainerId).find('input').val('');
        }
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#btn-add-voucher').on('click', function() {
            ERPLoader.loadModal("{{ route('business.crm.voucher.create') }}", '#addVoucherModal', {
                title: 'Tambah Voucher',
                errorMessage: 'Gagal memuat form tambah voucher.',
                onSuccess: function(modal) {
                    $('#add-type, #add-status').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: modal,
                        width: '100%',
                        minimumResultsForSearch: -1
                    });
                }
            });
        });

        table = $('#voucher-table').DataTable({
            processing: true,
            serverSide: true,
            orderCellsTop: true,
            ajax: "{{ route('business.crm.voucher.data') }}",
            dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lBf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
            buttons: [],
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'ps-4 text-muted'},
                {
                    data: 'code', 
                    name: 'code',
                    render: function(data) {
                        return '<code class="bg-secondary-subtle text-secondary px-2 py-1 rounded" style="font-size: 11.5px; font-weight: 500;">' + data + '</code>';
                    }
                },
                {data: 'name', name: 'name', class: 'fw-semibold text-heading'},
                {
                    data: 'type', 
                    name: 'type',
                    class: 'text-heading',
                    render: function(data) {
                        return data === 'percentage' ? 'Persentase' : 'Nominal';
                    }
                },
                {
                    data: 'value', 
                    name: 'value',
                    class: 'text-heading',
                    render: function(data, type, row) {
                        if(row.type === 'percentage') return data + '%';
                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data);
                    }
                },
                {
                    data: 'quota', 
                    name: 'quota',
                    class: 'text-heading',
                    render: function(data, type, row) {
                        if(!data) return 'Unlimited';
                        return `${row.used_count} / ${data}`;
                    }
                },
                {
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        if(data === 'active') {
                            return '<span class="badge bg-success-subtle text-success px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="bi bi-check-circle me-1"></i> Aktif</span>';
                        }
                        return '<span class="badge bg-danger-subtle text-danger px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="bi bi-x-circle me-1"></i> Nonaktif</span>';
                    }
                },
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    class: 'pe-4 text-end text-nowrap',
                    render: function(data, type, row) {
                        return '<div class="d-inline-flex gap-2">' +
                            '<button class="btn-icon-modern text-primary edit-btn" data-id="' + row.id + '" title="Edit" style="background: color-mix(in srgb, var(--primary-accent) 12%, transparent);">' +
                            '<i class="bi bi-pencil-square"></i>' +
                            '</button>' +
                            '<button class="btn-icon-modern text-danger delete-btn" data-id="' + row.id + '" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">' +
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
            if (processing) $('.dataTables_wrapper').addClass('is-processing');
            else $('.dataTables_wrapper').removeClass('is-processing');
        });

        $('#voucher-table thead tr.filter-row .column-filter').on('keyup change clear', function() {
            var th = $(this).closest('th');
            var index = th.index();
            if (table.column(index).search() !== this.value) {
                table.column(index).search(this.value).draw();
            }
        });

        $('#voucher-table thead tr.filter-row').on('click mousedown keydown', function(e) { e.stopPropagation(); });

        $(document).on('submit', '#add-voucher-form', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            btn.prop('disabled', true);
            
            form.find('.form-control, .form-select').removeClass('is-invalid');
            form.find('.invalid-feedback').html('');

            $.ajax({
                url: "{{ route('business.crm.voucher.store') }}",
                method: "POST",
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addVoucherModal').modal('hide');
                        table.ajax.reload();
                        AppAlert.success('Sukses!', response.message);
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
                        let errorMessage = 'Terjadi kesalahan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                        AppAlert.error('Error!', errorMessage);
                    }
                },
                complete: function() {
                    btn.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var url = "{{ url('business/crm/voucher') }}/" + id + "/edit";
            
            ERPLoader.loadModal(url, '#editVoucherModal', {
                title: 'Edit Voucher',
                errorMessage: 'Gagal memuat form edit.',
                onSuccess: function(modal) {
                    $('#edit-type, #edit-status').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: modal,
                        width: '100%',
                        minimumResultsForSearch: -1
                    });
                }
            });
        });

        $(document).on('submit', '#edit-voucher-form', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            btn.prop('disabled', true);
            
            form.find('.form-control, .form-select').removeClass('is-invalid');
            form.find('.invalid-feedback').html('');

            let id = $('#edit-id').val();

            $.ajax({
                url: "{{ url('business/crm/voucher') }}/" + id,
                type: "PUT",
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#editVoucherModal').modal('hide');
                        table.ajax.reload();
                        AppAlert.success('Sukses!', response.message);
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
                        let errorMessage = 'Terjadi kesalahan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                        AppAlert.error('Error!', errorMessage);
                    }
                },
                complete: function() {
                    btn.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            AppAlert.confirmDelete('Hapus Voucher?', 'Data voucher yang dihapus tidak dapat dikembalikan!').then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('business/crm/voucher') }}/${id}`,
                        method: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                AppAlert.success('Terhapus!', response.message);
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan saat menghapus data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                            AppAlert.error('Gagal!', errorMessage);
                        }
                    });
                }
            });
        });
    });
</script>
@endpush