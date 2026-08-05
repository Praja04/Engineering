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
        <a class="nav-link menu-link {{ request()->routeIs('master.mtc.*') ? '' : 'collapsed' }}" href="#sidebarMasterMtc"
            data-bs-toggle="collapse" role="button"
            aria-expanded="{{ request()->routeIs('master.mtc.*') ? 'true' : 'false' }}" aria-controls="sidebarMasterMtc">
            <i class="mdi mdi-account-wrench"></i> <span data-key="t-mtc">Master Mtc</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->routeIs('master.mtc.*') ? 'show' : '' }}" id="sidebarMasterMtc">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="{{ route('master.mtc.mesin.index') }}"
                        class="nav-link {{ request()->routeIs('master.mtc.mesin.index') ? 'active' : '' }}">
                        <i class="mdi mdi-view-grid"></i>Master Mesin
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('mtc/master/agenda/master') }}"
                        class="nav-link {{ request()->is('mtc/master/agenda/master') ? 'active' : '' }}">
                        <i class="mdi mdi-chart-bar me-2"></i> Master Agenda
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endif
