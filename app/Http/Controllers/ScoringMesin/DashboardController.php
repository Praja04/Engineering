<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Return view dashboard scoring
    public function dashboard_scoring_retail()
    {
        return view('dashboard.dashboard_mesin_retail');
    }

    // Overview All 20 Machines
    public function dashboard_downtime_all()
    {
        return view('dashboard.dashboard_downtime_all');
    }

    // Detail Machine Downtime & OEE
    public function dashboard_downtime_detail($machine = 'D1')
    {
        $machine = strtoupper($machine);
        return view('dashboard.dashboard_dowtime_retail', compact('machine'));
    }

    // Legacy fallback route
    public function dashboard_downtime()
    {
        return view('dashboard.dashboard_downtime_all');
    }
    // public function master_mesin()
    // {
    //     return view('scoringmesin.master_mesin');
    // }
}
