<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Maintenance\MtcP2hModel;
use App\Models\Maintenance\MtcMasterMesinModel;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcGensetP2hInspectionModel;
use App\Models\Maintenance\MtcApprovalModel;
use App\Models\NotificationsModel;

class MtcP2hController extends Controller
{
    /**
     * Form khusus Genset P2H
     */
    public function form()
    {
        $mesin = MtcMasterMesinModel::where('aktif', true)
            ->where(function ($q) {
                $q->where('jenis_mtc', 'like', '%Genset%')
                    ->orWhere('nama_mesin', 'like', '%Genset%');
            })
            ->orderBy('id')
            ->get();

        return view('maintenance.p2h.p2h_form', compact('mesin'));
    }

    /**
     * Tampilan Data P2H (Warehouse Forklift & Pallet Mover, plus opsi filter)
     */
    public function data()
    {
        return view('maintenance.p2h.p2h_data');
    }

    /**
     * Menyimpan data Genset P2H dari form input manual Engineering
     */
    public function store(Request $request)
    {
        $request->validate([
            'mesin_id' => 'required|exists:mtc_master_mesin,id',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable',
            'departemen' => 'required|string',
            'shift' => 'required|integer|in:1,2,3',
            'catatan' => 'nullable|string',
            'staff_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $mesin = MtcMasterMesinModel::findOrFail($request->mesin_id);
        $tanggal = $request->tanggal;
        $shift = $request->shift;

        // Cek duplikasi Genset P2H
        $exists = MtcGensetP2hInspectionModel::where('shift', $shift)
            ->where('mesin_id', $mesin->id)
            ->whereHas('main', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal)
                    ->where('jenis_mtc', 'Genset P2H');
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Data Genset P2H untuk tanggal dan shift tersebut sudah ada.',
            ], 422);
        }

