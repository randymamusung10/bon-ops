<x-modal id="createModal" title="Buat Jurnal Baru" size="xl">
    <form id="form-create-journal" enctype="multipart/form-data">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-form.label required>Tanggal Jurnal</x-form.label>
                <x-form.input type="date" name="date" value="{{ date('Y-m-d') }}" required />
            </div>
            <div class="col-md-4">
                <x-form.label>Tipe Referensi</x-form.label>
                <x-form.select name="reference_type" id="create-reference-type" class="select2-modal" style="width: 100%;">
                    <option value="">Tanpa Referensi</option>
                    <option value="PurchaseOrder">Purchase Order</option>
                    <option value="PosOrder">POS Order</option>
                    <option value="Manual">Manual</option>
                </x-form.select>
            </div>
            <div class="col-md-4">
                <x-form.label>ID Referensi</x-form.label>
                <x-form.select name="reference_id" id="create-reference-id" class="select2-modal" style="width: 100%;" disabled>
                    <option value="">Pilih Tipe Dulu</option>
                </x-form.select>
            </div>
            <div class="col-md-8 d-flex flex-column">
                <x-form.label>Keterangan Tambahan</x-form.label>
                <x-form.textarea name="notes" class="flex-grow-1" style="resize: none;" placeholder="Catatan jurnal..."></x-form.textarea>
            </div>
            <div class="col-md-4">
                <x-form.label>Lampiran Dokumen</x-form.label>
                <div class="position-relative p-3 text-center rounded-3 bg-light hover-shadow-sm" style="border: 1px dashed #cbd5e1; transition: all 0.2s;" id="dropzone-create">
                    <input type="file" name="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 2;" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this, 'create')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-cloud-arrow-up text-primary mb-1" style="font-size: 1.2rem;"></i>
                        <span class="text-muted fw-medium" style="font-size: 11px;" id="file-name-create">Pilih atau Letakkan File (Opsional)</span>
                        <span class="text-muted mt-1" style="font-size: 10px;">Maks 5MB (PDF/JPG/PNG)</span>
                    </div>
                </div>
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
                <button type="button" class="btn custom-btn btn-size-sm btn-variant-light" id="btn-load-reference" style="display: none;">
                    <i class="bi bi-arrow-clockwise me-1"></i> Muat Template
                </button>
                <x-button type="button" variant="ghost-primary" size="sm" id="btn-add-item" icon="bi-plus">Tambah Baris</x-button>
            </div>
        </div>

        <div class="table-responsive rounded-4 overflow-hidden mb-3" style="border: 1px solid rgba(226, 232, 240, 0.2);">
            <table class="table align-middle mb-0" id="items-table" style="font-size: 13px; --bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.2);">
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
                    <!-- Minimal 2 baris untuk start -->
                    <tr>
                        <td class="py-2 ps-4">
                            <x-form.select name="items[0][chart_of_account_id]" class="account-select" style="width: 100%;">
                                <option value="">Pilih Akun</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </x-form.select>
                        </td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[0][description]" placeholder="Contoh: Pendapatan jasa..." /></td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[0][debit]" class="text-end input-debit format-number" value="0" required /></td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[0][credit]" class="text-end input-credit format-number" value="0" required /></td>
                        <td class="text-center py-2 pe-4">
                            <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 ps-4">
                            <x-form.select name="items[1][chart_of_account_id]" class="account-select" style="width: 100%;">
                                <option value="">Pilih Akun</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </x-form.select>
                        </td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[1][description]" placeholder="Contoh: Kas atau Bank..." /></td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[1][debit]" class="text-end input-debit format-number" value="0" required /></td>
                        <td class="py-2"><x-form.input size="sm" type="text" name="items[1][credit]" class="text-end input-credit format-number" value="0" required /></td>
                        <td class="text-center py-2 pe-4">
                            <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                        <td colspan="2" class="py-2 text-end fw-bold">Total Keseluruhan (Rp)</td>
                        <td class="text-end py-3 pe-4 fw-bold" style="color: var(--primary-accent);"><span id="total-debit">0</span></td>
                        <td class="text-end py-3 pe-4 fw-bold" style="color: var(--primary-accent);"><span id="total-credit">0</span></td>
                        <td class="py-2 pe-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div id="balance-warning" class="text-danger small fw-semibold mt-1 mb-3" style="display:none;"><i class="bi bi-exclamation-triangle me-1"></i> Total Debit dan Kredit harus seimbang (Balance).</div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.2) !important;">
            <x-button type="button" variant="light" size="sm" data-bs-dismiss="modal">Batal</x-button>
            <x-button type="button" variant="primary" size="sm" id="btn-save-journal" icon="bi-check2">Simpan Draft</x-button>
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

