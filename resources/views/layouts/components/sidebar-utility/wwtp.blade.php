@can('permission', 'wwtp_menu')
    <li class="nav-item">
        <a class="nav-link menu-link {{ request()->is('wwtp/*') ? '' : 'collapsed' }}" href="#sidebarWWTP"
            data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('wwtp/*') ? 'true' : 'false' }}"
            aria-controls="sidebarWWTP">
            <i class="mdi mdi-water-outline"></i>
            <span data-key="t-dashboards2">WWTP</span>
        </a>

        <div class="collapse menu-dropdown {{ request()->is('wwtp/*') ? 'show' : '' }}" id="sidebarWWTP">
            <ul class="nav nav-sm flex-column">
                <!-- Proses WWTP -->
                @can('permission', 'wwtp_proses_menu')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('wwtp/proses') || request()->is('wwtp/form_proses') || request()->is('wwtp/data_proses') ? '' : 'collapsed' }}"
                            href="#sidebarProsesWWTP" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->is('wwtp/proses') || request()->is('wwtp/form_proses') || request()->is('wwtp/data_proses') ? 'true' : 'false' }}"
                            aria-controls="sidebarProsesWWTP">
                            <i class="mdi mdi-water-pump"></i>
                            <span data-key="t-widgets">Proses</span>
                        </a>

                        <div class="collapse menu-dropdown {{ request()->is('wwtp/form_proses') || request()->is('wwtp/data_proses') ? 'show' : '' }}"
                            id="sidebarProsesWWTP">
                            <ul class="nav nav-sm flex-column">
                                @can('permission', 'wwtp_form_proses')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/form_proses') ? 'active' : '' }}"
                                            href="{{ url('wwtp/form_proses') }}">
                                            <i class="mdi mdi-file-document-edit-outline"></i>
                                            <span data-key="t-widgets">Form Proses</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('permission', 'wwtp_data_proses')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/data_proses') ? 'active' : '' }}"
                                            href="{{ url('wwtp/data_proses') }}">
                                            <i class="mdi mdi-database"></i>
                                            <span data-key="t-widgets">Data Proses</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                <!-- Performance WWTP -->
                @can('permission', 'wwtp_performance_menu')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('wwtp/performance') || request()->is('wwtp/form_performance') || request()->is('wwtp/data_performance') ? '' : 'collapsed' }}"
                            href="#sidebarPerformanceWWTP" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->is('wwtp/performance') || request()->is('wwtp/form_performance') || request()->is('wwtp/data_performance') ? 'true' : 'false' }}"
                            aria-controls="sidebarPerformanceWWTP">
                            <i class="mdi mdi-chart-line"></i>
                            <span data-key="t-widgets">Performance</span>
                        </a>

                        <div class="collapse menu-dropdown {{ request()->is('wwtp/form_performance') || request()->is('wwtp/data_performance') ? 'show' : '' }}"
                            id="sidebarPerformanceWWTP">
                            <ul class="nav nav-sm flex-column">
                                @can('permission', 'wwtp_form_performance')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/form_performance') ? 'active' : '' }}"
                                            href="{{ url('wwtp/form_performance') }}">
                                            <i class="mdi mdi-file-document-edit-outline"></i>
                                            <span data-key="t-widgets">Form Performance</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('permission', 'wwtp_data_performance')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/data_performance') ? 'active' : '' }}"
                                            href="{{ url('wwtp/data_performance') }}">
                                            <i class="mdi mdi-database"></i>
                                            <span data-key="t-widgets">Data Performance</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                <!-- Sludge WWTP -->
                @can('permission', 'wwtp_sludge_menu')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('wwtp/sludge') || request()->is('wwtp/form_sludge') || request()->is('wwtp/data_sludge') ? '' : 'collapsed' }}"
                            href="#sidebarSludgeWWTP" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->is('wwtp/sludge') || request()->is('wwtp/form_sludge') || request()->is('wwtp/data_sludge') ? 'true' : 'false' }}"
                            aria-controls="sidebarSludgeWWTP">
                            <i class="mdi mdi-recycle"></i>
                            <span data-key="t-widgets">Sludge</span>
                        </a>

                        <div class="collapse menu-dropdown {{ request()->is('wwtp/form_sludge') || request()->is('wwtp/data_sludge') ? 'show' : '' }}"
                            id="sidebarSludgeWWTP">
                            <ul class="nav nav-sm flex-column">
                                @can('permission', 'wwtp_form_sludge')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/form_sludge') ? 'active' : '' }}"
                                            href="{{ url('wwtp/form_sludge') }}">
                                            <i class="mdi mdi-file-document-edit-outline"></i>
                                            <span data-key="t-widgets">Form Sludge</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('permission', 'wwtp_data_sludge')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/data_sludge') ? 'active' : '' }}"
                                            href="{{ url('wwtp/data_sludge') }}">
                                            <i class="mdi mdi-database"></i>
                                            <span data-key="t-widgets">Data Sludge</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                <!-- Approval WWTP -->
                @can('permission', 'wwtp_daily_approval')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('wwtp/approval') ? 'active' : '' }}"
                            href="{{ url('wwtp/approval') }}">
                            <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                            <span data-key="t-widgets">Daily Approval</span>
                        </a>
                    </li>
                @endcan

                <!-- Analisa WWTP -->
                @can('permission', 'wwtp_analisa_menu')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('wwtp/analisa') || request()->is('wwtp/form_analisa') || request()->is('wwtp/data_analisa') || request()->is('wwtp/manage_standar_analisa') || request()->is('wwtp/analisa/approval') ? '' : 'collapsed' }}"
                            href="#sidebarAnalisaWWTP" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->is('wwtp/analisa') || request()->is('wwtp/form_analisa') || request()->is('wwtp/data_analisa') || request()->is('wwtp/manage_standar_analisa') || request()->is('wwtp/analisa/approval') ? 'true' : 'false' }}"
                            aria-controls="sidebarAnalisaWWTP">
                            <i class="mdi mdi-flask-outline"></i>
                            <span data-key="t-widgets">Analisa WWTP</span>
                        </a>

                        <div class="collapse menu-dropdown {{ request()->is('wwtp/form_analisa') || request()->is('wwtp/data_analisa') || request()->is('wwtp/manage_standar_analisa') || request()->is('wwtp/analisa/approval') ? 'show' : '' }}"
                            id="sidebarAnalisaWWTP">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('wwtp/form_analisa') ? 'active' : '' }}"
                                        href="{{ url('wwtp/form_analisa') }}">
                                        <i class="mdi mdi-file-document-edit-outline"></i>
                                        <span data-key="t-widgets">Form</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('wwtp/data_analisa') ? 'active' : '' }}"
                                        href="{{ url('wwtp/data_analisa') }}">
                                        <i class="mdi mdi-database"></i>
                                        <span data-key="t-widgets">Data</span>
                                    </a>
                                </li>
                                @can('permission', 'wwtp_analisa_approval')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/analisa/approval') ? 'active' : '' }}"
                                            href="{{ url('wwtp/analisa/approval') }}">
                                            <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                                            <span data-key="t-widgets">Approval</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/manage_standar_analisa') ? 'active' : '' }}"
                                            href="{{ url('wwtp/manage_standar_analisa') }}">
                                            <i class="mdi mdi-database-cog-outline"></i>
                                            <span data-key="t-widgets">Master Data</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                <!-- Koloni WWTP -->
                @can('permission', 'wwtp_koloni_menu')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('wwtp/form_koloni') || request()->is('wwtp/data_koloni') || request()->is('wwtp/master_koloni') ? '' : 'collapsed' }}"
                            href="#sidebarKoloniWWTP" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->is('wwtp/form_koloni') || request()->is('wwtp/data_koloni') || request()->is('wwtp/master_koloni') ? 'true' : 'false' }}"
                            aria-controls="sidebarKoloniWWTP">
                            <i class="mdi mdi-calculator-variant-outline"></i>
                            <span data-key="t-widgets">Koloni</span>
                        </a>

                        <div class="collapse menu-dropdown {{ request()->is('wwtp/form_koloni') || request()->is('wwtp/data_koloni') || request()->is('wwtp/master_koloni') ? 'show' : '' }}"
                            id="sidebarKoloniWWTP">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('wwtp/form_koloni') ? 'active' : '' }}"
                                        href="{{ url('wwtp/form_koloni') }}">
                                        <i class="mdi mdi-file-document-edit-outline"></i>
                                        <span data-key="t-widgets">Form Koloni</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('wwtp/data_koloni') ? 'active' : '' }}"
                                        href="{{ url('wwtp/data_koloni') }}">
                                        <i class="mdi mdi-database"></i>
                                        <span data-key="t-widgets">Data Koloni</span>
                                    </a>
                                </li>
                                @if ($jabatan != 'operator')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/master_koloni') ? 'active' : '' }}"
                                            href="{{ url('wwtp/master_koloni') }}">
                                            <i class="mdi mdi-database-cog-outline"></i>
                                            <span data-key="t-widgets">Master Sample</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endcan

                <!-- Biaya Chemical WWTP -->
                @can('permission', 'wwtp_biaya_chemical_menu')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('wwtp/form_biaya_chemical') || request()->is('wwtp/data_biaya_chemical') || request()->is('wwtp/master_biaya_chemical') || request()->is('wwtp/form_pit_produksi*') ? '' : 'collapsed' }}"
                            href="#sidebarBiayaChemicalWWTP" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->is('wwtp/form_biaya_chemical') || request()->is('wwtp/data_biaya_chemical') || request()->is('wwtp/master_biaya_chemical') || request()->is('wwtp/form_pit_produksi*') ? 'true' : 'false' }}"
                            aria-controls="sidebarBiayaChemicalWWTP">
                            <i class="mdi mdi-cash-multiple"></i>
                            <span data-key="t-widgets">Biaya Chemical</span>
                        </a>

                        <div class="collapse menu-dropdown {{ request()->is('wwtp/form_biaya_chemical') || request()->is('wwtp/data_biaya_chemical') || request()->is('wwtp/master_biaya_chemical') || request()->is('wwtp/form_pit_produksi*') ? 'show' : '' }}"
                            id="sidebarBiayaChemicalWWTP">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('wwtp/form_pit_produksi*') ? 'active' : '' }}"
                                        href="{{ url('wwtp/form_pit_produksi') }}">
                                        <i class="mdi mdi-cube-outline"></i>
                                        <span data-key="t-widgets">Pit Produksi Harian</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('wwtp/form_biaya_chemical') ? 'active' : '' }}"
                                        href="{{ url('wwtp/form_biaya_chemical') }}">
                                        <i class="mdi mdi-file-document-edit-outline"></i>
                                        <span data-key="t-widgets">Form Biaya</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('wwtp/data_biaya_chemical') ? 'active' : '' }}"
                                        href="{{ url('wwtp/data_biaya_chemical') }}">
                                        <i class="mdi mdi-database"></i>
                                        <span data-key="t-widgets">Data Biaya</span>
                                    </a>
                                </li>
                                @if ($jabatan != 'operator')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('wwtp/master_biaya_chemical') ? 'active' : '' }}"
                                            href="{{ url('wwtp/master_biaya_chemical') }}">
                                            <i class="mdi mdi-database-cog-outline"></i>
                                            <span data-key="t-widgets">Master Harga</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endcan
            </ul>
        </div>
    </li>
@endcan
