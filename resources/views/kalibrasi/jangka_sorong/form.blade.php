@extends('layouts.app')

@section('title', 'Form Kalibrasi Jangka Sorong')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title d-sm-flex align-items-center justify-content-between">
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
                                    Form Kalibrasi Jangka Sorong
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="formJangkaSorong" method="POST">
                        @csrf
                        <div class="card border border-primary border-opacity-50 mb-4">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-clipboard-list-outline me-2"></i>Data Utama Kalibrasi</strong>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- PILIH KODE ALAT -->
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <label for="alat_id" class="form-label">Pilih Kode Alat</label>
                                        <div class="input-group">
                                            <select class="form-select" id="alat_id" name="alat_id">
                                                <option value="">-- Pilih Kode Alat --</option>
                                                @foreach ($alat as $a)
                                                    <option value="{{ $a->id }}">
                                                        {{ $a->kode_alat . ' - ' . $a->nama_alat }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="btnDetail" class="btn btn-outline-primary"
                                                data-bs-toggle="collapse" data-bs-target="#collapseAlatDetail" disabled>
                                                <i class="mdi mdi-information-outline me-2"></i>Detail
                                            </button>
                                        </div>
                                    </div>

                                    <!-- DATA KALIBRASI -->
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <label for="lokasi_kalibrasi" class="form-label">Lokasi Kalibrasi</label>
                                        <input type="text" class="form-control" id="lokasi_kalibrasi"
                                            name="lokasi_kalibrasi" placeholder="Laboratorium Kalibrasi">
                                    </div>

                                    <!-- DETAIL ALAT -->
                                    <div class="col-12">
                                        <div class="collapse mt-3" id="collapseAlatDetail">
                                            <div class="card border-info shadow-sm">
                                                <div class="card-header bg-soft-info">
                                                    <strong>Detail Alat</strong>
                                                </div>
                                                <div class="card-body small">
                                                    <div class="row g-2 gy-3">
                                                        <div class="col-sm-6 col-md-4"><strong>Departemen Pemilik:</strong>
                                                            <span id="departemen_pemilik">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Lokasi Alat:</strong>
                                                            <span id="lokasi_alat">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>No. Kalibrasi:</strong>
                                                            <span id="no_kalibrasi">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Nama Alat:</strong>
                                                            <span id="nama_alat">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Merk:</strong>
                                                            <span id="merk">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Tipe:</strong>
                                                            <span id="tipe">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Kapasitas:</strong>
                                                            <span id="kapasitas">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Resolusi:</strong>
                                                            <span id="resolusi">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Range Penggunaan:</strong>
                                                            <span id="range_penggunaan_alat">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Limits of Permissible
                                                                Error:</strong>
                                                            <span id="limits_of_permissible_error">-</span>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4"><strong>Metode Kalibrasi:</strong>
                                                            <span id="metode_kalibrasi">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SUHU -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="suhu_ruangan" class="form-label">Suhu Ruangan (°C)</label>
                                        <input type="number" class="form-control" id="suhu_ruangan" name="suhu_ruangan"
                                            placeholder="25">
                                    </div>

                                    <!-- KELEMBABAN -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="kelembaban" class="form-label">Kelembaban (%)</label>
                                        <input type="number" class="form-control" id="kelembaban" name="kelembaban"
                                            placeholder="47">
                                    </div>

                                    <!-- TANGGAL -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="tgl_kalibrasi" class="form-label">Tanggal Kalibrasi</label>
                                        <input type="date" class="form-control" id="tgl_kalibrasi" name="tgl_kalibrasi">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- === Data Pengukuran Jangka Sorong === --}}
                        <div class="card border border-primary border-opacity-50">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-thermometer me-2"></i>Data Pengukuran Jangka Sorong</strong>
                            </div>

                            <div class="card-body">

                                {{-- Input jumlah titik kalibrasi --}}
                                <div class="col-md-6 mb-3 gy-2 align-items-center">
                                    <label for="titik_naik" class="form-label">Jumlah Titik Kalibrasi</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="titik_kalibrasi" min="1"
                                            max="20" placeholder="0">
                                        <button class="btn btn-outline-primary" type="button"
                                            id="generateTitik">Generate</button>
                                        <button class="btn btn-outline-info" type="button" id="addRows">+ Tambah
                                            Titik</button>
                                    </div>
                                </div>

                                {{-- Container untuk field titik kalibrasi --}}
                                <div id="containerTitik"></div>

                                {{-- Tombol Aksi --}}
                                <div class="text-start mt-4">
                                    <div class="d-flex flex-wrap gap-2 justify-content-start">
                                        <button type="button" class="btn btn-outline-danger" id="btnResetForm">
                                            <i class="mdi mdi-close-circle-outline me-1"></i> Reset Draft
                                        </button>

                                        <button type="submit" id="btnSimpanKalibrasi"
                                            class="btn btn-success btnSaveKalibrasi">
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
            const container = $('#containerTitik');
            const masters = @json($masters);
            const STORAGE_KEY = 'cal_jangka_sorong_form';

            // ---- EVENT: pilih alat ----
            $('#alat_id').on('change', function() {
                const id = $(this).val();
                if (!id) {
                    $('#btnDetail').prop('disabled', true);
                    return;
                }

                // load detail alat dari API
                $.get(`/api/kalibrasi/pressure/data/alat/${id}`, function(res) {
                    const d = res.data || {};
                    $('#btnDetail').prop('disabled', false);
                    $('#departemen_pemilik').text(d.departemen_pemilik || '-');
                    $('#lokasi_alat').text(d.lokasi_alat || '-');
                    $('#no_kalibrasi').text(d.no_kalibrasi || '-');
                    $('#nama_alat').text(d.nama_alat || '-');
                    $('#merk').text(d.merk || '-');
                    $('#tipe').text(d.tipe || '-');
                    $('#kapasitas').text(d.kapasitas || '-');
                    $('#resolusi').text(d.resolusi || '-');
                    $('#range_penggunaan_alat').text(d.range_penggunaan_alat || '-');
                    $('#limits_of_permissible_error').text(d.limits_of_permissible_error || '-');
                    $('#metode_kalibrasi').text(d.metode_kalibrasi || '-');
                });
            });

            function saveForm() {

                let data = {
                    header: {},
                    detail: {}
                };

                // ================= HEADER =================
                $('#formJangkaSorong').find('input, select, textarea').each(function() {

                    const name = $(this).attr('name');
                    if (!name) return;

                    // Skip detail fields
                    if (name.includes('nilai_pembacaan') ||
                        name.includes('nilai_master') ||
                        name.includes('no[') ||
                        name.includes('master_id_titik')) {
                        return;
                    }

                    data.header[name] = $(this).val();
                });

                // ================= DETAIL =================
                $('.titik-item').each(function() {

                    const titik = $(this).data('titik');

                    let titikData = {
                        master_id: $(this).find('.selectMasterTitik').val(),
                        pembacaan: []
                    };

                    $(this).find(`input[name^="nilai_pembacaan[${titik}]"]`)
                        .each(function() {
                            titikData.pembacaan.push($(this).val());
                        });

                    data.detail[titik] = titikData;
                });

                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            }

            $(document).on('input change', '#formJangkaSorong input, #formJangkaSorong select', function() {
                saveForm();
            });

            function loadForm() {

                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) return;

                const data = JSON.parse(saved);

                // ================= LOAD HEADER =================
                Object.keys(data.header).forEach(name => {
                    $(`[name="${name}"]`).val(data.header[name]);
                });

                // ================= GENERATE TITIK =================
                const jumlahTitik = Object.keys(data.detail).length;

                if (jumlahTitik > 0) {
                    $('#titik_kalibrasi').val(jumlahTitik);
                    $('#generateTitik').trigger('click');
                }

                // ================= LOAD DETAIL =================
                setTimeout(() => {

                    Object.keys(data.detail).forEach(titik => {

                        const titikData = data.detail[titik];

                        const select = $(`select[name="master_id_titik[${titik}]"]`);
                        select.val(titikData.master_id).trigger('change');

                        titikData.pembacaan.forEach((val, i) => {
                            $(`input[name="nilai_pembacaan[${titik}][]"]`)
                                .eq(i)
                                .val(val);
                        });

                    });

                }, 200);
            }

            // === Generate titik kalibrasi ===
            let totalTitik = 0;

            function generateTitikHTML(t) {

                const tableRows = Array.from({
                    length: 10
                }, (_, i) => `
                    <tr>
                        <td>${i + 1}</td>
                        <td>
                            <input type="text"
                                class="form-control form-control-sm text-center bg-light nilaiMasterTitik${t}"
                                name="nilai_master[${t}][]"
                                readonly>
                        </td>
                        <td>
                            <input type="number"
                                step="0.0001"
                                class="form-control form-control-sm bg-soft-warning text-center"
                                name="nilai_pembacaan[${t}][]">
                            <input type="hidden" name="no[${t}][]" value="${i + 1}">
                        </td>
                    </tr>
                `).join('');

                return `
                    <div class="mt-4 border rounded p-3 shadow-sm titik-item" data-titik="${t}">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <h6 class="fw-bold mb-0 labelTitik">Titik ${t}</h6>

                                <select class="form-select selectMasterTitik"
                                        name="master_id_titik[${t}]"
                                        data-titik="${t}"
                                        style="width: 200px;">
                                    <option value="">-- Pilih Master --</option>
                                    ${masters.map(m =>
                                        `<option value="${m.id}" data-nilai="${m.nilai_master}">${m.no} - ${m.nilai_master}</option>`
                                    ).join('')}
                                </select>
                            </div>

                            <button type="button" class="btn btn-sm btn-danger deleteTitik">
                                ✕
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:5%">No</th>
                                        <th style="width:20%">Nilai Master</th>
                                        <th style="width:20%">Nilai Pembacaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableRows}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }

            $('#generateTitik').on('click', function() {

                const jumlah = parseInt($('#titik_kalibrasi').val());
                const container = $('#containerTitik');

                if (!jumlah || jumlah < 1) {
                    Swal.fire('Oops!', 'Masukkan jumlah titik kalibrasi yang valid.', 'warning');
                    return;
                }

                container.html('');
                totalTitik = jumlah;

                let html = '';
                for (let t = 1; t <= jumlah; t++) {
                    html += generateTitikHTML(t);
                }

                container.html(html);

                saveForm();
            });

            $('#addRows').on('click', function() {

                totalTitik++;

                const html = generateTitikHTML(totalTitik);
                $('#containerTitik').append(html);

                $('#titik_kalibrasi').val(totalTitik);

                saveForm();
            });

            $(document).on('click', '.deleteTitik', function() {

                $(this).closest('.titik-item').remove();

                renumberTitik();
                saveForm();
            });

            function renumberTitik() {

                totalTitik = 0;

                $('.titik-item').each(function(index) {

                    totalTitik = index + 1;

                    $(this).attr('data-titik', totalTitik);
                    $(this).find('.labelTitik').text('Titik ' + totalTitik);

                    // Update select
                    $(this).find('.selectMasterTitik')
                        .attr('name', `master_id_titik[${totalTitik}]`)
                        .attr('data-titik', totalTitik);

                    // Update semua input
                    $(this).find('tbody tr').each(function(i) {

                        $(this).find('input[type="text"]')
                            .attr('name', `nilai_master[${totalTitik}][]`)
                            .removeClass()
                            .addClass(
                                `form-control form-control-sm text-center bg-light nilaiMasterTitik${totalTitik}`
                            );

                        $(this).find('input[type="number"]')
                            .attr('name', `nilai_pembacaan[${totalTitik}][]`);

                        $(this).find('input[type="hidden"]')
                            .attr('name', `no[${totalTitik}][]`);
                    });
                });

                $('#titik_kalibrasi').val(totalTitik);
            }

            $(document).on('change', '.selectMasterTitik', function() {

                const nilaiRaw = $(this).find(':selected').data('nilai');
                const titik = $(this).data('titik');

                let nilai = '';

                if (nilaiRaw !== undefined && nilaiRaw !== '') {
                    nilai = parseFloat(nilaiRaw).toFixed(2);
                }

                $(`.nilaiMasterTitik${titik}`).val(nilai);
                saveForm();
            });

            loadForm();

            $('#formJangkaSorong').on('submit', function(e) {
                e.preventDefault();
                const alatId = $('#alat_id').val();

                if (!alatId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih alat dulu',
                        text: 'Silakan pilih kode alat sebelum submit.'
                    });
                    return;
                }

                // 🔥 Ambil SEMUA data dari form (header + detail)
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('kalibrasi.jangka-sorong.store') }}",
                    method: 'POST',
                    data: formData,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Mohon tunggu sebentar.',
                            didOpen: () => Swal.showLoading(),
                            allowOutsideClick: false
                        });
                    },
                    success: function(res) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message
                        });

                        // reset form
                        $('#formJangkaSorong')[0].reset();
                        $('#containerTitik').empty();
                        $('#alat_id').val('').trigger('change');

                        // hapus localStorage biar ga load ulang data lama
                        localStorage.removeItem(STORAGE_KEY);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors)
                                .flat()
                                .join('\n');

                            Swal.fire('Validasi Gagal!', errorMessages, 'error');
                        } else {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menyimpan data.',
                                'error'
                            );
                        }
                    }
                });
            });

            $('#btnResetForm').on('click', function() {

                Swal.fire({
                    title: 'Reset Form?',
                    text: 'Semua data yang belum disimpan akan hilang.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    const form = $('#formJangkaSorong')[0];
                    form.reset();
                    $('#containerTitik').empty();
                    $('#alat_id').val('').trigger('change');
                    $('#btnSimpanKalibrasi')
                        .prop('disabled', false)
                        .text('Selesai & Submit');
                    localStorage.removeItem(STORAGE_KEY);

                    Swal.fire({
                        icon: 'success',
                        title: 'Form berhasil direset'
                    });

                });

            });

        });
    </script>
@endsection
