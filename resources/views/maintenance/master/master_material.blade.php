@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --ink: #0f172a;
            --ink-soft: #475569;
            --ink-muted: #94a3b8;
            --surface: #ffffff;
            --surface-2: #f8fafc;
            --surface-3: #f1f5f9;
            --border: #e2e8f0;
            --accent: #2563eb;
            --accent-hov: #1d4ed8;
            --accent-bg: #eff6ff;
            --success: #16a34a;
            --success-bg: #f0fdf4;
            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --warning: #d97706;
            --warning-bg: #fffbeb;
            --radius-sm: 6px;
            --radius: 10px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, .07), 0 1px 2px rgba(15, 23, 42, .04);
            --shadow: 0 4px 12px rgba(15, 23, 42, .08), 0 2px 4px rgba(15, 23, 42, .05);
            --transition: .18s cubic-bezier(.4, 0, .2, 1);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow var(--transition), transform var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.blue {
            background: var(--accent-bg);
            color: var(--accent);
        }

        .stat-icon.green {
            background: var(--success-bg);
            color: var(--success);
        }

        .stat-icon.amber {
            background: var(--warning-bg);
            color: var(--warning);
        }

        .stat-icon.purple {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--ink-muted);
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-top: 4px;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border-color: #cbd5e1 !important;
            min-height: 38px !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid pb-5">

            {{-- HEADER --}}
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <span
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:4px 12px;border-radius:20px;background:rgba(37,99,235,.08);color:#2563eb;border:1px solid rgba(37,99,235,.15);display:inline-block;">
                        Master Maintenance &amp; Cost
                    </span>
                    <h3 class="fw-bold fs-3 mb-1 mt-2" style="letter-spacing:-0.5px;">Master Material &amp; Harga</h3>
                    <p class="text-secondary small mb-0 fw-medium">
                        Katalog sparepart, material perawatan, referensi harga per MID, dan integrasi data Warehouse &mdash;
                        <strong>{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</strong>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- <button type="button" class="btn btn-outline-info btn-sm px-3 py-2 fw-semibold" id="btnSyncWarehouse">
                        <i class="ri-refresh-line me-1"></i> Sync Warehouse API
                    </button> --}}
                    <a href="{{ route('master.mtc.material.downloadTemplate') }}"
                        class="btn btn-outline-secondary btn-sm px-3 py-2 fw-semibold">
                        <i class="ri-download-2-line me-1"></i> Download Template
                    </a>
                    <button type="button" class="btn btn-outline-success btn-sm px-3 py-2 fw-semibold"
                        data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                        <i class="ri-file-excel-2-line me-1"></i> Import Excel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm px-3 py-2 fw-semibold" id="btnOpenAddModal">
                        <i class="ri-add-line me-1"></i> Tambah Material
                    </button>
                </div>
            </div>

            {{-- STAT CARDS --}}
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="ri-stack-line"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="cardTotalItems">0</div>
                        <div class="stat-label">Total Material</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="cardItemsWithPrice">0</div>
                        <div class="stat-label">Memiliki Harga</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber">
                        <i class="ri-error-warning-line"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="cardItemsNoPrice">0</div>
                        <div class="stat-label">Belum Ada Harga</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="ri-price-tag-3-line"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="cardAvgPrice" style="font-size: 20px;">Rp 0</div>
                        <div class="stat-label">Rata-rata Harga Satuan</div>
                    </div>
                </div>
            </div>

            {{-- MAIN CARD & TABLE --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 p-4">
                    <div class="row g-3 align-items-center justify-content-between">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-light border"><i class="ri-search-line"></i></span>
                                <input type="text" id="searchInput" class="form-control border"
                                    placeholder="Cari MID, Nama Material, Kategori...">
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-12 d-flex justify-content-md-end gap-2 flex-wrap">
                            <div style="min-width: 180px;">
                                <select id="filterKategori" class="form-select form-select-sm border">
                                    <option value="">Semua Kategori</option>
                                </select>
                            </div>
                            <div style="min-width: 180px;">
                                <select id="filterStatusHarga" class="form-select form-select-sm border">
                                    <option value="">Semua Status Harga</option>
                                    <option value="has_price">Sudah Ada Harga</option>
                                    <option value="no_price">Belum Ada Harga (Rp 0)</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-light btn-sm border px-3" id="btnResetFilter"
                                title="Reset Filter">
                                <i class="ri-refresh-line"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 w-100" style="font-size: 13px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 60px;">NO</th>
                                    <th style="width: 130px;">MID BARANG</th>
                                    <th>NAMA / DESKRIPSI MATERIAL</th>
                                    <th style="width: 90px;">UOM</th>
                                    <th class="text-end" style="width: 160px;">HARGA SATUAN</th>
                                    <th style="width: 150px;">KATEGORI</th>
                                    <th>KETERANGAN</th>
                                    <th style="width: 140px;">TERAKHIR UPDATE</th>
                                    <th class="text-center pe-4" style="width: 110px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                        Memuat data material...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION CONTROLS --}}
                    <div class="d-flex justify-content-between align-items-center p-3 border-top flex-wrap gap-2">
                        <div class="text-muted small" id="paginationInfo">Menampilkan 0 data</div>
                        <ul class="pagination pagination-sm mb-0" id="paginationList"></ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ════════════════════ MODAL ADD / EDIT MATERIAL ════════════════════ --}}
    <div class="modal fade" id="modalMaterial" tabindex="-1" aria-labelledby="modalMaterialTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark fs-6" id="modalMaterialTitle">Tambah Material Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formMaterial">
                    @csrf
                    <input type="hidden" id="materialId" name="id">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">MID Barang (Warehouse API)</label>
                                <div class="input-group">
                                    <select class="form-control" id="selectMid" name="mid_select" style="width: 100%;">
                                        <option value="">Pilih / Cari MID dari Warehouse</option>
                                    </select>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">Pilih MID untuk mengisi deskripsi & UoM
                                    otomatis dari API Warehouse</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Input MID Manual (Opsional)</label>
                                <input type="text" class="form-control" id="inputMid" name="mid"
                                    placeholder="Contoh: 60021589">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Nama / Deskripsi Material <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="inputDeskripsi" name="deskripsi" required
                                    placeholder="Contoh: Filter Oli Forklift">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Satuan Unit (UOM)</label>
                                <input type="text" class="form-control" id="inputUom" name="uom"
                                    placeholder="PCS / UN / SET / LTR">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Harga Satuan (Rp) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border">Rp</span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="inputHarga" name="harga" required placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Kategori Material</label>
                                <input type="text" class="form-control" id="inputKategori" name="kategori"
                                    list="kategoriOptions" placeholder="Pilih atau ketik kategori...">
                                <datalist id="kategoriOptions">
                                    <option value="Forklift Part">
                                    <option value="Electrical">
                                    <option value="Motor Pump">
                                    <option value="Utility">
                                    <option value="Refrigerasi">
                                    <option value="Engine Diesel">
                                    <option value="Engine Electric">
                                    <option value="Consumable">
                                    <option value="Umum">
                                </datalist>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Keterangan / Catatan</label>
                                <textarea class="form-control" id="inputKeterangan" name="keterangan" rows="2"
                                    placeholder="Catatan tambahan spesifikasi atau peruntukan unit"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-3 px-4 border-top">
                        <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSaveMaterial">
                            <i class="ri-save-line me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ════════════════════ MODAL IMPORT EXCEL ════════════════════ --}}
    <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark fs-6" id="modalImportExcelTitle">
                        <i class="ri-file-excel-2-line text-success me-2"></i>Import Master Material &amp; Harga by Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formImportExcel" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 d-flex align-items-start gap-2 mb-3"
                            style="font-size: 12.5px;">
                            <i class="ri-information-line fs-5"></i>
                            <div>
                                Gunakan template excel yang telah disediakan agar format kolom sesuai. Jika baris memiliki
                                <strong>MID</strong> yang telah terdaftar, harga dan data akan otomatis diperbarui.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Pilih File Excel (.xlsx, .xls, .csv) <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="file_excel" id="fileExcelInput"
                                accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="{{ route('master.mtc.material.downloadTemplate') }}"
                                class="btn btn-link btn-sm text-decoration-none p-0 text-primary fw-semibold">
                                <i class="ri-download-line me-1"></i> Download Format Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-3 px-4 border-top">
                        <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitImport">
                            <i class="ri-upload-cloud-2-line me-1"></i> Upload &amp; Proses
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let allMaterials = [];
            let filteredMaterials = [];
            let currentPage = 1;
            const pageSize = 15;

            // Load Initial Data
            loadMaterialData();

            function loadMaterialData() {
                const kategori = $('#filterKategori').val();
                const statusHarga = $('#filterStatusHarga').val();

                $.ajax({
                    url: "{{ route('master.mtc.material.data') }}",
                    type: "GET",
                    data: {
                        kategori: kategori,
                        status_harga: statusHarga
                    },
                    success: function(res) {
                        if (res.status) {
                            allMaterials = res.data || [];

                            // Render KPI cards
                            $('#cardTotalItems').text((res.summary.total_items || 0).toLocaleString(
                                'id-ID'));
                            $('#cardItemsWithPrice').text((res.summary.items_with_price || 0)
                                .toLocaleString('id-ID'));
                            $('#cardItemsNoPrice').text((res.summary.items_no_price || 0)
                                .toLocaleString('id-ID'));
                            $('#cardAvgPrice').text('Rp ' + Math.round(res.summary.avg_price || 0)
                                .toLocaleString('id-ID'));

                            // Populate Kategori dropdown filter
                            const currentCat = $('#filterKategori').val();
                            let catOptions = '<option value="">Semua Kategori</option>';
                            (res.summary.categories || []).forEach(cat => {
                                catOptions +=
                                    `<option value="${cat}" ${currentCat === cat ? 'selected' : ''}>${cat}</option>`;
                            });
                            $('#filterKategori').html(catOptions);

                            applyFilter();
                        }
                    },
                    error: function() {
                        $('#tableBody').html(
                            '<tr><td colspan="9" class="text-center py-5 text-danger">Gagal memuat data material.</td></tr>'
                            );
                    }
                });
            }

            // Search and Filter handler
            function applyFilter() {
                const q = $('#searchInput').val().trim().toLowerCase();
                const kategori = $('#filterKategori').val();
                const statusHarga = $('#filterStatusHarga').val();

                filteredMaterials = allMaterials.filter(item => {
                    if (kategori && item.kategori !== kategori) return false;
                    if (statusHarga === 'has_price' && item.harga <= 0) return false;
                    if (statusHarga === 'no_price' && item.harga > 0) return false;

                    if (q) {
                        const searchStr =
                            `${item.mid || ''} ${item.deskripsi || ''} ${item.kategori || ''} ${item.keterangan || ''}`
                            .toLowerCase();
                        if (!searchStr.includes(q)) return false;
                    }
                    return true;
                });

                currentPage = 1;
                renderTable();
            }

            $('#searchInput').on('input', applyFilter);
            $('#filterKategori, #filterStatusHarga').on('change', function() {
                loadMaterialData();
            });
            $('#btnResetFilter').on('click', function() {
                $('#searchInput').val('');
                $('#filterKategori').val('');
                $('#filterStatusHarga').val('');
                loadMaterialData();
            });

            // Render Table with Pagination
            function renderTable() {
                const total = filteredMaterials.length;
                const totalPages = Math.ceil(total / pageSize) || 1;
                if (currentPage > totalPages) currentPage = totalPages;
                if (currentPage < 1) currentPage = 1;

                const startIdx = (currentPage - 1) * pageSize;
                const endIdx = Math.min(startIdx + pageSize, total);
                const pageItems = filteredMaterials.slice(startIdx, endIdx);

                if (total === 0) {
                    $('#tableBody').html(
                        '<tr><td colspan="9" class="text-center py-5 text-muted">Tidak ada data material yang sesuai.</td></tr>'
                        );
                    $('#paginationInfo').text('Menampilkan 0 data');
                    $('#paginationList').empty();
                    return;
                }

                let html = '';
                pageItems.forEach((item, idx) => {
                    const no = startIdx + idx + 1;
                    const priceBadge = item.harga > 0 ?
                        `<span class="fw-bold text-success">${item.harga_fmt}</span>` :
                        `<span class="badge bg-warning-subtle text-warning border border-warning-subtle">Rp 0 (Belum diisi)</span>`;

                    const midBadge = item.mid && item.mid !== '-' ?
                        `<span class="badge bg-light text-dark font-monospace border">${item.mid}</span>` :
                        `<span class="text-muted">—</span>`;

                    html += `
                        <tr>
                            <td class="ps-4 text-secondary fw-semibold">${no}</td>
                            <td>${midBadge}</td>
                            <td>
                                <span class="fw-bold text-dark">${item.deskripsi}</span>
                            </td>
                            <td><span class="badge bg-secondary-subtle text-secondary">${item.uom}</span></td>
                            <td class="text-end">${priceBadge}</td>
                            <td><span class="badge bg-primary-subtle text-primary">${item.kategori}</span></td>
                            <td class="text-muted small">${item.keterangan || '-'}</td>
                            <td class="text-muted small">${item.updated_at}</td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit py-1 px-2" data-item='${JSON.stringify(item)}' title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete py-1 px-2" data-id="${item.id}" data-name="${item.deskripsi}" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $('#tableBody').html(html);
                $('#paginationInfo').text(`Menampilkan ${startIdx + 1} - ${endIdx} dari ${total} material`);

                // Pagination buttons
                let pagHtml = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage - 1})">‹</a>
                </li>`;

                for (let p = 1; p <= totalPages; p++) {
                    if (p === 1 || p === totalPages || (p >= currentPage - 2 && p <= currentPage + 2)) {
                        pagHtml += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0)" onclick="goToPage(${p})">${p}</a>
                        </li>`;
                    } else if (p === currentPage - 3 || p === currentPage + 3) {
                        pagHtml += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
                    }
                }

                pagHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage + 1})">›</a>
                </li>`;

                $('#paginationList').html(pagHtml);
            }

            window.goToPage = function(p) {
                currentPage = p;
                renderTable();
            };

            // Init Select2 for Warehouse API MID Autocomplete
            $('#selectMid').select2({
                dropdownParent: $('#modalMaterial'),
                theme: 'bootstrap-5',
                placeholder: 'Cari MID atau Nama Barang dari Warehouse...',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: 'http://10.11.10.130:8087/api/wsp/barang',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: (response.data || []).map(function(item) {
                                return {
                                    id: item.mid_barang,
                                    text: item.mid_barang + ' - ' + item.nama_barang,
                                    nama_barang: item.nama_barang,
                                    uom: item.uom,
                                    qty: item.latest_stock?.qty_soh ?? 0
                                };
                            })
                        };
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (!data.id) return data.text;
                    return $(`
                        <div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">${data.id}</span>
                                <span class="badge bg-primary">${data.qty ?? 0} ${data.uom || ''}</span>
                            </div>
                            <div class="text-muted small">${data.nama_barang}</div>
                        </div>
                    `);
                },
                templateSelection: function(data) {
                    return data.id ? `${data.id} - ${data.nama_barang || data.text}` : data.text;
                }
            }).on('select2:select', function(e) {
                const item = e.params.data;
                $('#inputMid').val(item.id || '');
                $('#inputDeskripsi').val(item.nama_barang || '');
                $('#inputUom').val(item.uom || '');
            });

            // Open Add Modal
            $('#btnOpenAddModal').on('click', function() {
                $('#formMaterial')[0].reset();
                $('#materialId').val('');
                $('#selectMid').val(null).trigger('change');
                $('#modalMaterialTitle').text('Tambah Material Baru');
                $('#modalMaterial').modal('show');
            });

            // Open Edit Modal
            $(document).on('click', '.btn-edit', function() {
                const item = $(this).data('item');
                $('#formMaterial')[0].reset();
                $('#materialId').val(item.id);
                $('#inputMid').val(item.mid !== '-' ? item.mid : '');
                $('#inputDeskripsi').val(item.deskripsi);
                $('#inputUom').val(item.uom !== '-' ? item.uom : '');
                $('#inputHarga').val(item.harga);
                $('#inputKategori').val(item.kategori !== '-' ? item.kategori : '');
                $('#inputKeterangan').val(item.keterangan !== '-' ? item.keterangan : '');

                $('#selectMid').val(null).trigger('change');
                $('#modalMaterialTitle').text('Edit Material: ' + item.deskripsi);
                $('#modalMaterial').modal('show');
            });

            // Save Material Form Submit (Add or Update)
            $('#formMaterial').on('submit', function(e) {
                e.preventDefault();
                const id = $('#materialId').val();
                const isEdit = Boolean(id);
                const url = isEdit ? `{{ url('mtc/master/material/update') }}/${id}` :
                    "{{ route('master.mtc.material.store') }}";

                $('#btnSaveMaterial').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#btnSaveMaterial').prop('disabled', false).html(
                            '<i class="ri-save-line me-1"></i> Simpan Data');
                        if (res.status) {
                            $('#modalMaterial').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                            loadMaterialData();
                        } else {
                            Swal.fire('Perhatian', res.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        $('#btnSaveMaterial').prop('disabled', false).html(
                            '<i class="ri-save-line me-1"></i> Simpan Data');
                        const msg = xhr.responseJSON?.message ||
                            'Terjadi kesalahan saat menyimpan data.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Delete Material
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Hapus Material?',
                    text: `Anda yakin ingin menghapus "${name}" dari master material?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('mtc/master/material/delete') }}/${id}`,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: res.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    loadMaterialData();
                                } else {
                                    Swal.fire('Gagal', res.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Gagal menghapus material.',
                                'error');
                            }
                        });
                    }
                });
            });

            // Upload Excel Submit
            $('#formImportExcel').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $('#btnSubmitImport').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Mengimpor data...');

                $.ajax({
                    url: "{{ route('master.mtc.material.uploadExcel') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#btnSubmitImport').prop('disabled', false).html(
                            '<i class="ri-upload-cloud-2-line me-1"></i> Upload &amp; Proses'
                            );
                        if (res.status) {
                            $('#modalImportExcel').modal('hide');
                            $('#formImportExcel')[0].reset();
                            Swal.fire({
                                icon: 'success',
                                title: 'Import Selesai',
                                text: res.message
                            });
                            loadMaterialData();
                        } else {
                            Swal.fire('Gagal Import', res.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        $('#btnSubmitImport').prop('disabled', false).html(
                            '<i class="ri-upload-cloud-2-line me-1"></i> Upload &amp; Proses'
                            );
                        const msg = xhr.responseJSON?.message ||
                            'Terjadi kesalahan saat mengunggah file.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Sync Warehouse API Button
            $('#btnSyncWarehouse').on('click', function() {
                Swal.fire({
                    title: 'Sinkronkan dengan Warehouse API?',
                    text: 'Katalog barang dari API Warehouse akan ditarik dan dimasukkan ke Master Material.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Sinkronkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Sinkronisasi...',
                            text: 'Sedang mengambil data barang dari Warehouse...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('master.mtc.material.syncWarehouse') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire('Sukses', res.message, 'success');
                                    loadMaterialData();
                                } else {
                                    Swal.fire('Perhatian', res.message, 'warning');
                                }
                            },
                            error: function(xhr) {
                                const msg = xhr.responseJSON?.message ||
                                    'Gagal sinkronisasi data Warehouse.';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
