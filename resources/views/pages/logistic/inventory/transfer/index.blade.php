@extends('layouts.app')

@section('page_title', 'Mutasi Stok')
@section('page_description', 'Kelola perpindahan stok antar gudang.')
@section('page_actions')
    <x-button id="btn-add-transfer" variant="primary" size="sm" icon="bi-plus-lg">
        Buat Mutasi Baru
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="transfer-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">No. Dokumen</th>
                        <th class="py-3">Gudang Asal</th>
                        <th class="py-3">Gudang Tujuan</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
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
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table = $('#transfer-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('logistic.inventory.transfer.data') }}",
        order: [[1, 'desc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, class: 'ps-4 text-muted' },
            { data: 'date', name: 'date' },
            { data: 'document_number', name: 'document_number', class: 'fw-semibold text-heading' },
            { data: 'source', name: 'source' },
            { data: 'destination', name: 'destination' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'pe-4 text-end text-nowrap', render: function(data, type, row) {
                let uuid = row.uuid;
                let actions = '<div class="d-inline-flex gap-2">' +
                    '<button class="btn-icon-modern text-info show-btn" href="{{ url("logistic/inventory/transfer") }}/'+uuid+'" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
                    '<i class="bi bi-eye"></i>' +
                    '</button>';
                if (row.status === 'draft') {
                    actions += '<button class="btn-icon-modern text-warning edit-btn" data-uuid="'+uuid+'" title="Edit" style="background: rgba(245, 158, 11, 0.12);">' +
                        '<i class="bi bi-pencil"></i>' +
                        '</button>';
                    actions += '<button class="btn-icon-modern text-danger delete-btn" data-uuid="'+uuid+'" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">' +
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

    $('#btn-add-transfer').on('click', function(e) {
        e.preventDefault();
        ERPLoader.loadModal("{{ route('logistic.inventory.transfer.create') }}", '#createTransferModal', {
            title: 'Buat Mutasi Stok',
            errorMessage: 'Gagal memuat form mutasi stok.',
            onSuccess: function(modal) {
                // Init select2 for main fields
                modal.find('select[name="source_branch_id"], select[name="source_warehouse_id"], select[name="destination_branch_id"], select[name="destination_warehouse_id"]').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });

                let itemIndex = 0;
                
                function addItemRow() {
                    let template = $('#transfer-item-template').html();
                    template = template.replace(/__INDEX__/g, itemIndex);
                    $('#table-items tbody').append(template);
                    
                    $('#table-items tbody').find('select[name="items['+itemIndex+'][product_id]"]').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: modal,
                        width: '100%'
                    });
                    itemIndex++;
                }
                
                // Add initial row
                addItemRow();
                
                modal.find('#btn-add-item').on('click', function() {
                    addItemRow();
                });
                
                modal.find('#table-items').on('click', '.btn-remove-item', function() {
                    if($('#table-items tbody tr').length > 1) {
                        $(this).closest('tr').remove();
                    } else {
                        AppAlert.error('Peringatan', 'Minimal harus ada 1 item produk.');
                    }
                });

                // Handle form submit
                modal.find('#form-create-transfer').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    var sourceWh = form.find('[name="source_warehouse_id"]').val();
                    var destWh = form.find('[name="destination_warehouse_id"]').val();
                    if (sourceWh && destWh && sourceWh === destWh) {
                        AppAlert.error('Error Validasi!', 'Gudang tujuan tidak boleh sama dengan gudang asal.');
                        return;
                    }

                    var submitBtn = form.find('button[type="submit"]');
                    submitBtn.prop('disabled', true);
                    
                    $.ajax({
                        url: "{{ route('logistic.inventory.transfer.store') }}",
                        type: "POST",
                        data: form.serialize(),
                        dataType: "json",
                        headers: {
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#createTransferModal').modal('hide');
                                table.ajax.reload();
                                AppAlert.success('Tersimpan!', response.message);
                            } else {
                                AppAlert.error('Gagal!', response.message || 'Gagal menyimpan.');
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Terjadi kesalahan sistem.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                let errors = Object.values(xhr.responseJSON.errors).map(err => err[0]);
                                msg = errors.join('\n');
                            }
                            try {
                                AppAlert.error('Error Validasi!', msg);
                            } catch (e) {
                                alert("Error: " + msg);
                            }
                        },
                        complete: function() { submitBtn.prop('disabled', false); }
                    });
                });
            }
        });
    });

    $(document).on('click', '.show-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#showTransferModal', {
            title: 'Detail Mutasi Stok',
            errorMessage: 'Gagal mengambil data mutasi.'
        });
    });

    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var uuid = $(this).data('uuid');
        var url = "{{ url('logistic/inventory/transfer') }}/" + uuid + "/edit";
        
        ERPLoader.loadModal(url, '#editTransferModal', {
            title: 'Edit Mutasi Stok',
            errorMessage: 'Gagal memuat form mutasi stok.',
            onSuccess: function(modal) {
                // Init select2
                modal.find('select[name="source_branch_id"], select[name="source_warehouse_id"], select[name="destination_branch_id"], select[name="destination_warehouse_id"]').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });
                modal.find('.select2-product-edit').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });

                let itemIndex = modal.find('#table-items-edit tbody tr').length;
                
                modal.find('#btn-add-item-edit').on('click', function() {
                    let template = $('#transfer-item-template-edit').html();
                    template = template.replace(/__INDEX__/g, itemIndex);
                    modal.find('#table-items-edit tbody').append(template);
                    
                    modal.find('#table-items-edit tbody').find('select[name="items['+itemIndex+'][product_id]"]').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: modal,
                        width: '100%'
                    });
                    itemIndex++;
                });
                
                modal.find('#table-items-edit').on('click', '.btn-remove-item', function() {
                    if(modal.find('#table-items-edit tbody tr').length > 1) {
                        $(this).closest('tr').remove();
                    } else {
                        AppAlert.error('Peringatan', 'Minimal harus ada 1 item produk.');
                    }
                });

                // Handle form submit
                modal.find('#form-edit-transfer').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    var sourceWh = form.find('[name="source_warehouse_id"]').val();
                    var destWh = form.find('[name="destination_warehouse_id"]').val();
                    if (sourceWh && destWh && sourceWh === destWh) {
                        AppAlert.error('Error Validasi!', 'Gudang tujuan tidak boleh sama dengan gudang asal.');
                        return;
                    }

                    var submitBtn = form.find('button[type="submit"]');
                    submitBtn.prop('disabled', true);
                    
                    $.ajax({
                        url: "{{ url('logistic/inventory/transfer') }}/" + uuid,
                        type: "POST",
                        data: form.serialize(),
                        dataType: "json",
                        headers: {
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#editTransferModal').modal('hide');
                                table.ajax.reload();
                                AppAlert.success('Tersimpan!', response.message);
                            } else {
                                AppAlert.error('Gagal!', response.message || 'Gagal menyimpan.');
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Terjadi kesalahan sistem.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                let errors = Object.values(xhr.responseJSON.errors).map(err => err[0]);
                                msg = errors.join('\n');
                            }
                            try {
                                AppAlert.error('Error Validasi!', msg);
                            } catch (e) {
                                alert("Error: " + msg);
                            }
                        },
                        complete: function() { submitBtn.prop('disabled', false); }
                    });
                });
            }
        });
    });

    // HANDLE ACTION BUTTONS
    $(document).on('click', '.btn-action-transfer', function() {
        let uuid = $(this).data('uuid');
        let action = $(this).data('action'); // submit, approve, post
        let textMap = {
            'submit': 'mengajukan mutasi ini',
            'approve': 'menyetujui mutasi ini',
            'post': 'memposting mutasi ini dan mengubah stok'
        };

        AppAlert.confirm('Konfirmasi Aksi', 'Apakah Anda yakin ingin ' + textMap[action] + '?', 'Ya, Lanjutkan!').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/logistic/inventory/transfer/${uuid}/${action}`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        $('#showTransferModal').modal('hide');
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

    // HANDLE DELETE
    $(document).on('click', '.delete-btn', function() {
        var uuid = $(this).data('uuid');
        
        AppAlert.confirmDelete('Hapus Mutasi?', 'Dokumen mutasi yang masih berstatus draft akan dihapus permanen.').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('logistic/inventory/transfer') }}/" + uuid,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Terhapus!', response.message);
                        }
                    },
                    error: function(xhr) {
                        AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Gagal menghapus dokumen mutasi.');
                    }
                });
            }
        });
    });
});
</script>
@endpush