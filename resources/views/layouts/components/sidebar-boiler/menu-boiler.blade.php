@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if (in_array($jabatan, ['admin', 'operator', 'foreman', 'supervisor', 'dept_head']) &&
        in_array($bagian, ['IT', 'Engineering', 'Engineering Utility', 'Engineering WWTP']))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('boiler.*') ? '' : 'collapsed' }}" href="#sideBarBoiler"
            data-bs-toggle="collapse" role="button"
            aria-expanded="{{ request()->routeIs('boiler.*') ? 'true' : 'false' }}" aria-controls="sideBarBoiler">
            <i class="mdi mdi-cog"></i> <span data-key="t-boiler">Boiler</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->routeIs('boiler.*') ? 'show' : '' }}" id="sideBarBoiler">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="{{ route('boiler.form') }}"
                        class="nav-link {{ request()->routeIs('boiler.form') ? 'active' : '' }}">
                        <i class="mdi mdi-file-document"></i>Form Boiler</a>
                </li>
            </ul>
        </div>
    </li>
@endif
