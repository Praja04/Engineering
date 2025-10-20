@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
($jabatan === 'dept_head') ||
($jabatan === 'supervisor')||($jabatan === 'operator' && $bagian === 'Engineering WWTP') ||
($jabatan === 'foreman' && $bagian === 'Engineering WWTP')
)
<li class="nav-item">
    <a class="nav-link menu-link" href="#sidebarUtilityDashboard" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUtilityDashboard">
        <i class="mdi mdi-wrench"></i>
        <span data-key="t-dashboards">Dashboard Utility & WWTP</span>
    </a>
    <div class="collapse menu-dropdown" id="sidebarUtilityDashboard">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ url('utility/dashboard') }}" class="nav-link" data-key="t-analytics">
                    <i class="mdi mdi-chart-bar me-2"></i> Dashboard Utility
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="{{ url('wwtp/dashboard') }}">
                    <i class="mdi mdi-card-account-details"></i> <span data-key="t-widgets">Dashboard WWTP</span>
                </a>
            </li>
        </ul>
    </div>
</li>
@endif