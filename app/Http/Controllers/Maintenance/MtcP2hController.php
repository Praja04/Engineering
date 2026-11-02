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
use Carbon\Carbon;

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
     * Sinkronisasi data P2H dari Warehouse API
     */
    public function syncWarehouse(Request $request)
    {
        $customData = ($request->has('data') && is_array($request->input('data'))) ? $request->input('data') : null;
        $startDate = $request->input('start_date');
        $result = $this->performSyncP2h('Warehouse', 'http://127.0.0.1:8081', 'WAREHOUSE_BASE_URL', $customData, 'Warehouse', $startDate);

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Sinkronisasi data P2H dari Production API
     */
    public function syncProduction(Request $request)
    {
        $customData = ($request->has('data') && is_array($request->input('data'))) ? $request->input('data') : null;
        $startDate = $request->input('start_date');
        $result = $this->performSyncP2h('Production', 'http://127.0.0.1:8082', 'PRODUCTION_BASE_URL', $customData, 'Produksi', $startDate);

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Sinkronisasi data P2H dari Warehouse & Production sekaligus
     */
    public function syncAll(Request $request)
    {
        $startDate = $request->input('start_date');
        $whResult = $this->performSyncP2h('Warehouse', 'http://127.0.0.1:8081', 'WAREHOUSE_BASE_URL', null, 'Warehouse', $startDate);
        $prdResult = $this->performSyncP2h('Production', 'http://127.0.0.1:8082', 'PRODUCTION_BASE_URL', null, 'Produksi', $startDate);

        $whSuccess = $whResult['status'] ?? false;
        $prdSuccess = $prdResult['status'] ?? false;

        $whForklift = $whResult['data']['total_forklift'] ?? 0;
        $whPM = $whResult['data']['total_pallet_mover'] ?? 0;
        $prdForklift = $prdResult['data']['total_forklift'] ?? 0;
        $prdPM = $prdResult['data']['total_pallet_mover'] ?? 0;

        $totalForklift = $whForklift + $prdForklift;
        $totalPM = $whPM + $prdPM;
        $grandTotal = $totalForklift + $totalPM;

        $messages = [];
        if ($whSuccess) {
            $messages[] = "Warehouse: {$whResult['message']}";
        } else {
            $messages[] = "Warehouse Gagal: {$whResult['message']}";
        }

        if ($prdSuccess) {
            $messages[] = "Production: {$prdResult['message']}";
        } else {
            $messages[] = "Production Gagal: {$prdResult['message']}";
        }

        $overallStatus = $whSuccess || $prdSuccess;

        return response()->json([
            'status' => $overallStatus,
            'message' => implode(" | ", $messages),
            'data' => [
                'warehouse' => $whResult['data'] ?? null,
                'production' => $prdResult['data'] ?? null,
                'total_forklift' => $totalForklift,
                'total_pallet_mover' => $totalPM,
                'total' => $grandTotal,
            ]
        ], $overallStatus ? 200 : 500);
    }

    /**
     * Helper umum untuk menyinkronkan data P2H dari endpoint API atau payload
     */
    private function performSyncP2h(string $source, string $defaultBaseUrl, string $envKey, ?array $customData = null, string $defaultDept = 'Production', ?string $manualStartDate = null): array
    {
        // Tentukan tanggal mulai sync (H-1 dari tanggal data terakhir yang ada di database)
        // Dipisahkan secara ketat per-source (Warehouse vs Production) agar tidak saling mempengaruhi
        $startDate = $manualStartDate;
        $latestDate = null;

        if (empty($startDate)) {
            if (strcasecmp($source, 'Warehouse') === 0) {
                // Khusus data Warehouse: cek source Warehouse atau record yang memiliki warehouse_id
                $latestDate = MtcP2hModel::where(function ($q) {
                    $q->where('source', 'Warehouse')
                        ->orWhereNotNull('warehouse_id');
                })->max('tanggal');
            } elseif (strcasecmp($source, 'Production') === 0) {
                // Khusus data Production: cek source Production atau record yang memiliki production_id
                $latestDate = MtcP2hModel::where(function ($q) {
                    $q->where('source', 'Production')
                        ->orWhereNotNull('production_id');
                })->max('tanggal');
            } else {
                $latestDate = MtcP2hModel::where('source', $source)->max('tanggal');
            }

            // Jika source ini sudah memiliki riwayat data di database, tarik mulai H-1 dari tanggal terakhir source tersebut
            // Jika source ini belum memiliki data sama sekali ($latestDate == null), jangan gunakan fallback ke source lain!
            // Biarkan $startDate = null agar menarik semua data (get all) khusus untuk source yang belum ada data ini.
            if ($latestDate) {
                $startDate = Carbon::parse($latestDate)->subDay()->format('Y-m-d');
            }
        }

        // 1. Dapatkan payload
        if ($customData !== null && is_array($customData)) {
            $payload = $customData;
        } else {
            $baseUrl = env($envKey, $defaultBaseUrl);
            $baseUrl = rtrim($baseUrl, '/');
            $apiUrl = "{$baseUrl}/api/p2h/all-data";

            $queryParams = [
                'format' => 'separate',
            ];
            if (!empty($startDate)) {
                $queryParams['start_date'] = $startDate;
            }

            Log::info("Sinkronisasi data P2H {$source} dari: {$apiUrl}", [
                'params' => $queryParams,
                'tgl_terakhir' => $latestDate,
                'mulai_h_minus_1' => $startDate ?? 'SEMUA',
            ]);

            try {
                // Coba ambil format separate dengan parameter filter start_date
                $response = Http::timeout(30)->get($apiUrl, $queryParams);
                if (!$response->successful()) {
                    // Fallback tanpa format=separate jika API format lama
                    $fallbackParams = $queryParams;
                    unset($fallbackParams['format']);
                    $response = Http::timeout(30)->get($apiUrl, $fallbackParams);
                }
            } catch (\Exception $e) {
                return [
                    'status' => false,
                    'message' => "Gagal terhubung ke API {$source} ({$apiUrl}): " . $e->getMessage(),
                ];
            }

            if (!$response->successful()) {
                return [
                    'status' => false,
                    'message' => "Gagal menghubungi API {$source} (HTTP {$response->status()}). Pastikan server {$source} aktif di: {$apiUrl}",
                ];
            }

            $json = $response->json();
            $payload = $json['data'] ?? [];
        }

        // 2. Pilah antara forklift dan pallet mover
        $forklifts = [];
        $palletMovers = [];

        if (isset($payload['forklift']) || isset($payload['pallet_mover'])) {
            $forklifts = $payload['forklift'] ?? [];
            $palletMovers = $payload['pallet_mover'] ?? [];
        } elseif (is_array($payload)) {
            // Payload berupa array list data flat (sesuai format JSON response)
            foreach ($payload as $item) {
                $jenis = strtolower($item['jenis_p2h'] ?? '');
                if (str_contains($jenis, 'pallet') || str_contains($jenis, 'pm') || str_contains($jenis, 'mover')) {
                    $palletMovers[] = $item;
                } else {
                    $forklifts[] = $item;
                }
            }
        }

        $savedForklift = 0;
        $savedPalletMover = 0;

        DB::beginTransaction();
        try {
            // 3. Mapping data Forklift
            foreach ($forklifts as $item) {
                $nomorUnit = $item['nomor_unit'] ?? '';
                $itemDept = $item['dept'] ?? $defaultDept;
                $mesinId = $this->resolveMesinId($nomorUnit, $itemDept);

                $mapped = [
                    'mesin_id' => $mesinId,
                    'nomor_unit' => $nomorUnit,
                    'dept' => $itemDept,
                    'source' => $source,
                    'tanggal' => $item['tanggal'] ?? date('Y-m-d'),
                    'shift' => (string)($item['shift'] ?? '1'),
                    'jenis_p2h' => $item['jenis_p2h'] ?? 'Forklift',
                    'operator_name' => $item['operator_name'] ?? null,
                    'jam_operasional' => $item['jam_operasional'] ?? null,
                    'persentase' => isset($item['persentase']) ? $item['persentase'] : ($item['kelayakan']['persentase'] ?? null),
                    'status_kelayakan' => $item['kelayakan']['status'] ?? null,
                    'foto_kondisi_accu' => $item['foto_kondisi_accu'] ?? null,
                    'catatan' => $item['catatan'] ?? null,

                    // Checklist Forklift
                    'cek_baterai' => isset($item['cek_baterai']) ? (bool)$item['cek_baterai'] : (isset($item['check_battery']) ? (bool)$item['check_battery'] : null),
                    'cek_fork' => isset($item['cek_fork']) ? (bool)$item['cek_fork'] : (isset($item['check_fork']) ? (bool)$item['check_fork'] : null),
                    'kondisi_body_kebersihan' => isset($item['kondisi_body_kebersihan']) ? (bool)$item['kondisi_body_kebersihan'] : (isset($item['check_body_unit']) ? (bool)$item['check_body_unit'] : null),
                    'lampu_kiri' => isset($item['lampu_kiri']) ? (bool)$item['lampu_kiri'] : null,
                    'lampu_kanan' => isset($item['lampu_kanan']) ? (bool)$item['lampu_kanan'] : null,
                    'lampu_sorot' => isset($item['lampu_sorot']) ? (bool)$item['lampu_sorot'] : null,
                    'lampu_sign_depan_kanan' => isset($item['lampu_sign_depan_kanan']) ? (bool)$item['lampu_sign_depan_kanan'] : null,
                    'lampu_sign_depan_kiri' => isset($item['lampu_sign_depan_kiri']) ? (bool)$item['lampu_sign_depan_kiri'] : null,
                    'kipas_belakang' => isset($item['kipas_belakang']) ? (bool)$item['kipas_belakang'] : null,
                    'rantai_lift' => isset($item['rantai_lift']) ? (bool)$item['rantai_lift'] : null,
                    'sistem_hidrolik' => isset($item['sistem_hidrolik']) ? (bool)$item['sistem_hidrolik'] : (isset($item['check_hydraulic']) ? (bool)$item['check_hydraulic'] : null),
                    'kondisi_axle' => isset($item['kondisi_axle']) ? (bool)$item['kondisi_axle'] : null,
                    'sistem_kemudi' => isset($item['sistem_kemudi']) ? (bool)$item['sistem_kemudi'] : (isset($item['check_sistem_kemudi']) ? (bool)$item['check_sistem_kemudi'] : null),
                    'panel_display' => isset($item['panel_display']) ? (bool)$item['panel_display'] : null,
                    'air_aki' => isset($item['air_aki']) ? (bool)$item['air_aki'] : (isset($item['check_air_accu']) ? (bool)$item['check_air_accu'] : null),
                    'klakson' => isset($item['klakson']) ? (bool)$item['klakson'] : (isset($item['check_klakson']) ? (bool)$item['check_klakson'] : null),
                    'buzzer_mundur' => isset($item['buzzer_mundur']) ? (bool)$item['buzzer_mundur'] : null,
                    'kaca_spion' => isset($item['kaca_spion']) ? (bool)$item['kaca_spion'] : null,
                    'kondisi_ban' => isset($item['kondisi_ban']) ? (bool)$item['kondisi_ban'] : (isset($item['check_roda']) ? (bool)$item['check_roda'] : null),
                    'fungsi_rem' => isset($item['fungsi_rem']) ? (bool)$item['fungsi_rem'] : null,
                ];

                if ($source === 'Warehouse') {
                    $mapped['warehouse_id'] = $item['id'] ?? null;
                    if (!empty($mapped['warehouse_id'])) {
                        MtcP2hModel::updateOrCreate(
                            ['warehouse_id' => $mapped['warehouse_id'], 'jenis_p2h' => $mapped['jenis_p2h']],
                            $mapped
                        );
                    } else {
                        MtcP2hModel::updateOrCreate(
                            ['nomor_unit' => $mapped['nomor_unit'], 'tanggal' => $mapped['tanggal'], 'shift' => $mapped['shift'], 'jenis_p2h' => $mapped['jenis_p2h'], 'dept' => $mapped['dept']],
                            $mapped
                        );
                    }
                } elseif ($source === 'Production') {
                    $mapped['production_id'] = $item['id'] ?? null;
                    if (!empty($mapped['production_id'])) {
                        MtcP2hModel::updateOrCreate(
                            ['production_id' => $mapped['production_id'], 'jenis_p2h' => $mapped['jenis_p2h']],
                            $mapped
                        );
                    } else {
                        MtcP2hModel::updateOrCreate(
                            ['nomor_unit' => $mapped['nomor_unit'], 'tanggal' => $mapped['tanggal'], 'shift' => $mapped['shift'], 'jenis_p2h' => $mapped['jenis_p2h'], 'dept' => $mapped['dept']],
                            $mapped
                        );
                    }
                }

                $savedForklift++;
            }

            // 4. Mapping data Pallet Mover
            foreach ($palletMovers as $item) {
                $nomorUnit = $item['nomor_unit'] ?? '';
                $itemDept = $item['dept'] ?? $defaultDept;
                $mesinId = $this->resolveMesinId($nomorUnit, $itemDept);

                $mapped = [
                    'mesin_id' => $mesinId,
                    'nomor_unit' => $nomorUnit,
                    'dept' => $itemDept,
                    'source' => $source,
                    'tanggal' => $item['tanggal'] ?? date('Y-m-d'),
                    'shift' => (string)($item['shift'] ?? '1'),
                    'jenis_p2h' => $item['jenis_p2h'] ?? 'Pallet Mover',
                    'operator_name' => $item['operator_name'] ?? null,
                    'jam_operasional' => $item['jam_operasional'] ?? null,
                    'persentase' => isset($item['persentase']) ? $item['persentase'] : ($item['kelayakan']['persentase'] ?? null),
                    'status_kelayakan' => $item['kelayakan']['status'] ?? null,
                    'foto_kondisi_accu' => $item['foto_kondisi_accu'] ?? null,
                    'catatan' => $item['catatan'] ?? null,

                    // Checklist Pallet Mover
                    'air_aki' => isset($item['check_air_accu']) ? (bool)$item['check_air_accu'] : (isset($item['air_aki']) ? (bool)$item['air_aki'] : null),
                    'cek_baterai' => isset($item['check_battery']) ? (bool)$item['check_battery'] : (isset($item['cek_baterai']) ? (bool)$item['cek_baterai'] : null),
                    'kondisi_body_kebersihan' => isset($item['check_body_unit']) ? (bool)$item['check_body_unit'] : (isset($item['kondisi_body_kebersihan']) ? (bool)$item['kondisi_body_kebersihan'] : null),
                    'klakson' => isset($item['check_klakson']) ? (bool)$item['check_klakson'] : (isset($item['klakson']) ? (bool)$item['klakson'] : null),
                    'kondisi_ban' => isset($item['check_roda']) ? (bool)$item['check_roda'] : (isset($item['kondisi_ban']) ? (bool)$item['kondisi_ban'] : null),
                    'sistem_kemudi' => isset($item['check_sistem_kemudi']) ? (bool)$item['check_sistem_kemudi'] : (isset($item['sistem_kemudi']) ? (bool)$item['sistem_kemudi'] : null),
                    'check_kebersihan_unit' => isset($item['check_kebersihan_unit']) ? (bool)$item['check_kebersihan_unit'] : null,
                    'check_kunci_pm' => isset($item['check_kunci_pm']) ? (bool)$item['check_kunci_pm'] : null,
                    'sistem_hidrolik' => isset($item['check_hydraulic']) ? (bool)$item['check_hydraulic'] : (isset($item['sistem_hidrolik']) ? (bool)$item['sistem_hidrolik'] : null),
                ];

                if ($source === 'Warehouse') {
                    $mapped['warehouse_id'] = $item['id'] ?? null;
                    if (!empty($mapped['warehouse_id'])) {
                        MtcP2hModel::updateOrCreate(
                            ['warehouse_id' => $mapped['warehouse_id'], 'jenis_p2h' => $mapped['jenis_p2h']],
                            $mapped
                        );
                    } else {
                        MtcP2hModel::updateOrCreate(
                            ['nomor_unit' => $mapped['nomor_unit'], 'tanggal' => $mapped['tanggal'], 'shift' => $mapped['shift'], 'jenis_p2h' => $mapped['jenis_p2h'], 'dept' => $mapped['dept']],
                            $mapped
                        );
                    }
                } elseif ($source === 'Production') {
                    $mapped['production_id'] = $item['id'] ?? null;
                    if (!empty($mapped['production_id'])) {
                        MtcP2hModel::updateOrCreate(
                            ['production_id' => $mapped['production_id'], 'jenis_p2h' => $mapped['jenis_p2h']],
                            $mapped
                        );
                    } else {
                        MtcP2hModel::updateOrCreate(
                            ['nomor_unit' => $mapped['nomor_unit'], 'tanggal' => $mapped['tanggal'], 'shift' => $mapped['shift'], 'jenis_p2h' => $mapped['jenis_p2h'], 'dept' => $mapped['dept']],
                            $mapped
                        );
                    }
                }

                $savedPalletMover++;
            }

            DB::commit();

            $infoRange = $startDate ? "(Mulai H-1: {$startDate})" : "(Semua Data)";

            return [
                'status' => true,
                'message' => "Sinkronisasi P2H {$source} {$infoRange} berhasil! Diperbarui: {$savedForklift} Forklift & {$savedPalletMover} Pallet Mover.",
                'data' => [
                    'start_date' => $startDate,
                    'latest_date' => $latestDate,
                    'total_forklift' => $savedForklift,
                    'total_pallet_mover' => $savedPalletMover,
                    'total' => $savedForklift + $savedPalletMover,
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error sync P2H {$source}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status' => false,
                'message' => "Gagal sinkronisasi data P2H {$source}: " . $e->getMessage(),
            ];
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

        // Buat variasi kode untuk pencocokan
        $variants = [$nomorUnit, $cleanUnit];

        // Jika nomor unit diawali nama seperti FORKLIFT02 / PALLETMOVER03 / STACKER01
        if (preg_match('/^(FORKLIFT|FL|FORK|PALLETMOVER|PALLET|PM|STACKER|ST|ES)(\d+)$/i', $cleanUnit, $matchFull)) {
            $p = strtoupper($matchFull[1]);
            $num = (int)$matchFull[2];
            $numPad = sprintf('%02d', $num);

            if (str_starts_with($p, 'FORK') || $p === 'FL') {
                $variants[] = 'F' . $num;
                $variants[] = 'F' . $numPad;
                $variants[] = 'FORKLIFT ' . $num;
                $variants[] = 'FORKLIFT ' . $numPad;
            } elseif (str_starts_with($p, 'PALLET') || $p === 'PM') {
                $variants[] = 'PM' . $num;
                $variants[] = 'PM' . $numPad;
                $variants[] = 'PALLET MOVER ' . $num;
                $variants[] = 'PALLET MOVER ' . $numPad;
            } elseif (str_starts_with($p, 'ST') || $p === 'ES') {
                $variants[] = 'ES' . $num;
                $variants[] = 'ES' . $numPad;
            }
        } elseif (preg_match('/^([A-Z]+)(\d+)$/', $cleanUnit, $m)) {
            $prefix = $m[1];
            $num = (int)$m[2];
            $variants[] = $prefix . $num;
            $variants[] = $prefix . sprintf('%02d', $num);
        }

        $variants = array_unique($variants);

        // 1. Exact match kode_mesin / nama_mesin pada jenis_mtc Diesel P2H / Electric P2H
        $query1 = MtcMasterMesinModel::whereIn('jenis_mtc', $p2hTypes)
            ->where(function ($q) use ($variants) {
                foreach ($variants as $v) {
                    $q->orWhere('kode_mesin', $v)
                      ->orWhere('nama_mesin', $v);
                }
            });

        if (!empty($dept)) {
            $deptClean = str_contains(strtolower($dept), 'prod') ? 'Produksi' : (str_contains(strtolower($dept), 'ware') ? 'Warehouse' : $dept);
            $mesinWithDept = (clone $query1)->where('dept', 'like', '%' . $deptClean . '%')->first();
            if ($mesinWithDept) {
                return $mesinWithDept->id;
            }
        }

        $mesin = $query1->first();
        if ($mesin) {
            return $mesin->id;
        }

        // 2. Prefix match kode_mesin
        $query2 = MtcMasterMesinModel::whereIn('jenis_mtc', $p2hTypes)
            ->where(function ($q) use ($variants) {
                foreach ($variants as $v) {
                    $q->orWhere('kode_mesin', 'like', $v . '-%')
                      ->orWhere('kode_mesin', 'like', $v . ' %');
                }
            });

        if (!empty($dept)) {
            $deptClean = str_contains(strtolower($dept), 'prod') ? 'Produksi' : (str_contains(strtolower($dept), 'ware') ? 'Warehouse' : $dept);
            $mesinWithDept = (clone $query2)->where('dept', 'like', '%' . $deptClean . '%')->first();
            if ($mesinWithDept) {
                return $mesinWithDept->id;
            }
        }

        $mesin = $query2->first();
        if ($mesin) {
            return $mesin->id;
        }

        // 3. Match berdasarkan nama_mesin pada jenis_mtc P2H sesuai tipe unit & digit
        if (preg_match('/(\d+)/', $cleanUnit, $digitsMatch)) {
            $digits = $digitsMatch[1];
            $intNum = (int)$digitsMatch[1];
            $query3 = MtcMasterMesinModel::whereIn('jenis_mtc', $p2hTypes);

            $isForklift = str_contains($cleanUnit, 'FORK') || str_starts_with($cleanUnit, 'F');
            $isPM = str_contains($cleanUnit, 'PALLET') || str_starts_with($cleanUnit, 'PM');

            if ($isForklift) {
                $query3->where('nama_mesin', 'like', '%Forklift%');
            } elseif ($isPM) {
                $query3->where('nama_mesin', 'like', '%Pallet%');
            }

            $query3->where(function ($sq) use ($digits, $intNum) {
                $sq->where('nama_mesin', 'like', '% ' . $digits . '%')
                   ->orWhere('nama_mesin', 'like', '% ' . $intNum . '%')
                   ->orWhere('nama_mesin', 'like', '%.' . $digits . '%')
                   ->orWhere('nama_mesin', 'like', '%.' . $intNum . '%')
                   ->orWhere('kode_mesin', 'like', '%' . $digits)
                   ->orWhere('kode_mesin', 'like', '%' . $intNum);
            });

            if (!empty($dept)) {
                $deptClean = str_contains(strtolower($dept), 'prod') ? 'Produksi' : (str_contains(strtolower($dept), 'ware') ? 'Warehouse' : $dept);
                $mesinWithDept = (clone $query3)->where('dept', 'like', '%' . $deptClean . '%')->first();
                if ($mesinWithDept) {
                    return $mesinWithDept->id;
                }
            }

            $mesin = $query3->first();
            if ($mesin) {
                return $mesin->id;
            }
        }

        return null;
    }
}
