<x-modal id="createModal" title="Buat Pembayaran Supplier" size="lg">
    <form id="form-create-payment" enctype="multipart/form-data">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <x-form.label required>Faktur yang Dibayar</x-form.label>
                <x-form.select name="supplier_invoice_id" required>
                    <option value="">Pilih Faktur (Invoice) yang akan dilunasi</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" data-grand-total="{{ $inv->remaining_amount }}" data-pending="{{ $inv->total_pending ?? 0 }}">
                            {{ $inv->document_number }} — {{ $inv->supplier->name ?? '-' }} — Sisa: Rp {{ number_format($inv->remaining_amount, 2, ',', '.') }} (Total: Rp {{ number_format($inv->grand_total, 2, ',', '.') }})
                        </option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <div class="p-3 rounded-4" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Sisa Tagihan Faktur</span>
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
                <div class="form-text mt-1" style="font-size: 11px; display: none;">Sisa pelunasan setelah pembayaran: <span class="fw-bold preview-remaining-create">Rp 0</span></div>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Catatan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Catatan pembayaran (Opsional)"></x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Bukti Pembayaran (Opsional)</x-form.label>
                <div class="position-relative p-3 text-center rounded-3 bg-light hover-shadow-sm" style="border: 1px dashed #cbd5e1; transition: all 0.2s;" id="dropzone-payment-create">
                    <input type="file" id="payment_attachment_create" name="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-cloud-arrow-up text-primary mb-1" style="font-size: 1.2rem;"></i>
                        <span class="text-muted fw-medium" style="font-size: 11px;" id="file-name-payment-create">Pilih atau Letakkan File</span>
                        <span class="text-muted mt-1" style="font-size: 10px;">Maks 5MB (PDF/JPG/PNG)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="submit" variant="primary" size="sm" icon="bi-save">Simpan Draft Pembayaran</x-button>
        </div>
    </form>
</x-modal>

<script>
    $('#payment_attachment_create').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#file-name-payment-create').text(fileName).removeClass('text-muted').addClass('text-primary');
        } else {
            $('#file-name-payment-create').text('Pilih atau Letakkan File').removeClass('text-primary').addClass('text-muted');
        }
    });
</script>
