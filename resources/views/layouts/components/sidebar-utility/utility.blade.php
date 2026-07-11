@php
    $jabatan = Auth::user()->jabatan;
    $bagian = Auth::user()->bagian;

    $isUtility = request()->is('utility/form') || request()->is('utility/data') || request()->is('utility/approval');

    $isEsp =
        request()->is('utility/esp-operational-report*') ||
        request()->is('utility/esp-shift-report*') ||
        request()->is('utility/esp-coal-handover*');
    $isWs = request()->is('utility/water-softener*');
    $isGenset = request()->is('utility/warming-up-genset*');
    $isCompressor = request()->is('utility/compressor*');
    $isAhu = request()->is('utility/ahu*');
    $isMdp = request()->is('utility/mdp-monitoring*');
    $isCapacitorBank = request()->is('utility/capacitor-bank*');
    $isBoilerLog = request()->is('utility/boiler-logs*');
    $isCoolingTower = request()->is('utility/cooling-tower*');
    $isReverseOsmosis = request()->is('utility/reverse-osmosis*');
    $isAgendaRoWs = request()->is('utility/agenda-ro-ws*');
    $isAgendaTankFarm = request()->is('utility/agenda-tank-farm*');
    $isAgendaAhu = request()->is('utility/agenda-ahu*');
    $isPemantauanPompa = request()->is('utility/pemantauan-pompa-utility*');
    $isAgendaCoolingTower = request()->is('utility/agenda-cooling-tower*');
    $isAgendaCompressor = request()->is('utility/agenda-compressor*');
    $isAnalisisUtility = request()->is('utility/analisis-utility*');

    $isOperasional =
        $isEsp ||
        $isCapacitorBank ||
        $isWs ||
        $isGenset ||
        $isCompressor ||
        $isCoolingTower ||
        $isReverseOsmosis ||
        $isAgendaRoWs ||
        $isAgendaTankFarm ||
        $isAgendaAhu ||
        $isPemantauanPompa ||
        $isAgendaCoolingTower ||
        $isAgendaCompressor ||
        $isAnalisisUtility ||
        $isAhu ||
        $isMdp ||
        $isBoilerLog;
@endphp

