@extends('layouts.app')

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
                            <h4 class="card-title">History Kalibrasi Volumetrik</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover nowrap dt-responsive" id="historyTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Alat</th>
                                        <th>Tgl Kalibrasi</th>
                                        <th>Tgl Kalibrasi Ulang</th>
                                        <th>Lokasi</th>
                                        <th>Kondisi Ruangan</th>
                                        <th>Titik Kalibrasi</th>
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
                    url: `{{ url('api/kalibrasi/pressure/data') }}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        historyData = response.data;
                        renderTable();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                        alert('Gagal mengambil data. Silakan coba lagi.');
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
                    processing: true,
                    serverSide: false,
                    responsive: true,
                    scrollX: true,
                    language: {
                        lengthMenu: "Show _MENU_ entries",
                    },
                    columns: [{
                            data: null, // auto numbering
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            },
                        },
                        {
                            data: "alat.kode_alat",
                        },
                        {
                            data: "tgl_kalibrasi",
                            render: function(data) {
                                return formatDate(data);
                            },
                        },
                        {
                            data: "tgl_kalibrasi_ulang",
                            render: function(data) {
                                return formatDate(data);
                            },
                        },
                        {
                            data: "lokasi_kalibrasi",
                        },
                        {
                            data: null,
                            render: function(data) {
                                return `
                                    <p class="condition-text mb-0">
                                        Suhu: ${data.suhu_ruangan}°C<br>
                                        Kelembaban: ${data.kelembaban}%
                                    </p>
                                `;
                            },
                        },
                        {
                            data: "pressure_gabungan",
                            render: function(data) {
                                return data.length;
                            },
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function(data, type, row) {
                                return `
                                    <button class="btn btn-sm btn-outline-primary btn-detail" data-id="${row.id}" title="Detail Data">
                                        <i class="mdi mdi-eye"></i> Detail
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${row.id}" title="Delete Data">
                                        <i class="mdi mdi-delete"></i> Delete
                                    </button>
                                `;
                            }
                        }
                    ]
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

            // Show detail modal
            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                showDetailModal(id, historyData);
            });

            function showDetailModal(id, historyData) {
                let item = historyData.find(x => x.id === id);
                if (!item) return;

                // Isi data umum
                $('#detail_kode_alat').text(item.alat.kode_alat);
                $('#detail_nama_alat').text(item.alat.nama_alat);
                $('#detail_tgl_kalibrasi').text(formatDate(item.tgl_kalibrasi));
                $('#detail_tgl_ulang').text(formatDate(item.tgl_kalibrasi_ulang));
                $('#detail_lokasi').text(item.lokasi_kalibrasi);
                $('#detail_suhu').text(item.suhu_ruangan + '°C');
                $('#detail_kelembaban').text(item.kelembaban + '%');
                $('#detail_jenis').text(item.jenis_kalibrasi.toUpperCase());
                $('#detail_metode').text(item.alat.metode_kalibrasi);

                // Render pressure data
                let naikBody = $('#pressure_naik');
                let turunBody = $('#pressure_turun');
                naikBody.empty();
                turunBody.empty();
                let lastTitik = null;

                const pressures = (item.pressure || []).map(p => ({
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

                function renderList(item, arr, $body, tipe) {
                    if (!arr.length) {
                        $body.append('<tr><td colspan="9" class="text-center text-muted">Tidak ada data</td></tr>');
                        return;
                    }

                    const grouped = arr.reduce((acc, p) => {
                        if (!acc[p.titik_kalibrasi]) acc[p.titik_kalibrasi] = [];
                        acc[p.titik_kalibrasi].push(p);
                        return acc;
                    }, {});

                    const suffix = tipe === 'naik' ? '_naik' : '_turun';

                    Object.keys(grouped).forEach(titik => {
                        const dataTitik = grouped[titik];
                        const pg = item.pressure_gabungan?.find(pg => pg.titik_kalibrasi == titik);

                        dataTitik.forEach((p, i) => {
                            const showTitik = i === 0; // tampilkan hanya di baris pertama per titik
                            const showGabungan = i ===
                                0; // tampilkan nilai avg/sd/u hanya di baris pertama

                            $body.append(`
                                <tr>
                                    <td>${showTitik ? `<span class="badge bg-primary">${formatNumber(p.titik_kalibrasi)}</span>` : ''}</td>
                                    <td>${formatNumber(p.penunjuk_standar)}</td>
                                    <td>${formatNumber(p.penunjuk_alat)}</td>
                                    <td>${formatNumber(p.koreksi_standar)}</td>
                                    <td>${formatNumber(p.tekanan_standar)}</td>
                                    <td>${formatNumber(p.koreksi_alat)}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['avg_penunjuk_alat' + suffix]) : ''}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['avg_tekanan_standar' + suffix]) : ''}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['avg_kor_alat' + suffix]) : ''}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['std_deviasi' + suffix]) : ''}</td>
                                    <td>${showGabungan && pg ? formatNumber(pg['ketidak_pastian' + suffix]) : ''}</td>
                                </tr>
                            `);
                        });
                    });
                }

                renderList(item, naikArr, naikBody, 'naik');
                renderList(item, turunArr, turunBody, 'turun');

                // Render data gabungan
                if (item.pressure_gabungan && item.pressure_gabungan.length > 0) {
                    let tbody = $('#detail_gabungan');
                    tbody.empty();

                    $.each(item.pressure_gabungan, function(i, pg) {
                        let row = `
                        <tr>
                            <td><span class="badge badge-soft-primary">${formatNumber(pg.titik_kalibrasi)}</span></td>
                            <td>${parseFloat(pg.u_naik).toFixed(9)}</td>
                            <td>${parseFloat(pg.u_turun).toFixed(9)}</td>
                            <td>${parseFloat(pg.u_naik_kuadrat).toFixed(9)}</td>
                            <td>${parseFloat(pg.u_turun_kuadrat).toFixed(9)}</td>
                            <td class="highlight-value">${parseFloat(pg.u_gabungan).toFixed(9)}</td>
                        </tr>
                    `;
                        tbody.append(row);
                    });
                }

                // Show modal
                $('#detailModal').modal('show');
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
                            url: `{{ route('kalibrasi.pressure.delete', '') }}/` + id,
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