if (typeof window.previewAttachment !== 'function') {
    window.previewAttachment = function(url) {
        if (!url) return;
        let ext = url.split('?')[0].split('.').pop().toLowerCase();
        let content = '';
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
            content = `<img src="${url}" class="img-fluid rounded-3" style="max-height: 75vh; width: auto; margin: 0 auto; display: block;">`;
        } else if (ext === 'pdf') {
            content = `<iframe src="${url}" style="width: 100%; height: 75vh; border: none; border-radius: 8px;"></iframe>`;
        } else {
            window.open(url, '_blank');
            return;
        }
        
        if ($('#previewAttachmentModal').length === 0) {
            $('body').append(`
                <div class="modal fade" id="previewAttachmentModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header border-bottom-0 pb-0">
                                <h5 class="modal-title fw-bold">Preview Lampiran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center pt-2 pb-4 px-4" id="previewAttachmentContent">
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

$(document).ready(function() {
    let itemIndex = 2;

    $('.account-select').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#createModal')
    });

    $('#create-reference-type').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#createModal')
    });

    $('#create-reference-type').on('change', function() {
        let type = $(this).val();
        let refIdSelect = $('#create-reference-id');
        refIdSelect.empty().append('<option value="">Pilih Referensi</option>');
        
        if (!type || type === 'Manual') {
            if (refIdSelect.hasClass("select2-hidden-accessible")) {
                refIdSelect.select2('destroy');
            }
            refIdSelect.select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#createModal'),
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
            dropdownParent: $('#createModal'),
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

    $('#create-reference-type').trigger('change');

    function calculateTotal() {
        let totalDebit = 0;
        let totalCredit = 0;

        $('#items-table tbody tr').each(function() {
            let debitStr = window.AppFormat.unmaskNumber($(this).find('.input-debit').val());
            let creditStr = window.AppFormat.unmaskNumber($(this).find('.input-credit').val());
            
            let debitVal = parseFloat(debitStr) || 0;
            let creditVal = parseFloat(creditStr) || 0;

            totalDebit += debitVal;
            totalCredit += creditVal;
        });

        $('#total-debit').text(window.AppFormat.formatRupiah(totalDebit));
        $('#total-credit').text(window.AppFormat.formatRupiah(totalCredit));

        if (totalDebit !== totalCredit) {
            $('#balance-warning').show();
            $('#btn-save-journal').prop('disabled', true);
        } else {
            $('#balance-warning').hide();
            $('#btn-save-journal').prop('disabled', false);
        }
    }

    $(document).on('input', '.input-debit, .input-credit', function() {
        calculateTotal();
    });

    $('#btn-add-item').on('click', function() {
        let tr = `
        <tr>
            <td class="py-2 ps-4">
                <x-form.select name="items[${itemIndex}][chart_of_account_id]" class="account-select" style="width: 100%;">
                    <option value="">Pilih Akun</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                    @endforeach
                </x-form.select>
            </td>
            <td class="py-2"><x-form.input size="sm" type="text" name="items[${itemIndex}][description]" placeholder="Keterangan baris..." /></td>
            <td class="py-2"><x-form.input size="sm" type="text" name="items[${itemIndex}][debit]" class="text-end input-debit format-number" value="0" required /></td>
            <td class="py-2"><x-form.input size="sm" type="text" name="items[${itemIndex}][credit]" class="text-end input-credit format-number" value="0" required /></td>
            <td class="text-center py-2 pe-4">
                <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
        `;
        $('#items-table tbody').append(tr);
        
        $('#items-table tbody tr:last .account-select').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#createModal')
        });

        itemIndex++;
    });

    $(document).on('click', '.btn-remove-item', function() {
        if ($('#items-table tbody tr').length <= 2) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak dapat menghapus baris',
                text: 'Minimal harus ada 2 baris jurnal untuk mencatat Debit dan Kredit.',
            });
            return;
        }
        $(this).closest('tr').remove();
        calculateTotal();
    });

    function triggerLoadReference(type, id) {
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
                            $('#items-table tbody').empty();
                            itemIndex = 0;
                            
                            res.data.forEach(function(item) {
                                let tr = `
                                <tr>
                                    <td class="py-2 ps-4">
                                        <select name="items[${itemIndex}][chart_of_account_id]" class="form-select account-select" style="width: 100%;">
                                            <option value="">Pilih Akun</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2"><input class="form-control form-control-sm" type="text" name="items[${itemIndex}][description]" value="${item.description}" placeholder="Keterangan baris..." /></td>
                                    <td class="py-2"><input class="form-control form-control-sm text-end input-debit format-number" type="text" name="items[${itemIndex}][debit]" value="${item.debit}" required /></td>
                                    <td class="py-2"><input class="form-control form-control-sm text-end input-credit format-number" type="text" name="items[${itemIndex}][credit]" value="${item.credit}" required /></td>
                                    <td class="text-center py-2 pe-4">
                                        <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                `;
                                let $tr = $(tr);
                                $tr.find('.account-select').val(item.account_id);
                                $('#items-table tbody').append($tr);
                                itemIndex++;
                            });
                            
                            // Re-init components
                            $('.account-select').select2({
                                dropdownParent: $('#createModal'),
                                theme: 'bootstrap-5'
                            });
                            window.AppFormat.init();
                            calculateTotal();
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
                $('#items-table tbody').empty();
                itemIndex = 0;
                
                for(let i=0; i<2; i++) {
                    let tr = `
                    <tr>
                        <td class="py-2 ps-4">
                            <select name="items[${itemIndex}][chart_of_account_id]" class="form-select account-select" style="width: 100%;">
                                <option value="">Pilih Akun</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-2"><input class="form-control form-control-sm" type="text" name="items[${itemIndex}][description]" placeholder="Keterangan baris..." /></td>
                        <td class="py-2"><input class="form-control form-control-sm text-end input-debit format-number" type="text" name="items[${itemIndex}][debit]" value="0" required /></td>
                        <td class="py-2"><input class="form-control form-control-sm text-end input-credit format-number" type="text" name="items[${itemIndex}][credit]" value="0" required /></td>
                        <td class="text-center py-2 pe-4">
                            <button type="button" class="btn-icon-modern text-danger btn-remove-item mx-auto" title="Hapus" style="background: rgba(239, 68, 68, 0.12);"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    `;
                    $('#items-table tbody').append(tr);
                    itemIndex++;
                }
                
                $('.account-select').select2({ dropdownParent: $('#createModal'), theme: 'bootstrap-5' });
                window.AppFormat.init();
                calculateTotal();
                AppAlert.info('Pengisian Manual', 'Tabel baris jurnal telah disiapkan untuk pengisian manual.');
            } else if (result.isDismissed) {
                // Reset reference ID if user cancels the whole action
                $('#create-reference-id').val(null).trigger('change');
                $('#btn-load-reference').hide();
            }
        });
    }

    $('#btn-load-reference').on('click', function() {
        let type = $('#create-reference-type').val();
        let id = $('#create-reference-id').val();
        if (type && id) {
            triggerLoadReference(type, id);
        }
    });

    $('#create-reference-id').on('select2:select', function(e) {
        let type = $('#create-reference-type').val();
        let id = e.params.data.id;
        
        if (type && id) {
            $('#btn-load-reference').show();
            triggerLoadReference(type, id);
        } else {
            $('#btn-load-reference').hide();
        }
    });

    $('#create-reference-id').on('select2:clear', function(e) {
        $('#btn-load-reference').hide();
    });

    $('#btn-save-journal').on('click', function() {
        let form = $('#form-create-journal');
        
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

        let totalDebit = window.AppFormat.unmaskNumber($('#total-debit').text());
        let totalCredit = window.AppFormat.unmaskNumber($('#total-credit').text());

        if (totalDebit !== totalCredit) {
            AppAlert.warning('Jurnal Tidak Seimbang', 'Total Debit dan Kredit harus sama (Balance).');
            return;
        }

        if (totalDebit <= 0) {
            AppAlert.warning('Total Tidak Valid', 'Total Debit/Kredit harus lebih besar dari 0.');
            return;
        }

        // Validasi Reference ID agar jurnal tidak melebihi nominal referensi
        let refType = $('#create-reference-type').val();
        let refId = $('#create-reference-id').val();
        
        if (refType && refId) {
            let refText = $('#create-reference-id option:selected').text();
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
        
        // Kembalikan mask setelah serialize agar form tidak rusak jika error
        window.AppFormat.init();
        
        window.ERPLoader.show('Menyimpan Jurnal...', 'Sistem sedang memproses data...');
        
        $.ajax({
            url: "{{ route('business.finance.journal.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                window.ERPLoader.hide();
                $('#createModal').modal('hide');
                window.refreshTable();
                AppAlert.success('Berhasil!', res.message);
            },
            error: function(err) {
                window.ERPLoader.hide();
                AppAlert.error('Gagal!', err.responseJSON?.message || 'Terjadi kesalahan sistem.');
            }
        });
    });

    // Initial check
    calculateTotal();
});
</script>
