@extends('layouts.app')

@section('styles')
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .table-responsive {
            border-radius: 0.25rem;
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
            <!-- Filters -->


            <!-- Certificate List -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Daftar Sertifikat Kalibrasi</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="nowrap table table-hover table-bordered" id="dataTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Alat</th>
                                            <th>Nama Alat</th>
                                            <th>Jenis Kalibrasi</th>
                                            <th>Lokasi Kalibrasi</th>
                                            <th>Tgl Kalibrasi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- di isi oleh js --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal approval --}}
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
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

            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: false,
                responsive: true,
                scrollX: true,
                ajax: {
                    url: `{{ url('api/kalibrasi/certificate/data') }}`,
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'alat.kode_alat',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'alat.nama_alat',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'jenis_kalibrasi',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'lokasi_kalibrasi',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'tgl_kalibrasi',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'certificate.status',
                        render: function(data, type, row) {
                            if (!data) {
                                return '<span class="badge badge-soft-secondary">-</span>';
                            }

                            let badge = '';
                            switch (data.toLowerCase()) {
                                case 'draft':
                                    badge =
                                        '<span class="badge badge-soft-secondary">Draft</span>';
                                    break;
                                case 'pending':
                                    badge =
                                        '<span class="badge badge-soft-warning">Pending</span>';
                                    break;
                                case 'approved':
                                    badge =
                                        '<span class="badge badge-soft-success">Approved</span>';
                                    break;
                                case 'rejected':
                                    badge = '<span class="badge badge-soft-danger">Rejected</span>';
                                    break;
                                default:
                                    badge = '<span class="badge badge-soft-secondary">' + data +
                                        '</span>';
                                    break;
                            }
                            return badge;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-outline-primary req-approval-btn" data-id="${row.id}" title="Edit Data">
                                    <i class="mdi mdi-send-check"></i> Request Approval
                                </button>
                                <button class="btn btn-sm btn-outline-info detail-btn" data-id="${row.id}" title="Delete Data">
                                    <i class="mdi mdi-eye"></i> Detail
                                </button>
                            `;
                        }
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                language: {
                    lengthMenu: "Show _MENU_ entries",
                }
            });

            table.on('draw.dt', function() {
                let info = table.page.info();
                table.column(0, {
                        search: 'applied',
                        order: 'applied',
                        page: 'current'
                    })
                    .nodes()
                    .each(function(cell, i) {
                        cell.innerHTML = i + 1 + info.start;
                    });
            });

            // Approval Modal
            let sertifikatId = null;

            $(document).on('click', '.req-approval-btn', function() {
                sertifikatId = $(this).data('id');
                $('#sertifikatId').val(sertifikatId);

                // load approver dari server
                $.ajax({
                    url: `{{ url('api/kalibrasi/approvals/data') }}`, // endpoint untuk ambil daftar approver
                    type: 'GET',
                    success: function(res) {
                        let managerOptions =
                            '<option value="" selected disabled>Pilih Manager</option>';
                        let supervisorOptions =
                            '<option value="" selected disabled>Pilih Supervisor</option>';
                        let foremanOptions =
                            '<option value="" selected disabled>Pilih Foreman</option>';
                        let userOptions =
                            '<option value="" selected disabled>Pilih User</option>';

                        res.data.forEach(function(user) {
                            switch (user.jabatan) {
                                case 'dept_head':
                                    managerOptions +=
                                        `<option value="${user.id}">${user.username}</option>`;
                                    break;
                                case 'supervisor':
                                    supervisorOptions +=
                                        `<option value="${user.id}">${user.username}</option>`;
                                    break;
                                case 'foreman':
                                    foremanOptions +=
                                        `<option value="${user.id}">${user.username}</option>`;
                                    break;
                                case 'operator':
                                    userOptions +=
                                        `<option value="${user.id}">${user.username}</option>`;
                                    break;
                            }
                        });

                        $('#managerSelect').html(managerOptions);
                        $('#supervisorSelect').html(supervisorOptions);
                        $('#foremanSelect').html(foremanOptions);
                        $('#userSelect').html(userOptions);

                        $('#approvalModal').modal('show');
                    },
                    error: function() {
                        alert('Gagal memuat daftar approver');
                    }
                });
            });

            // Submit form approval
            $('#approvalForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('kalibrasi.certificate.req-approval') }}" + sertifikatId,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#approvalModal').modal('hide');
                        alert(res.message);
                        $('#dataTable').DataTable().ajax.reload(); // reload datatable
                    },
                    error: function() {
                        alert('Gagal mengirim request approval!');
                    }
                });
            });
        });
    </script>
@endsection
