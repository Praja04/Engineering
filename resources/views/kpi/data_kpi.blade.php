@extends('layouts.app')

@section('title', 'Data KPI')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- Card Filter --}}
            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-body">
                    <form id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="filterPeriode" class="form-label">Tipe Periode</label>
                            <select id="filterPeriode" class="form-select">
                                <option value="">-- Semua Periode --</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="filterTanggal" class="form-label">Tanggal</label>
                            <input type="date" id="filterTanggal" class="form-control">
                        </div>

                        <div class="col-md-4 d-flex">
                            <button type="button" id="btnReset" class="btn btn-secondary w-100">
                                <i class="mdi mdi-refresh me-1"></i> Reset Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Card Table --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data KPI</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-hover align-middle text-nowrap"
                            id="tableKpi">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tipe Periode</th>
                                    <th>Tanggal</th>
                                    <th>Week</th>
                                    <th>FG (Ton)</th>
                                    <th>Kecap Matang (Ton)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit --}}
    <div class="modal fade" id="modalKpi" tabindex="-1" aria-labelledby="modalKpiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-3">
                <div class="modal-header">
                    <i class="mdi mdi-pencil me-2"></i>
                    <h5 class="modal-title" id="modalKpiLabel">Tambah Data KPI</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formKpi">
                    <div class="modal-body">
                        <input type="hidden" id="kpiId">

                        <div class="mb-3">
                            <label for="jenisPeriode" class="form-label">Periode Type</label>
                            <select id="jenisPeriode" class="form-select" required>
                                <option value="">-- Pilih Periode --</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" id="tanggal" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="fg" class="form-label">Nilai FG (Ton)</label>
                            <input type="number" id="fg" class="form-control" step="0.01" min="0"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="kecap_matang" class="form-label">Kecap Matang (m³)</label>
                            <input type="number" id="kecap_matang" class="form-control" step="0.01" min="0"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const tableBody = $('#tableKpi tbody');
            const $modal = new bootstrap.Modal('#modalKpi');
            const $form = $('#formKpi');
            const today = new Date().toISOString().split('T')[0];
            $('#filterTanggal').val(today);

            const formatNumber = (num) => {
                const val = parseFloat(num);
                if (isNaN(val)) return '-';
                return val % 1 === 0 ? val.toFixed(0) : parseFloat(val.toString()).toString();
            };

            loadData();

            function loadData(periode = '', tanggal = '') {
                $.get("{{ route('kpi.get-data') }}", {
                    periode_tipe: periode,
                    tanggal: tanggal
                }, function(data) {
                    tableBody.empty();
                    if (data.length === 0) {
                        tableBody.append(`
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="mdi mdi-database-off mdi-36px mb-2"></i>
                                        <span class="fw-semibold">Tidak ada data ditemukan</span>
                                        <small class="text-secondary">Coba ubah filter atau tambah data baru.</small>
                                    </div>
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    $.each(data, function(i, item) {
                        const fg = formatNumber(item.fg);
                        const kecapMatang = formatNumber(item.kecap_matang);
                        const weekDisplay = item.week ? `Week ${item.week}` : '-';

                        tableBody.append(`
                            <tr>
                                <td>${i + 1}</td>
                                <td class="text-capitalize">${item.periode_tipe}</td>
                                <td>${item.tanggal}</td>
                                <td>${weekDisplay}</td>
                                <td>${fg}</td>
                                <td>${kecapMatang}</td>
                                <td>
                                    <button class="btn btn-sm btn-info me-1 btnEdit" data-id="${item.id}">
                                        <i class="mdi mdi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger btnDelete" data-id="${item.id}">
                                        <i class="mdi mdi-delete"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                });
            }

            // Filter otomatis ketika jenis atau tanggal berubah
            $('#filterPeriode, #filterTanggal').on('change', function() {
                const periode = $('#filterPeriode').val();
                const tanggal = $('#filterTanggal').val();

                // panggil fungsi loadData dengan parameter filter
                loadData(periode, tanggal);
            });

            $('#btnReset').on('click', function() {
                $('#filterForm')[0].reset();
                $('#filterTanggal').val(today);

                // Reload data dengan kondisi default
                loadData($('#filterPeriode').val(), $('#filterTanggal').val());
            });

            // Simpan / Update data
            $form.on('submit', function(e) {
                e.preventDefault();

                const id = $('#kpiId').val();
                const isUpdate = id !== '';
                const url = isUpdate ?
                    "{{ url('kpi/update') }}/" + id :
                    "{{ route('kpi.store') }}";
                const method = isUpdate ? 'PUT' : 'POST';

                const data = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    periode_tipe: $('#jenisPeriode').val(),
                    tanggal: $('#tanggal').val(),
                    fg: $('#fg').val(),
                    kecap_matang: $('#kecap_matang').val(),
                };

                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                $modal.hide();
                                loadData();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal menyimpan data!'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let message = 'Terjadi kesalahan saat menyimpan data!';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            text: message
                        });
                    }
                });
            });

            // Edit data
            $(document).on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                $.get("{{ url('kpi/show') }}/" + id, function(res) {
                    if (res.success) {
                        $('#modalKpiLabel').text('Edit Data Kpi');
                        $('#kpiId').val(res.data.id);
                        $('#jenisPeriode').val(res.data.periode_tipe);
                        $('#tanggal').val(res.data.tanggal);
                        $('#fg').val(formatNumber(res.data.fg));
                        $('#kecap_matang').val(formatNumber(res.data.kecap_matang));
                        $modal.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res.message
                        });
                    }
                });
            });

            // Hapus
            $(document).on('click', '.btnDelete', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin ingin menghapus data ini?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('kpi/delete') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: res.message,
                                        timer: 1000,
                                        showConfirmButton: false
                                    });
                                    loadData();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: 'Gagal menghapus data.'
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                let message = 'Terjadi kesalahan saat menghapus data!';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan!',
                                    text: message
                                });
                            }
                        });
                    }
                });
            });
        })
    </script>
@endsection
