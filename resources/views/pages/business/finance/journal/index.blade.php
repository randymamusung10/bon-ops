@extends('layouts.app')

@section('page_title', 'Jurnal Umum')
@section('page_description', 'Kelola entri jurnal umum secara manual dan otomatis.')
@section('page_actions')
    <x-button id="btn-add-journal" variant="primary" size="sm" icon="bi-plus-lg">
        Buat Jurnal Baru
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="journal-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Nomor Jurnal</th>
                        <th class="py-3">Keterangan</th>
                        <th class="py-3 text-end">Total Nilai</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
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

@push('modals')
<div id="modal-container"></div>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table = $('#journal-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('business.finance.journal.data') }}",
        order: [[1, 'desc']],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, class: 'ps-4 text-muted' },
            { data: 'date', name: 'date' },
            { data: 'journal_number', name: 'journal_number', class: 'fw-semibold text-heading' },
            { data: 'notes', name: 'notes', orderable: false },
            { data: 'total_debit', name: 'total_debit', class: 'text-end fw-semibold' },
            { data: 'status_badge', name: 'status', class: 'text-center', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'pe-4 text-end text-nowrap', render: function(data, type, row) {
                let uuid = row.uuid;
                let actions = '<div class="d-inline-flex gap-2">' +
                    '<button class="btn-icon-modern text-info show-btn" href="{{ url("business/finance/journal") }}/'+uuid+'" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
                    '<i class="bi bi-eye"></i>' +
                    '</button>';
                if (row.status === 'draft') {
                    actions += '<button class="btn-icon-modern text-warning edit-btn" href="{{ url("business/finance/journal") }}/'+uuid+'/edit" title="Edit" style="background: rgba(245, 158, 11, 0.12);">' +
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
            search: '_INPUT_',
            searchPlaceholder: 'Cari jurnal...',
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

    $('#btn-add-journal').on('click', function(e) {
        e.preventDefault();
        ERPLoader.loadModal("{{ route('business.finance.journal.create') }}", '#createModal', {
            title: 'Buat Jurnal Baru'
        });
    });

    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#editModal', {
            title: 'Edit Jurnal'
        });
    });

    $(document).on('click', '.show-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ERPLoader.loadModal(url, '#showModal', {
            title: 'Detail Jurnal'
        });
    });

    // Handle Workflow Buttons from Modal
    $(document).on('click', '.btn-action-journal', function() {
        let uuid = $(this).data('uuid');
        let action = $(this).data('action');
        let textMap = {
            'submit': 'mengajukan jurnal ini',
            'approve': 'menyetujui jurnal ini',
            'post': 'memposting jurnal ini ke Buku Besar'
        };

        AppAlert.confirm('Konfirmasi Aksi', 'Apakah Anda yakin ingin ' + textMap[action] + '?', 'Ya, Lanjutkan!').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/business/finance/journal/${uuid}/${action}`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        $('#showModal').modal('hide');
                        table.ajax.reload();
                        AppAlert.success('Berhasil!', res.message);
                    },
                    error: function(err) {
                        AppAlert.error('Gagal!', err.responseJSON?.message || 'Terjadi kesalahan sistem.');
                    }
                });
            }
        });
    });

    // Handle Delete
    $(document).on('click', '.delete-btn', function() {
        var uuid = $(this).data('uuid');
        
        AppAlert.confirmDelete('Hapus Jurnal?', 'Draft jurnal akan dihapus secara permanen.').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('business/finance/journal') }}/" + uuid,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        table.ajax.reload();
                        AppAlert.success('Terhapus!', response.message);
                    },
                    error: function(xhr) {
                        AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Gagal menghapus jurnal.');
                    }
                });
            }
        });
    });

    window.refreshTable = function() {
        table.ajax.reload(null, false);
    };
});
</script>
@endpush