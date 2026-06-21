@extends('layouts.app')

@section('page_title', 'Hutang Dagang (Accounts Payable)')
@section('page_description', 'Buku Pembantu Hutang berdasarkan Faktur Supplier yang belum dilunasi.')
@section('page_actions')
    <!-- Dashboard AP specific actions if needed -->
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="ap-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">Tanggal Faktur</th>
                        <th class="py-3">Nomor Faktur</th>
                        <th class="py-3">Supplier</th>
                        <th class="py-3">Jatuh Tempo</th>
                        <th class="py-3 text-end">Total Tagihan</th>
                        <th class="py-3 text-end">Sisa Hutang</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center">Aksi</th>
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
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table = $('#ap-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('business.finance.payable.data') }}",
        order: [[1, 'desc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, class: 'ps-4 text-muted' },
            { data: 'date', name: 'date' },
            { data: 'supplier_invoice_number', name: 'supplier_invoice_number', class: 'fw-semibold text-heading' },
            { data: 'supplier_name', name: 'supplier.name' },
            { data: 'due_date', name: 'due_date' },
            { data: 'grand_total', name: 'grand_total', class: 'text-end fw-semibold' },
            { data: 'remaining_balance', name: 'remaining_balance', class: 'text-end fw-bold text-danger' },
            { data: 'status_badge', name: 'status', class: 'text-center', orderable: false, searchable: false },
            { data: 'action', name: 'action', class: 'text-center', orderable: false, searchable: false }
        ],
        language: {
            search: '_INPUT_',
            searchPlaceholder: 'Cari hutang...',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            lengthMenu: 'Tampilkan _MENU_ entri'
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

    window.refreshTable = function() {
        table.ajax.reload(null, false);
    };

    $(document).on('click', '.btn-show', function(e) {
        e.preventDefault();
        var uuid = $(this).data('uuid');
        var url = "{{ url('business/finance/payable') }}/" + uuid + "/show-modal";
        
        ERPLoader.loadModal(url, '#show-modal-payable', {
            title: 'Detail Hutang'
        });
    });
});
</script>
@endpush