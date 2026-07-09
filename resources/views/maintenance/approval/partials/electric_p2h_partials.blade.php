@php
 $sipil = [
 'level_minyak_rem' => [
 'label' => 'Check Level Minyak Rem',
 'standar' => 'Berada di level max',
 ],
 'level_oli_hydraulic' => [
 'label' => 'Check Level Oli Hydraulic',
 'standar' => 'Berada di level max',
 ],
 'isi_air_aki' => [
 'label' => 'Check Isi Air Aki',
 'standar' => 'Berada di level standar',
 ],
 'baterai' => [
 'label' => 'Check Baterai',
 'standar' => 'Tidak kurang dari 30%',
 ],
 'hydraulic_system' => [
 'label' => 'Hydraulic System',
 'standar' => 'Berfungsi dengan baik dan terlubrikasi',
 ],
 'selang_hydraulic' => [
 'label' => 'Selang Hydraulic',
 'standar' => 'Tidak ada kebocoran oli',
 ],
 'lift_chains' => [
 'label' => 'Lift Chains',
 'standar' => 'Kekencangan kanan dan kiri sama serta terlubrikasi',
 ],
 'fork' => [
 'label' => 'Pengecekan Fork',
 'standar' => 'Tidak bengkok dan tidak patah',
 ],
 'body_unit' => [
 'label' => 'Check Body Unit',
 'standar' => 'Tidak lecet dan tidak penyok',
 ],
 'lampu_kombinasi_kiri' => [
 'label' => 'Check Lampu Kombinasi Kiri',
 'standar' => 'Menyala normal dan tidak pecah',
 ],
 'lampu_kombinasi_kanan' => [
 'label' => 'Check Lampu Kombinasi Kanan',
 'standar' => 'Menyala normal dan tidak pecah',
 ],
 'lampu_sorot' => [
 'label' => 'Check Lampu Sorot / Head Lamp',
 'standar' => 'Menyala normal dan tidak pecah',
 ],
 'lampu_sign_depan_kanan' => [
 'label' => 'Check Lampu Sign Depan Kanan',
 'standar' => 'Menyala normal dan tidak pecah',
 ],
 'lampu_sign_depan_kiri' => [
 'label' => 'Check Lampu Sign Depan Kiri',
 'standar' => 'Menyala normal dan tidak pecah',
 ],
 'klakson' => [
 'label' => 'Check Klakson / Horn',
 'standar' => 'Bunyi saat tombol ditekan',
 ],
 'buzzer_back' => [
 'label' => 'Check Buzzer Back',
 'standar' => 'Berbunyi normal saat maju dan mundur',
 ],
 'kaca_spion' => [
 'label' => 'Check Kaca Spion',
 'standar' => 'Terpasang dengan baik dan tidak pecah',
 ],
 'baut_roda' => [
 'label' => 'Check Kekencangan Baut Roda',
 'standar' => 'Kencang dan tidak patah',
 ],
 'ban' => [
 'label' => 'Check Ban',
 'standar' => 'Masih bagus dan layak pakai',
 ],
 'kebersihan_unit' => [
 'label' => 'Check Kebersihan Unit',
 'standar' => 'Bersih dari kotoran dan debu',
 ],
 'panel_display' => [
 'label' => 'Check Panel Display',
 'standar' => 'Berfungsi normal, tidak pecah, dan tidak ada alarm',
 ],
 'sistem_kemudi' => [
 'label' => 'Sistem Kemudi',
 'standar' => 'Tidak berat dan bergerak lancar',
 ],
 ];

 $fieldTypes = [
     'level_minyak_rem' => 'forklift',
     'level_oli_hydraulic' => 'forklift',
     'isi_air_aki' => 'all',
     'baterai' => 'all',
     'hydraulic_system' => 'all',
     'selang_hydraulic' => 'forklift',
     'lift_chains' => 'forklift_es',
     'fork' => 'all',
     'body_unit' => 'all',
     'lampu_kombinasi_kiri' => 'forklift',
     'lampu_kombinasi_kanan' => 'forklift',
     'lampu_sorot' => 'forklift',
     'lampu_sign_depan_kanan' => 'forklift',
     'lampu_sign_depan_kiri' => 'forklift',
     'klakson' => 'all',
     'buzzer_back' => 'forklift',
     'kaca_spion' => 'forklift',
     'baut_roda' => 'all',
     'ban' => 'all',
     'kebersihan_unit' => 'all',
     'panel_display' => 'all',
     'sistem_kemudi' => 'all',
 ];

 $namaMesin = strtoupper(optional($data->mesin)->nama_mesin ?? '');
 $isForklift = str_contains($namaMesin, 'FORKLIFT');
 $isPM = str_contains($namaMesin, 'PALLET MOVER') || str_contains($namaMesin, 'PM');
 $isES = str_contains($namaMesin, 'STACKER') || str_contains($namaMesin, 'STEKER') || str_contains($namaMesin, 'ES');

 $activeType = '';
 if ($isForklift) {
     $activeType = 'forklift';
 } elseif ($isPM) {
     $activeType = 'pm';
 } elseif ($isES) {
     $activeType = 'es';
 }

 function badge($v)
 {
 return $v === null
 ? '<span class="badge bg-secondary">No Check</span>'
 : ($v
 ? '<span class="badge bg-success">OK</span>'
 : '<span class="badge bg-danger">NG</span>');
 }
 @endphp

 <h5 class="mb-3">Mtc Electric P2H Inspection</h5>

 <table class="table table-sm table-borderless mb-4">
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
         <th>Departemen</th>
         <td>: {{ $main->departemen ?? '-' }}</td>
     </tr>
     <tr>
         <th>No Unit</th>
         <td>: {{ optional($data->mesin)->nama_mesin ?? $data->no_unit }}</td>
     </tr>
     <tr>
         <th>Shift</th>
         <td>: {{ $data->shift }}</td>
     </tr>
     <tr>
         <th>Hours Meter</th>
         <td>: {{ $data->hours_meter ?? '-' }}</td>
     </tr>
     @if (isset($data->persentase))
     <tr>
         <th>Persentase Score</th>
         <td>: <strong class="text-primary">{{ $data->persentase }}%</strong></td>
     </tr>
     @endif
     <tr>
         <th>Catatan</th>
         <td>: {{ $data->catatan ?? '-' }}</td>
     </tr>
     <tr>
         <th>Keterangan NOK</th>
         <td>: {{ $main->keterangan ?? '-' }}</td>
     </tr>
 </table>

 <h6 class="mb-2">Checklist Electric P2H</h6>

 <table class="table table-sm table-bordered align-middle">
     <thead>
         <tr class="table-light text-center">
             <th>Item</th>
             <th>Standar</th>
             <th>Status</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($sipil as $field => $item)
         @php
             $type = $fieldTypes[$field] ?? 'all';
             $visible = false;
             if (!$activeType) {
                 $visible = true;
             } elseif ($type === 'all') {
                 $visible = true;
             } elseif ($activeType === 'forklift') {
                 $visible = true;
             } elseif ($activeType === 'es' && $type === 'forklift_es') {
                 $visible = true;
             }
         @endphp
         @if ($visible)
         <tr>
             <td class="fw-semibold">{{ $item['label'] }}</td>
             <td>{{ $item['standar'] }}</td>
             <td class="text-center">
                 {!! badge($data->$field ?? null) !!}
             </td>
         </tr>
         @endif
         @endforeach
     </tbody>
 </table>