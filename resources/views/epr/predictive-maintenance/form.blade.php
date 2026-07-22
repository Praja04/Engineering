@extends('layouts.app')

@section('title', 'Predictive Maintenance Form')

@section('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }

    /* Photo grid */
    .photo-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
    .photo-slot {
        aspect-ratio: 1; border: 2px dashed var(--vz-border-color); border-radius: 10px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        cursor: pointer; position: relative; overflow: hidden; transition: all .2s;
        background: var(--vz-input-bg);
    }
    .photo-slot:hover { border-color: #3b82f6; background: rgba(59,130,246,.04); }
    .photo-slot img {
        position: absolute; inset: 0; width: 100%; height: 100%;
        object-fit: cover; border-radius: 8px;
    }
    .photo-slot .photo-del {
        position: absolute; top: 4px; right: 4px;
        background: rgba(239,68,68,.9); color: #fff;
        border: none; border-radius: 50%; width: 22px; height: 22px;
        font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center;
        z-index: 2;
    }
    .photo-slot.filled { border-style: solid; border-color: var(--vz-border-color); }

    /* Status radio buttons */
    .status-radio-group { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
    .status-radio-btn {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        padding: 10px 8px; border-radius: 8px;
        border: 2px solid var(--vz-border-color); background: var(--vz-input-bg);
        font-size: 13px; font-weight: 600; color: var(--vz-body-color);
        cursor: pointer; transition: all .15s; text-align: center;
    }
    .status-radio-btn:hover { border-color: #94a3b8; }
    .status-radio-btn.active-open     { border-color: #3b82f6; background: rgba(59,130,246,.08); color: #3b82f6; }
    .status-radio-btn.active-progress { border-color: #f59e0b; background: rgba(245,158,11,.08); color: #f59e0b; }
    .status-radio-btn.active-done     { border-color: #22c55e; background: rgba(34,197,94,.08);  color: #22c55e; }
    .status-radio-btn.active-onhold   { border-color: #ef4444; background: rgba(239,68,68,.08);  color: #ef4444; }

    .badge-status {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: .4px;
        text-transform: uppercase;
    }
    .badge-open     { background: rgba(59,130,246,.15);  color: #3b82f6; }
    .badge-progress { background: rgba(245,158,11,.15); color: #f59e0b; }
    .badge-done     { background: rgba(34,197,94,.15);  color: #22c55e; }
    .badge-onhold   { background: rgba(239,68,68,.15);  color: #ef4444; }

    @media (max-width: 576px) {
        .photo-grid { grid-template-columns: repeat(2, 1fr); }
        .status-radio-group { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')
@php
    $isOperator = Auth::user()->jabatan === 'operator';
@endphp
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Header (Reference Style Banner) --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-0"
                    style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%); border-radius: 12px;">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-edit-box-line text-info me-2"></i>
                                EPR — Predictive Maintenance Form
                            </h4>
                            <p class="text-white-50 mb-0">
                                Masukkan data hasil temuan dan log pekerjaan inspeksi lapangan
                            </p>
                        </div>
                        <a href="{{ route('epr.pm.data') }}" class="btn btn-outline-light rounded-pill btn-sm px-3">
                            <i class="ri-database-2-line me-1"></i> Lihat Riwayat Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body p-4 bg-white">
                        
                        {{-- Edit/Update Banner --}}
                        <div class="alert alert-warning d-none mb-4" id="editBanner">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong><i class="ri-edit-line me-1"></i> Mode Edit Laporan</strong>
                                    <div class="small" id="editBannerSub"></div>
                                </div>
                                <button class="btn btn-sm btn-outline-warning" onclick="cancelEdit()">Batal</button>
                            </div>
                        </div>
                        <div class="alert alert-info d-none mb-4" id="updateBanner">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong><i class="ri-arrow-go-forward-line me-1"></i> Update Lanjutan</strong>
                                    <div class="small" id="updateBannerSub"></div>
                                </div>
                                <button class="btn btn-sm btn-outline-info" onclick="cancelEdit()">Batal</button>
                            </div>
                        </div>

                        <form id="formReport">
                            @csrf
                            <input type="hidden" id="f-id" name="id">
                            <input type="hidden" id="f-parentId" name="parentId">

                            {{-- Section 1: Informasi Dasar --}}
                            <div class="mb-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="ri-file-list-3-line me-1"></i> Informasi Dasar
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row mb-4 g-3">
                                <div class="col-md-6" id="woSelectWrap">
                                    <label class="form-label small fw-bold">Rujukan Work Order (WO)</label>
                                    <select class="form-select" id="f-workOrderId" name="workOrderId" onchange="selectWorkOrder()">
                                        <option value="">-- Pekerjaan Mandiri (Tanpa WO) --</option>
                                    </select>
                                    <small class="text-muted" style="font-size: 10px;">Pilih Work Order aktif yang ditugaskan kepada Anda (jika ada)</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="f-date" name="date" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Nama Teknisi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="f-tech" name="tech" value="{{ Auth::user()->username }}" {{ $isOperator ? 'readonly' : '' }} required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Area / Unit <span class="text-danger">*</span></label>
                                    <select class="form-select" id="f-area" name="area" required>
                                        <option value="">Pilih area...</option>
                                        <option>Filling Retail</option>
                                        <option>Packing Retail</option>
                                        <option>Gravity Roller</option>
                                        <option>Workshop</option>
                                        <option>Pasteur</option>
                                        <option>Storage</option>
                                        <option>Lainnya</option>
                                    </select>
                                </div>

                                <div class="col-md-6 d-flex align-items-center" style="padding-top: 1.8rem;">
                                    <div class="w-100">
                                        <div class="mb-2" id="adhocSwitchWrap">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="f-adhoc" onchange="toggleAdhoc()">
                                                <label class="form-check-label fw-semibold" for="f-adhoc">
                                                    <i class="ri-flashlight-line text-warning me-1"></i> Pekerjaan Ad-hoc (Verbal)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="d-none" id="adhocTitleWrap">
                                            <input type="text" class="form-control form-control-sm" id="f-adhocTitle" name="adhocTitle" placeholder="cth: Ganti bearing D7, Perbaikan conveyor...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Data Hasil Inspeksi --}}
                            <div class="mb-3">
                                <h6 class="fw-bold text-success mb-2">
                                    <i class="ri-dashboard-3-line me-1"></i> Data Hasil Inspeksi
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Pekerjaan / Uraian <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="f-work" name="work" rows="3" placeholder="Uraian pekerjaan yang dilakukan..." required></textarea>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="f-timeStart" name="timeStart" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="f-timeEnd" name="timeEnd" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Status Pekerjaan <span class="text-danger">*</span></label>
                                    <input type="hidden" id="f-status" name="status" value="progress">
                                    <div class="status-radio-group">
                                        <div class="status-radio-btn" data-val="open" onclick="selectStatus('open')">
                                            🔵 Open
                                        </div>
                                        <div class="status-radio-btn active-progress" data-val="progress" onclick="selectStatus('progress')">
                                            🟡 Progress
                                        </div>
                                        <div class="status-radio-btn" data-val="done" onclick="selectStatus('done')">
                                            🟢 Done
                                        </div>
                                        <div class="status-radio-btn" data-val="onhold" onclick="selectStatus('onhold')">
                                            🔴 On Hold
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold">Keterangan / Temuan</label>
                                    <textarea class="form-control" id="f-notes" name="notes" rows="2" placeholder="Temuan, kendala, atau catatan tambahan..."></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold">Foto Dokumentasi <span class="text-muted fw-normal">(Maks 4, dikompres otomatis)</span></label>
                                    <div class="photo-grid" id="photoGrid"></div>
                                    <input type="file" id="photoInput" accept="image/*" style="display:none;" multiple>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <button type="submit" class="btn btn-primary w-100 py-2 fs-15 shadow-sm" id="btnSubmit">
                                <i class="ri-send-plane-line me-1"></i>
                                <span id="submitLabel">Kirim Laporan</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

            {{-- Optional Today's logs info block --}}
            <div class="card mt-4 border-0 shadow-sm" style="border-radius: 12px; display:none;" id="todayLogsCard">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="ri-history-line text-muted me-2"></i>Laporan Hari Ini</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Area</th>
                                    <th>Pekerjaan</th>
                                    <th>Jam</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="todayLogsBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

{{-- Lightbox --}}
<div id="lightbox" style="display:none; position:fixed; inset:0; z-index:2000; background:rgba(0,0,0,.92); align-items:center; justify-content:center;" onclick="closeLightbox()">
    <button style="position:absolute; top:16px; right:20px; background:rgba(255,255,255,.15); border:none; color:#fff; font-size:20px; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center;" onclick="closeLightbox()">✕</button>
    <button class="lb-nav lb-prev" onclick="lbNav(-1,event)" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:none; color:#fff; font-size:20px; cursor:pointer; width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center;">‹</button>
    <img id="lbImg" src="" style="max-width:92vw; max-height:86vh; object-fit:contain; border-radius:8px;">
    <button class="lb-nav lb-next" onclick="lbNav(1,event)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:none; color:#fff; font-size:20px; cursor:pointer; width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center;">›</button>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    let photos  = []; // { file, dataUrl, existing, path }
    let myWorkOrders = [];
    let editMode   = null; // null | 'edit' | 'update'
    let editId     = null;
    let parentId   = null;
    let lbImages   = [];
    let lbIdx      = 0;
    const MAX_PHOTOS = 4;

    const urlParams = new URLSearchParams(window.location.search);
    const paramEditId = urlParams.get('edit');
    const paramParentId = urlParams.get('parent');

    renderPhotoGrid();
    loadMyWorkOrders().then(() => {
        initFormState(paramEditId, paramParentId);
    });

    // ════════════════════════════════════════
    // LOAD WORK ORDERS ASSIGNED TO ME
    // ════════════════════════════════════════
    function loadMyWorkOrders() {
        return new Promise((resolve) => {
            $.get("{{ route('epr.wo.my-wo') }}", function(data) {
                myWorkOrders = data;
                const sel = $('#f-workOrderId');
                sel.empty().append('<option value="">-- Pekerjaan Mandiri (Tanpa WO) --</option>');
                myWorkOrders.forEach(wo => {
                    sel.append(`<option value="${wo.id}">${wo.wo_number} - ${wo.title}</option>`);
                });
                resolve();
            }).fail(() => resolve());
        });
    }

    window.selectWorkOrder = function() {
        const woId = $('#f-workOrderId').val();
        if (woId) {
            const wo = myWorkOrders.find(w => w.id == woId);
            if (wo) {
                $('#f-area').val(wo.area).prop('disabled', true);
                $('#f-work').val(`Pekerjaan rujukan WO: ${wo.title}.\n`);
                $('#f-adhoc').prop('checked', false).prop('disabled', true);
                toggleAdhoc();
                $('#adhocSwitchWrap').addClass('d-none');
            }
        } else {
            $('#f-area').prop('disabled', false).val('');
            $('#f-work').val('');
            $('#f-adhoc').prop('disabled', false);
            $('#adhocSwitchWrap').removeClass('d-none');
        }
    };

    async function initFormState(editIdVal, parentIdVal) {
        if (editIdVal) {
            // Edit Mode
            $.get("{{ route('epr.pm.get-reports') }}", function(data) {
                const r = data.find(x => x.id == editIdVal);
                if (!r) return;

                editMode = 'edit';
                editId = editIdVal;
                parentId = null;

                $('#f-id').val(r.id);
                $('#f-date').val(r.date || '');
                $('#f-tech').val(r.tech || '');
                $('#f-area').val(r.area || '');
                $('#f-work').val(r.work || '');
                $('#f-timeStart').val(r.timeStart || '');
                $('#f-timeEnd').val(r.timeEnd || '');
                $('#f-notes').val(r.notes || '');
                $('#f-adhoc').prop('checked', !!r.isAdhoc);
                toggleAdhoc();
                if (r.adhocTitle) $('#f-adhocTitle').val(r.adhocTitle);
                selectStatus(r.status || 'progress');

                if (r.workOrderId) {
                    $('#f-workOrderId').val(r.workOrderId);
                    $('#f-area').prop('disabled', true);
                    $('#f-adhoc').prop('disabled', true);
                    $('#adhocSwitchWrap').addClass('d-none');
                } else {
                    $('#f-workOrderId').val('');
                }

                photos = (r.photos || []).map(p => ({
                    dataUrl: p.url || p.thumb || '',
                    path: p.path || '',
                    existing: true,
                }));
                renderPhotoGrid();

                $('#editBanner').removeClass('d-none');
                $('#editBannerSub').text(`${formatDate(r.date)} · ${(r.work || '').substring(0, 50)}`);
                $('#submitLabel').text('Simpan Perubahan');
                $('#woSelectWrap').addClass('d-none'); // Hide WO select in edit mode to prevent changing linked WO
            });
        } else if (parentIdVal) {
            // Update Lanjutan Mode
            $.get("{{ route('epr.pm.get-reports') }}", function(data) {
                const r = data.find(x => x.id == parentIdVal);
                if (!r) return;

                editMode = 'update';
                editId = null;
                parentId = parentIdVal;

                const now = new Date();
                const hhmm = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

                $('#f-parentId').val(parentIdVal);
                $('#f-date').val(new Date().toISOString().slice(0, 10));
                $('#f-tech').val(r.tech || "{{ Auth::user()->name }}");
                $('#f-area').val(r.area || '').prop('disabled', true);
                $('#f-work').val('');
                $('#f-timeStart').val(hhmm);
                $('#f-timeEnd').val('');
                $('#f-notes').val('');
                $('#f-adhoc').prop('checked', !!r.isAdhoc).prop('disabled', true);
                toggleAdhoc();
                if (r.adhocTitle) $('#f-adhocTitle').val(r.adhocTitle);
                selectStatus('progress');

                if (r.workOrderId) {
                    $('#f-workOrderId').val(r.workOrderId);
                }
                $('#woSelectWrap').addClass('d-none'); // Hide WO select in updates
                $('#adhocSwitchWrap').addClass('d-none');

                photos = [];
                renderPhotoGrid();

                $('#updateBanner').removeClass('d-none');
                $('#updateBannerSub').text(`Lanjutan dari: ${formatDate(r.date)} — ${(r.adhocTitle || r.work || '').substring(0, 50)}`);
                $('#submitLabel').text('Kirim Update');
            });
        } else {
            loadTodayLogs();
        }
    }

    function loadTodayLogs() {
        const todayStr = new Date().toISOString().slice(0, 10);
        $.get("{{ route('epr.pm.get-reports') }}", function(data) {
            const todayLogs = data.filter(r => r.date === todayStr && !r.parentId && r.tech === "{{ Auth::user()->username }}");
            if (todayLogs.length > 0) {
                $('#todayLogsCard').show();
                let html = '';
                todayLogs.forEach(r => {
                    const statusBadge = {
                        open: '<span class="badge bg-primary-subtle text-primary">Open</span>',
                        progress: '<span class="badge bg-warning-subtle text-warning">Progress</span>',
                        done: '<span class="badge bg-success-subtle text-success">Done</span>',
                        onhold: '<span class="badge bg-danger-subtle text-danger">On Hold</span>',
                    }[r.status] || r.status;
                    html += `<tr>
                        <td><strong>${r.area}</strong></td>
                        <td style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${r.adhocTitle || r.work}</td>
                        <td>${r.timeStart}–${r.timeEnd}</td>
                        <td>${statusBadge}</td>
                    </tr>`;
                });
                $('#todayLogsBody').html(html);
            }
        });
    }

    // ════════════════════════════════════════
    // STATUS SELECTOR
    // ════════════════════════════════════════
    window.selectStatus = function(val) {
        $('#f-status').val(val);
        $('.status-radio-btn').removeClass('active-open active-progress active-done active-onhold');
        $(`.status-radio-btn[data-val="${val}"]`).addClass('active-' + val);
    };

    // ════════════════════════════════════════
    // AD-HOC TOGGLE
    // ════════════════════════════════════════
    window.toggleAdhoc = function() {
        const checked = $('#f-adhoc').is(':checked');
        if (checked) {
            $('#adhocTitleWrap').removeClass('d-none');
        } else {
            $('#adhocTitleWrap').addClass('d-none');
        }
    };

    // ════════════════════════════════════════
    // PHOTO GRID
    // ════════════════════════════════════════
    function renderPhotoGrid() {
        const grid = $('#photoGrid');
        grid.empty();
        for (let i = 0; i < MAX_PHOTOS; i++) {
            const p = photos[i];
            const slot = $('<div class="photo-slot' + (p ? ' filled' : '') + '"></div>');
            slot.attr('data-idx', i);
            if (p) {
                slot.html(`<img src="${p.dataUrl}" alt=""><button class="photo-del" data-idx="${i}">✕</button>`);
                slot.on('click', function(e) {
                    if ($(e.target).hasClass('photo-del')) return;
                    openLightboxFromPhotos(i);
                });
            } else {
                slot.html(`<i class="ri-camera-line" style="font-size:22px; color:var(--vz-text-muted);"></i>
                           <small class="text-muted mt-1">Foto ${i + 1}</small>`);
                slot.on('click', function() {
                    if (photos.length >= MAX_PHOTOS) return;
                    $('#photoInput').click();
                });
            }
            grid.append(slot);
        }

        // Delete handler
        grid.find('.photo-del').on('click', function(e) {
            e.stopPropagation();
            const idx = $(this).data('idx');
            photos.splice(idx, 1);
            renderPhotoGrid();
        });
    }

    // File input handler
    $('#photoInput').on('change', async function(e) {
        const files = Array.from(e.target.files);
        for (const file of files) {
            if (photos.length >= MAX_PHOTOS) break;
            const compressed = await compressImage(file, 1200, 0.78);
            const dataUrl = await toDataUrl(compressed);
            photos.push({ file: compressed, dataUrl, name: file.name, existing: false });
        }
        this.value = '';
        renderPhotoGrid();
    });

    // Client-side image compression
    function compressImage(file, maxPx, quality) {
        return new Promise(function(resolve) {
            const img = new Image();
            const url = URL.createObjectURL(file);
            img.onload = function() {
                URL.revokeObjectURL(url);
                let w = img.width, h = img.height;
                if (w > maxPx || h > maxPx) {
                    if (w > h) { h = Math.round(h / w * maxPx); w = maxPx; }
                    else       { w = Math.round(w / h * maxPx); h = maxPx; }
                }
                const c = document.createElement('canvas');
                c.width = w; c.height = h;
                c.getContext('2d').drawImage(img, 0, 0, w, h);
                c.toBlob(function(b) { resolve(b || file); }, 'image/jpeg', quality);
            };
            img.onerror = function() { resolve(file); };
            img.src = url;
        });
    }

    function toDataUrl(blob) {
        return new Promise(function(r) {
            const reader = new FileReader();
            reader.onload = function(e) { r(e.target.result); };
            reader.readAsDataURL(blob);
        });
    }

    // ════════════════════════════════════════
    // FORM SUBMIT
    // ════════════════════════════════════════
    $('#formReport').on('submit', async function(e) {
        e.preventDefault();

        // Enable temporarily to get values during serialize/FormData
        $('#f-area, #f-adhoc').prop('disabled', false);

        const date        = $('#f-date').val();
        const tech        = $('#f-tech').val().trim();
        const area        = $('#f-area').val();
        const work        = $('#f-work').val().trim();
        const timeStart   = $('#f-timeStart').val();
        const timeEnd     = $('#f-timeEnd').val();
        const status      = $('#f-status').val();
        const isAdhoc     = $('#f-adhoc').is(':checked');
        const adhocTitle  = $('#f-adhocTitle').val() || '';
        const notes       = $('#f-notes').val();
        const workOrderId = $('#f-workOrderId').val() || '';

        if (!date || !tech || !area || !work || !timeStart || !timeEnd) {
            Swal.fire('Validasi', 'Lengkapi semua field wajib (*)', 'warning');
            // Re-disable if they was active
            if (workOrderId) {
                $('#f-area, #f-adhoc').prop('disabled', true);
            }
            return;
        }
        if (isAdhoc && !adhocTitle.trim()) {
            Swal.fire('Validasi', 'Isi nama pekerjaan ad-hoc', 'warning');
            if (workOrderId) {
                $('#f-area, #f-adhoc').prop('disabled', true);
            }
            return;
        }

        const btn = $('#btnSubmit');
        btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-2"></div> Menyimpan...');

        try {
            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            if (editId) fd.append('id', editId);
            if (parentId) fd.append('parentId', parentId);
            fd.append('date', date);
            fd.append('tech', tech);
            fd.append('area', area);
            fd.append('work', work);
            fd.append('timeStart', timeStart);
            fd.append('timeEnd', timeEnd);
            fd.append('status', status);
            fd.append('isAdhoc', isAdhoc);
            fd.append('adhocTitle', adhocTitle);
            fd.append('notes', notes);
            fd.append('workOrderId', workOrderId);
            fd.append('woRef', '');

            // Existing photos
            photos.filter(p => p.existing).forEach(p => fd.append('existingPhotos[]', p.path));
            // New photos
            photos.filter(p => !p.existing).forEach((p, i) => fd.append('newPhotos[]', p.file, p.name || `photo_${i}.jpg`));

            const res = await fetch("{{ route('epr.pm.store') }}", { method: 'POST', body: fd });
            const result = await res.json();

            if (!res.ok || !result.success) throw new Error(result.message || 'Gagal menyimpan');

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: editMode === 'edit' ? 'Laporan berhasil diperbarui' : 'Laporan berhasil disimpan',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "{{ route('epr.pm.data') }}";
            });

        } catch(err) {
            Swal.fire('Error', err.message, 'error');
            btn.prop('disabled', false).html('<i class="ri-send-plane-line me-1"></i> <span id="submitLabel">Kirim Laporan</span>');
            if (workOrderId) {
                $('#f-area, #f-adhoc').prop('disabled', true);
            }
        }
    });

    // ════════════════════════════════════════
    // CANCEL / RESET
    // ════════════════════════════════════════
    function resetForm() {
        editMode = null; editId = null; parentId = null;
        $('#f-id').val('');
        $('#f-parentId').val('');
        $('#f-date').val(new Date().toISOString().slice(0, 10));
        $('#f-tech').val("{{ Auth::user()->username }}");
        $('#f-area').val('').prop('disabled', false);
        $('#f-work').val('');
        $('#f-timeStart').val('');
        $('#f-timeEnd').val('');
        $('#f-notes').val('');
        $('#f-adhoc').prop('checked', false).prop('disabled', false);
        toggleAdhoc();
        $('#f-adhocTitle').val('');
        selectStatus('progress');
        photos = [];
        renderPhotoGrid();
        $('#editBanner').addClass('d-none');
        $('#updateBanner').addClass('d-none');
        $('#submitLabel').text('Kirim Laporan');
        $('#woSelectWrap').removeClass('d-none');
        $('#adhocSwitchWrap').removeClass('d-none');
        $('#f-workOrderId').val('');
    }

    $('#btnResetForm').on('click', function() {
        resetForm();
    });

    window.cancelEdit = function() {
        window.location.href = "{{ route('epr.pm.form') }}";
    };

    // ════════════════════════════════════════
    // LIGHTBOX
    // ════════════════════════════════════════
    function openLightboxFromPhotos(idx) {
        lbImages = photos.map(p => p.dataUrl);
        lbIdx = idx;
        $('#lbImg').attr('src', lbImages[lbIdx]);
        $('#lightbox').css('display', 'flex');
    }

    window.closeLightbox = function() {
        $('#lightbox').css('display', 'none');
    };

    window.lbNav = function(dir, e) {
        e.stopPropagation();
        lbIdx = (lbIdx + dir + lbImages.length) % lbImages.length;
        $('#lbImg').attr('src', lbImages[lbIdx]);
    };

    function formatDate(d) {
        if (!d) return '—';
        try { return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }); }
        catch(e) { return d; }
    }
});
</script>
@endsection
