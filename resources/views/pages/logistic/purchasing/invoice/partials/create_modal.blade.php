<x-modal id="createModal" title="Buat Faktur Supplier (A/P Invoice)" size="xl">
    <form id="form-create-invoice">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <div class="p-3 rounded-4" style="background: rgba(14, 165, 233, 0.05); border: 1px dashed rgba(14, 165, 233, 0.3);">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                            <i class="bi bi-info-circle-fill" style="font-size: 14px;"></i>
                        </div>
                        <div>
                            <p class="mb-1 text-muted fw-medium" style="font-size: 12px;">Informasi Penting:</p>
                            <p class="mb-0 text-heading" style="font-size: 13px; line-height: 1.6;">Faktur hanya dapat dibuat dari <strong>Goods Receipt (GR)</strong> yang sudah berstatus <em>Posted</em>. Harga diambil otomatis dari Purchase Order terkait (3-Way Matching).</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Dokumen Penerimaan (GR)</x-form.label>
                <x-form.select name="goods_receipt_id" required>
                    <option value="">Pilih Dokumen GR</option>
                    @foreach($goodsReceipts as $gr)
                        <option value="{{ $gr->id }}">{{ $gr->document_number }} (PO: {{ $gr->purchaseOrder->po_number ?? '-' }}) — {{ $gr->supplier->name ?? '-' }}</option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Nomor Faktur (Dari Supplier)</x-form.label>
                <x-form.input type="text" name="supplier_invoice_number" required placeholder="Contoh: INV-SPL-2026/001" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Tanggal Faktur</x-form.label>
                <x-form.input type="date" name="date" required value="{{ date('Y-m-d') }}" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Jatuh Tempo (Due Date)</x-form.label>
                <x-form.input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+30 days')) }}" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-12">
                <x-form.label>Catatan Tambahan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Tuliskan catatan faktur (Opsional)"></x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-receipt text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Rincian Item Tagihan</h6>
            <div class="ms-auto">
                <span class="text-muted" style="font-size: 12px;">Pilih GR terlebih dahulu untuk memuat item dan harga dari PO</span>
            </div>
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
                <tbody id="invoice-items-container" class="border-top-0 text-heading">
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted" style="font-size: 13px;">Belum ada item yang dimuat. Pilih dokumen GR terlebih dahulu.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Summary / Kalkulasi Total --}}
        <div class="row">
            <div class="col-md-5 offset-md-7">
                <div class="p-3 rounded-4" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Subtotal</span>
                        <span class="fw-semibold text-heading" style="font-size: 13px;" id="subtotal-display">0</span>
                        <input type="hidden" name="subtotal" id="subtotal" value="0">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="text-muted fw-medium d-block" style="font-size: 12px;">Pajak / Tax (Rp)</span>
                            <small class="text-muted" style="font-size: 10px;">Dalam nominal Rupiah</small>
                        </div>
                        <input type="text" class="form-control custom-form-control text-end format-rupiah" name="tax_amount" id="tax-amount" value="0" style="width: 150px;">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                        <div>
                            <span class="text-muted fw-medium d-block" style="font-size: 12px;">Diskon (Rp)</span>
                            <small class="text-muted" style="font-size: 10px;">Dalam nominal Rupiah</small>
                        </div>
                        <input type="text" class="form-control custom-form-control text-end format-rupiah" name="discount_amount" id="discount-amount" value="0" style="width: 150px;">
                    </div>
                    <div style="border-top: 1px solid rgba(226, 232, 240, 0.2); padding-top: 10px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold" style="font-size: 13px;">Grand Total</span>
                            <span class="fw-bold text-primary" style="font-size: 16px;" id="grand-total-display">0</span>
                            <input type="hidden" name="grand_total" id="grand-total" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-save">Simpan Draft Faktur</x-button>
        </div>
    </form>
</x-modal>
