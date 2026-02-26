<?php

namespace App\Http\Controllers\Kalibrasi;

// use \Log;
use App\Http\Controllers\Controller;
use App\Mail\RequestApprovalMail;
use App\Models\Kalibrasi\KalibrasiApprovalModel;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\NotificationsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class KalibrasiCertificateController extends Controller
{
    public function reqApproval(Request $request, $id)
    {
        $request->validate([
            'manager_id'    => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
            'user_id'       => 'required|exists:users,id',
        ]);

        $sertifikat = KalibrasiSertifikatModel::findOrFail($id);

        DB::beginTransaction();

        try {

            $foremanId = Auth::id();

            // Urutan level approval
            $approvers = [
                1 => $foremanId,               // Foreman (auto approve)
                2 => $request->supervisor_id,  // Supervisor
                3 => $request->manager_id,     // Manager
                4 => $request->user_id,        // Operator
            ];

            $roleByLevel = [
                1 => 'Foreman',
                2 => 'Supervisor',
                3 => 'Manager',
                4 => 'User',
            ];

            foreach ($approvers as $level => $approverId) {

                if (!$approverId) continue;

                $user = User::findOrFail($approverId);

                $isForeman = $user->id === $foremanId;

                KalibrasiApprovalModel::create([
                    'sertifikat_id' => $sertifikat->id,
                    'approver_id'   => $user->id,
                    'status'        => $isForeman ? 'approved' : 'pending',
                    'level'         => $level,
                    'role'          => $roleByLevel[$level] ?? 'Unknown',
                    'action_at'     => $isForeman ? now() : null,
                    'action_by'     => $isForeman ? $foremanId : null,
                    'catatan'       => null,
                    'ttd'           => null,
                ]);

                if (!$isForeman) {

                    // Email
                    Mail::to($user->email)
                        ->queue(new RequestApprovalMail($sertifikat, $user->username));

                    // Notifikasi Database
                    NotificationsModel::create([
                        'user_id'         => $user->id,
                        'notifiable_type' => KalibrasiSertifikatModel::class,
                        'notifiable_id'   => $sertifikat->id,
                        'title'           => 'Approval Kalibrasi ' . ucfirst($sertifikat->kalibrasi->jenis_kalibrasi),
                        'message'         => 'Sertifikat kalibrasi menunggu persetujuan Anda.',
                        'url'             => route('kalibrasi.certificate.approvals'),
                        'is_read'         => false,
                    ]);
                }
            }

            // Update status sertifikat
            $sertifikat->update([
                'status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Request approval berhasil dikirim.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengirim request approval: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id, Request $request)
    {
        DB::beginTransaction();

        try {

            $approval = KalibrasiApprovalModel::findOrFail($id);

            $userId = Auth::id();

            // Pastikan yang approve adalah approver yang benar
            if ($approval->approver_id !== $userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak berhak melakukan approval ini.'
                ], 403);
            }

            // Cek apakah sudah pernah diproses
            if ($approval->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Approval sudah diproses sebelumnya.'
                ], 400);
            }

            // CEK LEVEL LOCKING
            $lowerLevelPending = KalibrasiApprovalModel::where('sertifikat_id', $approval->sertifikat_id)
                ->where('level', '<', $approval->level)
                ->where('status', '!=', 'approved')
                ->exists();

            if ($lowerLevelPending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Approval harus sesuai urutan level.'
                ], 400);
            }

            $ttdPath = null;

            if ($request->ttd_base64) {

                // Ambil bagian base64 saja
                $image = $request->ttd_base64;

                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);

                $imageName = 'ttd_' . $approval->approver->username . '_' . $approval->sertifikat_id . '.png';

                Storage::disk('public')->put(
                    'ttd/kalibrasi/' . $imageName,
                    base64_decode($image)
                );

                $ttdPath = 'ttd/kalibrasi/' . $imageName;
            }

            // Update approval
            $approval->update([
                'status'    => 'approved',
                'ttd'       => $ttdPath,
                'action_at' => now(),
                'action_by' => Auth::id(),
            ]);

            // Cek apakah semua sudah approved
            $stillPending = KalibrasiApprovalModel::where('sertifikat_id', $approval->sertifikat_id)
                ->where('status', 'pending')
                ->exists();

            if (!$stillPending) {
                $approval->sertifikat->update([
                    'status' => 'approved'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Approval berhasil.'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan.'
            ], 500);
        }
    }

    public function reject($id, Request $request)
    {
        DB::beginTransaction();

        try {

            $approval = KalibrasiApprovalModel::findOrFail($id);

            if ($approval->approver_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak berhak melakukan reject.'
                ], 403);
            }

            if ($approval->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Approval sudah diproses.'
                ], 400);
            }

            // Lock level
            $lowerLevelPending = KalibrasiApprovalModel::where('sertifikat_id', $approval->sertifikat_id)
                ->where('level', '<', $approval->level)
                ->where('status', '!=', 'approved')
                ->exists();

            if ($lowerLevelPending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reject harus sesuai urutan level.'
                ], 400);
            }

            $approval->update([
                'status'    => 'rejected',
                'catatan'   => $request->catatan ?? null,
                'action_at' => now(),
                'action_by' => Auth::id(),
            ]);

            // Kalau ada yang reject → sertifikat langsung rejected
            $approval->sertifikat->update([
                'status' => 'rejected'
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Approval berhasil ditolak.'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan.'
            ], 500);
        }
    }

    public function showApprovalPage()
    {
        $user = Auth::user();

        $approvals = KalibrasiApprovalModel::with([
            'sertifikat.kalibrasi.alat',
            'approver',
        ])
            ->where('status', 'pending')
            ->where(function ($q) use ($user) {
                $q->where('approver_id', $user->id)
                    ->orWhere('role', $user->role);
            })
            ->orderBy('level')
            ->orderBy('created_at')
            ->get();

        return view('kalibrasi.certificate.approval', compact('approvals'));
    }

    public function getSertifikatData(Request $request)
    {
        try {
            $userId = Auth::id();

            // Ambil semua approval berdasarkan user login
            $query = KalibrasiApprovalModel::with([
                'sertifikat:id,kalibrasi_id,user_id,status,notes,issued_at,created_at,updated_at',
                'sertifikat.kalibrasi:id,alat_id,user_id,lokasi_kalibrasi,tgl_kalibrasi,tgl_kalibrasi_ulang,jenis_kalibrasi,suhu_ruangan,kelembaban,created_at,updated_at',
                'sertifikat.kalibrasi.alat:id,kode_alat,nama_alat,metode_kalibrasi',
                'approver:id,username',
            ])
                ->where('approver_id', $userId);

            // 🔹 Filter opsional dari frontend
            if ($request->filled('tanggal')) {
                $query->whereHas('sertifikat.kalibrasi', function ($q) use ($request) {
                    $q->whereDate('tgl_kalibrasi', $request->tanggal);
                });
            }

            if ($request->filled('jenis')) {
                $query->whereHas('sertifikat.kalibrasi', function ($q) use ($request) {
                    $q->where('jenis_kalibrasi', $request->jenis);
                });
            }

            $data = $query->orderByDesc('id')->get();

            // 🔹 Transformasi: load relasi dinamis sesuai jenis kalibrasi
            $data->transform(function ($item) {
                $kalibrasi = $item->sertifikat->kalibrasi ?? null;
                if (!$kalibrasi) return $item;

                $relasi = $kalibrasi->getRelasiByJenis();
                if (!empty($relasi)) {
                    $kalibrasi->loadMissing($relasi);
                }

                $item->kalibrasi = $kalibrasi;
                return $item;
            });

            return response()->json([
                'status' => 'success',
                'data'   => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDataCertificate(Request $request)
    {
        try {
            $user = Auth::user();
            $tanggal = $request->input('tanggal');
            $jenis   = $request->input('jenis');

            $query = KalibrasiModel::select(
                'id',
                'alat_id',
                'lokasi_kalibrasi',
                'tgl_kalibrasi',
                'tgl_kalibrasi_ulang',
                'jenis_kalibrasi'
            )
                ->with([
                    'alat:id,kode_alat,nama_alat',
                    'certificate' => function ($q) {
                        $q->select('id', 'kalibrasi_id', 'status')
                            ->with([
                                'approvals' => function ($qa) {
                                    $qa->select(
                                        'id',
                                        'sertifikat_id',
                                        'approver_id',
                                        'status',
                                        'catatan',
                                        'action_at'
                                    )->with(['approver:id,username']);
                                }
                            ]);
                    },
                ])
                ->whereHas('certificate');

            if (!empty($tanggal)) {
                $query->whereDate('tgl_kalibrasi', $tanggal);
            }

            if (!empty($jenis)) {
                $query->where('jenis_kalibrasi', $jenis);
            }

            $jenisKalibrasi = KalibrasiModel::select('jenis_kalibrasi')
                ->distinct()
                ->pluck('jenis_kalibrasi');

            // paginate, misal 10 per halaman
            $data = $query->orderByDesc('id')->paginate(15);

            return response()->json([
                'status' => 'success',
                'role'   => $user->jabatan ?? null,
                'data'   => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                ],
                'filterOptions' => [
                    'jenis_kalibrasi' => $jenisKalibrasi
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserApprovals(Request $request)
    {
        try {
            $sertifikatId = $request->query('id');

            if (!$sertifikatId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sertifikat ID tidak ditemukan'
                ], 400);
            }

            // Ambil sertifikat
            $sertifikat = KalibrasiSertifikatModel::find($sertifikatId);
            if (!$sertifikat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sertifikat tidak ditemukan'
                ], 404);
            }

            // Ambil data kalibrasi untuk tahu departemen pemilik
            $kalibrasi = KalibrasiModel::find($sertifikat->kalibrasi_id);
            if (!$kalibrasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data kalibrasi tidak ditemukan'
                ], 404);
            }

            $departemenPemilik = $kalibrasi->alat->departemen_pemilik;

            // Ambil semua user
            $manager = User::where('jabatan', 'dept_head')
                ->get(['id', 'username', 'email', 'jabatan', 'nik', 'bagian']);
            $supervisor = User::where('jabatan', 'supervisor')
                ->get(['id', 'username', 'email', 'jabatan', 'nik', 'bagian']);
            $foreman = User::where('jabatan', 'foreman')
                ->where('departemen', 'engineering')
                ->get(['id', 'username', 'email', 'jabatan', 'nik', 'bagian']);
            $user = User::where('departemen', $departemenPemilik)
                ->get(['id', 'username', 'email', 'jabatan', 'nik', 'bagian']);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'manager'  => $manager,
                    'supervisor' => $supervisor,
                    'foreman' => $foreman,
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function processApproval(Request $request)
    {
        $request->validate([
            'sertifikat_id' => 'required|exists:kalibrasi_sertifikat,id',
            'status' => 'required|in:approved,rejected',
            'komentar' => 'nullable|string|max:500',
        ]);

        $sertifikatId = $request->sertifikat_id;
        $statusAksi = $request->status;
        $komentar = $request->komentar;

        // 1. Ambil record approval yang harus diproses oleh user saat ini
        $approval = KalibrasiApprovalModel::where('sertifikat_id', $sertifikatId)
            ->where('approver_id', Auth::id())
            ->whereNull('approved_at') // Pastikan hanya memproses yang belum direspons
            ->first();

        if (!$approval) {
            return response()->json(['message' => 'Anda tidak memiliki hak atau sudah memproses sertifikat ini.'], 403);
        }

        // 2. Update status approval user saat ini
        $approval->update([
            'status' => $statusAksi,
            'approved_at' => now(),
            'komentar' => $komentar, // Tambahkan kolom komentar
        ]);

        $sertifikat = $approval->sertifikat; // Ambil relasi sertifikat

        $message = "Sertifikat berhasil di-{$statusAksi}";

        if ($statusAksi === 'rejected') {
            // 3. LOGIKA REJECT: Jika ada 1 reject, ubah status sertifikat utama menjadi rejected
            $sertifikat->status = 'Rejected';
            $sertifikat->save();
            $message = "Sertifikat berhasil ditolak. Status sertifikat diubah menjadi Rejected.";
        } elseif ($statusAksi === 'approved') {
            // 4. LOGIKA APPROVE: Cek apakah semua sudah approve
            $totalApprovers = KalibrasiApprovalModel::where('sertifikat_id', $sertifikatId)->count();
            $approvedCount = KalibrasiApprovalModel::where('sertifikat_id', $sertifikatId)
                ->where('status', 'approved')
                ->count();

            // Cek apakah semua approver sudah setuju
            if ($approvedCount >= $totalApprovers) {
                $sertifikat->status = 'Approved';
                $sertifikat->save();
                $message = "Sertifikat telah disetujui oleh semua pihak. Status sertifikat diubah menjadi Approved.";
            } else {
                $message = "Sertifikat berhasil di-approve. Menunggu persetujuan dari approver lain.";
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

    public function downloadSertifikat($id)
    {
        try {
            // Ambil data sertifikat dan relasinya
            $sertifikat = KalibrasiSertifikatModel::with('kalibrasi.alat')->findOrFail($id);
            $kalibrasi = $sertifikat->kalibrasi;
            $alat = $kalibrasi->alat;
            $jenis = strtolower($kalibrasi->jenis_kalibrasi);

            $approvals = KalibrasiApprovalModel::where('sertifikat_id', $sertifikat->id)
                ->with('approver') // kalau kamu punya relasi approver()
                ->get()
                ->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'approver_id' => $a->approver_id,
                        'status' => $a->status,
                        'approver_name' => optional($a->approver)->username ?? '-',
                        'jabatan' => $a->approver->jabatan ?? '-',
                        'departemen' => $a->approver->departemen ?? '-',
                        'comment' => $a->comment,
                        'ttd' => $a->ttd
                    ];
                });

            // Tentukan path template Excel
            $templatePath = public_path("assets/templates/template_kalibrasi_{$jenis}_sertifikat.xlsx");
            if (!file_exists($templatePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Template sertifikat untuk {$jenis} tidak ditemukan."
                ], 404);
            }

            // Load template Excel dengan chart aktif
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setIncludeCharts(true);
            $spreadsheet = $reader->load($templatePath);

            // Load relasi sesuai jenis kalibrasi dan isi data ke template
            switch ($jenis) {
                case 'pressure':
                    $kalibrasi->load(['pressure.details']);
                    $this->_fillPressure($spreadsheet, $kalibrasi, $alat, $approvals, $sertifikat);
                    break;

                case 'volumetrik':
                    $kalibrasi->load(['volumetrik']);
                    $this->_fillVolumetrik($spreadsheet, $kalibrasi, $alat, $approvals, $sertifikat);
                    break;

                case 'temperature':
                    $kalibrasi->load(['temperature']);
                    $this->_fillTemperature($spreadsheet, $approvals, $kalibrasi, $alat, $sertifikat);
                    break;

                case 'thermohygrometer':
                    $kalibrasi->load(['thermohygrometer']);
                    $this->_fillThermo($spreadsheet, $approvals, $kalibrasi, $alat, $sertifikat);
                    break;

                case 'jangka_sorong':
                    $kalibrasi->load(['jangkaSorong', 'jangkaSorongSummary']);
                    $this->_fillJangkaSorong($spreadsheet, $approvals, $kalibrasi, $alat, $sertifikat);
                    break;

                case 'timbangan':
                    $this->_fillTimbangan($spreadsheet, $approvals, $kalibrasi, $alat, $sertifikat);
                    break;

                case 'instrumen':
                    $kalibrasi->load(['instrumen', 'keypad']);

                    $this->_fillInstrumen(
                        $spreadsheet,
                        $kalibrasi,
                        $alat,
                        $approvals,
                        $sertifikat
                    );
                    break;

                case 'dimensi':
                    $kalibrasi->load(['dimensi']);
                    $this->_fillDimensi($spreadsheet, $kalibrasi, $alat, $approvals, $sertifikat);
                    break;

                case 'flowmeter':
                    $kalibrasi->load(['flowmeter']);
                    $this->_fillFlowmeter($spreadsheet, $kalibrasi, $alat, $approvals, $sertifikat);
                    break;

                default:
                    return response()->json(['message' => 'Jenis kalibrasi tidak dikenali.'], 400);
            }

            // Siapkan lokasi penyimpanan
            $savePath = storage_path('sertifikat/sertifikat_kalibrasi');
            if (!file_exists($savePath)) {
                mkdir($savePath, 0755, true);
            }

            $filename = "sertifikat_kalibrasi_{$jenis}_{$id}.xlsx";
            $outputPath = "{$savePath}/{$filename}";

            // Simpan file hasil dengan chart tetap ada
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);
            $writer->save($outputPath);

            // Unduh file ke user
            return response()->download($outputPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat file sertifikat: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function _fillPressure(Spreadsheet $spreadsheet, $kalibrasi, $alat, $approvals, $sertifikat)
    {

        $sheet = $spreadsheet->getActiveSheet();

        // Header Information Alat & kalibrasi
        $sheet->setCellValue('I7', $alat->departemen_pemilik ?? '-');
        $sheet->setCellValue('I8', $alat->lokasi_alat ?? '-');
        $sheet->setCellValue('I9', $alat->no_kalibrasi ?? '-');
        $sheet->setCellValue('I10', $alat->nama_alat ?? '-');
        $sheet->setCellValue('I11', $alat->merk ?? '-');
        $sheet->setCellValue('I12', $alat->tipe ?? '-');
        $sheet->setCellValue('I13', $alat->kapasitas ?? '-');
        $sheet->setCellValue('I14', $alat->resolusi ?? '-');
        $sheet->setCellValue('AA7', $alat->range_penggunaan_alat ?? '-');
        $sheet->setCellValue('AA8', $alat->limits_of_permissible_error ?? '-');
        $sheet->setCellValue('AA9', $alat->kode_alat ?? '-');
        $sheet->setCellValue('I15', $kalibrasi->lokasi_kalibrasi ?? '-');
        $sheet->setCellValue('I16', $kalibrasi->suhu_ruangan ?? '-');
        $sheet->setCellValue('I17', $kalibrasi->kelembaban ?? '-');
        $sheet->setCellValue('AA10', $kalibrasi->tgl_kalibrasi ?? '-');
        $sheet->setCellValue('AA11', $kalibrasi->tgl_kalibrasi_ulang ?? '-');

        $rowStart = 26;
        $row = $rowStart;
        $index = 0;

        $pressures = $kalibrasi->pressure
            ->sortBy('titik_kalibrasi')
            ->values();

        $totalTitik = $pressures->count();

        $step = $totalTitik > 1 ? 100 / ($totalTitik - 1) : 100;

        // reverse khusus untuk TURUN
        $pressuresDesc = $pressures->reverse()->values();

        foreach ($pressures as $i => $pg) {

            $persentase = $totalTitik > 1 ? $index * $step : 100;
            $sheet->setCellValue("D{$row}", round($persentase, 2));

            // Ambil pasangan turun dari urutan terbalik
            $pgTurun = $pressuresDesc[$i];

            $sheet->setCellValue("H{$row}", $pg->titik_kalibrasi ?? '');
            $sheet->setCellValue("L{$row}", $pg->avg_penunjuk_alat_naik ?? '');
            $sheet->setCellValue("O{$row}", $pgTurun->avg_penunjuk_alat_turun ?? '');
            $sheet->setCellValue("R{$row}", $pg->avg_tekanan_standar_naik ?? '');
            $sheet->setCellValue("U{$row}", $pgTurun->avg_tekanan_standar_turun ?? '');
            $sheet->setCellValue("X{$row}", $pg->avg_koreksi_alat_naik ?? '');
            $sheet->setCellValue("AA{$row}", $pgTurun->avg_koreksi_alat_turun ?? '');
            $sheet->setCellValue("AD{$row}", $pg->u_gabungan ?? '');

            $row++;
            $index++;
        }


        $baseRow = 63;
        $nameRow = 67;
        // Mapping tetap untuk posisi tanda tangan berdasarkan jabatan
        $roleColumnMap = [
            'foreman'    => 'C',
            'supervisor' => 'H',
            'dept_head'  => 'M',
        ];

        // Loop semua approver
        foreach ($approvals as $approval) {
            $jabatan = strtolower($approval['jabatan'] ?? '');
            $departemen = strtolower($approval['departemen'] ?? '');
            $status = strtolower($approval['status'] ?? ''); // cek status approval

            // Default kolom berdasarkan jabatan
            $col = $roleColumnMap[$jabatan] ?? null;

            // Jika foreman dari departemen non-engineering → pindah ke kolom S
            if ($jabatan === 'foreman' && $departemen !== 'engineering') {
                $col = 'S';
            }

            if (!$col) {
                Log::warning("Kolom tidak ditemukan untuk jabatan={$jabatan}, departemen={$departemen}");
                continue;
            }

            // Tentukan range merge
            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // === Jika belum approve → kosongkan area ===
            if (!in_array($status, ['approved', 'rejected'])) {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, '');
                continue;
            }

            // === Path tanda tangan ===
            $approvedPath = public_path('assets/images/ttd/approved_sticker.png');
            $rejectedPath = public_path('assets/images/ttd/rejected_sticker.png');

            $status = strtolower($approval['status'] ?? '');
            $relativePath = $approval['ttd'] ?? null;

            $isDummy = true;

            // === Tentukan dummy berdasarkan status ===
            if ($status === 'approved') {
                $dummyPath = $approvedPath;
            } elseif ($status === 'rejected') {
                $dummyPath = $rejectedPath;
            } else {
                $dummyPath = $approvedPath;
            }

            // === Cek apakah ada TTD asli ===
            if ($relativePath) {
                $signaturePath = public_path('storage/' . $relativePath);

                if (file_exists($signaturePath) && $status === 'approved') {
                    $finalPath = $signaturePath;
                    $isDummy = false;
                } else {
                    $finalPath = $dummyPath;
                }
            } else {
                $finalPath = $dummyPath;
            }

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // === Gambar tanda tangan ===
            try {
                $rangeBounds = Coordinate::rangeBoundaries($mergeRange);
                $startCol = $rangeBounds[0][0];
                $endCol   = $rangeBounds[1][0];

                $startColLetter = Coordinate::stringFromColumnIndex($startCol);
                $endColLetter   = Coordinate::stringFromColumnIndex($endCol);

                // Hitung total lebar kolom
                $totalWidth = 0;
                for ($j = $startCol; $j <= $endCol; $j++) {
                    $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                }

                // Posisi gambar agak tengah
                $offsetX = ($totalWidth * 6.2 / 2) - 25;
                if ($offsetX < 0) $offsetX = 0;

                $drawing = new Drawing();
                $drawing->setPath($finalPath);
                $drawing->setHeight($isDummy ? 70 : 100);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX($isDummy ? 30 : 15);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $th) {
                Log::warning('Gagal memuat TTD: ' . $th->getMessage());
            }

            // === Nama approver di bawah tanda tangan ===
            $approverName = strtoupper($approval['approver_name'] ?? '-'); // kapital semua
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . $approverName . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        $rowEnd = $row - 1;

        if ($kalibrasi->pressure->count() > 0) {
            $chartCollection = $sheet->getChartCollection();

            foreach ($chartCollection as $chart) {
                if (!$chart || !$chart->getPlotArea()) continue;

                $plotArea = $chart->getPlotArea();
                $oldSeries = $plotArea->getPlotGroup();

                if (empty($oldSeries)) continue;

                $categoryRange = "Sertifikat!\$H\${$rowStart}:\$H\${$rowEnd}"; // Titik kalibrasi (X)
                $titikKalibrasiRange = "Sertifikat!\$H\${$rowStart}:\$H\${$rowEnd}"; // Titik kalibrasi (X)
                $alatNaikRange = "Sertifikat!\$L\${$rowStart}:\$L\${$rowEnd}";
                $alatTurunRange = "Sertifikat!\$O\${$rowStart}:\$O\${$rowEnd}";
                $standarNaikRange = "Sertifikat!\$R\${$rowStart}:\$R\${$rowEnd}";
                $standarTurunRange = "Sertifikat!\$U\${$rowStart}:\$U\${$rowEnd}";

                $categoryAxis = [
                    new DataSeriesValues(
                        'String',
                        null,
                        null,
                        $totalTitik,
                        range(1, $totalTitik) // isi kategori dengan 1,2,3,...
                    )
                ];

                $seriesList = [
                    [
                        'label' => '"Titik Kalibrasi"',
                        'range' => $titikKalibrasiRange,
                        'order' => [0],
                    ],
                    [
                        'label' => '"Alat Naik"',
                        'range' => $alatNaikRange,
                        'order' => [1],
                    ],
                    [
                        'label' => '"Alat Turun"',
                        'range' => $alatTurunRange,
                        'order' => [2],
                    ],
                    [
                        'label' => '"Standar Naik"',
                        'range' => $standarNaikRange,
                        'order' => [3],
                    ],
                    [
                        'label' => '"Standar Turun"',
                        'range' => $standarTurunRange,
                        'order' => [4],
                    ],
                ];

                $newSeries = [];
                foreach ($seriesList as $s) {
                    $newSeries[] = new DataSeries(
                        DataSeries::TYPE_LINECHART,           // tipe chart
                        null,                                 // grouping
                        $s['order'],                          // urutan
                        [new DataSeriesValues('String', $s['label'], null, 1)], // legend
                        $categoryAxis,                        // kategori
                        [new DataSeriesValues('Number', $s['range'], null, $totalTitik)] // nilai
                    );
                }

                $newPlotArea = new PlotArea(null, $newSeries);
                $chart->setPlotArea($newPlotArea);
            }
        }

        // Tanggal diterbitkan
        $issuedDate = $sertifikat->issued_at
            ? \Carbon\Carbon::parse($sertifikat->issued_at)->format('d/m/Y')
            : '-';

        $sheet->setCellValue('X62', "Diterbitkan tanggal : $issuedDate");

        return $spreadsheet;
    }

    private function _fillVolumetrik(Spreadsheet $spreadsheet, $kalibrasi, $alat, $approvals, $sertifikat)
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('I7', $alat->departemen_pemilik ?? '-');
        $sheet->setCellValue('I8', $alat->lokasi_alat ?? '-');
        $sheet->setCellValue('I9', $alat->no_kalibrasi ?? '-');
        $sheet->setCellValue('I10', $alat->nama_alat ?? '-');
        $sheet->setCellValue('I11', $alat->merk ?? '-');
        $sheet->setCellValue('I12', $alat->tipe ?? '-');
        $sheet->setCellValue('I13', $alat->kapasitas ?? '-');
        $sheet->setCellValue('I14', $alat->resolusi ?? '-');
        $sheet->setCellValue('AA7', $alat->range_penggunaan_alat ?? '-');
        $sheet->setCellValue('AA8', $alat->limits_of_permissible_error ?? '-');
        $sheet->setCellValue('AA9', $alat->kode_alat ?? '-');
        $sheet->setCellValue('I15', $kalibrasi->lokasi_kalibrasi ?? '-');
        $sheet->setCellValue('I16', $kalibrasi->suhu_ruangan ?? '-');
        $sheet->setCellValue('I17', $kalibrasi->kelembaban ?? '-');
        $sheet->setCellValue('AA10', $kalibrasi->tgl_kalibrasi ?? '-');
        $sheet->setCellValue('AA11', $kalibrasi->tgl_kalibrasi_ulang ?? '-');

        $row = 26;

        $volumetriks = $kalibrasi->volumetrik
            ->sortBy('titik_kalibrasi')
            ->values();

        foreach ($volumetriks as $v) {
            $firstDetail = $v->details->first();
            $penunjukAlatPertama = $firstDetail->penunjuk_alat ?? '-';

            $sheet->setCellValue("D{$row}", $v->titik_kalibrasi ?? '-');
            $sheet->setCellValue("L{$row}", $penunjukAlatPertama);
            $sheet->setCellValue("R{$row}", $v->avg_penunjuk_standar ?? '-');
            $sheet->setCellValue("X{$row}", $v->avg_koreksi ?? '-');
            $sheet->setCellValue("AD{$row}", $v->u_total ?? '-');

            $row++;
        }

        $baseRow = 62;
        $nameRow = 66;

        // Mapping tetap untuk posisi tanda tangan
        $roleColumnMap = [
            'foreman'    => 'C',
            'supervisor' => 'H',
            'dept_head'   => 'M',
        ];

        foreach ($approvals as $approval) {
            $jabatan = strtolower($approval['jabatan'] ?? '');
            $departemen = strtolower($approval['departemen'] ?? '');
            $status = strtolower($approval['status'] ?? ''); // tambahkan ini

            // Default kolom berdasarkan jabatan
            $col = $roleColumnMap[$jabatan] ?? null;

            // Jika foreman dari departemen non-engineering → pindah ke kolom S
            if ($jabatan === 'foreman' && $departemen !== 'engineering') {
                $col = 'S';
            }

            if (!$col) continue; // skip jika tidak ada mapping kolom

            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // === Jika belum approve → kosongkan area ===
            if (!in_array($status, ['approved', 'rejected'])) {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, '');
                continue;
            }

            // === Path tanda tangan ===
            $approvedPath = public_path('assets/images/ttd/approved_sticker.png');
            $rejectedPath = public_path('assets/images/ttd/rejected_sticker.png');

            $status = strtolower($approval['status'] ?? '');
            $relativePath = $approval['ttd'] ?? null;

            $isDummy = true;

            // === Tentukan dummy berdasarkan status ===
            if ($status === 'approved') {
                $dummyPath = $approvedPath;
            } elseif ($status === 'rejected') {
                $dummyPath = $rejectedPath;
            } else {
                $dummyPath = $approvedPath;
            }

            // === Cek apakah ada TTD asli ===
            if ($relativePath) {
                $signaturePath = public_path('storage/' . $relativePath);

                if (file_exists($signaturePath) && $status === 'approved') {
                    $finalPath = $signaturePath;
                    $isDummy = false;
                } else {
                    $finalPath = $dummyPath;
                }
            } else {
                $finalPath = $dummyPath;
            }

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            try {
                $rangeBounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($mergeRange);
                $startCol = $rangeBounds[0][0];
                $endCol   = $rangeBounds[1][0];

                $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                $endColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

                $totalWidth = 0;
                for ($j = $startCol; $j <= $endCol; $j++) {
                    $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                }

                $offsetX = ($totalWidth * 6.2 / 2) - 25;
                if ($offsetX < 0) $offsetX = 0;

                $drawing = new Drawing();
                $drawing->setPath($finalPath);
                $drawing->setHeight($isDummy ? 70 : 100);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX($isDummy ? 25 : 15);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $th) {
                Log::warning('Gagal memuat TTD: ' . $th->getMessage());
            }

            // Nama approver di bawah TTD (huruf kapital)
            $approverName = ucfirst($approval['approver_name'] ?? '-');
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . $approverName . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Tanggal diterbitkan
        $issuedDate = $sertifikat->issued_at
            ? \Carbon\Carbon::parse($sertifikat->issued_at)->format('d/m/Y')
            : '-';

        $sheet->setCellValue('X61', "Diterbitkan tanggal : $issuedDate");

        return $spreadsheet;
    }

    private function _fillTemperature(Spreadsheet $spreadsheet, $approvals, $kalibrasi, $alat, $sertifikat)
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('I7', $alat->departemen_pemilik ?? '-');
        $sheet->setCellValue('I8', $alat->lokasi_alat ?? '-');
        $sheet->setCellValue('I9', $alat->no_kalibrasi ?? '-');
        $sheet->setCellValue('I10', $alat->nama_alat ?? '-');
        $sheet->setCellValue('I11', $alat->merk ?? '-');
        $sheet->setCellValue('I12', $alat->tipe ?? '-');
        $sheet->setCellValue('I13', $alat->kapasitas ?? '-');
        $sheet->setCellValue('I14', $alat->resolusi ?? '-');
        $sheet->setCellValue('AA7', $alat->range_penggunaan_alat ?? '-');
        $sheet->setCellValue('AA8', $alat->limits_of_permissible_error ?? '-');
        $sheet->setCellValue('AA9', $alat->kode_alat ?? '-');
        $sheet->setCellValue('I15', $kalibrasi->lokasi_kalibrasi ?? '-');
        $sheet->setCellValue('I16', $kalibrasi->suhu_ruangan ?? '-');
        $sheet->setCellValue('I17', $kalibrasi->kelembaban ?? '-');
        $sheet->setCellValue('AA10', $kalibrasi->tgl_kalibrasi ?? '-');
        $sheet->setCellValue('AA11', $kalibrasi->tgl_kalibrasi_ulang ?? '-');

        // ===== Data Tekanan Naik & Turun =====
        $rowStart = 25;
        $row = $rowStart;

        $temperatures = $kalibrasi->temperature
            ->sortBy('titik_kalibrasi')
            ->values();

        $totalTitik = $temperatures->count();

        foreach ($temperatures as $tg) {

            $sheet->setCellValue("D{$row}", $tg->titik_kalibrasi ?? '');
            $sheet->setCellValue("L{$row}", $tg->avg_penunjuk_alat ?? '');
            $sheet->setCellValue("R{$row}", $tg->avg_suhu_standar ?? '');
            $sheet->setCellValue("X{$row}", $tg->avg_kor_alat ?? '');
            $sheet->setCellValue("AD{$row}", $tg->ketidakpastian ?? '');

            $row++;
        }

        $baseRow = 61;
        $nameRow = 65;

        // Mapping posisi tanda tangan berdasarkan jabatan
        $roleColumnMap = [
            'foreman'    => 'C',
            'supervisor' => 'H',
            'dept_head'  => 'M',
        ];

        // Loop semua approver
        foreach ($approvals as $approval) {
            $jabatan = strtolower($approval['jabatan'] ?? '');
            $departemen = strtolower($approval['departemen'] ?? '');
            $status = strtolower($approval['status'] ?? ''); // status approval

            // Default kolom berdasarkan jabatan
            $col = $roleColumnMap[$jabatan] ?? null;

            // Jika foreman dari departemen non-engineering → pindah ke kolom S
            if ($jabatan === 'foreman' && $departemen !== 'engineering') {
                $col = 'S';
            }

            if (!$col) {
                Log::warning("Kolom tidak ditemukan untuk jabatan={$jabatan}, departemen={$departemen}");
                continue;
            }

            // Tentukan range merge
            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // === Jika belum approve → kosongkan area ===
            if (!in_array($status, ['approved', 'rejected'])) {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, '');
                continue;
            }

            // === Path tanda tangan ===
            $approvedPath = public_path('assets/images/ttd/approved_sticker.png');
            $rejectedPath = public_path('assets/images/ttd/rejected_sticker.png');

            $status = strtolower($approval['status'] ?? '');
            $relativePath = $approval['ttd'] ?? null;

            $isDummy = true;

            // === Tentukan dummy berdasarkan status ===
            if ($status === 'approved') {
                $dummyPath = $approvedPath;
            } elseif ($status === 'rejected') {
                $dummyPath = $rejectedPath;
            } else {
                $dummyPath = $approvedPath;
            }

            // === Cek apakah ada TTD asli ===
            if ($relativePath) {
                $signaturePath = public_path('storage/' . $relativePath);

                if (file_exists($signaturePath) && $status === 'approved') {
                    $finalPath = $signaturePath;
                    $isDummy = false;
                } else {
                    $finalPath = $dummyPath;
                }
            } else {
                $finalPath = $dummyPath;
            }

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // === Gambar tanda tangan ===
            try {
                $rangeBounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($mergeRange);
                $startCol = $rangeBounds[0][0];
                $endCol   = $rangeBounds[1][0];

                $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                $endColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

                // Hitung total lebar kolom
                $totalWidth = 0;
                for ($j = $startCol; $j <= $endCol; $j++) {
                    $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                }

                // Posisi gambar agak tengah
                $offsetX = ($totalWidth * 6.2 / 2) - 25;
                if ($offsetX < 0) $offsetX = 0;

                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath($finalPath);
                $drawing->setHeight($isDummy ? 70 : 100);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX($isDummy ? 30 : 15);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $th) {
                Log::warning('Gagal memuat TTD: ' . $th->getMessage());
            }

            // === Nama approver di bawah tanda tangan ===
            $approverName = strtoupper($approval['approver_name'] ?? '-');
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . $approverName . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        $rowEnd = $row - 1;

        if ($kalibrasi->temperature->count() > 0) {
            $chartCollection = $sheet->getChartCollection();

            foreach ($chartCollection as $chart) {
                if (!$chart || !$chart->getPlotArea()) continue;

                $plotArea = $chart->getPlotArea();
                $oldSeries = $plotArea->getPlotGroup();

                if (empty($oldSeries)) continue;

                $categoryRange = 'Sertifikat!$D$' . $rowStart . ':$D$' . $rowEnd; // Titik kalibrasi (kategori)
                $titikKalibrasiRange = 'Sertifikat!$D$' . $rowStart . ':$D$' . $rowEnd; // nilai titik kalibrasi juga bisa numerik
                $alatUkurRange = 'Sertifikat!$L$' . $rowStart . ':$L$' . $rowEnd;
                $alatStandarRange = 'Sertifikat!$R$' . $rowStart . ':$R$' . $rowEnd;

                $categoryAxis = [
                    new DataSeriesValues(
                        'String',
                        null,
                        null,
                        $totalTitik,
                        range(1, $totalTitik) // isi kategori dengan 1,2,3,...
                    )
                ];

                $seriesList = [
                    [
                        'label' => '"Titik Kalibrasi"',
                        'range' => $titikKalibrasiRange,
                        'order' => [0],
                    ],
                    [
                        'label' => '"Alat Ukur"',
                        'range' => $alatUkurRange,
                        'order' => [1],
                    ],
                    [
                        'label' => '"Alat Standar"',
                        'range' => $alatStandarRange,
                        'order' => [2],
                    ]
                ];

                $newSeries = [];
                foreach ($seriesList as $s) {
                    $newSeries[] = new DataSeries(
                        DataSeries::TYPE_LINECHART,           // tipe chart
                        null,                                 // grouping
                        $s['order'],                          // urutan
                        [new DataSeriesValues('String', $s['label'], null, 1)], // legend
                        $categoryAxis,                        // kategori
                        [new DataSeriesValues('Number', $s['range'], null, $totalTitik)] // nilai
                    );
                }

                $newPlotArea = new PlotArea(null, $newSeries);
                $chart->setPlotArea($newPlotArea);
            }
        }

        // Tanggal diterbitkan
        $issuedDate = $sertifikat->issued_at
            ? \Carbon\Carbon::parse($sertifikat->issued_at)->format('d/m/Y')
            : '-';

        $sheet->setCellValue('X60', "Diterbitkan tanggal : $issuedDate");

        return $spreadsheet;
    }

    private function _fillThermo(Spreadsheet $spreadsheet, $approvals, $kalibrasi, $alat, $sertifikat)
    {
        $sheet = $spreadsheet->getActiveSheet();

        // Header Information Alat & kalibrasi
        $sheet->setCellValue('I7', $alat->departemen_pemilik ?? '-');
        $sheet->setCellValue('I8', $alat->lokasi_alat ?? '-');
        $sheet->setCellValue('I9', $alat->no_kalibrasi ?? '-');
        $sheet->setCellValue('I10', $alat->nama_alat ?? '-');
        $sheet->setCellValue('I11', $alat->merk ?? '-');
        $sheet->setCellValue('I12', $alat->tipe ?? '-');
        $sheet->setCellValue('I13', $alat->kapasitas ?? '-');
        $sheet->setCellValue('I14', $alat->resolusi ?? '-');
        $sheet->setCellValue('AA7', $alat->range_penggunaan_alat ?? '-');
        $sheet->setCellValue('AA8', $alat->limits_of_permissible_error ?? '-');
        $sheet->setCellValue('AA9', $alat->kode_alat ?? '-');
        $sheet->setCellValue('I15', $kalibrasi->lokasi_kalibrasi ?? '-');
        $sheet->setCellValue('I16', $kalibrasi->suhu_ruangan ?? '-');
        $sheet->setCellValue('I17', $kalibrasi->kelembaban ?? '-');
        $sheet->setCellValue('AA10', $kalibrasi->tgl_kalibrasi ?? '-');
        $sheet->setCellValue('AA11', $kalibrasi->tgl_kalibrasi_ulang ?? '-');

        // ===== Data Thermohygrometer =====
        $rowStart = 25;
        $row = $rowStart;
        $startRow = $row;

        $thermo = $kalibrasi->thermohygrometer;

        $totalTitik = $thermo->count();

        $first = $thermo->first();

        $ketidakpastian_suhu = $first->ketidak_pastian_suhu ?? '';
        $ketidakpastian_rh   = $first->ketidak_pastian_rh ?? '';

        foreach ($thermo as $tg) {

            $avg_tekanan_standar_suhu = (float) ($tg->avg_tekanan_standar_suhu ?? 0);
            $avg_penunjuk_alat_suhu   = (float) ($tg->avg_penunjuk_alat_suhu ?? 0);

            // Hitung selisih
            $selisih = $avg_tekanan_standar_suhu - $avg_penunjuk_alat_suhu;

            $posisi = $tg->posisi ?? '';
            $posisi = str_ireplace(['kanan', 'kiri'], ['Ka.', 'Ki.'], $posisi);
            $posisi = ucwords(strtolower($posisi));

            // Isi data ke Excel
            $sheet->setCellValue("D{$row}", $tg->titik_kalibrasi);
            $sheet->setCellValue("H{$row}", $posisi);
            $sheet->setCellValue("L{$row}", $avg_penunjuk_alat_suhu);
            $sheet->setCellValue("O{$row}", $tg->avg_penunjuk_alat_rh ?? '');
            $sheet->setCellValue("R{$row}", $avg_tekanan_standar_suhu);
            $sheet->setCellValue("U{$row}", $tg->avg_tekanan_standar_rh ?? '');
            $sheet->setCellValue("X{$row}", round($selisih, 2));
            $sheet->setCellValue("AA{$row}", $tg->avg_kor_alat_rh ?? '');

            $row++;
        }

        $endRow = $row - 1;

        if ($totalTitik > 0) {

            $rangeSuhu = "AD{$startRow}:AF{$endRow}";
            $rangeRh   = "AG{$startRow}:AI{$endRow}";

            // Unmerge kalau sudah pernah di-merge
            $mergedCells = $sheet->getMergeCells();

            if (isset($mergedCells[$rangeSuhu])) {
                $sheet->unmergeCells($rangeSuhu);
            }

            if (isset($mergedCells[$rangeRh])) {
                $sheet->unmergeCells($rangeRh);
            }

            // Merge sesuai panjang titik
            $sheet->mergeCells($rangeSuhu);
            $sheet->mergeCells($rangeRh);

            // Ambil dari first (lebih aman)
            $sheet->setCellValue("AD{$startRow}", $ketidakpastian_suhu);
            $sheet->setCellValue("AG{$startRow}", $ketidakpastian_rh);

            // Center alignment
            $sheet->getStyle($rangeSuhu)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->getStyle($rangeRh)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }


        $baseRow = 62;
        $nameRow = 66;

        // Mapping posisi tanda tangan berdasarkan jabatan
        $roleColumnMap = [
            'foreman'    => 'C',
            'supervisor' => 'H',
            'dept_head'  => 'M',
        ];

        // Loop semua approver
        foreach ($approvals as $approval) {
            $jabatan = strtolower($approval['jabatan'] ?? '');
            $departemen = strtolower($approval['departemen'] ?? '');
            $status = strtolower($approval['status'] ?? ''); // status approval

            // Default kolom berdasarkan jabatan
            $col = $roleColumnMap[$jabatan] ?? null;

            // Jika foreman dari departemen non-engineering → pindah ke kolom S
            if ($jabatan === 'foreman' && $departemen !== 'engineering') {
                $col = 'S';
            }

            if (!$col) {
                Log::warning("Kolom tidak ditemukan untuk jabatan={$jabatan}, departemen={$departemen}");
                continue;
            }

            // Tentukan range merge
            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // === Jika belum approve → kosongkan area ===
            if (!in_array($status, ['approved', 'rejected'])) {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, '');
                continue;
            }

            // === Path tanda tangan ===
            $approvedPath = public_path('assets/images/ttd/approved_sticker.png');
            $rejectedPath = public_path('assets/images/ttd/rejected_sticker.png');

            $status = strtolower($approval['status'] ?? '');
            $relativePath = $approval['ttd'] ?? null;

            $isDummy = true;

            // === Tentukan dummy berdasarkan status ===
            if ($status === 'approved') {
                $dummyPath = $approvedPath;
            } elseif ($status === 'rejected') {
                $dummyPath = $rejectedPath;
            } else {
                $dummyPath = $approvedPath;
            }

            // === Cek apakah ada TTD asli ===
            if ($relativePath) {
                $signaturePath = public_path('storage/' . $relativePath);

                if (file_exists($signaturePath) && $status === 'approved') {
                    $finalPath = $signaturePath;
                    $isDummy = false;
                } else {
                    $finalPath = $dummyPath;
                }
            } else {
                $finalPath = $dummyPath;
            }

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // === Gambar tanda tangan ===
            try {
                $rangeBounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($mergeRange);
                $startCol = $rangeBounds[0][0];
                $endCol   = $rangeBounds[1][0];

                $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                $endColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

                // Hitung total lebar kolom
                $totalWidth = 0;
                for ($j = $startCol; $j <= $endCol; $j++) {
                    $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                }

                // Posisi gambar agak tengah
                $offsetX = ($totalWidth * 6.2 / 2) - 25;
                if ($offsetX < 0) $offsetX = 0;

                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath($finalPath);
                $drawing->setHeight($isDummy ? 70 : 100);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX($isDummy ? 30 : 15);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $th) {
                Log::warning('Gagal memuat TTD: ' . $th->getMessage());
            }

            // === Nama approver di bawah tanda tangan ===
            $approverName = strtoupper($approval['approver_name'] ?? '-');
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . $approverName . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        $rowEnd = $row - 1;

        if ($totalTitik > 0) {
            $chartCollection = $sheet->getChartCollection();

            foreach ($chartCollection as $chart) {
                if (!$chart || !$chart->getPlotArea()) continue;

                $plotArea = $chart->getPlotArea();
                $oldSeries = $plotArea->getPlotGroup();

                if (empty($oldSeries)) continue;

                $categoryRange = "Sertifikat!\$H\${$rowStart}:\$H\${$rowEnd}"; // Titik kalibrasi (X)
                $titikKalibrasiRange = "Sertifikat!\$H\${$rowStart}:\$H\${$rowEnd}"; // Titik kalibrasi (X)
                $alatNaikRange = "Sertifikat!\$L\${$rowStart}:\$L\${$rowEnd}";
                $alatTurunRange = "Sertifikat!\$O\${$rowStart}:\$O\${$rowEnd}";
                $standarNaikRange = "Sertifikat!\$R\${$rowStart}:\$R\${$rowEnd}";
                $standarTurunRange = "Sertifikat!\$U\${$rowStart}:\$U\${$rowEnd}";

                $categoryAxis = [
                    new DataSeriesValues(
                        'String',
                        null,
                        null,
                        $totalTitik,
                        range(1, $totalTitik) // isi kategori dengan 1,2,3,...
                    )
                ];

                $seriesList = [
                    [
                        'label' => '"Titik Kalibrasi"',
                        'range' => $titikKalibrasiRange,
                        'order' => [0],
                    ],
                    [
                        'label' => '"Alat Naik"',
                        'range' => $alatNaikRange,
                        'order' => [1],
                    ],
                    [
                        'label' => '"Alat Turun"',
                        'range' => $alatTurunRange,
                        'order' => [2],
                    ],
                    [
                        'label' => '"Standar Naik"',
                        'range' => $standarNaikRange,
                        'order' => [3],
                    ],
                    [
                        'label' => '"Standar Turun"',
                        'range' => $standarTurunRange,
                        'order' => [4],
                    ],
                ];

                $newSeries = [];
                foreach ($seriesList as $s) {
                    $newSeries[] = new DataSeries(
                        DataSeries::TYPE_LINECHART,           // tipe chart
                        null,                                 // grouping
                        $s['order'],                          // urutan
                        [new DataSeriesValues('String', $s['label'], null, 1)], // legend
                        $categoryAxis,                        // kategori
                        [new DataSeriesValues('Number', $s['range'], null, $totalTitik)] // nilai
                    );
                }

                $newPlotArea = new PlotArea(null, $newSeries);
                $chart->setPlotArea($newPlotArea);
            }
        }

        // Tanggal diterbitkan
        $issuedDate = $sertifikat->issued_at
            ? \Carbon\Carbon::parse($sertifikat->issued_at)->format('d/m/Y')
            : '-';

        $sheet->setCellValue('X61', "Diterbitkan tanggal : $issuedDate");

        return $spreadsheet;
    }

    private function _fillJangkaSorong(Spreadsheet $spreadsheet, $approvals, $kalibrasi, $alat, $sertifikat)
    {
        $sheet = $spreadsheet->getActiveSheet();

        // Header Information Alat & kalibrasi
        $sheet->setCellValue('I7', $alat->departemen_pemilik ?? '-');
        $sheet->setCellValue('I8', $alat->lokasi_alat ?? '-');
        $sheet->setCellValue('I9', $alat->no_kalibrasi ?? '-');
        $sheet->setCellValue('I10', $alat->nama_alat ?? '-');
        $sheet->setCellValue('I11', $alat->merk ?? '-');
        $sheet->setCellValue('I12', $alat->tipe ?? '-');
        $sheet->setCellValue('I13', $alat->kapasitas ?? '-');
        $sheet->setCellValue('I14', $alat->resolusi ?? '-');
        $sheet->setCellValue('AA7', $alat->range_penggunaan_alat ?? '-');
        $sheet->setCellValue('AA8', $alat->limits_of_permissible_error ?? '-');
        $sheet->setCellValue('AA9', $alat->kode_alat ?? '-');
        $sheet->setCellValue('I15', $kalibrasi->lokasi_kalibrasi ?? '-');
        $sheet->setCellValue('I16', $kalibrasi->suhu_ruangan ?? '-');
        $sheet->setCellValue('I17', $kalibrasi->kelembaban ?? '-');
        $sheet->setCellValue('AA10', $kalibrasi->tgl_kalibrasi ?? '-');
        $sheet->setCellValue('AA11', $kalibrasi->tgl_kalibrasi_ulang ?? '-');

        // ===== Data Jangka Sorong =====
        $rowStart = 26;
        $row = $rowStart;
        $startRow = $row;

        $jangkaSorong = $kalibrasi->jangkaSorong
            ->sortBy('master_ke')
            ->values();

        $totalTitik = $jangkaSorong->count();

        foreach ($jangkaSorong as $tg) {

            $sheet->setCellValue("D{$row}", $tg->master->nilai_master ?? '');
            $sheet->setCellValue("L{$row}", $tg->avg_pembacaan ?? '');
            $sheet->setCellValue("R{$row}", $tg->master->nilai_master ?? '');
            $sheet->setCellValue("X{$row}", $tg->koreksi ?? '');

            $row++;
        }

        // Tentukan baris terakhir setelah loop
        $endRow = $row - 1;

        // Range merge
        $ketidakPastianNilai = optional($kalibrasi->jangkaSorongSummary->first())->ketidakpastian ?? '';
        $ketidakPastian = "AD{$startRow}:AI{$endRow}";

        // Cek dan unmerge hanya jika memang terdaftar
        $mergedCells = $sheet->getMergeCells();

        if (isset($mergedCells[$ketidakPastian])) {
            $sheet->unmergeCells($ketidakPastian);
        }

        // Merge ulang
        $sheet->mergeCells($ketidakPastian);

        $sheet->setCellValue("AD{$startRow}", $ketidakPastianNilai);

        $sheet->getStyle("AD{$startRow}:AF{$endRow}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $baseRow = 55;
        $nameRow = 59;

        // Mapping posisi tanda tangan berdasarkan jabatan
        $roleColumnMap = [
            'foreman'    => 'C',
            'supervisor' => 'H',
            'dept_head'  => 'M',
        ];

        // Loop semua approver
        foreach ($approvals as $approval) {
            $jabatan = strtolower($approval['jabatan'] ?? '');
            $departemen = strtolower($approval['departemen'] ?? '');
            $status = strtolower($approval['status'] ?? ''); // status approval

            // Default kolom berdasarkan jabatan
            $col = $roleColumnMap[$jabatan] ?? null;

            // Jika foreman dari departemen non-engineering → pindah ke kolom S
            if ($jabatan === 'foreman' && $departemen !== 'engineering') {
                $col = 'S';
            }

            if (!$col) {
                Log::warning("Kolom tidak ditemukan untuk jabatan={$jabatan}, departemen={$departemen}");
                continue;
            }

            // Tentukan range merge
            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // === Jika belum approve → kosongkan area ===
            if (!in_array($status, ['approved', 'rejected'])) {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, '');
                continue;
            }

            // === Path tanda tangan ===
            $approvedPath = public_path('assets/images/ttd/approved_sticker.png');
            $rejectedPath = public_path('assets/images/ttd/rejected_sticker.png');

            $status = strtolower($approval['status'] ?? '');
            $relativePath = $approval['ttd'] ?? null;

            $isDummy = true;

            // === Tentukan dummy berdasarkan status ===
            if ($status === 'approved') {
                $dummyPath = $approvedPath;
            } elseif ($status === 'rejected') {
                $dummyPath = $rejectedPath;
            } else {
                $dummyPath = $approvedPath;
            }

            // === Cek apakah ada TTD asli ===
            if ($relativePath) {
                $signaturePath = public_path('storage/' . $relativePath);

                if (file_exists($signaturePath) && $status === 'approved') {
                    $finalPath = $signaturePath;
                    $isDummy = false;
                } else {
                    $finalPath = $dummyPath;
                }
            } else {
                $finalPath = $dummyPath;
            }

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // === Gambar tanda tangan ===
            try {
                $rangeBounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($mergeRange);
                $startCol = $rangeBounds[0][0];
                $endCol   = $rangeBounds[1][0];

                $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                $endColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

                // Hitung total lebar kolom
                $totalWidth = 0;
                for ($j = $startCol; $j <= $endCol; $j++) {
                    $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                }

                // Posisi gambar agak tengah
                $offsetX = ($totalWidth * 6.2 / 2) - 25;
                if ($offsetX < 0) $offsetX = 0;

                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath($finalPath);
                $drawing->setHeight($isDummy ? 70 : 100);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX($isDummy ? 30 : 15);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $th) {
                Log::warning('Gagal memuat TTD: ' . $th->getMessage());
            }

            // === Nama approver di bawah tanda tangan ===
            $approverName = strtoupper($approval['approver_name'] ?? '-');
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . $approverName . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        $rowEnd = $row - 1;

        if ($totalTitik > 0) {
            $chartCollection = $sheet->getChartCollection();

            foreach ($chartCollection as $chart) {
                if (!$chart || !$chart->getPlotArea()) continue;

                $plotArea = $chart->getPlotArea();
                $oldSeries = $plotArea->getPlotGroup();

                if (empty($oldSeries)) continue;

                $categoryRange = "Sertifikat!\$D\${$rowStart}:\$D\${$rowEnd}"; // Titik kalibrasi (X)
                $titikKalibrasiRange = "Sertifikat!\$D\${$rowStart}:\$D\${$rowEnd}"; // Titik kalibrasi (X)
                $pembacaanAlat = "Sertifikat!\$L\${$rowStart}:\$L\${$rowEnd}";
                $pembacaanStandar = "Sertifikat!\$R\${$rowStart}:\$R\${$rowEnd}";

                $categoryAxis = [
                    new DataSeriesValues(
                        'String',
                        null,
                        null,
                        $totalTitik,
                        range(1, $totalTitik) // isi kategori dengan 1,2,3,...
                    )
                ];

                $seriesList = [
                    [
                        'label' => '"Titik Kalibrasi"',
                        'range' => $titikKalibrasiRange,
                        'order' => [0],
                    ],
                    [
                        'label' => '"Pembacaan Alat"',
                        'range' => $pembacaanAlat,
                        'order' => [1],
                    ],
                    [
                        'label' => '"Pembacaan Standar"',
                        'range' => $pembacaanStandar,
                        'order' => [2],
                    ],
                ];

                $newSeries = [];
                foreach ($seriesList as $s) {
                    $newSeries[] = new DataSeries(
                        DataSeries::TYPE_LINECHART,           // tipe chart
                        null,                                 // grouping
                        $s['order'],                          // urutan
                        [new DataSeriesValues('String', $s['label'], null, 1)], // legend
                        $categoryAxis,                        // kategori
                        [new DataSeriesValues('Number', $s['range'], null, $totalTitik)] // nilai
                    );
                }

                $newPlotArea = new PlotArea(null, $newSeries);
                $chart->setPlotArea($newPlotArea);
            }
        }

        // Tanggal diterbitkan
        $issuedDate = $sertifikat->issued_at
            ? \Carbon\Carbon::parse($sertifikat->issued_at)->format('d/m/Y')
            : '-';

        $sheet->setCellValue('X54', "Diterbitkan tanggal : $issuedDate");

        return $spreadsheet;
    }

    private function _fillTimbangan(Spreadsheet $spreadsheet, $approvals, $kalibrasi, $alat, $sertifikat)
    {
        $sheet = $spreadsheet->getActiveSheet();

        // Header Information Alat & kalibrasi
        $sheet->setCellValue('I7', $alat->departemen_pemilik ?? '-');
        $sheet->setCellValue('I8', $alat->lokasi_alat ?? '-');
        $sheet->setCellValue('I9', $alat->no_kalibrasi ?? '-');
        $sheet->setCellValue('I10', $alat->nama_alat ?? '-');
        $sheet->setCellValue('I11', $alat->merk ?? '-');
        $sheet->setCellValue('I12', $alat->tipe ?? '-');
        $sheet->setCellValue('I13', $alat->kapasitas ?? '-');
        $sheet->setCellValue('I14', $alat->resolusi ?? '-');
        $sheet->setCellValue('AA7', $alat->range_penggunaan_alat ?? '-');
        $sheet->setCellValue('AA8', $alat->limits_of_permissible_error ?? '-');
        $sheet->setCellValue('AA9', $alat->kode_alat ?? '-');
        $sheet->setCellValue('I15', $kalibrasi->lokasi_kalibrasi ?? '-');
        $sheet->setCellValue('I16', $kalibrasi->suhu_ruangan ?? '-');
        $sheet->setCellValue('I17', $kalibrasi->kelembaban ?? '-');
        $sheet->setCellValue('AA10', $kalibrasi->tgl_kalibrasi ?? '-');
        $sheet->setCellValue('AA11', $kalibrasi->tgl_kalibrasi_ulang ?? '-');

        // Data Kemampuan Ulang Pembacaan
        $summaryList = $kalibrasi->kemampuanUlangSummary;

        $map = [
            'mendekati_nol'      => 27,
            'setengah_kapasitas' => 28,
            'full_kapasitas'     => 29,
        ];

        foreach ($map as $jenis => $row) {

            $summary = $summaryList?->firstWhere('jenis', $jenis);

            if ($summary) {

                $sheet->setCellValue("N{$row}", $summary->massa ?? '-'); // tidak ada beban di response
                $sheet->setCellValue("S{$row}", (float) $summary->std_dev);
                $sheet->setCellValue("X{$row}", $summary->maks_perbedaan_akhir ?? '-');
            } else {

                $sheet->setCellValue("N{$row}", '-');
                $sheet->setCellValue("S{$row}", '-');
                $sheet->setCellValue("X{$row}", '-');
            }
        }

        // Data Keseragaman Skala
        $keseragaman = $kalibrasi->keseragamanSkalaSummary()
            ->orderBy('massa_ke')
            ->get();

        if ($keseragaman->isNotEmpty()) {

            $startRow = 37;

            foreach ($keseragaman as $i => $data) {

                $row = $startRow + $i;

                $sheet->setCellValue("C{$row}", $data->beban ?? '-');
                $sheet->setCellValue("I{$row}", $data->koreksi_skala ?? '-');
            }
        }

        // Data Pengaruh Pada Pinggan
        $pinggan = $kalibrasi->pingganSummary;

        if ($pinggan) {

            $row = 40;

            $sheet->setCellValue("R{$row}", $pinggan->summary_tengah ?? '-');
            $sheet->setCellValue("U{$row}", $pinggan->summary_depan ?? '-');
            $sheet->setCellValue("X{$row}", $pinggan->summary_belakang ?? '-');
            $sheet->setCellValue("AA{$row}", $pinggan->summary_kiri ?? '-');
            $sheet->setCellValue("AD{$row}", $pinggan->summary_kanan ?? '-');
            $sheet->setCellValue("AG{$row}", $pinggan->selisih_maks ?? '-');
        }

        // Data Pengnolan Beban (Tare)
        $tareCollection = $kalibrasi->tareSummary;

        if ($tareCollection && $tareCollection->count()) {

            $tanpa  = $tareCollection->firstWhere('kondisi', 'tanpa');
            $dengan = $tareCollection->firstWhere('kondisi', 'dengan');

            $sheet->setCellValue('V48', $tanpa->selisih_mz ?? '-');
            $sheet->setCellValue('V49', $dengan->selisih_mz ?? '-');
        }

        // Histerisis
        $histerisis = $kalibrasi->histerisisSummary;

        if ($histerisis) {
            $sheet->setCellValue('AD48', $histerisis->setengah_kapasitas ?? '-');
            $sheet->setCellValue('AG48', $histerisis->histerisis ?? '-');
        }

        // Ketidak Pastian
        $kp = $kalibrasi->ketidakpastianSummary;

        if ($kp) {
            $sheet->setCellValue('N49', $kp->ketidakpastian_perluas ?? '-');
        }

        $baseRow = 66;
        $nameRow = 70;

        $roleColumnMap = [
            'foreman'    => 'C',
            'supervisor' => 'H',
            'dept_head'  => 'M',
        ];

        foreach ($approvals as $approval) {
            $jabatan = strtolower($approval['jabatan'] ?? '');
            $departemen = strtolower($approval['departemen'] ?? '');
            $status = strtolower($approval['status'] ?? ''); // status approval

            // Default kolom berdasarkan jabatan
            $col = $roleColumnMap[$jabatan] ?? null;

            // Jika foreman dari departemen non-engineering → pindah ke kolom S
            if ($jabatan === 'foreman' && $departemen !== 'engineering') {
                $col = 'S';
            }

            if (!$col) {
                Log::warning("Kolom tidak ditemukan untuk jabatan={$jabatan}, departemen={$departemen}");
                continue;
            }

            // Tentukan range merge
            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // === Jika belum approve → kosongkan area ===
            if (!in_array($status, ['approved', 'rejected'])) {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, '');
                continue;
            }

            // === Path tanda tangan ===
            $approvedPath = public_path('assets/images/ttd/approved_sticker.png');
            $rejectedPath = public_path('assets/images/ttd/rejected_sticker.png');

            $status = strtolower($approval['status'] ?? '');
            $relativePath = $approval['ttd'] ?? null;

            $isDummy = true;

            // === Tentukan dummy berdasarkan status ===
            if ($status === 'approved') {
                $dummyPath = $approvedPath;
            } elseif ($status === 'rejected') {
                $dummyPath = $rejectedPath;
            } else {
                $dummyPath = $approvedPath;
            }

            // === Cek apakah ada TTD asli ===
            if ($relativePath) {
                $signaturePath = public_path('storage/' . $relativePath);

                if (file_exists($signaturePath) && $status === 'approved') {
                    $finalPath = $signaturePath;
                    $isDummy = false;
                } else {
                    $finalPath = $dummyPath;
                }
            } else {
                $finalPath = $dummyPath;
            }

            // dd($approval);

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // === Gambar tanda tangan ===
            try {
                $rangeBounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($mergeRange);
                $startCol = $rangeBounds[0][0];
                $endCol   = $rangeBounds[1][0];

                $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                $endColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

                // Hitung total lebar kolom
                $totalWidth = 0;
                for ($j = $startCol; $j <= $endCol; $j++) {
                    $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                }

                // Posisi gambar agak tengah
                $offsetX = ($totalWidth * 6.2 / 2) - 25;
                if ($offsetX < 0) $offsetX = 0;

                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath($finalPath);
                $drawing->setWidth($isDummy ? 70 : 100);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX($isDummy ? 30 : 15);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $th) {
                Log::warning('Gagal memuat TTD: ' . $th->getMessage());
            }

            // === Nama approver di bawah tanda tangan ===
            $approverName = strtoupper($approval['approver_name'] ?? '-');
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . $approverName . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        // Tanggal diterbitkan
        $issuedDate = $sertifikat->issued_at
            ? \Carbon\Carbon::parse($sertifikat->issued_at)->format('d/m/Y')
            : '-';

        $sheet->setCellValue('X65', "Diterbitkan tanggal : $issuedDate");

        return $spreadsheet;
    }

    private function _fillInstrumen(Spreadsheet $spreadsheet, $kalibrasi, $alat, $approvals, $sertifikat)
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('I7', $alat->departemen_pemilik ?? '-');
        $sheet->setCellValue('I8', $alat->lokasi_alat ?? '-');
        $sheet->setCellValue('I9', $alat->no_kalibrasi ?? '-');
        $sheet->setCellValue('I10', $alat->nama_alat ?? '-');
        $sheet->setCellValue('I11', $alat->merk ?? '-');
        $sheet->setCellValue('I12', $alat->tipe ?? '-');
        $sheet->setCellValue('I13', $alat->kapasitas ?? '-');
        $sheet->setCellValue('I14', $alat->resolusi ?? '-');
        $sheet->setCellValue('AA7', $alat->range_penggunaan_alat ?? '-');
        $sheet->setCellValue('AA8', $alat->limits_of_permissible_error ?? '-');
        $sheet->setCellValue('AA9', $alat->kode_alat ?? '-');
        $sheet->setCellValue('I15', $kalibrasi->lokasi_kalibrasi ?? '-');
        $sheet->setCellValue('I16', $kalibrasi->suhu_ruangan ?? '-');
        $sheet->setCellValue('I17', $kalibrasi->kelembaban ?? '-');
        $sheet->setCellValue('AA10', $kalibrasi->tgl_kalibrasi ?? '-');
        $sheet->setCellValue('AA11', $kalibrasi->tgl_kalibrasi_ulang ?? '-');

        $rowStart = 26;
        $row = $rowStart;

        $instruments = $kalibrasi->instrumen
            ->sortBy('titik_kalibrasi')
            ->values();

        $totalTitik = $instruments->count();

        foreach ($instruments as $tg) {

            $n = 5; // jumlah pengulangan

            $uRepeat = $tg->std_dev / sqrt($n);

            $uMaster = match ($tg->titik_kalibrasi) {
                '4'  => 0.02,
                '7'  => 0.02,
                '10' => 0.03,
                default => 0.02,
            };

            // Gabungan
            $uGab = sqrt(
                pow($uRepeat, 2) +
                    pow($uMaster, 2)
            );

            $U = 2 * $uGab;

            $sheet->setCellValue("D{$row}", $tg->titik_kalibrasi ?? '');
            $sheet->setCellValue("L{$row}", $tg->avg_pembacaan ?? '');
            $sheet->setCellValue("R{$row}", $tg->nilai_master ?? '');
            $sheet->setCellValue("X{$row}", $tg->koreksi ?? '');
            $sheet->setCellValue("AD{$row}", $uGab);

            $row++;
        }

        $baseRow = 60;
        $nameRow = 64;

        // Mapping posisi tanda tangan berdasarkan jabatan
        $roleColumnMap = [
            'foreman'    => 'C',
            'supervisor' => 'H',
            'dept_head'  => 'M',
        ];

        foreach ($approvals as $approval) {
            $jabatan = strtolower($approval['jabatan'] ?? '');
            $departemen = strtolower($approval['departemen'] ?? '');
            $status = strtolower($approval['status'] ?? ''); // status approval

            // Default kolom berdasarkan jabatan
            $col = $roleColumnMap[$jabatan] ?? null;

            // Jika foreman dari departemen non-engineering → pindah ke kolom S
            if ($jabatan === 'foreman' && $departemen !== 'engineering') {
                $col = 'S';
            }

            if (!$col) {
                Log::warning("Kolom tidak ditemukan untuk jabatan={$jabatan}, departemen={$departemen}");
                continue;
            }

            // Tentukan range merge
            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // === Jika belum approve → kosongkan area ===
            if (!in_array($status, ['approved', 'rejected'])) {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, '');
                continue;
            }

            // === Path tanda tangan ===
            $approvedPath = public_path('assets/images/ttd/approved_sticker.png');
            $rejectedPath = public_path('assets/images/ttd/rejected_sticker.png');

            $status = strtolower($approval['status'] ?? '');
            $relativePath = $approval['ttd'] ?? null;

            $isDummy = true;

            // === Tentukan dummy berdasarkan status ===
            if ($status === 'approved') {
                $dummyPath = $approvedPath;
            } elseif ($status === 'rejected') {
                $dummyPath = $rejectedPath;
            } else {
                $dummyPath = $approvedPath;
            }

            // dd($dummyPath);

            // === Cek apakah ada TTD asli ===
            if ($relativePath) {
                $signaturePath = public_path('storage/' . $relativePath);

                if (file_exists($signaturePath) && $status === 'approved') {
                    $finalPath = $signaturePath;
                    $isDummy = false;
                } else {
                    $finalPath = $dummyPath;
                }
            } else {
                $finalPath = $dummyPath;
            }

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // === Gambar tanda tangan ===
            try {
                $rangeBounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($mergeRange);
                $startCol = $rangeBounds[0][0];
                $endCol   = $rangeBounds[1][0];

                $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                $endColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

                // Hitung total lebar kolom
                $totalWidth = 0;
                for ($j = $startCol; $j <= $endCol; $j++) {
                    $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                }

                // Posisi gambar agak tengah
                $offsetX = ($totalWidth * 6.2 / 2) - 25;
                if ($offsetX < 0) $offsetX = 0;

                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath($finalPath);
                $drawing->setHeight($isDummy ? 65 : 70);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX($isDummy ? 30 : 15);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $th) {
                Log::warning('Gagal memuat TTD: ' . $th->getMessage());
            }

            // === Nama approver di bawah tanda tangan ===
            $approverName = strtoupper($approval['approver_name'] ?? '-');
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . $approverName . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Kurva
        $rowEnd = $row - 1;

        if ($kalibrasi->instrumen->count() > 0) {
            $chartCollection = $sheet->getChartCollection();

            foreach ($chartCollection as $chart) {
                if (!$chart || !$chart->getPlotArea()) continue;

                $plotArea = $chart->getPlotArea();
                $oldSeries = $plotArea->getPlotGroup();

                if (empty($oldSeries)) continue;

                $categoryRange = 'Sertifikat!$D$' . $rowStart . ':$D$' . $rowEnd; // Titik kalibrasi (kategori)
                $titikKalibrasiRange = 'Sertifikat!$D$' . $rowStart . ':$D$' . $rowEnd; // nilai titik kalibrasi juga bisa numerik
                $alatUkurRange = 'Sertifikat!$L$' . $rowStart . ':$L$' . $rowEnd;
                $alatStandarRange = 'Sertifikat!$R$' . $rowStart . ':$R$' . $rowEnd;

                $categoryAxis = [
                    new DataSeriesValues(
                        'String',
                        null,
                        null,
                        $totalTitik,
                        range(1, $totalTitik) // isi kategori dengan 1,2,3,...
                    )
                ];

                $seriesList = [
                    [
                        'label' => '"Titik Kalibrasi"',
                        'range' => $titikKalibrasiRange,
                        'order' => [0],
                    ],
                    [
                        'label' => '"Pembacaan Alat"',
                        'range' => $alatUkurRange,
                        'order' => [1],
                    ],
                    [
                        'label' => '"Pembacaan Standar"',
                        'range' => $alatStandarRange,
                        'order' => [2],
                    ]
                ];

                $newSeries = [];
                foreach ($seriesList as $s) {
                    $newSeries[] = new DataSeries(
                        DataSeries::TYPE_LINECHART,           // tipe chart
                        null,                                 // grouping
                        $s['order'],                          // urutan
                        [new DataSeriesValues('String', $s['label'], null, 1)], // legend
                        $categoryAxis,                        // kategori
                        [new DataSeriesValues('Number', $s['range'], null, $totalTitik)] // nilai
                    );
                }

                $newPlotArea = new PlotArea(null, $newSeries);
                $chart->setPlotArea($newPlotArea);
            }
        }

        // Tanggal diterbitkan
        $issuedDate = $sertifikat->issued_at
            ? \Carbon\Carbon::parse($sertifikat->issued_at)->format('d/m/Y')
            : '-';

        $sheet->setCellValue('X59', "Diterbitkan tanggal : $issuedDate");

        return $spreadsheet;
    }

    private function _fillDimensi(Spreadsheet $spreadsheet, $kalibrasi, $alat, $approvals, $sertifikat)
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('I7', $alat->departemen_pemilik ?? '-');
        $sheet->setCellValue('I8', $alat->lokasi_alat ?? '-');
        $sheet->setCellValue('I9', $alat->no_kalibrasi ?? '-');
        $sheet->setCellValue('I10', $alat->nama_alat ?? '-');
        $sheet->setCellValue('I11', $alat->merk ?? '-');
        $sheet->setCellValue('I12', $alat->tipe ?? '-');
        $sheet->setCellValue('I13', $alat->kapasitas ?? '-');
        $sheet->setCellValue('I14', $alat->resolusi ?? '-');
        $sheet->setCellValue('AA7', $alat->range_penggunaan_alat ?? '-');
        $sheet->setCellValue('AA8', $alat->limits_of_permissible_error ?? '-');
        $sheet->setCellValue('AA9', $alat->kode_alat ?? '-');
        $sheet->setCellValue('I15', $kalibrasi->lokasi_kalibrasi ?? '-');
        $sheet->setCellValue('I16', $kalibrasi->suhu_ruangan ?? '-');
        $sheet->setCellValue('I17', $kalibrasi->kelembaban ?? '-');
        $sheet->setCellValue('AA10', $kalibrasi->tgl_kalibrasi ?? '-');
        $sheet->setCellValue('AA11', $kalibrasi->tgl_kalibrasi_ulang ?? '-');

        $rowStart = 26;
        $row = 26;

        $dimention = $kalibrasi->dimensi
            ->sortBy('titik_kalibrasi')
            ->values();

        $totalTitik = $dimention->count();

        foreach ($dimention as $v) {
            $sheet->setCellValue("D{$row}", $v->titik_kalibrasi ?? '-');
            $sheet->setCellValue("L{$row}", $v->nilai_master ?? '-');
            $sheet->setCellValue("R{$row}", $v->avg_pembacaan ?? '-');
            $sheet->setCellValue("X{$row}", $v->koreksi ?? '-');
            $sheet->setCellValue("AD{$row}", $v->ketidakpastian ?? '-');

            $row++;
        }

        $baseRow = 55;
        $nameRow = 59;

        // Mapping tetap untuk posisi tanda tangan
        $roleColumnMap = [
            'foreman'    => 'C',
            'supervisor' => 'H',
            'dept_head'   => 'M',
        ];

        foreach ($approvals as $approval) {
            $jabatan = strtolower($approval['jabatan'] ?? '');
            $departemen = strtolower($approval['departemen'] ?? '');
            $status = strtolower($approval['status'] ?? ''); // tambahkan ini

            // Default kolom berdasarkan jabatan
            $col = $roleColumnMap[$jabatan] ?? null;

            // Jika foreman dari departemen non-engineering → pindah ke kolom S
            if ($jabatan === 'foreman' && $departemen !== 'engineering') {
                $col = 'S';
            }

            if (!$col) continue; // skip jika tidak ada mapping kolom

            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // === Jika belum approve → kosongkan area ===
            if (!in_array($status, ['approved', 'rejected'])) {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, '');
                continue;
            }

            // === Path tanda tangan ===
            $approvedPath = public_path('assets/images/ttd/approved_sticker.png');
            $rejectedPath = public_path('assets/images/ttd/rejected_sticker.png');

            $status = strtolower($approval['status'] ?? '');
            $relativePath = $approval['ttd'] ?? null;

            $isDummy = true;

            // === Tentukan dummy berdasarkan status ===
            if ($status === 'approved') {
                $dummyPath = $approvedPath;
            } elseif ($status === 'rejected') {
                $dummyPath = $rejectedPath;
            } else {
                $dummyPath = $approvedPath;
            }

            // === Cek apakah ada TTD asli ===
            if ($relativePath) {
                $signaturePath = public_path('storage/' . $relativePath);

                if (file_exists($signaturePath) && $status === 'approved') {
                    $finalPath = $signaturePath;
                    $isDummy = false;
                } else {
                    $finalPath = $dummyPath;
                }
            } else {
                $finalPath = $dummyPath;
            }

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            try {
                $rangeBounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($mergeRange);
                $startCol = $rangeBounds[0][0];
                $endCol   = $rangeBounds[1][0];

                $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                $endColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

                $totalWidth = 0;
                for ($j = $startCol; $j <= $endCol; $j++) {
                    $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                }

                $offsetX = ($totalWidth * 6.2 / 2) - 25;
                if ($offsetX < 0) $offsetX = 0;

                $drawing = new Drawing();
                $drawing->setPath($finalPath);
                $drawing->setHeight($isDummy ? 70 : 80);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX($isDummy ? 25 : 15);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $th) {
                Log::warning('Gagal memuat TTD: ' . $th->getMessage());
            }

            // Nama approver di bawah TTD (huruf kapital)
            $approverName = ucfirst($approval['approver_name'] ?? '-');
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . $approverName . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Kurva
        $rowEnd = $row - 1;

        if ($kalibrasi->dimensi->count() > 0) {
            $chartCollection = $sheet->getChartCollection();

            foreach ($chartCollection as $chart) {
                if (!$chart || !$chart->getPlotArea()) continue;

                $plotArea = $chart->getPlotArea();
                $oldSeries = $plotArea->getPlotGroup();

                if (empty($oldSeries)) continue;

                $categoryRange = 'Sertifikat!$D$' . $rowStart . ':$D$' . $rowEnd; // Titik kalibrasi (kategori)
                $titikKalibrasiRange = 'Sertifikat!$D$' . $rowStart . ':$D$' . $rowEnd; // nilai titik kalibrasi juga bisa numerik
                $alatUkurRange = 'Sertifikat!$L$' . $rowStart . ':$L$' . $rowEnd;
                $alatStandarRange = 'Sertifikat!$R$' . $rowStart . ':$R$' . $rowEnd;

                $categoryAxis = [
                    new DataSeriesValues(
                        'String',
                        null,
                        null,
                        $totalTitik,
                        range(1, $totalTitik) // isi kategori dengan 1,2,3,...
                    )
                ];

                $seriesList = [
                    [
                        'label' => '"Titik Kalibrasi"',
                        'range' => $titikKalibrasiRange,
                        'order' => [0],
                    ],
                    [
                        'label' => '"Pembacaan Alat"',
                        'range' => $alatUkurRange,
                        'order' => [1],
                    ],
                    [
                        'label' => '"Pembacaan Standar"',
                        'range' => $alatStandarRange,
                        'order' => [2],
                    ]
                ];

                $newSeries = [];
                foreach ($seriesList as $s) {
                    $newSeries[] = new DataSeries(
                        DataSeries::TYPE_LINECHART,           // tipe chart
                        null,                                 // grouping
                        $s['order'],                          // urutan
                        [new DataSeriesValues('String', $s['label'], null, 1)], // legend
                        $categoryAxis,                        // kategori
                        [new DataSeriesValues('Number', $s['range'], null, $totalTitik)] // nilai
                    );
                }

                $newPlotArea = new PlotArea(null, $newSeries);
                $chart->setPlotArea($newPlotArea);
            }
        }

        // Tanggal diterbitkan
        $issuedDate = $sertifikat->issued_at
            ? \Carbon\Carbon::parse($sertifikat->issued_at)->format('d/m/Y')
            : '-';

        $sheet->setCellValue('X54', "Diterbitkan tanggal : $issuedDate");

        return $spreadsheet;
    }

    private function _fillFlowmeter(Spreadsheet $spreadsheet, $kalibrasi, $alat, $approvals, $sertifikat)
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('I7', $alat->departemen_pemilik ?? '-');
        $sheet->setCellValue('I8', $alat->lokasi_alat ?? '-');
        $sheet->setCellValue('I9', $alat->no_kalibrasi ?? '-');
        $sheet->setCellValue('I10', $alat->nama_alat ?? '-');
        $sheet->setCellValue('I11', $alat->merk ?? '-');
        $sheet->setCellValue('I12', $alat->tipe ?? '-');
        $sheet->setCellValue('I13', $alat->kapasitas ?? '-');
        $sheet->setCellValue('I14', $alat->resolusi ?? '-');
        $sheet->setCellValue('AA7', $alat->range_penggunaan_alat ?? '-');
        $sheet->setCellValue('AA8', $alat->limits_of_permissible_error ?? '-');
        $sheet->setCellValue('AA9', $alat->kode_alat ?? '-');
        $sheet->setCellValue('I15', $kalibrasi->lokasi_kalibrasi ?? '-');
        $sheet->setCellValue('I16', $kalibrasi->suhu_ruangan ?? '-');
        $sheet->setCellValue('I17', $kalibrasi->kelembaban ?? '-');
        $sheet->setCellValue('AA10', $kalibrasi->tgl_kalibrasi ?? '-');
        $sheet->setCellValue('AA11', $kalibrasi->tgl_kalibrasi_ulang ?? '-');

        $rowStart = 25;
        $row = 25;

        $flowmter = $kalibrasi->flowmeter
            ->sortBy('titik_kalibrasi')
            ->values();

        $totalTitik = $flowmter->count();

        foreach ($flowmter as $v) {
            $sheet->setCellValue("D{$row}", $v->titik_kalibrasi ?? '-');
            $sheet->setCellValue("L{$row}", $v->avg_pembacaan ?? '-');
            $sheet->setCellValue("R{$row}", $v->nilai_master ?? '-');
            $sheet->setCellValue("X{$row}", $v->koreksi ?? '-');
            $sheet->setCellValue("AD{$row}", $v->ketidakpastian ?? '-');

            $row++;
        }

        $baseRow = 60;
        $nameRow = 64;

        // Mapping tetap untuk posisi tanda tangan
        $roleColumnMap = [
            'foreman'    => 'C',
            'supervisor' => 'H',
            'dept_head'   => 'M',
        ];

        foreach ($approvals as $approval) {
            $jabatan = strtolower($approval['jabatan'] ?? '');
            $departemen = strtolower($approval['departemen'] ?? '');
            $status = strtolower($approval['status'] ?? ''); // tambahkan ini

            // Default kolom berdasarkan jabatan
            $col = $roleColumnMap[$jabatan] ?? null;

            // Jika foreman dari departemen non-engineering → pindah ke kolom S
            if ($jabatan === 'foreman' && $departemen !== 'engineering') {
                $col = 'S';
            }

            if (!$col) continue; // skip jika tidak ada mapping kolom

            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // === Jika belum approve → kosongkan area ===
            if (!in_array($status, ['approved', 'rejected'])) {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, '');
                continue;
            }

            // === Path tanda tangan ===
            $approvedPath = public_path('assets/images/ttd/approved_sticker.png');
            $rejectedPath = public_path('assets/images/ttd/rejected_sticker.png');

            $status = strtolower($approval['status'] ?? '');
            $relativePath = $approval['ttd'] ?? null;

            $isDummy = true;

            // === Tentukan dummy berdasarkan status ===
            if ($status === 'approved') {
                $dummyPath = $approvedPath;
            } elseif ($status === 'rejected') {
                $dummyPath = $rejectedPath;
            } else {
                $dummyPath = $approvedPath;
            }

            // === Cek apakah ada TTD asli ===
            if ($relativePath) {
                $signaturePath = public_path('storage/' . $relativePath);

                if (file_exists($signaturePath) && $status === 'approved') {
                    $finalPath = $signaturePath;
                    $isDummy = false;
                } else {
                    $finalPath = $dummyPath;
                }
            } else {
                $finalPath = $dummyPath;
            }

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            try {
                $rangeBounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($mergeRange);
                $startCol = $rangeBounds[0][0];
                $endCol   = $rangeBounds[1][0];

                $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                $endColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

                $totalWidth = 0;
                for ($j = $startCol; $j <= $endCol; $j++) {
                    $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                }

                $offsetX = ($totalWidth * 6.2 / 2) - 25;
                if ($offsetX < 0) $offsetX = 0;

                $drawing = new Drawing();
                $drawing->setPath($finalPath);
                $drawing->setHeight($isDummy ? 70 : 80);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX($isDummy ? 25 : 15);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            } catch (\Throwable $th) {
                Log::warning('Gagal memuat TTD: ' . $th->getMessage());
            }

            // Nama approver di bawah TTD (huruf kapital)
            $approverName = ucfirst($approval['approver_name'] ?? '-');
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . $approverName . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Kurva
        $rowEnd = $row - 1;

        if ($kalibrasi->flowmeter->count() > 0) {
            $chartCollection = $sheet->getChartCollection();

            foreach ($chartCollection as $chart) {
                if (!$chart || !$chart->getPlotArea()) continue;

                $plotArea = $chart->getPlotArea();
                $oldSeries = $plotArea->getPlotGroup();

                if (empty($oldSeries)) continue;

                $categoryRange = 'Sertifikat!$D$' . $rowStart . ':$D$' . $rowEnd; // Titik kalibrasi (kategori)
                $titikKalibrasiRange = 'Sertifikat!$D$' . $rowStart . ':$D$' . $rowEnd; // nilai titik kalibrasi juga bisa numerik
                $alatUkurRange = 'Sertifikat!$L$' . $rowStart . ':$L$' . $rowEnd;
                $alatStandarRange = 'Sertifikat!$R$' . $rowStart . ':$R$' . $rowEnd;

                $categoryAxis = [
                    new DataSeriesValues(
                        'String',
                        null,
                        null,
                        $totalTitik,
                        range(1, $totalTitik) // isi kategori dengan 1,2,3,...
                    )
                ];

                $seriesList = [
                    [
                        'label' => '"Titik Kalibrasi"',
                        'range' => $titikKalibrasiRange,
                        'order' => [0],
                    ],
                    [
                        'label' => '"Pembacaan Alat"',
                        'range' => $alatUkurRange,
                        'order' => [1],
                    ],
                    [
                        'label' => '"Pembacaan Standar"',
                        'range' => $alatStandarRange,
                        'order' => [2],
                    ]
                ];

                $newSeries = [];
                foreach ($seriesList as $s) {
                    $newSeries[] = new DataSeries(
                        DataSeries::TYPE_LINECHART,           // tipe chart
                        null,                                 // grouping
                        $s['order'],                          // urutan
                        [new DataSeriesValues('String', $s['label'], null, 1)], // legend
                        $categoryAxis,                        // kategori
                        [new DataSeriesValues('Number', $s['range'], null, $totalTitik)] // nilai
                    );
                }

                $newPlotArea = new PlotArea(null, $newSeries);
                $chart->setPlotArea($newPlotArea);
            }
        }

        // Tanggal diterbitkan
        $issuedDate = $sertifikat->issued_at
            ? \Carbon\Carbon::parse($sertifikat->issued_at)->format('d/m/Y')
            : '-';

        $sheet->setCellValue('X59', "Diterbitkan tanggal : $issuedDate");

        return $spreadsheet;
    }
}
