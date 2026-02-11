<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcMasterMesinModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_master_mesin';

    protected $fillable = [
        'jenis_mtc',
        'nama_mesin',
        'lokasi',
        'frekuensi',
        'aktif',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
