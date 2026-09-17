@extends('layouts.app')

@section('title', 'Data Check Mtc P2H')

@section('styles')
    <style>
        .card-soft {
            border: 1px solid #eee;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .small-muted {
            color: #6c757d;
            font-size: .85rem;
        }

        .group-title {
            font-weight: 700;
            color: #0d6efd;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin: 14px 0 10px;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 20px;
        }

        @media (max-width: 992px) {
            .items-grid {
                grid-template-columns: 1fr;
            }
        }

        .item-cell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 6px 0;
            border-bottom: 1px dashed #eee;
        }

        .item-cell:last-child {
            border-bottom: none;
        }

        .item-cell .item-label {
            font-size: .95rem;
            line-height: 1.4;
        }

        .checklist-edit-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="card card-soft shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-1">Data Maintenance P2H</h4>
                        <div class="small-muted">Data pemeriksaan Forklift & Pallet Mover yang disinkronkan dari sistem
                            Warehouse & Production</div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ri-refresh-line me-1"></i> Sync Data P2H
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item py-2" href="javascript:void(0)" id="btnSyncAll"><i
                                            class="ri-refresh-line text-primary me-2"></i><strong>Sync Semua</strong>
                                        (Warehouse & Production)</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item py-2" href="javascript:void(0)" id="btnSyncWarehouse"><i
                                            class="ri-store-2-line text-success me-2"></i>Sync Data
                                        <strong>Warehouse</strong></a></li>
                                <li><a class="dropdown-item py-2" href="javascript:void(0)" id="btnSyncProduction"><i
                                            class="ri-settings-4-line text-warning me-2"></i>Sync Data
                                        <strong>Production</strong></a></li>
                            </ul>
                        </div>
                        <a href="{{ route('p2h.form.index') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Input Genset P2H
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- FILTER --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="filterDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nama Unit / Mesin</label>
                            <input type="text" class="form-control" id="filterNoUnit"
                                placeholder="Contoh: F16, PM01, Forklift">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Jenis P2H</label>
                            <select class="form-select" id="filterJenisP2h">
                                <option value="">Semua Jenis</option>
                                <option value="Forklift">Forklift</option>
                                <option value="Pallet Mover">Pallet Mover</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Departemen</label>
                            <input type="text" class="form-control" id="filterDepartemen" placeholder="Warehouse">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-block">&nbsp;</label>
                            <div class="d-flex gap-2 text-nowrap">
                                <button type="button" class="btn btn-outline-primary w-100" id="btnApply">
                                    <i class="mdi mdi-filter me-1"></i> Terapkan</button>
                                <button type="button" class="btn btn-outline-secondary w-100" id="btnReset">
                                    <i class="mdi mdi-restart me-1"></i> Reset</button>
                            </div>
                        </div>
                    </div>

                    {{-- TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tabelP2h" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>No Unit</th>
                                    <th>Master Mesin</th>
                                    <th>Dept</th>
                                    <th class="text-center">Shift</th>
                                    <th>Operator</th>
                                    <th>Hours Meter</th>
                                    <th>Score (%)</th>
                                    <th>Kelayakan</th>
                                    <th style="width:120px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyP2h">
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div id="paginationInfo" class="small text-muted"></div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="paginationList"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="fw-bold fs-5" id="detailTitle">Detail Inspeksi P2H</div>
                        <div class="small-muted" id="detailSub"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailBody">
                    {{-- Injected dynamically --}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="formEditP2h">
                    @csrf
                    <input type="hidden" id="editId" name="id">

                    <div class="modal-header">
                        <div>
                            <div class="fw-bold fs-5">Edit Data P2H</div>
                            <div class="text-muted small" id="editSub"></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal *</label>
                                <input type="date" class="form-control" name="tanggal" id="editTanggal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Departemen</label>
                                <input type="text" class="form-control" name="departemen" id="editDepartemen">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No Unit / Code</label>
                                <input type="text" class="form-control" name="nomor_unit" id="editNomorUnit"
                                    readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Shift *</label>
                                <select name="shift" class="form-select" id="editShift" required>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hours Meter (Jam Operasional)</label>
                                <input type="number" step="any" class="form-control" name="hours_meter"
                                    id="editHourMeter">
                            </div>
                            <div class="col-md-9">
                                <label class="form-label">Catatan</label>
                                <input type="text" class="form-control" name="catatan" id="editCatatan">
                            </div>
                        </div>

                        <div class="card shadow-sm mt-3 mb-3">
                            <div class="card-header bg-light">
                                <h6 class="fw-bold mb-0">Checklist Pemeriksaan Warehouse</h6>
                            </div>
                            <div class="card-body" id="editChecklistContainer">
                                {{-- Dynamically populated based on available checklist --}}
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveEdit">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const API_URL = "{{ url('api/mtc/p2h/get-data') }}";
            const SYNC_URL = "{{ route('p2h.sync.warehouse') }}";
            const SYNC_PRODUCTION_URL = "{{ route('p2h.sync.production') }}";
            const SYNC_ALL_URL = "{{ route('p2h.sync.all') }}";
            const UPDATE_URL = "{{ url('mtc/p2h/data/update') }}";
            const DELETE_URL = "{{ url('mtc/p2h/data/delete') }}";

            let currentRows = [];

            // Master metadata checklist sesuai API Warehouse & Production
            const checklistDict = {
                cek_baterai: {
                    label: 'Baterai / Battery',
                    standar: 'Kondisi baik, daya >= 30%'
                },
                air_aki: {
                    label: 'Air Aki / Level Accu',
                    standar: 'Berada di level standar/normal'
                },
                cek_fork: {
                    label: 'Cek Fork',
                    standar: 'Tidak bengkok, tidak retak/patah'
                },
                kondisi_body_kebersihan: {
                    label: 'Body Unit & Kebersihan',
                    standar: 'Bersih, tidak lecet/penyok parah'
                },
                rantai_lift: {
                    label: 'Rantai Lift',
                    standar: 'Kekencangan seimbang, terlubrikasi'
                },
                sistem_hidrolik: {
                    label: 'Sistem Hidrolik',
                    standar: 'Berfungsi normal, tidak ada kebocoran'
                },
                sistem_kemudi: {
                    label: 'Sistem Kemudi',
                    standar: 'Tidak berat, bergerak lancar'
                },
                panel_display: {
                    label: 'Panel Display & Indikator',
                    standar: 'Berfungsi normal, tidak ada alarm error'
                },
                klakson: {
                    label: 'Klakson',
                    standar: 'Berbunyi nyaring saat ditekan'
                },
                buzzer_mundur: {
                    label: 'Buzzer Mundur',
                    standar: 'Berbunyi saat unit mundur'
                },
                kaca_spion: {
                    label: 'Kaca Spion',
                    standar: 'Terpasang lengkap, tidak pecah'
                },
                kondisi_ban: {
                    label: 'Kondisi Ban / Roda',
                    standar: 'Layak pakai, tidak aus berlebih'
                },
                fungsi_rem: {
                    label: 'Fungsi Rem',
                    standar: 'Berfungsi baik, tidak blong'
                },
                lampu_kiri: {
                    label: 'Lampu Kiri',
                    standar: 'Menyala normal'
                },
                lampu_kanan: {
                    label: 'Lampu Kanan',
                    standar: 'Menyala normal'
                },
                lampu_sorot: {
                    label: 'Lampu Sorot',
                    standar: 'Menyala normal, tidak pecah'
                },
                lampu_sign_depan_kanan: {
                    label: 'Sign Depan Kanan',
                    standar: 'Menyala berkedip'
                },
                lampu_sign_depan_kiri: {
                    label: 'Sign Depan Kiri',
                    standar: 'Menyala berkedip'
                },
                kipas_belakang: {
                    label: 'Kipas Belakang',
                    standar: 'Berputar normal'
                },
                kondisi_axle: {
                    label: 'Kondisi Axle',
                    standar: 'Normal, tidak goyang'
                },
                check_kunci_pm: {
                    label: 'Kunci Pallet Mover',
                    standar: 'Berfungsi baik dan lengkap'
                },
                check_kebersihan_unit: {
                    label: 'Kebersihan Unit PM',
                    standar: 'Bersih dari kotoran'
                }
            };

            function fmtDate(iso) {
                if (!iso) return '-';
                const d = new Date(iso);
                if (isNaN(d.getTime())) return iso;
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function statusBadge(val) {
                if (val === true || val === 1 || val === "1") return `<span class="badge bg-success">OK</span>`;
                if (val === false || val === 0 || val === "0") return `<span class="badge bg-danger">NOK</span>`;
                return `<span class="badge bg-secondary">Tidak Diisi</span>`;
            }

            function kelayakanBadge(status) {
                if (!status) return '-';
                let s = status.toLowerCase();
                if (s === 'layak') {
                    return `<span class="badge bg-success">${status}</span>`;
                } else if (s.includes('perhatian')) {
                    return `<span class="badge bg-warning text-dark">${status}</span>`;
                } else {
                    return `<span class="badge bg-danger">${status}</span>`;
                }
            }

            function buildDetailHTML(row) {
                const mesinInfo = row.mesin ? `${row.mesin.nama_mesin} (${row.mesin.kode_mesin || '-'})` :
                    'Belum Terhubung ke Master';
                const scoreDisplay = row.persentase !== null ? `<b>${row.persentase}%</b>` : '-';
                const jamOp = row.jam_operasional !== null ? `${row.jam_operasional}` : '-';
                const syncSource = row.source || (row.warehouse_id ? 'Warehouse' : (row.production_id ?
                    'Production' : '-'));
                const syncIdVal = row.warehouse_id ? `WH #${row.warehouse_id}` : (row.production_id ?
                    `PRD #${row.production_id}` : `#${row.id}`);
                const sourceBadge = syncSource === 'Production' ?
                    `<span class="badge bg-warning text-dark">Production</span>` : (syncSource === 'Warehouse' ?
                        `<span class="badge bg-info">Warehouse</span>` :
                        `<span class="badge bg-secondary">${syncSource}</span>`);

                let cells = '';
                let countItems = 0;

                Object.keys(checklistDict).forEach(k => {
                    if (row[k] !== undefined && row[k] !== null) {
                        countItems++;
                        const meta = checklistDict[k];
                        cells += `
                            <div class="item-cell">
                                <div class="item-info">
                                    <div class="item-label fw-semibold">${meta.label}</div>
                                    <div class="text-muted small">${meta.standar}</div>
                                </div>
                                <div class="item-status">${statusBadge(row[k])}</div>
                            </div>
                        `;
                    }
                });

                let fotoAccuHtml = '';
                if (row.foto_kondisi_accu) {
                    fotoAccuHtml = `
                        <div class="col-md-12 mt-2">
                            <div class="fw-semibold text-muted small mb-1">Foto Kondisi Accu:</div>
                            <div>
                                <a href="${row.foto_kondisi_accu}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="ri-image-line me-1"></i> Lihat Foto Accu
                                </a>
                            </div>
                        </div>
                    `;
                }

                return `
                    <div class="row g-3 p-3 bg-light rounded mb-3">
                        <div class="col-md-3">
                            <div class="text-muted small">Nomor Unit</div>
                            <div class="fw-bold fs-6 text-primary">${row.nomor_unit || '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Jenis P2H</div>
                            <div class="fw-bold">${row.jenis_p2h || '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Departemen</div>
                            <div class="fw-bold">${row.dept || '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Tanggal / Shift</div>
                            <div class="fw-bold">${fmtDate(row.tanggal)} (Shift ${row.shift || '-'})</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted small">Koneksi Master Mesin</div>
                            <div class="fw-bold text-dark">${mesinInfo}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Operator</div>
                            <div class="fw-bold">${row.operator_name || '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Jam Operasional (HM)</div>
                            <div class="fw-bold">${jamOp}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Skor Kelayakan</div>
                            <div class="fw-bold fs-6 text-success">${scoreDisplay}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Status Kelayakan</div>
                            <div>${kelayakanBadge(row.status_kelayakan)}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Sumber & ID Sync</div>
                            <div>${sourceBadge} <span class="badge bg-secondary">${syncIdVal}</span></div>
                        </div>
                        ${fotoAccuHtml}
                    </div>

                    <div class="mb-3">
                        <div class="group-title mb-2">Hasil Checklist (${countItems} Item)</div>
                        <div class="items-grid">${cells || '<div class="text-muted">Tidak ada item checklist yang terisi.</div>'}</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="group-title">Catatan</div>
                            <div class="border p-2 rounded bg-light">${row.catatan || '-'}</div>
                        </div>
                    </div>
                `;
            }

            let currentPage = 1;
            const pageSize = 10;
            let totalRecords = 0;

            function loadTableData(page = 1) {
                currentPage = page;
                const start = (currentPage - 1) * pageSize;

                $('#tbodyP2h').html(`
                    <tr>
                        <td colspan="12" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2 text-muted">Memuat data...</span>
                        </td>
                    </tr>
                `);

                const filters = {
                    date: $('#filterDate').val() || null,
                    no_unit: $('#filterNoUnit').val() || null,
                    jenis_p2h: $('#filterJenisP2h').val() || null,
                    departemen: $('#filterDepartemen').val() || null,
                    start: start,
                    length: pageSize
                };

                $.ajax({
                    url: API_URL,
                    type: 'GET',
                    data: filters,
                    dataType: 'json',
                    success: function(res) {
                        currentRows = res.data || [];
                        totalRecords = res.recordsFiltered || 0;

                        if (currentRows.length === 0) {
                            $('#tbodyP2h').html(`
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">Tidak ada data ditemukan</td>
                                </tr>
                            `);
                            $('#paginationInfo').text('Menampilkan 0 sampai 0 dari 0 data');
                            $('#paginationList').empty();
                            return;
                        }

                        let html = '';
                        currentRows.forEach((row, index) => {
                            const rowNum = start + index + 1;
                            const machineName = row.mesin ? `${row.mesin.nama_mesin}` :
                                `<span class="text-muted fst-italic">Belum cocok</span>`;
                            const shiftVal = row.shift ? `Shift ${row.shift}` : '-';
                            const hoursVal = row.jam_operasional !== null ? row
                                .jam_operasional : '-';
                            const percentageVal = row.persentase !== null ?
                                `<b>${row.persentase}%</b>` : '-';

                            let typeBadge = '';
                            if (row.jenis_p2h === 'Forklift') {
                                typeBadge =
                                    `<span class="badge bg-soft-primary text-primary">Forklift</span>`;
                            } else if (row.jenis_p2h === 'Pallet Mover') {
                                typeBadge =
                                    `<span class="badge bg-soft-info text-info">Pallet Mover</span>`;
                            } else {
                                typeBadge =
                                    `<span class="badge bg-soft-secondary text-secondary">${row.jenis_p2h || 'P2H'}</span>`;
                            }

                            let sourceTag = '';
                            if (row.source === 'Production' || (!row.source && row
                                    .production_id)) {
                                sourceTag =
                                    `<span class="badge bg-soft-warning text-dark border border-warning" style="font-size:0.68rem;">Production</span>`;
                            } else if (row.source === 'Warehouse' || (!row.source && row
                                    .warehouse_id)) {
                                sourceTag =
                                    `<span class="badge bg-soft-info text-info border border-info" style="font-size:0.68rem;">Warehouse</span>`;
                            }

                            const deptDisplay =
                                `<div>${row.dept || '-'}</div>${sourceTag ? '<div class="mt-1">' + sourceTag + '</div>' : ''}`;

                            const showBtn =
                                `<button class="btn btn-sm btn-info btn-detail me-1" data-id="${row.id}" title="Lihat Detail"><i class="mdi mdi-eye"></i></button>`;
                            const editBtn =
                                `<button class="btn btn-sm btn-primary btn-edit me-1" data-id="${row.id}" title="Edit"><i class="mdi mdi-pencil"></i></button>`;
                            const delBtn =
                                `<button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" title="Hapus"><i class="mdi mdi-trash-can"></i></button>`;

                            html += `
                                <tr>
                                    <td class="text-center">${rowNum}</td>
                                    <td>${fmtDate(row.tanggal)}</td>
                                    <td>${typeBadge}</td>
                                    <td><span class="fw-semibold text-primary">${row.nomor_unit || '-'}</span></td>
                                    <td>${machineName}</td>
                                    <td>${deptDisplay}</td>
                                    <td class="text-center">${shiftVal}</td>
                                    <td>${row.operator_name || '-'}</td>
                                    <td>${hoursVal}</td>
                                    <td>${percentageVal}</td>
                                    <td>${kelayakanBadge(row.status_kelayakan)}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            ${showBtn}
                                            ${editBtn}
                                            ${delBtn}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#tbodyP2h').html(html);

                        // Pagination
                        const totalPages = Math.ceil(totalRecords / pageSize);
                        const endRow = Math.min(start + pageSize, totalRecords);
                        $('#paginationInfo').text(
                            `Menampilkan ${start + 1} sampai ${endRow} dari ${totalRecords} data`);

                        let pagHtml = '';
                        pagHtml += `
                            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                <a class="page-link" href="#" data-page="${currentPage - 1}">Sebelumnya</a>
                            </li>
                        `;

                        const pages = [];
                        if (totalPages <= 7) {
                            for (let i = 1; i <= totalPages; i++) pages.push(i);
                        } else {
                            pages.push(1);
                            let pStart = Math.max(2, currentPage - 1);
                            let pEnd = Math.min(totalPages - 1, currentPage + 1);

                            if (currentPage <= 3) {
                                pStart = 2;
                                pEnd = 4;
                            } else if (currentPage >= totalPages - 2) {
                                pStart = totalPages - 3;
                                pEnd = totalPages - 1;
                            }

                            if (pStart > 2) {
                                if (pStart === 3) {
                                    pages.push(2);
                                } else {
                                    pages.push('...');
                                }
                            }

                            for (let i = pStart; i <= pEnd; i++) {
                                pages.push(i);
                            }

                            if (pEnd < totalPages - 1) {
                                if (pEnd === totalPages - 2) {
                                    pages.push(totalPages - 1);
                                } else {
                                    pages.push('...');
                                }
                            }

                            pages.push(totalPages);
                        }

                        pages.forEach(p => {
                            if (p === '...') {
                                pagHtml += `
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                `;
                            } else {
                                pagHtml += `
                                    <li class="page-item ${currentPage === p ? 'active' : ''}">
                                        <a class="page-link" href="#" data-page="${p}">${p}</a>
                                    </li>
                                `;
                            }
                        });

                        pagHtml += `
                            <li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}">
                                <a class="page-link" href="#" data-page="${currentPage + 1}">Berikutnya</a>
                            </li>
                        `;
                        $('#paginationList').html(pagHtml);
                    },
                    error: function() {
                        $('#tbodyP2h').html(`
                            <tr>
                                <td colspan="12" class="text-center text-danger py-4">Gagal memuat data P2H.</td>
                            </tr>
                        `);
                    }
                });
            }

            // Inisialisasi data tabel pertama kali
            loadTableData(1);

            // Filter button events
            $('#btnApply').on('click', function() {
                loadTableData(1);
            });
            $('#btnReset').on('click', function() {
                $('#filterDate').val('');
                $('#filterNoUnit').val('');
                $('#filterJenisP2h').val('');
                $('#filterDepartemen').val('');
                loadTableData(1);
            });

            // Pagination click
            $(document).on('click', '#paginationList a', function(e) {
                e.preventDefault();
                if ($(this).parent().hasClass('disabled')) return;
                const page = parseInt($(this).data('page'));
                if (page && page !== currentPage) {
                    loadTableData(page);
                }
            });

            // Detail modal
            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                const row = currentRows.find(r => r.id == id);
                if (!row) return;

                $('#detailTitle').text(`P2H ${row.jenis_p2h} - Unit ${row.nomor_unit}`);
                $('#detailSub').text(
                `Inspeksi tanggal ${fmtDate(row.tanggal)} (Shift ${row.shift || '-'})`);
                $('#detailBody').html(buildDetailHTML(row));
                $('#modalDetail').modal('show');
            });

            // Edit modal
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const row = currentRows.find(r => r.id == id);
                if (!row) return;

                $('#editId').val(row.id);
                $('#editTanggal').val(row.tanggal ? row.tanggal.split('T')[0] : '');
                $('#editDepartemen').val(row.dept || '');
                $('#editNomorUnit').val(row.nomor_unit || '');
                $('#editShift').val(row.shift || '1');
                $('#editHourMeter').val(row.jam_operasional || '');
                $('#editCatatan').val(row.catatan || '');
                $('#editSub').text(`Unit ${row.nomor_unit} (${row.jenis_p2h})`);

                // Bangun checklist editor
                let editHtml = '<div class="row g-2">';
                Object.keys(checklistDict).forEach(k => {
                    if (row[k] !== undefined) {
                        const meta = checklistDict[k];
                        const val = row[k];
                        const isOk = val === true || val === 1 || val === "1";
                        const isNok = val === false || val === 0 || val === "0";
                        const isNull = val === null || val === undefined;

                        editHtml += `
                            <div class="col-md-6 col-12">
                                <div class="checklist-edit-row">
                                    <div class="pe-2">
                                        <div class="fw-semibold text-dark small">${meta.label}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">${meta.standar}</div>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="${k}" id="${k}_ok" value="1" ${isOk ? 'checked' : ''}>
                                        <label class="btn btn-outline-success px-2" for="${k}_ok">OK</label>

                                        <input type="radio" class="btn-check" name="${k}" id="${k}_nok" value="0" ${isNok ? 'checked' : ''}>
                                        <label class="btn btn-outline-danger px-2" for="${k}_nok">NOK</label>

                                        <input type="radio" class="btn-check" name="${k}" id="${k}_null" value="" ${isNull ? 'checked' : ''}>
                                        <label class="btn btn-outline-secondary px-2" for="${k}_null">-</label>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });
                editHtml += '</div>';

                $('#editChecklistContainer').html(editHtml);
                $('#modalEdit').modal('show');
            });

            // Submit Edit Form
            $('#formEditP2h').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editId').val();
                const formData = $(this).serialize();
                const $btn = $('#btnSaveEdit');

                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: `${UPDATE_URL}/${id}`,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Data P2H berhasil diperbarui.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#modalEdit').modal('hide');
                        loadTableData(currentPage);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat memperbarui data.'
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Perubahan');
                    }
                });
            });

            // Delete Record
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const row = currentRows.find(r => r.id == id);
                const unitName = row ? `unit ${row.nomor_unit}` : 'data ini';

                Swal.fire({
                    title: 'Hapus data P2H?',
                    text: `Apakah Anda yakin ingin menghapus pemeriksaan ${unitName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((res) => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: `${DELETE_URL}/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            success: function(r) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus',
                                    text: r.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadTableData(currentPage);
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Gagal menghapus data P2H.'
                                });
                            }
                        });
                    }
                });
            });

            // Helper generic function for sync trigger
            function triggerSync(url, title, text, loadingText) {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ri-refresh-line me-1"></i> Ya, Sync Sekarang',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyinkronkan Data...',
                            text: loadingText,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sync Berhasil',
                                    text: res.message,
                                    confirmButtonText: 'Tutup'
                                });
                                loadTableData(1);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Sync Gagal',
                                    text: xhr.responseJSON?.message ||
                                        'Tidak dapat menghubungi server API. Pastikan server aktif di host/port yang sesuai.'
                                });
                            }
                        });
                    }
                });
            }

            // Sync Data Warehouse
            $('#btnSyncWarehouse').on('click', function() {
                triggerSync(
                    SYNC_URL,
                    'Sync Data Warehouse?',
                    'Data inspeksi P2H Forklift dan Pallet Mover akan ditarik dari sistem Warehouse.',
                    'Sedang menghubungi API Warehouse & menyinkronkan data P2H...'
                );
            });

            // Sync Data Production
            $('#btnSyncProduction').on('click', function() {
                triggerSync(
                    SYNC_PRODUCTION_URL,
                    'Sync Data Production?',
                    'Data inspeksi P2H Forklift dan Pallet Mover akan ditarik dari sistem Production.',
                    'Sedang menghubungi API Production & menyinkronkan data P2H...'
                );
            });

            // Sync All Data (Warehouse & Production)
            $('#btnSyncAll').on('click', function() {
                triggerSync(
                    SYNC_ALL_URL,
                    'Sync Semua Data (Warehouse & Production)?',
                    'Data inspeksi P2H Forklift dan Pallet Mover akan ditarik dari sistem Warehouse & Production.',
                    'Sedang menyinkronkan seluruh data P2H dari Warehouse & Production...'
                );
            });
        });
    </script>
@endsection
