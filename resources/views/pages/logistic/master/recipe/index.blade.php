@extends('layouts.app')

@section('page_title', 'Resep Produk')
@section('page_description', 'Kelola daftar formula/resep bahan baku untuk produk menu Anda.')
@section('page_actions')
    <x-button id="btn-add-recipe" variant="primary" size="sm" icon="bi-plus-lg">
        Tambah Resep
    </x-button>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Data Table Card -->
    <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
        <div class="table-responsive">
            <table id="recipes-table" class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                    <tr style="font-size: 13px; color: var(--text-muted); letter-spacing: 0.2px;">
                        <th class="ps-4 py-3" style="width: 5%;">No</th>
                        <th class="py-3" style="width: 15%;">Kode</th>
                        <th class="py-3" style="width: 25%;">Nama Resep</th>
                        <th class="py-3" style="width: 20%;">Menu Produk</th>
                        <th class="py-3" style="width: 15%;">Total Cost (HPP)</th>
                        <th class="py-3" style="width: 10%;">Status</th>
                        <th class="pe-4 text-end py-3" style="width: 10%;">Aksi</th>
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
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table = $('#recipes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('logistic.master.recipe.data') }}"
        },
        dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"lf>t<"d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4"ip>',
        columns: [
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                class: 'ps-4 text-muted',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'code', name: 'code', class: 'fw-mono text-heading' },
            { data: 'name', name: 'name', class: 'fw-semibold text-heading' },
            { data: 'product.name', name: 'product.name', class: 'text-heading' },
            { data: 'total_cost', name: 'total_cost', class: 'text-end text-heading fw-semibold' },
            { 
                data: 'status', 
                name: 'status', 
                class: 'text-center',
                render: function(data) {
                    if (data === 'active') {
                        return '<span class="badge bg-success-subtle text-success px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="bi bi-check-circle me-1"></i> Aktif</span>';
                    } else if (data === 'draft') {
                        return '<span class="badge bg-warning-subtle text-warning px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="bi bi-info-circle me-1"></i> Draft</span>';
                    } else {
                        return '<span class="badge bg-danger-subtle text-danger px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;"><i class="bi bi-x-circle me-1"></i> Nonaktif</span>';
                    }
                }
            },
            { 
                data: 'uuid', 
                name: 'uuid', 
                orderable: false, 
                searchable: false, 
                class: 'pe-4 text-end text-nowrap',
                render: function(data, type, row) {
                    return '<div class="d-inline-flex gap-2">' +
                        '<button class="btn-icon-modern text-info btn-show-recipe" data-uuid="' + data + '" title="Detail" style="background: rgba(14, 165, 233, 0.12);">' +
                        '<i class="bi bi-eye"></i>' +
                        '</button>' +
                        '<button class="btn-icon-modern text-primary btn-edit-recipe" data-uuid="' + data + '" title="Edit" style="background: color-mix(in srgb, var(--primary-accent) 12%, transparent);">' +
                        '<i class="bi bi-pencil-square"></i>' +
                        '</button>' +
                        '<button class="btn-icon-modern text-danger btn-delete-recipe" data-uuid="' + data + '" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">' +
                        '<i class="bi bi-trash3"></i>' +
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
            searchPlaceholder: 'Cari secara global...',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 data',
            lengthMenu: 'Tampilkan _MENU_ entri',
            paginate: { previous: 'Prev', next: 'Next' }
        }
    });

    $('.dataTables_length select').select2({ theme: 'bootstrap-5', width: '75px', minimumResultsForSearch: -1 });

    // Initialize decimal input masks
    function initDecimalMask() {
        $('.mask-decimal').off('blur').on('blur', function() {
            var val = $(this).val();
            if (val === '') return;
            val = val.replace(/[^0-9,\.]/g, '');
            $(this).val(val);
        });
    }

    // Modal show trigger
    $('#btn-add-recipe').on('click', function() {
        ERPLoader.loadModal("{{ route('logistic.master.recipe.create') }}", '#addRecipeModal', {
            title: 'Tambah Resep Baru',
            errorMessage: 'Gagal memuat form tambah resep.',
            onSuccess: function(modal) {
                $('.select2-modal').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });
                
                // Reset index and add row
                rowIndex = 0;
                addRow(modal);
                
                $('#btn-add-row').off('click').on('click', function() {
                    addRow(modal);
                });
            }
        });
    });

    var rowIndex = 0;
    function addRow(modal) {
        var row = `
            <tr id="row-${rowIndex}">
                <td>
                    <select class="form-select select2-ingredient" name="items[${rowIndex}][product_id]" required style="width: 100%;">
                        <option value="">Pilih Bahan Baku</option>
                        @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}" data-unit-id="{{ $ing->unit_id }}">{{ $ing->name }} ({{ $ing->code }})</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control text-end format-number" name="items[${rowIndex}][quantity]" placeholder="0" required>
                </td>
                <td>
                    <select class="form-select select2-unit" name="items[${rowIndex}][unit_id]" required style="width: 100%;">
                        <option value="">Satuan</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn-icon-modern text-danger btn-remove-row mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        var $row = $(row);
        $('#recipe-items-table tbody').append($row);

        // Init select2 for new row
        $row.find('.select2-ingredient').select2({
            theme: 'bootstrap-5',
            dropdownParent: modal,
            width: '100%'
        }).on('change', function() {
            var selected = $(this).find(':selected');
            var unitId = selected.data('unit-id');
            if (unitId) {
                $(this).closest('tr').find('.select2-unit').val(unitId).trigger('change');
            }
        });

        $row.find('.select2-unit').select2({
            theme: 'bootstrap-5',
            dropdownParent: modal,
            width: '100%'
        });

        initDecimalMask();
        rowIndex++;
    }

    $(document).on('click', '.btn-remove-row', function() {
        if ($('#recipe-items-table tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            AppAlert.warning('Peringatan', 'Minimal harus ada 1 bahan baku di dalam resep.');
        }
    });

    // Store Recipe
    $(document).on('submit', '#add-recipe-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');
        $('#add-items-error').html('');

        $.ajax({
            url: "{{ route('logistic.master.recipe.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addRecipeModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Resep Disimpan!', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        if (key.indexOf('items') !== -1) {
                            $('#add-items-error').html(val[0]);
                        } else {
                            var idKey = key.replace(/\./g, '_');
                            $('#add-' + idKey).addClass('is-invalid');
                            $('#add-' + idKey + '-error').html(val[0]);
                        }
                    });
                } else {
                    AppAlert.error('Error!', 'Terjadi kesalahan saat menyimpan resep.');
                }
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    // Show Detail Recipe
    $(document).on('click', '.btn-show-recipe', function() {
        var uuid = $(this).data('uuid');
        var url = "{{ url('logistic/master/recipe') }}/" + uuid;
        ERPLoader.loadModal(url, '#showRecipeModal', {
            title: 'Detail Resep Produk',
            errorMessage: 'Gagal mengambil data detail resep.'
        });
    });

    // Edit Recipe Modal Load
    $(document).on('click', '.btn-edit-recipe', function() {
        var uuid = $(this).data('uuid');
        var url = "{{ url('logistic/master/recipe') }}/" + uuid + "/edit";
        ERPLoader.loadModal(url, '#editRecipeModal', {
            title: 'Edit Resep Produk',
            errorMessage: 'Gagal mengambil data resep.',
            onSuccess: function(modal) {
                $('.select2-modal-edit').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: modal,
                    width: '100%'
                });

                // Init dynamic rows edit
                var editRowIndex = $('.edit-row-item').length;
                
                $('#btn-edit-add-row').on('click', function() {
                    var row = `
                        <tr class="edit-row-item" id="edit-row-${editRowIndex}">
                            <td>
                                <select class="form-select select2-edit-ingredient" name="items[${editRowIndex}][product_id]" required style="width: 100%;">
                                    <option value="">Pilih Bahan Baku</option>
                                    @foreach($ingredients as $ing)
                                        <option value="{{ $ing->id }}" data-unit-id="{{ $ing->unit_id }}">{{ $ing->name }} ({{ $ing->code }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control text-end format-number" name="items[${editRowIndex}][quantity]" placeholder="0" required>
                            </td>
                            <td>
                                <select class="form-select select2-edit-unit" name="items[${editRowIndex}][unit_id]" required style="width: 100%;">
                                    <option value="">Satuan</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-icon-modern text-danger btn-remove-edit-row mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    var $row = $(row);
                    $('#edit-recipe-items-table tbody').append($row);

                    $row.find('.select2-edit-ingredient').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: modal,
                        width: '100%'
                    }).on('change', function() {
                        var selected = $(this).find(':selected');
                        var unitId = selected.data('unit-id');
                        if (unitId) {
                            $(this).closest('tr').find('.select2-edit-unit').val(unitId).trigger('change');
                        }
                    });

                    $row.find('.select2-edit-unit').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: modal,
                        width: '100%'
                    });

                    initDecimalMask();
                    editRowIndex++;
                });

                $(document).on('click', '.btn-remove-edit-row', function() {
                    if ($('#edit-recipe-items-table tbody tr').length > 1) {
                        $(this).closest('tr').remove();
                    } else {
                        AppAlert.warning('Peringatan', 'Minimal harus ada 1 bahan baku di dalam resep.');
                    }
                });

                initDecimalMask();
            }
        });
    });

    // Update Recipe
    $(document).on('submit', '#edit-recipe-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var uuid = $('#edit-uuid').val();
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');
        $('#edit-items-error').html('');

        $.ajax({
            url: "{{ url('logistic/master/recipe') }}/" + uuid,
            type: "POST", // method spoofing via _method=PUT in form
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editRecipeModal').modal('hide');
                    table.ajax.reload();
                    AppAlert.success('Resep Diperbarui!', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        if (key.indexOf('items') !== -1) {
                            $('#edit-items-error').html(val[0]);
                        } else {
                            var idKey = key.replace(/\./g, '_');
                            $('#edit-' + idKey).addClass('is-invalid');
                            $('#edit-' + idKey + '-error').html(val[0]);
                        }
                    });
                } else {
                    AppAlert.error('Error!', 'Terjadi kesalahan saat memperbarui resep.');
                }
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    // Delete Recipe
    $(document).on('click', '.btn-delete-recipe', function() {
        var uuid = $(this).data('uuid');
        
        AppAlert.confirmDelete('Hapus Resep?', 'Apakah Anda yakin ingin menghapus resep ini?').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('logistic/master/recipe') }}/" + uuid,
                    type: "DELETE",
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            AppAlert.success('Data Dihapus!', response.message);
                        }
                    },
                    error: function() {
                        AppAlert.error('Gagal!', 'Gagal menghapus resep.');
                    }
                });
            }
        });
    });
});
</script>
@endpush