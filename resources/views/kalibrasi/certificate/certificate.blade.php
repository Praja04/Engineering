@extends('layouts.app')

@section('title', 'Certificate Kalibrasi')

@section('styles')
    <style>
        .icon-wrapper {
            width: 32px;
            height: 32px;
        }

        #certificateTable tbody tr:hover {
            background-color: #f8f9fa !important;
            transition: 0.2s;
        }

        #certificateTable tr.collapse td {
            border-top: none !important;
            padding: 0 !important;
            transition: padding 0.3s ease, background-color 0.3s ease;
        }

        /* Wrapper dengan border fix biar ga telat muncul */
        .detail-wrapper {
            /* Hapus border-radius dari sini */
            background: #f9fafb;
            border-left: 3px solid #0d6efd;
            height: 100%;
            /* Tambahkan sedikit padding horizontal di wrapper, bukan di td */
            padding: 0 1rem;
        }

        .detail-content {
            opacity: 0;
            opacity: 0;
            transition: opacity 0.2s ease;
            transition-delay: 0.1s;
        }

        /* Saat collapse terbuka → konten fade-in */
        .collapse.show .detail-content {
            opacity: 1;
            transition-delay: 0s;
        }

        #certificateTable .collapse h6 {
            font-size: 0.9rem;
            color: #495057;
        }

        #certificateTable .collapse .row>div {
            padding: 4px 8px;
            border-bottom: 1px dashed #e9ecef;
        }

        #certificateTable .collapse .row>div:last-child {
            border-bottom: none;
        }

        #certificateTable tr.collapse.show td {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0 1rem 0 !important;
        }

        /* Hilangkan animasi buka/tutup Bootstrap Collapse */
        #certificateTable .collapse {
            transition: none !important;
            height: auto !important;
        }

        /* Jaga agar konten tetap muncul instant tanpa delay fade */
        #certificateTable .detail-content {
            transition: none !important;
            opacity: 1 !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row ">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Certificate Management</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Certificates</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card shadow-sm rounded-3 mb-3" data-aos="fade-up">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <!-- Filter Tanggal -->
                        <div class="col-md-4">
                            <label for="filterTanggal" class="form-label fw-semibold">Tanggal Kalibrasi</label>
                            <input type="date" id="filterTanggal" name="tanggal" class="form-control">
                        </div>

                        <!-- Filter Jenis -->
                        <div class="col-md-4">
                            <label for="filterJenis" class="form-label fw-semibold">Jenis Kalibrasi</label>
                            <select id="filterJenis" name="jenis" class="form-select">
                                <option value="">Semua Jenis</option>
                                <!-- nanti opsi dinamis dari backend -->
                            </select>
                        </div>

                        <!-- Tombol Reset -->
                        <div class="col-md-4">
                            <button id="btnResetFilter" class="btn btn-outline-primary w-100">
                                <i class="mdi mdi-refresh me-1"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Certificate List -->
            <div class="card shadow-sm rounded-3" data-aos="fade-up">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-semibold">Daftar Sertifikat Kalibrasi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="certificateTable"
                            class="table table-hover table-striped table-borderless align-middle text-nowrap"
                            style="display: none;">
                            <thead class="table-info">
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>Kode Alat</th>
                                    <th>Jenis Kalibrasi</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal approval --}}
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <form id="approvalForm">
                @csrf
                <input type="hidden" id="sertifikatId" name="sertifikat_id">
                <div class="modal-content shadow-lg rounded-3">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Approver</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Manager</label>
                            <select name="manager_id" id="managerSelect" class="form-select">
                                <option value="" selected disabled> Pilih Manager </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Supervisor</label>
                            <select name="supervisor_id" id="supervisorSelect" class="form-select">
                                <option value="" selected disabled> Pilih Supervisor </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foreman</label>
                            <select name="foreman_id" id="foremanSelect" class="form-select">
                                <option value="" selected disabled> Pilih Foreman </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select name="user_id" id="userSelect" class="form-select">
                                <option value="" selected disabled> Pilih User </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><span class="mdi mdi-send me-2"></span>Send
                            Request</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            loadData();
            let jenisLoaded = false;

            function loadData(filters = {}) {
                $.ajax({
                    url: "/kalibrasi/certificate/data",
                    type: "GET",
                    data: filters,
                    dataType: "json",
                    success: function(response) {
                        let tbody = $("#tableBody");
                        tbody.empty();

                        // === Isi dropdown filter hanya sekali ===
                        if (!jenisLoaded && response.filterOptions?.jenis_kalibrasi) {
                            const select = $('#filterJenis');
                            const jenisOptions = response.filterOptions.jenis_kalibrasi;

                            select.empty().append('<option value="">Semua Jenis</option>');

                            jenisOptions.forEach(opt => {
                                // Format jadi Capitalized
                                const formatted = opt
                                    .toLowerCase()
                                    .split(' ')
                                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                                    .join(' ');

                                select.append(`<option value="${opt}">${formatted}</option>`);
                            });

                            jenisLoaded = true;
                        }


                        const userRole = response.role || ''; // ambil role dari backend

                        if (!response.data || response.data.length === 0) {
                            tbody.append(`
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center text-muted">
                                            <i class="mdi mdi-file-search-outline display-4 mb-2"></i>
                                            <p class="fw-semibold mb-1">Tidak ada data sertifikat</p>
                                            <small>Silakan ubah filter atau cek kembali data Anda</small>
                                        </div>
                                    </td>
                                </tr>
                            `);
                            $("#certificateTable").fadeIn();
                            return;
                        }

                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(index, item) {
                                const statusSertifikat = item.certificate?.status;
                                let statusBadge = getStatusBadge(statusSertifikat);
                                let rowId = `collapseRow-${item.id}`;
                                let keteranganText = '-';
                                let actionButtons = '';

                                // Cek kalau semua approval sudah approved
                                const allApproved = item.certificate?.approvals?.length > 0 &&
                                    item.certificate.approvals.every(a => a.status ===
                                        'approved');

                                // Kalau sudah approved, tampilkan tombol Download Sertifikat
                                if (allApproved) {
                                    actionButtons += `
                                        <a href="/kalibrasi/certificate/download/${item.certificate?.id}" 
                                        target="_blank" 
                                        class="btn btn-outline-success btn-sm">
                                            <i class="mdi mdi-file-download-outline me-1"></i> Download
                                        </a>
                                    `;
                                }

                                if (userRole === 'foreman' && !allApproved &&
                                    statusSertifikat === 'draft') {
                                    actionButtons += `
                                       <button class="btn btn-outline-primary btn-sm req-approval-btn" 
                                            data-id="${item.certificate?.id}">
                                            <i class="mdi mdi-send-check-outline me-1"></i>Request Approval
                                        </button>
                                    `;

                                    keteranganText = `
                                        <button class="btn btn-secondary btn-sm d-flex align-items-center gap-1" disabled>
                                                <i class="mdi mdi-timer-sand"></i> Siap Diajukan
                                            </button>
                                    `;
                                }


                                if (['pending', 'approved', 'rejected'].includes(
                                        statusSertifikat)) {
                                    let approvalsHtml = '';

                                    if (item.certificate?.approvals?.length > 0) {
                                        // Buat isi list dulu
                                        const approvalList = item.certificate.approvals.map(
                                            app => {
                                                const badgeClass =
                                                    app.status === 'approved' ?
                                                    'bg-success' :
                                                    app.status === 'rejected' ?
                                                    'bg-danger' :
                                                    app.status === 'read' ?
                                                    'bg-info' : 'bg-warning';

                                                return `
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                          <strong>${app.approver?.username ?? app.approver_email}</strong><br>
                                                            <span class="text-muted">${app.comment ?? 'No Comment'}</span>
                                                        </div>
                                                        <span class="badge ${badgeClass}">${app.status}</span>
                                                    </li>
                                                `;
                                            }).join('');

                                        approvalsHtml = `
                                            <div class="collapse" id="approvalDetail-${item.id}">
                                                <ul class="list-group list-group-flush small mt-2">
                                                    ${approvalList}
                                                </ul>
                                            </div>
                                        `;
                                    } else {
                                        approvalsHtml =
                                            `<p class="text-muted small mt-2 mb-0">Belum ada data approval</p>`;
                                    }

                                    let statusLabel;
                                    if (statusSertifikat === 'pending') {
                                        statusLabel = `
                                            <button class="btn btn-outline-warning btn-sm d-flex align-items-center gap-1" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#approvalDetail-${item.id}">
                                                <i class="mdi mdi-timer-sand"></i> Menunggu Approval...
                                            </button>
                                        `;
                                    } else if (statusSertifikat === 'approved') {
                                        statusLabel = `
                                            <button class="btn btn-outline-success btn-sm d-flex align-items-center gap-1" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#approvalDetail-${item.id}">
                                                <i class="mdi mdi-check-circle-outline"></i> Sudah Disetujui
                                            </button>
                                        `;
                                    } else if (statusSertifikat === 'rejected') {
                                        statusLabel = `
                                            <button class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#approvalDetail-${item.id}">
                                                <i class="mdi mdi-close-circle-outline"></i> Approval Ditolak
                                            </button>
                                        `;
                                    } else {
                                        statusLabel = `
                                            <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#approvalDetail-${item.id}">
                                                <i class="mdi mdi-help-circle-outline"></i> Status Tidak Diketahui
                                            </button>
                                        `;
                                    }

                                    keteranganText = `
                                        ${statusLabel}
                                        ${approvalsHtml}
                                    `;

                                }

                                // Tombol Detail selalu ditampilkan
                                const detailButton = `
                                    <button class="btn btn-outline-info detail-btn btn-sm" data-bs-toggle="collapse" data-bs-target="#${rowId}" aria-expanded="false" aria-controls="${rowId}">
                                        <i class="mdi mdi-eye-outline me-1"></i> Detail
                                    </button>
                                `;

                                let row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>
                                            <span class="icon-wrapper bg-soft-info rounded p-2 me-2 d-inline-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-tools text-info fs-5"></i>
                                            </span>
                                            ${item.alat?.kode_alat || '-'}
                                        </td>
                                        <td>${capitalizeWords(item.jenis_kalibrasi)}</td>
                                        <td>${item.lokasi_kalibrasi || '-'}</td>
                                        <td>${item.tgl_kalibrasi || '-'}</td>
                                        <td>${statusBadge}</td>
                                        <td class="text-center">
                                            <div class="d-flex flex-nowrap justify-content-center gap-2"> 
                                                ${actionButtons}
                                                ${detailButton}
                                            </div>
                                        </td>
                                        <td>${keteranganText}</td> 
                                    </tr>
                                `;

                                let detailRow = `
                                    <tr class="collapse" id="${rowId}">
                                        <td colspan="8"> <div class="detail-wrapper">
                                            <div class="detail-content p-3">
                                                <h6 class="fw-semibold mb-3">Detail Kalibrasi</h6>
                                                <div class="row g-3 small">
                                                    <div class="col-md-4"><strong>Nama Alat:</strong> ${item.alat?.nama_alat || '-'}</div>
                                                    <div class="col-md-4"><strong>Kode Alat:</strong> ${item.alat?.kode_alat || '-'}</div>
                                                    <div class="col-md-4"><strong>Status Sertifikat:</strong> ${statusBadge}</div>
                                                    
                                                    <div class="col-md-4"><strong>Jenis Kalibrasi:</strong> ${item.jenis_kalibrasi || '-'}</div>
                                                    <div class="col-md-4"><strong>Lokasi Kalibrasi:</strong> ${item.lokasi_kalibrasi || '-'}</div>
                                                    <div class="col-md-4"><strong>Tanggal Kalibrasi:</strong> ${item.tgl_kalibrasi || '-'}</div>

                                                    <div class="col-md-4"><strong>Tanggal Kalibrasi Ulang:</strong> ${item.tgl_kalibrasi_ulang || '-'}</div>
                                                    @if (Auth::user()->jabatan == 'operator')
                                                        <div class="col-md-8 text-end">
                                                            <a href="/kalibrasi/certificate/download/${item.certificate?.id}" target="_blank" class="btn btn-sm btn-info me-2">
                                                            <i class="mdi mdi-file-document-outline"></i> Download Sertifikat
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                                tbody.append(row, detailRow);
                            });
                            $("#certificateTable").fadeIn();
                        } else {
                            tbody.append(`
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Tidak ada data</td> </tr>
                            `);
                            $("#certificateTable").fadeIn();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal memuat data!',
                        });
                    }
                });
            }

            // Approval Modal
            let sertifikatId = null;

            $(document).on('click', '.req-approval-btn', function() {
                sertifikatId = $(this).data('id');
                console.log(sertifikatId);
                $('#sertifikatId').val(sertifikatId);
                const $btn = $('.req-approval-btn');

                $btn.prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Loading...'
                    );

                // load approver dari server
                $.ajax({
                    url: `{{ url('api/kalibrasi/approvals/data') }}`, // endpoint untuk ambil daftar approver
                    type: 'GET',
                    data: {
                        id: sertifikatId
                    },
                    success: function(res) {
                        let managerOptions =
                            '<option value="" selected disabled>Pilih Manager</option>';
                        let supervisorOptions =
                            '<option value="" selected disabled>Pilih Supervisor</option>';
                        let foremanOptions =
                            '<option value="" selected disabled>Pilih Foreman</option>';
                        let userOptions =
                            '<option value="" selected disabled>Pilih User</option>';

                        const data = res.data; // ambil objek data di dalam re

                        // Loop per jabatan
                        data.manager.forEach(user => {
                            managerOptions +=
                                `<option value="${user.id}">${user.username}</option>`;
                        });

                        data.supervisor.forEach(user => {
                            supervisorOptions +=
                                `<option value="${user.id}">${user.username}</option>`;
                        });

                        data.foreman.forEach(user => {
                            foremanOptions +=
                                `<option value="${user.id}">${user.username}</option>`;
                        });

                        data.user.forEach(user => {
                            userOptions +=
                                `<option value="${user.id}">${user.username}</option>`;
                        });

                        $('#managerSelect').html(managerOptions);
                        $('#supervisorSelect').html(supervisorOptions);
                        $('#foremanSelect').html(foremanOptions);
                        $('#userSelect').html(userOptions);

                        $('#approvalModal').modal('show');
                    },
                    error: function() {
                        alert('Gagal memuat daftar approver');
                    },
                    complete: function() {
                        $btn.prop('disabled', false)
                            .html(
                                '<i class="mdi mdi-send-check-outline me-1"></i> Request Approval'
                            );
                    }
                });
            });

            // Submit form approval
            $('#approvalForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                $('#approvalModal').modal('hide'); // Tutup modal dulu

                // Tampilkan loading swal
                Swal.fire({
                    title: 'Mengirim...',
                    text: 'Mohon tunggu, sedang mengirim request approval.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `{{ route('kalibrasi.certificate.req-approval', '') }}/` + sertifikatId,
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        Swal.close(); // Tutup loading

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        loadData(); // reload datatable
                    },
                    error: function(xhr) {
                        Swal.close(); // Tutup loading
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message ||
                                'Gagal mengirim request approval!',
                        });
                    }
                });
            });

            $('#filterTanggal, #filterJenis').on('change', function() {
                const filters = {
                    tanggal: $('#filterTanggal').val(),
                    jenis: $('#filterJenis').val()
                };
                loadData(filters);
            });

            $('#btnResetFilter').on('click', function() {
                $('#filterTanggal').val('');
                $('#filterJenis').val('');
                loadData(); // refresh data setelah reset
            });

            // Helper function
            function capitalizeWords(str) {
                return str ? str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase()) : '-';
            }

            function getStatusBadge(status) {
                if (!status) return '<span class="badge badge-soft-secondary">-</span>';

                switch (status.toLowerCase()) {
                    case "draft":
                        return '<span class="badge badge-soft-secondary text-uppercase">Draft</span>';
                    case "pending":
                        return '<span class="badge badge-soft-warning text-uppercase">Pending</span>';
                    case "approved":
                        return '<span class="badge badge-soft-success text-uppercase">Approved</span>';
                    case "rejected":
                        return '<span class="badge badge-soft-danger text-uppercase">Rejected</span>';
                    default:
                        return `<span class="badge badge-soft-light text-uppercase">${status}</span>`;
                }
            }
        });
    </script>
@endsection
