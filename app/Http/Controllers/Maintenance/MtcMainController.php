<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcApprovalModel;
use App\Models\Maintenance\MtcMainModel;
use App\Models\NotificationsModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MtcMainController extends Controller
{
    public function tracking($id)
    {
        $approvals = MtcApprovalModel::where('mtc_main_id', $id)
            ->with('approver:id,username')
            ->orderBy('level')
            ->get()
            ->map(function ($a) {
                return [
                    'level'     => $a->level,
                    'role'      => ucfirst($a->role),
                    'status'    => $a->status,
                    'approver'  => $a->approver->username ?? '-',
                    'catatan'   => $a->catatan,
                    'action_at' => $a->action_at,
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $approvals
        ]);
    }

    public function getApprovers()
    {
        $staff = User::where('departemen', 'engineering')
            ->whereIn('jabatan', ['foreman', 'supervisor'])
            ->where(function ($q) {
                $q->where('bagian', 'Engineering Maintenance & Improvement')
                    ->orWhere('bagian', 'Engineering');
            })
            ->get(['id', 'username']);

        $user = User::where('departemen', 'engineering')
            ->where('jabatan', 'supervisor')
            ->where(function ($q) {
                $q->where('bagian', 'Engineering Maintenance & Improvement')
                    ->orWhere('bagian', 'Engineering');
            })
            ->get(['id', 'username', 'departemen']);

        return response()->json([
            'staff' => $staff,
            'user'  => $user
        ]);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $main = MtcMainModel::findOrFail($id);

            // hapus notification
            NotificationsModel::where([
                'notifiable_type' => MtcMainModel::class,
                'notifiable_id'   => $id
            ])->delete();

            // terakhir hapus main
            $main->delete();
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data maintenance berhasil dihapus',
        ]);
    }

    public function downloadMaintenanceData(Request $request, $jenis_mtc, $id)
    {
        try {
            switch ($jenis_mtc) {
                case 'Motor Pompa':
                    return $this->exportMotorPump($id);
                case 'Utility':
                    return $this->exportUtility($id);
                case 'Electrical':
                    return $this->exportElectrical($id);
                case 'Refrigerasi':
                    return $this->exportRefrigerasi($id);
                case 'Electric Engine':
                    return $this->exportElectricEngine($id);
                case 'Diesel Engine':
                    return $this->exportDieselEngine($id);
                case 'Battery':
                    return $this->exportBattery($id);
                case 'Sipil':
                    return $this->exportSipil($id);
                case 'Electric P2h':
                    return $this->exportElectricP2h($id);
                case 'Diesel P2h':
                    return $this->exportDieselP2h($id);
                default:
                    return response()->json(['message' => 'Jenis maintenance tidak valid'], 404);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error', 'error' => $e->getMessage()], 500);
        }
    }

    private function insertApprovalSticker($sheet, $cell)
    {
        $drawing = new Drawing();
        $drawing->setName('Approved');
        $drawing->setDescription('Approved Sticker');
        $drawing->setPath(public_path('storage/mtc/ttd/utility_approved_sticker.png'));
        $drawing->setHeight(20);
        $drawing->setCoordinates($cell);
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(2);
        $drawing->setWorksheet($sheet);
    }

    private function exportMotorPump($id)
    {
        $path = public_path('assets/templates/maintenance/motor_pump.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Motor Pompa')
            ->where('id', $id)
            ->with('motorPump', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Motor
            'electrical_motor' => 8,
            'putaran_motor' => 9,
            'fibrasi_suara_motor' => 10,
            'bearing_motor' => 11,
            'pelumasan_motor' => 12,
            'kebersihan_unit_body_motor' => 13,

            // Pompa
            'putaran_pompa' => 16,
            'shaft_karet_coupling_pompa' => 17,
            'fan_belt_pompa' => 18,
            'pressure_pompa' => 19,
            'mechanical_seal_pompa' => 20,
            'gasket_pompa' => 21,
            'impeler' => 22,
            'kebersihan_unit_body_pompa' => 23,

            // Aksesoris
            'valve_aksesoris' => 26,
            'cek_valve_aksesoris' => 27,
            'flow_meter_aksesoris' => 28,
            'strainer_aksesoris' => 29,
            'alat_ukur_aksesoris' => 30,
            'kelengkapan_baut_mur_aksesoris' => 31,

            // Gearbox
            'tambah_ganti_oli_gearbox' => 33,
            'unit_area_gearbox' => 34,
            'oil_seal_gearbox' => 35,
            'filter_udara_gearbox' => 36,
            'bearing_gearbox' => 37,
        ];

        foreach ($data as $main) {

            $inspection = $main->motorPump;
            if (!$inspection) continue;

            // ================= HEADER =================
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('C5', $main->waktu_selesai ?? '-');
            $sheet->setCellValue('C6', $main->departemen ?? '-');
            $sheet->setCellValue('F3', ': ' . $inspection->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('F4', ': ' . $inspection->mesin->kode_mesin ?? '-');
            $sheet->setCellValue('F5', ': ' . $inspection->mesin->lokasi ?? '-');
            $sheet->setCellValue('F6', ': ' . $main->paket ?? '-');
            $sheet->setCellValue('A41', 'Tindakan Korektif : ' . ($main->korektif ?? ''));

            // ================= PARSE KETERANGAN =================
            $keteranganMap = [];

            if (!empty($main->keterangan)) {
                $items = explode('|', $main->keterangan);

                foreach ($items as $item) {
                    $parts = explode(':', $item, 2);

                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $val = trim($parts[1]);

                        $keteranganMap[$key] = $val;
                    }
                }
            }

            foreach ($fieldRowMap as $field => $row) {

                $value = $inspection->{$field};

                if ($value === true) {
                    $kondisi = '✓';
                } elseif ($value === false) {
                    $kondisi = '✗';
                } else {
                    $kondisi = '';
                }

                // kolom kondisi
                $sheet->setCellValue('D' . $row, $kondisi);

                $ket = $keteranganMap[$field] ?? '';
                $sheet->setCellValue('E' . $row, $ket);
            }

            $materialRow = 43;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('D' . $materialRow, $item->mid ?? '');
                    $sheet->setCellValue('F' . $materialRow, $item->deskripsi ?? '');
                    $sheet->setCellValue('H' . $materialRow, $item->qty ?? '');

                    $materialRow++;
                }
            }

            // ================= APPROVAL =================
            if ($main->approvals && $main->approvals->count()) {
                foreach ($main->approvals as $item) {
                    if ($item->status !== 'approved') continue;
                    switch (strtolower($item->role)) {
                        case 'teknisi':
                            $sheet->setCellValue('D53', 'Dibuat: ' . $item->approver?->username ?? '-');
                            $this->insertApprovalSticker($sheet, 'G53');
                            break;
                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'G55');
                            break;
                        case 'user':
                            $this->insertApprovalSticker($sheet, 'G57');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'motor_pump');
    }

    private function exportUtility($id)
    {
        $path = public_path('assets/templates/maintenance/utility.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Utility')
            ->where('id', $id)
            ->with('utility', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Cooling Tower
            'cleaning_saringan_cooling_tower' => 8,
            'cleaning_unit_cooling_tower' => 9,
            'cleaning_bak_cooling_tower' => 10,

            // RO
            'check_sensor_tank_farm_ro_produk' => 12,
            'cleaning_flow_rate_mmf_1' => 13,
            'cleaning_flow_rate_mmf_2' => 14,
            'cleaning_flow_rate_ro_produk' => 15,
            'cleaning_flow_rate_ro_reject' => 16,
            'penggantian_micron_filter_cip' => 17,
            'penggantian_micron_filter_makeup_water' => 18,
            'cleaning_cip_tank' => 19,
            'cip_membrane_reverse_osmosis' => 20,
            'check_fungsi_valve' => 21,
            'cleaning_unit_ro_mesin' => 22,

            // Compressor
            'sirkulasi_phe_aq55vsd' => 24,
            'penggantian_air_ro_aq55vsd' => 25,
            'cleaning_compressor_aq55vsd' => 26,
            'cleaning_jalur_cooling_aq55vsd' => 27,
            'cleaning_dryer_fd185' => 28,
            'cleaning_compressor_ga37' => 29,
            'cleaning_dryer_fd120' => 30,
            'lubrikasi_motor_compressor_aq55vsd' => 31,
            'cleaning_compressor_sm55' => 32,

            // Tank Farm
            'cleaning_sensor_level_tank_farm' => 33,
            'cleaning_sensor_level_fresh_water_menara' => 34,
            'cleaning_sensor_level_ro_reject_menara' => 35,
            'cleaning_sensor_level_intermediate' => 36,

            // Boiler
            'check_safety_valve' => 38,
            'cleaning_level_gauge' => 39,
            'cleaning_level_transmitter' => 40,
            'check_pressure_transmitter' => 41,
            'check_temperature_transmitter' => 42,
            'cleaning_sensor_o2_co2' => 43,
            'check_chaingrate' => 44,
            'check_ruang_bakar' => 45,
            'check_back_chamber' => 46,
            'check_guillotine' => 47,
            'check_wet_ash_conveyor' => 48,
            'check_bottom_ash_conveyor' => 49,
            'check_conveyor_batu_bara' => 50,
            'check_feeder' => 51,
            'cleaning_bak_wet_ash_conveyor' => 52,
            'check_feed_tank' => 53,

            // WWTP
            'check_line_limbah' => 55,
            'check_line_chemical' => 56,
            'check_tangki_kotak' => 57,
            'check_tangki_bulat' => 58,
        ];

        foreach ($data as $main) {
            $inspection = $main->utility;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('C5', $main->waktu_selesai ?? '-');
            $sheet->setCellValue('C6', $main->departemen ?? '-');
            $sheet->setCellValue('F3', ': ' . $main->utility->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('F4', ': ' . $main->utility->mesin->kode_mesin ?? '-');
            $sheet->setCellValue('F5', ': ' . $main->utility->mesin->lokasi ?? '-');
            $sheet->setCellValue('F6', ': ' . $main->paket ?? '-');
            $sheet->setCellValue('A67', 'Tindakan Korektif : ' . $main->korektif);

            // ================= PARSE KETERANGAN =================
            $keteranganMap = [];

            if (!empty($main->keterangan)) {
                $items = explode('|', $main->keterangan);

                foreach ($items as $item) {
                    $parts = explode(':', $item, 2);

                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $val = trim($parts[1]);

                        $keteranganMap[$key] = $val;
                    }
                }
            }

            foreach ($fieldRowMap as $field => $row) {

                $value = $inspection->{$field};

                if ($value === true) {
                    $kondisi = '✓';
                } elseif ($value === false) {
                    $kondisi = '✗';
                } else {
                    $kondisi = '';
                }

                $sheet->setCellValue('D' . $row, $kondisi);

                $ket = $keteranganMap[$field] ?? '';
                $sheet->setCellValue('E' . $row, $ket);
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 69;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('D' . $materialRow, $item->mid ?? '');
                    $sheet->setCellValue('F' . $materialRow, $item->deskripsi ?? '');
                    $sheet->setCellValue('H' . $materialRow, $item->qty ?? '');

                    $materialRow++;
                }
            }

            // Sticker Approval
            if ($main->approvals && $main->approvals->count()) {

                foreach ($main->approvals as $item) {

                    // hanya kalau approved
                    if ($item->status !== 'approved') continue;

                    switch (strtolower($item->role)) {

                        case 'teknisi':
                            $sheet->setCellValue('D73', 'Dibuat: ' . $item->approver?->username ?? '-');
                            $this->insertApprovalSticker($sheet, 'G73');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'G75');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'G77');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'utility');
    }

    private function exportElectrical($id)
    {
        $path = public_path('assets/templates/maintenance/electrical.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Electrical')
            ->where('id', $id)
            ->with('electrical', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Panel
            'check_kunci' => 8,
            'check_koneksi_kabel' => 9,
            'check_wiring_panel' => 10,
            'check_lampu_indikator' => 11,
            'check_name_plate' => 12,
            'check_unit_electrical' => 13,
            'check_grounding' => 14,
            'check_kebersihan' => 15,
            'check_bus_bar' => 16,
            'check_nilai_grounding' => 17,

            // Penerangan
            'check_kondisi_lampu' => 19,
            'check_cover_lampu' => 20,
            'check_wiring_penerangan' => 21,
            'check_saklar' => 22,
            'check_penyangga_penerangan' => 23,

            // Sistem Distribusi
            'check_stecker' => 26,
            'check_stop_kontak' => 27,
            'check_terminal_listrik' => 28,
            'check_pengabelan_distribusi' => 29,
            'check_support_pelindung_distribusi' => 30,

            // Capacitor Bank
            'check_kondisi_fisik_capacitor' => 33,
            'check_nilai_farad' => 34,
            'check_nilai_ampere' => 35,
            'check_kebersihan_capacitor' => 36,

            // Trafo
            'check_kebocoran_oli_sisi_bawah' => 39,
            'check_kebocoran_oli_sisi_atas' => 40,
            'check_level_oli' => 41,
        ];

        foreach ($data as $main) {
            $inspection = $main->electrical;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('C5', $main->waktu_selesai ?? '-');
            $sheet->setCellValue('C6', $main->departemen ?? '-');
            $sheet->setCellValue('F3', ': ' . $inspection->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('F4', ': ' . $inspection->mesin->kode_mesin ?? '-');
            $sheet->setCellValue('F5', ': ' . $inspection->mesin->lokasi ?? '-');
            $sheet->setCellValue('F6', ': ' . $main->paket ?? '-');
            $sheet->setCellValue('A49', 'Tindakan Korektif : ' . $main->korektif);

            $keteranganMap = [];

            if (!empty($main->keterangan)) {
                $items = explode('|', $main->keterangan);

                foreach ($items as $item) {
                    $parts = explode(':', $item, 2);

                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $val = trim($parts[1]);

                        $keteranganMap[$key] = $val;
                    }
                }
            }

            foreach ($fieldRowMap as $field => $row) {

                $value = $inspection->{$field};

                if ($value === true) {
                    $kondisi = '✓';
                } elseif ($value === false) {
                    $kondisi = '✗';
                } else {
                    $kondisi = '';
                }

                $sheet->setCellValue('D' . $row, $kondisi);
                $ket = $keteranganMap[$field] ?? '';
                $sheet->setCellValue('E' . $row, $ket);
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 51;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('D' . $materialRow, $item->mid ?? '');
                    $sheet->setCellValue('F' . $materialRow, $item->deskripsi ?? '');
                    $sheet->setCellValue('H' . $materialRow, $item->qty ?? '');

                    $materialRow++;
                }
            }

            // Sticker Approval
            if ($main->approvals && $main->approvals->count()) {

                foreach ($main->approvals as $item) {

                    // hanya kalau approved
                    if ($item->status !== 'approved') continue;

                    switch (strtolower($item->role)) {

                        case 'teknisi':
                            $sheet->setCellValue('D57', 'Dibuat: ' . $item->approver?->username ?? '-');
                            $this->insertApprovalSticker($sheet, 'G57');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'G59');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'G61');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'electrical');
    }

    private function exportRefrigerasi($id)
    {
        $path = public_path('assets/templates/maintenance/refrigerasi.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Refrigerasi')
            ->where('id', $id)
            ->with('refrigerasi', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Unit Indoor
            'check_filter_udara' => 8,
            'check_cover_filter_udara' => 9,
            'check_electrical_indoor' => 10,
            'check_suhu_evaporator' => 11,
            'check_indikator_display' => 12,
            'check_motor_blower' => 13,
            'check_fan_belt_blower' => 14,
            'check_pergerakan_motor_swing' => 15,
            'check_kontroler_indoor' => 16,
            'check_saluran_drain_kondensasi' => 17,
            'sirkulasi_evaporator' => 18,

            // Unit Outdoor
            'check_kondisi_kondensor' => 21,
            'check_electrical_outdoor' => 22,
            'check_motor_fan' => 23,
            'check_tekanan_freon' => 24,
            'pelumasan_motor_fan' => 25,
            'kebersihan_unit_body_outdoor' => 26,

            // Jalur Distribusi
            'check_jalur_freon' => 29,
            'check_jalur_distribusi_udara' => 30,
            'check_jalur_return_udara' => 31,
        ];

        foreach ($data as $main) {
            $inspection = $main->refrigerasi;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('C5', $main->waktu_selesai ?? '-');
            $sheet->setCellValue('C6', $main->departemen ?? '-');
            $sheet->setCellValue('F3', ': ' . $inspection->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('F4', ': ' . $inspection->mesin->kode_mesin ?? '-');
            $sheet->setCellValue('F5', ': ' . $inspection->mesin->lokasi ?? '-');
            $sheet->setCellValue('F6', ': ' . $main->paket ?? '-');
            $sheet->setCellValue('A35', 'Tindakan Korektif : ' . $main->korektif);

            $keteranganMap = [];

            if (!empty($main->keterangan)) {
                $items = explode('|', $main->keterangan);

                foreach ($items as $item) {
                    $parts = explode(':', $item, 2);

                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $val = trim($parts[1]);

                        $keteranganMap[$key] = $val;
                    }
                }
            }

            foreach ($fieldRowMap as $field => $row) {

                $value = $inspection->{$field};

                if ($value === true) {
                    $kondisi = '✓';
                } elseif ($value === false) {
                    $kondisi = '✗';
                } else {
                    $kondisi = '';
                }

                $sheet->setCellValue('D' . $row, $kondisi);
                $ket = $keteranganMap[$field] ?? '';
                $sheet->setCellValue('E' . $row, $ket);
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 37;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('D' . $materialRow, $item->mid ?? '');
                    $sheet->setCellValue('F' . $materialRow, $item->deskripsi ?? '');
                    $sheet->setCellValue('H' . $materialRow, $item->qty ?? '');

                    $materialRow++;
                }
            }

            // Sticker Approval
            if ($main->approvals && $main->approvals->count()) {

                foreach ($main->approvals as $item) {

                    // hanya kalau approved
                    if ($item->status !== 'approved') continue;

                    switch (strtolower($item->role)) {

                        case 'teknisi':
                            $sheet->setCellValue('D47', 'Dibuat: ' . $item->approver?->username ?? '-');
                            $this->insertApprovalSticker($sheet, 'G47');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'G49');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'G51');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'refrigerasi');
    }

    private function exportElectricEngine($id)
    {
        $path = public_path('assets/templates/maintenance/electric_engine.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Electric Engine')
            ->where('id', $id)
            ->with('electricEngine', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Forklift Electrical - General
            'check_general' => 9,
            'check_buzzer_back' => 10,
            'check_klakson' => 11,
            'check_pilot_lamp' => 12,
            'check_lampu_sorot' => 13,
            'check_lampu_kombinasi_kanan_belakang' => 14,
            'check_lampu_kombinasi_kiri_belakang' => 15,
            'check_kaca_sepion' => 16,

            // Battery, Charger & Electrical System
            'check_battery' => 19,
            'check_skun_battery' => 20,
            'check_terminal_charger_battery' => 21,
            'check_kunci_kontak' => 22,
            'check_main_contactor' => 23,
            'check_microswitch' => 24,
            'check_eps_controller' => 25,
            'check_steering_motor' => 26,
            'check_fan' => 27,
            'check_fuse' => 28,
            'check_display_control' => 29,
            'check_wiring_terminal' => 30,
            'check_carbon_brush' => 31,

            // Drive, Steering, Mast, Hydraulic & Braking System
            'check_steering_wheel' => 33,
            'check_baut_roda' => 34,
            'check_drive_caster_load_wheel' => 35,
            'check_lift_chain' => 36,
            'check_lift_bracket' => 37,
            'check_hydraulic_hose' => 38,
            'check_motor_hydraulic_pump' => 39,
            'check_fork' => 40,
            'check_lift_rollers' => 41,
            'check_mast_rollers' => 42,
            'check_lift_cylinders' => 43,
            'check_tilt_cylinders' => 44,
            'check_control_valve' => 45,
            'check_hydraulic_tank' => 46,
            'check_overhead_guard' => 47,
            'check_all_bolt_nut' => 48,
            'check_power_steering' => 49,
            'check_brake_cam_adjust_bolt' => 51,
            'check_axle' => 51,
            'check_greasing_point' => 52,
            'check_air_spring' => 53,

            // Oil
            'ganti_gear_oil' => 56,
            'ganti_hydraulic_oil' => 57,
            'ganti_return_filter' => 58,
            'ganti_brake_oil' => 59,
        ];

        foreach ($data as $main) {
            $inspection = $main->electricEngine;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('C5', $main->waktu_selesai ?? '-');
            $sheet->setCellValue('C6', $main->departemen ?? '-');
            $sheet->setCellValue('C7', $main->paket ?? '-');
            $sheet->setCellValue('G3', ': ' . $inspection->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('G4', ': ' . $inspection->mesin->kode_mesin ?? '-');
            $sheet->setCellValue('G5', ': ' . $inspection->mesin->lokasi ?? '-');
            $sheet->setCellValue('G6', ': ' . $main->running_hour ?? '-');
            $sheet->setCellValue('A65', 'Tindakan Korektif : ' . $main->korektif);

            $keteranganMap = [];

            if (!empty($main->keterangan)) {
                $items = explode('|', $main->keterangan);

                foreach ($items as $item) {
                    $parts = explode(':', $item, 2);

                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $val = trim($parts[1]);

                        $keteranganMap[$key] = $val;
                    }
                }
            }

            foreach ($fieldRowMap as $field => $row) {

                $value = $inspection->{$field};

                if ($value === true) {
                    $kondisi = '✓';
                } elseif ($value === false) {
                    $kondisi = '✗';
                } else {
                    $kondisi = '';
                }

                $sheet->setCellValue('E' . $row, $kondisi);
                $ket = $keteranganMap[$field] ?? '';
                $sheet->setCellValue('F' . $row, $ket);
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 67;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('E' . $materialRow, $item->mid ?? '');
                    $sheet->setCellValue('G' . $materialRow, $item->deskripsi ?? '');
                    $sheet->setCellValue('I' . $materialRow, $item->qty ?? '');

                    $materialRow++;
                }
            }

            // Sticker Approval
            if ($main->approvals && $main->approvals->count()) {

                foreach ($main->approvals as $item) {

                    // hanya kalau approved
                    if ($item->status !== 'approved') continue;

                    switch (strtolower($item->role)) {

                        case 'teknisi':
                            $sheet->setCellValue('D72', 'Dibuat: ' . $item->approver?->username ?? '-');
                            $this->insertApprovalSticker($sheet, 'H72');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'H74');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'H76');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'electric_engine');
    }

    private function exportDieselEngine($id)
    {
        $path = public_path('assets/templates/maintenance/diesel_engine.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Diesel Engine')
            ->where('id', $id)
            ->with('dieselEngine', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // ENGINE
            'check_kondisi_level_oli_mesin' => 9,
            'check_kondisi_radiator_hose' => 10,
            'check_kondisi_level_air_radiator' => 11,
            'check_water_pump' => 12,
            'check_injection_pump_injector_piping' => 13,
            'check_turbocharger_manifold' => 14,
            'check_fan_v_belt' => 15,
            'check_automatic_tensioner_belt' => 16,
            'check_engine_mounting' => 17,
            'check_air_filter_condition' => 18,
            'check_clearence_valve_drain_valve' => 19,
            'check_engine_oil_filter' => 20,
            'check_air_radiator' => 21,
            'check_minyak_kopling' => 22,
            'check_fuel_filter' => 23,

            // ELECTRIC
            'check_kondisi_aki_level_air_aki' => 25,
            'check_fungsi_starting_motor' => 26,
            'check_fungsi_alternator' => 27,
            'check_sensor_sensor_gauge' => 28,
            'check_fuse_control_switch' => 29,
            'check_control_display' => 30,
            'check_indicator_wiring' => 31,

            // TRANSMISI / BRAKE / DRIVE SHAFT
            'check_kondisi_level_oli_transmisi' => 33,
            'check_fungsi_transmisi' => 34,
            'check_filter_oli_transmisi' => 35,
            'check_fungsi_rem' => 36,
            'check_oli_tidak_ada_yang_bocor' => 37,
            'check_kondisi_drive_shaft' => 38,

            // HYDRAULIC
            'check_kondisi_level_hydraulic_oil' => 40,
            'check_kondisi_hydraulic_oil_filter' => 41,
            'check_fungsi_hydraulic_system' => 42,
            'check_fungsi_steering_system' => 43,
            'check_kondisi_hydraulic_cylinder' => 44,
            'check_kondisi_steering_cylinder' => 45,
            'check_kondisi_axle_oil' => 46,
            'check_kondisi_baut_roda_hydraulic' => 47,
            'check_kondisi_bucket_pin_bucket' => 48,
            'check_kondisi_dump_pin_bushing' => 49,

            // GENERAL
            'check_klakson' => 51,
            'check_buzzer_back' => 52,
            'check_kondisi_basket_fresh_body' => 53,
            'check_kaca_sepion' => 54,
            'check_kondisi_roda_ban' => 55,
            'check_baut_roda_general' => 56,
            'check_lampu_depan_kanan' => 57,
            'check_lampu_depan_kiri' => 58,
            'check_baut_bearing_molen' => 59,
            'check_baut_hanger_as_roda' => 60,
        ];

        foreach ($data as $main) {
            $inspection = $main->dieselEngine;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('C5', $main->waktu_selesai ?? '-');
            $sheet->setCellValue('C6', $main->departemen ?? '-');
            $sheet->setCellValue('C7', $main->paket ?? '-');
            $sheet->setCellValue('G3', ': ' . $inspection->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('G4', ': ' . $inspection->mesin->kode_mesin ?? '-');
            $sheet->setCellValue('G5', ': ' . $inspection->mesin->lokasi ?? '-');
            $sheet->setCellValue('G6', ': ' . $main->running_hour ?? '-');
            $sheet->setCellValue('A62', 'Tindakan Korektif : ' . $main->korektif);

            $keteranganMap = [];

            if (!empty($main->keterangan)) {
                $items = explode('|', $main->keterangan);

                foreach ($items as $item) {
                    $parts = explode(':', $item, 2);

                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $val = trim($parts[1]);

                        $keteranganMap[$key] = $val;
                    }
                }
            }

            foreach ($fieldRowMap as $field => $row) {

                $value = $inspection->{$field};

                if ($value === true) {
                    $kondisi = '✓';
                } elseif ($value === false) {
                    $kondisi = '✗';
                } else {
                    $kondisi = '';
                }

                $sheet->setCellValue('E' . $row, $kondisi);
                $ket = $keteranganMap[$field] ?? '';
                $sheet->setCellValue('F' . $row, $ket);
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 64;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('E' . $materialRow, $item->mid ?? '');
                    $sheet->setCellValue('G' . $materialRow, $item->deskripsi ?? '');
                    $sheet->setCellValue('I' . $materialRow, $item->qty ?? '');

                    $materialRow++;
                }
            }

            // Sticker Approval
            if ($main->approvals && $main->approvals->count()) {

                foreach ($main->approvals as $item) {

                    // hanya kalau approved
                    if ($item->status !== 'approved') continue;

                    switch (strtolower($item->role)) {

                        case 'teknisi':
                            $sheet->setCellValue('D74', 'Dibuat: ' . $item->approver?->username ?? '-');
                            $this->insertApprovalSticker($sheet, 'H74');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'H76');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'H78');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'diesel_engine');
    }

    private function exportBattery($id)
    {
        $path = public_path('assets/templates/maintenance/battery.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Battery')
            ->where('id', $id)
            ->with('battery', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $columns = ['D', 'G', 'J', 'M', 'P', 'S'];

        $fieldOffset = [
            'voltase' => 0,
            'level_air_aki' => 1,
            'intercell' => 2,
            'kondisi_skun' => 3,
            'kondisi_unit' => 4,
            'grounding' => 5,
        ];

        foreach ($data as $main) {

            $batteries = $main->battery;
            if (!$batteries || $batteries->isEmpty()) continue;

            $first = $batteries->first();

            $sheet->setCellValue('C4', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C6', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('K4', $first->battery_type ?? '-');
            $sheet->setCellValue('K6', $first->no_seri ?? '-');
            $sheet->setCellValue('R4', $first->no_unit ?? '-');
            $sheet->setCellValue('R39', ': ' . ($first->total_voltase ?? '-'));
            $sheet->setCellValue('R40', ': ' . ($first->kondisi_plug_battery ?? '-'));
            $sheet->setCellValue('A43', 'Catatan : ' . ($first->catatan ?? ''));

            foreach ($batteries as $inspection) {

                $cell = (int) $inspection->cell;
                if ($cell < 1 || $cell > 24) continue;

                $col = $columns[($cell - 1) % 6];

                $group = floor(($cell - 1) / 6);
                $startRow = 10 + ($group * 7);

                foreach ($fieldOffset as $field => $offset) {

                    $row = $startRow + $offset;
                    $value = $inspection->{$field};

                    if (in_array($field, ['voltase', 'grounding'])) {
                        $sheet->setCellValue($col . $row, $value ?? '');
                    } else {
                        $kondisi = ($value === true) ? '✓' : (($value === false) ? '✗' : '');
                        $sheet->setCellValue($col . $row, $kondisi);
                    }
                }
            }

            // APPROVAL
            if ($main->approvals && $main->approvals->count()) {

                foreach ($main->approvals as $item) {

                    if ($item->status !== 'approved') continue;

                    switch (strtolower($item->role)) {

                        case 'teknisi':
                            $this->insertApprovalSticker($sheet, 'A50');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'J50');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'R50');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'battery');
    }

    private function exportSipil($id)
    {
        $path = public_path('assets/templates/maintenance/sipil.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Sipil')
            ->where('id', $id)
            ->with('sipil', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            'plumbing' => 8,
            'plafon' => 9,
            'lantai' => 10,
            'dinding' => 11,
            'jendela' => 12,
            'pintu' => 13,
            'rooling_fast_door' => 14,
        ];

        foreach ($data as $main) {
            $inspection = $main->sipil;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('C5', $main->waktu_selesai ?? '-');
            $sheet->setCellValue('G3', ': ' . $main->area ?? '-');
            $sheet->setCellValue('G4', ': ' . $main->lokasi ?? '-');
            $sheet->setCellValue('G5', ': ' . $main->departemen ?? '-');
            $sheet->setCellValue('A16', 'Rekomendasi : ' . $main->rekomendasi);
            $sheet->setCellValue('A17', 'Tindakan Korektif : ' . $main->korektif);

            $keteranganMap = [];

            if (!empty($main->keterangan)) {
                $items = explode('|', $main->keterangan);

                foreach ($items as $item) {
                    $parts = explode(':', $item, 2);

                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $val = trim($parts[1]);

                        $keteranganMap[$key] = $val;
                    }
                }
            }

            foreach ($fieldRowMap as $field => $row) {

                $value = $inspection->{$field};

                // Kosongkan dulu dua kolom biar aman
                $sheet->setCellValue('D' . $row, '');
                $sheet->setCellValue('E' . $row, '');

                if ($value === true) {
                    $sheet->setCellValue('D' . $row, '✓');
                } elseif ($value === false) {
                    $sheet->setCellValue('E' . $row, '✗');
                }

                $ket = $keteranganMap[$field] ?? '';
                $sheet->setCellValue('F' . $row, $ket);
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 19;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('E' . $materialRow, $item->mid ?? '');
                    $sheet->setCellValue('G' . $materialRow, $item->deskripsi ?? '');
                    $sheet->setCellValue('H' . $materialRow, $item->qty ?? '');
                    $sheet->setCellValue('I' . $materialRow, $item->uom ?? '');

                    $materialRow++;
                }
            }

            // Sticker Approval
            if ($main->approvals && $main->approvals->count()) {

                foreach ($main->approvals as $item) {

                    // hanya kalau approved
                    if ($item->status !== 'approved') continue;

                    switch (strtolower($item->role)) {

                        case 'teknisi':
                            $sheet->setCellValue('E34', 'Dibuat: ' . $item->approver?->username ?? '-');
                            $this->insertApprovalSticker($sheet, 'H34');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'H36');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'H38');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'sipil');
    }

    private function exportElectricP2h($id)
    {
        $path = public_path('assets/templates/maintenance/electric_p2h.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Electric P2h')
            ->where('id', $id)
            ->with('electricP2h', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            'level_minyak_rem' => 8,
            'level_oli_hydraulic' => 9,
            'isi_air_aki' => 10,
            'baterai' => 11,
            'hydraulic_system' => 12,
            'selang_hydraulic' => 13,
            'lift_chains' => 14,
            'fork' => 15,
            'body_unit' => 16,
            'lampu_kombinasi_kiri' => 17,
            'lampu_kombinasi_kanan' => 18,
            'lampu_sorot' => 19,
            'lampu_sign_depan_kanan' => 20,
            'lampu_sign_depan_kiri' => 21,
            'klakson' => 22,
            'buzzer_back' => 23,
            'kaca_spion' => 24,
            'baut_roda' => 25,
            'ban' => 26,
            'kebersihan_unit' => 27,
            'panel_display' => 28,
            'hours_meter' => 29,
            'sistem_kemudi' => 30,
        ];

        $shiftColumnMap = [
            1 => 'I',
            2 => 'J',
            3 => 'K',
        ];

        foreach ($data as $main) {
            $inspection = $main->electricP2h;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('D4', ': ' . $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('D5', ': ' . $main->electricP2h->no_unit ?? '-');
            $sheet->setCellValue('I4', $main->departemen ?? '-');
            $sheet->setCellValue('I31', 'Catatan : ' . $main->electricP2h->catatan ?? '-');

            foreach ($fieldRowMap as $field => $row) {

                $value = $inspection->{$field};

                $col = $shiftColumnMap[$inspection->shift] ?? 'I';

                // khusus hours_meter → langsung isi angka
                if ($field === 'hours_meter') {
                    $sheet->setCellValue($col . $row, $value ?? '');
                    continue;
                }

                // default kondisi
                if ($value === true) {
                    $kondisi = '✓';
                } elseif ($value === false) {
                    $kondisi = '✗';
                } else {
                    $kondisi = '';
                }

                $sheet->setCellValue($col . $row, $kondisi);
            }

            // Sticker Approval
            if ($main->approvals && $main->approvals->count()) {

                $shiftColumnMapApproval = [
                    1 => 'I',
                    2 => 'J',
                    3 => 'K',
                ];

                foreach ($main->approvals as $item) {

                    // hanya kalau approved
                    if ($item->status !== 'approved') continue;

                    // tentukan kolom berdasarkan shift
                    $col = $shiftColumnMapApproval[$item->shift] ?? null;
                    if (!$col) continue;

                    // tentukan baris berdasarkan role
                    switch (strtolower($item->role)) {

                        case 'teknisi':
                            $cell = $col . '31';
                            break;

                        case 'staff':
                        case 'supervisor':
                            $cell = $col . '37';
                            break;

                        default:
                            break;
                    }

                    // insert sticker
                    $this->insertApprovalSticker($sheet, $cell);
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'electric_p2h');
    }

    private function exportDieselP2h($id)
    {
        $path = public_path('assets/templates/maintenance/diesel_p2h.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Diesel P2h')
            ->where('id', $id)
            ->with('dieselP2h', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            'klakson' => 8,
            'buzzer_back' => 9,
            'oli_mesin' => 10,
            'radiator_hose' => 11,
            'water_pump' => 12,
            'injection_system' => 13,
            'fan_vbelt' => 14,
            'turbocharger_manifold' => 15,
            'tensioner_belt' => 16,
            'starting_motor' => 17,
            'alternator' => 18,
            'control_display' => 19,
            'oli_transmisi' => 20,
            'aki' => 21,
            'engine_mounting' => 22,
            'filter_oli_transmisi' => 23,
            'fungsi_rem' => 24,
            'fungsi_kopling' => 25,
            'oli_hydraulic' => 26,
            'hydraulic_system' => 27,
            'steering_system' => 28,
            'body_back_rest' => 29,
            'kaca_spion' => 30,
            'bucket_pin' => 31,
            'dump_pin_bushing' => 32,
            'seal_hydraulic' => 33,
            'roda_ban_baut' => 34,
            'lampu_unit' => 35,
            'baut_bearing_molen' => 36,
            'baut_hanger_as' => 37,
            'hours_meter' => 38,
            'baut_grease' => 40,
            'katup_pembuangan_angin' => 41,
        ];

        $shiftColumnMap = [
            1 => 'I',
            2 => 'J',
            3 => 'K',
        ];

        foreach ($data as $main) {
            $inspection = $main->dieselP2h;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('E4', ': ' . $main->dieselP2h->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('E5', ': ' . $main->dieselP2h->no_unit ?? '-');
            $sheet->setCellValue('I4', ': ' . $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('I5', ': ' . $main->departemen ?? '-');
            $sheet->setCellValue('I42', 'Catatan : ' . $main->dieselP2h->catatan ?? '-');

            foreach ($fieldRowMap as $field => $row) {

                $value = $inspection->{$field};

                $col = $shiftColumnMap[$inspection->shift] ?? 'I';

                // khusus hours_meter → langsung isi angka
                if ($field === 'hours_meter') {
                    $sheet->setCellValue($col . $row, $value ?? '');
                    continue;
                }

                // default kondisi
                if ($value === true) {
                    $kondisi = '✓';
                } elseif ($value === false) {
                    $kondisi = '✗';
                } else {
                    $kondisi = '';
                }

                $sheet->setCellValue($col . $row, $kondisi);
            }

            // Sticker Approval
            if ($main->approvals && $main->approvals->count()) {

                $shiftColumnMapApproval = [
                    1 => 'F',
                    2 => 'G',
                    3 => 'H',
                ];

                foreach ($main->approvals as $item) {

                    // hanya kalau approved
                    if ($item->status !== 'approved') continue;

                    // tentukan kolom berdasarkan shift
                    $col = $shiftColumnMapApproval[$item->shift] ?? null;
                    if (!$col) continue;

                    // tentukan baris berdasarkan role
                    switch (strtolower($item->role)) {

                        case 'teknisi':
                            $cell = $col . '43';
                            break;

                        case 'staff':
                        case 'supervisor':
                            $cell = $col . '45';
                            break;

                        default:
                            break;
                    }

                    // insert sticker
                    $this->insertApprovalSticker($sheet, $cell);
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'diesel_p2h');
    }

    private function downloadExcel($spreadsheet, $name)
    {
        $writer = new Xlsx($spreadsheet);

        $filename = $name . '_maintenance_' . date('YmdHis') . '.xlsx';
        $path = storage_path('app/public/exports/' . $filename);

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
}
