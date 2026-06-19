<x-modal id="createModal" title="Buat Pembayaran Supplier" size="lg">
    <form id="form-create-payment">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <x-form.label required>Faktur yang Dibayar</x-form.label>
                <x-form.select name="supplier_invoice_id" required>
                    <option value="">Pilih Faktur (Invoice) yang akan dilunasi</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" data-grand-total="{{ $inv->grand_total }}">
                            {{ $inv->document_number }} — {{ $inv->supplier->name ?? '-' }} — Rp {{ number_format($inv->grand_total, 2, ',', '.') }}
                        </option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <div class="p-3 rounded-4" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Total Tagihan Faktur</span>
                        <span class="fw-bold text-primary" style="font-size: 15px;" id="invoice-amount-display">—</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Tanggal Pembayaran</x-form.label>
                <x-form.input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Metode Pembayaran</x-form.label>
                <x-form.select name="payment_method" required>
                    <option value="transfer">Transfer Bank</option>
                    <option value="cash">Tunai (Cash)</option>
                    <option value="giro">Giro</option>
                    <option value="cheque">Cek</option>
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label>Nama Bank</x-form.label>
                <x-form.input type="text" name="bank_name" placeholder="Contoh: BCA, Mandiri, BNI" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label>No. Rekening</x-form.label>
                <x-form.input type="text" name="bank_account_number" placeholder="Nomor rekening tujuan" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label>No. Referensi / No. Bukti</x-form.label>
                <x-form.input type="text" name="bank_reference" placeholder="No. transfer / No. cek / No. giro" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Jumlah Dibayar (Rp)</x-form.label>
                <x-form.input type="text" class="format-rupiah" name="payment_amount" required placeholder="0" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Catatan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Catatan pembayaran (Opsional)"></x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-save">Simpan Draft Pembayaran</x-button>
        </div>
    </form>
</x-modal>
