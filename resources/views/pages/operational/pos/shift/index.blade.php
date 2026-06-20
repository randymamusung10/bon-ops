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
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary rounded px-2.5 py-1 me-2.5">
                <i class="bi bi-clock-history text-white" style="font-size: 15px;"></i>
            </div>
            <h5 class="fw-bold mb-0 text-heading" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.2px;">Riwayat Shift Kasir</h5>
        </div>

        <div class="table-responsive">
            <table id="shift-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">Kasir</th>
                        <th class="py-3">Cabang</th>
                        <th class="py-3">Waktu Buka</th>
                        <th class="py-3">Waktu Tutup</th>
                        <th class="py-3">Modal Awal</th>
                        <th class="py-3">Kas Fisik Akhir</th>
                        <th class="py-3">Status</th>
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
            <x-form.input type="number" name="start_cash" id="start_cash" min="0" value="100000" required placeholder="Masukkan modal kas awal" />
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
        <div class="mb-3">
            <x-form.label required>Kas Fisik Akhir (Rp)</x-form.label>
            <x-form.input type="number" name="actual_end_cash" id="actual_end_cash" min="0" required placeholder="Hitung kas fisik di laci" />
            <div class="invalid-feedback"></div>
            <small class="text-muted mt-1 d-block">Hitung nominal kas fisik riil yang ada pada laci kasir saat ini.</small>
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
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#shift-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('operational.pos.shift.data') }}",
        order: [[3, 'desc']],
        columns: [
            { data: null, searchable: false, orderable: false, class: 'ps-4 text-muted', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
            { data: 'user.name', name: 'user.name', class: 'fw-semibold text-heading' },
            { data: 'branch.name', name: 'branch.name' },
            { data: 'start_time', name: 'start_time' },
            { data: 'end_time', name: 'end_time' },
            { data: 'start_cash', name: 'start_cash' },
            { data: 'actual_end_cash', name: 'actual_end_cash' },
            { data: 'status_badge', name: 'status', orderable: false, class: 'pe-4' }
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

    $('#btn-open-shift').on('click', function() {
        $('#openShiftModal').modal('show');
    });

    $('#btn-close-shift').on('click', function() {
        $('#closeShiftModal').modal('show');
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
});
</script>
@endpush