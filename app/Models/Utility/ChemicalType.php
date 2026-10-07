<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalType extends Model
{
    use HasFactory;

    protected $table = 'chemical_types';
    protected $primaryKey = 'id';
    protected $fillable = ['chemical_area_id', 'nama_chemical', 'satuan', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function area()
    {
        return $this->belongsTo(ChemicalArea::class, 'chemical_area_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('nama_chemical');
    }
}
