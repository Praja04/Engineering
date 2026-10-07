<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirArea extends Model
{
    use HasFactory;

    protected $table = 'air_area_utility';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_area', 'is_active', 'deskripsi'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('nama_area');
    }
}
