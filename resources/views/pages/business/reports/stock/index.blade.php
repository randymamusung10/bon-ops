@extends('layouts.app')

@section('page_title', 'Laporan Valuasi Stok (Stock Report)')
@section('page_description', 'Laporan saldo akhir persediaan barang beserta nilai valuasinya.')

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 p-4 mb-4 border-0 shadow-sm" style="background: var(--bg-dark-secondary);">
        <form id="filter-form" action="{{ route('business.reports.stock.export') }}" method="GET">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <x-form.label>Gudang</x-form.label>
                    <x-form.select name="warehouse_id" id="warehouse_id" class="select2-basic">
                        <option value="">Semua Gudang</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </x-form.select>
                </div>
                <div class="col-lg-8 col-md-12 d-flex align-items-end gap-2 mt-md-3 mt-lg-0">
                    <x-button type="button" id="btn-filter" variant="primary" icon="bi-search">Tampilkan</x-button>
                    <x-button type="submit" variant="ghost-success" icon="bi-file-earmark-excel">Export Excel</x-button>
                </div>
            </div>
        </form>
    </div>

    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="stock-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">Kode Produk</th>
                        <th class="py-3">Nama Produk</th>
                        <th class="py-3">Gudang</th>
                        <th class="py-3 text-end">Qty Tersedia</th>
                        <th class="py-3 text-end">Harga Beli Rata-Rata (Cost)</th>
                        <th class="py-3 text-end">Total Valuasi (Aset)</th>
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
    $('.select2-basic').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    var table = $('#stock-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('business.reports.stock.data') }}",
            data: function (d) {
                d.warehouse_id = $('#warehouse_id').val();
            }
        },
        order: [[2, 'asc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, class: 'ps-4 text-muted' },
            { data: 'product_code', name: 'product_code', class: 'fw-semibold text-heading' },
            { data: 'product_name', name: 'product_name' },
            { data: 'warehouse_name', name: 'warehouse_name' },
            { data: 'qty', name: 'qty', class: 'text-end fw-semibold text-primary' },
            { data: 'cost', name: 'cost', class: 'text-end text-muted', searchable: false, orderable: false },
            { data: 'valuation', name: 'valuation', class: 'text-end fw-bold text-success', searchable: false, orderable: false }
        ],
        language: {
            search: '_INPUT_',
            searchPlaceholder: 'Cari produk...',
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

    $('#btn-filter').click(function() {
        table.draw();
    });
});
</script>
@endpush