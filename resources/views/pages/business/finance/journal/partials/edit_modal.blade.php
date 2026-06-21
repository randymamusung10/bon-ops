<x-modal id="editModal" title="Edit Jurnal (Draft)" size="xl">
    <form id="form-edit-journal" enctype="multipart/form-data">
        @method('PUT')
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-form.label required>Tanggal Jurnal</x-form.label>
                <x-form.input type="date" name="date" value="{{ $journal->date->format('Y-m-d') }}" required />
            </div>
            <div class="col-md-4">
                <x-form.label>Tipe Referensi</x-form.label>
                <x-form.select name="reference_type" id="edit-reference-type" class="select2-modal-edit" style="width: 100%;">
                    <option value="" {{ empty($journal->reference_type) ? 'selected' : '' }}>Tanpa Referensi</option>
                    <option value="PurchaseOrder" {{ $journal->reference_type === 'PurchaseOrder' ? 'selected' : '' }}>Purchase Order</option>
                    <option value="PosOrder" {{ $journal->reference_type === 'PosOrder' ? 'selected' : '' }}>POS Order</option>
                    <option value="Manual" {{ $journal->reference_type === 'Manual' ? 'selected' : '' }}>Manual</option>
                </x-form.select>
            </div>
            <div class="col-md-4">
                <x-form.label>ID Referensi</x-form.label>
                <x-form.select name="reference_id" id="edit-reference-id" class="select2-modal-edit" style="width: 100%;">
                    @if($journal->reference_id)
                        <option value="{{ $journal->reference_id }}" selected>{{ $referenceText }}</option>
                    @else
                        <option value="">Pilih Tipe Dulu</option>
                    @endif
                </x-form.select>
            </div>
            <div class="col-md-8 d-flex flex-column">
                <x-form.label>Keterangan Tambahan</x-form.label>
                <x-form.textarea name="notes" class="flex-grow-1" style="resize: none;">{{ $journal->notes }}</x-form.textarea>
            </div>
            <div class="col-md-4">
                <x-form.label>Lampiran Dokumen</x-form.label>
                <div class="position-relative p-3 text-center rounded-3 bg-light hover-shadow-sm" style="border: 1px dashed #cbd5e1; transition: all 0.2s;" id="dropzone-edit">
                    <input type="file" name="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this, 'edit')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-cloud-arrow-up text-primary mb-1" style="font-size: 1.2rem;"></i>
                        <span class="text-muted fw-medium" style="font-size: 11px;" id="file-name-edit">{{ $journal->attachment_path ? 'Timpa dengan File Baru (Opsional)' : 'Pilih atau Letakkan File Baru' }}</span>
                        <span class="text-muted mt-1" style="font-size: 10px;">Maks 5MB (PDF/JPG/PNG)</span>
                    </div>
                </div>
                @if($journal->attachment_path)
                    <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(var(--bs-primary-rgb), 0.05); border: 1px solid rgba(var(--bs-primary-rgb), 0.2);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white rounded d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                    <i class="bi bi-file-earmark-check text-primary" style="font-size: 1.1rem;"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold text-primary" style="font-size: 12px;">Lampiran Tersimpan</span>
                                    <span class="text-muted" style="font-size: 10px;">Telah diunggah sebelumnya</span>
                                </div>
                            </div>
                            <button type="button" onclick="previewAttachment('{{ Storage::url($journal->attachment_path) }}')" class="btn btn-primary btn-sm rounded-3 py-1 px-3 d-flex align-items-center gap-1 shadow-sm" style="font-size: 11px;">
                                <i class="bi bi-eye"></i> Preview
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded px-2 py-1 me-2">
                    <i class="bi bi-list-ul text-white" style="font-size: 14px;"></i>
                </div>
                <h6 class="fw-bold mb-0 text-heading" style="letter-spacing: 0.3px;">Baris Jurnal</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn custom-btn btn-size-sm btn-variant-light" id="btn-load-reference-edit" style="display: {{ $journal->reference_id ? 'inline-block' : 'none' }};">
                    <i class="bi bi-arrow-clockwise me-1"></i> Muat Template
                </button>
                <x-button type="button" variant="ghost-primary" size="sm" id="btn-add-item-edit" icon="bi-plus">Tambah Baris</x-button>
            </div>
        </div>

        <div class="table-responsive rounded-4 overflow-hidden mb-3" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table align-middle mb-0" id="items-table-edit" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
                <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent); border-bottom: 1px solid rgba(226, 232, 240, 0.2);">
                    <tr class="text-muted" style="letter-spacing: 0.2px;">
                        <th width="35%" class="py-2 ps-4 border-0">Akun (COA) <span class="text-danger">*</span></th>
                        <th width="30%" class="py-2 border-0">Keterangan Baris</th>
                        <th width="15%" class="py-2 text-end border-0">Debit (Rp) <span class="text-danger">*</span></th>
                        <th width="15%" class="py-2 text-end border-0">Kredit (Rp) <span class="text-danger">*</span></th>
                        <th width="5%" class="text-center py-2 pe-4 border-0"></th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($journal->items as $index => $item)
                    <tr>
                        <td class="py-2 ps-4">
                            <x-form.select name="items[{{ $index }}][chart_of_account_id]" class="account-select" style="width: 100%;">
                                <option value="">Pilih Akun</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ $item->chart_of_account_id == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </x-form.select>
                        </td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[{{ $index }}][description]" value="{{ $item->description }}" placeholder="Keterangan baris..." /></td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[{{ $index }}][debit]" value="{{ number_format($item->debit, 0, ',', '.') }}" class="text-end input-debit-edit format-number" required /></td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[{{ $index }}][credit]" value="{{ number_format($item->credit, 0, ',', '.') }}" class="text-end input-credit-edit format-number" required /></td>
                        <td class="text-center py-2 pe-4">
                            <button type="button" class="btn-icon-modern text-danger btn-remove-item-edit mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                        <td colspan="2" class="py-2 text-end fw-bold">Total Keseluruhan (Rp)</td>
                        <td class="text-end py-3 pe-4 fw-bold" style="color: var(--primary-accent);"><span id="total-debit-edit">0</span></td>
                        <td class="text-end py-3 pe-4 fw-bold" style="color: var(--primary-accent);"><span id="total-credit-edit">0</span></td>
                        <td class="py-2 pe-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div id="balance-warning-edit" class="text-danger small fw-semibold mt-1 mb-3" style="display:none;"><i class="bi bi-exclamation-triangle me-1"></i> Total Debit dan Kredit harus seimbang (Balance).</div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="button" variant="primary" size="sm" id="btn-update-journal" icon="bi-check2">Update Draft</x-button>
        </div>
    </form>
