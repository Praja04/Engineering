@extends('layouts.app')

@section('title', 'Data Kalibrasi Timbangan')

@section('styles')
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title d-sm-flex align-items-center justify-content-between">
                        {{-- <h4 class="mb-sm-0">Form Input TKBM</h4> --}}

                        <a href="{{ route('kalibrasi.data.dashboard') }}"
                            class="btn btn-outline-primary rounded-pill px-4 d-flex align-items-center">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">History Kalibrasi Timbangan</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover nowrap dt-responsive" id="historyTable" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Alat</th>
                                        <th>Tgl Kalibrasi</th>
                                        <th>Tgl Kalibrasi Ulang</th>
                                        <th>Lokasi</th>
                                        <th>Kondisi Ruangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                    <!-- Data akan diisi via jQuery -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
                        Detail Kalibrasi Timbangan
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
                                data-bs-target="#pembacaan-pane">Pembacaan</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#keseragaman-pane">Keseragaman
                                Skala</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pinggan-pane">Pinggan</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tare-pane">Tare</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#histerisis-pane">Histerisis</button>
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

                        <!-- Tab Pembacaan -->
                        <div class="tab-pane fade" id="pembacaan-pane">
                            <div id="detail_pembacaan" class="table-responsive"></div>
                            <hr>
                            <div id="detail_pembacaan_summary"></div>
                        </div>

                        <!-- Tab Keseragaman Skala -->
                        <div class="tab-pane fade" id="keseragaman-pane">
                            <div id="detail_keseragaman" class="table-responsive"></div>
                            <hr>
                            <div id="detail_keseragaman_summary"></div>
                        </div>

                        <!-- Tab Pinggan -->
                        <div class="tab-pane fade" id="pinggan-pane">
                            <div id="detail_pinggan" class="table-responsive"></div>
                            <hr>
                            <div id="detail_pinggan_summary"></div>
                        </div>

                        <!-- Tab Tare -->
                        <div class="tab-pane fade" id="tare-pane">
                            <div id="detail_tare" class="table-responsive"></div>
                            <hr>
                            <div id="detail_tare_summary"></div>
                        </div>

                        <!-- Tab Histerisis -->
                        <div class="tab-pane fade" id="histerisis-pane">
                            <div id="detail_histerisis" class="table-responsive"></div>
                            <hr>
                            <div id="detail_histerisis_summary"></div>
                        </div>
                    </div> <!-- /tab-content -->
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
            fetchHistoryData();

            // Data dari API
            let historyData = [];

            // Fetch data dari API
            function fetchHistoryData() {
                $.ajax({
                    url: `{{ url('api/kalibrasi/timbangan/data') }}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        historyData = response.data;
                        renderTable();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Gagal mengambil data.',
                            icon: 'error'
                        });
                    }
                });
            }

            // Render table
            function renderTable() {
                if ($.fn.DataTable.isDataTable('#historyTable')) {
                    $('#historyTable').DataTable().destroy();
                }

                $('#historyTable').DataTable({
                    data: historyData,
                    responsive: true,
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    scrollX: true, // hilangkan horizontal scroll biar lebih clean
                    ordering: false,
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                        search: "Cari:",
                        info: "Showing _START_ - _END_ from _TOTAL_ data",
                        infoEmpty: "Tidak ada data",
                        infoFiltered: "(difilter dari total _MAX_ data)"
                    },
                    columns: [{
                            data: null,
                            className: "text-center",
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "alat.kode_alat"
                        },
                        {
                            data: "tgl_kalibrasi",
                            render: data => formatDate(data)
                        },
                        {
                            data: "tgl_kalibrasi_ulang",
                            render: data => formatDate(data)
                        },
                        {
                            data: "lokasi_kalibrasi"
                        },
                        {
                            data: null,
                            render: data => `
                                <div>
                                    <small class="text-muted d-block">Suhu: ${data.suhu_ruangan}</small>
                                    <small class="text-muted">Kelembaban: ${data.kelembaban}</small>
                                </div>
                            `
                        },
                        {
                            data: null,
                            orderable: false,
                            className: "text-center",
                            render: (data, type, row) => `
                                <div>
                                    <button class="btn btn-sm btn-soft-info btn-detail" data-id="${row.id}" title="Detail">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-soft-danger delete-btn" data-id="${row.id}" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            `
                        }
                    ],
                    dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6 text-end"p>>',
                });

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

            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                showDetailModal(id, historyData);
            });

            function formatNumber(val) {
                if (val === null || val === undefined || val === '') return '-';
                const num = parseFloat(val);
                if (isNaN(num)) return val;
                return parseFloat(num.toFixed(2)).toString();
            }

            function showDetailModal(id, historyData) {
                const item = historyData.find(d => d.id === id);
                if (!item) {
                    Swal.fire('Oops!', 'Data tidak ditemukan!', 'error');
                    return;
                }

                const alat = item.alat ?? {};

                // === Informasi Umum ===
                $('#detail_kode_alat').text(alat.kode_alat ?? '-');
                $('#detail_nama_alat').text(alat.nama_alat ?? '-');
                $('#detail_tgl_kalibrasi').text(item.tgl_kalibrasi ?? '-');
                $('#detail_tgl_ulang').text(item.tgl_kalibrasi_ulang ?? '-');
                $('#detail_lokasi').text(item.lokasi_kalibrasi ?? '-');
                $('#detail_suhu').text(item.suhu_ruangan ?? '-');
                $('#detail_kelembaban').text(item.kelembaban ?? '-');
                $('#detail_jenis').text(alat.jenis_kalibrasi ?? '-');
                $('#detail_metode').text(alat.metode_kalibrasi ?? 'Tidak ada data metode.');

                renderKemampuanUlang(item);
                renderKeseragamanSkala(item);
                renderPinggan(item);
                renderTare(item);
                renderHisterisis(item);

                // === Tampilkan modal ===
                $('#detailModal').modal('show');
            }

            function renderKemampuanUlang(item) {
                const data = Array.isArray(item.kemampuan_ulang) ? item.kemampuan_ulang : [];
                const summary = item.kemampuan_ulang_summary ?? null;

                if (data.length === 0) {
                    $('#detail_pembacaan').html('<p class="text-muted">Tidak ada data pembacaan.</p>');
                    $('#detail_pembacaan_summary').html('');
                    return;
                }

                // Group berdasarkan jenis
                const grouped = data.reduce((acc, row) => {
                    if (!acc[row.jenis]) acc[row.jenis] = [];
                    acc[row.jenis].push(row);
                    return acc;
                }, {});

                let html = '';

                Object.entries(grouped).forEach(([jenis, rows]) => {
                    const rowHtml = rows.map(r => `
                        <tr>
                            <td>${r.ulangan ?? '-'}</td>
                            <td>${r.nilai_z ?? '-'}</td>
                            <td>${r.nilai_m ?? '-'}</td>
                            <td>${r.selisih ?? '-'}</td>
                            <td>${r.maks_perbedaan ?? '-'}</td>
                        </tr>
                    `).join('');

                    html += `
                        <h6 class="mt-3"><strong>${jenis.replace('_',' ')}</strong></h6>
                        <table class="table table-bordered table-sm mb-3 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Ulangan</th>
                                    <th>Nilai Z</th>
                                    <th>Nilai M</th>
                                    <th>Selisih</th>
                                    <th>Maks. Perbedaan</th>
                                </tr>
                            </thead>
                            <tbody>${rowHtml}</tbody>
                        </table>
                    `;
                });

                $('#detail_pembacaan').html(html);

                // === Summary ===
                if (summary) {
                    $('#detail_pembacaan_summary').html(`
                        <h6 class="mt-3"><strong>Summary</strong></h6>
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Jenis</th>
                                    <th>Standar Deviasi</th>
                                    <th>Maks. Perbedaan Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${summary.jenis ?? '-'}</td>
                                    <td>${summary.std_dev ?? '-'}</td>
                                    <td>${summary.maks_perbedaan_akhir ?? '-'}</td>
                                </tr>
                            </tbody>
                        </table>
                    `);
                } else {
                    $('#detail_pembacaan_summary').html('<p class="text-muted">Tidak ada data summary.</p>');
                }
            }

            function renderKeseragamanSkala(item) {
                const data = Array.isArray(item.keseragaman_skala) ? item.keseragaman_skala : [];
                const summary = item.keseragaman_skala_summary ?? null;

                if (data.length === 0) {
                    $('#detail_keseragaman').html('<p class="text-muted">Tidak ada data keseragaman.</p>');
                    $('#detail_keseragaman_summary').html('');
                    return;
                }

                let html = `
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Massa Ke</th>
                                <th>Jenis</th>
                                <th>Beban</th>
                                <th>Pembacaan</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                data.forEach(r => {
                    html += `
                        <tr>
                            <td>${r.massa_ke ?? '-'}</td>
                            <td>${r.jenis ?? '-'}</td>
                            <td>${r.beban ?? '-'}</td>
                            <td>${r.pembacaan ?? '-'}</td>
                        </tr>
                    `;
                });

                html += `</tbody></table>`;
                $('#detail_keseragaman').html(html);

                if (summary) {
                    $('#detail_keseragaman_summary').html(`
                        <h6 class="mt-3"><strong>Summary</strong></h6>
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Massa Ke</th>
                                    <th>Avg Z</th>
                                    <th>Avg M</th>
                                    <th>Selisih Z-M</th>
                                    <th>Koreksi Skala</th>
                                    <th>Absolut Koreksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${summary.massa_ke ?? '-'}</td>
                                    <td>${summary.avg_z ?? '-'}</td>
                                    <td>${summary.avg_m ?? '-'}</td>
                                    <td>${summary.selisih_zm ?? '-'}</td>
                                    <td>${summary.koreksi_skala ?? '-'}</td>
                                    <td>${summary.absolut_koreksi ?? '-'}</td>
                                </tr>
                            </tbody>
                        </table>
                    `);
                }
            }

            function renderPinggan(item) {
                const pinggan = item.pinggan ?? [];
                const summary = item.pinggan_summary ?? null;

                if (pinggan.length === 0) {
                    $('#detail_pinggan').html('<p class="text-muted">Tidak ada data pinggan.</p>');
                    return;
                }

                let html = '';

                pinggan.forEach((p, index) => {
                    const rowHtml = (p.details ?? []).map(d => `
                        <tr>
                            <td>${d.percobaan ?? '-'}</td>
                            <td>${d.posisi ?? '-'}</td>
                            <td>${d.nilai ?? '-'}</td>
                        </tr>
                    `).join('');

                    html += `
                        <h6 class="mt-3"><strong>Percobaan ${index + 1}</strong></h6>
                        <p>Diameter: ${p.diameter ?? '-'} | Massa: ${p.massa ?? '-'}</p>
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Percobaan</th>
                                    <th>Posisi</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>${rowHtml}</tbody>
                        </table>
                    `;
                });

                $('#detail_pinggan').html(html);

                if (summary) {
                    $('#detail_pinggan_summary').html(`
                        <h6 class="mt-3"><strong>Summary</strong></h6>
                        <table class="table table-bordered table-sm text-center">
                            <tbody>
                                <tr>
                                    <td>Minimum</td>
                                    <td>${summary.minimum ?? '-'}</td>
                                </tr>
                                <tr>
                                    <td>Maximum</td>
                                    <td>${summary.maximum ?? '-'}</td>
                                </tr>
                                <tr>
                                    <td>Selisih Maks</td>
                                    <td>${summary.selisih_maks ?? '-'}</td>
                                </tr>
                            </tbody>
                        </table>
                    `);
                }
            }

            function renderTare(item) {
                const data = item.tare ?? [];
                const summary = item.tare_summary ?? null;

                if (data.length === 0) {
                    $('#detail_tare').html('<p class="text-muted">Tidak ada data tare.</p>');
                    return;
                }

                let html = `
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Kondisi</th>
                                <th>Label</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                data.forEach(r => {
                    html += `
                        <tr>
                            <td>${r.kondisi ?? '-'}</td>
                            <td>${r.label ?? '-'}</td>
                            <td>${r.nilai ?? '-'}</td>
                        </tr>
                    `;
                });

                html += `</tbody></table>`;
                $('#detail_tare').html(html);

                if (summary) {
                    $('#detail_tare_summary').html(`
                        <h6 class="mt-3"><strong>Summary</strong></h6>
                        <table class="table table-bordered table-sm text-center">
                            <tr>
                                <td>Massa</td>
                                <td>${summary.massa ?? '-'}</td>
                            </tr>
                            <tr>
                                <td>Selisih MZ</td>
                                <td>${summary.selisih_mz ?? '-'}</td>
                            </tr>
                            <tr>
                                <td>Pengaruh</td>
                                <td>${summary.pengaruh ?? '-'}</td>
                            </tr>
                        </table>
                    `);
                }
            }

            function renderHisterisis(item) {
                const data = Array.isArray(item.histerisis) ? item.histerisis : [];
                const summary = item.histerisis_summary ?? null;

                const container = $('#detail_histerisis');
                container.empty();

                if (data.length === 0) {
                    container.append('<p class="text-muted">Tidak ada data histerisis.</p>');
                    return;
                }

                // === Group berdasarkan pengulangan ===
                const grouped = data.reduce((acc, row) => {
                    if (!acc[row.pengulangan]) acc[row.pengulangan] = {};
                    acc[row.pengulangan][row.label] = row.nilai;
                    return acc;
                }, {});

                let html = `
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Percobaan</th>
                                <th>M1</th>
                                <th>M2</th>
                                <th>Z1</th>
                                <th>Z2</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                Object.entries(grouped).forEach(([pengulangan, values]) => {
                    html += `
                        <tr>
                            <td>${pengulangan}</td>
                            <td>${values.m1 ?? '-'}</td>
                            <td>${values.m2 ?? '-'}</td>
                            <td>${values.z1 ?? '-'}</td>
                            <td>${values.z2 ?? '-'}</td>
                        </tr>
                    `;
                });

                html += `</tbody></table>`;
                container.append(html);

                // === Summary ===
                if (summary) {
                    container.append(`
                        <div class="mt-3">
                            <h6 class="fw-bold mb-2">Ringkasan Histerisis</h6>
                            <table class="table table-sm table-bordered text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pembacaan Terkecil</th>
                                        <th>½ Kapasitas</th>
                                        <th>Avg M1-M2</th>
                                        <th>Avg Z1-Z2</th>
                                        <th>Nilai MZ</th>
                                        <th>Histerisis</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${summary.pembacaan_terkecil ?? '-'}</td>
                                        <td>${summary.setengah_kapasitas ?? '-'}</td>
                                        <td>${summary.avg_m1m2 ?? '-'}</td>
                                        <td>${summary.avg_z1z2 ?? '-'}</td>
                                        <td>${summary.nilai_mz ?? '-'}</td>
                                        <td>${summary.histerisis ?? '-'}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `);
                }
            }

            // Delete btn
            $('#historyTable').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('kalibrasi.jangka-sorong.delete', '') }}/` + id,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message ||
                                        'Your file has been deleted.'
                                });
                                fetchHistoryData();
                            },
                            error: function(err) {
                                console.error("Error deleting data:", err);
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
