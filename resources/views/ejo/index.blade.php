@extends('layouts.app')

@section('title', 'EJO Management')

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
        --surface: #ffffff;
        --surface-2: #f8fafc;
        --border: #e2e8f0;
        --text: #0f172a;
        --text-muted: #64748b;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .04);
        --shadow-lg: 0 20px 48px rgba(0, 0, 0, .12), 0 8px 16px rgba(0, 0, 0, .06);
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

    /* ── Page Header ── */
    .ejo-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        padding: 28px 32px;
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
    }

    .ejo-header-left h1 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 4px;
        letter-spacing: -.3px;
    }

    .ejo-header-left p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
    }

    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--surface);
        color: var(--primary);
        border: 1.5px solid var(--primary);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all .2s;
    }

    .btn-create:hover {
        background: var(--primary-light);
        box-shadow: 0 4px 12px rgba(37, 99, 235, .2);
        transform: translateY(-1px);
        color: var(--primary);
        text-decoration: none;
    }

    /* ── Import Button ── */
    .btn-import {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        box-shadow: 0 2px 8px rgba(5, 150, 105, .35);
        position: relative;
        overflow: hidden;
    }

    .btn-import::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, .15) 0%, transparent 60%);
    }

    .btn-import:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(5, 150, 105, .45);
    }

    .btn-import:active {
        transform: translateY(0);
    }

    /* ── Drop Overlay ── */
    #dropOverlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, .6);
        backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
    }

    #dropOverlay.active {
        display: flex;
    }

    .drop-zone {
        background: var(--surface);
        border-radius: 20px;
        border: 2.5px dashed #2563eb;
        padding: 64px 80px;
        text-align: center;
        animation: dropIn .25s cubic-bezier(.34, 1.56, .64, 1);
    }

    @keyframes dropIn {
        from {
            opacity: 0;
            transform: scale(.88);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .drop-zone svg {
        color: var(--primary);
        margin-bottom: 16px;
    }

    .drop-zone h3 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 8px;
    }

    .drop-zone p {
        font-size: 14px;
        color: var(--text-muted);
        margin: 0;
    }

    /* ── Filter Card ── */
    .filter-card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-right: 4px;
        white-space: nowrap;
    }

    .filter-select,
    .filter-input {
        height: 40px;
        padding: 0 14px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 14px;
        color: var(--text);
        background: var(--surface-2);
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        min-width: 180px;
    }

    .filter-select:focus,
    .filter-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        background: #fff;
    }

    .btn-filter {
        height: 40px;
        padding: 0 20px;
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-filter:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 12px rgba(37, 99, 235, .35);
    }

    .btn-reset {
        height: 40px;
        padding: 0 16px;
        background: transparent;
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all .15s;
    }

    .btn-reset:hover {
        border-color: var(--danger);
        color: var(--danger);
        background: #fef2f2;
    }

    /* ── Table Card ── */
    .data-ejo {
        margin-top: 25px;
        padding: 26px 15px;
    }

    .table-card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .table-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid var(--border);
    }

    .table-card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .record-count {
        font-size: 12px;
        font-weight: 600;
        color: var(--primary);
        background: var(--primary-light);
        padding: 2px 10px;
        border-radius: 20px;
    }

    /* ── Table ── */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead th {
        background: var(--surface-2);
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .7px;
        padding: 12px 16px;
        border-bottom: 1.5px solid var(--border);
        white-space: nowrap;
    }

    tbody tr {
        transition: background .12s;
        border-bottom: 1px solid #f1f5f9;
    }

    tbody tr:last-child {
        border-bottom: none;
    }

    tbody tr:hover {
        background: #f8fafc;
    }

    tbody td {
        padding: 14px 16px;
        font-size: 13.5px;
        color: var(--text);
        vertical-align: middle;
    }

    /* Ticket ID chip */
    .ticket-id {
        font-family: 'JetBrains Mono', monospace;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--primary);
        background: var(--primary-light);
        padding: 3px 10px;
        border-radius: 6px;
        white-space: nowrap;
    }

    /* Status Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .badge-done {
        background: var(--success-light);
        color: var(--success);
    }

    .badge-open {
        background: var(--warning-light);
        color: var(--warning);
    }

    /* Progress */
    .progress-wrap {
        min-width: 130px;
    }

    .progress-bar-outer {
        height: 6px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
        margin-bottom: 4px;
    }

    .progress-bar-inner {
        height: 100%;
        background: linear-gradient(90deg, #2563eb, #7c3aed);
        border-radius: 99px;
        transition: width .6s cubic-bezier(.22, 1, .36, 1);
    }

    .progress-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
    }

    /* Requestor avatar */
    .requestor-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #818cf8, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    /* Detail button */
    .btn-detail {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        background: var(--primary-light);
        color: var(--primary);
        border: 1.5px solid rgba(37, 99, 235, .2);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all .15s;
    }

    .btn-detail:hover {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, .3);
    }

    /* ── Loading Overlay ── */
    #loadingOverlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9998;
        background: rgba(15, 23, 42, .55);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
    }

    #loadingOverlay.active {
        display: flex;
    }

    .loading-card {
        background: #fff;
        border-radius: 16px;
        padding: 40px 52px;
        text-align: center;
        box-shadow: var(--shadow-lg);
        animation: dropIn .2s cubic-bezier(.34, 1.56, .64, 1);
    }

    .spinner {
        width: 52px;
        height: 52px;
        border: 4px solid #e2e8f0;
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin .75s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .loading-card h4 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 6px;
    }

    .loading-card p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
    }

    .file-progress-outer {
        width: 240px;
        height: 4px;
        background: #e2e8f0;
        border-radius: 99px;
        margin: 16px auto 0;
        overflow: hidden;
    }

    .file-progress-inner {
        height: 100%;
        background: linear-gradient(90deg, #2563eb, #7c3aed);
        border-radius: 99px;
        width: 0%;
    }

    /* ── Toast ── */
    #toastContainer {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .toast {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        background: #fff;
        border-radius: var(--radius);
        padding: 16px 20px;
        box-shadow: var(--shadow-lg);
        min-width: 320px;
        max-width: 400px;
        animation: toastIn .35s cubic-bezier(.34, 1.56, .64, 1);
        border-left: 4px solid var(--primary);
    }

    .toast.toast-success {
        border-color: var(--success);
    }

    .toast.toast-error {
        border-color: var(--danger);
    }

    .toast.toast-warning {
        border-color: var(--warning);
    }

    @keyframes toastIn {
        from {
            opacity: 0;
            transform: translateX(40px) scale(.94);
        }

        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes toastOut {
        from {
            opacity: 1;
            transform: translateX(0);
            max-height: 100px;
            margin-bottom: 0;
        }

        to {
            opacity: 0;
            transform: translateX(40px);
            max-height: 0;
            padding: 0;
            margin: 0;
        }
    }

    .toast.hiding {
        animation: toastOut .3s ease forwards;
        overflow: hidden;
    }

    .toast-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .toast-success .toast-icon {
        background: var(--success-light);
        color: var(--success);
    }

    .toast-error .toast-icon {
        background: #fef2f2;
        color: var(--danger);
    }

    .toast-warning .toast-icon {
        background: var(--warning-light);
        color: var(--warning);
    }

    .toast-info .toast-icon {
        background: var(--primary-light);
        color: var(--primary);
    }

    .toast-body {
        flex: 1;
    }

    .toast-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 3px;
    }

    .toast-msg {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
    }

    .toast-close {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 0;
        font-size: 16px;
        line-height: 1;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .toast-close:hover {
        color: var(--text);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 64px 24px;
    }

    .empty-state svg {
        color: #cbd5e1;
        margin-bottom: 16px;
    }

    .empty-state h4 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-muted);
        margin: 0 0 6px;
    }

    .empty-state p {
        font-size: 13px;
        color: #94a3b8;
        margin: 0;
    }

    /* Skeleton */
    .skeleton-cell {
        height: 14px;
        border-radius: 6px;
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
    }

    @keyframes shimmer {
        from {
            background-position: 200% 0;
        }

        to {
            background-position: -200% 0;
        }
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    /* ── Pagination ── */
    .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 24px;
        border-top: 1px solid var(--border);
        background: var(--surface-2);
        flex-wrap: wrap;
        gap: 10px;
    }

    .pagination-info {
        font-size: 13px;
        color: var(--text-muted);
    }

    .pagination-info strong {
        color: var(--text);
        font-weight: 600;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .pg-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 6px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--surface);
        color: var(--text);
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all .15s;
        text-decoration: none;
        line-height: 1;
    }

    .pg-btn:hover:not(:disabled) {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    .pg-btn.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(37, 99, 235, .3);
    }

    .pg-btn:disabled {
        opacity: .4;
        cursor: not-allowed;
    }

    .pg-ellipsis {
        font-size: 13px;
        color: var(--text-muted);
        padding: 0 4px;
        line-height: 34px;
    }
