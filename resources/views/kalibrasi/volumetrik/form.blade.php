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
                                                    <option value="{{ $alat->id }}">{{ $alat->nama_alat }}</option>
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
                                        <input type="hidden" name="suhu_ruangan_final" id="suhu_ruangan_final">
                                        <label for="suhu_ruangan" class="form-label">Suhu Ruangan</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="suhu_ruangan" name="suhu_ruangan"
                                                placeholder="25">
                                            <span class="input-group-text">±</span>
                                            <input type="number" class="form-control" id="toleransi_suhu"
                                                name="toleransi_suhu" placeholder="1">
                                            <span class="input-group-text">°C</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <input type="hidden" name="kelembaban_final" id="kelembaban_final">
                                        <label for="kelembaban" class="form-label">Kelembaban</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="kelembaban" name="kelembaban"
                                                placeholder="47">
                                            <span class="input-group-text">±</span>
                                            <input type="number" class="form-control" id="toleransi_kelembaban"
                                                name="toleransi_kelembaban" placeholder="3">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <label class="form-label fw-semibold">Tanggal Kalibrasi</label>
                                        <input type="date" name="tgl_kalibrasi" id="tgl_kalibrasi"
                                            class="form-control" required>
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

                                <div class="row mb-3 gy-2 align-items-center">
                                    <div class="col-auto">
                                        <label class="fw-semibold">Jumlah Titik Kalibrasi:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="number" id="jumlahBaris" class="form-control" min="1"
                                            max="50" placeholder="Contoh: 10" style="width: 120px;">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" id="generateRows" class="btn btn-outline-primary">
                                            <i class="mdi mdi-table-plus me-1"></i> Generate
                                        </button>
                                    </div>
                                </div>

                                {{-- === Data Pengukuran Volumetrik === --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tableVolumetrik">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Titik Kalibrasi</th>
                                                <th>Penunjuk Standar</th>
                                                <th>Penunjuk Alat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center text-muted">
                                                <td colspan="4">Silakan tentukan jumlah titik kalibrasi di atas</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-start mt-4">
                                    <div class="d-flex flex-wrap gap-2 justify-content-start">
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                                            id="btnReset">
                                            <i class="mdi mdi-close-circle-outline me-1"></i> Reset
                                        </button>

                                        <button type="button" id="btnPreview"
                                            class="btn btn-outline-info rounded-pill px-4">
                                            <i class="mdi mdi-eye-outline me-1"></i> Preview
                                        </button>

                                        <button type="submit" class="btn btn-success rounded-pill px-4">
                                            <i class="mdi mdi-content-save me-1"></i> Submit Kalibrasi
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

    <!-- Modal Preview -->
    <div class="modal fade" id="modalPreview" tabindex="-1" aria-labelledby="modalPreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPreviewLabel">
                        Preview Isian Form
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small">
                    <div id="previewContent">
                        <p class="text-muted">Belum ada data untuk ditampilkan.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
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

            // Generate baris sesuai input jumlah
            $('#generateRows').on('click', function() {
                const jumlah = parseInt($('#jumlahBaris').val());
                const tbody = $('#tableVolumetrik tbody');
                tbody.empty();

                if (!jumlah || jumlah <= 0) {
                    tbody.html(
                        '<tr><td colspan="4" class="text-center text-muted">Masukkan jumlah titik kalibrasi yang valid.</td></tr>'
                    );
                    return;
                }

                for (let i = 0; i < jumlah; i++) {
                    tbody.append(`
                        <tr data-row>
                            <td><input type="number" step="any" name="data[${i}][titik_kalibrasi]" class="form-control titik-kalibrasi" value="${i + 1}" required></td>
                            <td><input type="number" step="any" name="data[${i}][penunjuk_standar]" class="form-control penunjuk-standar" placeholder="9.94" required></td>
                            <td><input type="number" step="any" name="data[${i}][penunjuk_alat]" class="form-control penunjuk-alat" placeholder="10.00" required></td>
                        </tr>
                    `);
                }
            });

            // Submit form via AJAX
            $('#formVolumetrik').on('submit', function(e) {
                e.preventDefault();
                const suhu = $('#suhu_ruangan').val();
                const toleransiSuhu = $('#toleransi_suhu').val();
                const kelembaban = $('#kelembaban').val();
                const toleransiKelembaban = $('#toleransi_kelembaban').val();

                // Format data gabungan
                const suhuFormatted = suhu && toleransiSuhu ?
                    `${suhu}°C ± ${toleransiSuhu}°C` :
                    suhu ? `${suhu}°C` : '';

                const kelembabanFormatted = kelembaban && toleransiKelembaban ?
                    `${kelembaban}% ± ${toleransiKelembaban}%` :
                    kelembaban ? `${kelembaban}%` : '';

                // Masukkan hasil ke hidden input
                $('#suhu_ruangan_final').val(suhuFormatted);
                $('#kelembaban_final').val(kelembabanFormatted);

                let formData = $('#formVolumetrik').serializeArray();

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
                        Swal.close();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message ||
                                'Data kalibrasi volumetrik berhasil disimpan.'
                        });
                        $('#formVolumetrik')[0].reset();
                        $('#tableVolumetrik tbody').html(
                            '<tr class="text-center text-muted"><td colspan="4">Silakan tentukan jumlah titik kalibrasi di atas</td></tr>'
                        );
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat menyimpan data.'
                        });
                    }
                });
            });

            // reset button
            $(document).on('click', '#btnReset', function() {
                $('#formVolumetrik')[0].reset();
                $('#alat_id').val('').trigger('change');
                $('#tableVolumetrik tbody').html(`
                    <tr class="text-center text-muted">
                        <td colspan="4">Silakan tentukan jumlah titik kalibrasi di atas</td>
                    </tr>
                `);

                $('#collapseAlatDetail').collapse('hide');

                // localStorage.removeItem(STORAGE_KEY);
            });

            $('#btnPreview').on('click', function() {
                // contoh ambil data form manual (bisa diganti sesuai ID input kamu)
                const data = {
                    nama_alat: $('#alat_id option:selected').text() || '-',
                    lokasi_kalibrasi: $('#lokasi_kalibrasi').val() || '-',
                    suhu_final: $('#suhu_ruangan_final').val() || '-',
                    suhu: $('#suhu_ruangan').val() || '-',
                    toleransi_suhu: $('#toleransi_suhu').val() || '-',
                    kelembaban_final: $('#kelembaban_final').val() || '-',
                    kelembaban: $('#kelembaban').val() || '-',
                    toleransi_kelembaban: $('#toleransi_kelembaban').val() || '-',
                    tgl_kalibrasi: $('#tgl_kalibrasi').val() || '-',
                    data_pengukuran: [] // nanti isi dari form dinamis
                };

                // contoh ambil data pengukuran (kalau kamu punya row dinamis)
                $('[data-row]').each(function(i) {
                    data.data_pengukuran.push({
                        titik: $(this).find('.titik-kalibrasi').val() || '-',
                        standar: $(this).find('.penunjuk-standar').val() || '-',
                        alat: $(this).find('.penunjuk-alat').val() || '-'
                    });
                });

                // generate HTML header
                let html = `
                    <div class="mb-4">
                        <h6 class="fw-bold text-info border-bottom pb-1">
                            <i class="mdi mdi-information-outline me-1"></i> Data Header
                        </h6>
                        <div class="row g-2">
                            <div class="col-sm-6 col-md-4"><strong>Alat ID:</strong> ${data.nama_alat}</div>
                            <div class="col-sm-6 col-md-4"><strong>Lokasi Kalibrasi:</strong> ${data.lokasi_kalibrasi}</div>

                            <div class="col-sm-6 col-md-4"><strong>Suhu Ruangan:</strong> ${data.suhu}</div>
                            <div class="col-sm-6 col-md-4"><strong>Toleransi Suhu:</strong> ${data.toleransi_suhu}</div>
                            <div class="col-sm-6 col-md-4"><strong>Suhu Final:</strong> ${data.suhu_final}</div>

                            <div class="col-sm-6 col-md-4"><strong>Kelembaban:</strong> ${data.kelembaban}</div>
                            <div class="col-sm-6 col-md-4"><strong>Toleransi Kelembaban:</strong> ${data.toleransi_kelembaban}</div>
                            <div class="col-sm-6 col-md-4"><strong>Kelembaban Final:</strong> ${data.kelembaban_final}</div>

                            <div class="col-sm-6 col-md-4"><strong>Tanggal Kalibrasi:</strong> ${data.tgl_kalibrasi}</div>
                        </div>
                    </div>
                `;

                // generate tabel pengukuran
                html += `
                    <h6 class="fw-bold text-info border-bottom pb-1 mt-4">
                        <i class="mdi mdi-table me-1"></i> Data Pengukuran
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>Titik Kalibrasi</th>
                                    <th>Penunjuk Standar</th>
                                    <th>Penunjuk Alat</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                if (data.data_pengukuran.length > 0) {
                    data.data_pengukuran.forEach((row, i) => {
                        html += `
                            <tr>
                                <td>${row.titik}</td>
                                <td>${row.standar}</td>
                                <td>${row.alat}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada data pengukuran</td>
                        </tr>
                    `;
                }

                html += `
                            </tbody>
                        </table>
                    </div>
                `;

                // tampilkan ke modal
                $('#previewContent').html(html);
                $('#modalPreview').modal('show');
            });

        })
    </script>
@endsection
