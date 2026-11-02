<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngUtilityProjectArea extends Model
{
    use HasFactory;

    protected $table = 'eng_utility_project_areas';

    protected $fillable = [
        'nama_area',
        'kode_area',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function projects()
    {
        return $this->hasMany(EngUtilityProject::class, 'area', 'nama_area');
    }
}
