<x-modal id="editModal" title="Edit Purchase Request (Draft)" size="xl">
    <form id="form-edit-pr">
        @csrf
        @method('PUT')
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-form.label required>Cabang Peminta</x-form.label>
                <x-form.select name="branch_id" required>
                    <option value="">Pilih Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $requestData->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <x-form.label required>Tanggal PR</x-form.label>
                <x-form.input type="date" name="date" required value="{{ $requestData->date }}" />
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-4">
                <x-form.label>Estimasi Dibutuhkan</x-form.label>
                <x-form.input type="date" name="expected_date" value="{{ $requestData->expected_date }}" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Catatan / Alasan Permintaan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Tuliskan alasan permintaan (opsional)">{{ $requestData->notes }}</x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3 mt-2">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Daftar Item Permintaan</h6>
        </div>

        <div class="table-responsive rounded-4 overflow-hidden mb-3" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table table-hover align-middle mb-0" id="table-edit-items" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="40%" class="py-3 ps-4 border-0">Produk</th>
                        <th width="20%" class="py-3 border-0">Satuan</th>
                        <th width="20%" class="py-3 border-0 text-end">Kuantitas</th>
                        <th width="15%" class="py-3 border-0">Keterangan</th>
                        <th width="5%" class="text-center py-3 pe-4 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 text-heading">
                    @foreach($requestData->items as $index => $item)
                        <tr>
                            <td class="py-3 ps-4">
                                <x-form.select class="select2-product" name="items[{{ $index }}][product_id]" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ $item->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </x-form.select>
                            </td>
                            <td class="py-3">
                                <x-form.select class="select2-unit" name="items[{{ $index }}][unit_id]" required>
                                    <option value="">-- Satuan --</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}" {{ $item->unit_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </x-form.select>
                            </td>
                            <td class="py-3">
                                <input type="text" class="form-control custom-form-control text-end qty-input format-number" name="items[{{ $index }}][quantity]" required placeholder="0" value="{{ $item->quantity }}">
                            </td>
                            <td class="py-3">
                                <input type="text" class="form-control custom-form-control" name="items[{{ $index }}][notes]" placeholder="(Opsional)" value="{{ $item->notes }}">
                            </td>
                            <td class="text-center py-3 pe-4">
                                <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <x-button type="button" variant="ghost-primary" size="sm" id="btn-add-edit-item" icon="bi-plus">Tambah Item</x-button>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-save">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>

<!-- Hidden Template for new row -->
<template id="pr-edit-item-template">
    <tr>
        <td class="py-3 ps-4">
            <x-form.select class="select2-product" name="items[__INDEX__][product_id]" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </x-form.select>
        </td>
        <td class="py-3">
            <x-form.select class="select2-unit" name="items[__INDEX__][unit_id]" required>
                <option value="">-- Satuan --</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </x-form.select>
        </td>
        <td class="py-3">
            <input type="text" class="form-control custom-form-control text-end qty-input format-number" name="items[__INDEX__][quantity]" required placeholder="0">
        </td>
        <td class="py-3">
            <input type="text" class="form-control custom-form-control" name="items[__INDEX__][notes]" placeholder="(Opsional)">
        </td>
        <td class="text-center py-3 pe-4">
            <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
$(document).ready(function() {
    let modal = $('#editModal');
    
    // Init main select2
    modal.find('select[name="branch_id"]').select2({
        theme: 'bootstrap-5',
        dropdownParent: modal,
        width: '100%'
    });

    // Init existing items
    modal.find('.select2-product, .select2-unit').select2({
        theme: 'bootstrap-5',
        dropdownParent: modal,
        width: '100%'
    });

    let itemIndex = {{ count($requestData->items) }};

    function addItemRow() {
        let template = $('#pr-edit-item-template').html();
        template = template.replace(/__INDEX__/g, itemIndex);
        $('#table-edit-items tbody').append(template);
        
        $('#table-edit-items tbody').find('select[name="items['+itemIndex+'][product_id]"], select[name="items['+itemIndex+'][unit_id]"]').select2({
            theme: 'bootstrap-5',
            dropdownParent: modal,
            width: '100%'
        });
        itemIndex++;
    }
    
    modal.find('#btn-add-edit-item').on('click', function() {
        addItemRow();
    });
    
    modal.find('#table-edit-items').on('click', '.btn-remove-item', function() {
        if($('#table-edit-items tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            AppAlert.error('Peringatan', 'Minimal harus ada 1 item produk.');
        }
    });

    // Handle form submit
    modal.find('#form-edit-pr').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: "{{ route('logistic.purchasing.request.update', $requestData->uuid) }}",
            type: "POST", // The @method('PUT') inside the form will handle the HTTP method override
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    modal.modal('hide');
                    if(typeof table !== 'undefined') table.ajax.reload();
                    AppAlert.success('Tersimpan!', response.message);
                }
            },
            error: function(xhr) {
                AppAlert.error('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });

    window.AppFormat.init();
});
</script>
