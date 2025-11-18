<?php

namespace App\Http\Controllers\Kalibrasi;

// use \Log;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\RequestApprovalMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Kalibrasi\KalibrasiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use App\Models\Kalibrasi\KalibrasiSertifikatApprovalModel;

class KalibrasiCertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function reqApproval(Request $request, $id)
    {
        $request->validate([
            'manager_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
            'foreman_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $sertifikat = KalibrasiSertifikatModel::findOrFail($id);

        DB::beginTransaction();
        try {
            $approvers = [
                $request->manager_id,
                $request->supervisor_id,
                $request->foreman_id,
                $request->user_id
            ];

            foreach ($approvers as $approverId) {
                if ($approverId) {
                    $user = User::find($approverId);

                    $status = ($user->id === Auth::id()) ? 'approved' : 'pending';

                    KalibrasiSertifikatApprovalModel::create([
                        'sertifikat_id' => $sertifikat->id,
                        'approver_id' => $user->id,
                        'approver_email' => $user->email,
                        'status' => $status
                    ]);

                    // Kirim email hanya ke yang belum approve otomatis
                    if ($status === 'pending') {
                        Mail::to($user->email)
                            ->send(new RequestApprovalMail($sertifikat, $user->username));
                    }
                }
            }

            // Update status sertifikat -> kalau operator langsung approve, tetap pending ke atasannya
            $sertifikat->update(['status' => 'pending']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Request approval berhasil dikirim ke semua approver.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim request approval: ' . $e->getMessage()
            ], 500);
        }
    }


    public function handleApproval(Request $request)
    {
        $request->validate([
            'id' => 'required|integer', // ideally this is approval_id
            'status' => 'required|in:approved,rejected',
            'komentar' => 'nullable|string'
        ]);

        $userId = Auth::id();

        // Coba cari approval berdasarkan id (anggap id = approval.id)
        $approval = KalibrasiSertifikatApprovalModel::where('id', $request->id)
            ->where('approver_id', $userId)
            ->first();

        // Fallback: jika tidak ditemukan, coba treat id sebagai sertifikat_id (legacy)
        if (!$approval) {
            $approval = KalibrasiSertifikatApprovalModel::where('sertifikat_id', $request->id)
                ->where('approver_id', $userId)
                ->first();
        }

        if (!$approval) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data approval tidak ditemukan atau Anda tidak memiliki izin untuk menyetujui sertifikat ini.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Update approval row untuk approver ini
            $approval->update([
                'status' => $request->status,
                'comment' => $request->komentar ?? $approval->comment,
                'approved_at' => now(),
            ]);

            // Sekarang cek semua approval untuk sertifikat ini
            $sertifikatId = $approval->sertifikat_id;

            $allApprovals = KalibrasiSertifikatApprovalModel::where('sertifikat_id', $sertifikatId)->get();

            // Hitung status
            $hasRejected = $allApprovals->contains(function ($a) {
                return $a->status === 'rejected';
            });

            $allApproved = $allApprovals->every(function ($a) {
                return $a->status === 'approved';
            });

            // Tentukan status baru untuk sertifikat global
            if ($hasRejected) {
                $newSertifikatStatus = 'rejected';
            } elseif ($allApproved && $allApprovals->count() > 0) {
                $newSertifikatStatus = 'approved';
            } else {
                $newSertifikatStatus = 'pending';
            }

            // Update sertifikat utama hanya jika berubah
            $sertifikat = KalibrasiSertifikatModel::find($sertifikatId);
            if ($sertifikat && $sertifikat->status !== $newSertifikatStatus) {
                $sertifikat->update([
                    'status' => $newSertifikatStatus,
                    'issued_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $request->status === 'approved'
                    ? 'Sertifikat berhasil disetujui.'
                    : 'Sertifikat berhasil ditolak.',
                'sertifikat_status' => $newSertifikatStatus
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function showApprovalPage($id = null)
    {
        return view('kalibrasi.certificate.approval', compact('id'));
    }

    public function getSertifikatData(Request $request)
    {
        try {
            $userId = Auth::id();

            // Ambil semua approval berdasarkan user login
            $query = KalibrasiSertifikatApprovalModel::with([
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
                                        'approver_email',
                                        'status',
                                        'comment',
                                        'approved_at'
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


            $data = $query->orderByDesc('id')->get();

            return response()->json([
                'status' => 'success',
                'role'   => $user->jabatan ?? null,
                'data'   => $data,
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
        $approval = KalibrasiSertifikatApprovalModel::where('sertifikat_id', $sertifikatId)
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
            $totalApprovers = KalibrasiSertifikatApprovalModel::where('sertifikat_id', $sertifikatId)->count();
            $approvedCount = KalibrasiSertifikatApprovalModel::where('sertifikat_id', $sertifikatId)
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

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function downloadSertifikat($id)
    {
        try {
            // Ambil data sertifikat dan relasinya
            $sertifikat = KalibrasiSertifikatModel::with('kalibrasi.alat')->findOrFail($id);
            $kalibrasi = $sertifikat->kalibrasi;
            $alat = $kalibrasi->alat;
            $jenis = strtolower($kalibrasi->jenis_kalibrasi);

            $approvals = KalibrasiSertifikatApprovalModel::where('sertifikat_id', $sertifikat->id)
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

            // 🧠 Load template Excel dengan chart aktif
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setIncludeCharts(true);
            $spreadsheet = $reader->load($templatePath);

            // Load relasi sesuai jenis kalibrasi dan isi data ke template
            switch ($jenis) {
                case 'pressure':
                    $kalibrasi->load(['pressure', 'pressureGabungan']);
                    $this->_fillPressure($spreadsheet, $kalibrasi, $alat, $approvals);
                    break;

                case 'volumetrik':
                    $kalibrasi->load(['volumetrik']);
                    $this->_fillVolumetrik($spreadsheet, $kalibrasi, $alat, $approvals);
                    break;

                case 'temperature':
                    $kalibrasi->load(['temperature', 'temperatureGabungan']);
                    $this->_fillTemperature($spreadsheet, $approvals, $kalibrasi, $alat);
                    break;

                case 'thermohygrometer':
                    $kalibrasi->load(['thermohygrometer', 'thermohygrometerGabungan']);
                    $this->_fillThermo($spreadsheet, $approvals, $kalibrasi, $alat);
                    break;

                case 'jangka_sorong':
                    $kalibrasi->load(['jangkaSorong', 'jangkaSorongSummary', 'jangkaSorongFinalSummary']);
                    $this->_fillJangkaSorong($spreadsheet, $approvals, $kalibrasi, $alat);
                    break;

                case 'timbangan':
                    $kalibrasi->load(['pembacaanSummary', 'jangkaSorongSummary', 'jangkaSorongFinalSummary']);
                    $this->_fillTimbangan($spreadsheet, $approvals, $kalibrasi, $alat);
                    break;

                default:
                    return response()->json(['message' => 'Jenis kalibrasi tidak dikenali.'], 400);
            }

            // Siapkan lokasi penyimpanan
            $savePath = storage_path('assets/images/ttd/my ttd.jpg');
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

    private function _fillPressure(Spreadsheet $spreadsheet, $kalibrasi, $alat, $approvals)
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

        // ===== Data Tekanan Naik & Turun =====
        $rowStart = 26;
        $row = $rowStart;
        $index = 0;

        $totalTitik = count($kalibrasi->pressureGabungan);
        if ($totalTitik > 0) {
            $step = 100 / ($totalTitik - 1); // jarak antar titik (0% sampai 100%)
        }

        foreach ($kalibrasi->pressureGabungan as $pg) {
            // Hitung persentase posisi titik kalibrasi
            $persentase = $totalTitik > 1 ? $index * $step : 100; // kalau cuma 1 titik = 100%
            $sheet->setCellValue("D{$row}", round($persentase, 2));

            $sheet->setCellValue("H{$row}", $pg->titik_kalibrasi ?? '');
            $sheet->setCellValue("L{$row}", $pg->avg_penunjuk_alat_naik ?? '');
            $sheet->setCellValue("O{$row}", $pg->avg_penunjuk_alat_turun ?? '');
            $sheet->setCellValue("R{$row}", $pg->avg_tekanan_standar_naik ?? '');
            $sheet->setCellValue("U{$row}", $pg->avg_tekanan_standar_turun ?? '');
            $sheet->setCellValue("X{$row}", $pg->avg_kor_alat_naik ?? '');
            $sheet->setCellValue("AA{$row}", $pg->avg_kor_alat_turun ?? '');
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
            if ($status !== 'approved') {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, ''); // kosongkan nama juga
                continue;
            }

            // === Path tanda tangan ===
            $signaturePath = public_path("assets/images/ttd/{$approval['approver_id']}.png");
            $dummyPath = public_path('assets/images/ttd/my ttd.jpg');
            $finalPath = file_exists($signaturePath) ? $signaturePath : $dummyPath;

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
                $drawing->setHeight(70);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX(30);
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

        return $spreadsheet;
    }

    private function _fillVolumetrik(Spreadsheet $spreadsheet, $kalibrasi, $alat, $approvals)
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
        $u_total = $kalibrasi->volumetrikGabungan->u_total ?? null;
        $formatted_u_total = $u_total !== null ? number_format((float)$u_total, 3, '.', '') : '-';
        foreach ($kalibrasi->volumetrik as $v) {
            $sheet->setCellValue("D{$row}", $v->titik_kalibrasi ?? '-');
            $sheet->setCellValue("L{$row}", $v->penunjuk_alat ?? '-');
            $sheet->setCellValue("R{$row}", $v->penunjuk_standar ?? '-');
            $sheet->setCellValue("X{$row}", $v->koreksi ?? '-');

            $sheet->setCellValue("AD{$row}", $formatted_u_total);
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

            // Kosongkan area jika belum approve
            if ($status !== 'approved') {
                Log::info("Skip karena belum approve: {$approval['approver_name']}");
                $sheet->mergeCells($mergeRange);
                $sheet->setCellValue($col . $nameRow, ''); // kosongkan nama juga
                continue;
            }

            // Path tanda tangan
            $signaturePath = public_path("assets/images/ttd/{$approval['approver_id']}.png");
            $dummyPath = public_path('assets/images/ttd/my ttd.jpg');
            $finalPath = file_exists($signaturePath) ? $signaturePath : $dummyPath;

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
                $drawing->setHeight(70);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX(25);
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
    }

    private function _fillTemperature(Spreadsheet $spreadsheet, $approvals, $kalibrasi, $alat)
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
        $index = 0;

        $totalTitik = count($kalibrasi->temperatureGabungan);

        foreach ($kalibrasi->temperatureGabungan as $tg) {
            // Hitung persentase posisi titik kalibrasi
            // $persentase = $totalTitik > 1 ? $index * $step : 100; // kalau cuma 1 titik = 100%
            // $sheet->setCellValue("D{$row}", round($persentase, 2));
            $sheet->setCellValue("D{$row}", $tg->titik_kalibrasi ?? '');
            $sheet->setCellValue("L{$row}", $tg->avg_penunjuk_alat ?? '');
            // $sheet->setCellValue("O{$row}", $tg->avg_penunjuk_alat_turun ?? '');
            $sheet->setCellValue("R{$row}", $tg->avg_suhu_standar ?? '');
            // $sheet->setCellValue("U{$row}", $tg->avg_tekanan_standar_turun ?? '');
            $sheet->setCellValue("X{$row}", $tg->avg_kor_alat ?? '');
            // $sheet->setCellValue("AA{$row}", $tg->avg_kor_alat_turun ?? '');
            $sheet->setCellValue("AD{$row}", $tg->ketidakpastian ?? '');
            // $sheet->setCellValue("AG{$row}", $tg->u_gabungan ?? '');

            $row++;
            $index++;
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
            if ($status !== 'approved') {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, ''); // kosongkan nama
                continue;
            }

            // === Path tanda tangan ===
            $signaturePath = public_path("assets/images/ttd/{$approval['approver_id']}.png");
            $dummyPath = public_path('assets/images/ttd/my ttd.jpg');
            $finalPath = file_exists($signaturePath) ? $signaturePath : $dummyPath;

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
                $drawing->setHeight(70);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX(30);
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

        if ($totalTitik > 0) {
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

        return $spreadsheet;
    }

    private function _fillThermo(Spreadsheet $spreadsheet, $approvals, $kalibrasi, $alat)
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

        $totalTitik = count($kalibrasi->thermohygrometerGabungan);

        $totalData = $kalibrasi->thermohygrometerGabungan->count();
        $first = $kalibrasi->thermohygrometerGabungan->first();

        $ketidakpastian_suhu = $first->ketidak_pastian_suhu ?? '';
        $ketidakpastian_rh = $first->ketidak_pastian_rh ?? '';
        foreach ($kalibrasi->thermohygrometerGabungan as $tg) {
            // Pastikan nilai yang akan dihitung tidak null
            $avg_tekanan_standar_suhu = $tg->avg_tekanan_standar_suhu ?? 0;
            $avg_penunjuk_alat_suhu = $tg->avg_penunjuk_alat_suhu ?? 0;

            // Hitung selisih (R - L)
            $selisih = $avg_tekanan_standar_suhu - $avg_penunjuk_alat_suhu;

            $posisi = $tg->posisi ?? '';
            $posisi = str_ireplace(['kanan', 'kiri'], ['Ka.', 'Ki.'], $posisi);
            $posisi = ucwords(strtolower($posisi));

            // Isi data ke Excel
            $sheet->setCellValue("H{$row}", $posisi);
            $sheet->setCellValue("L{$row}", $avg_penunjuk_alat_suhu);
            $sheet->setCellValue("O{$row}", $tg->avg_penunjuk_alat_rh ?? '');
            $sheet->setCellValue("R{$row}", $avg_tekanan_standar_suhu);
            $sheet->setCellValue("U{$row}", $tg->avg_tekanan_standar_rh ?? '');
            $sheet->setCellValue("X{$row}", round($selisih, 2)); // hasil R - L
            $sheet->setCellValue("AA{$row}", $tg->avg_kor_alat_rh ?? '');

            $row++;
        }

        // Tentukan baris terakhir setelah loop
        $endRow = $row - 1;

        // Range merge
        $rangeSuhu = "AD{$startRow}:AF{$endRow}";
        $rangeRh   = "AG{$startRow}:AI{$endRow}";

        // Cek dan unmerge hanya jika memang terdaftar
        $mergedCells = $sheet->getMergeCells();

        if (isset($mergedCells[$rangeSuhu])) {
            $sheet->unmergeCells($rangeSuhu);
        }
        if (isset($mergedCells[$rangeRh])) {
            $sheet->unmergeCells($rangeRh);
        }

        // Merge ulang
        $sheet->mergeCells($rangeSuhu);
        $sheet->mergeCells($rangeRh);

        $sheet->setCellValue("AD{$startRow}", $ketidakpastian_suhu);
        $sheet->setCellValue("AG{$startRow}", $ketidakpastian_rh);

        $sheet->getStyle("AD{$startRow}:AF{$endRow}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle("AG{$startRow}:AI{$endRow}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

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
            if ($status !== 'approved') {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, ''); // kosongkan nama
                continue;
            }

            // === Path tanda tangan ===
            $signaturePath = public_path("assets/images/ttd/{$approval['approver_id']}.png");
            $dummyPath = public_path('assets/images/ttd/my ttd.jpg');
            $finalPath = file_exists($signaturePath) ? $signaturePath : $dummyPath;

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
                $drawing->setHeight(70);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX(30);
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

        return $spreadsheet;
    }

    private function _fillJangkaSorong(Spreadsheet $spreadsheet, $approvals, $kalibrasi, $alat)
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

        $totalTitik = count($kalibrasi->jangkaSorongSummary);

        foreach ($kalibrasi->jangkaSorongSummary as $tg) {

            // Isi data ke Excel
            $sheet->setCellValue("D{$row}", $tg->master->nilai_master ?? '');
            $sheet->setCellValue("L{$row}", $tg->avg_pembacaan ?? '');
            $sheet->setCellValue("R{$row}", $tg->master->nilai_master ?? '');
            $sheet->setCellValue("X{$row}", $tg->koreksi ?? '');

            $row++;
        }

        // Tentukan baris terakhir setelah loop
        $endRow = $row - 1;

        // Range merge
        $ketidakPastianNilai = optional($kalibrasi->jangkaSorongFinalSummary->first())->ketidakpastian ?? '';
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

        $baseRow = 63;
        $nameRow = 67;

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
            if ($status !== 'approved') {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, ''); // kosongkan nama
                continue;
            }

            // === Path tanda tangan ===
            $signaturePath = public_path("assets/images/ttd/{$approval['approver_id']}.png");
            $dummyPath = public_path('assets/images/ttd/my ttd.jpg');
            $finalPath = file_exists($signaturePath) ? $signaturePath : $dummyPath;

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
                $drawing->setHeight(70);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX(30);
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

        return $spreadsheet;
    }

    private function _fillTimbangan(Spreadsheet $spreadsheet, $approvals, $kalibrasi, $alat)
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
        if (!empty($kalibrasi->pembacaanSummary) && count($kalibrasi->pembacaanSummary) >= 3) {
            $summaryList = $kalibrasi->pembacaanSummary;

            $cellMap = [
                0 => 27, // Mendekati nol
                1 => 28, // Setengah kapasitas
                2 => 29, // Kapasitas maksimum
            ];

            foreach ($summaryList as $index => $summary) {
                if (isset($cellMap[$index])) {
                    $row = $cellMap[$index];

                    $beban       = $summary->beban       ?? 0;
                    $stdDev      = $summary->std_dev     ?? 0;
                    $perbedaan   = $summary->maks_perbedaan_akhir   ?? 0;

                    $sheet->setCellValue("N{$row}", $beban);
                    $sheet->setCellValue("S{$row}", $stdDev);
                    $sheet->setCellValue("X{$row}", $perbedaan);
                }
            }
        } else {
            foreach ([27, 28, 29] as $row) {
                $sheet->setCellValue("N{$row}", '-');
                $sheet->setCellValue("S{$row}", '-');
                $sheet->setCellValue("X{$row}", '-');
            }
        }

        // Data Keseragaman Skala
        if (!empty($kalibrasi->keseragamanSummary)) {
            $keseragamanList = $kalibrasi->keseragamanSummary;

            $startRow = 37; // baris awal untuk keseragaman
            foreach ($keseragamanList as $i => $data) {
                $row = $startRow + $i;
                $beban    = $data->beban ?? '-';
                $koreksi  = $data->koreksi_skala ?? '-';

                $sheet->setCellValue("C{$row}", $beban);
                $sheet->setCellValue("I{$row}", $koreksi);
            }
        } else {
            Log::info('Tidak ada data keseragaman skala untuk kalibrasi ID: ' . ($kalibrasi->id ?? '-'));
        }

        // Data Pengaruh Pada Pinggan
        if (!empty($kalibrasi->pingganSummary)) {
            $percobaan1 = collect($kalibrasi->pingganSummary)->firstWhere('percobaan', 1);

            if ($percobaan1) {
                $row = 40; // baris awal pengisian pinggan

                $sheet->setCellValue("R{$row}", $percobaan1->smry_tengah ?? '-');
                $sheet->setCellValue("U{$row}", $percobaan1->smry_depan ?? '-');
                $sheet->setCellValue("X{$row}", $percobaan1->smry_belakang ?? '-');
                $sheet->setCellValue("AA{$row}", $percobaan1->smry_kiri ?? '-');
                $sheet->setCellValue("AD{$row}", $percobaan1->smry_kanan ?? '-');
                $sheet->setCellValue("AG{$row}", $percobaan1->selisih_maks ?? '-');
            } else {
                Log::info('Percobaan ke-1 tidak ditemukan untuk kalibrasi ID: ' . ($kalibrasi->id ?? '-'));
            }
        }

        // Data Pengnolan Beban (Tare)
        if (!empty($kalibrasi->tareSummary)) {
            $tare = $kalibrasi->tareSummary;

            $sheet->setCellValue('V48', $tare->selisih_mz_tanpa_nol ?? '-');
            $sheet->setCellValue('V49', $tare->selisih_mz_dengan_nol ?? '-');
        }

        // Histerisis
        if (!empty($kalibrasi->histerisisSummary)) {
            $tare = $kalibrasi->histerisisSummary;

            $sheet->setCellValue('AD48', $tare->setengah_kapasitas ?? '-');
            $sheet->setCellValue('AG48', $tare->histerisis ?? '-');
        }

        // Ketidak Pastian

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
            if ($status !== 'approved') {
                Log::info("Belum approve: {$approval['approver_name']} (status={$status})");
                $sheet->mergeCells($mergeRange);
                $sheet->mergeCells($nameRange);
                $sheet->setCellValue($col . $nameRow, ''); // kosongkan nama
                continue;
            }

            // === Path tanda tangan ===
            $signaturePath = public_path("assets/images/ttd/{$approval['approver_id']}.png");
            $dummyPath = public_path('assets/images/ttd/my ttd.jpg');
            $finalPath = file_exists($signaturePath) ? $signaturePath : $dummyPath;

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
                $drawing->setHeight(70);
                $drawing->setCoordinates($startColLetter . $baseRow);
                $drawing->setOffsetX(30);
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

        return $spreadsheet;
    }
}
