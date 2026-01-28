@extends('layouts.app')

@section('styles')
    <style>
        .status-ok {
            color: #28a745;
            font-weight: bold;
        }

        .status-ng {
            color: #dc3545;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        #detailModal .modal-dialog {
            max-width: 90%;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card card-soft shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fw-bold">Data Maintenance Battery</h4>
                        <div class="small-muted">List inspeksi + detail hasil pengecekan</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ url('/mtc/form/battery/index') }}" class="btn btn-primary">
                            + Input Baru
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- FILTER --}}
                    <div class="row g-3 mb-4">
                        {{-- <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="filterStart">
                        </div> --}}
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="filterDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipe baterai</label>
                            <select class="form-select" id="filterTipeBaterai">
                                <option value="">Semua</option>
                                <option value="Z">Z</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cari no unit atau no seri</label>
                            <input type="text" class="form-control" id="filterUnit">

                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <div class="d-flex gap-2 text-nowrap">
                                {{-- <button type="button" class="btn btn-outline-primary w-100" id="btnApply">
                                    <i class="mdi mdi-filter me-2"></i> Terapkan</button> --}}
                                <button type="button" class="btn btn-outline-secondary w-100" id="btnReset">
                                    <i class="mdi mdi-restart"></i> Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="batteryTable" class="table table-bordered table-hover table-striped">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal, Waktu</th>
                                        <th>Battery Type</th>
                                        <th>No Unit</th>
                                        <th>No Seri</th>
                                        <th>Keterangan</th>
                                        <th>Dibuat Oleh</th>
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
    </div>

    <!-- Modal Detail Inspeksi -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Inspeksi Battery</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Tanggal:</strong> <span id="modalTanggal"></span></div>
                        <div class="col-md-4"><strong>Waktu:</strong> <span id="modalWaktu"></span></div>
                        <div class="col-md-4"><strong>Dibuat oleh:</strong> <span id="modalUser"></span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Battery Type:</strong> <span id="modalType"></span></div>
                        <div class="col-md-4"><strong>No Unit:</strong> <span id="modalUnit"></span></div>
                        <div class="col-md-4"><strong>No Seri:</strong> <span id="modalSeri"></span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Keterangan:</strong> <span id="modalKeterangan"></span></div>
                    </div>

                    <hr>
                    <h6>Hasil Inspeksi Cell</h6>
                    <table class="table table-bordered detail-table">
                        <thead class="table-light text-nowrap">
                            <tr>
                                <th>Cell</th>
                                <th>Voltase</th>
                                <th>Level Air Aki</th>
                                <th>Intercell</th>
                                <th>Kondisi Skun</th>
                                <th>Kondisi Unit</th>
                                <th>Grounding</th>
                            </tr>
                        </thead>
                        <tbody id="modalDetailBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data Battery #<span id="editBatteryId"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        @csrf
                        <input type="hidden" name="id" id="editId">

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" id="editTanggal" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Waktu</label>
                                <input type="time" name="waktu" id="editWaktu" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Battery Type</label>
                                <input type="text" name="battery_type" id="editBatteryType" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>No Unit</label>
                                <input type="text" name="no_unit" id="editNoUnit" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>No Seri</label>
                                <input type="text" name="no_seri" id="editNoSeri" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Keterangan</label>
                                <input type="text" name="keterangan" id="editKeterangan" class="form-control">
                            </div>
                        </div>

                        <hr>
                        <h6>Detail Cell</h6>
                        <div id="editDetailsContainer" class="row g-3"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSaveEdit">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const API_URL = "{{ url('api/mtc/battery/get-data') }}";
            const DELETE_URL = "{{ url('mtc/data/battery/delete') }}";
            const UPDATE_URL = "{{ url('mtc/data/battery/update') }}";

            // Inisialisasi DataTable
            const table = $('#batteryTable').DataTable({
                ajax: {
                    url: API_URL,
                    data: function(d) {
                        d.date = $('#filterDate').val() || null;
                        d.tipe_baterai = $('#filterTipeBaterai').val() || null;
                        d.unit = $('#filterUnit').val() || null;
                    },
                    dataSrc: function(json) {
                        if (json.status && json.data) return json.data;
                        return [];
                    }
                },
                columns: [{
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: null,
                        render: function(data) {
                            return `
                                <div>
                                    <div>${data.tanggal}</div>
                                    <small class="text-muted">${data.waktu}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'battery_type',
                        defaultContent: '-'
                    },
                    {
                        data: 'no_unit',
                        defaultContent: '-'
                    },
                    {
                        data: 'no_seri',
                        defaultContent: '-'
                    },
                    {
                        data: 'keterangan',
                        defaultContent: '-'
                    },
                    {
                        data: 'user.username',
                        defaultContent: 'Unknown'
                    },
                    {
                        data: null,
                        className: 'text-center text-nowrap',
                        render: function(data) {
                            return `
                                    <button class="btn btn-sm btn-primary btnDetail" data-id="${data.id}" title="Detail"><i class="mdi mdi-eye"></i></button>
                                    <button class="btn btn-sm btn-info btnEdit" data-id="${data.id}" title="Edit"><i class="mdi mdi-pencil"></i></button>
                                    <button class="btn btn-sm btn-danger btnDelete" data-id="${data.id}" title="Hapus"><i class="mdi mdi-delete"></i></button>
                                    <button class="btn btn-sm btn-warning btn-print" data-id="${data.id}" title="Download"><i class="mdi mdi-download"></i></button>
                                `;
                        }
                    }
                ],
                processing: true,
                serverSide: false,
                searching: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                order: [
                    [0, 'asc']
                ],
            });

            // Event tombol Detail
            $('#batteryTable tbody').on('click', '.btnDetail', function() {
                const id = $(this).data('id');
                const rowData = table.row($(this).parents('tr')).data();

                $('#modalBatteryId').text(rowData.id);
                $('#modalTanggal').text(rowData.tanggal);
                $('#modalWaktu').text(rowData.waktu);
                $('#modalType').text(rowData.battery_type || '-');
                $('#modalUnit').text(rowData.no_unit || '-');
                $('#modalSeri').text(rowData.no_seri || '-');
                $('#modalKeterangan').text(rowData.keterangan || '-');
                $('#modalUser').text(rowData.user?.username || 'Unknown');

                let detailHtml = '';
                const sortedDetails = [...rowData.details].sort((a, b) => a.cell - b.cell);

                sortedDetails.forEach(detail => {
                    // Fungsi helper untuk konversi 1/0 ke teks + class
                    const getStatus = (val) => {
                        if (val === 1 || val === true || val === '1') {
                            return {
                                text: 'OK',
                                class: 'status-ok'
                            };
                        } else {
                            return {
                                text: 'Tidak OK',
                                class: 'status-ng'
                            };
                        }
                    };

                    const voltaseStatus = getStatus(detail.voltase);
                    const airAkiStatus = getStatus(detail.level_air_aki);
                    const intercellStatus = getStatus(detail.intercell);
                    const skunStatus = getStatus(detail.kondisi_skun);
                    const unitStatus = getStatus(detail.kondisi_unit);
                    const groundingStatus = getStatus(detail.grounding);

                    detailHtml += `
                        <tr>
                            <td>${detail.cell}</td>
                            <td class="${voltaseStatus.class}">${voltaseStatus.text}</td>
                            <td class="${airAkiStatus.class}">${airAkiStatus.text}</td>
                            <td class="${intercellStatus.class}">${intercellStatus.text}</td>
                            <td class="${skunStatus.class}">${skunStatus.text}</td>
                            <td class="${unitStatus.class}">${unitStatus.text}</td>
                            <td class="${groundingStatus.class}">${groundingStatus.text}</td>
                        </tr>
                    `;
                });

                $('#modalDetailBody').html(detailHtml);
                $('#detailModal').modal('show');
            });

            // Event tombol Edit
            $('#batteryTable tbody').on('click', '.btnEdit', function() {
                const id = $(this).data('id');
                editBattery(id);
            });

            // Event tombol Delete
            $('#batteryTable tbody').on('click', '.btnDelete', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus data ini?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: `${DELETE_URL}/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data derhasil dihapus',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message ||
                                    'Gagal hapus data'
                            });
                        }
                    });
                });
            });

            // Fungsi Edit (buka modal edit)
            function editBattery(id) {
                const rowData = table.rows().every(function() {
                    if (this.data().id == id) {
                        const battery = this.data();

                        $('#editId').val(battery.id);
                        $('#editBatteryId').text(battery.id);
                        $('#editTanggal').val(battery.tanggal);
                        $('#editWaktu').val(battery.waktu);
                        $('#editBatteryType').val(battery.battery_type);
                        $('#editNoUnit').val(battery.no_unit);
                        $('#editNoSeri').val(battery.no_seri);
                        $('#editKeterangan').val(battery.keterangan);

                        let detailHtml = '';
                        const sortedDetails = [...battery.details].sort((a, b) => a.cell - b.cell);

                        sortedDetails.forEach((detail, index) => {
                            // Fungsi helper untuk selected OK/NG berdasarkan 1/0
                            const isOK = (val) => (val === 1 || val === true || val === '1') ?
                                'selected' : '';

                            detailHtml += `
                                <div class="col-md-6">
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-header fw-bold">Cell ${detail.cell}</div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <input type="hidden" name="details[${index}][id]" value="${detail.id}">
                                                <input type="hidden" name="details[${index}][cell]" value="${detail.cell}">

                                                <div class="col-6">
                                                    <label>Voltase</label>
                                                    <select name="details[${index}][voltase]" class="form-select">
                                                        <option value="1" ${isOK(detail.voltase)}>OK</option>
                                                        <option value="0" ${!isOK(detail.voltase) ? 'selected' : ''}>NG</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>Level Air Aki</label>
                                                    <select name="details[${index}][level_air_aki]" class="form-select">
                                                        <option value="1" ${isOK(detail.level_air_aki)}>OK</option>
                                                        <option value="0" ${!isOK(detail.level_air_aki) ? 'selected' : ''}>NG</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>Intercell</label>
                                                    <select name="details[${index}][intercell]" class="form-select">
                                                        <option value="1" ${isOK(detail.intercell)}>OK</option>
                                                        <option value="0" ${!isOK(detail.intercell) ? 'selected' : ''}>NG</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>Kondisi Skun</label>
                                                    <select name="details[${index}][kondisi_skun]" class="form-select">
                                                        <option value="1" ${isOK(detail.kondisi_skun)}>OK</option>
                                                        <option value="0" ${!isOK(detail.kondisi_skun) ? 'selected' : ''}>NG</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>Kondisi Unit</label>
                                                    <select name="details[${index}][kondisi_unit]" class="form-select">
                                                        <option value="1" ${isOK(detail.kondisi_unit)}>OK</option>
                                                        <option value="0" ${!isOK(detail.kondisi_unit) ? 'selected' : ''}>NG</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>Grounding</label>
                                                    <select name="details[${index}][grounding]" class="form-select">
                                                        <option value="1" ${isOK(detail.grounding)}>OK</option>
                                                        <option value="0" ${!isOK(detail.grounding) ? 'selected' : ''}>NG</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        $('#editDetailsContainer').html(detailHtml);
                        $('#editModal').modal('show');
                    }
                });
            }

            $('#filterDate, #filterTipeBaterai, #filterUnit').on('change keyup', function() {
                table.ajax.reload();
            });

            $('#btnApply').on('click', () => table.ajax.reload());

            $('#btnReset').on('click', () => {
                $('#filterDate, #filterUnit, #filterTipeBaterai').val('');
                table.ajax.reload();
            });

            // Simpan edit (sama seperti sebelumnya)
            $('#btnSaveEdit').click(function() {
                const id = $('#editId').val();
                const formData = $('#editForm').serialize();

                $('#editLoading').show();
                $('#btnSaveEdit').prop('disabled', true);

                $.ajax({
                    url: `${UPDATE_URL}/${id}`,
                    method: 'POST',
                    data: formData,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire('Berhasil', 'Data berhasil diperbarui', 'success');
                            $('#editModal').modal('hide');
                            table.ajax.reload(); // reload DataTable
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menyimpan',
                            'error');
                    },
                    complete: function() {
                        $('#editLoading').hide();
                        $('#btnSaveEdit').prop('disabled', false);
                    }
                });
            });

        });
    </script>
@endsection
