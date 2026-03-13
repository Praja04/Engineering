@extends('layouts.app')

@section('title', 'Tambah EJO')

@section('content')
<style>
    #back-to-top {
        display: none !important;
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #2563eb;
        --primary-light: #eff6ff;
        --primary-dark: #1d4ed8;
        --success: #059669;
        --danger: #dc2626;
        --surface: #ffffff;
        --surface-2: #f8fafc;
        --border: #e2e8f0;
        --text: #0f172a;
        --text-muted: #64748b;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
        --radius: 12px;
        --radius-sm: 8px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #f1f5f9;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        margin-bottom: 20px;
        transition: color .15s;
    }

    .back-btn:hover {
        color: var(--primary);
    }

    /* ── Page Header ── */
    .page-header {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .page-header h1 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 4px;
        letter-spacing: -.3px;
    }

    .page-header p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
    }

    /* ── Form Layout ── */
    .form-wrapper {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }

    /* ── Card ── */
    .card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header h5 {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .card-header .card-icon {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .card-body {
        padding: 24px;
    }

    /* ── Form Grid ── */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-bottom: 6px;
    }

    .form-group label .required {
        color: var(--danger);
        font-size: 14px;
        line-height: 1;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 14px;
        color: var(--text);
        background: var(--surface-2);
        outline: none;
        transition: border-color .15s, box-shadow .15s;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        background: #fff;
    }

    .form-control.is-error {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .08);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
        line-height: 1.6;
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
        cursor: pointer;
    }

    .error-msg {
        font-size: 12px;
        color: var(--danger);
        margin-top: 5px;
        display: none;
    }

    .error-msg.show {
        display: block;
    }

    /* ── Ticket ID Preview ── */
    .ticket-preview {
        background: var(--surface-2);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 13px;
        font-weight: 600;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ticket-preview .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .3;
        }
    }

    /* ── Action Buttons ── */
    .action-card {
        position: sticky;
        top: 20px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 11px 20px;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all .15s;
        width: 100%;
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, .3);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 6px 20px rgba(37, 99, 235, .4);
        transform: translateY(-1px);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-primary:disabled {
        opacity: .7;
        cursor: not-allowed;
        transform: none;
    }

    .btn-ghost {
        background: transparent;
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        margin-top: 10px;
    }

    .btn-ghost:hover {
        border-color: var(--text-muted);
        color: var(--text);
        background: var(--surface-2);
    }

    /* ── Classification Selector ── */
    .class-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 4px;
    }

    .class-option {
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 12px;
        cursor: pointer;
        transition: all .15s;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        background: var(--surface-2);
    }

    .class-option:hover {
        border-color: var(--primary);
        color: var(--text);
    }

    .class-option.selected {
        border-color: var(--primary);
        background: var(--primary-light);
        color: var(--primary);
    }

    .class-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* ── Summary Card ── */
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed var(--border);
        font-size: 13px;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .summary-row .s-label {
        color: var(--text-muted);
        font-weight: 500;
    }

    .summary-row .s-val {
        font-weight: 700;
        color: var(--text);
        text-align: right;
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ── Toast ── */
    #toastContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .toast {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: #fff;
        border-radius: 10px;
        padding: 14px 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
        min-width: 300px;
        border-left: 4px solid var(--primary);
        animation: slideIn .25s ease;
    }

    .toast.hiding {
        animation: slideOut .25s ease forwards;
    }

    .toast-success {
        border-color: var(--success);
    }

    .toast-error {
        border-color: var(--danger);
    }

    .toast-body p {
        margin: 0;
        font-size: 13px;
    }

    .toast-title {
        font-weight: 700;
        color: var(--text);
        margin-bottom: 2px !important;
    }

    .toast-msg {
        color: var(--text-muted);
    }

    .toast-close {
        margin-left: auto;
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-muted);
        font-size: 14px;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideOut {
        to {
            opacity: 0;
            transform: translateX(20px);
        }
    }

    /* ── Spinner ── */
    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, .4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite;
        display: none;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div class="page-content">
    <div class="container-fluid">

        <a href="/ejo" class="back-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 12H5M5 12l7 7M5 12l7-7" />
            </svg>
            Kembali ke Daftar EJO
        </a>

        <!-- Header -->
        <div class="page-header">
            <div class="header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="12" y1="18" x2="12" y2="12" />
                    <line x1="9" y1="15" x2="15" y2="15" />
                </svg>
            </div>
            <div>
                <h1>Tambah EJO Baru</h1>
                <p>Buat Engineering Job Order secara manual</p>
            </div>
        </div>

        <div class="form-wrapper">

            <!-- LEFT: Form -->
            <div>

                <!-- Identitas -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 3v4M8 3v4" />
                            </svg>
                        </div>
                        <h5>Identitas EJO</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">

                            <div class="form-group">
                                <label>Ticket ID <span class="required">*</span></label>
                                <input type="text" id="ticketId" class="form-control" placeholder="Contoh: EJO-2024-001" oninput="updateSummary()">
                                <div class="error-msg" id="errTicketId">Ticket ID wajib diisi dan harus unik.</div>
                            </div>

                            <div class="form-group">
                                <label>OS In</label>
                                <input type="text" id="osIn" class="form-control" placeholder="Nomor OS">
                            </div>

                            <div class="form-group">
                                <label>Request Date</label>
                                <input type="date" id="requestDate" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Department</label>
                                <input type="text" id="department" class="form-control" placeholder="Nama department" oninput="updateSummary()">
                            </div>

                            <div class="form-group">
                                <label>Requestor</label>
                                <input type="text" id="requestor" class="form-control" placeholder="Nama requestor" oninput="updateSummary()">
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Detail Pekerjaan -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                        </div>
                        <h5>Detail Pekerjaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">

                            <div class="form-group">
                                <label>Category</label>
                                <select id="category" class="form-control" onchange="autoDetectClassification()">
                                    <option value="">— Pilih Category —</option>
                                    <option value="Drawing">Drawing</option>
                                    <option value="Project">Project</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Repair">Repair</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Module</label>
                                <input type="text" id="module" class="form-control" placeholder="Nama modul">
                            </div>

                            <div class="form-group full">
                                <label>Subject <span class="required">*</span></label>
                                <input type="text" id="subject" class="form-control" placeholder="Judul / subjek EJO" oninput="updateSummary()">
                                <div class="error-msg" id="errSubject">Subject wajib diisi.</div>
                            </div>

                            <div class="form-group full">
                                <label>Description</label>
                                <textarea id="description" class="form-control" placeholder="Deskripsi detail pekerjaan..."></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Klasifikasi -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                <line x1="4" y1="22" x2="4" y2="15" />
                            </svg>
                        </div>
                        <h5>Klasifikasi EJO</h5>
                    </div>
                    <div class="card-body">
                        <p style="font-size:13px;color:var(--text-muted);margin:0 0 14px">Pilih klasifikasi yang sesuai. Akan terdeteksi otomatis berdasarkan category.</p>

                        <input type="hidden" id="classificationId">

                        <div id="classGrid" class="class-grid">
                            <!-- Populated by JS -->
                        </div>

                        <div id="autoDetectNote" style="display:none;margin-top:12px;background:var(--primary-light);color:var(--primary);border-radius:8px;padding:10px 14px;font-size:13px;font-weight:600">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 8v4l3 3" />
                            </svg>
                            <span id="autoDetectText">Klasifikasi terdeteksi otomatis</span>
                        </div>
                    </div>
                </div>

                <!-- Jadwal -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </div>
                        <h5>Jadwal & Estimasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">

                            <div class="form-group">
                                <label>Schedule</label>
                                <input type="date" id="schedule" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Est. Time (jam)</label>
                                <input type="number" id="estTime" class="form-control" placeholder="Contoh: 8" min="0">
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT: Summary & Actions -->
            <div class="action-card">

                <!-- Action Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </div>
                        <h5>Simpan EJO</h5>
                    </div>
                    <div class="card-body">
                        <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px;line-height:1.5">
                            Pastikan semua field wajib (bertanda <span style="color:var(--danger);font-weight:700">*</span>) sudah terisi sebelum menyimpan.
                        </p>
                        <button class="btn btn-primary" id="submitBtn" onclick="submitForm()">
                            <div class="spinner" id="btnSpinner"></div>
                            <svg id="btnIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z" />
                                <polyline points="17 21 17 13 7 13 7 21" />
                                <polyline points="7 3 7 8 15 8" />
                            </svg>
                            Simpan EJO
                        </button>
                        <a href="/ejo" class="btn btn-ghost">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                            Batal
                        </a>
                    </div>
                </div>

                <!-- Summary Preview -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </div>
                        <h5>Pratinjau</h5>
                    </div>
                    <div class="card-body" style="padding:16px 24px">
                        <div class="summary-row">
                            <span class="s-label">Ticket ID</span>
                            <span class="s-val" id="sumTicketId">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="s-label">Subject</span>
                            <span class="s-val" id="sumSubject">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="s-label">Department</span>
                            <span class="s-val" id="sumDept">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="s-label">Requestor</span>
                            <span class="s-val" id="sumRequestor">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="s-label">Klasifikasi</span>
                            <span class="s-val" id="sumClass">—</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<div id="toastContainer"></div>

