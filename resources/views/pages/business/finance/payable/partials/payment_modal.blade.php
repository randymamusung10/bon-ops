<x-modal id="payment-modal-payable" title="Bayar Hutang Dagang" size="lg">
    <form id="form-pay-payable">
        @csrf
        <div class="d-flex align-items-center mb-3 p-3 rounded-4" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.1);">
            <div class="d-flex justify-content-center align-items-center rounded-circle" style="width: 50px; height: 50px; background: color-mix(in srgb, var(--primary-accent) 15%, transparent); color: var(--primary-accent); font-size: 24px;">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="ms-3">
                <h6 class="mb-1 fw-bold" style="color: var(--text-heading);">{{ $invoice->supplier_invoice_number }}</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info-subtle text-info px-2 py-1" style="font-size: 11px;">
                        <i class="bi bi-shop me-1"></i>{{ $invoice->supplier->name ?? '-' }}
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size: 11px;"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-3">

            @php
                $sisaHutang = $invoice->remaining_amount;
            @endphp

            @if(isset($pendingPayments) && $pendingPayments->count() > 0)
                <div class="col-12">
                    <div class="alert alert-warning rounded-3 mb-1 p-3" style="font-size: 13px; border-left: 4px solid var(--bs-warning);">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-warning"></i>
                            <div>
                                <strong>Pembayaran Dalam Proses!</strong> Ada <strong>{{ $pendingPayments->count() }} transaksi</strong> senilai <strong>Rp {{ number_format($pendingPayments->sum('payment_amount'), 2, ',', '.') }}</strong> yang masih dalam antrean persetujuan. Sisa yang bisa dibayarkan saat ini telah disesuaikan.
                            </div>
                        </div>
                        
                        <div class="table-responsive rounded-3 mt-3" style="border: 1px solid color-mix(in srgb, var(--bs-warning) 30%, transparent); background: color-mix(in srgb, var(--bg-dark-secondary) 50%, transparent);">
                            <table class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: color-mix(in srgb, var(--bs-warning) 20%, transparent);">
                                <thead style="background-color: color-mix(in srgb, var(--bs-warning) 15%, transparent);">
                                    <tr style="font-size: 11px; color: var(--text-heading); letter-spacing: 0.2px; font-weight: 600;">
                                        <th class="py-2 ps-3" style="width: 20%; font-weight: 600;">Tanggal</th>
                                        <th class="py-2" style="width: 35%; font-weight: 600;">Nomor Dokumen</th>
                                        <th class="py-2 text-center" style="width: 20%; font-weight: 600;">Status</th>
                                        <th class="py-2 text-end pe-3" style="width: 25%; font-weight: 600;">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 11px; color: var(--text-heading);">
                                    @foreach($pendingPayments as $pending)
                                        <tr>
                                            <td class="ps-3 fw-medium">{{ \Carbon\Carbon::parse($pending->payment_date)->format('d/m/Y') }}</td>
                                            <td class="fw-medium">{{ $pending->document_number }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-warning-subtle text-warning" style="text-transform: capitalize; font-size: 10px;">{{ $pending->status }}</span>
                                            </td>
                                            <td class="text-end pe-3 fw-bold text-warning">Rp {{ number_format($pending->payment_amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-12">
                <div class="p-3 rounded-4" style="background: color-mix(in srgb, var(--primary-accent) 4%, transparent); border: 1px solid rgba(226, 232, 240, 0.1);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-medium" style="font-size: 12px;">Sisa Hutang (yang dapat dibayar)</span>
                        <span class="fw-bold text-primary" style="font-size: 15px;" id="sisa-hutang-display">
                            Rp {{ number_format($sisaHutang, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->payments->isNotEmpty())
            <div class="mb-3">
                <label class="form-label mb-2" style="font-size: 12px; font-weight: 600;"><i class="bi bi-clock-history me-1 text-warning"></i>Riwayat Pembayaran</label>
                <div class="table-responsive rounded-4" style="border: 1px solid rgba(226, 232, 240, 0.6);">
                    <table class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                        <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                            <tr style="font-size: 12px; color: var(--text-muted); letter-spacing: 0.2px;">
                                <th class="py-2 ps-3" style="width: 25%; font-weight: 600;">Tanggal</th>
                                <th class="py-2" style="width: 20%; font-weight: 600;">Metode</th>
                                <th class="py-2" style="width: 30%; font-weight: 600;">Catatan & Lampiran</th>
                                <th class="py-2 text-end pe-3" style="width: 25%; font-weight: 600;">Nominal</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 12px; color: var(--text-heading);">
                            @foreach($invoice->payments as $payment)
                                <tr>
                                    <td class="ps-3 fw-medium">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="text-transform: capitalize;">
                                            <i class="bi bi-credit-card me-1"></i>{{ $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($payment->notes)
                                                <div class="text-muted" style="font-size: 11px;"><i class="bi bi-chat-text me-1"></i>{{ $payment->notes }}</div>
                                            @endif
                                            @if($payment->attachment_path)
                                                <div>
                                                    <button type="button" class="badge bg-info-subtle text-info border-0 btn-view-attachment" data-url="{{ asset($payment->attachment_path) }}" style="font-size: 10px;">
                                                        <i class="bi bi-paperclip me-1"></i>Lihat Lampiran
                                                    </button>
                                                </div>
                                            @else
                                                @if(!$payment->notes) <span class="text-muted fst-italic" style="font-size: 11px;">-</span> @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end pe-3 fw-bold text-success">
                                        Rp {{ number_format($payment->payment_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($invoice->remaining_amount > 0)
        <div class="row g-2 mb-3">

            <div class="col-md-6">
                <x-form.label required>Tanggal Pembayaran</x-form.label>
                <x-form.input type="date" id="payment_date" name="payment_date" required value="{{ date('Y-m-d') }}" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Jumlah Dibayar (Rp)</x-form.label>
                <x-form.input type="text" id="payment_amount" class="format-rupiah" name="payment_amount" required placeholder="0" value="{{ number_format($invoice->remaining_amount, 0, ',', '.') }}" />
                <div class="form-text" style="font-size: 11px;">Sisa pelunasan setelah pembayaran: <span id="preview-remaining" class="fw-bold">Rp 0</span></div>
            </div>

            <div class="col-md-6">
                <x-form.label required>Metode Pembayaran</x-form.label>
                <x-form.select id="payment_method" name="payment_method" class="form-select-sm" required>
                    <option value="transfer" selected>Transfer Bank</option>
                    <option value="cash">Tunai (Cash)</option>
                    <option value="giro">Giro</option>
                    <option value="cheque">Cek</option>
                </x-form.select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label>Nama Bank</x-form.label>
                <x-form.input type="text" id="bank_name" name="bank_name" placeholder="Contoh: BCA, Mandiri" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label>No. Rekening</x-form.label>
                <x-form.input type="text" id="bank_account_number" name="bank_account_number" placeholder="Nomor rekening" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <x-form.label>No. Referensi / No. Bukti</x-form.label>
                <x-form.input type="text" id="bank_reference" name="bank_reference" placeholder="No. transfer / No. cek" />
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <x-form.label>Catatan</x-form.label>
                <x-form.textarea id="notes" name="notes" rows="2" placeholder="Catatan pembayaran (Opsional)"></x-form.textarea>
                <div class="invalid-feedback"></div>
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
            <div class="alert alert-success py-2 mb-4" style="font-size: 13px;">
                <i class="bi bi-check-circle me-2"></i> Tagihan hutang ini sudah lunas sepenuhnya.
            </div>
        @endif

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Tutup</x-button>
            @if($invoice->remaining_amount > 0)
                <x-button type="submit" variant="primary" size="sm" icon="bi-check2-circle">Proses Pembayaran</x-button>
            @endif
        </div>
    </form>
</x-modal>

<script>
$(document).ready(function() {
    var remainingBalance = {{ $invoice->remaining_amount }};

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
            container.find('.payment-feedback').html('<i class="bi bi-exclamation-triangle-fill me-1"></i>Jumlah melebihi sisa hutang! Kelebihan: <b>Rp ' + window.AppFormat.formatRupiah(kelebihan) + '</b>').show();
            container.find('.form-text').hide();
            
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
        dropdownParent: $('#payment-modal-payable'),
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

    $('.btn-view-attachment').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var ext = url.split('.').pop().toLowerCase();
        
        $('#custom-lightbox-ap').fadeIn(200).css('display', 'flex');
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            $('#lightbox-iframe-ap').hide();
            $('#lightbox-img-ap').attr('src', url).show();
        } else {
            $('#lightbox-img-ap').hide();
            $('#lightbox-iframe-ap').attr('src', url).show();
        }
    });

    $('#close-lightbox-ap, #custom-lightbox-ap').on('click', function(e) {
        if (e.target !== this && e.target.id !== 'close-lightbox-ap') return;
        $('#custom-lightbox-ap').fadeOut(200, function() {
            $('#lightbox-iframe-ap').attr('src', '');
            $('#lightbox-img-ap').attr('src', '');
        });
    });

    // Format Rupiah Input
    $('.format-rupiah').on('input', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        if(val) {
            $(this).val(new Intl.NumberFormat('id-ID').format(val));
        }
    });

    $('#form-pay-payable').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        var amountText = form.find('input[name="payment_amount"]').val();
        var rawAmount = parseFloat(window.AppFormat.unmaskNumber(amountText)) || 0;

        if (rawAmount > remainingBalance) {
            var kelebihan = rawAmount - remainingBalance;
            AppAlert.error('Gagal!', 'Jumlah bayar melebihi sisa hutang sebesar Rp ' + window.AppFormat.formatRupiah(kelebihan) + '.');
            return;
        }

        var formData = new FormData(this);
        formData.set('payment_amount', rawAmount);
        
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...').prop('disabled', true);
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        $.ajax({
            url: "{{ route('business.finance.payable.pay', $uuid) }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#payment-modal-payable').modal('hide');
                    if(typeof window.refreshTable === 'function') {
                        window.refreshTable();
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            popup: 'rounded-4'
                        }
                    });
                }
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);
                amountInput.val(originalAmount); // restore format
                if(xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            var input = form.find('[name="'+key+'"]');
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(value[0]);
                        });
                    } else if (xhr.responseJSON.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON.message,
                            customClass: {
                                popup: 'rounded-4'
                            }
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan sistem.',
                        customClass: {
                            popup: 'rounded-4'
                        }
                    });
                }
            }
        });
    });
});
</script>

<!-- Fullscreen Lightbox Overlay -->
<div id="custom-lightbox-ap" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.85); z-index: 99999; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
    <button type="button" class="btn-close btn-close-white" id="close-lightbox-ap" style="position: absolute; top: 25px; right: 30px; font-size: 20px; opacity: 1; cursor: pointer;"></button>
    <div style="width: 90%; height: 90%; display: flex; justify-content: center; align-items: center;">
        <img id="lightbox-img-ap" src="" style="max-width: 100%; max-height: 100%; object-fit: contain; display: none; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <iframe id="lightbox-iframe-ap" src="" style="width: 100%; height: 100%; border: none; display: none; background: white; border-radius: 8px;"></iframe>
    </div>
</div>
