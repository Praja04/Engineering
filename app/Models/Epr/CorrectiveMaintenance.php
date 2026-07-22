<?php

namespace App\Models\Epr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CorrectiveMaintenance extends Model
{
    use HasFactory;

    protected $table = 'epr_corrective_maintenances';

    protected $fillable = [
        'tanggal',
        'shift',
        'grup',
        'mesin',
        'pouch_sachet',
        'jam_mulai',
        'jam_selesai',
        'total_menit',
        'keterangan',
        'downtime',
        'jenis_dt_id',
        'am_pm',
        'electrical_mechanical',
        'created_by'
    ];

    public function jenisDt()
    {
        return $this->belongsTo(JenisDt::class, 'jenis_dt_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
