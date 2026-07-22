@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
$jabatan === 'admin' ||
$jabatan === 'dept_head' ||
$jabatan === 'supervisor' ||
($jabatan === 'operator' && in_array($bagian, ['Engineering', 'Engineering Maintenance & Improvement'])) ||
($jabatan === 'foreman' && in_array($bagian, ['Engineering', 'Engineering Maintenance & Improvement'])))
<li class="nav-item">
    <a class="nav-link menu-link {{ request()->is('mtc/dashboard/*') || request()->is('mtc/agenda') ? '' : 'collapsed' }}" href="#sidebarMtcDashboard" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('mtc/dashboard/*') || request()->is('mtc/agenda') ? 'true' : 'false' }}" aria-controls="sidebarMtcDashboard">
        <i class="mdi mdi-toolbox"></i>
        <span data-key="t-dashboards">Dashboard MTC</span>
    </a>
    <div class="collapse menu-dropdown {{ request()->is('mtc/dashboard/*') || request()->is('mtc/agenda') ? 'show' : '' }}" id="sidebarMtcDashboard">
        <ul class="nav nav-sm flex-column">

            <li class="nav-item">
                <a href="{{ url('mtc/agenda') }}" class="nav-link {{ request()->is('mtc/agenda') ? 'active' : '' }}" data-key="t-analytics">
                    <i class="mdi mdi-chart-bar me-2"></i> Agenda
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('mtc.dashboard.material') }}" class="nav-link {{ request()->routeIs('mtc.dashboard.material') ? 'active' : '' }}" data-key="t-materials">
                    <i class="mdi mdi-cube-outline me-2"></i> Kebutuhan Material
                </a>
            </li>

        </ul>
    </div>
</li>
@endif