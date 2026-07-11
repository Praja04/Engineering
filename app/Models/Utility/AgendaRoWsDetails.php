<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaRoWsDetails extends Model
{
    use HasFactory;

    protected $table = 'agenda_ro_ws_details';

    protected $fillable = [
        'agenda_ro_ws_id',
        'tanggal',
        'inspeksi_hpt_pump',
        'inspeksi_cip_pump',
        'inspeksi_blower_ro',
        'cek_chemical',
        'pencatatan_flow_meter_produksi',
        'cek_nilai_conductivity',
        'cek_dp_1st_2st',
        'cek_dp_mmf_1_2',
        'pencatatan_flow_meter_konsumsi',
        'backwash_mmf_1',
        'backwash_mmf_2',
        'cek_kondisi_rotameter_mmf_1',
        'cek_kondisi_rotameter_mmf_2',
        'cek_kondisi_rotameter_ro_product',
        'cek_kondisi_rotameter_ro_reject',
        'kalibrasi_dosis_kimia',
        'cleaning_unit_ro',
        'cleaning_unit_mmf_1',
        'cleaning_unit_mmf_2',
        'cek_output_hardness',
        'cek_flow_produk',
        'regenerasi_mesin_ws',
        'cek_pompa_transfer',
        'cek_pompa_suplai',
        'cleaning_tanki_buffer_ws',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function agendaRoWs()
    {
        return $this->belongsTo(AgendaRoWs::class, 'agenda_ro_ws_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
