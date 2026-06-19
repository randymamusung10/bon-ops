<x-modal id="editModal" title="Edit Faktur Supplier (A/P Invoice)" size="xl">
    <form id="form-edit-invoice" data-uuid="{{ $invoice->uuid }}">
        @csrf
        @method('PUT')
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <x-form.label required>Dokumen Penerimaan (GR)</x-form.label>
                <select name="goods_receipt_id_disabled" class="form-select" disabled>
                    <option value="{{ $invoice->goods_receipt_id }}">
                        {{ $invoice->goodsReceipt->document_number ?? '-' }} (PO: {{ $invoice->purchaseOrder->po_number ?? '-' }}) — {{ $invoice->supplier->name ?? '-' }}
                    </option>
                </select>
                <input type="hidden" name="goods_receipt_id" value="{{ $invoice->goods_receipt_id }}">
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Nomor Faktur (Dari Supplier)</x-form.label>
                <x-form.input type="text" name="supplier_invoice_number" required value="{{ $invoice->supplier_invoice_number }}" placeholder="Contoh: INV-SPL-2026/001" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Tanggal Faktur</x-form.label>
                <x-form.input type="date" name="date" required value="{{ \Carbon\Carbon::parse($invoice->date)->format('Y-m-d') }}" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Jatuh Tempo (Due Date)</x-form.label>
                <x-form.input type="date" name="due_date" required value="{{ \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') }}" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-12">
                <x-form.label>Catatan Tambahan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Tuliskan catatan faktur (Opsional)">{{ $invoice->notes }}</x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-receipt text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Rincian Item Tagihan</h6>
        </div>

        <div class="table-responsive rounded-4 overflow-hidden mb-4" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="5%" class="text-center py-3 ps-3 border-0">No</th>
                        <th width="35%" class="py-3 border-0">Produk</th>
                        <th width="10%" class="text-center py-3 border-0">Satuan</th>
                        <th width="15%" class="text-end py-3 border-0">Qty Diterima</th>
                        <th width="18%" class="text-end py-3 border-0">Harga Satuan (PO)</th>
                        <th width="17%" class="text-end py-3 pe-3 border-0">Total Harga</th>
                    </tr>
                </thead>
                <tbody id="invoice-items-container-edit" class="border-top-0 text-heading">
                    @foreach($invoice->items as $index => $item)
                        <tr>
                            <td class="text-center py-2 ps-3 border-0 border-bottom border-light">{{ $index + 1 }}</td>
                            <td class="py-2 border-0 border-bottom border-light">
                                {{ $item->product->name ?? '-' }}
                                <input type="hidden" name="items[{{ $index }}][goods_receipt_item_id]" value="{{ $item->goods_receipt_item_id }}">
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                <input type="hidden" name="items[{{ $index }}][unit_id]" value="{{ $item->unit_id }}">
                            </td>
                            <td class="py-2 text-center border-0 border-bottom border-light">{{ $item->unit->name ?? '-' }}</td>
                            <td class="py-2 border-0 border-bottom border-light" style="width: 120px;">
                                <input type="text" class="form-control custom-form-control text-end qty-input format-number" name="items[{{ $index }}][quantity]" value="{{ number_format($item->quantity, 2, ',', '.') }}" required>
                            </td>
                            <td class="py-2 border-0 border-bottom border-light" style="width: 150px;">
                                <input type="text" class="form-control custom-form-control text-end price-input format-rupiah" name="items[{{ $index }}][unit_price]" value="{{ number_format($item->unit_price, 2, ',', '.') }}" required>
                            </td>
                            <td class="py-2 text-end text-primary fw-bold border-0 border-bottom border-light item-total-display" style="width: 150px;">
                                {{ number_format($item->total_price, 2, ',', '.') }}
                            </td>
                            <td class="py-2 pe-3 border-0 border-bottom border-light d-none">
                                <input type="hidden" class="item-total-price" name="items[{{ $index }}][total_price]" value="{{ $item->total_price }}">
                                <input type="text" class="form-control custom-form-control" name="items[{{ $index }}][notes]" value="{{ $item->notes }}" placeholder="Catatan">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary / Kalkulasi Total --}}
        <div class="row">
            <div class="col-md-5 offset-md-7">
                <div class="p-3 rounded-4" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Subtotal</span>
                        <span class="fw-semibold text-heading" style="font-size: 13px;" id="subtotal-display-edit">{{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
                        <input type="hidden" name="subtotal" id="subtotal-edit" value="{{ $invoice->subtotal }}">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="text-muted fw-medium d-block" style="font-size: 12px;">Pajak / Tax (Rp)</span>
                            <small class="text-muted" style="font-size: 10px;">Dalam nominal Rupiah</small>
                        </div>
                        <input type="text" class="form-control custom-form-control text-end format-rupiah" name="tax_amount" id="tax-amount-edit" value="{{ number_format($invoice->tax_amount, 0, ',', '.') }}" style="width: 150px;">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                        <div>
                            <span class="text-muted fw-medium d-block" style="font-size: 12px;">Diskon (Rp)</span>
                            <small class="text-muted" style="font-size: 10px;">Dalam nominal Rupiah</small>
                        </div>
                        <input type="text" class="form-control custom-form-control text-end format-rupiah" name="discount_amount" id="discount-amount-edit" value="{{ number_format($invoice->discount_amount, 0, ',', '.') }}" style="width: 150px;">
                    </div>
                    <div style="border-top: 1px solid rgba(226, 232, 240, 0.2); padding-top: 10px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold" style="font-size: 13px;">Grand Total</span>
                            <span class="fw-bold text-primary" style="font-size: 16px;" id="grand-total-display-edit">{{ number_format($invoice->grand_total, 2, ',', '.') }}</span>
                            <input type="hidden" name="grand_total" id="grand-total-edit" value="{{ $invoice->grand_total }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-save">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>

<script>
$(document).ready(function() {
    window.AppFormat.init();
});
</script>
