@extends('layouts.app')

@section('page_title', 'Stock Opname')
@section('page_description', 'Kelola perhitungan stok fisik secara berkala untuk mencocokkan stok sistem dengan stok aktual.')
@section('page_actions')
    <x-button id="btn-export-excel" variant="ghost-success" size="sm" icon="bi-file-earmark-excel">
        Excel
    </x-button>
    <x-button id="btn-add-opname" variant="primary" size="sm" icon="bi-plus-lg">
        Buat Opname
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="opname-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">No. Dokumen</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Gudang</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <th class="ps-4 pb-3"></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Dokumen..."></th>
                        <th class="pb-3"></th>
                        <th class="pb-3"></th>
                        <th class="pb-3">
                            <select class="form-select form-select-sm column-filter">
                                <option value="">Semua</option>
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                                <option value="approved">Approved</option>
                                <option value="posted">Posted</option>
                            </select>
                        </th>
                        <th class="pe-4 pb-3"></th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px; color: var(--text-heading);">
                    <tr>
                        <td colspan="6" class="text-center py-5">
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
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table = $('#opname-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('logistic.inventory.opname.data') }}",
        orderCellsTop: true,
        order: [[2, 'desc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lBf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: 'Data_Stock_Opname',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            }
        ],
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: null, searchable: false, orderable: false, class: 'ps-4 text-muted', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
            { data: 'document_number', name: 'document_number', class: 'fw-semibold text-heading' },
            { data: 'date', name: 'date' },
            { data: 'warehouse.name', name: 'warehouse.name' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'uuid', name: 'uuid', class: 'pe-4 text-end text-nowrap', orderable: false, searchable: false, render: function(data, type, row) {
                let actions = '<div class="d-inline-flex gap-2">' +
                    '<button class="btn-icon-modern text-info show-btn" href="{{ url("logistic/inventory/opname") }}/'+data+'" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
                    '<i class="bi bi-eye"></i>' +
                    '</button>';
                if (row.status === 'draft') {
                    actions += '<button class="btn-icon-modern text-primary edit-btn" href="{{ url("logistic/inventory/opname") }}/'+data+'/edit" title="Edit" style="background: rgba(59, 130, 246, 0.12);">' +
                    '<i class="bi bi-pencil"></i>' +
                    '</button>';
                    actions += '<button class="btn-icon-modern text-danger delete-btn" data-uuid="'+data+'" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">' +
                    '<i class="bi bi-trash"></i>' +
                    '</button>';
                }
                actions += '</div>';
                return actions;
            }}
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

    $('#opname-table thead tr.filter-row select.column-filter').select2({
        theme: 'bootstrap-5', width: '100%', minimumResultsForSearch: -1
    });

    $('#opname-table thead tr.filter-row').on('click mousedown keydown', function(e) {
        e.stopPropagation();
    });

    $('#opname-table thead tr.filter-row .column-filter').on('keyup change clear', function() {
        var th = $(this).closest('th');
        var index = th.index();
        if (table.column(index).search() !== this.value) {
            table.column(index).search(this.value).draw();
        }
    });

    $('#btn-export-excel').on('click', function(e) { e.preventDefault(); table.button('.buttons-excel').trigger(); });

    $('#btn-add-opname').on('click', function(e) {
        e.preventDefault();
        ERPLoader.loadModal("{{ route('logistic.inventory.opname.create') }}", '#addOpnameModal', {
            title: 'Buat Stock Opname',
            errorMessage: 'Gagal memuat form stock opname.',
            onSuccess: function(modal) {
                modal.find('#add-branch_id, #add-warehouse_id').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });

                let itemIndex = 0;

                function addItemRow(productId = '', actualQty = '0', notes = '') {
                    let template = $('#item-row-template').html();
                    template = template.replace(/__INDEX__/g, itemIndex);
                    modal.find('#items-table tbody').append(template);

                    let rowSelect = modal.find('select[name="items['+itemIndex+'][product_id]"]');
                    rowSelect.select2({
                        theme: 'bootstrap-5',
                        dropdownParent: modal,
                        width: '100%'
                    });

                    if (productId !== '') {
                        rowSelect.val(productId).trigger('change');
                    }
                    modal.find('input[name="items['+itemIndex+'][actual_qty]"]').val(actualQty);
                    modal.find('input[name="items['+itemIndex+'][notes]"]').val(notes);

                    itemIndex++;
                }

                modal.find('#btn-add-item').on('click', function() {
                    addItemRow();
                });

                modal.find('#items-table').on('click', '.btn-remove-item', function() {
                    $(this).closest('tr').remove();
                });

                // Muat Stok Sistem
                modal.find('#btn-load-system-stock').on('click', function() {
                    let warehouseId = modal.find('#add-warehouse_id').val();
                    if (!warehouseId) {
                        AppAlert.error('Peringatan', 'Silakan pilih gudang terlebih dahulu.');
                        return;
                    }

                    $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Loading...');

                    $.ajax({
                        url: "{{ route('logistic.inventory.opname.system_stock') }}",
                        type: "GET",
                        data: { warehouse_id: warehouseId },
                        success: function(response) {
                            modal.find('#items-table tbody').empty();
                            itemIndex = 0;
                            if (response.length === 0) {
                                AppAlert.info('Informasi', 'Tidak ada produk yang terdaftar di sistem.');
                                addItemRow();
                            } else {
                                response.forEach(function(item) {
                                    addItemRow(item.product_id, item.system_qty, '');
                                });
                            }
                        },
                        error: function() {
                            AppAlert.error('Error', 'Gagal memuat stok sistem.');
                        },
                        complete: function() {
                            modal.find('#btn-load-system-stock').prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1"></i> Muat Stok Sistem');
                        }
                    });
                });

                // Inisialisasi baris pertama
                addItemRow();
            }
        });
    });

    $(document).on('submit', '#add-opname-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        form.find('.form-control, .form-select').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ route('logistic.inventory.opname.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addOpnameModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Tersimpan!', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        let field = form.find('[name="'+key+'"]');
                        if(field.length === 0) {
                            let parts = key.split('.');
                            if(parts.length === 3) {
                                field = form.find('[name="'+parts[0]+'['+parts[1]+']['+parts[2]+']"]');
                            }
                        }

                        if(field.length > 0) {
                            field.addClass('is-invalid');
                            if(field.next('.invalid-feedback').length === 0) {
                                field.after('<div class="invalid-feedback d-block" style="font-size: 11px;">'+val[0]+'</div>');
                            } else {
                                field.next('.invalid-feedback').html(val[0]).show();
                            }
                        } else {
                            AppAlert.error('Validasi Gagal', val[0]);
                        }
                    });
                } else {
                    AppAlert.error('Error!', 'Terjadi kesalahan sistem.');
                }
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    $(document).on('click', '.show-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#showOpnameModal', {
            title: 'Detail Stock Opname',
            errorMessage: 'Gagal mengambil data stock opname.'
        });
    });

    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#editOpnameModal', {
            title: 'Edit Stock Opname',
            errorMessage: 'Gagal mengambil data stock opname.',
            onSuccess: function(modal) {
                modal.find('#edit-branch_id, #edit-warehouse_id').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });

                modal.find('.product-select').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });

                let itemIndexEdit = modal.find('#items-table tbody tr').length;

                function addItemRowEdit() {
                    let template = $('#item-row-template-edit').html();
                    template = template.replace(/__INDEX__/g, itemIndexEdit);
                    modal.find('#items-table tbody').append(template);

                    modal.find('#items-table tbody').find('select[name="items['+itemIndexEdit+'][product_id]"]').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: modal,
                        width: '100%'
                    });
                    itemIndexEdit++;
                }

                modal.find('#btn-add-item-edit').on('click', function() {
                    addItemRowEdit();
                });

                modal.find('#items-table').on('click', '.btn-remove-item', function() {
                    $(this).closest('tr').remove();
                });
            }
        });
    });

    $(document).on('submit', '#edit-opname-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var uuid = form.data('uuid');
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        form.find('.form-control, .form-select').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        $.ajax({
            url: "{{ url('logistic/inventory/opname') }}/" + uuid,
            type: "PUT",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editOpnameModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Tersimpan!', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        let field = form.find('[name="'+key+'"]');
                        if(field.length === 0) {
                            let parts = key.split('.');
                            if(parts.length === 3) {
                                field = form.find('[name="'+parts[0]+'['+parts[1]+']['+parts[2]+']"]');
                            }
                        }

                        if(field.length > 0) {
                            field.addClass('is-invalid');
                            if(field.next('.invalid-feedback').length === 0) {
                                field.after('<div class="invalid-feedback d-block" style="font-size: 11px;">'+val[0]+'</div>');
                            } else {
                                field.next('.invalid-feedback').html(val[0]).show();
                            }
                        } else {
                            AppAlert.error('Validasi Gagal', val[0]);
                        }
                    });
                } else {
                    AppAlert.error('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
                }
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    $(document).on('click', '.btn-action-opname', function() {
        var uuid = $(this).data('uuid');
        var action = $(this).data('action');
        var textMap = {
            'submit': 'mengajukan dokumen stock opname ini',
            'approve': 'menyetujui dokumen stock opname ini',
            'post': 'mem-posting dokumen stock opname ini dan memperbarui stok fisik'
        };

        AppAlert.confirm('Konfirmasi Aksi', 'Apakah Anda yakin ingin ' + textMap[action] + '?', 'Ya, Lanjutkan!').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/logistic/inventory/opname/${uuid}/${action}`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        $('#showOpnameModal').modal('hide');
                        table.ajax.reload();
                        AppAlert.success('Berhasil!', res.message);
                    },
                    error: function(err) {
                        AppAlert.error('Gagal!', err.responseJSON?.message || 'Terjadi kesalahan.');
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        var uuid = $(this).data('uuid');

        AppAlert.confirmDelete('Hapus Stock Opname?', 'Dokumen yang masih berstatus draft akan dihapus permanen.').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('logistic/inventory/opname') }}/" + uuid,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Terhapus!', response.message);
                        }
                    },
                    error: function(xhr) {
                        AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Gagal menghapus dokumen.');
                    }
                });
            }
        });
    });
});
</script>
@endpush