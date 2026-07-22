<?php

namespace App\Models\Epr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmMachineKpi extends Model
{
    use HasFactory;

    protected $table = 'epr_cm_machine_kpis';

    protected $fillable = [
        'month',
        'mesin',
        'availability_pct',
        'performance_pct',
        'quality_pct',
        'oee_pct',
        'pm_compliance_pct',
        'repeat_failure_pct',
        'minor_stop_freq',
        'cost_per_hour',
        'energy_per_pack',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
