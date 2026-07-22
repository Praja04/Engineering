<?php

namespace App\Models\Epr;

use Illuminate\Database\Eloquent\Model;

class PredictiveMaintenancePhoto extends Model
{
    protected $table = 'epr_predictive_maintenance_photos';

    protected $fillable = [
        'predictive_maintenance_id',
        'photo_path'
    ];

    public function predictiveMaintenance()
    {
        return $this->belongsTo(PredictiveMaintenance::class, 'predictive_maintenance_id');
    }
}
