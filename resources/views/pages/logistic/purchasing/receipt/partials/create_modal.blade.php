<x-modal id="createModal" title="Penerimaan Barang Baru (GR)" size="xl">
    <form id="form-create-receipt">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <x-form.label required>Gudang Penerima</x-form.label>
                <x-form.select name="warehouse_id" required>
                    <option value="">Pilih Gudang</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Nomor Purchase Order (PO)</x-form.label>
                <x-form.select name="purchase_order_id" required>
                    <option value="">Pilih Dokumen PO</option>
                    @foreach($purchaseOrders as $po)
                        <option value="{{ $po->id }}">{{ $po->po_number }} ({{ $po->supplier->name ?? '-' }})</option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6">
                <x-form.label required>Tanggal Penerimaan</x-form.label>
                <x-form.input type="date" name="date" required value="{{ date('Y-m-d') }}" />
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-12">
                <x-form.label>Catatan Penerimaan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Tuliskan catatan kondisi barang atau penerimaan (Opsional)"></x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary rounded px-2 py-1 me-2">
                <i class="bi bi-box-seam text-white" style="font-size: 14px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Item Barang Diterima</h6>
            <div class="ms-auto">
                <span class="text-muted" style="font-size: 12px;">Pilih PO terlebih dahulu untuk memuat item pesanan</span>
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
                <tbody class="border-top-0 text-heading" id="po-items-container">
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada item pesanan yang dimuat.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-save">Simpan Penerimaan</x-button>
        </div>
    </form>
</x-modal>
