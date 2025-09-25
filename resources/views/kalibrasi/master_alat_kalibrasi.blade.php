@extends('layouts.app')

@section('styles')
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title d-sm-flex align-items-center justify-content-between">
                        {{-- <h4 class="mb-sm-0">Form Input TKBM</h4> --}}

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">RackMan</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                                <li class="breadcrumb-item active">Alat Kalibrasi</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Data Alat Kalibrasi</h4>
                            <div>
                                {{-- btn download template --}}
                                <a href="{{ route('master.download.template') }}" class="btn btn-info me-2">
                                    <i class="mdi mdi-download"></i> Download Template
                                </a>

                                <!-- Tombol Import -->
                                <form id="formImport" action="{{ route('master.import') }}" method="POST"
                                    enctype="multipart/form-data" class="d-inline me-2">
                                    @csrf
                                    <input type="file" name="file" id="fileImport" accept=".csv, .xlsx"
                                        style="display: none;">
                                    <button type="button" class="btn btn-success" id="btnImport">
                                        <i class="mdi mdi-upload"></i> Import File
                                    </button>
                                </form>

                                <!-- Tombol Tambah -->
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="mdi mdi-plus me-2"></i>Tambah Alat
                                </button>
                            </div>
                        </div>


                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select id="filterJenis" class="form-select">
                                        <option value="">-- All Jenis Kalibrasi --</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="filterDepartemen" class="form-select">
                                        <option value="">-- All Departemen --</option>
                                    </select>
                                </div>
                            </div>
                            <table class="nowrap table table-striped dt-responsive" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Alat</th>
                                        <th>Nama Alat</th>
                                        <th>Jenis Kalibrasi</th>
                                        <th>Departemen Pemilik</th>
                                        <th>Lokasi Alat</th>
                                        @if (Session::get('jabatan') !== 'operator')
                                            <th data-orderable="false">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Di isi oleh js --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Alat --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Alat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formTambahAlatKalibrasi" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row gy-4">
                            {{-- <div class="col-xxl-3 col-md-6">
                                <label for="user_id" class="form-label">Petugas</label>
                                <input type="text" class="form-control" id="user_id" name="user_id"
                                    value="{{ Auth::user()->username }}" readonly>
                            </div> --}}
                            <div class="col-xxl-3 col-md-6">
                                <label for="kode_alat" class="form-label">Kode Alat</label>
                                <input type="text" class="form-control" id="kode_alat" name="kode_alat">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="nama_alat" class="form-label">Nama Alat</label>
                                <input type="text" class="form-control" id="nama_alat" name="nama_alat">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="jenis_kalibrasi" class="form-label">Jenis Kalibrasi</label>
                                <input type="text" class="form-control" id="jenis_kalibrasi" name="jenis_kalibrasi">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="text" class="form-control" id="jumlah" name="jumlah">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="departemen_pemilik" class="form-label">Departemen Pemilik</label>
                                <input type="text" class="form-control" id="departemen_pemilik"
                                    name="departemen_pemilik">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="lokasi_alat" class="form-label">Lokasi Alat</label>
                                <input type="text" class="form-control" id="lokasi_alat" name="lokasi_alat">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="no_kalibrasi" class="form-label">Nomor Kalibrasi</label>
                                <input type="text" class="form-control" id="no_kalibrasi" name="no_kalibrasi">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="merk" class="form-label">Merk</label>
                                <input type="text" class="form-control" id="merk" name="merk">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="tipe" class="form-label">Tipe</label>
                                <input type="text" class="form-control" id="tipe" name="tipe">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="kapasitas" class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="resolusi" class="form-label">Resolusi</label>
                                <input type="number" class="form-control" id="resolusi" name="resolusi"
                                    step="any">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="range_penggunaan" class="form-label">Range Penggunaan (Bar)</label>
                                <input type="range" class="form-range" id="range_penggunaan" name="range_penggunaan"
                                    min="1" max="10" step="1" value="1"
                                    oninput="document.getElementById('rangeLabelEdit').innerText = '1 - ' + this.value + ' Bar'">
                                <p class="mt-2"><strong id="rangeLabelEdit">1 - 1 Bar</strong></p>
                            </div>

                            <div class="col-xxl-3 col-md-6">
                                <label for="limits_permissible_error" class="form-label">Limits of Permissible
                                    Error</label>
                                <input type="number" class="form-control" id="limits_permissible_error"
                                    name="limits_permissible_error">
                            </div>
                            <div class="col-xxl-6 col-md-6">
                                <label for="metode_kalibrasi" class="form-label">Metode Kalibrasi</label>
                                <textarea class="form-control" id="metode_kalibrasi" name="metode_kalibrasi" cols="30" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Alat --}}
    <div class="modal fade" id="modalEditAlat" tabindex="-1" aria-labelledby="editAlatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info py-3">
                    <h5 class="modal-title text-white" id="editAlatLabel">Edit Alat Kalibrasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditAlat" enctype="multipart/form-data">
                    <div class="modal-body row g-3">
                        <input type="hidden" id="editId" name="id">
                        <div class="col-md-6">
                            <label for="edit_kode_alat" class="form-label">Kode Alat</label>
                            <input type="text" class="form-control" id="edit_kode_alat" name="edit_kode_alat">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nama_alat" class="form-label">Nama Alat</label>
                            <input type="text" class="form-control" id="edit_nama_alat" name="edit_nama_alat">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_jenis_kalibrasi" class="form-label">Jenis Kalibrasi</label>
                            <input type="text" class="form-control" id="edit_jenis_kalibrasi"
                                name="edit_jenis_kalibrasi">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_jumlah" class="form-label">Jumlah</label>
                            <input type="text" class="form-control" id="edit_jumlah" name="edit_jumlah">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_departemen_pemilik" class="form-label">Departemen Pemilik</label>
                            <input type="text" class="form-control" id="edit_departemen_pemilik"
                                name="edit_departemen_pemilik">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_lokasi_alat" class="form-label">Lokasi Alat</label>
                            <input type="text" class="form-control" id="edit_lokasi_alat" name="edit_lokasi_alat">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_no_kalibrasi" class="form-label">No Kalibrasi</label>
                            <input type="text" class="form-control" id="edit_no_kalibrasi" name="edit_no_kalibrasi">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_merk" class="form-label">Merk</label>
                            <input type="text" class="form-control" id="edit_merk" name="edit_merk">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_tipe" class="form-label">Tipe</label>
                            <input type="text" class="form-control" id="edit_tipe" name="edit_tipe">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_kapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="edit_kapasitas" name="edit_kapasitas">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_resolusi" class="form-label">Resolusi</label>
                            <input type="number" step="0.01" class="form-control" id="edit_resolusi"
                                name="edit_resolusi">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_range_penggunaan" class="form-label">Range Penggunaan (Bar)</label>
                            <input type="range" class="form-range" id="edit_range_penggunaan" min="1"
                                max="10" step="1"
                                oninput="document.getElementById('rangeLabel').innerText = '1 - ' + this.value + ' Bar'">
                            <p class="mt-2"><strong id="rangeLabel">1 - 1 Bar</strong></p>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_limits_permissible_error" class="form-label">Limits of Permissible
                                Error</label>
                            <input type="number" class="form-control" id="edit_limits_permissible_error"
                                name="edit_limits_permissible_error">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal detail --}}
    <div class="modal fade" id="detailModalAlat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Data Alat Kalibrasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-2">
                        <div class="col-md-4">
                            <strong>Kode Alat:</strong>
                            <p id="detail_kode_alat"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Nama Alat:</strong>
                            <p id="detail_nama_alat"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Jenis Kalibrasi:</strong>
                            <p id="detail_jenis_kalibrasi"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Jumlah:</strong>
                            <p id="detail_jumlah"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Departemen Pemilik:</strong>
                            <p id="detail_departemen_pemilik"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Lokasi Alat:</strong>
                            <p id="detail_lokasi_alat"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>No Kalibrasi:</strong>
                            <p id="detail_no_kalibrasi"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Merk:</strong>
                            <p id="detail_merk"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Tipe:</strong>
                            <p id="detail_tipe"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Kapasitas:</strong>
                            <p><span id="detail_kapasitas"></span> Bar</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Resolusi:</strong>
                            <p><span id="detail_resolusi"></span> Bar</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Range Penggunaan:</strong>
                            <p>1 - <span id="detail_range_penggunaan"></span> bar</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Limits of Permissible Error:</strong>
                            <p><span id="detail_limist_permissible_error"></span> Bar</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            getFilters();

            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: false,
                responsive: true,
                scrollX: true,
                ajax: {
                    url: `{{ url('api/kalibrasi/data/master/alat') }}`,
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1; // otomatis nomor urut
                        }
                    },
                    {
                        data: 'kode_alat',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'nama_alat',
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
                        data: 'departemen_pemilik',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'lokasi_alat',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    @if (Session::get('jabatan') !== 'operator')
                        {
                            data: null,
                            orderable: false,
                            render: function(data, type, row) {
                                return `
                                    <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}" title="Edit Data">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" title="Delete Data">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info detail-btn" data-id="${row.id}" title="Detail Data">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                `;
                            }
                        }
                    @endif
                ],
                order: [
                    [0, 'asc']
                ],
                language: {
                    lengthMenu: "Show _MENU_ entries",
                }
            });

            // Form submit tambah alat
            $("#formTambahAlatKalibrasi").submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                console.log(formData);

                $.ajax({
                    url: "{{ route('store.master.alat') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1000
                        });
                        $('#formTambahAlatKalibrasi')[0].reset();
                        $('#modalTambah').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                        getFilters();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON?.message ||
                                'Terjadi kesalahan tidak diketahui'
                        });
                    }
                });
            });

            // Edit button click
            $('#dataTable').on('click', '.edit-btn', function() {
                const id = $(this).data('id');

                $.ajax({
                    url: `{{ url('api/kalibrasi/show/master/alat') }}/${id}`,
                    method: 'GET',
                    success: function(response) {
                        const data = response.data;

                        $('#editId').val(data.id);
                        $('#edit_kode_alat').val(data.kode_alat);
                        $('#edit_nama_alat').val(data.nama_alat);
                        $('#edit_jenis_kalibrasi').val(data.jenis_kalibrasi);
                        $('#edit_jumlah').val(data.jumlah);
                        $('#edit_departemen_pemilik').val(data.departemen_pemilik || 0);
                        $('#edit_lokasi_alat').val(data.lokasi_alat || 0);
                        $('#edit_no_kalibrasi').val(data.no_kalibrasi || 0);
                        $('#edit_merk').val(data.merk || 0);
                        $('#edit_tipe').val(data.tipe || 0);
                        $('#edit_kapasitas').val(data.kapasitas || 0);
                        $('#edit_resolusi').val(data.resolusi || 0);
                        $('#edit_range_penggunaan')
                            .val(data.range_penggunaan ||
                                1)
                            .trigger('input');
                        $('#rangeLabel').text('1 - ' + (data.range_penggunaan || 1) + ' Bar');

                        $('#edit_limits_permissible_error').val(data.limits_permissible_error ||
                            0);

                        $('#modalEditAlat').modal('show');
                    },
                    error: function(err) {
                        console.error("Error fetching data:", err);
                        Swal.fire('Error!', 'There was an error fetching the data.', 'error');
                    }
                });
            });

            // Edit form submit
            $('#formEditAlat').submit(function(e) {
                e.preventDefault();

                const id = $('#editId').val();

                const formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `{{ route('update.master.alat', '') }}/` + id,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Success!', response.message, 'success');
                        $('#modalEditAlat').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                        getFilters();
                    },
                    error: function(err) {
                        let errorMsg = 'There was an error updating the data.';
                        if (err.responseJSON && err.responseJSON.message) {
                            errorMsg = err.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            });

            // Detail button click event
            $('#dataTable').on('click', '.detail-btn', function() {
                const id = $(this).data('id');

                $.ajax({
                    url: `{{ url('api/kalibrasi/show/master/alat') }}/${id}`,
                    method: 'GET',
                    success: function(response) {
                        const data = response.data;

                        $('#detail_kode_alat').text(data.kode_alat);
                        $('#detail_nama_alat').text(data.nama_alat);
                        $('#detail_jenis_kalibrasi').text(data.jenis_kalibrasi);
                        $('#detail_jumlah').text(data.jumlah);
                        $('#detail_departemen_pemilik').text(data.departemen_pemilik);
                        $('#detail_lokasi_alat').text(data.lokasi_alat);
                        $('#detail_no_kalibrasi').text(data.no_kalibrasi);
                        $('#detail_merk').text(data.merk);
                        $('#detail_tipe').text(data.tipe);
                        $('#detail_kapasitas').text(data.kapasitas);
                        $('#detail_resolusi').text(data.resolusi ?? '0');
                        $('#detail_range_penggunaan').text(data.range_penggunaan);
                        $('#detail_limist_permissible_error').text(data
                            .limits_permissible_error);
                        // $('#detailUser').text(
                        //     data.username ?
                        //     data.username.replace(/\b\w/g, function(l) {
                        //         return l.toUpperCase();
                        //     }) :
                        //     '-'
                        // );

                        $('#detailModalAlat').modal('show');
                    },
                    error: function(err) {
                        console.error("Error fetching detail:", err);
                        Swal.fire('Error!', 'Gagal mengambil detail data.', 'error');
                    }
                });
            });

            // Delete button click 
            $('#dataTable').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('delete.master.alat', '') }}/` + id,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                );

                                $('#dataTable').DataTable().ajax.reload();
                                getFilters();
                            },
                            error: function(err) {
                                console.error("Error deleting data:", err);
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // filtering
            function getFilters() {
                $('#filterJenis').empty().append('<option value="">All Jenis Kalibrasi</option>');
                $('#filterDepartemen').empty().append('<option value="">All Departemen</option>');

                $.get("{{ url('api/kalibrasi/master/filters') }}", function(res) {
                    res.jenis.forEach(function(item) {
                        $('#filterJenis').append(`<option value="${item}">${item}</option>`);
                    });

                    res.departemen.forEach(function(item) {
                        $('#filterDepartemen').append(`<option value="${item}">${item}</option>`);
                    });
                });
            }

            // Apply filter
            $('#filterJenis').on('change', function() {
                table.column(3).search(this.value).draw();
            });

            $('#filterDepartemen').on('change', function() {
                table.column(4).search(this.value).draw();
            });

            // Import handler
            $('#btnImport').click(function() {
                $('#fileImport').click();
            });

            $('#fileImport').change(function() {
                var formData = new FormData($('#formImport')[0]);

                $.ajax({
                    url: $('#formImport').attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'File berhasil diimport.'
                            });
                        } else if (response.status === 'partial') {
                            // kalau sebagian gagal
                            let errorList = response.errors.map(e => `<li>${e}</li>`).join('');
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sebagian berhasil!',
                                html: `<p>${response.message}</p>${errorList}`,
                                width: 600
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message ||
                                    'Terjadi kesalahan saat import.'
                            });
                        }

                        // reload table kalau ada data yang masuk
                        $('#dataTable').DataTable().ajax.reload();
                        $('#formImport')[0].reset(); // reset form
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat import.'
                        });
                    }
                });
            });
        })
    </script>
@endsection
