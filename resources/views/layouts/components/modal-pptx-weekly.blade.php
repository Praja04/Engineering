<!-- Modal Filter PPTX Weekly Utility (Senin - Jumat) -->
<div class="modal fade" id="modalFilterPptxWeekly" tabindex="-1" aria-labelledby="modalFilterPptxWeeklyLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #0f172a; border: 1px solid #1e293b; color: #f8fafc; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div class="modal-header" style="border-bottom: 1px solid #1e293b; padding: 14px 20px;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-file-powerpoint-box text-warning" style="font-size: 22px;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="modalFilterPptxWeeklyLabel">Export PPTX Weekly Utility</h6>
                        <small style="font-size: 11px; color: #94a3b8;">Filter Periode Mingguan (Senin s/d Jumat)</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPptxWeekly" action="{{ route('utility.pptx_weekly') }}" method="GET" target="_blank">
                <input type="hidden" name="mode" value="mingguan">
                <input type="hidden" name="week" id="pptxInputWeek" value="1">
                <input type="hidden" name="week_label" id="pptxInputWeekLabel" value="Week 1">

                <div class="modal-body" style="padding: 20px;">
                    <!-- Row 1: Tahun & Bulan -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-light mb-1">
                                <i class="mdi mdi-calendar-blank text-info me-1"></i> Tahun
                            </label>
                            <select id="pptxYearSelect" class="form-select form-select-sm" style="background: #1e293b; color: #f8fafc; border-color: #334155;">
                                @php
                                    $currentYear = (int)date('Y');
                                @endphp
                                @for ($y = $currentYear + 1; $y >= $currentYear - 2; $y--)
                                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-light mb-1">
                                <i class="mdi mdi-calendar-month text-info me-1"></i> Bulan
                            </label>
                            <select id="pptxMonthSelect" class="form-select form-select-sm" style="background: #1e293b; color: #f8fafc; border-color: #334155;">
                                @php
                                    $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    $currentMonth = (int)date('n');
                                @endphp
                                @foreach ($months as $num => $name)
                                    <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Dropdown Week -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-warning mb-1">
                            <i class="mdi mdi-calendar-week me-1"></i> Pilih Minggu ke (Week)
                        </label>
                        <select id="pptxWeekSelect" class="form-select form-select-sm fw-semibold" style="background: #1e293b; color: #fbbf24; border-color: #475569;">
                            <!-- Populated dynamically by JS -->
                        </select>
                        <div class="form-text" style="color: #94a3b8; font-size: 11px;">
                            <i class="mdi mdi-check-circle-outline text-success me-1"></i> Otomatis mengatur periode kerja dari hari <b>Senin sampai Jumat</b>.
                        </div>
                    </div>

                    <!-- Row 3: Rentang Tanggal Terpilih (Senin s/d Jumat) -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-light mb-1">
                                <i class="mdi mdi-calendar-start text-cyan me-1"></i> Mulai (Senin)
                            </label>
                            <input type="date" id="pptxStartDate" name="start_date" class="form-control form-control-sm" style="background: #1e293b; color: #f8fafc; border-color: #334155;" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-light mb-1">
                                <i class="mdi mdi-calendar-end text-cyan me-1"></i> Selesai (Jumat)
                            </label>
                            <input type="date" id="pptxEndDate" name="end_date" class="form-control form-control-sm" style="background: #1e293b; color: #f8fafc; border-color: #334155;" required>
                        </div>
                    </div>

                    <!-- Preview Summary Card -->
                    <div style="background: rgba(30, 41, 59, 0.7); border: 1px solid #334155; border-radius: 8px; padding: 12px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-light">
                                <i class="mdi mdi-eye-outline text-info me-1"></i> Preview Periode PPTX:
                            </span>
                            <span id="pptxBadgeWeek" class="badge bg-warning text-dark fw-bold px-2 py-1">Week 1</span>
                        </div>
                        <div id="pptxSummaryText" class="fw-semibold text-info" style="font-size: 13px;">
                            Senin, 05 Okt 2026 - Jumat, 09 Okt 2026
                        </div>
                        <div class="mt-2 pt-2 border-top border-secondary d-flex align-items-center justify-content-between" style="font-size: 11px; color: #94a3b8;">
                            <span><i class="mdi mdi-chart-line text-warning me-1"></i> Chart Slide 8:</span>
                            <span class="text-white">BB vs Steam & Kondensat (5 Hari Kerja)</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-top: 1px solid #1e293b; padding: 12px 20px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnDownloadPptxWeekly" class="btn btn-sm btn-warning text-dark fw-bold d-flex align-items-center gap-1 shadow-sm">
                        <i class="mdi mdi-download" id="iconDownloadPptx"></i>
                        <span id="textDownloadPptx">Download PPTX</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    // Helper to calculate business weeks (Monday - Friday) in a given month
    function getBusinessWeeksInMonth(year, month) {
        const weeks = [];
        const firstDay = new Date(year, month - 1, 1);
        const lastDay = new Date(year, month, 0);

        let dayOfWeek = firstDay.getDay();
        let isoDay = dayOfWeek === 0 ? 7 : dayOfWeek;

        let currentMonday = new Date(year, month - 1, 1);
        if (isoDay <= 5) {
            currentMonday.setDate(currentMonday.getDate() - (isoDay - 1));
        } else {
            currentMonday.setDate(currentMonday.getDate() + (8 - isoDay));
        }

        const pad = (n) => String(n).padStart(2, '0');
        const toYmd = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

        const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const formatFmt = (d) => `${pad(d.getDate())} ${monthsShort[d.getMonth()]}`;
        const formatFull = (d) => `${pad(d.getDate())} ${monthsShort[d.getMonth()]} ${d.getFullYear()}`;

        let weekNum = 1;
        while (currentMonday <= lastDay) {
            let friday = new Date(currentMonday);
            friday.setDate(currentMonday.getDate() + 4);

            weeks.push({
                week: weekNum,
                startDate: toYmd(currentMonday),
                endDate: toYmd(friday),
                startDisplay: formatFmt(currentMonday),
                endDisplay: formatFull(friday),
                label: `Week ${weekNum} (${formatFmt(currentMonday)} - ${formatFull(friday)})`
            });

            currentMonday.setDate(currentMonday.getDate() + 7);
            weekNum++;
        }
        return weeks;
    }

    function initPptxWeeklyModal() {
        const modalEl = document.getElementById('modalFilterPptxWeekly');
        if (!modalEl) return;

        // Ensure modal is attached directly to document.body so no CSS parent clips or darkens it
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }

        const yearSelect = document.getElementById('pptxYearSelect');
        const monthSelect = document.getElementById('pptxMonthSelect');
        const weekSelect = document.getElementById('pptxWeekSelect');
        const startInput = document.getElementById('pptxStartDate');
        const endInput = document.getElementById('pptxEndDate');
        const inputWeekNum = document.getElementById('pptxInputWeek');
        const inputWeekLabel = document.getElementById('pptxInputWeekLabel');
        const badgeWeek = document.getElementById('pptxBadgeWeek');
        const summaryText = document.getElementById('pptxSummaryText');
        const form = document.getElementById('formPptxWeekly');

        function updateWeeksDropdown() {
            const y = parseInt(yearSelect.value, 10);
            const m = parseInt(monthSelect.value, 10);
            const weeks = getBusinessWeeksInMonth(y, m);

            weekSelect.innerHTML = '';
            const todayStr = (new Date()).toISOString().slice(0, 10);
            let selectedIdx = 0;

            weeks.forEach((w, idx) => {
                const opt = document.createElement('option');
                opt.value = idx;
                opt.textContent = w.label;
                opt.dataset.start = w.startDate;
                opt.dataset.end = w.endDate;
                opt.dataset.week = w.week;
                opt.dataset.weekLabel = `Week ${w.week}`;
                opt.dataset.startDisplay = w.startDisplay;
                opt.dataset.endDisplay = w.endDisplay;

                // Check if current date falls within this week
                if (todayStr >= w.startDate && todayStr <= w.endDate) {
                    selectedIdx = idx;
                }
                weekSelect.appendChild(opt);
            });

            if (weeks.length > 0) {
                weekSelect.selectedIndex = selectedIdx;
                applySelectedWeek();
            }
        }

        function applySelectedWeek() {
            const selectedOpt = weekSelect.options[weekSelect.selectedIndex];
            if (!selectedOpt) return;

            const start = selectedOpt.dataset.start;
            const end = selectedOpt.dataset.end;
            const week = selectedOpt.dataset.week;
            const weekLabel = selectedOpt.dataset.weekLabel;

            startInput.value = start;
            endInput.value = end;
            inputWeekNum.value = week;
            inputWeekLabel.value = weekLabel;

            badgeWeek.textContent = weekLabel;
            summaryText.textContent = `Senin, ${selectedOpt.dataset.startDisplay} — Jumat, ${selectedOpt.dataset.endDisplay}`;
        }

        function updateFromCustomDates() {
            const s = startInput.value;
            const e = endInput.value;
            if (!s || !e) return;

            const d1 = new Date(s);
            const d2 = new Date(e);

            const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const pad = (n) => String(n).padStart(2, '0');
            const fmt = (d) => `${pad(d.getDate())} ${monthsShort[d.getMonth()]} ${d.getFullYear()}`;

            summaryText.textContent = `${fmt(d1)} — ${fmt(d2)}`;
        }

        yearSelect.addEventListener('change', updateWeeksDropdown);
        monthSelect.addEventListener('change', updateWeeksDropdown);
        weekSelect.addEventListener('change', applySelectedWeek);
        startInput.addEventListener('change', updateFromCustomDates);
        endInput.addEventListener('change', updateFromCustomDates);

        // Form submit handler: brief downloading feedback then auto-close
        form.addEventListener('submit', function() {
            const btn = document.getElementById('btnDownloadPptxWeekly');
            const icon = document.getElementById('iconDownloadPptx');
            const text = document.getElementById('textDownloadPptx');

            if (btn && icon && text) {
                btn.disabled = true;
                icon.className = 'spinner-border spinner-border-sm me-1';
                text.textContent = 'Membuat PPTX...';

                setTimeout(function() {
                    btn.disabled = false;
                    icon.className = 'mdi mdi-download';
                    text.textContent = 'Download PPTX';
                    const bsModal = bootstrap.Modal.getInstance(modalEl);
                    if (bsModal) {
                        bsModal.hide();
                    }
                }, 3500);
            }
        });

        // Initialize dropdown values on load
        updateWeeksDropdown();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPptxWeeklyModal);
    } else {
        initPptxWeeklyModal();
    }
})();
</script>
