@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if ($jabatan != 'operator' && in_array($bagian, ['Engineering', 'Engineering Kalibrasi']))
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->routeIs('master.kalibrasi.*') ? '' : 'collapsed' }}"
            href="#sidebarMasterKalibrasi" data-bs-toggle="collapse" role="button"
            aria-expanded="{{ request()->routeIs('master.kalibrasi.*') ? 'true' : 'false' }}"
            aria-controls="sidebarMasterKalibrasi">
            <i class="mdi mdi-book-cog"></i> <span data-key="t-kalibrasi">Kalibrasi</span>
        </a>
        <div class="collapse menu-dropdown {{ request()->routeIs('master.kalibrasi.*') ? 'show' : '' }}"
            id="sidebarMasterKalibrasi">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a href="{{ route('master.kalibrasi.alat') }}"
                        class="nav-link {{ request()->routeIs('master.kalibrasi.alat') ? 'active' : '' }}">
                        <i class="mdi mdi-book-cog"></i> <span data-key="t-albras">Alat Kalibrasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('master.kalibrasi.jangka_sorong') }}"
                        class="nav-link {{ request()->routeIs('master.kalibrasi.jangka_sorong') ? 'active' : '' }}"
                        data-key="t-tkbm">
                        <i class="mdi mdi-book-open"></i>Jangka Sorong</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('master.kalibrasi.timbangan') }}"
                        class="nav-link {{ request()->routeIs('master.kalibrasi.timbangan') ? 'active' : '' }}"
                        data-key="t-tkbm">
                        <i class="mdi mdi-book-open"></i>Timbangan</a>
                </li>
            </ul>
        </div>

    </li>
@endif
