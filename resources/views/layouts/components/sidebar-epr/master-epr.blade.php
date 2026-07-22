@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if ($jabatan != 'operator' && in_array($bagian, ['IT', 'Engineering', 'Engineering Produksi (EPR)']))
<li class="nav-item">
    <a class="nav-link menu-link {{ request()->routeIs('epr.master.*') ? '' : 'collapsed' }}"
        href="#sidebarMasterEpr" data-bs-toggle="collapse" role="button"
        aria-expanded="{{ request()->routeIs('epr.master.*') ? 'true' : 'false' }}"
        aria-controls="sidebarMasterEpr">
        <i class="mdi mdi-book-cog"></i> <span data-key="t-master-epr">EPR (Produksi)</span>
    </a>
    <div class="collapse menu-dropdown {{ request()->routeIs('epr.master.*') ? 'show' : '' }}"
        id="sidebarMasterEpr">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('epr.master.jenis-dt.index') }}"
                    class="nav-link {{ request()->routeIs('epr.master.jenis-dt.*') ? 'active' : '' }}">
                    <i class="mdi mdi-book-open"></i> <span>Jenis DT</span>
                </a>
            </li>
        </ul>
    </div>
</li>
@endif
