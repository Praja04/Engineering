<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalArea extends Model
{
    use HasFactory;

    protected $table = 'chemical_areas';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_area', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function types()
    {
        return $this->hasMany(ChemicalType::class, 'chemical_area_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('nama_area');
    }
}
