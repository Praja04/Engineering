<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpInfluentHarian extends Model
{
    //
    use HasFactory;

    protected $table = 'wwtp_influent_harian';

    protected $fillable = [
        'tanggal',
        'shift',
        'pit_sparta',
        'pit_garam',
        'pit_domestik',
        'pit_produksi_step3',
        'pit_storage',
        'pit_proses_wwtp2',
        'pit_outlet',
        'pit_boiler',
        'debit1',
        'running_wwtp1',
        'debit2',
        'running_wwtp2',
    ];
}