</style>

@section('content')
<div class="page-content">
    <div class="container-fluid" style="padding: 24px;">

        <!-- Header -->
        <div class="ejo-header">
            <div class="ejo-header-left">
                <h1>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:-.15em;margin-right:8px;color:var(--primary)">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                        <rect x="9" y="3" width="6" height="4" rx="1" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                    EJO Management
                </h1>
                <p>Kelola dan pantau seluruh tiket EJO secara real-time</p>
            </div>

            <div style="display:flex;align-items:center;gap:10px;">
                <input type="file" id="excelFile" name="file" hidden accept=".xlsx,.xls,.csv">

                <a href="{{ url('ejo/create') }}" class="btn-create">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Buat EJO
                </a>

                <button class="btn-import" onclick="document.getElementById('excelFile').click()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                    Import Excel
                </button>
            </div>
        </div>

        <!-- Filter -->
        <div class="filter-card">
            <span class="filter-label">Filter</span>

            <select id="filterStatus" class="filter-select">
                <option value="">Semua Status</option>
                <option value="Open">Open</option>
                <option value="Done">Done</option>
            </select>

            <select id="filterClassification" class="filter-select">
                <option value="">Semua Klasifikasi</option>
            </select>

            <div style="position:relative;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" id="searchTicket" class="filter-input" placeholder="Cari Ticket ID..." style="padding-left:36px;" onkeyup="if(event.key==='Enter')loadEjo(1)">
            </div>

            <button class="btn-filter" onclick="loadEjo(1)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                </svg>
                Terapkan
            </button>

            <button class="btn-reset" onclick="resetFilter()">Reset</button>
        </div>

        <!-- Table -->
        <div class="data-ejo">
            <div class="table-card">
                <div class="table-card-header">
                    <div class="table-card-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <path d="M3 9h18M9 21V9" />
                        </svg>
                        Daftar Tiket
                        <span class="record-count" id="recordCount">memuat...</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Departemen</th>
                                <th>Subjek</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Requestor</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ejoBody"></tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrap" id="paginationWrap" style="display:none;">
                    <div class="pagination-info" id="paginationInfo"></div>
                    <div class="pagination-controls" id="paginationControls"></div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="loading-card">
        <div class="spinner"></div>
        <h4>Mengimpor Data...</h4>
        <p>Harap tunggu, sedang memproses file Excel Anda</p>
        <div class="file-progress-outer">
            <div class="file-progress-inner" id="fileProgress"></div>
        </div>
    </div>
