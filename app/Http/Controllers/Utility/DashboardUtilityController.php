<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardUtilityController extends Controller
{
    public function getApprovers()
    {
        $staff = User::where('departemen', 'engineering')
            ->where('jabatan', 'foreman')
            ->where(function ($q) {
                $q->where('bagian', 'Engineering WWTP')
                    ->orWhere('bagian', 'Engineering');
            })
            ->get(['id', 'username']);

        $spv = User::where('jabatan', 'supervisor')
            ->where(function ($q) {
                $q->where('departemen', 'engineering');
            })
            ->get(['id', 'username']);

        $user = User::where('jabatan', 'supervisor')
            ->Where('departemen', 'engineering')
            ->get(['id', 'username', 'departemen']);

        return response()->json([
            'staff' => $staff,
            'supervisor' => $spv,
            'user'  => $user,
        ]);
    }

    //
    public function utility()
    {
        return view('dashboard.utility.dashboard');
    }
    public function listrik()
    {
        return view('dashboard.utility.listrik');
    }
    public function air()
    {
        return view('dashboard.utility.air');
    }
    public function chemical()
    {
        return view('dashboard.utility.chemical');
    }
    public function wwtp_proses()
    {
        return view('dashboard.wwtp.dashboard_proses');
    }
    public function wwtp_performance()
    {
        return view('dashboard.wwtp.dashboard_performance');
    }
    public function wwtp_sludge()
    {
        return view('dashboard.wwtp.dashboard_sludge');
    }
    public function wwtp_visualisasi()
    {
        return view('dashboard.wwtp.dashboard_visualisasi');
    }
}
