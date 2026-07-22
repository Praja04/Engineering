@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
    ($jabatan === 'admin') ||
    ($jabatan === 'dept_head') ||
    ($jabatan === 'supervisor' && $bagian === 'Engineering Produksi (EPR)') ||
    ($jabatan === 'foreman' && $bagian === 'Engineering Produksi (EPR)')
)
<li class="nav-item">
    <a class="nav-link menu-link {{ request()->is('epr/dashboard') ? '' : 'collapsed' }}" href="#sidebarEprDashboard" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('epr/dashboard') ? 'true' : 'false' }}" aria-controls="sidebarEprDashboard">
        <i class="mdi mdi-poll"></i>
        <span data-key="t-dashboards">Dashboard EPR</span>
    </a>
    <div class="collapse menu-dropdown {{ request()->is('epr/dashboard') ? 'show' : '' }}" id="sidebarEprDashboard">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('epr.dashboard') }}" class="nav-link {{ request()->is('epr/dashboard') ? 'active' : '' }}">
                    <i class="mdi mdi-chart-bar me-2"></i> EPR Center
                </a>
            </li>
        </ul>
    </div>
</li>
@endif
