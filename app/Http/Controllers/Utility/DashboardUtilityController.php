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
    public function wwtp()
    {
        return view('dashboard.wwtp.dashboard');
    }
}
