@extends('layouts.app')

@section('title', 'Data Check Mtc Diesel P2H')

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
                                <input type="text" class="form-control" name="nama_mesin" id="editNamaMesin" required>
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
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-select" id="editShift">
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>

                        <div id="editDetails"><!-- injected checklist items --></div>

                        {{-- <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Rekomendasi</label>
                                <textarea class="form-control" name="rekomendasi" id="editNoUnit" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Korektif</label>
                                <textarea class="form-control" name="korektif" id="editKorektif" rows="2"></textarea>
                            </div>
                        </div> --}}
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
            const API_URL = "{{ url('api/mtc/diesel-p2h/get-data') }}";
            const DELETE_URL = "{{ url('mtc/data/diesel-p2h/delete') }}";
            const UPDATE_URL = "{{ url('mtc/data/diesel-p2h/update') }}";

            let currentRows = [];

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

            function statusBadge(kondisi) {
                if (kondisi === true || kondisi === 1 || kondisi === "1")
                    return `<span class="badge bg-success">OK</span>`;
                if (kondisi === false || kondisi === 0 || kondisi === "0")
                    return `<span class="badge bg-danger">NOK</span>`;
                return `<span class="badge bg-secondary">No Check</span>`;
            }

            function summarize(details) {
                let ok = 0,
                    ng = 0,
                    nu = 0;
                details.forEach(d => {
                    if (d.kondisi === true || d.kondisi === 1) ok++;
                    else if (d.kondisi === false || d.kondisi === 0) ng++;
                    else nu++;
                });
                return `
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge badge-soft-success">YA: ${ok}</span>
                        <span class="badge badge-soft-danger">TIDAK: ${ng}</span>
                        <span class="badge badge-soft-secondary">No Check: ${nu}</span>
                    </div>
                `;
            }

            function buildDetailHTML(row) {
                const detailsHtml = row.details?.map(d => `
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body p-3">
                                <h6 class="card-title mb-1 fw-bold">
                                    ${d.item?.item_pengecekan || '-'}
                                </h6>
                                <small class="text-muted d-block mb-2">${d.item?.kondisi_normal || '-'}</small>

                                <div class="d-flex justify-content-start align-items-center mb-2">
                                    ${statusBadge(d.kondisi)}
                                </div>

                                ${
                                    d.kondisi === false
                                    ? `<div class="small text-muted border-top pt-2 mt-2"><strong>Keterangan:</strong> ${d.keterangan || '-'}</div>`
                                    : ''
                                }
                            </div>
                        </div>
                    </div>
                `).join('');

                return `
                    <div class="detail-meta row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="meta-label">Tanggal</div>
                            <div class="meta-value">${fmtDate(row.tanggal)}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="meta-label">Nama Mesin</div>
                            <div class="meta-value">${fmtDate(row.nama_mesin)}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="meta-label">No Unit</div>
                            <div class="meta-value">${row.no_unit ?? '-'}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="meta-label">Shift</div>
                            <div class="meta-value">${row.shift ?? '-'}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="meta-label">Departemen</div>
                            <div class="meta-value">${row.departemen ?? '-'}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="meta-label">Dibuat oleh</div>
                            <div class="meta-value">${row.created_by?.username ?? '-'}</div>
                        </div>
                    </div>

                    <div class="group-title mt-4 mb-3">Hasil Pengecekan</div>
                    <div class="row g-3">${detailsHtml}</div>
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
                        data: 'nama_mesin',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'no_unit',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'departemen',
                        orderable: false,
                        defaultContent: '-'
                    },
                    {
                        data: 'shift',
                        orderable: false,
                        defaultContent: '-'
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

            // Edit modal (sesuaikan field)
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const row = currentRows.find(x => x.id == id);
                if (!row) return;

                $('#editId').val(row.id);
                $('#editNamaMesin').val(row.nama_mesin ?? '');
                $('#editTanggal').val(row.tanggal ? row.tanggal.split('T')[0] : '');
                $('#editDepartemen').val(row.departemen ?? '');
                $('#editNoUnit').val(row.no_unit ?? '');
                $('#editShift').val(row.shift ?? '');

                $('#editSub').text(fmtDate(row.tanggal));

                const editHtml = row.details.map(d => `
                    <div class="col-md-6 col-lg-4">
                        <div class="item-edit" data-item-id="${d.item_id}">
                            <input type="hidden" name="items[${d.item_id}][item_id]" value="${d.item_id}">

                            <div class="item-label">
                                ${d.item?.item_pengecekan}
                            </div>

                            <div class="btn-group btn-group-sm status-3 mt-2" role="group">

                                <input type="radio" class="btn-check edit-radio"
                                    name="items[${d.item_id}][kondisi]"
                                    id="kondisi_${d.item_id}_null"
                                    value=""
                                    ${d.kondisi === null ? 'checked' : ''}>
                                <label class="btn btn-outline-secondary" for="kondisi_${d.item_id}_null">No Check</label>

                                <input type="radio" class="btn-check edit-radio"
                                    name="items[${d.item_id}][kondisi]"
                                    id="kondisi_${d.item_id}_ok"
                                    value="1" ${d.kondisi === true ? 'checked' : ''}>
                                <label class="btn btn-outline-success" for="kondisi_${d.item_id}_ok">YA</label>

                                <input type="radio" class="btn-check edit-radio"
                                    name="items[${d.item_id}][kondisi]"
                                    id="kondisi_${d.item_id}_ng"
                                    value="0" ${d.kondisi === false ? 'checked' : ''}>
                                <label class="btn btn-outline-danger" for="kondisi_${d.item_id}_ng">TIDAK</label>
                            </div>

                            <div class="mt-2 ket-wrap ${d.kondisi === false ? '' : 'd-none'}">
                                <textarea class="form-control form-control-sm"
                                    name="items[${d.item_id}][keterangan]"
                                    placeholder="Keterangan wajib jika TIDAK">${d.keterangan ?? ''}</textarea>
                            </div>
                        </div>
                    </div>
                `).join('');

                $('#editDetails').html(`<div class="row g-3">${editHtml}</div>`);

                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });


            // Logic radio change di edit modal
            $(document).on('change', '.edit-radio', function() {
                const $wrap = $(this).closest('.item-edit').find('.ket-wrap');
                const val = $(this).val();
                if (val === '0') {
                    $wrap.removeClass('d-none');
                    $wrap.find('textarea').prop('required', true);
                } else {
                    $wrap.addClass('d-none');
                    $wrap.find('textarea').prop('required', false).val('').removeClass('is-invalid');
                }
            });

            // Submit edit
            $('#formEditDieselP2h').on('submit', function(e) {
                e.preventDefault();

                const id = $('#editId').val();
                const $btn = $('#btnSaveEdit');
                const $spin = $('#spinEdit');

                $btn.prop('disabled', true);
                $spin.removeClass('d-none');

                let valid = true;
                $('.ket-wrap:not(.d-none)').each(function() {
                    const $input = $(this).find('textarea');
                    if (!$input.val().trim()) {
                        $input.addClass('is-invalid');
                        valid = false;
                    } else {
                        $input.removeClass('is-invalid');
                    }
                });

                if (!valid) {
                    $btn.prop('disabled', false);
                    $spin.addClass('d-none');
                    return;
                }

                // Buat FormData
                const formData = new FormData(this);

                // Paksa kirim semua field header (ambil dari input modal)
                formData.set('tanggal', $('#editTanggal').val() || ''); // selalu kirim, meski kosong
                formData.set('departemen', $('#editDepartemen').val() || '');
                formData.set('no_unit', $('#editNoUnit').val() || '');
                formData.set('shift', $('#editShift').val() || '');

                // Pastikan details terkirim (sudah otomatis dari radio & textarea)

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
                    text: `Data inspeksi #${id} akan dihapus permanen`,
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
                        success: () => {
                            Swal.fire('Berhasil', 'Data dihapus', 'success');
                            dtDieselP2h.ajax.reload(null, false);
                        },
                        error: xhr => Swal.fire('Gagal', xhr.responseJSON?.message ||
                            'Gagal hapus', 'error')
                    });
                });
            });
        });
    </script>
@endsection
