@extends('layouts.app')

@section('page_title', 'Laporan Pembelian (Purchase Report)')
@section('page_description', 'Riwayat faktur pembelian dan pengeluaran ke supplier.')

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 p-4 mb-4 border-0 shadow-sm" style="background: var(--bg-dark-secondary);">
        <form id="filter-form">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <x-form.label>Tanggal Mulai</x-form.label>
                    <x-form.input type="date" name="start_date" id="start_date" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" />
                </div>
                <div class="col-lg-3 col-md-6">
                    <x-form.label>Tanggal Akhir</x-form.label>
                    <x-form.input type="date" name="end_date" id="end_date" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" />
                </div>
                <div class="col-lg-3 col-md-6">
                    <x-form.label>Status Faktur</x-form.label>
                    <x-form.select name="status" id="status" class="select2-basic">
                        <option value="">Semua Status</option>
                        <option value="paid">Lunas</option>
                        <option value="posted">Belum Lunas (Posted)</option>
                        <option value="approved">Approved</option>
                    </x-form.select>
                </div>
                <div class="col-lg-3 col-md-12 d-flex align-items-end justify-content-lg-end gap-2 mt-md-3 mt-lg-0">
                    <x-button type="button" id="btn-filter" variant="primary" icon="bi-search">Tampilkan</x-button>
                </div>
            </div>
        </form>
    </div>

    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="purchase-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Nomor Faktur</th>
                        <th class="py-3">Supplier</th>
                        <th class="py-3 text-end">Total Tagihan</th>
                        <th class="py-3 text-center">Status</th>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2-basic').select2({ theme: 'bootstrap-5', width: '100%', minimumResultsForSearch: -1 });

    var table = $('#purchase-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('business.reports.purchase.data') }}",
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.status = $('#status').val();
            }
        },
        order: [[1, 'desc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, class: 'ps-4 text-muted' },
            { data: 'date', name: 'date' },
            { data: 'supplier_invoice_number', name: 'supplier_invoice_number', class: 'fw-semibold text-heading' },
            { data: 'supplier_name', name: 'supplier.name' },
            { data: 'grand_total', name: 'grand_total', class: 'text-end fw-semibold text-danger' },
            { data: 'status_badge', name: 'status', class: 'text-center', orderable: false, searchable: false }
        ],
        language: {
            search: '_INPUT_',
            searchPlaceholder: 'Cari faktur...',
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