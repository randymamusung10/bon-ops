<x-modal id="modal-payment" title="Pelunasan Piutang" size="lg">
    <form id="form-payment" enctype="multipart/form-data">
        <input type="hidden" id="payment_uuid" name="payment_uuid" value="{{ $uuid }}">
        
        @php
            $grandTotal = $order->grand_total;
            $paidAmount = $order->paid_amount;
            $remaining = max(0, $grandTotal - $paidAmount);
        @endphp

        <div class="row g-2 mb-4">
            <div class="col-4">
                <div class="p-2 rounded-4 h-100 text-center d-flex flex-column justify-content-center" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                    <div class="text-muted fw-medium mb-1" style="font-size: 11px;">Total Tagihan</div>
                    <div class="fw-bold text-heading" style="font-size: 13px;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 rounded-4 h-100 text-center d-flex flex-column justify-content-center" style="background: rgba(16, 185, 129, 0.05); border: 1px dashed rgba(16, 185, 129, 0.2);">
                    <div class="text-muted fw-medium mb-1" style="font-size: 11px;">Sudah Dibayar</div>
                    <div class="fw-bold text-success" style="font-size: 13px;">Rp {{ number_format($paidAmount, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 rounded-4 h-100 text-center d-flex flex-column justify-content-center" style="background: rgba(239, 68, 68, 0.05); border: 1px dashed rgba(239, 68, 68, 0.2);">
                    <div class="text-muted fw-medium mb-1" style="font-size: 11px;">Sisa Tagihan</div>
                    <div class="fw-bold text-danger" style="font-size: 13px;">Rp {{ number_format($remaining, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        @if($order->payments->isNotEmpty())
            <div class="mb-4">
                <label class="form-label" style="font-size: 12px; font-weight: 600;">Riwayat Pembayaran</label>
                <div class="list-group list-group-flush border rounded">
                    @foreach($order->payments as $payment)
                        <div class="list-group-item px-3 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold" style="font-size: 13px;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary ms-2">{{ strtoupper($payment->payment_method) }}</span>
                                </div>
                                <div class="text-muted" style="font-size: 11px;">{{ $payment->payment_date->format('d/m/Y') }}</div>
                            </div>
                            @if($payment->notes || $payment->attachment_path)
                                <div class="mt-1" style="font-size: 11px;">
                                    @if($payment->notes)<div class="text-muted fst-italic">{{ $payment->notes }}</div>@endif
                                    @if($payment->attachment_path)
                                        <a href="{{ asset($payment->attachment_path) }}" target="_blank" class="text-decoration-none mt-1 d-inline-block"><i class="bi bi-paperclip"></i> Lihat Lampiran</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($remaining > 0)
            <div class="row g-3">
                <div class="col-md-6">
                    <x-form.label required>Tanggal Pembayaran</x-form.label>
                    <x-form.input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required />
                </div>
                
                <div class="col-md-6">
                    <x-form.label required>Metode Pembayaran</x-form.label>
                    <x-form.select id="payment_method" name="payment_method" class="form-select-sm" required>
                        <option value="transfer">Transfer Bank</option>
                        <option value="cash">Tunai (Cash)</option>
                    </x-form.select>
                </div>

                <div class="col-md-6">
                    <x-form.label required>Jumlah Bayar (Rp)</x-form.label>
                    <x-form.input type="text" class="format-rupiah" id="payment_amount" name="amount" value="{{ number_format($remaining, 0, ',', '.') }}" required />
                    <div class="form-text" style="font-size: 11px;">Sisa pelunasan setelah pembayaran: <span id="preview-remaining" class="fw-bold">Rp 0</span></div>
                </div>

                <div class="col-md-6">
                    <x-form.label>Keterangan (Opsional)</x-form.label>
                    <x-form.textarea id="payment_notes" name="payment_notes" placeholder="Tambahkan catatan pembayaran jika ada..." rows="1"></x-form.textarea>
                </div>

                <div class="col-12">
                    <x-form.label>Bukti Pembayaran (Opsional)</x-form.label>
                    <div class="position-relative p-3 text-center rounded-3 bg-light hover-shadow-sm" style="border: 1px dashed #cbd5e1; transition: all 0.2s;" id="dropzone-payment">
                        <input type="file" id="payment_attachment" name="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="bi bi-cloud-arrow-up text-primary mb-1" style="font-size: 1.2rem;"></i>
                            <span class="text-muted fw-medium" style="font-size: 11px;" id="file-name-payment">Pilih atau Letakkan File</span>
                            <span class="text-muted mt-1" style="font-size: 10px;">Maks 5MB (PDF/JPG/PNG)</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-success py-2" style="font-size: 13px;">
                <i class="bi bi-check-circle me-2"></i> Tagihan ini sudah lunas sepenuhnya.
            </div>
        @endif

        <div class="d-flex justify-content-end gap-2 pt-3 mt-4 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
            @if($remaining > 0)
                <x-button type="button" variant="primary" size="sm" id="btn-submit-payment" icon="bi-check2">Simpan Pembayaran</x-button>
            @endif
        </div>
    </form>
</x-modal>

<script>
$(document).ready(function() {
    var remainingBalance = {{ $remaining }};

    function updatePreview() {
        var inputVal = $('#payment_amount').val();
        var inputAmount = parseFloat(window.AppFormat.unmaskNumber(inputVal)) || 0;
        
        var sisa = Math.max(0, remainingBalance - inputAmount);
        $('#preview-remaining').text('Rp ' + sisa.toLocaleString('id-ID'));

        var container = $('#payment_amount').closest('div');

        if (inputAmount > remainingBalance) {
            var kelebihan = inputAmount - remainingBalance;
            $('#payment_amount').addClass('is-invalid');
            
            if (container.find('.payment-feedback').length === 0) {
                $('<div class="payment-feedback text-danger fw-medium mt-1" style="font-size: 11px;"></div>').insertAfter('#payment_amount');
            }
            container.find('.payment-feedback').html('<i class="bi bi-exclamation-triangle-fill me-1"></i>Jumlah melebihi sisa tagihan! Kelebihan: <b>Rp ' + window.AppFormat.formatRupiah(kelebihan) + '</b>').show();
            container.find('.form-text').hide();
            
            // Clean up old invalid-feedback if any from previous code
            container.find('.invalid-feedback').remove();
        } else {
            $('#payment_amount').removeClass('is-invalid');
            container.find('.payment-feedback').hide();
            container.find('.form-text').show();
        }
    }

    $('#payment_amount').on('input', updatePreview);
    updatePreview();

    // Init Select2
    $('#payment_method').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modal-payment'),
        width: '100%',
        minimumResultsForSearch: -1
    });

    // File name update
    $('#payment_attachment').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#file-name-payment').text(fileName).removeClass('text-muted').addClass('text-primary');
        } else {
            $('#file-name-payment').text('Pilih atau Letakkan File').removeClass('text-primary').addClass('text-muted');
        }
    });

    $('#btn-submit-payment').on('click', function() {
        var uuid = $('#payment_uuid').val();
        var paymentDate = $('#payment_date').val();
        var amountText = $('#payment_amount').val();
        var amount = parseFloat(window.AppFormat.unmaskNumber(amountText)) || 0;
        var paymentMethod = $('#payment_method').val();

        if (!paymentDate || !amount || !paymentMethod) {
            AppAlert.error('Gagal!', 'Tanggal, Nominal, dan Metode Pembayaran wajib diisi.');
            return;
        }

        if (amount > remainingBalance) {
            var kelebihan = amount - remainingBalance;
            AppAlert.error('Gagal!', 'Jumlah bayar melebihi sisa tagihan sebesar Rp ' + window.AppFormat.formatRupiah(kelebihan) + '.');
            return;
        }

        AppAlert.confirm('Konfirmasi Pembayaran', 'Apakah Anda yakin ingin menyimpan data pembayaran ini?', 'Ya, Simpan').then((result) => {
            if (result.isConfirmed) {
                var btn = $('#btn-submit-payment');
                var originalHtml = btn.html();
                btn.html('<div class="spinner-border spinner-border-sm text-light" role="status"></div>').prop('disabled', true);

                var formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('payment_date', paymentDate);
                formData.append('amount', amount);
                formData.append('payment_method', paymentMethod);
                formData.append('payment_notes', $('#payment_notes').val());
                
                var attachment = $('#payment_attachment')[0].files[0];
                if (attachment) {
                    formData.append('attachment', attachment);
                }

                $.ajax({
                    url: "{{ url('business/finance/receivable') }}/" + uuid + "/pay",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modal-payment').modal('hide');
                        if (typeof window.refreshTable === 'function') {
                            window.refreshTable();
                        } else if (typeof table !== 'undefined') {
                            table.ajax.reload(null, false);
                        }
                        AppAlert.success('Berhasil!', response.message);
                    },
                    error: function(xhr) {
                        AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Gagal menyimpan pembayaran.');
                    },
                    complete: function() {
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            }
        });
    });
});
</script>
