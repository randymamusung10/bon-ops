@extends('layouts.app')

@section('page_title', 'Shift Kasir')
@section('page_description', 'Manajemen shift kasir, buka kasir (modal awal), dan tutup kasir (verifikasi kas akhir).')

@section('content')
<div class="container-fluid px-0">
    <!-- Active Shift Status Card -->
    <div class="card rounded-4 border-0 shadow-sm p-4 mb-4" style="background: var(--bg-dark-secondary);">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                @if($activeShift)
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bi bi-unlock-fill" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0 text-heading" style="font-family: 'Outfit', sans-serif;">Shift Kasir Aktif</h5>
                            <span class="badge bg-success-subtle text-success px-2.5 py-0.5 rounded-pill" style="font-size: 11px;">Aktif</span>
                        </div>
                        <p class="text-muted mb-0 mt-1" style="font-size: 13px;">
                            Buka sejak: <strong class="text-heading">{{ $activeShift->start_time->format('d/m/Y H:i:s') }}</strong> | 
                            Modal Awal: <strong class="text-success">Rp {{ number_format($activeShift->start_cash, 0, ',', '.') }}</strong>
                        </p>
                    </div>
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bi bi-lock-fill" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0 text-heading" style="font-family: 'Outfit', sans-serif;">Shift Kasir Tertutup</h5>
                            <span class="badge bg-secondary-subtle text-secondary px-2.5 py-0.5 rounded-pill" style="font-size: 11px;">Tutup</span>
                        </div>
                        <p class="text-muted mb-0 mt-1" style="font-size: 13px;">Silakan buka shift kasir baru untuk memulai transaksi POS.</p>
                    </div>
                @endif
            </div>
            
            <div class="d-flex gap-2">
                @if($activeShift)
                    <x-button type="button" variant="danger" size="md" id="btn-close-shift" icon="bi-lock-fill">
                        Tutup Shift Kasir
                    </x-button>
                    <a href="{{ route('operational.pos.terminal') }}">
                        <x-button type="button" variant="primary" size="md" icon="bi-pc-display">
                            Buka POS Terminal
                        </x-button>
                    </a>
                @else
                    <x-button type="button" variant="success" size="md" id="btn-open-shift" icon="bi-unlock-fill">
                        Buka Shift Kasir Baru
                    </x-button>
                @endif
            </div>
        </div>
    </div>

    <!-- History Card -->
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="shift-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3" style="width: 15%;">Kasir</th>
                        <th class="py-3" style="width: 12%;">Cabang</th>
                        <th class="py-3" style="width: 14%;">Waktu Buka</th>
                        <th class="py-3" style="width: 14%;">Waktu Tutup</th>
                        <th class="py-3" style="width: 12%;">Modal Awal</th>
                        <th class="py-3" style="width: 12%;">Kas Fisik Akhir</th>
                        <th class="py-3" style="width: 8%;">Status</th>
                        <th class="pe-4 text-end py-3" style="width: 8%;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px; color: var(--text-heading);">
                    <!-- Will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Buka Shift -->
<x-modal id="openShiftModal" title="Buka Shift Kasir">
    <form id="open-shift-form">
        @csrf
        <div class="mb-3">
            <x-form.label required>Kas Awal / Modal Awal (Rp)</x-form.label>
            <x-form.input type="text" name="start_cash" id="start_cash" class="format-number" value="100000" required placeholder="Masukkan modal kas awal" />
            <div class="invalid-feedback"></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="success" size="sm" icon="bi-check-lg">Buka Shift</x-button>
        </div>
    </form>
</x-modal>

