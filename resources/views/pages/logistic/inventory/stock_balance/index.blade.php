@extends('layouts.app')

@section('page_title', 'Saldo Stok')
@section('page_description', 'Pantau jumlah persediaan barang di berbagai cabang dan gudang secara real-time.')
@section('page_actions')
    <x-button id="btn-export-excel" variant="ghost-success" size="sm" icon="bi-file-earmark-excel">
        Excel
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="balance-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">Cabang</th>
                        <th class="py-3">Gudang</th>
                        <th class="py-3">Produk</th>
                        <th class="py-3 text-end pe-4">Stok Saat Ini</th>
                    </tr>
                    <tr class="filter-row">
                        <th class="ps-4 pb-3"></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Cabang..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Gudang..."></th>
                        <th class="pb-3"><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari Produk..."></th>
                        <th class="pe-4 pb-3"></th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px; color: var(--text-heading);">
                    <tr>
                        <td colspan="5" class="text-center py-5">
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
    var table = $('#balance-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('logistic.inventory.balance.data') }}",
        orderCellsTop: true,
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lBf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: 'Data_Saldo_Stok',
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            }
        ],
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: null, searchable: false, orderable: false, class: 'ps-4 text-muted', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
            { data: 'branch.name', name: 'branch.name', class: 'fw-semibold text-heading' },
            { data: 'warehouse.name', name: 'warehouse.name' },
            { data: 'product.name', name: 'product.name', render: function(data, type, row) { return data + ' <span class="text-muted ms-1">(' + (row.product.code || '-') + ')</span>'; } },
            { data: 'qty', name: 'qty', class: 'text-end pe-4 fw-bold text-primary', render: function(data, type, row) { return parseFloat(data).toLocaleString('id-ID') + ' ' + (row.product.unit ? row.product.unit.code : ''); } }
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

    $('#balance-table thead tr.filter-row .column-filter').on('keyup change clear', function() {
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
