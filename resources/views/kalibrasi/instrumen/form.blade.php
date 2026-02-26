@extends('layouts.app')

@section('title', 'Form Instrumen')

@section('styles')
    <style>
        #tableInstrumen td {
            padding: 4px 6px !important;
            /* kecilin padding */
            vertical-align: middle;
        }

        .mini-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .input-mini {
            width: 100%;
            padding: 4px;
            font-size: 12px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fcf0cc;
        }

        .input-mini:focus {
            outline: none;
            border-color: #0d6efd;
        }

        .checkbox-lg {
            transform: scale(1.5);
            /* ubah 1.3 - 1.8 sesuai selera */
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title d-sm-flex align-items-center justify-content-start">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Tombol Back -->
                            <a href="{{ route('kalibrasi.form.dashboard') }}"
                                class="btn btn-outline-primary rounded-pill px-4 d-flex align-items-center">
                                <i class="mdi mdi-arrow-left me-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header bg-soft-primary py-3">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-between g-2">
                            <div class="col-12 col-sm-auto d-flex align-items-center flex-wrap gap-2">
                                <h5 class="mb-0 d-flex align-items-center">
                                    Form Kalibrasi Instrumen
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card-body">
                    <form id="formInstrumen" method="POST">
                        @csrf

                        {{-- === Data Utama Kalibrasi === --}}
                        <div class="card border border-primary border-opacity-50 mb-4">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-clipboard-list-outline me-2"></i>Data Utama Kalibrasi</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 gy-3">
                                    <div class="col-lg-6 col-md-8 col-sm-12">
                                        <label for="alat_id" class="form-label">Pilih Alat <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select class="form-select" id="alat_id" name="alat_id" required>
                                                <option value="">-- Pilih Alat --</option>
                                                @foreach ($alat as $alat)
                                                    <option value="{{ $alat->id }}">
                                                        {{ $alat->kode_alat . ' - ' . $alat->nama_alat }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="btnDetail" class="btn btn-outline-primary"
                                                data-bs-toggle="collapse" data-bs-target="#collapseAlatDetail" disabled>
                                                <i class="mdi mdi-information-outline me-2"></i>Detail
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-4 col-sm-12">
                                        <label class="form-label fw-semibold">Lokasi Kalibrasi <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="lokasi_kalibrasi" id="lokasi_kalibrasi"
                                            class="form-control" placeholder="Lab Kalibrasi 1" required>
                                    </div>

                                    <!-- DETAIL ALAT (COLLAPSE) -->
                                    <div class="collapse mb-3 mt-4" id="collapseAlatDetail">
                                        <div class="card border-info shadow-sm">
                                            <div class="card-header bg-soft-info">
                                                <strong>Detail Alat</strong>
                                            </div>
                                            <div class="card-body small">
                                                <div class="row g-2 gy-3">
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Departemen Pemilik:</strong> <span
                                                            id="departemen_pemilik">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Lokasi Alat:</strong> <span id="lokasi_alat">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>No. Kalibrasi:</strong> <span id="no_kalibrasi">-</span>
                                                    </div>

                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Nama Alat:</strong> <span id="nama_alat">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Merk:</strong> <span id="merk">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Tipe:</strong> <span id="tipe">-</span>
                                                    </div>

                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Kapasitas:</strong> <span id="kapasitas">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Resolusi:</strong> <span id="resolusi">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Range Penggunaan:</strong> <span
                                                            id="range_penggunaan_alat">-</span>
                                                    </div>

                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Limits of Permissible Error:</strong> <span
                                                            id="limits_of_permissible_error">-</span>
                                                    </div>
                                                    <div class="col-sm-6 col-md-4">
                                                        <strong>Metode Kalibrasi:</strong> <span
                                                            id="metode_kalibrasi">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4 col-12">
                                        <label for="suhu_ruangan" class="form-label">Suhu Ruangan (°C) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="suhu_ruangan" name="suhu_ruangan"
                                            placeholder="25" required>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <label for="kelembaban" class="form-label">Kelembaban (%) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="kelembaban" name="kelembaban"
                                            placeholder="47" required>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <label class="form-label fw-semibold">Tanggal Kalibrasi <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="tgl_kalibrasi" id="tgl_kalibrasi"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- === Input Jumlah Baris === --}}
                        <div class="card border border-primary border-opacity-50">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-beaker-outline me-2"></i>Data Pengukuran Instrumen</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4 align-items-end gy-3">
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Jenis Alat Ukur <span class="text-danger">*</span>
                                        </label>
                                        <select name="jenis_alat_ukur" class="form-select select-with-other-alat">
                                            <option value="">-- Pilih --</option>
                                            <option value="ph_meter">pH Meter</option>
                                            <option value="viscometer">Viscometer</option>
                                            <option value="refractometer">Refractometer</option>
                                            <option value="colorimeter">Colorimeter</option>
                                            <option value="tds_meter">TDS Meter</option>
                                            <option value="conductivity_meter">Conductivity Meter</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-other-alat"
                                            placeholder="Masukkan jenis alat ukur lain..." style="display:none;">
                                    </div>

                                </div>

                                <div class="row mb-4 align-items-end gy-3">
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Jenis Standar <span class="text-danger">*</span>
                                        </label>
                                        <select name="jenis_standar" class="form-select select-with-other-standar">
                                            <option value="">-- Pilih --</option>
                                            <option value="buffer_solution">Buffer Solution</option>
                                            <option value="vicosity_standar">Viscosity Standar</option>
                                            <option value="brix_standar">Brix Standar</option>
                                            <option value="chlorine_buffer_solution">Chlorine Buffer Solution</option>
                                            <option value="turbidity_standar">Turbidity Standar</option>
                                            <option value="conductivity_meter">Conductivity Meter</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control input-other-standar"
                                            placeholder="Masukkan jenis standar lain..." style="display:none;">
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tableKeypad">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Tested / Adjusted <span
                                                        class="text-danger">*</span></th>
                                                <th class="text-center">Measured Value <span class="text-danger">*</span>
                                                </th>
                                                <th class="text-center">Criterion / Tolerance <span
                                                        class="text-danger">*</span>
                                                </th>
                                                <th class="text-center">Passed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <!-- Titik Kalibrasi -->
                                                <td>
                                                    <span class="fw-bold text-center">Keypad</span>
                                                </td>

                                                <!-- Tested / Adjusted (Checkbox) -->
                                                <td class="text-center">
                                                    <input class="checkbox-lg" type="checkbox" name="tested"
                                                        value="1" required>
                                                </td>

                                                <!-- Measured Value (OK / NOK) -->
                                                <td>
                                                    <select name="measured_value" class="form-select" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="ok">OK</option>
                                                        <option value="nok">NOK</option>
                                                    </select>
                                                </td>

                                                <!-- Criterion / Tolerance -->
                                                <td>

                                                    <select name="criterion" class="form-select mt-1" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Operation">Operation</option>
                                                        <option value="Falled">Falled</option>
                                                    </select>

                                                </td>

                                                <!-- Passed (Yes / No) -->
                                                <td>
                                                    <select name="passed" class="form-select">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-6 mb-3 gy-2 align-items-center">
                                    <label for="titik_naik" class="form-label">Jumlah Titik Kalibrasi</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="jumlahBaris" name="titik_naik"
                                            min="1" max="20" placeholder="0">
                                        <button class="btn btn-outline-primary" type="button"
                                            id="generateRows">Generate</button>
                                        <button class="btn btn-outline-info" type="button" id="addRows">+ Tambah
                                            Titik</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tableInstrumen">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">Titik Kalibrasi</th>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Alat</th>
                                                <th class="text-center">Standar</th>
                                                <th class="text-center">Pembacaan Alat</th>
                                                <th class="text-center">Pembacaan Standar</th>
                                                <th style="width:80px;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center text-muted" id="emptyState">
                                                <td colspan="7">Silakan tentukan jumlah titik kalibrasi di atas</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mb-4 mt-4">
                                    <div class="div col-12">
                                        <label class="form-label">
                                            Catatan (Opsional)
                                        </label>
                                        <textarea name="catatan" id="catatan" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="text-start mt-4">
                                    <div class="d-flex flex-wrap gap-2 justify-content-start">
                                        <button type="button" class="btn btn-outline-danger" id="btnReset">
                                            <i class="mdi mdi-close-circle-outline me-1"></i> Reset Draft
                                        </button>

                                        <button type="submit" class="btn btn-success">
                                            <i class="mdi mdi-content-save me-1"></i> Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
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
            $('#alat_id').change(function() {
                var id = $(this).val();
                if (!id) {
                    $('#btnDetail').prop('disabled', true);
                    return;
                }

                $.get('/api/kalibrasi/pressure/data/alat/' + id, function(res) {
                    let data = res.data;

                    // Aktifkan tombol "Lihat Detail"
                    $('#btnDetail').prop('disabled', false);

                    // Isi data ke span (bukan input)
                    $('#departemen_pemilik').text(data.departemen_pemilik || '-');
                    $('#lokasi_alat').text(data.lokasi_alat || '-');
                    $('#no_kalibrasi').text(data.no_kalibrasi || '-');
                    $('#nama_alat').text(data.nama_alat || '-');
                    $('#merk').text(data.merk || '-');
                    $('#tipe').text(data.tipe || '-');
                    $('#kapasitas').text(data.kapasitas || '-');
                    $('#resolusi').text(data.resolusi || '-');
                    $('#range_penggunaan_alat').text(data.range_penggunaan_alat || '-');
                    $('#limits_of_permissible_error').text(data.limits_of_permissible_error || '-');
                    $('#metode_kalibrasi').text(data.metode_kalibrasi || '-');
                });
            });

            const STORAGE_KEY = "draft_kalibrasi_instrumen";

            function saveDraft() {
                let form = $("#formInstrumen");

                let data = form.serializeArray();

                form.find("input[type=checkbox]").each(function() {
                    if (!this.checked) {
                        data.push({
                            name: this.name,
                            value: ""
                        });
                    }
                });

                let totalGroup = new Set(
                    $("#tableInstrumen tbody tr").map(function() {
                        return $(this).data("group");
                    }).get()
                ).size;

                let draft = {
                    formData: data,
                    jumlahBaris: totalGroup,
                    otherAlat: $(".input-other-alat").val(),
                    otherStandar: $(".input-other-standar").val()
                };

                localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
            }

            function loadDraft() {
                let draft = localStorage.getItem(STORAGE_KEY);
                if (!draft) return;

                draft = JSON.parse(draft);

                // Restore jumlah baris dulu
                if (draft.jumlahBaris && draft.jumlahBaris > 0) {
                    $("#jumlahBaris").val(draft.jumlahBaris);
                    generateRows(parseInt(draft.jumlahBaris));
                }

                // Setelah row ada, baru restore value
                setTimeout(function() {

                    let fieldTracker = {};

                    draft.formData.forEach(function(item) {

                        let fields = $(`[name="${item.name}"]`);

                        if (!fields.length) return;

                        // checkbox
                        if (fields.first().attr("type") === "checkbox") {
                            fields.prop("checked", item.value ? true : false);
                            return;
                        }

                        // Kalau hanya satu field biasa
                        if (fields.length === 1) {
                            fields.val(item.value);
                            return;
                        }

                        // Kalau array (multiple input dengan name sama)
                        if (!fieldTracker[item.name]) {
                            fieldTracker[item.name] = 0;
                        }

                        fields.eq(fieldTracker[item.name]).val(item.value);

                        fieldTracker[item.name]++;
                    });

                    // restore other
                    if (draft.otherAlat) {
                        $(".input-other-alat").val(draft.otherAlat).show();
                    }

                    if (draft.otherStandar) {
                        $(".input-other-standar").val(draft.otherStandar).show();
                    }

                }, 100);
            }

            $(document).on("input change", "#formInstrumen input, #formInstrumen select", function() {
                saveDraft();
            });


            function generateRows(jumlah) {
                let tbody = $("#tableInstrumen tbody");
                tbody.empty(); // ini hanya dipakai saat reset total

                if (jumlah < 1) {
                    tbody.html(`
                        <tr class="emptyState">
                            <td colspan="7">Silakan tentukan jumlah titik kalibrasi di atas</td>
                        </tr>
                    `);
                    return;
                }

                for (let i = 1; i <= jumlah; i++) {
                    appendGroup(i);
                }

            }

            function appendGroup(i) {

                let tbody = $("#tableInstrumen tbody");

                for (let j = 1; j <= 5; j++) {

                    let row = `<tr data-group="${i}">`;

                    if (j === 1) {
                        row += `
                            <td rowspan="5">
                                <input type="text" name="titik_kalibrasi[${i}]" class="input-mini text-center">
                            </td>

                            <td rowspan="5">
                                <input type="text" name="nomor[${i}]" class="input-mini text-center">
                            </td>
                        `;
                    }

                    row += `
                        <td><input type="number" step="0.001" name="alat[${i}][]" class="input-mini"></td>
                        <td><input type="number" step="0.001" name="standar[${i}][]" class="input-mini"></td>
                        <td><input type="number" step="0.001" name="pembacaan_alat[${i}][]" class="input-mini"></td>
                        <td><input type="number" step="0.001" name="pembacaan_standar[${i}][]" class="input-mini"></td>
                    `;

                    if (j === 1) {
                        row += `
                            <td rowspan="5" class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                    data-group="${i}">
                                    ✕
                                </button>
                            </td>
                        `;
                    }

                    row += `</tr>`;
                    tbody.append(row);
                }
            }

            // Generate berdasarkan input jumlah
            $("#generateRows").on("click", function() {
                let jumlah = parseInt($("#jumlahBaris").val());
                if (!jumlah || jumlah < 1) return;
                generateRows(jumlah);
                saveDraft();
            });

            // add rows
            $("#addRows").on("click", function() {

                let lastRow = $("#tableInstrumen tbody tr:last");

                let currentGroup = 0;

                if (lastRow.length && lastRow.data("group") !== undefined) {
                    currentGroup = parseInt(lastRow.data("group"));
                }

                appendGroup(currentGroup + 1);
                saveDraft();
            });

            // delete rows
            $(document).on("click", ".btn-delete", function() {

                let group = $(this).data("group");

                $(`#tableInstrumen tbody tr[data-group="${group}"]`).remove();

                // update input jumlahBaris juga (opsional)
                let totalGroup = new Set(
                    $("#tableInstrumen tbody tr").map(function() {
                        return $(this).data("group");
                    }).get()
                ).size;

                $("#jumlahBaris").val(totalGroup);

                saveDraft();
            });

            loadDraft();

            $(document).on('change', '.select-with-other-alat', function() {
                let inputOther = $('.input-other-alat');

                if ($(this).val() === 'other') {
                    inputOther.show().focus();
                } else {
                    inputOther.hide().val('');
                }

                saveDraft();
            });

            $(document).on('change', '.select-with-other-standar', function() {
                let inputOther = $('.input-other-standar');

                if ($(this).val() === 'other') {
                    inputOther.show().focus();
                } else {
                    inputOther.hide().val('');
                }

                saveDraft();
            });

            $("#formInstrumen").on("submit", function(e) {
                e.preventDefault();

                let formData = {};

                formData._token = $("input[name='_token']").val();
                formData.alat_id = $("select[name='alat_id']").val();
                formData.lokasi_kalibrasi = $("input[name='lokasi_kalibrasi']").val();
                formData.suhu_ruangan = $("input[name='suhu_ruangan']").val();
                formData.kelembaban = $("input[name='kelembaban']").val();
                formData.tgl_kalibrasi = $("input[name='tgl_kalibrasi']").val();
                formData.catatan = $("textarea[name='catatan']").val();

                let jenisAlat = $("select[name='jenis_alat_ukur']").val();
                if (jenisAlat === "other") {
                    jenisAlat = $(".input-other-alat").val();
                }

                let jenisStandar = $("select[name='jenis_standar']").val();
                if (jenisStandar === "other") {
                    jenisStandar = $(".input-other-standar").val();
                }

                formData.jenis_alat_ukur = jenisAlat;
                formData.jenis_standar = jenisStandar;

                formData.tested = $("input[name='tested']").val();
                formData.measured = $("select[name='measured_value']").val();
                formData.criterion = $("select[name='criterion']").val();
                formData.passed = $("select[name='passed']").val();

                formData.data = [];

                let groups = [...new Set(
                    $("#tableInstrumen tbody tr").map(function() {
                        return $(this).data("group");
                    }).get()
                )].filter(g => g !== undefined);

                groups.forEach(function(group) {

                    let titikObj = {
                        group: group,
                        titik_kalibrasi: $(`input[name="titik_kalibrasi[${group}]"]`).val(),
                        indikator: $(`input[name="nomor[${group}]"]`).val(),
                        alat: [],
                        standar: [],
                        pembacaan_alat: [],
                        pembacaan_standar: []
                    };

                    $(`input[name="alat[${group}][]"]`).each(function() {
                        titikObj.alat.push($(this).val());
                    });

                    $(`input[name="standar[${group}][]"]`).each(function() {
                        titikObj.standar.push($(this).val());
                    });

                    $(`input[name="pembacaan_alat[${group}][]"]`).each(function() {
                        titikObj.pembacaan_alat.push($(this).val());
                    });

                    $(`input[name="pembacaan_standar[${group}][]"]`).each(function() {
                        titikObj.pembacaan_standar.push($(this).val());
                    });

                    formData.data.push(titikObj);
                });

                console.log("DATA FINAL:", formData);

                $.ajax({
                    url: "{{ route('kalibrasi.instrumen.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Data kalibrasi berhasil disimpan.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // RESET FORM
                        $('#formInstrumen')[0].reset();

                        $('#tableInstrumen tbody').html(`
                            <tr class="text-center text-muted">
                                <td colspan="7">
                                    Silakan tentukan jumlah titik kalibrasi di atas
                                </td>
                            </tr>
                        `);

                        localStorage.removeItem(STORAGE_KEY);
                    },

                    error: function(xhr) {

                        if (xhr.status === 422) {

                            let errors = xhr.responseJSON.errors;
                            let errorMessages = '';

                            $.each(errors, function(key, value) {
                                errorMessages += `• ${value[0]}<br>`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: errorMessages
                            });

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    }
                });

            });

            // reset button
            $('#btnReset').on('click', function() {

                Swal.fire({
                    title: 'Hapus Draft?',
                    text: "Data draft lokal akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {

                    if (result.isConfirmed) {

                        localStorage.removeItem(STORAGE_KEY);

                        $('#formInstrumen')[0].reset();

                        $('#tableInstrumen tbody').html(`
                            <tr class="text-center text-muted">
                                <td colspan="7">
                                    Silakan tentukan jumlah titik kalibrasi di atas
                                </td>
                            </tr>
                        `);

                        Swal.fire({
                            icon: 'success',
                            title: 'Draft Dihapus',
                            text: 'Draft lokal berhasil dibersihkan.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });
    </script>
@endsection
