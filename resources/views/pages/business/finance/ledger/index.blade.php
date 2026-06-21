@extends('layouts.app')

@section('page_title', 'Buku Besar')
@section('page_description', 'Lihat mutasi jurnal dan saldo untuk setiap akun (Chart of Account).')

@section('content')
<div class="container-fluid px-0">


    <!-- Filter Card -->
    <div class="card rounded-4 p-4 mb-4 border-0 shadow-sm">
        <form id="filterForm">
            <div class="row g-3">
                <div class="col-lg-5 col-md-12">
                    <x-form.label>Akun (COA)</x-form.label>
                    <x-form.select id="account_id" name="account_id">
                        <option value="">Semua Akun</option>
                    </x-form.select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <x-form.label>Tanggal Mulai</x-form.label>
                    <x-form.input type="date" id="start_date" name="start_date" />
                </div>
                <div class="col-lg-2 col-md-6">
                    <x-form.label>Tanggal Akhir</x-form.label>
                    <x-form.input type="date" id="end_date" name="end_date" />
                </div>
                <div class="col-lg-3 col-md-12 d-flex align-items-end justify-content-lg-end gap-2 mt-md-3 mt-lg-0">
                    <x-button id="btnReset" variant="light" icon="bi-arrow-counterclockwise" data-bs-toggle="tooltip" title="Reset Filter">Reset</x-button>
                    <x-button id="btnFilter" variant="primary" icon="bi-search">Tampilkan</x-button>
                    <x-button id="btnPrint" variant="ghost-primary" icon="bi-printer" data-bs-toggle="tooltip" title="Cetak Laporan"></x-button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4" id="summaryCards" style="display: none;">
        <div class="col-md-3">
            <div class="card rounded-4 border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary me-3" style="width: 42px; height: 42px;">
                        <i class="bi bi-wallet2" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1" style="font-size: 12px; font-weight: 500; letter-spacing: 0.2px;">Saldo Awal</p>
                        <h6 class="fw-bold mb-0" id="summary_beginning_balance" style="font-size: 17px;">Rp 0</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-4 border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger me-3" style="width: 42px; height: 42px;">
                        <i class="bi bi-arrow-down-left" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1" style="font-size: 12px; font-weight: 500; letter-spacing: 0.2px;">Total Debit</p>
                        <h6 class="fw-bold mb-0" id="summary_total_debit" style="font-size: 17px;">Rp 0</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-4 border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success me-3" style="width: 42px; height: 42px;">
                        <i class="bi bi-arrow-up-right" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1" style="font-size: 12px; font-weight: 500; letter-spacing: 0.2px;">Total Kredit</p>
                        <h6 class="fw-bold mb-0" id="summary_total_credit" style="font-size: 17px;">Rp 0</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card rounded-4 border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary me-3" style="width: 42px; height: 42px;">
                        <i class="bi bi-cash-stack" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1" style="font-size: 12px; font-weight: 500; letter-spacing: 0.2px;">Saldo Akhir</p>
                        <h6 class="fw-bold mb-0" id="summary_ending_balance" style="font-size: 17px;">Rp 0</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="ledgerTable" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th width="5%" class="text-center">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="20%">Akun</th>
                        <th width="15%">Referensi</th>
                        <th class="py-3">Deskripsi</th>
                        <th class="py-3 text-end">Debit</th>
                        <th class="py-3 text-end">Kredit</th>
                        <th width="15%" class="py-3 text-end pe-4">Saldo</th>
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
    // Set default dates (Start of month to today)
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    $('#start_date').val(firstDay.toISOString().split('T')[0]);
    $('#end_date').val(today.toISOString().split('T')[0]);

    // Initialize Select2 for Account
    $('#account_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih Akun...',
        allowClear: true,
        ajax: {
            url: "{{ route('business.finance.coa.select2') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    only_detail: true
                };
            },
            processResults: function (data) {
                return {
                    results: data.results || data
                };
            },
            cache: true
        }
    });

    let isReset = true; // initially empty

    var table = $('#ledgerTable').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        ajax: {
            url: "{{ route('business.finance.ledger.data') }}",
            data: function (d) {
                d.account_id = $('#account_id').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.is_reset = isReset ? 1 : 0;
            }
        },
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'date', name: 'date' },
            { data: 'account', name: 'account.name' },
            { data: 'reference', name: 'source_type', orderable: false },
            { data: 'description', name: 'description', orderable: false },
            { data: 'debit', name: 'debit', class: 'text-end fw-semibold' },
            { data: 'credit', name: 'credit', class: 'text-end fw-semibold' },
            { data: 'running_balance', name: 'running_balance', class: 'text-end pe-4 fw-bold', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        language: {
            search: '_INPUT_',
            searchPlaceholder: 'Cari deskripsi...',
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

    function loadSummary() {
        return $.ajax({
            url: "{{ route('business.finance.ledger.summary') }}",
            type: "GET",
            data: {
                account_id: $('#account_id').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                is_reset: isReset ? 1 : 0
            },
            success: function(res) {
                if(res.success) {
                    $('#summaryCards').show();
                    const formatRp = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                    $('#summary_beginning_balance').text(formatRp(res.data.beginning_balance));
                    $('#summary_total_debit').text(formatRp(res.data.total_debit));
                    $('#summary_total_credit').text(formatRp(res.data.total_credit));
                    $('#summary_ending_balance').text(formatRp(res.data.ending_balance));
                } else {
                    toastr.error(res.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat memuat ringkasan saldo.');
            }
        });
    }

    $('#btnReset').on('click', function () {
        $('#filterForm')[0].reset();
        $('#account_id').val(null).trigger('change');
        isReset = true;
        
        // Clear summaries to 0
        $('#summary_beginning_balance').text('Rp 0');
        $('#summary_total_debit').text('Rp 0');
        $('#summary_total_credit').text('Rp 0');
        $('#summary_ending_balance').text('Rp 0');
        
        table.ajax.reload();
        toggleSaldoColumn();
    });

    $('#btnFilter').on('click', function () {
        let $btn = $(this);
        isReset = false;
        let originalContent = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>').prop('disabled', true);

        $('#summaryCards').show();
        
        let dtPromise = new Promise((resolve) => {
            table.ajax.reload(function() {
                resolve();
            });
        });

        let summaryPromise = loadSummary();

        Promise.all([dtPromise, summaryPromise]).finally(() => {
            $btn.html(originalContent).prop('disabled', false);
        });
    });

    // Auto load data on initialization
    $('#summaryCards').show();
    loadSummary();
    toggleSaldoColumn();

    // Toggle Saldo column based on Account filter
    function toggleSaldoColumn() {
        const accountId = $('#account_id').val();
        // Index 7 is running_balance
        table.column(7).visible(accountId ? true : false);
    }

    table.on('xhr.dt', function ( e, settings, json, xhr ) {
        toggleSaldoColumn();
    });

    // Click handler for Jurnal reference
    $(document).on('click', '.show-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#showModal', {
            title: 'Detail Jurnal'
        });
    });

    $('#btnPrint').on('click', function() {
        const accountId = $('#account_id').val() || '';
        const startDate = $('#start_date').val() || '';
        const endDate = $('#end_date').val() || '';
        const url = `{{ route('business.finance.ledger.print') }}?account_id=${accountId}&start_date=${startDate}&end_date=${endDate}`;
        window.open(url, '_blank');
    });
});
</script>
@endpush