<script>
    /* ── Classification Data ── */
    // Sesuaikan dengan data dari DB Anda
    const classifications = []

    // Load dari API
    fetch('/api/ejo/classifications')
        .then(r => r.json())
        .then(data => {
            data.forEach(c => classifications.push(c))
            renderClassifications()
        })
        .catch(() => {
            // Fallback static data jika API belum tersedia
            const fallback = [{
                    id: 1,
                    name: 'Mekanik',
                    color: '#2563eb'
                },
                {
                    id: 2,
                    name: 'Sipil',
                    color: '#7c3aed'
                },
                {
                    id: 3,
                    name: 'Maintenance / Improvement',
                    color: '#059669'
                },
                {
                    id: 4,
                    name: 'Repair Part',
                    color: '#d97706'
                },
            ]
            fallback.forEach(c => classifications.push(c))
            renderClassifications()
        })

    function renderClassifications() {
        const colors = ['#2563eb', '#7c3aed', '#059669', '#d97706', '#dc2626', '#0891b2']
        document.getElementById('classGrid').innerHTML = classifications.map((c, i) => `
            <div class="class-option" data-id="${c.id}" data-name="${c.name}" onclick="selectClass(this)">
                <div class="class-dot" style="background:${colors[i % colors.length]}"></div>
                ${c.name}
            </div>
        `).join('')
    }

    let selectedClassName = ''

    function selectClass(el) {
        document.querySelectorAll('.class-option').forEach(e => e.classList.remove('selected'))
        el.classList.add('selected')
        document.getElementById('classificationId').value = el.dataset.id
        selectedClassName = el.dataset.name
        updateSummary()
    }

    function autoDetectClassification() {
        const cat = document.getElementById('category').value.toLowerCase()
        const note = document.getElementById('autoDetectNote')
        const noteText = document.getElementById('autoDetectText')

        let target = null

        if (cat.includes('drawing') || cat.includes('project')) {
            target = 'Mekanik'
        } else if (cat.includes('maintenance')) {
            target = 'Maintenance / Improvement'
        } else if (cat.includes('repair')) {
            target = 'Repair Part'
        }

        if (target) {
            document.querySelectorAll('.class-option').forEach(el => {
                el.classList.remove('selected')
                if (el.dataset.name === target) {
                    el.classList.add('selected')
                    document.getElementById('classificationId').value = el.dataset.id
                    selectedClassName = target
                }
            })
            noteText.innerText = `Terdeteksi otomatis: ${target}`
            note.style.display = 'block'
        } else {
            note.style.display = 'none'
        }

        updateSummary()
    }

    /* ── Summary ── */
    function updateSummary() {
        const set = (id, val) => {
            const el = document.getElementById(id)
            el.innerText = val || '—'
        }
        set('sumTicketId', document.getElementById('ticketId').value)
        set('sumSubject', document.getElementById('subject').value)
        set('sumDept', document.getElementById('department').value)
        set('sumRequestor', document.getElementById('requestor').value)
        set('sumClass', selectedClassName)
    }

    /* ── Toast ── */
    function showToast(type, title, message) {
        const icons = {
            success: `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
            error: `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`
        }
        const t = document.createElement('div')
        t.className = `toast toast-${type}`
        t.innerHTML = `<div style="color:${type==='success'?'var(--success)':'var(--danger)'}">${icons[type]}</div><div class="toast-body"><p class="toast-title">${title}</p><p class="toast-msg">${message}</p></div><button class="toast-close" onclick="this.parentElement.remove()">✕</button>`
        document.getElementById('toastContainer').appendChild(t)
        setTimeout(() => {
            t.classList.add('hiding');
            setTimeout(() => t.remove(), 320)
        }, 4500)
    }

    /* ── Submit ── */
    function submitForm() {
        // Reset errors
        document.querySelectorAll('.error-msg').forEach(e => e.classList.remove('show'))
        document.querySelectorAll('.form-control').forEach(e => e.classList.remove('is-error'))

        const ticketId = document.getElementById('ticketId').value.trim()
        const subject = document.getElementById('subject').value.trim()
        let valid = true

        if (!ticketId) {
            document.getElementById('errTicketId').classList.add('show')
            document.getElementById('ticketId').classList.add('is-error')
            valid = false
        }
        if (!subject) {
            document.getElementById('errSubject').classList.add('show')
            document.getElementById('subject').classList.add('is-error')
            valid = false
        }
        if (!valid) {
            showToast('error', 'Validasi Gagal', 'Lengkapi field yang wajib diisi.')
            return
        }

        // Loading state
        const btn = document.getElementById('submitBtn')
        const icon = document.getElementById('btnIcon')
        const spinner = document.getElementById('btnSpinner')
        btn.disabled = true
        icon.style.display = 'none'
        spinner.style.display = 'block'

        const payload = {
            ticket_id: ticketId,
            subject: subject,
            os_in: document.getElementById('osIn').value || null,
            department: document.getElementById('department').value || null,
            requestor: document.getElementById('requestor').value || null,
            category: document.getElementById('category').value || null,
            module: document.getElementById('module').value || null,
            description: document.getElementById('description').value || null,
            request_date: document.getElementById('requestDate').value || null,
            schedule: document.getElementById('schedule').value || null,
            est_time: document.getElementById('estTime').value || null,
            classification_id: document.getElementById('classificationId').value || null,
        }

        fetch('/api/ejo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(res => {
                if (res.data?.id) {
                    showToast('success', 'EJO Berhasil Dibuat!', `Ticket ${ticketId} telah tersimpan.`)
                    setTimeout(() => window.location.href = `/ejo/${res.data.id}`, 1200)
                } else {
                    throw new Error(res.message ?? 'Gagal')
                }
            })
            .catch(err => {
                btn.disabled = false
                icon.style.display = ''
                spinner.style.display = 'none'
                showToast('error', 'Gagal Menyimpan', err.message ?? 'Terjadi kesalahan. Coba lagi.')
            })
    }

    /* ── Keyboard shortcut ── */
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') submitForm()
    })
</script>

@endsection