@extends('layouts.app')

@section('title', 'Data Riwayat Biaya Chemical WWTP')

@section('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(41, 156, 219, 0.25);
            border-color: #299cdb;
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }

        .nav-tabs-custom .nav-link {
            font-weight: 600;
            color: #495057;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 0.75rem 1.25rem;
            transition: all 0.2s;
        }

        .nav-tabs-custom .nav-link.active {
            color: #299cdb;
            border-bottom-color: #299cdb;
            background-color: transparent;
        }

        .number-cell {
            font-family: monospace;
            text-align: right;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">Riwayat Data Biaya Chemical WWTP</h4>
                            <p class="text-muted mb-0">Daftar pencatatan manual, perhitungan biaya bulanan, dan biaya per
                                meter kubik.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/wwtp/form_biaya_chemical') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Data Biaya
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="filterTahun" class="form-label small text-muted fw-semibold">Filter
                                        Tahun</label>
                                    <select id="filterTahun" class="form-select">
                                        <option value="">Semua Tahun</option>
                                        @php
                                            $currYear = date('Y');
                                        @endphp
                                        @for ($y = $currYear - 3; $y <= $currYear + 1; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterBulan" class="form-label small text-muted fw-semibold">Filter
                                        Bulan</label>
                                    <select id="filterBulan" class="form-select">
                                        <option value="">Semua Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="searchData" class="form-label small text-muted fw-semibold">Cari
                                        Data</label>
                                    <input type="text" id="searchData" class="form-control" placeholder="Cari data...">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="btnReset" class="btn btn-soft-secondary w-100">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 p-0">
                            <!-- Custom Nav Tabs matching user's spreadsheet tabs -->
                            <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tabPemakaian" role="tab"
                                        aria-selected="true">
                                        <i class="mdi mdi-flask-outline me-1"></i> 1. Pemakaian Chemical (kg/bulan)
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tabBiaya" role="tab"
                                        aria-selected="false">
                                        <i class="mdi mdi-currency-usd me-1"></i> 2. Biaya Chemical (Rp/bulan)
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tabBiayaM3" role="tab"
                                        aria-selected="false">
                                        <i class="mdi mdi-speedometer me-1"></i> 3. Biaya per Kubik (Rp/m³)
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content text-muted">

                                <!-- TAB 1: PEMAKAIAN CHEMICAL -->
                                <div class="tab-pane active" id="tabPemakaian" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle mb-0 text-nowrap"
                                            id="tablePemakaian">
                                            <!-- Generated dynamically by JS -->
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB 2: BIAYA CHEMICAL -->
                                <div class="tab-pane" id="tabBiaya" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle mb-0 text-nowrap" id="tableBiaya">
                                            <!-- Generated dynamically by JS -->
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB 3: BIAYA PER KUBIK -->
                                <div class="tab-pane" id="tabBiayaM3" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle mb-0 text-nowrap"
                                            id="tableBiayaM3">
                                            <!-- Generated dynamically by JS -->
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <!-- Pagination Container -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted small" id="paginationInfo">
                                    Menampilkan 0 sampai 0 dari 0 data
                                </div>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-rounded mb-0" id="paginationList">
                                        <!-- Will be populated dynamically -->
                                    </ul>
                                </nav>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modalEditLabel">Edit Data Biaya Chemical</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEdit">
                    @csrf
                    <input type="hidden" id="recordId" name="id">
                    <div class="modal-body text-dark">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Periode</label>
                            <div class="bg-light p-3 rounded">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="small text-muted">Tahun:</div>
                                        <div class="fw-bold fs-5 text-dark" id="editTahun">-</div>
                                    </div>
                                    <div class="col-6 border-start">
                                        <div class="small text-muted">Bulan:</div>
                                        <div class="fw-bold fs-5 text-primary" id="editBulan">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_limbah" class="form-label fw-semibold">Limbah Diolah (m³)</label>
                            <input type="number" step="0.01" class="form-control" id="edit_limbah"
                                name="limbah_di_olah" required min="0">
                        </div>

                        <div class="border-top pt-3">
                            <h6 class="text-primary fw-bold mb-3">Input Kuantitas Chemical (kg/bulan)</h6>
                            <div class="row" id="editChemicalInputsContainer">
                                <!-- Dynamic inputs generated via JS -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnUpdateRecord">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let currentDataList = [];
            let activeStandards = [];

            // Helper: nama bulan Indonesia
            const indonesianMonths = [
                "", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            // Format to Indonesian Rupiah representation
            function formatRupiah(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'decimal',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                }).format(value);
            }

            // Load Data function
            function loadData(page = 1) {
                currentPage = page;
                const tahun = $('#filterTahun').val();
                const bulan = $('#filterBulan').val();
                const search = $('#searchData').val();

                $.ajax({
                    url: "{{ url('api/wwtp-biaya-chemical') }}",
                    method: 'GET',
                    data: {
                        page: page,
                        tahun: tahun,
                        bulan: bulan,
                        search: search,
                        per_page: 12
                    },
                    success: function(response) {
                        currentDataList = response.records.data;
                        activeStandards = response.standards;

                        renderTables(currentDataList, activeStandards, response.records.from);
                        renderPagination(response.records);
                    },
                    error: function() {
                        const errorMsg =
                            `<tbody class="text-center text-danger py-4"><tr><td colspan="10">Gagal memuat data. Silakan coba kembali.</td></tr></tbody>`;
                        $('#tablePemakaian, #tableBiaya, #tableBiayaM3').html(errorMsg);
                    }
                });
            }

            // Init Load
            loadData(1);

            // Table rendering
            function renderTables(records, standards, fromIndex) {
                if (standards.length === 0) {
                    const emptyMsg =
                        `<tbody><tr><td class="text-center py-4 text-muted">Tidak ada data chemical di master</td></tr></tbody>`;
                    $('#tablePemakaian, #tableBiaya, #tableBiayaM3').html(emptyMsg);
                    return;
                }

                // 1. Build table headers
                let thPemakaian =
                    `<thead class="table-light text-center"><tr><th style="width: 60px;">No</th><th>Tahun</th><th>Bulan</th><th>Limbah Diolah (m³)</th>`;
                let thBiaya =
                    `<thead class="table-light text-center"><tr><th style="width: 60px;">No</th><th>Tahun</th><th>Bulan</th>`;
                let thBiayaM3 =
                    `<thead class="table-light text-center"><tr><th style="width: 60px;">No</th><th>Tahun</th><th>Bulan</th><th class="table-success">Total Cost/m³ (Rp)</th>`;

                standards.forEach(std => {
                    thPemakaian += `<th>${std.chemical_name} (kg/bulan)</th>`;
                    thBiaya += `<th>Cost ${std.chemical_name} (Rp)</th>`;
                    thBiayaM3 += `<th>Cost ${std.chemical_name}/m³</th>`;
                });

                thPemakaian += `<th style="width: 120px;">Aksi</th></tr></thead>`;
                thBiaya +=
                    `<th class="table-info">Total Cost Chemical (Rp)</th><th style="width: 120px;">Aksi</th></tr></thead>`;
                thBiayaM3 += `<th style="width: 120px;">Aksi</th></tr></thead>`;

                // 2. Build table bodies
                let tbPemakaian = '<tbody>';
                let tbBiaya = '<tbody>';
                let tbBiayaM3 = '<tbody>';

                if (records.length === 0) {
                    const colCount = standards.length + 5;
                    const emptyRow =
                        `<tr><td colspan="${colCount}" class="text-center py-4 text-muted">Tidak ada data ditemukan</td></tr>`;
                    tbPemakaian += emptyRow;
                    tbBiaya += emptyRow;
                    tbBiayaM3 += emptyRow;
                } else {
                    records.forEach((item, index) => {
                        const dateObj = new Date(item.tanggal);
                        const tahun = dateObj.getFullYear();
                        const bulanText = indonesianMonths[dateObj.getMonth() + 1];
                        const num = (fromIndex || 1) + index;

                        const actionButtons = `
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-soft-warning btn-sm btn-edit" data-id="${item.id}">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button class="btn btn-soft-danger btn-sm btn-delete" data-id="${item.id}">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        `;

                        // Row Pemakaian
                        tbPemakaian += `
                            <tr>
                                <td class="text-center fw-semibold">${num}</td>
                                <td class="text-center">${tahun}</td>
                                <td class="text-center fw-semibold text-dark">${bulanText}</td>
                                <td class="number-cell fw-semibold text-info">${formatRupiah(item.limbah_di_olah)}</td>
                        `;

                        // Row Biaya
                        tbBiaya += `
                            <tr>
                                <td class="text-center fw-semibold">${num}</td>
                                <td class="text-center">${tahun}</td>
                                <td class="text-center fw-semibold text-dark">${bulanText}</td>
                        `;

                        // Row BiayaM3
                        tbBiayaM3 += `
                            <tr>
                                <td class="text-center fw-semibold">${num}</td>
                                <td class="text-center">${tahun}</td>
                                <td class="text-center fw-semibold text-dark">${bulanText}</td>
                                <td class="number-cell table-success fw-bold text-success">${formatRupiah(item.total_cost_m3)}</td>
                        `;

                        // Fill dynamically based on standards
                        standards.forEach(std => {
                            const chem = item.chemicals[std.chemical_name] || {
                                qty: 0,
                                cost: 0,
                                cost_m3: 0
                            };

                            tbPemakaian += `<td class="number-cell">${formatRupiah(chem.qty)}</td>`;
                            tbBiaya += `<td class="number-cell">${formatRupiah(chem.cost)}</td>`;
                            tbBiayaM3 +=
                                `<td class="number-cell">${formatRupiah(chem.cost_m3)}</td>`;
                        });

                        tbPemakaian += actionButtons + '</tr>';
                        tbBiaya +=
                            `<td class="number-cell table-info fw-bold text-primary">${formatRupiah(item.total_cost)}</td>` +
                            actionButtons + '</tr>';
                        tbBiayaM3 += actionButtons + '</tr>';
                    });

                    // Hitung total untuk baris Total (Column 4 dst, 1-3 colspan Total)
                    let totalLimbah = 0;
                    let totalCost = 0;
                    let totalCostM3 = 0;
                    let totalChemicalsQty = {};
                    let totalChemicalsCost = {};
                    let totalChemicalsCostM3 = {};

                    standards.forEach(std => {
                        totalChemicalsQty[std.chemical_name] = 0;
                        totalChemicalsCost[std.chemical_name] = 0;
                        totalChemicalsCostM3[std.chemical_name] = 0;
                    });

                    records.forEach(item => {
                        totalLimbah += parseFloat(item.limbah_di_olah) || 0;
                        totalCost += parseFloat(item.total_cost) || 0;
                        totalCostM3 += parseFloat(item.total_cost_m3) || 0;

                        standards.forEach(std => {
                            const chem = item.chemicals[std.chemical_name] || {
                                qty: 0,
                                cost: 0,
                                cost_m3: 0
                            };
                            totalChemicalsQty[std.chemical_name] += parseFloat(chem.qty) || 0;
                            totalChemicalsCost[std.chemical_name] += parseFloat(chem.cost) || 0;
                            totalChemicalsCostM3[std.chemical_name] += parseFloat(chem.cost_m3) ||
                                0;
                        });
                    });

                    // Build total rows
                    let trTotalPemakaian = `
                        <tr class="table-light fw-bold border-top border-dark">
                            <td colspan="3" class="text-center text-dark fw-bold">Total: </td>
                            <td class="number-cell text-info">${formatRupiah(totalLimbah)}</td>
                    `;
                    standards.forEach(std => {
                        trTotalPemakaian +=
                            `<td class="number-cell">${formatRupiah(totalChemicalsQty[std.chemical_name])}</td>`;
                    });
                    trTotalPemakaian += `<td class="text-center">-</td></tr>`;

                    let trTotalBiaya = `
                        <tr class="table-light fw-bold border-top border-dark">
                            <td colspan="3" class="text-center text-dark fw-bold">Total:</td>
                    `;
                    standards.forEach(std => {
                        trTotalBiaya +=
                            `<td class="number-cell">${formatRupiah(totalChemicalsCost[std.chemical_name])}</td>`;
                    });
                    trTotalBiaya += `
                            <td class="number-cell table-info text-primary">${formatRupiah(totalCost)}</td>
                            <td class="text-center">-</td>
                        </tr>
                    `;

                    let trTotalBiayaM3 = `
                        <tr class="table-light fw-bold border-top border-dark">
                            <td colspan="3" class="text-center text-dark fw-bold">Total:</td>
                            <td class="number-cell table-success text-success">${formatRupiah(totalCostM3)}</td>
                    `;
                    standards.forEach(std => {
                        trTotalBiayaM3 +=
                            `<td class="number-cell">${formatRupiah(totalChemicalsCostM3[std.chemical_name])}</td>`;
                    });
                    trTotalBiayaM3 += `<td class="text-center">-</td></tr>`;

                    tbPemakaian += trTotalPemakaian;
                    tbBiaya += trTotalBiaya;
                    tbBiayaM3 += trTotalBiayaM3;
                }

                tbPemakaian += '</tbody>';
                tbBiaya += '</tbody>';
                tbBiayaM3 += '</tbody>';

                $('#tablePemakaian').html(thPemakaian + tbPemakaian);
                $('#tableBiaya').html(thBiaya + tbBiaya);
                $('#tableBiayaM3').html(thBiayaM3 + tbBiayaM3);
            }

            // Pagination Rendering
            function renderPagination(response) {
                $('#paginationInfo').text(
                    `Menampilkan ${response.from || 0} sampai ${response.to || 0} dari ${response.total} data`);

                let paginationHtml = '';

                // Previous button
                if (response.prev_page_url) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page - 1}"><i class="mdi mdi-chevron-left"></i></a></li>`;
                } else {
                    paginationHtml +=
                        `<li class="page-item disabled"><span class="page-link"><i class="mdi mdi-chevron-left"></i></span></li>`;
                }

                // Page numbers
                for (let i = 1; i <= response.last_page; i++) {
                    if (i === response.current_page) {
                        paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                    } else {
                        paginationHtml +=
                            `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                    }
                }

                // Next button
                if (response.next_page_url) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page + 1}"><i class="mdi mdi-chevron-right"></i></a></li>`;
                } else {
                    paginationHtml +=
                        `<li class="page-item disabled"><span class="page-link"><i class="mdi mdi-chevron-right"></i></span></li>`;
                }

                $('#paginationList').html(paginationHtml);
            }

            // Pagination click handler
            $(document).on('click', '#paginationList .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    loadData(page);
                }
            });

            // Filters and Search listeners
            $('#filterTahun, #filterBulan').on('change', function() {
                loadData(1);
            });

            let searchTimer;
            $('#searchData').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    loadData(1);
                }, 300);
            });

            // Reset handler
            $('#btnReset').on('click', function() {
                $('#filterTahun').val('');
                $('#filterBulan').val('');
                $('#searchData').val('');
                loadData(1);
            });

            // Show Edit Modal
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const item = currentDataList.find(x => x.id == id);
                if (!item) return;

                const dateObj = new Date(item.tanggal);
                const tahun = dateObj.getFullYear();
                const bulanText = indonesianMonths[dateObj.getMonth() + 1];

                $('#recordId').val(item.id);
                $('#editTahun').text(tahun);
                $('#editBulan').text(bulanText);
                $('#edit_limbah').val(item.limbah_di_olah);

                // Build chemical inputs dynamically
                let inputsHtml = '';
                activeStandards.forEach(std => {
                    const chem = item.chemicals[std.chemical_name] || {
                        qty: 0
                    };
                    inputsHtml += `
                        <div class="col-md-6 mb-3">
                            <label for="edit_qty_${std.id}" class="form-label fw-semibold">${std.chemical_name}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="edit_qty_${std.id}" name="qty[${std.id}]" value="${chem.qty}" required min="0">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                    `;
                });
                $('#editChemicalInputsContainer').html(inputsHtml);

                $('#modalEdit').modal('show');
            });

            // Submit Edit Form
            $('#formEdit').on('submit', function(e) {
                e.preventDefault();
                const id = $('#recordId').val();
                const btn = $('#btnUpdateRecord');
                const originalText = btn.html();

                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: `{{ url('wwtp/biaya-chemical') }}/${id}`,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#modalEdit').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadData(currentPage);
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let msg = 'Gagal menyimpan data!';
                        if (error && error.errors) {
                            msg = Object.values(error.errors).flat().join('<br>');
                        } else if (error && error.message) {
                            msg = error.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: msg
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Delete Record
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const item = currentDataList.find(x => x.id == id);
                if (!item) return;

                const dateObj = new Date(item.tanggal);
                const bulanText = indonesianMonths[dateObj.getMonth() + 1];
                const tahun = dateObj.getFullYear();

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Data biaya chemical untuk bulan ${bulanText} ${tahun} akan dihapus secara permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('wwtp/biaya-chemical') }}/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadData(currentPage);
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus data biaya chemical.'
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
