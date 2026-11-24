@extends('layouts.app')

@section('title', 'Form Volumetrik')

@section('styles')
    <style>
        #collapseDetailAlat strong {
            color: #495057;
        }

        #collapseDetailAlat span {
            color: #0d6efd;
        }

        .mini-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .mini-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }

        .input-mini {
            width: 100%;
            padding: 4px;
            font-size: 12px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fcf0cc;
            transition: all 0.2s ease-in-out;
        }

        .input-mini:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
        }

        .input-mini.required-slot {
            border-left: 3px solid #0d6efd;
        }

        .input-mini.optional-slot {
            border-left: 3px solid #adb5bd;
            background-color: #f8f9fa;
        }

        .input-mini.pair-invalid {
            border-color: #dc3545 !important;
            background-color: #ffe6e6 !important;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25) !important;
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
                                    Form Kalibrasi Volumetrik
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card-body">
                    <form id="formVolumetrik" method="POST">
                        @csrf

                        {{-- === Data Utama Kalibrasi === --}}
                        <div class="card border border-primary border-opacity-50 mb-4">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-clipboard-list-outline me-2"></i>Data Utama Kalibrasi</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 gy-3">
                                    <div class="col-lg-6 col-md-8 col-sm-12">
                                        <label for="alat_id" class="form-label">Pilih Alat</label>
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
                                        <label class="form-label fw-semibold">Lokasi Kalibrasi</label>
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
                                        <label for="suhu_ruangan" class="form-label">Suhu Ruangan (°C)</label>
                                        <input type="number" class="form-control" id="suhu_ruangan" name="suhu_ruangan"
                                            placeholder="25">
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <label for="kelembaban" class="form-label">Kelembaban (%)</label>
                                        <input type="number" class="form-control" id="kelembaban" name="kelembaban"
                                            placeholder="47">
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <label class="form-label fw-semibold">Tanggal Kalibrasi</label>
                                        <input type="date" name="tgl_kalibrasi" id="tgl_kalibrasi" class="form-control"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- === Input Jumlah Baris === --}}
                        <div class="card border border-primary border-opacity-50">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-beaker-outline me-2"></i>Data Pengukuran Volumetrik</strong>
                            </div>
                            <div class="card-body">

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

                                {{-- Ketentuan Pengisian Alert --}}
                                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2">
                                    <i class="mdi mdi-information-outline fs-5 text-primary"></i>
                                    <div class="fs-12">
                                        <strong>Ketentuan Pengisian:</strong>
                                        Setiap titik kalibrasi <strong>minimal 3 baris pertama wajib diisi lengkap</strong>. Baris ke-4 hingga 10 bersifat <strong>opsional</strong>. Namun, jika penunjuk standar diisi pada baris tertentu, maka penunjuk alat pada baris yang sama <strong>wajib diisi (dan sebaliknya)</strong>.
                                    </div>
                                </div>

                                {{-- === Data Pengukuran Volumetrik === --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tableVolumetrik">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width:140px;">Titik Kalibrasi</th>
                                                <th class="text-center">
                                                    Penunjuk Standar
                                                    <div class="fs-11 text-muted fw-normal">Min. 3 baris terisi</div>
                                                </th>
                                                <th class="text-center">
                                                    Penunjuk Alat
                                                    <div class="fs-11 text-muted fw-normal">Min. 3 baris terisi</div>
                                                </th>
                                                <th style="width:80px;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center text-muted" id="emptyState">
                                                <td colspan="4">Silakan tentukan jumlah titik kalibrasi di atas</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
            $('#alat_id').select2({
                theme: 'bootstrap-5'
            });

            // === Load detail alat ===
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

            const STORAGE_KEY = 'draft_volumetrik';

            function saveDraft() {

                const formData = {
                    header: {
                        alat_id: $('#alat_id').val(),
                        lokasi_kalibrasi: $('#lokasi_kalibrasi').val(),
                        suhu_ruangan: $('#suhu_ruangan').val(),
                        kelembaban: $('#kelembaban').val(),
                        tgl_kalibrasi: $('#tgl_kalibrasi').val(),
                    },
                    jumlahBaris: $('#tableVolumetrik tbody tr').length,
                    data: []
                };

                $('#tableVolumetrik tbody tr').each(function() {

                    const titik = $(this).find('input[name*="titik_kalibrasi"]').val();

                    let standar = [];
                    let alat = [];

                    $(this).find('input[name*="penunjuk_standar"]').each(function() {
                        standar.push($(this).val());
                    });

                    $(this).find('input[name*="penunjuk_alat"]').each(function() {
                        alat.push($(this).val());
                    });

                    // kalau semua kosong skip
                    const isEmpty = !titik &&
                        standar.every(v => !v) &&
                        alat.every(v => !v);

                    if (!isEmpty) {
                        formData.data.push({
                            titik_kalibrasi: titik,
                            penunjuk_standar: standar,
                            penunjuk_alat: alat
                        });
                    }
                });

                localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
            }

            $(document).on('input change', '#formVolumetrik input, #formVolumetrik select', function() {
                saveDraft();
            });

            function createInputRow(index, rowData = null) {
                let standarInputs = '';
                let alatInputs = '';

                const stdValues = rowData && rowData.penunjuk_standar ? rowData.penunjuk_standar : [];
                const alatValues = rowData && rowData.penunjuk_alat ? rowData.penunjuk_alat : [];
                const titikVal = rowData && rowData.titik_kalibrasi !== undefined ? rowData.titik_kalibrasi : '';

                for (let j = 0; j < 10; j++) {
                    const sVal = stdValues[j] !== undefined && stdValues[j] !== null ? stdValues[j] : '';
                    const aVal = alatValues[j] !== undefined && alatValues[j] !== null ? alatValues[j] : '';
                    const isRequired = j < 3;
                    const slotClass = isRequired ? 'required-slot' : 'optional-slot';
                    const placeholder = isRequired ? `${j + 1} (Wajib)` : `${j + 1} (Opsional)`;

                    standarInputs += `
                        <input type="number" step="any"
                            name="data[${index}][penunjuk_standar][]"
                            value="${sVal}"
                            data-row="${j}"
                            placeholder="${placeholder}"
                            class="input-mini ${slotClass}">
                    `;

                    alatInputs += `
                        <input type="number" step="any"
                            name="data[${index}][penunjuk_alat][]"
                            value="${aVal}"
                            data-row="${j}"
                            placeholder="${placeholder}"
                            class="input-mini ${slotClass}">
                    `;
                }

                return `
                    <tr>
                        <td class="text-center align-top pt-2">
                            <input type="number" step="any"
                                name="data[${index}][titik_kalibrasi]"
                                value="${titikVal}"
                                placeholder="Titik"
                                class="input-mini text-center fw-bold">
                        </td>
                        <td>
                            <div class="mini-container">
                                ${standarInputs}
                            </div>
                        </td>
                        <td>
                            <div class="mini-container">
                                ${alatInputs}
                            </div>
                        </td>
                        <td class="text-center align-top pt-2">
                            <button type="button" class="btn btn-sm btn-danger btn-delete-row" title="Hapus Titik">✕</button>
                        </td>
                    </tr>
                `;
            }

            // Validasi langsung satu pasang input
            function validatePair($input) {
                const $row = $input.closest('tr');
                const rowIndex = $input.data('row');
                if (rowIndex === undefined) return;

                const $stdInputs = $row.find('input[name*="penunjuk_standar"]');
                const $alatInputs = $row.find('input[name*="penunjuk_alat"]');

                const $std = $stdInputs.eq(rowIndex);
                const $alat = $alatInputs.eq(rowIndex);

                const stdVal = $std.val().trim();
                const alatVal = $alat.val().trim();

                if (stdVal !== '' && alatVal === '') {
                    $alat.addClass('pair-invalid').attr('title', 'Penunjuk Alat wajib diisi karena Penunjuk Standar terisi');
                    $std.removeClass('pair-invalid').removeAttr('title');
                } else if (stdVal === '' && alatVal !== '') {
                    $std.addClass('pair-invalid').attr('title', 'Penunjuk Standar wajib diisi karena Penunjuk Alat terisi');
                    $alat.removeClass('pair-invalid').removeAttr('title');
                } else {
                    $std.removeClass('pair-invalid').removeAttr('title');
                    $alat.removeClass('pair-invalid').removeAttr('title');
                }
            }

            $(document).on('input blur change', 'input[name*="penunjuk_standar"], input[name*="penunjuk_alat"]', function() {
                validatePair($(this));
            });

            function loadDraft() {

                const draft = localStorage.getItem(STORAGE_KEY);
                if (!draft) return;

                const data = JSON.parse(draft);

                // restore header
                $('#alat_id').val(data.header.alat_id).trigger('change');
                $('#lokasi_kalibrasi').val(data.header.lokasi_kalibrasi);
                $('#suhu_ruangan').val(data.header.suhu_ruangan);
                $('#toleransi_suhu').val(data.header.toleransi_suhu);
                $('#kelembaban').val(data.header.kelembaban);
                $('#toleransi_kelembaban').val(data.header.toleransi_kelembaban);
                $('#tgl_kalibrasi').val(data.header.tgl_kalibrasi);

                const tbody = $('#tableVolumetrik tbody');

                if (!data.data.length) return;

                tbody.empty();
                data.data.forEach((row, i) => {
                    tbody.append(createInputRow(i, row));
                });

                // Jalankan validasi visual pada data yang di-restore
                tbody.find('input[name*="penunjuk_standar"]').each(function() {
                    validatePair($(this));
                });
            }

            loadDraft();

            // Generate baris sesuai input jumlah
            $('#generateRows').on('click', function() {

                const jumlah = parseInt($('#jumlahBaris').val());
                const tbody = $('#tableVolumetrik tbody');
                tbody.empty();

                if (!jumlah || jumlah <= 0) {
                    tbody.html(`
                        <tr class="text-center text-muted">
                            <td colspan="4">Masukkan jumlah titik kalibrasi yang valid.</td>
                        </tr>
                    `);
                    return;
                }

                for (let i = 0; i < jumlah; i++) {
                    tbody.append(createInputRow(i));
                }

                saveDraft();
            });

            $(document).on('click', '.btn-delete-row', function() {

                $(this).closest('tr').remove();

                // renumber ulang name atribut array
                $('#tableVolumetrik tbody tr').each(function(index) {

                    $(this).find('input[name^="data"]').each(function() {

                        const name = $(this).attr('name');
                        const newName = name.replace(/data\[\d+\]/, `data[${index}]`);
                        $(this).attr('name', newName);
                    });
                });

                if ($('#tableVolumetrik tbody tr').length === 0) {
                    $('#tableVolumetrik tbody').html(`
                        <tr class="text-center text-muted">
                            <td colspan="4">Silakan tentukan jumlah titik kalibrasi di atas</td>
                        </tr>
                    `);
                }

                saveDraft();
            });

            $('#addRows').on('click', function() {

                const tbody = $('#tableVolumetrik tbody');

                // kalau masih empty state, kosongkan dulu
                if (tbody.find('tr').length === 1 && tbody.find('td').length === 1) {
                    tbody.empty();
                }

                const rowIndex = tbody.find('tr').length; // index berikutnya
                tbody.append(createInputRow(rowIndex));

                saveDraft();
            });

            // Submit form via AJAX
            $('#formVolumetrik').on('submit', function(e) {
                e.preventDefault();

                // Validasi form langsung (client-side)
                const $rows = $('#tableVolumetrik tbody tr');
                if ($rows.length === 0 || $rows.first().attr('id') === 'emptyState' || $rows.first().find('td').length === 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Masih Kosong',
                        text: 'Silakan tentukan dan tambahkan minimal satu titik kalibrasi terlebih dahulu.'
                    });
                    return false;
                }

                let clientErrors = [];

                $rows.each(function(i) {
                    if ($(this).attr('id') === 'emptyState' || $(this).find('td').length === 1) return;

                    const $titikInput = $(this).find('input[name*="titik_kalibrasi"]');
                    const titikVal = $titikInput.val().trim();
                    const titikLabel = titikVal ? `Titik ${titikVal}` : `Titik ke-${i + 1}`;

                    if (!titikVal) {
                        clientErrors.push(`${titikLabel}: Nilai titik kalibrasi wajib diisi.`);
                        $titikInput.addClass('pair-invalid');
                    } else {
                        $titikInput.removeClass('pair-invalid');
                    }

                    const $stdInputs = $(this).find('input[name*="penunjuk_standar"]');
                    const $alatInputs = $(this).find('input[name*="penunjuk_alat"]');

                    let filledCount = 0;

                    for (let j = 0; j < 10; j++) {
                        const $std = $stdInputs.eq(j);
                        const $alat = $alatInputs.eq(j);
                        const sVal = $std.val().trim();
                        const aVal = $alat.val().trim();

                        if (sVal !== '' && aVal === '') {
                            clientErrors.push(`${titikLabel} baris ke-${j + 1}: Penunjuk Alat wajib diisi karena Penunjuk Standar telah terisi.`);
                            $alat.addClass('pair-invalid');
                        } else if (sVal === '' && aVal !== '') {
                            clientErrors.push(`${titikLabel} baris ke-${j + 1}: Penunjuk Standar wajib diisi karena Penunjuk Alat telah terisi.`);
                            $std.addClass('pair-invalid');
                        } else if (sVal !== '' && aVal !== '') {
                            filledCount++;
                            $std.removeClass('pair-invalid');
                            $alat.removeClass('pair-invalid');
                        } else {
                            $std.removeClass('pair-invalid');
                            $alat.removeClass('pair-invalid');
                        }
                    }

                    if (filledCount < 3) {
                        clientErrors.push(`${titikLabel}: Minimal 3 baris pengisian (Standar & Alat) harus diisi lengkap. Saat ini baru terisi ${filledCount} baris.`);
                        for (let j = 0; j < 3; j++) {
                            if ($stdInputs.eq(j).val().trim() === '') $stdInputs.eq(j).addClass('pair-invalid');
                            if ($alatInputs.eq(j).val().trim() === '') $alatInputs.eq(j).addClass('pair-invalid');
                        }
                    }
                });

                if (clientErrors.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Form Belum Lengkap',
                        html: '<div class="text-start fs-13"><ul class="mb-0 text-danger ps-3">' +
                            clientErrors.map(err => `<li class="mb-1">${err}</li>`).join('') +
                            '</ul></div>'
                    });
                    return false;
                }
                e.preventDefault();

                let formData = $(this).serialize();

                Swal.fire({
                    title: 'Menyimpan data...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "{{ route('kalibrasi.volumetrik.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(res) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message ||
                                'Data kalibrasi volumetrik berhasil disimpan.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // reset form
                        $('#formVolumetrik')[0].reset();

                        $('#tableVolumetrik tbody').html(`
                            <tr class="text-center text-muted">
                                <td colspan="4">
                                    Silakan tentukan jumlah titik kalibrasi di atas
                                </td>
                            </tr>
                        `);

                        // hapus draft localStorage
                        localStorage.removeItem(STORAGE_KEY);
                    },
                    error: function(xhr) {

                        Swal.close();

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

                        $('#formVolumetrik')[0].reset();

                        $('#tableVolumetrik tbody').html(`
                            <tr class="text-center text-muted">
                                <td colspan="4">
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

        })
    </script>
@endsection
