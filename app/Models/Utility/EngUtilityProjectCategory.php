<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngUtilityProjectCategory extends Model
{
    use HasFactory;

    protected $table = 'eng_utility_project_categories';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function projects()
    {
        return $this->hasMany(EngUtilityProject::class, 'kategori', 'nama_kategori');
    }
}
