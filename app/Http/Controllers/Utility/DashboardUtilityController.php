<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function utility()
    {
        return view('dashboard.utility.dashboard_utility');
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
        $wwtpArea = \App\Models\Utility\ChemicalArea::where('nama_area', 'like', '%wwtp%')->first();
        $utilityArea = \App\Models\Utility\ChemicalArea::where('nama_area', 'like', '%utility%')->first();

        $wwtpChemicals = \App\Models\Utility\ChemicalType::when($wwtpArea, fn($q) => $q->where('chemical_area_id', $wwtpArea->id))
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        if ($wwtpChemicals->isEmpty()) {
            $wwtpChemicals = \App\Models\Utility\ChemicalType::where('is_active', true)
                ->whereIn('nama_chemical', [
                    'PAC powder 1', 'PAC powder 2', 'BE-100', 'C-204', 'C-9040 step 1',
                    'C-9040 step 2', 'Denfloc 260 PA', 'Denfloc 945', 'NaOH', 'Defoamer', 'NPK'
                ])
                ->orderBy('id', 'asc')
                ->get();
        }

        $utilityChemicals = \App\Models\Utility\ChemicalType::when($utilityArea, fn($q) => $q->where('chemical_area_id', $utilityArea->id))
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        return view('dashboard.utility.chemical', compact('wwtpChemicals', 'utilityChemicals'));
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
    public function wwtp_wco()
    {
        $defaultStartDate = Carbon::today()->startOfMonth()->toDateString();
        $defaultEndDate   = Carbon::today()->endOfMonth()->toDateString();

        return view('dashboard.wwtp.dashboard_wco', compact('defaultStartDate', 'defaultEndDate'));
    }
}

