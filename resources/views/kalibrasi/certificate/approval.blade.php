@extends('layouts.app')

@section('styles')
    <style>

    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow-sm rounded-3 mb-4" data-aos="fade-up">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <!-- Filter Tanggal -->
                        <div class="col-md-4">
                            <label for="filterTanggal" class="form-label fw-semibold">Tanggal Kalibrasi</label>
                            <input type="date" id="filterTanggal" name="tanggal" class="form-control">
                        </div>

                        <!-- Filter Jenis -->
                        <div class="col-md-4">
                            <label for="filterJenis" class="form-label fw-semibold">Jenis Kalibrasi</label>
                            <select id="filterJenis" name="jenis" class="form-select">
                                <option value="">Semua Jenis</option>
                            </select>
                        </div>

                        <!-- Tombol Reset -->
                        <div class="col-md-4 d-flex align-items-end">
                            <button id="resetFilter" class="btn btn-outline-primary w-100">
                                <i class="mdi mdi-refresh me-1"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm rounded-3 mb-4" data-aos="fade-up">
                <div class="card-body">
                    <div class="row" id="sertifikat-list-container">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-3 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="modalDetailLabel">Detail Sertifikat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDetailBody"></div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Detail Kalibrasi Pressure
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Tabs -->
                    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab"
                                data-bs-target="#info-pane">Informasi</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#measurement-pane">Pengukuran</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Hitung U
                                Gabungan</button>
                        </li>
                    </ul>

                    <div class="tab-content p-3">
                        <!-- Tab 1: Informasi -->
                        <div class="tab-pane fade show active" id="info-pane">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-primary border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-event"></i> Kode Alat
                                            </small>
                                            <strong id="detail_kode_alat"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-primary border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-event"></i> Nama Alat
                                            </small>
                                            <strong id="detail_nama_alat"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-primary border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-event"></i> Tanggal Kalibrasi
                                            </small>
                                            <strong id="detail_tgl_kalibrasi"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-success border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-check"></i> Tgl Kalibrasi Ulang
                                            </small>
                                            <strong id="detail_tgl_ulang"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-info border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-geo-alt"></i> Lokasi
                                            </small>
                                            <strong id="detail_lokasi"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-warning border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-thermometer-half"></i> Suhu Ruangan
                                            </small>
                                            <strong id="detail_suhu"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-info border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-droplet"></i> Kelembaban
                                            </small>
                                            <strong id="detail_kelembaban"></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-animate border-start border-primary border-3">
                                        <div class="card-body">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-gear"></i> Jenis Kalibrasi
                                            </small>
                                            <strong id="detail_jenis"></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-primary">
                                <strong><i class="bi bi-book me-2"></i>Metode Kalibrasi</strong>
                                <p class="mb-0 mt-2 small" id="detail_metode"></p>
                            </div>
                        </div>

                        <!-- Tab 2: Pengukuran -->
                        <div class="tab-pane fade" id="measurement-pane">
                            <!-- Tekanan Naik -->
                            <div class="card mb-3 border-success">
                                <div class="card-header bg-success text-white">
                                    <i class="bi bi-arrow-up-circle-fill me-2"></i>
                                    <strong>Tekanan Naik</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Titik</th>
                                                    <th>Penunjuk Standar</th>
                                                    <th>Penunjuk Alat</th>
                                                    <th>Koreksi Standar</th>
                                                    <th>Tekanan Standar</th>
                                                    <th>Koreksi Alat</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pressure_naik"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tekanan Turun -->
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <i class="bi bi-arrow-down-circle-fill me-2"></i>
                                    <strong>Tekanan Turun</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Titik</th>
                                                    <th>Penunjuk Standar</th>
                                                    <th>Penunjuk Alat</th>
                                                    <th>Koreksi Standar</th>
                                                    <th>Tekanan Standar</th>
                                                    <th>Koreksi Alat</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pressure_turun"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Hasil -->
                        <div class="tab-pane fade" id="result-pane">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <i class="bi bi-calculator-fill me-2"></i>
                                    <strong>Hasil Perhitungan Ketidakpastian</strong>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Titik</th>
                                                    <th>U Naik</th>
                                                    <th>U Turun</th>
                                                    <th>U Naik²</th>
                                                    <th>U Turun²</th>
                                                    <th class="bg-success-subtle">U Gabungan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detail_gabungan"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const baseUrlApi = `{{ url('kalibrasi/certificate/approval/data') }}`;

            fetchSertifikat(baseUrlApi);

            $('#filterTanggal, #filterJenis').on('change', function() {
                const tanggal = $('#filterTanggal').val();
                const jenis = $('#filterJenis').val();

                // Buat query string dinamis
                const params = new URLSearchParams();
                if (tanggal) params.append('tanggal', tanggal);
                if (jenis) params.append('jenis', jenis);

                // Gabungkan URL + query
                const url = params.toString() ? `${baseUrlApi}?${params.toString()}` : baseUrlApi;

                fetchSertifikat(url);
            });

            // Tombol reset
            $('#resetFilter').on('click', function() {
                $('#filterTanggal').val('');
                $('#filterJenis').val('');
                fetchSertifikat(baseUrlApi);
            });

            function fetchSertifikat(url) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(res) {
                        const container = $('#sertifikat-list-container');
                        const data = res.data;
                        container.empty();

                        if ($('#filterJenis option').length <= 1 && Array.isArray(data)) {
                            filterJenis(data);
                        }

                        if ($.isArray(data)) {
                            if (data.length > 0) {
                                displayListView(data, container);
                            } else {
                                displayEmptyState(container, res.message ||
                                    'Tidak ada data sertifikat.');
                            }
                        } else {
                            displayEmptyState(container, 'Data tidak valid.');
                        }
                    },
                    error: function() {
                        $('#sertifikat-list-container').html(
                            '<div class="col-md-12"><div class="alert alert-danger">Gagal memuat data sertifikat.</div></div>'
                        );
                    }
                });
            }

            function filterJenis(data) {
                const jenisSet = new Set();

                data.forEach(item => {
                    const jenis = item.kalibrasi?.jenis_kalibrasi;
                    if (jenis) jenisSet.add(jenis);
                });

                $('#filterJenis').find('option:not(:first)').remove();

                // Tambahkan option dengan format Capitalized
                jenisSet.forEach(jenis => {
                    const formatted = jenis
                        .toLowerCase()
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');

                    $('#filterJenis').append(`<option value="${jenis}">${formatted}</option>`);
                });
            }

            function displayListView(data, container) {
                const rowsHtml = data.map((item, i) => {
                    const tgl_kalibrasi = item.kalibrasi.tgl_kalibrasi ?? '—';
                    const kodeAlat = item.kalibrasi?.alat?.kode_alat ?? '—';
                    const namaAlat = item.kalibrasi?.alat?.nama_alat ?? '—';
                    const jenisKalibrasi = item.kalibrasi?.jenis_kalibrasi ?? '—';
                    const status = item.status ?? 'pending';
                    const komentar = item.comment ?? '';

                    const badgeClass =
                        status === 'approved' ? 'success' :
                        status === 'pending' || status === 'read' ? 'warning' :
                        'danger';

                    // Tombol detail selalu ada
                    let actionColumn = `
                        <button class="btn btn-outline-primary btn-sm btn-detail" data-id="${item.id}">
                            <i class="mdi mdi-eye me-2"></i>Detail
                        </button>
                    `;

                    // Tambahkan tombol approve/reject hanya jika masih pending atau read
                    if (status === 'pending' || status === 'read') {
                        actionColumn += `
                            <button class="btn btn-success btn-sm btn-approve" data-id="${item.id}">
                                <i class="mdi mdi-check me-2"></i>Approve
                            </button>
                            <button class="btn btn-danger btn-sm btn-reject" data-id="${item.id}">
                                <i class="mdi mdi-close me-2"></i>Reject
                            </button>
                        `;
                    } else {
                        // Kalau sudah final, tampilkan badge
                        actionColumn += `
                            <span class="badge badge-soft-${badgeClass} px-3 py-2 ms-2 text-uppercase">
                                <i class="mdi mdi-${status === 'approved' ? 'check' : 'close'} me-1"></i>
                                ${status === 'approved' ? 'Disetujui' : 'Ditolak'}
                            </span>
                        `;
                    }

                    return `
                    <tr data-id="${item.id}">
                        <td>${i + 1}</td>
                        <td>${kodeAlat}</td>
                        <td>${namaAlat}</td>
                        <td>${tgl_kalibrasi}</td>
                        <td>${jenisKalibrasi}</td>
                        <td>
                            <span class="badge badge-soft-${badgeClass} text-uppercase">${status}</span>
                        </td>
                        <td>
                            <textarea class="form-control form-control komentar"
                                placeholder="Tulis komentar..."
                                rows="1"
                                data-id="${item.id}"
                                style="min-width:150px;resize:none;"
                                ${status !== 'pending' && status !== 'read' ? 'disabled' : ''}>${komentar}</textarea>
                        </td>
                        <td class="text-nowrap gap-2">${actionColumn}</td>
                    </tr>
                `;
                }).join('');

                const tableHtml = `
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-info">
                                <tr>
                                    <th>#</th>
                                    <th>Kode Alat</th>
                                    <th>Nama Alat</th>
                                    <th>Tgl Kalibrasi</th>
                                    <th>Jenis Kalibrasi</th>
                                    <th>Status</th>
                                    <th>Komentar</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>${rowsHtml}</tbody>
                        </table>
                    </div>
                `;

                container.html(tableHtml);

                // Event handler
                $('.btn-detail').on('click', function() {
                    const id = $(this).data('id');
                    const item = data.find(d => d.id === id);
                    showDetailModal(item);
                });

                $('.btn-approve').on('click', function() {
                    const id = $(this).data('id');
                    const komentar = $(`.komentar[data-id="${id}"]`).val();
                    handleApproval(id, 'approved', komentar);
                });

                $('.btn-reject').on('click', function() {
                    const id = $(this).data('id');
                    const komentar = $(`.komentar[data-id="${id}"]`).val();
                    handleApproval(id, 'rejected', komentar);
                });
            }

            function handleApproval(id, status, komentar) {
                if (!komentar && status === 'rejected') {
                    toastr.warning('Komentar wajib diisi jika menolak sertifikat.');
                    return;
                }

                Swal.fire({
                    title: `Yakin ingin ${status === 'approved' ? 'menyetujui' : 'menolak'} sertifikat ini?`,
                    text: komentar ? `Komentar: "${komentar}"` : 'Tanpa komentar.',
                    icon: status === 'approved' ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: status === 'approved' ? '#28a745' : '#dc3545'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('kalibrasi.certificate.approval.action') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id,
                            status,
                            komentar
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memproses...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        },
                        success: function(res) {
                            Swal.close();

                            if (res.status === 'success') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: res.message,
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    didClose: () => {
                                        fetchSertifikat(
                                            `{{ url('kalibrasi/certificate/approval/data') }}`
                                        );
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: res.message || 'Terjadi kesalahan.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            toastr.error(xhr.responseJSON?.message ||
                                'Gagal memproses approval.');
                        }
                    });
                });
            }

            function showDetailModal(item) {
                if (!item) return;

                const kalibrasi = item.kalibrasi ?? item.sertifikat?.kalibrasi ?? {};
                const alat = kalibrasi.alat ?? {};

                // Data umum
                $('#detail_kode_alat').text(alat.kode_alat ?? '—');
                $('#detail_nama_alat').text(alat.nama_alat ?? '—');
                $('#detail_tgl_kalibrasi').text(formatDate(kalibrasi.tgl_kalibrasi));
                $('#detail_tgl_ulang').text(formatDate(kalibrasi.tgl_kalibrasi_ulang));
                $('#detail_lokasi').text(kalibrasi.lokasi_kalibrasi ?? '—');
                $('#detail_suhu').text(kalibrasi.suhu_ruangan ? `${kalibrasi.suhu_ruangan}°C` : '—');
                $('#detail_kelembaban').text(kalibrasi.kelembaban ? `${kalibrasi.kelembaban}%` : '—');
                $('#detail_jenis').text((kalibrasi.jenis_kalibrasi ?? '').toUpperCase());
                $('#detail_metode').text(alat.metode_kalibrasi ?? '—');

                // --- Pressure data ---
                const naikBody = $('#pressure_naik');
                const turunBody = $('#pressure_turun');
                naikBody.empty();
                turunBody.empty();

                const pressures = (kalibrasi.pressure || []).map(p => ({
                    ...p,
                    tekanan: (p.tekanan || '').toString().toLowerCase().trim(),
                    titik_kalibrasi: p.titik_kalibrasi ?? '',
                    penunjuk_standar: p.penunjuk_standar ?? '',
                    penunjuk_alat: p.penunjuk_alat ?? '',
                    koreksi_standar: p.koreksi_standar ?? '',
                    tekanan_standar: p.tekanan_standar ?? '',
                    koreksi_alat: p.koreksi_alat ?? ''
                }));

                const naikArr = pressures.filter(p => p.tekanan === 'naik')
                    .sort((a, b) => a.titik_kalibrasi - b.titik_kalibrasi);
                const turunArr = pressures.filter(p => p.tekanan === 'turun')
                    .sort((a, b) => a.titik_kalibrasi - b.titik_kalibrasi);

                function formatNumber(val) {
                    const num = parseFloat(val);
                    if (isNaN(num)) return '—';
                    return num.toFixed(1); // hanya 1 angka desimal
                }

                function renderList(arr, $body) {
                    if (arr.length === 0) {
                        $body.append('<tr><td colspan="6" class="text-center text-muted">No data</td></tr>');
                        return;
                    }

                    let lastTitik = null;
                    arr.forEach((p) => {
                        const showTitik = lastTitik !== p.titik_kalibrasi;
                        const titikCell = showTitik ?
                            `<span class="badge badge-soft-primary">${formatNumber(p.titik_kalibrasi)}</span>` :
                            '';
                        lastTitik = p.titik_kalibrasi;

                        $body.append(`
                            <tr>
                                <td>${titikCell}</td>
                                <td>${formatNumber(p.penunjuk_standar)}</td>
                                <td>${formatNumber(p.penunjuk_alat)}</td>
                                <td>${formatNumber(p.koreksi_standar)}</td>
                                <td>${formatNumber(p.tekanan_standar)}</td>
                                <td>${formatNumber(p.koreksi_alat)}</td>
                            </tr>
                        `);
                    });
                }

                renderList(naikArr, naikBody);
                renderList(turunArr, turunBody);

                // --- U gabungan ---
                const tbody = $('#detail_gabungan');
                tbody.empty();

                if (kalibrasi.pressure_gabungan?.length > 0) {
                    kalibrasi.pressure_gabungan.forEach((pg) => {
                        tbody.append(`
                            <tr>
                                <td><span class="badge badge-soft-primary">${formatNumber(pg.titik_kalibrasi)}</span></td>
                                <td>${pg.u_naik}</td>
                                <td>${pg.u_turun}</td>
                                <td>${pg.u_naik_kuadrat}</td>
                                <td>${pg.u_turun_kuadrat}</td>
                                <td class="highlight-value">${pg.u_gabungan}</td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="6" class="text-center text-muted">No data</td></tr>');
                }

                $('#detailModal').modal('show');
            }

            function formatDate(dateString) {
                let date = new Date(dateString);
                let options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                return date.toLocaleDateString('id-ID', options);
            }

            function displayEmptyState(container, message = 'Tidak ada data sertifikat yang tersedia.') {
                container.append(`
                    <div class="col-md-12">
                        <div class="alert alert-info text-center" role="alert">
                            <i class="mdi mdi-information-outline me-2"></i> ${message}
                        </div>
                    </div>
                `);
            }
        });
    </script>
@endsection
