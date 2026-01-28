<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcElectricP2hInspectionModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_electric_p2h_inspections';

    protected $fillable = [
        'tanggal',
        'no_unit',
        'departemen',
        'shift',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    public function details()
    {
        return $this->hasMany(MtcElectricP2hInspectionDetailModel::class, 'inspection_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
