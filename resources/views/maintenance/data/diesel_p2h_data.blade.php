@extends('layouts.app')

@section('title', 'Data Check Mtc Electric P2H')

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

    .status-3 .btn {
        white-space: nowrap;
        font-size: .85rem;
        padding: .4rem .6rem;
    }

    .item-edit {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .item-edit .item-label {
        font-weight: 600;
        font-size: .95rem;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="card card-soft shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fw-bold">Data Maintenance Diesel P2H</h4>
                    <div class="small-muted">List inspeksi + detail hasil pengecekan</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('/mtc/form/diesel-p2h/index') }}" class="btn btn-primary">
                        + Input Baru
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
                        <label class="form-label">No Unit</label>
                        <input type="text" class="form-control" id="filterNoUnit">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Departemen</label>
                        <input type="text" class="form-control" id="filterDepartemen">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="d-flex gap-2 text-nowrap">
                            <button type="button" class="btn btn-outline-primary w-100" id="btnApply">
                                <i class="mdi mdi-filter me-2"></i> Terapkan</button>
                            <button type="button" class="btn btn-outline-secondary w-100" id="btnReset">
                                <i class="mdi mdi-restart"></i> Reset</button>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabelDieselP2h">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Nama Mesin</th>
                                <th>No Unit</th>
                                <th>Departemen</th>
                                <th>Shift</th>
                                <th style="width:180px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
                    <div class="fw-bold" id="detailTitle">Detail Inspeksi Diesel P2H</div>
                    <div class="small-muted" id="detailSub"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailBody">
                {{-- injected --}}
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
            <form id="formEditDieselP2h">
                @csrf
                <input type="hidden" id="editId" name="id">

                <div class="modal-header">
                    <div>
                        <div class="fw-bold">Edit Inspeksi Mtc Diesel P2H</div>
                        <div class="text-muted small" id="editSub"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Nama Mesin *</label>
                            <select class="form-select" name="mesin_id" id="editNamaMesin" required>
                                {{-- <option value="">-- Pilih Mesin --</option> --}}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal *</label>
                            <input type="date" class="form-control" name="tanggal" id="editTanggal" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Departemen</label>
                            <input type="text" class="form-control" name="departemen" id="editDepartemen">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">No Unit</label>
                            <input type="text" class="form-control" name="no_unit" id="editNoUnit">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="waktu_mulai" id="editWaktuMulai" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="waktu_selesai" id="editWaktuSelesai" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Shift</label>
                            <select name="shift" class="form-select" id="editShift">
                                <option value="">-- Pilih Shift --</option>
                                <option value="1">Shift 1</option>
                                <option value="2">Shift 2</option>
                                <option value="3">Shift 3</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hours Meter (Jam Operasional) <span class="text-danger">*</span></label>
                            <input type="numeric" class="form-control" name="hour_meter" id="editHourMeter" required>
                        </div>
                    </div>

                    <div id="editSections"><!-- injected checklist items --></div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="catatan" id="editCatatan" rows="2"></textarea>
                        </div>
                    </div>
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
        const API_URL = "{{ url('api/mtc/diesel-p2h/get-data') }}";
        const DELETE_URL = "{{ url('mtc/main/delete') }}";
        const UPDATE_URL = "{{ url('mtc/data/diesel-p2h/update') }}";

        let currentRows = [];
        const mesinList = @json($mesin);

        $('#editNamaMesin').select2({
            theme: 'bootstrap-5',
            placeholder: 'Cari nama mesin / lokasi...',
            allowClear: true,
            width: '100%',
            templateResult: function(data) {
                if (!data.id) return data.text;

                const parts = data.text.split(' - ');
                return $(`
                        <div>
                            <strong>${parts[0]}</strong><br>
                            <small class="text-muted">${parts[1] ?? ''}</small>
                        </div>
                    `);
            }
        });

        function populateMesinOptions() {
            const select = $('#editNamaMesin');

            mesinList.forEach(m => {
                select.append(`
                        <option value="${m.id}">
                            ${m.nama_mesin} - ${m.lokasi}
                        </option>
                    `);
            });
        }

        populateMesinOptions();

        const dieselP2h = {
            klakson: {
                label: 'Check Klakson',
                standar: 'Bunyi ketika tombol ditekan',
            },
            buzzer_back: {
                label: 'Check Buzzer Back',
                standar: 'Berbunyi normal saat maju dan mundur',
            },
            oli_mesin: {
                label: 'Check Kondisi & Level Oli Mesin',
                standar: 'Berada di level max dan tidak ada kebocoran',
            },
            radiator_hose: {
                label: 'Check Kondisi Level Radiator & Hose',
                standar: 'Berada di level max dan tidak ada kebocoran',
            },
            water_pump: {
                label: 'Check Water Pump',
                standar: 'Tidak ada kebocoran',
            },
            injection_system: {
                label: 'Check Injection Pump, Injector & Piping',
                standar: 'Tidak ada kebocoran',
            },
            fan_vbelt: {
                label: 'Check Fan & V-Belt',
                standar: 'Berfungsi baik dan V-belt tidak retak atau putus',
            },
            turbocharger_manifold: {
                label: 'Check Turbocharger & Manifold',
                standar: 'Berfungsi baik dan terlubrikasi',
            },
            tensioner_belt: {
                label: 'Check Automatic Tensioner Belt',
                standar: 'Berfungsi dengan baik',
            },
            starting_motor: {
                label: 'Check Fungsi Starting Motor',
                standar: 'Berfungsi dengan baik',
            },
            alternator: {
                label: 'Check Fungsi Alternator',
                standar: 'Berfungsi dengan baik',
            },
            control_display: {
                label: 'Check Control Display',
                standar: 'Berfungsi normal, tidak pecah, dan tidak ada alarm',
            },
            oli_transmisi: {
                label: 'Check Kondisi & Level Oli Transmisi',
                standar: 'Berada di level max dan tidak ada kebocoran',
            },
            aki: {
                label: 'Check Kondisi Aki & Level Air Aki',
                standar: 'Level max, aki tidak drop, dan bersih',
            },
            engine_mounting: {
                label: 'Check Engine Mounting',
                standar: 'Berfungsi dengan baik',
            },
            filter_oli_transmisi: {
                label: 'Check Filter Oli Transmisi',
                standar: 'Tidak ada kebocoran oli',
            },
            fungsi_rem: {
                label: 'Check Fungsi Rem',
                standar: 'Berfungsi dengan baik dan tidak blong',
            },
            fungsi_kopling: {
                label: 'Check Fungsi Kopling',
                standar: 'Berfungsi dengan baik dan tidak macet',
            },
            oli_hydraulic: {
                label: 'Check Kondisi & Level Oli Hydraulic',
                standar: 'Berada di level max dan tidak ada kebocoran',
            },
            hydraulic_system: {
                label: 'Check Fungsi Hydraulic System',
                standar: 'Berfungsi dengan baik dan terlubrikasi',
            },
            steering_system: {
                label: 'Check Fungsi Steering System',
                standar: 'Tidak berat dan bergerak lancar',
            },
            body_back_rest: {
                label: 'Check Kondisi Back Rest & Body',
                standar: 'Tidak ada cacat atau penyok',
            },
            kaca_spion: {
                label: 'Check Kaca Spion',
                standar: 'Terpasang lengkap dan tidak pecah',
            },
            bucket_pin: {
                label: 'Check Kondisi Bucket & Pin Bucket',
                standar: 'Berfungsi baik dan tidak retak atau hilang',
            },
            dump_pin_bushing: {
                label: 'Check Kondisi Dump, Pin & Bushing',
                standar: 'Berfungsi dan tidak retak atau hilang',
            },
            seal_hydraulic: {
                label: 'Check Kondisi Seal Hydraulic',
                standar: 'Tidak ada kebocoran oli',
            },
            roda_ban_baut: {
                label: 'Check Kondisi Roda, Ban & Baut',
                standar: 'Ban layak pakai dan baut terpasang kencang',
            },
            lampu_unit: {
                label: 'Check Lampu Depan & Belakang (Kanan & Kiri)',
                standar: 'Menyala normal dan tidak pecah',
            },
            baut_bearing_molen: {
                label: 'Check Baut Bearing Molen & Gandengan',
                standar: 'Baut terpasang utuh dan kencang',
            },
            baut_hanger_as: {
                label: 'Check Baut Hanger As Roda',
                standar: 'Baut terpasang utuh dan kencang',
            },
            baut_grease: {
                label: 'Check Kondisi Baut Grease',
                standar: 'Baut tidak aus dan terlumasi grease',
            },
            katup_pembuangan_angin: {
                label: 'Check Katup Pembuangan Angin',
                standar: 'Berfungsi dengan baik',
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

        function buildDetailHTML(row) {
            const section = (title, items, rowData) => {
                const cells = Object.entries(items).map(([key, item]) => `
                        <div class="item-cell d-flex justify-content-between align-items-start gap-3">
                            <div class="item-info">
                                <div class="item-label fw-semibold">
                                    ${item.label}
                                </div>

                                <div class="item-standar text-muted small mt-1">
                                    ${item.standar ?? ''}
                                </div>
                            </div>

                            <div class="item-status mt-1">
                                ${statusBadge(rowData?.[key])}
                            </div>

                        </div>

                    `).join('');

                return `
                        <div class="mb-3">
                            <div class="group-title">${title}</div>
                            <div class="items-grid">${cells}</div>
                        </div>
                    `;
            };

            return `
                    <div class="detail-meta row g-3 mb-2">
                        <div class="col-md-3">
                            <div class="meta-label">Tanggal</div>
                            <div class="meta-value">${row.tanggal ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">Waktu Mulai</div>
                            <div class="meta-value">${row.waktu_mulai ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">Waktu Selesai</div>
                            <div class="meta-value">${row.waktu_selesai ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">Departemen</div>
                            <div class="meta-value">${row.departemen ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">No Unit</div>
                            <div class="meta-value">${row.diesel_p2h.no_unit ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">No Unit</div>
                            <div class="meta-value">${row.diesel_p2h.shift ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">Dibuat oleh</div>
                            <div class="meta-value">${row.created_by.username ?? row.created_by.created_by ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">Hours Meter (Jam Operasional)</div>
                            <div class="meta-value">${row.diesel_p2h.hours_meter ?? row.diesel_p2h.hours_meter ?? '-'}</div>
                        </div>
                    </div>

                    ${section('Electric P2H', dieselP2h, row.diesel_p2h)}

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="group-title">Keterangan NOK</div>
                            <div>${row.keterangan ?? '-'}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="group-title">Catatan</div>
                            <div>${row.diesel_p2h.catatan ?? '-'}</div>
                        </div>
                    </div>
                `;
        }

        const dtDieselP2h = $('#tabelDieselP2h').DataTable({
            processing: true,
            serverSide: false,
            searching: false,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            order: [
                [0, 'asc']
            ],
            ajax: {
                url: API_URL,
                data: function(d) {
                    d.date = $('#filterDate').val() || null;
                    d.no_unit = $('#filterNoUnit').val() || null;
                    d.departemen = $('#filterDepartemen').val() || null;
                },
                dataSrc: function(json) {
                    currentRows = json.data || [];
                    return currentRows;
                }
            },
            columns: [{
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'tanggal',
                    render: function(data) {
                        return fmtDate(data);
                    }
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
                    data: 'diesel_p2h.mesin.nama_mesin',
                    orderable: false,
                    defaultContent: '-'
                },
                {
                    data: 'diesel_p2h.no_unit',
                    orderable: false,
                    defaultContent: '-'
                },
                {
                    data: 'departemen',
                    orderable: false,
                    defaultContent: '-'
                },
                {
                    data: 'diesel_p2h.shift',
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
                    orderable: false,
                    render: function(row) {
                        return `
                                <button class="btn btn-sm btn-primary btn-detail" data-id="${row.id}" title="Detail"><i class="mdi mdi-eye"></i></button>
                                <button class="btn btn-sm btn-info btn-edit" data-id="${row.id}" title="Edit"><i class="mdi mdi-pencil"></i></button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" title="Hapus"><i class="mdi mdi-delete"></i></button>
                                <button class="btn btn-sm btn-warning btn-print" data-id="${row.id}" title="Download"><i class="mdi mdi-download"></i></button>
                            `;
                    }
                }
            ],
            language: {
                emptyTable: `<div class="py-4 text-center text-muted">Tidak ada data</div>`,
                processing: "Memuat..."
            }
        });

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

        // Apply/reset filter
        $('#filterDate, #filterDepartemen, #filterNoUnit').on('change keyup', function() {
            dtDieselP2h.ajax.reload();
        });

        $('#btnApply').on('click', () => dtDieselP2h.ajax.reload());

        $('#btnReset').on('click', () => {
            $('#filterDate, #filterDepartemen, #filterNoUnit').val('');
            dtDieselP2h.ajax.reload();
        });

        // Detail modal
        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            const row = currentRows.find(x => x.id == id);
            if (!row) return;

            $('#detailSub').text(`${fmtDate(row.tanggal)}`);
            $('#detailBody').html(buildDetailHTML(row));

            new bootstrap.Modal(document.getElementById('modalDetail')).show();
        });

        function toDateInputValue(iso) {
            // iso: "2026-01-21T17:00:00.000000Z" -> "2026-01-21"
            if (!iso) return '';
            const d = new Date(iso);
            if (isNaN(d.getTime())) return iso.slice(0, 10);
            return d.toISOString().slice(0, 10);
        }

        function valToState(v) {
            // convert backend true/false/null -> '1'/'0'/'' untuk form
            if (v === true || v === 1 || v === "1") return '1';
            if (v === false || v === 0 || v === "0") return '0';
            return '';
        }

        function renderEditSection(title, items, row) {

            const dieselP2hlData = row?.diesel_p2h || {};

            const cells = Object.entries(items).map(([key, item]) => {
                const rawValue = dieselP2hlData[key];
                const state = valToState(rawValue);

                return `
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="item-edit" data-field="${key}" data-label="${item.label}">
                                
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <!-- LABEL + STANDAR -->
                                    <div class="item-info">
                                        <div class="item-label fw-semibold">
                                            ${item.label}
                                        </div>
                                        <div class="text-muted small mt-1">
                                            ${item.standar ?? ''}
                                        </div>
                                    </div>

                                    <!-- STATUS -->
                                    <div class="btn-group btn-group-sm status-3" role="group">
                                        <input type="radio" class="btn-check edit-radio"
                                            name="${key}" id="${key}_null" value=""
                                            ${state === '' ? 'checked' : ''}>
                                        <label class="btn btn-outline-secondary" for="${key}_null">No Check</label>

                                        <input type="radio" class="btn-check edit-radio"
                                            name="${key}" id="${key}_ok" value="1"
                                            ${state === '1' ? 'checked' : ''}>
                                        <label class="btn btn-outline-success" for="${key}_ok">OK</label>

                                        <input type="radio" class="btn-check edit-radio"
                                            name="${key}" id="${key}_ng" value="0"
                                            ${state === '0' ? 'checked' : ''}>
                                        <label class="btn btn-outline-danger" for="${key}_ng">No OK</label>
                                    </div>
                                </div>

                                <div class="mt-2 ket-wrap ${state === '0' ? '' : 'd-none'}">
                                    <input type="text"
                                        class="form-control form-control-sm ket-input"
                                        placeholder="Keterangan wajib jika No OK"
                                        data-ket-for="${key}">
                                </div>

                            </div>
                        </div>
                    `;
            }).join('');

            return `
                    <div class="mb-3">
                        <div class="group-title">${title}</div>
                        <div class="row g-3">${cells}</div>
                    </div>
                `;
        }

        $(document).on('change', '.edit-radio', function() {
            const $card = $(this).closest('.item-edit');
            const field = $card.data('field');
            const label = $card.data('label');
            const val = $(`input[name="${field}"]:checked`).val(); // '', '1', '0'
            const $wrap = $card.find('.ket-wrap');
            const $input = $wrap.find('.ket-input');

            if (val === '0') {
                $wrap.removeClass('d-none');
                $input.attr('required', true);
            } else {
                $wrap.addClass('d-none');
                $input.val('').removeAttr('required').removeClass('is-invalid');
            }
        });

        function parseKeteranganPairs(text) {
            const map = {};
            if (!text) return map;

            // split by |, tapi aman untuk spasi
            text.split('|').map(s => s.trim()).forEach(part => {
                const idx = part.indexOf(':');
                if (idx === -1) return;
                const key = part.slice(0, idx).trim();
                const val = part.slice(idx + 1).trim();
                if (key) map[key] = val;
            });
            return map;
        }

        function buildEditForm(row) {
            const html = `
                    ${renderEditSection('Diesel P2H', dieselP2h, row)}
                `;
            $('#editSections').html(html);
        }

        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const row = currentRows.find(x => x.id == id);
            if (!row) return;

            $('#editId').val(row.id);
            $('#editTanggal').val(toDateInputValue(row.tanggal));
            $('#editWaktuMulai').val((row.waktu_mulai ?? '').toString().slice(0, 5));
            $('#editWaktuSelesai').val((row.waktu_selesai ?? '').toString().slice(0, 5));
            $('#editNoUnit').val(row.diesel_p2h.no_unit ?? '');
            $('#editShift').val(row.diesel_p2h.shift ?? '');
            $('#editDepartemen').val(row.departemen ?? '');
            $('#editCatatan').val(row.diesel_p2h.catatan ?? '');
            $('#editHourMeter').val(row.diesel_p2h.hours_meter ?? '');
            $('#editNamaMesin')
                .val(row.diesel_p2h?.mesin_id)
                .trigger('change');
            $('#editSub').text(`${fmtDate(row.tanggal)} • ${row.waktu_mulai ?? '-'} - ${row.waktu_selesai ?? '-'}`);

            buildEditForm(row);

            const ketMap = parseKeteranganPairs(row.keterangan);

            $('#editSections .item-edit').each(function() {
                const label = $(this).data('label');
                const field = $(this).data('field');
                const v = ketMap[label];
                if (v) {
                    $(this).find('.ket-input').val(v);
                }
            });

            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        });

        function collectNotOkDetails() {
            let pairs = [];
            let valid = true;

            $('#editSections .item-edit').each(function() {
                const $card = $(this);
                const field = $card.data('field');
                const label = $card.data('label'); // sudah humanize
                const state = $card.find(`input[name="${field}"]:checked`).val(); // '', '1', '0'

                if (state === '0') {
                    const $ket = $card.find('.ket-input');
                    const text = ($ket.val() || '').trim();

                    if (!text) {
                        valid = false;
                        $ket.addClass('is-invalid');
                    } else {
                        $ket.removeClass('is-invalid');
                        pairs.push(`${label}: ${text}`);
                    }
                } else {
                    $card.find('.ket-input').removeClass('is-invalid');
                }
            });

            return {
                valid,
                detailString: pairs.join(' | ')
            };
        }

        // Submit edit
        $('#formEditDieselP2h').on('submit', function(e) {
            e.preventDefault();

            const id = $('#editId').val();
            const $btn = $('#btnSaveEdit');
            const $spin = $('#spinEdit');

            $btn.prop('disabled', true);
            $spin.removeClass('d-none');

            const {
                valid,
                detailString
            } = collectNotOkDetails();

            if (!valid) {
                const $first = $('#modalEdit .is-invalid').first();
                if ($first.length) {
                    const $body = $('#modalEdit .modal-body');
                    $body.animate({
                        scrollTop: $body.scrollTop() + $first.position().top - 120
                    }, 200);
                    $first.focus();
                }
                $btn.prop('disabled', false);
                $spin.addClass('d-none');
                return;
            }

            const formData = new FormData(this);
            formData.set('keterangan', detailString);

            $.ajax({
                url: `${UPDATE_URL}/${id}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire('Berhasil', res.message || 'Data berhasil diperbaharui',
                        'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalEdit'))
                        .hide();
                    dtDieselP2h.ajax.reload(null, false);
                },
                error: function(xhr) {
                    Swal.fire('Gagal', xhr.responseJSON?.message || 'Gagal update',
                        'error');
                },
                complete: function() {
                    $btn.prop('disabled', false);
                    $spin.addClass('d-none');
                }
            });
        });

        // Delete
        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Hapus data?',
                text: `Data inspeksi Mtc Electric P2H akan dihapus permanen`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus'
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: `${DELETE_URL}/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire('Berhasil', res.message ||
                            'Data berhasil dihapus',
                            'success');
                        dtDieselP2h.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message ||
                            'Gagal update',
                            'error');
                    },
                });
            });
        });
    });
</script>
@endsection