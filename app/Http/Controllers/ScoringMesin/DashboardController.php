<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //return view dashboard
    public function dashboard_scoring_retail()
    {
        return view('dashboard.dashboard_mesin_retail');
    }
    public function dashboard_downtime()
    {
        return view('dashboard.dashboard_dowtime_retail');
    }
    // public function master_mesin()
    // {
    //     return view('scoringmesin.master_mesin');
    // }
}
