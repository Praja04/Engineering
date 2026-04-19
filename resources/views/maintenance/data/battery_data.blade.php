@extends('layouts.app')

@section('styles')
    <style>
        .status-ok {
            color: #28a745;
            font-weight: bold;
        }

        .status-ng {
            color: #dc3545;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        #detailModal .modal-dialog {
            max-width: 90%;
        }

        /* BORDER MERAH SAAT NG */
        .check-wrapper.ng-active {
            border-left: 4px solid #dc3545;
            padding-left: 8px;
            border-radius: 4px;
        }

        /* SELECT NG */
        .check-wrapper.ng-active select {
            border-color: #dc3545;
        }

        /* INPUT KETERANGAN NG */
        .check-wrapper.ng-active .keterangan-input {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        /* OPTIONAL: efek halus */
        .check-wrapper {
            transition: all 0.2s ease;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card card-soft shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fw-bold">Data Maintenance Battery</h4>
                        <div class="small-muted">List inspeksi + detail hasil pengecekan</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ url('/mtc/form/battery/index') }}" class="btn btn-primary">
                            + Input Baru
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- FILTER --}}
                    <div class="row g-3 mb-4">
                        {{-- <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="filterStart">
                        </div> --}}
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="filterDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipe baterai</label>
                            <input class="form-control" id="filterTipeBaterai">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cari no unit atau no seri</label>
                            <input type="text" class="form-control" id="filterUnit">

                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <div class="d-flex gap-2 text-nowrap">
                                {{-- <button type="button" class="btn btn-outline-primary w-100" id="btnApply">
                                    <i class="mdi mdi-filter me-2"></i> Terapkan</button> --}}
                                <button type="button" class="btn btn-outline-secondary w-100" id="btnReset">
                                    <i class="mdi mdi-restart"></i> Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="batteryTable" class="table table-bordered table-hover table-striped">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Waktu Mulai</th>
                                        <th>Waktu Selesai</th>

                                        <th>Battery Type</th>
                                        <th>No Unit</th>
                                        <th>No Seri</th>
                                        <th>status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Inspeksi -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Inspeksi Battery</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Tanggal:</strong> <span id="modalTanggal"></span></div>
                        <div class="col-md-4"><strong>Waktu Mulai:</strong> <span id="modalWaktuMulai"></span></div>
                        <div class="col-md-4"><strong>Waktu Selesai:</strong> <span id="modalWaktuSelesai"></span></div>
                        <div class="col-md-4"><strong>Dibuat oleh:</strong> <span id="modalUser"></span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Battery Type:</strong> <span id="modalType"></span></div>
                        <div class="col-md-4"><strong>No Unit:</strong> <span id="modalUnit"></span></div>
                        <div class="col-md-4"><strong>No Seri:</strong> <span id="modalSeri"></span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12"><strong>Catatan:</strong> <span id="modalCatatan"></span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12"><strong>Keterangan NOK:</strong> <span id="modalKeterangan"></span></div>
                    </div>

                    <hr>
                    <h6>Hasil Inspeksi Cell</h6>
                    <table class="table table-bordered detail-table">
                        <thead class="table-light text-nowrap">
                            <tr>
                                <th>Cell</th>
                                <th>Voltase</th>
                                <th>Level Air Aki</th>
                                <th>Intercell</th>
                                <th>Kondisi Skun</th>
                                <th>Kondisi Unit</th>
                                <th>Grounding</th>
                            </tr>
                        </thead>
                        <tbody id="modalDetailBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="editFormBattery">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data Battery #<span id="editBatteryId"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="editId">
                        <input type="hidden" name="keterangan" id="editKeterangan">

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" id="editTanggal" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" id="editWaktuMulai" class="form-control" step="60"
                                    value="{{ old('waktu_mulai', now()->format('H:i')) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label>Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" id="editWaktuSelesai" class="form-control" step="60"
                                    value="{{ old('waktu_selesai', now()->format('H:i')) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label>Battery Type</label>
                                <input type="text" name="battery_type" id="editBatteryType" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>No Unit</label>
                                <input type="text" name="no_unit" id="editNoUnit" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>No Seri</label>
                                <input type="text" name="no_seri" id="editNoSeri" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Catatan</label>
                                <textarea class="form-control" name="catatan" id="editCatatan" rows="3"></textarea>
                                {{-- <input type="text" name="catatan" id="editCatatan" class="form-control"> --}}
                            </div>
                        </div>

                        <hr>
                        <h6>Detail Cell</h6>
                        <div id="editDetailsContainer" class="row g-3"></div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddCell">
                            + Add Cell
                        </button>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveEdit">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="spinEdit"></span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tracking --}}
    <div class="modal fade" id="modalTracking" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="fw-bold" id="trackingTitle">Tracking Appoval</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="trackingBody">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const API_URL = "{{ url('api/mtc/battery/get-data') }}";
            const DELETE_URL = "{{ url('mtc/main/delete') }}";
            const UPDATE_URL = "{{ url('mtc/data/battery/update') }}";

            // Inisialisasi DataTable
            const table = $('#batteryTable').DataTable({
                ajax: {
                    url: API_URL,
                    data: function(d) {
                        d.date = $('#filterDate').val() || null;
                        d.tipe_baterai = $('#filterTipeBaterai').val() || null;
                        d.unit = $('#filterUnit').val() || null;
                    },
                    dataSrc: function(json) {
                        if (json.status && json.data) return json.data;
                        return [];
                    }
                },
                columns: [{
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: 'tanggal',
                        className: 'text-start',
                        orderable: false,
                        render: function(data, type) {
                            if (type === 'display') return fmtDate(data);
                            return data; // sorting pakai ISO
                        },
                    },
                    {
                        data: 'waktu_mulai',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'waktu_selesai',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'battery.battery_type',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'battery.no_unit',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'battery.no_seri',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: null,
                        render: function(row) {
                            return `
                                <span class="badge cursor-pointer badge-status"
                                    data-id="${row.id}">
                                    ${statusBadge(row.status)}
                                </span>
                            `;
                        }

                    },
                    {
                        data: null,
                        className: 'text-center text-nowrap',
                        render: function(data) {
                            return `
                                    <button class="btn btn-sm btn-primary btnDetail" data-id="${data.id}" title="Detail"><i class="mdi mdi-eye"></i></button>
                                    <button class="btn btn-sm btn-info btnEdit" data-id="${data.id}" title="Edit"><i class="mdi mdi-pencil"></i></button>
                                    <button class="btn btn-sm btn-danger btnDelete" data-id="${data.id}" title="Hapus"><i class="mdi mdi-delete"></i></button>
                                    <button class="btn btn-sm btn-warning btn-print" data-id="${data.id}" title="Download"><i class="mdi mdi-download"></i></button>
                                `;
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                searching: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                order: [
                    [0, 'asc']
                ],
            });

            function statusBadge(val) {
                if (val === true || val === 1 || val === "1") return `<span class="badge bg-success">OK</span>`;
                if (val === false || val === 0 || val === "0") return `<span class="badge bg-danger">No OK</span>`;

                if (val === 'pending') {
                    return `<span class="badge bg-warning">${val}</span>`
                } else if (val === 'waiting') {
                    return `<span class="badge bg-info">${val}</span>`
                } else if (val === 'approved') {
                    return `<span class="badge bg-success">${val}</span>`
                } else if (val === 'rejected') {
                    return `<span class="badge bg-danger">${val}</span>`
                };

                return `<span class="badge bg-secondary">No Check</span>`;
            }

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

            function fmtDateTime(dateStr) {
                const d = new Date(dateStr);
                return d.toLocaleString('id-ID', {
                    dateStyle: 'medium',
                    timeStyle: 'short'
                });
            }

            function openTracking(id) {

                $('#trackingTitle').text('Tracking Approval');
                $('#trackingBody').html('<div class="text-center py-4">Memuat...</div>');
                $('#modalTracking').modal('show');

                $.get(`/mtc/main/tracking/${id}`)
                    .done(res => {

                        if (!res.status || !res.data.length) {
                            $('#trackingBody').html(
                                '<div class="text-muted text-center">Belum ada approval</div>');
                            return;
                        }

                        const statusMap = {
                            approved: 'success',
                            rejected: 'danger',
                            pending: 'secondary',
                            read: 'info'
                        };

                        let html = `<ul class="list-group list-group-flush">`;

                        res.data.forEach(item => {
                            html += `
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <div class="fw-bold">
                                            ${item.role}
                                        </div>
                                        <div class="text-muted small">
                                            Approver: ${item.approver}
                                        </div>
                                        ${item.catatan ? `<div class="fst-italic small mt-1">${item.catatan}</div>` : ''}
                                    </div>

                                    <div class="text-end">
                                        <span class="badge bg-${statusMap[item.status] || 'secondary'}">
                                            ${item.status.toUpperCase()}
                                        </span>
                                        <div class="small text-muted mt-1">
                                            ${item.action_at ? fmtDateTime(item.action_at) : '-'}
                                        </div>
                                    </div>
                                </li>
                            `;
                        });

                        html += `</ul>`;
                        $('#trackingBody').html(html);
                    })
                    .fail(() => {
                        $('#trackingBody').html('<div class="text-danger text-center">Terjadi kesalahan</div>');
                    });
            }

            $(document).on('click', '.badge-status', function() {
                const id = $(this).data('id');
                openTracking(id);
            });

            // Event tombol Detail
            $('#batteryTable tbody').on('click', '.btnDetail', function() {
                const id = $(this).data('id');
                const rowData = table.row($(this).parents('tr')).data();

                // Data utama dari row (yang sudah di-flatten atau diambil dari API response)
                $('#modalBatteryId').text(rowData.id);
                $('#modalTanggal').text(rowData.tanggal);
                $('#modalWaktuMulai').text(rowData.waktu_mulai || '-');
                $('#modalWaktuSelesai').text(rowData.waktu_selesai || '-');


                // Ambil dari object battery
                const battery = rowData.battery || {};
                $('#modalType').text(battery.battery_type || '-');
                $('#modalUnit').text(battery.no_unit || '-');
                $('#modalSeri').text(battery.no_seri || '-');

                // Keterangan ada di level utama (bukan di battery)
                $('#modalKeterangan').text(rowData.keterangan || '-');
                $('#modalCatatan').text(battery.catatan || '-');

                // User yang create
                $('#modalUser').text(rowData.created_by?.username || 'Unknown');

                // Bagian detail cells
                let detailHtml = '';

                if (battery.details && Array.isArray(battery.details) && battery.details.length > 0) {
                    // Sort berdasarkan nomor cell (aman kalau urutannya dari DB sudah benar, tapi sort tetap lebih aman)
                    const sortedDetails = [...battery.details].sort((a, b) => a.cell - b.cell);

                    sortedDetails.forEach(detail => {
                        // Helper untuk menentukan status OK / Tidak OK + class
                        const getStatus = (val) => {
                            // API mengirim true/false untuk beberapa field, string voltase/grounding
                            if (val === true || val === 1 || val === '1') {
                                return {
                                    text: 'OK',
                                    class: 'status-ok'
                                };
                            } else if (val === false || val === 0 || val === '0') {
                                return {
                                    text: 'Tidak OK',
                                    class: 'status-ng'
                                };
                            } else {
                                // Untuk voltase & grounding yang berupa string angka
                                return {
                                    text: val || '-',
                                    class: ''
                                };
                            }
                        };

                        const voltaseStatus = getStatus(detail.voltase);
                        const airAkiStatus = getStatus(detail.level_air_aki);
                        const intercellStatus = getStatus(detail.intercell);
                        const skunStatus = getStatus(detail.kondisi_skun);
                        const unitStatus = getStatus(detail.kondisi_unit);
                        const groundingStatus = getStatus(detail.grounding);

                        detailHtml += `
                            <tr>
                                <td>${detail.cell || '-'}</td>
                                <td class="${voltaseStatus.class}">${voltaseStatus.text}</td>
                                <td class="${airAkiStatus.class}">${airAkiStatus.text}</td>
                                <td class="${intercellStatus.class}">${intercellStatus.text}</td>
                                <td class="${skunStatus.class}">${skunStatus.text}</td>
                                <td class="${unitStatus.class}">${unitStatus.text}</td>
                                <td class="${groundingStatus.class}">${groundingStatus.text}</td>
                            </tr>
                        `;
                    });
                } else {
                    detailHtml =
                        '<tr><td colspan="7" class="text-center">Tidak ada data detail cell</td></tr>';
                }

                $('#modalDetailBody').html(detailHtml);
                $('#detailModal').modal('show');
            });

            // Event tombol Edit
            $('#batteryTable tbody').on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                editBattery(id);
            });

            function parseKeteranganPairs(text) {
                const map = {};
                if (!text) return map;

                text.split('|').forEach(part => {
                    part = part.trim();
                    if (!part) return;

                    const idx = part.indexOf(':');
                    if (idx === -1) return;

                    const key = part.slice(0, idx).trim();
                    const value = part.slice(idx + 1).trim();

                    map[key] = value;
                });

                return map;
            }

            function buildKeteranganString(map) {
                return Object.entries(map)
                    .map(([k, v]) => `${k}: ${v}`)
                    .join(', ');
            }

            function editBattery(id) {
                const row = table.row($(`tr:has(.btnEdit[data-id="${id}"])`));
                const rowData = row.data();
                if (!rowData) return;

                const battery = rowData.battery || {};

                // ===== FIELD UTAMA =====
                $('#editId').val(rowData.id);
                $('#editBatteryId').text(rowData.id);
                $('#editTanggal').val(rowData.tanggal);
                $('#editWaktuMulai').val(rowData.waktu_mulai);
                $('#editWaktuSelesai').val(rowData.waktu_selesai);

                $('#editBatteryType').val(battery.battery_type || '');
                $('#editNoUnit').val(battery.no_unit || '');
                $('#editNoSeri').val(battery.no_seri || '');
                $('#editCatatan').val(battery.catatan || '');

                const keteranganMap = parseKeteranganPairs(rowData.keterangan || '');

                let detailHtml = '';

                if (Array.isArray(battery.details) && battery.details.length > 0) {
                    const sortedDetails = [...battery.details].sort((a, b) => a.cell - b.cell);

                    sortedDetails.forEach((detail, index) => {
                        const isOK = (v) => v === 1 || v === '1' || v === true;

                        detailHtml += `
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-3" data-cell="${detail.cell}">
                                    <div class="card-header fw-bold">Cell ${detail.cell}</div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <input type="hidden" name="details[${index}][id]" value="${detail.id || ''}">
                                            <input type="hidden" name="details[${index}][cell]" value="${detail.cell}">

                                            <div class="col-6">
                                                <label>Voltase</label>
                                                <input type="number" step="0.01" class="form-control"
                                                    name="details[${index}][voltase]" value="${detail.voltase ?? ''}">
                                            </div>

                                            <div class="col-6">
                                                <label>Grounding</label>
                                                <input type="number" step="0.01" class="form-control"
                                                    name="details[${index}][grounding]" value="${detail.grounding ?? ''}">
                                            </div>

                                            ${buildCheckField(index, 'level_air_aki', 'Level Air Aki', detail.level_air_aki)}
                                            ${buildCheckField(index, 'intercell', 'Intercell', detail.intercell)}
                                            ${buildCheckField(index, 'kondisi_skun', 'Kondisi Skun', detail.kondisi_skun)}
                                            ${buildCheckField(index, 'kondisi_unit', 'Kondisi Unit', detail.kondisi_unit)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    detailHtml = '<div class="col-12 text-center py-4">Tidak ada data detail cell</div>';
                }

                $('#editDetailsContainer').html(detailHtml);

                // ===== PREFILL KETERANGAN DARI BACKEND =====
                Object.entries(keteranganMap).forEach(([key, value]) => {
                    const cell = key.match(/(\d+)$/)?.[1];
                    if (!cell) return;

                    const lowerKey = key.toLowerCase();

                    let field = '';
                    if (lowerKey.includes('level air')) field = 'level_air_aki';
                    else if (lowerKey.includes('intercell')) field = 'intercell';
                    else if (lowerKey.includes('skun')) field = 'kondisi_skun';
                    else if (lowerKey.includes('unit')) field = 'kondisi_unit';

                    if (!field) return;

                    const card = $(`.card[data-cell="${cell}"]`);
                    const input = card.find(`input[name*="[keterangan_${field}]"]`);

                    input.val(value).removeClass('d-none');
                    card.find(`select[name*="[${field}]"]`).val('0'); // paksa NG
                });

                // // ===== HANDLER OK / NG =====
                $('.form-select.status-select').off('change').on('change', function() {
                    const wrapper = $(this).closest('.check-wrapper');
                    const card = $(this).closest('.card');
                    const input = wrapper.find('.keterangan-input');

                    if ($(this).val() === '0') { // NG
                        wrapper.addClass('ng-active');
                        card.addClass('cell-ng');
                        input.removeClass('d-none');
                    } else { // OK
                        wrapper.removeClass('ng-active');
                        input.addClass('d-none').val('');

                        // cek: masih ada NG lain di card?
                        if (card.find('.status-select[value="0"]:selected').length === 0) {
                            card.removeClass('cell-ng');
                        }
                    }
                }).trigger('change');

                $('#editModal').modal('show');
            }

            function buildCheckField(index, field, label, value) {
                const isOK = value === 1 || value === '1' || value === true;

                return `
                    <div class="col-12 check-wrapper">
                        <label>${label}</label>
                        <select class="form-select status-select mb-1"
                            name="details[${index}][${field}]">
                            <option value="1" ${isOK ? 'selected' : ''}>OK</option>
                            <option value="0" ${!isOK ? 'selected' : ''}>NG</option>
                        </select>

                        <input type="text"
                            name="details[${index}][keterangan_${field}]"
                            class="form-control form-control-sm keterangan-input d-none"
                            placeholder="Keterangan (Wajib)">
                    </div>
                `;
            }

            $('#btnAddCell').off('click').on('click', function() {
                const newCell = getMaxCellNumber() + 1;
                const index = $('#editDetailsContainer .card').length;

                const html = `
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-3 cell-new"
                            data-cell="${newCell}"
                            data-is-new="1">

                            <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                                <div>
                                    Cell ${newCell}
                                    <span class="badge bg-info ms-2">NEW</span>
                                </div>

                                <button type="button"
                                    class="btn btn-sm btn-danger btnDeleteCell">
                                    ✕
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="row g-2">
                                    <input type="hidden" name="details[${index}][cell]" value="${newCell}">
                                    <input type="hidden" name="details[${index}][is_new]" value="1">

                                    <div class="col-6">
                                        <label>Voltase</label>
                                        <input type="number" step="0.01" class="form-control"
                                            name="details[${index}][voltase]">
                                    </div>

                                    <div class="col-6">
                                        <label>Grounding</label>
                                        <input type="number" step="0.01" class="form-control"
                                            name="details[${index}][grounding]">
                                    </div>

                                    ${buildCheckField(index, 'level_air_aki', 'Level Air Aki')}
                                    ${buildCheckField(index, 'intercell', 'Intercell')}
                                    ${buildCheckField(index, 'kondisi_skun', 'Kondisi Skun')}
                                    ${buildCheckField(index, 'kondisi_unit', 'Kondisi Unit')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#editDetailsContainer').append(html);

                const $newCard = $('#editDetailsContainer')
                    .find(`.card[data-cell="${newCell}"]`);

                $newCard.find('.status-select')
                    .val('1') // OK
                    .trigger('change');
            });

            $(document).on('click', '.btnDeleteCell', function() {
                const card = $(this).closest('.card');

                if (card.data('is-new') !== 1) {
                    Swal.fire('Tidak diizinkan', 'Cell bawaan tidak bisa dihapus', 'warning');
                    return;
                }

                card.closest('.col-md-6').remove();
            });

            function getMaxCellNumber() {
                let max = 0;
                $('.card[data-cell]').each(function() {
                    const cell = parseInt($(this).data('cell'));
                    if (cell > max) max = cell;
                });
                return max;
            }

            $(document).on('change', '.status-select', function() {

                const wrapper = $(this).closest('.check-wrapper');
                const card = $(this).closest('.card');
                const input = wrapper.find('.keterangan-input');

                if ($(this).val() === '0') { // NG
                    wrapper.addClass('ng-active');
                    card.addClass('cell-ng');
                    input.removeClass('d-none');

                } else { // OK
                    wrapper.removeClass('ng-active');
                    input.addClass('d-none').val('');

                    // cek masih ada NG lain
                    const stillNg = card.find('.status-select').filter(function() {
                        return $(this).val() === '0';
                    }).length > 0;

                    if (!stillNg) {
                        card.removeClass('cell-ng');
                    }
                }

            });

            // Trigger SETELAH plotting keterangan
            $('.form-select').trigger('change');

            $('#filterDate, #filterTipeBaterai, #filterUnit').on('change keyup', function() {
                table.ajax.reload();
            });

            $('#btnApply').on('click', () => table.ajax.reload());

            $('#btnReset').on('click', () => {
                $('#filterDate, #filterUnit, #filterTipeBaterai').val('');
                table.ajax.reload();
            });

            function collectNotOkDetailsEdit() {
                const list = [];

                $('#editDetailsContainer .card[data-cell]').each(function() {
                    const card = $(this);
                    const cell = card.data('cell');

                    card.find('.check-wrapper').each(function() {
                        const wrapper = $(this);
                        const select = wrapper.find('.status-select');

                        if (select.val() !== '0') return; // hanya NG

                        const label = wrapper.find('label').first().text().trim();
                        const noteInput = wrapper.find('.keterangan-input');
                        const note = noteInput.val()?.trim() || '(tidak ada keterangan)';

                        // Format: Kondisi Skun 12: Miring
                        list.push(`${label} ${cell}: ${note}`);
                    });
                });

                return list.join(' | ');
            }

            // Simpan edit (sama seperti sebelumnya)
            $('#editFormBattery').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editId').val();

                // ambil keterangan hasil formatting
                const keteranganFormatted = collectNotOkDetailsEdit();
                $('#editKeterangan').val(keteranganFormatted);

                let waktuMulai = $('#editWaktuMulai').val();
                if (waktuMulai && waktuMulai.length === 8) {
                    $('#editWaktuMulai').val(waktuMulai.slice(0, 5));
                }

                let waktuSelesai = $('#editWaktuSelesai').val();
                if (waktuSelesai && waktuSelesai.length === 8) {
                    $('#editWaktuSelesai').val(waktuSelesai.slice(0, 5));
                }

                const formData = $('#editFormBattery').serialize();

                console.log(formData);

                $('#editLoading').show();
                $('#btnSaveEdit').prop('disabled', true);

                $.ajax({
                    url: `${UPDATE_URL}/${id}`,
                    method: 'POST',
                    data: formData,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire('Berhasil', 'Data berhasil diperbarui', 'success');
                            $('#editModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.message || 'Gagal menyimpan',
                            'error'
                        );
                    },
                    complete: function() {
                        $('#editLoading').hide();
                        $('#btnSaveEdit').prop('disabled', false);
                    }
                });
            });

            // Event tombol Delete
            $('#batteryTable tbody').on('click', '.btnDelete', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus data ini?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: `${DELETE_URL}/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message || 'Data derhasil dihapus',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message ||
                                    'Gagal hapus data'
                            });
                        }
                    });
                });
            });

        });
    </script>
@endsection
