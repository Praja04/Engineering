@extends('layouts.app')

@section('title', 'Master Harga Chemical WWTP')

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
                            <h4 class="mb-1">Master Harga Standar Chemical WWTP</h4>
                            <p class="text-muted mb-0">Kelola harga standar per kilogram untuk kalkulasi biaya chemical
                                bulanan.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/wwtp/form_biaya_chemical') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-file-document-edit-outline me-1"></i> Form Input Biaya
                            </a>
                            <button type="button" class="btn btn-info text-white" id="btnAddChemical">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Chemical Baru
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="row">
                <div class="col-lg">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-0 py-3">
                            <h5 class="card-title mb-0">Daftar Standar Harga Chemical</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="standardsTable" class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px;" class="text-center">No</th>
                                            <th>Tipe Chemical</th>
                                            <th class="text-end" style="width: 200px;">Harga Standar (Rp/kg)</th>
                                            <th>Dibuat / Terakhir Diubah Oleh</th>
                                            <th style="width: 250px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="standardsTableBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="mdi mdi-loading mdi-spin fs-4 mb-2 d-block"></i>
                                                Memuat data harga standar...
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

    <!-- Modal Form (Tambah / Edit Standard) -->
    <div class="modal fade" id="modalStandard" tabindex="-1" aria-labelledby="modalStandardLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modalStandardLabel">Ubah Harga Standar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formStandard">
                    @csrf
                    <input type="hidden" id="standardId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="chemical_name" class="form-label fw-semibold">Nama Chemical <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="chemical_name" name="chemical_name" required
                                placeholder="Contoh: PAC">
                        </div>
                        <div class="mb-3">
                            <label for="harga_standar" class="form-label fw-semibold">Harga Standar (Rp/kg) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="harga_standar" name="harga_standar" required
                                    min="0" step="0.01" placeholder="Contoh: 8400">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white" id="btnSaveStandard">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Format to Indonesian Rupiah representation
            function formatRupiah(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'decimal',
                    minimumFractionDigits: 0
                }).format(value);
            }

            // Load Data
            function loadStandardsData() {
                $.ajax({
                    url: "{{ url('api/wwtp-biaya-chemical/standards') }}",
                    method: 'GET',
                    success: function(response) {
                        let rows = '';
                        if (response.length === 0) {
                            rows =
                                `<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada data harga standar ditemukan</td></tr>`;
                        } else {
                            response.forEach((item, index) => {
                                const updater = item.updated_by?.username ?? item.created_by
                                    ?.username ?? '-';
                                rows += `
                                    <tr>
                                        <td class="text-center fw-semibold">${index + 1}</td>
                                        <td class="fw-bold text-dark">${item.chemical_name}</td>
                                        <td class="text-end fw-bold text-primary">Rp ${formatRupiah(item.harga_standar)}</td>
                                        <td>${updater}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-soft-warning btn-sm btn-edit" 
                                                    data-id="${item.id}" 
                                                    data-name="${item.chemical_name}" 
                                                    data-price="${item.harga_standar}">
                                                    <i class="mdi mdi-pencil me-1"></i> Edit Harga
                                                </button>
                                                <button class="btn btn-soft-danger btn-sm btn-delete" 
                                                    data-id="${item.id}" 
                                                    data-name="${item.chemical_name}">
                                                    <i class="mdi mdi-trash-can me-1"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        $('#standardsTableBody').html(rows);
                    },
                    error: function() {
                        $('#standardsTableBody').html(
                            `<tr><td colspan="5" class="text-center text-danger py-4">Gagal memuat data. Silakan refresh halaman.</td></tr>`
                        );
                    }
                });
            }

            // Init load
            loadStandardsData();

            // Show Add Modal
            $('#btnAddChemical').on('click', function() {
                $('#formStandard')[0].reset();
                $('#standardId').val('');
                $('#chemical_name').prop('readonly', false).removeClass('bg-light');
                $('#modalStandardLabel').text('Tambah Chemical Baru');
                $('#modalStandard').modal('show');
            });

            // Show Edit Modal
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = $(this).data('price');

                $('#standardId').val(id);
                $('#chemical_name').val(name).prop('readonly', true).addClass('bg-light');
                $('#harga_standar').val(price);
                $('#modalStandardLabel').text(`Ubah Harga Standar - ${name}`);
                $('#modalStandard').modal('show');
            });

            // Form Submit (Add / Edit)
            $('#formStandard').on('submit', function(e) {
                e.preventDefault();
                const id = $('#standardId').val();
                const url = id ? `{{ url('api/wwtp-biaya-chemical/standards') }}/${id}` :
                    `{{ url('api/wwtp-biaya-chemical/standards') }}`;
                const method = id ? 'PUT' : 'POST';
                const btn = $('#btnSaveStandard');
                const originalText = btn.html();

                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        chemical_name: $('#chemical_name').val(),
                        harga_standar: $('#harga_standar').val()
                    },
                    success: function(response) {
                        $('#modalStandard').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadStandardsData();
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

            // Delete Standard
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Chemical "${name}" akan dihapus dari master beserta seluruh rincian riwayat penginputannya!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('api/wwtp-biaya-chemical/standards') }}/${id}`,
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
                                loadStandardsData();
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus chemical.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
