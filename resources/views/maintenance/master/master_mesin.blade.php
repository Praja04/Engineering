@extends('layouts.app')

@section('styles')
    <style>
        /* ── Design Tokens ──────────────────────────────────────────────────────── */
        :root {
            --ink: #0f172a;
            --ink-soft: #475569;
            --ink-muted: #94a3b8;
            --surface: #ffffff;
            --surface-2: #f8fafc;
            --surface-3: #f1f5f9;
            --border: #e2e8f0;
            --border-2: #cbd5e1;
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
            --shadow-lg: 0 10px 30px rgba(15, 23, 42, .10), 0 4px 8px rgba(15, 23, 42, .06);
            --transition: .18s cubic-bezier(.4, 0, .2, 1);
        }

        /* ── Layout ─────────────────────────────────────────────────────────────── */
        .page-wrapper {
            padding: 24px;
        }

        /* ── Page Header ─────────────────────────────────────────────────────────── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .page-header-left {}

        .page-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--ink-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-weight: 500;
        }

        .page-breadcrumb span {
            color: var(--ink-soft);
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--ink);
            margin: 0;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--ink-muted);
            margin: 4px 0 0;
        }

        .page-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* ── Stat Cards ──────────────────────────────────────────────────────────── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
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

        .stat-icon.red {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .stat-icon.amber {
            background: var(--warning-bg);
            color: var(--warning);
        }

        .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--ink-muted);
            margin-top: 2px;
            font-weight: 500;
        }

        /* ── Card ────────────────────────────────────────────────────────────────── */
        .card-custom {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-custom-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            gap: 12px;
            flex-wrap: wrap;
        }

        .card-custom-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-custom-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--ink);
            margin: 0;
        }

        .card-custom-body {
            padding: 0;
        }

        /* ── Toolbar ─────────────────────────────────────────────────────────────── */
        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 24px;
            border-bottom: 1px solid var(--border);
            gap: 12px;
            flex-wrap: wrap;
            background: var(--surface-2);
        }

        .toolbar-left,
        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Search input */
        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ink-muted);
            font-size: 14px;
            pointer-events: none;
        }

        .search-box input {
            padding: 7px 12px 7px 32px;
            border: 1px solid var(--border-2);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--ink);
            background: var(--surface);
            width: 220px;
            transition: border-color var(--transition), box-shadow var(--transition);
            outline: none;
        }

        .search-box input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        /* Filter select */
        .filter-select {
            padding: 7px 10px;
            border: 1px solid var(--border-2);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--ink);
            background: var(--surface);
            outline: none;
            cursor: pointer;
            transition: border-color var(--transition);
        }

        .filter-select:focus {
            border-color: var(--accent);
        }

        /* ── Buttons ─────────────────────────────────────────────────────────────── */
        .btn-base {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all var(--transition);
            white-space: nowrap;
            text-decoration: none;
            line-height: 1.4;
        }

        .btn-base:focus {
            outline: none;
        }

        .btn-primary-c {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .btn-primary-c:hover {
            background: var(--accent-hov);
            border-color: var(--accent-hov);
            color: #fff;
        }

        .btn-primary-c:active {
            transform: scale(.97);
        }

        .btn-outline-c {
            background: var(--surface);
            color: var(--ink-soft);
            border-color: var(--border-2);
        }

        .btn-outline-c:hover {
            background: var(--surface-3);
            color: var(--ink);
            border-color: var(--border-2);
        }

        .btn-success-c {
            background: var(--success);
            color: #fff;
            border-color: var(--success);
        }

        .btn-success-c:hover {
            background: #15803d;
            border-color: #15803d;
            color: #fff;
        }

        .btn-icon-only {
            padding: 6px 8px;
        }

        .btn-sm-c {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Danger ghost */
        .btn-ghost-danger {
            background: transparent;
            color: var(--danger);
            border-color: transparent;
            padding: 5px 8px;
        }

        .btn-ghost-danger:hover {
            background: var(--danger-bg);
            border-color: var(--danger-bg);
        }

        /* Warning ghost */
        .btn-ghost-warning {
            background: transparent;
            color: var(--warning);
            border-color: transparent;
            padding: 5px 8px;
        }

        .btn-ghost-warning:hover {
            background: var(--warning-bg);
            border-color: var(--warning-bg);
        }

        /* ── Table ───────────────────────────────────────────────────────────────── */
        .table-wrap {
            overflow-x: auto;
        }

        table.tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        table.tbl thead {
            background: var(--surface-3);
            border-top: none;
        }

        table.tbl thead th {
            padding: 11px 16px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--ink-muted);
            white-space: nowrap;
            border-bottom: 1px solid var(--border);
            user-select: none;
        }

        table.tbl thead th.sortable {
            cursor: pointer;
        }

        table.tbl thead th.sortable:hover {
            color: var(--ink-soft);
        }

        table.tbl tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background var(--transition);
        }

        table.tbl tbody tr:last-child {
            border-bottom: none;
        }

        table.tbl tbody tr:hover {
            background: var(--surface-2);
        }

        table.tbl td {
            padding: 13px 16px;
            color: var(--ink-soft);
            vertical-align: middle;
        }

        table.tbl td.td-primary {
            color: var(--ink);
            font-weight: 500;
        }

        .table-empty {
            text-align: center;
            padding: 48px 16px;
            color: var(--ink-muted);
        }

        .table-empty i {
            font-size: 32px;
            margin-bottom: 8px;
            display: block;
        }

        /* ── Badges ──────────────────────────────────────────────────────────────── */
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .03em;
        }

        .badge-pill::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            display: inline-block;
        }

        .badge-active {
            background: var(--success-bg);
            color: var(--success);
        }

        .badge-active::before {
            background: var(--success);
        }

        .badge-inactive {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .badge-inactive::before {
            background: var(--danger);
        }

        .badge-jenis {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            background: var(--accent-bg);
            color: var(--accent);
            letter-spacing: .02em;
        }

        /* ── Pagination ──────────────────────────────────────────────────────────── */
        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            border-top: 1px solid var(--border);
            background: var(--surface-2);
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-info-text {
            font-size: 12px;
            color: var(--ink-muted);
        }

        .pagination-wrap {
            display: flex;
            gap: 4px;
        }

        .page-btn {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-2);
            background: var(--surface);
            font-size: 12px;
            color: var(--ink-soft);
            cursor: pointer;
            transition: all var(--transition);
        }

        .page-btn:hover {
            background: var(--accent-bg);
            border-color: var(--accent);
            color: var(--accent);
        }

        .page-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            font-weight: 600;
        }

        .page-btn:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        /* ── Modal ───────────────────────────────────────────────────────────────── */
        .modal-content-custom {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .modal-header-custom {
            background: var(--ink);
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: none;
        }

        .modal-header-custom .modal-title {
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-header-custom .btn-close-custom {
            background: rgba(255, 255, 255, .15);
            border: none;
            cursor: pointer;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, .8);
            transition: background var(--transition);
            font-size: 14px;
        }

        .modal-header-custom .btn-close-custom:hover {
            background: rgba(255, 255, 255, .25);
            color: #fff;
        }

        .modal-body-custom {
            padding: 24px;
        }

        .modal-footer-custom {
            padding: 14px 24px;
            background: var(--surface-2);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        /* Form controls */
        .form-group-c {
            margin-bottom: 16px;
        }

        .form-group-c:last-child {
            margin-bottom: 0;
        }

        .form-label-c {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--ink-soft);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .form-label-c .req {
            color: var(--danger);
            margin-left: 2px;
        }

        .form-control-c {
            display: block;
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border-2);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--ink);
            background: var(--surface);
            transition: border-color var(--transition), box-shadow var(--transition);
            outline: none;
            appearance: none;
        }

        .form-control-c:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Toggle switch for aktif */
        .toggle-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-label {
            font-size: 13px;
            color: var(--ink-soft);
        }

        .toggle {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
        }

        .toggle input {
            display: none;
        }

        .toggle-slider {
            position: absolute;
            inset: 0;
            background: var(--border-2);
            border-radius: 100px;
            cursor: pointer;
            transition: background var(--transition);
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            left: 3px;
            top: 3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            transition: transform var(--transition);
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
        }

        .toggle input:checked+.toggle-slider {
            background: var(--success);
        }

        .toggle input:checked+.toggle-slider::before {
            transform: translateX(18px);
        }

        /* ── Frekuensi checkboxes ────────────────────────────────────────────────── */
        .frekuensi-check-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 4px;
        }

        .frek-check-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border: 1px solid var(--border-2);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--ink-soft);
            cursor: pointer;
            transition: all var(--transition);
            user-select: none;
        }

        .frek-check-item:has(input:checked) {
            background: var(--accent-bg);
            border-color: var(--accent);
            color: var(--accent);
            font-weight: 500;
        }

        .frek-check-item input[type="checkbox"] {
            accent-color: var(--accent);
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        .badge-frek {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            background: var(--surface-3);
            color: var(--ink-soft);
            margin-right: 2px;
        }

        /* ── Upload Modal specific ────────────────────────────────────────────────── */
        .drop-zone {
            border: 2px dashed var(--border-2);
            border-radius: var(--radius);
            padding: 36px 24px;
            text-align: center;
            cursor: pointer;
            transition: all var(--transition);
            background: var(--surface-2);
        }

        .drop-zone:hover,
        .drop-zone.drag-over {
            border-color: var(--accent);
            background: var(--accent-bg);
        }

        .drop-zone-icon {
            font-size: 36px;
            color: var(--ink-muted);
            margin-bottom: 10px;
            display: block;
            transition: color var(--transition);
        }

        .drop-zone:hover .drop-zone-icon,
        .drop-zone.drag-over .drop-zone-icon {
            color: var(--accent);
        }

        .drop-zone-text {
            font-size: 14px;
            font-weight: 500;
            color: var(--ink-soft);
        }

        .drop-zone-sub {
            font-size: 12px;
            color: var(--ink-muted);
            margin-top: 4px;
        }

        .file-preview-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            background: var(--success-bg);
            border: 1px solid #bbf7d0;
            border-radius: var(--radius-sm);
        }

        .file-preview-icon {
            font-size: 22px;
            color: var(--success);
            flex-shrink: 0;
        }

        .file-preview-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--ink);
            flex: 1;
        }

        .file-preview-size {
            font-size: 11px;
            color: var(--ink-muted);
        }

        /* Import result */
        .import-result {
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            font-size: 13px;
        }

        .import-result.success {
            background: var(--success-bg);
            border: 1px solid #bbf7d0;
            color: var(--success);
        }

        .import-result.has-errors {
            background: var(--warning-bg);
            border: 1px solid #fde68a;
            color: var(--warning);
        }

        .error-list {
            margin: 10px 0 0;
            padding: 0;
            list-style: none;
            max-height: 150px;
            overflow-y: auto;
        }

        .error-list li {
            padding: 4px 0;
            border-bottom: 1px solid rgba(0, 0, 0, .06);
            font-size: 12px;
            color: var(--ink-soft);
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .error-list li:last-child {
            border-bottom: none;
        }

        .error-row-num {
            background: var(--danger-bg);
            color: var(--danger);
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* ── Spinner ─────────────────────────────────────────────────────────────── */
        .spin {
            animation: spin .7s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Skeleton ─────────────────────────────────────────────────────────────── */
        .skeleton {
            background: linear-gradient(90deg, var(--surface-3) 25%, var(--border) 50%, var(--surface-3) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 4px;
            height: 14px;
        }

        @keyframes shimmer {
            to {
                background-position: -200% 0;
            }
        }

        /* ── Responsive ──────────────────────────────────────────────────────────── */
        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
            }

            .table-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box input {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-wrapper">

                {{-- ── Page Header ───────────────────────────────────────────────────────── --}}
                <div class="page-header">
                    <div class="page-header-left">
                        <div class="page-breadcrumb">
                            <i class="mdi mdi-home-outline"></i>
                            <span>/</span><span>Maintenance</span>
                            <span>/</span><span>Master Mesin</span>
                        </div>
                        <h1 class="page-title">Master Mesin</h1>
                        <p class="page-subtitle">Kelola data mesin dan peralatan maintenance</p>
                    </div>
                    <div class="page-header-actions">
                        <button class="btn-base btn-outline-c" id="btnDownloadTemplate">
                            <i class="mdi mdi-file-excel-outline"></i> Template Excel
                        </button>
                        <button class="btn-base btn-success-c" id="btnOpenUpload">
                            <i class="mdi mdi-upload-outline"></i> Upload Excel
                        </button>
                        <button class="btn-base btn-primary-c" id="btnAdd">
                            <i class="mdi mdi-plus"></i> Tambah Mesin
                        </button>
                    </div>
                </div>

                {{-- ── Stat Cards ─────────────────────────────────────────────────────────── --}}
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="mdi mdi-engine-outline"></i></div>
                        <div>
                            <div class="stat-value" id="statTotal">—</div>
                            <div class="stat-label">Total Mesin</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="mdi mdi-check-circle-outline"></i></div>
                        <div>
                            <div class="stat-value" id="statAktif">—</div>
                            <div class="stat-label">Aktif</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red"><i class="mdi mdi-close-circle-outline"></i></div>
                        <div>
                            <div class="stat-value" id="statNonAktif">—</div>
                            <div class="stat-label">Non-Aktif</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon amber"><i class="mdi mdi-tag-multiple-outline"></i></div>
                        <div>
                            <div class="stat-value" id="statJenis">—</div>
                            <div class="stat-label">Jenis MTC</div>
                        </div>
                    </div>
                </div>

                {{-- ── Table Card ──────────────────────────────────────────────────────────── --}}
                <div class="card-custom">
                    <div class="table-toolbar">
                        <div class="toolbar-left">
                            <div class="search-box">
                                <i class="mdi mdi-magnify"></i>
                                <input type="text" id="searchInput" placeholder="Cari nama mesin, lokasi…">
                            </div>
                            <select class="filter-select" id="filterJenis">
                                <option value="">Semua Jenis</option>
                                <option value="Diesel Engine">Diesel Engine</option>
                                <option value="Electric Engine">Electric Engine</option>
                                <option value="Refrigerasi">Refrigerasi</option>
                                <option value="Sipil">Sipil</option>
                                <option value="Utility">Utility</option>

                                <option value="Motor Pompa">Motor Pump</option>
                                <option value="Electrical">Electrical</option>
                                <option value="Battery">Battery</option>
                                <option value="Electric P2H">Electric P2H</option>
                                <option value="Diesel P2H">Diesel P2H</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <select class="filter-select" id="filterAktif">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="toolbar-right">
                            <span class="table-info-text" id="tableCount">— data</span>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="tbl" id="tableMesin">
                            <thead>
                                <tr>
                                    <th style="width:48px">#</th>
                                    <th>Jenis MTC</th>
                                    <th>Nama Mesin</th>
                                    <th>Lokasi</th>
                                    <th>Dept</th>
                                    <th>Kode Mesin</th>
                                    <th>Frekuensi</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th style="width:90px; text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="10">
                                        <div class="table-empty">
                                            <i class="mdi mdi-loading spin"></i>
                                            <div>Memuat data…</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer">
                        <div class="table-info-text" id="paginationInfo">—</div>
                        <div class="pagination-wrap" id="paginationWrap"></div>
                    </div>
                </div>

            </div>{{-- /page-wrapper --}}

            {{-- ═══════════════════════════════════════════════════════
     MODAL — Tambah / Edit Mesin
        ═══════════════════════════════════════════════════════ --}}
            <div class="modal fade" id="modalMesin" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" style="max-width:540px">
                    <div class="modal-content modal-content-custom">
                        <div class="modal-header-custom">
                            <span class="modal-title" id="modalMesinLabel">
                                <i class="mdi mdi-engine-outline"></i>
                                <span id="modalMesinTitle">Tambah Mesin</span>
                            </span>
                            <button class="btn-close-custom" data-bs-dismiss="modal">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>

                        <form id="formMesin" novalidate>
                            @csrf
                            <input type="hidden" id="mesinId">

                            <div class="modal-body-custom">
                                <div class="form-group-c">
                                    <label class="form-label-c">Jenis Maintenance <span class="req">*</span></label>
                                    <select class="form-control-c" id="jenis_mtc" required>
                                        <option value="">— Pilih Jenis —</option>
                                        <option value="Diesel Engine">Diesel Engine</option>
                                        <option value="Electric Engine">Electric Engine</option>
                                        <option value="Refrigerasi">Refrigerasi</option>
                                        <option value="Sipil">Sipil</option>
                                        <option value="Utility">Utility</option>

                                        <option value="Motor Pompa">Motor Pump</option>
                                        <option value="Electrical">Electrical</option>
                                        <option value="Battery">Battery</option>
                                        <option value="Electric P2H">Electric P2H</option>
                                        <option value="Diesel P2H">Diesel P2H</option>
                                        <option value="others">Lainnya</option>
                                    </select>
                                </div>

                                <div class="form-group-c">
                                    <label class="form-label-c">Nama Mesin <span class="req">*</span></label>
                                    <input type="text" class="form-control-c" id="nama_mesin"
                                        placeholder="Contoh: Kompresor Udara #1" required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group-c">
                                        <label class="form-label-c">Lokasi <span class="req">*</span></label>
                                        <input type="text" class="form-control-c" id="lokasi"
                                            placeholder="Contoh: Lantai 1" required>
                                    </div>
                                    <div class="form-group-c">
                                        <label class="form-label-c">Departemen</label>
                                        <input type="text" class="form-control-c" id="dept"
                                            placeholder="Contoh: Produksi">
                                    </div>
                                </div>

                                <div class="form-group-c">
                                    <label class="form-label-c">Kode Mesin</label>
                                    <input type="text" class="form-control-c" id="kode_mesin"
                                        placeholder="Contoh: MES-001">
                                </div>

                                <div class="form-group-c">
                                    <label class="form-label-c">Frekuensi</label>
                                    <div class="frekuensi-check-group">
                                        <label class="frek-check-item">
                                            <input type="checkbox" name="frekuensi[]" value="hari"> Harian
                                        </label>
                                        <label class="frek-check-item">
                                            <input type="checkbox" name="frekuensi[]" value="minggu"> Mingguan
                                        </label>
                                        <label class="frek-check-item">
                                            <input type="checkbox" name="frekuensi[]" value="bulan"> Bulanan
                                        </label>
                                        <label class="frek-check-item">
                                            <input type="checkbox" name="frekuensi[]" value="tahun"> Tahunan
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group-c" id="groupAktif" style="display:none">
                                    <label class="form-label-c">Status</label>
                                    <div class="toggle-wrap">
                                        <label class="toggle">
                                            <input type="checkbox" id="aktif" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <span class="toggle-label" id="aktifLabel">Aktif</span>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer-custom">
                                <button type="button" class="btn-base btn-outline-c"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn-base btn-primary-c" id="btnSimpan">
                                    <i class="mdi mdi-content-save-outline"></i>
                                    <span id="btnSimpanText">Simpan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════
            MODAL — Upload Excel
        ═══════════════════════════════════════════════════════ --}}
            <div class="modal fade" id="modalUpload" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" style="max-width:480px">
                    <div class="modal-content modal-content-custom">
                        <div class="modal-header-custom" style="background:var(--success)">
                            <span class="modal-title">
                                <i class="mdi mdi-file-excel-outline"></i> Import Data via Excel
                            </span>
                            <button class="btn-close-custom" data-bs-dismiss="modal">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>

                        <div class="modal-body-custom">
                            {{-- Drop zone --}}
                            <div id="dropZone" class="drop-zone">
                                <input type="file" id="fileExcel" accept=".xlsx,.xls" style="display:none">
                                <i class="mdi mdi-cloud-upload-outline drop-zone-icon"></i>
                                <div class="drop-zone-text">Drag &amp; drop file Excel di sini</div>
                                <div class="drop-zone-sub">atau klik untuk memilih file</div>
                                <div class="drop-zone-sub" style="margin-top:8px;font-size:11px;color:var(--ink-muted)">
                                    Format: .xlsx / .xls &nbsp;·&nbsp; Maks. 5 MB
                                </div>
                            </div>

                            {{-- File preview --}}
                            <div id="filePreview" class="file-preview-box mt-3" style="display:none">
                                <i class="mdi mdi-file-excel file-preview-icon"></i>
                                <div>
                                    <div class="file-preview-name" id="fileName"></div>
                                    <div class="file-preview-size" id="fileSize"></div>
                                </div>
                                <button type="button" class="btn-base btn-ghost-danger btn-sm-c ms-auto"
                                    id="btnClearFile">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>

                            {{-- Result --}}
                            <div id="uploadResult" style="display:none; margin-top:14px"></div>
                        </div>

                        <div class="modal-footer-custom">
                            <button type="button" class="btn-base btn-outline-c" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn-base btn-success-c" id="btnImport" disabled>
                                <i class="mdi mdi-upload" id="importIcon"></i>
                                <i class="mdi mdi-loading spin d-none" id="importSpinner"></i>
                                Import
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            'use strict';

            /* ── Routes ─────────────────────────────────────────────────────────────── */
            const R = {
                getData: '{{ url('api/mtc/master/mesin/get-data') }}',
                store: '{{ route('master.mtc.mesin.store') }}',
                update: (id) => `{{ url('mtc/master/mesin/update') }}/${id}`,
                destroy: (id) => `{{ url('mtc/master/mesin/delete') }}/${id}`,
                download: '{{ route('downloadTemplate') }}',
                upload: '{{ route('uploadExcel') }}',
                csrf: '{{ csrf_token() }}',
            };

            /* ── State ──────────────────────────────────────────────────────────────── */
            let allData = [];
            let filtered = [];
            let page = 1;
            const perPage = 15;

            /* ── Helpers ─────────────────────────────────────────────────────────────── */
            function formatJenis(v) {
                if (!v) return '—';
                return v.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            }

            function fmtDate(str) {
                if (!str) return '—';
                return str.substring(0, 10);
            }

            function ajax(url, opts = {}) {
                return $.ajax({
                    url,
                    ...opts
                });
            }

            /* ── Data ───────────────────────────────────────────────────────────────── */
            function loadData() {
                $('#tableBody').html(`
            <tr><td colspan="10">
                <div class="table-empty">
                    <i class="mdi mdi-loading spin" style="font-size:28px; color:var(--ink-muted)"></i>
                    <div style="margin-top:8px; font-size:13px; color:var(--ink-muted)">Memuat data…</div>
                </div>
            </td></tr>`);

                $.get(R.getData, function(res) {
                    allData = res.data ?? [];
                    updateStats();
                    applyFilter();
                }).fail(function() {
                    $('#tableBody').html(`<tr><td colspan="10"><div class="table-empty" style="color:var(--danger)">
                <i class="mdi mdi-alert-outline" style="font-size:28px"></i>
                <div style="margin-top:8px">Gagal memuat data.</div>
            </div></td></tr>`);
                });
            }

            function updateStats() {
                const total = allData.length;
                const aktif = allData.filter(d => d.aktif).length;
                const jenis = new Set(allData.map(d => d.jenis_mtc)).size;

                $('#statTotal').text(total);
                $('#statAktif').text(aktif);
                $('#statNonAktif').text(total - aktif);
                $('#statJenis').text(jenis);
            }

            function applyFilter() {
                const q = $('#searchInput').val().toLowerCase().trim();
                const jenis = $('#filterJenis').val();
                const aktif = $('#filterAktif').val();

                filtered = allData.filter(d => {
                    const matchQ = !q || [d.nama_mesin, d.lokasi, d.dept, d.kode_mesin]
                        .some(v => (v ?? '').toLowerCase().includes(q));
                    const matchJ = !jenis || d.jenis_mtc === jenis;
                    const matchA = aktif === '' || String(d.aktif ? 1 : 0) === aktif;
                    return matchQ && matchJ && matchA;
                });

                page = 1;
                renderTable();
            }

            function renderTable() {
                const total = filtered.length;
                const start = (page - 1) * perPage;
                const paged = filtered.slice(start, start + perPage);

                $('#tableCount').text(`${total} data`);

                if (!paged.length) {
                    $('#tableBody').html(`<tr><td colspan="10">
                <div class="table-empty">
                    <i class="mdi mdi-magnify" style="font-size:28px; color:var(--ink-muted)"></i>
                    <div style="margin-top:8px; font-size:13px; color:var(--ink-muted)">
                        ${allData.length ? 'Tidak ada data yang cocok.' : 'Belum ada data mesin.'}
                    </div>
                </div></td></tr>`);
                    $('#paginationInfo').text('—');
                    $('#paginationWrap').html('');
                    return;
                }

                let html = '';
                paged.forEach((item, i) => {
                    html += `
            <tr>
                <td style="color:var(--ink-muted);font-size:12px">${start + i + 1}</td>
                <td><span class="badge-jenis">${formatJenis(item.jenis_mtc)}</span></td>
                <td class="td-primary">${item.nama_mesin ?? '—'}</td>
                <td>${item.lokasi ?? '—'}</td>
                <td>${item.dept ?? '<span style="color:var(--ink-muted)">—</span>'}</td>
                <td style="font-family:monospace;font-size:12px">${item.kode_mesin ?? '—'}</td>
                <td>${item.frekuensi_list && item.frekuensi_list.length
                    ? item.frekuensi_list.map(f => `<span class="badge-frek">${f.label ?? (f.interval + ' ' + f.satuan)}</span>`).join('')
                    : '<span style="color:var(--ink-muted)">—</span>'
                }</td>
                <td>
                    <span class="badge-pill ${item.aktif ? 'badge-active' : 'badge-inactive'}">
                        ${item.aktif ? 'Aktif' : 'Non-Aktif'}
                    </span>
                </td>
                <td style="font-size:12px;color:var(--ink-muted)">${fmtDate(item.created_at)}</td>
                <td style="text-align:center">
                    <button class="btn-base btn-ghost-warning btn-sm-c btn-icon-only btnEdit"
                            title="Edit" data-item='${JSON.stringify(item).replace(/'/g, "&#39;")}'>
                        <i class="mdi mdi-pencil-outline"></i>
                    </button>
                    <button class="btn-base btn-ghost-danger btn-sm-c btn-icon-only btnDelete"
                            title="Hapus" data-id="${item.id}">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </td>
            </tr>`;
                });
                $('#tableBody').html(html);

                /* pagination */
                const totalPages = Math.ceil(total / perPage);
                const from = start + 1;
                const to = Math.min(start + perPage, total);
                $('#paginationInfo').text(`Menampilkan ${from}–${to} dari ${total} data`);

                let pHtml = `<button class="page-btn" ${page <= 1 ? 'disabled' : ''} data-p="${page - 1}">
                        <i class="mdi mdi-chevron-left"></i></button>`;
                for (let p = 1; p <= totalPages; p++) {
                    if (totalPages > 7 && Math.abs(p - page) > 2 && p !== 1 && p !== totalPages) {
                        if (p === 2 || p === totalPages - 1) pHtml +=
                            `<span class="page-btn" style="pointer-events:none">…</span>`;
                        continue;
                    }
                    pHtml += `<button class="page-btn ${p === page ? 'active' : ''}" data-p="${p}">${p}</button>`;
                }
                pHtml += `<button class="page-btn" ${page >= totalPages ? 'disabled' : ''} data-p="${page + 1}">
                    <i class="mdi mdi-chevron-right"></i></button>`;
                $('#paginationWrap').html(pHtml);
            }

            /* ── Search / Filter ─────────────────────────────────────────────────────── */
            let searchTimer;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(applyFilter, 280);
            });
            $('#filterJenis, #filterAktif').on('change', applyFilter);

            /* ── Pagination click ────────────────────────────────────────────────────── */
            $(document).on('click', '.page-btn:not([disabled]):not(.active)', function() {
                const p = parseInt($(this).data('p'));
                if (p) {
                    page = p;
                    renderTable();
                }
            });

            /* ── Modal: Tambah ───────────────────────────────────────────────────────── */
            $('#btnAdd').on('click', function() {
                $('#formMesin')[0].reset();
                $('#mesinId').val('');
                $('#modalMesinTitle').text('Tambah Mesin');
                $('#groupAktif').hide();
                $('#aktif').prop('checked', true);
                $('input[name="frekuensi[]"]').prop('checked', false);
                $('#modalMesin').modal('show');
            });

            /* ── Modal: Edit ─────────────────────────────────────────────────────────── */
            $(document).on('click', '.btnEdit', function() {
                let d = $(this).data('item');
                if (typeof d === 'string') d = JSON.parse(d);

                $('#mesinId').val(d.id);
                $('#jenis_mtc').val(d.jenis_mtc);
                $('#nama_mesin').val(d.nama_mesin);
                $('#lokasi').val(d.lokasi);
                $('#dept').val(d.dept ?? '');
                $('#kode_mesin').val(d.kode_mesin ?? '');
                $('#aktif').prop('checked', !!d.aktif);
                $('#aktifLabel').text(d.aktif ? 'Aktif' : 'Non-Aktif');
                $('#groupAktif').show();
                $('#modalMesinTitle').text('Edit Mesin');

                // set frekuensi checkboxes
                $('input[name="frekuensi[]"]').prop('checked', false);
                const list = d.frekuensi_list ?? [];
                list.forEach(f => {
                    $(`input[name="frekuensi[]"][value="${f.satuan}"]`).prop('checked', true);
                });

                $('#modalMesin').modal('show');
            });

            /* Toggle label */
            $('#aktif').on('change', function() {
                $('#aktifLabel').text(this.checked ? 'Aktif' : 'Non-Aktif');
            });

            /* ── Form Submit ─────────────────────────────────────────────────────────── */
            $('#formMesin').on('submit', function(e) {
                e.preventDefault();

                const id = $('#mesinId').val();
                const isUpdate = !!id;
                const url = isUpdate ? R.update(id) : R.store;

                const payload = {
                    _token: R.csrf,
                    _method: 'POST',
                    jenis_mtc: $('#jenis_mtc').val(),
                    nama_mesin: $('#nama_mesin').val(),
                    lokasi: $('#lokasi').val(),
                    dept: $('#dept').val() || null,
                    kode_mesin: $('#kode_mesin').val() || null,
                    'frekuensi[]': $('input[name="frekuensi[]"]:checked').map(function() {
                        return this.value;
                    }).get(),
                    aktif: $('#aktif').is(':checked') ? 1 : 0,
                };

                Swal.fire({
                    title: 'Menyimpan…',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url,
                    type: 'POST',
                    data: payload,
                    success(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#modalMesin').modal('hide');
                        loadData();
                    },
                    error(xhr) {
                        let msg = 'Terjadi kesalahan.';
                        if (xhr.status === 422) {
                            msg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg
                        });
                    }
                });
            });

            /* ── Delete ──────────────────────────────────────────────────────────────── */
            $(document).on('click', '.btnDelete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data ini?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                }).then(r => {
                    if (!r.isConfirmed) return;
                    $.ajax({
                        url: R.destroy(id),
                        type: 'POST',
                        data: {
                            _token: R.csrf,
                            _method: 'DELETE'
                        },
                        success(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: res.message,
                                timer: 1200,
                                showConfirmButton: false
                            });
                            loadData();
                        },
                        error() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Tidak dapat menghapus data.'
                            });
                        }
                    });
                });
            });

            /* ── Download Template ───────────────────────────────────────────────────── */
            $('#btnDownloadTemplate').on('click', () => window.location.href = R.download);

            /* ── Upload Modal ────────────────────────────────────────────────────────── */
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileExcel');

            $('#btnOpenUpload').on('click', () => $('#modalUpload').modal('show'));

            function setUploadFile(file) {
                if (!file) return;
                if (!file.name.match(/\.(xlsx|xls)$/i)) {
                    showUploadResult('has-errors', '<b>Format tidak valid.</b> Gunakan file .xlsx atau .xls.');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    showUploadResult('has-errors', '<b>Ukuran file terlalu besar.</b> Maksimum 5 MB.');
                    return;
                }
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;

                $('#fileName').text(file.name);
                $('#fileSize').text((file.size / 1024).toFixed(1) + ' KB');
                $('#filePreview').show();
                $('#dropZone').hide();
                $('#btnImport').prop('disabled', false);
                $('#uploadResult').hide();
            }

            fileInput.addEventListener('change', () => setUploadFile(fileInput.files[0]));
            dropZone.addEventListener('click', () => fileInput.click());

            ['dragenter', 'dragover'].forEach(ev =>
                dropZone.addEventListener(ev, e => {
                    e.preventDefault();
                    dropZone.classList.add('drag-over');
                })
            );
            ['dragleave', 'drop'].forEach(ev =>
                dropZone.addEventListener(ev, e => {
                    e.preventDefault();
                    dropZone.classList.remove('drag-over');
                })
            );
            dropZone.addEventListener('drop', e => setUploadFile(e.dataTransfer.files[0]));

            $('#btnClearFile').on('click', function() {
                fileInput.value = '';
                $('#filePreview').hide();
                $('#dropZone').show();
                $('#btnImport').prop('disabled', true);
                $('#uploadResult').hide();
            });

            $('#btnImport').on('click', async function() {
                if (!fileInput.files.length) return;

                $('#importIcon').addClass('d-none');
                $('#importSpinner').removeClass('d-none');
                $(this).prop('disabled', true);

                const fd = new FormData();
                fd.append('file_excel', fileInput.files[0]);
                fd.append('_token', R.csrf);

                try {
                    const res = await fetch(R.upload, {
                        method: 'POST',
                        body: fd
                    });
                    const json = await res.json();

                    if (json.status) {
                        let errHtml = '';
                        if (json.errors && json.errors.length) {
                            errHtml = `<ul class="error-list mt-2">`;
                            json.errors.forEach(e => {
                                errHtml +=
                                    `<li><span class="error-row-num">Baris ${e.baris}</span>${e.masalah}</li>`;
                            });
                            errHtml += `</ul>`;
                        }
                        showUploadResult(json.skipped ? 'has-errors' : 'success',
                            `<b>${json.inserted}</b> data berhasil diimport.
                    ${json.skipped ? `<b>${json.skipped}</b> baris dilewati.` : ''}` + errHtml);
                        loadData();
                    } else {
                        const msg = json.message ?? 'Upload gagal.';
                        showUploadResult('has-errors', msg);
                    }
                } catch (err) {
                    showUploadResult('has-errors', 'Terjadi kesalahan jaringan.');
                } finally {
                    $('#importIcon').removeClass('d-none');
                    $('#importSpinner').addClass('d-none');
                    $('#btnImport').prop('disabled', false);
                }
            });

            function showUploadResult(type, html) {
                $('#uploadResult').html(`<div class="import-result ${type}">${html}</div>`).show();
            }

            /* Reset upload modal on close */
            document.getElementById('modalUpload').addEventListener('hidden.bs.modal', function() {
                fileInput.value = '';
                $('#filePreview').hide();
                $('#dropZone').show();
                dropZone.classList.remove('drag-over');
                $('#btnImport').prop('disabled', true);
                $('#uploadResult').hide();
            });

            /* ── Init ────────────────────────────────────────────────────────────────── */
            loadData();

        })();
    </script>
@endsection
