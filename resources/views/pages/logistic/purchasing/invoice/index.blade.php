@extends('layouts.app')

@section('page_title', 'Faktur Supplier (A/P Invoice)')
@section('page_description', 'Kelola data tagihan masuk dari Supplier untuk proses pembayaran.')
@section('page_actions')
    <x-button id="btn-add-invoice" variant="primary" size="sm" icon="bi-plus-lg">
        Buat Faktur Baru
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="invoice-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">No. Internal</th>
                        <th class="py-3">No. Faktur (Supplier)</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Jatuh Tempo</th>
                        <th class="py-3">Supplier</th>
                        <th class="py-3 text-end">Total Tagihan</th>
                        <th class="py-3 text-end">Sisa Tagihan</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px; color: var(--text-heading);">
                    <tr>
                        <td colspan="10" class="text-center py-5">
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

    var table = $('#invoice-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('logistic.purchasing.invoice.data') }}",
        order: [[3, 'desc']], 
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, class: 'ps-4 text-muted' },
            { data: 'document_number', name: 'document_number', class: 'fw-semibold text-heading' },
            { data: 'supplier_invoice_number', name: 'supplier_invoice_number' },
            { data: 'date', name: 'date' },
            { data: 'due_date', name: 'due_date' },
            { data: 'supplier_name', name: 'supplier.name' },
            { data: 'grand_total', name: 'grand_total', class: 'text-end fw-bold' },
            { data: 'remaining_balance', name: 'remaining_balance', class: 'text-end fw-bold text-primary' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'pe-4 text-end text-nowrap', render: function(data, type, row) {
                let uuid = row.uuid;
                let actions = '<div class="d-inline-flex gap-2">' +
                    '<button class="btn-icon-modern text-info show-btn" href="{{ url("logistic/purchasing/invoice") }}/'+uuid+'" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
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

    $('#btn-add-invoice').on('click', function(e) {
        e.preventDefault();
        ERPLoader.loadModal("{{ route('logistic.purchasing.invoice.create') }}", '#createModal', {
            title: 'Buat Faktur Baru',
            errorMessage: 'Gagal memuat form Faktur Supplier.',
            onSuccess: function(modal) {
                $('#form-create-invoice select[name="goods_receipt_id"]').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#createModal'),
                    width: '100%'
                });
            }
        });
    });

    $(document).on('click', '.show-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#showModal', {
            title: 'Detail Faktur Supplier',
            errorMessage: 'Gagal mengambil detail dokumen.'
        });
    });

    // Buka modal Edit
    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var uuid = $(this).data('uuid');
        var url = "{{ url('logistic/purchasing/invoice') }}/" + uuid + "/edit";
        ERPLoader.loadModal(url, '#editModal', {
            title: 'Edit Faktur Supplier',
            errorMessage: 'Gagal mengambil form edit faktur.'
        });
    });

    // Form Submit Create
    $(document).on('submit', '#form-create-invoice', function(e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = form.find('button[type="submit"]');
        let originalText = submitBtn.html();
        
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('logistic.purchasing.invoice.store') }}",
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
    $(document).on('submit', '#form-edit-invoice', function(e) {
        e.preventDefault();
        let form = $(this);
        let uuid = form.data('uuid');
        let submitBtn = form.find('button[type="submit"]');
        let originalText = submitBtn.html();
        
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...').prop('disabled', true);
        
        $.ajax({
            url: "{{ url('logistic/purchasing/invoice') }}/" + uuid,
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
    $(document).on('click', '.btn-action-invoice', function() {
        let uuid = $(this).data('uuid');
        let action = $(this).data('action');
        let textMap = {
            'submit': 'mengajukan faktur ini untuk verifikasi',
            'approve': 'menyetujui faktur ini',
            'post': 'memposting faktur ini sehingga diakui sebagai Hutang Usaha (AP)'
        };

        AppAlert.confirm('Konfirmasi Aksi', 'Apakah Anda yakin ingin ' + textMap[action] + '?', 'Ya, Lanjutkan!').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/logistic/purchasing/invoice/${uuid}/${action}`,
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
        
        AppAlert.confirmDelete('Hapus Draft Faktur?', 'Draft Faktur akan dihapus secara permanen.').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('logistic/purchasing/invoice') }}/" + uuid,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Terhapus!', response.message);
                        }
                    },
                    error: function(xhr) {
                        AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Gagal menghapus draft Faktur.');
                    }
                });
            }
        });
    });

    // Kalkulator Form
    function calculateTotals(suffix = '') {
        let subtotal = 0;
        let containerId = suffix === '-edit' ? '#invoice-items-container-edit' : '#invoice-items-container';
        
        $(containerId).find('.item-total-price').each(function() {
            let val = parseFloat($(this).val()) || 0;
            subtotal += val;
        });

        let tax = parseFloat(window.AppFormat.unmaskNumber($('#tax-amount' + suffix).val())) || 0;
        let discount = parseFloat(window.AppFormat.unmaskNumber($('#discount-amount' + suffix).val())) || 0;
        let grandTotal = subtotal + tax - discount;

        $('#subtotal' + suffix).val(subtotal);
        $('#subtotal-display' + suffix).text(subtotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        
        $('#grand-total' + suffix).val(grandTotal);
        $('#grand-total-display' + suffix).text(grandTotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    $(document).on('input', '.qty-input, .price-input', function() {
        let row = $(this).closest('tr');
        let qty = parseFloat(window.AppFormat.unmaskNumber(row.find('.qty-input').val())) || 0;
        let price = parseFloat(window.AppFormat.unmaskNumber(row.find('.price-input').val())) || 0;
        let total = qty * price;
        row.find('.item-total-price').val(total);
        row.find('.item-total-display').text(total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        
        let isEdit = row.closest('tbody').attr('id') === 'invoice-items-container-edit';
        calculateTotals(isEdit ? '-edit' : '');
    });

    $(document).on('input', '#tax-amount, #discount-amount', function() {
        calculateTotals();
    });

    $(document).on('input', '#tax-amount-edit, #discount-amount-edit', function() {
        calculateTotals('-edit');
    });

    // Fetch GR Items when GR is selected
    $(document).on('change', 'select[name="goods_receipt_id"]', function() {
        let grId = $(this).val();
        let container = $('#invoice-items-container');
        container.empty();
        $('#subtotal').val(0);
        $('#subtotal-display').text('0');
        $('#grand-total').val(0);
        $('#grand-total-display').text('0');
        $('#tax-amount').val(0);
        $('#discount-amount').val(0);

        if (!grId) return;

        container.html('<tr><td colspan="7" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Memuat data dari Goods Receipt dan Purchase Order...</td></tr>');

        $.ajax({
            url: "{{ url('logistic/purchasing/invoice/get-gr') }}/" + grId,
            type: 'GET',
            success: function(res) {
                if(res.items && res.items.length > 0) {
                    let html = '';
                    let subtotal = 0;
                    res.items.forEach(function(item, index) {
                        subtotal += parseFloat(item.total_price);
                        html += `
                        <tr>
                            <td class="text-center py-2 ps-3 border-0 border-bottom border-light">${index + 1}</td>
                            <td class="py-2 border-0 border-bottom border-light">
                                ${item.product_name}
                                <input type="hidden" name="items[${index}][goods_receipt_item_id]" value="${item.goods_receipt_item_id}">
                                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                <input type="hidden" name="items[${index}][unit_id]" value="${item.unit_id}">
                            </td>
                            <td class="py-2 text-center border-0 border-bottom border-light">${item.unit_name}</td>
                            <td class="py-2 border-0 border-bottom border-light" style="width: 120px;">
                                <input type="text" class="form-control custom-form-control text-end qty-input format-number" name="items[${index}][quantity]" value="${window.AppFormat.formatRupiah(item.received_qty)}" required>
                            </td>
                            <td class="py-2 border-0 border-bottom border-light" style="width: 150px;">
                                <input type="text" class="form-control custom-form-control text-end price-input format-rupiah" name="items[${index}][unit_price]" value="${window.AppFormat.formatRupiah(item.unit_price)}" required>
                            </td>
                            <td class="py-2 text-end text-primary fw-bold border-0 border-bottom border-light item-total-display" style="width: 150px;">
                                ${parseFloat(item.total_price).toLocaleString('id-ID')}
                            </td>
                            <td class="py-2 pe-3 border-0 border-bottom border-light d-none">
                                <input type="hidden" class="item-total-price" name="items[${index}][total_price]" value="${item.total_price}">
                                <input type="text" class="form-control custom-form-control" name="items[${index}][notes]" placeholder="Catatan">
                            </td>
                        </tr>
                        `;
                    });
                    container.html(html);
                    calculateTotals();
                } else {
                    container.html('<tr><td colspan="7" class="text-center py-4 text-danger">Tidak ada item di dalam GR ini.</td></tr>');
                }
            },
            error: function() {
                container.html('<tr><td colspan="7" class="text-center py-4 text-danger">Gagal mengambil data GR.</td></tr>');
            }
        });
    });

    window.refreshTable = function() {
        table.ajax.reload(null, false);
    };
});
</script>
@endpush