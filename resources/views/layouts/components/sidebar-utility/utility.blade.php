@php
$jabatan = Auth::user()->jabatan;
$bagian = Auth::user()->bagian;

$isUtility = request()->is('utility/form') || request()->is('utility/data');

$isEsp = request()->is('utility/esp-operational-report*');
$isWs = request()->is('utility/water-softener*');

$isOperasional = $isEsp || $isWs;
@endphp

@if (
$jabatan === 'dept_head' ||
$jabatan === 'supervisor' ||
($jabatan === 'operator' && in_array($bagian, ['Engineering', 'Engineering WWTP'])) ||
($jabatan === 'foreman' && in_array($bagian, ['Engineering', 'Engineering WWTP']))
)

{{-- ================= UTILITY ================= --}}
<li class="nav-item">
    <a class="nav-link menu-link {{ $isUtility ? '' : 'collapsed' }}" href="#sidebarUtilityMenu" data-bs-toggle="collapse">
        <i class="mdi mdi-flash"></i>
        <span>Utility</span>
    </a>

    <div class="collapse menu-dropdown {{ $isUtility ? 'show' : '' }}" id="sidebarUtilityMenu">
        <ul class="nav nav-sm flex-column">

            <li class="nav-item">
                <a class="nav-link {{ request()->is('utility/form') ? 'active' : '' }}" href="{{ url('utility/form') }}">
                    <i class="mdi mdi-file-document-edit-outline"></i>
                    <span>Form Utility</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('utility/data') ? 'active' : '' }}" href="{{ url('utility/data') }}">
                    <i class="mdi mdi-database-eye-outline"></i>
                    <span>Data Utility</span>
                </a>
            </li>

        </ul>
    </div>
</li>

{{-- ================= OPERASIONAL ================= --}}
<li class="nav-item">
    <a class="nav-link menu-link {{ $isOperasional ? '' : 'collapsed' }}" href="#sidebarOperasionalMenu" data-bs-toggle="collapse">
        <i class="mdi mdi-file-chart"></i>
        <span>Operasional</span>
    </a>

    <div class="collapse menu-dropdown {{ $isOperasional ? 'show' : '' }}" id="sidebarOperasionalMenu">
        <ul class="nav nav-sm flex-column">

            {{-- ===== ESP ===== --}}
            <li class="nav-item">
                <a class="nav-link menu-link {{ $isEsp ? '' : 'collapsed' }}" href="#espMenu" data-bs-toggle="collapse">
                    <span>ESP</span>
                </a>

                <div class="collapse menu-dropdown {{ $isEsp ? 'show' : '' }}" id="espMenu">
                    <ul class="nav nav-sm flex-column">

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('utility/esp-operational-report/form') ? 'active' : '' }}" href="{{ url('utility/esp-operational-report/') }}">
                                Form ESP
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('utility/esp-operational-report/data') ? 'active' : '' }}" href="{{ url('utility/esp-operational-report/data') }}">
                                Data ESP
                            </a>
                        </li>

                        @if ($jabatan != 'operator')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('utility/esp-shift-report/approval') ? 'active' : '' }}" href="{{ url('utility/esp-shift-report/approval') }}">
                                Approval ESP
                            </a>
                        </li>
                        @endif

                    </ul>
                </div>
            </li>

            {{-- ===== WATER SOFTENER ===== --}}
            <li class="nav-item">
                <a class="nav-link menu-link {{ $isWs ? '' : 'collapsed' }}" href="#wsMenu" data-bs-toggle="collapse">
                    <span>WS (Water Softener)</span>
                </a>

                <div class="collapse menu-dropdown {{ $isWs ? 'show' : '' }}" id="wsMenu">
                    <ul class="nav nav-sm flex-column">

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('utility/water-softener') ? 'active' : '' }}" href="{{ url('utility/water-softener/') }}">
                                Form WS
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('utility/water-softener/rekap') ? 'active' : '' }}" href="{{ url('utility/water-softener/rekap') }}">
                                Data WS
                            </a>
                        </li>

                        @if ($jabatan != 'operator')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('utility/water-softener/approval') ? 'active' : '' }}" href="{{ url('utility/water-softener/approval') }}">
                                Approval WS
                            </a>
                        </li>
                        @endif

                    </ul>
                </div>
            </li>

        </ul>
    </div>
</li>

@endif