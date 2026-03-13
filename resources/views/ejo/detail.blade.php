@extends('layouts.app')

@section('title', 'Detail EJO')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #2563eb;
        --primary-light: #eff6ff;
        --primary-dark: #1d4ed8;
        --success: #059669;
        --success-light: #ecfdf5;
        --warning: #d97706;
        --warning-light: #fffbeb;
        --danger: #dc2626;
        --danger-light: #fef2f2;
        --surface: #ffffff;
        --surface-2: #f8fafc;
        --border: #e2e8f0;
        --text: #0f172a;
        --text-muted: #64748b;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .04);
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

    /* ── Back Button ── */
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
    .detail-header {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        padding: 28px 32px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .detail-header-left h1 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 6px;
        letter-spacing: -.3px;
    }

    .detail-header-left .ticket-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--primary-light);
        color: var(--primary);
        font-family: 'JetBrains Mono', monospace;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .detail-header-right {
        display: flex;
        gap: 10px;
        flex-shrink: 0;
        align-items: center;
    }

    /* ── Status Badge ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .badge-open {
        background: #eff6ff;
        color: #2563eb;
    }

    .badge-done {
        background: #ecfdf5;
        color: #059669;
    }

    .badge-hold {
        background: #fffbeb;
        color: #d97706;
    }

    .badge-cancel {
        background: #fef2f2;
        color: #dc2626;
    }

    .badge-default {
        background: #f1f5f9;
        color: #64748b;
    }

    /* ── Cards ── */
    .card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        margin-bottom: 20px;
    }

    .card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header h5 {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-header h5 svg {
        color: var(--primary);
    }

    .card-body {
        padding: 24px;
    }

    /* ── Info Grid ── */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .info-item label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--text-muted);
        margin-bottom: 4px;
    }

    .info-item .info-value {
        font-size: 14px;
        font-weight: 500;
        color: var(--text);
    }

    .info-item.full {
        grid-column: 1 / -1;
    }

    .description-box {
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 14px 16px;
        font-size: 14px;
        color: var(--text);
        line-height: 1.6;
        min-height: 60px;
    }

    /* ── Progress ── */
    .progress-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
        animation: fadeUp .3s ease both;
    }

    .progress-item:last-child {
        border-bottom: none;
    }

    .pct-badge {
        flex-shrink: 0;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pct-badge.done {
        background: var(--success-light);
        color: var(--success);
    }

    .progress-meta {
        flex: 1;
    }

    .progress-meta .p-note {
        font-size: 14px;
        color: var(--text);
        margin-bottom: 4px;
    }

    .progress-meta .p-info {
        font-size: 12px;
        color: var(--text-muted);
    }

    .progress-track {
        height: 6px;
        background: #e2e8f0;
        border-radius: 99px;
        margin-top: 8px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, #2563eb, #60a5fa);
        transition: width .5s ease;
    }

    .progress-fill.done {
        background: linear-gradient(90deg, #059669, #34d399);
    }

    /* ── Form Controls ── */
    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .5px;
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

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    /* ── Buttons ── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all .15s;
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 12px rgba(37, 99, 235, .35);
        transform: translateY(-1px);
    }

    .btn-success {
        background: var(--success);
        color: #fff;
    }

    .btn-success:hover {
        background: #047857;
        box-shadow: 0 4px 12px rgba(5, 150, 105, .35);
        transform: translateY(-1px);
    }

    .btn-danger-soft {
        background: var(--danger-light);
        color: var(--danger);
        padding: 4px 10px;
        font-size: 12px;
    }

    .btn-danger-soft:hover {
        background: #fecaca;
    }

    /* ── Notes ── */
    .note-item {
        display: flex;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
        animation: fadeUp .3s ease both;
    }

    .note-item:last-child {
        border-bottom: none;
    }

    .avatar {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .note-content .note-author {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 3px;
    }

    .note-content .note-text {
        font-size: 14px;
        color: var(--text);
        line-height: 1.5;
    }

    .note-content .note-date {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* ── Attachments ── */
    .attachment-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        margin-bottom: 8px;
        animation: fadeUp .3s ease both;
        transition: border-color .15s;
    }

    .attachment-item:hover {
        border-color: var(--primary);
    }

    .attachment-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .attachment-name {
        flex: 1;
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        text-decoration: none;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .attachment-name:hover {
        color: var(--primary);
    }

    /* ── Team Assign ── */
    .team-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--primary-light);
        color: var(--primary-dark);
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        margin: 4px 4px 4px 0;
    }

    /* ── File Upload Area ── */
    .upload-area {
        border: 2px dashed var(--border);
        border-radius: var(--radius-sm);
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        background: var(--surface-2);
        margin-bottom: 12px;
    }

    .upload-area:hover,
    .upload-area.drag-over {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .upload-area p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 6px 0 0;
    }

    .upload-area svg {
        color: var(--text-muted);
    }

    #attachmentFileInput {
        display: none;
    }

    /* ── Empty State ── */
    .empty-mini {
        text-align: center;
        padding: 24px 0;
        color: var(--text-muted);
        font-size: 13px;
    }

    .empty-mini svg {
        margin-bottom: 8px;
        opacity: .4;
    }

    /* ── Divider ── */
    .add-section {
        border-top: 1px solid var(--border);
        padding-top: 20px;
        margin-top: 4px;
    }

    /* ── Skeleton ── */
    .skeleton {
        background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 6px;
    }

    @keyframes shimmer {
        to {
            background-position: -200% 0;
        }
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
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
        border-left: 4px solid #2563eb;
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
        padding: 0;
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

    .btn-edit {
        background: var(--surface);
        color: var(--text);
        border: 1.5px solid var(--border);
    }

    .btn-edit:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    /* ── Edit Modal ── */
    .edit-modal-box {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 680px;
        max-height: 90vh;
        box-shadow: var(--shadow-md);
        animation: dropIn .25s cubic-bezier(.34, 1.56, .64, 1);
        display: flex;
        flex-direction: column;
    }

    .edit-modal-head {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .edit-modal-head h4 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .edit-modal-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
    }

    .edit-modal-foot {
        padding: 16px 24px;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .edit-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .edit-group {
        margin-bottom: 0;
    }

    .edit-group.full {
        grid-column: 1 / -1;
    }

    .edit-group label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--text-muted);
        margin-bottom: 6px;
    }

    .edit-section-title {
        grid-column: 1 / -1;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .6px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 4px;
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .5);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-backdrop.active {
        display: flex;
    }

    .modal-box {
        background: #fff;
        border-radius: 16px;
        padding: 28px;
        width: 100%;
        max-width: 420px;
        box-shadow: var(--shadow-md);
        animation: dropIn .25s cubic-bezier(.34, 1.56, .64, 1);
    }

    .modal-box h4 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 20px;
    }

    @keyframes dropIn {
        from {
            opacity: 0;
            transform: scale(.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-footer {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .btn-ghost {
        background: transparent;
        border: 1.5px solid var(--border);
        color: var(--text-muted);
        padding: 9px 18px;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-ghost:hover {
        border-color: var(--text-muted);
        color: var(--text);
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
        <div class="detail-header">
            <div class="detail-header-left">
                <h1 id="ticketTitle">
                    <div class="skeleton" style="width:300px;height:22px"></div>
                </h1>
                <div class="ticket-chip" id="ticketChip">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="2" y="7" width="20" height="14" rx="2" />
                        <path d="M16 3v4M8 3v4" />
                    </svg>
                    <span id="ticketIdChip">Loading...</span>
                </div>
            </div>
            <div class="detail-header-right">
                <span id="statusBadge" class="badge badge-default">—</span>
                <button class="btn btn-edit" onclick="openEditModal()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Edit
                </button>
                <button class="btn btn-primary" onclick="openAssignModal()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    Assign Tim
                </button>
            </div>
        </div>

        <div class="row" style="display:flex;gap:0;margin:0 -10px">

            <!-- LEFT COLUMN -->
            <div style="flex:1;padding:0 10px;min-width:0">

                <!-- Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 8v4l3 3" />
                            </svg>
                            Informasi EJO
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Department</label>
                                <div class="info-value" id="department">—</div>
                            </div>
                            <div class="info-item">
                                <label>Requestor</label>
                                <div class="info-value" id="requestor">—</div>
                            </div>
                            <div class="info-item">
                                <label>Category</label>
                                <div class="info-value" id="category">—</div>
                            </div>
                            <div class="info-item">
                                <label>Module</label>
                                <div class="info-value" id="module">—</div>
                            </div>
                            <div class="info-item">
                                <label>Tipe</label>
                                <div class="info-value" id="ejoType">—</div>
                            </div>
                            <div class="info-item">
                                <label>Klasifikasi</label>
                                <div class="info-value" id="classification">—</div>
                            </div>
                            <div class="info-item">
                                <label>Request Date</label>
                                <div class="info-value" id="requestDate">—</div>
                            </div>
                            <div class="info-item">
                                <label>Schedule</label>
                                <div class="info-value" id="schedule">—</div>
                            </div>
                            <div class="info-item">
                                <label>Est. Time</label>
                                <div class="info-value" id="estTime">—</div>
                            </div>
                            <div class="info-item">
                                <label>Date Done</label>
                                <div class="info-value" id="dateDone">—</div>
                            </div>
                            <div class="info-item full">
                                <label>Tim yang Ditugaskan</label>
                                <div id="teamList">—</div>
                            </div>
                            <div class="info-item full">
                                <label>Description</label>
                                <div class="description-box" id="description">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Card -->
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                            </svg>
                            Progress Update
                        </h5>
                        <span id="latestPct" style="font-size:13px;font-weight:700;color:var(--primary)">—</span>
                    </div>
                    <div class="card-body">
                        <div id="progressList">
                            <div class="empty-mini">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                </svg>
                                <p>Belum ada progress</p>
                            </div>
                        </div>
                        <div class="add-section">
                            <div class="form-group">
                                <label>Progress %</label>
                                <input type="number" id="progressPercent" class="form-control" placeholder="0 – 100" min="0" max="100">
                            </div>
                            <div class="form-group">
                                <label>Catatan Progress</label>
                                <textarea id="progressNote" class="form-control" placeholder="Deskripsi update progress..."></textarea>
                            </div>
                            <button class="btn btn-primary" onclick="addProgress()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah Progress
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                            Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="noteList">
                            <div class="empty-mini">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                </svg>
                                <p>Belum ada catatan</p>
                            </div>
                        </div>
                        <div class="add-section">
                            <div class="form-group">
                                <label>Tulis Note</label>
                                <textarea id="noteText" class="form-control" placeholder="Tulis catatan Anda..."></textarea>
                            </div>
                            <button class="btn btn-primary" onclick="addNote()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Tambah Note
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN -->
            <div style="width:340px;flex-shrink:0;padding:0 10px">

                <!-- Attachment Card -->
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                            </svg>
                            Lampiran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="attachmentList">
                            <div class="empty-mini">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                                </svg>
                                <p>Belum ada lampiran</p>
                            </div>
                        </div>
                        <div class="add-section">
                            <div class="upload-area" onclick="document.getElementById('attachmentFileInput').click()" id="uploadArea">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                <p>Klik atau drag file ke sini</p>
                            </div>
                            <input type="file" id="attachmentFileInput" onchange="handleFileSelect(this)">
                            <div id="selectedFile" style="display:none;background:var(--primary-light);color:var(--primary);padding:8px 12px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:10px"></div>
                            <button class="btn btn-success" style="width:100%;justify-content:center" onclick="uploadFile()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                Upload Lampiran
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Edit EJO Modal -->
<div class="modal-backdrop" id="editModal">
    <div class="edit-modal-box">

        <div class="edit-modal-head">
            <h4>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:6px;color:var(--primary)">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Edit EJO
            </h4>
            <button onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:18px;line-height:1">✕</button>
        </div>

        <div class="edit-modal-body">
            <div class="edit-grid">

                <!-- Identitas -->
                <div class="edit-section-title">Identitas</div>

                <div class="edit-group">
                    <label>Ticket ID</label>
                    <input type="text" id="e_ticket_id" class="form-control" placeholder="EJO-2024-001">
                </div>
                <div class="edit-group">
                    <label>OS In</label>
                    <input type="text" id="e_os_in" class="form-control" placeholder="Nomor OS">
                </div>
                <div class="edit-group">
                    <label>Department</label>
                    <input type="text" id="e_department" class="form-control">
                </div>
                <div class="edit-group">
                    <label>Requestor</label>
                    <input type="text" id="e_requestor" class="form-control">
                </div>
                <div class="edit-group">
                    <label>Request Date</label>
                    <input type="date" id="e_request_date" class="form-control">
                </div>
                <div class="edit-group">
                    <label>Status</label>
                    <select id="e_status" class="form-control" style="appearance:none;background-image:url(\" data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' %3E%3Cpolyline points='6 9 12 15 18 9' /%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px">
                        <option value="">— Pilih Status —</option>
                        <option value="Open">Open</option>
                        <option value="On Progress">On Progress</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Done">Done</option>
                        <option value="Cancel">Cancel</option>
                    </select>
                </div>

                <!-- Detail Pekerjaan -->
                <div class="edit-section-title">Detail Pekerjaan</div>

                <div class="edit-group">
                    <label>Category</label>
                    <select id="e_category" class="form-control" style="appearance:none;background-image:url(\" data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' %3E%3Cpolyline points='6 9 12 15 18 9' /%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px">
                        <option value="">— Pilih Category —</option>
                        <option value="Drawing">Drawing</option>
                        <option value="Project">Project</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Repair">Repair</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="edit-group">
                    <label>Module</label>
                    <input type="text" id="e_module" class="form-control">
                </div>
                <div class="edit-group full">
                    <label>Subject</label>
                    <input type="text" id="e_subject" class="form-control">
                </div>
                <div class="edit-group full">
                    <label>Description</label>
                    <textarea id="e_description" class="form-control" style="min-height:80px"></textarea>
                </div>

                <!-- Klasifikasi -->
                <div class="edit-section-title">Klasifikasi</div>

                <div class="edit-group full">
                    <label>Klasifikasi EJO</label>
                    <select id="e_classification_id" class="form-control" style="appearance:none;background-image:url(\" data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' %3E%3Cpolyline points='6 9 12 15 18 9' /%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px">
                        <option value="">— Pilih Klasifikasi —</option>
                    </select>
                </div>

                <!-- Jadwal -->
                <div class="edit-section-title">Jadwal & Estimasi</div>

                <div class="edit-group">
                    <label>Schedule</label>
                    <input type="date" id="e_schedule" class="form-control">
                </div>
                <div class="edit-group">
                    <label>Est. Time (jam)</label>
                    <input type="number" id="e_est_time" class="form-control" min="0">
                </div>
                <div class="edit-group">
                    <label>Date Done</label>
                    <input type="date" id="e_date_done" class="form-control">
                </div>

            </div>
        </div>

        <div class="edit-modal-foot">
            <button class="btn-ghost" onclick="closeEditModal()">Batal</button>
            <button class="btn btn-primary" id="btnSaveEdit" onclick="saveEdit()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                    <polyline points="7 3 7 8 15 8" />
                </svg>
                Simpan Perubahan
            </button>
        </div>

    </div>
</div>

<!-- Assign Team Modal -->
<div class="modal-backdrop" id="assignModal">
    <div class="modal-box" style="max-width:480px">
        <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 16px">Assign User ke EJO</h4>

        <!-- Search -->
        <div style="position:relative;margin-bottom:14px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" id="userSearchAssign" class="form-control" placeholder="Cari nama, jabatan, departemen..." style="padding-left:36px" oninput="filterAssignUsers()">
        </div>

        <!-- Already assigned info -->
        <div id="assignedInfo" style="display:none;background:var(--primary-light);color:var(--primary);border-radius:8px;padding:9px 12px;font-size:12px;font-weight:600;margin-bottom:12px">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <span id="assignedInfoText"></span>
        </div>

        <!-- User list -->
        <div id="assignUserList" style="max-height:320px;overflow-y:auto;margin:0 -4px">
            <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin .7s linear infinite;display:inline-block;margin-bottom:8px">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                </svg>
                <p style="margin:0">Memuat users...</p>
            </div>
        </div>

        <div class="modal-footer" style="margin-top:16px">
            <button class="btn-ghost" onclick="closeAssignModal()">Batal</button>
            <button class="btn btn-primary" id="btnAssignUser" onclick="assignUser()" disabled>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                Assign
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toastContainer"></div>

<script>
    const ticketId = "{{ $id }}"
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? ''

    /* ── Toast ── */
    function showToast(type, title, message) {
        const icons = {
            success: `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
            error: `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`
        }
        const t = document.createElement('div')
        t.className = `toast toast-${type}`
        t.innerHTML = `<div class="toast-icon" style="color:${type==='success'?'var(--success)':'var(--danger)'}">${icons[type]}</div><div class="toast-body"><p class="toast-title">${title}</p><p class="toast-msg">${message}</p></div><button class="toast-close" onclick="this.parentElement.remove()">✕</button>`
        document.getElementById('toastContainer').appendChild(t)
        setTimeout(() => {
            t.classList.add('hiding');
            setTimeout(() => t.remove(), 320)
        }, 4000)
    }

    /* ── Helpers ── */
    function statusBadgeClass(s) {
        if (!s) return 'badge-default'
        const sl = s.toLowerCase()
        if (sl === 'done' || sl === 'closed') return 'badge-done'
        if (sl === 'open' || sl === 'on progress') return 'badge-open'
        if (sl === 'on hold') return 'badge-hold'
        if (sl === 'cancel') return 'badge-cancel'
        return 'badge-default'
    }

    function fmtDate(d) {
        if (!d) return '—'
        return new Date(d).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        })
    }

    function initials(name) {
        if (!name) return '?'
        return name.trim().split(' ').slice(0, 2).map(n => n[0].toUpperCase()).join('')
    }

    /* ── Load Ticket ── */
    function loadTicket() {
        fetch(`/api/ejo/${ticketId}`)
            .then(r => r.json())
            .then(d => {
                _ticketData = d // simpan untuk edit modal
                document.getElementById('ticketTitle').innerText = d.subject ?? '(no subject)'
                document.getElementById('ticketIdChip').innerText = d.ticket_id ?? '—'
                document.getElementById('department').innerText = d.department ?? '—'
                document.getElementById('requestor').innerText = d.requestor ?? '—'
                document.getElementById('category').innerText = d.category ?? '—'
                document.getElementById('module').innerText = d.module ?? '—'
                document.getElementById('ejoType').innerText = d.type ?? '—'
                document.getElementById('classification').innerText = d.classification?.name ?? '—'
                document.getElementById('requestDate').innerText = fmtDate(d.request_date)
                document.getElementById('schedule').innerText = fmtDate(d.schedule)
                document.getElementById('estTime').innerText = d.est_time ? d.est_time + ' jam' : '—'
                document.getElementById('dateDone').innerText = fmtDate(d.date_done)
                document.getElementById('description').innerText = d.description ?? '—'

                // status badge
                const badge = document.getElementById('statusBadge')
                badge.className = `badge ${statusBadgeClass(d.status)}`
                badge.innerText = d.status ?? '—'

                // simpan assigned user ids untuk cek duplikat di modal
                window._assignedUserIds = d.teams ? d.teams.map(t => t.user_id).filter(Boolean) : []

                // teams
                const tl = document.getElementById('teamList')
                if (d.teams && d.teams.length) {
                    tl.innerHTML = d.teams.map(t => `
                        <span class="team-chip">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            ${t.user?.username ?? '—'}
                        </span>`).join('')
                } else {
                    tl.innerHTML = '<span style="color:var(--text-muted);font-size:13px">Belum ada user di-assign</span>'
                }
            })
            .catch(() => showToast('error', 'Gagal', 'Tidak dapat memuat data tiket.'))
    }

    /* ── Load Progress ── */
    function loadProgress() {
        fetch(`/api/ejo/${ticketId}/progress`)
            .then(r => r.json())
            .then(list => {
                if (!list.length) {
                    document.getElementById('progressList').innerHTML = `<div class="empty-mini"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg><p>Belum ada progress</p></div>`
                    document.getElementById('latestPct').innerText = '—'
                    return
                }
                document.getElementById('latestPct').innerText = list[0].progress_percent + '%'
                document.getElementById('progressList').innerHTML = list.map((p, i) => `
                    <div class="progress-item" style="animation-delay:${i*40}ms">
                        <div class="pct-badge ${p.progress_percent==100?'done':''}">${p.progress_percent}%</div>
                        <div class="progress-meta">
                            <div class="p-note">${p.progress_note ?? '(no note)'}</div>
                            <div class="progress-track"><div class="progress-fill ${p.progress_percent==100?'done':''}" style="width:${p.progress_percent}%"></div></div>
                            <div class="p-info">${p.user?.username ?? '—'} · ${fmtDate(p.created_at)}</div>
                        </div>
                        <button class="btn btn-danger-soft" onclick="deleteProgress(${p.id})">Hapus</button>
                    </div>
                `).join('')
            })
    }

    function addProgress() {
        const pct = document.getElementById('progressPercent').value
        const note = document.getElementById('progressNote').value
        if (!pct) return showToast('error', 'Validasi', 'Masukkan persentase progress.')

        fetch(`/api/ejo/${ticketId}/progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    progress_percent: pct,
                    progress_note: note
                })
            })
            .then(r => r.json())
            .then(() => {
                document.getElementById('progressPercent').value = ''
                document.getElementById('progressNote').value = ''
                loadProgress()
                loadTicket()
                showToast('success', 'Berhasil', 'Progress berhasil ditambahkan.')
            })
            .catch(() => showToast('error', 'Gagal', 'Terjadi kesalahan.'))
    }

    function deleteProgress(id) {
        if (!confirm('Hapus progress ini?')) return
        fetch(`/api/ejo/progress/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF
                }
            })
            .then(() => {
                loadProgress();
                showToast('success', 'Dihapus', 'Progress dihapus.')
            })
    }

    /* ── Load Notes ── */
    function loadNotes() {
        fetch(`/api/ejo/${ticketId}/note`)
            .then(r => r.json())
            .then(list => {
                if (!list.length) {
                    document.getElementById('noteList').innerHTML = `<div class="empty-mini"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><p>Belum ada catatan</p></div>`
                    return
                }
                document.getElementById('noteList').innerHTML = list.map((n, i) => `
                    <div class="note-item" style="animation-delay:${i*40}ms">
                        <div class="avatar">${initials(n.user?.username)}</div>
                        <div class="note-content" style="flex:1">
                            <div class="note-author">${n.user?.username ?? '—'}</div>
                            <div class="note-text">${n.note}</div>
                            <div class="note-date">${fmtDate(n.created_at)}</div>
                        </div>
                        <button class="btn btn-danger-soft" onclick="deleteNote(${n.id})">Hapus</button>
                    </div>
                `).join('')
            })
    }

    function addNote() {
        const note = document.getElementById('noteText').value
        if (!note.trim()) return showToast('error', 'Validasi', 'Note tidak boleh kosong.')

        fetch(`/api/ejo/${ticketId}/note`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    note
                })
            })
            .then(r => r.json())
            .then(() => {
                document.getElementById('noteText').value = ''
                loadNotes()
                showToast('success', 'Berhasil', 'Note berhasil ditambahkan.')
            })
            .catch(() => showToast('error', 'Gagal', 'Terjadi kesalahan.'))
    }

    function deleteNote(id) {
        if (!confirm('Hapus note ini?')) return
        fetch(`/api/ejo/note/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF
                }
            })
            .then(() => {
                loadNotes();
                showToast('success', 'Dihapus', 'Note dihapus.')
            })
    }

    /* ── Attachments ── */
    function loadAttachments() {
        fetch(`/api/ejo/${ticketId}/attachment`)
            .then(r => r.json())
            .then(list => {
                if (!list.length) {
                    document.getElementById('attachmentList').innerHTML = `<div class="empty-mini"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg><p>Belum ada lampiran</p></div>`
                    return
                }
                document.getElementById('attachmentList').innerHTML = list.map((a, i) => `
                    <div class="attachment-item" style="animation-delay:${i*40}ms">
                        <div class="attachment-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <a href="/storage/${a.file_path}" target="_blank" class="attachment-name">${a.file_name}</a>
                        <button class="btn btn-danger-soft" onclick="deleteAttachment(${a.id})">Hapus</button>
                    </div>
                `).join('')
            })
    }

    function handleFileSelect(input) {
        const file = input.files[0]
        if (!file) return
        const sel = document.getElementById('selectedFile')
        sel.style.display = 'block'
        sel.innerHTML = `📎 ${file.name}`
    }

    function uploadFile() {
        const file = document.getElementById('attachmentFileInput').files[0]
        if (!file) return showToast('error', 'Validasi', 'Pilih file terlebih dahulu.')

        const form = new FormData()
        form.append('file', file)

        fetch(`/api/ejo/${ticketId}/attachment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF
                },
                body: form
            })
            .then(r => r.json())
            .then(() => {
                document.getElementById('attachmentFileInput').value = ''
                document.getElementById('selectedFile').style.display = 'none'
                loadAttachments()
                showToast('success', 'Berhasil', 'File berhasil diupload.')
            })
            .catch(() => showToast('error', 'Gagal', 'Upload gagal.'))
    }

    function deleteAttachment(id) {
        if (!confirm('Hapus lampiran ini?')) return
        fetch(`/api/ejo/attachment/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF
                }
            })
            .then(() => {
                loadAttachments();
                showToast('success', 'Dihapus', 'Lampiran dihapus.')
            })
    }

    // Drag & drop upload area
    const uploadArea = document.getElementById('uploadArea')
    uploadArea.addEventListener('dragover', e => {
        e.preventDefault();
        uploadArea.classList.add('drag-over')
    })
    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'))
    uploadArea.addEventListener('drop', e => {
        e.preventDefault()
        uploadArea.classList.remove('drag-over')
        const file = e.dataTransfer.files[0]
        if (file) {
            const dt = new DataTransfer()
            dt.items.add(file)
            document.getElementById('attachmentFileInput').files = dt.files
            handleFileSelect(document.getElementById('attachmentFileInput'))
        }
    })

    /* ── Edit Modal ── */
    let _ticketData = {}

    function openEditModal() {
        // Load classifications ke dropdown
        fetch('/api/ejo/classifications')
            .then(r => r.json())
            .then(list => {
                const sel = document.getElementById('e_classification_id')
                sel.innerHTML = '<option value="">— Pilih Klasifikasi —</option>' +
                    list.map(c => `<option value="${c.id}" ${_ticketData.classification_id == c.id ? 'selected' : ''}>${c.name} (${c.type_name ?? ''})</option>`).join('')
            })

        // Isi form dari data yang sudah di-load
        const d = _ticketData
        document.getElementById('e_ticket_id').value = d.ticket_id ?? ''
        document.getElementById('e_os_in').value = d.os_in ?? ''
        document.getElementById('e_department').value = d.department ?? ''
        document.getElementById('e_requestor').value = d.requestor ?? ''
        document.getElementById('e_request_date').value = d.request_date ? d.request_date.split('T')[0] : ''
        document.getElementById('e_status').value = d.status ?? ''
        document.getElementById('e_category').value = d.category ?? ''
        document.getElementById('e_module').value = d.module ?? ''
        document.getElementById('e_subject').value = d.subject ?? ''
        document.getElementById('e_description').value = d.description ?? ''
        document.getElementById('e_schedule').value = d.schedule ? d.schedule.split('T')[0] : ''
        document.getElementById('e_est_time').value = d.est_time ?? ''
        document.getElementById('e_date_done').value = d.date_done ? d.date_done.split('T')[0] : ''

        document.getElementById('editModal').classList.add('active')
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('active')
    }

    function saveEdit() {
        const btn = document.getElementById('btnSaveEdit')
        btn.disabled = true
        btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin .7s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Menyimpan...`

        const payload = {
            ticket_id: document.getElementById('e_ticket_id').value || null,
            os_in: document.getElementById('e_os_in').value || null,
            department: document.getElementById('e_department').value || null,
            requestor: document.getElementById('e_requestor').value || null,
            request_date: document.getElementById('e_request_date').value || null,
            status: document.getElementById('e_status').value || null,
            category: document.getElementById('e_category').value || null,
            module: document.getElementById('e_module').value || null,
            subject: document.getElementById('e_subject').value || null,
            description: document.getElementById('e_description').value || null,
            classification_id: document.getElementById('e_classification_id').value || null,
            schedule: document.getElementById('e_schedule').value || null,
            est_time: document.getElementById('e_est_time').value || null,
            date_done: document.getElementById('e_date_done').value || null,
        }

        fetch(`/api/ejo/${ticketId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(res => {
                if (res.data) {
                    showToast('success', 'Berhasil', 'Data EJO berhasil diperbarui.')
                    closeEditModal()
                    loadTicket()
                } else {
                    throw new Error(res.message ?? 'Gagal menyimpan')
                }
            })
            .catch(err => showToast('error', 'Gagal', err.message))
            .finally(() => {
                btn.disabled = false
                btn.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan Perubahan`
            })
    }

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal()
    })

    /* ── Assign User ── */
    let allUsersData = []
    let selectedUserId = null

    function openAssignModal() {
        selectedUserId = null
        document.getElementById('userSearchAssign').value = ''
        document.getElementById('btnAssignUser').disabled = true
        document.getElementById('assignModal').classList.add('active')

        if (allUsersData.length) {
            renderAssignUsers(allUsersData)
        } else {
            fetchAssignUsers()
        }
    }

    function closeAssignModal() {
        document.getElementById('assignModal').classList.remove('active')
        selectedUserId = null
    }

    function fetchAssignUsers() {
        fetch('/users/data')
            .then(r => r.json())
            .then(users => {
                allUsersData = users
                renderAssignUsers(users)
            })
            .catch(() => {
                document.getElementById('assignUserList').innerHTML = `
                    <p style="text-align:center;color:var(--danger);font-size:13px;padding:20px">Gagal memuat users.</p>`
            })
    }

    function renderAssignUsers(users) {
        // Ambil user_id yang sudah di-assign dari data ticket
        const assignedIds = (window._assignedUserIds ?? [])

        if (!users.length) {
            document.getElementById('assignUserList').innerHTML = `
                <p style="text-align:center;color:var(--text-muted);font-size:13px;padding:20px">Tidak ada user ditemukan.</p>`
            return
        }

        // Update info badge
        const info = document.getElementById('assignedInfo')
        if (assignedIds.length) {
            info.style.display = 'block'
            document.getElementById('assignedInfoText').innerText =
                `${assignedIds.length} user sudah di-assign ke EJO ini`
        } else {
            info.style.display = 'none'
        }

        document.getElementById('assignUserList').innerHTML = users.map(u => {
            const isAssigned = assignedIds.includes(u.id)
            const isSelected = selectedUserId === u.id
            return `
            <div onclick="${isAssigned ? '' : `selectAssignUser(this, ${u.id})`}"
                 style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;cursor:${isAssigned?'default':'pointer'};
                        border:2px solid ${isSelected?'var(--primary)':'transparent'};
                        background:${isSelected?'var(--primary-light)':isAssigned?'var(--surface-2)':'transparent'};
                        opacity:${isAssigned?.5:1};transition:all .12s"
                 id="urow-${u.id}">
                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#7c3aed);
                            color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    ${initials(u.username)}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:700;color:var(--text)">${u.username}</div>
                    <div style="font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        ${[u.jabatan, u.departemen, u.bagian].filter(Boolean).join(' · ') || u.email || '—'}
                    </div>
                </div>
                ${isAssigned
                    ? `<span style="font-size:11px;font-weight:600;background:var(--success-light);color:var(--success);padding:3px 8px;border-radius:20px;flex-shrink:0">Sudah assign</span>`
                    : `<div style="width:20px;height:20px;border-radius:50%;border:2px solid ${isSelected?'var(--primary)':'var(--border)'};
                                  background:${isSelected?'var(--primary)':'transparent'};display:flex;align-items:center;justify-content:center;flex-shrink:0" id="chk-${u.id}">
                           ${isSelected?`<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>`:''}
                       </div>`
                }
            </div>`
        }).join('')
    }

    function selectAssignUser(el, userId) {
        selectedUserId = userId
        document.getElementById('btnAssignUser').disabled = false
        // Re-render dengan user terpilih
        const q = document.getElementById('userSearchAssign').value.toLowerCase()
        const filtered = q ?
            allUsersData.filter(u =>
                (u.username ?? '').toLowerCase().includes(q) ||
                (u.jabatan ?? '').toLowerCase().includes(q) ||
                (u.departemen ?? '').toLowerCase().includes(q)) :
            allUsersData
        renderAssignUsers(filtered)
    }

    function filterAssignUsers() {
        const q = document.getElementById('userSearchAssign').value.toLowerCase()
        const filtered = allUsersData.filter(u =>
            (u.username ?? '').toLowerCase().includes(q) ||
            (u.jabatan ?? '').toLowerCase().includes(q) ||
            (u.departemen ?? '').toLowerCase().includes(q) ||
            (u.bagian ?? '').toLowerCase().includes(q) ||
            (u.nik ?? '').toLowerCase().includes(q)
        )
        renderAssignUsers(filtered)
    }

    function assignUser() {
        if (!selectedUserId) return

        const btn = document.getElementById('btnAssignUser')
        btn.disabled = true
        btn.innerText = 'Menyimpan...'

        fetch(`/api/ejo/${ticketId}/assign-team`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    user_id: selectedUserId
                })
            })
            .then(r => r.json())
            .then(res => {
                if (res.message && res.message.includes('sudah')) {
                    showToast('error', 'Duplikat', res.message)
                } else {
                    showToast('success', 'Berhasil', 'User berhasil di-assign ke EJO ini.')
                    closeAssignModal()
                    loadTicket()
                }
            })
            .catch(() => showToast('error', 'Gagal', 'Terjadi kesalahan.'))
            .finally(() => {
                btn.disabled = false
                btn.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Assign`
            })
    }

    // Close modal on backdrop click
    document.getElementById('assignModal').addEventListener('click', function(e) {
        if (e.target === this) closeAssignModal()
    })

    /* ── Init ── */
    loadTicket()
    loadProgress()
    loadNotes()
    loadAttachments()
</script>

@endsection