</x-modal>

<script>
if (typeof window.updateFileName !== 'function') {
    window.updateFileName = function(input, type) {
        let fileName = input.files[0] ? input.files[0].name : (type === 'create' ? 'Pilih atau Letakkan File (Opsional)' : 'Pilih atau Letakkan File Baru');
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
    let itemIndexEdit = {{ $journal->items->count() }};

    $('.account-select').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#editModal')
    });

    $('#edit-reference-type').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#editModal')
    });

    $('#edit-reference-type').on('change', function(e, isInit) {
        let type = $(this).val();
        let refIdSelect = $('#edit-reference-id');
        
        if (!isInit) {
            refIdSelect.empty().append('<option value="">Pilih Referensi</option>');
        }
        
        if (!type || type === 'Manual') {
            if (refIdSelect.hasClass("select2-hidden-accessible")) {
                refIdSelect.select2('destroy');
            }
            refIdSelect.select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#editModal'),
                tags: type === 'Manual',
                data: []
            });
            refIdSelect.prop('disabled', !type);
            return;
        }

        refIdSelect.prop('disabled', false);
        if (refIdSelect.hasClass("select2-hidden-accessible")) {
            refIdSelect.select2('destroy');
        }
        refIdSelect.select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#editModal'),
            ajax: {
                url: "{{ route('business.finance.journal.references') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, type: type };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });
    });

    $('#edit-reference-type').trigger('change', [true]);

    function calculateTotalEdit() {
        let totalDebit = 0;
        let totalCredit = 0;

        $('#items-table-edit tbody tr').each(function() {
            let debitStr = window.AppFormat.unmaskNumber($(this).find('.input-debit-edit').val());
            let creditStr = window.AppFormat.unmaskNumber($(this).find('.input-credit-edit').val());
            
            let debitVal = parseFloat(debitStr) || 0;
            let creditVal = parseFloat(creditStr) || 0;

            totalDebit += debitVal;
            totalCredit += creditVal;
        });

        $('#total-debit-edit').text(window.AppFormat.formatRupiah(totalDebit));
        $('#total-credit-edit').text(window.AppFormat.formatRupiah(totalCredit));

        if (Math.abs(totalDebit - totalCredit) > 0.01) {
            $('#balance-warning-edit').show();
            $('#btn-update-journal').prop('disabled', true);
        } else {
            $('#balance-warning-edit').hide();
            $('#btn-update-journal').prop('disabled', false);
        }
    }

    $(document).on('input', '.input-debit-edit, .input-credit-edit', function() {
        calculateTotalEdit();
    });

    $('#btn-add-item-edit').on('click', function() {
        let tr = `
        <tr>
            <td class="py-2 ps-4">
                <x-form.select name="items[${itemIndexEdit}][chart_of_account_id]" class="account-select" style="width: 100%;">
                    <option value="">Pilih Akun</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                    @endforeach
                </x-form.select>
            </td>
            <td class="py-2"><x-form.input size="sm" type="text" name="items[${itemIndexEdit}][description]" placeholder="Keterangan baris..." /></td>
            <td class="py-2"><x-form.input size="sm" type="text" name="items[${itemIndexEdit}][debit]" class="text-end input-debit-edit format-number" value="0" required /></td>
            <td class="py-2"><x-form.input size="sm" type="text" name="items[${itemIndexEdit}][credit]" class="text-end input-credit-edit format-number" value="0" required /></td>
            <td class="text-center py-2 pe-4">
                <button type="button" class="btn-icon-modern text-danger btn-remove-item-edit mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
        `;
        $('#items-table-edit tbody').append(tr);
        
        $('#items-table-edit tbody tr:last .account-select').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#editModal')
        });

        itemIndexEdit++;
    });

    $(document).on('click', '.btn-remove-item-edit', function() {
        if ($('#items-table-edit tbody tr').length <= 2) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak dapat menghapus baris',
                text: 'Minimal harus ada 2 baris jurnal untuk mencatat Debit dan Kredit.',
            });
            return;
        }
        $(this).closest('tr').remove();
        calculateTotalEdit();
    });

    function triggerLoadReferenceEdit(type, id) {
        Swal.fire({
            title: 'Muat Template Jurnal?',
            text: 'Sistem dapat membuatkan baris jurnal secara otomatis berdasarkan tipe transaksi ini.',
            icon: 'question',
            iconColor: 'var(--primary-accent)',
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonText: 'Ya, Muat',
            denyButtonText: 'Tidak, Isi Sendiri',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn custom-btn position-relative overflow-hidden btn-size-md btn-variant-primary ms-2',
                denyButton: 'btn custom-btn position-relative overflow-hidden btn-size-md btn-variant-light ms-2',
                popup: 'rounded-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.ERPLoader.show('Memuat Template', 'Sistem sedang menganalisis dokumen referensi...');
                $.ajax({
                    url: "{{ route('business.finance.journal.reference_details') }}",
                    data: { type: type, id: id },
                    success: function(res) {
                        window.ERPLoader.hide();
                        if(res.success && res.data && res.data.length > 0) {
                            $('#items-table-edit tbody').empty();
                            itemIndexEdit = 0;
                            
                            res.data.forEach(function(item) {
                                let tr = `
                                <tr>
                                    <td class="py-2 ps-4">
                                        <select name="items[${itemIndexEdit}][chart_of_account_id]" class="form-select account-select" style="width: 100%;">
                                            <option value="">Pilih Akun</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2"><input class="form-control form-control-sm" type="text" name="items[${itemIndexEdit}][description]" value="${item.description}" placeholder="Keterangan baris..." /></td>
                                    <td class="py-2"><input class="form-control form-control-sm text-end input-debit-edit format-number" type="text" name="items[${itemIndexEdit}][debit]" value="${item.debit}" required /></td>
                                    <td class="py-2"><input class="form-control form-control-sm text-end input-credit-edit format-number" type="text" name="items[${itemIndexEdit}][credit]" value="${item.credit}" required /></td>
                                    <td class="text-center py-2 pe-4">
                                        <button type="button" class="btn-icon-modern text-danger btn-remove-item-edit mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                `;
                                let $tr = $(tr);
                                $tr.find('.account-select').val(item.account_id);
                                $('#items-table-edit tbody').append($tr);
                                itemIndexEdit++;
                            });
                            
                            // Re-init components
                            $('.account-select').select2({
                                dropdownParent: $('#editModal'),
                                theme: 'bootstrap-5'
                            });
                            window.AppFormat.init();
                            calculateTotalEdit();
                        } else {
                            AppAlert.error('Gagal', 'Referensi ini belum didukung untuk otomatisasi jurnal.');
                        }
                    },
                    error: function() {
                        window.ERPLoader.hide();
                        AppAlert.error('Terjadi Kesalahan', 'Gagal memuat detail referensi.');
                    }
                });
            } else if (result.isDenied) {
                // Reset input manual
                $('#items-table-edit tbody').empty();
                itemIndexEdit = 0;
                
                for(let i=0; i<2; i++) {
                    let tr = `
                    <tr>
                        <td class="py-2 ps-4">
                            <select name="items[${itemIndexEdit}][chart_of_account_id]" class="form-select account-select" style="width: 100%;">
                                <option value="">Pilih Akun</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-2"><input class="form-control form-control-sm" type="text" name="items[${itemIndexEdit}][description]" placeholder="Keterangan baris..." /></td>
                        <td class="py-2"><input class="form-control form-control-sm text-end input-debit-edit format-number" type="text" name="items[${itemIndexEdit}][debit]" value="0" required /></td>
                        <td class="py-2"><input class="form-control form-control-sm text-end input-credit-edit format-number" type="text" name="items[${itemIndexEdit}][credit]" value="0" required /></td>
                        <td class="text-center py-2 pe-4">
                            <button type="button" class="btn-icon-modern text-danger btn-remove-item-edit mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    `;
                    $('#items-table-edit tbody').append(tr);
                    itemIndexEdit++;
                }
                
                $('.account-select').select2({ dropdownParent: $('#editModal'), theme: 'bootstrap-5' });
                window.AppFormat.init();
                calculateTotalEdit();
                AppAlert.info('Pengisian Manual', 'Tabel baris jurnal telah disiapkan untuk pengisian manual.');
            }
        });
    }

    $('#btn-load-reference-edit').on('click', function() {
        let type = $('#edit-reference-type').val();
        let id = $('#edit-reference-id').val();
        if (type && id) {
            triggerLoadReferenceEdit(type, id);
        }
    });

    $('#edit-reference-id').on('select2:select', function(e) {
        let type = $('#edit-reference-type').val();
        let id = e.params.data.id;
        
        if (type && id) {
            $('#btn-load-reference-edit').show();
            triggerLoadReferenceEdit(type, id);
        } else {
            $('#btn-load-reference-edit').hide();
        }
    });

    $('#edit-reference-id').on('select2:clear', function(e) {
        $('#btn-load-reference-edit').hide();
    });

    $('#btn-update-journal').on('click', function() {
        let form = $('#form-edit-journal');
        
        let isValid = true;
        form.find('.account-select').each(function() {
            if (!$(this).val()) {
                AppAlert.warning('Data Belum Lengkap', 'Harap pilih Akun (COA) untuk setiap baris jurnal.');
                isValid = false;
                return false;
            }
        });
        
        if (!isValid) return;

        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        let totalDebit = window.AppFormat.unmaskNumber($('#total-debit-edit').text());
        let totalCredit = window.AppFormat.unmaskNumber($('#total-credit-edit').text());

        if (totalDebit !== totalCredit) {
            AppAlert.warning('Jurnal Tidak Seimbang', 'Total Debit dan Kredit harus sama (Balance).');
            return;
        }

        if (totalDebit <= 0) {
            AppAlert.warning('Total Tidak Valid', 'Total Debit/Kredit harus lebih besar dari 0.');
            return;
        }

        // Validasi Reference ID agar jurnal tidak melebihi nominal referensi
        let refType = $('#edit-reference-type').val();
        let refId = $('#edit-reference-id').val();
        
        if (refType && refId) {
            let refText = $('#edit-reference-id option:selected').text();
            let match = refText.match(/Rp\s*([\d\.]+)/);
            if (match) {
                let refTotal = window.AppFormat.unmaskNumber(match[1]);
                if (totalDebit > refTotal) {
                    AppAlert.warning('Total Melebihi Referensi', 'Total nominal Jurnal (Rp ' + window.AppFormat.formatRupiah(totalDebit) + ') tidak boleh melebihi nilai dokumen referensi (Rp ' + window.AppFormat.formatRupiah(refTotal) + ').');
                    return;
                }
            }
        }

        // Unmask inputs agar yang terkirim adalah angka asli tanpa titik ribuan
        form.find('.format-number').each(function() {
            let val = $(this).val();
            if(val) {
                $(this).val(window.AppFormat.unmaskNumber(val));
            }
        });

        let formData = new FormData(form[0]);
        // Add PUT method spoofing since form data might not have it implicitly via new FormData if it's outside form
        // Though @method('PUT') already adds _method=PUT to form data. Let's make sure.
        
        // Kembalikan mask setelah serialize agar form tidak rusak jika error
        window.AppFormat.init();

        window.ERPLoader.show('Menyimpan Perubahan...', 'Sistem sedang memproses data...');
        
        $.ajax({
            url: "{{ route('business.finance.journal.update', $journal->uuid) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                window.ERPLoader.hide();
                $('#editModal').modal('hide');
                window.refreshTable();
                AppAlert.success('Berhasil!', res.message);
            },
            error: function(err) {
                window.ERPLoader.hide();
                AppAlert.error('Gagal!', err.responseJSON?.message || 'Terjadi kesalahan sistem.');
            }
        });
    });

    calculateTotalEdit();
});
</script>

