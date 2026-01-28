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
                            <div class="col-md-3">
                                <label>Keterangan</label>
                                <input type="text" name="keterangan" class="form-control">
                            </div>
                        </div>

                        <hr>

                        {{-- DETAIL BATTERY --}}
                        <div class="d-flex justify-content-between mb-2">
                            <h6>Detail Battery (Minimal 12 Cell)</h6>
                        </div>

                        <div id="batteryContainer"></div>

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
                Object.entries(batteryFields).forEach(([field, label]) => {
                    const name = `details[${cellIndex}][${field}]`;
                    const idOk = `${field}_${cellIndex}_ok`;
                    const idNg = `${field}_${cellIndex}_ng`;

                    fieldsHtml += `
                            <div class="col-md-6 col-12">
                                <div class="card shadow-sm item-card p-3 item-row">
                                    <label class="form-label fw-semibold">${label}</label>

                                    <div class="btn-group btn-group-sm w-100">
                                        <input type="radio" class="btn-check status-radio"
                                            name="${name}" value="1" id="${idOk}" required>
                                        <label class="btn btn-outline-success" for="${idOk}">OK</label>

                                        <input type="radio" class="btn-check status-radio"
                                            name="${name}" value="0" id="${idNg}">
                                        <label class="btn btn-outline-danger" for="${idNg}">Tidak OK</label>
                                    </div>

                                    <small class="status-label-default fst-italic">Belum dicek</small>
                                </div>
                            </div>
                        `;
                });

                let deleteBtn = (!isDefault) ?
                    `<button type="button" class="btn btn-sm btn-danger btnDeleteCell">Delete Cell</button>` :
                    '';

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
                saveFormToLocal();
            });

            // DELETE CELL (hanya > 12)
            $(document).on('click', '.btnDeleteCell', function() {
                $(this).closest('.cell-card').remove();
            });

            // STATUS LABEL + WARNA
            $(document).on('change', '.status-radio', function() {
                let wrapper = $(this).closest('.item-card');
                let label = wrapper.find('.status-label-default');

                if ($(this).val() === '1') {
                    label.text('OK').removeClass('text-danger fst-italic').addClass('text-success');
                } else if ($(this).val() === '0') {
                    label.text('Tidak OK').removeClass('text-success fst-italic').addClass('text-danger');
                }

                // SAVE tiap perubahan
                saveFormToLocal();
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
                        // location.reload();
                        $('#formBattery')[0].reset();
                    }
                });
            });

            function saveFormToLocal() {
                const formData = $('#formBattery').serializeArray();
                const data = {};

                formData.forEach(item => {
                    data[item.name] = item.value;
                });

                // Simpan status label untuk SEMUA item-card (bukan hanya yang checked)
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
                // console.log("Saved data:", data); // debug
            }

            function loadFormFromLocal() {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) {
                    console.log("Tidak ada data tersimpan → pakai 12 cell default yang sudah dibuat");
                    return;
                }

                let data;
                try {
                    data = JSON.parse(saved);
                    // console.log("Data dari local:", data);
                } catch (e) {
                    // console.error("Local corrupt:", e);
                    localStorage.removeItem(STORAGE_KEY);
                    return;
                }

                const savedCellCount = parseInt(data.cellCount) || MIN_CELL;

                // Selalu isi header dulu (header tidak boleh di-reset)
                $('#formBattery input, #formBattery select, #formBattery textarea').each(function() {
                    const name = $(this).attr('name');
                    if (name && data.hasOwnProperty(name) && data[name] !== undefined) {
                        if ($(this).attr('type') === 'radio') {
                            const value = data[name];
                            if (value) {
                                $(`input[name="${name}"][value="${value}"]`).prop('checked', true).trigger(
                                    'change');
                            }
                        } else {
                            $(this).val(data[name] || ''); // pakai '' kalau null/undefined
                        }
                    }

                });

                // Baru tangani cell
                if (savedCellCount <= MIN_CELL) {
                    for (let i = 0; i < savedCellCount; i++) {
                        Object.keys(data).forEach(key => {
                            if (key.startsWith(`details[${i}][`)) {
                                const field = key.split('[').pop().replace(']', '');
                                const value = data[key];
                                if (value) {
                                    const selector =
                                        `input[name="details[${i}][${field}]"][value="${value}"]`;
                                    const $radio = $(selector);

                                    if ($radio.length) {
                                        $radio.prop('checked', true);

                                        // Update label secara manual (ini yang paling penting)
                                        const $wrapper = $radio.closest('.item-card');
                                        const $label = $wrapper.find('.status-label-default');
                                        const savedLabelKey =
                                            `label_details[${i}][${field}]`; // pakai format persis sama dengan save

                                        let labelText = 'Belum dicek';
                                        let classToAdd = 'fst-italic';
                                        let classToRemove = 'text-success text-danger';

                                        if (data[savedLabelKey]) {
                                            labelText = data[savedLabelKey];
                                            if (labelText === 'OK') {
                                                classToAdd = 'text-success';
                                                classToRemove = 'text-danger fst-italic';
                                            } else if (labelText === 'Tidak OK') {
                                                classToAdd = 'text-danger';
                                                classToRemove = 'text-success fst-italic';
                                            }
                                            // Kalau "Belum dicek" tetap default
                                        }

                                        $label.text(labelText).removeClass(classToRemove).addClass(
                                            classToAdd);

                                        // Trigger change untuk konsistensi
                                        $radio.trigger('change');
                                    } else {
                                        console.warn(
                                            `Radio tidak ditemukan untuk cell ${i}, field ${field}:`,
                                            selector);
                                    }
                                }
                            }
                        });
                    }
                } else {
                    $('#batteryContainer').empty();
                    cellIndex = 0;
                    cellNumber = 1;

                    for (let i = 0; i < savedCellCount; i++) {
                        const isDefault = i < MIN_CELL;
                        renderCellCard(isDefault);

                        Object.keys(data).forEach(key => {
                            if (key.startsWith(`details[${i}][`)) {
                                const field = key.split('[').pop().replace(']', '');
                                const value = data[key];

                                if (value) {
                                    const selector =
                                        `input[name="details[${i}][${field}]"][value="${value}"]`;
                                    const $radio = $(selector);

                                    if ($radio.length) {
                                        $radio.prop('checked', true);

                                        // Update label secara manual (ini yang paling penting)
                                        const $wrapper = $radio.closest('.item-card');
                                        const $label = $wrapper.find('.status-label-default');
                                        const savedLabelKey =
                                            `label_details[${i}][${field}]`; // pakai format persis sama dengan save

                                        let labelText = 'Belum dicek';
                                        let classToAdd = 'fst-italic';
                                        let classToRemove = 'text-success text-danger';

                                        if (data[savedLabelKey]) {
                                            labelText = data[savedLabelKey];
                                            if (labelText === 'OK') {
                                                classToAdd = 'text-success';
                                                classToRemove = 'text-danger fst-italic';
                                            } else if (labelText === 'Tidak OK') {
                                                classToAdd = 'text-danger';
                                                classToRemove = 'text-success fst-italic';
                                            }
                                            // Kalau "Belum dicek" tetap default
                                        }

                                        $label.text(labelText).removeClass(classToRemove).addClass(
                                            classToAdd);

                                        // Trigger change untuk konsistensi
                                        $radio.trigger('change');
                                    } else {
                                        console.warn(
                                            `Radio tidak ditemukan untuk cell ${i}, field ${field}:`,
                                            selector);
                                    }
                                }
                            }
                        });
                    }
                }

                // Update counter global
                cellIndex = Math.max(savedCellCount, MIN_CELL);
                cellNumber = cellIndex + 1;

                // console.log("Load selesai. Total cell:", $('.cell-card').length);
            }

            // Panggil save di lebih banyak tempat
            $('#formBattery').on('change input', 'input, select, textarea', function() {
                saveFormToLocal();
            });

            $(document).on('click', '.btnDeleteCell', function() {
                $(this).closest('.cell-card').remove();
                saveFormToLocal(); // tambahkan ini
            });

            // SUBMIT AJAX
            $('#formBattery').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('mtc.battery.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('.btn-success').prop('disabled', true).text('Menyimpan...');
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Data berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        })

                        $('#formBattery')[0].reset();
                        localStorage.removeItem(STORAGE_KEY);
                        $('.status-label-default').each(function() {
                            $(this)
                                .text('Belum dicek')
                                .removeClass('text-success text-danger')
                                .addClass('fst-italic');
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        let msg = '';

                        if (errors) {
                            Object.values(errors).forEach(err => {
                                msg += err[0] + '\n';
                            });
                        } else {
                            msg = 'Terjadi kesalahan.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat menyimpan data'
                        });
                    },
                    complete: function() {
                        $('.btn-success').prop('disabled', false).text('Simpan Data');
                    }
                });
            });
        });
    </script>
@endsection
