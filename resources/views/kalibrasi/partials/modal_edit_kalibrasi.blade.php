<!-- Modal Edit Kalibrasi (Shared for All Types) -->
<div class="modal fade" id="editKalibrasiModal" tabindex="-1" aria-labelledby="editKalibrasiModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="mdi mdi-pencil fs-4 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0 fw-bold" id="editKalibrasiModalLabel">Edit Data Kalibrasi & Sertifikat</h5>
                        <small class="text-white-50">Edit informasi utama, nilai rata-rata (averages), dan hasil perhitungan sertifikat</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="badgeEditJenis" class="badge bg-white text-primary px-3 py-2 fw-semibold fs-7 shadow-sm text-uppercase"></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <form id="formEditKalibrasi">
                @csrf
                <input type="hidden" id="edit_kalibrasi_id" name="id">
                <input type="hidden" id="edit_jenis_kalibrasi" name="jenis_kalibrasi">

                <div class="modal-body p-0">
                    <!-- Loading Spinner -->
                    <div id="editLoadingSpinner" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h6 class="mt-3 text-muted fw-semibold">Memuat data kalibrasi...</h6>
                    </div>

                    <!-- Modal Content Form -->
                    <div id="editModalContent" style="display: none;">
                        <!-- Nav Tabs -->
                        <ul class="nav nav-pills arrow-navtabs nav-primary bg-light px-3 pt-3 border-bottom" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active px-4 py-2" data-bs-toggle="tab" data-bs-target="#tabEditUmum" role="tab">
                                    <i class="mdi mdi-format-list-bulleted me-1"></i> 1. Informasi Umum (Data Utama Kalibrasi)
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link px-4 py-2" data-bs-toggle="tab" data-bs-target="#tabEditPengukuran" role="tab">
                                    <i class="mdi mdi-calculator-variant me-1"></i> 2. Nilai Average & Parameter Sertifikat
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content p-4">
                            <!-- TAB 1: INFORMASI UMUM -->
                            <div class="tab-pane fade show active" id="tabEditUmum" role="tabpanel">
                                <!-- Card Info Alat (Read-only) -->
                                <div class="card bg-soft-light border mb-4 shadow-none">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h6 class="card-title mb-0 text-muted fs-7 text-uppercase fw-bold">
                                            <i class="mdi mdi-information-outline me-1"></i> Informasi Alat Terkalibrasi (Data Master)
                                        </h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Kode Alat</small>
                                                <span class="fw-bold fs-6 text-primary" id="edit_alat_kode">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Nama Alat</small>
                                                <span class="fw-semibold" id="edit_alat_nama">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Merk / Tipe</small>
                                                <span class="fw-semibold" id="edit_alat_merk_tipe">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">No. Kalibrasi</small>
                                                <span class="fw-semibold" id="edit_alat_no">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Departemen Pemilik</small>
                                                <span class="fw-semibold" id="edit_alat_dept">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Lokasi Alat</small>
                                                <span class="fw-semibold" id="edit_alat_lokasi">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Kapasitas / Resolusi</small>
                                                <span class="fw-semibold" id="edit_alat_kapasitas">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Metode Kalibrasi</small>
                                                <span class="fw-semibold" id="edit_alat_metode">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Fields Header / Cal Main -->
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                    <i class="mdi mdi-clipboard-edit-outline me-1"></i> Data Umum Sertifikat Kalibrasi (cal_main)
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="edit_tgl_kalibrasi" class="form-label fw-semibold">Tanggal Kalibrasi <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit_tgl_kalibrasi" name="tgl_kalibrasi" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_tgl_kalibrasi_ulang" class="form-label fw-semibold">Tanggal Kalibrasi Ulang <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit_tgl_kalibrasi_ulang" name="tgl_kalibrasi_ulang" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_lokasi_kalibrasi" class="form-label fw-semibold">Lokasi Kalibrasi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_lokasi_kalibrasi" name="lokasi_kalibrasi" placeholder="Contoh: Ruang QC / Lab Kalibrasi" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_suhu_ruangan" class="form-label fw-semibold">Suhu Ruangan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_suhu_ruangan" name="suhu_ruangan" placeholder="Contoh: 23°C ± 1°C" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_kelembaban" class="form-label fw-semibold">Kelembaban (RH) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_kelembaban" name="kelembaban" placeholder="Contoh: 50% ± 3%" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="edit_catatan" class="form-label fw-semibold">Catatan Kalibrasi</label>
                                        <textarea class="form-control" id="edit_catatan" name="catatan" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: DATA PENGUKURAN & SERTIFIKAT -->
                            <div class="tab-pane fade" id="tabEditPengukuran" role="tabpanel">
                                <div class="alert alert-info d-flex align-items-center mb-3 py-2 px-3">
                                    <i class="mdi mdi-information-variant-circle fs-4 me-2"></i>
                                    <div class="small">
                                        Data di bawah ini merupakan nilai rata-rata (averages), deviasi, dan ketidakpastian yang <strong>langsung dicetak ke lembar Sertifikat Kalibrasi</strong>. Anda dapat mengedit nilai ini secara presisi.
                                    </div>
                                </div>

                                <!-- Dynamic Container for Each Kalibrasi Type -->
                                <div id="containerEditPengukuran">
                                    <!-- Rendered dynamically by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" id="btnSubmitEditKalibrasi" class="btn btn-primary px-4 shadow-sm">
                        <span class="spinner-border spinner-border-sm me-1" id="submitEditSpinner" style="display: none;"></span>
                        <i class="mdi mdi-content-save me-1" id="submitEditIcon"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    window.userCanEditKalibrasi = {{ (Auth::user() && strtolower(Auth::user()->jabatan ?? '') !== 'operator') ? 'true' : 'false' }};

    // Helper to format/sanitize number or return raw string
    window.openEditKalibrasiModal = function(kalibrasiId) {
        if (!kalibrasiId) return;

        if (!window.userCanEditKalibrasi) {
            Swal.fire({
                icon: 'warning',
                title: 'Akses Ditolak',
                text: 'User dengan jabatan Operator tidak diizinkan mengubah data kalibrasi.'
            });
            return;
        }

        const modalEl = document.getElementById('editKalibrasiModal');
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();

        $('#editLoadingSpinner').show();
        $('#editModalContent').hide();
        $('#btnSubmitEditKalibrasi').prop('disabled', true);

        // Fetch Data from server
        $.ajax({
            url: `/kalibrasi/get-data-edit/${kalibrasiId}`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success' && res.data) {
                    populateEditModal(res.data);
                    $('#editLoadingSpinner').hide();
                    $('#editModalContent').fadeIn(200);
                    $('#btnSubmitEditKalibrasi').prop('disabled', false);
                } else {
                    Swal.fire('Error', res.message || 'Gagal memuat data kalibrasi', 'error');
                    bsModal.hide();
                }
            },
            error: function(err) {
                console.error(err);
                Swal.fire('Error', err.responseJSON?.message || 'Terjadi kesalahan saat memuat data', 'error');
                bsModal.hide();
            }
        });
    };

    function populateEditModal(data) {
        // Set basic hidden ids
        $('#edit_kalibrasi_id').val(data.id);
        const jenis = (data.jenis_kalibrasi || '').toLowerCase().replace(/[- ]/g, '_');
        $('#edit_jenis_kalibrasi').val(jenis);
        $('#badgeEditJenis').text((data.jenis_kalibrasi || '').replace(/_/g, ' '));

        // Alat Master Info
        const alat = data.alat || {};
        $('#edit_alat_kode').text(alat.kode_alat || '-');
        $('#edit_alat_nama').text(alat.nama_alat || '-');
        $('#edit_alat_merk_tipe').text(`${alat.merk || '-'} / ${alat.tipe || '-'}`);
        $('#edit_alat_no').text(alat.no_kalibrasi || '-');
        $('#edit_alat_dept').text(alat.departemen_pemilik || '-');
        $('#edit_alat_lokasi').text(alat.lokasi_alat || '-');
        $('#edit_alat_kapasitas').text(`${alat.kapasitas || '-'} | Res: ${alat.resolusi || '-'}`);
        $('#edit_alat_metode').text(alat.metode_kalibrasi || '-');

        // Form Fields Tab 1
        $('#edit_tgl_kalibrasi').val(data.tgl_kalibrasi ? data.tgl_kalibrasi.substring(0, 10) : '');
        $('#edit_tgl_kalibrasi_ulang').val(data.tgl_kalibrasi_ulang ? data.tgl_kalibrasi_ulang.substring(0, 10) : '');
        $('#edit_lokasi_kalibrasi').val(data.lokasi_kalibrasi || '');
        $('#edit_suhu_ruangan').val(data.suhu_ruangan || '');
        $('#edit_kelembaban').val(data.kelembaban || '');
        $('#edit_catatan').val(data.catatan || '');

        // Render Dynamic Tab 2 based on jenis
        renderPengukuranTab(jenis, data);
    }

    function renderPengukuranTab(jenis, data) {
        const container = $('#containerEditPengukuran');
        container.empty();

        switch(jenis) {
            case 'pressure':
                renderPressureInputs(container, data.pressure || []);
                break;
            case 'volumetrik':
                renderVolumetrikInputs(container, data.volumetrik || []);
                break;
            case 'temperature':
                renderTemperatureInputs(container, data.temperature || []);
                break;
            case 'thermohygrometer':
                renderThermohygrometerInputs(container, data.thermohygrometer || []);
                break;
            case 'jangka_sorong':
                renderJangkaSorongInputs(container, data.jangka_sorong || [], data.jangka_sorong_summary || []);
                break;
            case 'timbangan':
                renderTimbanganInputs(container, data);
                break;
            case 'instrumen':
                renderInstrumenInputs(container, data.instrumen || []);
                break;
            case 'dimensi':
                renderDimensiInputs(container, data.dimensi || []);
                break;
            case 'flowmeter':
                renderFlowmeterInputs(container, data.flowmeter || []);
                break;
            default:
                container.html(`<div class="alert alert-warning">Jenis kalibrasi <strong>${jenis}</strong> belum memiliki template edit khusus.</div>`);
        }
    }

    // 1. PRESSURE
    function renderPressureInputs(container, items) {
        if (!items.length) {
            container.html('<p class="text-muted text-center py-4">Tidak ada data titik kalibrasi pressure.</p>');
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light text-center small align-middle">
                        <tr>
                            <th rowspan="2" style="width: 80px;">Titik</th>
                            <th colspan="4" class="text-success bg-soft-success">Arah Naik</th>
                            <th colspan="4" class="text-info bg-soft-info">Arah Turun</th>
                            <th colspan="3" class="text-primary bg-soft-primary">Ketidakpastian & U</th>
                        </tr>
                        <tr>
                            <th>Avg Alat</th>
                            <th>Avg Standar</th>
                            <th>Avg Koreksi</th>
                            <th>Std Dev</th>
                            <th>Avg Alat</th>
                            <th>Avg Standar</th>
                            <th>Avg Koreksi</th>
                            <th>Std Dev</th>
                            <th>U Naik</th>
                            <th>U Turun</th>
                            <th>U Gabungan</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        items.forEach((p, i) => {
            html += `
                <tr class="row-pressure" data-index="${i}">
                    <input type="hidden" name="pressure[${i}][id]" value="${p.id}">
                    <td>
                        <input type="text" class="form-control form-control-sm text-center fw-bold" name="pressure[${i}][titik_kalibrasi]" value="${p.titik_kalibrasi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-alat-naik" name="pressure[${i}][avg_penunjuk_alat_naik]" value="${p.avg_penunjuk_alat_naik ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-std-naik" name="pressure[${i}][avg_tekanan_standar_naik]" value="${p.avg_tekanan_standar_naik ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-kor-naik fw-semibold text-danger" name="pressure[${i}][avg_koreksi_alat_naik]" value="${p.avg_koreksi_alat_naik ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="pressure[${i}][std_deviasi_naik]" value="${p.std_deviasi_naik ?? ''}">
                    </td>

                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-alat-turun" name="pressure[${i}][avg_penunjuk_alat_turun]" value="${p.avg_penunjuk_alat_turun ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-std-turun" name="pressure[${i}][avg_tekanan_standar_turun]" value="${p.avg_tekanan_standar_turun ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-kor-turun fw-semibold text-danger" name="pressure[${i}][avg_koreksi_alat_turun]" value="${p.avg_koreksi_alat_turun ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="pressure[${i}][std_deviasi_turun]" value="${p.std_deviasi_turun ?? ''}">
                    </td>

                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-u-naik" name="pressure[${i}][u_naik]" value="${p.u_naik ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-u-turun" name="pressure[${i}][u_turun]" value="${p.u_turun ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-u-gabungan fw-bold text-success" name="pressure[${i}][u_gabungan]" value="${p.u_gabungan ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;
        container.html(html);

        // Auto Calc Kor & U Gabungan triggers
        container.on('input', '.inp-alat-naik, .inp-std-naik', function() {
            const tr = $(this).closest('tr');
            const alat = parseFloat(tr.find('.inp-alat-naik').val());
            const std = parseFloat(tr.find('.inp-std-naik').val());
            if (!isNaN(alat) && !isNaN(std)) {
                tr.find('.inp-kor-naik').val((std - alat).toFixed(4));
            }
        });
        container.on('input', '.inp-alat-turun, .inp-std-turun', function() {
            const tr = $(this).closest('tr');
            const alat = parseFloat(tr.find('.inp-alat-turun').val());
            const std = parseFloat(tr.find('.inp-std-turun').val());
            if (!isNaN(alat) && !isNaN(std)) {
                tr.find('.inp-kor-turun').val((std - alat).toFixed(4));
            }
        });
        container.on('input', '.inp-u-naik, .inp-u-turun', function() {
            const tr = $(this).closest('tr');
            const un = parseFloat(tr.find('.inp-u-naik').val());
            const ut = parseFloat(tr.find('.inp-u-turun').val());
            if (!isNaN(un) && !isNaN(ut)) {
                tr.find('.inp-u-gabungan').val(Math.sqrt(Math.pow(un, 2) + Math.pow(ut, 2)).toFixed(9));
            }
        });
    }

    // 2. VOLUMETRIK
    function renderVolumetrikInputs(container, items) {
        if (!items.length) {
            container.html('<p class="text-muted text-center py-4">Tidak ada data titik kalibrasi volumetrik.</p>');
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center">
                    <thead class="table-light small">
                        <tr>
                            <th>Titik Kalibrasi</th>
                            <th>Penunjuk Alat</th>
                            <th>Avg Penunjuk Standar</th>
                            <th>Avg Koreksi</th>
                            <th>Stdev Standar</th>
                            <th>U Total</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        items.forEach((v, i) => {
            const firstDetail = (v.details && v.details.length) ? v.details[0] : {};
            const penunjukAlat = firstDetail.penunjuk_alat ?? '';

            html += `
                <tr class="row-volumetrik">
                    <input type="hidden" name="volumetrik[${i}][id]" value="${v.id}">
                    <td>
                        <input type="text" class="form-control form-control-sm text-center fw-bold" name="volumetrik[${i}][titik_kalibrasi]" value="${v.titik_kalibrasi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-vol-alat" name="volumetrik[${i}][penunjuk_alat]" value="${penunjukAlat}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-vol-std" name="volumetrik[${i}][avg_penunjuk_standar]" value="${v.avg_penunjuk_standar ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-vol-kor fw-semibold text-danger" name="volumetrik[${i}][avg_koreksi]" value="${v.avg_koreksi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="volumetrik[${i}][stdev_penunjuk_standar]" value="${v.stdev_penunjuk_standar ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end fw-bold text-success" name="volumetrik[${i}][u_total]" value="${v.u_total ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        container.html(html);

        container.on('input', '.inp-vol-alat, .inp-vol-std', function() {
            const tr = $(this).closest('tr');
            const alat = parseFloat(tr.find('.inp-vol-alat').val());
            const std = parseFloat(tr.find('.inp-vol-std').val());
            if (!isNaN(alat) && !isNaN(std)) {
                tr.find('.inp-vol-kor').val((std - alat).toFixed(4));
            }
        });
    }

    // 3. TEMPERATURE
    function renderTemperatureInputs(container, items) {
        if (!items.length) {
            container.html('<p class="text-muted text-center py-4">Tidak ada data titik kalibrasi temperature.</p>');
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center">
                    <thead class="table-light small">
                        <tr>
                            <th>Titik Kalibrasi</th>
                            <th>Avg Penunjuk Alat</th>
                            <th>Avg Suhu Standar</th>
                            <th>Avg Koreksi Alat</th>
                            <th>Std Dev</th>
                            <th>Ketidakpastian</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        items.forEach((t, i) => {
            html += `
                <tr class="row-temperature">
                    <input type="hidden" name="temperature[${i}][id]" value="${t.id}">
                    <td>
                        <input type="text" class="form-control form-control-sm text-center fw-bold" name="temperature[${i}][titik_kalibrasi]" value="${t.titik_kalibrasi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-temp-alat" name="temperature[${i}][avg_penunjuk_alat]" value="${t.avg_penunjuk_alat ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-temp-std" name="temperature[${i}][avg_suhu_standar]" value="${t.avg_suhu_standar ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-temp-kor fw-semibold text-danger" name="temperature[${i}][avg_kor_alat]" value="${t.avg_kor_alat ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="temperature[${i}][stdev]" value="${t.stdev ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end fw-bold text-success" name="temperature[${i}][ketidakpastian]" value="${t.ketidakpastian ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        container.html(html);

        container.on('input', '.inp-temp-alat, .inp-temp-std', function() {
            const tr = $(this).closest('tr');
            const alat = parseFloat(tr.find('.inp-temp-alat').val());
            const std = parseFloat(tr.find('.inp-temp-std').val());
            if (!isNaN(alat) && !isNaN(std)) {
                tr.find('.inp-temp-kor').val((std - alat).toFixed(4));
            }
        });
    }

    // 4. THERMOHYGROMETER
    function renderThermohygrometerInputs(container, items) {
        if (!items.length) {
            container.html('<p class="text-muted text-center py-4">Tidak ada data thermohygrometer.</p>');
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center">
                    <thead class="table-light small">
                        <tr>
                            <th rowspan="2">Titik</th>
                            <th rowspan="2">Posisi</th>
                            <th colspan="4" class="text-danger bg-soft-danger">Parameter Suhu (°C)</th>
                            <th colspan="4" class="text-info bg-soft-info">Parameter RH (%)</th>
                        </tr>
                        <tr>
                            <th>Avg Alat</th>
                            <th>Avg Standar</th>
                            <th>Avg Koreksi</th>
                            <th>Ketidakpastian</th>
                            <th>Avg Alat</th>
                            <th>Avg Standar</th>
                            <th>Avg Koreksi</th>
                            <th>Ketidakpastian</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        items.forEach((th, i) => {
            html += `
                <tr class="row-thermo">
                    <input type="hidden" name="thermohygrometer[${i}][id]" value="${th.id}">
                    <td>
                        <input type="text" class="form-control form-control-sm text-center fw-bold" name="thermohygrometer[${i}][titik_kalibrasi]" value="${th.titik_kalibrasi ?? ''}">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-center" name="thermohygrometer[${i}][posisi]" value="${th.posisi ?? ''}">
                    </td>

                    <!-- Suhu -->
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-th-alat-suhu" name="thermohygrometer[${i}][avg_penunjuk_alat_suhu]" value="${th.avg_penunjuk_alat_suhu ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-th-std-suhu" name="thermohygrometer[${i}][avg_tekanan_standar_suhu]" value="${th.avg_tekanan_standar_suhu ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-th-kor-suhu fw-semibold text-danger" name="thermohygrometer[${i}][avg_koreksi_suhu]" value="${th.avg_koreksi_suhu ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="thermohygrometer[${i}][ketidak_pastian_suhu]" value="${th.ketidak_pastian_suhu ?? ''}">
                    </td>

                    <!-- RH -->
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-th-alat-rh" name="thermohygrometer[${i}][avg_penunjuk_alat_rh]" value="${th.avg_penunjuk_alat_rh ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-th-std-rh" name="thermohygrometer[${i}][avg_tekanan_standar_rh]" value="${th.avg_tekanan_standar_rh ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-th-kor-rh fw-semibold text-info" name="thermohygrometer[${i}][avg_koreksi_rh]" value="${th.avg_koreksi_rh ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="thermohygrometer[${i}][ketidak_pastian_rh]" value="${th.ketidak_pastian_rh ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        container.html(html);

        container.on('input', '.inp-th-alat-suhu, .inp-th-std-suhu', function() {
            const tr = $(this).closest('tr');
            const alat = parseFloat(tr.find('.inp-th-alat-suhu').val());
            const std = parseFloat(tr.find('.inp-th-std-suhu').val());
            if (!isNaN(alat) && !isNaN(std)) {
                tr.find('.inp-th-kor-suhu').val((std - alat).toFixed(2));
            }
        });
        container.on('input', '.inp-th-alat-rh, .inp-th-std-rh', function() {
            const tr = $(this).closest('tr');
            const alat = parseFloat(tr.find('.inp-th-alat-rh').val());
            const std = parseFloat(tr.find('.inp-th-std-rh').val());
            if (!isNaN(alat) && !isNaN(std)) {
                tr.find('.inp-th-kor-rh').val((std - alat).toFixed(2));
            }
        });
    }

    // 5. JANGKA SORONG
    function renderJangkaSorongInputs(container, items, summaries) {
        const summary = (summaries && summaries.length) ? summaries[0] : {};

        let html = `
            <div class="row g-4">
                <div class="col-md-8">
                    <h6 class="fw-bold text-primary mb-2">Titik Pengukuran Pembacaan & Koreksi</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-center">
                            <thead class="table-light small">
                                <tr>
                                    <th>Nilai Master / Titik</th>
                                    <th>Avg Pembacaan</th>
                                    <th>Koreksi</th>
                                    <th>Std Dev</th>
                                </tr>
                            </thead>
                            <tbody>
        `;

        items.forEach((js, i) => {
            const nilaiMaster = js.master ? js.master.nilai_master : '-';
            html += `
                <tr>
                    <input type="hidden" name="jangka_sorong[${i}][id]" value="${js.id}">
                    <td class="fw-bold">${nilaiMaster}</td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="jangka_sorong[${i}][avg_pembacaan]" value="${js.avg_pembacaan ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end fw-semibold text-danger" name="jangka_sorong[${i}][koreksi]" value="${js.koreksi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="jangka_sorong[${i}][std_dev]" value="${js.std_dev ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold text-success mb-2">Summary Sertifikat</h6>
                    <div class="card border p-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Std Dev Total</label>
                            <input type="number" step="any" class="form-control form-control-sm" name="jangka_sorong_summary[std_dev_total]" value="${summary.std_dev_total ?? ''}">
                        </div>
                        <div>
                            <label class="form-label small fw-semibold">Ketidakpastian (U)</label>
                            <input type="number" step="any" class="form-control form-control-sm fw-bold text-success" name="jangka_sorong_summary[ketidakpastian]" value="${summary.ketidakpastian ?? ''}">
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.html(html);
    }

    // 6. TIMBANGAN
    function renderTimbanganInputs(container, data) {
        const kemampuan = data.kemampuan_ulang_summary || [];
        const keseragaman = data.keseragaman_skala_summary || [];
        const pinggan = data.pinggan_summary || {};
        const tare = data.tare_summary || [];
        const histerisis = data.histerisis_summary || {};
        const kp = data.ketidakpastian_summary || {};

        let html = `
            <div class="row g-4">
                <!-- 1. Kemampuan Ulang -->
                <div class="col-12">
                    <div class="card border mb-0">
                        <div class="card-header bg-soft-primary py-2">
                            <h6 class="mb-0 fw-bold text-primary">1. Kemampuan Ulang Pembacaan</h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm text-center mb-0 align-middle">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Posisi / Kapasitas</th>
                                            <th>Massa</th>
                                            <th>Std Dev</th>
                                            <th>Maks Perbedaan Akhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
        `;

        const jenisLabels = {
            'mendekati_nol': 'Mendekati Nol',
            'setengah_kapasitas': 'Setengah (1/2) Kapasitas',
            'full_kapasitas': 'Kapasitas Penuh (Full)'
        };

        kemampuan.forEach((k, i) => {
            html += `
                <tr>
                    <input type="hidden" name="kemampuan_ulang_summary[${i}][id]" value="${k.id}">
                    <td class="fw-semibold text-start">${jenisLabels[k.jenis] || k.jenis}</td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-end" name="kemampuan_ulang_summary[${i}][massa]" value="${k.massa ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="kemampuan_ulang_summary[${i}][std_dev]" value="${k.std_dev ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="kemampuan_ulang_summary[${i}][maks_perbedaan_akhir]" value="${k.maks_perbedaan_akhir ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Keseragaman Skala -->
                <div class="col-md-6">
                    <div class="card border h-100 mb-0">
                        <div class="card-header bg-soft-success py-2">
                            <h6 class="mb-0 fw-bold text-success">2. Keseragaman Skala</h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="table-responsive" style="max-height: 250px;">
                                <table class="table table-bordered table-sm text-center mb-0 align-middle">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Massa Ke</th>
                                            <th>Beban</th>
                                            <th>Koreksi Skala</th>
                                        </tr>
                                    </thead>
                                    <tbody>
        `;

        keseragaman.forEach((ks, i) => {
            html += `
                <tr>
                    <input type="hidden" name="keseragaman_skala_summary[${i}][id]" value="${ks.id}">
                    <td>${ks.massa_ke ?? (i+1)}</td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-end" name="keseragaman_skala_summary[${i}][beban]" value="${ks.beban ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end text-danger fw-semibold" name="keseragaman_skala_summary[${i}][koreksi_skala]" value="${ks.koreksi_skala ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Pinggan -->
                <div class="col-md-6">
                    <div class="card border h-100 mb-0">
                        <div class="card-header bg-soft-warning py-2">
                            <h6 class="mb-0 fw-bold text-warning">3. Pengaruh Posisi Pinggan</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label small mb-1">Tengah</label>
                                    <input type="number" step="any" class="form-control form-control-sm text-end" name="pinggan_summary[summary_tengah]" value="${pinggan.summary_tengah ?? ''}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small mb-1">Depan</label>
                                    <input type="number" step="any" class="form-control form-control-sm text-end" name="pinggan_summary[summary_depan]" value="${pinggan.summary_depan ?? ''}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small mb-1">Belakang</label>
                                    <input type="number" step="any" class="form-control form-control-sm text-end" name="pinggan_summary[summary_belakang]" value="${pinggan.summary_belakang ?? ''}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small mb-1">Kiri</label>
                                    <input type="number" step="any" class="form-control form-control-sm text-end" name="pinggan_summary[summary_kiri]" value="${pinggan.summary_kiri ?? ''}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small mb-1">Kanan</label>
                                    <input type="number" step="any" class="form-control form-control-sm text-end" name="pinggan_summary[summary_kanan]" value="${pinggan.summary_kanan ?? ''}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small mb-1 fw-bold text-danger">Selisih Maks</label>
                                    <input type="number" step="any" class="form-control form-control-sm text-end fw-bold text-danger" name="pinggan_summary[selisih_maks]" value="${pinggan.selisih_maks ?? ''}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Tare & Histerisis & Ketidakpastian -->
                <div class="col-md-4">
                    <div class="card border h-100 mb-0">
                        <div class="card-header bg-soft-info py-2">
                            <h6 class="mb-0 fw-bold text-info">4. Pengnolan Beban (Tare)</h6>
                        </div>
                        <div class="card-body p-2">
        `;

        tare.forEach((t, i) => {
            html += `
                <div class="mb-2">
                    <input type="hidden" name="tare_summary[${i}][id]" value="${t.id}">
                    <label class="form-label small mb-1 fw-semibold text-capitalize">${t.kondisi} Tare - Selisih MZ</label>
                    <input type="number" step="any" class="form-control form-control-sm text-end" name="tare_summary[${i}][selisih_mz]" value="${t.selisih_mz ?? ''}">
                </div>
            `;
        });

        html += `
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border h-100 mb-0">
                        <div class="card-header bg-soft-secondary py-2">
                            <h6 class="mb-0 fw-bold text-secondary">5. Histerisis</h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="mb-2">
                                <label class="form-label small mb-1">1/2 Kapasitas</label>
                                <input type="number" step="any" class="form-control form-control-sm text-end" name="histerisis_summary[setengah_kapasitas]" value="${histerisis.setengah_kapasitas ?? ''}">
                            </div>
                            <div>
                                <label class="form-label small mb-1">Nilai Histerisis</label>
                                <input type="number" step="any" class="form-control form-control-sm text-end text-danger fw-semibold" name="histerisis_summary[histerisis]" value="${histerisis.histerisis ?? ''}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border h-100 mb-0">
                        <div class="card-header bg-soft-danger py-2">
                            <h6 class="mb-0 fw-bold text-danger">6. Ketidakpastian</h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="mb-2">
                                <label class="form-label small mb-1">Ketidakpastian Gabungan</label>
                                <input type="number" step="any" class="form-control form-control-sm text-end" name="ketidakpastian_summary[ketidakpastian_gabungan]" value="${kp.ketidakpastian_gabungan ?? ''}">
                            </div>
                            <div>
                                <label class="form-label small mb-1 fw-bold text-success">Ketidakpastian Diperluas (U)</label>
                                <input type="number" step="any" class="form-control form-control-sm text-end fw-bold text-success" name="ketidakpastian_summary[ketidakpastian_perluas]" value="${kp.ketidakpastian_perluas ?? ''}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.html(html);
    }

    // 7. INSTRUMEN
    function renderInstrumenInputs(container, items) {
        if (!items.length) {
            container.html('<p class="text-muted text-center py-4">Tidak ada data titik kalibrasi instrumen.</p>');
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center">
                    <thead class="table-light small">
                        <tr>
                            <th>Titik Kalibrasi</th>
                            <th>Nilai Master</th>
                            <th>Avg Pembacaan</th>
                            <th>Koreksi</th>
                            <th>Std Dev</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        items.forEach((ins, i) => {
            html += `
                <tr>
                    <input type="hidden" name="instrumen[${i}][id]" value="${ins.id}">
                    <td>
                        <input type="text" class="form-control form-control-sm text-center fw-bold" name="instrumen[${i}][titik_kalibrasi]" value="${ins.titik_kalibrasi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-ins-master" name="instrumen[${i}][nilai_master]" value="${ins.nilai_master ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-ins-pembacaan" name="instrumen[${i}][avg_pembacaan]" value="${ins.avg_pembacaan ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-ins-kor fw-semibold text-danger" name="instrumen[${i}][koreksi]" value="${ins.koreksi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="instrumen[${i}][std_dev]" value="${ins.std_dev ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        container.html(html);

        container.on('input', '.inp-ins-master, .inp-ins-pembacaan', function() {
            const tr = $(this).closest('tr');
            const master = parseFloat(tr.find('.inp-ins-master').val());
            const pembacaan = parseFloat(tr.find('.inp-ins-pembacaan').val());
            if (!isNaN(master) && !isNaN(pembacaan)) {
                tr.find('.inp-ins-kor').val((master - pembacaan).toFixed(4));
            }
        });
    }

    // 8. DIMENSI
    function renderDimensiInputs(container, items) {
        if (!items.length) {
            container.html('<p class="text-muted text-center py-4">Tidak ada data dimensi.</p>');
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center">
                    <thead class="table-light small">
                        <tr>
                            <th>Titik Kalibrasi</th>
                            <th>Nilai Master</th>
                            <th>Avg Pembacaan</th>
                            <th>Koreksi</th>
                            <th>Std Dev</th>
                            <th>Ketidakpastian</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        items.forEach((dim, i) => {
            html += `
                <tr>
                    <input type="hidden" name="dimensi[${i}][id]" value="${dim.id}">
                    <td>
                        <input type="text" class="form-control form-control-sm text-center fw-bold" name="dimensi[${i}][titik_kalibrasi]" value="${dim.titik_kalibrasi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-dim-master" name="dimensi[${i}][nilai_master]" value="${dim.nilai_master ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-dim-pembacaan" name="dimensi[${i}][avg_pembacaan]" value="${dim.avg_pembacaan ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-dim-kor fw-semibold text-danger" name="dimensi[${i}][koreksi]" value="${dim.koreksi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="dimensi[${i}][std_dev]" value="${dim.std_dev ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end fw-bold text-success" name="dimensi[${i}][ketidakpastian]" value="${dim.ketidakpastian ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        container.html(html);

        container.on('input', '.inp-dim-master, .inp-dim-pembacaan', function() {
            const tr = $(this).closest('tr');
            const master = parseFloat(tr.find('.inp-dim-master').val());
            const pembacaan = parseFloat(tr.find('.inp-dim-pembacaan').val());
            if (!isNaN(master) && !isNaN(pembacaan)) {
                tr.find('.inp-dim-kor').val((master - pembacaan).toFixed(4));
            }
        });
    }

    // 9. FLOWMETER
    function renderFlowmeterInputs(container, items) {
        if (!items.length) {
            container.html('<p class="text-muted text-center py-4">Tidak ada data flowmeter.</p>');
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center">
                    <thead class="table-light small">
                        <tr>
                            <th>Titik Kalibrasi</th>
                            <th>Nilai Master</th>
                            <th>Avg Pembacaan</th>
                            <th>Koreksi</th>
                            <th>Std Dev</th>
                            <th>Ketidakpastian</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        items.forEach((fm, i) => {
            html += `
                <tr>
                    <input type="hidden" name="flowmeter[${i}][id]" value="${fm.id}">
                    <td>
                        <input type="text" class="form-control form-control-sm text-center fw-bold" name="flowmeter[${i}][titik_kalibrasi]" value="${fm.titik_kalibrasi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-fm-master" name="flowmeter[${i}][nilai_master]" value="${fm.nilai_master ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-fm-pembacaan" name="flowmeter[${i}][avg_pembacaan]" value="${fm.avg_pembacaan ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end inp-fm-kor fw-semibold text-danger" name="flowmeter[${i}][koreksi]" value="${fm.koreksi ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end" name="flowmeter[${i}][std_dev]" value="${fm.std_dev ?? ''}">
                    </td>
                    <td>
                        <input type="number" step="any" class="form-control form-control-sm text-end fw-bold text-success" name="flowmeter[${i}][ketidakpastian]" value="${fm.ketidakpastian ?? ''}">
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        container.html(html);

        container.on('input', '.inp-fm-master, .inp-fm-pembacaan', function() {
            const tr = $(this).closest('tr');
            const master = parseFloat(tr.find('.inp-fm-master').val());
            const pembacaan = parseFloat(tr.find('.inp-fm-pembacaan').val());
            if (!isNaN(master) && !isNaN(pembacaan)) {
                tr.find('.inp-fm-kor').val((master - pembacaan).toFixed(4));
            }
        });
    }

    // FORM SUBMIT HANDLER
    $(document).ready(function() {
        $('#formEditKalibrasi').on('submit', function(e) {
            e.preventDefault();

            const kalibrasiId = $('#edit_kalibrasi_id').val();
            if (!kalibrasiId) return;

            const $btn = $('#btnSubmitEditKalibrasi');
            const $spinner = $('#submitEditSpinner');
            const $icon = $('#submitEditIcon');

            $btn.prop('disabled', true);
            $spinner.show();
            $icon.hide();

            const formData = $(this).serialize();

            $.ajax({
                url: `/kalibrasi/update-data-kalibrasi/${kalibrasiId}`,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    $icon.show();

                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Data kalibrasi dan sertifikat berhasil diperbarui!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        const modalEl = document.getElementById('editKalibrasiModal');
                        const bsModal = bootstrap.Modal.getInstance(modalEl);
                        if (bsModal) bsModal.hide();

                        // Refresh table on current page
                        if (typeof fetchHistoryData === 'function') {
                            fetchHistoryData();
                        } else if (typeof loadData === 'function') {
                            loadData();
                        }
                    } else {
                        Swal.fire('Error', res.message || 'Gagal menyimpan pembaruan', 'error');
                    }
                },
                error: function(err) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    $icon.show();

                    console.error(err);
                    const msg = err.responseJSON?.message || 'Terjadi kesalahan pada server saat memperbarui data.';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        // Global delegation for Edit button
        $(document).on('click', '.btn-edit', function(e) {
            if (!window.userCanEditKalibrasi) {
                e.preventDefault();
                e.stopPropagation();
                Swal.fire({
                    icon: 'warning',
                    title: 'Akses Ditolak',
                    text: 'User dengan jabatan Operator tidak diizinkan mengubah data kalibrasi.'
                });
                return;
            }
            const id = $(this).data('id');
            if (id) {
                openEditKalibrasiModal(id);
            }
        });
    });
})();
</script>
