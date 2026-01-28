@extends('layouts.app')

@section('title', ' Data Check Mtc Motor Pump')

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
                        <h4 class="fw-bold">Data Maintenance Motor Pump</h4>
                        <div class="small-muted">List inspeksi + detail hasil pengecekan</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ url('/mtc/form/motor-pump/index') }}" class="btn btn-primary">
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
                            <input type="text" class="form-control" id="filterNama" placeholder="contoh: Pompa...">

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
                        <table class="table table-hover align-middle" id="tblMotorPump">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Mesin</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Paket</th>
                                    <th>Ringkasan</th>
                                    <th>Dibuat Oleh</th>
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
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                <form id="formEditMotorPump">
                    @csrf
                    <input type="hidden" id="editId" name="id">

                    <div class="modal-header">
                        <div>
                            <div class="fw-bold">Edit Inspeksi Mtc Motor Pump</div>
                            <div class="text-muted small" id="editSub"></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama Mesin *</label>
                                <input type="text" class="form-control" name="nama_mesin" id="editNamaMesin" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal *</label>
                                <input type="date" class="form-control" name="tanggal" id="editTanggal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Paket</label>
                                <input type="text" class="form-control" name="paket" id="editPaket">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Waktu</label>
                                <input type="time" class="form-control" name="waktu" id="editWaktu" readonly>
                            </div>
                        </div>

                        <div id="editSections"><!-- injected --></div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Tindakan Korektif</label>
                                <textarea class="form-control" name="korektif" id="editKorektif" rows="3"></textarea>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const API_URL = "{{ url('api/mtc/motor-pump/get-data') }}";
            const DELETE_URL = "{{ url('mtc/data/motor-pump/delete') }}";
            const UPDATE_URL = "{{ url('mtc/data/motor-pump/update') }}";

            let currentRows = [];

            const fields = {
                motor: [
                    ['electrical_motor', 'Kondisi Electrical'],
                    ['putaran_motor', 'Putaran Motor'],
                    ['fibrasi_suara_motor', 'Fibrasi dan Suara Motor'],
                    ['bearing_motor', 'Bearing Motor'],
                    ['pelumasan_motor', 'Pelumasan Motor'],
                    ['kebersihan_unit_body_motor', 'Kebersihan Body Motor'],
                ],
                pompa: [
                    ['putaran_pompa', 'Putaran Pompa'],
                    ['shaft_karet_coupling_pompa', 'Shaft & Karet Coupling'],
                    ['fan_belt_pompa', 'Fan Belt'],
                    ['pressure_pompa', 'Pressure Pompa'],
                    ['mechanical_seal_pompa', 'Mechanical Seal'],
                    ['gasket_pompa', 'Gasket Pompa'],
                    ['impeler', 'Kondisi Impeller'],
                    ['kebersihan_unit_body_pompa', 'Kebersihan Unit & Body Pompa'],
                ],
                aksesoris: [
                    ['valve_aksesoris', 'Valve'],
                    ['cek_valve_aksesoris', 'Cek Valve'],
                    ['flow_meter_aksesoris', 'Flow Meter'],
                    ['strainer_aksesoris', 'Strainer / Saringan'],
                    ['alat_ukur_aksesoris', 'Alat Ukur'],
                    ['kelengkapan_baut_mur_aksesoris', 'Kelengkapan Baut & Mur'],
                ],
                gearbox: [
                    ['tambah_ganti_oli_gearbox', 'Penambahan / Penggantian Oli Gearbox'],
                    ['unit_area_gearbox', 'Unit & Area Gearbox'],
                    ['oil_seal_gearbox', 'Oil Seal Gearbox'],
                    ['filter_udara_gearbox', 'Filter Udara Gearbox'],
                    ['bearing_gearbox', 'Bearing Gearbox'],
                ],
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
                return `<span class="badge bg-secondary">No Check</span>`;
            }

            function summarize(row) {
                const allKeys = Object.values(fields).flat().map(x => x[0]);
                let ok = 0,
                    ng = 0,
                    nu = 0;

                allKeys.forEach(k => {
                    const v = row[k];
                    if (v === true || v === 1 || v === "1") ok++;
                    else if (v === false || v === 0 || v === "0") ng++;
                    else nu++;
                });

                return `
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge badge-soft-success">OK: ${ok}</span>
                        <span class="badge badge-soft-danger">No OK: ${ng}</span>
                        <span class="badge badge-soft-secondary">No Check: ${nu}</span>
                    </div>
                `;
            }

            function buildDetailHTML(row) {
                const section = (title, items) => {
                    const cells = items.map(([key, label]) => `
                        <div class="item-cell">
                        <div class="item-label">${label}</div>
                        <div>${statusBadge(row[key])}</div>
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
                        <div class="col-md-4">
                            <div class="meta-label">Nama Mesin</div>
                            <div class="meta-value">${row.nama_mesin ?? '-'}</div>
                        </div>
                        <div class="col-md-2">
                            <div class="meta-label">Tanggal</div>
                            <div class="meta-value">${fmtDate(row.tanggal)}</div>
                        </div>
                        <div class="col-md-2">
                            <div class="meta-label">Waktu</div>
                            <div class="meta-value">${row.waktu ?? '-'}</div>
                        </div>
                        <div class="col-md-2">
                            <div class="meta-label">Paket</div>
                            <div class="meta-value">${row.paket ?? '-'}</div>
                        </div>
                        <div class="col-md-2">
                            <div class="meta-label">Dibuat oleh</div>
                            <div class="meta-value">${row.user?.username ?? row.created_by ?? '-'}</div>
                        </div>
                    </div>

                    ${section('Electrical Motor', fields.motor)}
                    ${section('Pompa', fields.pompa)}
                    ${section('Aksesoris', fields.aksesoris)}
                    ${section('Gearbox', fields.gearbox)}

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                        <div class="group-title">Keterangan</div>
                        <div>${row.keterangan ?? '-'}</div>
                        </div>
                        <div class="col-md-6">
                        <div class="group-title">Tindakan Korektif</div>
                        <div>${row.korektif ?? '-'}</div>
                        </div>
                    </div>
                    `;
            }

            const dtMotorPump = $('#tblMotorPump').DataTable({
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
                        data: 'nama_mesin',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'tanggal',
                        render: function(data, type) {
                            if (type === 'display') return fmtDate(data);
                            return data; // sorting pakai ISO
                        }
                    },
                    {
                        data: 'waktu',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'paket',
                        defaultContent: '-',
                        orderable: false,
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(row) {
                            return summarize(row);
                        }
                    },
                    {
                        data: null,
                        render: function(row) {
                            return row.user?.username ?? row.created_by ?? '-';
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
                dtMotorPump.ajax.reload();
            });

            $('#btnReset').on('click', function(e) {
                e.preventDefault();
                $('#filterDate').val('');
                $('#filterPaket').val('');
                $('#filterNama').val('');
                dtMotorPump.ajax.reload();
            });

            // Detail modal
            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                const row = currentRows.find(x => x.id == id);
                if (!row) return;

                $('#detailTitle').text('Detail Inspeksi Mtc Motor Pump');
                $('#detailSub').text(`${fmtDate(row.tanggal)} - ${row.waktu ?? '-'}`);
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
                    const state = valToState(row[key]);
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

                // split by comma, tapi aman untuk spasi
                text.split(',').map(s => s.trim()).forEach(part => {
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
                    ${renderEditSection('Electrical Motor', fields.motor, row)}
                    ${renderEditSection('Pompa', fields.pompa, row)}
                    ${renderEditSection('Aksesoris', fields.aksesoris, row)}
                    ${renderEditSection('Gearbox', fields.gearbox, row)}
                `;
                $('#editSections').html(html);
            }

            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const row = currentRows.find(x => x.id == id);
                if (!row) return;

                $('#editId').val(row.id);
                $('#editNamaMesin').val(row.nama_mesin ?? '');
                $('#editTanggal').val(toDateInputValue(row.tanggal));
                $('#editWaktu').val((row.waktu ?? '').toString().slice(0, 5));
                $('#editPaket').val(row.paket ?? '');
                // $('#editKeterangan').val(row.keterangan ?? '');
                $('#editKorektif').val(row.korektif ?? '');

                $('#editSub').text(`${fmtDate(row.tanggal)} • ${row.waktu ?? '-'}`);

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
                    detailString: pairs.join(', ')
                };
            }

            $('#formEditMotorPump').on('submit', function(e) {
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
                        $('#tblMotorPump').DataTable().ajax.reload(null, false);
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
                    text: `Data #${id} akan dihapus permanen`,
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
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data derhasil dihapus',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            dtMotorPump.ajax.reload(null,
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
