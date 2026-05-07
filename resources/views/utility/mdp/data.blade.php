@extends('layouts.app')

@section('title', 'Rekap Pemantauan MDP')

@section('styles')
    <style>
        .card-header-custom {
            background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%);
            color: white;
            padding: 1.25rem;
            border-radius: 12px 12px 0 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #6c757d;
            border-top: none;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 text-white fw-bold">
                            <i class="ri-history-line me-2 text-warning"></i>
                            Rekap Pemantauan MDP
                        </h4>
                        <p class="mb-0 text-white-50 small">Engineering Utility · Riwayat Pemantauan Harian</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('mdp-monitoring.index') }}" class="btn btn-warning btn-sm rounded-pill px-3">
                            <i class="ri-add-line me-1"></i> Input Baru
                        </a>
                        <button class="btn btn-outline-light btn-sm rounded-pill px-3" id="btnExport">
                            <i class="ri-download-2-line me-1"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    {{-- Filter --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Bulan & Tahun</label>
                            <input type="month" id="filterBulan" class="form-control" value="{{ date('Y-m') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Status Approval</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">-- Semua Status --</option>
                                <option value="submitted">Submitted</option>
                                <option value="approved_foreman">Approved Foreman</option>
                                <option value="approved_supervisor">Approved Supervisor</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="btnFilter">
                                <i class="ri-filter-3-line me-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Operator</th>
                                    <th>E-Del (kWh)</th>
                                    <th>Arus (A)</th>
                                    <th>Tegangan (V)</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                {{-- Data via AJAX --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div id="paginationInfo" class="small text-muted"></div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Export Excel -->
    <div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="ri-file-excel-2-line me-1"></i> Export Excel MDP</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formExport" action="{{ route('mdp-monitoring.export') }}" method="GET" target="_blank">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bulan</label>
                                <select name="bulan" class="form-select">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}">
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0" style="font-size: 0.85rem;">
                            <i class="ri-information-line me-1"></i> Data akan diekspor menggunakan template Excel bulanan
                            yang tersedia.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formExport" class="btn btn-success px-4">
                        <i class="ri-download-cloud-2-line me-1"></i> Download Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom p-3 text-white bg-info">
                    <h5 class="modal-title fw-bold text-white">Detail Pemantauan MDP</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="detailContent">
                    {{-- Content via AJAX --}}
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white"><i class="ri-edit-line me-1"></i> Edit Data MDP</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEdit">
                        <input type="hidden" id="edit_id">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">E_Del (kWh)</label>
                                <input type="number" step="any" name="e_del" id="edit_e_del"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Arus Avg</label>
                                <input type="number" step="any" name="arus_rata_rata" id="edit_arus_rata_rata"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Tegangan Avg</label>
                                <input type="number" step="any" name="tegangan_rata_rata"
                                    id="edit_tegangan_rata_rata" class="form-control">
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Arus I1</label>
                                <input type="number" step="any" name="arus_i1" id="edit_arus_i1"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Arus I2</label>
                                <input type="number" step="any" name="arus_i2" id="edit_arus_i2"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Arus I3</label>
                                <input type="number" step="any" name="arus_i3" id="edit_arus_i3"
                                    class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Tegangan V1</label>
                                <input type="number" step="any" name="tegangan_v1" id="edit_tegangan_v1"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Tegangan V2</label>
                                <input type="number" step="any" name="tegangan_v2" id="edit_tegangan_v2"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Tegangan V3</label>
                                <input type="number" step="any" name="tegangan_v3" id="edit_tegangan_v3"
                                    class="form-control">
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Daya Total (kW)</label>
                                <input type="number" step="any" name="daya_total" id="edit_daya_total"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Temp Trafo (°C)</label>
                                <input type="number" step="any" name="temperatur_transformator"
                                    id="edit_temperatur_transformator" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Level Oil</label>
                                <select name="level_oil" id="edit_level_oil" class="form-select">
                                    <option value="ok">OK</option>
                                    <option value="nok">NOK</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnUpdate">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            loadData(1);

            $('#btnFilter').click(function() {
                loadData(1);
            });

            function loadData(page) {

                const params = {
                    bulan: $('#filterBulan').val(),
                    status: $('#filterStatus').val(),
                    page: page
                };

                $.get("{{ route('mdp-monitoring.json') }}", params, function(res) {
                    renderTable(res.data, res.pagination);
                    renderPagination(res.pagination);

                });
            }

            function renderTable(data, pagination) {
                let html = '';
                if (data.length === 0) {
                    html =
                        '<tr><td colspan="9" class="text-center py-5 text-muted">Tidak ada data ditemukan</td></tr>';
                } else {
                    const formatNum = (v) => v ? Number(v) : '-';
                    data.forEach((item, index) => {
                        const no = (pagination.current_page - 1) * pagination.per_page + index + 1;

                        let actionButtons = `
                            <button class="btn btn-sm btn-outline-info btn-detail"
                                data-id="${item.id}" title="Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                        `;

                        if (item.status === 'submitted') {
                            actionButtons += `
                                <button class="btn btn-sm btn-outline-primary btn-edit"
                                    data-id="${item.id}" title="Edit">
                                    <i class="ri-edit-line"></i>
                                </button>

                                <button class="btn btn-sm btn-outline-danger btn-delete"
                                    data-id="${item.id}" title="Hapus">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            `;
                        } else {
                            actionButtons += `
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    <i class="ri-lock-line"></i>
                                </button>
                            `;
                        }

                        html += `
                        <tr>
                            <td>${no}</td>
                            <td>${formatDate(item.tanggal_laporan)}</td>
                            <td>${item.jam_pencatatan}</td>
                            <td>${item.operator?.username || '-'}</td>
                            <td>${formatNum(item.e_del)}</td>
                            <td>${formatNum(item.arus_rata_rata)}</td>
                            <td>${formatNum(item.tegangan_rata_rata)}</td>
                            <td>${getStatusBadge(item.status)}</td>
                            <td class="text-center">
                                <div>
                                    ${actionButtons}
                                </div>
                            </td>
                        </tr>
                    `;
                    });
                }
                $('#tableBody').html(html);
            }

            function renderPagination(pagination) {
                let html = '';
                if (pagination.last_page > 1) {
                    for (let i = 1; i <= pagination.last_page; i++) {
                        html += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                    }
                }
                $('#paginationLinks').html(html);
                $('#paginationInfo').text(`Showing data ${dataRange(pagination)}`);
            }

            function dataRange(p) {
                if (p.total === 0) return '0';
                const start = (p.current_page - 1) * p.per_page + 1;
                const end = Math.min(p.current_page * p.per_page, p.total);
                return `${start} - ${end} of ${p.total}`;
            }

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                loadData($(this).data('page'));
            });

            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                showDetail(id);
            });

            function showDetail(id) {

                $.get("{{ url('utility/mdp-monitoring/json') }}/" + id, function(res) {
                    const d = res.data;
                    const html = `
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Umum</h6>
                                <table class="table table-sm table-borderless">
                                    <tr><td class="text-muted" width="40%">Tanggal</td><td>: ${formatDate(d.tanggal_laporan)}</td></tr>
                                    <tr><td class="text-muted">Jam</td><td>: ${d.jam_pencatatan}</td></tr>
                                    <tr><td class="text-muted">Operator</td><td>: ${d.operator?.username || '-'}</td></tr>
                                    <tr><td class="text-muted">Status</td><td>: ${getStatusBadge(d.status)}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold border-bottom pb-2 mb-3">Approval</h6>
                                <table class="table table-sm table-borderless">
                                    <tr><td class="text-muted" width="40%">Foreman</td><td>: ${d.foreman?.username || '-'}</td></tr>
                                    <tr><td class="text-muted">Supervisor</td><td>: ${d.supervisor?.username || '-'}</td></tr>
                                    ${d.reject_reason ? `<tr class="text-danger"><td class="text-muted">Alasan Reject</td><td>: ${d.reject_reason}</td></tr>` : ''}
                                </table>
                            </div>
                            <div class="col-12">
                                <h6 class="fw-bold border-bottom pb-2 mb-3">Data Teknis</h6>
                                <div class="row g-3">
                                    ${renderTechnicalItem('E-Del', d.e_del, 'kWh')}
                                    ${renderTechnicalItem('Arus Avg', d.arus_rata_rata, 'A')}
                                    ${renderTechnicalItem('Arus I1', d.arus_i1, 'A')}
                                    ${renderTechnicalItem('Arus I2', d.arus_i2, 'A')}
                                    ${renderTechnicalItem('Arus I3', d.arus_i3, 'A')}
                                    ${renderTechnicalItem('Volt Avg', d.tegangan_rata_rata, 'V')}
                                    ${renderTechnicalItem('Volt V1', d.tegangan_v1, 'V')}
                                    ${renderTechnicalItem('Volt V2', d.tegangan_v2, 'V')}
                                    ${renderTechnicalItem('Volt V3', d.tegangan_v3, 'V')}
                                    ${renderTechnicalItem('Daya Total', d.daya_total, 'kW')}
                                    ${renderTechnicalItem('Daya P1', d.daya_p1, 'kW')}
                                    ${renderTechnicalItem('Daya P2', d.daya_p2, 'kW')}
                                    ${renderTechnicalItem('Daya P3', d.daya_p3, 'kW')}
                                    ${renderTechnicalItem('Temp Trafo', d.temperatur_transformator, '°C')}
                                    ${renderTechnicalItem('Level Oil', d.level_oil.toUpperCase(), '')}
                                </div>
                            </div>
                        </div>
                    `;
                    $('#detailContent').html(html);
                    $('#modalDetail').modal('show');

                });
            }

            function renderTechnicalItem(label, value, unit) {

                const formatNum = (v) => {
                    if (v === null || v === undefined || v === '') {
                        return '-';
                    }

                    return isNaN(v) ? v : Number(v);
                };

                return `
                    <div class="col-md-3 col-6">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">${label}</small>
                            <span class="fw-bold">${formatNum(value)}</span> <small>${unit}</small>
                        </div>
                    </div>
                `;
            }

            function getStatusBadge(status) {
                const badges = {
                    'submitted': '<span class="badge bg-info">Submitted</span>',
                    'approved_foreman': '<span class="badge bg-warning text-dark">Appr Foreman</span>',
                    'approved_supervisor': '<span class="badge bg-success">Approved</span>',
                    'rejected': '<span class="badge bg-danger">Rejected</span>'
                };
                return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
            }

            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const formatNum = (v) => v ? Number(v) : '-';

                $.get("{{ url('utility/mdp-monitoring/json') }}/" + id, function(res) {
                    const d = res.data;
                    $('#edit_id').val(d.id);
                    $('#edit_e_del').val(formatNum(d.e_del));
                    $('#edit_arus_rata_rata').val(formatNum(d.arus_rata_rata));
                    $('#edit_arus_i1').val(formatNum(d.arus_i1));
                    $('#edit_arus_i2').val(formatNum(d.arus_i2));
                    $('#edit_arus_i3').val(formatNum(d.arus_i3));
                    $('#edit_tegangan_rata_rata').val(formatNum(d.tegangan_rata_rata));
                    $('#edit_tegangan_v1').val(formatNum(d.tegangan_v1));
                    $('#edit_tegangan_v2').val(formatNum(d.tegangan_v2));
                    $('#edit_tegangan_v3').val(formatNum(d.tegangan_v3));
                    $('#edit_daya_total').val(formatNum(d.daya_total));
                    $('#edit_temperatur_transformator').val(formatNum(d.temperatur_transformator));
                    $('#edit_level_oil').val(d.level_oil);
                    $('#modalEdit').modal('show');
                });
            });

            $('#btnUpdate').click(function() {
                const id = $('#edit_id').val();
                const formData = $('#formEdit').serialize();

                Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: "Data MDP akan diperbarui.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: "{{ url('utility/mdp-monitoring/update') }}/" + id,
                            method: 'PUT',
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            success: function(res) {
                                $('#modalEdit').modal('hide');
                                Swal.fire('Berhasil', res.message, 'success');
                                loadData(1);
                            },
                            error: function(xhr) {

                                Swal.fire('Error', xhr.responseJSON.message ||
                                    'Gagal update data', 'error');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: "{{ url('utility/mdp-monitoring') }}/" + id,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            success: function(res) {
                                Swal.fire('Terhapus!', res.message, 'success');
                                loadData(1);
                            },
                            error: function(xhr) {

                                Swal.fire('Error', xhr.responseJSON.message ||
                                    'Gagal menghapus data', 'error');
                            }
                        });
                    }
                });
            });

            function formatDate(dateString) {
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            }

            // ── Export ──
            $('#btnExport').on('click', function() {
                $('#modalExport').modal('show');
            });
        });
    </script>
@endsection
