<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Epr\CorrectiveMaintenance;
use App\Models\Epr\CmCost;
use App\Models\Epr\CmMachineKpi;
use App\Models\Epr\CmActionPlan;
use App\Models\Epr\JenisDt;
use Carbon\Carbon;

class EprCmSeeder extends Seeder
{
    public function run(): void
    {
        $jenisDts = JenisDt::all();
        $jenisId = $jenisDts->first()?->id ?? 1;

        // Current Month (e.g. 2026-07) and previous months
        $currentMonth = date('Y-m'); // 2026-07

        // 1. Seed Corrective Maintenance Reports for 2026-07 and past months
        $machinesData = [
            ['mesin' => 'F2 / A', 'pouch_sachet' => 'Pouch', 'total_menit' => 478, 'grup' => 'A', 'shift' => '1', 'dt' => 'Trouble Conveyor Jammed'],
            ['mesin' => 'F2 / A', 'pouch_sachet' => 'Pouch', 'total_menit' => 320, 'grup' => 'B', 'shift' => '2', 'dt' => 'Motor overheated'],
            ['mesin' => 'D5 / H', 'pouch_sachet' => 'Sachet', 'total_menit' => 327, 'grup' => 'A', 'shift' => '1', 'dt' => 'Component Aus Roller'],
            ['mesin' => 'D5 / H', 'pouch_sachet' => 'Sachet', 'total_menit' => 210, 'grup' => 'C', 'shift' => '3', 'dt' => 'Settingan Sealer Tidak Stabil'],
            ['mesin' => 'D1 / D', 'pouch_sachet' => 'Pouch', 'total_menit' => 553, 'grup' => 'B', 'shift' => '2', 'dt' => 'Trouble Sensor Optical'],
            ['mesin' => 'D12 / AE', 'pouch_sachet' => 'Sachet', 'total_menit' => 410, 'grup' => 'A', 'shift' => '1', 'dt' => 'Gearbox Noise'],
            ['mesin' => 'D13 / AF', 'pouch_sachet' => 'Sachet', 'total_menit' => 258, 'grup' => 'C', 'shift' => '3', 'dt' => 'Pneumatic Cylinder Leaking'],
            ['mesin' => 'D7 / J', 'pouch_sachet' => 'Pouch', 'total_menit' => 333, 'grup' => 'B', 'shift' => '2', 'dt' => 'Heater Element Burned'],
            ['mesin' => 'D11 / B', 'pouch_sachet' => 'Sachet', 'total_menit' => 186, 'grup' => 'A', 'shift' => '1', 'dt' => 'Belt Conveyor Putus'],
            ['mesin' => 'D16 / AI', 'pouch_sachet' => 'Pouch', 'total_menit' => 211, 'grup' => 'C', 'shift' => '3', 'dt' => 'Vacuum Pump Loss Suction'],
            ['mesin' => 'D8 / K', 'pouch_sachet' => 'Sachet', 'total_menit' => 125, 'grup' => 'A', 'shift' => '1', 'dt' => 'Filling Nozzle Clogged'],
            ['mesin' => 'D17 / AJ', 'pouch_sachet' => 'Pouch', 'total_menit' => 48, 'grup' => 'B', 'shift' => '2', 'dt' => 'Limit Switch Misaligned'],
        ];

        // Clear existing CM data for current month before seeding
        CorrectiveMaintenance::where('tanggal', 'like', $currentMonth . '%')->delete();

        foreach ($machinesData as $idx => $m) {
            $day = str_pad(rand(1, 20), 2, '0', STR_PAD_LEFT);
            CorrectiveMaintenance::create([
                'tanggal' => "$currentMonth-$day",
                'shift' => $m['shift'],
                'grup' => $m['grup'],
                'mesin' => $m['mesin'],
                'pouch_sachet' => $m['pouch_sachet'],
                'jam_mulai' => '08:00',
                'jam_selesai' => date('H:i', strtotime("08:00 + {$m['total_menit']} minutes")),
                'total_menit' => $m['total_menit'],
                'keterangan' => 'Perbaikan darurat oleh tim mekanik/elektrik',
                'downtime' => $m['dt'],
                'jenis_dt_id' => $jenisId,
                'am_pm' => ($idx % 2 == 0) ? 'AM' : 'PM',
                'electrical_mechanical' => ($idx % 2 == 0) ? 'Mechanical' : 'Electrical',
            ]);
        }

        // Also seed previous 4 months for breakdown trend
        for ($i = 1; $i <= 4; $i++) {
            $pastM = date('Y-m', strtotime("-$i month"));
            if (CorrectiveMaintenance::where('tanggal', 'like', $pastM . '%')->count() == 0) {
                foreach ($machinesData as $idx => $m) {
                    $day = str_pad(rand(1, 25), 2, '0', STR_PAD_LEFT);
                    $factor = (5 - $i) * 0.2;
                    CorrectiveMaintenance::create([
                        'tanggal' => "$pastM-$day",
                        'shift' => $m['shift'],
                        'grup' => $m['grup'],
                        'mesin' => $m['mesin'],
                        'pouch_sachet' => $m['pouch_sachet'],
                        'jam_mulai' => '08:00',
                        'jam_selesai' => '10:00',
                        'total_menit' => round($m['total_menit'] * $factor),
                        'keterangan' => 'Historical downtime log',
                        'downtime' => $m['dt'],
                        'jenis_dt_id' => $jenisId,
                        'am_pm' => 'AM',
                        'electrical_mechanical' => 'Mechanical',
                    ]);
                }
            }
        }

        // 2. Seed Costs Table (`epr_cm_costs`)
        CmCost::where('tanggal', 'like', $currentMonth . '%')->delete();
        $costsData = [
            ['mesin' => 'D5 / H', 'kategori' => 'Sparepart', 'deskripsi' => 'Penggantian Roller Main Shaft', 'biaya' => 54100000],
            ['mesin' => 'D12 / AE', 'kategori' => 'Overhaul', 'deskripsi' => 'Overhaul Gearbox Drive Unit', 'biaya' => 17100000],
            ['mesin' => 'F2 / A', 'kategori' => 'Sparepart', 'deskripsi' => 'Replacement Belt & Motor Drive', 'biaya' => 15900000],
            ['mesin' => 'D1 / D', 'kategori' => 'Jasa', 'deskripsi' => 'Calibration & Optical Sensor Service', 'biaya' => 14000000],
            ['mesin' => 'D13 / AF', 'kategori' => 'Material', 'deskripsi' => 'Pneumatic Valves & Hosing Assembly', 'biaya' => 8900000],
            ['mesin' => 'D7 / J', 'kategori' => 'Sparepart', 'deskripsi' => 'Heater Element & Solid State Relay', 'biaya' => 7500000],
            ['mesin' => 'D17 / AJ', 'kategori' => 'Sparepart', 'deskripsi' => 'Limit Switch & Bracket Replacement', 'biaya' => 5200000],
        ];

        foreach ($costsData as $c) {
            CmCost::create([
                'mesin' => $c['mesin'],
                'tanggal' => "$currentMonth-10",
                'kategori_biaya' => $c['kategori'],
                'deskripsi' => $c['deskripsi'],
                'jumlah_biaya' => $c['biaya'],
            ]);
        }

        // 3. Seed Machine KPIs Table (`epr_cm_machine_kpis`)
        CmMachineKpi::where('month', $currentMonth)->delete();
        $kpiData = [
            ['mesin' => 'D17 / AJ', 'avail' => 88.0, 'perf' => 93.5, 'qual' => 99.3, 'oee' => 81.6, 'pm' => 95.0, 'repeat' => 5.2, 'minor' => 8.0, 'cost_hr' => 35.0, 'energy' => 0.32],
            ['mesin' => 'D8 / K',   'avail' => 87.0, 'perf' => 92.0, 'qual' => 98.8, 'oee' => 79.0, 'pm' => 94.0, 'repeat' => 8.1, 'minor' => 9.5, 'cost_hr' => 42.0, 'energy' => 0.35],
            ['mesin' => 'D7 / J',   'avail' => 82.0, 'perf' => 91.0, 'qual' => 98.5, 'oee' => 73.6, 'pm' => 91.0, 'repeat' => 12.0, 'minor' => 11.0, 'cost_hr' => 48.0, 'energy' => 0.38],
            ['mesin' => 'D11 / B',  'avail' => 83.0, 'perf' => 89.5, 'qual' => 98.2, 'oee' => 72.7, 'pm' => 90.0, 'repeat' => 14.2, 'minor' => 12.0, 'cost_hr' => 52.0, 'energy' => 0.37],
            ['mesin' => 'F2 / A',   'avail' => 78.0, 'perf' => 82.0, 'qual' => 96.5, 'oee' => 61.5, 'pm' => 85.0, 'repeat' => 24.5, 'minor' => 18.2, 'cost_hr' => 85.0, 'energy' => 0.45],
            ['mesin' => 'D5 / H',   'avail' => 84.0, 'perf' => 84.0, 'qual' => 97.0, 'oee' => 68.7, 'pm' => 88.0, 'repeat' => 19.8, 'minor' => 15.0, 'cost_hr' => 78.0, 'energy' => 0.42],
            ['mesin' => 'D1 / D',   'avail' => 85.0, 'perf' => 83.0, 'qual' => 97.0, 'oee' => 68.6, 'pm' => 86.0, 'repeat' => 21.0, 'minor' => 16.5, 'cost_hr' => 72.0, 'energy' => 0.41],
        ];

        foreach ($kpiData as $k) {
            CmMachineKpi::create([
                'month' => $currentMonth,
                'mesin' => $k['mesin'],
                'availability_pct' => $k['avail'],
                'performance_pct' => $k['perf'],
                'quality_pct' => $k['qual'],
                'oee_pct' => $k['oee'],
                'pm_compliance_pct' => $k['pm'],
                'repeat_failure_pct' => $k['repeat'],
                'minor_stop_freq' => $k['minor'],
                'cost_per_hour' => $k['cost_hr'],
                'energy_per_pack' => $k['energy'],
            ]);
        }

        // 4. Seed Action Plans Table (`epr_cm_action_plans`)
        CmActionPlan::where('month', $currentMonth)->delete();
        $actionPlansData = [
            [
                'mesin' => 'F2 / A',
                'isu' => 'Breakdown Tertinggi (8,67%)',
                'akar' => 'Trouble Conveyor Sering Jammed',
                'saran' => 'Overhaul Conveyor, Perbaiki Guide & Sensor Monitoring Harian',
                'pic' => 'MECH',
                'target' => "$currentMonth-17",
                'w1' => 'red', 'w2' => 'red', 'w3' => 'none', 'w4' => 'none', 'status' => 'Progress'
            ],
            [
                'mesin' => 'D5 / H',
                'isu' => 'Cost Tinggi & Breakdown',
                'akar' => 'Component Aus, Setting Tidak Stabil',
                'saran' => 'Root Cause Analysis, Ganti Part Aus, Setting & Standardisasi',
                'pic' => 'MECH',
                'target' => "$currentMonth-17",
                'w1' => 'red', 'w2' => 'red', 'w3' => 'none', 'w4' => 'none', 'status' => 'Progress'
            ],
            [
                'mesin' => 'D1 / D',
                'isu' => 'MTTR Tertinggi (553 menit)',
                'akar' => 'Proses Finding Problem Lama',
                'saran' => 'Improve Response, Checklist Troubleshooting, Tools Readiness',
                'pic' => 'MECH/EL',
                'target' => "$currentMonth-24",
                'w1' => 'none', 'w2' => 'orange', 'w3' => 'orange', 'w4' => 'none', 'status' => 'Open'
            ],
            [
                'mesin' => 'D12 / AE',
                'isu' => 'Cost Spike (Bulan Ini)',
                'akar' => 'Major Repair, Part Mahal',
                'saran' => 'Spare Part Audit, Planning Overhaul',
                'pic' => 'STORE',
                'target' => "$currentMonth-24",
                'w1' => 'none', 'w2' => 'yellow', 'w3' => 'yellow', 'w4' => 'none', 'status' => 'Open'
            ],
        ];

        foreach ($actionPlansData as $ap) {
            CmActionPlan::create([
                'month' => $currentMonth,
                'mesin' => $ap['mesin'],
                'isu_utama' => $ap['isu'],
                'akar_masalah' => $ap['akar'],
                'saran_perbaikan' => $ap['saran'],
                'pic' => $ap['pic'],
                'target_date' => $ap['target'],
                'w1_status' => $ap['w1'],
                'w2_status' => $ap['w2'],
                'w3_status' => $ap['w3'],
                'w4_status' => $ap['w4'],
                'status' => $ap['status'],
            ]);
        }
    }
}
