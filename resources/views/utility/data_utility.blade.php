@extends('layouts.app')

@section('title', 'Data Utility')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Header -->
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="row align-items-end">
                                <div class="col-sm-10">
                                    <div class="p-3">
                                        <h1>Utility Data</h1>
                                        <p class="fs-16 lh-base">Periksa Utility Untuk Diri Kita Sendiri!</p>
                                    </div>
                                </div>
                                <div class="col-sm-2 text-end">
                                    <img src="{{ asset('assets/images/gudang.png') }}" class="img-fluid" alt=""
                                        style="max-height: 100px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="collectedMonthlyContainer" class="mt-3"></div>

            <!-- Card Unit -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card clickable card-unit listrik-card" data-unit="Listrik">
                        <div class="card-body text-center">
                            <h4 class="card-title">Pemakaian Listrik</h4>
                            <img src="{{ asset('assets/images/listrik.png') }}" alt="gambar" class="img-fluid"
                                style="border-radius: 20px; max-height: 150px; object-fit: contain;">
                            <p class="text-muted">Klik untuk data</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card clickable card-unit air-card" data-unit="Air">
                        <div class="card-body text-center">
                            <h4 class="card-title">Pemakaian Air</h4>
                            <img src="{{ asset('assets/images/air.png') }}" alt="gambar" class="img-fluid"
                                style="border-radius: 20px; max-height: 150px; object-fit: contain;">
                            <p class="text-muted">Klik untuk data</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card clickable card-unit chemical-card" data-unit="Chemical">
                        <div class="card-body text-center">
                            <h4 class="card-title">Pemakaian Chemical</h4>
                            <img src="{{ asset('assets/images/chemical.png') }}" alt="gambar" class="img-fluid"
                                style="border-radius: 20px; max-height: 150px; object-fit: contain;">
                            <p class="text-muted">Klik untuk data</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Container with Filters -->
            <div id="table-container" style="display: none;">
                <!-- Filter Controls -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filter Bulan</label>
                        <input type="month" id="filterMonth" class="form-control">
                        <small class="text-muted">Default: Bulan sekarang</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Cari Tanggal</label>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari tanggal...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filter Tanggal Spesifik</label>
                        <input type="date" id="filterDate" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">&nbsp;</label>
                        <button class="btn btn-secondary w-100" id="resetFilter">
                            <i class="ri-refresh-line"></i> Reset Filter
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 id="table-title" class="mb-0"></h5>
                            <span id="data-info" class="badge bg-info fs-14"></span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="dateTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30%">Tanggal</th>
                                        <th width="20%">Total Entries</th>
                                        <th width="50%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Baris diisi oleh JS/jQuery -->
                                </tbody>
                            </table>

                            <div id="pagination" class="mt-3 d-flex justify-content-center"></div>
                        </div>

                        <!-- Export Buttons -->
                        <div id="export-container" class="mt-3" style="display: none;">
                            <hr>
                            <div class="d-flex gap-2">
                                <button class="btn btn-success" id="exportListrikBtn" style="display: none;">
                                    <i class="ri-file-excel-line"></i> Export Excel (Listrik)
                                </button>
                                <button class="btn btn-success" id="exportAirBtn" style="display: none;">
                                    <i class="ri-file-excel-line"></i> Export Excel (Air)
                                </button>
                                <button class="btn btn-success" id="exportChemicalBtn" style="display: none;">
                                    <i class="ri-file-excel-line"></i> Export Excel (Chemical)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Pilih Bulan untuk Export -->
            <div class="modal fade" id="bulanModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pilih Bulan untuk Export</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label fw-semibold">Pilih Bulan</label>
                            <input type="month" id="bulanPicker" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" id="confirmExport">
                                <i class="ri-download-line"></i> Download
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Detail -->
            <div class="modal fade" id="detailModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Details: <span id="modalDate"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="modalContent">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Edit Panel -->
            <div class="modal fade" id="editPanelModal" tabindex="-1">
                <div class="modal-dialog">
                    <form id="editPanelForm" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="editPanelFormBody">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Submit Monthly removed -->
        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const userJabatan = "{{ Auth::user()->jabatan }}";

        $(document).ready(function() {
            const endpoints = {
                Listrik: "{{ url('api/utility/data/listrik') }}",
                Air: "{{ url('utility/data/air') }}",
                Chemical: "{{ url('utility/data/chemical') }}"
            };

            let currentUnit = '';
            let allData = [];
            let isMonthLocked = false;

            // Set default bulan ke bulan sekarang
            const currentMonth = new Date().toISOString().slice(0, 7); // Format: YYYY-MM
            $('#filterMonth').val(currentMonth);
            $('#bulanPicker').val(currentMonth); // Set default untuk export juga

            $('.card-unit').on('click', function() {
                currentUnit = $(this).data('unit');
                $('#table-title').text(`Data Pemakaian ${currentUnit}`);
                $('#table-container').show();

                // Scroll ke table container
                $('html, body').animate({
                    scrollTop: $('#table-container').offset().top - 100
                }, 500);

                loadData();

                $('#export-container').show();
                $('#exportListrikBtn').toggle(currentUnit === 'Listrik');
                $('#exportAirBtn').toggle(currentUnit === 'Air');
                $('#exportChemicalBtn').toggle(currentUnit === 'Chemical');
            });

            function loadData() {
                const bulan = $('#filterMonth').val();

                // Show loading indicator
                const tbody = $('#dateTable tbody');
                tbody.html(
                    '<tr><td colspan="3" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Memuat data...</td></tr>'
                );

                $.get("{{ url('utility/approval/check') }}", {
                    bulan: bulan,
                    tipe: currentUnit.toLowerCase()
                }, function(checkRes) {
                    isMonthLocked = checkRes.locked;

                    $.ajax({
                        url: endpoints[currentUnit],
                        type: 'GET',
                        data: {
                            bulan: bulan
                        },
                        success: function(data) {
                            allData = data;
                            updateDataInfo(data.length, bulan);
                            applyFilters();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading data:', error);
                            tbody.html(
                                '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>'
                            );
                            Swal.fire({
                                title: 'Error!',
                                text: 'Gagal memuat data. Silakan coba lagi.',
                                icon: 'error'
                            });
                        }
                    });
                });
            }

            function updateDataInfo(count, bulan) {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                    'Des'
                ];
                const [year, month] = bulan.split('-');
                const monthName = monthNames[parseInt(month) - 1];
                $('#data-info').text(`${count} data | ${monthName} ${year}`);
            }

            $('#searchInput, #filterDate').on('input change', applyFilters);

            $('#filterMonth').on('change', function() {
                if (currentUnit) {
                    loadData(); // Reload data dari server dengan filter bulan baru
                }
            });

            $('#resetFilter').on('click', function() {
                $('#searchInput, #filterDate').val('');
                $('#filterMonth').val(currentMonth); // Reset ke bulan sekarang
                if (currentUnit) {
                    loadData();
                }
            });

            $('#exportListrikBtn, #exportAirBtn, #exportChemicalBtn').on('click', function() {
                $('#bulanPicker').val($('#filterMonth').val()); // Set ke bulan yang sedang aktif
                $('#bulanModal').modal('show');
            });

            $('#confirmExport').on('click', function() {
                const bulan = $('#bulanPicker').val();
                if (!bulan) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Silakan pilih bulan terlebih dahulu.',
                        icon: 'warning'
                    });
                    return;
                }

                let baseUrl = "{{ url('utility/export-pemakaian-') }}";
                let url = '';

                switch (currentUnit) {
                    case 'Listrik':
                        url = `${baseUrl}listrik?bulan=${bulan}`;
                        break;
                    case 'Air':
                        url = `${baseUrl}air?bulan=${bulan}`;
                        break;
                    case 'Chemical':
                        url = `${baseUrl}chemical?bulan=${bulan}`;
                        break;
                    default:
                        Swal.fire({
                            title: 'Error!',
                            text: 'Unit tidak dikenal untuk export.',
                            icon: 'error'
                        });
                        return;
                }

                window.open(url, '_blank');
                $('#bulanModal').modal('hide');
            });

            $(document).on('click', '.view-detail', function() {
                const entry = $(this).data('entry');
                $('#modalDate').text(entry.tanggal);
                $('#modalContent').html(
                    '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading data...</p></div>'
                );
                $('#detailModal').modal('show');

                // Render data dengan sedikit delay untuk smooth UX
                setTimeout(() => {
                    $('#modalContent').html(generatePivot(entry));
                }, 200);
            });

            function applyFilters() {
                const keyword = $('#searchInput').val().toLowerCase();
                const filterDate = $('#filterDate').val();

                const filtered = allData.filter(item => {
                    const tanggal = item.tanggal.toLowerCase();
                    return (!keyword || tanggal.includes(keyword)) &&
                        (!filterDate || tanggal === filterDate);
                });

                renderPagination(filtered);
            }

            function getEntryCount(entry) {
                if (currentUnit === 'Air' || currentUnit === 'Chemical') return entry.data?.length || 0;
                if (currentUnit === 'Listrik') return entry.panels?.length || 0;
                return 0;
            }

            function renderPagination(data, itemsPerPage = 10) {
                const paginationContainer = $('#pagination').empty();
                const totalPages = Math.ceil(data.length / itemsPerPage);
                let currentPage = 1;

                function renderPage(page) {
                    const start = (page - 1) * itemsPerPage;
                    const sliced = data.slice(start, start + itemsPerPage);
                    renderTableRows(sliced);
                }

                function renderTableRows(dataSlice) {
                    const tbody = $('#dateTable tbody').empty();

                    if (!dataSlice.length) {
                        tbody.append(
                            '<tr><td colspan="3" class="text-center text-muted py-4"><i class="ri-file-list-line fs-1 d-block mb-2"></i>Tidak ada data ditemukan.</td></tr>'
                        );
                        return;
                    }

                    dataSlice.forEach(item => {
                        const count = getEntryCount(item);
                        const row = `
                    <tr>
                        <td><strong>${item.tanggal}</strong></td>
                        <td><span class="badge bg-primary-subtle text-primary">${count} entries</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary view-detail" data-entry='${JSON.stringify(item)}'>
                                <i class="ri-eye-line"></i> Lihat Detail
                            </button>
                        </td>
                    </tr>`;
                        tbody.append(row);
                    });
                }

                function buildButtons() {
                    paginationContainer.empty();

                    if (totalPages <= 1) return; // No pagination needed

                    // Previous button
                    if (currentPage > 1) {
                        const prevBtn = $(
                            `<button class="btn btn-sm btn-outline-primary mx-1"><i class="ri-arrow-left-s-line"></i></button>`
                        );
                        prevBtn.on('click', () => {
                            currentPage--;
                            renderPage(currentPage);
                            buildButtons();
                        });
                        paginationContainer.append(prevBtn);
                    }

                    // Page numbers
                    for (let i = 1; i <= totalPages; i++) {
                        // Show first, last, current, and adjacent pages
                        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                            const btn = $(
                                `<button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'} mx-1">${i}</button>`
                            );
                            btn.on('click', () => {
                                currentPage = i;
                                renderPage(currentPage);
                                buildButtons();
                            });
                            paginationContainer.append(btn);
                        } else if (i === currentPage - 2 || i === currentPage + 2) {
                            paginationContainer.append('<span class="mx-1">...</span>');
                        }
                    }

                    // Next button
                    if (currentPage < totalPages) {
                        const nextBtn = $(
                            `<button class="btn btn-sm btn-outline-primary mx-1"><i class="ri-arrow-right-s-line"></i></button>`
                        );
                        nextBtn.on('click', () => {
                            currentPage++;
                            renderPage(currentPage);
                            buildButtons();
                        });
                        paginationContainer.append(nextBtn);
                    }
                }

                renderPage(currentPage);
                buildButtons();
            }

            function generatePivot(entry) {
                switch (currentUnit) {
                    case 'Listrik':
                        return buildListrikTable(entry);
                    case 'Air':
                        return buildAirTable(entry);
                    case 'Chemical':
                        return buildChemicalTable(entry);
                    default:
                        return '<p class="text-muted">Data tidak tersedia.</p>';
                }
            }

            function renderTable(headers, rows, rowHeader) {
                return `
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-bordered table-striped table-hover" style="min-width: 800px;">
                    <thead class="table-light">
                        <tr>
                            <th class="bg-primary text-white">${rowHeader}</th>
                            ${headers.map(h => `<th class="bg-primary text-white">${h}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>${rows.join('')}</tbody>
                </table>
            </div>`;
            }

            function buildListrikTable(entry) {
                const headers = entry.panels;
                const parameters = Object.keys(entry.rows || {});

                const operatorRow = `<tr class="table-secondary">
                <th>Operator</th>
                ${headers.map(p => {
                    const operatorName = entry.operator?.[p] ?? '-';
                    const editButton = (userJabatan !== 'operator' && !isMonthLocked) ? `
                                    <button class="btn btn-sm btn-warning btn-edit-panel mt-1" 
                                            data-panel="${p}" 
                                            data-entry='${JSON.stringify(entry)}'>
                                        <i class="ri-edit-line"></i> Edit
                                    </button>` : '';
                    return `<td>${operatorName}${editButton}</td>`;
                }).join('')}
            </tr>`;

                const usageRow = `<tr class="table-info">
                <th>Usage (MWh)</th>
                ${headers.map(p => {
                    const usage = entry.usage?.[p];
                    const displayValue = usage !== null && usage !== undefined ? parseFloat(usage).toFixed(3) : '-';
                    return `<td><strong>${displayValue}</strong></td>`;
                }).join('')}
            </tr>`;

                const paramRows = parameters.map(param => {
                    const cells = headers.map(p => {
                        const value = entry.rows[param][p];
                        return `<td>${value !== null && value !== undefined ? value : '-'}</td>`;
                    });
                    return `<tr><th>${param.toUpperCase()}</th>${cells.join('')}</tr>`;
                });

                return renderTable(headers, [operatorRow, usageRow, ...paramRows], 'Keterangan');
            }

            function buildAirTable(entry) {
                const headers = entry.data.map(d => d.jenis_pemakaian);
                const rows = [{
                        label: 'Pemakaian Awal',
                        cells: entry.data.map(d => `<strong>${d.pemakaian_awal}</strong> m³`)
                    },
                    {
                        label: 'Pemakaian Akhir',
                        cells: entry.data.map(d => `<strong>${d.pemakaian_akhir}</strong> m³`)
                    },
                    {
                        label: 'Total Pemakaian',
                        cells: entry.data.map(d => {
                            const total = parseFloat(d.pemakaian_akhir) - parseFloat(d.pemakaian_awal);
                            return `<span class="badge bg-success">${total.toFixed(2)} m³</span>`;
                        })
                    },
                    {
                        label: 'Created By',
                        cells: entry.data.map(d => d.created_by)
                    },
                    {
                        label: 'Action',
                        cells: entry.data.map(d => {
                            if (userJabatan !== 'operator' && !isMonthLocked) {
                                return `
                                <button class="btn btn-sm btn-warning btn-edit-air"
                                        data-entry='${JSON.stringify(d)}'
                                        data-tanggal="${entry.tanggal}">
                                    <i class="ri-edit-line"></i> Edit
                                </button>`;
                            } else {
                                return '-';
                            }
                        })
                    }
                ];

                const markup = rows.map((r, idx) => {
                    const rowClass = idx < 3 ? 'table-light' : '';
                    return `<tr class="${rowClass}"><th>${r.label}</th>${r.cells.map(c => `<td>${c}</td>`).join('')}</tr>`;
                });
                return renderTable(headers, markup, 'Jenis Pemakaian');
            }

            function buildChemicalTable(entry) {
                const shifts = Array.from(new Set(entry.data.flatMap(d => d.shifts.map(s => s.shift))));
                const canEdit = userJabatan !== 'operator' && !isMonthLocked;

                const rows = entry.data.map(d => {
                    const dataRows = ['nilai_pemakaian', 'area', 'operator', 'notes'].map(attr => {
                        const cells = shifts.map(shift => {
                            const shiftData = d.shifts.find(s => s.shift === shift);
                            return `<td>${shiftData?.[attr] ?? '-'}</td>`;
                        });
                        const label = attr.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                        return `<tr><th>${label}</th>${cells.join('')}</tr>`;
                    });

                    const actionCells = shifts.map(shift => {
                        const shiftData = d.shifts.find(s => s.shift === shift);
                        if (shiftData && canEdit) {
                            return `<td>
                            <button class="btn btn-sm btn-warning btn-edit-chemical"
                                    data-shift="${shift}" 
                                    data-jenis="${d.jenis_pemakaian}" 
                                    data-tanggal="${entry.tanggal}"
                                    data-entry='${JSON.stringify(shiftData)}'>
                                <i class="ri-edit-line"></i> Edit
                            </button>
                        </td>`;
                        } else {
                            return `<td>-</td>`;
                        }
                    });

                    const actionRow = `<tr class="table-light"><th>Action</th>${actionCells.join('')}</tr>`;

                    return `
                    <tr class="table-primary"><th colspan="${shifts.length + 1}"><strong>${d.jenis_pemakaian}</strong></th></tr>
                    ${dataRows.join('')}
                    ${actionRow}
                `;
                });

                return renderTable(shifts, rows, 'Parameter');
            }

            $(document).on('click', '.btn-edit-panel', function() {
                const panel = $(this).data('panel');
                const data = $(this).data('entry');

                const volt = data.rows?.volt?.[panel] ?? '';
                const amp = data.rows?.a?.[panel] ?? '';
                const kw = data.rows?.kw?.[panel] ?? '';
                const mwh = data.rows?.mwh?.[panel] ?? '';
                const cos = data.rows?.cos?.[panel] ?? '';

                const formHtml = `
                <input type="hidden" name="tanggal" value="${data.tanggal}">
                <input type="hidden" name="panel_type" value="${panel}">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Volt</label>
                    <input type="number" step="0.01" class="form-control" name="volt" value="${volt}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ampere</label>
                    <input type="number" step="0.01" class="form-control" name="a" value="${amp}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">KW</label>
                    <input type="number" step="0.01" class="form-control" name="kw" value="${kw}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">MWH</label>
                    <input type="number" step="0.001" class="form-control" name="mwh" value="${mwh}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Cos φ</label>
                    <input type="number" step="0.01" class="form-control" name="cos" value="${cos}">
                </div>
            `;

                $('#editPanelFormBody').html(formHtml);
                $('#editPanelModal').modal('show');
            });

            $(document).on('click', '.btn-edit-air', function() {
                const data = $(this).data('entry');
                const tanggal = $(this).data('tanggal');

                const formHtml = `
                <input type="hidden" name="id" value="${data.id ?? ''}">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" value="${tanggal}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jenis Pemakaian</label>
                    <input class="form-control" name="jenis_pemakaian" value="${data.jenis_pemakaian}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pemakaian Awal (m³)</label>
                    <input type="number" step="0.01" class="form-control" name="pemakaian_awal" value="${data.pemakaian_awal}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pemakaian Akhir (m³)</label>
                    <input type="number" step="0.01" class="form-control" name="pemakaian_akhir" value="${data.pemakaian_akhir}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea class="form-control" name="notes" rows="3">${data.notes ?? ''}</textarea>
                </div>
            `;

                $('#editPanelFormBody').html(formHtml);
                $('#editPanelModal').modal('show');
            });

            $(document).on('click', '.btn-edit-chemical', function() {
                const shift = $(this).data('shift');
                const jenis = $(this).data('jenis');
                const tanggal = $(this).data('tanggal');
                const data = $(this).data('entry');
                const rawNilai = data.nilai_pemakaian ?? '';
                const angkaNilai = typeof rawNilai === 'string' ? rawNilai.match(/\d+(\.\d+)?/)?.[0] ?? '' :
                    rawNilai;

                const formHtml = `
                <input type="hidden" name="tanggal" value="${tanggal}">
                <input type="hidden" name="shift" value="${shift}">
                <input type="hidden" name="chemical_area" value="${data.area ?? ''}">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jenis Pemakaian</label>
                    <input class="form-control" name="jenis_pemakaian" value="${jenis}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nilai Pemakaian</label>
                    <input type="number" step="0.01" class="form-control" name="nilai_pemakaian" value="${angkaNilai}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea class="form-control" name="notes" rows="3">${data.notes ?? ''}</textarea>
                </div>
            `;

                $('#editPanelFormBody').html(formHtml);
                $('#editPanelModal').modal('show');
            });

            $('#editPanelForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                let url = '';

                if (currentUnit === 'Listrik') {
                    url = "{{ url('utility/update-panel-listrik') }}";
                } else if (currentUnit === 'Air') {
                    url = "{{ url('utility/update-pemakaian-air') }}";
                } else if (currentUnit === 'Chemical') {
                    url = "{{ url('utility/update-pemakaian-chemical') }}";
                }

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.post(url, formData, function(res) {
                    $('#editPanelModal').modal('hide');
                    $('#detailModal').modal('hide');

                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil diperbarui.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    setTimeout(() => {
                        loadData(); // Reload data setelah update
                    }, 2000);
                }).fail(function(xhr) {
                    const errorMsg = xhr.responseJSON?.message ||
                        'Gagal menyimpan data. Silakan periksa kembali.';
                    Swal.fire({
                        title: 'Gagal!',
                        text: errorMsg,
                        icon: 'error'
                    });
                }).always(function() {
                    submitBtn.prop('disabled', false).html(originalText);
                });
            });

            // loadCollected and submitMonthly handlers removed
        });
    </script>

    <style>
        .clickable {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .clickable:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .listrik-card {
            border-left: 4px solid #dc3545;
        }

        .listrik-card:hover {
            background: linear-gradient(135deg, #ffe5e5 0%, #fff 100%);
            border-left-color: #dc3545;
        }

        .air-card {
            border-left: 4px solid #0d6efd;
        }

        .air-card:hover {
            background: linear-gradient(135deg, #e0f0ff 0%, #fff 100%);
            border-left-color: #0d6efd;
        }

        .chemical-card {
            border-left: 4px solid #6c757d;
        }

        .chemical-card:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #fff 100%);
            border-left-color: #6c757d;
        }

        .table-border-thick td,
        .table-border-thick th {
            border: 1px solid #333 !important;
        }

        /* Smooth transitions for all elements */
        .card,
        .btn,
        .modal-content {
            transition: all 0.3s ease;
        }

        /* Better pagination styling */
        #pagination button {
            transition: all 0.2s ease;
        }

        #pagination button:hover:not(.btn-primary) {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        /* Loading spinner center alignment */
        .spinner-border {
            vertical-align: middle;
        }

        /* Badge styling */
        .badge {
            font-weight: 500;
            padding: 0.5rem 1rem;
        }

        /* Form label styling */
        .form-label.fw-semibold {
            margin-bottom: 0.5rem;
            color: #495057;
        }

        /* Modal animations */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
        }

        /* Table hover effect */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            cursor: pointer;
        }

        /* Responsive table improvements */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            #pagination button {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
        }
    </style>
@endsection
