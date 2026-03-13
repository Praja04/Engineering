@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;

$ejoMenu = [
[
'type' => 'Drawing',
'classifications' => [
['id' => 1, 'name' => 'Sipil'],
['id' => 2, 'name' => 'Mekanik'],
],
],
[
'type' => 'Project',
'classifications' => [
['id' => 3, 'name' => 'Mekanik'],
['id' => 4, 'name' => 'Sipil'],
['id' => 5, 'name' => 'Maintenance / Improvement'],
['id' => 6, 'name' => 'Repair Part'],
],
],
];
@endphp

@if (
$jabatan === 'dept_head' ||
$jabatan === 'supervisor' ||
($jabatan === 'operator' && in_array($bagian, ['Engineering', 'Engineering Workshop & Project'])) ||
($jabatan === 'foreman' && in_array($bagian, ['Engineering', 'Engineering Workshop & Project']))
)

<li class="nav-item">
    {{-- Parent: EJO --}}
    <a class="nav-link menu-link {{ request()->is('ejo*') ? '' : 'collapsed' }}" href="#sidebarEjoMenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('ejo*') ? 'true' : 'false' }}" aria-controls="sidebarEjoMenu">
        <i class="mdi mdi-clipboard-text-outline"></i>
        <span data-key="t-ejo">EJO</span>
    </a>

    <div class="collapse menu-dropdown {{ request()->is('ejo*') ? 'show' : '' }}" id="sidebarEjoMenu">
        <ul class="nav nav-sm flex-column">

            {{-- Dashboard EJO --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('ejo/dashboard') ? 'active' : '' }}" href="{{ url('ejo/dashboard') }}">
                    <i class="mdi mdi-view-dashboard-outline"></i>
                    <span data-key="t-ejo-dashboard">Dashboard</span>
                </a>
            </li>

            {{-- Semua EJO --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('ejo') && !request()->has('classification') ? 'active' : '' }}" href="{{ url('ejo') }}">
                    <i class="mdi mdi-view-list-outline"></i>
                    <span data-key="t-ejo-all">Semua EJO</span>
                </a>
            </li>

            {{-- Loop per Type (Drawing, Project, dll) --}}
            @foreach ($ejoMenu as $index => $type)
            <li class="nav-item">
                @php
                $typeActive = collect($type['classifications'])
                ->contains(fn($c) => request('classification') == $c['id']);
                @endphp

                <a class="nav-link menu-link {{ $typeActive ? '' : 'collapsed' }}" href="#sidebarEjo{{ $index }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $typeActive ? 'true' : 'false' }}" aria-controls="sidebarEjo{{ $index }}">
                    <i class="mdi mdi-folder-outline"></i>
                    <span>{{ $type['type'] }}</span>
                </a>

                <div class="collapse menu-dropdown {{ $typeActive ? 'show' : '' }}" id="sidebarEjo{{ $index }}">
                    <ul class="nav nav-sm flex-column">

                        @foreach ($type['classifications'] as $class)
                        <li class="nav-item">
                            <a class="nav-link {{ request('classification') == $class['id'] ? 'active' : '' }}" href="{{ url('ejo') }}?classification={{ $class['id'] }}">
                                <i class="mdi mdi-circle-small"></i>
                                <span>{{ $class['name'] }}</span>
                            </a>
                        </li>
                        @endforeach

                    </ul>
                </div>
            </li>
            @endforeach

        </ul>
    </div>
</li>

@endif