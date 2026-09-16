<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MtcMasterMaterialModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_master_material';

    protected $fillable = [
        'mid',
        'deskripsi',
        'uom',
        'harga',
        'kategori',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'harga' => 'double',
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
