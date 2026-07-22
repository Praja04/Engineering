<?php

namespace App\Models\Epr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmCost extends Model
{
    use HasFactory;

    protected $table = 'epr_cm_costs';

    protected $fillable = [
        'corrective_maintenance_id',
        'mesin',
        'tanggal',
        'kategori_biaya',
        'deskripsi',
        'jumlah_biaya',
        'created_by'
    ];

    public function correctiveMaintenance()
    {
        return $this->belongsTo(CorrectiveMaintenance::class, 'corrective_maintenance_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
