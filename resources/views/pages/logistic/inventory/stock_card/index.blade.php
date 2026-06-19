@extends('layouts.app')

@section('page_title', 'Kartu Stok')
@section('page_description', 'Lacak riwayat pergerakan stok barang masuk dan keluar.')
@section('page_actions')
    <x-button id="btn-export-excel" variant="ghost-success" size="sm" icon="bi-file-earmark-excel">
        Excel
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="card-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3">Tanggal</th>
                        <th class="py-3">Gudang</th>
                        <th class="py-3">Produk</th>
                        <th class="py-3">Referensi</th>
                        <th class="py-3 text-end text-success">Masuk</th>
                        <th class="py-3 text-end text-danger">Keluar</th>
                        <th class="py-3 text-end pe-4 text-primary">Saldo</th>
                    </tr>
                    <tr class="filter-row">
                        <th class="ps-4 pb-3"></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Gudang..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Produk..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Referensi..."></th>
                        <th class="pb-3"></th>
                        <th class="pb-3"></th>
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

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#card-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('logistic.inventory.card.data') }}",
        orderCellsTop: true,
        order: [[0, 'desc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lBf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: 'Data_Kartu_Stok',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }
            }
        ],
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'date', name: 'date', class: 'ps-4' },
            { data: 'warehouse.name', name: 'warehouse.name', class: 'fw-semibold text-heading' },
            { data: 'product.name', name: 'product.name', render: function(data, type, row) { return data + ' <span class="text-muted ms-1">(' + (row.product.code || '-') + ')</span>'; } },
            { data: 'reference_type', name: 'reference_type', render: function(data, type, row) {
                var ref = '';
                if(data === 'stock_adjustment') ref = '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill" style="font-size:10px;">Penyesuaian</span>';
                else if(data === 'purchase_receipt') ref = '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill" style="font-size:10px;">Penerimaan</span>';
                else ref = '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill" style="font-size:10px;">'+data+'</span>';
                var refNumber = row.reference_number ? row.reference_number : ('ID: ' + row.reference_id);
                return ref + '<br><small class="text-muted fw-semibold mt-1 d-inline-block" style="font-size: 11px;">'+refNumber+'</small>';
            } },
            { data: 'qty_in', name: 'qty_in', class: 'text-end text-success fw-bold', render: function(data) { return data > 0 ? '+'+parseFloat(data).toLocaleString('id-ID') : '-'; } },
            { data: 'qty_out', name: 'qty_out', class: 'text-end text-danger fw-bold', render: function(data) { return data > 0 ? '-'+parseFloat(data).toLocaleString('id-ID') : '-'; } },
            { data: 'balance_after', name: 'balance_after', class: 'text-end pe-4 text-primary fw-bold', render: function(data, type, row) { return parseFloat(data).toLocaleString('id-ID') + ' ' + (row.product.unit ? row.product.unit.code : ''); } }
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

    $('#card-table thead tr.filter-row .column-filter').on('keyup change clear', function() {
        var th = $(this).closest('th');
        var index = th.index();
        if (table.column(index).search() !== this.value) {
            table.column(index).search(this.value).draw();
        }
    });

    $('#btn-export-excel').on('click', function(e) { e.preventDefault(); table.button('.buttons-excel').trigger(); });
});
</script>
@endpush
