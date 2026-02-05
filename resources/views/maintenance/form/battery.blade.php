@extends('layouts.app')

@section('styles')
    <style>
        .cell-card {
            border-width: 2px;
        }

        .item-card {
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .item-card.not-ok {
            background-color: rgba(220, 53, 69, 0.05);
        }

        .border-danger {
            background-color: #f6717e !important;
        }

        .border-success {
            background-color: #48cc8e !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5>Pengecekan Battery</h5>
                </div>
                <div class="card-body">
                    <form id="formBattery">
                        @csrf

                        {{-- HEADER --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Waktu</label>
                                <input type="time" name="waktu" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Battery Type</label>
                                <input type="text" name="battery_type" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>No Unit</label>
                                <input type="text" name="no_unit" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label>No Seri</label>
                                <input type="text" name="no_seri" class="form-control">
                            </div>

                        </div>

                        <hr>

                        {{-- DETAIL BATTERY --}}
                        <div class="d-flex justify-content-between mb-2">
                            <h6>Detail Battery (Minimal 12 Cell)</h6>
                        </div>

                        <div id="batteryContainer"></div>

                        <div class="row g-3 mb-3 mt-4">
                            <div class="col-md-12">
                                <label>Catatan</label>
                                <textarea class="form-control" name="catatan" id="catatan" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-primary" id="btnAddCell">
                                + Add Cell
                            </button>
                            <button type="button" class="btn btn-danger" id="btnReset">
                                Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal TTD --}}
    <div class="modal fade" id="modalTtd" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tanda Tangan Teknisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <canvas id="signature-pad" style="border:1px solid #ccc; width:100%; height:200px;"></canvas>

                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearTtd">
                            Reset TTD
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnSaveTtd">
                        Simpan & Kirim
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Pilih Approver --}}
    <div class="modal fade" id="modalApprover" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Approver</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="staffDropdown" class="form-label">Staff</label>
                        <select class="form-select" id="staffDropdown">
                            <option value="">Pilih staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="userDropdown" class="form-label">User MT/MTC</label>
                        <select class="form-select" id="userDropdown">
                            <option value="">Pilih user</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSelectApprover">Lanjut</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const STORAGE_KEY = 'form_mtc_battery_data';

            let cellIndex = 0;
            let cellNumber = 1;
            const MIN_CELL = 12;

            const batteryFields = {
                voltase: 'Voltase',
                level_air_aki: 'Level Air Aki',
                intercell: 'Intercell',
                kondisi_skun: 'Kondisi Skun',
                kondisi_unit: 'Kondisi Unit',
                grounding: 'Grounding',
            };

            function renderCellCard(isDefault = false) {
                let fieldsHtml = '';

                // Definisikan field yang pakai numeric input (bukan radio)
                const numericFields = ['voltase', 'grounding'];

                Object.entries(batteryFields).forEach(([field, label]) => {
                    const name = `details[${cellIndex}][${field}]`;

                    if (numericFields.includes(field)) {
                        // Field numeric: input number + optional step untuk desimal
                        fieldsHtml += `
                            <div class="col-md-6 col-12">
                                <div class="card shadow-sm item-card p-3 item-row">
                                    <label class="form-label fw-semibold">${label}</label>
                                    <input type="number" 
                                        class="form-control form-control-sm"
                                        name="${name}"
                                        step="0.01" 
                                        min="0"
                                        placeholder="Masukkan nilai (contoh: 12.5)"
                                        required>
                                    <small class="status-label-default fst-italic text-muted mt-1">
                                        Masukkan nilai pengukuran
                                    </small>
                                </div>
                            </div>
                        `;
                    } else {
                        // Field lain: radio OK / Tidak OK
                        const idOk = `${field}_${cellIndex}_ok`;
                        const idNg = `${field}_${cellIndex}_ng`;

                        fieldsHtml += `
                            <div class="col-md-6 col-12">
                                <div class="card shadow-sm item-card p-3 item-row">
                                    <label class="form-label fw-semibold">${label}</label>

                                    <div class="btn-group btn-group-sm w-100">
                                        <input type="radio"
                                            class="btn-check status-radio"
                                            name="${name}"
                                            value="1"
                                            id="${idOk}">
                                        <label class="btn btn-outline-success" for="${idOk}">OK</label>


                                        <input type="radio" class="btn-check status-radio"
                                            name="${name}" value="0" id="${idNg}">
                                        <label class="btn btn-outline-danger" for="${idNg}">Tidak OK</label>
                                    </div>

                                    <small class="status-label-default fst-italic">Belum dicek</small>
                                </div>
                            </div>
                        `;
                    }
                });

                let deleteBtn = '';

                if (!isDefault) {
                    deleteBtn =
                        `<button type="button" class="btn btn-sm btn-danger btnDeleteCell">Delete Cell</button>`;
                }

                let card = `
                    <div class="card mb-4 cell-card border">
                        <div class="card-header d-flex justify-content-between align-items-center fw-bold">
                            Cell ${cellNumber}
                            ${deleteBtn}
                        </div>

                        <div class="card-body">
                            <input type="hidden"
                                name="details[${cellIndex}][cell]"
                                value="${cellNumber}">

                            <div class="row g-3">
                                ${fieldsHtml}
                            </div>
                        </div>
                    </div>
                `;

                $('#batteryContainer').append(card);
                cellIndex++;
                cellNumber++;
            }

            // AUTO GENERATE 12 CELL
            for (let i = 0; i < MIN_CELL; i++) {
                renderCellCard(true);
            }

            loadFormFromLocal();

            // ADD CELL
            $('#btnAddCell').click(function() {
                renderCellCard(false);
                renumberCells();
                saveFormToLocal();
            });

            function renumberCells() {
                $('.cell-card').each(function(index) {
                    const newNumber = index + 1;

                    // Update header text (cari text node yang mengandung "Cell")
                    const $header = $(this).find('.card-header');
                    $header.contents().filter(function() {
                        return this.nodeType === 3 && this.textContent.trim().startsWith('Cell');
                    }).replaceWith(`Cell ${newNumber} `);

                    // Update hidden input cell number
                    $(this).find('input[name*="][cell]"]').val(newNumber);

                    // Update semua name attributes agar index sesuai urutan baru (penting!)
                    const oldIndexMatch = $(this).find('[name^="details["]').first().attr('name')?.match(
                        /details\[(\d+)\]/);
                    if (oldIndexMatch) {
                        const oldIndex = oldIndexMatch[1];
                        if (oldIndex != index) {
                            $(this).find('[name^="details["]').each(function() {
                                let name = $(this).attr('name');
                                name = name.replace(`details[${oldIndex}]`, `details[${index}]`);
                                $(this).attr('name', name);
                            });
                        }
                    }
                });

                // Update global counter
                cellIndex = $('.cell-card').length;
                cellNumber = cellIndex + 1;
            }
            // DELETE CELL (hanya > 12)
            $(document).on('click', '.btnDeleteCell', function() {
                $(this).closest('.cell-card').remove();
                renumberCells();
                saveFormToLocal();
            });

            // STATUS LABEL + WARNA
            $(document).on('change', '.status-radio', function() {
                const $container = $(this).closest('.item-card');
                let $noteContainer = $container.find('.note-container');

                if (!$noteContainer.length) {
                    $noteContainer = $(`
                        <div class="note-container mt-2 d-none">
                            <label class="form-label text-danger small">Keterangan kenapa Tidak OK</label>
                            <input type="text" class="form-control form-control-sm note-input"
                                name="${$(this).attr('name')}_note">
                        </div>
                    `);
                    $container.append($noteContainer);
                }

                const isNG = $(this).val() === '0';
                $noteContainer.toggleClass('d-none', !isNG);

                // Update label status juga (untuk visual)
                const $label = $container.find('.status-label-default');
                if ($(this).val() === '1') {
                    $label.text('OK').removeClass('text-danger fst-italic').addClass('text-success');
                } else if ($(this).val() === '0') {
                    $label.text('Tidak OK').removeClass('text-success fst-italic').addClass('text-danger');
                }

                saveFormToLocal(); // simpan tiap perubahan
            });

            $('#btnReset').on('click', function() {
                Swal.fire({
                    title: 'Reset Form?',
                    text: 'Semua isian akan dikosongkan',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, reset',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        localStorage.removeItem(STORAGE_KEY);
                        $('#formBattery')[0].reset();
                        $('.status-label-default').each(function() {
                            $(this)
                                .text('Belum dicek')
                                .removeClass('text-success text-danger')
                                .addClass('fst-italic text-muted');
                        });
                        $('.note-container').remove();
                        $('.status-radio').prop('checked', false);
                        $('.item-card').removeClass('border-danger border-success');
                    }
                });
            });

            function saveFormToLocal() {
                const formData = $('#formBattery').serializeArray();
                const data = {};

                formData.forEach(item => {
                    data[item.name] = item.value;
                });

                // Simpan status label (tetap seperti sebelumnya)
                $('.item-card').each(function() {
                    const $label = $(this).find('.status-label-default');
                    const name = $(this).find('input[type="radio"]').first().attr('name');
                    if (name) {
                        const labelText = $label.text().trim();
                        data[`label_${name}`] = labelText;
                    }
                });

                data.cellCount = $('.cell-card').length;

                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            }

            function loadFormFromLocal() {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) {
                    console.log("No saved data → keep default 12 cells");
                    return;
                }

                let data;
                try {
                    data = JSON.parse(saved);
                } catch (e) {
                    console.error("Corrupt localStorage:", e);
                    localStorage.removeItem(STORAGE_KEY);
                    return;
                }

                const savedCellCount = parseInt(data.cellCount) || MIN_CELL;


                if (savedCellCount > MIN_CELL) {
                    $('#batteryContainer').empty(); // Kosongkan kalau ada cell tambahan
                    cellIndex = 0;
                    cellNumber = 1;

                    for (let i = 0; i < savedCellCount; i++) {
                        const isDefault = i < MIN_CELL; // true hanya untuk 0-11 (cell 1-12)
                        renderCellCard(isDefault);
                    }
                }

                setTimeout(() => {
                    // 2. Load nilai ke semua field (radio, number, note)
                    Object.keys(data).forEach(key => {

                        const value = data[key];

                        // === RADIO BOOLEAN ===
                        if (key.startsWith('details[') && (value === '0' || value === '1')) {
                            const radioSelector =
                                `input[type="radio"][name="${CSS.escape(key)}"][value="${value}"]`;

                            const $radio = $(radioSelector);
                            if ($radio.length) {
                                $radio.prop('checked', true).trigger('change');
                            }
                            return;
                        }

                        // === INPUT LAIN ===
                        const $el = $(`[name="${CSS.escape(key)}"]`);
                        if ($el.length) {
                            $el.val(value);
                        }
                    });


                    // 3. Restore label status
                    $('.item-card').each(function(index) {
                        const name = $(this).find('input[type="radio"]').first().attr('name');
                        if (name && data[`label_${name}`]) {
                            const labelText = data[`label_${name}`];
                            const $label = $(this).find('.status-label-default');

                            $label.text(labelText);

                            if (labelText === 'OK') {
                                $label.removeClass('fst-italic text-danger').addClass(
                                    'text-success');
                            } else if (labelText === 'Tidak OK') {
                                $label.removeClass('fst-italic text-success').addClass(
                                    'text-danger');
                            } else {
                                $label.addClass('fst-italic');
                            }
                        }
                    });

                    // 4. Re-number supaya urut & update index name kalau perlu
                    renumberCells();

                    // console.log(`Loaded ${$('.cell-card').length} cells. Example note:`,
                    //     $('[name="details[0][level_air_aki]_note"]').val());

                }, 50); // Delay kecil untuk DOM settle

                cellIndex = savedCellCount;
                cellNumber = savedCellCount + 1;
            }

            // Panggil save di lebih banyak tempat
            $('#formBattery').on('change input', 'input, select, textarea', function() {
                saveFormToLocal();
            });

            $(document).on('click', '.btnDeleteCell', function() {
                $(this).closest('.cell-card').remove();
                saveFormToLocal(); // tambahkan ini
            });

            function collectNotOkDetails() {
                const notOkList = [];

                $('.item-card').each(function() {
                    const $item = $(this);

                    // Cek apakah field ini NG
                    const $ngRadio = $item.find('.status-radio[value="0"]:checked');
                    if (!$ngRadio.length) return;

                    // Label field (misal: Kondisi Skun)
                    const label = $item.find('label.form-label.fw-semibold').text().trim();

                    // Ambil nomor cell dari hidden input di parent .cell-card
                    const cellNumber = $item
                        .closest('.cell-card')
                        .find('input[name$="[cell]"]')
                        .val();

                    // Ambil catatan NG
                    const note =
                        $item.find('.note-input').val()?.trim() ||
                        '(tidak ada keterangan)';

                    // Format: Kondisi Skun 12: Miring
                    notOkList.push(`${label} ${cellNumber}: ${note}`);
                });

                return notOkList.join(' | ');
            }

            // Tanda Tangan
            let signaturePad = null;
            let pendingFormData = null;
            let selectedStaff = null;
            let selectedUser = null;

            $('#formBattery').on('submit', function(e) {
                e.preventDefault();
                pendingFormData = new FormData(this);

                $('#modalApprover').modal('show');

                // Load staff & user maintenance dari API
                $.get('/api/mtc/users/approvers', function(res) {
                    const $staffDropdown = $('#staffDropdown');
                    const $userDropdown = $('#userDropdown');

                    $staffDropdown.empty().append(`<option value="">Pilih staff</option>`);
                    res.staff.forEach(user => {
                        $staffDropdown.append(
                            `<option value="${user.id}">${user.username}</option>`);
                    });

                    $userDropdown.empty().append(`<option value="">Pilih user</option>`);
                    res.user.forEach(user => {
                        $userDropdown.append(
                            `<option value="${user.id}">${user.username}</option>`);
                    });
                });
            });

            // Pilih staff
            $('#staffDropdown').on('change', function() {
                selectedStaff = $(this).val();
            });

            // Pilih user maintenance
            $('#userDropdown').on('change', function() {
                selectedUser = $(this).val();
            });

            // Klik tombol pilih
            $('#btnSelectApprover').on('click', function() {
                if (!selectedStaff || !selectedUser) {
                    Swal.fire('Pilih staff dan user maintenance terlebih dahulu');
                    return;
                }
                pendingFormData.append('staff_id', selectedStaff);
                pendingFormData.append('user_id', selectedUser);

                $('#modalApprover').modal('hide');
                $('#modalTtd').modal('show'); // lanjut modal TTD
            });

            $('#modalTtd').on('shown.bs.modal', function() {
                if (!signaturePad) {
                    const canvas = document.getElementById('signature-pad');
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 200;
                    signaturePad = new SignaturePad(canvas);
                }
            });

            $('#btnClearTtd').on('click', function() {
                signaturePad.clear();
            });

            $('#modalTtd').on('hidden.bs.modal', function() {
                if (signaturePad) signaturePad.clear();
            });

            $('#btnSaveTtd').on('click', function() {
                if (!signaturePad || signaturePad.isEmpty()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'TTD belum diisi'
                    });
                    return;
                }

                for (let key of pendingFormData.keys()) {
                    if (key.includes('_note')) {
                        pendingFormData.delete(key);
                    }
                }

                const ttdBase64 = signaturePad.toDataURL('image/png');
                const keterangan = collectNotOkDetails();

                pendingFormData.append('ttd_base64', ttdBase64);

                if (keterangan) {
                    pendingFormData.append('keterangan', keterangan);
                }

                // Pastikan _token selalu fresh
                pendingFormData.delete('_token');
                pendingFormData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $('#modalTtd').modal('hide');

                submitFinalForm(pendingFormData);
            });

            function submitFinalForm(formData) {
                const $btn = $('#btn-submit');
                $btn.prop('disabled', true);

                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                $.ajax({
                    url: "{{ route('mtc.battery.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            localStorage.removeItem(STORAGE_KEY);
                            $('#formBattery')[0].reset();
                            $('.status-label-default').each(function() {
                                $(this)
                                    .text('Belum dicek')
                                    .removeClass('text-success text-danger')
                                    .addClass('fst-italic');
                            });
                            $('.note-container').remove();
                            // resetFormMotorPump();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            }
        });
    </script>
@endsection
