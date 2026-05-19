@extends('layouts.app')

@section('title', 'Approval Pemantauan MDP')

@section('styles')
<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header-custom">
                <h4 class="mb-1 text-white fw-bold">
                    <i class="ri-checkbox-circle-line me-2 text-warning"></i>
                    Approval Pemantauan MDP
                </h4>
                <p class="mb-0 text-white-50 small">Engineering Utility · Persetujuan Laporan Harian</p>
            </div>
            <div class="card-body p-4">
                <div id="bulkActionContainer" class="mb-3 d-none">
                    <div class="d-flex align-items-center bg-light p-3 rounded-3 border">
                        <span class="me-auto fw-medium"><span id="selectedCount">0</span> item terpilih</span>
                        <button class="btn btn-success btn-sm me-2 px-3" id="btnBulkApprove">
                            <i class="ri-checkbox-circle-line me-1"></i> Approve Selected
                        </button>
                        <button class="btn btn-danger btn-sm px-3" id="btnBulkReject">
                            <i class="ri-close-circle-line me-1"></i> Reject Selected
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Operator</th>
                                <th>E-Del (kWh)</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                                    Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Detail & Action --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-3">
                <h5 class="modal-title fw-bold">Detail Laporan MDP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        loadData();

        function loadData() {
            $('#loadingOverlay').removeClass('d-none');
            $.get("{{ route('mdp-monitoring.json') }}", {
                mode: 'approval'
            }, function(res) {
                renderTable(res.data);
                $('#loadingOverlay').addClass('d-none');
            });
        }

        function renderTable(data) {
            let html = '';
            if (data.length === 0) {
                html = '<tr><td colspan="8" class="text-center py-5 text-muted"><i class="ri-check-line text-success fs-3 d-block mb-2"></i>Semua laporan sudah diproses.</td></tr>';
            } else {
                data.forEach((item, index) => {
                    html += `
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input check-item" type="checkbox" value="${item.id}">
                                </div>
                            </td>
                            <td>${index + 1}</td>
                            <td>${formatDate(item.tanggal_laporan)}</td>
                            <td>${item.jam_pencatatan}</td>
                            <td>${item.operator?.username || '-'}</td>
                            <td>${item.e_del || '-'}</td>
                            <td>${getStatusBadge(item.status)}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary rounded-pill px-3 btn-detail me-1" data-id="${item.id}">
                                    <i class="ri-eye-line me-1"></i> Periksa
                                </button>
                                <button class="btn btn-sm btn-success rounded-pill px-3 btn-approve me-1" data-id="${item.id}" data-status="${item.status}">
                                    <i class="ri-check-line me-1"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-danger rounded-pill px-3 btn-reject" data-id="${item.id}">
                                    <i class="ri-close-line me-1"></i> Reject
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#tableBody').html(html);
            updateBulkUI();
        }

        function updateBulkUI() {
            const checkedCount = $('.check-item:checked').length;
            const totalCount = $('.check-item').length;

            $('#selectedCount').text(checkedCount);
            if (checkedCount > 0) {
                $('#bulkActionContainer').removeClass('d-none');
            } else {
                $('#bulkActionContainer').addClass('d-none');
            }

            $('#selectAll').prop('checked', checkedCount === totalCount && totalCount > 0);
        }

        $(document).on('change', '#selectAll', function() {
            $('.check-item').prop('checked', $(this).is(':checked'));
            updateBulkUI();
        });

        $(document).on('change', '.check-item', function() {
            updateBulkUI();
        });

        $('#btnBulkApprove').click(function() {
            const ids = $('.check-item:checked').map(function() {
                return $(this).val();
            }).get();

            Swal.fire({
                title: 'Approve Massal?',
                text: `Anda akan menyetujui ${ids.length} laporan sekaligus.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loadingOverlay').removeClass('d-none');
                    $.post("{{ route('mdp-monitoring.bulk-approve') }}", {
                        _token: "{{ csrf_token() }}",
                        ids: ids
                    }, function(res) {
                        Swal.fire('Berhasil', res.message, 'success');
                        loadData();
                    }).fail(function(err) {
                        Swal.fire('Gagal', err.responseJSON?.message || 'Terjadi kesalahan', 'error');
                        $('#loadingOverlay').addClass('d-none');
                    });
                }
            });
        });

        $('#btnBulkReject').click(function() {
            const ids = $('.check-item:checked').map(function() {
                return $(this).val();
            }).get();

            Swal.fire({
                title: 'Tolak Massal',
                text: `Anda akan menolak ${ids.length} laporan sekaligus.`,
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Tulis alasan...',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak Semua',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $('#loadingOverlay').removeClass('d-none');
                    $.post("{{ route('mdp-monitoring.bulk-reject') }}", {
                        _token: "{{ csrf_token() }}",
                        ids: ids,
                        reason: result.value
                    }, function(res) {
                        Swal.fire('Ditolak', res.message, 'info');
                        loadData();
                    }).fail(function(err) {
                        Swal.fire('Gagal', err.responseJSON?.message || 'Terjadi kesalahan', 'error');
                        $('#loadingOverlay').addClass('d-none');
                    });
                }
            });
        });

        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            showDetail(id);
        });

        function showDetail(id) {
            $('#loadingOverlay').removeClass('d-none');
            $.get("{{ url('utility/mdp-monitoring/json') }}/" + id, function(res) {
                const d = res.data;
                const html = `
                    <div class="row g-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold border-bottom pb-2 mb-3">Data Pemantauan MDP</h6>
                            <div class="row g-3">
                                ${renderTechnicalItem('E-Del', d.e_del, 'kWh')}
                                ${renderTechnicalItem('Arus Avg', d.arus_rata_rata, 'A')}
                                ${renderTechnicalItem('Volt Avg', d.tegangan_rata_rata, 'V')}
                                ${renderTechnicalItem('Daya Total', d.daya_total, 'kW')}
                                ${renderTechnicalItem('Arus I1', d.arus_i1, 'A')}
                                ${renderTechnicalItem('Arus I2', d.arus_i2, 'A')}
                                ${renderTechnicalItem('Arus I3', d.arus_i3, 'A')}
                                ${renderTechnicalItem('Volt V1', d.tegangan_v1, 'V')}
                                ${renderTechnicalItem('Volt V2', d.tegangan_v2, 'V')}
                                ${renderTechnicalItem('Volt V3', d.tegangan_v3, 'V')}
                                ${renderTechnicalItem('Temp Trafo', d.temperatur_transformator, '°C')}
                                ${renderTechnicalItem('Level Oil', d.level_oil?.toUpperCase(), '')}
                            </div>
                        </div>
                    </div>
                `;
                $('#detailContent').html(html);

                $('#modalDetail').modal('show');
                $('#loadingOverlay').addClass('d-none');
            });
        }

        function renderTechnicalItem(label, value, unit) {
            return `
                <div class="col-md-3 col-6">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block">${label}</small>
                        <span class="fw-bold">${value || '-'}</span> <small>${unit}</small>
                    </div>
                </div>
            `;
        }

        $(document).on('click', '.btn-approve', function() {
            const id = $(this).data('id');
            const status = $(this).data('status');
            const url = status === 'submitted' ?
                "{{ url('utility/mdp-monitoring/approve-foreman') }}/" + id :
                "{{ url('utility/mdp-monitoring/approve-supervisor') }}/" + id;

            Swal.fire({
                title: 'Setujui Laporan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(url, {
                        _token: "{{ csrf_token() }}"
                    }, function(res) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#modalDetail').modal('hide');
                        loadData();
                    });
                }
            });
        });

        $(document).on('click', '.btn-reject', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Tolak Laporan',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Tulis alasan...',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $.post("{{ url('utility/mdp-monitoring/reject') }}/" + id, {
                        _token: "{{ csrf_token() }}",
                        reason: result.value
                    }, function(res) {
                        Swal.fire('Ditolak', res.message, 'info');
                        $('#modalDetail').modal('hide');
                        loadData();
                    });
                }
            });
        });

        function getStatusBadge(status) {
            const badges = {
                'submitted': '<span class="badge bg-info text-white">Menunggu Foreman</span>',
                'approved_foreman': '<span class="badge bg-warning text-dark">Menunggu Supervisor</span>',
            };
            return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
        }

        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }
    });
</script>
@endsection