<!-- Modal Tutup Shift -->
@if($activeShift)
<x-modal id="closeShiftModal" title="Tutup Shift Kasir">
    <form id="close-shift-form" data-uuid="{{ $activeShift->uuid }}">
        @csrf
        
        <!-- Live Shift Report Summary -->
        <div class="p-3 rounded-4 mb-4" style="background: rgba(226, 232, 240, 0.03); border: 1px solid rgba(226, 232, 240, 0.08);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted" style="font-size: 12px;">Modal Kas Awal</span>
                <span class="fw-medium text-heading" id="summary-start-cash" style="font-size: 13px;">Rp 0</span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted" style="font-size: 12px;">Total Penjualan Tunai (Cash)</span>
                <span class="fw-medium text-heading" id="summary-cash-sales" style="font-size: 13px;">Rp 0</span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted" style="font-size: 12px;">Total Penjualan Non-Tunai</span>
                <span class="fw-medium text-heading" id="summary-non-cash-sales" style="font-size: 13px;">Rp 0</span>
            </div>
            <hr style="opacity: 0.1; margin: 10px 0;">
            <div class="d-flex align-items-center justify-content-between mb-0">
                <span class="fw-semibold text-heading" style="font-size: 13px;">Ekspektasi Kas di Laci</span>
                <span class="fw-bold text-primary" id="summary-expected-cash" style="font-size: 14px;">Rp 0</span>
            </div>
        </div>

        <div class="mb-3">
            <x-form.label required>Kas Fisik Akhir (Rp)</x-form.label>
            <x-form.input type="text" name="actual_end_cash" id="actual_end_cash" class="format-number" required placeholder="Hitung kas fisik di laci" />
            <div class="invalid-feedback"></div>
            <small class="text-muted mt-1 d-block">Hitung nominal kas fisik riil yang ada pada laci kasir saat ini.</small>
        </div>

        <!-- Discrepancy Indicator -->
        <div class="p-3 rounded-4 mb-3 d-none" id="discrepancy-container">
            <div class="d-flex align-items-center justify-content-between">
                <span class="fw-semibold" style="font-size: 13px;" id="discrepancy-label">Selisih Kas</span>
                <span class="fw-bold" style="font-size: 14px;" id="summary-discrepancy">Rp 0</span>
            </div>
        </div>

        <div class="mb-3">
            <x-form.label>Catatan Tutup Shift</x-form.label>
            <x-form.textarea name="notes" id="notes" rows="2" placeholder="Catatan selisih kas / kendala operasional (opsional)"></x-form.textarea>
            <div class="invalid-feedback"></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="danger" size="sm" icon="bi-lock-fill">Tutup Shift</x-button>
        </div>
    </form>
</x-modal>
@endif

<!-- Modal Detail Shift -->
<x-modal id="detailShiftModal" title="Detail Shift Kasir">
    <div class="mb-4 p-3 rounded-4" style="background: rgba(226, 232, 240, 0.03); border: 1px solid rgba(226, 232, 240, 0.08);">
        <div class="row g-2" style="font-size: 13px;">
            <div class="col-6">
                <span class="text-muted d-block">Kasir</span>
                <strong class="text-heading" id="det-cashier">-</strong>
            </div>
            <div class="col-6">
                <span class="text-muted d-block">Cabang</span>
                <strong class="text-heading" id="det-branch">-</strong>
            </div>
            <div class="col-6 mt-2">
                <span class="text-muted d-block">Waktu Buka</span>
                <strong class="text-heading" id="det-start-time">-</strong>
            </div>
            <div class="col-6 mt-2">
                <span class="text-muted d-block">Waktu Tutup</span>
                <strong class="text-heading" id="det-end-time">-</strong>
            </div>
            <div class="col-6 mt-2">
                <span class="text-muted d-block">Status</span>
                <span id="det-status">-</span>
            </div>
            <div class="col-6 mt-2">
                <span class="text-muted d-block">Jumlah Transaksi</span>
                <strong class="text-heading" id="det-total-trx">0</strong>
            </div>
        </div>
    </div>

    <h6 class="fw-bold text-heading mb-2">Ringkasan Penjualan</h6>
    <div class="p-3 rounded-4 mb-4" style="background: rgba(226, 232, 240, 0.03); border: 1px solid rgba(226, 232, 240, 0.08);">
        <div class="d-flex align-items-center justify-content-between mb-2" style="font-size: 13px;">
            <span class="text-muted">Penjualan Tunai (Cash)</span>
            <span class="fw-medium text-heading" id="det-cash-sales">Rp 0</span>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-2" style="font-size: 13px;">
            <span class="text-muted">Penjualan Debit</span>
            <span class="fw-medium text-heading" id="det-debit-sales">Rp 0</span>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-2" style="font-size: 13px;">
            <span class="text-muted">Penjualan Credit</span>
            <span class="fw-medium text-heading" id="det-credit-sales">Rp 0</span>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-2" style="font-size: 13px;">
            <span class="text-muted">Penjualan QRIS</span>
            <span class="fw-medium text-heading" id="det-qris-sales">Rp 0</span>
        </div>
        <hr style="opacity: 0.1; margin: 10px 0;">
        <div class="d-flex align-items-center justify-content-between" style="font-size: 13px;">
            <span class="fw-semibold text-heading">Total Penjualan</span>
            <span class="fw-bold text-primary" id="det-total-sales" style="font-size: 14px;">Rp 0</span>
        </div>
    </div>

    <h6 class="fw-bold text-heading mb-2">Rekonsiliasi Kas</h6>
    <div class="p-3 rounded-4 mb-3" style="background: rgba(226, 232, 240, 0.03); border: 1px solid rgba(226, 232, 240, 0.08);">
        <div class="d-flex align-items-center justify-content-between mb-2" style="font-size: 13px;">
            <span class="text-muted">Modal Kas Awal</span>
            <span class="fw-medium text-heading" id="det-start-cash">Rp 0</span>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-2" style="font-size: 13px;">
            <span class="text-muted">Ekspektasi Kas di Laci</span>
            <span class="fw-medium text-heading" id="det-expected-cash">Rp 0</span>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-2" style="font-size: 13px;">
            <span class="text-muted">Kas Fisik Akhir</span>
            <span class="fw-medium text-heading" id="det-actual-cash">-</span>
        </div>
        <hr style="opacity: 0.1; margin: 10px 0;">
        <div class="d-flex align-items-center justify-content-between" style="font-size: 13px;">
            <span class="fw-semibold" id="det-discrepancy-label">Selisih Kas</span>
            <span class="fw-bold" id="det-discrepancy" style="font-size: 14px;">-</span>
        </div>
    </div>

    <div class="mb-0" style="font-size: 13px;">
        <span class="text-muted d-block mb-1">Catatan</span>
        <span class="text-heading" id="det-notes">-</span>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
        <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
    </div>
