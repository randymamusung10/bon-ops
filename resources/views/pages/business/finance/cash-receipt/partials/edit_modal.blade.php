<x-modal id="editModal" title="Edit Penerimaan Kas: {{ $transaction->transaction_number }}" size="xl">
    <form id="form-edit-receipt" enctype="multipart/form-data">
        @method('PUT')
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-form.label required>Tanggal Transaksi</x-form.label>
                <x-form.input type="date" name="date" value="{{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') }}" required />
            </div>
            <div class="col-md-4">
                <x-form.label required>Disimpan Ke (Akun Kas/Bank)</x-form.label>
                <x-form.select name="account_id" id="edit-account-id" class="select2-modal" style="width: 100%;" required>
                    <option value="">Pilih Akun Kas/Bank</option>
                    @foreach($cashAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ $transaction->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <div class="col-md-4">
                <x-form.label>No. Referensi Eksternal</x-form.label>
                <x-form.input type="text" name="reference_number" value="{{ $transaction->reference_number }}" placeholder="Contoh: INV-001 / Slip Transfer" />
                <div class="form-text text-muted" style="font-size: 11px; line-height: 1.2; margin-top: 4px;"><i class="bi bi-info-circle me-1"></i>Ketik nomor dokumen fisik/luar. Misal: No. Cek, ID Mutasi Bank, No. Struk.</div>
            </div>
            <div class="col-md-8 d-flex flex-column">
                <x-form.label>Keterangan Umum</x-form.label>
                <x-form.textarea name="description" class="flex-grow-1" style="resize: none;" placeholder="Catatan transaksi...">{{ $transaction->description }}</x-form.textarea>
            </div>
            <div class="col-md-4">
                <x-form.label>Lampiran Dokumen</x-form.label>
                <div class="position-relative p-3 text-center rounded-3 bg-light hover-shadow-sm" style="border: 1px dashed {{ $transaction->attachment_path ? 'var(--bs-primary)' : '#cbd5e1' }}; background-color: {{ $transaction->attachment_path ? 'rgba(var(--bs-primary-rgb), 0.03)' : 'var(--bs-light)' }}; transition: all 0.2s;" id="dropzone-edit">
                    <input type="file" name="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this, 'edit')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-cloud-arrow-up text-primary mb-1" style="font-size: 1.2rem;"></i>
                        <span class="{{ $transaction->attachment_path ? 'text-primary' : 'text-muted' }} fw-medium" style="font-size: 11px;" id="file-name-edit">
                            {{ $transaction->attachment_path ? 'Ganti File: ' . basename($transaction->attachment_path) : 'Pilih atau Letakkan File Baru' }}
                        </span>
                        <span class="text-muted mt-1" style="font-size: 10px;">Maks 2MB (PDF/JPG/PNG)</span>
                    </div>
                </div>
                @if($transaction->attachment_path)
                    <div class="mt-2 text-end">
                        <a href="{{ Storage::url($transaction->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 11px;">
                            <i class="bi bi-eye"></i> Lihat File Saat Ini
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded px-2 py-1 me-2">
                    <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
                </div>
                <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Rincian Penerimaan</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <x-button type="button" variant="ghost-primary" size="sm" id="btn-add-item-edit" icon="bi-plus">Tambah Baris</x-button>
            </div>
        </div>

        <div class="table-responsive rounded-4 overflow-hidden mb-3" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table align-middle mb-0" id="items-table-edit" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="35%" class="py-2 ps-4 border-0">Sumber Dana (Akun) <span class="text-danger">*</span></th>
                        <th width="40%" class="py-2 border-0">Keterangan Baris <span class="text-danger">*</span></th>
                        <th width="20%" class="py-2 text-end border-0">Nominal (Rp) <span class="text-danger">*</span></th>
                        <th width="5%" class="text-center py-2 pe-4 border-0"></th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($transaction->items as $index => $item)
                    <tr>
                        <td class="py-2 ps-4">
                            <input type="hidden" name="items[{{$index}}][id]" value="{{ $item->id }}">
                            <x-form.select name="items[{{$index}}][account_id]" class="account-select-edit" style="width: 100%;" required>
                                <option value="">Pilih Akun Sumber</option>
                                @foreach($otherAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ $item->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </x-form.select>
                        </td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[{{$index}}][description]" value="{{ $item->description }}" placeholder="Contoh: Pendapatan Bunga, Modal Awal..." required /></td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[{{$index}}][amount]" class="text-end input-amount-edit format-number" value="{{ number_format($item->amount, 0, '', '') }}" required /></td>
                        <td class="text-center py-2 pe-4">
                            <button type="button" class="btn-icon-modern text-danger btn-remove-item-edit mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                        <td colspan="2" class="py-2 text-end fw-bold">Total Penerimaan (Rp)</td>
                        <td class="text-end py-3 pe-4 fw-bold text-primary"><span id="total-amount-edit">0</span></td>
                        <td class="py-2 pe-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="button" variant="primary" size="sm" id="btn-update-receipt" icon="bi-check2">Simpan Perubahan</x-button>
        </div>
    </form>
</x-modal>

<script>
if (typeof window.updateFileName !== 'function') {
    window.updateFileName = function(input, type) {
        let fileName = input.files[0] ? input.files[0].name : (type === 'create' ? 'Pilih / Letakkan File' : 'Pilih / Letakkan File Baru');
        $('#file-name-' + type).text(fileName);
        if(input.files[0]) {
            $('#dropzone-' + type).css('border-color', 'var(--bs-primary)').css('background-color', 'rgba(var(--bs-primary-rgb), 0.03)');
            $('#file-name-' + type).addClass('text-primary').removeClass('text-muted');
        } else {
            $('#dropzone-' + type).css('border-color', '#cbd5e1').css('background-color', 'var(--bs-light)');
            $('#file-name-' + type).removeClass('text-primary').addClass('text-muted');
        }
    };
}

$(document).ready(function() {
    let itemIndex = {{ count($transaction->items) }};

    $('#edit-account-id').select2({ theme: 'bootstrap-5', dropdownParent: $('#editModal') });
    $('.account-select-edit').select2({ theme: 'bootstrap-5', dropdownParent: $('#editModal') });
    window.AppFormat.init();

    function calculateTotalEdit() {
        let total = 0;
        $('#items-table-edit tbody tr').each(function() {
            let amountStr = window.AppFormat.unmaskNumber($(this).find('.input-amount-edit').val());
            let val = parseFloat(amountStr) || 0;
            total += val;
        });
        $('#total-amount-edit').text(window.AppFormat.formatRupiah(total));
        
        if (total <= 0) {
            $('#btn-update-receipt').prop('disabled', true);
        } else {
            $('#btn-update-receipt').prop('disabled', false);
        }
    }

    $(document).on('input', '.input-amount-edit', function() {
        calculateTotalEdit();
    });

    $('#btn-add-item-edit').on('click', function() {
        let tr = `
        <tr>
            <td class="py-2 ps-4">
                <x-form.select name="items[${itemIndex}][account_id]" class="account-select-edit" style="width: 100%;" required>
                    <option value="">Pilih Akun Sumber</option>
                    @foreach($otherAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                    @endforeach
                </x-form.select>
            </td>
            <td class="py-2"><x-form.input size="sm" type="text" name="items[${itemIndex}][description]" placeholder="Keterangan baris..." required /></td>
            <td class="py-2"><x-form.input size="sm" type="text" name="items[${itemIndex}][amount]" class="text-end input-amount-edit format-number" value="0" required /></td>
            <td class="text-center py-2 pe-4">
                <button type="button" class="btn-icon-modern text-danger btn-remove-item-edit mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
        `;
        $('#items-table-edit tbody').append(tr);
        $('#items-table-edit tbody tr:last .account-select-edit').select2({ theme: 'bootstrap-5', dropdownParent: $('#editModal') });
        window.AppFormat.init();
        itemIndex++;
        calculateTotalEdit();
    });

    $(document).on('click', '.btn-remove-item-edit', function() {
        if ($('#items-table-edit tbody tr').length <= 1) {
            AppAlert.warning('Minimal 1 Baris', 'Transaksi harus memiliki setidaknya 1 rincian.');
            return;
        }
        $(this).closest('tr').remove();
        calculateTotalEdit();
    });

    $('#btn-update-receipt').on('click', function() {
        let form = $('#form-edit-receipt');
        
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        let total = window.AppFormat.unmaskNumber($('#total-amount-edit').text());
        if (total <= 0) {
            AppAlert.warning('Total Tidak Valid', 'Total Penerimaan harus lebih besar dari 0.');
            return;
        }

        // Unmask inputs
        form.find('.format-number').each(function() {
            let val = $(this).val();
            if(val) $(this).val(window.AppFormat.unmaskNumber(val));
        });

        let formData = new FormData(form[0]);
        window.AppFormat.init();
        
        let submitBtn = $(this);
        let originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Memproses...');
        
        $.ajax({
            url: "{{ route('business.finance.cash_receipt.update', $transaction->uuid) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $('#editModal').modal('hide');
                window.refreshTable();
                AppAlert.success('Berhasil!', res.message);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html(originalHtml);
                AppAlert.error('Gagal!', err.responseJSON?.message || 'Terjadi kesalahan sistem.');
            }
        });
    });

    calculateTotalEdit();
});
</script>
