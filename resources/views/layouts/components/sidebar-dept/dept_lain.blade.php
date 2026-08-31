 <li class="nav-item">
     <a href="{{ url('mtc/approval/index') }}" class="nav-link menu-link">
         <i class="mdi mdi-checkbox-marked-circle-outline"></i> <span data-key="mtc">Approval MTC</span>
     </a>
 </li>

 <li class="nav-item">
     <a class="nav-link menu-link {{ request()->routeIs('kalibrasi.*') ? '' : 'collapsed' }}" href="#sideBarPressure"
         data-bs-toggle="collapse" role="button"
         aria-expanded="{{ request()->routeIs('kalibrasi.*') ? 'true' : 'false' }}" aria-controls="sideBarPressure">
         <i class="mdi mdi-ruler-square"></i> <span data-key="t-kalibrasi">Kalibrasi</span>
     </a>
     <div class="collapse menu-dropdown {{ request()->routeIs('kalibrasi.*') ? 'show' : '' }}" id="sideBarPressure">
         <ul class="nav nav-sm flex-column">
             <li class="nav-item">
                 <a href="{{ route('kalibrasi.certificate') }}"
                     class="nav-link {{ request()->routeIs('kalibrasi.certificate') ? 'active' : '' }}"
                     data-key="t-tkbm">
                     <i class="mdi mdi-certificate"></i>Cetificate</a>
             </li>
             <li class="nav-item">
                 <a href="{{ route('kalibrasi.sticker') }}"
                     class="nav-link {{ request()->routeIs('kalibrasi.sticker') ? 'active' : '' }}" data-key="t-tkbm">
                     <i class="mdi mdi-sticker"></i>Sticker</a>
             </li>
             @if ($jabatan != 'operator')
                 <li class="nav-item">
                     <a href="{{ route('kalibrasi.certificate.approvals') }}"
                         class="nav-link {{ request()->routeIs(['kalibrasi.certificate.approvals', 'kalibrasi.certificate.approval.detail']) ? 'active' : '' }}"
                         data-key="t-tkbm">
                         <i class="mdi mdi-check-decagram"></i>Approval</a>
                 </li>
             @endif
         </ul>
     </div>
 </li>
 <li class="nav-item">
     <a class="nav-link menu-link" href="{{ url('/ejo-engineer') }}">
         <i class="mdi mdi-cogs"></i>
         <span data-key="t-ejo-engineer">EJO Portal</span>
     </a>
 </li>
