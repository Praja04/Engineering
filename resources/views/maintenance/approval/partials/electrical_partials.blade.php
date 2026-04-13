 @php
 $checklist = [
 'Panel' => [
 'check_kunci' => 'Check Kunci',
 'check_koneksi_kabel' => 'Check Koneksi Kabel',
 'check_wiring_panel' => 'Check Wiring Panel',
 'check_lampu_indikator' => 'Check Lampu Indikator',
 'check_name_plate' => 'Check Name Plate',
 'check_unit_electrical' => 'Check Unit Electrical',
 'check_grounding' => 'Check Grounding',
 'check_kebersihan' => 'Check Kebersihan',
 'check_bus_bar' => 'Check Bus Bar',
 'check_nilai_grounding' => 'Check Nilai Grounding',
 ],

 'Penerangan' => [
 'check_kondisi_lampu' => 'Check Kondisi Lampu',
 'check_cover_lampu' => 'Check Cover Lampu',
 'check_wiring_penerangan' => 'Check Wiring Penerangan',
 'check_saklar' => 'Check Saklar',
 'check_penyangga_penerangan' => 'Check Penyangga Penerangan',
 ],

 'Sistem Distribusi' => [
 'check_stecker' => 'Check Stecker',
 'check_stop_kontak' => 'Check Stop Kontak',
 'check_terminal_listrik' => 'Check Terminal Listrik',
 'check_pengabelan_distribusi' => 'Check Pengkabelan Distribusi',
 'check_support_pelindung_distribusi' => 'Check Support & Pelindung Distribusi',
 ],

 'Capacitor Bank' => [
 'check_kondisi_fisik_capacitor' => 'Check Kondisi Fisik Capacitor',
 'check_nilai_farad' => 'Check Nilai Farad',
 'check_nilai_ampere' => 'Check Nilai Ampere',
 'check_kebersihan_capacitor' => 'Check Kebersihan (Capacitor)',
 ],

 'Trafo' => [
 'check_kebocoran_oli_sisi_bawah' => 'Check Kebocoran Oli Sisi Bawah',
 'check_kebocoran_oli_sisi_atas' => 'Check Kebocoran Oli Sisi Atas',
 'check_level_oli' => 'Check Level Oli',
 ],
 ];

 function badge($v)
 {
 return $v === null
 ? '<span class="badge bg-secondary">No Check</span>'
 : ($v
 ? '<span class="badge bg-success">OK</span>'
 : '<span class="badge bg-danger">NG</span>');
 }
 @endphp

 <h5 class="mb-3">Mtc Electrical Inspection</h5>

 <table class="table table-sm table-borderless mb-4">
     <tr>
         <th width="160">Mesin</th>
         <td>: {{ $data->mesin->nama_mesin ?? '-' }}</td>
     </tr>
     <tr>
         <th>Lokasi</th>
         <td>: {{ $data->mesin->lokasi ?? '-' }}</td>
     </tr>
     <tr>
         <th>Departemen</th>
         <td>: {{ $main->departemen ?? '-' }}</td>
     </tr>
     <tr>
         <th>Tanggal</th>
         <td>: {{ $main->tanggal }}</td>
     </tr>
     <tr>
         <th>Waktu Mulai</th>
         <td>: {{ $main->waktu_mulai ?? '-' }}</td>
     </tr>
     <tr>
         <th>Waktu Selesai</th>
         <td>: {{ $main->waktu_selesai ?? '-' }}</td>
     </tr>
     <tr>
         <th>Paket</th>
         <td>: {{ $main->paket }}</td>
     </tr>
     <tr>
         <th>Korektif</th>
         <td>: {{ $main->korektif ?? '-' }}</td>
     </tr>
     <tr>
         <th>Keterangan NOK</th>
         <td>: {{ $main->keterangan ?? '-' }}</td>
     </tr>
 </table>

 <h6 class="mb-2">Checklist</h6>

 <table class="table table-sm table-bordered align-middle">
     <thead>
         <tr class="table-light text-center">
             <th width="120">Bagian</th>
             <th>Item</th>
             <th width="80">Status</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($checklist as $section => $items)
         @php $rowspan = count($items); @endphp
         @foreach ($items as $field => $label)
         <tr>
             @if ($loop->first)
             <td rowspan="{{ $rowspan }}" class="fw-bold align-middle text-center">
                 {{ $section }}
             </td>
             @endif
             <td>{{ $label }}</td>
             <td class="text-center">{!! badge($data->$field) !!}</td>
         </tr>
         @endforeach
         @endforeach
     </tbody>
 </table>

 <h6 class="mt-4 mb-2">Kebutuhan Material</h6>

 @if ($main->kebutuhanMaterial->isEmpty())
 <div class="text-muted fst-italic">
     Tidak ada kebutuhan material
 </div>
 @else
 <table class="table table-sm table-bordered align-middle">
     <thead class="table-light text-center">
         <tr>
             <th width="120">MID</th>
             <th>Deskripsi</th>
             <th width="80">Qty</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($main->kebutuhanMaterial as $material)
         <tr>
             <td class="text-center">{{ $material->mid }}</td>
             <td>{{ $material->deskripsi }}</td>
             <td class="text-center">{{ $material->qty }}</td>
         </tr>
         @endforeach
     </tbody>
 </table>
 @endif