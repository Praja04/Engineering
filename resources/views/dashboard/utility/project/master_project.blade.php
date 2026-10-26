@extends('layouts.app')

@section('title', 'Master Area & Kategori Project Utility')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Header Banner ── --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
                        <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-settings-4-line text-warning me-2"></i>
                                    Master Area & Kategori Project Utility
                                </h4>
                                <p class="text-white-50 mb-0 fs-13">
                                    Kelola daftar area plant dan kategori pekerjaan secara fleksibel sesuai perkembangan
                                    operasional
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('utility.project.data') }}"
                                    class="btn btn-light btn-sm fw-semibold shadow-sm">
                                    <i class="ri-table-line me-1"></i> Data Project
                                </a>
                                <a href="{{ route('utility.project.form') }}"
                                    class="btn btn-warning btn-sm fw-semibold shadow-sm">
                                    <i class="ri-add-line me-1"></i> Form Project
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Nav Tabs & Content ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom p-0">
                            <ul class="nav nav-tabs nav-tabs-custom nav-success p-2 pb-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#tabArea"
                                        role="tab">
                                        <i class="ri-community-line me-1"></i> Master Area Project
                                        <span id="badgeAreaCount" class="badge bg-primary-subtle text-primary ms-1">-</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tabCategory" role="tab">
                                        <i class="ri-price-tag-3-line me-1"></i> Master Kategori Project
                                        <span id="badgeCategoryCount"
                                            class="badge bg-primary-subtle text-primary ms-1">-</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            <div class="tab-content text-muted">

                                {{-- ── TAB 1: MASTER AREA ── --}}
                                <div class="tab-pane active" id="tabArea" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Daftar Area Plant Project</h6>
                                            <small class="text-muted">Area kerja untuk pengelompokan project engineering
                                                utility</small>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalAddArea">
                                            <i class="ri-add-line me-1"></i> Tambah Area Baru
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-nowrap mb-0">
                                            <thead class="table-light fs-12 text-muted">
                                                <tr>
                                                    <th style="width: 40px;" class="text-center">No</th>
                                                    <th style="min-width: 150px;">Nama Area</th>
                                                    <th style="width: 120px;" class="text-center">Kode Prefix</th>
                                                    <th>Deskripsi</th>
                                                    <th style="width: 120px;" class="text-center">Project Terkait</th>
                                                    <th style="width: 100px;" class="text-center">Status</th>
                                                    <th style="width: 120px;" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="areaTableBody">
                                                <tr>
                                                    <td colspan="7" class="text-center py-4 text-muted">
                                                        <div class="spinner-border text-primary spinner-border-sm me-2"
                                                            role="status"></div>
                                                        Memuat data area...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- ── TAB 2: MASTER KATEGORI ── --}}
                                <div class="tab-pane" id="tabCategory" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Daftar Kategori Project</h6>
                                            <small class="text-muted">Kategori target kinerja & inisiatif pekerjaan
                                                utility</small>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalAddCategory">
                                            <i class="ri-add-line me-1"></i> Tambah Kategori Baru
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-nowrap mb-0">
                                            <thead class="table-light fs-12 text-muted">
                                                <tr>
                                                    <th style="width: 40px;" class="text-center">No</th>
                                                    <th style="min-width: 180px;">Nama Kategori</th>
                                                    <th>Deskripsi</th>
                                                    <th style="width: 120px;" class="text-center">Project Terkait</th>
                                                    <th style="width: 100px;" class="text-center">Status</th>
                                                    <th style="width: 120px;" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="categoryTableBody">
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-muted">
                                                        <div class="spinner-border text-primary spinner-border-sm me-2"
                                                            role="status"></div>
                                                        Memuat data kategori...
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

        </div>
    </div>

    {{-- ── MODAL ADD AREA ── --}}
    <div class="modal fade" id="modalAddArea" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="ri-community-line text-primary me-2"></i>Tambah
                        Area Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAddArea" action="{{ route('utility.project.master.area.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nama Area <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_area"
                                placeholder="Contoh: Chiller / Genset / ETP" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Kode Prefix (Opsional)</label>
                            <input type="text" class="form-control" name="kode_area" placeholder="Contoh: CHL / GEN">
                            <small class="text-muted fs-11">Digunakan untuk penomoran otomatis project
                                (PRJ-XXX-001)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="2" placeholder="Keterangan cakupan area kerja..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitAddArea" class="btn btn-primary"><i
                                class="ri-save-line me-1"></i> Simpan Area</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── MODAL EDIT AREA ── --}}
    <div class="modal fade" id="modalEditArea" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="ri-pencil-line text-primary me-2"></i>Edit Area
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditArea" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nama Area <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_area_nama" name="nama_area" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Kode Prefix (Opsional)</label>
                            <input type="text" class="form-control" id="edit_area_kode" name="kode_area">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Deskripsi</label>
                            <textarea class="form-control" id="edit_area_deskripsi" name="deskripsi" rows="2"></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_area_active" name="is_active"
                                value="1">
                            <label class="form-check-label text-dark" for="edit_area_active">Status Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitEditArea" class="btn btn-primary"><i
                                class="ri-save-line me-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── MODAL ADD CATEGORY ── --}}
    <div class="modal fade" id="modalAddCategory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="ri-price-tag-3-line text-primary me-2"></i>Tambah
                        Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAddCategory" action="{{ route('utility.project.master.category.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nama Kategori <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_kategori"
                                placeholder="Contoh: Efisiensi Energi / Safety K3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="2"
                                placeholder="Penjelasan fokus pekerjaan untuk kategori ini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitAddCategory" class="btn btn-primary"><i
                                class="ri-save-line me-1"></i> Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── MODAL EDIT CATEGORY ── --}}
    <div class="modal fade" id="modalEditCategory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="ri-pencil-line text-primary me-2"></i>Edit
                        Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditCategory" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nama Kategori <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_cat_nama" name="nama_kategori" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Deskripsi</label>
                            <textarea class="form-control" id="edit_cat_deskripsi" name="deskripsi" rows="2"></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_cat_active" name="is_active"
                                value="1">
                            <label class="form-check-label text-dark" for="edit_cat_active">Status Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitEditCategory" class="btn btn-primary"><i
                                class="ri-save-line me-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // ── Setup AJAX CSRF Token ──
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            function escapeHtml(str) {
                if (!str) return '';
                return $('<div>').text(str).html();
            }

            // ── Load Master Data (Area & Kategori) via Async jQuery AJAX ──
            function loadMasterData() {
                $('#areaTableBody').html(`
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                    Memuat data area...
                </td>
            </tr>
        `);

                $('#categoryTableBody').html(`
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                    Memuat data kategori...
                </td>
            </tr>
        `);

                $.ajax({
                    url: '{{ route('utility.project.master') }}',
                    type: 'GET',
                    data: {
                        format: 'json'
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            // 1. Render Area
                            const areas = res.areas || [];
                            $('#badgeAreaCount').text(areas.length);

                            if (areas.length === 0) {
                                $('#areaTableBody').html(`
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data area master.</td>
                            </tr>
                        `);
                            } else {
                                let areaHtml = '';
                                $.each(areas, function(idx, area) {
                                    const statusBadge = area.is_active ?
                                        '<span class="badge bg-success-subtle text-success">Aktif</span>' :
                                        '<span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>';

                                    areaHtml += `
                                <tr id="row-area-${area.id}">
                                    <td class="text-center fw-semibold text-muted">${idx + 1}</td>
                                    <td>
                                        <span class="fw-bold text-dark fs-13">${escapeHtml(area.nama_area)}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-secondary border font-monospace">${escapeHtml(area.kode_area || '-')}</span>
                                    </td>
                                    <td class="fs-12 text-muted">
                                        ${escapeHtml(area.deskripsi || '-')}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary">${area.projects_count || 0} Project</span>
                                    </td>
                                    <td class="text-center">
                                        ${statusBadge}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-area"
                                            data-id="${area.id}"
                                            data-nama="${escapeHtml(area.nama_area)}"
                                            data-kode="${escapeHtml(area.kode_area || '')}"
                                            data-deskripsi="${escapeHtml(area.deskripsi || '')}"
                                            data-active="${area.is_active ? 1 : 0}"
                                            title="Edit Area">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-area"
                                            data-id="${area.id}"
                                            data-nama="${escapeHtml(area.nama_area)}"
                                            title="Hapus Area">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                                });
                                $('#areaTableBody').html(areaHtml);
                            }

                            // 2. Render Kategori
                            const categories = res.categories || [];
                            $('#badgeCategoryCount').text(categories.length);

                            if (categories.length === 0) {
                                $('#categoryTableBody').html(`
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data kategori master.</td>
                            </tr>
                        `);
                            } else {
                                let catHtml = '';
                                $.each(categories, function(idx, cat) {
                                    const statusBadge = cat.is_active ?
                                        '<span class="badge bg-success-subtle text-success">Aktif</span>' :
                                        '<span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>';

                                    catHtml += `
                                <tr id="row-cat-${cat.id}">
                                    <td class="text-center fw-semibold text-muted">${idx + 1}</td>
                                    <td>
                                        <span class="fw-bold text-dark fs-13">${escapeHtml(cat.nama_kategori)}</span>
                                    </td>
                                    <td class="fs-12 text-muted">
                                        ${escapeHtml(cat.deskripsi || '-')}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary">${cat.projects_count || 0} Project</span>
                                    </td>
                                    <td class="text-center">
                                        ${statusBadge}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-cat"
                                            data-id="${cat.id}"
                                            data-nama="${escapeHtml(cat.nama_kategori)}"
                                            data-deskripsi="${escapeHtml(cat.deskripsi || '')}"
                                            data-active="${cat.is_active ? 1 : 0}"
                                            title="Edit Kategori">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-cat"
                                            data-id="${cat.id}"
                                            data-nama="${escapeHtml(cat.nama_kategori)}"
                                            title="Hapus Kategori">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                                });
                                $('#categoryTableBody').html(catHtml);
                            }
                        }
                    },
                    error: function() {
                        $('#areaTableBody').html(
                            '<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data area dari server.</td></tr>'
                        );
                        $('#categoryTableBody').html(
                            '<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data kategori dari server.</td></tr>'
                        );
                    }
                });
            }

            // ── Load Data on Initial Ready ──
            loadMasterData();

            // ── Form Add Area via AJAX ──
            $('#formAddArea').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitAddArea');
                const origText = $btn.html();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#modalAddArea').modal('hide');
                            $('#formAddArea')[0].reset();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadMasterData();
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal menyimpan area.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });

            // ── Edit Area Modal ──
            $(document).on('click', '.btn-edit-area', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const kode = $(this).data('kode');
                const desk = $(this).data('deskripsi');
                const active = $(this).data('active') == 1;

                $('#formEditArea').attr('action', `{{ url('utility/project/master/area') }}/${id}`);
                $('#edit_area_nama').val(nama);
                $('#edit_area_kode').val(kode);
                $('#edit_area_deskripsi').val(desk);
                $('#edit_area_active').prop('checked', active);

                $('#modalEditArea').modal('show');
            });

            // ── Form Edit Area via AJAX ──
            $('#formEditArea').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitEditArea');
                const origText = $btn.html();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#modalEditArea').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadMasterData();
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memperbarui area.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });

            // ── Delete Area via Async jQuery AJAX ──
            $(document).on('click', '.btn-delete-area', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Area?',
                    text: `Anda yakin ingin menghapus area "${nama}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('utility/project/master/area') }}/${id}`,
                            type: 'DELETE',
                            dataType: 'json',
                            success: function(res) {
                                if (res.success) {
                                    $(`#row-area-${id}`).fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: res.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    loadMasterData();
                                } else {
                                    Swal.fire('Tidak Dapat Dihapus', res.message,
                                        'warning');
                                }
                            },
                            error: function(xhr) {
                                let msg = 'Gagal menghapus area.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // ── Form Add Category via AJAX ──
            $('#formAddCategory').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitAddCategory');
                const origText = $btn.html();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#modalAddCategory').modal('hide');
                            $('#formAddCategory')[0].reset();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadMasterData();
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal menyimpan kategori.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });

            // ── Edit Category Modal ──
            $(document).on('click', '.btn-edit-cat', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const desk = $(this).data('deskripsi');
                const active = $(this).data('active') == 1;

                $('#formEditCategory').attr('action', `{{ url('utility/project/master/category') }}/${id}`);
                $('#edit_cat_nama').val(nama);
                $('#edit_cat_deskripsi').val(desk);
                $('#edit_cat_active').prop('checked', active);

                $('#modalEditCategory').modal('show');
            });

            // ── Form Edit Category via AJAX ──
            $('#formEditCategory').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitEditCategory');
                const origText = $btn.html();
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#modalEditCategory').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadMasterData();
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memperbarui kategori.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });

            // ── Delete Category via Async jQuery AJAX ──
            $(document).on('click', '.btn-delete-cat', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Kategori?',
                    text: `Anda yakin ingin menghapus kategori "${nama}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('utility/project/master/category') }}/${id}`,
                            type: 'DELETE',
                            dataType: 'json',
                            success: function(res) {
                                if (res.success) {
                                    $(`#row-cat-${id}`).fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: res.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    loadMasterData();
                                } else {
                                    Swal.fire('Tidak Dapat Dihapus', res.message,
                                        'warning');
                                }
                            },
                            error: function(xhr) {
                                let msg = 'Gagal menghapus kategori.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
