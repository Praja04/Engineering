<?php

namespace App\Http\Controllers\Kalibrasi;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\RequestApprovalMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
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
    public function store(Request $request, $id)
    {
        $request->validate([
            'manager_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
            'foreman_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $sertifikat = KalibrasiSertifikatModel::findOrFail($id);

        // Mulai transaction supaya aman
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

                    KalibrasiSertifikatApprovalModel::create([
                        'sertifikat_id' => $sertifikat->id,
                        'approver_id' => $user->id,
                        'approver_email' => $user->email,
                        'status' => 'pending'
                    ]);

                    Mail::to($user->email)->later(now()->addSeconds(5), new RequestApprovalMail($sertifikat, $user->username));
                }
            }

            // update status sertifikat jadi pending
            $sertifikat->update(['status' => 'pending']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Request approval berhasil dikirim ke approver.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim request approval: ' . $e->getMessage()
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

    // public function showApprovals(string $id)
    // {
    //     $sertifikat = KalibrasiSertifikatModel::with(['kalibrasi', 'user'])->findOrFail($id);

    //     return view('kalibrasi.certificate.approval', compact('sertifikat'));
    // }

    public function getSertifikatData($id = null)
    {
        // Jika ada ID, ambil satu objek. Jika tidak, ambil koleksi.
        try {
            if ($id) {
                $sertifikat = KalibrasiSertifikatModel::with(['kalibrasi.alat', 'user'])->findOrFail($id);
                return response()->json([
                    'status' => 'success',
                    'data'   => $sertifikat
                ]);
            }

            // Jika tidak ada ID, kembalikan koleksi (daftar)
            $sertifikat = KalibrasiSertifikatModel::with(['kalibrasi.alat', 'user'])->get();

            return response()->json([
                'status' => 'success',
                'data'   => $sertifikat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getDataCertificate()
    {
        try {
            $data = KalibrasiModel::selectRaw('id,alat_id,lokasi_kalibrasi,tgl_kalibrasi,tgl_kalibrasi_ulang,jenis_kalibrasi')
                ->with(
                    'alat:id,kode_alat,nama_alat',
                    'certificate:id,kalibrasi_id,status'
                )
                ->get();

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

    public function getUserApprovals()
    {
        try {
            $data = User::selectRaw('id,username,email,jabatan,nik,bagian')
                ->get();

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

    public function approve(Request $request, $id)
    {
        $sertifikat = KalibrasiSertifikatModel::findOrFail($id);

        // update status approval di table certificate_approval
        KalibrasiSertifikatApprovalModel::where('sertifikat_id', $sertifikat->id)
            ->where('approver_id', Auth::id())
            ->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

        // jika semua approver sudah approve, update status sertifikat jadi approved
        $pending = KalibrasiSertifikatApprovalModel::where('sertifikat_id', $sertifikat->id)
            ->whereNull('approved_at')
            ->exists();

        if (!$pending) {
            $sertifikat->status = 'approved';
            $sertifikat->save();
        }

        return redirect()->route('approvals.show', $sertifikat->id)
            ->with('success', 'Sertifikat berhasil di-approve');
    }

    public function reject($id)
    {
        $approval = KalibrasiSertifikatApprovalModel::findOrFail($id);

        $approval->update([
            'status' => 'rejected',
            'approved_at' => now(),
        ]);

        // Kalau ada yang reject → sertifikat otomatis rejected
        $approval->sertifikat->update(['status' => 'rejected']);

        return response()->json(['status' => 'success', 'message' => 'Approval ditolak']);
    }
}
