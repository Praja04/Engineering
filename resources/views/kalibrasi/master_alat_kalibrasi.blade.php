@extends('layouts.app')

@section('styles')
    <style>
        .btn-header:hover,
        .btn-header:hover i,
        .btn-header:hover span {
            color: black !important;
        }
    </style>
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
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Alat Kalibrasi</a></li>
                                {{-- <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                                <li class="breadcrumb-item active">Alat Kalibrasi</li> --}}
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h4 class="card-title mb-0">List Alat Kalibrasi</h4>
                            <div class="d-flex flex-wrap gap-2">

                                {{-- Download Template --}}
                                <!-- Mobile: Icon only -->
                                <a href="{{ route('master.download.template') }}"
                                    class="btn btn-info waves-effect waves-light d-md-none" title="Download Template">
                                    <i class="mdi mdi-download"></i>
                                </a>
                                <!-- Desktop: Icon + Text -->
                                <a href="{{ route('master.download.template') }}"
                                    class="btn btn-info waves-effect waves-light d-none d-md-inline-flex align-items-center gap-1"
                                    title="Download Template">
                                    <i class="mdi mdi-download"></i>
                                    <span>Download Template</span>
                                </a>

                                {{-- Import File --}}
                                <form id="formImport" action="{{ route('master.import') }}" method="POST"
                                    enctype="multipart/form-data" class="d-inline">
                                    @csrf
                                    <input type="file" name="file" id="fileImport" accept=".csv, .xlsx"
                                        style="display: none;">
                                    <!-- Mobile: Icon only -->
                                    <button type="button" class="btn btn-success waves-effect waves-light d-md-none"
                                        id="btnImport" title="Import File">
                                        <i class="mdi mdi-upload"></i>
                                    </button>
                                    <!-- Desktop: Icon + Text -->
                                    <button type="button"
                                        class="btn btn-success waves-effect waves-light d-none d-md-inline-flex align-items-center gap-1"
                                        id="btnImportDesktop" title="Import File">
                                        <i class="mdi mdi-upload"></i>
                                        <span>Import File</span>
                                    </button>
                                </form>

                                {{-- Tambah Alat --}}
                                <!-- Mobile: Icon only -->
                                <button class="btn btn-primary waves-effect waves-light d-md-none" data-bs-toggle="modal"
                                    data-bs-target="#modalTambah" title="Add Alat">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                                <!-- Desktop: Icon + Text -->
                                <button
                                    class="btn btn-primary waves-effect waves-light d-none d-md-inline-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#modalTambah" title="Add Alat">
                                    <i class="mdi mdi-plus"></i>
                                    <span>Add Alat</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <!-- Versi Desktop/Tablet -->
                                <div class="d-none d-md-flex flex-wrap align-items-center gap-2">
                                    <!-- Icon Filter -->
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-filter-outline text-primary fs-2"></i>
                                    </div>

                                    <!-- Filter Jenis Kalibrasi -->
                                    <div class="btn-group flex-fill flex-md-grow-0" style="min-width: 200px;">
                                        <button type="button" class="btn btn-outline-primary dropdown-toggle w-100"
                                            data-bs-toggle="dropdown" aria-expanded="false" id="btnJenisKalibrasi">
                                            All Jenis Kalibrasi
                                        </button>
                                        <div class="dropdown-menu w-100" id="filterJenis">
                                            <!-- isi dropdown Jenis Kalibrasi diinject JS -->
                                        </div>
                                        <input type="hidden" name="jenis_kalibrasi" id="selectedJenisKalibrasi">
                                    </div>

                                    <!-- Filter Departemen -->
                                    <div class="btn-group flex-fill flex-md-grow-0" style="min-width: 200px;">
                                        <button type="button" class="btn btn-outline-primary dropdown-toggle w-100"
                                            data-bs-toggle="dropdown" aria-expanded="false" id="btnDepartemen">
                                            All Departemen
                                        </button>
                                        <div class="dropdown-menu w-100" id="filterDepartemen">
                                            <!-- isi dropdown Departemen diinject JS -->
                                        </div>
                                        <input type="hidden" name="departemen" id="selectedDepartemen">
                                    </div>
                                </div>

                                <!-- Versi  -->
                                <div class="d-md-none">
                                    <button class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                                        data-bs-target="#filterModal">
                                        <i class="mdi mdi-filter-outline me-1"></i> Filter
                                    </button>
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

    {{-- Modal filter --}}
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- Dropdown Jenis Kalibrasi -->
                    <div class="mb-3">
                        <label class="form-label">Jenis Kalibrasi</label>
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle w-100"
                                data-bs-toggle="dropdown" aria-expanded="false" id="btnJenisKalibrasiMobile">
                                All Jenis Kalibrasi
                            </button>
                            <div class="dropdown-menu w-100" id="filterJenisMobile"></div>
                        </div>
                    </div>

                    <!-- Dropdown Departemen -->
                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle w-100"
                                data-bs-toggle="dropdown" aria-expanded="false" id="btnDepartemenMobile">
                                All Departemen
                            </button>
                            <div class="dropdown-menu w-100" id="filterDepartemenMobile"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Alat --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Add Alat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formTambahAlatKalibrasi" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-xxl-3 col-md-6">
                                <label for="jenis_kalibrasi" class="form-label">Jenis Kalibrasi</label>
                                <select type="text" class="form-select" id="jenis_kalibrasi" name="jenis_kalibrasi">
                                    <option value="" disabled selected>Pilih jenis kalibrasi</option>
                                    <option value="dimention">Dimention</option>
                                    <option value="magnetic">Magnetic</option>
                                    <option value="massa">Massa</option>
                                    <option value="pressure">Pressure</option>
                                    <option value="temperature">Temperature</option>
                                    <option value="volumetrik">Volumetrik</option>
                                </select>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="kode_alat" class="form-label">Kode Alat</label>
                                <input type="text" class="form-control" id="kode_alat" name="kode_alat">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="nama_alat" class="form-label">Nama Alat</label>
                                <input type="text" class="form-control" id="nama_alat" name="nama_alat">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah">
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
                                <label for="limits_permissible_error" class="form-label">Limits of Permissible
                                    Error</label>
                                <input type="number" class="form-control" id="limits_permissible_error"
                                    name="limits_permissible_error">
                            </div>
                            <div class="col-xxl-6 col-md-6">
                                <label class="form-label">Range Penggunaan</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="min_range_use" name="min_range_use"
                                        placeholder="Min" step="any">
                                    <span class="input-group-text">–</span>
                                    <input type="number" class="form-control" id="max_range_use" name="max_range_use"
                                        placeholder="Max" step="any">
                                    <span class="input-group-text" id="unit_range">unit</span>
                                </div>
                            </div>
                            {{-- <div class="col-xxl-6 col-md-6">
                                <label for="metode_kalibrasi" class="form-label">Metode Kalibrasi</label>
                                <textarea class="form-control" id="metode_kalibrasi" name="metode_kalibrasi" cols="30" rows="3" readonly>Diadopsi dari : "The Expression of Uncertainty and Confidence in Measurement" Oleh UKAS (United Kingdom Accreditation Service) M3003, Edition 3, November 2012</textarea>
                            </div> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Alat --}}
    <div class="modal fade" id="modalEditAlat" tabindex="-1" aria-labelledby="editAlatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-3">
                    <h5 class="modal-title" id="editAlatLabel">Edit Alat Kalibrasi</h5>
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
                            <select type="text" class="form-select" id="edit_jenis_kalibrasi"
                                name="edit_jenis_kalibrasi">
                                <option value="" disabled selected>Pilih jenis kalibrasi</option>
                                <option value="dimention">Dimention</option>
                                <option value="magnetic">Magnetic</option>
                                <option value="massa">Massa</option>
                                <option value="pressure">Pressure</option>
                                <option value="temperature">Temperature</option>
                                <option value="volumetrik">Volumetrik</option>
                            </select>
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
                        <div class="col-xxl-6 col-md-6">
                            <label class="form-label">Range Penggunaan</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="edit_min_range_use"
                                    name="edit_min_range_use" placeholder="Min" step="any">
                                <span class="input-group-text">–</span>
                                <input type="number" class="form-control" id="edit_max_range_use"
                                    name="edit_max_range_use" placeholder="Max" step="any">
                                <span class="input-group-text" id="unit_range">unit</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_limits_permissible_error" class="form-label">Limits of Permissible
                                Error</label>
                            <input type="number" class="form-control" id="edit_limits_permissible_error"
                                name="edit_limits_permissible_error">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-info" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Simpan Perubahan</button>
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
                            <p><span id="detail_range_penggunaan"></span></p>
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
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_alat',
                        render: function(data, type, row) {
                            if (!data) return '-';
                            return `
                                <span class="detail-btn text-primary fw-bold" style="cursor:pointer;" data-id="${row.id}" title="Detail Data">
                                    ${data}
                                </span>
                            `;
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
                                    <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${row.id}" title="Edit Data">
                                        <i class="mdi mdi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${row.id}" title="Delete Data">
                                        <i class="mdi mdi-delete"></i> Delete
                                    </button>
                                `;
                            }
                        }
                    @endif
                ],
                order: [
                    [1, 'asc']
                ],
                language: {
                    lengthMenu: "Show _MENU_ entries",
                }
            });

            // auto number tabel
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
                            timer: 1500
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
                        $('#edit_jenis_kalibrasi').val(data.jenis_kalibrasi).trigger('change');
                        $('#edit_jumlah').val(data.jumlah);
                        $('#edit_departemen_pemilik').val(data.departemen_pemilik || 0);
                        $('#edit_lokasi_alat').val(data.lokasi_alat || 0);
                        $('#edit_no_kalibrasi').val(data.no_kalibrasi || 0);
                        $('#edit_merk').val(data.merk || 0);
                        $('#edit_tipe').val(data.tipe || 0);
                        $('#edit_kapasitas').val(data.kapasitas || 0);
                        $('#edit_resolusi').val(data.resolusi || 0);
                        if (data.range_use) {
                            let parts = data.range_use.split('s/d');
                            let minVal = parts[0] ? parts[0].trim() : '';
                            let maxVal = parts[1] ? parts[1].trim() : '';
                            $('#edit_min_range_use').val(minVal);
                            $('#edit_max_range_use').val(maxVal);
                        }

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
                        $('#detail_limist_permissible_error').text(data
                            .limits_permissible_error);

                        let range = data.range_use || '';
                        let formatted = range.replace(/s\/d/gi, ' s/d ').trim();
                        $('#detail_range_penggunaan').text(formatted + ' bar');

                        $('#detailModalAlat').modal('show');
                    },
                    error: function(err) {
                        console.error("Error fetching detail:", err);
                        Swal.fire('Error!', 'Failed to retrieve data details.', 'error');
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
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message ||
                                        'Your file has been deleted.'
                                });

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
                // === Jenis Kalibrasi ===
                const $dropdownJenis = $('#filterJenis');
                const $btnJenis = $('#btnJenisKalibrasi');
                const $hiddenJenis = $('#selectedJenisKalibrasi');

                // === Departemen ===
                const $dropdownDep = $('#filterDepartemen');
                const $btnDep = $('#btnDepartemen');
                const $hiddenDep = $('#selectedDepartemen');

                // === Mobile Jenis Kalibrasi ===
                const $dropdownJenisMobile = $('#filterJenisMobile');
                const $btnJenisMobile = $('#btnJenisKalibrasiMobile');

                // === Mobile Departemen ===
                const $dropdownDepMobile = $('#filterDepartemenMobile');
                const $btnDepMobile = $('#btnDepartemenMobile');

                // Reset isi dropdown
                $dropdownJenis.empty();
                $dropdownDep.empty();
                $dropdownJenisMobile.empty();
                $dropdownDepMobile.empty();

                // Tambah default option Jenis Kalibrasi
                const defaultJenis = `
                    <button class="dropdown-item" type="button" data-value="">
                        All Jenis Kalibrasi
                    </button>
                `;

                $dropdownJenis.append(defaultJenis);
                $dropdownJenisMobile.append(defaultJenis);

                // Tambah default option Departemen
                const defaultDep = `
                    <button class="dropdown-item" type="button" data-value="">
                        All Departemen
                    </button>
                `;
                $dropdownDep.append(defaultDep);
                $dropdownDepMobile.append(defaultDep);

                $.get("{{ url('api/kalibrasi/master/filters') }}")
                    .done(function(res) {
                        // === isi Jenis Kalibrasi ===
                        if (res?.jenis && Array.isArray(res.jenis)) {
                            res.jenis.forEach(item => {
                                const option = `
                                    <button class="dropdown-item" type="button" data-value="${item}">
                                        ${item}
                                    </button>
                                `;
                                $dropdownJenis.append(option);
                                $dropdownJenisMobile.append(option);
                            });
                        }

                        // === isi Departemen ===
                        if (res?.departemen && Array.isArray(res.departemen)) {
                            res.departemen.forEach(item => {
                                const option = `
                                    <button class="dropdown-item" type="button" data-value="${item}">
                                        ${item}
                                    </button>`;
                                $dropdownDep.append(option);
                                $dropdownDepMobile.append(option);
                            });
                        }

                        // Event handler dropdown Jenis Kalibrasi (desktop + mobile)
                        $dropdownJenis.add($dropdownJenisMobile)
                            .off('click').on('click', '.dropdown-item', function() {
                                const value = $(this).data('value');
                                const text = $(this).text();

                                $btnJenis.text(text);
                                $btnJenisMobile.text(text);

                                $hiddenJenis.val(value);

                                // 🚀 Apply filter ke DataTable (kolom index 3 → sesuaikan!)
                                table.column(3).search(value).draw();
                            });

                        // Event handler dropdown Departemen (desktop + mobile)
                        $dropdownDep.add($dropdownDepMobile)
                            .off('click').on('click', '.dropdown-item', function() {
                                const value = $(this).data('value');
                                const text = $(this).text();

                                $btnDep.text(text);
                                $btnDepMobile.text(text);

                                $hiddenDep.val(value);

                                // 🚀 Apply filter ke DataTable (kolom index 4 → sesuaikan!)
                                table.column(4).search(value).draw();
                            });
                    })
                    .fail(function(xhr, status, error) {
                        console.error("Failed to load filters:", error);
                    });
            }



            // Apply filter
            $('#filterJenis, #filterJenisMobile').on('change', function() {
                table.column(3).search(this.value).draw();
            });

            $('#filterDepartemen, #filterDepartemenMobile').on('change', function() {
                table.column(4).search(this.value).draw();
            });

            // Import handler
            $('#btnImport, #btnImportDesktop').on('click', function() {
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
                                title: 'Success!',
                                text: response.message || 'File successfully imported.'
                            });
                        } else if (response.status === 'partial') {
                            // kalau sebagian gagal
                            let errorList = response.errors.map(e => `<li>${e}</li>`).join('');
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning!',
                                html: `<p>${response.message}</p>${errorList}`,
                                width: 600
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: response.message ||
                                    'An error occurred during import.'
                            });
                        }

                        // reload table kalau ada data yang masuk
                        $('#dataTable').DataTable().ajax.reload();
                        $('#formImport')[0].reset(); // reset form
                        $('#fileImport').val('');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed!',
                            text: xhr.responseJSON?.message ||
                                'An error occurred during import.'
                        });
                    }
                });
            });

            // form unit
            $('#jenis_kalibrasi', '#edit_jenis_kalibrasi').on('change', function() {
                let jenis = $(this).val();
                let unit = 'unit';

                switch (jenis) {
                    case 'pressure':
                        unit = 'Bar';
                        break;
                    case 'temperature':
                        unit = '°C';
                        break;
                    case 'volumetrik':
                        unit = 'ml';
                        break;
                    case 'massa':
                        unit = 'g'; // default g
                        break;
                    case 'dimensi':
                    case 'magnetic':
                    default:
                        unit = '-';
                }

                $('#unit_range, #unit_range2').text(unit);
            });
        })
    </script>
@endsection