</div>

<!-- Drag & Drop Overlay -->
<div id="dropOverlay">
    <div class="drop-zone">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="17 8 12 3 7 8" />
            <line x1="12" y1="3" x2="12" y2="15" />
        </svg>
        <h3>Lepas file di sini</h3>
        <p>File Excel (.xlsx, .xls, .csv) akan diimport otomatis</p>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer"></div>

<script>
    /* ══════════════════════════════
   TOAST SYSTEM
══════════════════════════════ */
    function showToast(type, title, message, duration = 4500) {
        const icons = {
            success: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
            error: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
            warning: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
            info: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
        }

        const toast = document.createElement('div')
        toast.className = `toast toast-${type}`
        toast.innerHTML = `
        <div class="toast-icon">${icons[type] ?? icons.info}</div>
        <div class="toast-body">
            <p class="toast-title">${title}</p>
            <p class="toast-msg">${message}</p>
        </div>
        <button class="toast-close" onclick="dismissToast(this.parentElement)">✕</button>
    `
        document.getElementById('toastContainer').appendChild(toast)
        setTimeout(() => dismissToast(toast), duration)
    }

    function dismissToast(toast) {
        if (!toast || toast.classList.contains('hiding')) return
        toast.classList.add('hiding')
        setTimeout(() => toast.remove(), 320)
    }

    /* ══════════════════════════════
       IMPORT
    ══════════════════════════════ */
    document.getElementById('excelFile').addEventListener('change', function() {
        if (!this.files[0]) return
        importFile(this.files[0])
        this.value = ''
    })

    function importFile(file) {
        const ext = file.name.split('.').pop().toLowerCase()
        if (!['xlsx', 'xls', 'csv'].includes(ext)) {
            showToast('error', 'Format Tidak Didukung', 'Harap unggah file Excel (.xlsx, .xls) atau CSV.')
            return
        }

        // Show loading overlay & animate progress bar
        const overlay = document.getElementById('loadingOverlay')
        const prog = document.getElementById('fileProgress')
        overlay.classList.add('active')
        prog.style.transition = 'none'
        prog.style.width = '0%'
        setTimeout(() => {
            prog.style.transition = 'width 2.5s ease'
            prog.style.width = '85%'
        }, 50)

        const formData = new FormData()
        formData.append('file', file)

        fetch('/api/ejo/import', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                prog.style.transition = 'width .3s ease'
                prog.style.width = '100%'
                setTimeout(() => {
                    overlay.classList.remove('active')
                    showToast('success', 'Import Berhasil!', `${res.count ?? 'Semua'} data berhasil diimport ke sistem.`)
                    loadEjo(1)
                }, 350)
            })
            .catch(() => {
                overlay.classList.remove('active')
                showToast('error', 'Import Gagal', 'Terjadi kesalahan saat mengimpor file. Silakan coba lagi.')
            })
    }

    /* ══════════════════════════════
       DRAG & DROP
    ══════════════════════════════ */
    let dragCounter = 0

    document.addEventListener('dragenter', e => {
        dragCounter++
        if (e.dataTransfer.types.includes('Files'))
            document.getElementById('dropOverlay').classList.add('active')
    })

    document.addEventListener('dragleave', () => {
        if (--dragCounter <= 0) {
            dragCounter = 0
            document.getElementById('dropOverlay').classList.remove('active')
        }
    })

    document.addEventListener('dragover', e => e.preventDefault())

    document.addEventListener('drop', e => {
        e.preventDefault()
        dragCounter = 0
        document.getElementById('dropOverlay').classList.remove('active')
        const file = e.dataTransfer.files[0]
        if (file) importFile(file)
    })

    /* ══════════════════════════════
       LOAD TABLE
    ══════════════════════════════ */
    // Baca classification dari URL query string (dari sidebar)
    const urlParams = new URLSearchParams(window.location.search)
    const urlClassification = urlParams.get('classification') ?? ''

    // Set filter classification jika ada di URL
    if (urlClassification) {
        const sel = document.getElementById('filterClassification')
        if (sel) sel.value = urlClassification
    }

    let currentPage = 1

    function loadEjo(page = 1) {
        currentPage = page
        const status = document.getElementById('filterStatus').value
        const search = document.getElementById('searchTicket').value
        // Ambil dari dropdown (sudah ter-set oleh loadClassifications), fallback ke urlClassification
        const classification = document.getElementById('filterClassification')?.value ?? urlClassification
        const tbody = document.getElementById('ejoBody')

        // Skeleton rows
        tbody.innerHTML = Array(6).fill(0).map(() => `
        <tr>
            ${[80,100,160,90,60,120,100,80,60].map(w => `
                <td><div class="skeleton-cell" style="width:${w}px"></div></td>
            `).join('')}
        </tr>
    `).join('')

        // Sembunyikan pagination saat loading
        document.getElementById('paginationWrap').style.display = 'none'

        fetch(`/api/ejo?status=${encodeURIComponent(status)}&search=${encodeURIComponent(search)}&classification=${encodeURIComponent(classification)}&page=${page}`)
            .then(res => res.json())
            .then(res => renderTable(res))
            .catch(() => {
                showToast('error', 'Gagal Memuat Data', 'Periksa koneksi jaringan Anda.')
                tbody.innerHTML = `<tr><td colspan="9">${emptyStateHTML('Gagal memuat data', 'Periksa koneksi jaringan.')}</td></tr>`
            })
    }

    function renderTable(res) {
        const data = res.data ?? []
        const total = res.total ?? data.length
        const perPage = res.per_page ?? 20
        const lastPage = res.last_page ?? 1
        const currentPg = res.current_page ?? 1
        const from = res.from ?? (data.length ? 1 : 0)
        const to = res.to ?? data.length

        document.getElementById('recordCount').textContent = `${total} data`

        if (data.length === 0) {
            document.getElementById('ejoBody').innerHTML = `<tr><td colspan="9">${emptyStateHTML()}</td></tr>`
            document.getElementById('paginationWrap').style.display = 'none'
            return
        }

        document.getElementById('ejoBody').innerHTML = data.map((ejo, i) => `
        <tr style="animation: fadeUp .3s ease ${i * 35}ms both;">
            <td><span class="ticket-id">${ejo.ticket_id}</span></td>
            <td style="color:var(--text-muted)">${ejo.department ?? '—'}</td>
            <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${ejo.subject ?? ''}">${ejo.subject ?? '—'}</td>
            <td>${ejo.category ?? '—'}</td>
            <td><span>${ejo.status}</span></td>
            <td>${renderProgress(ejo.progress)}</td>
            <td>
                <div class="requestor-cell">
                    <div class="avatar">${initials(ejo.requestor)}</div>
                    <span>${ejo.requestor ?? '—'}</span>
                </div>
            </td>
            <td style="color:var(--text-muted);font-size:13px;white-space:nowrap">${formatDate(ejo.request_date)}</td>
            <td>
                <a href="/ejo/${ejo.id}" class="btn-detail">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Detail
                </a>
            </td>
        </tr>
    `).join('')

        renderPagination(currentPg, lastPage, total, from, to)
    }

    function renderPagination(currentPg, lastPage, total, from, to) {
        if (lastPage <= 1) {
            document.getElementById('paginationWrap').style.display = 'none'
            return
        }

        document.getElementById('paginationWrap').style.display = 'flex'

        // Info teks
        document.getElementById('paginationInfo').innerHTML =
            `Menampilkan <strong>${from}–${to}</strong> dari <strong>${total}</strong> data`

        // Bangun nomor halaman dengan elipsis
        const pages = buildPageRange(currentPg, lastPage)

        const prevDisabled = currentPg <= 1 ? 'disabled' : ''
        const nextDisabled = currentPg >= lastPage ? 'disabled' : ''

        let html = `
            <button class="pg-btn" ${prevDisabled} onclick="loadEjo(${currentPg - 1})" title="Sebelumnya">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </button>`

        pages.forEach(p => {
            if (p === '...') {
                html += `<span class="pg-ellipsis">…</span>`
            } else {
                html += `<button class="pg-btn ${p === currentPg ? 'active' : ''}" onclick="loadEjo(${p})">${p}</button>`
            }
        })

        html += `
            <button class="pg-btn" ${nextDisabled} onclick="loadEjo(${currentPg + 1})" title="Berikutnya">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>`

        document.getElementById('paginationControls').innerHTML = html
    }

    // Buat array halaman dengan elipsis: [1, '...', 4, 5, 6, '...', 12]
    function buildPageRange(current, last) {
        if (last <= 7) return Array.from({
            length: last
        }, (_, i) => i + 1)

        const pages = []
        const delta = 1 // halaman di kiri/kanan current

        pages.push(1)

        const left = Math.max(2, current - delta)
        const right = Math.min(last - 1, current + delta)

        if (left > 2) pages.push('...')

        for (let i = left; i <= right; i++) pages.push(i)

        if (right < last - 1) pages.push('...')

        pages.push(last)

        return pages
    }

    function renderProgress(progress) {
        const pct = (progress && progress.length) ? (progress[0].progress_percent ?? 0) : 0
        return `
        <div class="progress-wrap">
            <div class="progress-bar-outer">
                <div class="progress-bar-inner" style="width:${pct}%"></div>
            </div>
            <span class="progress-label">${pct}%</span>
        </div>`
    }

    function emptyStateHTML(title = 'Tidak ada data', msg = 'Coba ubah filter pencarian Anda.') {
        return `
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
                <path d="M8 21h8M12 17v4"/>
            </svg>
            <h4>${title}</h4>
            <p>${msg}</p>
        </div>`
    }

    function initials(name) {
        if (!name) return '?'
        return name.trim().split(' ').slice(0, 2).map(n => n[0].toUpperCase()).join('')
    }

    function formatDate(date) {
        if (!date) return '—'
        return new Date(date).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        })
    }

    function resetFilter() {
        document.getElementById('filterStatus').value = ''
        document.getElementById('searchTicket').value = ''
        document.getElementById('filterClassification').value = ''
        // Update URL supaya sidebar tidak aktif classification lama
        history.replaceState(null, '', '/ejo')
        loadEjo(1)
    }



    function loadClassifications() {
        return fetch('/api/ejo/classifications')
            .then(res => res.json())
            .then(data => {
                const sel = document.getElementById('filterClassification')
                data.forEach(c => {
                    const opt = document.createElement('option')
                    opt.value = c.id
                    opt.textContent = c.name + (c.type_name ? ` (${c.type_name})` : '')
                    sel.appendChild(opt)
                })
                // Set value SETELAH semua options sudah ditambahkan
                if (urlClassification) sel.value = urlClassification
            })
            .catch(() => {})
    }

    loadClassifications().then(() => loadEjo(1))
</script>
@endsection