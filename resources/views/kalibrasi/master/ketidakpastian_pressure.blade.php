@extends('layouts.app')

@section('title', 'Master Ketidakpastian Pressure')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Header Section -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Master Ketidakpastian Pressure</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Kalibrasi</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                                <li class="breadcrumb-item active">Ketidakpastian Pressure</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header py-3">
                    <div class="row g-3 align-items-center justify-content-between">
                        <div class="col-md-4">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="mdi mdi-gauge me-2"></i> Ketidakpastian Tiap Titik Kalibrasi Pressure
                            </h5>
                            <small class="text-muted">Master nilai ketidakpastian standar per kode alat & titik
                                kalibrasi</small>
                        </div>
                        <div class="col-md-8 text-md-end d-flex flex-wrap gap-2 justify-content-md-end">
                            <!-- Filter Alat -->
                            <div style="min-width: 220px;">
                                <select class="form-select form-select-sm" id="filterKodeAlat">
                                    <option value="">-- Semua Kode Alat --</option>
                                    @foreach ($alatList as $alat)
                                        <option value="{{ $alat->kode_alat }}">
                                            {{ $alat->kode_alat }} - {{ $alat->nama_alat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Search -->
                            <div style="min-width: 200px;">
                                <input type="text" class="form-control form-control-sm" id="searchTable"
                                    placeholder="Cari data...">
                            </div>
                            <!-- Tombol Import Excel -->
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalImport">
                                <i class="mdi mdi-file-excel-outline me-1"></i> Import Excel
                            </button>
                            <!-- Tombol Tambah -->
                            <button class="btn btn-sm btn-primary" id="btnAdd">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Data
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center" id="tableMaster">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No.</th>
                                    <th>Kode Alat</th>
                                    <th>Nama Alat</th>
                                    <th>Titik Kalibrasi</th>
                                    <th class="text-success fw-bold">Nilai Ketidakpastian</th>
                                    <th>Keterangan</th>
                                    <th style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Populated via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah / Edit -->
            <div class="modal fade" id="modalMaster" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fw-semibold text-white" id="modalTitle">Tambah Ketidakpastian</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="formMaster">
                            @csrf
                            <input type="hidden" id="master_id" name="id">
                            <div class="modal-body">
                                <!-- Pilih Alat -->
                                <div class="mb-3">
                                    <label for="modal_alat_select" class="form-label fw-semibold">
                                        Pilih Alat Kalibrasi <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="modal_alat_select">
                                        <option value="">-- Pilih dari Daftar Alat Pressure --</option>
                                        @foreach ($alatList as $alat)
                                            <option value="{{ $alat->id }}" data-kode="{{ $alat->kode_alat }}"
                                                data-nama="{{ $alat->nama_alat }}">
                                                {{ $alat->kode_alat }} ({{ $alat->nama_alat }})
                                            </option>
                                        @endforeach
                                        <option value="custom">-- Input Kode Alat Manual --</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="kode_alat" class="form-label fw-semibold">
                                            Kode Alat <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="kode_alat" id="kode_alat" class="form-control"
                                            placeholder="Contoh: PRS/TEST/01" required>
                                        <input type="hidden" name="alat_id" id="alat_id">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nama_alat" class="form-label fw-semibold">Nama Alat</label>
                                        <input type="text" name="nama_alat" id="nama_alat" class="form-control"
                                            placeholder="Nama alat...">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="titik_kalibrasi" class="form-label fw-semibold">
                                            Titik Kalibrasi <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="any" name="titik_kalibrasi" id="titik_kalibrasi"
                                            class="form-control text-center" placeholder="Contoh: 5 atau 10" required>
                                        <div class="form-text">Nilai titik uji (bar/psi/dll)</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nilai_ketidakpastian" class="form-label fw-semibold text-success">
                                            Nilai Ketidakpastian <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="any" name="nilai_ketidakpastian"
                                            id="nilai_ketidakpastian"
                                            class="form-control text-center fw-bold text-success"
                                            placeholder="Contoh: 0.08" required>
                                        <div class="form-text">Nilai yang akan tampil di sertifikat</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="keterangan" class="form-label fw-semibold">Keterangan (Opsional)</label>
                                    <input type="text" name="keterangan" id="keterangan" class="form-control"
                                        placeholder="Catatan / rujukan manual...">
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" id="btnSubmit">
                                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Import Excel -->
            <div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportTitle"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title fw-semibold text-white" id="modalImportTitle">
                                <i class="mdi mdi-file-excel-outline me-1"></i> Import Master Ketidakpastian
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="formImport" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-info d-flex align-items-center mb-3 py-2 px-3">
                                    <i class="mdi mdi-information-outline fs-4 me-2"></i>
                                    <div>
                                        Gunakan format template Excel. Jika kode alat dan titik kalibrasi sudah ada di
                                        database, nilai ketidakpastian akan diperbarui secara otomatis.
                                    </div>
                                </div>
                                <div class="mb-3 text-center">
                                    <a href="{{ route('master.kalibrasi.ketidakpastian_pressure.download_template') }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="mdi mdi-download me-1"></i> Download Format Template Excel (.xlsx)
                                    </a>
                                </div>
                                <div class="mb-3">
                                    <label for="import_file" class="form-label fw-semibold">Pilih File Excel (.xlsx /
                                        .xls) <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="import_file" name="file"
                                        accept=".xlsx, .xls" required>
                                    <div class="form-text">Pastikan baris pertama adalah header dan urutan kolom sesuai
                                        template: <b>Kode Alat, Titik Kalibrasi, Nilai Ketidakpastian, Keterangan</b>.</div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light py-2">
                                <button type="button" class="btn btn-sm btn-secondary"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-success" id="btnSubmitImport">
                                    <i class="mdi mdi-upload me-1"></i> Unggah & Import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const modal = new bootstrap.Modal(document.getElementById('modalMaster'));
            let allData = [];

            // Format number helper
            function formatNumber(value, decimals = 4) {
                if (value === null || value === undefined || value === '') return '-';
                let num = parseFloat(value);
                if (isNaN(num)) return '-';
                // Jika memiliki desimal, buang trailing zero yang berlebih tapi pertahankan nilai presisi
                return num.toString().replace(/(\.\d*?[1-9])0+$|\.0*$/, '$1');
            }

            // Load data via AJAX
            function loadData() {
                const kode = $('#filterKodeAlat').val();
                const search = $('#searchTable').val();

                $.ajax({
                    url: "{{ route('master.kalibrasi.ketidakpastian_pressure.index') }}",
                    type: "GET",
                    data: {
                        kode_alat: kode,
                        search: search
                    },
                    success: function(data) {
                        allData = data;
                        renderTable(data);
                    },
                    error: function(xhr) {
                        console.error("Gagal memuat data:", xhr);
                        $('#tableMaster tbody').html(`
                            <tr><td colspan="7" class="text-danger py-4">Gagal memuat data dari server.</td></tr>
                        `);
                    }
                });
            }

            // Render table rows
            function renderTable(data) {
                const tbody = $('#tableMaster tbody');
                tbody.empty();

                if (!data || data.length === 0) {
                    tbody.html(`
                        <tr>
                            <td colspan="7" class="text-muted py-4">
                                <i class="mdi mdi-information-outline me-1"></i> Belum ada data ketidakpastian.
                            </td>
                        </tr>
                    `);
                    return;
                }

                $.each(data, function(i, item) {
                    const row = `
                        <tr>
                            <td>${i + 1}</td>
                            <td><span class="badge bg-primary-subtle text-primary fs-12">${item.kode_alat}</span></td>
                            <td>${item.nama_alat || (item.alat ? item.alat.nama_alat : '-')}</td>
                            <td class="fw-semibold">${formatNumber(item.titik_kalibrasi)}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success fs-13 px-2 py-1">
                                    ${parseFloat(item.nilai_ketidakpastian).toFixed(4).replace(/\.?0+$/, '') || item.nilai_ketidakpastian}
                                </span>
                            </td>
                            <td class="text-muted fs-12">${item.keterangan || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning me-1 btnEdit" data-id="${item.id}" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btnDelete" data-id="${item.id}" data-kode="${item.kode_alat}" data-titik="${item.titik_kalibrasi}" title="Hapus">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            // Initial load
            loadData();

            // Filter & Search events
            $('#filterKodeAlat').on('change', loadData);
            $('#searchTable').on('input', function() {
                loadData();
            });

            // Handle Alat dropdown in Modal
            $('#modal_alat_select').on('change', function() {
                const selected = $(this).find('option:selected');
                const val = $(this).val();

                if (val && val !== 'custom') {
                    $('#alat_id').val(val);
                    $('#kode_alat').val(selected.data('kode')).prop('readonly', true);
                    $('#nama_alat').val(selected.data('nama')).prop('readonly', true);
                } else if (val === 'custom') {
                    $('#alat_id').val('');
                    $('#kode_alat').val('').prop('readonly', false).focus();
                    $('#nama_alat').val('').prop('readonly', false);
                } else {
                    $('#alat_id').val('');
                    $('#kode_alat').val('').prop('readonly', false);
                    $('#nama_alat').val('').prop('readonly', false);
                }
            });

            // Tombol Tambah
            $('#btnAdd').on('click', function() {
                $('#formMaster')[0].reset();
                $('#master_id').val('');
                $('#alat_id').val('');
                $('#modal_alat_select').val('').trigger('change');
                $('#kode_alat').prop('readonly', false);
                $('#nama_alat').prop('readonly', false);
                $('#modalTitle').text('Tambah Ketidakpastian Pressure');
                modal.show();
            });

            // Tombol Edit
            $(document).on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                const item = allData.find(d => d.id == id);

                if (!item) {
                    // Fetch if not in current view
                    $.get("{{ url('kalibrasi/master/ketidakpastian-pressure') }}/" + id, function(res) {
                        populateEditModal(res);
                    });
                    return;
                }

                populateEditModal(item);
            });

            function populateEditModal(item) {
                $('#master_id').val(item.id);
                $('#alat_id').val(item.alat_id || '');
                $('#kode_alat').val(item.kode_alat);
                $('#nama_alat').val(item.nama_alat);
                $('#titik_kalibrasi').val(item.titik_kalibrasi);
                $('#nilai_ketidakpastian').val(item.nilai_ketidakpastian);
                $('#keterangan').val(item.keterangan || '');

                if (item.alat_id) {
                    $('#modal_alat_select').val(item.alat_id);
                } else {
                    $('#modal_alat_select').val('custom');
                }

                $('#modalTitle').text('Edit Ketidakpastian Pressure');
                modal.show();
            }

            // Submit Form (Tambah / Update)
            $('#formMaster').on('submit', function(e) {
                e.preventDefault();

                const id = $('#master_id').val();
                const isEdit = !!id;
                const url = isEdit ?
                    "{{ url('kalibrasi/master/ketidakpastian-pressure') }}/" + id :
                    "{{ route('master.kalibrasi.ketidakpastian_pressure.store') }}";
                const method = isEdit ? 'PUT' : 'POST';

                $('#btnSubmit').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#btnSubmit').prop('disabled', false).html(
                            '<i class="mdi mdi-content-save-outline me-1"></i> Simpan');
                        modal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message ||
                                'Data ketidakpastian berhasil disimpan.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        loadData();
                    },
                    error: function(xhr) {
                        $('#btnSubmit').prop('disabled', false).html(
                            '<i class="mdi mdi-content-save-outline me-1"></i> Simpan');
                        const msg = xhr.responseJSON?.message ||
                            'Terjadi kesalahan saat menyimpan data.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg
                        });
                    }
                });
            });

            // Handle Import Excel
            $('#formImport').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const $btn = $('#btnSubmitImport');
                $btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Mengimpor...');

                $.ajax({
                    url: "{{ route('master.kalibrasi.ketidakpastian_pressure.import') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $btn.prop('disabled', false).html(
                            '<i class="mdi mdi-upload me-1"></i> Unggah & Import');
                        const importModalEl = document.getElementById('modalImport');
                        const modalInstance = bootstrap.Modal.getInstance(importModalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        $('#formImport')[0].reset();

                        if (response.status === 'partial') {
                            let errorList = '';
                            if (response.errors && response.errors.length > 0) {
                                errorList =
                                    '<div class="text-start mt-2 p-2 bg-light border rounded" style="max-height: 180px; overflow-y: auto;"><ul class="mb-0 text-danger ps-3">' +
                                    response.errors.map(function(err) {
                                        return `<li><small>${err}</small></li>`;
                                    }).join('') +
                                    '</ul></div>';
                            }

                            Swal.fire({
                                icon: 'warning',
                                title: 'Selesai Sebagian',
                                html: `<p class="mb-1">${response.message}</p>${errorList}`,
                                confirmButtonText: 'Tutup'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }

                        loadData();
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(
                            '<i class="mdi mdi-upload me-1"></i> Unggah & Import');
                        let errMsg = xhr.responseJSON?.message ||
                            'Terjadi kesalahan saat mengimpor data.';
                        let errorDetails = '';

                        if (xhr.responseJSON?.errors) {
                            if (Array.isArray(xhr.responseJSON.errors)) {
                                errorDetails =
                                    '<div class="text-start mt-2 p-2 bg-light border rounded" style="max-height: 180px; overflow-y: auto;"><ul class="mb-0 text-danger ps-3">' +
                                    xhr.responseJSON.errors.map(function(err) {
                                        return `<li><small>${err}</small></li>`;
                                    }).join('') +
                                    '</ul></div>';
                            } else if (typeof xhr.responseJSON.errors === 'object') {
                                let list = Object.values(xhr.responseJSON.errors).flat();
                                errorDetails =
                                    '<div class="text-start mt-2 p-2 bg-light border rounded" style="max-height: 180px; overflow-y: auto;"><ul class="mb-0 text-danger ps-3">' +
                                    list.map(function(err) {
                                        return `<li><small>${err}</small></li>`;
                                    }).join('') +
                                    '</ul></div>';
                            }
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Import Gagal!',
                            html: `<p class="mb-1">${errMsg}</p>${errorDetails}`
                        });
                    }
                });
            });

            // Delete action
            $(document).on('click', '.btnDelete', function() {
                const id = $(this).data('id');
                const kode = $(this).data('kode');
                const titik = $(this).data('titik');

                Swal.fire({
                    title: 'Hapus Ketidakpastian?',
                    html: `Apakah Anda yakin ingin menghapus data ketidakpastian untuk alat <b>${kode}</b> pada titik <b>${titik}</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('kalibrasi/master/ketidakpastian-pressure') }}/" +
                                id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message ||
                                        'Data berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadData();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: xhr.responseJSON?.message ||
                                        'Gagal menghapus data.'
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
