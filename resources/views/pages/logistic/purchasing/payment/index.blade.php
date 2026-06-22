@extends('layouts.app')

@section('page_title', 'Pembayaran Supplier')
@section('page_description', 'Kelola pembayaran atas Faktur Supplier (Hutang AP).')
@section('page_actions')
    <x-button id="btn-add-payment" variant="primary" size="sm" icon="bi-plus-lg">
        Buat Pembayaran Baru
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="payment-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">No. Pembayaran</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Ref. Faktur</th>
                        <th class="py-3">Supplier</th>
                        <th class="py-3">Metode</th>
                        <th class="py-3 text-end">Jumlah Bayar</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px; color: var(--text-heading);">
                    <tr>
                        <td colspan="9" class="text-center py-5">
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

    var table = $('#payment-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('logistic.purchasing.payment.data') }}",
        order: [[2, 'desc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, class: 'ps-4 text-muted' },
            { data: 'document_number', name: 'document_number', class: 'fw-semibold text-heading' },
            { data: 'payment_date', name: 'payment_date' },
            { data: 'invoice_number', name: 'supplierInvoice.document_number' },
            { data: 'supplier_name', name: 'supplier.name' },
            { data: 'payment_method', name: 'payment_method', class: 'text-capitalize' },
            { data: 'payment_amount', name: 'payment_amount', class: 'text-end fw-bold text-primary' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'pe-4 text-end text-nowrap', render: function(data, type, row) {
                let uuid = row.uuid;
                let actions = '<div class="d-inline-flex gap-2">' +
                    '<button class="btn-icon-modern text-info show-btn" href="{{ url("logistic/purchasing/payment") }}/'+uuid+'" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
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
            processing: `<div class="d-flex flex-column align-items-center gap-2">
                <div class="modern-loader-spinner" style="width: 36px; height: 36px;">
                    <div class="spinner-outer" style="border-width: 2.5px;"></div>
                    <div class="spinner-inner" style="border-width: 1.5px;"></div>
                    <div class="spinner-dot" style="width: 5px; height: 5px;"></div>
                </div>
                <span class="fw-semibold text-muted" style="font-size: 12px; letter-spacing: 0.2px;">Memuat Data...</span>
            </div>`,
            search: '_INPUT_',
            searchPlaceholder: 'Cari secara global...',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 data',
            lengthMenu: 'Tampilkan _MENU_ entri',
            paginate: { previous: 'Prev', next: 'Next' }
        }
    });

    // Buka modal Buat Pembayaran
    $('#btn-add-payment').on('click', function(e) {
        e.preventDefault();
        ERPLoader.loadModal("{{ route('logistic.purchasing.payment.create') }}", '#createModal', {
            title: 'Buat Pembayaran Baru',
            errorMessage: 'Gagal memuat form Pembayaran Supplier.',
            onSuccess: function(modal) {
                modal.find('select[name="supplier_invoice_id"]').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });
                modal.find('select[name="payment_method"]').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%',
                    minimumResultsForSearch: -1 // Hide search box for simple options
                });
            }
        });
    });

    // Buka modal Detail
    $(document).on('click', '.show-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#showModal', {
            title: 'Detail Pembayaran',
            errorMessage: 'Gagal mengambil detail dokumen.'
        });
    });

    // Buka modal Edit
    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var uuid = $(this).data('uuid');
        var url = "{{ url('logistic/purchasing/payment') }}/" + uuid + "/edit";
        ERPLoader.loadModal(url, '#editModal', {
            title: 'Edit Pembayaran',
            errorMessage: 'Gagal mengambil form edit pembayaran.'
        });
        
        // Trigger preview update after modal loads
        setTimeout(function() {
            var editModal = $('#editModal');
            if(editModal.length) {
                editModal.find('input[name="payment_amount"]').trigger('input');
            }
        }, 800);
    });

    // Submit form Create
    $(document).on('submit', '#form-create-payment', function(e) {
        e.preventDefault();
        let form = $(this);
        
        let amountInput = form.find('input[name="payment_amount"]');
        let option = form.find('select[name="supplier_invoice_id"] option:selected');
        let remainingBalance = option.data('grand-total') || 0;
        let inputAmount = parseFloat(window.AppFormat.unmaskNumber(amountInput.val())) || 0;
        
        if (option.val() && inputAmount > remainingBalance) {
            let kelebihan = inputAmount - remainingBalance;
            AppAlert.error('Gagal!', 'Jumlah bayar melebihi sisa hutang sebesar Rp ' + window.AppFormat.formatRupiah(kelebihan) + '.');
            return false;
        }
        
        let submitBtn = form.find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...').prop('disabled', true);

        $.ajax({
            url: "{{ route('logistic.purchasing.payment.store') }}",
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
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

    // Submit form Edit
    $(document).on('submit', '#form-edit-payment', function(e) {
        e.preventDefault();
        let form = $(this);
        let uuid = form.data('uuid');
        
        let amountInput = form.find('input[name="payment_amount"]');
        let option = form.find('select[name="supplier_invoice_id"] option:selected');
        let remainingBalance = option.data('grand-total') || 0;
        let inputAmount = parseFloat(window.AppFormat.unmaskNumber(amountInput.val())) || 0;
        
        if (option.val() && inputAmount > remainingBalance) {
            let kelebihan = inputAmount - remainingBalance;
            AppAlert.error('Gagal!', 'Jumlah bayar melebihi sisa hutang sebesar Rp ' + window.AppFormat.formatRupiah(kelebihan) + '.');
            return false;
        }
        
        let submitBtn = form.find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...').prop('disabled', true);

        let formData = new FormData(this);
        // Method override for PUT
        formData.append('_method', 'PUT');

        $.ajax({
            url: "{{ url('logistic/purchasing/payment') }}/" + uuid,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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

    // Aksi workflow (Submit, Approve, Post)
    $(document).on('click', '.btn-action-payment', function() {
        let uuid = $(this).data('uuid');
        let action = $(this).data('action');
        let textMap = {
            'submit':  'mengajukan pembayaran ini untuk verifikasi',
            'approve': 'menyetujui pembayaran ini',
            'post':    'memposting pembayaran ini sehingga hutang AP dianggap lunas'
        };

        AppAlert.confirm('Konfirmasi Aksi', 'Apakah Anda yakin ingin ' + (textMap[action] || action) + '?', 'Ya, Lanjutkan!').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/logistic/purchasing/payment/${uuid}/${action}`,
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

    // Hapus draft
    $(document).on('click', '.delete-btn', function() {
        var uuid = $(this).data('uuid');
        AppAlert.confirmDelete('Hapus Draft Pembayaran?', 'Draft ini akan dihapus secara permanen.').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('logistic/purchasing/payment') }}/" + uuid,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Terhapus!', response.message);
                        }
                    },
                    error: function(xhr) {
                        AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Gagal menghapus draft.');
                    }
                });
            }
        });
    });

    // Auto-fill jumlah bayar saat pilih Invoice
    $(document).on('change', 'select[name="supplier_invoice_id"]', function() {
        let option = $(this).find('option:selected');
        let grandTotal = option.data('grand-total') || 0;
        let pending = option.data('pending') || 0;
        let isEdit = $(this).closest('form').attr('id') === 'form-edit-payment';
        let amountInput = isEdit ? $('#editModal').find('input[name="payment_amount"]') : $('#createModal').find('input[name="payment_amount"]');
        let displayEl = isEdit ? $('#invoice-amount-display-edit') : $('#invoice-amount-display');
        let formEl = $(this).closest('form');
        
        amountInput.val(window.AppFormat.formatRupiah(grandTotal));
        displayEl.text('Rp ' + parseFloat(grandTotal).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        
        updatePreview(formEl, isEdit);
        
        // Handle pending warning
        formEl.find('.pending-warning-box').remove();
        if (pending > 0) {
            let warningHtml = `
            <div class="col-12 pending-warning-box mt-2">
                <div class="alert alert-warning d-flex align-items-center rounded-3 mb-0" style="font-size: 13px; border-left: 4px solid var(--bs-warning);">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-3 text-warning"></i>
                    <div>
                        <strong>Pembayaran Dalam Proses!</strong> Ada transaksi senilai <strong>Rp ${parseFloat(pending).toLocaleString('id-ID')}</strong> yang masih dalam antrean persetujuan. Sisa yang bisa dibayarkan di atas telah disesuaikan.
                    </div>
                </div>
            </div>`;
            $(warningHtml).insertAfter($(this).closest('.col-md-12'));
        }
    });

    function updatePreview(formEl, isEdit) {
        let option = formEl.find('select[name="supplier_invoice_id"] option:selected');
        if(!option.val()) {
            formEl.find('.form-text').hide();
            return;
        }
        
        let remainingBalance = option.data('grand-total') || 0;
        let amountInput = formEl.find('input[name="payment_amount"]');
        let inputVal = amountInput.val();
        let inputAmount = parseFloat(window.AppFormat.unmaskNumber(inputVal)) || 0;
        
        let sisa = Math.max(0, remainingBalance - inputAmount);
        
        let previewElClass = isEdit ? '.preview-remaining-edit' : '.preview-remaining-create';
        formEl.find(previewElClass).text('Rp ' + sisa.toLocaleString('id-ID'));

        let container = amountInput.closest('div');

        if (inputAmount > remainingBalance) {
            let kelebihan = inputAmount - remainingBalance;
            amountInput.addClass('is-invalid');
            
            if (container.find('.payment-feedback').length === 0) {
                $('<div class="payment-feedback text-danger fw-medium mt-1" style="font-size: 11px;"></div>').insertAfter(amountInput);
            }
            container.find('.payment-feedback').html('<i class="bi bi-exclamation-triangle-fill me-1"></i>Jumlah melebihi sisa hutang! Kelebihan: <b>Rp ' + window.AppFormat.formatRupiah(kelebihan) + '</b>').show();
            container.find('.form-text').hide();
            container.find('.invalid-feedback').remove();
        } else {
            amountInput.removeClass('is-invalid');
            container.find('.payment-feedback').hide();
            container.find('.form-text').show();
        }
    }

    $(document).on('input', 'input[name="payment_amount"]', function() {
        let formEl = $(this).closest('form');
        let isEdit = formEl.attr('id') === 'form-edit-payment';
        updatePreview(formEl, isEdit);
    });

});
</script>
@endpush