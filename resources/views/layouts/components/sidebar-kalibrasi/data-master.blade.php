@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;
@endphp

@if (
($jabatan === 'dept_head') ||
($jabatan === 'supervisor') ||
($jabatan === 'foreman' && $bagian === 'Engineering Kalibrasi'))

<li class="nav-item">
    <a href="{{ route('master.alat') }}" class="nav-link menu-link {{ request()->routeIs('master.alat') ? 'active' : '' }}">
        <i class="mdi mdi-book-cog"></i> <span data-key="t-albras">Alat Kalibrasi</span>
    </a>
</li>

@endif