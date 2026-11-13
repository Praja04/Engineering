<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WwtpRecord extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'kategori',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Relasi Influent (1 : 1 atau 1 : N sesuai kebutuhan)
    public function influent()
    {
        return $this->hasOne(WwtpInfluent::class);
    }

    // Relasi Effluent
    public function effluent()
    {
        return $this->hasOne(WwtpEffluent::class);
    }
}
