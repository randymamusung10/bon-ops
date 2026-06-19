<x-modal id="editModal" title="Edit Pembayaran Supplier" size="lg">
    <form id="form-edit-payment" data-uuid="{{ $payment->uuid }}">
        @csrf
        @method('PUT')
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <x-form.label required>Faktur yang Dibayar</x-form.label>
                <x-form.select name="supplier_invoice_id" required>
                    <option value="">Pilih Faktur (Invoice) yang akan dilunasi</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" data-grand-total="{{ $inv->remaining_amount }}" {{ $payment->supplier_invoice_id == $inv->id ? 'selected' : '' }}>
                            {{ $inv->document_number }} — {{ $inv->supplier->name ?? '-' }} — Sisa: Rp {{ number_format($inv->remaining_amount, 2, ',', '.') }} (Total: Rp {{ number_format($inv->grand_total, 2, ',', '.') }})
                        </option>
                    @endforeach
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>

            @php
                $selectedInvoice = $invoices->firstWhere('id', $payment->supplier_invoice_id);
                $selectedRemaining = $selectedInvoice ? $selectedInvoice->remaining_amount : 0;
            @endphp
            <div class="col-12">
                <div class="p-3 rounded-4" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Sisa Tagihan Faktur</span>
                        <span class="fw-bold text-primary" style="font-size: 15px;" id="invoice-amount-display-edit">
                            Rp {{ number_format($selectedRemaining, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Tanggal Pembayaran</x-form.label>
                <x-form.input type="date" name="payment_date" required value="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Metode Pembayaran</x-form.label>
                <x-form.select name="payment_method" required>
                    <option value="transfer" {{ $payment->payment_method === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="cash" {{ $payment->payment_method === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="giro" {{ $payment->payment_method === 'giro' ? 'selected' : '' }}>Giro</option>
                    <option value="cheque" {{ $payment->payment_method === 'cheque' ? 'selected' : '' }}>Cek</option>
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label>Nama Bank</x-form.label>
                <x-form.input type="text" name="bank_name" value="{{ $payment->bank_name }}" placeholder="Contoh: BCA, Mandiri, BNI" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label>No. Rekening</x-form.label>
                <x-form.input type="text" name="bank_account_number" value="{{ $payment->bank_account_number }}" placeholder="Nomor rekening tujuan" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label>No. Referensi / No. Bukti</x-form.label>
                <x-form.input type="text" name="bank_reference" value="{{ $payment->bank_reference }}" placeholder="No. transfer / No. cek / No. giro" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Jumlah Dibayar (Rp)</x-form.label>
                <x-form.input type="text" class="format-rupiah" name="payment_amount" required value="{{ number_format($payment->payment_amount, 2, ',', '.') }}" placeholder="0" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Catatan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Catatan pembayaran (Opsional)">{{ $payment->notes }}</x-form.textarea>
                <div class="invalid-feedback"></div>
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
    let modal = $('#editModal');
    modal.find('select[name="supplier_invoice_id"]').select2({
        theme: 'bootstrap-5',
        dropdownParent: modal,
        width: '100%'
    });
    modal.find('select[name="payment_method"]').select2({
        theme: 'bootstrap-5',
        dropdownParent: modal,
        width: '100%',
        minimumResultsForSearch: Infinity
    });

    window.AppFormat.init();
});
</script>
