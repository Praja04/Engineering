<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterListrikPanel extends Model
{
    use HasFactory;

    protected $table = 'master_listrik_panels';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_panel',
        'deskripsi',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Scope for active panels ordered
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan')->orderBy('nama_panel');
    }
}
