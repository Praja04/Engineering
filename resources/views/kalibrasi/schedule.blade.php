@extends('layouts.app')

@section('styles')
    <style>
        #scheduleTable tbody tr.status-urgent {
            background-color: #fff5f5;
        }

        #scheduleTable tbody tr.status-warning {
            background-color: #fffbf0;
        }

        #scheduleTable tbody tr.status-safe {
            background-color: #f8fff9;
        }

        .filter-btn {
            transition: all 0.3s ease;
            border-radius: 20px;
            font-weight: 500;
            padding: 0.4rem 1rem;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .filter-btn.active {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Valid -->
                <div class="col-md-4">
                    <div class="card card-animate shadow-sm border-0 rounded-4"
                        style="background: linear-gradient(135deg, #e8fdf5, #d0f5e6); color: #22543d;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-semibold mb-1">Valid</h6>
                                <h2 class="fw-bold mb-0" id="countValid">0</h2>
                                <p class="mb-0">More than 14 days</p>
                            </div>
                            <i class="mdi mdi-check-circle-outline fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>

                <!-- Almost Expired -->
                <div class="col-md-4">
                    <div class="card card-animate shadow-sm border-0 rounded-4"
                        style="background: linear-gradient(135deg, #fffbea, #fff3cd); color: #7c5700;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-semibold mb-1">Almost Expired</h6>
                                <h2 class="fw-bold mb-0" id="countAlmost">0</h2>
                                <p class="mb-0">Less than 14 days</p>
                            </div>
                            <i class="mdi mdi-alert-outline fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>

                <!-- Expired -->
                <div class="col-md-4">
                    <div class="card card-animate shadow-sm border-0 rounded-4"
                        style="background: linear-gradient(135deg, #fde2e4, #fad2e1); color: #6d0202;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-semibold mb-1">Expired</h6>
                                <h2 class="fw-bold mb-0" id="countExpired">0</h2>
                                <p class="mb-0">Past the recalibration date</p>
                            </div>
                            <i class="mdi mdi-close-circle-outline fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-primary text-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0" style="color: #fff !important">
                                    <i class="mdi mdi-calendar-clock me-2"></i>Calibration Schedule
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <button class="btn btn-sm btn-outline-primary filter-btn active" data-filter="all">
                                        <i class="mdi mdi-filter-variant me-1"></i>All
                                    </button>
                                    <button class="btn btn-sm btn-outline-info filter-btn" data-filter="Pressure">
                                        <i class="mdi mdi-gauge me-1"></i>Pressure
                                    </button>
                                    <button class="btn btn-sm btn-outline-success filter-btn" data-filter="Massa">
                                        <i class="mdi mdi-scale-balance me-1"></i>Massa
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="Temperature">
                                        <i class="mdi mdi-thermometer me-1"></i>Temperature
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="Volumetrik">
                                        <i class="mdi mdi-beaker me-1"></i>Volumetrik
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="Dimention">
                                        <i class="mdi mdi-ruler me-1"></i>Dimention
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="Magnetic">
                                        <i class="mdi mdi-magnet me-1"></i>Magnetic
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="scheduleTable" class="table table-hover align-middle text-nowrap"
                                    style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Alat</th>
                                            <th>Lokasi</th>
                                            <th>Jenis Kalibrasi</th>
                                            <th>Tanggal Kalibrasi</th>
                                            <th>Tanggal Kalibrasi Ulang</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data dari API -->
                                    </tbody>
                                </table>
                            </div>
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
            $.get("{{ url('api/kalibrasi/schedule') }}", function(res) {
                const schedules = res.data || [];

                schedules.sort((a, b) => new Date(a.tgl_kalibrasi_ulang) - new Date(b.tgl_kalibrasi_ulang));

                let rows = "";

                schedules.forEach((item, index) => {
                    const today = new Date();
                    const ulang = new Date(item.tgl_kalibrasi_ulang);
                    const diffDays = Math.ceil((ulang - today) / (1000 * 60 * 60 * 24));

                    let statusClass = "";
                    let statusBadge = "";
                    let validCount = 0;
                    let warningCount = 0;
                    let expiredCount = 0;

                    if (diffDays < 0) {
                        statusClass = "status-expired";
                        statusBadge =
                            `<span class="badge badge-soft-danger px-3 py-2">Expired</span>`;
                        expiredCount++;
                    } else if (diffDays <= 14) {
                        statusClass = "status-warning";
                        statusBadge =
                            `<span class="badge badge-soft-warning text-dark px-3 py-2">${diffDays} hari lagi</span>`;
                        warningCount++;
                    } else {
                        statusClass = "status-safe";
                        statusBadge =
                            `<span class="badge badge-soft-success px-3 py-2">${diffDays} hari lagi</span>`;
                        validCount++;
                    }

                    $("#countValid").text(validCount);
                    $("#countAlmost").text(warningCount);
                    $("#countExpired").text(expiredCount);
                    let jenis = item.jenis_kalibrasi.toLowerCase();

                    rows += `
                        <tr class="${statusClass}">
                            <td>${index + 1}</td>
                            <td>${item.alat?.kode_alat || '-'}</td>
                            <td>${item.lokasi_kalibrasi}</td>
                            <td>${getJenisBadge(item.jenis_kalibrasi)}</td>
                            <td>${item.tgl_kalibrasi}</td>
                            <td>${item.tgl_kalibrasi_ulang}</td>
                            <td>${statusBadge}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary calibrate-btn" 
                                        onclick="window.location.href='/kalibrasi/${jenis}/index'"
                                        data-id="${item.id}" 
                                        title="Calibrate Data">
                                    <i class="mdi mdi-hand-pointing-up"></i> Calibrate Now
                                </button>
                            </td>
                        </tr>
                    `;
                });

                $("#scheduleTable tbody").html(rows);

                // Inisialisasi DataTables
                const table = $('#scheduleTable').DataTable({
                    order: [
                        [5, 'asc']
                    ],
                    pageLength: 10,
                    responsive: true,
                    language: {
                        lengthMenu: "Show _MENU_ data",
                        paginate: {
                            next: "Next",
                            previous: "Prev"
                        }
                    }
                });

                // Filter button functionality
                $('.filter-btn').on('click', function() {
                    const filterValue = $(this).data('filter');

                    // Update active button
                    $('.filter-btn').removeClass('active');
                    $(this).addClass('active');

                    // Apply filter to DataTables
                    if (filterValue === 'all') {
                        table.column(3).search('').draw();
                    } else {
                        table.column(3).search('^' + filterValue + '$', true, false).draw();
                    }
                });
            });

            // Tentukan warna badge sesuai jenis kalibrasi
            function getJenisBadge(jenis) {
                // Capitalize huruf pertama
                const jenisCap = jenis.charAt(0).toUpperCase() + jenis.slice(1);

                switch (jenis.toLowerCase()) {
                    case "pressure":
                        return `<span class="badge badge-soft-info px-3 py-2">${jenisCap}</span>`;
                    case "massa":
                        return `<span class="badge badge-soft-success px-3 py-2">${jenisCap}</span>`;
                    case "temperature":
                        return `<span class="badge badge-soft-warning text-dark px-3 py-2">${jenisCap}</span>`;
                    case "volumetrik":
                        return `<span class="badge badge-soft-primary px-3 py-2">${jenisCap}</span>`;
                    case "dimention":
                        return `<span class="badge badge-soft-danger px-3 py-2">${jenisCap}</span>`;
                    case "magnetic":
                        return `<span class="badge badge-soft-secondary px-3 py-2">${jenisCap}</span>`;
                    default:
                        return `<span class="badge badge-soft-dark px-3 py-2">${jenisCap}</span>`;
                }
            }
        });
    </script>
@endsection