        DB::transaction(function () use ($request, $mesin) {
            $userId = Auth::id();

            $main = MtcMainModel::create([
                'jenis_mtc' => 'Genset P2H',
                'tanggal' => $request->tanggal,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'departemen' => $request->departemen,
                'status' => 'pending',
                'keterangan' => $request->keterangan,
                'created_by' => $userId,
            ]);

            $checklistFields = [
                'level_oli_mesin',
                'kebocoran_oli_mesin',
                'level_coolant_radiator',
                'kebocoran_coolant',
                'level_bahan_bakar',
                'kebocoran_bahan_bakar',
                'kondisi_aki_baterai',
                'tegangan_baterai',
                'filter_udara',
                'kondisi_panel_genset',
                'emergency_stop',
                'suara_mesin_running',
                'kebersihan_area_genset',
                'kondisi_knalpot_exhaust'
            ];

            $checklistData = [];
            foreach ($checklistFields as $field) {
                $val = $request->input($field);
                $checklistData[$field] = ($val !== null && $val !== '') ? (int)$val : null;
            }

            MtcGensetP2hInspectionModel::create(array_merge($checklistData, [
                'mtc_main_id' => $main->id,
                'mesin_id' => $mesin->id,
                'no_unit' => $request->no_unit ?: $mesin->kode_mesin,
                'shift' => $request->shift,
                'hours_meter' => $request->hours_meter,
                'catatan' => $request->catatan,
            ]));

            // Approval Flow untuk Genset
            $ttdPaths = [
                'teknisi' => 'mtc/ttd/ttd_teknisi.jpeg',
                'staff'   => 'mtc/ttd/ttd_staff.jpeg',
                'user'    => 'mtc/ttd/ttd_user.jpeg',
            ];

            $approvalFlows = [
                ['level' => 1, 'role' => 'teknisi', 'approver_id' => $userId, 'auto' => true],
                ['level' => 2, 'role' => 'staff', 'approver_id' => $request->staff_id, 'auto' => false],
                ['level' => 3, 'role' => 'user', 'approver_id' => $request->user_id, 'auto' => false],
            ];

            $notificationSent = false;
            foreach ($approvalFlows as $flow) {
                $isAuto = $flow['auto'];
                MtcApprovalModel::create([
                    'mtc_main_id' => $main->id,
                    'level'       => $flow['level'],
                    'role'        => $flow['role'],
                    'approver_id' => $flow['approver_id'],
                    'status'      => $isAuto ? 'approved' : 'pending',
                    'ttd'         => $isAuto ? ($ttdPaths[$flow['role']] ?? null) : null,
                    'action_at'   => $isAuto ? now() : null,
                    'action_by'   => $isAuto ? $userId : null,
                ]);

                if (!$isAuto && !$notificationSent) {
                    NotificationsModel::create([
                        'user_id'         => $flow['approver_id'],
                        'notifiable_type' => MtcMainModel::class,
                        'notifiable_id'   => $main->id,
                        'title'           => 'Approval Genset P2H',
                        'message'         => 'Maintenance Genset P2H tanggal ' . date('d F Y', strtotime($main->tanggal)) . ' menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);
                    $notificationSent = true;
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data Genset P2H berhasil disimpan',
        ]);
    }

    /**
     * Mengambil data P2H (dari mtc_p2h) untuk DataTable
     */
    public function getData(Request $request)
    {
        $query = MtcP2hModel::query()->with([
            'mesin:id,nama_mesin,kode_mesin,dept',
        ]);

        // Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // Filter nomor unit / mesin
        if ($request->filled('no_unit')) {
            $val = trim($request->no_unit);
            $query->where(function ($q) use ($val) {
                $q->where('nomor_unit', 'like', '%' . $val . '%')
                    ->orWhereHas('mesin', function ($sq) use ($val) {
                        $sq->where('nama_mesin', 'like', '%' . $val . '%')
                            ->orWhere('kode_mesin', 'like', '%' . $val . '%');
                    });
            });
        }

        // Filter shift
        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        // Filter departemen
        if ($request->filled('departemen')) {
            $query->where('dept', 'like', '%' . $request->departemen . '%');
        }

        // Filter jenis_p2h
        if ($request->filled('jenis_p2h')) {
            $query->where('jenis_p2h', $request->jenis_p2h);
        }

        $total = $query->count();

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        ]);
    }

    /**
     * Memperbarui data P2H
     */
    public function update(Request $request, $id)
    {
        $record = MtcP2hModel::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required',
            'hours_meter' => 'nullable|numeric',
            'catatan' => 'nullable|string',
        ]);

        $fields = [
            'cek_baterai',
            'cek_fork',
            'kondisi_body_kebersihan',
            'lampu_kiri',
            'lampu_kanan',
            'lampu_sorot',
            'lampu_sign_depan_kanan',
            'lampu_sign_depan_kiri',
            'kipas_belakang',
            'rantai_lift',
            'sistem_hidrolik',
            'kondisi_axle',
            'sistem_kemudi',
            'panel_display',
            'air_aki',
            'klakson',
            'buzzer_mundur',
            'kaca_spion',
            'kondisi_ban',
            'fungsi_rem',
            'check_kunci_pm',
            'check_kebersihan_unit'
        ];

