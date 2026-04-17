@extends('layouts.app')

@section('title', 'Master Alat Kalibrasi')

@section('styles')
    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            transition: all 0.2s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .form-select,
        .form-control {
            border-radius: 6px;
        }

        label.form-label {
            font-size: 0.85rem;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Header -->
            <div class="row g-3 align-items-center mb-4">
                <!-- Judul -->
                <div class="col-md-6 col-12">
                    <h3 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                        <i class="mdi mdi-gauge-low"></i>
                        <span>Calibration Tools List</span>
                    </h3>
                </div>

                <!-- Tombol -->
                <div class="col-md-6 col-12">
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1"
                                data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="mdi mdi-plus"></i> Add
                            </button>
                        </div>
                        <div class="col-md-6 col-12">
                            <button
                                class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2"
                                data-bs-toggle="modal" data-bs-target="#modalImport">
                                <i class="mdi mdi-database-import-outline"></i> Import Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Filter Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body py-4 px-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <select class="form-select" id="selectDepartemen">
                                <option value="">All Departmen</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select class="form-select" id="selectJenisKalibrasi">
                                <option value="">All Jenis Kalibrasi</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex align-items-end justify-content-md-end">
                            <button class="btn btn-outline-secondary w-100" id="btnResetFilter">
                                <i class="mdi mdi-filter-off me-1"></i> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-striped mb-0 text-nowrap" id="dataTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Kode Alat</th>
                                    <th>Nama Alat</th>
                                    <th>Jenis Kalibrasi</th>
                                    <th>Dept. Pemilik</th>
                                    <th>Lokasi Alat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimuat oleh JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modalImportLabel">
                        <i class="mdi mdi-upload-outline fs-4"></i> Import Data
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-muted mb-3">
                        Silakan unduh template terlebih dahulu, kemudian unggah file sesuai format.
                    </p>

                    <div class="d-flex flex-column gap-3">
                        <!-- Download Template -->
                        <a href="{{ route('master.download.template') }}"
                            class="btn btn-outline-info d-flex align-items-center justify-content-center gap-2">
                            <i class="mdi mdi-download-outline fs-5"></i> Download Template
                        </a>

                        <!-- Upload File -->
                        <form id="formImport" action="{{ route('master.import') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="fileImport" class="form-label fw-semibold">Pilih File (.xlsx)</label>
                                <input class="form-control" type="file" id="fileImport" name="file" accept=".xlsx">
                            </div>
                            <div class="text-end">
                                <button type="button" id="btnUpload" class="btn btn-success px-4">
                                    <i class="mdi mdi-upload fs-5 me-1"></i> Upload
                                </button>
                            </div>
                        </form>

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
                                    <option value="timbangan">Timbangan</option>
                                    <option value="pressure">Pressure</option>
                                    <option value="temperature">Temperature</option>
                                    <option value="volumetrik">Volumetrik</option>
                                    <option value="thermohygrometer">Thermohygrometer</option>
                                    <option value="jangka_sorong">Jangka Sorong</option>
                                    <option value="instrumen">Instrumen</option>
                                    <option value="dimensi">Dimensi</option>
                                    <option value="flowmeter">Flowmeter</option>
                                </select>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="kode_alat" class="form-label">Kode Alat</label>
                                <input type="text" class="form-control" id="kode_alat" name="kode_alat"
                                    placeholder="QRM/RPM/PPV/001">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="nama_alat" class="form-label">Nama Alat</label>
                                <input type="text" class="form-control" id="nama_alat" name="nama_alat"
                                    placeholder="Pipet Volume 20 ml">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="text" class="form-control" id="jumlah" name="jumlah"
                                    placeholder="1">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="departemen_pemilik" class="form-label">Departemen Pemilik</label>
                                <input type="text" class="form-control" id="departemen_pemilik"
                                    name="departemen_pemilik" placeholder="Quality Control">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="lokasi_alat" class="form-label">Lokasi Alat</label>
                                <input type="text" class="form-control" id="lokasi_alat" name="lokasi_alat"
                                    placeholder="Laboratorium RMPM">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="no_kalibrasi" class="form-label">Nomor Kalibrasi</label>
                                <input type="text" class="form-control" id="no_kalibrasi" name="no_kalibrasi"
                                    placeholder="CAL/VLT/077">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="merk" class="form-label">Merk</label>
                                <input type="text" class="form-control" id="merk" name="merk"
                                    placeholder="Iwaki">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="tipe" class="form-label">Tipe</label>
                                <input type="text" class="form-control" id="tipe" name="tipe"
                                    placeholder="Analog">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="kapasitas" class="form-label">Kapasitas</label>
                                <input type="text" class="form-control" id="kapasitas" name="kapasitas"
                                    placeholder="20">
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <label for="resolusi" class="form-label">Resolusi</label>
                                <input type="text" class="form-control" id="resolusi" name="resolusi"
                                    step="any" placeholder="2">
                            </div>
                            <div class="col-xxl-6 col-md-6">
                                <label for="range_penggunaan_alat" class="form-label">Range Penggunaan Alat</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="range_min" name="range_min"
                                        placeholder="0" min="0">
                                    <span class="input-group-text">-</span>
                                    <input type="number" class="form-control" id="range_max" name="range_max"
                                        placeholder="20" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="limits_permissible_error" class="form-label">Limits of Permissible
                                    Error</label>
                                <div class="input-group">
                                    <span class="input-group-text">±</span>
                                    <input type="number" class="form-control" id="limits_permissible_error"
                                        name="limits_permissible_error" placeholder="0.03" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="metode_kalibrasi" class="form-label">Metode Kalibrasi</label>
                                    <textarea name="metode_kalibrasi" id="metode_kalibrasi" class="form-control" rows="3"
                                        placeholder="Masukkan metode kalibrasi..."></textarea>
                                </div>
                            </div>
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
                            <input type="text" class="form-control" id="edit_kode_alat" name="edit_kode_alat"
                                placeholder="QRM/RPM/PPV/001">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nama_alat" class="form-label">Nama Alat</label>
                            <input type="text" class="form-control" id="edit_nama_alat" name="edit_nama_alat"
                                placeholder="Pipet Volume 20 ml">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_jenis_kalibrasi" class="form-label">Jenis Kalibrasi</label>
                            <select type="text" class="form-select" id="edit_jenis_kalibrasi"
                                name="edit_jenis_kalibrasi">
                                <option value="" disabled selected>Pilih jenis kalibrasi</option>
                                <option value="dimention">Dimention</option>
                                <option value="magnetic">Magnetic</option>
                                <option value="timbangan">Timbangan</option>
                                <option value="pressure">Pressure</option>
                                <option value="temperature">Temperature</option>
                                <option value="volumetrik">Volumetrik</option>
                                <option value="thermohygrometer">Thermohygrometer</option>
                                <option value="jangka_sorong">Jangka Sorong</option>
                                <option value="instrumen">Instrumen</option>
                                <option value="dimensi">Dimensi</option>
                                <option value="flowmeter">Flowmeter</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_jumlah" class="form-label">Jumlah</label>
                            <input type="text" class="form-control" id="edit_jumlah" name="edit_jumlah"
                                placeholder="1">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_departemen_pemilik" class="form-label">Departemen Pemilik</label>
                            <input type="text" class="form-control" id="edit_departemen_pemilik"
                                name="edit_departemen_pemilik" placeholder="Quality Control">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_lokasi_alat" class="form-label">Lokasi Alat</label>
                            <input type="text" class="form-control" id="edit_lokasi_alat" name="edit_lokasi_alat"
                                placeholder="Laboratorium RMPM">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_no_kalibrasi" class="form-label">No Kalibrasi</label>
                            <input type="text" class="form-control" id="edit_no_kalibrasi" name="edit_no_kalibrasi"
                                placeholder="CAL/VLT/077">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_merk" class="form-label">Merk</label>
                            <input type="text" class="form-control" id="edit_merk" name="edit_merk"
                                placeholder="Iwaki">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_tipe" class="form-label">Tipe</label>
                            <input type="text" class="form-control" id="edit_tipe" name="edit_tipe"
                                placeholder="Analog">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_kapasitas" class="form-label">Kapasitas</label>
                            <input type="text" class="form-control" id="edit_kapasitas" name="edit_kapasitas"
                                placeholder="20 ml">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_resolusi" class="form-label">Resolusi</label>
                            <input type="text" step="0.01" class="form-control" id="edit_resolusi"
                                name="edit_resolusi" placeholder="2 ml">
                        </div>
                        <div class="col-xxl-6 col-md-6">
                            <label for="edit_range_penggunaan_alat" class="form-label">Range Penggunaan Alat</label>
                            <input type="text" class="form-control" id="edit_range_penggunaan_alat"
                                name="edit_range_penggunaan_alat" placeholder="0-20 ml">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_limits_permissible_error" class="form-label">Limits of Permissible
                                Error</label>
                            <input type="text" class="form-control" id="edit_limits_permissible_error"
                                name="edit_limits_permissible_error" placeholder="± 0,03 ml">
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_metode_kalibrasi" class="form-label">Metode Kalibrasi</label>
                                <textarea name="edit_metode_kalibrasi" id="edit_metode_kalibrasi" class="form-control" rows="3"
                                    placeholder="Masukkan metode kalibrasi..."></textarea>
                            </div>
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
                            <p><span id="detail_kapasitas"></span></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Resolusi:</strong>
                            <p><span id="detail_resolusi"></span></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Range Penggunaan:</strong>
                            <p><span id="detail_range_penggunaan"></span></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Limits of Permissible Error:</strong>
                            <p><span id="detail_limits_permissible_error"></span></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Metode Kalibrasi:</strong>
                            <p><span id="detail_metode_kalibrasi"></span></p>
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
            function formatTitleCase(text) {
                if (!text) return '-';
                return text
                    .toLowerCase() // pastikan huruf kecil semua dulu
                    .replace(/_/g, ' ') // ganti underscore jadi spasi
                    .replace(/\b\w/g, c => c.toUpperCase()); // kapital tiap kata
            }

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
                                   <i class="mdi mdi-tools me-2 fs-6"></i>${data}
                                </span>
                            `;
                        }
                    },
                    {
                        data: 'nama_alat',
                        orderable: false,
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'jenis_kalibrasi',
                        orderable: false,
                        render: function(data, type, row) {
                            return formatTitleCase(data);
                        }
                    },
                    {
                        data: 'departemen_pemilik',
                        orderable: false,
                        render: function(data, type, row) {
                            return formatTitleCase(data);
                        }
                    },
                    {
                        data: 'lokasi_alat',
                        orderable: false,
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
                },
                columnDefs: [{
                    targets: 0,
                    className: 'text-center'
                }]
            });

            // Ambil data unik untuk dropdown filter (setelah dataTable load)
            table.on('xhr.dt', function(e, settings, json, xhr) {
                if (!json?.data) return;

                // Ambil nilai unik departemen dan jenis kalibrasi
                const departemenList = [...new Set(json.data.map(i => i.departemen_pemilik).filter(
                    Boolean))];
                const jenisList = [...new Set(json.data.map(i => i.jenis_kalibrasi).filter(Boolean))];

                // Isi dropdown departemen
                const $departemen = $('#selectDepartemen');
                $departemen.empty().append(`<option value="">All Departmen</option>`);
                departemenList
                    .map(d => formatTitleCase(d)) // ubah snake_case → Title Case
                    .forEach(dep => {
                        $departemen.append(`<option value="${dep}">${dep}</option>`);
                    });

                // Isi dropdown jenis kalibrasi
                const $jenis = $('#selectJenisKalibrasi');
                $jenis.empty().append(`<option value="">All Jenis Kalibrasi</option>`);
                jenisList
                    .map(j => formatTitleCase(j)) // ubah snake_case → Title Case
                    .forEach(jk => {
                        $jenis.append(`<option value="${jk}">${jk}</option>`);
                    });
            });

            // Filter berdasarkan dropdown
            $('#selectDepartemen, #selectJenisKalibrasi').on('change', function() {
                let departemen = $('#selectDepartemen').val();
                let jenis = $('#selectJenisKalibrasi').val();

                // Terapkan filter kolom
                table.column(4).search(departemen);
                table.column(3).search(jenis);
                table.draw();
            });

            // Tombol reset filter
            $('#btnResetFilter').on('click', function() {
                $('#selectDepartemen').val('');
                $('#selectJenisKalibrasi').val('');

                // Hapus filter dari DataTable
                table.column(3).search('');
                table.column(4).search('');
                table.draw();
            });

            // auto number tabel
            table.on('draw.dt', function() {
                let info = table.page.info();
                table.column(0, {
                        search: 'applied',
                        order: 'applied',
                        page: 'current',
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
                        $('#edit_jenis_kalibrasi').val(data.jenis_kalibrasi)
                            .trigger('change');
                        $('#edit_jumlah').val(data.jumlah);
                        $('#edit_departemen_pemilik').val(formatTitleCase(data
                            .departemen_pemilik) || 0);
                        $('#edit_lokasi_alat').val(data.lokasi_alat || 0);
                        $('#edit_no_kalibrasi').val(data.no_kalibrasi || 0);
                        $('#edit_merk').val(data.merk || '');
                        $('#edit_tipe').val(data.tipe || '');
                        $('#edit_kapasitas').val(data.kapasitas || '');
                        $('#edit_resolusi').val(data.resolusi || '');
                        $('#edit_range_penggunaan_alat').val(data.range_penggunaan_alat ||
                            '');
                        $('#edit_limits_permissible_error').val(data
                            .limits_of_permissible_error || '');
                        $('#edit_metode_kalibrasi').val(data.metode_kalibrasi || '');

                        $('#modalEditAlat').modal('show');
                    },
                    error: function(err) {
                        console.error("Error fetching data:", err);
                        Swal.fire('Error!', 'There was an error fetching the data.',
                            'error');
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
                if (!id) return;

                $.ajax({
                    url: `{{ url('api/kalibrasi/show/master/alat') }}/${id}`,
                    type: 'GET',
                    success: function(response) {
                        const data = response?.data;
                        if (!data) {
                            Swal.fire('Error!', 'Data alat tidak ditemukan.', 'error');
                            return;
                        }

                        // Isi semua field detail
                        $('#detail_kode_alat').text(data.kode_alat || '-');
                        $('#detail_nama_alat').text(data.nama_alat || '-');
                        $('#detail_jenis_kalibrasi').text(data.jenis_kalibrasi || '-');
                        $('#detail_jumlah').text(data.jumlah ?? '-');
                        $('#detail_departemen_pemilik').text(data.departemen_pemilik ||
                            '-');
                        $('#detail_lokasi_alat').text(data.lokasi_alat || '-');
                        $('#detail_no_kalibrasi').text(data.no_kalibrasi || '-');
                        $('#detail_merk').text(data.merk || '-');
                        $('#detail_tipe').text(data.tipe || '-');
                        $('#detail_kapasitas').text(data.kapasitas ?? '0');
                        $('#detail_resolusi').text(data.resolusi ?? '0');
                        $('#detail_limits_permissible_error').text(data
                            .limits_of_permissible_error ?? '0');
                        $('#detail_range_penggunaan').text(data.range_penggunaan_alat ||
                            '-');
                        $('#detail_metode_kalibrasi').text(data.metode_kalibrasi || '-');

                        // Tampilkan modal
                        $('#detailModalAlat').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error fetching detail:', xhr);
                        Swal.fire('Error!', 'Gagal mengambil detail data alat.', 'error');
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

            // Tampilkan modal saat tombol import ditekan
            $('#btnImport').on('click', function() {
                $('#modalImport').modal('show');
            });

            // Tombol Upload di modal ditekan
            $('#btnUpload').on('click', function() {
                const fileInput = $('#fileImport')[0];
                if (!fileInput.files.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'File belum dipilih!',
                        text: 'Silakan pilih file terlebih dahulu.'
                    });
                    return;
                }

                let formData = new FormData($('#formImport')[0]);

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
                            let errorList = response.errors.map(e => `<li>${e}</li>`).join('');
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sebagian gagal!',
                                html: `<p>${response.message}</p><ul>${errorList}</ul>`,
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

                        $('#dataTable').DataTable().ajax.reload();
                        $('#formImport')[0].reset();
                        $('#modalImport').modal('hide');
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
