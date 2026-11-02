<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class WwtpPitProduksiHarian extends Model
{
    use HasFactory;

    protected $table = 'wwtp_pit_produksi_harian';

    protected $fillable = [
        'tanggal',
        'nilai_pit_produksi',
        'catatan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'nilai_pit_produksi' => 'double',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