        $checklistData = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $val = $request->input($field);
                $checklistData[$field] = ($val !== null && $val !== '') ? (bool)(int)$val : null;
            }
        }

        $record->update(array_merge($checklistData, [
            'tanggal' => $request->tanggal,
            'shift' => (string)$request->shift,
            'dept' => $request->departemen ?? $record->dept,
            'jam_operasional' => $request->hours_meter ?? $record->jam_operasional,
            'catatan' => $request->catatan,
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Data P2H berhasil diperbarui',
            'data' => $record,
        ]);
    }

    /**
     * Hapus data P2H
     */
    public function destroy($id)
    {
        $record = MtcP2hModel::findOrFail($id);
        $record->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data P2H berhasil dihapus',
        ]);
    }

    /**
     * Sinkronisasi data P2H dari Warehouse API (/api/p2h/all-data?format=separate)
     */
    public function syncWarehouse(Request $request)
    {
        try {
            // Bisa menerima payload langsung jika webhook / testing
            if ($request->has('data') && is_array($request->input('data'))) {
                $payload = $request->input('data');
            } else {
                $baseUrl = env('WAREHOUSE_BASE_URL', 'http://127.0.0.1:8081');
                $baseUrl = rtrim($baseUrl, '/');
                $apiUrl = "{$baseUrl}/api/p2h/all-data?format=separate";

                Log::info("Sinkronisasi data P2H Warehouse dari: {$apiUrl}");

                $response = Http::timeout(30)->get($apiUrl);

                if (!$response->successful()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Gagal menghubungi API Warehouse (HTTP ' . $response->status() . '). Pastikan server warehouse aktif di: ' . $apiUrl,
                    ], 500);
                }

                $json = $response->json();
                $payload = $json['data'] ?? [];
            }

            $forklifts = $payload['forklift'] ?? [];
            $palletMovers = $payload['pallet_mover'] ?? [];

            $savedForklift = 0;
            $savedPalletMover = 0;

            DB::beginTransaction();

            // 1. Mapping data Forklift
            foreach ($forklifts as $item) {
                $nomorUnit = $item['nomor_unit'] ?? '';
                $mesinId = $this->resolveMesinId($nomorUnit, $item['dept'] ?? null);

                $mapped = [
                    'warehouse_id' => $item['id'] ?? null,
                    'mesin_id' => $mesinId,
                    'nomor_unit' => $nomorUnit,
                    'dept' => $item['dept'] ?? 'Warehouse',
                    'tanggal' => $item['tanggal'] ?? date('Y-m-d'),
                    'shift' => (string)($item['shift'] ?? '1'),
                    'jenis_p2h' => $item['jenis_p2h'] ?? 'Forklift',
                    'operator_name' => $item['operator_name'] ?? null,
                    'jam_operasional' => $item['jam_operasional'] ?? null,
                    'persentase' => isset($item['persentase']) ? $item['persentase'] : ($item['kelayakan']['persentase'] ?? null),
                    'status_kelayakan' => $item['kelayakan']['status'] ?? null,
                    'foto_kondisi_accu' => $item['foto_kondisi_accu'] ?? null,
                    'catatan' => $item['catatan'] ?? null,

                    // Checklist
                    'cek_baterai' => isset($item['cek_baterai']) ? (bool)$item['cek_baterai'] : null,
                    'cek_fork' => isset($item['cek_fork']) ? (bool)$item['cek_fork'] : null,
                    'kondisi_body_kebersihan' => isset($item['kondisi_body_kebersihan']) ? (bool)$item['kondisi_body_kebersihan'] : null,
                    'lampu_kiri' => isset($item['lampu_kiri']) ? (bool)$item['lampu_kiri'] : null,
                    'lampu_kanan' => isset($item['lampu_kanan']) ? (bool)$item['lampu_kanan'] : null,
                    'lampu_sorot' => isset($item['lampu_sorot']) ? (bool)$item['lampu_sorot'] : null,
                    'lampu_sign_depan_kanan' => isset($item['lampu_sign_depan_kanan']) ? (bool)$item['lampu_sign_depan_kanan'] : null,
                    'lampu_sign_depan_kiri' => isset($item['lampu_sign_depan_kiri']) ? (bool)$item['lampu_sign_depan_kiri'] : null,
                    'kipas_belakang' => isset($item['kipas_belakang']) ? (bool)$item['kipas_belakang'] : null,
                    'rantai_lift' => isset($item['rantai_lift']) ? (bool)$item['rantai_lift'] : null,
                    'sistem_hidrolik' => isset($item['sistem_hidrolik']) ? (bool)$item['sistem_hidrolik'] : null,
                    'kondisi_axle' => isset($item['kondisi_axle']) ? (bool)$item['kondisi_axle'] : null,
                    'sistem_kemudi' => isset($item['sistem_kemudi']) ? (bool)$item['sistem_kemudi'] : null,
                    'panel_display' => isset($item['panel_display']) ? (bool)$item['panel_display'] : null,
                    'air_aki' => isset($item['air_aki']) ? (bool)$item['air_aki'] : null,
                    'klakson' => isset($item['klakson']) ? (bool)$item['klakson'] : null,
                    'buzzer_mundur' => isset($item['buzzer_mundur']) ? (bool)$item['buzzer_mundur'] : null,
                    'kaca_spion' => isset($item['kaca_spion']) ? (bool)$item['kaca_spion'] : null,
                    'kondisi_ban' => isset($item['kondisi_ban']) ? (bool)$item['kondisi_ban'] : null,
                    'fungsi_rem' => isset($item['fungsi_rem']) ? (bool)$item['fungsi_rem'] : null,
                ];

                if (!empty($mapped['warehouse_id'])) {
                    MtcP2hModel::updateOrCreate(
                        [
                            'warehouse_id' => $mapped['warehouse_id'],
                            'jenis_p2h' => $mapped['jenis_p2h'],
                        ],
                        $mapped
                    );
                } else {
                    MtcP2hModel::updateOrCreate(
                        [
                            'nomor_unit' => $mapped['nomor_unit'],
                            'tanggal' => $mapped['tanggal'],
                            'shift' => $mapped['shift'],
                            'jenis_p2h' => $mapped['jenis_p2h'],
                        ],
                        $mapped
                    );
                }
                $savedForklift++;
            }

            // 2. Mapping data Pallet Mover
            foreach ($palletMovers as $item) {
                $nomorUnit = $item['nomor_unit'] ?? '';
                $mesinId = $this->resolveMesinId($nomorUnit, $item['dept'] ?? null);

                $mapped = [
                    'warehouse_id' => $item['id'] ?? null,
                    'mesin_id' => $mesinId,
                    'nomor_unit' => $nomorUnit,
                    'dept' => $item['dept'] ?? 'Warehouse',
                    'tanggal' => $item['tanggal'] ?? date('Y-m-d'),
                    'shift' => (string)($item['shift'] ?? '1'),
                    'jenis_p2h' => $item['jenis_p2h'] ?? 'Pallet Mover',
                    'operator_name' => $item['operator_name'] ?? null,
                    'persentase' => $item['kelayakan']['persentase'] ?? null,
                    'status_kelayakan' => $item['kelayakan']['status'] ?? null,
                    'foto_kondisi_accu' => $item['foto_kondisi_accu'] ?? null,
                    'catatan' => $item['catatan'] ?? null,

                    // Checklist Pallet Mover
                    'air_aki' => isset($item['check_air_accu']) ? (bool)$item['check_air_accu'] : null,
                    'cek_baterai' => isset($item['check_battery']) ? (bool)$item['check_battery'] : null,
                    'kondisi_body_kebersihan' => isset($item['check_body_unit']) ? (bool)$item['check_body_unit'] : null,
                    'klakson' => isset($item['check_klakson']) ? (bool)$item['check_klakson'] : null,
                    'kondisi_ban' => isset($item['check_roda']) ? (bool)$item['check_roda'] : null,
                    'sistem_kemudi' => isset($item['check_sistem_kemudi']) ? (bool)$item['check_sistem_kemudi'] : null,
                    'check_kebersihan_unit' => isset($item['check_kebersihan_unit']) ? (bool)$item['check_kebersihan_unit'] : null,
                    'check_kunci_pm' => isset($item['check_kunci_pm']) ? (bool)$item['check_kunci_pm'] : null,
                    'sistem_hidrolik' => isset($item['check_hydraulic']) ? (bool)$item['check_hydraulic'] : null,
                ];

                if (!empty($mapped['warehouse_id'])) {
                    MtcP2hModel::updateOrCreate(
                        [
                            'warehouse_id' => $mapped['warehouse_id'],
                            'jenis_p2h' => $mapped['jenis_p2h'],
                        ],
                        $mapped
                    );
                } else {
                    MtcP2hModel::updateOrCreate(
                        [
                            'nomor_unit' => $mapped['nomor_unit'],
                            'tanggal' => $mapped['tanggal'],
                            'shift' => $mapped['shift'],
                            'jenis_p2h' => $mapped['jenis_p2h'],
                        ],
                        $mapped
                    );
                }
                $savedPalletMover++;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Sinkronisasi P2H Warehouse berhasil! Diperbarui: {$savedForklift} Forklift & {$savedPalletMover} Pallet Mover.",
                'data' => [
                    'total_forklift' => $savedForklift,
                    'total_pallet_mover' => $savedPalletMover,
                    'total' => $savedForklift + $savedPalletMover,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncWarehouse P2H: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Gagal sinkronisasi data P2H: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mencocokkan nomor_unit dari API dengan kode_mesin di mtc_master_mesin
     * khusus untuk jenis_mtc 'Diesel P2H' dan 'Electric P2H'.
     */
    public function resolveMesinId($nomorUnit, $dept = null)
    {
        if (empty($nomorUnit)) {
            return null;
        }

        $nomorUnit = trim($nomorUnit);
        $cleanUnit = strtoupper(str_replace(['-', ' ', '_'], '', $nomorUnit));
        $p2hTypes = ['Diesel P2H', 'Electric P2H', 'Electrical P2H'];

        // Buat variasi kode untuk pencocokan leading zero (misal F1 <-> F01, PM3 <-> PM03)
        $variants = [$nomorUnit, $cleanUnit];
        if (preg_match('/^([A-Z]+)(\d+)$/', $cleanUnit, $m)) {
            $prefix = $m[1];
            $num = (int)$m[2];
            $variants[] = $prefix . $num;
            $variants[] = $prefix . sprintf('%02d', $num);
        }
        $variants = array_unique($variants);

        // 1. Exact match kode_mesin pada jenis_mtc Diesel P2H / Electric P2H
        $mesin = MtcMasterMesinModel::whereIn('jenis_mtc', $p2hTypes)
            ->where(function ($q) use ($variants) {
                foreach ($variants as $v) {
                    $q->orWhere('kode_mesin', $v);
                }
            })
            ->first();

        if ($mesin) {
            return $mesin->id;
        }

        // 2. Prefix match kode_mesin (contoh: 'PM04' cocok dengan 'PM04-WRH' jika ada di P2H)
        $mesin = MtcMasterMesinModel::whereIn('jenis_mtc', $p2hTypes)
            ->where(function ($q) use ($variants) {
                foreach ($variants as $v) {
                    $q->orWhere('kode_mesin', 'like', $v . '-%')
                      ->orWhere('kode_mesin', 'like', $v . ' %');
                }
            })
            ->first();

        if ($mesin) {
            return $mesin->id;
        }

        // 3. Match berdasarkan nama_mesin pada jenis_mtc P2H sesuai tipe unit
        if (isset($m) && $m) {
            $prefix = $m[1];
            $digits = $m[2];
            $intNum = (int)$m[2];
            $query = MtcMasterMesinModel::whereIn('jenis_mtc', $p2hTypes);

            $matchedType = false;
            if ($prefix === 'F') {
                $query->where('nama_mesin', 'like', '%Forklift%');
                $matchedType = true;
            } elseif ($prefix === 'PM') {
                $query->where('nama_mesin', 'like', '%Pallet%');
                $matchedType = true;
            } elseif ($prefix === 'ES') {
                $query->where(function ($sq) {
                    $sq->where('nama_mesin', 'like', '%Stacker%')
                       ->orWhere('nama_mesin', 'like', '%Stecker%');
                });
                $matchedType = true;
            }

            if ($matchedType) {
                $query->where(function ($sq) use ($digits, $intNum) {
                    $sq->where('nama_mesin', 'like', '% ' . $digits . '%')
                       ->orWhere('nama_mesin', 'like', '% ' . $intNum . '%')
                       ->orWhere('nama_mesin', 'like', '%.' . $digits . '%')
                       ->orWhere('nama_mesin', 'like', '%.' . $intNum . '%');
                });

                $mesin = $query->first();
                if ($mesin) {
                    return $mesin->id;
                }
            }
        }

        return null;
    }
}
