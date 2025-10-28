@extends('layouts.app')

@section('styles')
    <style>
        .titik-kalibrasi-block {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .titik-kalibrasi-block:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }

        .measurement-row {
            background-color: white;
            border-radius: 0.25rem;
            border: 1px solid #e9ecef;
            margin-bottom: 0.5rem;
        }

        .measurement-header {
            background-color: #e9ecef;
            font-weight: 600;
            font-size: 0.875rem;
            color: #495057;
        }

        .info-fab {
            position: fixed;
            top: 80px;
            /* jarak dari bawah layar */
            right: 30px;
            /* jarak dari kanan layar */
            width: 54px;
            height: 54px;
            background: linear-gradient(135deg, #4c9efc, #6db9fb);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(46, 142, 255, 0.4);
            color: white;
            cursor: pointer;
            z-index: 2000;
            transition: all .25s ease;
        }

        /* efek hover glow */
        .info-fab:hover {
            transform: translateY(-4px) scale(1.08);
            box-shadow: 0 10px 25px rgba(46, 142, 255, 0.6), 0 0 12px rgba(46, 142, 255, 0.5) inset;
        }

        /* animasi pulse */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(46, 142, 255, 0.6);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(46, 142, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(46, 142, 255, 0);
            }
        }

        .info-fab::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            width: 100%;
            height: 100%;
            animation: pulse 2s infinite;
        }
    </style>
