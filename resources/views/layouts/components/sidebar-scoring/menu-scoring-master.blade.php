@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
($jabatan === 'dept_head') ||
($jabatan === 'supervisor') ||
($jabatan === 'foreman' && $bagian === 'Engineering Maintenance & Improvement')
)
<li class="nav-item">
    <a class="nav-link menu-link" href="#sidebarScoringMesin" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarScoringMesin">
        <i class="mdi mdi-robot-industrial"></i>
        <span data-key="t-dashboards">Data Mesin</span>
    </a>
    <div class="collapse menu-dropdown" id="sidebarScoringMesin">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ url('machine-processes') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-clipboard-list-outline me-2"></i> Data Master Scoring
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('scoring-mesin/machines') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-cogs me-2"></i> Data Master Mesin
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('scoring-mesin/process-parameters') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-tune-variant me-2"></i> Data Master Proses
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('scoring-mesin/sections') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-view-dashboard-outline me-2"></i> Data Master Section
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('scoring-mesin/parts') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-puzzle-outline me-2"></i> Data Master Part
                </a>
            </li>
        </ul>
    </div>
</li>
@endif