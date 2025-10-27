@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;
@endphp

@if ($jabatan != 'operator' && $bagian == 'Engineering')
    <li class="nav-item">
        <a href="{{ route('boiler.dashboard') }}"
            class="nav-link menu-link {{ request()->routeIs('boiler.dashboard') ? 'active' : '' }}">
            <i class="mdi mdi-cog"></i> <span data-key="t-albras">Dashboard Boiler</span>
        </a>
    </li>
@endif