<script>
if (typeof window.previewAttachment !== 'function') {
    window.previewAttachment = function(url) {
        if (!url) return;
        let ext = url.split('?')[0].split('.').pop().toLowerCase();
        let content = '';
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
            content = `<img src="${url}" class="img-fluid" style="max-height: 85vh; width: auto; margin: 0 auto; display: block; object-fit: contain;">`;
        } else if (ext === 'pdf') {
            content = `<iframe src="${url}" style="width: 100%; height: 85vh; border: none;"></iframe>`;
        } else {
            window.open(url, '_blank');
            return;
        }
        
        if ($('#previewAttachmentModal').length === 0) {
            $('body').append(`
                <div class="modal fade" id="previewAttachmentModal" tabindex="-1" aria-hidden="true" style="z-index: 9999; backdrop-filter: blur(5px);">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content bg-dark border-0 shadow-lg overflow-hidden" style="border-radius: 12px;">
                            <div class="position-absolute top-0 end-0 z-3 p-3">
                                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 0.5rem;"></button>
                            </div>
                            <div class="modal-body text-center p-0 d-flex align-items-center justify-content-center" id="previewAttachmentContent" style="min-height: 400px; background: #1a1a1a;">
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
        $('#previewAttachmentContent').html(content);
        let previewModal = new bootstrap.Modal(document.getElementById('previewAttachmentModal'));
        previewModal.show();
    };
}
</script>
