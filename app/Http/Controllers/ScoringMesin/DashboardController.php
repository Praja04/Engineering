<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //return view dashboard
    public function index()
    {
        return view('scoring_mesin.dashboard');
    }

    // public function master_mesin()
    // {
    //     return view('scoringmesin.master_mesin');
    // }
}
