<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngUtilityProjectLog extends Model
{
    use HasFactory;

    protected $table = 'eng_utility_project_logs';

    protected $fillable = [
        'project_id',
        'progress_sebelumnya',
        'progress_baru',
        'catatan',
        'tanggal',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date:d-m-Y',
        'progress_sebelumnya' => 'integer',
        'progress_baru' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(EngUtilityProject::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
