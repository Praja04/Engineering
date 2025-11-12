@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if (
    $jabatan === 'dept_head' ||
        $jabatan === 'supervisor' ||
        ($jabatan === 'operator' && in_array($bagian, ['Engineering', 'Engineering WWTP'])) ||
        ($jabatan === 'foreman' && in_array($bagian, ['Engineering', 'Engineering WWTP'])))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('kpi.*') ? '' : 'collapsed' }}" href="#sidebarUtility"
            data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('kpi.*') ? 'true' : 'false' }}"
            aria-controls="sidebarUtility">
            <i class="mdi mdi-wrench"></i>
            <span data-key="t-dashboards">Utility</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->routeIs('kpi.*') ? 'show' : '' }}" id="sidebarUtility">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ url('utility/form') }}">
                        <i class="mdi mdi-card-account-details"></i> <span data-key="t-widgets">Form
                            Utility</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ url('utility/data') }}">
                        <i class="mdi mdi-card-account-details"></i> <span data-key="t-widgets">Data
                            Utility</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kpi.form') ? 'active' : '' }}"
                        href="{{ url('kpi/form') }}">
                        <i class="mdi mdi-poll"></i> <span data-key="t-widgets">Form
                            KPI</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kpi.data') ? 'active' : '' }}"
                        href="{{ url('kpi/data') }}">
                        <i class="mdi mdi-poll"></i> <span data-key="t-widgets">Data
                            KPI</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endif
