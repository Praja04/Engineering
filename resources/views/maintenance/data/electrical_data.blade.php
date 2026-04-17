@extends('layouts.app')

@section('title', ' Data Check Mtc Electrical')

@section('styles')
<style>
    .card-soft {
        border: 1px solid #eee;
        /* border-radius: 14px; */
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    .small-muted {
        color: #6c757d;
        font-size: .85rem;
    }

    .item-line {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px dashed #eee;
    }

    .item-line:last-child {
        border-bottom: none;
    }

    .detail-meta .meta-label {
        font-size: .8rem;
        color: #6c757d;
    }

    .detail-meta .meta-value {
        font-weight: 600;
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
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px 22px;
    }

    @media (max-width: 992px) {
        .items-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .items-grid {
            grid-template-columns: 1fr;
        }
    }

    .item-cell {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 4px 0;
    }

    .item-cell .item-label {
        font-size: .92rem;
        line-height: 1.25rem;
    }

    .status-3 .btn {
        white-space: nowrap;
    }

    .item-edit {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .item-edit .item-label {
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="card card-soft shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fw-bold">Data Maintenance Electrical</h4>
                    <div class="small-muted">List inspeksi + detail hasil pengecekan</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('/mtc/form/electrical/index') }}" class="btn btn-primary">
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
                        <label class="form-label">Paket</label>
                        <select class="form-select" id="filterPaket">
                            <option value="">Semua</option>
                            <option value="Z">Z</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cari Nama Mesin</label>
                        <input type="text" class="form-control" id="filterNama">

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
                    <table class="table table-hover align-middle" id="tabelElectrical">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Mesin</th>
                                <th>Lokasi</th>
                                <th>Tanggal</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Paket</th>
                                <th>status</th>
                                <th style="width:180px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center small-muted py-4">Memuat data...</td>
                            </tr>
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
                    <div class="fw-bold" id="detailTitle">Detail</div>
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
            <form id="formEditElectrical">
                @csrf
                <input type="hidden" id="editId" name="id">

                <div class="modal-header">
                    <div>
                        <div class="fw-bold">Edit Inspeksi Mtc Electrical</div>
                        <div class="text-muted small" id="editSub"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
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
                            <label class="form-label">Paket</label>
                            <select class="form-select" name="paket" id="editPaket">
                                <option value="">-- Pilih --</option>
                                <option>Z</option>
                                <option>A</option>
                                <option>B</option>
                                <option>C</option>
                                <option>D</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Waktu Mulai</label>
                            <input type="time" class="form-control" name="waktu_mulai" id="editWaktuMulai" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Waktu Selesai</label>
                            <input type="time" class="form-control" name="waktu_selesai" id="editWaktuSelesai" readonly>
                        </div>
                    </div>

                    <div id="editSections"><!-- injected --></div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Tindakan Korektif</label>
                            <textarea class="form-control" name="korektif" id="editKorektif" rows="3"></textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="fw-bold mb-2">Kebutuhan Material</div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="materialTableEdit">
                            <thead class="table-light text-nowrap">
                                <tr>
                                    <th style="width: 20%">MID</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 15%">Jumlah</th>
                                    <th class="text-center" style="width: 10%">
                                        <button type="button" class="btn btn-sm btn-primary" id="btnAddMaterialEdit">
                                            +
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- dynamic rows -->
                            </tbody>
                        </table>
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
        const API_URL = "{{ url('api/mtc/electrical/get-data') }}";
        const DELETE_URL = "{{ url('mtc/main/delete') }}";
        const UPDATE_URL = "{{ url('mtc/data/electrical/update') }}";

        let currentRows = [];
        const mesinList = @json($mesin);

        const fields = {
            panel: [
                ['check_kunci', 'Check Kunci'],
                ['check_koneksi_kabel', 'Check Koneksi Kabel'],
                ['check_wiring_panel', 'Check Wiring Panel'],
                ['check_lampu_indikator', 'Check Lampu Indikator'],
                ['check_name_plate', 'Check Name Plate'],
                ['check_unit_electrical', 'Check Unit Electrical'],
                ['check_grounding', 'Check Grounding'],
                ['check_kebersihan', 'Check Kebersihan'],
                ['check_bus_bar', 'Check Bus Bar'],
                ['check_nilai_grounding', 'Check Nilai Grounding'],
            ],

            penerangan: [
                ['check_kondisi_lampu', 'Check Kondisi Lampu'],
                ['check_cover_lampu', 'Check Cover Lampu'],
                ['check_wiring_penerangan', 'Check Wiring Penerangan'],
                ['check_saklar', 'Check Saklar'],
                ['check_penyangga_penerangan', 'Check Penyangga Penerangan'],
            ],

            sistem_distribusi: [
                ['check_stecker', 'Check Stecker'],
                ['check_stop_kontak', 'Check Stop Kontak'],
                ['check_terminal_listrik', 'Check Terminal Listrik'],
                ['check_pengabelan_distribusi', 'Check Pengkabelan Distribusi'],
                ['check_support_pelindung_distribusi', 'Check Support & Pelindung Distribusi'],
            ],

            capacitor_bank: [
                ['check_kondisi_fisik_capacitor', 'Check Kondisi Fisik Capacitor'],
                ['check_nilai_farad', 'Check Nilai Farad'],
                ['check_nilai_ampere', 'Check Nilai Ampere'],
                ['check_kebersihan_capacitor', 'Check Kebersihan'],
            ],

            trafo: [
                ['check_kebocoran_oli_sisi_bawah', 'Check Kebocoran Oli Sisi Bawah'],
                ['check_kebocoran_oli_sisi_atas', 'Check Kebocoran Oli Sisi Atas'],
                ['check_level_oli', 'Check Level Oli'],
            ],
        };

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

        populateMesinOptions();

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
            const section = (title, items) => {
                const cells = items.map(([key, label]) => `
                        <div class="item-cell">
                            <div class="item-label">${label}</div>
                            <div>${statusBadge(row.electrical?.[key])}</div>
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
                            <div class="meta-label">Nama Mesin</div>
                            <div class="meta-value">${row.electrical.mesin.nama_mesin ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">Lokasi</div>
                            <div class="meta-value">${row.electrical.mesin.lokasi ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">Tanggal</div>
                            <div class="meta-value">${fmtDate(row.tanggal)}</div>
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
                            <div class="meta-label">Paket</div>
                            <div class="meta-value">${row.paket ?? '-'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="meta-label">Dibuat oleh</div>
                            <div class="meta-value">${row.created_by.username ?? row.created_by.created_by ?? '-'}</div>
                        </div>
                    </div>

                    ${section('Panel', fields.panel)}
                    ${section('Penerangan', fields.penerangan)}
                    ${section('Sistem Distribusi', fields.sistem_distribusi)}
                    ${section('Capacitor Bank', fields.capacitor_bank)}
                    ${section('Trafo', fields.trafo)}

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="group-title">Keterangan</div>
                            <div>${row.electrical?.keterangan ?? '-'}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-title">Tindakan Korektif</div>
                            <div>${row.electrical?.korektif ?? '-'}</div>
                        </div>
                        <div class="col-md-12">
                            <div class="group-title">Kebutuhan Material</div>
                           ${
    row.kebutuhan_material && row.kebutuhan_material.length
    ? row.kebutuhan_material.map(m => `
        <div>${m.mid} - ${m.deskripsi} - ${m.qty}</div>
      `).join('')
    : '<div>-</div>'
}
                        </div>
                    </div>
                    `;
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

        const dtElectrical = $('#tabelElectrical').DataTable({
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
                    // filter custom milikmu
                    d.date = $('#filterDate').val() || null;
                    d.paket = $('#filterPaket').val() || null;
                    d.nama_mesin = $('#filterNama').val() || null;
                },
                dataSrc: function(json) {
                    // simpan untuk modal detail
                    currentRows = json.data || [];
                    return currentRows;
                },
                error: function() {
                    // DataTables punya tampilan default "No data", tapi kalau mau custom:
                    // (opsional) bisa pakai language.emptyTable
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
                    data: 'electrical.mesin.nama_mesin',
                    orderable: false,
                    defaultContent: '-'
                },
                {
                    data: 'electrical.mesin.lokasi',
                    render: function(data, type) {
                        if (type === 'display') return fmtDate(data);
                        return data; // sorting pakai ISO
                    }
                },
                {
                    data: 'tanggal',
                    orderable: false,
                    defaultContent: '-'
                },
                {
                    data: 'waktu_mulai',
                    defaultContent: '-',
                    orderable: false,
                },
                {
                    data: 'waktu_selesai',
                    defaultContent: '-',
                    orderable: false,
                },
                {
                    data: 'paket',
                    defaultContent: '-',
                    orderable: false,
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
                    orderable: false,
                    className: 'text-center text-nowrap',
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
                emptyTable: `
                        <div class="py-4 text-center text-muted">
                        Tidak ada data
                        </div>
                    `,
                processing: "Memuat..."
            }
        });

        // apply/reset filter 
        $('#btnApply').on('click', function(e) {
            e.preventDefault();
            dtElectrical.ajax.reload();
        });

        $('#btnReset').on('click', function(e) {
            e.preventDefault();
            $('#filterDate').val('');
            $('#filterPaket').val('');
            $('#filterNama').val('');
            dtElectrical.ajax.reload();
        });

        // Detail modal
        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            const row = currentRows.find(x => x.id == id);
            if (!row) return;

            $('#detailTitle').text('Detail Inspeksi Mtc Electrical');
            $('#detailSub').text(`${fmtDate(row.tanggal)} • ${row.waktu_mulai ?? '-'} - ${row.waktu_selesai ?? '-'}`);
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
            const cells = items.map(([key, label]) => {
                const state = valToState(row.electrical?.[key]);
                return `
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="item-edit" data-field="${key}" data-label="${label}">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="item-label">${label}</div>

                                    <div class="btn-group btn-group-sm status-3" role="group">
                                    <input type="radio" class="btn-check edit-radio" name="${key}" id="${key}_null" value="" ${state===''?'checked':''}>
                                    <label class="btn btn-outline-secondary" for="${key}_null">No Check</label>

                                    <input type="radio" class="btn-check edit-radio" name="${key}" id="${key}_ok" value="1" ${state==='1'?'checked':''}>
                                    <label class="btn btn-outline-success" for="${key}_ok">OK</label>

                                    <input type="radio" class="btn-check edit-radio" name="${key}" id="${key}_ng" value="0" ${state==='0'?'checked':''}>
                                    <label class="btn btn-outline-danger" for="${key}_ng">No OK</label>
                                    </div>
                                </div>

                                <div class="mt-2 ket-wrap ${state==='0' ? '' : 'd-none'}">
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
                    ${renderEditSection('Panel', fields.panel, row)}
                    ${renderEditSection('Penerangan', fields.penerangan, row)}
                    ${renderEditSection('Sistem Distribusi', fields.sistem_distribusi, row)}
                    ${renderEditSection('Capacitor Bank', fields.capacitor_bank, row)}
                    ${renderEditSection('Trafo', fields.trafo, row)}
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
            $('#editPaket').val(row.paket ?? '');
            // $('#editKeterangan').val(row.keterangan ?? '');
            $('#editKorektif').val(row.electrical?.korektif ?? '');
            $('#editNamaMesin')
                .val(row.electrical?.mesin_id)
                .trigger('change');

            $('#editSub').text(`${fmtDate(row.tanggal)} • ${row.waktu_mulai ?? '-'} - ${row.waktu_selesai ?? '-'}`);

            buildEditForm(row);
            renderEditMaterials(
                row.kebutuhan_material ? [row.kebutuhan_material] : []
            );

            const ketMap = parseKeteranganPairs(row.electrical?.keterangan);
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

        function renderEditMaterials(materials) {
            const tbody = $('#materialTableEdit tbody');
            tbody.empty();

            if (!materials || materials.length === 0) {
                addMaterialRowEdit();
                return;
            }

            materials.forEach((item, index) => {
                const row = $(materialRowTemplate(index));

                row.find('.material-id').val(item.id);
                row.find('.material-mid').val(item.mid);
                row.find('.material-deskripsi').val(item.deskripsi);
                row.find('.material-qty').val(item.qty);

                tbody.append(row);
            });
        }

        function materialRowTemplate(index) {
            return `
                    <tr class="material-row">
                        <input type="hidden" name="materials[${index}][id]" class="material-id">

                        <td>
                            <input type="number"
                                name="materials[${index}][mid]"
                                class="form-control form-control-sm material-mid">
                        </td>

                        <td>
                            <input type="text"
                                name="materials[${index}][deskripsi]"
                                class="form-control form-control-sm material-deskripsi">
                        </td>

                        <td>
                            <input type="number"
                                name="materials[${index}][qty]"
                                class="form-control form-control-sm material-qty"
                                min="1">
                        </td>

                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-sm btn-danger btnRemoveMaterial">
                                ×
                            </button>
                        </td>
                    </tr>
                `;
        }

        $('#btnAddMaterialEdit').on('click', function() {
            addMaterialRowEdit();
        });

        function addMaterialRowEdit() {
            const index = $('#materialTableEdit tbody tr').length;
            $('#materialTableEdit tbody').append(materialRowTemplate(index));
        }

        $(document).on('click', '.btnRemoveMaterial', function() {
            $(this).closest('tr').remove();
            reindexMaterialRows();
        });

        function reindexMaterialRows() {
            $('#materialTableEdit tbody tr').each(function(i) {
                $(this).find('input').each(function() {
                    this.name = this.name.replace(/\[\d+]/, `[${i}]`);
                });
            });
        }

        $('#formEditElectrical').on('submit', function(e) {
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data diupdate',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    bootstrap.Modal.getInstance(document.getElementById('modalEdit'))
                        .hide();

                    // reload datatable (stay page)
                    $('#tabelElectrical').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Gagal update data'
                    });
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
                icon: 'warning',
                title: 'Hapus data?',
                text: `Data Mtc Electrical akan dihapus permanen`,
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
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
                        dtElectrical.ajax.reload(null,
                            false);
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