@endsection

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
                        <div id="infoBtn" class="info-fab">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true">
                                <circle cx="12" cy="12" r="12" fill="#2e8eff" />
                                <text x="12" y="17" text-anchor="middle" font-size="14" font-family="Arial, sans-serif"
                                    font-weight="700" fill="#fff">i</text>
                            </svg>
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
                                    Form Kalibrasi Pressure
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="formKalibrasi" method="POST">
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
                                                    <option value="{{ $a->id }}">{{ $a->kode_alat }}</option>
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
                                        <input type="hidden" name="suhu_ruangan_final" id="suhu_ruangan_final">
                                        <label for="suhu_ruangan" class="form-label">Suhu Ruangan</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="suhu_ruangan"
                                                name="suhu_ruangan" placeholder="25">
                                            <span class="input-group-text">±</span>
                                            <input type="number" class="form-control" id="toleransi_suhu"
                                                name="toleransi_suhu" placeholder="1">
                                            <span class="input-group-text">°C</span>
                                        </div>
                                    </div>

                                    <!-- KELEMBABAN -->
                                    <div class="col-12 col-md-6 col-xl-4">
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

                                    <!-- TANGGAL -->
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="tgl_kalibrasi" class="form-label">Tanggal Kalibrasi</label>
                                        <input type="date" class="form-control" id="tgl_kalibrasi"
                                            name="tgl_kalibrasi">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- === Data Pengukuran Pressure === --}}
                        <div class="card border border-primary border-opacity-50">
                            <div class="card-header bg-light">
                                <strong><i class="mdi mdi-gauge me-2"></i>Data Pengukuran Pressure</strong>
                            </div>
                            <div class="card-body">
                                <!-- Nav tabs -->
                                <ul class="nav nav-pills" id="kalibrasiTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link waves-effect waves-light active" data-bs-toggle="tab"
                                            href="#tekanan-naik">
                                            <i class="fas fa-arrow-up me-1"></i>Tekanan Naik
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link waves-effect waves-light" data-bs-toggle="tab"
                                            href="#tekanan-turun">
                                            <i class="fas fa-arrow-down me-1"></i>Tekanan Turun
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-4">
                                    <!-- Tekanan Naik -->
                                    <div class="tab-pane fade show active" id="tekanan-naik">
                                        <div class="col-xxl-3 col-md-3 mb-3">
                                            <label for="titik_naik" class="form-label">Jumlah Titik Kalibrasi</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="titik_naik"
                                                    name="titik_naik" min="1" max="10" placeholder="0">
                                                <button type="button" class="btn btn-outline-primary btn-generate"
                                                    id="generateNaik">
                                                    <i class="mdi mdi-plus me-1"></i>Buat / Tambah Titik
                                                </button>
                                            </div>
                                        </div>
                                        <div id="containerNaik"></div>
                                    </div>

                                    <!-- Tekanan Turun -->
                                    <div class="tab-pane fade" id="tekanan-turun">
                                        <div class="col-xxl-3 col-md-3 mb-3">
                                            <label for="titik_turun" class="form-label">Jumlah Titik Kalibrasi</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="titik_turun"
                                                    name="titik_turun" min="1" max="10" placeholder="0">
                                                <button type="button" class="btn btn-outline-primary btn-generate"
                                                    id="generateTurun">
                                                    <i class="mdi mdi-plus me-1"></i>Buat / Tambah Titik
                                                </button>
                                            </div>
                                        </div>
                                        <div id="containerTurun"></div>
                                    </div>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="text-start mt-4">
                                    <div class="d-flex flex-wrap gap-2 justify-content-start">
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                                            id="btnResetKalibrasi">
                                            <i class="mdi mdi-close-circle-outline me-1"></i> Reset
                                        </button>

                                        <button type="submit" id="btnPreview"
                                            class="btn btn-outline-info rounded-pill px-4">
                                            <i class="mdi mdi-eye-outline me-1"></i> Preview
                                        </button>

                                        <button type="submit" id="btnSimpanKalibrasi"
                                            class="btn btn-success btnSaveKalibrasi rounded-pill px-4">
                                            <i class="mdi mdi-send-check-outline me-1"></i> Submit & Kirim ke Foreman
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

    {{-- Modal flow Information --}}
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">
                        <i class="mdi mdi-information-outline me-2"></i> Alur Pengisian Form Kalibrasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">Pilih <b>Kode Alat</b>, sistem akan otomatis mengisi informasi alat.
                        </li>
                        <li class="list-group-item">Isi <b>Lokasi Kalibrasi</b> sesuai tempat pelaksanaan.</li>
                        <li class="list-group-item">Masukkan <b>Suhu Ruangan</b> beserta toleransinya (±).</li>
                        <li class="list-group-item">Masukkan <b>Kelembaban</b> beserta toleransinya (±).</li>
                        <li class="list-group-item">Pilih <b>Tanggal Kalibrasi</b>.</li>
                        <li class="list-group-item">Klik <b>Simpan</b> untuk menyimpan data.</li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Preview --}}
    <div class="modal fade" id="modalPreviewKalibrasi" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-eye-outline me-2"></i>Preview Data Kalibrasi</h5>
                    <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="previewBody">
                    <div class="text-center text-muted py-5">
                        <i class="mdi mdi-loading mdi-spin fs-1 mb-2"></i><br>
                        Memuat preview...
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-pencil-outline me-1"></i> Edit Lagi
                    </button>
                    <button class="btn btn-success" id="btnSubmitFinalFromPreview">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Submit Final
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            const STORAGE_KEY = 'formKalibrasiPressure';

            $(document).on('input change',
                '#formKalibrasi input, #formKalibrasi select, #formKalibrasi textarea, #collapseDataPressure input',
                function() {
                    saveForm();
                });

            function saveForm() {
                let headerData = $('#formKalibrasi').serialize();

                // Ambil nilai titik naik dan turun secara eksplisit
                let titikNaikVal = 'titik_naik=' + ($('#titik_naik').val() || 0);
                let titikTurunVal = 'titik_turun=' + ($('#titik_turun').val() || 0);

                // Ambil data pressure dinamis
                let pressureData = $('#collapseDataPressure').find('input').serialize();

                // Gabungkan semua data, pastikan titik_naik dan titik_turun ada di awal
                let combinedData = titikNaikVal + '&' + titikTurunVal + '&' + headerData + '&' + pressureData;

                localStorage.setItem(STORAGE_KEY, combinedData);

                // DEBUGGING CHECK
                console.log('✅ Data berhasil disimpan ke localStorage.');
                console.log('Data tersimpan:', combinedData); // Sekarang cek log ini, harusnya ada titik_naik=X
            }

            $('#alat_id').change(function() {
                var id = $(this).val();

                if (!id) {
                    $('#btnDetail').prop('disabled', true);
                    return;
                }

                $.get('/api/kalibrasi/pressure/data/alat/' + id, function(res) {
                    let data = res.data;

                    $('#btnDetail').prop('disabled', false);
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

            // Load data dari localStorage
            function loadForm() {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) return;

                if (!saved) {
                    console.log('❌ Tidak ada data tersimpan di localStorage.');
                    return;
                }

                console.log('✅ Memuat data dari localStorage. Data raw:', saved);

                const params = new URLSearchParams(saved);

                const titikNaik = parseInt(params.get('titik_naik')) || 0;
                const titikTurun = parseInt(params.get('titik_turun')) || 0;

                if (titikNaik > 0) {
                    $('#titik_naik').val(titikNaik);
                    generateTitikKalibrasi('containerNaik', titikNaik);
                }
                if (titikTurun > 0) {
                    $('#titik_turun').val(titikTurun);
                    generateTitikKalibrasi('containerTurun', titikTurun);
                }

                setTimeout(() => {
                    params.forEach((value, key) => {

                        const $el = $(`[name="${key}"]`);

                        if ($el.length) {
                            // Khusus untuk select (seperti alat_id)
                            if ($el.is('select')) {
                                $el.val(value).trigger('change');
                            }
                            // Untuk input/textarea
                            else {
                                $el.val(value);
                            }
                        }
                    });

                    // 5. Update badge setelah semua nilai terisi
                    updateAllBadges();
                }, 50); // Delay singkat 50ms (lebih aman dari 150ms)
            }

            function updateAllBadges() {
                $('#containerNaik .titik-kalibrasi-block, #containerTurun .titik-kalibrasi-block').each(function(i,
                    block) {
                    const containerId = $(block).parent().attr('id');
                    const titikNo = i + 1;
                    const $inputAlat1 = $(`#${containerId}_alat_${titikNo}_1`);
                    const $badge = $(`#badge_${containerId}_${titikNo}`);

                    // Panggil fungsi input trigger dari generateTitikKalibrasi agar badge terupdate
                    if ($inputAlat1.length) {
                        // Pastikan badge terupdate dengan nilai dari input
                        $badge.text($inputAlat1.val() || "0.0");
                    }
                });
            }

            // Fungsi generate titik kalibrasi (tetap sama)
            function generateTitikKalibrasi(containerId, jumlah) {
                const $container = $('#' + containerId);

                // 1. Hitung berapa banyak titik yang sudah ada (existingTitik)
                const existingTitik = $container.children('.titik-kalibrasi-block').length;

                // 💡 Kasus 1: Mengurangi Jumlah Titik (Hapus yang berlebih)
                if (jumlah < existingTitik) {
                    // Hapus elemen berlebih dari belakang
                    $container.children('.titik-kalibrasi-block').slice(jumlah).remove();
                    console.log(`Titik yang dihapus dari ${containerId}: ${existingTitik - jumlah}`);
                    return;
                }

                // 💡 Kasus 2: Menambah Jumlah Titik (Mulai dari existingTitik + 1)
                if (jumlah > existingTitik) {
                    console.log(`Menambahkan ${jumlah - existingTitik} titik baru ke ${containerId}`);
                    for (let i = existingTitik + 1; i <= jumlah; i++) { // Mulai loop dari titik berikutnya
                        const titikBlock = `
                            <div class="titik-kalibrasi-block p-3 mb-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 text-primary">
                                        Titik Kalibrasi ${i}
                                        <span class="badge bg-primary ms-2" id="badge_${containerId}_${i}">0.0</span>
                                    </h6>
                                </div>
                                ${generateMeasurementRows(containerId, i)}
                            </div>
                        `;
                        $container.append(titikBlock);

                        // Pasang event listener dan update badge untuk titik yang baru dibuat
                        const $inputAlat1 = $(`#${containerId}_alat_${i}_1`);
                        const $badge = $(`#badge_${containerId}_${i}`);

                        $badge.text($inputAlat1.val() || "0.0");

                        $inputAlat1.off("input").on("input", function() {
                            $badge.text($(this).val() || "0.0");
                            saveForm
                                (); // Tambahkan saveForm() di sini agar auto-save saat pengukuran diubah
                        });
                    }
                }

                // Jika jumlah == existingTitik, tidak terjadi apa-apa (data aman)
            }

            // Baris input pengukuran
            function generateMeasurementRows(containerId, titikNo) {
                return `
                    <div class="row g-3 mb-3">
                        <!-- Penunjuk Standar -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold d-block">Penunjuk Standar</label>
                            <input type="number" class="form-control mb-2" id="${containerId}_standar_${titikNo}_1" name="${containerId}_standar_${titikNo}_1" step="0.1" placeholder="0.0">
                            <input type="number" class="form-control mb-2" id="${containerId}_standar_${titikNo}_2" name="${containerId}_standar_${titikNo}_2" step="0.1" placeholder="0.0">
                            <input type="number" class="form-control" id="${containerId}_standar_${titikNo}_3" name="${containerId}_standar_${titikNo}_3" step="0.1" placeholder="0.0">
                        </div>
                        <!-- Penunjuk Alat -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold d-block">Penunjuk Alat</label>
                            <input type="number" class="form-control mb-2" id="${containerId}_alat_${titikNo}_1" name="${containerId}_alat_${titikNo}_1" step="0.1" placeholder="0.0">
                            <input type="number" class="form-control mb-2" id="${containerId}_alat_${titikNo}_2" name="${containerId}_alat_${titikNo}_2" step="0.1" placeholder="0.0">
                            <input type="number" class="form-control" id="${containerId}_alat_${titikNo}_3" name="${containerId}_alat_${titikNo}_3" step="0.1" placeholder="0.0">
                        </div>
                    </div>
                `;
            }

            // Tombol generate
            $('#generateNaik').click(function() {
                const jumlah = parseInt($('#titik_naik').val()) || 0;
                generateTitikKalibrasi('containerNaik', jumlah);
                saveForm();
            });

            $('#generateTurun').click(function() {
                const jumlah = parseInt($('#titik_turun').val()) || 0;
                generateTitikKalibrasi('containerTurun', jumlah);
                saveForm();
            });

            // Jalankan load saat halaman dibuka
            loadForm();

            // simpan button
            $(document).on('click', '.btnSaveKalibrasi', function(e) {
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

                let formData = $('#formKalibrasi').serializeArray();
                console.log(formData);
                let data = {};

                formData.forEach(function(item) {
                    data[item.name] = item.value;
                });

                let isFormFilled = formData.some(item => {
                    if (item.name === '_token') return false;
                    return item.value && item.value.trim() !== '';
                });

                if (!isFormFilled) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Form is empty!',
                        text: 'Please fill in the calibration form first.'
                    });
                    return;
                }

                // Array gabungan pressure
                data.pressure = [];
                let adaNaik = false;
                let adaTurun = false;
                let naikCount = 0;
                let turunCount = 0;

                // Ambil data TEKANAN NAIK
                $('#containerNaik .titik-kalibrasi-block').each(function(i, block) {
                    for (let k = 1; k <= 3; k++) {
                        let standar = $(block).find(
                            `input[name="containerNaik_standar_${i+1}_${k}"]`).val();
                        let alat = $(block).find(
                            `input[name="containerNaik_alat_${i+1}_${k}"]`).val();

                        if (standar || alat) {
                            adaNaik = true;
                            naikCount++;

                            // jadikan nilai alat sebagai titik_kalibrasi
                            let titik = alat;

                            data.pressure.push({
                                titik_kalibrasi: titik,
                                tekanan: 'naik',
                                penunjuk_standar: standar,
                                penunjuk_alat: alat,
                                koreksi_standar: 0
                            });
                        }
                    }
                });

                // Ambil data TEKANAN TURUN
                $('#containerTurun .titik-kalibrasi-block').each(function(i, block) {
                    for (let k = 1; k <= 3; k++) {
                        let standar = $(block).find(
                            `input[name="containerTurun_standar_${i+1}_${k}"]`).val();
                        let alat = $(block).find(
                            `input[name="containerTurun_alat_${i+1}_${k}"]`).val();

                        if (standar || alat) {
                            adaTurun = true;
                            turunCount++;

                            let titik = alat; // samakan titik dengan nilai alat

                            data.pressure.push({
                                titik_kalibrasi: titik,
                                tekanan: 'turun',
                                penunjuk_standar: standar,
                                penunjuk_alat: alat,
                                koreksi_standar: 0
                            });
                        }
                    }
                });

                // Validasi isi
                if (!adaNaik || !adaTurun) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Tidak Lengkap!',
                        text: 'Silakan isi data untuk tekanan atas dan bawah.'
                    });
                    return; // stop proses
                }

                // Validasi panjang data naik & turun harus sama
                if (naikCount !== turunCount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data tidak cocok!',
                        text: 'Jumlah titik kalibrasi untuk Tekanan Naik dan Turun harus sama.'
                    });
                    return;
                }

                $.ajax({
                    url: `{{ route('kalibrasi.pressure.store') }}`,
                    method: 'POST',
                    data: {
                        ...data,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Success', res.message, 'success');

                            $('#formKalibrasi')[0].reset();
                            $('#containerNaik').empty();
                            $('#containerTurun').empty();
                            $('#titik_naik').val('');
                            $('#titik_turun').val('');

                            localStorage.removeItem(STORAGE_KEY);
                        } else {
                            Swal.fire('Failed', res.message || 'Something went wrong!',
                                'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Ini validasi gagal
                            let errors = xhr.responseJSON.errors;
                            let msg = "";
                            Object.keys(errors).forEach(function(key) {
                                msg += errors[key][0] + "<br>";
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation failed!',
                                html: msg, // pake html biar <br> bisa kebaca
                            });
                        } else {
                            // Error selain validasi (500, 404, dll)
                            Swal.fire('Error', 'Server error occurred!', 'error');
                        }
                    }
                });
            });



            // reset button
            $(document).on('click', '#btnResetKalibrasi', function() {
                $('#formKalibrasi')[0].reset();
                $('#containerNaik').empty();
                $('#containerTurun').empty();
                $('#titik_naik').val('');
                $('#titik_turun').val('');
                $('#alat_id').val('').trigger('change');

                localStorage.removeItem(STORAGE_KEY);
            });

            // saat dibuka
            $('.collapse').on('show.bs.collapse', function() {
                $(this).prev('.card-header').find('.toggle-icon')
                    .removeClass('mdi-chevron-down')
                    .addClass('mdi-chevron-up');
            });

            // saat ditutup
            $('.collapse').on('hide.bs.collapse', function() {
                $(this).prev('.card-header').find('.toggle-icon')
                    .removeClass('mdi-chevron-up')
                    .addClass('mdi-chevron-down');
            });

            // Info alert
            $('#infoBtn').on('click', function(e) {
                e.stopPropagation(); // biar ga ikut trigger collapse
                Swal.fire({
                    title: 'Alur Pengisian Form',
                    html: `
                        <style>
                            .swal2-popup ol {
                                padding-left: 25px;
                                margin: 0;
                                text-align: left;
                            }
                            .swal2-popup ol li {
                                margin-bottom: 6px;
                            }
                        </style>
                        <ol>
                            <li>Pilih <b>Kode Alat</b>, sistem akan auto-fill data alat.</li>
                            <li>Isi <b>Lokasi Kalibrasi</b> sesuai tempat.</li>
                            <li>Masukkan <b>Suhu Ruangan</b> & toleransinya (±).</li>
                            <li>Masukkan <b>Kelembaban</b> & toleransinya (±).</li>
                            <li>Pilih <b>Tanggal Kalibrasi</b>.</li>
                            <li>Isi pengukuran <b>Tekanan Naik & Tekanan Turun</b>.</li>
                            <li>Isi pengukuran <b>Jangan sampai ada yang kosong dan berbeda</b>.</li>
                            <li>Klik <b>Save</b> untuk menyimpan data.</li>
                        </ol>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'rounded-4 shadow'
                    }
                });
            });

            $(document).on('click', '#btnPreview', function(e) {
                e.preventDefault();

                // Ambil data utama form
                const formData = $('#formKalibrasi').serializeArray();
                const data = {};
                formData.forEach(item => data[item.name] = item.value);

                // Ambil titik pengukuran (naik & turun)
                let pressureData = {
                    naik: [],
                    turun: []
                };

                ['Naik', 'Turun'].forEach(type => {
                    $(`#container${type} .titik-kalibrasi-block`).each(function(index) {
                        let titik = {
                            no: index + 1,
                            standar: [],
                            alat: []
                        };
                        for (let j = 1; j <= 3; j++) {
                            titik.standar.push($(
                                    `#container${type}_standar_${index + 1}_${j}`)
                                .val() || '-');
                            titik.alat.push($(`#container${type}_alat_${index + 1}_${j}`)
                                .val() || '-');
                        }
                        pressureData[type.toLowerCase()].push(titik);
                    });
                });

                // ============================
                // BAGIAN PREVIEW HTML
                // ============================
                let html = `
                    <h5 class="fw-bold text-primary mb-3">Informasi Kalibrasi</h5>
                    <div class="row">
                         <div class="col-md-6 mb-2"><strong>Kode Alat:</strong> ${$('#alat_id option:selected').text() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Nama Alat:</strong> ${$('#nama_alat').val() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Merk:</strong> ${$('#merk').val() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Tipe:</strong> ${$('#tipe').val() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Kapasitas:</strong> ${$('#kapasitas').val() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Resolusi:</strong> ${$('#resolusi').val() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Range Penggunaan:</strong> ${$('#range_penggunaan_alat').val() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Departemen Pemilik:</strong> ${$('#departemen_pemilik').val() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Lokasi Alat:</strong> ${$('#lokasi_alat').val() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Metode Kalibrasi:</strong> ${$('#metode_kalibrasi').val() || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Lokasi Kalibrasi:</strong> ${data.lokasi_kalibrasi || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Suhu Ruangan:</strong> ${data.suhu_ruangan || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Kelembaban:</strong> ${data.kelembaban || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Tanggal Kalibrasi:</strong> ${data.tgl_kalibrasi || '-'}</div>
                    </div>

                    <hr class="my-3">

                    <h5 class="fw-bold text-success mt-4 mb-2">Tekanan Naik</h5>
                    ${generatePreviewTable(pressureData.naik)}

                    <h5 class="fw-bold text-danger mt-4 mb-2">Tekanan Turun</h5>
                    ${generatePreviewTable(pressureData.turun)}
                `;

                $('#previewBody').html(html);
                $('#modalPreviewKalibrasi').modal('show');
            });

            function generatePreviewTable(data) {
                if (!data.length) return '<p class="text-muted fst-italic">Belum ada titik kalibrasi.</p>';

                let rows = data.map(titik => `
                    <tr>
                        <td>${titik.no}</td>
                        <td>${titik.standar.join('<br>')}</td>
                        <td>${titik.alat.join('<br>')}</td>
                    </tr>
                `).join('');

                return `
                    <table class="table table-sm table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Titik</th>
                                <th width="45%">Penunjuk Standar</th>
                                <th width="45%">Penunjuk Alat</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                `;
            }

        })
    </script>
@endsection
