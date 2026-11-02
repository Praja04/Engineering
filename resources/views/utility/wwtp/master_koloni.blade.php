@extends('layouts.app')

@section('title', 'Master Data Koloni WWTP')

@section('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(41, 156, 219, 0.25);
            border-color: #299cdb;
        }

        .btn-info {
            background-color: #299cdb;
            border-color: #299cdb;
        }

        .btn-info:hover {
            background-color: #2284ba;
            border-color: #2284ba;
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">Master Data Koloni WWTP</h4>
                            <p class="text-muted mb-0">Kelola daftar master sampel koloni untuk penginputan mingguan.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/wwtp/form_koloni') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-file-document-edit-outline me-1"></i> Form Koloni
                            </a>
                            <button type="button" class="btn btn-info text-white" id="btnAddSample">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Master Sample
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Daftar Master Sample Koloni</h5>
                                <div style="width: 250px;">
                                    <input type="text" id="searchMaster" class="form-control"
                                        placeholder="Cari sample...">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="masterTable" class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px;" class="text-center">No</th>
                                            <th>Nama Sample</th>
                                            <th style="width: 200px;" class="text-center">Tanggal Dibuat</th>
                                            <th>Dibuat Oleh</th>
                                            <th style="width: 150px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="masterTableBody">
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="mdi mdi-loading mdi-spin fs-4 mb-2 d-block"></i>
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
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalMaster" tabindex="-1" aria-labelledby="modalMasterLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modalMasterLabel">Tambah Master Sample</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formMaster">
                    @csrf
                    <input type="hidden" id="sampleId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_sample" class="form-label fw-semibold">Nama Sample <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_sample" name="nama_sample" required
                                placeholder="Contoh: Equalisasi">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white" id="btnSaveMaster">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Load Data
            function loadMasterData() {
                const search = $('#searchMaster').val();
                $.ajax({
                    url: "{{ url('api/wwtp-koloni/master') }}",
                    method: 'GET',
                    data: {
                        search: search
                    },
                    success: function(response) {
                        let rows = '';
                        if (response.length === 0) {
                            rows =
                                `<tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada data sampel koloni ditemukan</td></tr>`;
                        } else {
                            response.forEach((item, index) => {
                                const formattedDate = new Date(item.created_at)
                                    .toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    });
                                rows += `
                                    <tr>
                                        <td class="text-center fw-semibold">${index + 1}</td>
                                        <td class="fw-semibold text-dark">${item.nama_sample}</td>
                                        <td class="text-center text-muted">${formattedDate}</td>
                                        <td>${item.created_by?.username ?? '-'}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-soft-warning btn-sm btn-edit" data-id="${item.id}">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                <button class="btn btn-soft-danger btn-sm btn-delete" data-id="${item.id}">
                                                    <i class="mdi mdi-trash-can"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        $('#masterTableBody').html(rows);
                    },
                    error: function() {
                        $('#masterTableBody').html(
                            `<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data. Silakan refresh halaman.</td></tr>`
                        );
                    }
                });
            }

            // Init load
            loadMasterData();

            // Search with debounce
            let searchTimer;
            $('#searchMaster').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(loadMasterData, 300);
            });

            // Show Add Modal
            $('#btnAddSample').on('click', function() {
                $('#formMaster')[0].reset();
                $('#sampleId').val('');
                $('#modalMasterLabel').text('Tambah Master Sample');
                $('#modalMaster').modal('show');
            });

            // Show Edit Modal
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ url('api/wwtp-koloni/master') }}/${id}`,
                    method: 'GET',
                    success: function(response) {
                        $('#sampleId').val(response.id);
                        $('#nama_sample').val(response.nama_sample);
                        $('#modalMasterLabel').text('Edit Master Sample');
                        $('#modalMaster').modal('show');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal mengambil data sampel.'
                        });
                    }
                });
            });

            // Form Submit (Add / Edit)
            $('#formMaster').on('submit', function(e) {
                e.preventDefault();
                const id = $('#sampleId').val();
                const url = id ? `{{ url('/wwtp/koloni-master/update') }}/${id}` :
                    `{{ url('/wwtp/koloni-master/store') }}`;
                const method = id ? 'PUT' : 'POST';

                const btn = $('#btnSaveMaster');
                const originalText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        nama_sample: $('#nama_sample').val()
                    },
                    success: function(response) {
                        $('#modalMaster').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadMasterData();
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let msg = 'Gagal menyimpan data!';
                        if (error && error.errors) {
                            msg = Object.values(error.errors).flat().join('<br>');
                        } else if (error && error.message) {
                            msg = error.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: msg
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Delete Sample
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data sampel koloni ini akan dihapus permanen beserta seluruh rekaman data koloninya!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('api/wwtp-koloni/master') }}/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadMasterData();
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus sampel.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
