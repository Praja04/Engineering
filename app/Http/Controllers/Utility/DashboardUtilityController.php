<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardUtilityController extends Controller
{
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
}
