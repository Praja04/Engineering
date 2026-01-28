<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcSipilInspectionModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_sipil_inspections';

    protected $fillable = [
        'tanggal',
        'waktu',
        'area',
        'rekomendasi',
        'korektif',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'waktu'   => 'datetime:H:i:s',
    ];

    public function details()
    {
        return $this->hasMany(MtcSipilInspectionDetailModel::class, 'inspection_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
