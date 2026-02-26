@extends('layouts.app')
@section('title', 'Form Timbangan')

@section('styles')
    <style>
        #collapseDetailAlat strong {
            color: #495057;
        }

        #collapseDetailAlat span {
            color: #0d6efd;
        }

        .kemampuan-ulang-table input {
            background-color: #fcf0cc;
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
                                    Form Kalibrasi Timbangan
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card-body">
                    <form id="formTimbangan" method="POST">
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
                                    <div class="col-md-3 col-12">
                                        <label for="suhu_ruangan" class="form-label">Suhu Ruangan (°C)</label>
                                        <input type="number" class="form-control" id="suhu_ruangan" name="suhu_ruangan"
                                            placeholder="25">
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <label for="kelembaban" class="form-label">Kelembaban (%)</label>
                                        <input type="number" class="form-control" id="kelembaban" name="kelembaban"
                                            placeholder="47">
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <label class="form-label fw-semibold">Tanggal Kalibrasi</label>
                                        <input type="date" name="tgl_kalibrasi" id="tgl_kalibrasi" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <label for="pembacaan_terkecil" class="form-label">Pmebacaan Terkecil
                                            (gram)</label>
                                        <input type="number" class="form-control" id="pembacaan_terkecil"
                                            name="pembacaan_terkecil" placeholder="1000">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- === Input Jumlah Baris === --}}
                        <div class="card border border-primary border-opacity-50 mb-4">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-scale-balance me-2"></i>Form Pengukuran Kalibrasi
                                    Timbangan</strong>
                            </div>

                            <div class="card-body">
                                <h5 class="text-left fw-bold mb-4 mt-2">
                                    <span class="bg-soft-primary px-3 py-2">A. Pengujian Kemampuan Ulang Pembacaan
                                        (Mendekati
                                        Nol)</span>
                                </h5>

                                <div class="table-responsive">
                                    <table
                                        class="table table-bordered text-center align-middle mb-0 kemampuan-ulang-table">

                                        {{-- HEADER LEVEL 1 --}}
                                        <thead>
                                            <tr class="table-light">
                                                <th rowspan="4" style="width:80px" class="align-middle">Ulangan Ke-
                                                </th>
                                                <th colspan="2">Mendekati Nol</th>
                                                <th colspan="2">Setengah Kapasitas</th>
                                                <th colspan="2">Full Kapasitas</th>
                                            </tr>

                                            {{-- HEADER LEVEL 2 (Titik Massa) --}}
                                            <tr>
                                                @foreach (['mendekati_nol', 'setengah_kapasitas', 'full_kapasitas'] as $slug)
                                                    <th colspan="2">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-center"
                                                            name="titik_massa_{{ $slug }}"
                                                            placeholder="Titik Massa (gram)">
                                                    </th>
                                                @endforeach
                                            </tr>

                                            {{-- HEADER LEVEL 3 --}}
                                            <tr class="table-light">
                                                <th>Z</th>
                                                <th>M</th>
                                                <th>Z</th>
                                                <th>M</th>
                                                <th>Z</th>
                                                <th>M</th>
                                            </tr>
                                        </thead>

                                        {{-- BODY --}}
                                        <tbody>
                                            @for ($i = 1; $i <= 10; $i++)
                                                <tr>
                                                    <td>{{ $i }}</td>

                                                    @foreach (['mendekati_nol', 'setengah_kapasitas', 'full_kapasitas'] as $slug)
                                                        <td>
                                                            <input type="number" step="0.01"
                                                                class="form-control form-control-sm text-center"
                                                                name="data[{{ $slug }}][{{ $i }}][z]">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01"
                                                                class="form-control form-control-sm text-center"
                                                                name="data[{{ $slug }}][{{ $i }}][m]">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endfor
                                        </tbody>

                                    </table>
                                </div>

                                <h5 class="text-left fw-bold mb-3 mt-4">
                                    <span class="bg-soft-primary px-3 py-2">B. Keseragaman Skala</span>
                                </h5>

                                <div class="table-responsive">
                                    <table
                                        class="table table-bordered text-center align-middle mb-0 kemampuan-ulang-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:120px">Beban Timbangan</th>
                                                <th style="width:150px">Beban (gram)</th>
                                                <th style="width:150px">Pembacaan Skala</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            {{-- Pola: 0 → 1M → 1M → 0 → 2M → 2M → ... → 9M --}}
                                            @for ($i = 1; $i <= 9; $i++)
                                                {{-- Nol sebelum M --}}
                                                <tr>
                                                    <td>0</td>
                                                    <td>
                                                        <input class="form-control form-control-sm" type="number"
                                                            step="0.01"
                                                            name="keseragaman[0_{{ $i }}][beban]">
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm" type="number"
                                                            step="0.01"
                                                            name="keseragaman[0_{{ $i }}][pembacaan]">
                                                    </td>
                                                </tr>

                                                @if ($i > 0)
                                                    {{-- M pertama --}}
                                                    <tr>
                                                        <td>{{ $i }}M</td>
                                                        <td>
                                                            <input class="form-control form-control-sm" type="number"
                                                                step="0.01"
                                                                name="keseragaman[{{ $i }}M_1][beban]">
                                                        </td>
                                                        <td>
                                                            <input class="form-control form-control-sm" type="number"
                                                                step="0.01"
                                                                name="keseragaman[{{ $i }}M_1][pembacaan]">
                                                        </td>
                                                    </tr>

                                                    {{-- M kedua --}}
                                                    <tr>
                                                        <td>{{ $i }}M</td>
                                                        <td>
                                                            <input class="form-control form-control-sm" type="number"
                                                                step="0.01"
                                                                name="keseragaman[{{ $i }}M_2][beban]">
                                                        </td>
                                                        <td>
                                                            <input class="form-control form-control-sm" type="number"
                                                                step="0.01"
                                                                name="keseragaman[{{ $i }}M_2][pembacaan]">
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>

                                <h5 class="text-left fw-bold mt-4 mb-3">
                                    <span class="bg-soft-primary px-3 py-2">C. Pengaruh Penyimpanan Pada Pinggan</span>
                                </h5>

                                <div class="table-responsive">
                                    <table
                                        class="table table-bordered text-center align-middle mb-0 kemampuan-ulang-table">
                                        <tbody>

                                            {{-- Diameter --}}
                                            <tr>
                                                <td colspan="2" style="text-align:left;"><strong>Diameter
                                                        Pinggan</strong></td>
                                                <td>:</td>
                                                <td>
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="pinggan[diameter]" class="info-input">
                                                </td>
                                                <td style="text-align:left;">mm</td>
                                            </tr>

                                            {{-- Massa --}}
                                            <tr>
                                                <td colspan="2" style="text-align:left;"><strong>Massa Diatas
                                                        Pinggan</strong></td>
                                                <td>:</td>
                                                <td>
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="pinggan[massa]" class="info-input">
                                                </td>
                                                <td style="text-align:left;">gram</td>
                                            </tr>

                                            {{-- Loop Percobaan --}}
                                            @for ($p = 1; $p <= 3; $p++)
                                                {{-- Judul Percobaan --}}
                                                <tr>
                                                    <td colspan="5" style="text-align:left;"><strong>Percobaan
                                                            {{ $p }}</strong></td>
                                                </tr>

                                                {{-- Posisi --}}
                                                <tr class="table-light">
                                                    <th>Tengah</th>
                                                    <th>Depan</th>
                                                    <th>Belakang</th>
                                                    <th>Kiri</th>
                                                    <th>Kanan</th>
                                                </tr>

                                                {{-- Input --}}
                                                <tr>
                                                    <td><input class="form-control form-control-sm" type="number"
                                                            step="0.01"
                                                            name="pinggan[percobaan_{{ $p }}][tengah]"></td>
                                                    <td><input class="form-control form-control-sm" type="number"
                                                            step="0.01"
                                                            name="pinggan[percobaan_{{ $p }}][depan]"></td>
                                                    <td><input class="form-control form-control-sm" type="number"
                                                            step="0.01"
                                                            name="pinggan[percobaan_{{ $p }}][belakang]"></td>
                                                    <td><input class="form-control form-control-sm" type="number"
                                                            step="0.01"
                                                            name="pinggan[percobaan_{{ $p }}][kiri]"></td>
                                                    <td><input class="form-control form-control-sm" type="number"
                                                            step="0.01"
                                                            name="pinggan[percobaan_{{ $p }}][kanan]"></td>
                                                </tr>
                                            @endfor

                                        </tbody>
                                    </table>
                                </div>

                                <h5 class="text-left fw-bold mt-4 mb-3">
                                    <span class="bg-soft-primary px-3 py-2">D. Pengaruh Pengenolan Beban (Tare)</span>
                                </h5>

                                <div class="table-responsive">
                                    <table
                                        class="table table-bordered text-center align-middle mb-0 kemampuan-ulang-table">
                                        <tbody>

                                            {{-- Massa --}}
                                            <tr>
                                                <td colspan="3" style="text-align:left;">
                                                    <strong>Massa Diatas Pinggan (gram)</strong>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="tare[massa]" class="info-input">
                                                </td>
                                            </tr>

                                            {{-- Header Tanpa / Dengan Tare --}}
                                            <tr>
                                                <th colspan="2">Tanpa Pengenolan</th>
                                                <th colspan="2">Memakai Pengenolan</th>
                                            </tr>

                                            <tr>
                                                <th>Beban</th>
                                                <th>Pembacaan</th>
                                                <th>Beban</th>
                                                <th>Pembacaan</th>
                                            </tr>

                                            {{-- Zero --}}
                                            <tr>
                                                <td>Zero</td>
                                                <td><input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="tare[tanpa][zero_1]"></td>
                                                <td>Zero</td>
                                                <td><input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="tare[dengan][zero_1]">
                                                </td>
                                            </tr>

                                            {{-- M 1 --}}
                                            <tr>
                                                <td>M</td>
                                                <td><input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="tare[tanpa][m_1]"></td>
                                                <td>M</td>
                                                <td><input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="tare[dengan][m_1]"></td>
                                            </tr>

                                            {{-- M 2 --}}
                                            <tr>
                                                <td>M</td>
                                                <td><input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="tare[tanpa][m_2]"></td>
                                                <td>M</td>
                                                <td><input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="tare[dengan][m_2]"></td>
                                            </tr>

                                            {{-- Zero akhir --}}
                                            <tr>
                                                <td>Zero</td>
                                                <td><input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="tare[tanpa][zero_2]"></td>
                                                <td>Zero</td>
                                                <td><input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="tare[dengan][zero_2]">
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                                <h5 class="text-left fw-bold mt-4 mb-3">
                                    <span class="bg-soft-primary px-3 py-2"> E. Histerisis</span>
                                </h5>

                                <div class="table-responsive">
                                    <table
                                        class="table table-bordered text-center align-middle mb-0 kemampuan-ulang-table">
                                        <tbody>

                                            {{-- Pembacaan Terkecil --}}
                                            <tr>
                                                <td colspan="3" style="text-align:left;">
                                                    <strong>Pembacaan Terkecil Timbangan</strong>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="histerisis[pembacaan_terkecil]"
                                                        class="info-input">
                                                </td>
                                                <td style="text-align:left;">gram</td>
                                            </tr>

                                            {{-- M Setengah Kapasitas --}}
                                            <tr>
                                                <td colspan="3" style="text-align:left;">
                                                    <strong>M (Setengah Kapasitas Timbangan)</strong>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.01" name="histerisis[m_setengah]" class="info-input">
                                                </td>
                                                <td style="text-align:left;">gram</td>
                                            </tr>

                                            {{-- Header utama --}}
                                            <tr>
                                                <th colspan="2">Beban Diatas Pinggan</th>

                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                            </tr>

                                            {{-- Zero Awal --}}
                                            <tr>
                                                <td>Zero</td>
                                                <td>Z1</td>
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <td>
                                                        <input class="form-control form-control-sm" type="number"
                                                            step="0.01" name="histerisis[z1][{{ $i }}]">
                                                    </td>
                                                @endfor
                                            </tr>

                                            {{-- M naik --}}
                                            <tr>
                                                <td>M</td>
                                                <td>m1</td>
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <td>
                                                        <input class="form-control form-control-sm" type="number"
                                                            step="0.01" name="histerisis[m1][{{ $i }}]">
                                                    </td>
                                                @endfor
                                            </tr>

                                            {{-- M + M' --}}
                                            <tr>
                                                <td>M + M'</td>
                                                <td></td>
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <td>
                                                        <input class="form-control form-control-sm" type="number"
                                                            step="0.01"
                                                            name="histerisis[m_plus][{{ $i }}]">
                                                    </td>
                                                @endfor
                                            </tr>

                                            {{-- M turun --}}
                                            <tr>
                                                <td>M</td>
                                                <td>m2</td>
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <td>
                                                        <input class="form-control form-control-sm" type="number"
                                                            step="0.01" name="histerisis[m2][{{ $i }}]">
                                                    </td>
                                                @endfor
                                            </tr>

                                            {{-- Zero akhir --}}
                                            <tr>
                                                <td>Zero</td>
                                                <td>Z2</td>
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <td>
                                                        <input class="form-control form-control-sm" type="number"
                                                            step="0.01" name="histerisis[z2][{{ $i }}]">
                                                    </td>
                                                @endfor
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

            const STORAGE_KEY = "draft_kalibrasi_timbangan";
            const $form = $("#formTimbangan");

            // ==============================
            // LOAD DATA DARI LOCALSTORAGE
            // ==============================
            let savedData = localStorage.getItem(STORAGE_KEY);

            if (savedData) {
                let data = JSON.parse(savedData);

                $.each(data, function(name, value) {
                    $form.find('[name="' + name + '"]').val(value);
                });

                console.log("Draft berhasil dimuat dari localStorage");
            }

            $form.on("input change", "input, select, textarea", function() {

                let formData = {};

                $form.find("input, select, textarea").each(function() {
                    let name = $(this).attr("name");
                    if (name) {
                        formData[name] = $(this).val();
                    }
                });

                localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
            });

            $('#formTimbangan').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('kalibrasi.timbangan.store') }}",
                    method: 'POST',
                    data: $(this).serialize(), // <<< ini saja
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data pembacaan berhasil disimpan!',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        localStorage.removeItem(STORAGE_KEY);
                        $('#formTimbangan')[0].reset();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal menyimpan data, silakan coba lagi.',
                        });
                    }
                });
            });

            $("#btnReset").click(function() {

                Swal.fire({
                    title: 'Hapus Draft?',
                    text: "Semua data yang belum disimpan akan hilang!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {

                        localStorage.removeItem(STORAGE_KEY);
                        $form[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Draft dihapus!',
                            text: 'Data draft berhasil dibersihkan.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                    }

                });

            });
        });
    </script>
@endsection
