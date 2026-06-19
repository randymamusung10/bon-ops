<x-modal id="createModal" title="Buat Purchase Order Baru" size="xl">
    <form id="form-create-po">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <x-form.label required>Cabang Pemesan</x-form.label>
                <x-form.select name="branch_id" required>
                    <option value="">Pilih Cabang</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Supplier</x-form.label>
                <x-form.select name="supplier_id" required>
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Tanggal PO</x-form.label>
                <x-form.input type="date" name="date" required value="{{ date('Y-m-d') }}" />
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6">
                <x-form.label>Estimasi Diterima</x-form.label>
                <x-form.input type="date" name="expected_date" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Catatan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Catatan PO (opsional)"></x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3 mt-2">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Daftar Item Pemesanan</h6>
        </div>

        <div class="table-responsive rounded-4 overflow-hidden mb-3" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table table-hover align-middle mb-0" id="table-items" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="35%" class="py-3 ps-4 border-0">Produk</th>
                        <th width="15%" class="py-3 border-0">Satuan</th>
                        <th width="15%" class="py-3 border-0 text-end">Kuantitas</th>
                        <th width="15%" class="py-3 border-0 text-end">Harga Satuan</th>
                        <th width="15%" class="py-3 border-0 text-end">Total</th>
                        <th width="5%" class="text-center py-3 pe-4 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 text-heading">
                    <!-- Items inserted here via JS -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold py-3">Total Keseluruhan</td>
                        <td class="text-end fw-bold py-3 text-primary" id="grand-total-text">Rp 0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div>
            <x-button type="button" variant="ghost-primary" size="sm" id="btn-add-item" icon="bi-plus">Tambah Item</x-button>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Draft</x-button>
        </div>
    </form>
</x-modal>

<!-- Hidden Template for new row -->
<template id="po-item-template">
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
            <input type="text" class="form-control custom-form-control text-end price-input format-rupiah" name="items[__INDEX__][unit_price]" required placeholder="0">
        </td>
        <td class="py-3 text-end fw-semibold total-text">
            Rp 0
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
    let modal = $('#createModal');
    
    // Init main select2
    modal.find('select[name="branch_id"], select[name="supplier_id"]').select2({
        theme: 'bootstrap-5',
        dropdownParent: modal,
        width: '100%'
    });

    let itemIndex = 0;
    
    function calculateRowTotal(tr) {
        let qty = parseFloat(window.AppFormat.unmaskNumber(tr.find('.qty-input').val())) || 0;
        let price = parseFloat(window.AppFormat.unmaskNumber(tr.find('.price-input').val())) || 0;
        let total = qty * price;
        
        tr.find('.total-text').text('Rp ' + total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        calculateGrandTotal();
    }
    
    function calculateGrandTotal() {
        let grandTotal = 0;
        $('#table-items tbody tr').each(function() {
            let qty = parseFloat(window.AppFormat.unmaskNumber($(this).find('.qty-input').val())) || 0;
            let price = parseFloat(window.AppFormat.unmaskNumber($(this).find('.price-input').val())) || 0;
            grandTotal += (qty * price);
        });
        $('#grand-total-text').text('Rp ' + grandTotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    function addItemRow() {
        let template = $('#po-item-template').html();
        template = template.replace(/__INDEX__/g, itemIndex);
        $('#table-items tbody').append(template);
        
        $('#table-items tbody').find('select[name="items['+itemIndex+'][product_id]"], select[name="items['+itemIndex+'][unit_id]"]').select2({
            theme: 'bootstrap-5',
            dropdownParent: modal,
            width: '100%'
        });
        itemIndex++;
    }
    
    // Add initial row
    addItemRow();
    
    modal.find('#btn-add-item').on('click', function() {
        addItemRow();
    });
    
    modal.find('#table-items').on('click', '.btn-remove-item', function() {
        if($('#table-items tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateGrandTotal();
        } else {
            AppAlert.error('Peringatan', 'Minimal harus ada 1 item produk.');
        }
    });

    modal.find('#table-items').on('input', '.qty-input, .price-input', function() {
        calculateRowTotal($(this).closest('tr'));
    });

    // Handle form submit
    modal.find('#form-create-po').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: "{{ route('logistic.purchasing.order.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    modal.modal('hide');
                    if(typeof refreshTable === 'function') refreshTable();
                    AppAlert.success('Tersimpan!', response.message);
                }
            },
            error: function(xhr) {
                AppAlert.error('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
            },
            complete: function() { submitBtn.prop('disabled', false); }
        });
    });
});
</script>
