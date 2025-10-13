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
    <a class="nav-link menu-link" href="#sidebarDashboardScoring" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboardScoring">
        <i class="mdi mdi-speedometer"></i>
        <span data-key="t-dashboards">Dashboard Retail</span>
    </a>
    <div class="collapse menu-dropdown" id="sidebarDashboardScoring">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ url('/dashboard/mesin/scoring') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-chart-bar me-2"></i> Dashboard Scoring Mesin
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('dashboard/mesin/downtime') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-timer-sand-empty me-2"></i> Dashboard Downtime Mesin
                </a>
            </li>
        </ul>
    </div>
</li>

@endif