<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WCO – SHE Dashboard WWTP | PT BAS</title>
        <link rel="shortcut icon" href="{{ asset('assets/images/logo/kecap.png') }}">

        <!-- CSS Dependencies -->
        <link href="{{ asset('material/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap"
            rel="stylesheet">

        <style>
            :root {
                --wco-bg: #070c18;
                --wco-card-bg: #0d1527;
                --wco-card-header: #101c35;
                --wco-border: #1e293b;
                --wco-cyan: #06b6d4;
                --wco-green: #10b981;
                --wco-amber: #f59e0b;
                --wco-purple: #8b5cf6;
                --wco-blue: #3b82f6;
                --wco-red: #ef4444;
                --wco-text-muted: #94a3b8;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                min-height: 100vh;
                background-color: var(--wco-bg) !important;
                color: #f1f5f9;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                overflow-x: hidden;
            }

            /* Custom Dark Scrollbar & Corner Fix to eliminate white scrollbar box */
            ::-webkit-scrollbar {
                width: 5px;
                height: 5px;
            }

            ::-webkit-scrollbar-track {
                background: #0d1527 !important;
            }

            ::-webkit-scrollbar-thumb {
                background: #1e293b !important;
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #06b6d4 !important;
            }

            ::-webkit-scrollbar-corner {
                background: #0d1527 !important;
            }

            .wco-dashboard-wrapper {
                background-color: var(--wco-bg);
                color: #f1f5f9;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                padding-bottom: 0.5rem;
            }

            /* Header styling */
            .wco-header {
                background: linear-gradient(135deg, #0b1329 0%, #0d1a38 50%, #0a1124 100%);
                border: 1px solid #1e2e4a;
                border-radius: 12px;
                padding: 10px 18px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
                margin-bottom: 12px;
            }

            .wco-header-title {
                font-size: 20px;
                font-weight: 800;
                letter-spacing: 1.2px;
                color: #ffffff;
                text-shadow: 0 0 15px rgba(6, 182, 212, 0.35);
                margin-bottom: 0px;
                line-height: 1.2;
            }

            .wco-header-sub {
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 1px;
                color: var(--wco-cyan);
                text-transform: uppercase;
            }

            .wco-header-motto {
                font-size: 10px;
                font-style: italic;
                color: #94a3b8;
            }

            /* Common Card Styling - Compact & Fixed Overflow for Perfect Corners */
            .wco-card {
                background: var(--wco-card-bg);
                border: 1px solid var(--wco-border);
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
                margin-bottom: 12px;
                display: flex;
                flex-direction: column;
                height: 100%;
                overflow: hidden;
                /* Ensures rounded corners are never clipped */
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }

            .wco-card:hover {
                border-color: rgba(56, 189, 248, 0.4);
                box-shadow: 0 6px 20px rgba(6, 182, 212, 0.12);
            }

            .wco-card-header {
                background-color: var(--wco-card-header);
                border-bottom: 1px solid var(--wco-border);
                padding: 7px 10px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-top-left-radius: 9px;
                border-top-right-radius: 9px;
            }

            .wco-card-title {
                font-size: 10.5px;
                font-weight: 700;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                color: #ffffff;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .wco-card-body {
                padding: 8px 10px;
                flex-grow: 1;
                overflow: hidden;
            }

            /* Top KPI Cards */
            .kpi-score-card {
                background: linear-gradient(135deg, #091a2f 0%, #0a233f 100%);
                border: 1px solid #19436d;
                border-radius: 10px;
                padding: 8px 10px;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                overflow: hidden;
            }

            .kpi-top-card {
                background: var(--wco-card-bg);
                border: 1px solid var(--wco-border);
                border-radius: 10px;
                padding: 8px 10px;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                overflow: hidden;
                transition: all 0.2s ease;
            }

            .kpi-top-card:hover {
                border-color: var(--wco-cyan);
            }

            .kpi-top-header {
                display: flex;
                align-items: center;
                gap: 6px;
                margin-bottom: 2px;
            }

            .kpi-top-icon {
                width: 26px;
                height: 26px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 15px;
                flex-shrink: 0;
            }

            .kpi-top-label {
                font-size: 9.5px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #94a3b8;
                line-height: 1.1;
            }

            .kpi-top-val {
                font-size: 16px;
                font-weight: 800;
                color: #ffffff;
                letter-spacing: -0.3px;
                line-height: 1.2;
            }

            .kpi-top-val.text-danger {
                color: var(--wco-red) !important;
            }

            .kpi-top-val.text-success {
                color: var(--wco-green) !important;
            }

            .kpi-top-card.border-danger-subtle {
                border-color: rgba(239, 68, 68, 0.45) !important;
            }

            .kpi-top-card.border-success-subtle {
                border-color: rgba(16, 185, 129, 0.35) !important;
            }

            .kpi-top-sub {
                font-size: 8.5px;
                font-weight: 600;
                color: var(--wco-cyan);
                display: flex;
                align-items: center;
                gap: 4px;
                margin-top: 2px;
            }

            /* Equalisasi mini list */
            .eq-pill-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 3px 5px;
                margin-top: 3px;
            }

            .eq-pill-item {
                background: rgba(255, 255, 255, 0.04);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 4px;
                padding: 1px 5px;
                font-size: 8.5px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .eq-pill-item span {
                color: #94a3b8;
            }

            .eq-pill-item strong {
                color: #38bdf8;
                font-family: monospace;
                font-size: 9px;
            }

            /* Info WWTP Tangki List */
            .info-tank-item {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 3px 6px;
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 6px;
                transition: all 0.2s ease;
            }

            .info-tank-item:hover {
                background: rgba(255, 255, 255, 0.05);
                border-color: rgba(6, 182, 212, 0.3);
            }

            .info-tank-item img {
                width: 32px;
                height: 32px;
                object-fit: contain;
                filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
            }

            .info-tank-meta {
                overflow: hidden;
                line-height: 1.15;
            }

            .info-tank-name {
                font-size: 9px;
                font-weight: 700;
                color: #94a3b8;
                white-space: nowrap;
                text-overflow: ellipsis;
                overflow: hidden;
            }

            .info-tank-cap {
                font-size: 11px;
                font-weight: 800;
                color: #38bdf8;
                font-family: monospace;
            }

            /* Removal Mini */
            .removal-metric-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 1px 0;
                font-size: 9.5px;
            }

            .removal-metric-row span {
                color: #94a3b8;
            }

            .removal-metric-row strong {
                color: #10b981;
                font-size: 11px;
            }

            /* ApexCharts Tooltip Dark Styling for WCO Dashboard (Dark Mode) */
            body:not(.wco-light-mode) .apexcharts-tooltip,
            body:not(.wco-light-mode) .apexcharts-tooltip.apexcharts-theme-light,
            body:not(.wco-light-mode) .apexcharts-tooltip.apexcharts-theme-dark,
            body:not(.wco-light-mode) div.apexcharts-tooltip {
                background: #0f172a !important;
                color: #f8fafc !important;
                border: 1px solid #334155 !important;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6) !important;
            }

            body:not(.wco-light-mode) .apexcharts-tooltip-title {
                background: #1e293b !important;
                border-bottom: 1px solid #334155 !important;
                color: #38bdf8 !important;
                font-weight: 700 !important;
                padding: 4px 8px !important;
            }

            body:not(.wco-light-mode) .apexcharts-tooltip-text,
            body:not(.wco-light-mode) .apexcharts-tooltip-text-y-label,
            body:not(.wco-light-mode) .apexcharts-tooltip-text-y-value,
            body:not(.wco-light-mode) .apexcharts-tooltip-series-group {
                color: #f8fafc !important;
            }

            body:not(.wco-light-mode) .apexcharts-tooltip-series-group.apexcharts-active,
            body:not(.wco-light-mode) .apexcharts-tooltip-series-group:last-child {
                padding-bottom: 4px !important;
            }

            body:not(.wco-light-mode) .apexcharts-xaxistooltip,
            body:not(.wco-light-mode) .apexcharts-yaxistooltip {
                background: #0f172a !important;
                color: #38bdf8 !important;
                border: 1px solid #334155 !important;
            }

            body:not(.wco-light-mode) .apexcharts-xaxistooltip:after,
            body:not(.wco-light-mode) .apexcharts-xaxistooltip:before {
                border-bottom-color: #0f172a !important;
            }

            /* High Tech Tables */
            .wco-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                font-size: 10.5px;
            }

            .wco-table th {
                background-color: rgba(255, 255, 255, 0.03);
                color: #94a3b8;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                padding: 4px 6px;
                border-bottom: 1px solid var(--wco-border);
                font-size: 9.5px;
            }

            .wco-table td {
                padding: 4px 6px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.04);
                color: #e2e8f0;
                vertical-align: middle;
            }

            .wco-table tr:hover td {
                background-color: rgba(255, 255, 255, 0.02);
            }

            .wco-table tr:last-child td {
                border-bottom: none;
            }

            /* Status indicators */
            .pulse-dot {
                display: inline-block;
                width: 6px;
                height: 6px;
                border-radius: 50%;
                margin-right: 4px;
            }

            .pulse-green {
                background-color: #10b981;
                box-shadow: 0 0 6px #10b981;
            }

            .pulse-red {
                background-color: #ef4444;
                box-shadow: 0 0 6px #ef4444;
            }

            .badge-ok {
                background: rgba(16, 185, 129, 0.15);
                color: #10b981;
                border: 1px solid rgba(16, 185, 129, 0.4);
                border-radius: 4px;
                padding: 1px 5px;
                font-size: 8.5px;
                font-weight: 700;
            }

            .badge-warn {
                background: rgba(245, 158, 11, 0.15);
                color: #f59e0b;
                border: 1px solid rgba(245, 158, 11, 0.4);
                border-radius: 4px;
                padding: 1px 5px;
                font-size: 8.5px;
                font-weight: 700;
            }

            .badge-danger {
                background: rgba(239, 68, 68, 0.15);
                color: #ef4444;
                border: 1px solid rgba(239, 68, 68, 0.4);
                border-radius: 4px;
                padding: 1px 5px;
                font-size: 8.5px;
                font-weight: 700;
            }

            /* Operation Status List */
            .op-status-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 2.5px 6px;
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 5px;
                margin-bottom: 3px;
                transition: all 0.2s ease;
            }

            .op-status-item:hover {
                background: rgba(255, 255, 255, 0.04);
                border-color: rgba(6, 182, 212, 0.3);
            }

            .op-status-item:last-child {
                margin-bottom: 0;
            }

            /* Chart Containers - Controlled Height */
            .wco-chart-box {
                height: 135px;
                width: 100%;
                position: relative;
            }

            .wco-mini-stat {
                font-size: 9.5px;
                color: #94a3b8;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 2px 6px;
                background: rgba(255, 255, 255, 0.02);
                border-radius: 4px;
                margin-bottom: 2px;
            }

            /* Bottom Motto Banner */
            .wco-footer-banner {
                background: linear-gradient(90deg, #09162e 0%, #0d2247 50%, #09162e 100%);
                border: 1px solid #1e3a63;
                border-radius: 8px;
                padding: 6px 14px;
                text-align: center;
                font-size: 10.5px;
                font-weight: 700;
                letter-spacing: 0.8px;
                color: #38bdf8;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
                margin-top: 4px;
            }

            /* Filter dropdown & dates */
            .filter-ctrl {
                background: rgba(15, 23, 42, 0.8) !important;
                border: 1px solid #334155 !important;
                color: #f8fafc !important;
                font-size: 11px !important;
            }

            .filter-ctrl:focus {
                border-color: var(--wco-cyan) !important;
                box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2) !important;
            }

            /* ========================================================= */
            /* LIGHT MODE THEME OVERRIDES (DEFAULT: DARK)                */
            /* ========================================================= */
            body.wco-light-mode,
            body.wco-light-mode .main-content,
            body.wco-light-mode .wco-dashboard-wrapper {
                background-color: #f1f5f9 !important;
                color: #1e293b !important;
            }

            body.wco-light-mode footer.footer {
                background-color: #ffffff !important;
                color: #64748b !important;
                border-top: 1px solid #e2e8f0 !important;
            }

            body.wco-light-mode ::-webkit-scrollbar-track {
                background: #f1f5f9 !important;
            }

            body.wco-light-mode ::-webkit-scrollbar-thumb {
                background: #cbd5e1 !important;
            }

            body.wco-light-mode ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8 !important;
            }

            body.wco-light-mode ::-webkit-scrollbar-corner {
                background: #f1f5f9 !important;
            }

            body.wco-light-mode .wco-header {
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%) !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05) !important;
            }

            body.wco-light-mode .wco-header-title {
                color: #0f172a !important;
                text-shadow: none !important;
            }

            body.wco-light-mode .wco-header-sub {
                color: #0284c7 !important;
            }

            body.wco-light-mode .wco-header-motto {
                color: #64748b !important;
            }

            body.wco-light-mode .wco-header h4 {
                color: #0f172a !important;
            }

            body.wco-light-mode .wco-card {
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
            }

            body.wco-light-mode .wco-card:hover {
                border-color: #38bdf8 !important;
                box-shadow: 0 6px 16px rgba(14, 165, 233, 0.12) !important;
            }

            body.wco-light-mode .wco-card-header {
                background-color: #f8fafc !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }

            body.wco-light-mode .wco-card-title {
                color: #0f172a !important;
            }

            body.wco-light-mode .wco-table th {
                background-color: #f8fafc !important;
                color: #475569 !important;
                border-bottom: 1px solid #cbd5e1 !important;
            }

            body.wco-light-mode .wco-table td {
                border-bottom: 1px solid #f1f5f9 !important;
                color: #334155;
            }

            body.wco-light-mode .wco-table tr:hover td {
                background-color: #f8fafc !important;
            }

            body.wco-light-mode .kpi-top-card {
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
            }

            body.wco-light-mode .kpi-top-card:hover {
                border-color: #38bdf8 !important;
            }

            body.wco-light-mode .kpi-top-val {
                color: #0f172a;
            }

            /* Preserve vibrant status colors for metrics & numbers in light mode */
            body.wco-light-mode .text-success,
            body.wco-light-mode .wco-table td.text-success,
            body.wco-light-mode .wco-table td span.text-success,
            body.wco-light-mode .kpi-top-val.text-success,
            body.wco-light-mode .kpi-top-sub.text-success,
            body.wco-light-mode span.text-success {
                color: #16a34a !important;
            }

            body.wco-light-mode .text-danger,
            body.wco-light-mode .wco-table td.text-danger,
            body.wco-light-mode .wco-table td span.text-danger,
            body.wco-light-mode .kpi-top-val.text-danger,
            body.wco-light-mode .kpi-top-sub.text-danger,
            body.wco-light-mode span.text-danger {
                color: #dc2626 !important;
            }

            body.wco-light-mode .text-warning,
            body.wco-light-mode .wco-table td.text-warning,
            body.wco-light-mode .wco-table td span.text-warning,
            body.wco-light-mode .kpi-top-val.text-warning,
            body.wco-light-mode span.text-warning {
                color: #d97706 !important;
            }

            body.wco-light-mode .text-cyan,
            body.wco-light-mode .wco-table td.text-cyan,
            body.wco-light-mode .wco-table td span.text-cyan,
            body.wco-light-mode span.text-cyan {
                color: #0891b2 !important;
            }

            body.wco-light-mode .text-info,
            body.wco-light-mode .wco-table td.text-info,
            body.wco-light-mode .wco-table td span.text-info,
            body.wco-light-mode span.text-info {
                color: #0284c7 !important;
            }

            body.wco-light-mode .kpi-top-label {
                color: #64748b !important;
            }

            body.wco-light-mode .kpi-score-card {
                background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%) !important;
                border: 1px solid #7dd3fc !important;
            }

            body.wco-light-mode .filter-ctrl {
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
                color: #0f172a !important;
            }

            body.wco-light-mode .filter-ctrl:focus {
                border-color: #0284c7 !important;
                box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2) !important;
            }

            body.wco-light-mode .text-white {
                color: #0f172a !important;
            }

            body.wco-light-mode .text-muted {
                color: #64748b !important;
            }

            body.wco-light-mode .info-tank-item {
                background: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
            }

            body.wco-light-mode .info-tank-name {
                color: #475569 !important;
            }

            body.wco-light-mode .eq-pill-item {
                background: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
            }

            body.wco-light-mode .eq-pill-item span {
                color: #64748b !important;
            }

            body.wco-light-mode .badge.bg-dark {
                background-color: #e2e8f0 !important;
                border-color: #cbd5e1 !important;
                color: #1e293b !important;
            }

            body.wco-light-mode .badge-ok {
                background: rgba(16, 185, 129, 0.12) !important;
                color: #047857 !important;
                border-color: rgba(16, 185, 129, 0.3) !important;
            }

            body.wco-light-mode .badge-danger {
                background: rgba(239, 68, 68, 0.12) !important;
                color: #b91c1c !important;
                border-color: rgba(239, 68, 68, 0.3) !important;
            }

            body.wco-light-mode .badge-warn {
                background: rgba(245, 158, 11, 0.12) !important;
                color: #b45309 !important;
                border-color: rgba(245, 158, 11, 0.3) !important;
            }

            body.wco-light-mode div[style*="rgba(255, 255, 255, 0.03)"],
            body.wco-light-mode div[style*="rgba(255, 255, 255, 0.04)"] {
                background: #f8fafc !important;
                border-color: #e2e8f0 !important;
            }

            body.wco-light-mode div[style*="rgba(255,255,255,0.08)"] {
                background: #cbd5e1 !important;
            }

            body.wco-light-mode div[style*="background: #0f172a"] {
                background: #ffffff !important;
                border-color: #cbd5e1 !important;
            }

            body.wco-light-mode .apexcharts-canvas text {
                fill: #475569 !important;
            }

            body.wco-light-mode .apexcharts-tooltip,
            body.wco-light-mode .apexcharts-tooltip.apexcharts-theme-light,
            body.wco-light-mode .apexcharts-tooltip.apexcharts-theme-dark,
            body.wco-light-mode div.apexcharts-tooltip {
                background: #ffffff !important;
                color: #0f172a !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12) !important;
            }

            body.wco-light-mode .apexcharts-tooltip-title {
                background: #f1f5f9 !important;
                color: #0369a1 !important;
                border-bottom: 1px solid #e2e8f0 !important;
                font-weight: 700 !important;
                padding: 4px 8px !important;
            }

            body.wco-light-mode .apexcharts-tooltip-text,
            body.wco-light-mode .apexcharts-tooltip-text-y-label,
            body.wco-light-mode .apexcharts-tooltip-text-y-value,
            body.wco-light-mode .apexcharts-tooltip-text-goals-value,
            body.wco-light-mode .apexcharts-tooltip-series-group,
            body.wco-light-mode .apexcharts-tooltip span,
            body.wco-light-mode .apexcharts-tooltip-series-group span,
            body.wco-light-mode .apexcharts-tooltip-title span {
                color: #0f172a !important;
            }

            body.wco-light-mode .apexcharts-tooltip-text-y-label {
                color: #475569 !important;
                font-weight: 600 !important;
            }

            body.wco-light-mode .apexcharts-tooltip-text-y-value {
                color: #0f172a !important;
                font-weight: 700 !important;
            }

            body.wco-light-mode .apexcharts-xaxistooltip,
            body.wco-light-mode .apexcharts-yaxistooltip {
                background: #ffffff !important;
                color: #0284c7 !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
            }

            body.wco-light-mode .apexcharts-xaxistooltip:after,
            body.wco-light-mode .apexcharts-xaxistooltip:before {
                border-bottom-color: #ffffff !important;
            }

            /* Theme toggle button styling */
            .btn-theme-toggle {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid #334155;
                color: #f8fafc;
                font-size: 11px;
                padding: 4px 9px;
                border-radius: 6px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-weight: 600;
                transition: all 0.2s ease;
            }

            .btn-theme-toggle:hover {
                background: rgba(255, 255, 255, 0.1);
                border-color: #38bdf8;
                color: #38bdf8;
            }

            body.wco-light-mode .btn-theme-toggle {
                background: #ffffff !important;
                border-color: #cbd5e1 !important;
                color: #334155 !important;
            }

            body.wco-light-mode .btn-theme-toggle:hover {
                background: #f1f5f9 !important;
                border-color: #0284c7 !important;
                color: #0284c7 !important;
            }
        </style>
    </head>

    <body class="wco-body">
        <script>
            (function() {
                try {
                    if (localStorage.getItem('wco_theme') === 'light') {
                        document.body.classList.add('wco-light-mode');
                    }
                } catch (e) {}
            })();
        </script>
        <div class="wco-dashboard-wrapper p-2 p-md-3">
            <div class="container-fluid px-1">

                <!-- ======================================================= -->
                <!-- HEADER SECTION -->
                <!-- ======================================================= -->
                <div class="wco-header">
                    <div class="row align-items-center g-2">
                        <div class="col-xl-3 col-lg-4 col-md-12 d-flex align-items-center gap-2">
                            <div
                                style="width: 36px; height: 36px; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="mdi mdi-water-pump text-cyan"
                                    style="font-size: 22px; color: var(--wco-cyan);"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 text-white fw-bold" style="letter-spacing: 1px; font-size: 15px;">WWTP
                                </h4>
                                <div style="font-size: 9px; color: #38bdf8; font-weight: 600; letter-spacing: 0.5px;">
                                    WASTEWATER
                                    TREATMENT PLANT</div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-12 text-center">
                            <div class="wco-header-title">WCO – SHE DASHBOARD</div>
                            <div class="wco-header-sub">WORLD CLASS OPERATIONAL – PILLAR SHE</div>
                            <div class="wco-header-motto">"Safe People, Compliant Process, Clean Environment"</div>
                        </div>

                        <div
                            class="col-xl-5 col-lg-4 col-md-12 d-flex align-items-center justify-content-end gap-2 flex-wrap">
                            <select id="wcoPeriodSelect" class="form-select form-select-sm filter-ctrl"
                                style="width: 120px;">
                                <option value="this_month" selected>Bulan Ini</option>
                                <option value="last_month">Bulan Lalu</option>
                                <option value="last_7_days">7 Hari</option>
                                <option value="last_30_days">30 Hari</option>
                                <option value="custom">Custom</option>
                            </select>

                            <div id="wcoCustomDates" class="d-none d-flex align-items-center gap-1">
                                <input type="date" id="wcoStartDate" class="form-control form-control-sm filter-ctrl"
                                    style="width: 115px;">
                                <span class="text-muted small">s/d</span>
                                <input type="date" id="wcoEndDate" class="form-control form-control-sm filter-ctrl"
                                    style="width: 115px;">
                            </div>

                            <button id="btnRefreshWco"
                                class="btn btn-sm btn-outline-info d-flex align-items-center gap-1"
                                title="Muat ulang data">
                                <i class="mdi mdi-refresh"></i>
                            </button>

                            <button id="btnThemeToggle" class="btn-theme-toggle" type="button"
                                title="Ganti Mode Tampilan (Dark / Light)">
                                <i class="mdi mdi-weather-sunny text-warning" id="themeIcon"></i>
                                <span id="themeText">Light</span>
                            </button>

                            <button id="btnFullscreen"
                                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                title="Layar Penuh (Fullscreen)" type="button">
                                <i class="mdi mdi-fullscreen" id="fsIcon"></i>
                                <span class="d-none d-md-inline" id="fsText">Full</span>
                            </button>

                            <a href="{{ url('home') }}"
                                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                title="Kembali ke Portal Engineering">
                                <i class="mdi mdi-home-outline"></i>
                                <span class="d-none d-md-inline">Portal</span>
                            </a>

                            <div class="badge bg-dark border border-secondary px-2 py-1 text-muted"
                                style="font-size: 9.5px;">
                                <i class="mdi mdi-clock-outline me-1 text-cyan"></i>
                                <span id="wcoLastUpdate">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- TOP ROW: 5 KPI CARDS + OVERALL SCORE GAUGE (1 TO 5) -->
                <!-- ======================================================= -->
                <div class="row g-2 mb-2">
                    <!-- Card: REMOVAL EFFLUENT (Presentase Gede - Menggantikan Score) -->
                    <div class="col-xl-2 col-lg-4 col-md-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                    <i class="mdi mdi-percent-outline"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">REMOVAL EFFLUENT</div>
                                    <div class="text-muted" style="font-size: 8.5px;">HASIL REMOVAL INFLUENT SAMPAI
                                        EFFLUENT
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-around my-auto py-1">
                                <div class="text-center">
                                    <div class="text-muted"
                                        style="font-size: 8.5px; font-weight: 700; letter-spacing: 0.5px;">
                                        TSS REMOVAL</div>
                                    <div class="fw-bold text-success font-monospace" id="remTssVal"
                                        style="font-size: 18px; line-height: 1.1;">0%</div>
                                    <div class="text-muted" style="font-size: 8px;">Target ≥ 95%</div>
                                </div>
                                <div style="width: 1px; height: 32px; background: rgba(255,255,255,0.1);"></div>
                                <div class="text-center">
                                    <div class="text-muted"
                                        style="font-size: 8.5px; font-weight: 700; letter-spacing: 0.5px;">
                                        COD REMOVAL</div>
                                    <div class="fw-bold text-success font-monospace" id="remCodVal"
                                        style="font-size: 18px; line-height: 1.1;">0%</div>
                                    <div class="text-muted" style="font-size: 8px;">Target ≥ 98%</div>
                                </div>
                            </div>
                            <div class="kpi-top-sub text-success" id="remSubTarget">
                                <i class="mdi mdi-target"></i> TARGET: TSS ≥ 95% | COD ≥ 98%
                            </div>
                        </div>
                    </div>

                    <!-- Card 1: TOTAL COST / M3 (Biaya per Kubik di Biaya Chemical) -->
                    <div class="col-xl-2 col-lg-4 col-md-6">
                        <div class="kpi-top-card" id="cardBiayaPerM3">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon" id="topCostM3Icon"
                                    style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                    <i class="mdi mdi-cash-multiple"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">BIAYA PER KUBIK</div>
                                    <div class="text-muted" style="font-size: 8.5px;" id="topCostM3SubLabel">PERIODE: <span class="text-cyan fw-bold" id="topCostM3Month">-</span></div>
                                </div>
                            </div>
                            <div class="my-auto">
                                <div class="kpi-top-val text-success" id="topCostM3Val">Rp 0</div>
                                <div class="d-flex align-items-center justify-content-between mt-1 px-1" style="font-size: 8px;">
                                    <span class="badge bg-dark border border-secondary text-info px-1 py-0" id="topCostM3ShiftInfo" title="Data shift yang sudah masuk">
                                        <i class="mdi mdi-clock-check-outline me-1"></i><span id="topCostM3ShiftText">-</span>
                                    </span>
                                    <span class="text-muted font-monospace" id="topCostM3Vol" title="Total akumulasi Pit Produksi Step 3">- m³</span>
                                </div>
                            </div>
                            <div class="kpi-top-sub text-success" id="topCostM3Sub">
                                <i class="mdi mdi-arrow-down-bold"></i> TARGET ≤ Rp 20.000 / m³
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: TOTAL COST CHEMICAL (Total Cost Chemical / bulan) -->
                    <div class="col-xl-2 col-lg-4 col-md-6">
                        <div class="kpi-top-card" id="cardTotalCostChem">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon" id="topCostTotalIcon"
                                    style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                    <i class="mdi mdi-flask-round-bottom-outline"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">TOTAL COST CHEMICAL</div>
                                    <div class="text-muted" style="font-size: 8.5px;" id="topCostTotalSubLabel">PERIODE: <span class="text-cyan fw-bold" id="topCostTotalMonth">-</span></div>
                                </div>
                            </div>
                            <div class="my-auto">
                                <div class="kpi-top-val text-success" id="topCostTotalVal">Rp 0</div>
                            </div>
                            <div class="kpi-top-sub text-success" id="topCostTotalSub">
                                <i class="mdi mdi-check-circle-outline"></i> STANDAR ≤ Rp 45.000.000
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: PENGANGKUTAN SLUDGE (Total Tonase Pengangkutan Sludge & Cost) -->
                    <div class="col-xl-2 col-lg-4 col-md-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                                    <i class="mdi mdi-truck-delivery-outline"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">PENGANGKUTAN SLUDGE</div>
                                    <div class="text-muted" style="font-size: 8.5px;">TONASE & TOTAL BIAYA</div>
                                </div>
                            </div>
                            <div class="my-auto">
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <span class="kpi-top-val" id="topPengangkutanSludgeVal">0 Ton</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1 pt-1"
                                    style="border-top: 1px dashed rgba(255,255,255,0.1);">
                                    <span class="text-muted" style="font-size: 8.5px; font-weight: 600;">TOTAL
                                        BIAYA:</span>
                                    <span class="text-warning fw-bold font-monospace" id="topPengangkutanSludgeCost"
                                        style="font-size: 11.5px;">Rp 0</span>
                                </div>
                            </div>
                            <div class="kpi-top-sub text-warning" id="topPengangkutanSludgeSub"
                                title="Rumus: Ton × 1000 × Rp 1.400 = Total Biaya">
                                <i class="mdi mdi-calculator"></i> TARIF: Rp 1.400 / KG
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: EQUALISASI (INFLUENT) (pH, TSS, COD, EC) -->
                    <div class="col-xl-2 col-lg-4 col-md-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                                    <i class="mdi mdi-tune-vertical"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">EQUALISASI (INFLUENT)</div>
                                    <div class="text-muted" style="font-size: 8.5px;">PARAMETER AIR MASUK</div>
                                </div>
                            </div>
                            <div class="eq-pill-grid">
                                <div class="eq-pill-item"><span>pH:</span> <strong id="eqValPh">-</strong></div>
                                <div class="eq-pill-item"><span>TSS:</span> <strong id="eqValTss">-</strong></div>
                                <div class="eq-pill-item"><span>COD:</span> <strong id="eqValCod">-</strong></div>
                                <div class="eq-pill-item"><span>EC:</span> <strong id="eqValEc">-</strong></div>
                            </div>
                            <div class="kpi-top-sub text-info mt-1">
                                <i class="mdi mdi-information-outline"></i> Rata-rata Periode
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: ANALISA AIR LIMBAH EFFLUENT (COD, TSS, pH, EC) -->
                    <div class="col-xl-2 col-lg-4 col-md-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                                    <i class="mdi mdi-water-check-outline"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">ANALISA EFFLUENT</div>
                                    <div class="text-muted" style="font-size: 8.5px;">PARAMETER AIR KELUAR</div>
                                </div>
                            </div>
                            <div class="eq-pill-grid">
                                <div class="eq-pill-item"><span>pH:</span> <strong id="effValPh">-</strong></div>
                                <div class="eq-pill-item"><span>TSS:</span> <strong id="effValTss">-</strong></div>
                                <div class="eq-pill-item"><span>COD:</span> <strong id="effValCod">-</strong></div>
                                <div class="eq-pill-item"><span>EC:</span> <strong id="effValEc">-</strong></div>
                            </div>
                            <div class="kpi-top-sub text-info mt-1">
                                <i class="mdi mdi-check-circle-outline"></i> Baku Mutu: COD ≤ 300
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- ROW 1: 4 CARDS (INFORMASI WWTP, STATUS OPERASI, ENVIRONMENT PERF, CHEMICAL CONSUMPTION) -->
                <!-- ======================================================= -->
                <div class="row g-2 mb-2">
                    <!-- 6. INFORMASI WWTP (Kapasitas Tangki Permanent) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-information-outline text-info"></i> INFORMASI WWTP
                                </h5>
                                <span class="badge bg-primary-subtle text-primary" style="font-size: 8.5px;">KAPASITAS
                                    TANGKI</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2 p-1 px-2"
                                    style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 6px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div
                                            style="width: 24px; height: 24px; background: rgba(6, 182, 212, 0.12); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <i class="mdi mdi-water-pump text-cyan" style="font-size: 14px;"></i>
                                        </div>
                                        <div>
                                            <div
                                                style="font-size: 8.5px; color: #94a3b8; font-weight: 700; letter-spacing: 0.3px;">
                                                DEBIT WWTP 1</div>
                                            <div class="fw-bold font-monospace text-cyan" id="infoDebit1Val"
                                                style="font-size: 11px;">- m³/h</div>
                                        </div>
                                    </div>
                                    <div style="width: 1px; height: 22px; background: rgba(255,255,255,0.08);"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div
                                            style="width: 24px; height: 24px; background: rgba(59, 130, 246, 0.12); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <i class="mdi mdi-water-pump text-primary" style="font-size: 14px;"></i>
                                        </div>
                                        <div>
                                            <div
                                                style="font-size: 8.5px; color: #94a3b8; font-weight: 700; letter-spacing: 0.3px;">
                                                DEBIT WWTP 2</div>
                                            <div class="fw-bold font-monospace text-primary" id="infoDebit2Val"
                                                style="font-size: 11px;">- m³/h</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-1">
                                    <div class="col-6">
                                        <div class="info-tank-item">
                                            <img src="{{ asset('assets/images/wwtp/dashboard/EQUALISASI.png') }}"
                                                alt="Equal">
                                            <div class="info-tank-meta">
                                                <div class="info-tank-name">Equal</div>
                                                <div class="info-tank-cap">20 m³</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-tank-item">
                                            <img src="{{ asset('assets/images/wwtp/dashboard/ANAEROB.png') }}"
                                                alt="Anaerob">
                                            <div class="info-tank-meta">
                                                <div class="info-tank-name">Anaerob</div>
                                                <div class="info-tank-cap">426 m³</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-tank-item">
                                            <img src="{{ asset('assets/images/wwtp/dashboard/AEROB.png') }}"
                                                alt="Aerob">
                                            <div class="info-tank-meta">
                                                <div class="info-tank-name">Aerob</div>
                                                <div class="info-tank-cap">170 m³</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-tank-item">
                                            <img src="{{ asset('assets/images/wwtp/dashboard/LUMPUR AKTIF.png') }}"
                                                alt="Lumpur Aktif">
                                            <div class="info-tank-meta">
                                                <div class="info-tank-name">Lumpur Aktif</div>
                                                <div class="info-tank-cap">160 m³</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-tank-item">
                                            <img src="{{ asset('assets/images/wwtp/dashboard/DAF.png') }}"
                                                alt="DAF">
                                            <div class="info-tank-meta">
                                                <div class="info-tank-name">DAF</div>
                                                <div class="info-tank-cap">10 m³</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-tank-item">
                                            <img src="{{ asset('assets/images/wwtp/dashboard/OUTLET.png') }}"
                                                alt="Outlet">
                                            <div class="info-tank-meta">
                                                <div class="info-tank-name">Outlet</div>
                                                <div class="info-tank-cap">1 m³</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 10. FLOWMETER AIR LIMBAH (User #10: Pit Sparta, Pit Garam, Pit Domestic, Pit Produksi Step, Pit Outlet) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-speedometer text-success"></i> FLOWMETER AIR LIMBAH
                                </h5>
                                <span class="badge bg-success-subtle text-success" style="font-size: 8.5px;">5 PITS
                                    HARIAN</span>
                            </div>
                            <div class="wco-card-body p-2" id="wcoStatusOperasiContainer"
                                style="max-height: 185px; overflow-y: auto;">
                                <div class="text-center p-2 text-muted" style="font-size: 10px;">Memuat flowmeter...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 8. PERFORMANCE WWTP (User #8: Influent, Outlet Anaerob, Aerob, DAF, Effluent) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-leaf text-success"></i> PERFORMANCE WWTP
                                </h5>
                                <span class="badge bg-success-subtle text-success" style="font-size: 8.5px;">5
                                    TAHAPAN</span>
                            </div>
                            <div class="wco-card-body p-0">
                                <div class="table-responsive">
                                    <table class="wco-table" style="font-size: 9.5px;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 4px 4px;">Tahapan</th>
                                                <th class="text-center" style="padding: 4px 3px;">pH</th>
                                                <th class="text-center" style="padding: 4px 3px;">COD</th>
                                                <th class="text-center" style="padding: 4px 3px;">TSS</th>
                                                <th class="text-center" style="padding: 4px 3px;"
                                                    title="Removal COD (%)">
                                                    Rem. COD</th>
                                                <th class="text-center" style="padding: 4px 3px;"
                                                    title="Removal TSS (%)">
                                                    Rem. TSS</th>
                                                <th class="text-center" style="padding: 4px 4px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableEnvPerfBody">
                                            <!-- Filled by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 9. CHEMICAL CONSUMPTION (User #9: Pemakaian Chemical WWTP) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-flask text-warning"></i> CHEMICAL USAGE
                                </h5>
                                <span class="badge bg-warning-subtle text-warning" style="font-size: 8.5px;" id="badgeChemConsPeriod">THIS
                                    MONTH</span>
                            </div>
                            <div class="wco-card-body p-0">
                                <div class="table-responsive" style="max-height: 185px; overflow-y: auto;">
                                    <table class="wco-table" style="font-size: 9.5px;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 4px 4px;">Chemical</th>
                                                <th class="text-end" style="padding: 4px 4px;" title="Rata-rata Pemakaian Harian (kg/hari)">Qty</th>
                                                <th class="text-end" style="padding: 4px 4px;" title="Standar Kuota Harian (kg/hari)">Std</th>
                                                <th class="text-end" style="padding: 4px 4px;">Cost/m³</th>
                                                <th class="text-center" style="padding: 4px 4px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableChemConsBody">
                                            <!-- Filled by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- ROW 2: 4 TREND CARDS (SAFETY PERF, INFLUENT MINGGUAN, EFFLUENT MINGGUAN, KEPATUHAN COD) -->
                <!-- ======================================================= -->
                <div class="row g-2 mb-2">
                    <!-- 7. INFLUENT SPARTA & PIT GARAM (Data WWTP Proses Harian: Sparta & Pit Garam) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-water-sync text-cyan"></i> INFLUENT SPARTA & GARAM
                                </h5>
                                <span class="badge bg-info-subtle text-info" style="font-size: 8.5px;">PROSES
                                    HARIAN</span>
                            </div>
                            <div class="wco-card-body p-2">
                                <div class="wco-mini-stat d-flex justify-content-between align-items-center">
                                    <span style="font-size: 8.5px; color: #94a3b8;">Std: ≤ 50 m³/day</span>
                                    <div class="d-flex align-items-center gap-1" id="safetyDailyTotal">
                                        <span class="badge-ok" id="statSpartaBadge">Sparta: -</span>
                                        <span class="badge-ok" id="statGaramBadge">Garam: -</span>
                                    </div>
                                </div>
                                <div class="wco-chart-box" id="chartSafetyPerf"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 14. INFLUENT MINGGUAN (User #14: Proses WWTP Influent Mingguan) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-chart-areaspline text-success"></i> INFLUENT MINGGUAN
                                </h5>
                                <span class="badge bg-success-subtle text-success" style="font-size: 8.5px;">PIT
                                    BREAKDOWN</span>
                            </div>
                            <div class="wco-card-body p-2">
                                <div class="wco-mini-stat">
                                    <span>Sparta, Garam, Step 3, Storage</span>
                                    <span class="badge-ok">WEEKLY</span>
                                </div>
                                <div class="wco-chart-box" id="chartInfluentMingguan"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 11. TREND EFFLUENT MINGGUAN (User #11: Full Proses vs DAF Pre) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-chart-bar text-primary"></i> EFFLUENT MINGGUAN
                                </h5>
                                <span class="badge bg-primary-subtle text-primary" style="font-size: 8.5px;">PROSES
                                    WWTP</span>
                            </div>
                            <div class="wco-card-body p-2">
                                <div class="wco-mini-stat">
                                    <span>Full Proses vs DAF Pre (m³)</span>
                                    <span class="badge bg-primary-subtle text-primary" style="font-size: 8.5px;">VOL
                                        EFFLUENT</span>
                                </div>
                                <div class="wco-chart-box" id="chartEffluentMingguan"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 12. TREND KEPATUHAN EFFLUENT (COD) (Baku Mutu 300 PPM) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-check-decagram text-warning"></i> KEPATUHAN COD
                                </h5>
                                <span class="badge bg-warning-subtle text-warning" style="font-size: 8.5px;">≤ 300
                                    PPM</span>
                            </div>
                            <div class="wco-card-body p-2">
                                <div class="wco-mini-stat">
                                    <span>COD vs Baku Mutu (300 ppm):</span>
                                    <strong class="text-success" id="codComplianceBadge">100% OK</strong>
                                </div>
                                <div class="wco-chart-box" id="chartKepatuhanCod"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- ROW 3: 4 CARDS (PERFORMANCE SAMPEL, SLUDGE, TOP 5 RISK, KEPATUHAN TSS) -->
                <!-- ======================================================= -->
                <div class="row g-2 mb-2">
                    <!-- 13. PERFORMANCE AERASI DAN LUMPUR AKTIF (User #13: Aerasi 1-6 & Lumpur Aktif) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-bacteria text-cyan"></i> PERFORMANCE AERASI DAN LUMPUR AKTIF
                                </h5>
                                <span class="badge bg-info-subtle text-info" style="font-size: 8.5px;">AERASI 1-6 &
                                    LA</span>
                            </div>
                            <div class="wco-card-body p-0">
                                <div class="table-responsive">
                                    <table class="wco-table">
                                        <thead>
                                            <tr>
                                                <th>Sampel</th>
                                                <th class="text-center" title="Standar: 300 - 500 ml">SV30</th>
                                                <th class="text-center" title="Standar: 1000 - 5000 ppm">MLSS</th>
                                                <th class="text-center" title="Standar: 90 - 150">SVI</th>
                                                <th class="text-center" title="Standar: 2 - 4 ppm">DO</th>
                                                <th class="text-center" title="Standar: 7 - 9">pH</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableAerasiBody">
                                            <!-- Filled by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 15. SLUDGE MANAGEMENT (Parameter Harian: Drain lumpur, Running Hour scp, hasil lumpur, content sludge) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-recycle text-purple"></i> SLUDGE MANAGEMENT
                                </h5>
                                <span class="badge bg-purple-subtle text-purple" style="font-size: 8.5px;">PARAMETER
                                    HARIAN</span>
                            </div>
                            <div class="wco-card-body p-1">
                                <table class="wco-table" style="font-size: 9.5px;">
                                    <thead>
                                        <tr>
                                            <th style="padding: 4px 4px;">Parameter</th>
                                            <th class="text-end" style="padding: 4px 4px;">Hasil</th>
                                            <th class="text-end" style="padding: 4px 4px;">Std</th>
                                            <th class="text-center" style="padding: 4px 4px;">Sat</th>
                                            <th class="text-center" style="padding: 4px 4px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-white" style="padding: 4px 4px;">Drain Lumpur</td>
                                            <td class="text-end font-monospace" style="padding: 4px 4px;"><span
                                                    id="sludgeDrainVal">0</span></td>
                                            <td class="text-end font-monospace text-white" style="padding: 4px 4px;">≤
                                                19</td>
                                            <td class="text-center text-white" style="padding: 4px 4px;">m³</td>
                                            <td class="text-center" style="padding: 4px 4px;"><span
                                                    id="sludgeDrainStatus" class="badge-ok">OK</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-white" style="padding: 4px 4px;">Running Hour SCP</td>
                                            <td class="text-end font-monospace" style="padding: 4px 4px;"><span
                                                    id="sludgeRunningVal">0</span></td>
                                            <td class="text-end font-monospace text-white" style="padding: 4px 4px;">≤
                                                6</td>
                                            <td class="text-center text-white" style="padding: 4px 4px;">Jam</td>
                                            <td class="text-center" style="padding: 4px 4px;"><span
                                                    id="sludgeRunningStatus" class="badge-ok">OK</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-white" style="padding: 4px 4px;">Hasil Lumpur</td>
                                            <td class="text-end font-monospace" style="padding: 4px 4px;"><span
                                                    id="sludgeHasilVal">0</span></td>
                                            <td class="text-end font-monospace text-white" style="padding: 4px 4px;">≤
                                                1.2
                                            </td>
                                            <td class="text-center text-white" style="padding: 4px 4px;"
                                                id="sludgeHasilSat">
                                                Ton</td>
                                            <td class="text-center" style="padding: 4px 4px;"><span
                                                    id="sludgeHasilStatus" class="badge-ok">OK</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-white" style="padding: 4px 4px;">Content Sludge</td>
                                            <td class="text-end font-monospace" style="padding: 4px 4px;"><span
                                                    id="sludgeContentVal">0</span></td>
                                            <td class="text-end font-monospace text-white" style="padding: 4px 4px;">≥
                                                20</td>
                                            <td class="text-center text-white" style="padding: 4px 4px;">%</td>
                                            <td class="text-center" style="padding: 4px 4px;"><span
                                                    id="sludgeContentStatus" class="badge-ok">OK</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 16. TOTAL KOLONI DALAM PENGOLAHAN BIOLOGI (Grafik Standar 10^5) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-bacteria text-success"></i> TOTAL KOLONI DALAM PENGOLAHAN BIOLOGI
                                </h5>
                                <span class="badge bg-success-subtle text-success" style="font-size: 8.5px;">STANDAR:
                                    10⁵
                                    CFU</span>
                            </div>
                            <div class="wco-card-body p-2">
                                <div class="wco-mini-stat">
                                    <span>Batas Standar: 1.0 × 10⁵ CFU/mL</span>
                                    <span class="badge bg-success-subtle text-success" style="font-size: 8px;">≥
                                        10⁵</span>
                                </div>
                                <div class="wco-chart-box" id="chartTopRiskKoloni"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 17. KEPATUHAN EFFLUENT (TSS) / SHE COMPLIANCE (User #17: Kepatuhan TSS) -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="wco-card">
                            <div class="wco-card-header">
                                <h5 class="wco-card-title">
                                    <i class="mdi mdi-water-check text-cyan"></i> KEPATUHAN TSS
                                </h5>
                                <span class="badge bg-info-subtle text-info" style="font-size: 8.5px;">≤ 100
                                    PPM</span>
                            </div>
                            <div class="wco-card-body p-2">
                                <div class="wco-mini-stat">
                                    <span>TSS vs Baku Mutu (100 ppm):</span>
                                    <strong class="text-success" id="tssComplianceBadge">96% OK</strong>
                                </div>
                                <div class="wco-chart-box" id="chartKepatuhanTss"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- BOTTOM BANNER -->
                <!-- ======================================================= -->
                <div class="wco-footer-banner">
                    "DISIPLIN SHE ADALAH BUDAYA KITA, WWTP AMAN – LINGKUNGAN TERJAGA – PRODUKSI BERKELANJUTAN"
                </div>

            </div>
        </div>

        <!-- JS Dependencies -->
        <script src="{{ asset('material/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('material/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
        <script>
            if (typeof ApexCharts === 'undefined') {
                document.write('<script src="https://cdn.jsdelivr.net/npm/apexcharts"><\/script>');
            }
        </script>

        <script>
            // Fullscreen toggle logic
            document.addEventListener('DOMContentLoaded', function() {
                const fsBtn = document.getElementById('btnFullscreen');
                if (fsBtn) {
                    fsBtn.addEventListener('click', function() {
                        if (!document.fullscreenElement) {
                            document.documentElement.requestFullscreen().catch(err => {
                                console.log(`Fullscreen error: ${err.message}`);
                            });
                            const icon = document.getElementById('fsIcon');
                            const text = document.getElementById('fsText');
                            if (icon) icon.className = 'mdi mdi-fullscreen-exit';
                            if (text) text.textContent = 'Exit';
                        } else {
                            if (document.exitFullscreen) {
                                document.exitFullscreen();
                                const icon = document.getElementById('fsIcon');
                                const text = document.getElementById('fsText');
                                if (icon) icon.className = 'mdi mdi-fullscreen';
                                if (text) text.textContent = 'Full';
                            }
                        }
                    });
                }
            });

            let wcoCharts = {};

            document.addEventListener('DOMContentLoaded', function() {
                initDefaultDates();
                loadWcoData();

                // Theme toggle initialization (Default: dark)
                const savedTheme = localStorage.getItem('wco_theme') || 'dark';
                applyTheme(savedTheme, false);

                const themeBtn = document.getElementById('btnThemeToggle');
                if (themeBtn) {
                    themeBtn.addEventListener('click', function() {
                        const isCurrentLight = document.body.classList.contains('wco-light-mode');
                        applyTheme(isCurrentLight ? 'dark' : 'light', true);
                    });
                }

                // Event Listeners
                document.getElementById('btnRefreshWco').addEventListener('click', function() {
                    loadWcoData();
                });

                document.getElementById('wcoPeriodSelect').addEventListener('change', function() {
                    const val = this.value;
                    const customDatesDiv = document.getElementById('wcoCustomDates');
                    if (val === 'custom') {
                        customDatesDiv.classList.remove('d-none');
                    } else {
                        customDatesDiv.classList.add('d-none');
                        setDatesByPeriod(val);
                        loadWcoData();
                    }
                });

                document.getElementById('wcoStartDate').addEventListener('change', function() {
                    if (document.getElementById('wcoPeriodSelect').value === 'custom') loadWcoData();
                });
                document.getElementById('wcoEndDate').addEventListener('change', function() {
                    if (document.getElementById('wcoPeriodSelect').value === 'custom') loadWcoData();
                });
            });

            function applyTheme(theme, updateCharts = true) {
                const isLight = (theme === 'light');
                const icon = document.getElementById('themeIcon');
                const text = document.getElementById('themeText');
                const btn = document.getElementById('btnThemeToggle');

                if (isLight) {
                    document.body.classList.add('wco-light-mode');
                    if (icon) icon.className = 'mdi mdi-weather-night text-primary';
                    if (text) text.textContent = 'Dark';
                    if (btn) btn.title = 'Beralih ke Dark Mode';
                } else {
                    document.body.classList.remove('wco-light-mode');
                    if (icon) icon.className = 'mdi mdi-weather-sunny text-warning';
                    if (text) text.textContent = 'Light';
                    if (btn) btn.title = 'Beralih ke Light Mode';
                }

                localStorage.setItem('wco_theme', theme);

                if (updateCharts && typeof wcoCharts !== 'undefined') {
                    const chartThemeMode = isLight ? 'light' : 'dark';
                    const gridColor = isLight ? '#e2e8f0' : '#1e293b';
                    const labelColor = isLight ? '#64748b' : '#94a3b8';

                    Object.keys(wcoCharts).forEach(domId => {
                        try {
                            if (wcoCharts[domId]) {
                                wcoCharts[domId].updateOptions({
                                    theme: {
                                        mode: chartThemeMode
                                    },
                                    tooltip: {
                                        theme: chartThemeMode
                                    },
                                    grid: {
                                        borderColor: gridColor
                                    },
                                    xaxis: {
                                        labels: {
                                            style: {
                                                colors: labelColor
                                            }
                                        }
                                    },
                                    yaxis: {
                                        labels: {
                                            style: {
                                                colors: labelColor
                                            }
                                        }
                                    }
                                });
                            }
                        } catch (e) {
                            // ignore if chart not ready
                        }
                    });
                }
            }

            function initDefaultDates() {
                const today = new Date();
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

                const fmt = d =>
                    `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                document.getElementById('wcoStartDate').value = fmt(firstDay);
                document.getElementById('wcoEndDate').value = fmt(lastDay);
            }

            function setDatesByPeriod(period) {
                const today = new Date();
                const fmt = d =>
                    `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

                if (period === 'this_month') {
                    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    document.getElementById('wcoStartDate').value = fmt(firstDay);
                    document.getElementById('wcoEndDate').value = fmt(lastDay);
                } else if (period === 'last_month') {
                    const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
                    document.getElementById('wcoStartDate').value = fmt(firstDay);
                    document.getElementById('wcoEndDate').value = fmt(lastDay);
                } else if (period === 'last_7_days') {
                    const past = new Date();
                    past.setDate(today.getDate() - 7);
                    document.getElementById('wcoStartDate').value = fmt(past);
                    document.getElementById('wcoEndDate').value = fmt(today);
                } else if (period === 'last_30_days') {
                    const past = new Date();
                    past.setDate(today.getDate() - 30);
                    document.getElementById('wcoStartDate').value = fmt(past);
                    document.getElementById('wcoEndDate').value = fmt(today);
                }
            }

            function renderOrUpdateChart(domId, options) {
                const el = document.getElementById(domId);
                if (!el || typeof ApexCharts === 'undefined') return;

                const isLight = document.body.classList.contains('wco-light-mode');
                if (isLight) {
                    if (!options.theme) options.theme = {};
                    options.theme.mode = 'light';
                    if (!options.tooltip) options.tooltip = {};
                    options.tooltip.theme = 'light';
                    if (!options.grid) options.grid = {};
                    options.grid.borderColor = '#e2e8f0';
                    if (options.xaxis && options.xaxis.labels && options.xaxis.labels.style) {
                        options.xaxis.labels.style.colors = '#64748b';
                    }
                    if (options.yaxis && options.yaxis.labels && options.yaxis.labels.style) {
                        options.yaxis.labels.style.colors = '#64748b';
                    }
                    if (options.legend && options.legend.labels) {
                        options.legend.labels.colors = '#475569';
                    }
                } else {
                    if (!options.theme) options.theme = {};
                    options.theme.mode = 'dark';
                    if (!options.tooltip) options.tooltip = {};
                    options.tooltip.theme = 'dark';
                }

                if (wcoCharts[domId]) {
                    wcoCharts[domId].updateOptions(options);
                } else {
                    wcoCharts[domId] = new ApexCharts(el, options);
                    wcoCharts[domId].render();
                }
            }

            // Generic section fetcher
            async function fetchWcoSection(section, start, end) {
                try {
                    const url =
                        `{{ route('wwtp.dashboard_wco_data') }}?section=${section}&start_date=${start}&end_date=${end}`;
                    const res = await fetch(url);
                    if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                    const data = await res.json();
                    if (data.last_update) {
                        document.getElementById('wcoLastUpdate').textContent = data.last_update;
                    }
                    return data;
                } catch (err) {
                    console.error(`Error loading section ${section}:`, err);
                    return null;
                }
            }

            // 1 & 2. Biaya Chemical (Top Cards & Chemical Consumption Table)
            async function loadBiayaSection(start, end) {
                const data = await fetchWcoSection('biaya', start, end);
                if (!data) return;

                const monthLabel = data.biaya_bulan || (data.card2_total_cost_chem ? data.card2_total_cost_chem.bulan : '');
                const hasData = (data.has_biaya_data !== false);

                const m3MonthEl = document.getElementById('topCostM3Month');
                if (m3MonthEl) {
                    m3MonthEl.textContent = monthLabel || '-';
                    m3MonthEl.className = hasData ? 'text-cyan fw-bold' : 'text-warning fw-semibold';
                }
                const totalMonthEl = document.getElementById('topCostTotalMonth');
                if (totalMonthEl) {
                    totalMonthEl.textContent = monthLabel || '-';
                    totalMonthEl.className = hasData ? 'text-cyan fw-bold' : 'text-warning fw-semibold';
                }
                const badgeConsEl = document.getElementById('badgeChemConsPeriod');
                if (badgeConsEl) {
                    badgeConsEl.textContent = monthLabel || 'THIS MONTH';
                    badgeConsEl.className = hasData ? 'badge bg-warning-subtle text-warning' : 'badge bg-secondary-subtle text-muted';
                }

                if (data.card1_biaya_per_m3) {
                    const c1 = data.card1_biaya_per_m3;
                    const valEl = document.getElementById('topCostM3Val');
                    const subEl = document.getElementById('topCostM3Sub');
                    const iconEl = document.getElementById('topCostM3Icon');
                    const cardEl = document.getElementById('cardBiayaPerM3');

                    if (valEl) {
                        valEl.innerHTML = `${c1.formatted}`;
                    }

                    const shiftTextEl = document.getElementById('topCostM3ShiftText');
                    const volEl = document.getElementById('topCostM3Vol');
                    const shiftBadgeEl = document.getElementById('topCostM3ShiftInfo');

                    if (shiftTextEl) {
                        shiftTextEl.textContent = c1.shifts_text || 'Shift: -';
                    }
                    if (volEl) {
                        volEl.textContent = c1.pit_step3_formatted || (c1.total_pit_step3 ? `${c1.total_pit_step3} m³` : '- m³');
                    }
                    if (shiftBadgeEl) {
                        if (!hasData || !c1.shifts_text || c1.shifts_text === 'Belum Ada Shift') {
                            shiftBadgeEl.className = 'badge bg-dark border border-secondary text-muted px-1 py-0';
                        } else {
                            shiftBadgeEl.className = 'badge bg-info-subtle border border-info-subtle text-info px-1 py-0';
                        }
                    }

                    if (!hasData) {
                        if (valEl) valEl.className = 'kpi-top-val text-muted';
                        if (subEl) {
                            subEl.className = 'kpi-top-sub text-muted';
                            subEl.innerHTML = `<i class="mdi mdi-alert-circle-outline"></i> Belum ada data input`;
                        }
                        if (iconEl) {
                            iconEl.style.background = 'rgba(148, 163, 184, 0.15)';
                            iconEl.style.color = '#94a3b8';
                        }
                        if (cardEl) {
                            cardEl.classList.remove('border-danger-subtle', 'border-success-subtle');
                        }
                    } else {
                        const isOverM3 = c1.is_over !== undefined ?
                            c1.is_over :
                            (Number(c1.value) > 20000);

                        if (isOverM3) {
                            if (valEl) valEl.className = 'kpi-top-val text-danger';
                            if (subEl) {
                                subEl.className = 'kpi-top-sub text-danger';
                                subEl.innerHTML =
                                    `<i class="mdi mdi-arrow-up-bold"></i> ${c1.target || 'TARGET ≤ Rp 20.000 / m³'}`;
                            }
                            if (iconEl) {
                                iconEl.style.background = 'rgba(239, 68, 68, 0.15)';
                                iconEl.style.color = '#ef4444';
                            }
                            if (cardEl) {
                                cardEl.classList.remove('border-success-subtle');
                                cardEl.classList.add('border-danger-subtle');
                            }
                        } else {
                            if (valEl) valEl.className = 'kpi-top-val text-success';
                            if (subEl) {
                                subEl.className = 'kpi-top-sub text-success';
                                subEl.innerHTML =
                                    `<i class="mdi mdi-arrow-down-bold"></i> ${c1.target || 'TARGET ≤ Rp 20.000 / m³'}`;
                            }
                            if (iconEl) {
                                iconEl.style.background = 'rgba(16, 185, 129, 0.15)';
                                iconEl.style.color = '#10b981';
                            }
                            if (cardEl) {
                                cardEl.classList.remove('border-danger-subtle');
                                cardEl.classList.add('border-success-subtle');
                            }
                        }
                    }
                }

                if (data.card2_total_cost_chem) {
                    const totalChem = data.card2_total_cost_chem;
                    const valEl = document.getElementById('topCostTotalVal');
                    const subEl = document.getElementById('topCostTotalSub');
                    const iconEl = document.getElementById('topCostTotalIcon');
                    const cardEl = document.getElementById('cardTotalCostChem');

                    if (valEl) {
                        valEl.textContent = totalChem.formatted;
                    }

                    if (!hasData) {
                        if (valEl) valEl.className = 'kpi-top-val text-muted';
                        if (subEl) {
                            subEl.className = 'kpi-top-sub text-muted';
                            subEl.innerHTML = `<i class="mdi mdi-alert-circle-outline"></i> Belum ada data input`;
                        }
                        if (iconEl) {
                            iconEl.style.background = 'rgba(148, 163, 184, 0.15)';
                            iconEl.style.color = '#94a3b8';
                        }
                        if (cardEl) {
                            cardEl.classList.remove('border-danger-subtle', 'border-success-subtle');
                        }
                    } else {
                        const isOverTotal = totalChem.is_over !== undefined ?
                            totalChem.is_over :
                            (Number(totalChem.value) > 45000000);

                        if (isOverTotal) {
                            if (valEl) valEl.className = 'kpi-top-val text-danger';
                            if (subEl) {
                                subEl.className = 'kpi-top-sub text-danger';
                                subEl.innerHTML = `<i class="mdi mdi-arrow-up-bold"></i> STANDAR ≤ Rp 45 Jt`;
                            }
                            if (iconEl) {
                                iconEl.style.background = 'rgba(239, 68, 68, 0.15)';
                                iconEl.style.color = '#ef4444';
                            }
                            if (cardEl) {
                                cardEl.classList.remove('border-success-subtle');
                                cardEl.classList.add('border-danger-subtle');
                            }
                        } else {
                            if (valEl) valEl.className = 'kpi-top-val text-success';
                            if (subEl) {
                                subEl.className = 'kpi-top-sub text-success';
                                subEl.innerHTML = `<i class="mdi mdi-check-circle-outline"></i> STANDAR ≤ Rp 45 Jt`;
                            }
                            if (iconEl) {
                                iconEl.style.background = 'rgba(16, 185, 129, 0.15)';
                                iconEl.style.color = '#10b981';
                            }
                            if (cardEl) {
                                cardEl.classList.remove('border-danger-subtle');
                                cardEl.classList.add('border-success-subtle');
                            }
                        }
                    }
                }

                if (data.card9_chem_consumption && Array.isArray(data.card9_chem_consumption)) {
                    let tbodyHtml = '';
                    if (!hasData) {
                        tbodyHtml = `<tr><td colspan="5" class="text-center text-muted py-3" style="font-size: 9px;"><i class="mdi mdi-information-outline me-1"></i>Belum ada data biaya chemical untuk periode ${monthLabel}</td></tr>`;
                    } else {
                        data.card9_chem_consumption.forEach(item => {
                            const isOver = item.is_over !== undefined ?
                                item.is_over :
                                (item.standard !== undefined ? (Number(item.qty) > Number(item.standard)) : false);
                            const qtyColor = isOver ? 'text-danger fw-bold' : 'text-success fw-bold';
                            const badgeClass = isOver ? 'badge-danger' : 'badge-ok';
                            const statusText = item.status || (isOver ? 'OVER' : 'AMAN');
                            const stdText = (item.standard !== undefined && item.standard !== null && Number(item.standard) > 0) ?
                                `${Number(item.standard).toLocaleString('id-ID')} kg` :
                                (item.standard_text || '-');
                            const costM3Text = (item.cost_m3 !== undefined && item.cost_m3 !== null) ?
                                `Rp ${Number(item.cost_m3).toLocaleString('id-ID')}` :
                                '-';

                            tbodyHtml += `
                        <tr>
                            <td class="fw-semibold text-white" style="font-size: 9px; padding: 3px 4px; white-space: nowrap;">${item.chemical_name}</td>
                            <td class="text-end font-monospace ${qtyColor}" style="font-size: 9px; padding: 3px 4px;">${Number(item.qty).toLocaleString('id-ID')} kg</td>
                            <td class="text-end font-monospace text-muted" style="font-size: 9px; padding: 3px 4px;">${stdText}</td>
                            <td class="text-end font-monospace text-muted" style="font-size: 8.5px; padding: 3px 4px;">${costM3Text}</td>
                            <td class="text-center" style="padding: 3px 4px;"><span class="${badgeClass}">${statusText}</span></td>
                        </tr>
                    `;
                        });
                    }
                    document.getElementById('tableChemConsBody').innerHTML = tbodyHtml;
                }
            }

            // 4, 5, 8. Analisa Parameter & Removal Outlet
            async function loadAnalisaSection(start, end) {
                const data = await fetchWcoSection('analisa', start, end);
                if (!data) return;

                if (data.card5_removal_outlet) {
                    const rem = data.card5_removal_outlet;
                    const tssVal = parseFloat(rem.tss_pct) || 0;
                    const codVal = parseFloat(rem.cod_pct) || 0;

                    const tssEl = document.getElementById('remTssVal');
                    if (tssEl) {
                        tssEl.textContent = `${rem.tss_pct}%`;
                        tssEl.className = `fw-bold font-monospace ${tssVal >= 95 ? 'text-success' : 'text-danger'}`;
                    }

                    const codEl = document.getElementById('remCodVal');
                    if (codEl) {
                        codEl.textContent = `${rem.cod_pct}%`;
                        codEl.className = `fw-bold font-monospace ${codVal >= 98 ? 'text-success' : 'text-danger'}`;
                    }

                    if (document.getElementById('remSubTarget')) {
                        const allOk = tssVal >= 95 && codVal >= 98;
                        document.getElementById('remSubTarget').className =
                            `kpi-top-sub ${allOk ? 'text-success' : 'text-danger'}`;
                        document.getElementById('remSubTarget').innerHTML =
                            `<i class="mdi mdi-target"></i> ${rem.target || 'TARGET: TSS ≥ 95% | COD ≥ 98%'}`;
                    }
                }

                if (data.card4_equalisasi) {
                    document.getElementById('eqValPh').textContent = data.card4_equalisasi.ph || '-';
                    document.getElementById('eqValTss').textContent = (data.card4_equalisasi.tss || '-') + ' ppm';
                    document.getElementById('eqValCod').textContent = (data.card4_equalisasi.cod || '-') + ' ppm';
                    document.getElementById('eqValEc').textContent = (data.card4_equalisasi.ec || '-') + ' mS';
                }

                if (data.card5_effluent_analisa) {
                    document.getElementById('effValPh').textContent = data.card5_effluent_analisa.ph || '-';
                    document.getElementById('effValTss').textContent = (data.card5_effluent_analisa.tss || '-') + ' ppm';
                    document.getElementById('effValCod').textContent = (data.card5_effluent_analisa.cod || '-') + ' ppm';
                    document.getElementById('effValEc').textContent = (data.card5_effluent_analisa.ec || '-') + ' mS';
                }

                if (data.card8_env_perf && Array.isArray(data.card8_env_perf)) {
                    let tbodyHtml = '';
                    const items = data.card8_env_perf;

                    const calcRemPct = (inVal, outVal) => {
                        const a = parseFloat(inVal);
                        const b = parseFloat(outVal);
                        if (isNaN(a) || isNaN(b) || a <= 0) return null;
                        return Math.round(((a - b) / a) * 1000) / 10;
                    };

                    items.forEach((item, idx) => {
                        let remCod = item.rem_cod;
                        let remTss = item.rem_tss;
                        let status = item.status || 'OK';
                        let standard = item.standard || '';
                        let codOk = true;
                        let tssOk = true;

                        // Fallback client-side calculation if rem_cod is not provided by backend
                        if (remCod === undefined || remCod === null) {
                            if (idx === 0) {
                                remCod = '-';
                                remTss = '-';
                                standard = 'Baseline';
                                codOk = true;
                                tssOk = true;
                            } else {
                                const prev = items[idx - 1];
                                const cRem = calcRemPct(prev.cod, item.cod);
                                const tRem = calcRemPct(prev.tss, item.tss);
                                remCod = cRem !== null ? cRem + '%' : '-';
                                remTss = tRem !== null ? tRem + '%' : '-';

                                if (idx === 1) { // Outlet Anaerob (COD >= 80%, TSS >= 10%)
                                    standard = 'COD ≥ 80% | TSS ≥ 10%';
                                    codOk = cRem !== null && cRem >= 80;
                                    tssOk = tRem !== null && tRem >= 10;
                                } else if (idx === 2) { // Aerob (COD >= 80%, TSS >= 50%)
                                    standard = 'COD ≥ 80% | TSS ≥ 50%';
                                    codOk = cRem !== null && cRem >= 80;
                                    tssOk = tRem !== null && tRem >= 50;
                                } else if (idx === 3) { // Outlet DAF (COD >= 10%, TSS >= 70%)
                                    standard = 'COD ≥ 10% | TSS ≥ 70%';
                                    codOk = cRem !== null && cRem >= 10;
                                    tssOk = tRem !== null && tRem >= 70;
                                } else if (idx === 4) { // Effluent (COD <= 300, TSS <= 100)
                                    standard = 'COD ≤ 300 | TSS ≤ 100';
                                    const cVal = parseFloat(item.cod) || 0;
                                    const tVal = parseFloat(item.tss) || 0;
                                    codOk = cVal <= 300;
                                    tssOk = tVal <= 100;
                                }
                            }
                            status = (codOk && tssOk) ? 'OK' : 'NOK';
                        } else {
                            codOk = item.cod_ok !== undefined ? item.cod_ok : (status === 'OK');
                            tssOk = item.tss_ok !== undefined ? item.tss_ok : (status === 'OK');
                        }

                        const isOk = (status === 'OK');
                        const badgeClass = isOk ? 'badge-ok' : 'badge-danger';

                        // Warna persentase COD: merah jika di bawah standar, hijau jika memenuhi
                        const remCodColor = (remCod === '-' || idx === 0) ?
                            'text-muted' :
                            (codOk ? 'text-success fw-semibold' : 'text-danger fw-bold');

                        // Warna persentase TSS: merah jika di bawah standar, hijau jika memenuhi
                        const remTssColor = (remTss === '-' || idx === 0) ?
                            'text-muted' :
                            (tssOk ? 'text-success fw-semibold' : 'text-danger fw-bold');

                        // Highlight Effluent jika melewati baku mutu
                        const codCellClass = (idx === 4 && !codOk) ?
                            'text-center font-monospace text-danger fw-bold' :
                            'text-center font-monospace text-cyan';
                        const tssCellClass = (idx === 4 && !tssOk) ?
                            'text-center font-monospace text-danger fw-bold' :
                            'text-center font-monospace text-info';

                        tbodyHtml += `
                    <tr>
                        <td class="fw-semibold text-white" style="font-size: 8.5px; white-space: nowrap; padding: 4px 4px;">${item.parameter}</td>
                        <td class="text-center font-monospace" style="padding: 4px 3px; font-size: 8.5px;">${item.ph ?? '-'}</td>
                        <td class="${codCellClass}" style="padding: 4px 3px; font-size: 8.5px;">${item.cod ?? '-'}</td>
                        <td class="${tssCellClass}" style="padding: 4px 3px; font-size: 8.5px;">${item.tss ?? '-'}</td>
                        <td class="text-center font-monospace ${remCodColor}" style="padding: 4px 3px; font-size: 8.5px;" title="Std: ${standard}">${remCod}</td>
                        <td class="text-center font-monospace ${remTssColor}" style="padding: 4px 3px; font-size: 8.5px;" title="Std: ${standard}">${remTss}</td>
                        <td class="text-center" style="padding: 4px 4px;"><span class="${badgeClass}" title="Std: ${standard}">${status}</span></td>
                    </tr>
                    `;
                    });
                    document.getElementById('tableEnvPerfBody').innerHTML = tbodyHtml;
                }
            }

            // 10. Status Operasi Pit
            async function loadStatusOperasiSection(start, end) {
                const data = await fetchWcoSection('status_operasi', start, end);
                if (!data) return;

                if (data.card10_status_operasi && Array.isArray(data.card10_status_operasi)) {
                    let containerHtml = '';
                    data.card10_status_operasi.forEach(item => {
                        const isOver = item.is_over === true || (item.status && item.status.toUpperCase() ===
                            'OVER');
                        const badgeClass = isOver ? 'badge-danger' : 'badge-ok';
                        const pulseClass = isOver ? 'pulse-red' : 'pulse-green';
                        const volColor = isOver ? 'text-danger fw-bold font-monospace' :
                            'text-info fw-bold font-monospace';

                        containerHtml += `
                    <div class="op-status-item" title="Standar: max ${item.standard || '-'} m³/day">
                        <div class="d-flex align-items-center gap-2">
                            <img src="${item.img}" alt="${item.name}" style="width: 20px; height: 20px; object-fit: contain;">
                            <div>
                                <div class="fw-bold text-white" style="font-size: 9.5px;">${item.name}</div>
                                <div class="text-muted" style="font-size: 8px;">${item.subtext}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="${badgeClass}"><span class="pulse-dot ${pulseClass}"></span>${item.status}</span>
                            <div class="${volColor}" style="font-size: 9px; margin-top: 1px;">${item.volume}</div>
                        </div>
                    </div>
                `;
                    });
                    document.getElementById('wcoStatusOperasiContainer').innerHTML = containerHtml;
                }
            }

            // 7. Influent Sparta & Pit Garam (WWTP Proses Harian)
            async function loadSafetyPerfSection(start, end) {
                const data = await fetchWcoSection('safety_perf', start, end);
                if (!data) return;

                if (data.card7_safety_perf) {
                    const sp = data.card7_safety_perf;
                    const categories = sp.categories || [];
                    const spartaData = sp.sparta || [];
                    const garamData = sp.garam || [];

                    // Update Mini Stat badges
                    const spartaBadge = document.getElementById('statSpartaBadge');
                    const garamBadge = document.getElementById('statGaramBadge');
                    if (spartaBadge && sp.avg_sparta !== undefined) {
                        const isSpartaOver = sp.is_sparta_over !== undefined ? sp.is_sparta_over : (sp.avg_sparta > 50);
                        spartaBadge.className = isSpartaOver ? 'badge-danger' : 'badge-ok';
                        spartaBadge.textContent = `Sparta: ${sp.avg_sparta} m³`;
                        spartaBadge.title =
                            `Rata-rata Sparta: ${sp.avg_sparta} m³/day (Std: ≤ 50 m³) | Total: ${sp.total_sparta || '-'} m³`;
                    }
                    if (garamBadge && sp.avg_garam !== undefined) {
                        const isGaramOver = sp.is_garam_over !== undefined ? sp.is_garam_over : (sp.avg_garam > 50);
                        garamBadge.className = isGaramOver ? 'badge-danger' : 'badge-ok';
                        garamBadge.textContent = `Garam: ${sp.avg_garam} m³`;
                        garamBadge.title =
                            `Rata-rata Garam: ${sp.avg_garam} m³/day (Std: ≤ 50 m³) | Total: ${sp.total_garam || '-'} m³`;
                    }

                    const safetyOptions = {
                        chart: {
                            type: 'bar',
                            height: 135,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: {
                            mode: 'dark'
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '50%',
                                borderRadius: 2,
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -9,
                            style: {
                                fontSize: '7.5px',
                                fontWeight: 600,
                                colors: ['#38bdf8', '#60a5fa']
                            },
                            formatter: function(val) {
                                return val > 0 ? Number(val).toLocaleString('id-ID') : '';
                            }
                        },
                        colors: ['#06b6d4', '#3b82f6'],
                        series: [{
                                name: 'Pit Sparta',
                                data: spartaData
                            },
                            {
                                name: 'Pit Garam',
                                data: garamData
                            }
                        ],
                        xaxis: {
                            categories: categories,
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '8.5px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '8.5px'
                                }
                            }
                        },
                        grid: {
                            borderColor: '#1e293b',
                            strokeDashArray: 3,
                            padding: {
                                top: 5,
                                bottom: -5,
                                left: 5,
                                right: 5
                            }
                        },
                        legend: {
                            position: 'top',
                            fontSize: '8.5px',
                            labels: {
                                colors: '#94a3b8'
                            },
                            itemMargin: {
                                horizontal: 4
                            }
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: function(val) {
                                    return val !== null && val !== undefined ? Number(val).toLocaleString('id-ID') +
                                        ' m³/day' : '-';
                                }
                            }
                        }
                    };
                    renderOrUpdateChart('chartSafetyPerf', safetyOptions);
                }
            }

            // 14. Influent Mingguan (Area Chart)
            async function loadInfluentMingguanSection(start, end) {
                const data = await fetchWcoSection('influent_mingguan', start, end);
                if (!data) return;

                if (data.card14_influent_mingguan) {
                    const im = data.card14_influent_mingguan;
                    const influentWeeklyOptions = {
                        chart: {
                            type: 'area',
                            height: 135,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: {
                            mode: 'dark'
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -3,
                            style: {
                                fontSize: '7.5px',
                                fontWeight: 600,
                                colors: ['#38bdf8', '#34d399', '#fbbf24']
                            },
                            background: {
                                enabled: false
                            },
                            formatter: function(val) {
                                return val !== null && val !== undefined && val !== '' ? Number(val).toLocaleString(
                                    'id-ID') : '';
                            }
                        },
                        markers: {
                            size: 2.5,
                            hover: {
                                size: 4
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        colors: ['#06b6d4', '#10b981', '#f59e0b'],
                        series: [{
                                name: 'Pit Sparta',
                                data: im.sparta || []
                            },
                            {
                                name: 'Pit Garam',
                                data: im.garam || []
                            },
                            {
                                name: 'Step 3',
                                data: im.step3 || []
                            }
                        ],
                        xaxis: {
                            categories: im.categories || [],
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '9px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '9px'
                                }
                            }
                        },
                        grid: {
                            borderColor: '#1e293b',
                            strokeDashArray: 3,
                            padding: {
                                top: 5,
                                bottom: -5,
                                left: 5,
                                right: 5
                            }
                        },
                        legend: {
                            position: 'top',
                            fontSize: '8.5px',
                            labels: {
                                colors: '#94a3b8'
                            },
                            itemMargin: {
                                horizontal: 3
                            }
                        },
                        tooltip: {
                            theme: 'dark'
                        }
                    };
                    renderOrUpdateChart('chartInfluentMingguan', influentWeeklyOptions);
                }
            }

            // 11. Trend Effluent Mingguan (Bar Chart)
            async function loadEffluentMingguanSection(start, end) {
                const data = await fetchWcoSection('effluent_mingguan', start, end);
                if (!data) return;

                if (data.card11_trend_effluent_mingguan) {
                    const em = data.card11_trend_effluent_mingguan;
                    const effluentWeeklyOptions = {
                        chart: {
                            type: 'bar',
                            height: 135,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: {
                            mode: 'dark'
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '50%',
                                borderRadius: 2,
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -10,
                            style: {
                                fontSize: '7.5px',
                                fontWeight: 600,
                                colors: ['#93c5fd', '#c4b5fd']
                            },
                            formatter: function(val) {
                                return val ? Number(val).toLocaleString('id-ID') : '';
                            }
                        },
                        colors: ['#3b82f6', '#8b5cf6'],
                        series: [{
                                name: 'Full Proses',
                                data: em.full_proses || []
                            },
                            {
                                name: 'DAF Pre',
                                data: em.daf_pre || []
                            }
                        ],
                        xaxis: {
                            categories: em.categories || [],
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '9px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '9px'
                                }
                            }
                        },
                        grid: {
                            borderColor: '#1e293b',
                            strokeDashArray: 3,
                            padding: {
                                top: 5,
                                bottom: -5,
                                left: 5,
                                right: 5
                            }
                        },
                        legend: {
                            position: 'top',
                            fontSize: '8.5px',
                            labels: {
                                colors: '#94a3b8'
                            },
                            itemMargin: {
                                horizontal: 3
                            }
                        },
                        tooltip: {
                            theme: 'dark'
                        }
                    };
                    renderOrUpdateChart('chartEffluentMingguan', effluentWeeklyOptions);
                }
            }

            // 12. Trend Kepatuhan COD (Line Chart with Baku Mutu 300)
            async function loadKepatuhanCodSection(start, end) {
                const data = await fetchWcoSection('kepatuhan_cod', start, end);
                if (!data) return;

                if (data.card12_trend_kepatuhan_effluent_cod) {
                    const cod = data.card12_trend_kepatuhan_effluent_cod;
                    const bakuMutuVal = cod.baku_mutu || 300;
                    if (document.getElementById('codComplianceBadge')) {
                        document.getElementById('codComplianceBadge').textContent = (cod.compliance ?? 100) + '% OK';
                    }
                    const codOptions = {
                        chart: {
                            type: 'line',
                            height: 135,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: {
                            mode: 'dark'
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -4,
                            style: {
                                fontSize: '8px',
                                fontWeight: 600,
                                colors: ['#34d399']
                            },
                            background: {
                                enabled: false
                            },
                            formatter: function(val) {
                                return val !== null && val !== undefined ? Number(val).toLocaleString('id-ID') : '';
                            }
                        },
                        markers: {
                            size: 3,
                            hover: {
                                size: 5
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        colors: ['#10b981'],
                        series: [{
                            name: 'COD (ppm)',
                            data: cod.values || []
                        }],
                        annotations: {
                            yaxis: [{
                                y: bakuMutuVal,
                                borderColor: '#ef4444',
                                strokeDashArray: 2,
                                label: {
                                    text: `Baku Mutu (${bakuMutuVal})`,
                                    style: {
                                        color: '#fff',
                                        background: '#ef4444',
                                        fontSize: '8px'
                                    }
                                }
                            }]
                        },
                        xaxis: {
                            categories: cod.categories || [],
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '9px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '9px'
                                }
                            }
                        },
                        grid: {
                            borderColor: '#1e293b',
                            strokeDashArray: 3,
                            padding: {
                                top: 5,
                                bottom: -5,
                                left: 5,
                                right: 5
                            }
                        },
                        tooltip: {
                            theme: 'dark'
                        }
                    };
                    renderOrUpdateChart('chartKepatuhanCod', codOptions);
                }
            }

            // 13. Performance Aerasi Dan Lumpur Aktif (Table)
            async function loadPerformanceSampleSection(start, end) {
                const data = await fetchWcoSection('performance_sampel', start, end);
                if (!data) return;

                if (data.card13_chem_storage_performance) {
                    const samples = data.card13_chem_storage_performance.samples || [];
                    let aerasiHtml = '';
                    samples.forEach(s => {
                        const sv30 = parseFloat(s.sv30);
                        const mlss = parseFloat(s.mlss);
                        const svi = parseFloat((s.svi !== undefined && s.svi !== null) ? s.svi : s.svl);
                        const doVal = parseFloat(s.do);
                        const ph = parseFloat(s.ph);

                        // Standar validasi:
                        // 1. Sv'30: 300 - 500 ml
                        const sv30Ok = s.sv30_ok !== undefined ? s.sv30_ok : (!isNaN(sv30) ? (sv30 >= 300 && sv30 <=
                            500) : true);
                        // 2. Mlss: 1000 - 5000 ppm
                        const mlssOk = s.mlss_ok !== undefined ? s.mlss_ok : (!isNaN(mlss) ? (mlss >= 1000 &&
                            mlss <= 5000) : true);
                        // 3. SVI: 90 - 150
                        const sviOk = s.svi_ok !== undefined ? s.svi_ok : (!isNaN(svi) ? (svi >= 90 && svi <= 150) :
                            true);
                        // 4. DO: 2 - 4 ppm (jika < 2 merah, 2-4 hijau, > 4 merah)
                        const doOk = s.do_ok !== undefined ? s.do_ok : (!isNaN(doVal) ? (doVal >= 2.0 && doVal <= 4.0) : true);
                        // 5. pH: 7 - 9
                        const phOk = s.ph_ok !== undefined ? s.ph_ok : (!isNaN(ph) ? (ph >= 7.0 && ph <= 9.0) :
                            true);

                        const isAllOk = (sv30Ok && mlssOk && sviOk && doOk && phOk);
                        const rowStatus = s.status || (isAllOk ? 'OK' : 'NOT OK');
                        const badgeClass = rowStatus === 'OK' ? 'badge-ok' : 'badge-danger';

                        const sv30Color = sv30Ok ? 'text-white' : 'text-danger fw-bold';
                        const mlssColor = mlssOk ? 'text-cyan' : 'text-danger fw-bold';
                        const sviColor = sviOk ? 'text-warning' : 'text-danger fw-bold';
                        let doColor = 'text-muted';
                        if (!isNaN(doVal)) {
                            doColor = (doVal >= 2.0 && doVal <= 4.0) ? 'text-success fw-bold' : 'text-danger fw-bold';
                        }
                        const phColor = phOk ? 'text-muted' : 'text-danger fw-bold';

                        const sviDisplay = !isNaN(svi) ? svi : '-';

                        aerasiHtml += `
                    <tr>
                        <td class="fw-semibold text-white" style="font-size: 9.5px;">${s.nama_sampel}</td>
                        <td class="text-center font-monospace ${sv30Color}" title="Standar: 300 - 500 ml">${s.sv30 ?? '-'}</td>
                        <td class="text-center font-monospace ${mlssColor}" title="Standar: 1000 - 5000 ppm">${s.mlss ?? '-'}</td>
                        <td class="text-center font-monospace ${sviColor}" title="Standar: 90 - 150">${sviDisplay}</td>
                        <td class="text-center font-monospace ${doColor}" title="Standar: 2 - 4 ppm">${s.do ?? '-'}</td>
                        <td class="text-center font-monospace ${phColor}" title="Standar: 7 - 9">${s.ph ?? '-'}</td>
                        <td class="text-center"><span class="${badgeClass}">${rowStatus}</span></td>
                    </tr>
                `;
                    });
                    document.getElementById('tableAerasiBody').innerHTML = aerasiHtml;
                }
            }

            // 3 & 15. Sludge Management & Pengangkutan
            async function loadSludgeMgmtSection(start, end) {
                const data = await fetchWcoSection('sludge_mgmt', start, end);
                if (!data) return;

                if (data.card3_pengangkutan_sludge) {
                    const p = data.card3_pengangkutan_sludge;
                    const tonVal = parseFloat(p.total_tonase) || 0;
                    const costVal = p.total_cost !== undefined ? p.total_cost : (tonVal * 1000 * 1400);
                    const costFormatted = p.cost_formatted || ('Rp ' + Number(costVal).toLocaleString('id-ID'));

                    document.getElementById('topPengangkutanSludgeVal').textContent = p.formatted || (tonVal.toLocaleString(
                        'id-ID', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 2
                        }) + ' Ton');

                    const costEl = document.getElementById('topPengangkutanSludgeCost');
                    if (costEl) {
                        costEl.textContent = costFormatted;
                    }

                    const subEl = document.getElementById('topPengangkutanSludgeSub');
                    if (subEl && p.formula) {
                        subEl.title = `Rumus: ${p.formula}`;
                    }
                }

                if (data.card15_sludge_mgmt) {
                    const sl = data.card15_sludge_mgmt;

                    // 1. Drain Lumpur (Standar: 19 m³/hari, di atas merah, dibawah/sama hijau)
                    const drainVal = parseFloat(sl.drain_lumpur) || 0;
                    const drainOk = sl.drain_lumpur_ok !== undefined ? sl.drain_lumpur_ok : (drainVal <= 19.0);
                    const drainEl = document.getElementById('sludgeDrainVal');
                    if (drainEl) {
                        drainEl.textContent = drainVal.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 1
                        });
                        drainEl.className = drainOk ? 'text-success fw-bold' : 'text-danger fw-bold';
                    }
                    const drainStatusEl = document.getElementById('sludgeDrainStatus');
                    if (drainStatusEl) {
                        drainStatusEl.className = drainOk ? 'badge-ok' : 'badge-danger';
                        drainStatusEl.textContent = drainOk ? 'OK' : 'OVER';
                    }

                    // 2. Running Hour SCP (Standar: 6 jam/hari, di atas merah, dibawah/sama hijau)
                    const runningVal = parseFloat(sl.running_hour_scp) || 0;
                    const runningOk = sl.running_hour_ok !== undefined ? sl.running_hour_ok : (runningVal <= 6.0);
                    const runningEl = document.getElementById('sludgeRunningVal');
                    if (runningEl) {
                        runningEl.textContent = runningVal.toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 1
                        });
                        runningEl.className = runningOk ? 'text-success fw-bold' : 'text-danger fw-bold';
                    }
                    const runningStatusEl = document.getElementById('sludgeRunningStatus');
                    if (runningStatusEl) {
                        runningStatusEl.className = runningOk ? 'badge-ok' : 'badge-danger';
                        runningStatusEl.textContent = runningOk ? 'OK' : 'OVER';
                    }

                    // 3. Hasil Lumpur (Standar: 1.2 ton/hari, satuan Ton bukan kg, di atas merah, dibawah/sama hijau)
                    let hasilVal = parseFloat(sl.hasil_lumpur) || 0;
                    if (hasilVal > 50) hasilVal = hasilVal / 1000;
                    const hasilOk = sl.hasil_lumpur_ok !== undefined ? sl.hasil_lumpur_ok : (hasilVal <= 1.2);
                    const hasilEl = document.getElementById('sludgeHasilVal');
                    if (hasilEl) {
                        hasilEl.textContent = hasilVal.toLocaleString('id-ID', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 2
                        });
                        hasilEl.className = hasilOk ? 'text-success fw-bold' : 'text-danger fw-bold';
                    }
                    const hasilStatusEl = document.getElementById('sludgeHasilStatus');
                    if (hasilStatusEl) {
                        hasilStatusEl.className = hasilOk ? 'badge-ok' : 'badge-danger';
                        hasilStatusEl.textContent = hasilOk ? 'OK' : 'OVER';
                    }
                    const hasilSatEl = document.getElementById('sludgeHasilSat');
                    if (hasilSatEl) {
                        hasilSatEl.textContent = 'Ton';
                    }

                    // 4. Content Sludge (Standar: 20%, dibawah 20% merah, di atas/sama hijau)
                    const contentVal = parseFloat(sl.sludge_content) || 0;
                    const contentOk = sl.sludge_content_ok !== undefined ? sl.sludge_content_ok : (contentVal >= 20.0);
                    const contentEl = document.getElementById('sludgeContentVal');
                    if (contentEl) {
                        contentEl.textContent = contentVal.toLocaleString('id-ID', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1
                        });
                        contentEl.className = contentOk ? 'text-success fw-bold' : 'text-danger fw-bold';
                    }
                    const contentStatusEl = document.getElementById('sludgeContentStatus');
                    if (contentStatusEl) {
                        contentStatusEl.className = contentOk ? 'badge-ok' : 'badge-danger';
                        contentStatusEl.textContent = contentOk ? 'OK' : 'LOW';
                    }
                }
            }

            // 16. Total Koloni dalam Pengolahan Biologi (Bar Chart with Standar 10^5)
            async function loadTopRiskKoloniSection() {
                const data = await fetchWcoSection('top_risk_koloni', '', '');
                if (!data) return;

                if (data.card16_top_risk_koloni) {
                    const kData = data.card16_top_risk_koloni;
                    const categories = kData.categories || [];
                    const values = kData.values || [];
                    const strings = kData.strings || [];

                    const koloniOptions = {
                        chart: {
                            type: 'bar',
                            height: 135,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: {
                            mode: 'dark'
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                barHeight: '55%',
                                borderRadius: 2,
                                colors: {
                                    ranges: [{
                                            from: 0,
                                            to: 0.999,
                                            color: '#ef4444'
                                        }, // Di bawah standar = merah
                                        {
                                            from: 1.0,
                                            to: 999999,
                                            color: '#10b981'
                                        } // Di atas/memenuhi standar = hijau
                                    ]
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            textAnchor: 'start',
                            style: {
                                fontSize: '8.5px',
                                colors: ['#ffffff']
                            },
                            formatter: function(val, opt) {
                                return (strings[opt.dataPointIndex] || (val + ' × 10⁵')).split(' ')[0] + ' × 10⁵';
                            },
                            offsetX: 5
                        },
                        series: [{
                            name: 'Koloni (× 10⁵ CFU)',
                            data: values
                        }],
                        xaxis: {
                            categories: categories,
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '8.5px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '9px',
                                    fontWeight: 600
                                }
                            }
                        },
                        annotations: {
                            xaxis: [{
                                x: 1.0,
                                borderColor: '#10b981',
                                strokeDashArray: 3,
                                label: {
                                    text: 'Standar (10⁵)',
                                    orientation: 'horizontal',
                                    style: {
                                        color: '#ffffff',
                                        background: '#10b981',
                                        fontSize: '8px'
                                    }
                                }
                            }]
                        },
                        grid: {
                            borderColor: '#1e293b',
                            strokeDashArray: 3,
                            padding: {
                                top: -10,
                                bottom: -5,
                                left: 10,
                                right: 10
                            }
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: function(val, opt) {
                                    const str = strings[opt.dataPointIndex] || (val + ' × 10⁵ CFU/mL');
                                    const status = val >= 1.0 ? ' (MEMENUHI STANDAR ≥ 10⁵)' :
                                        ' (DI BAWAH STANDAR < 10⁵)';
                                    return str + status;
                                }
                            }
                        }
                    };
                    renderOrUpdateChart('chartTopRiskKoloni', koloniOptions);
                }
            }

            // 17. Kepatuhan Effluent TSS (Line Chart with Baku Mutu 100)
            async function loadKepatuhanTssSection(start, end) {
                const data = await fetchWcoSection('kepatuhan_tss', start, end);
                if (!data) return;

                const tss = data.card17_she_training_effluent_tss || data.card17_hse_training_effluent_tss;
                if (tss) {
                    const bakuMutuTss = tss.baku_mutu || 100;
                    if (document.getElementById('tssComplianceBadge')) {
                        const comp = tss.compliance ?? 96;
                        document.getElementById('tssComplianceBadge').textContent = comp + '% OK';
                        document.getElementById('tssComplianceBadge').className = comp >= 90 ? 'text-success' :
                            'text-danger';
                    }
                    const tssOptions = {
                        chart: {
                            type: 'line',
                            height: 135,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: {
                            mode: 'dark'
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -4,
                            style: {
                                fontSize: '8px',
                                fontWeight: 600,
                                colors: ['#38bdf8']
                            },
                            background: {
                                enabled: false
                            },
                            formatter: function(val) {
                                return val !== null && val !== undefined ? Number(val).toLocaleString('id-ID') : '';
                            }
                        },
                        markers: {
                            size: 3,
                            hover: {
                                size: 5
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        colors: ['#06b6d4'],
                        series: [{
                            name: 'TSS (ppm)',
                            data: tss.values || []
                        }],
                        annotations: {
                            yaxis: [{
                                y: bakuMutuTss,
                                borderColor: '#ef4444',
                                strokeDashArray: 2,
                                label: {
                                    text: `Baku Mutu (${bakuMutuTss})`,
                                    style: {
                                        color: '#fff',
                                        background: '#ef4444',
                                        fontSize: '8px'
                                    }
                                }
                            }]
                        },
                        xaxis: {
                            categories: tss.categories || [],
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '9px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '9px'
                                }
                            }
                        },
                        grid: {
                            borderColor: '#1e293b',
                            strokeDashArray: 3,
                            padding: {
                                top: 5,
                                bottom: -5,
                                left: 5,
                                right: 5
                            }
                        },
                        tooltip: {
                            theme: 'dark'
                        }
                    };
                    renderOrUpdateChart('chartKepatuhanTss', tssOptions);
                }
            }

            // 6. Informasi WWTP (Debit 1 & Debit 2)
            async function loadInformasiWWTPSection(start, end) {
                const data = await fetchWcoSection('informasi_wwtp', start, end);
                if (!data) return;

                const info = data.card6_informasi_wwtp || data;
                if (info && info.debit1 !== undefined) {
                    document.getElementById('infoDebit1Val').textContent = `${info.debit1} m³/h`;
                }
                if (info && info.debit2 !== undefined) {
                    document.getElementById('infoDebit2Val').textContent = `${info.debit2} m³/h`;
                }
            }

            // Master Loader: All sections triggered in parallel!
            function loadWcoData() {
                const start = document.getElementById('wcoStartDate').value;
                const end = document.getElementById('wcoEndDate').value;

                // Trigger every card asynchronously in parallel.
                // Whichever finishes first renders immediately!
                loadBiayaSection(start, end);
                loadAnalisaSection(start, end);
                loadInformasiWWTPSection(start, end);
                loadStatusOperasiSection(start, end);
                loadSafetyPerfSection(start, end);
                loadInfluentMingguanSection(start, end);
                loadEffluentMingguanSection(start, end);
                loadKepatuhanCodSection(start, end);
                loadPerformanceSampleSection(start, end);
                loadSludgeMgmtSection(start, end);
                loadTopRiskKoloniSection();
                loadKepatuhanTssSection(start, end);
            }
        </script>
    </body>

</html>
