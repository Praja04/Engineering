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
                                <label>Waktu Mulai</label>
                                <input type="text" name="waktu_mulai" id="waktu_mulai" class="form-control"
                                    placeholder="Pilih waktu" required>
                            </div>
                            <div class="col-md-3">
                                <label>Waktu Selesai</label>
                                <input type="text" name="waktu_selesai" id="waktu_selesai" class="form-control"
                                    placeholder="Pilih waktu">
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
                            <div class="col-md-3">
                                <label>Total Voltase</label>
                                <input type="number" name="total_voltase" class="form-control" step="0.01"
                                    min="0" placeholder="Masukkan total voltase">
                            </div>
                        </div>

                        <hr>

                        {{-- OVERALL BATTERY CHECKLISTS --}}
                        <div class="card p-3 mb-3 shadow-sm border-light">
                            <h6 class="fw-bold mb-3 text-primary">Kondisi Keseluruhan Baterai</h6>
                            <div class="row g-3">
                                <!-- Intercell -->
                                <div class="col-md-3 col-sm-6 col-12">
                                    <div class="card shadow-sm item-card p-3 item-row-main">
                                        <label class="form-label fw-semibold">Intercell</label>
                                        <div class="btn-group btn-group-sm w-100">
                                            <input type="radio" class="btn-check status-radio" name="intercell"
                                                value="1" id="intercell_ok">
                                            <label class="btn btn-outline-success" for="intercell_ok">OK</label>
                                            <input type="radio" class="btn-check status-radio" name="intercell"
                                                value="0" id="intercell_ng">
                                            <label class="btn btn-outline-danger" for="intercell_ng">Tidak OK</label>
                                        </div>
                                        <small class="status-label-default fst-italic mt-1 text-muted">Belum dicek</small>
                                    </div>
                                </div>
                                <!-- Kondisi Skun -->
                                <div class="col-md-3 col-sm-6 col-12">
                                    <div class="card shadow-sm item-card p-3 item-row-main">
                                        <label class="form-label fw-semibold">Kondisi Skun</label>
                                        <div class="btn-group btn-group-sm w-100">
                                            <input type="radio" class="btn-check status-radio" name="kondisi_skun"
                                                value="1" id="kondisi_skun_ok">
                                            <label class="btn btn-outline-success" for="kondisi_skun_ok">OK</label>
                                            <input type="radio" class="btn-check status-radio" name="kondisi_skun"
                                                value="0" id="kondisi_skun_ng">
                                            <label class="btn btn-outline-danger" for="kondisi_skun_ng">Tidak OK</label>
                                        </div>
                                        <small class="status-label-default fst-italic mt-1 text-muted">Belum dicek</small>
                                    </div>
                                </div>
                                <!-- Kondisi Unit -->
                                <div class="col-md-3 col-sm-6 col-12">
                                    <div class="card shadow-sm item-card p-3 item-row-main">
                                        <label class="form-label fw-semibold">Kondisi Unit</label>
                                        <div class="btn-group btn-group-sm w-100">
                                            <input type="radio" class="btn-check status-radio" name="kondisi_unit"
                                                value="1" id="kondisi_unit_ok">
                                            <label class="btn btn-outline-success" for="kondisi_unit_ok">OK</label>
                                            <input type="radio" class="btn-check status-radio" name="kondisi_unit"
                                                value="0" id="kondisi_unit_ng">
                                            <label class="btn btn-outline-danger" for="kondisi_unit_ng">Tidak OK</label>
                                        </div>
                                        <small class="status-label-default fst-italic mt-1 text-muted">Belum dicek</small>
                                    </div>
                                </div>
                                <!-- Kondisi Plug Battery -->
                                <div class="col-md-3 col-sm-6 col-12">
                                    <div class="card shadow-sm item-card p-3 item-row-main">
                                        <label class="form-label fw-semibold">Kondisi Plug Battery</label>
                                        <div class="btn-group btn-group-sm w-100">
                                            <input type="radio" class="btn-check status-radio"
                                                name="kondisi_plug_battery" value="1" id="plug_battery_ok">
                                            <label class="btn btn-outline-success" for="plug_battery_ok">OK</label>
                                            <input type="radio" class="btn-check status-radio"
                                                name="kondisi_plug_battery" value="0" id="plug_battery_ng">
                                            <label class="btn btn-outline-danger" for="plug_battery_ng">Tidak OK</label>
                                        </div>
                                        <small class="status-label-default fst-italic mt-1 text-muted">Belum dicek</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- DETAIL BATTERY --}}
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h6 class="mb-0">Detail Battery (<span id="cellCountLabel">12</span> Cell)</h6>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-secondary fw-semibold small">Tipe Unit:</span>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Tipe Unit Filter">
                                    <input type="radio" class="btn-check" name="tipe_unit_selector" id="unit_pm"
                                        value="pm" checked autocomplete="off">
                                    <label class="btn btn-outline-dark" for="unit_pm">Pallet Mover / ES (12 Cell)</label>

                                    <input type="radio" class="btn-check" name="tipe_unit_selector" id="unit_forklift"
                                        value="forklift" autocomplete="off">
                                    <label class="btn btn-outline-dark" for="unit_forklift">Forklift (24 Cell)</label>
                                </div>
                            </div>
                        </div>

                        <div id="batteryContainer"></div>

                        <div class="row g-3 mb-3 mt-4">
                            <div class="col-md-12">
                                <label>Grounding</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-medium">Negatif</span>
                                    <input type="number" id="grounding_neg" class="form-control" step="0.01"
                                        min="0" placeholder="Negatif V" required>
                                    <span class="input-group-text bg-light fw-medium">V</span>
                                    <span class="input-group-text bg-light fw-medium">Positif</span>
                                    <input type="number" id="grounding_pos" class="form-control" step="0.01"
                                        min="0" placeholder="Positif V" required>
                                    <span class="input-group-text bg-light fw-medium">V</span>
                                </div>
                                <input type="hidden" name="grounding" id="grounding">
                            </div>
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
                    <img src="{{ asset('storage/mtc/ttd/ttd_teknisi.jpeg') }}"
                        style="max-width: 100%; border: 1px solid #ccc;">
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
                        <label class="form-label">Staff Engineering</label>
                        <select class="form-select" id="staffDropdown">
                            <option value="">Pilih staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User MT/MTC</label>
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

            flatpickr("#waktu_mulai", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1,
            });

            flatpickr("#waktu_selesai", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1,
            });
            let cellIndex = 0;
            let cellNumber = 1;
            const MIN_CELL = 12;

            const batteryFields = {
                voltase: 'Voltase',
                level_air_aki: 'Level Air Aki',
            };

            function renderCellCard(isDefault = false) {
                let fieldsHtml = '';

                // Definisikan field yang pakai numeric input (bukan radio)
                const numericFields = ['voltase'];

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

            // Dynamic cell generation function
            function generateCells(count) {
                $('#batteryContainer').empty();
                cellIndex = 0;
                cellNumber = 1;
                $('#cellCountLabel').text(count);

                for (let i = 0; i < count; i++) {
                    renderCellCard(true); // Default cells (12 or 24) cannot be deleted
                }
            }

            // Initial generation (default 12 cells for PM)
            generateCells(12);

            // Tipe Unit selection listener
            $('input[name="tipe_unit_selector"]').on('change', function() {
                const val = $(this).val();
                const count = (val === 'forklift') ? 24 : 12;

                let hasData = false;
                $('#batteryContainer input[type="number"]').each(function() {
                    if ($(this).val() !== '') hasData = true;
                });
                $('#batteryContainer input[type="radio"]:checked').each(function() {
                    hasData = true;
                });

                if (hasData) {
                    Swal.fire({
                        title: 'Ganti Tipe Unit?',
                        text: 'Mengubah tipe unit akan mengosongkan semua data cell yang sudah diisi.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Ganti',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            generateCells(count);
                        } else {
                            if (val === 'forklift') {
                                $('#unit_pm').prop('checked', true);
                            } else {
                                $('#unit_forklift').prop('checked', true);
                            }
                        }
                    });
                } else {
                    generateCells(count);
                }
            });

            // ADD CELL
            $('#btnAddCell').click(function() {
                renderCellCard(false);
                renumberCells();
            });

            function renumberCells() {
                $('.cell-card').each(function(index) {
                    const newNumber = index + 1;

                    // Update header text
                    const $header = $(this).find('.card-header');
                    $header.contents().filter(function() {
                        return this.nodeType === 3 && this.textContent.trim().startsWith('Cell');
                    }).replaceWith(`Cell ${newNumber} `);

                    // Update hidden input cell number
                    $(this).find('input[name*="][cell]"]').val(newNumber);

                    // Update all name attributes index
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

            // DELETE CELL
            $(document).on('click', '.btnDeleteCell', function() {
                $(this).closest('.cell-card').remove();
                renumberCells();
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

                const isNG = $(this).val() === '0' || $(this).val() === 'Tidak OK';
                $noteContainer.toggleClass('d-none', !isNG);

                // Update label status juga (untuk visual)
                const $label = $container.find('.status-label-default');
                if ($(this).val() === '1' || $(this).val() === 'OK') {
                    $label.text('OK').removeClass('text-danger fst-italic').addClass('text-success');
                } else if ($(this).val() === '0' || $(this).val() === 'Tidak OK') {
                    $label.text('Tidak OK').removeClass('text-success fst-italic').addClass('text-danger');
                }
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
                        $('#formBattery')[0].reset();
                        $('#unit_pm').prop('checked', true);
                        generateCells(12);
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

            function collectNotOkDetails() {
                const notOkList = [];

                $('.item-card').each(function() {
                    const $item = $(this);

                    // Cek apakah field ini NG (0 atau Tidak OK)
                    const $ngRadio = $item.find(
                        '.status-radio[value="0"]:checked, .status-radio[value="Tidak OK"]:checked');
                    if (!$ngRadio.length) return;

                    // Label field
                    const label = $item.find('label.form-label.fw-semibold').text().trim();

                    // Ambil nomor cell
                    const cellCard = $item.closest('.cell-card');
                    const cellNumber = cellCard.length ? cellCard.find('input[name$="[cell]"]').val() :
                        null;

                    // Ambil catatan NG
                    const note = $item.find('.note-input').val()?.trim() || '(tidak ada keterangan)';

                    if (cellNumber) {
                        notOkList.push(`${label} ${cellNumber}: ${note}`);
                    } else {
                        notOkList.push(`${label} (Overall): ${note}`);
                    }
                });

                return notOkList.join(' | ');
            }

            // Tanda Tangan
            let pendingFormData = null;
            let selectedStaff = null;
            let selectedUser = null;

            $('#formBattery').on('submit', function(e) {
                e.preventDefault();

                // Join Negative V and Positive V grounding values before creating FormData
                const neg = $('#grounding_neg').val();
                const pos = $('#grounding_pos').val();
                $('#grounding').val(neg || pos ? `${neg || '0'} / ${pos || '0'}` : '');

                pendingFormData = new FormData(this);
                $('#modalApprover').modal('show');

                $.get('/api/mtc/users/approvers', function(res) {
                    const $staffDropdown = $('#staffDropdown');
                    $staffDropdown.empty().append('<option value="">Pilih staff</option>');
                    res.staff.forEach(u => {
                        $staffDropdown.append(
                            `<option value="${u.id}">${u.username}</option>`);
                    });

                    const $userDropdown = $('#userDropdown');
                    $userDropdown.empty().append('<option value="">Pilih user</option>');
                    res.user.forEach(u => {
                        $userDropdown.append(
                            `<option value="${u.id}">${u.username}</option>`);
                    });
                });
            });

            $(document).on('change', '#staffDropdown', function() {
                selectedStaff = $(this).val() || null;
            });

            $(document).on('change', '#userDropdown', function() {
                selectedUser = $(this).val() || null;
            });

            $('#btnSelectApprover').on('click', function() {
                if (!selectedStaff || !selectedUser) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih approver',
                        text: 'Pilih staff dan user MT/MTC terlebih dahulu'
                    });
                    return;
                }
                pendingFormData.append('staff_id', selectedStaff);
                pendingFormData.append('user_id', selectedUser);
                $('#modalApprover').modal('hide');
                $('#modalTtd').modal('show');
            });

            $('#btnSaveTtd').on('click', function() {
                for (let key of pendingFormData.keys()) {
                    if (key.includes('_note')) {
                        pendingFormData.delete(key);
                    }
                }

                const keterangan = collectNotOkDetails();
                pendingFormData.append('ttd_path', 'mtc/ttd/ttd_teknisi.jpeg');
                if (keterangan) {
                    pendingFormData.append('keterangan', keterangan);
                }
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