</x-modal>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#shift-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('operational.pos.shift.data') }}",
        order: [[3, 'desc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        columns: [
            { data: null, searchable: false, orderable: false, class: 'ps-4 text-muted', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
            { data: 'user.name', name: 'user.name', class: 'fw-semibold text-heading' },
            { data: 'branch.name', name: 'branch.name' },
            { data: 'start_time', name: 'start_time' },
            { data: 'end_time', name: 'end_time' },
            { data: 'start_cash', name: 'start_cash' },
            { data: 'actual_end_cash', name: 'actual_end_cash' },
            { data: 'status_badge', name: 'status', orderable: false },
            {
                data: 'uuid',
                orderable: false,
                searchable: false,
                class: 'pe-4 text-end text-nowrap',
                render: function(data, type, row) {
                    return '<div class="d-inline-flex gap-2">' +
                        '<button class="btn-icon-modern text-info btn-shift-detail" data-uuid="' + data + '" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
                        '<i class="bi bi-eye"></i>' +
                        '</button>' +
                        '</div>';
                }
            }
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
            searchPlaceholder: 'Cari shift...',
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

    $('.dataTables_length select').select2({
        theme: 'bootstrap-5',
        width: '75px',
        minimumResultsForSearch: -1
    });

    $('#btn-open-shift').on('click', function() {
        $('#openShiftModal').modal('show');
    });

    function formatMoney(amount) {
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(amount);
    }

    var expectedCashVal = 0;

    $('#btn-close-shift').on('click', function() {
        var uuid = $('#close-shift-form').data('uuid');
        // Show loader before showing modal
        $.ajax({
            url: `/operational/pos/shift/summary/${uuid}`,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    expectedCashVal = res.summary.expected_end_cash;
                    
                    $('#summary-start-cash').html('Rp ' + formatMoney(res.summary.start_cash));
                    $('#summary-cash-sales').html('Rp ' + formatMoney(res.summary.cash_sales));
                    $('#summary-non-cash-sales').html('Rp ' + formatMoney(res.summary.non_cash_sales));
                    $('#summary-expected-cash').html('Rp ' + formatMoney(res.summary.expected_end_cash));
                    
                    // Reset input and discrepancy
                    $('#actual_end_cash').val('').removeClass('is-invalid');
                    $('#discrepancy-container').addClass('d-none');
                    
                    $('#closeShiftModal').modal('show');
                }
            },
            error: function() {
                AppAlert.error('Gagal!', 'Tidak dapat memuat data ringkasan shift kasir.');
            }
        });
    });

    $('#actual_end_cash').on('keyup change input', function() {
        var rawVal = window.AppFormat.unmaskNumber($(this).val());
        var actualVal = parseFloat(rawVal) || 0;
        
        var diff = actualVal - expectedCashVal;
        
        $('#discrepancy-container').removeClass('d-none');
        if (diff === 0) {
            $('#discrepancy-container').css('background', 'rgba(16, 185, 129, 0.08)').css('border', '1px solid rgba(16, 185, 129, 0.2)');
            $('#discrepancy-label').text('Kas Sesuai (Klop)').css('color', 'var(--bs-success)');
            $('#summary-discrepancy').html('Rp 0').css('color', 'var(--bs-success)');
        } else if (diff > 0) {
            // green or primary accent
            $('#discrepancy-container').css('background', 'rgba(59, 130, 246, 0.08)').css('border', '1px solid rgba(59, 130, 246, 0.2)');
            $('#discrepancy-label').text('Selisih Lebih (Surplus)').css('color', 'var(--bs-primary)');
            $('#summary-discrepancy').html('+Rp ' + formatMoney(diff)).css('color', 'var(--bs-primary)');
        } else {
            $('#discrepancy-container').css('background', 'rgba(239, 68, 68, 0.08)').css('border', '1px solid rgba(239, 68, 68, 0.2)');
            $('#discrepancy-label').text('Selisih Kurang (Shortage)').css('color', 'var(--bs-danger)');
            $('#summary-discrepancy').html('-Rp ' + formatMoney(Math.abs(diff))).css('color', 'var(--bs-danger)');
        }
    });

    $('#open-shift-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        form.find('.form-control').removeClass('is-invalid');

        $.ajax({
            url: "{{ route('operational.pos.shift.open') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                if (res.success) {
                    $('#openShiftModal').modal('hide');
                    AppAlert.success('Berhasil!', res.message);
                    setTimeout(() => { location.reload(); }, 1000);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        form.find('[name="'+key+'"]').addClass('is-invalid').next('.invalid-feedback').html(val[0]);
                    });
                } else {
                    AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
                }
            }
        });
    });

    $('#close-shift-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var uuid = form.data('uuid');
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        form.find('.form-control').removeClass('is-invalid');

        $.ajax({
            url: `/operational/pos/shift/close/${uuid}`,
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                if (res.success) {
                    $('#closeShiftModal').modal('hide');
                    AppAlert.success('Berhasil!', res.message);
                    setTimeout(() => { location.reload(); }, 1000);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        form.find('[name="'+key+'"]').addClass('is-invalid').next('.invalid-feedback').html(val[0]);
                    });
                } else {
                    AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
                }
            }
        });
    });

    // View Shift Detail
    $(document).on('click', '.btn-shift-detail', function() {
        var uuid = $(this).data('uuid');
        $.ajax({
            url: `/operational/pos/shift/detail/${uuid}`,
            type: "GET",
            success: function(res) {
                if (res.success) {
                    var s = res.shift;
                    $('#det-cashier').text(s.cashier);
                    $('#det-branch').text(s.branch);
                    $('#det-start-time').text(s.start_time);
                    $('#det-end-time').text(s.end_time);
                    $('#det-total-trx').text(s.total_transactions + ' transaksi');
                    
                    if (s.status === 'open') {
                        $('#det-status').html('<span class="badge bg-success-subtle text-success px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="bi bi-unlock-fill me-1"></i> Open</span>');
                    } else {
                        $('#det-status').html('<span class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="bi bi-lock-fill me-1"></i> Closed</span>');
                    }

                    // Sales breakdown
                    $('#det-cash-sales').text('Rp ' + formatMoney(s.cash_sales));
                    $('#det-debit-sales').text('Rp ' + formatMoney(s.debit_sales));
                    $('#det-credit-sales').text('Rp ' + formatMoney(s.credit_sales));
                    $('#det-qris-sales').text('Rp ' + formatMoney(s.qris_sales));
                    $('#det-total-sales').text('Rp ' + formatMoney(s.total_sales));

                    // Cash reconciliation
                    $('#det-start-cash').text('Rp ' + formatMoney(s.start_cash));
                    $('#det-expected-cash').text('Rp ' + formatMoney(s.expected_end_cash));
                    $('#det-actual-cash').text(s.actual_end_cash !== null ? 'Rp ' + formatMoney(s.actual_end_cash) : '-');
                    $('#det-notes').text(s.notes);

                    // Discrepancy
                    if (s.discrepancy !== null) {
                        if (s.discrepancy === 0) {
                            $('#det-discrepancy-label').text('Kas Sesuai (Klop)').css('color', 'var(--bs-success)');
                            $('#det-discrepancy').text('Rp 0').css('color', 'var(--bs-success)');
                        } else if (s.discrepancy > 0) {
                            $('#det-discrepancy-label').text('Selisih Lebih (Surplus)').css('color', 'var(--bs-primary)');
                            $('#det-discrepancy').text('+Rp ' + formatMoney(s.discrepancy)).css('color', 'var(--bs-primary)');
                        } else {
                            $('#det-discrepancy-label').text('Selisih Kurang (Shortage)').css('color', 'var(--bs-danger)');
                            $('#det-discrepancy').text('-Rp ' + formatMoney(Math.abs(s.discrepancy))).css('color', 'var(--bs-danger)');
                        }
                    } else {
                        $('#det-discrepancy-label').text('Selisih Kas').css('color', '');
                        $('#det-discrepancy').text('-').css('color', '');
                    }

                    $('#detailShiftModal').modal('show');
                }
            },
            error: function() {
                AppAlert.error('Gagal!', 'Tidak dapat memuat detail shift kasir.');
            }
        });
    });
});
</script>
@endpush