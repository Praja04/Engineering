<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcMasterMesinModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_master_mesin';

    protected $fillable = [
        'jenis_mtc',
        'nama_mesin',
        'lokasi',
        'dept',
        'kode_mesin',
        'aktif',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function frekuensi()
    {
        return $this->hasMany(MtcMesinFrekuensiModel::class, 'mesin_id')
        ->orderBy('satuan')
        ->orderBy('interval');
    }

    /**
     * Mengambil map ID mesin yang status terakhirnya adalah 'Off'
     *
     * @param string $jenisMtc
     * @return array<int, array{main_id: int, tanggal: string, paket: string}>
     */
    public static function getCurrentlyOffMachineMap(string $jenisMtc): array
    {
        $tableMap = [
            'Motor Pompa'     => 'mtc_motor_pump_inspections',
            'Utility'         => 'mtc_utility_inspections',
            'Electrical'      => 'mtc_electrical_inspections',
            'Refrigerasi'     => 'mtc_refrigerasi_inspections',
            'Electric Engine' => 'mtc_electric_engine_inspections',
            'Diesel Engine'   => 'mtc_diesel_engine_inspections',
        ];

        $tbl = $tableMap[$jenisMtc] ?? null;
        if (!$tbl) {
            return [];
        }

        $latestMtcPerMesin = \Illuminate\Support\Facades\DB::table($tbl . ' as d')
            ->join('mtc_main as m', 'm.id', '=', 'd.mtc_main_id')
            ->select('d.mesin_id', 'm.id as main_id', 'm.paket', 'm.tanggal')
            ->whereIn('m.id', function ($query) use ($tbl) {
                $query->select(\Illuminate\Support\Facades\DB::raw('MAX(m2.id)'))
                    ->from($tbl . ' as d2')
                    ->join('mtc_main as m2', 'm2.id', '=', 'd2.mtc_main_id')
                    ->groupBy('d2.mesin_id');
            })
            ->get();

        $offMap = [];
        foreach ($latestMtcPerMesin as $row) {
            if (strtolower(trim($row->paket ?? '')) === 'off') {
                $offMap[$row->mesin_id] = [
                    'main_id' => $row->main_id,
                    'tanggal' => $row->tanggal,
                    'paket'   => $row->paket,
                ];
            }
        }

        return $offMap;
    }
}
