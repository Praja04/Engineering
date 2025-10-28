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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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
                        'approver_name' => optional($a->approver)->username ?? '-',
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
                    $this->_fillVolumetrik($spreadsheet, $sertifikat, $kalibrasi, $alat);
                    break;

                case 'timbangan':
                    $kalibrasi->load(['timbangan']);
                    $this->_fillTimbangan($spreadsheet, $sertifikat, $kalibrasi, $alat);
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
        $columns = ['C', 'H', 'M', 'S'];

        foreach ($approvals as $i => $approval) {
            if (!isset($columns[$i])) break; // Maks 4 kolom

            $col = $columns[$i];
            $mergeRange = "{$col}{$baseRow}:" . chr(ord($col) + 4) . "{$baseRow}";
            $nameRange  = "{$col}{$nameRow}:" . chr(ord($col) + 4) . "{$nameRow}";

            // Cek gambar ttd individu
            $signaturePath = public_path("assets/images/ttd/{$approval['approver_id']}.png");
            $dummyPath = public_path('assets/images/ttd/my ttd.jpg');
            $finalPath = file_exists($signaturePath) ? $signaturePath : $dummyPath;

            // Merge area untuk area tanda tangan
            $sheet->mergeCells($mergeRange);
            $sheet->getStyle($mergeRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            if (file_exists($finalPath)) {
                try {
                    // Hitung posisi tengah dari range merge
                    $rangeBounds = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($mergeRange);
                    $startCol = $rangeBounds[0][0];
                    $endCol   = $rangeBounds[1][0];

                    // Konversi ke huruf kolom
                    $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
                    $endColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

                    // Hitung total lebar kolom yang di-merge
                    $totalWidth = 0;
                    for ($j = $startCol; $j <= $endCol; $j++) {
                        $totalWidth += $sheet->getColumnDimensionByColumn($j)->getWidth();
                    }

                    // Hitung offset X agar gambar di tengah
                    $offsetX = ($totalWidth * 6.2 / 2) - 25;
                    if ($offsetX < 0) $offsetX = 0;

                    // Tambahkan gambar
                    $drawing = new Drawing();
                    $drawing->setPath($finalPath);
                    $drawing->setHeight(70);
                    $drawing->setCoordinates($startColLetter . $baseRow);
                    $drawing->setOffsetX($offsetX);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                } catch (\Throwable $th) {
                    Log::warning('Gagal memuat TTD: ' . $th->getMessage());
                }
            }

            // Nama approver di bawah TTD
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue($col . $nameRow, '( ' . ($approval['approver_name'] ?? '-') . ' )');
            $sheet->getStyle($nameRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        $rowEnd = $row - 1;

        if ($totalTitik > 0) {
            $chartCollection = $sheet->getChartCollection();
            if ($chartCollection->count() > 0) {
                foreach ($chartCollection as $chart) {

                    $plotArea = $chart->getPlotArea();
                    $series = is_object($plotArea) && method_exists($plotArea, 'getPlotSeries')
                        ? $plotArea->getPlotSeries()
                        : [];

                    if (empty($series)) {
                        continue;
                    }

                    // -----------------------------------------------------
                    // Tentukan Range Data Dinamis
                    // -----------------------------------------------------

                    // Kategori (Sumbu X): Titik Kalibrasi (Kolom H)
                    $categoryRange = 'Sertifikat!$H$' . $rowStart . ':$H$' . $rowEnd;

                    // Nilai (Sumbu Y)
                    // Asumsi urutan series di template Anda: L, O, R, U
                    $valueRanges = [
                        'Sertifikat!$L$' . $rowStart . ':$L$' . $rowEnd, // Pembacaan Alat Naik
                        'Sertifikat!$O$' . $rowStart . ':$O$' . $rowEnd, // Pembacaan Alat Turun
                        'Sertifikat!$R$' . $rowStart . ':$R$' . $rowEnd, // Pembacaan Standar Naik
                        'Sertifikat!$U$' . $rowStart . ':$U$' . $rowEnd, // Pembacaan Standar Turun
                        // ... tambahkan range lain jika ada series lain
                    ];

                    // -----------------------------------------------------
                    // Proses Pembaruan Series
                    // -----------------------------------------------------

                    // Hanya ambil series sebanyak data range yang kita siapkan
                    $seriesToUpdate = array_slice($series, 0, count($valueRanges));

                    foreach ($seriesToUpdate as $seriesIndex => $seriesItem) {

                        // --- 1. Perbarui Kategori (X-Axis) ---
                        $categories = $seriesItem->getCategories();
                        if (!empty($categories) && $categories[0]) {
                            $categories[0] = new DataSeriesValues(
                                'String',
                                $categoryRange,
                                NULL,
                                $totalTitik
                            );
                            $seriesItem->setCategories($categories);
                        }

                        // --- 2. Perbarui Nilai (Y-Axis) ---
                        $values = $seriesItem->getValues();
                        if (!empty($values) && isset($valueRanges[$seriesIndex])) {
                            // Perbarui value[0] untuk series saat ini
                            $values[0] = new DataSeriesValues(
                                'Number',
                                $valueRanges[$seriesIndex], // Ambil range dari array yang disiapkan
                                NULL,
                                $totalTitik
                            );
                            $seriesItem->setValues($values);
                        }
                    }
                }
            }
        }

        return $spreadsheet;
    }

    private function _fillVolumetrik(Spreadsheet $spreadsheet, $sertifikat, $kalibrasi)
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', $kalibrasi->alat->kode_alat ?? '-');
        $sheet->setCellValue('B3', $kalibrasi->alat->nama_alat ?? '-');
        $sheet->setCellValue('B4', $kalibrasi->tgl_kalibrasi ?? '-');

        $row = 8;
        foreach ($kalibrasi->volumetrik as $v) {
            $sheet->setCellValue("A{$row}", $v->volume_terukur ?? '-');
            $sheet->setCellValue("B{$row}", $v->error ?? '-');
            $row++;
        }
    }

    private function _fillTimbangan(Spreadsheet $spreadsheet, $sertifikat, $kalibrasi)
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', $kalibrasi->alat->kode_alat ?? '-');
        $sheet->setCellValue('B3', $kalibrasi->alat->nama_alat ?? '-');
        $sheet->setCellValue('B4', $kalibrasi->tgl_kalibrasi ?? '-');

        $row = 8;
        foreach ($kalibrasi->timbangan as $t) {
            $sheet->setCellValue("A{$row}", $t->beban ?? '-');
            $sheet->setCellValue("B{$row}", $t->penunjukan ?? '-');
            $sheet->setCellValue("C{$row}", $t->kesalahan ?? '-');
            $row++;
        }
    }
}
