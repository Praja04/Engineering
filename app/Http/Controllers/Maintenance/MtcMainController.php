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
            ->where('jabatan', '!=', 'operator')
            ->where(function ($q) {
                $q->where('bagian', 'Engineering Maintenance & Improvement')
                    ->orWhere('bagian', 'Engineering');
            })
            ->get(['id', 'username']);

        $user = User::where('jabatan', 'supervisor')
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

    public function downloadMaintenanceData(Request $request, $jenis_mtc)
    {
        try {
            switch ($jenis_mtc) {
                case 'Motor Pompa':
                    return $this->exportMotorPump();
                case 'Utility':
                    return $this->exportUtility();
                case 'Electrical':
                    return $this->exportElectrical();
                case 'Refrigerasi':
                    return $this->exportRefrigerasi();
                case 'Electric Engine':
                    return $this->exportElectricEngine();
                case 'Diesel Engine':
                    return $this->exportDieselEngine();
                case 'Battery':
                    return $this->exportBattery();
                case 'Sipil':
                    return $this->exportSipil();
                case 'Electric P2h':
                    return $this->exportElectricP2h();
                case 'Diesel P2h':
                    return $this->exportDieselP2h();
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
        $drawing->setPath(public_path('assets/images/ttd/utility_approved_sticker.png'));
        $drawing->setHeight(20);
        $drawing->setCoordinates($cell);
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(2);
        $drawing->setWorksheet($sheet);
    }

    private function exportMotorPump()
    {
        $path = public_path('assets/templates/maintenance/motor_pump.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Motor Pompa')
            ->with('motorPump', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Motor
            'electrical_motor' => 6,
            'putaran_motor' => 7,
            'fibrasi_suara_motor' => 8,
            'bearing_motor' => 9,
            'pelumasan_motor' => 10,
            'kebersihan_unit_body_motor' => 11,

            // Pompa
            'putaran_pompa' => 14,
            'shaft_karet_coupling_pompa' => 15,
            'fan_belt_pompa' => 16,
            'pressure_pompa' => 17,
            'mechanical_seal_pompa' => 18,
            'gasket_pompa' => 19,
            'impeler' => 20,
            'kebersihan_unit_body_pompa' => 21,

            // Aksesoris
            'valve_aksesoris' => 24,
            'cek_valve_aksesoris' => 25,
            'flow_meter_aksesoris' => 26,
            'strainer_aksesoris' => 27,
            'alat_ukur_aksesoris' => 28,
            'kelengkapan_baut_mur_aksesoris' => 29,

            // Gearbox
            'tambah_ganti_oli_gearbox' => 31,
            'unit_area_gearbox' => 32,
            'oil_seal_gearbox' => 33,
            'filter_udara_gearbox' => 34,
            'bearing_gearbox' => 35,
        ];

        foreach ($data as $main) {
            $inspection = $main->motorPump;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('F3', $main->motorPump->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('A39', 'Tindakan Korektif : ' . $main->korektif);

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
                $sheet->setCellValue('E' . $row, $main->keterangan ?? '');
            }

            // 🔥 KEBUTUHAN MATERIAL
            $materialRow = 41;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('D' . $materialRow, $item->deskripsi ?? '');
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
                            $this->insertApprovalSticker($sheet, 'G51');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'G53');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'G55');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'motor_pump');
    }

    private function exportUtility()
    {
        $path = public_path('assets/templates/maintenance/utility.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Utility')
            ->with('utility', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Cooling Tower
            'cleaning_saringan_cooling_tower' => 6,
            'cleaning_unit_cooling_tower' => 7,
            'cleaning_bak_cooling_tower' => 8,

            // RO
            'check_sensor_tank_farm_ro_produk' => 10,
            'cleaning_flow_rate_mmf_1' => 11,
            'cleaning_flow_rate_mmf_2' => 12,
            'cleaning_flow_rate_ro_produk' => 13,
            'cleaning_flow_rate_ro_reject' => 14,
            'penggantian_micron_filter_cip' => 15,
            'penggantian_micron_filter_makeup_water' => 16,
            'cleaning_cip_tank' => 17,
            'cip_membrane_reverse_osmosis' => 18,
            'check_fungsi_valve' => 19,
            'cleaning_unit_ro_mesin' => 20,

            // Compressor
            'sirkulasi_phe_aq55vsd' => 22,
            'penggantian_air_ro_aq55vsd' => 23,
            'cleaning_compressor_aq55vsd' => 24,
            'cleaning_jalur_cooling_aq55vsd' => 25,
            'cleaning_dryer_fd185' => 26,
            'cleaning_compressor_ga37' => 27,
            'cleaning_dryer_fd120' => 28,
            'lubrikasi_motor_compressor_aq55vsd' => 29,
            'cleaning_compressor_sm55' => 30,

            // Tank Farm
            'cleaning_sensor_level_tank_farm' => 31,
            'cleaning_sensor_level_fresh_water_menara' => 32,
            'cleaning_sensor_level_ro_reject_menara' => 33,
            'cleaning_sensor_level_intermediate' => 34,

            // Boiler
            'check_safety_valve' => 36,
            'cleaning_level_gauge' => 37,
            'cleaning_level_transmitter' => 38,
            'check_pressure_transmitter' => 39,
            'check_temperature_transmitter' => 40,
            'cleaning_sensor_o2_co2' => 41,
            'check_chaingrate' => 42,
            'check_ruang_bakar' => 43,
            'check_back_chamber' => 44,
            'check_guillotine' => 45,
            'check_wet_ash_conveyor' => 46,
            'check_bottom_ash_conveyor' => 47,
            'check_conveyor_batu_bara' => 48,
            'check_feeder' => 49,
            'cleaning_bak_wet_ash_conveyor' => 50,
            'check_feed_tank' => 51,

            // WWTP
            'check_line_limbah' => 53,
            'check_line_chemical' => 54,
            'check_tangki_kotak' => 55,
            'check_tangki_bulat' => 56,
        ];

        foreach ($data as $main) {
            $inspection = $main->utility;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('F3', $main->utility->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('A65', 'Tindakan Korektif : ' . $main->korektif);

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
                $sheet->setCellValue('E' . $row, $main->keterangan ?? '');
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 67;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('D' . $materialRow, $item->deskripsi ?? '');
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
                            $this->insertApprovalSticker($sheet, 'G71');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'G73');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'G75');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'utility');
    }

    private function exportElectrical()
    {
        $path = public_path('assets/templates/maintenance/electrical.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Electrical')
            ->with('electrical', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Panel
            'check_kunci' => 6,
            'check_koneksi_kabel' => 7,
            'check_wiring_panel' => 8,
            'check_lampu_indikator' => 9,
            'check_name_plate' => 10,
            'check_unit_electrical' => 11,
            'check_grounding' => 12,
            'check_kebersihan' => 13,
            'check_bus_bar' => 14,
            'check_nilai_grounding' => 15,

            // Penerangan
            'check_kondisi_lampu' => 17,
            'check_cover_lampu' => 18,
            'check_wiring_penerangan' => 19,
            'check_saklar' => 20,
            'check_penyangga_penerangan' => 21,

            // Sistem Distribusi
            'check_stecker' => 24,
            'check_stop_kontak' => 25,
            'check_terminal_listrik' => 26,
            'check_pengabelan_distribusi' => 27,
            'check_support_pelindung_distribusi' => 28,

            // Capacitor Bank
            'check_kondisi_fisik_capacitor' => 31,
            'check_nilai_farad' => 32,
            'check_nilai_ampere' => 33,
            'check_kebersihan_capacitor' => 34,

            // Trafo
            'check_kebocoran_oli_sisi_bawah' => 37,
            'check_kebocoran_oli_sisi_atas' => 38,
            'check_level_oli' => 39,
        ];

        foreach ($data as $main) {
            $inspection = $main->electrical;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('F3', $main->electrical->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('A47', 'Tindakan Korektif : ' . $main->korektif);

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
                $sheet->setCellValue('E' . $row, $main->keterangan ?? '');
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 49;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('D' . $materialRow, $item->deskripsi ?? '');
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
                            $this->insertApprovalSticker($sheet, 'G55');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'G57');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'G59');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'electrical');
    }

    private function exportRefrigerasi()
    {
        $path = public_path('assets/templates/maintenance/refrigerasi.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Refrigerasi')
            ->with('refrigerasi', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Unit Indoor
            'check_filter_udara' => 6,
            'check_cover_filter_udara' => 7,
            'check_electrical_indoor' => 8,
            'check_suhu_evaporator' => 9,
            'check_indikator_display' => 10,
            'check_motor_blower' => 11,
            'check_fan_belt_blower' => 12,
            'check_pergerakan_motor_swing' => 13,
            'check_kontroler_indoor' => 14,
            'check_saluran_drain_kondensasi' => 15,
            'sirkulasi_evaporator' => 16,

            // Unit Outdoor
            'check_kondisi_kondensor' => 19,
            'check_electrical_outdoor' => 20,
            'check_motor_fan' => 21,
            'check_tekanan_freon' => 22,
            'pelumasan_motor_fan' => 23,
            'kebersihan_unit_body_outdoor' => 24,

            // Jalur Distribusi
            'check_jalur_freon' => 27,
            'check_jalur_distribusi_udara' => 28,
            'check_jalur_return_udara' => 29,
        ];

        foreach ($data as $main) {
            $inspection = $main->refrigerasi;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('F3', $main->refrigerasi->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('A33', 'Tindakan Korektif : ' . $main->korektif);

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
                $sheet->setCellValue('E' . $row, $main->keterangan ?? '');
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 35;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('D' . $materialRow, $item->deskripsi ?? '');
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
                            $this->insertApprovalSticker($sheet, 'G45');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'G47');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'G49');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'refrigerasi');
    }

    private function exportElectricEngine()
    {
        $path = public_path('assets/templates/maintenance/electric_engine.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Electric Engine')
            ->with('electricEngine', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // Forklift Electrical - General
            'check_buzzer_back' => 6,
            'check_klakson' => 7,
            'check_pilot_lamp' => 8,
            'check_lampu_sorot' => 9,
            'check_lampu_kombinasi_kanan_belakang' => 10,
            'check_lampu_kombinasi_kiri_belakang' => 11,
            'check_kaca_sepion' => 12,

            // Battery, Charger & Electrical System
            'check_battery' => 16,
            'check_skun_battery' => 17,
            'check_terminal_charger_battery' => 18,
            'check_kunci_kontak' => 19,
            'check_main_contactor' => 20,
            'check_microswitch' => 21,
            'check_eps_controller' => 22,
            'check_steering_motor' => 23,
            'check_fan' => 24,
            'check_fuse' => 25,
            'check_display_control' => 26,
            'check_wiring_terminal' => 27,
            'check_carbon_brush' => 28,

            // Drive, Steering, Mast, Hydraulic & Braking System
            'check_steering_wheel' => 30,
            'check_baut_roda' => 31,
            'check_drive_caster_load_wheel' => 32,
            'check_lift_chain' => 33,
            'check_lift_bracket' => 34,
            'check_hydraulic_hose' => 35,
            'check_motor_hydraulic_pump' => 36,
            'check_fork' => 37,
            'check_lift_rollers' => 38,
            'check_mast_rollers' => 39,
            'check_lift_cylinders' => 40,
            'check_tilt_cylinders' => 41,
            'check_control_valve' => 42,
            'check_hydraulic_tank' => 43,
            'check_overhead_guard' => 44,
            'check_all_bolt_nut' => 45,
            'check_power_steering' => 46,
            'check_brake_cam_adjust_bolt' => 47,
            'check_axle' => 48,
            'check_greasing_point' => 49,
            'check_air_spring' => 50,

            // Oil
            'ganti_gear_oil' => 53,
            'ganti_hydraulic_oil' => 54,
            'ganti_return_filter' => 55,
            'ganti_brake_oil' => 56,
        ];

        foreach ($data as $main) {
            $inspection = $main->electricEngine;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('F3', $main->electricEngine->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('A62', 'Tindakan Korektif : ' . $main->korektif);

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
                $sheet->setCellValue('F' . $row, $main->keterangan ?? '');
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 64;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('E' . $materialRow, $item->deskripsi ?? '');
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
                            $this->insertApprovalSticker($sheet, 'H69');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'H71');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'H73');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'electric_engine');
    }

    private function exportDieselEngine()
    {
        $path = public_path('assets/templates/maintenance/diesel_engine.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Diesel Engine')
            ->with('dieselEngine', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            // ENGINE
            'check_kondisi_level_oli_mesin' => 6,
            'check_kondisi_radiator_hose' => 7,
            'check_kondisi_level_air_radiator' => 8,
            'check_water_pump' => 9,
            'check_injection_pump_injector_piping' => 10,
            'check_turbocharger_manifold' => 11,
            'check_fan_v_belt' => 12,
            'check_automatic_tensioner_belt' => 13,
            'check_engine_mounting' => 14,
            'check_air_filter_condition' => 15,
            'check_clearence_valve_drain_valve' => 16,
            'check_engine_oil_filter' => 17,
            'check_air_radiator' => 18,
            'check_minyak_kopling' => 19,
            'check_fuel_filter' => 20,

            // ELECTRIC
            'check_kondisi_aki_level_air_aki' => 22,
            'check_fungsi_starting_motor' => 23,
            'check_fungsi_alternator' => 24,
            'check_sensor_sensor_gauge' => 25,
            'check_fuse_control_switch' => 26,
            'check_control_display' => 27,
            'check_indicator_wiring' => 28,

            // TRANSMISI / BRAKE / DRIVE SHAFT
            'check_kondisi_level_oli_transmisi' => 30,
            'check_fungsi_transmisi' => 31,
            'check_filter_oli_transmisi' => 32,
            'check_fungsi_rem' => 33,
            'check_oli_tidak_ada_yang_bocor' => 34,
            'check_kondisi_drive_shaft' => 35,

            // HYDRAULIC
            'check_kondisi_level_hydraulic_oil' => 37,
            'check_kondisi_hydraulic_oil_filter' => 38,
            'check_fungsi_hydraulic_system' => 39,
            'check_fungsi_steering_system' => 40,
            'check_kondisi_hydraulic_cylinder' => 41,
            'check_kondisi_steering_cylinder' => 42,
            'check_kondisi_axle_oil' => 43,
            'check_kondisi_baut_roda_hydraulic' => 44,
            'check_kondisi_bucket_pin_bucket' => 45,
            'check_kondisi_dump_pin_bushing' => 46,

            // GENERAL
            'check_klakson' => 48,
            'check_buzzer_back' => 49,
            'check_kondisi_basket_fresh_body' => 50,
            'check_kaca_sepion' => 51,
            'check_kondisi_roda_ban' => 52,
            'check_baut_roda_general' => 53,
            'check_lampu_depan_kanan' => 54,
            'check_lampu_depan_kiri' => 55,
            'check_baut_bearing_molen' => 56,
            'check_baut_hanger_as_roda' => 57,
        ];

        foreach ($data as $main) {
            $inspection = $main->dieselEngine;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('G3', $main->dieselEngine->mesin->nama_mesin ?? '-');
            $sheet->setCellValue('G4', $main->running_hour ?? '-');
            $sheet->setCellValue('A59', 'Tindakan Korektif : ' . $main->korektif);

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
                $sheet->setCellValue('F' . $row, $main->keterangan ?? '');
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 61;

            if ($main->kebutuhanMaterial && $main->kebutuhanMaterial->count()) {
                foreach ($main->kebutuhanMaterial as $item) {

                    $sheet->setCellValue('E' . $materialRow, $item->deskripsi ?? '');
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
                            $this->insertApprovalSticker($sheet, 'H71');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'H73');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'H75');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'diesel_engine');
    }

    private function exportBattery()
    {
        $path = public_path('assets/templates/maintenance/battery.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Battery')
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

    private function exportSipil()
    {
        $path = public_path('assets/templates/maintenance/sipil.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Sipil')
            ->with('sipil', 'kebutuhanMaterial', 'approvals')
            ->orderBy('tanggal', 'desc')
            ->get();

        $fieldRowMap = [
            'plumbing' => 7,
            'plafon' => 8,
            'lantai' => 9,
            'dinding' => 10,
            'jendela' => 11,
            'pintu' => 12,
            'rooling_fast_door' => 13,
        ];

        foreach ($data as $main) {
            $inspection = $main->sipil;
            if (!$inspection) continue;

            // header (sekali isi aja, bukan per row)
            $sheet->setCellValue('C3', $main->tanggal ? \Carbon\Carbon::parse($main->tanggal)->format('d-m-Y') : '-');
            $sheet->setCellValue('C4', $main->waktu_mulai ?? '-');
            $sheet->setCellValue('G3', $main->area ?? '-');
            $sheet->setCellValue('A15', 'Rekomendasi : ' . $main->rekomendasi);
            $sheet->setCellValue('A16', 'Tindakan Korektif : ' . $main->korektif);

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

                $sheet->setCellValue('F' . $row, $main->keterangan ?? '');
            }

            // KEBUTUHAN MATERIAL
            $materialRow = 18;

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
                            $this->insertApprovalSticker($sheet, 'H33');
                            break;

                        case 'staff':
                            $this->insertApprovalSticker($sheet, 'H35');
                            break;

                        case 'user':
                            $this->insertApprovalSticker($sheet, 'H37');
                            break;
                    }
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'diesel_engine');
    }

    private function exportElectricP2h()
    {
        $path = public_path('assets/templates/maintenance/electric_p2h.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Electric P2h')
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
                            continue;
                    }

                    // insert sticker
                    $this->insertApprovalSticker($sheet, $cell);
                }
            }

            break;
        }

        return $this->downloadExcel($spreadsheet, 'electric_p2h');
    }

    private function exportDieselP2h()
    {
        $path = public_path('assets/templates/maintenance/diesel_p2h.xlsx');

        if (!file_exists($path)) {
            return response()->json(['message' => 'Template not found'], 404);
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $data = MtcMainModel::where('jenis_mtc', 'Diesel P2h')
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
                            continue;
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
