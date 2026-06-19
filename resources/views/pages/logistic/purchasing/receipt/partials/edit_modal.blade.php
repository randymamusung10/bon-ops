<x-modal id="editModal" title="Edit Penerimaan Barang (Draft)" size="xl">
    <form id="form-edit-receipt" action="{{ route('logistic.purchasing.receipt.update', $receipt->uuid) }}">
        @csrf
        @method('PUT')
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <x-form.label required>Gudang Penerima</x-form.label>
                <x-form.select name="warehouse_id" required>
                    <option value="">Pilih Gudang</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ $receipt->warehouse_id == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Nomor Purchase Order (PO)</x-form.label>
                <x-form.select name="purchase_order_id" required>
                    <option value="">Pilih Dokumen PO</option>
                    @foreach($purchaseOrders as $po)
                        <option value="{{ $po->id }}" {{ $receipt->purchase_order_id == $po->id ? 'selected' : '' }}>{{ $po->po_number }} ({{ $po->supplier->name ?? '-' }})</option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Tanggal Penerimaan</x-form.label>
                <x-form.input type="date" name="date" required value="{{ $receipt->date }}" />
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-12">
                <x-form.label>Catatan Penerimaan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Tuliskan catatan kondisi barang atau penerimaan (Opsional)">{{ $receipt->notes }}</x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-box-seam text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Item Barang Diterima</h6>
            <div class="ms-auto">
                <span class="text-muted" style="font-size: 12px;">Data akan dimuat ulang jika PO diubah</span>
            </div>
        </div>

        <div class="table-responsive rounded-4 overflow-hidden mb-4" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="5%" class="text-center py-3 ps-3 border-0">No</th>
                        <th width="35%" class="py-3 border-0">Produk</th>
                        <th width="10%" class="text-center py-3 border-0">Satuan</th>
                        <th width="15%" class="text-end py-3 border-0">Qty Pesanan</th>
                        <th width="15%" class="text-end py-3 border-0">Qty Diterima</th>
                        <th width="20%" class="py-3 pe-3 border-0">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 text-heading" id="po-edit-items-container">
                    @forelse($receipt->items as $index => $item)
                        <tr>
                            <td class="text-center py-2 ps-3 border-0 border-bottom border-light">{{ $index + 1 }}</td>
                            <td class="py-2 border-0 border-bottom border-light">
                                {{ $item->product->name ?? '-' }}
                                <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->purchase_order_item_id }}">
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                <input type="hidden" name="items[{{ $index }}][unit_id]" value="{{ $item->unit_id }}">
                            </td>
                            <td class="py-2 text-center border-0 border-bottom border-light">{{ $item->unit->name ?? '-' }}</td>
                            <td class="py-2 text-end text-primary fw-bold border-0 border-bottom border-light">
                                {{ number_format($item->ordered_qty, 0, ',', '.') }}
                                <input type="hidden" name="items[{{ $index }}][ordered_qty]" value="{{ $item->ordered_qty }}">
                            </td>
                            <td class="py-2 border-0 border-bottom border-light">
                                <input type="text" class="form-control custom-form-control text-end format-number qty-input" name="items[{{ $index }}][received_qty]" value="{{ $item->received_qty }}" required>
                            </td>
                            <td class="py-2 pe-3 border-0 border-bottom border-light">
                                <input type="text" class="form-control custom-form-control" name="items[{{ $index }}][notes]" placeholder="Catatan" value="{{ $item->notes }}">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada item pesanan yang dimuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-save">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>

<script>
$(document).ready(function() {
    // Fetch PO Items when PO is selected (in edit modal, update the container)
    $('#form-edit-receipt').on('change', 'select[name="purchase_order_id"]', function() {
        let poId = $(this).val();
        let container = $('#po-edit-items-container');
        container.empty();

        if (!poId) return;

        container.html('<tr><td colspan="6" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</td></tr>');

        $.ajax({
            url: "{{ url('logistic/purchasing/receipt/get-po') }}/" + poId,
            type: 'GET',
            success: function(res) {
                if(res.items && res.items.length > 0) {
                    let html = '';
                    res.items.forEach(function(item, index) {
                        html += `
                        <tr>
                            <td class="text-center py-2 ps-3 border-0 border-bottom border-light">${index + 1}</td>
                            <td class="py-2 border-0 border-bottom border-light">
                                ${item.product.name}
                                <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">
                                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                <input type="hidden" name="items[${index}][unit_id]" value="${item.unit_id}">
                            </td>
                            <td class="py-2 text-center border-0 border-bottom border-light">${item.unit.name}</td>
                            <td class="py-2 text-end text-primary fw-bold border-0 border-bottom border-light">
                                ${parseFloat(item.quantity).toLocaleString('id-ID')}
                                <input type="hidden" name="items[${index}][ordered_qty]" value="${item.quantity}">
                            </td>
                            <td class="py-2 border-0 border-bottom border-light">
                                <input type="text" class="form-control custom-form-control text-end format-number qty-input" name="items[${index}][received_qty]" value="${item.quantity}" required>
                            </td>
                            <td class="py-2 pe-3 border-0 border-bottom border-light">
                                <input type="text" class="form-control custom-form-control" name="items[${index}][notes]" placeholder="Catatan">
                            </td>
                        </tr>
                        `;
                    });
                    container.html(html);
                    window.AppFormat.init();
                } else {
                    container.html('<tr><td colspan="6" class="text-center py-4 text-danger">Tidak ada item di dalam PO ini.</td></tr>');
                }
            },
            error: function() {
                container.html('<tr><td colspan="6" class="text-center py-4 text-danger">Gagal mengambil data PO.</td></tr>');
            }
        });
    });
});
</script>
