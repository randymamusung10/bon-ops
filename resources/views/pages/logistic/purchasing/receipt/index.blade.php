@extends('layouts.app')

@section('page_title', 'Penerimaan Barang (Goods Receipt)')
@section('page_description', 'Kelola data penerimaan fisik barang dari Supplier berdasarkan Purchase Order.')
@section('page_actions')
    <x-button id="btn-add-receipt" variant="primary" size="sm" icon="bi-plus-lg">
        Buat Penerimaan Baru
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="receipt-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">No. Dokumen</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Gudang Penerima</th>
                        <th class="py-3">Supplier</th>
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

    var table = $('#receipt-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('logistic.purchasing.receipt.data') }}",
        order: [[2, 'desc']], // Urut berdasarkan tanggal
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, class: 'ps-4 text-muted' },
            { data: 'document_number', name: 'document_number', class: 'fw-semibold text-heading' },
            { data: 'date', name: 'date' },
            { data: 'warehouse_name', name: 'warehouse.name' },
            { data: 'supplier_name', name: 'supplier.name' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'pe-4 text-end text-nowrap', render: function(data, type, row) {
                let uuid = row.uuid;
                let actions = '<div class="d-inline-flex gap-2">' +
                    '<button class="btn-icon-modern text-info show-btn" href="{{ url("logistic/purchasing/receipt") }}/'+uuid+'" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
                    '<i class="bi bi-eye"></i>' +
                    '</button>';
                if (row.status === 'draft') {
                    actions += '<button class="btn-icon-modern text-warning edit-btn" href="{{ url("logistic/purchasing/receipt") }}/'+uuid+'/edit" title="Edit" style="background: rgba(245, 158, 11, 0.12);">' +
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

    $('#btn-add-receipt').on('click', function(e) {
        e.preventDefault();
        ERPLoader.loadModal("{{ route('logistic.purchasing.receipt.create') }}", '#createModal', {
            title: 'Buat Penerimaan Barang',
            errorMessage: 'Gagal memuat form Penerimaan Barang.',
            onSuccess: function(modal) {
                $('#form-create-receipt select[name="warehouse_id"], #form-create-receipt select[name="purchase_order_id"]').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#createModal'),
                    width: '100%'
                });
            }
        });
    });

    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#editModal', {
            title: 'Edit Penerimaan Barang',
            errorMessage: 'Gagal memuat form Edit Penerimaan Barang.',
            onSuccess: function(modal) {
                $('#form-edit-receipt select[name="warehouse_id"], #form-edit-receipt select[name="purchase_order_id"]').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#editModal'),
                    width: '100%'
                });
                window.AppFormat.init();
            }
        });
    });

    $(document).on('click', '.show-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#showModal', {
            title: 'Detail Penerimaan Barang',
            errorMessage: 'Gagal mengambil detail dokumen.'
        });
    });

    // Form Submit Create
    $(document).on('submit', '#form-create-receipt', function(e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = form.find('button[type="submit"]');
        let originalText = submitBtn.html();
        
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('logistic.purchasing.receipt.store') }}",
            type: 'POST',
            data: form.serialize(),
            success: function(res) {
                if (res.success) {
                    $('#createModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Berhasil!', res.message);
                }
            },
            error: function(xhr) {
                AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.');
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Form Submit Edit
    $(document).on('submit', '#form-edit-receipt', function(e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = form.find('button[type="submit"]');
        let originalText = submitBtn.html();
        let url = form.attr('action');
        
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...').prop('disabled', true);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(res) {
                if (res.success) {
                    $('#editModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Berhasil!', res.message);
                }
            },
            error: function(xhr) {
                AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.');
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // HANDLE ACTION BUTTONS
    $(document).on('click', '.btn-action-receipt', function() {
        let uuid = $(this).data('uuid');
        let action = $(this).data('action');
        let textMap = {
            'submit': 'mengajukan dokumen penerimaan ini',
            'approve': 'menyetujui dokumen penerimaan ini',
            'post': 'memposting penerimaan ini agar stok gudang bertambah'
        };

        AppAlert.confirm('Konfirmasi Aksi', 'Apakah Anda yakin ingin ' + textMap[action] + '?', 'Ya, Lanjutkan!').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/logistic/purchasing/receipt/${uuid}/${action}`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        $('#showModal').modal('hide');
                        table.ajax.reload();
                        AppAlert.success('Berhasil!', res.message || 'Aksi berhasil dilakukan.');
                    },
                    error: function(err) {
                        AppAlert.error('Gagal!', err.responseJSON?.message || 'Terjadi kesalahan sistem.');
                    }
                });
            }
        });
    });

    // HANDLE DELETE
    $(document).on('click', '.delete-btn', function() {
        var uuid = $(this).data('uuid');
        
        AppAlert.confirmDelete('Hapus Draft Penerimaan?', 'Draft Penerimaan Barang akan dihapus secara permanen.').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('logistic/purchasing/receipt') }}/" + uuid,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Terhapus!', response.message);
                        }
                    },
                    error: function(xhr) {
                        AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Gagal menghapus draft Penerimaan Barang.');
                    }
                });
            }
        });
    });

    // Fetch PO Items when PO is selected
    $(document).on('change', 'select[name="purchase_order_id"]', function() {
        let poId = $(this).val();
        let container = $('#po-items-container');
        container.empty();

        if (!poId) return;

        container.html('<tr><td colspan="6" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</td></tr>');

        $.ajax({
            url: "{{ url('logistic/purchasing/receipt/get-po') }}/" + poId,
            type: 'GET',
            success: function(res) {
                if(res.items && res.items.length > 0) {
                    let html = '';
                    res.items.forEach(function(item, index) {
                        html += `
                        <tr>
                            <td class="text-center py-2 ps-3 border-0 border-bottom border-light">${index + 1}</td>
                            <td class="py-2 border-0 border-bottom border-light">
                                ${item.product.name}
                                <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">
                                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                <input type="hidden" name="items[${index}][unit_id]" value="${item.unit_id}">
                            </td>
                            <td class="py-2 text-center border-0 border-bottom border-light">${item.unit.name}</td>
                            <td class="py-2 text-end text-primary fw-bold border-0 border-bottom border-light">
                                ${parseFloat(item.quantity).toLocaleString('id-ID')}
                                <input type="hidden" name="items[${index}][ordered_qty]" value="${item.quantity}">
                            </td>
                            <td class="py-2 border-0 border-bottom border-light">
                                <input type="text" class="form-control custom-form-control text-end format-number qty-input" name="items[${index}][received_qty]" value="${item.quantity}" required>
                            </td>
                            <td class="py-2 pe-3 border-0 border-bottom border-light">
                                <input type="text" class="form-control custom-form-control" name="items[${index}][notes]" placeholder="Catatan">
                            </td>
                        </tr>
                        `;
                    });
                    container.html(html);
                } else {
                    container.html('<tr><td colspan="6" class="text-center py-4 text-danger">Tidak ada item di dalam PO ini.</td></tr>');
                }
            },
            error: function() {
                container.html('<tr><td colspan="6" class="text-center py-4 text-danger">Gagal mengambil data PO.</td></tr>');
            }
        });
    });

    window.refreshTable = function() {
        table.ajax.reload(null, false);
    };
});
</script>
@endpush