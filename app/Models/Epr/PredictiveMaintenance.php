<?php

namespace App\Models\Epr;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PredictiveMaintenance extends Model
{
    protected $table = 'epr_predictive_maintenances';

    protected $fillable = [
        'date',
        'tech_name',
        'area',
        'wo_ref',
        'work_order_id',
        'work_description',
        'time_start',
        'time_end',
        'status',
        'notes',
        'is_adhoc',
        'adhoc_title',
        'parent_id',
        'created_by'
    ];

    public function photos()
    {
        return $this->hasMany(PredictiveMaintenancePhoto::class, 'predictive_maintenance_id');
    }

    public function parent()
    {
        return $this->belongsTo(PredictiveMaintenance::class, 'parent_id');
    }

    public function updates()
    {
        return $this->hasMany(PredictiveMaintenance::class, 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
