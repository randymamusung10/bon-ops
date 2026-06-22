<x-modal id="editModal" title="Edit Pembayaran Supplier" size="lg">
    <form id="form-edit-payment" data-uuid="{{ $payment->uuid }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <x-form.label required>Faktur yang Dibayar</x-form.label>
                <x-form.select name="supplier_invoice_id" required>
                    <option value="">Pilih Faktur (Invoice) yang akan dilunasi</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" data-grand-total="{{ $inv->remaining_amount }}" data-pending="{{ $inv->total_pending ?? 0 }}" {{ $payment->supplier_invoice_id == $inv->id ? 'selected' : '' }}>
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
                <x-form.input type="text" class="format-rupiah" name="payment_amount" required placeholder="0" value="{{ number_format($payment->payment_amount, 0, ',', '.') }}" />
                <div class="form-text mt-1" style="font-size: 11px; display: none;">Sisa pelunasan setelah pembayaran: <span class="fw-bold preview-remaining-edit">Rp 0</span></div>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Catatan</x-form.label>
                <x-form.textarea name="notes" rows="2" placeholder="Catatan pembayaran (Opsional)">{{ $payment->notes }}</x-form.textarea>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Bukti Pembayaran</x-form.label>
                @if($payment->attachment_path)
                    <div class="d-flex align-items-center justify-content-between p-2 px-3 mb-2 rounded-3" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.2);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-text text-primary" style="font-size: 1.2rem;"></i>
                            <div>
                                <div class="fw-semibold text-primary" style="font-size: 12px;">Lampiran Saat Ini</div>
                                <div class="text-muted" style="font-size: 11px;">Tersimpan di server</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill btn-view-attachment px-3" style="font-size: 11px;" data-url="{{ asset($payment->attachment_path) }}">
                            <i class="bi bi-eye me-1"></i>Lihat
                        </button>
                    </div>
                @endif

                <div class="position-relative p-3 text-center rounded-3 bg-light hover-shadow-sm" style="border: 1px dashed #cbd5e1; transition: all 0.2s;" id="dropzone-payment-edit">
                    <input type="file" id="payment_attachment_edit" name="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-cloud-arrow-up text-muted mb-1" style="font-size: 1.2rem;"></i>
                        <span class="text-muted fw-medium" style="font-size: 11px;" id="file-name-payment-edit">
                            {{ $payment->attachment_path ? 'Pilih file baru untuk mengganti lampiran di atas' : 'Pilih atau Letakkan File Bukti Pembayaran (Opsional)' }}
                        </span>
                        <span class="text-muted mt-1" style="font-size: 10px;">Maks 5MB (PDF/JPG/PNG)</span>
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

    $('#payment_attachment_edit').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#file-name-payment-edit').text(fileName).removeClass('text-muted').addClass('text-primary');
        } else {
            $('#file-name-payment-edit').text('Pilih atau Letakkan File Baru (Timpa yang lama)').removeClass('text-primary').addClass('text-muted');
        }
    });

    $('.btn-view-attachment').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var ext = url.split('.').pop().toLowerCase();
        
        $('#custom-lightbox-edit').fadeIn(200).css('display', 'flex');
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            $('#lightbox-iframe-edit').hide();
            $('#lightbox-img-edit').attr('src', url).show();
        } else {
            $('#lightbox-img-edit').hide();
            $('#lightbox-iframe-edit').attr('src', url).show();
        }
    });

    $('#close-lightbox-edit, #custom-lightbox-edit').on('click', function(e) {
        if (e.target !== this && e.target.id !== 'close-lightbox-edit') return;
        $('#custom-lightbox-edit').fadeOut(200, function() {
            $('#lightbox-iframe-edit').attr('src', '');
            $('#lightbox-img-edit').attr('src', '');
        });
    });
});
</script>

<!-- Fullscreen Lightbox Overlay -->
<div id="custom-lightbox-edit" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.85); z-index: 99999; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
    <button type="button" class="btn-close btn-close-white" id="close-lightbox-edit" style="position: absolute; top: 25px; right: 30px; font-size: 20px; opacity: 1; cursor: pointer;"></button>
    <div style="width: 90%; height: 90%; display: flex; justify-content: center; align-items: center;">
        <img id="lightbox-img-edit" src="" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <iframe id="lightbox-iframe-edit" src="" style="width: 100%; height: 100%; border: none; display: none; background: white; border-radius: 8px;"></iframe>
    </div>
</div>
