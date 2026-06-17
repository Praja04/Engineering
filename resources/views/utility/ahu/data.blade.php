@extends('layouts.app')

@section('title', 'Data Rekap AHU')

@section('styles')
    <style>
        [data-layout-mode="dark"] .ahu-detail-card {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        [data-layout-mode="dark"] .ahu-detail-card h6 {
            color: #f3f3f3;
        }

        [data-layout-mode="dark"] .ahu-detail-card small {
            color: #adb5bd !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card border-0 shadow-sm mb-3"
                style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%); border-radius: 12px;">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="text-white fw-bold mb-1">
                            <i class="ri-database-2-line text-success me-2"></i> AHU - Rekap Data
                        </h4>
                        <p class="text-white-50 mb-0">Daftar log harian Air Handling Unit</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('ahu.index') }}" class="btn btn-warning btn-sm rounded-pill px-3">
                            <i class="ri-add-line me-1"></i> Input
                        </a>
                        <button class="btn btn-outline-light btn-sm rounded-pill px-3" id="btnExport">
                            <i class="ri-download-2-line me-1"></i> Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter Bulan</label>
                            <input type="month" id="filter_bulan" class="form-control" value="{{ date('Y-m') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100" onclick="loadData(1)">
                                <i class="ri-filter-3-line me-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if (auth()->user()->jabatan === 'foreman')
                <div id="collectedMonthlyContainer"></div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th class="text-center">AHU 1 (Amp/Temp)</th>
                                    <th class="text-center">AHU 2 (Amp/Temp)</th>
                                    <th class="text-center">AHU 3 (Amp/Temp)</th>
                                    <th class="text-center">AHU 4 (Amp/Temp)</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyData"></tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div id="paginationInfo"></div>
                        <nav>
                            <ul class="pagination mb-0" id="paginationLinks"></ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">
                        <i class="ri-file-list-3-line me-2"></i>
                        Detail Monitoring AHU
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="detailContent">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Submit Monthly -->
    <div class="modal fade" id="modalSubmitMonthly" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formSubmitMonthly" class="modal-content">
                @csrf
                <input type="hidden" name="bulan" id="sm_bulan">
                <input type="hidden" name="tahun" id="sm_tahun">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Approval Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Supervisor</label>
                        <select name="supervisor_id" id="sm_supervisor_id" class="form-select" required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning w-100">Kirim Approval</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form id="formEdit" class="modal-content">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Data AHU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jam</label>
                            <input type="text" name="jam" id="edit_jam" class="form-control" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        @foreach ([1, 2, 3, 4] as $i)
                            <div class="col-md-6">
                                <div class="card border shadow-none bg-light bg-opacity-10">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-2">AHU {{ $i }}</h6>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="small mb-1">Ampere</label>
                                                <input type="number" step="0.01" name="ampere_{{ $i }}"
                                                    id="edit_ampere_{{ $i }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-4">
                                                <label class="small mb-1">Set Temp</label>
                                                <input type="number" step="0.01" name="set_temp_{{ $i }}"
                                                    id="edit_set_temp_{{ $i }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-4">
                                                <label class="small mb-1">Press In</label>
                                                <input type="number" step="0.01"
                                                    name="pressure_in_{{ $i }}"
                                                    id="edit_pressure_in_{{ $i }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-4">
                                                <label class="small mb-1">Press Out</label>
                                                <input type="number" step="0.01"
                                                    name="pressure_out_{{ $i }}"
                                                    id="edit_pressure_out_{{ $i }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-4">
                                                <label class="small mb-1">CT In</label>
                                                <input type="number" step="0.01" name="ct_in_{{ $i }}"
                                                    id="edit_ct_in_{{ $i }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-4">
                                                <label class="small mb-1">CT Out</label>
                                                <input type="number" step="0.01" name="ct_out_{{ $i }}"
                                                    id="edit_ct_out_{{ $i }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-12">
                                                <label class="small mb-1">Temp Out</label>
                                                <input type="number" step="0.01" name="temp_out_{{ $i }}"
                                                    id="edit_temp_out_{{ $i }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        const API_URL = "{{ route('ahu.get-data') }}";
        const EXPORT_URL = "{{ route('ahu.export') }}";

        function loadData(page = 1) {
            $.ajax({
                url: API_URL,
                type: "GET",
                data: {
                    bulan: $('#filter_bulan').val(),
                    page: page
                },
                success: function(res) {
                    let html = '';
                    const fN = (v) => v ? Number(v) : '-';
                    res.data.forEach(item => {
                        let st = '';
                        if (item.approval_status == 'draft') st =
                            '<span class="badge bg-warning">Draft</span>';
                        else if (item.approval_status == 'submitted') st =
                            '<span class="badge bg-info">Submitted</span>';
                        else if (item.approval_status == 'approved_foreman') st =
                            '<span class="badge bg-primary">Approve FM</span>';
                        else if (item.approval_status == 'approved_supervisor') st =
                            '<span class="badge bg-success">Approved</span>';
                        else if (item.approval_status == 'rejected') st =
                            '<span class="badge bg-danger">Rejected</span>';

                        html += `
                        <tr>
                            <td>${item.tanggal}</td>
                            <td>${item.jam.substring(0,5)}</td>
                            <td class="text-center">${fN(item.ampere_1)} / ${fN(item.temp_out_1)}</td>
                            <td class="text-center">${fN(item.ampere_2)} / ${fN(item.temp_out_2)}</td>
                            <td class="text-center">${fN(item.ampere_3)} / ${fN(item.temp_out_3)}</td>
                            <td class="text-center">${fN(item.ampere_4)} / ${fN(item.temp_out_4)}</td>
                            <td>${st}</td>
                            <td class="text-center">
                                <div>
                                    <button class="btn btn-sm btn-info" onclick="showDetail(${item.id})" title="Detail">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="editData(${item.id})" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteData(${item.id})" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    });
                    $('#tbodyData').html(html ||
                        '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
                    renderPagination(res.pagination, loadData);
                    loadCollected();
                }
            });
        }

        function renderPagination(pagination, callback) {
            let html = '';
            if (pagination && pagination.last_page > 1) {
                html += `<li class="page-item ${pagination.current_page == 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="${callback.name}(${pagination.current_page - 1})">Prev</a>
            </li>`;

                for (let i = 1; i <= pagination.last_page; i++) {
                    if (i == 1 || i == pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination
                            .current_page + 2)) {
                        html += `<li class="page-item ${pagination.current_page == i ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="${callback.name}(${i})">${i}</a>
                    </li>`;
                    } else if (i == pagination.current_page - 3 || i == pagination.current_page + 3) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                html += `<li class="page-item ${pagination.current_page == pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="${callback.name}(${pagination.current_page + 1})">Next</a>
            </li>`;
            }
            $('#paginationLinks').html(html);
            if (pagination) {
                $('#paginationInfo').html(
                    `Showing <b>${pagination.total > 0 ? (pagination.current_page - 1) * pagination.per_page + 1 : 0}</b> to <b>${Math.min(pagination.current_page * pagination.per_page, pagination.total)}</b> of <b>${pagination.total}</b> entries`
                );
            }
        }

        function loadCollected() {
            if ("{{ auth()->user()->jabatan }}" !== "foreman") {
                return;
            }
            $.get("{{ route('ahu.get-collected') }}", function(res) {
                let container = $('#collectedMonthlyContainer').empty();
                res.results.forEach(m => {
                    let app = m.approval;
                    let html = `
                    <div class="card shadow-sm border-warning mb-3">
                        <div class="card-header bg-soft-warning d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-warning-emphasis fw-bold">Data Terkumpul Bulan ${moment().month(app.bulan-1).format('MMMM')} ${app.tahun}</h6>
                            <button class="btn btn-sm btn-warning" onclick="openSubmitMonthly(${app.bulan}, ${app.tahun})">Submit Approval</button>
                        </div>
                    </div>
                `;
                    container.append(html);
                });
            });
        }

        function openSubmitMonthly(bulan, tahun) {
            $('#sm_bulan').val(bulan);
            $('#sm_tahun').val(tahun);
            $.get('/api/utility/users/approvers', function(data) {
                // Isi dropdown Supervisor — dari data.user (jabatan supervisor)
                const supervisorList = data.user ?? [];
                let supervisorOpts = '<option value="">— Pilih Supervisor —</option>';
                supervisorList.forEach(function(u) {
                    supervisorOpts += `<option value="${u.id}">${u.username}</option>`;
                });
                $('#sm_supervisor_id').html(supervisorOpts);
            }).fail(function() {
                $('#sm_supervisor_id').html('<option value="">Gagal memuat data</option>');
                toastr.error('Gagal memuat daftar approver');
            });

            $('#modalSubmitMonthly').modal('show');
        }

        $('#formSubmitMonthly').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('ahu.submit-monthly') }}", $(this).serialize(), function(res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#modalSubmitMonthly').modal('hide');
                loadData();
            });
        });

        function showDetail(id) {
            $.get("{{ url('utility/ahu/show') }}/" + id, function(res) {
                let d = res.data;

                const fN = (v) => {
                    if (v === null || v === undefined || v === '') return '-';
                    return Number(v).toLocaleString('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                };

                let html = '<div class="row g-3">';

                [1, 2, 3, 4].forEach(i => {
                    html += `
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <span><i class="ri-dashboard-3-line me-1"></i> AHU ${i}</span>
                                </div>

                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted">Ampere</td>
                                                <td class="fw-semibold text-end">${fN(d['ampere_'+i])} A</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Set Temperature</td>
                                                <td class="fw-semibold text-end">${fN(d['set_temp_'+i])} °C</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Pressure In</td>
                                                <td class="fw-semibold text-end">${fN(d['pressure_in_'+i])} Bar</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Pressure Out</td>
                                                <td class="fw-semibold text-end">${fN(d['pressure_out_'+i])} Bar</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">CT In</td>
                                                <td class="fw-semibold text-end">${fN(d['ct_in_'+i])} °C</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">CT Out</td>
                                                <td class="fw-semibold text-end">${fN(d['ct_out_'+i])} °C</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Temperature Out</td>
                                                <td class="fw-semibold text-end">${fN(d['temp_out_'+i])} °C</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        `;
                });

                html += '</div>';

                $('#detailContent').html(html);
                $('#modalDetail').modal('show');
            });
        }

        $('#btnExport').click(function() {
            const bulan = $('#filter_bulan').val();
            if (!bulan) return Swal.fire('Info', 'Pilih bulan terlebih dahulu', 'info');
            window.open(`${EXPORT_URL}?bulan=${bulan}`, '_blank');
        });

        function editData(id) {
            $.get("{{ url('utility/ahu/show') }}/" + id, function(res) {
                let d = res.data;
                const fN = (v) => v ? Number(v) : '';
                $('#edit_id').val(d.id);
                $('#edit_tanggal').val(d.tanggal.substring(0, 10));
                $('#edit_jam').val(d.jam.substring(0, 5));
                [1, 2, 3, 4].forEach(i => {
                    $(`#edit_ampere_${i}`).val(fN(d[`ampere_${i}`]));
                    $(`#edit_set_temp_${i}`).val(fN(d[`set_temp_${i}`]));
                    $(`#edit_pressure_in_${i}`).val(fN(d[`pressure_in_${i}`]));
                    $(`#edit_pressure_out_${i}`).val(fN(d[`pressure_out_${i}`]));
                    $(`#edit_ct_in_${i}`).val(fN(d[`ct_in_${i}`]));
                    $(`#edit_ct_out_${i}`).val(fN(d[`ct_out_${i}`]));
                    $(`#edit_temp_out_${i}`).val(fN(d[`temp_out_${i}`]));
                });
                $('#modalEdit').modal('show');
            });
        }

        $('#formEdit').submit(function(e) {
            e.preventDefault();
            let id = $('#edit_id').val();
            $.post("{{ url('utility/ahu/update') }}/" + id, $(this).serialize(), function(res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#modalEdit').modal('hide');
                loadData();
            });
        });

        function deleteData(id) {
            Swal.fire({
                title: 'Hapus data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('utility/ahu/destroy') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire('Dihapus', res.message, 'success');
                            loadData();
                        }
                    });
                }
            });
        }

        $(document).ready(() => {
            loadData();
            flatpickr('#edit_jam', {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
                minuteIncrement: 1,
                allowInput: false,
            });
        });
    </script>
@endsection
