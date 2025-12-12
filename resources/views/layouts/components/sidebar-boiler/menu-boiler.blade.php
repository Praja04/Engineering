@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if (in_array($jabatan, ['operator', 'foreman', 'supervisor', 'dept_head']) &&
        in_array($bagian, ['Engineering', 'Engineering Utility']))
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
                {{-- <li class="nav-item">
                    <a href="{{ route('boiler.data') }}"
                        class="nav-link {{ request()->routeIs('boiler.data') ? 'active' : '' }}" data-key="t-tkbm">
                        <i class="mdi mdi-book-open"></i>Data Boiler</a>
                </li> --}}
            </ul>
        </div>
    </li>
@endif
