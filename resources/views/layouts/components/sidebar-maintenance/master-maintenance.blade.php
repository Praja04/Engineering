@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if (in_array($jabatan, ['operator', 'foreman', 'supervisor', 'dept_head']) &&
        in_array($bagian, ['Engineering', 'Engineering Utility']))
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
                        <i class="mdi mdi-view-grid"></i>Master Mesin</a>
                    {{-- <a href="#" data-bs-target="#sidebarFormMtc" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ request()->is('mtc/form/*') ? 'true' : 'false' }}"
                        aria-controls="sidebarFormMtc" class="nav-link" data-key="t-m-mtc">
                        <i class="mdi mdi-view-grid"></i>Form Mtc
                    </a> --}}
                    {{-- <div class="collapse menu-dropdown {{ request()->is('mtc/form/*') ? 'show' : '' }}"
                        id="sidebarFormMtc">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('mtc.motor-pump.index') }}"
                                    class="nav-link {{ request()->routeIs('mtc.motor-pump.index') ? 'active' : '' }}">
                                    <i class="mdi mdi-file-document"></i>Form Motor Pump</a>
                            </li>
                        </ul>
                    </div> --}}
                </li>
            </ul>
        </div>
    </li>
@endif