@if (
    $jabatan === 'admin' ||
        $jabatan === 'dept_head' ||
        $jabatan === 'supervisor' ||
        ($jabatan === 'operator' && in_array($bagian, ['Engineering', 'Engineering WWTP'])) ||
        ($jabatan === 'foreman' && in_array($bagian, ['Engineering', 'Engineering WWTP'])))

    {{-- ================= UTILITY ================= --}}
    <li class="nav-item">
        <a class="nav-link menu-link {{ $isUtility ? '' : 'collapsed' }}" href="#sidebarUtilityMenu"
            data-bs-toggle="collapse">
            <i class="mdi mdi-flash"></i>
            <span>Utility</span>
        </a>

        <div class="collapse menu-dropdown {{ $isUtility ? 'show' : '' }}" id="sidebarUtilityMenu">
            <ul class="nav nav-sm flex-column">

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('utility/form') ? 'active' : '' }}"
                        href="{{ url('utility/form') }}">
                        <i class="mdi mdi-file-document-edit-outline"></i>
                        <span>Form Utility</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('utility/data') ? 'active' : '' }}"
                        href="{{ url('utility/data') }}">
                        <i class="mdi mdi-database-eye-outline"></i>
                        <span>Data Utility</span>
                    </a>
                </li>

                @if ($jabatan != 'operator')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('utility/approval') ? 'active' : '' }}"
                            href="{{ url('utility/approval') }}">
                            <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                            <span>Approval Utility</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </li>

    {{-- ================= OPERASIONAL ================= --}}
    <li class="nav-item">
        <a class="nav-link menu-link {{ $isOperasional ? '' : 'collapsed' }}" href="#sidebarOperasionalMenu"
            aria-expanded="{{ $isOperasional ? 'true' : 'false' }}" data-bs-toggle="collapse">
            <i class="mdi mdi-file-chart"></i>
            <span>Operasional</span>
        </a>

        <div class="collapse menu-dropdown {{ $isOperasional ? 'show' : '' }}" id="sidebarOperasionalMenu">
            <ul class="nav nav-sm flex-column">
                {{-- AHU --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isAhu ? '' : 'collapsed' }}" href="#ahuMenu"
                        aria-expanded="{{ $isAhu ? 'true' : 'false' }}" data-bs-toggle="collapse">
                        <span>AHU</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isAhu ? 'show' : '' }}" id="ahuMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/ahu') ? 'active' : '' }}"
                                    href="{{ url('utility/ahu/') }}">
                                    Form AHU
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/ahu/data') ? 'active' : '' }}"
                                    href="{{ url('utility/ahu/data') }}">
                                    Data AHU
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/ahu/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/ahu/approval') }}">
                                        Approval AHU
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Capacitor Bank --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isCapacitorBank ? '' : 'collapsed' }}" href="#capacitorBankMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isCapacitorBank ? 'true' : 'false' }}">
                        <span>Capacitor Bank</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isCapacitorBank ? 'show' : '' }}" id="capacitorBankMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/capacitor-bank') ? 'active' : '' }}"
                                    href="{{ url('utility/capacitor-bank') }}">
                                    Form Capacitor Bank
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/capacitor-bank/rekap') ? 'active' : '' }}"
                                    href="{{ url('utility/capacitor-bank/rekap') }}">
                                    Data Capacitor Bank
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/capacitor-bank/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/capacitor-bank/approval') }}">
                                        Approval Capacitor Bank
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Compressor --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isCompressor ? '' : 'collapsed' }}" href="#compressorMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isCompressor ? 'true' : 'false' }}">
                        <span>Compressor</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isCompressor ? 'show' : '' }}" id="compressorMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/compressor') ? 'active' : '' }}"
                                    href="{{ url('utility/compressor/') }}">
                                    Form Compressor
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/compressor/data') ? 'active' : '' }}"
                                    href="{{ url('utility/compressor/data') }}">
                                    Data Compressor
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/compressor/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/compressor/approval') }}">
                                        Approval Compressor
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- ===== ESP ===== --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isEsp ? '' : 'collapsed' }}" href="#espMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isEsp ? 'true' : 'false' }}">
                        <span>ESP</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isEsp ? 'show' : '' }}" id="espMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/esp-operational-report') ? 'active' : '' }}"
                                    href="{{ url('utility/esp-operational-report/') }}">
                                    Form ESP
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/esp-operational-report/data') ? 'active' : '' }}"
                                    href="{{ url('utility/esp-operational-report/data') }}">
                                    Data ESP
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/esp-shift-report/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/esp-shift-report/approval') }}">
                                        Approval ESP
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- MDP Monitoring --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isMdp ? '' : 'collapsed' }}" href="#mdpMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isMdp ? 'true' : 'false' }}">
                        <span>MDP Monitoring</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isMdp ? 'show' : '' }}" id="mdpMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/mdp-monitoring') ? 'active' : '' }}"
                                    href="{{ url('utility/mdp-monitoring/') }}">
                                    Form MDP
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/mdp-monitoring/data') ? 'active' : '' }}"
                                    href="{{ url('utility/mdp-monitoring/data') }}">
                                    Data MDP
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/mdp-monitoring/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/mdp-monitoring/approval') }}">
                                        Approval MDP
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Boiler Logs --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isBoilerLog ? '' : 'collapsed' }}" href="#boilerLogMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isBoilerLog ? 'true' : 'false' }}">
                        <span>Boiler Logs</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isBoilerLog ? 'show' : '' }}" id="boilerLogMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/boiler-logs/data') ? 'active' : '' }}"
                                    href="{{ url('utility/boiler-logs/data') }}">
                                    Data Boiler
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/boiler-logs/form') ? 'active' : '' }}"
                                    href="{{ url('utility/boiler-logs/form') }}">
                                    Form Boiler
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/boiler-logs/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/boiler-logs/approval') }}">
                                        Approval Boiler
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- ===== WATER SOFTENER ===== --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isWs ? '' : 'collapsed' }}" href="#wsMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isWs ? 'true' : 'false' }}">
                        <span>WS (Water Softener)</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isWs ? 'show' : '' }}" id="wsMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/water-softener') ? 'active' : '' }}"
                                    href="{{ url('utility/water-softener/') }}">
                                    Form WS
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/water-softener/rekap') ? 'active' : '' }}"
                                    href="{{ url('utility/water-softener/rekap') }}">
                                    Data WS
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/water-softener/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/water-softener/approval') }}">
                                        Approval WS
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Warming Up Genset --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isGenset ? '' : 'collapsed' }}" href="#gensetMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isGenset ? 'true' : 'false' }}">
                        <span>Warming Up Genset</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isGenset ? 'show' : '' }}" id="gensetMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/warming-up-genset') ? 'active' : '' }}"
                                    href="{{ url('utility/warming-up-genset/') }}">
                                    Form Genset
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/warming-up-genset/data') ? 'active' : '' }}"
                                    href="{{ url('utility/warming-up-genset/data') }}">
                                    Data Genset
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/warming-up-genset/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/warming-up-genset/approval') }}">
                                        Approval Genset
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Cooling Tower --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isCoolingTower ? '' : 'collapsed' }}" href="#coolingTowerMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isCoolingTower ? 'true' : 'false' }}">
                        <span>Cooling Tower</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isCoolingTower ? 'show' : '' }}" id="coolingTowerMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/cooling-tower') ? 'active' : '' }}"
                                    href="{{ url('utility/cooling-tower/') }}">
                                    Form Cooling Tower
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/cooling-tower/data') ? 'active' : '' }}"
                                    href="{{ url('utility/cooling-tower/data') }}">
                                    Data Cooling Tower
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/cooling-tower/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/cooling-tower/approval') }}">
                                        Approval Cooling Tower
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Reverse Osmosis --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isReverseOsmosis ? '' : 'collapsed' }}"
                        href="#reverseOsmosisMenu" data-bs-toggle="collapse"
                        aria-expanded="{{ $isReverseOsmosis ? 'true' : 'false' }}">
                        <span>Reverse Osmosis</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isReverseOsmosis ? 'show' : '' }}"
                        id="reverseOsmosisMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/reverse-osmosis') ? 'active' : '' }}"
                                    href="{{ url('utility/reverse-osmosis/') }}">
                                    Form Reverse Osmosis
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/reverse-osmosis/data') ? 'active' : '' }}"
                                    href="{{ url('utility/reverse-osmosis/data') }}">
                                    Data Reverse Osmosis
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/reverse-osmosis/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/reverse-osmosis/approval') }}">
                                        Approval Reverse Osmosis
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Pemantauan Pompa Utility --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isPemantauanPompa ? '' : 'collapsed' }}"
                        href="#pemantauanPompaMenu" data-bs-toggle="collapse"
                        aria-expanded="{{ $isPemantauanPompa ? 'true' : 'false' }}">
                        <span>Pemantauan Pompa</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isPemantauanPompa ? 'show' : '' }}"
                        id="pemantauanPompaMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/pemantauan-pompa-utility') ? 'active' : '' }}"
                                    href="{{ url('utility/pemantauan-pompa-utility/') }}">
                                    Form Pemantauan Pompa
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/pemantauan-pompa-utility/data') ? 'active' : '' }}"
                                    href="{{ url('utility/pemantauan-pompa-utility/data') }}">
                                    Data Pemantauan Pompa
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/pemantauan-pompa-utility/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/pemantauan-pompa-utility/approval') }}">
                                        Approval Pemantauan Pompa
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Agenda RO-WS --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isAgendaRoWs ? '' : 'collapsed' }}" href="#agendaRoWsMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isAgendaRoWs ? 'true' : 'false' }}">
                        <span>Agenda RO-WS</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isAgendaRoWs ? 'show' : '' }}" id="agendaRoWsMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-ro-ws') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-ro-ws/') }}">
                                    Form Agenda RO-WS
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-ro-ws/data') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-ro-ws/data') }}">
                                    Data Agenda RO-WS
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/agenda-ro-ws/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/agenda-ro-ws/approval') }}">
                                        Approval Agenda RO-WS
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Agenda Tank Farm & Hydrant --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isAgendaTankFarm ? '' : 'collapsed' }}"
                        href="#agendaTankFarmMenu" data-bs-toggle="collapse"
                        aria-expanded="{{ $isAgendaTankFarm ? 'true' : 'false' }}">
                        <span>Agenda Tank Farm & Hydrant</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isAgendaTankFarm ? 'show' : '' }}"
                        id="agendaTankFarmMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-tank-farm') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-tank-farm/') }}">
                                    Form Agenda TF-HY
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-tank-farm/data') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-tank-farm/data') }}">
                                    Data Agenda TF-HY
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/agenda-tank-farm/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/agenda-tank-farm/approval') }}">
                                        Approval Agenda TF-HY
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Agenda AHU --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isAgendaAhu ? '' : 'collapsed' }}" href="#agendaAhuMenu"
                        data-bs-toggle="collapse" aria-expanded="{{ $isAgendaAhu ? 'true' : 'false' }}">
                        <span>Agenda AHU</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isAgendaAhu ? 'show' : '' }}" id="agendaAhuMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-ahu') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-ahu/') }}">
                                    Form Agenda AHU
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-ahu/data') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-ahu/data') }}">
                                    Data Agenda AHU
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/agenda-ahu/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/agenda-ahu/approval') }}">
                                        Approval Agenda AHU
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Agenda Cooling Tower --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isAgendaCoolingTower ? '' : 'collapsed' }}"
                        href="#agendaCoolingTowerMenu" data-bs-toggle="collapse"
                        aria-expanded="{{ $isAgendaCoolingTower ? 'true' : 'false' }}">
                        <span>Agenda Cooling Tower</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isAgendaCoolingTower ? 'show' : '' }}"
                        id="agendaCoolingTowerMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-cooling-tower') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-cooling-tower/') }}">
                                    Form Agenda CT
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-cooling-tower/data') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-cooling-tower/data') }}">
                                    Data Agenda CT
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/agenda-cooling-tower/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/agenda-cooling-tower/approval') }}">
                                        Approval Agenda CT
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Agenda Compressor --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isAgendaCompressor ? '' : 'collapsed' }}"
                        href="#agendaCompressorMenu" data-bs-toggle="collapse"
                        aria-expanded="{{ $isAgendaCompressor ? 'true' : 'false' }}">
                        <span>Agenda Compressor</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isAgendaCompressor ? 'show' : '' }}"
                        id="agendaCompressorMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-compressor') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-compressor/') }}">
                                    Form Agenda Compressor
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/agenda-compressor/data') ? 'active' : '' }}"
                                    href="{{ url('utility/agenda-compressor/data') }}">
                                    Data Agenda Compressor
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/agenda-compressor/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/agenda-compressor/approval') }}">
                                        Approval Agenda Compressor
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                {{-- Analisis Utility --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isAnalisisUtility ? '' : 'collapsed' }}"
                        href="#analisisUtilityMenu" data-bs-toggle="collapse"
                        aria-expanded="{{ $isAnalisisUtility ? 'true' : 'false' }}">
                        <span>Analisis Utility</span>
                    </a>

                    <div class="collapse menu-dropdown {{ $isAnalisisUtility ? 'show' : '' }}"
                        id="analisisUtilityMenu">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/analisis-utility') ? 'active' : '' }}"
                                    href="{{ url('utility/analisis-utility/') }}">
                                    Form Analisis Utility
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('utility/analisis-utility/data') ? 'active' : '' }}"
                                    href="{{ url('utility/analisis-utility/data') }}">
                                    Data Analisis Utility
                                </a>
                            </li>

                            @if ($jabatan != 'operator')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('utility/analisis-utility/approval') ? 'active' : '' }}"
                                        href="{{ url('utility/analisis-utility/approval') }}">
                                        Approval Analisis Utility
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
