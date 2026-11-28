<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MTC Maintenance Analytics Dashboard | PT BAS</title>
        <link rel="shortcut icon" href="{{ asset('assets/images/logo/kecap.png') }}">

        <!-- CSS Dependencies -->
        <link href="{{ asset('material/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css"
            id="flatpickrThemeDark">

        <style>
            :root {
                --mtc-bg: #080c16;
                --mtc-card-bg: rgba(15, 23, 42, 0.82);
                --mtc-card-header: #0f172a;
                --mtc-border: rgba(255, 255, 255, 0.08);
                --mtc-border-hover: rgba(56, 189, 248, 0.35);
                --mtc-text-primary: #f8fafc;
                --mtc-text-secondary: #94a3b8;
                --mtc-text-muted: #64748b;

                /* Controlled Accent Colors */
                --mtc-primary: #38bdf8;
                --mtc-primary-dark: #0284c7;
                --mtc-primary-glow: rgba(56, 189, 248, 0.18);
                --mtc-success: #10b981;
                --mtc-success-glow: rgba(16, 185, 129, 0.18);
                --mtc-danger: #f43f5e;
                --mtc-danger-glow: rgba(244, 63, 94, 0.18);
            }

            html,
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                min-height: 100vh;
                background-color: var(--mtc-bg) !important;
                color: var(--mtc-text-primary);
                font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                overflow-x: hidden;
            }

            /* Custom Dark Scrollbar */
            ::-webkit-scrollbar {
                width: 5px;
                height: 5px;
            }

            ::-webkit-scrollbar-track {
                background: #090e1a !important;
            }

            ::-webkit-scrollbar-thumb {
                background: #1e293b !important;
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #38bdf8 !important;
            }

            .dashboard-wrapper {
                background: radial-gradient(circle at 10% 8%, rgba(30, 41, 59, 0.45) 0%, transparent 40%),
                    radial-gradient(circle at 90% 50%, rgba(15, 23, 42, 0.5) 0%, transparent 50%),
                    var(--mtc-bg);
                padding: 10px 14px 28px 14px;
                min-height: 100vh;
            }

            .font-mono {
                font-family: 'JetBrains Mono', monospace !important;
            }

            /* ── Header Styling ── */
            .mtc-header {
                background: linear-gradient(135deg, rgba(17, 24, 39, 0.94) 0%, rgba(13, 19, 33, 0.96) 100%);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                border: 1px solid var(--mtc-border);
                border-radius: 12px;
                padding: 10px 16px;
                box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.08);
                margin-bottom: 12px;
                position: relative;
                overflow: hidden;
            }

            .mtc-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 2px;
                background: linear-gradient(90deg, #0284c7 0%, #38bdf8 50%, #0284c7 100%);
                opacity: 0.85;
            }

            .mtc-header-title {
                font-size: 16px;
                font-weight: 800;
                letter-spacing: 0.6px;
                color: #ffffff;
                margin-bottom: 0px;
                line-height: 1.2;
                text-transform: uppercase;
            }

            .mtc-header-sub {
                font-size: 9.5px;
                font-weight: 700;
                letter-spacing: 1px;
                color: #38bdf8;
                text-transform: uppercase;
            }

            .mtc-header-motto {
                font-size: 9px;
                font-style: italic;
                color: #94a3b8;
            }

            .badge-stream-pulse {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 3px 8px;
                border-radius: 14px;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                background: rgba(16, 185, 129, 0.12);
                color: #34d399;
                border: 1px solid rgba(16, 185, 129, 0.25);
            }

            .pulse-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background-color: #10b981;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
                animation: pulseRing 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
            }

            @keyframes pulseRing {
                to {
                    box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
                }
            }

            /* ── Filter Segmented Switch ── */
            .filter-pill-container {
                display: inline-flex;
                background: rgba(15, 23, 42, 0.85);
                border: 1px solid var(--mtc-border);
                border-radius: 8px;
                padding: 3px;
                gap: 3px;
            }

            .filter-pill-btn {
                background: transparent;
                color: var(--mtc-text-secondary);
                border: none;
                border-radius: 6px;
                padding: 4px 11px;
                font-size: 11.5px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .filter-pill-btn:hover {
                color: #f8fafc;
                background: rgba(255, 255, 255, 0.05);
            }

            .filter-pill-btn.active {
                background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
                color: #38bdf8;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(56, 189, 248, 0.3);
            }

            .filter-ctrl {
                background: rgba(15, 23, 42, 0.85) !important;
                border: 1px solid var(--mtc-border) !important;
                color: #f8fafc !important;
                border-radius: 6px !important;
                font-size: 11px !important;
                font-weight: 600 !important;
                padding: 4px 8px !important;
                transition: all 0.2s ease;
            }

            .filter-ctrl:focus {
                border-color: #38bdf8 !important;
                box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2) !important;
                outline: none;
            }

            /* ── Unified KPI Glass Cards ── */
            .kpi-glass-card {
                background: var(--mtc-card-bg);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid var(--mtc-border);
                border-radius: 10px;
                padding: 12px 14px;
                position: relative;
                overflow: hidden;
                transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .kpi-glass-card:hover {
                transform: translateY(-2px);
                border-color: rgba(56, 189, 248, 0.3);
                box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.5), 0 0 14px -2px rgba(56, 189, 248, 0.12);
            }

            .kpi-glass-card::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            }

            .kpi-icon-pill {
                width: 32px;
                height: 32px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                background: rgba(56, 189, 248, 0.1);
                color: #38bdf8;
                border: 1px solid rgba(56, 189, 248, 0.2);
                flex-shrink: 0;
            }

            .kpi-icon-pill.alert {
                background: rgba(244, 63, 94, 0.1);
                color: #f43f5e;
                border: 1px solid rgba(244, 63, 94, 0.25);
            }

            .kpi-icon-pill.success {
                background: rgba(16, 185, 129, 0.1);
                color: #10b981;
                border: 1px solid rgba(16, 185, 129, 0.25);
            }

            .kpi-val {
                font-size: 1.6rem;
                font-weight: 800;
                line-height: 1.15;
                letter-spacing: -0.02em;
                margin: 6px 0 3px 0;
                color: #ffffff;
            }

            .kpi-val.optimal {
                color: #34d399 !important;
            }

            .kpi-val.danger {
                color: #f43f5e !important;
            }

            .kpi-val .unit {
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--mtc-text-secondary);
                margin-left: 3px;
            }

            .kpi-val.optimal .unit {
                color: #6ee7b7 !important;
            }

            .kpi-val.danger .unit {
                color: #fda4af !important;
            }

            .kpi-badge {
                font-size: 10px;
                font-weight: 700;
                padding: 2px 7px;
                border-radius: 4px;
                display: inline-flex;
                align-items: center;
                gap: 3px;
            }

            .kpi-badge.optimal {
                background: rgba(16, 185, 129, 0.15) !important;
                color: #34d399 !important;
                border: 1px solid rgba(16, 185, 129, 0.25) !important;
            }

            .kpi-badge.danger {
                background: rgba(244, 63, 94, 0.15) !important;
                color: #fb7185 !important;
                border: 1px solid rgba(244, 63, 94, 0.25) !important;
            }

            .kpi-badge.neutral {
                background: rgba(56, 189, 248, 0.12) !important;
                color: #38bdf8 !important;
                border: 1px solid rgba(56, 189, 248, 0.25) !important;
            }

            .kpi-progress-bar {
                height: 4px;
                background: rgba(255, 255, 255, 0.08);
                border-radius: 3px;
                overflow: hidden;
                margin-top: 6px;
            }

            .kpi-progress-fill {
                height: 100%;
                border-radius: 3px;
                background: #38bdf8;
                transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .kpi-progress-fill.optimal,
            .kpi-progress-fill.success {
                background: #10b981 !important;
            }

            .kpi-progress-fill.danger {
                background: #f43f5e !important;
            }

            .text-mtbf-optimal {
                color: #34d399 !important;
            }

            .text-mtbf-danger {
                color: #f43f5e !important;
            }

            /* ── Unified Chart Cards ── */
            .mtc-chart-card {
                background: var(--mtc-card-bg);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid var(--mtc-border);
                border-radius: 10px;
                padding: 12px 14px;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
                margin-bottom: 0;
                position: relative;
                transition: border-color 0.2s ease;
            }

            .mtc-chart-card:hover {
                border-color: rgba(255, 255, 255, 0.12);
            }

            .mtc-card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 10px;
                padding-bottom: 6px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            .mtc-card-title {
                font-size: 0.88rem;
                font-weight: 700;
                color: #ffffff;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 6px;
                letter-spacing: -0.01em;
            }

            .mtc-card-subtitle {
                font-size: 10px;
                color: var(--mtc-text-muted);
                margin-top: 2px;
            }

            /* ── Tables Styling ── */
            .mtc-table {
                width: 100%;
                margin-bottom: 0;
                color: #e2e8f0;
                font-size: 11.5px;
            }

            .mtc-table th {
                background: rgba(15, 23, 42, 0.85);
                color: var(--mtc-text-secondary);
                font-weight: 700;
                text-transform: uppercase;
                font-size: 10px;
                letter-spacing: 0.05em;
                padding: 7px 9px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
                white-space: nowrap;
            }

            .mtc-table td {
                padding: 8px 9px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.04);
                vertical-align: middle;
            }

            .mtc-table tbody tr:hover {
                background: rgba(255, 255, 255, 0.03);
            }

            .rank-badge {
                width: 20px;
                height: 20px;
                border-radius: 4px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: 10.5px;
                background: rgba(255, 255, 255, 0.06);
                color: #cbd5e1;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .rank-badge.top-1 {
                background: rgba(56, 189, 248, 0.18);
                color: #38bdf8;
                border: 1px solid rgba(56, 189, 248, 0.35);
            }

            .table-scrollable {
                max-height: 270px;
                overflow-y: auto;
            }

            /* ApexCharts Tooltip */
            .apexcharts-tooltip.apexcharts-theme-dark {
                background: rgba(15, 23, 42, 0.95) !important;
                backdrop-filter: blur(10px) !important;
                border: 1px solid rgba(255, 255, 255, 0.12) !important;
                box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.6) !important;
                border-radius: 6px !important;
            }

            .apexcharts-tooltip-title {
                background: rgba(30, 41, 59, 0.9) !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
                font-weight: 700 !important;
            }

            /* ── Asynchronous Skeleton & Loader ── */
            .kpi-skeleton-pulse {
                display: inline-block;
                opacity: 0.45;
                animation: kpiPulse 1.2s infinite ease-in-out;
            }

            @keyframes kpiPulse {
                0% {
                    opacity: 0.3;
                }

                50% {
                    opacity: 0.7;
                }

                100% {
                    opacity: 0.3;
                }
            }

            .chart-loading-overlay {
                position: absolute;
                inset: 0;
                background: rgba(15, 23, 42, 0.78);
                backdrop-filter: blur(3px);
                -webkit-backdrop-filter: blur(3px);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                z-index: 15;
                border-radius: 10px;
                transition: opacity 0.25s ease, visibility 0.25s ease;
            }

            .chart-loading-overlay.hidden {
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
            }

            /* ═══════════════════════════════════════════════════════════════
               LIGHT MODE THEME
            ═══════════════════════════════════════════════════════════════ */
            body.theme-light {
                --mtc-bg: #f8fafc;
                --mtc-card-bg: #ffffff;
                --mtc-card-header: #ffffff;
                --mtc-border: #e2e8f0;
                --mtc-border-hover: rgba(2, 132, 199, 0.4);
                --mtc-text-primary: #0f172a;
                --mtc-text-secondary: #000000;
                --mtc-text-muted: #000000;
                background-color: var(--mtc-bg) !important;
                color: var(--mtc-text-primary) !important;
            }

            body.theme-light .dashboard-wrapper {
                background: radial-gradient(circle at 10% 8%, rgba(226, 232, 240, 0.7) 0%, transparent 40%),
                    radial-gradient(circle at 90% 50%, rgba(241, 245, 249, 0.85) 0%, transparent 50%),
                    #f1f5f9 !important;
            }

            body.theme-light::-webkit-scrollbar-track {
                background: #f1f5f9 !important;
            }

            body.theme-light::-webkit-scrollbar-thumb {
                background: #cbd5e1 !important;
            }

            body.theme-light::-webkit-scrollbar-thumb:hover {
                background: #94a3b8 !important;
            }

            /* Header in Light Mode */
            body.theme-light .mtc-header {
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05) !important;
            }

            body.theme-light .mtc-header-title {
                color: #0f172a !important;
            }

            body.theme-light .mtc-header-motto {
                color: #000000 !important;
                font-weight: 500;
            }

            /* Text in Light Mode: Grey text transformed into solid black */
            body.theme-light .text-secondary,
            body.theme-light .text-muted,
            body.theme-light .mtc-card-subtitle,
            body.theme-light .kpi-glass-card .text-secondary,
            body.theme-light .kpi-glass-card .text-muted,
            body.theme-light .text-secondary.small,
            body.theme-light .mtc-header .text-secondary,
            body.theme-light .mtc-header .text-muted {
                color: #000000 !important;
                font-weight: 600;
            }

            /* Elements with light/white text adapt to dark */
            body.theme-light .text-white,
            body.theme-light .text-light,
            body.theme-light .mtc-card-title,
            body.theme-light .kpi-val:not(.optimal):not(.danger),
            body.theme-light .kpi-val:not(.optimal):not(.danger) .unit {
                color: #0f172a !important;
            }

            /* Dynamic KPI Colors in Light Mode */
            body.theme-light .kpi-val.optimal,
            body.theme-light .text-mtbf-optimal {
                color: #059669 !important;
            }

            body.theme-light .kpi-val.danger,
            body.theme-light .text-mtbf-danger {
                color: #dc2626 !important;
            }

            body.theme-light .kpi-val.optimal .unit {
                color: #047857 !important;
            }

            body.theme-light .kpi-val.danger .unit {
                color: #b91c1c !important;
            }

            body.theme-light .kpi-badge.optimal {
                background: #d1fae5 !important;
                color: #065f46 !important;
                border: 1px solid #a7f3d0 !important;
            }

            body.theme-light .kpi-badge.danger {
                background: #fee2e2 !important;
                color: #991b1b !important;
                border: 1px solid #fecaca !important;
            }

            body.theme-light .kpi-progress-fill.optimal,
            body.theme-light .kpi-progress-fill.success {
                background: #059669 !important;
            }

            body.theme-light .kpi-progress-fill.danger {
                background: #dc2626 !important;
            }

            /* Cards in Light Mode */
            body.theme-light .kpi-glass-card,
            body.theme-light .mtc-chart-card {
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04) !important;
            }

            body.theme-light .kpi-glass-card:hover {
                border-color: rgba(2, 132, 199, 0.35) !important;
                box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.08), 0 0 12px -2px rgba(56, 189, 248, 0.2) !important;
            }

            body.theme-light .mtc-chart-card:hover {
                border-color: #cbd5e1 !important;
            }

            body.theme-light .mtc-card-header {
                border-bottom: 1px solid #f1f5f9 !important;
            }

            body.theme-light .kpi-progress-bar {
                background: #e2e8f0 !important;
            }

            /* Filter Controls in Light Mode */
            body.theme-light .filter-pill-container {
                background: #e2e8f0 !important;
                border: 1px solid #cbd5e1 !important;
            }

            body.theme-light .filter-pill-btn {
                color: #000000 !important;
            }

            body.theme-light .filter-pill-btn:hover {
                color: #0f172a !important;
                background: rgba(0, 0, 0, 0.05) !important;
            }

            body.theme-light .filter-pill-btn.active {
                background: #ffffff !important;
                color: #0284c7 !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08) !important;
            }

            body.theme-light .filter-ctrl {
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
                color: #0f172a !important;
            }

            body.theme-light .filter-ctrl:focus {
                border-color: #0284c7 !important;
                box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2) !important;
            }

            /* Action Buttons & Badges in Light Mode */
            body.theme-light .btn-dark {
                background: #ffffff !important;
                color: #000000 !important;
                border-color: #cbd5e1 !important;
            }

            body.theme-light .btn-dark:hover {
                background: #f1f5f9 !important;
            }

            body.theme-light .btn-outline-secondary {
                border-color: #cbd5e1 !important;
                color: #1e293b !important;
            }

            body.theme-light .btn-outline-secondary:hover {
                background: #e2e8f0 !important;
                color: #000000 !important;
            }

            body.theme-light .btn-outline-info {
                border-color: #0284c7 !important;
                color: #0284c7 !important;
            }

            body.theme-light .btn-outline-info:hover {
                background: #0284c7 !important;
                color: #ffffff !important;
            }

            body.theme-light .badge.bg-dark {
                background-color: #f1f5f9 !important;
                color: #000000 !important;
                border-color: #cbd5e1 !important;
            }

            body.theme-light .badge.bg-dark.text-secondary {
                color: #000000 !important;
            }

            body.theme-light .badge.bg-dark.text-info {
                color: #0284c7 !important;
            }

            body.theme-light .badge.bg-dark.text-danger {
                color: #e11d48 !important;
            }

            /* Tables in Light Mode */
            body.theme-light .mtc-table {
                color: #0f172a !important;
            }

            body.theme-light .mtc-table th {
                background: #f8fafc !important;
                color: #000000 !important;
                border-bottom: 2px solid #e2e8f0 !important;
                font-weight: 800 !important;
            }

            body.theme-light .mtc-table td {
                border-bottom: 1px solid #f1f5f9 !important;
                color: #0f172a !important;
            }

            body.theme-light .mtc-table td .text-secondary {
                color: #000000 !important;
            }

            body.theme-light .mtc-table td .fw-bold.text-white {
                color: #0f172a !important;
            }

            body.theme-light .mtc-table td .font-mono.text-light {
                color: #0f172a !important;
            }

            body.theme-light .mtc-table tbody tr:hover {
                background: rgba(2, 132, 199, 0.04) !important;
            }

            body.theme-light .rank-badge {
                background: #f1f5f9 !important;
                color: #0f172a !important;
                border-color: #cbd5e1 !important;
            }

            body.theme-light .rank-badge.top-1 {
                background: rgba(56, 189, 248, 0.18) !important;
                color: #0284c7 !important;
                border-color: #38bdf8 !important;
            }

            /* Tooltips and Overlays in Light Mode */
            body.theme-light .chart-loading-overlay {
                background: rgba(255, 255, 255, 0.85) !important;
            }

            body.theme-light .apexcharts-tooltip.apexcharts-theme-light {
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.15) !important;
                border-radius: 6px !important;
                color: #0f172a !important;
            }

            body.theme-light .apexcharts-tooltip-title {
                background: #f8fafc !important;
                border-bottom: 1px solid #e2e8f0 !important;
                color: #0f172a !important;
                font-weight: 700 !important;
            }

            /* ApexCharts DataLabels & Total Labels in Light Mode */
            body.theme-light .apexcharts-datalabels-group rect,
            body.theme-light .apexcharts-data-labels rect,
            body.theme-light .apexcharts-datalabel-bg {
                fill: transparent !important;
                stroke: transparent !important;
            }

            body.theme-light .apexcharts-datalabels-group text,
            body.theme-light .apexcharts-datalabels text.apexcharts-datalabel-value,
            body.theme-light .apexcharts-data-labels text.apexcharts-datalabel {
                fill: #000000 !important;
            }

            /* Modal Unit Off Custom Dark/Light Styling */
            .modal-dark .modal-content {
                background: #0b1120;
                border: 1px solid rgba(255, 255, 255, 0.12);
                color: #f8fafc;
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.75);
                border-radius: 12px;
            }

            .modal-dark .modal-header {
                background: #0f172a;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            .modal-dark .modal-footer {
                background: #0f172a;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
            }

            .modal-dark .modal-table,
            .modal-dark .modal-table > :not(caption) > * > * {
                --vz-table-bg: transparent !important;
                --vz-table-accent-bg: transparent !important;
                --vz-table-hover-bg: transparent !important;
                --vz-table-hover-color: inherit !important;
                --bs-table-bg: transparent !important;
                --bs-table-accent-bg: transparent !important;
                --bs-table-hover-bg: transparent !important;
                --bs-table-hover-color: inherit !important;
                box-shadow: none !important;
            }

            .modal-dark .modal-table th {
                background: #1e293b !important;
                color: #94a3b8 !important;
                font-size: 11px;
                font-weight: 700;
                border-color: rgba(255, 255, 255, 0.08) !important;
                box-shadow: none !important;
            }

            .modal-dark .modal-table td {
                border-color: rgba(255, 255, 255, 0.06) !important;
                color: #e2e8f0;
                font-size: 12px;
                background-color: transparent !important;
                box-shadow: none !important;
            }

            .modal-dark .modal-table tbody tr {
                background-color: transparent !important;
                transition: background-color 0.15s ease-in-out;
            }

            .modal-dark .modal-table tbody tr:hover {
                background-color: #1e293b !important;
            }

            .modal-dark .modal-table tbody tr:hover > td,
            .modal-dark .modal-table tbody tr:hover > th {
                background-color: transparent !important;
                box-shadow: none !important;
                color: #f8fafc !important;
            }

            .modal-dark .modal-table tbody tr:hover .modal-machine-name {
                color: #ffffff !important;
            }

            /* Badges inside modal table */
            .modal-unit-code {
                display: inline-block;
                padding: 3px 8px;
                font-family: 'JetBrains Mono', monospace;
                font-size: 11px;
                font-weight: 600;
                background: #0f172a !important;
                color: #38bdf8 !important;
                border: 1px solid rgba(56, 189, 248, 0.35) !important;
                border-radius: 4px;
            }

            .modal-machine-name {
                color: #ffffff !important;
                font-size: 12px;
            }

            .modal-off-time {
                color: #cbd5e1 !important;
                font-size: 11px;
            }

            .modal-cat-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 3px 8px;
                font-size: 11px;
                font-weight: 700;
                border-radius: 4px;
                letter-spacing: 0.2px;
            }

            .modal-cat-mhe {
                background: rgba(56, 189, 248, 0.2) !important;
                color: #38bdf8 !important;
                border: 1px solid rgba(56, 189, 248, 0.45) !important;
            }

            .modal-cat-pompa {
                background: rgba(16, 185, 129, 0.2) !important;
                color: #10b981 !important;
                border: 1px solid rgba(16, 185, 129, 0.45) !important;
            }

            .modal-cat-utility {
                background: rgba(245, 158, 11, 0.2) !important;
                color: #f59e0b !important;
                border: 1px solid rgba(245, 158, 11, 0.45) !important;
            }

            .modal-cat-ac {
                background: rgba(168, 85, 247, 0.2) !important;
                color: #c084fc !important;
                border: 1px solid rgba(168, 85, 247, 0.45) !important;
            }

            .modal-status-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 3px 10px;
                font-size: 11px;
                font-weight: 700;
                border-radius: 4px;
                letter-spacing: 0.3px;
            }

            .modal-status-danger {
                background: rgba(244, 63, 94, 0.25) !important;
                color: #f43f5e !important;
                border: 1px solid rgba(244, 63, 94, 0.5) !important;
            }

            .modal-status-warning {
                background: rgba(245, 158, 11, 0.25) !important;
                color: #f59e0b !important;
                border: 1px solid rgba(245, 158, 11, 0.5) !important;
            }

            .modal-status-secondary {
                background: rgba(148, 163, 184, 0.25) !important;
                color: #94a3b8 !important;
                border: 1px solid rgba(148, 163, 184, 0.5) !important;
            }

            /* Light mode overrides for modal */
            body.theme-light .modal-dark .modal-content {
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                color: #0f172a !important;
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.15) !important;
            }

            body.theme-light .modal-dark .modal-header {
                background: #f8fafc !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }

            body.theme-light .modal-dark .modal-footer {
                background: #f8fafc !important;
                border-top: 1px solid #e2e8f0 !important;
            }

            body.theme-light .modal-dark .modal-table,
            body.theme-light .modal-dark .modal-table > :not(caption) > * > * {
                --vz-table-bg: transparent !important;
                --vz-table-accent-bg: transparent !important;
                --vz-table-hover-bg: transparent !important;
                --bs-table-bg: transparent !important;
                --bs-table-accent-bg: transparent !important;
                --bs-table-hover-bg: transparent !important;
                box-shadow: none !important;
            }

            body.theme-light .modal-dark .modal-table th {
                background: #f1f5f9 !important;
                color: #475569 !important;
                border-color: #e2e8f0 !important;
                box-shadow: none !important;
            }

            body.theme-light .modal-dark .modal-table td {
                border-color: #e2e8f0 !important;
                color: #1e293b !important;
                box-shadow: none !important;
            }

            body.theme-light .modal-dark .modal-table tbody tr:hover {
                background-color: #f1f5f9 !important;
            }

            body.theme-light .modal-dark .modal-table tbody tr:hover > td,
            body.theme-light .modal-dark .modal-table tbody tr:hover > th {
                background-color: transparent !important;
                box-shadow: none !important;
                color: #0f172a !important;
            }

            body.theme-light .modal-dark .btn-close {
                filter: none !important;
            }

            body.theme-light #searchUnitOffModal {
                background: #ffffff !important;
                border-color: #cbd5e1 !important;
                color: #0f172a !important;
            }

            body.theme-light .modal-machine-name {
                color: #0f172a !important;
            }

            body.theme-light .modal-off-time {
                color: #334155 !important;
            }

            body.theme-light .modal-unit-code {
                background: #f1f5f9 !important;
                color: #0284c7 !important;
                border-color: #cbd5e1 !important;
            }

            /* Metric summary tiles above charts */
            .mtc-stat-tile {
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.07);
                border-radius: 6px;
                padding: 4px 4px;
                transition: all 0.2s ease;
                text-align: center;
            }

            .mtc-stat-tile:hover {
                background: rgba(255, 255, 255, 0.06);
                border-color: rgba(56, 189, 248, 0.3);
            }

            .mtc-stat-label {
                font-size: 8px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mtc-stat-val {
                font-family: 'JetBrains Mono', monospace;
                font-size: 11.5px;
                font-weight: 700;
                line-height: 1.2;
                margin-top: 1px;
            }

            .mtc-stat-sub {
                font-size: 7.5px;
                color: var(--mtc-text-muted);
                line-height: 1.1;
                margin-top: 1px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            body.theme-light .mtc-stat-tile {
                background: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
            }

            body.theme-light .mtc-stat-tile:hover {
                background: #f1f5f9 !important;
                border-color: #0284c7 !important;
            }

            body.theme-light .mtc-stat-sub {
                color: #64748b !important;
            }
        </style>
    </head>

    <body>
        <div class="dashboard-wrapper">
            <div class="container-fluid px-1">

                {{-- ═══════════════════════════════════════════════════════════════
                 1. TOP EXECUTIVE HEADER & CONTROLS
            ═══════════════════════════════════════════════════════════════ --}}
                <div class="mtc-header">
                    <div class="row align-items-center g-2">
                        <!-- Left: Brand -->
                        <div class="col-xl-3 col-lg-3 col-md-12 d-flex align-items-center gap-2">
                            <div
                                style="width: 36px; height: 36px; background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.35); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="mdi mdi-wrench-cog" style="font-size: 20px; color: #38bdf8;"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="mb-0 fw-bold" style="letter-spacing: 0.6px; font-size: 14px;">MAINTENANCE
                                        DASHBOARD</h4>
                                </div>
                                <div style="font-size: 9px; color: #38bdf8; font-weight: 700; letter-spacing: 0.5px;">
                                    ENGINEERING - MTC
                                </div>
                            </div>
                        </div>

                        <!-- Center: Title -->
                        <div class="col-xl-4 col-lg-4 col-md-12 text-center d-none d-lg-block">
                            <div class="mtc-header-title">MAINTENANCE PERFORMANCE COMMAND CENTER</div>
                            <div class="mtc-header-sub">MHE • MOTOR POMPA • AC • UTILITY PLANT</div>
                            <div class="mtc-header-motto">"Reliability &amp; Maintainability (MTTR &bull; MTBF &bull;
                                Downtime Losses)"</div>
                        </div>

                        <!-- Right: Filters & Controls -->
                        <div
                            class="col-xl-5 col-lg-5 col-md-12 d-flex align-items-center justify-content-end gap-2 flex-wrap">
                            {{-- Segmented Mode Switcher --}}
                            <div class="filter-pill-container" id="filterTypeSwitch">
                                <button type="button" class="filter-pill-btn active" data-type="weekly">
                                    <i class="mdi mdi-calendar-week"></i> Weekly
                                </button>
                                <button type="button" class="filter-pill-btn" data-type="monthly">
                                    <i class="mdi mdi-calendar-month"></i> Monthly
                                </button>
                                <button type="button" class="filter-pill-btn" data-type="yearly">
                                    <i class="mdi mdi-calendar-blank"></i> Yearly
                                </button>
                                <button type="button" class="filter-pill-btn" data-type="custom">
                                    <i class="mdi mdi-calendar-range"></i> Custom
                                </button>
                            </div>

                            {{-- Dynamic Sub-Filters --}}
                            <div id="weeklyFilterGroup" class="d-flex align-items-center gap-1">
                                <button type="button"
                                    class="btn btn-sm btn-dark border border-secondary border-opacity-25 px-1 py-1"
                                    id="btnPrevWeek" title="Minggu Sebelumnya">
                                    <i class="mdi mdi-chevron-left"></i>
                                </button>
                                <select id="selectWeek" class="filter-ctrl font-mono" style="min-width: 220px;">
                                    @if (!empty($weeksOptions))
                                        @foreach ($weeksOptions as $wNum => $wText)
                                            <option value="{{ $wNum }}"
                                                {{ $wNum == $currentWeek ? 'selected' : '' }}>
                                                {{ $wText }}
                                            </option>
                                        @endforeach
                                    @else
                                        @for ($w = 1; $w <= 52; $w++)
                                            <option value="{{ $w }}"
                                                {{ $w == $currentWeek ? 'selected' : '' }}>
                                                Week {{ $w }} (2026)
                                            </option>
                                        @endfor
                                    @endif
                                </select>
                                <button type="button"
                                    class="btn btn-sm btn-dark border border-secondary border-opacity-25 px-1 py-1"
                                    id="btnNextWeek" title="Minggu Berikutnya">
                                    <i class="mdi mdi-chevron-right"></i>
                                </button>
                            </div>

                            <div id="monthlyFilterGroup" class="d-flex align-items-center gap-1 d-none">
                                <select id="selectMonth" class="filter-ctrl">
                                    @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $num => $monthName)
                                        <option value="{{ $num }}"
                                            {{ $num == $currentMonth ? 'selected' : '' }}>
                                            {{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                                <select id="selectYear" class="filter-ctrl font-mono">
                                    <option value="2026" selected>2026</option>
                                    <option value="2025">2025</option>
                                </select>
                            </div>

                            <div id="yearlyFilterGroup" class="d-flex align-items-center gap-1 d-none">
                                <select id="selectYearly" class="filter-ctrl font-mono" style="min-width: 140px;">
                                    @for ($y = $currentYear; $y >= 2024; $y--)
                                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>
                                            Tahun {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div id="customFilterGroup" class="d-none">
                                <input type="text" id="customDateRange" class="filter-ctrl"
                                    placeholder="Pilih Rentang..." style="width: 180px;">
                            </div>

                            {{-- Action Controls --}}
                            <div class="d-flex align-items-center gap-1">
                                <button type="button"
                                    class="btn btn-sm btn-outline-info d-flex align-items-center gap-1" id="btnRefresh"
                                    title="Refresh Telemetri">
                                    <i class="mdi mdi-refresh" id="refreshIcon"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    id="btnFullscreen" title="Layar Penuh (Wallboard)">
                                    <i class="mdi mdi-fullscreen" id="fsIcon"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    id="btnThemeToggle" title="Beralih Tema (Gelap / Terang)">
                                    <i class="mdi mdi-weather-sunny" id="themeToggleIcon"></i>
                                    <span class="d-none d-xl-inline" id="themeToggleText">Light</span>
                                </button>
                                <a href="{{ url('home') }}"
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    title="Kembali ke Portal">
                                    <i class="mdi mdi-home-outline"></i>
                                    <span class="d-none d-md-inline">Portal</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Status Strip --}}
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-secondary border-opacity-10"
                        style="font-size: 11px;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-secondary fw-medium">Periode:</span>
                            <span
                                class="badge bg-primary bg-opacity-20 text-info border border-info border-opacity-25 font-mono px-2 py-1"
                                id="activeFilterLabel">
                                Memuat data...
                            </span>
                            <span class="text-muted">| Jam Standar: <strong class="text-light font-mono"
                                    id="operatingHoursLabel">168 Jam</strong></span>
                        </div>
                        <div class="text-muted">
                            <i class="mdi mdi-clock-outline me-1"></i> Terakhir Diperbarui: <span id="lastUpdatedTime"
                                class="text-light fw-semibold">Baru saja</span>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════════════
                 2. KPI CARDS GRID (UNIFIED 6 METRICS)
            ═══════════════════════════════════════════════════════════════ --}}
                <div class="row g-2 mb-2">
                    {{-- Card 1: Schedule Completion --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">Schedule Completion</div>
                                    <span class="kpi-badge optimal mt-1" id="kpiCompletionBadge">Target
                                        &gt;95.0%</span>
                                </div>
                                <div class="kpi-icon-pill success">
                                    <i class="mdi mdi-calendar-check"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono" id="kpiCompletionVal">
                                    --<span class="unit">%</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill success" id="kpiCompletionBar" style="width: 0%;">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Realisasi:</span>
                                    <span class="text-light font-mono" id="kpiCompletionCount">-- / -- Unit</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Total Breakdown --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">Total Breakdown</div>
                                    <span class="kpi-badge optimal mt-1" id="kpiBreakdownBadge">Target &lt;5x</span>
                                </div>
                                <div class="kpi-icon-pill alert">
                                    <i class="mdi mdi-alert-octagon-outline"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono" id="kpiBreakdownVal">
                                    --<span class="unit">x</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill danger" id="kpiBreakdownBar" style="width: 0%;">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Kategori:</span>
                                    <span class="text-secondary font-mono">Paket Korektif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Downtime --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">Downtime Lost</div>
                                    <span class="kpi-badge optimal mt-1" id="kpiDowntimeBadge">Target &lt;2.0%</span>
                                </div>
                                <div class="kpi-icon-pill">
                                    <i class="mdi mdi-timer-sand"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono" id="kpiDowntimeVal">
                                    --<span class="unit">%</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill" id="kpiDowntimeBar" style="width: 0%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted" title="Rumus: Total Jam Korektif / 624 × 100%">Jam Lost /
                                        624:</span>
                                    <span class="text-light font-mono" id="kpiDowntimeHours">-- Jam</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4: MTTR --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">MTTR (Repair)</div>
                                    <span class="kpi-badge neutral mt-1" id="kpiMttrBadge">Target &lt;3.0 Jam</span>
                                </div>
                                <div class="kpi-icon-pill">
                                    <i class="mdi mdi-wrench-clock"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono" id="kpiMttrVal">
                                    --<span class="unit">Jam</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill" id="kpiMttrBar" style="width: 0%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Formula:</span>
                                    <span class="text-light">Durasi / Korektif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 5: MTBF --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">MTBF (Reliability)</div>
                                    <span class="kpi-badge optimal mt-1" id="kpiMtbfBadge">Target &gt;612 Jam</span>
                                </div>
                                <div class="kpi-icon-pill">
                                    <i class="mdi mdi-heart-pulse"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono" id="kpiMtbfVal">
                                    --<span class="unit">Jam</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill" id="kpiMtbfBar" style="width: 0%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Formula:</span>
                                    <span class="text-light font-mono"
                                        title="Rata-rata MTBF Unit: (624 Jam - Total MTTR) / Korektif">Avg (624 - MTTR)
                                        / Korektif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 6: Cost Maintenance --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">Maintenance Cost</div>
                                    <span class="kpi-badge neutral mt-1" id="kpiCostBadge">Budget 95 Jt</span>
                                </div>
                                <div class="kpi-icon-pill">
                                    <i class="mdi mdi-cash-multiple"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono" id="kpiCostVal" style="font-size: 1.45rem;">
                                    --
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill" id="kpiCostBar" style="width: 0%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Budget Terpakai:</span>
                                    <span class="text-light font-mono" id="kpiCostUtil">--%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════════════
                 3. CHART 1: SCHEDULE MAINTENANCE (FULL WIDTH)
            ═══════════════════════════════════════════════════════════════ --}}
                <div class="row g-2">
                    {{-- CHART 1: SCHEDULE MAINTENANCE --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-chart-box-outline text-info"></i> Schedule Maintenance
                                    </h4>
                                    <div class="mtc-card-subtitle">
                                        Realisasi vs Total Agenda (Akumulasi Stacked per Minggu)
                                    </div>
                                </div>
                                <span
                                    class="badge bg-dark text-secondary border border-secondary border-opacity-25 font-mono px-2 py-1"
                                    style="font-size: 10.5px;">
                                    Target Realisasi: &gt;95%
                                </span>
                            </div>
                            <div id="chartScheduleMaintenance" style="min-height: 295px;"></div>
                        </div>
                    </div>

                    {{-- Chart 2: Total Breakdown Frequency --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-chart-timeline-variant-shimmer text-danger"></i>
                                        Total Breakdown (Frequency Korektif)
                                    </h4>
                                </div>
                                <span
                                    class="badge bg-dark text-secondary border border-secondary border-opacity-25 font-mono px-2 py-1"
                                    style="font-size: 10.5px;">
                                    Target: &lt;5x
                                </span>
                            </div>
                            <!-- Summary per Jenis MTC -->
                            <div class="row g-1 mb-1 px-3 pt-2">
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label text-info">MHE</div>
                                        <div class="mtc-stat-val text-info" id="valBdMhe">0x</div>
                                        <div class="mtc-stat-sub">Kejadian</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label text-success">POMPA</div>
                                        <div class="mtc-stat-val text-success" id="valBdPompa">0x</div>
                                        <div class="mtc-stat-sub">Kejadian</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label text-warning">UTILITY</div>
                                        <div class="mtc-stat-val text-warning" id="valBdUtility">0x</div>
                                        <div class="mtc-stat-sub">Kejadian</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label" style="color: #c084fc;">AC</div>
                                        <div class="mtc-stat-val" style="color: #c084fc;" id="valBdAc">0x</div>
                                        <div class="mtc-stat-sub">Kejadian</div>
                                    </div>
                                </div>
                            </div>
                            <div id="chartTotalBreakdown" style="min-height: 245px;"></div>
                        </div>
                    </div>

                    {{-- Chart 3: Total Downtime Percentage & Loss Trend --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-chart-areaspline text-info"></i>
                                        Total Downtime
                                    </h4>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        class="badge bg-dark text-info border border-info border-opacity-25 font-mono px-2 py-1"
                                        id="downtimeAvgBadge" style="font-size: 10.5px;">
                                        Avg: 1.5%
                                    </span>
                                    <span
                                        class="badge bg-dark text-danger border border-danger border-opacity-25 font-mono px-2 py-1"
                                        style="font-size: 10.5px;">
                                        Target: &lt;2%
                                    </span>
                                </div>
                            </div>
                            <!-- Summary per Jenis MTC -->
                            <div class="row g-1 mb-1 px-3 pt-2">
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label text-info">MHE</div>
                                        <div class="mtc-stat-val text-info" id="valDtMhe">0h</div>
                                        <div class="mtc-stat-sub">Jam Lost</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label text-success">POMPA</div>
                                        <div class="mtc-stat-val text-success" id="valDtPompa">0h</div>
                                        <div class="mtc-stat-sub">Jam Lost</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label text-warning">UTILITY</div>
                                        <div class="mtc-stat-val text-warning" id="valDtUtility">0h</div>
                                        <div class="mtc-stat-sub">Jam Lost</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label" style="color: #c084fc;">AC</div>
                                        <div class="mtc-stat-val" style="color: #c084fc;" id="valDtAc">0h</div>
                                        <div class="mtc-stat-sub">Jam Lost</div>
                                    </div>
                                </div>
                            </div>
                            <div id="chartTotalDowntime" style="min-height: 245px;"></div>
                        </div>
                    </div>

                    {{-- Chart 4: Unit Off --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-power-plug-off text-danger"></i>
                                        Unit Off (Area Breakdown)
                                    </h4>
                                    <div class="mtc-card-subtitle">
                                        Proporsi unit maintenance dengan Paket = Off
                                    </div>
                                </div>
                                <span class="badge bg-danger bg-opacity-20 border border-danger border-opacity-25"
                                    id="unitOffTotalBadge" style="font-size: 10.5px; cursor: pointer;"
                                    title="Klik untuk lihat detail unit offline">
                                    0 Units Off
                                </span>
                            </div>
                            <div id="chartUnitOff" style="min-height: 280px; cursor: pointer;"
                                title="Klik pada potongan chart untuk lihat detail"></div>
                        </div>
                    </div>

                    {{-- Chart 5: MTTR vs MTBF Correlation --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-chart-line text-info"></i>
                                        Korelasi MTBF &amp; MTTR
                                    </h4>
                                    <div class="mtc-card-subtitle">
                                        Keandalan (MTBF) &amp; Pemulihan (MTTR)
                                    </div>
                                </div>
                                <span class="badge bg-dark text-info border border-info border-opacity-25 font-mono"
                                    style="font-size: 10px;">
                                    MTTR &bull; MTBF
                                </span>
                            </div>
                            <!-- Summary per Jenis MTC -->
                            <div class="row g-1 mb-1 px-3 pt-2">
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label text-info">MHE</div>
                                        <div class="mtc-stat-val text-info" id="valCorrMhe">0h</div>
                                        <div class="mtc-stat-sub" id="subCorrMhe">MTBF: 624h</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label text-success">POMPA</div>
                                        <div class="mtc-stat-val text-success" id="valCorrPompa">0h</div>
                                        <div class="mtc-stat-sub" id="subCorrPompa">MTBF: 624h</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label text-warning">UTILITY</div>
                                        <div class="mtc-stat-val text-warning" id="valCorrUtility">0h</div>
                                        <div class="mtc-stat-sub" id="subCorrUtility">MTBF: 624h</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="mtc-stat-tile">
                                        <div class="mtc-stat-label" style="color: #c084fc;">AC</div>
                                        <div class="mtc-stat-val" style="color: #c084fc;" id="valCorrAc">0h</div>
                                        <div class="mtc-stat-sub" id="subCorrAc">MTBF: 624h</div>
                                    </div>
                                </div>
                            </div>
                            <div id="chartMttrMtbfCorrelation" style="min-height: 240px;"></div>
                        </div>
                    </div>

                    {{-- Chart 6: Cost Maintenance Details --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-currency-usd text-info"></i>
                                        Detail Maintenance Cost
                                    </h4>
                                    <div class="mtc-card-subtitle">
                                        Akumulasi Biaya per Kategori Dept. Engineering (MHE, Pompa, Utility, AC)
                                    </div>
                                </div>
                                <span
                                    class="badge bg-dark text-secondary border border-secondary border-opacity-25 font-mono"
                                    id="budgetLimitBadge" style="font-size: 10.5px;">
                                    Budget: 95 Jt
                                </span>
                            </div>
                            <div id="chartCostMaintenance" style="min-height: 285px;"></div>
                        </div>
                    </div>

                    {{-- Table 7: Top Mesin MTBF --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-shield-check-outline"></i>
                                        Top Mesin MTBF
                                    </h4>
                                    <div class="mtc-card-subtitle">Urutan MTBF mesin (Nilai Terendah)</div>
                                </div>
                                <span
                                    class="badge bg-info bg-opacity-20 border border-info border-opacity-25 font-mono"
                                    style="font-size: 10px;">
                                    Reliability
                                </span>
                            </div>
                            <div class="table-scrollable">
                                <table class="table mtc-table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 28px;">#</th>
                                            <th>Mesin / Aset</th>
                                            <th>Area</th>
                                            <th class="text-end">MTBF</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyTopMtbf">
                                        {{-- Dynamically injected --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Table 8: Top MTTR Terlama --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-clock-alert-outline"></i>
                                        Top MTTR Terlama
                                    </h4>
                                    <div class="mtc-card-subtitle">Perbaikan paling lama (Maintenance Bottleneck)</div>
                                </div>
                                <span class="badge bg-dark text-info border border-info border-opacity-25 font-mono"
                                    style="font-size: 10px;">
                                    Bottleneck
                                </span>
                            </div>
                            <div class="table-scrollable">
                                <table class="table mtc-table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 28px;">#</th>
                                            <th>Mesin / Aset</th>
                                            <th>Area</th>
                                            <th class="text-end">MTTR</th>
                                            <th>Kendala / Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyTopMttr">
                                        {{-- Dynamically injected --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Table 9: Top Cost Tertinggi --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-cash-fast text-info"></i>
                                        Top Cost Tertinggi
                                    </h4>
                                    <div class="mtc-card-subtitle">Pengeluaran terbesar unit Engineering (Budget Impact)</div>
                                </div>
                                <span class="badge bg-dark border border-secondary border-opacity-25 font-mono"
                                    style="font-size: 10px;">
                                    Top Spender
                                </span>
                            </div>
                            <div class="table-scrollable">
                                <table class="table mtc-table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 28px;">#</th>
                                            <th>Mesin / Aset</th>
                                            <th>Area</th>
                                            <th class="text-end">Total Cost</th>
                                            <th class="text-end">% Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyTopCost">
                                        {{-- Dynamically injected --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- MODAL DETAIL UNIT OFF -->
        <div class="modal fade modal-dark" id="modalUnitOffDetail" tabindex="-1"
            aria-labelledby="modalUnitOffDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header py-2 px-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 rounded bg-danger bg-opacity-20 text-white">
                                <i class="mdi mdi-power-plug-off fs-5"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" id="modalUnitOffDetailLabel"
                                    style="font-size: 15px;">
                                    Detail Data Unit Off (Area Breakdown)
                                </h5>
                                <div class="text-secondary small" style="font-size: 11px;">
                                    Daftar unit berstatus Offline / Paket Off aktif di lapangan
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <!-- Toolbar Filter & Search -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div class="btn-group btn-group-sm" role="group" id="unitOffAreaFilters">
                                <button type="button" class="btn btn-outline-info active" data-filter="ALL">
                                    Semua Area <span class="badge bg-info text-dark ms-1" id="countFilterAll">0</span>
                                </button>
                                <button type="button" class="btn btn-outline-info"
                                    data-filter="MHE (Diesel & Electric)">
                                    MHE <span class="badge bg-secondary ms-1" id="countFilterMHE">0</span>
                                </button>
                                <button type="button" class="btn btn-outline-info" data-filter="Motor Pompa">
                                    Motor Pompa <span class="badge bg-secondary ms-1" id="countFilterPompa">0</span>
                                </button>
                                <button type="button" class="btn btn-outline-info" data-filter="Utility Plant">
                                    Utility <span class="badge bg-secondary ms-1" id="countFilterUtility">0</span>
                                </button>
                                <button type="button" class="btn btn-outline-info" data-filter="AC">
                                    AC <span class="badge bg-secondary ms-1" id="countFilterAC">0</span>
                                </button>
                            </div>
                            <div class="position-relative" style="min-width: 240px;">
                                <input type="text" class="form-control form-control-sm ps-4 font-mono"
                                    id="searchUnitOffModal" placeholder="Cari kode, nama, lokasi..."
                                    style="background: rgba(15,23,42,0.6); border-color: rgba(255,255,255,0.15); color: #fff; font-size: 11px;">
                                <i
                                    class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-2 text-secondary"></i>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive rounded border border-secondary border-opacity-25"
                            style="max-height: 440px;">
                            <table class="table align-middle mb-0 modal-table">
                                <thead class="sticky-top">
                                    <tr>
                                        <th style="width: 38px;" class="text-center">#</th>
                                        <th style="min-width: 110px;">Kode Unit</th>
                                        <th style="min-width: 170px;">Nama Mesin / Unit</th>
                                        <th style="min-width: 110px;">Kategori</th>
                                        <th style="min-width: 130px;">Lokasi / Area</th>
                                        <th style="min-width: 120px;">Off Sejak</th>
                                        <th style="min-width: 120px;">Downtime</th>
                                        <th style="min-width: 180px;">Kendala / Keterangan</th>
                                        <th style="min-width: 90px;" class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyUnitOffModal">
                                    <!-- Injected dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 justify-content-between">
                        <div class="small text-secondary" id="modalUnitOffFooterInfo" style="font-size: 11.5px;">
                            Menampilkan 0 unit offline
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- JAVASCRIPT DEPENDENCIES -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{ asset('material/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            $(document).ready(function() {
                // ─────────────────────────────────────────────────────────────
                // GLOBAL STATE & APEXCHARTS INSTANCES
                // ─────────────────────────────────────────────────────────────
                let activeFilterType = 'weekly';
                let charts = {
                    scheduleMaintenance: null,
                    totalBreakdown: null,
                    totalDowntime: null,
                    unitOff: null,
                    mttrMtbfCorrelation: null,
                    costMaintenance: null
                };

                let lastChartData = {
                    schedule: null,
                    breakdown: null,
                    downtime: null,
                    correlation: null,
                    cost: null,
                    unitOff: null
                };

                function isLightMode() {
                    return $('body').hasClass('theme-light');
                }

                function getChartTheme() {
                    const light = isLightMode();
                    return {
                        textColor: light ? '#000000' : '#94a3b8',
                        textBoldColor: light ? '#000000' : '#ffffff',
                        borderColor: light ? '#e2e8f0' : 'rgba(255, 255, 255, 0.06)',
                        axisBorderColor: light ? '#cbd5e1' : 'rgba(255, 255, 255, 0.1)',
                        legendColor: light ? '#000000' : '#cbd5e1',
                        tooltipTheme: light ? 'light' : 'dark',
                        totalDataLabelColor: light ? '#000000' : '#ffffff',
                        cardBg: light ? '#ffffff' : '#0f172a'
                    };
                }

                // Theme Toggle Functionality
                function applyTheme(isLight) {
                    $('body').toggleClass('theme-light', isLight);
                    $('#themeToggleIcon')
                        .toggleClass('mdi-weather-sunny', !isLight)
                        .toggleClass('mdi-weather-night', isLight);
                    $('#themeToggleText').text(isLight ? 'Dark' : 'Light');

                    if (isLight) {
                        $('#flatpickrThemeDark').attr('disabled', 'disabled');
                    } else {
                        $('#flatpickrThemeDark').removeAttr('disabled');
                    }

                    // Re-render cached charts with the theme palette
                    if (lastChartData.schedule) renderScheduleMaintenanceChart(lastChartData.schedule);
                    if (lastChartData.breakdown) renderTotalBreakdownChart(lastChartData.breakdown);
                    if (lastChartData.downtime) renderTotalDowntimeChart(lastChartData.downtime);
                    if (lastChartData.correlation) renderMttrMtbfCorrelationChart(lastChartData.correlation);
                    if (lastChartData.cost) renderCostMaintenanceChart(lastChartData.cost);
                    if (lastChartData.unitOff) renderUnitOffChart(lastChartData.unitOff);
                }

                // Restore saved theme on initial load
                const savedTheme = localStorage.getItem('mtc_theme');
                if (savedTheme === 'light') {
                    applyTheme(true);
                }

                $('#btnThemeToggle').on('click', function() {
                    const willBeLight = !$('body').hasClass('theme-light');
                    localStorage.setItem('mtc_theme', willBeLight ? 'light' : 'dark');
                    applyTheme(willBeLight);
                });

                // Initialize Flatpickr for custom range
                const fpCustom = flatpickr("#customDateRange", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    defaultDate: [
                        new Date(new Date().setDate(new Date().getDate() - 30)),
                        new Date()
                    ],
                    theme: "dark",
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            fetchDashboardData();
                        }
                    }
                });

                // ─────────────────────────────────────────────────────────────
                // FILTER CONTROLS & EVENT LISTENERS
                // ─────────────────────────────────────────────────────────────
                $('#filterTypeSwitch .filter-pill-btn').on('click', function() {
                    $('#filterTypeSwitch .filter-pill-btn').removeClass('active');
                    $(this).addClass('active');
                    activeFilterType = $(this).data('type');

                    $('#weeklyFilterGroup').toggleClass('d-none', activeFilterType !== 'weekly');
                    $('#monthlyFilterGroup').toggleClass('d-none', activeFilterType !== 'monthly');
                    $('#yearlyFilterGroup').toggleClass('d-none', activeFilterType !== 'yearly');
                    $('#customFilterGroup').toggleClass('d-none', activeFilterType !== 'custom');

                    fetchDashboardData();
                });

                $('#selectWeek, #selectMonth, #selectYear, #selectYearly').on('change', function() {
                    fetchDashboardData();
                });

                $('#btnPrevWeek').on('click', function() {
                    let cur = parseInt($('#selectWeek').val()) || 1;
                    if (cur > 1) {
                        $('#selectWeek').val(cur - 1).trigger('change');
                    }
                });

                $('#btnNextWeek').on('click', function() {
                    let cur = parseInt($('#selectWeek').val()) || 1;
                    if (cur < 52) {
                        $('#selectWeek').val(cur + 1).trigger('change');
                    }
                });

                $('#btnRefresh').on('click', function() {
                    const $icon = $('#refreshIcon');
                    $icon.addClass('mdi-spin');
                    fetchDashboardData().finally(() => {
                        setTimeout(() => $icon.removeClass('mdi-spin'), 600);
                    });
                });

                // Fullscreen Toggle
                $('#btnFullscreen').on('click', function() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            console.log("Error attempting to enable fullscreen:", err);
                        });
                        $('#fsIcon').removeClass('mdi-fullscreen').addClass('mdi-fullscreen-exit');
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                            $('#fsIcon').removeClass('mdi-fullscreen-exit').addClass('mdi-fullscreen');
                        }
                    }
                });

                document.addEventListener('fullscreenchange', function() {
                    const isFs = !!document.fullscreenElement;
                    $('#fsIcon').toggleClass('mdi-fullscreen-exit', isFs).toggleClass('mdi-fullscreen', !isFs);
                    setTimeout(() => {
                        window.dispatchEvent(new Event('resize'));
                    }, 300);
                });

                // ─────────────────────────────────────────────────────────────
                // ASYNC LOADING HELPERS & SKELETONS
                // ─────────────────────────────────────────────────────────────
                function showChartLoading(selector, label) {
                    const $card = $(selector).closest('.mtc-chart-card');
                    let $overlay = $card.find('.chart-loading-overlay');
                    if (!$overlay.length) {
                        $overlay = $(`
                            <div class="chart-loading-overlay">
                                <div class="spinner-border spinner-border-sm text-info mb-1" role="status"></div>
                                <span class="text-secondary small font-mono" style="font-size: 10px;">${label || 'Memuat...'}</span>
                            </div>
                        `);
                        $card.append($overlay);
                    } else {
                        $overlay.find('span').text(label || 'Memuat...');
                        $overlay.stop(true, true).removeClass('hidden').fadeIn(150);
                    }
                }

                function hideChartLoading(selector) {
                    const $card = $(selector).closest('.mtc-chart-card');
                    const $overlay = $card.find('.chart-loading-overlay');
                    if ($overlay.length) {
                        $overlay.stop(true, true).fadeOut(200, function() {
                            $(this).addClass('hidden');
                        });
                    }
                }

                function setTableLoading(tbodyId, colCount, message) {
                    $(tbodyId).html(`
                        <tr>
                            <td colspan="${colCount}" class="text-center py-4 text-muted">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="spinner-border spinner-border-sm text-info" role="status"></div>
                                    <span style="font-size: 11px;">${message || 'Memuat data...'}</span>
                                </div>
                            </td>
                        </tr>
                    `);
                }

                function setKpiLoading() {
                    $('#kpiCompletionVal, #kpiBreakdownVal, #kpiDowntimeVal, #kpiMttrVal, #kpiMtbfVal, #kpiCostVal')
                        .each(function() {
                            if (!$(this).find('.kpi-skeleton-pulse').length) {
                                $(this).html('<span class="kpi-skeleton-pulse">--</span>');
                            }
                        });
                }

                // ─────────────────────────────────────────────────────────────
                // MODULAR UI UPDATERS (Rendered as soon as section data arrives)
                // ─────────────────────────────────────────────────────────────
                function updateMetaUI(filterMeta) {
                    if (!filterMeta) return;
                    $('#activeFilterLabel').text(filterMeta.label);
                    if (filterMeta.operating_hours) {
                        $('#operatingHoursLabel').text(filterMeta.operating_hours + ' Jam');
                    }
                    $('#lastUpdatedTime').text(new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }) + ' WIB');
                }

                function applyKpiCardStatus(valSelector, badgeSelector, barSelector, isOptimal) {
                    const $val = $(valSelector);
                    const $badge = $(badgeSelector);
                    const $bar = $(barSelector);

                    if (isOptimal) {
                        $val.removeClass('danger').addClass('optimal');
                        $badge.removeClass('danger neutral').addClass('optimal');
                        $bar.removeClass('danger').addClass('optimal');
                    } else {
                        $val.removeClass('optimal').addClass('danger');
                        $badge.removeClass('optimal neutral').addClass('danger');
                        $bar.removeClass('optimal').addClass('danger');
                    }
                }

                // 1. Schedule Maintenance
                function updateScheduleUI(sc, chartData) {
                    if (sc) {
                        $('#kpiCompletionVal').html(`${sc.value}<span class="unit">%</span>`);
                        $('#kpiCompletionBar').css('width', `${Math.min(100, sc.value)}%`);
                        $('#kpiCompletionCount').text(`${sc.completed_count} / ${sc.planned_count} Unit`);
                        const isOptimal = sc.status === 'optimal' || sc.value >= sc.target;
                        applyKpiCardStatus('#kpiCompletionVal', '#kpiCompletionBadge', '#kpiCompletionBar', isOptimal);
                        $('#kpiCompletionBadge').text(
                            `Target >${sc.target}% (${isOptimal ? 'Achieved' : 'Need Focus'})`);
                    }
                    if (chartData) {
                        renderScheduleMaintenanceChart(chartData);
                        hideChartLoading('#chartScheduleMaintenance');
                    }
                }

                // 2. Reliability (Breakdown, Downtime, MTTR, MTBF, Correlation)
                function updateReliabilityUI(kpi, chartsData) {
                    if (kpi) {
                        if (kpi.total_breakdown) {
                            const bd = kpi.total_breakdown;
                            $('#kpiBreakdownVal').html(`${bd.value}<span class="unit">x</span>`);
                            const bdPct = Math.min(100, (bd.value / (bd.target * 2)) * 100);
                            $('#kpiBreakdownBar').css('width', `${bdPct}%`);
                            const isOptimal = bd.status === 'optimal' || bd.value <= bd.target;
                            applyKpiCardStatus('#kpiBreakdownVal', '#kpiBreakdownBadge', '#kpiBreakdownBar', isOptimal);
                            $('#kpiBreakdownBadge').text(isOptimal ? `Aman (<${bd.target}x)` :
                                `Alarm (>${bd.target}x)`);
                        }
                        if (kpi.downtime) {
                            const dt = kpi.downtime;
                            $('#kpiDowntimeVal').html(`${dt.percentage}<span class="unit">%</span>`);
                            let hoursText = `${dt.hours} Jam`;
                            if (dt.backup_hours && Number(dt.backup_hours) > 0) {
                                hoursText +=
                                    ` <span style="font-size:9.5px; color:#10b981; font-weight:600;" title="${dt.backup_hours} jam perbaikan korektif tidak dihitung downtime karena ter-backup">(-${dt.backup_hours}h backup)</span>`;
                            }
                            $('#kpiDowntimeHours').html(hoursText);
                            const dtPct = Math.min(100, (dt.percentage / (dt.target_percentage * 2)) * 100);
                            $('#kpiDowntimeBar').css('width', `${dtPct}%`);
                            const isOptimal = dt.status === 'optimal' || dt.percentage <= dt.target_percentage;
                            applyKpiCardStatus('#kpiDowntimeVal', '#kpiDowntimeBadge', '#kpiDowntimeBar', isOptimal);
                            $('#kpiDowntimeBadge').text(`Target <${dt.target_percentage}%`);
                        }
                        if (kpi.mttr) {
                            const mttr = kpi.mttr;
                            $('#kpiMttrVal').html(`${mttr.value}<span class="unit">${mttr.unit}</span>`);
                            const mttrPct = Math.min(100, (mttr.value / (mttr.target * 2)) * 100);
                            $('#kpiMttrBar').css('width', `${mttrPct}%`);
                            const isOptimal = mttr.status === 'optimal' || mttr.value <= mttr.target;
                            applyKpiCardStatus('#kpiMttrVal', '#kpiMttrBadge', '#kpiMttrBar', isOptimal);
                            $('#kpiMttrBadge').text(`Target <${mttr.target} Jam`);
                        }
                        if (kpi.mtbf) {
                            const mtbf = kpi.mtbf;
                            $('#kpiMtbfVal').html(`${mtbf.value}<span class="unit">${mtbf.unit}</span>`);
                            const mtbfPct = Math.min(100, (mtbf.value / 624) * 100);
                            $('#kpiMtbfBar').css('width', `${mtbfPct}%`);
                            const isOptimal = mtbf.value >= 612; // Standar minimal MTBF > 612 Jam
                            applyKpiCardStatus('#kpiMtbfVal', '#kpiMtbfBadge', '#kpiMtbfBar', isOptimal);
                            $('#kpiMtbfBadge').text(`Target >612 Jam (${isOptimal ? 'Optimal' : 'Under Target'})`);
                        }
                    }
                    if (chartsData) {
                        if (chartsData.total_breakdown) {
                            renderTotalBreakdownChart(chartsData.total_breakdown);
                            hideChartLoading('#chartTotalBreakdown');
                        }
                        if (chartsData.total_downtime) {
                            renderTotalDowntimeChart(chartsData.total_downtime);
                            hideChartLoading('#chartTotalDowntime');
                        }
                        if (chartsData.mttr_mtbf_correlation) {
                            renderMttrMtbfCorrelationChart(chartsData.mttr_mtbf_correlation);
                            hideChartLoading('#chartMttrMtbfCorrelation');
                        }
                    }
                }

                // 3. Maintenance Cost
                function updateCostUI(cost, chartData) {
                    if (cost) {
                        $('#kpiCostVal').text(cost.value);
                        $('#kpiCostUtil').text(`${cost.budget_utilization}%`);
                        $('#kpiCostBar').css('width', `${Math.min(100, cost.budget_utilization)}%`);
                        const isOptimal = cost.status === 'optimal' || cost.budget_utilization <= 100;
                        applyKpiCardStatus('#kpiCostVal', '#kpiCostBadge', '#kpiCostBar', isOptimal);
                        $('#kpiCostBadge').text(isOptimal ? `Budget ${cost.budget}` : `Over Budget ${cost.budget}`);
                    }
                    if (chartData) {
                        renderCostMaintenanceChart(chartData);
                        hideChartLoading('#chartCostMaintenance');
                    }
                }

                // 4. Top Machines Analytics
                function updateTopMachinesUI(tables) {
                    if (!tables) return;
                    if (tables.top_mtbf) renderTopMtbfTable(tables.top_mtbf);
                    if (tables.top_mttr) renderTopMttrTable(tables.top_mttr);
                    if (tables.top_cost) renderTopCostTable(tables.top_cost);
                }

                // 5. Unit Off
                let currentUnitOffList = [];
                let activeUnitOffFilter = 'ALL';

                function updateUnitOffUI(chartData, unitList) {
                    if (unitList) {
                        currentUnitOffList = unitList;
                        renderUnitOffDetailsTable(unitList);
                    }
                    if (chartData) {
                        renderUnitOffChart(chartData);
                        hideChartLoading('#chartUnitOff');
                    }
                }

                // Full Dashboard UI Updater (fallback)
                function updateDashboardUI(data) {
                    if (data.filter_meta) updateMetaUI(data.filter_meta);
                    if (data.kpi?.schedule_completion && data.charts?.schedule_maintenance) {
                        updateScheduleUI(data.kpi.schedule_completion, data.charts.schedule_maintenance);
                    }
                    if (data.kpi?.total_breakdown) {
                        updateReliabilityUI(data.kpi, data.charts);
                    }
                    if (data.kpi?.cost && data.charts?.cost_maintenance) {
                        updateCostUI(data.kpi.cost, data.charts.cost_maintenance);
                    }
                    if (data.tables) {
                        updateTopMachinesUI(data.tables);
                    }
                    if (data.charts?.unit_off || data.tables?.unit_off_list) {
                        updateUnitOffUI(data.charts?.unit_off, data.tables?.unit_off_list);
                    }
                }

                // ─────────────────────────────────────────────────────────────
                // ASYNC MODULAR FETCH (Fastest responses render immediately)
                // ─────────────────────────────────────────────────────────────
                let currentLoadSeq = 0;
                let activeXhrRequests = [];

                function abortActiveRequests() {
                    activeXhrRequests.forEach(xhr => {
                        if (xhr && xhr.abort && xhr.readyState !== 4) {
                            xhr.abort();
                        }
                    });
                    activeXhrRequests = [];
                }

                function fetchSection(sectionName, params, loadSeq) {
                    const sectionParams = Object.assign({}, params, {
                        section: sectionName
                    });
                    const xhr = $.ajax({
                        url: "{{ route('mtc.dashboard.data') }}",
                        type: "GET",
                        data: sectionParams,
                        success: function(res) {
                            if (loadSeq !== currentLoadSeq) return;
                            if (!res || !res.status) {
                                console.error(`Invalid response for section: ${sectionName}`, res);
                                return;
                            }

                            // Instantly update meta on first arriving packet
                            if (res.filter_meta) {
                                updateMetaUI(res.filter_meta);
                            }

                            // Immediately populate the section that arrived
                            if (sectionName === 'cost') {
                                updateCostUI(res.kpi?.cost, res.charts?.cost_maintenance);
                            } else if (sectionName === 'unit_off') {
                                updateUnitOffUI(res.charts?.unit_off, res.tables?.unit_off_list);
                            } else if (sectionName === 'top_machines') {
                                updateTopMachinesUI(res.tables);
                            } else if (sectionName === 'reliability') {
                                updateReliabilityUI(res.kpi, res.charts);
                            } else if (sectionName === 'schedule') {
                                updateScheduleUI(res.kpi?.schedule_completion, res.charts
                                    ?.schedule_maintenance);
                            }
                        },
                        error: function(err) {
                            if (loadSeq !== currentLoadSeq || err.statusText === 'abort') return;
                            console.error(`Failed to load section: ${sectionName}`, err);
                        }
                    });

                    activeXhrRequests.push(xhr);
                    return xhr;
                }

                function fetchDashboardData() {
                    abortActiveRequests();
                    const loadSeq = ++currentLoadSeq;

                    let selectedYear = $('#selectYear').val() || {{ $currentYear }};
                    if (activeFilterType === 'yearly') {
                        selectedYear = $('#selectYearly').val() || selectedYear;
                    }

                    const params = {
                        filter_type: activeFilterType,
                        year: selectedYear,
                        month: $('#selectMonth').val() || {{ $currentMonth }},
                        week: $('#selectWeek').val() || {{ $currentWeek }}
                    };

                    if (activeFilterType === 'custom') {
                        const rangeStr = $('#customDateRange').val();
                        if (rangeStr && rangeStr.includes(' to ')) {
                            const parts = rangeStr.split(' to ');
                            params.start_date = parts[0];
                            params.end_date = parts[1];
                        }
                    }

                    // Set UI to loading state
                    $('#activeFilterLabel').html('<i class="mdi mdi-loading mdi-spin me-1"></i>Memperbarui data...');
                    setKpiLoading();
                    setTableLoading('#tbodyTopMtbf', 5, 'Memuat Top MTBF...');
                    setTableLoading('#tbodyTopMttr', 5, 'Memuat Top MTTR...');
                    setTableLoading('#tbodyTopCost', 5, 'Memuat Top Cost...');
                    setTableLoading('#tbodyUnitOffDetails', 8, 'Memuat status unit offline...');

                    showChartLoading('#chartScheduleMaintenance', 'Memuat Schedule...');
                    showChartLoading('#chartTotalBreakdown', 'Memuat Breakdown...');
                    showChartLoading('#chartTotalDowntime', 'Memuat Downtime...');
                    showChartLoading('#chartUnitOff', 'Memuat Unit Off...');
                    showChartLoading('#chartMttrMtbfCorrelation', 'Memuat Korelasi MTBF vs MTTR...');
                    showChartLoading('#chartCostMaintenance', 'Memuat Data Cost...');

                    // Launch all 5 sections asynchronously!
                    // Fastest sections (Cost, Unit Off, Top Tables, Breakdown) will render first immediately!
                    const pCost = fetchSection('cost', params, loadSeq);
                    const pUnitOff = fetchSection('unit_off', params, loadSeq);
                    const pTopMachines = fetchSection('top_machines', params, loadSeq);
                    const pReliability = fetchSection('reliability', params, loadSeq);
                    const pSchedule = fetchSection('schedule', params, loadSeq);

                    return Promise.allSettled([pCost, pUnitOff, pTopMachines, pReliability, pSchedule]).then(() => {
                        if (loadSeq === currentLoadSeq) {
                            $('#refreshIcon').removeClass('mdi-spin');
                        }
                    });
                }

                // ─────────────────────────────────────────────────────────────
                // RENDER CHARTS FUNCTIONS (COHESIVE PALETTE)
                // ─────────────────────────────────────────────────────────────

                // Chart 1: Schedule Maintenance (Stacked Bar Chart % of Total Agenda)
                function renderScheduleMaintenanceChart(chartData) {
                    lastChartData.schedule = chartData;
                    const theme = getChartTheme();

                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'bar',
                            height: 295,
                            stacked: true,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 600
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                borderRadius: 4,
                                columnWidth: '38%',
                                dataLabels: {
                                    total: {
                                        enabled: true,
                                        formatter: function(val) {
                                            const num = Math.min(100, Math.round(Number(val) * 10) / 10);
                                            return (num >= 99.95 ? '100%' : (num % 1 === 0 ? num.toFixed(0) :
                                                num.toFixed(1)) + '%');
                                        },
                                        style: {
                                            fontSize: '11px',
                                            fontFamily: 'JetBrains Mono',
                                            fontWeight: 800,
                                            color: theme.totalDataLabelColor
                                        },
                                        offsetY: -6
                                    }
                                }
                            }
                        },
                        colors: ['#38bdf8', '#10b981', '#f59e0b', '#a855f7', '#06b6d4', '#ec4899', '#6366f1'],
                        dataLabels: {
                            enabled: true,
                            formatter: val => (val && val >= 6) ? Math.round(val) + '%' : '',
                            style: {
                                fontSize: '9px',
                                fontFamily: 'JetBrains Mono',
                                fontWeight: 700,
                                colors: ['#ffffff']
                            },
                            background: {
                                enabled: false
                            }
                        },
                        xaxis: {
                            categories: chartData.categories, // ['MHE', 'Motor Pompa', 'Utility', 'Sipil']
                            labels: {
                                style: {
                                    colors: theme.textColor,
                                    fontSize: '11px',
                                    fontFamily: 'JetBrains Mono',
                                    fontWeight: 600
                                }
                            },
                            axisBorder: {
                                color: theme.axisBorderColor
                            }
                        },
                        yaxis: {
                            min: 0,
                            max: 100,
                            forceNiceScale: false,
                            tickAmount: 5,
                            title: {
                                text: 'Realisasi Agenda (%)',
                                style: {
                                    color: theme.textColor,
                                    fontSize: '10.5px',
                                    fontWeight: 600
                                }
                            },
                            labels: {
                                formatter: val => Math.round(val) + '%',
                                style: {
                                    colors: theme.textColor,
                                    fontSize: '10.5px'
                                }
                            }
                        },
                        annotations: {
                            yaxis: [{
                                y: 95,
                                borderColor: '#10b981',
                                strokeDashArray: 4,
                                label: {
                                    borderColor: '#10b981',
                                    style: {
                                        color: '#ffffff',
                                        background: '#065f46',
                                        fontSize: '9.5px',
                                        fontWeight: 600
                                    },
                                    text: 'Target: 95%'
                                }
                            }]
                        },
                        grid: {
                            borderColor: theme.borderColor,
                            strokeDashArray: 3
                        },
                        legend: {
                            show: true,
                            position: 'bottom',
                            horizontalAlign: 'center',
                            floating: false,
                            fontSize: '11px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 600,
                            labels: {
                                colors: theme.legendColor
                            },
                            markers: {
                                radius: 3,
                                width: 10,
                                height: 10
                            },
                            itemMargin: {
                                horizontal: 10,
                                vertical: 4
                            }
                        },
                        tooltip: {
                            theme: theme.tooltipTheme,
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(val, {
                                    seriesIndex,
                                    dataPointIndex,
                                    w
                                }) {
                                    const s = w.config.series[seriesIndex];
                                    if (s && s.real_units && s.planned_units) {
                                        const c = s.real_units[dataPointIndex];
                                        const p = s.planned_units[dataPointIndex];
                                        const dRange = s.date_range ? ' (' + s.date_range + ')' : '';
                                        return Number(val).toFixed(1) + '%' + dRange + ' — ' + c + '/' + p +
                                            ' unit';
                                    }
                                    return Number(val).toFixed(1) + '%';
                                }
                            }
                        }
                    };

                    if (charts.scheduleMaintenance) charts.scheduleMaintenance.destroy();
                    charts.scheduleMaintenance = new ApexCharts(document.querySelector("#chartScheduleMaintenance"),
                        options);
                    charts.scheduleMaintenance.render();
                }

                // Chart 2: Total Breakdown
                function renderTotalBreakdownChart(chartData) {
                    lastChartData.breakdown = chartData;
                    const theme = getChartTheme();

                    if (chartData.by_jenis) {
                        $('#valBdMhe').text((chartData.by_jenis.mhe || 0) + 'x');
                        $('#valBdPompa').text((chartData.by_jenis.pompa || 0) + 'x');
                        $('#valBdUtility').text((chartData.by_jenis.utility || 0) + 'x');
                        $('#valBdAc').text((chartData.by_jenis.ac || 0) + 'x');
                    }

                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'bar',
                            height: 245,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '30%',
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        colors: ['#f43f5e'],
                        dataLabels: {
                            enabled: true,
                            formatter: val => val + 'x',
                            offsetY: -16,
                            style: {
                                fontSize: '10px',
                                fontWeight: 700,
                                colors: [theme.textBoldColor]
                            }
                        },
                        annotations: {
                            yaxis: [{
                                y: chartData.target,
                                borderColor: '#64748b',
                                strokeDashArray: 4,
                                label: {
                                    borderColor: '#64748b',
                                    style: {
                                        color: '#ffffff',
                                        background: '#334155',
                                        fontSize: '9.5px',
                                        fontWeight: 700
                                    },
                                    text: 'Target Limit (' + chartData.target + 'x)'
                                }
                            }]
                        },
                        xaxis: {
                            categories: chartData.categories,
                            labels: {
                                rotate: 0,
                                rotateAlways: false,
                                hideOverlappingLabels: false,
                                trim: false,
                                style: {
                                    colors: theme.textColor,
                                    fontSize: '11px',
                                    fontFamily: 'JetBrains Mono',
                                    fontWeight: 600
                                }
                            },
                            axisBorder: {
                                color: theme.axisBorderColor
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Frekuensi Breakdown',
                                style: {
                                    color: theme.textColor,
                                    fontSize: '10.5px',
                                    fontWeight: 600
                                }
                            },
                            labels: {
                                style: {
                                    colors: theme.textColor,
                                    fontSize: '10.5px'
                                }
                            },
                            max: Math.max(6, Math.max(...chartData.series[0].data) + 2)
                        },
                        grid: {
                            borderColor: theme.borderColor,
                            strokeDashArray: 3
                        },
                        legend: {
                            show: true,
                            showForSingleSeries: true,
                            position: 'bottom',
                            horizontalAlign: 'center',
                            floating: false,
                            fontSize: '11px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 600,
                            labels: {
                                colors: theme.legendColor
                            },
                            markers: {
                                radius: 3,
                                width: 10,
                                height: 10
                            },
                            itemMargin: {
                                horizontal: 10,
                                vertical: 4
                            }
                        },
                        tooltip: {
                            theme: theme.tooltipTheme,
                            x: {
                                formatter: function(val, {
                                    dataPointIndex,
                                    w
                                }) {
                                    const details = chartData.category_details || [];
                                    return details[dataPointIndex] || val;
                                }
                            },
                            y: {
                                formatter: val => val + ' Kejadian Korektif'
                            }
                        }
                    };

                    if (charts.totalBreakdown) charts.totalBreakdown.destroy();
                    charts.totalBreakdown = new ApexCharts(document.querySelector("#chartTotalBreakdown"), options);
                    charts.totalBreakdown.render();
                }

                // Chart 3: Total Downtime
                function renderTotalDowntimeChart(chartData) {
                    lastChartData.downtime = chartData;
                    const theme = getChartTheme();

                    if (chartData.by_jenis) {
                        $('#valDtMhe').text((chartData.by_jenis.mhe || 0) + 'h');
                        $('#valDtPompa').text((chartData.by_jenis.pompa || 0) + 'h');
                        $('#valDtUtility').text((chartData.by_jenis.utility || 0) + 'h');
                        $('#valDtAc').text((chartData.by_jenis.ac || 0) + 'h');
                    }

                    const dataVals = (chartData.series && chartData.series[0] && chartData.series[0].data) ?
                        chartData.series[0].data.map(v => Number(v) || 0) : [];
                    const maxData = dataVals.length ? Math.max(...dataVals) : 0;
                    const targetVal = Number(chartData.target) || 2.0;
                    const highestVal = Math.max(maxData, targetVal, 3.0);
                    // Add 25% headroom so highest points and datalabels are never clipped at the ceiling
                    const yMax = Math.ceil(highestVal * 1.25);

                    if (chartData.average !== undefined) {
                        $('#downtimeAvgBadge').text('Avg: ' + chartData.average + '%');
                    }

                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'area',
                            height: 245,
                            parentHeightOffset: 0,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true
                            }
                        },
                        colors: ['#10b981'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.35,
                                opacityTo: 0.05,
                                stops: [0, 95, 100]
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: val => val + '%',
                            offsetY: -6,
                            style: {
                                fontSize: '9.5px',
                                fontWeight: 700,
                                colors: [isLightMode() ? '#0284c7' : '#38bdf8']
                            },
                            background: {
                                enabled: false
                            }
                        },
                        annotations: {
                            yaxis: [{
                                y: chartData.target,
                                borderColor: '#f43f5e',
                                strokeDashArray: 4,
                                label: {
                                    borderColor: '#f43f5e',
                                    style: {
                                        color: '#ffffff',
                                        background: '#e11d48',
                                        fontSize: '9px',
                                        fontWeight: 700
                                    },
                                    text: 'Limit < ' + chartData.target + '%'
                                }
                            }]
                        },
                        xaxis: {
                            categories: chartData.categories,
                            labels: {
                                rotate: 0,
                                rotateAlways: false,
                                hideOverlappingLabels: false,
                                trim: false,
                                style: {
                                    colors: theme.textColor,
                                    fontSize: '11px',
                                    fontFamily: 'JetBrains Mono',
                                    fontWeight: 600
                                }
                            },
                            axisBorder: {
                                color: theme.axisBorderColor
                            }
                        },
                        yaxis: {
                            min: 0,
                            max: yMax,
                            forceNiceScale: true,
                            title: {
                                text: 'Downtime (%)',
                                style: {
                                    color: theme.textColor,
                                    fontSize: '10.5px',
                                    fontWeight: 600
                                }
                            },
                            labels: {
                                style: {
                                    colors: theme.textColor,
                                    fontSize: '10.5px'
                                },
                                formatter: val => (typeof val === 'number' ? (val % 1 === 0 ? val : val.toFixed(
                                    1)) : val) + '%'
                            }
                        },
                        grid: {
                            borderColor: theme.borderColor,
                            strokeDashArray: 3,
                            padding: {
                                top: 20,
                                right: 15,
                                left: 10,
                                bottom: 0
                            }
                        },
                        legend: {
                            show: true,
                            showForSingleSeries: true,
                            position: 'bottom',
                            horizontalAlign: 'center',
                            floating: false,
                            fontSize: '11px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 600,
                            labels: {
                                colors: theme.legendColor
                            },
                            markers: {
                                radius: 3,
                                width: 10,
                                height: 10
                            },
                            itemMargin: {
                                horizontal: 10,
                                vertical: 4
                            }
                        },
                        tooltip: {
                            theme: theme.tooltipTheme,
                            x: {
                                formatter: function(val, {
                                    dataPointIndex,
                                    w
                                }) {
                                    const details = chartData.category_details || [];
                                    return details[dataPointIndex] || val;
                                }
                            },
                            y: {
                                formatter: function(val, {
                                    dataPointIndex
                                }) {
                                    const hours = (chartData.hours_series && chartData.hours_series[
                                            dataPointIndex] !== undefined) ?
                                        chartData.hours_series[dataPointIndex] : null;
                                    const backupHours = (chartData.backup_hours_series && chartData
                                            .backup_hours_series[dataPointIndex] !== undefined) ?
                                        chartData.backup_hours_series[dataPointIndex] : 0;
                                    let str = val + '%';
                                    if (hours !== null) {
                                        str += ` (${hours} Jam Lost)`;
                                    }
                                    if (backupHours > 0) {
                                        str +=
                                            `<br/><span style="color:#10b981;font-size:10px;">🛡️ ${backupHours} jam dicover backup unit (bukan downtime)</span>`;
                                    }
                                    return str;
                                }
                            }
                        }
                    };

                    if (charts.totalDowntime) charts.totalDowntime.destroy();
                    charts.totalDowntime = new ApexCharts(document.querySelector("#chartTotalDowntime"), options);
                    charts.totalDowntime.render();
                }

                // Chart 7: Unit Off (Donut)
                function renderUnitOffChart(chartData) {
                    lastChartData.unitOff = chartData;
                    const theme = getChartTheme();

                    const totalUnits = chartData.total !== undefined ? chartData.total : chartData.series.reduce((a,
                        b) => a + b, 0);
                    $('#unitOffTotalBadge').text(totalUnits + ' Units Off');

                    if (totalUnits === 0) {
                        if (charts.unitOff) {
                            charts.unitOff.destroy();
                            charts.unitOff = null;
                        }
                        const titleClass = isLightMode() ? 'fw-bold text-dark' : 'fw-semibold text-white';
                        const subtitleClass = isLightMode() ? 'small text-dark' : 'small text-secondary';
                        $('#chartUnitOff').html(`
                            <div class="d-flex flex-column align-items-center justify-content-center text-center p-4" style="min-height: 280px;">
                                <div class="rounded-circle p-3 mb-2" style="background: rgba(16, 185, 129, 0.1);">
                                    <i class="mdi mdi-check-circle-outline text-success" style="font-size: 32px;"></i>
                                </div>
                                <div class="${titleClass} mb-1">Tidak Ada Unit Off</div>
                                <div class="${subtitleClass}">Tidak ada unit dengan Paket = Off pada periode ini.</div>
                            </div>
                        `);
                        return;
                    }

                    $('#chartUnitOff').empty();

                    const options = {
                        series: chartData.series,
                        labels: chartData.labels,
                        chart: {
                            type: 'donut',
                            height: 280,
                            events: {
                                dataPointSelection: function(event, chartContext, config) {
                                    const selectedIndex = config.dataPointIndex;
                                    if (selectedIndex !== undefined && selectedIndex !== -1 && chartData
                                        .labels && chartData.labels[selectedIndex]) {
                                        const selectedLabel = chartData.labels[selectedIndex];
                                        openUnitOffModal(selectedLabel);
                                    }
                                }
                            },
                            animations: {
                                enabled: true
                            }
                        },
                        colors: chartData.colors && chartData.colors.length ? chartData.colors : ['#38bdf8',
                            '#10b981', '#f59e0b', '#a855f7'
                        ],
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '72%',
                                    labels: {
                                        show: true,
                                        name: {
                                            show: true,
                                            fontSize: '11px',
                                            color: theme.textColor
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '18px',
                                            fontFamily: 'JetBrains Mono',
                                            fontWeight: 800,
                                            color: theme.textBoldColor,
                                            formatter: val => val + ' Unit'
                                        },
                                        total: {
                                            show: true,
                                            label: 'Total Off',
                                            color: theme.textColor,
                                            fontSize: '10px',
                                            formatter: () => totalUnits + ' Unit'
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'bottom',
                            fontSize: '10.5px',
                            fontWeight: 600,
                            labels: {
                                colors: theme.legendColor
                            },
                            markers: {
                                radius: 2
                            }
                        },
                        stroke: {
                            colors: [theme.cardBg],
                            width: 2
                        },
                        tooltip: {
                            theme: theme.tooltipTheme,
                            y: {
                                formatter: val => val + ' Unit Off'
                            }
                        }
                    };

                    if (charts.unitOff) charts.unitOff.destroy();
                    charts.unitOff = new ApexCharts(document.querySelector("#chartUnitOff"), options);
                    charts.unitOff.render();
                }

                // Chart 8: MTBF vs MTTR Correlation Line Chart
                function renderMttrMtbfCorrelationChart(chartData) {
                    lastChartData.correlation = chartData;
                    const theme = getChartTheme();

                    if (chartData.by_jenis) {
                        const bj = chartData.by_jenis;
                        if (bj.mhe) {
                            $('#valCorrMhe').text((bj.mhe.mttr || 0) + 'h');
                            $('#subCorrMhe').text('MTBF: ' + (bj.mhe.mtbf || 0) + 'h');
                        }
                        if (bj.pompa) {
                            $('#valCorrPompa').text((bj.pompa.mttr || 0) + 'h');
                            $('#subCorrPompa').text('MTBF: ' + (bj.pompa.mtbf || 0) + 'h');
                        }
                        if (bj.utility) {
                            $('#valCorrUtility').text((bj.utility.mttr || 0) + 'h');
                            $('#subCorrUtility').text('MTBF: ' + (bj.utility.mtbf || 0) + 'h');
                        }
                        if (bj.ac) {
                            $('#valCorrAc').text((bj.ac.mttr || 0) + 'h');
                            $('#subCorrAc').text('MTBF: ' + (bj.ac.mtbf || 0) + 'h');
                        }
                    }

                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'line',
                            height: 240,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true
                            }
                        },
                        colors: ['#38bdf8', '#f59e0b'],
                        stroke: {
                            curve: 'smooth',
                            width: [3, 3],
                            dashArray: [0, 0]
                        },
                        markers: {
                            size: [5, 5],
                            strokeWidth: 2,
                            strokeColors: theme.cardBg,
                            hover: {
                                size: 7
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val, opt) {
                                if (opt.seriesIndex === 0) return Math.round(val) + 'h';
                                return Number(val).toFixed(1) + 'h';
                            },
                            offsetY: -6,
                            style: {
                                fontSize: '9.5px',
                                fontFamily: 'JetBrains Mono',
                                fontWeight: 700,
                                colors: isLightMode() ? ['#0284c7', '#d97706'] : ['#38bdf8', '#f59e0b']
                            },
                            background: {
                                enabled: false
                            }
                        },
                        xaxis: {
                            categories: chartData.categories,
                            labels: {
                                rotate: 0,
                                rotateAlways: false,
                                hideOverlappingLabels: false,
                                trim: false,
                                style: {
                                    colors: theme.textColor,
                                    fontSize: '11px',
                                    fontFamily: 'JetBrains Mono',
                                    fontWeight: 600
                                }
                            },
                            axisBorder: {
                                color: theme.axisBorderColor
                            }
                        },
                        yaxis: [{
                                title: {
                                    text: 'MTBF (Jam)',
                                    style: {
                                        color: isLightMode() ? '#0284c7' : '#38bdf8',
                                        fontSize: '10px',
                                        fontWeight: 700
                                    }
                                },
                                labels: {
                                    formatter: val => Math.round(val) + 'h',
                                    style: {
                                        colors: isLightMode() ? '#0284c7' : '#38bdf8',
                                        fontSize: '10px',
                                        fontFamily: 'JetBrains Mono'
                                    }
                                }
                            },
                            {
                                opposite: true,
                                title: {
                                    text: 'MTTR (Jam)',
                                    style: {
                                        color: isLightMode() ? '#d97706' : '#f59e0b',
                                        fontSize: '10px',
                                        fontWeight: 700
                                    }
                                },
                                labels: {
                                    formatter: val => Number(val).toFixed(1) + 'h',
                                    style: {
                                        colors: isLightMode() ? '#d97706' : '#f59e0b',
                                        fontSize: '10px',
                                        fontFamily: 'JetBrains Mono'
                                    }
                                }
                            }
                        ],
                        grid: {
                            borderColor: theme.borderColor,
                            strokeDashArray: 3
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            labels: {
                                colors: theme.legendColor
                            },
                            fontSize: '10.5px',
                            fontWeight: 600,
                            markers: {
                                radius: 2
                            }
                        },
                        tooltip: {
                            theme: theme.tooltipTheme,
                            shared: true,
                            intersect: false,
                            x: {
                                formatter: function(val, {
                                    dataPointIndex,
                                    w
                                }) {
                                    const details = chartData.category_details || [];
                                    return details[dataPointIndex] || val;
                                }
                            },
                            y: {
                                formatter: function(val, {
                                    seriesIndex
                                }) {
                                    if (seriesIndex === 0) return val + ' Jam (MTBF Keandalan)';
                                    return val + ' Jam (MTTR Pemulihan)';
                                }
                            }
                        }
                    };

                    if (charts.mttrMtbfCorrelation) charts.mttrMtbfCorrelation.destroy();
                    charts.mttrMtbfCorrelation = new ApexCharts(document.querySelector("#chartMttrMtbfCorrelation"),
                        options);
                    charts.mttrMtbfCorrelation.render();
                }

                // Chart 9: Cost Maintenance (Stacked Bar Chart by Category)
                function renderCostMaintenanceChart(chartData) {
                    lastChartData.cost = chartData;
                    const theme = getChartTheme();

                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'bar',
                            height: 295,
                            stacked: true,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 600
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                borderRadius: 4,
                                barHeight: '52%',
                                dataLabels: {
                                    total: {
                                        enabled: true,
                                        formatter: function(val) {
                                            const num = Number(val) || 0;
                                            if (num <= 0) return '';
                                            return 'Rp ' + (num % 1 === 0 ? num.toFixed(0) : num.toFixed(1)) +
                                                ' Jt';
                                        },
                                        style: {
                                            fontSize: '10.5px',
                                            fontFamily: 'JetBrains Mono',
                                            fontWeight: 800,
                                            color: theme.totalDataLabelColor
                                        },
                                        offsetX: 8,
                                        offsetY: 0
                                    }
                                }
                            }
                        },
                        colors: ['#38bdf8', '#10b981', '#f59e0b', '#a855f7', '#06b6d4', '#ec4899', '#6366f1'],
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
                                const num = Number(val) || 0;
                                return (num >= 0.5) ? (num % 1 === 0 ? num.toFixed(0) : num.toFixed(1)) +
                                    ' Jt' : '';
                            },
                            style: {
                                fontSize: '9px',
                                fontFamily: 'JetBrains Mono',
                                fontWeight: 700,
                                colors: ['#ffffff']
                            },
                            background: {
                                enabled: false
                            }
                        },
                        xaxis: {
                            categories: chartData.categories, // ['MHE', 'Motor Pompa', 'Utility', 'AC']
                            labels: {
                                style: {
                                    colors: theme.textColor,
                                    fontSize: '10px',
                                    fontFamily: 'JetBrains Mono',
                                    fontWeight: 500
                                },
                                formatter: function(val) {
                                    const num = Number(val) || 0;
                                    return (num % 1 === 0 ? num.toFixed(0) : num.toFixed(1)) + ' Jt';
                                }
                            },
                            axisBorder: {
                                color: theme.axisBorderColor
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: theme.textColor,
                                    fontSize: '11px',
                                    fontFamily: 'Inter, sans-serif',
                                    fontWeight: 600
                                }
                            }
                        },
                        grid: {
                            borderColor: theme.borderColor,
                            strokeDashArray: 3,
                            xaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: false
                                }
                            }
                        },
                        legend: {
                            show: true,
                            position: 'bottom',
                            horizontalAlign: 'center',
                            floating: false,
                            fontSize: '11px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 600,
                            labels: {
                                colors: theme.legendColor
                            },
                            markers: {
                                radius: 3,
                                width: 10,
                                height: 10
                            },
                            itemMargin: {
                                horizontal: 10,
                                vertical: 4
                            }
                        },
                        tooltip: {
                            theme: theme.tooltipTheme,
                            shared: true,
                            intersect: false,
                            x: {
                                formatter: function(val) {
                                    return val;
                                }
                            },
                            y: {
                                formatter: function(val, {
                                    seriesIndex,
                                    dataPointIndex,
                                    w
                                }) {
                                    const s = w.config.series[seriesIndex];
                                    const dRange = s && s.date_range ? ' (' + s.date_range + ')' : '';
                                    return 'Rp ' + Number(val).toFixed(2) + ' Jt' + dRange;
                                }
                            }
                        }
                    };

                    if (charts.costMaintenance) charts.costMaintenance.destroy();
                    charts.costMaintenance = new ApexCharts(document.querySelector("#chartCostMaintenance"), options);
                    charts.costMaintenance.render();
                }

                // ─────────────────────────────────────────────────────────────
                // RENDER TABLES & LISTS
                // ─────────────────────────────────────────────────────────────

                function renderTopMtbfTable(rows) {
                    let html = '';
                    if (!rows || rows.length === 0) {
                        html =
                            `<tr><td colspan="5" class="text-center text-secondary py-3 small"><i class="mdi mdi-information-outline"></i> Tidak ada catatan korektif pada periode ini</td></tr>`;
                        $('#tbodyTopMtbf').html(html);
                        return;
                    }
                    rows.forEach(item => {
                        const rankClass = item.rank === 1 ? 'top-1' : '';
                        const isOptimal = item.nilai_mtbf >= 612;
                        const badgeClass = isOptimal ? 'optimal' : 'danger';
                        const statusLabel = isOptimal ? 'Optimal' : 'Critical';
                        const mtbfClass = isOptimal ? 'text-mtbf-optimal' : 'text-mtbf-danger';
                        html += `
                    <tr>
                        <td><span class="rank-badge ${rankClass}">${item.rank}</span></td>
                        <td>
                            <div class="fw-bold text-white">${item.nama_mesin}</div>
                            <div class="text-secondary small font-mono" style="font-size: 9.5px;">${item.kode_mesin}</div>
                        </td>
                        <td><span class="badge bg-dark text-secondary border border-secondary border-opacity-25" style="font-size: 9.5px;">${item.area}</span></td>
                        <td class="text-end font-mono fw-bold ${mtbfClass}">${item.nilai_mtbf} Jam</td>
                        <td class="text-center">
                            <span class="kpi-badge ${badgeClass}">${statusLabel}</span>
                        </td>
                    </tr>
                `;
                    });
                    $('#tbodyTopMtbf').html(html);
                }

                function renderTopMttrTable(rows) {
                    let html = '';
                    if (!rows || rows.length === 0) {
                        html =
                            `<tr><td colspan="5" class="text-center text-secondary py-3 small"><i class="mdi mdi-information-outline"></i> Tidak ada catatan korektif pada periode ini</td></tr>`;
                        $('#tbodyTopMttr').html(html);
                        return;
                    }
                    rows.forEach(item => {
                        const rankClass = item.rank === 1 ? 'top-1' : '';
                        const ket = item.keterangan || '-';
                        html += `
                    <tr>
                        <td><span class="rank-badge ${rankClass}">${item.rank}</span></td>
                        <td>
                            <div class="fw-bold text-white">${item.nama_mesin}</div>
                            <div class="text-secondary small font-mono" style="font-size: 9.5px;">${item.kode_mesin}</div>
                        </td>
                        <td><span class="badge bg-dark text-secondary border border-secondary border-opacity-25" style="font-size: 9.5px;">${item.area}</span></td>
                        <td class="text-end font-mono fw-bold text-info">${item.rata_mttr} Jam</td>
                        <td>
                            <span class="text-secondary small" title="${ket}">
                                ${ket.length > 30 ? ket.substring(0, 30) + '...' : ket}
                            </span>
                        </td>
                    </tr>
                `;
                    });
                    $('#tbodyTopMttr').html(html);
                }

                function renderTopCostTable(rows) {
                    let html = '';
                    if (!rows || rows.length === 0) {
                        html =
                            `<tr><td colspan="5" class="text-center text-secondary py-3 small"><i class="mdi mdi-information-outline"></i> Tidak ada data biaya pada periode ini</td></tr>`;
                        $('#tbodyTopCost').html(html);
                        return;
                    }
                    rows.forEach(item => {
                        const rankClass = item.rank === 1 ? 'top-1' : '';
                        html += `
                    <tr>
                        <td><span class="rank-badge ${rankClass}">${item.rank}</span></td>
                        <td>
                            <div class="fw-bold text-white">${item.nama_mesin}</div>
                            <div class="text-secondary small font-mono" style="font-size: 9.5px;">${item.kode_mesin}</div>
                        </td>
                        <td><span class="badge bg-dark text-secondary border border-secondary border-opacity-25" style="font-size: 9.5px;">${item.area}</span></td>
                        <td class="text-end font-mono fw-bold text-info">${item.total_cost_formatted}</td>
                        <td class="text-end font-mono text-light">${item.persentase}%</td>
                    </tr>
                `;
                    });
                    $('#tbodyTopCost').html(html);
                }

                function renderUnitOffDetailsTable(units) {
                    let tableHtml = '';

                    units.forEach(u => {
                        const isAlert = u.status_badge === 'danger' || u.status_badge === 'warning';
                        const statusBadgeClass = isAlert ?
                            'bg-danger bg-opacity-20 text-danger border border-danger border-opacity-25' :
                            'bg-secondary bg-opacity-20 text-secondary border border-secondary border-opacity-25';

                        tableHtml += `
                    <tr>
                        <td><span class="badge bg-dark text-info border border-info border-opacity-25 font-mono">${u.unit_id}</span></td>
                        <td class="fw-bold text-white">${u.name}</td>
                        <td><span class="badge bg-dark text-secondary border border-secondary border-opacity-25">${u.category}</span></td>
                        <td class="text-secondary">${u.lokasi}</td>
                        <td class="font-mono text-light">${u.off_since}</td>
                        <td class="font-mono fw-bold text-info">${u.downtime_str}</td>
                        <td class="text-secondary">${u.cause}</td>
                        <td class="text-center">
                            <span class="badge ${statusBadgeClass} px-2 py-1">
                                ${u.status}
                            </span>
                        </td>
                    </tr>
                `;
                    });

                    $('#tbodyUnitOffDetails').html(tableHtml);
                }

                // ─────────────────────────────────────────────────────────────
                // MODAL UNIT OFF INTERACTION
                // ─────────────────────────────────────────────────────────────
                function openUnitOffModal(filterArea) {
                    activeUnitOffFilter = filterArea || 'ALL';
                    $('#searchUnitOffModal').val('');

                    // Hitung jumlah unit per kategori untuk badge filter
                    const countAll = currentUnitOffList.length;
                    const countMHE = currentUnitOffList.filter(u => (u.area_label === 'MHE (Diesel & Electric)' || u
                        .category === 'MHE')).length;
                    const countPompa = currentUnitOffList.filter(u => (u.area_label === 'Motor Pompa' || u.category ===
                        'Motor Pompa')).length;
                    const countUtility = currentUnitOffList.filter(u => (u.area_label === 'Utility Plant' || u
                        .category === 'Utility')).length;
                    const countAC = currentUnitOffList.filter(u => (u.area_label === 'AC' || u.category === 'AC'))
                        .length;

                    $('#countFilterAll').text(countAll);
                    $('#countFilterMHE').text(countMHE);
                    $('#countFilterPompa').text(countPompa);
                    $('#countFilterUtility').text(countUtility);
                    $('#countFilterAC').text(countAC);

                    // Set status tombol filter aktif
                    $('#unitOffAreaFilters button').removeClass('active');
                    let matchedBtn = $(`#unitOffAreaFilters button[data-filter="${activeUnitOffFilter}"]`);
                    if (matchedBtn.length) {
                        matchedBtn.addClass('active');
                    } else {
                        $('#unitOffAreaFilters button[data-filter="ALL"]').addClass('active');
                        activeUnitOffFilter = 'ALL';
                    }

                    renderUnitOffModalTable();

                    const modalEl = document.getElementById('modalUnitOffDetail');
                    if (window.bootstrap && bootstrap.Modal) {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modalInstance.show();
                    } else {
                        $('#modalUnitOffDetail').modal('show');
                    }
                }

                function renderUnitOffModalTable() {
                    const searchTerm = ($('#searchUnitOffModal').val() || '').toLowerCase().trim();

                    const filtered = currentUnitOffList.filter(u => {
                        // Filter Kategori/Area
                        if (activeUnitOffFilter !== 'ALL') {
                            const matchesArea = (u.area_label === activeUnitOffFilter) ||
                                (u.category === activeUnitOffFilter) ||
                                (activeUnitOffFilter.includes(u.category));
                            if (!matchesArea) return false;
                        }
                        // Filter Search Keyword
                        if (searchTerm) {
                            const searchStr =
                                `${u.unit_id} ${u.name} ${u.category} ${u.lokasi} ${u.cause} ${u.status}`
                                .toLowerCase();
                            if (!searchStr.includes(searchTerm)) return false;
                        }
                        return true;
                    });

                    let html = '';
                    if (filtered.length === 0) {
                        html = `
                            <tr>
                                <td colspan="9" class="text-center py-4 text-secondary">
                                    <div class="mb-1"><i class="mdi mdi-information-outline fs-3"></i></div>
                                    <div class="fw-semibold">Tidak ada unit offline</div>
                                    <div class="small">Tidak ada data unit yang cocok dengan filter atau kata kunci pencarian.</div>
                                </td>
                            </tr>
                        `;
                    } else {
                        filtered.forEach((u, idx) => {
                            let catClass = 'modal-cat-mhe';
                            if (u.category === 'Motor Pompa') catClass = 'modal-cat-pompa';
                            else if (u.category === 'Utility') catClass = 'modal-cat-utility';
                            else if (u.category === 'AC') catClass = 'modal-cat-ac';

                            let statusClass = 'modal-status-danger';
                            if (u.status_badge === 'warning') statusClass = 'modal-status-warning';
                            else if (u.status_badge === 'secondary') statusClass = 'modal-status-secondary';

                            html += `
                                <tr>
                                    <td class="text-center font-mono text-secondary" style="font-size: 11px;">${idx + 1}</td>
                                    <td>
                                        <span class="modal-unit-code">
                                            ${u.unit_id}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold modal-machine-name">${u.name}</div>
                                    </td>
                                    <td>
                                        <span class="modal-cat-badge ${catClass}">
                                            ${u.category}
                                        </span>
                                    </td>
                                    <td style="color: #94a3b8; font-size: 11.5px;">${u.lokasi || '-'}</td>
                                    <td>
                                        <span class="font-mono modal-off-time">${u.off_since || '-'}</span>
                                    </td>
                                    <td>
                                        <span class="font-mono fw-bold text-info" style="font-size: 11.5px;">${u.downtime_str || '-'}</span>
                                    </td>
                                    <td style="color: #94a3b8; font-size: 11.5px;">${u.cause || '-'}</td>
                                    <td class="text-center">
                                        <span class="modal-status-badge ${statusClass}">
                                            ${u.status}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });
                    }

                    $('#tbodyUnitOffModal').html(html);
                    $('#modalUnitOffFooterInfo').text(
                        `Menampilkan ${filtered.length} dari ${currentUnitOffList.length} total unit offline`);
                }

                // Event listener klik tombol filter area di modal
                $(document).on('click', '#unitOffAreaFilters button', function() {
                    $('#unitOffAreaFilters button').removeClass('active');
                    $(this).addClass('active');
                    activeUnitOffFilter = $(this).data('filter');
                    renderUnitOffModalTable();
                });

                // Event listener live search di modal
                $(document).on('input', '#searchUnitOffModal', function() {
                    renderUnitOffModalTable();
                });

                // Event listener klik badge total unit off pada card header
                $(document).on('click', '#unitOffTotalBadge', function() {
                    openUnitOffModal('ALL');
                });

                // ─────────────────────────────────────────────────────────────
                // INITIAL LOAD
                // ─────────────────────────────────────────────────────────────
                fetchDashboardData();
            });
        </script>
    </body>

</html>
