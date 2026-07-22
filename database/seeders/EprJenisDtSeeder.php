<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Epr\JenisDt;

class EprJenisDtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            'Finger',
            'Electrical (Sensor)',
            'Take Up',
            'Nozzle',
            'Coding',
            'Open Vacuum',
            'Drive',
            'Electrical (Motor)',
            'Open Checker',
            'Conveyor',
            'Magazine',
            'Nozzle Air',
            'Electrical (Cable)',
            'Sealing',
            'Crank In',
            'Pouch Supply',
            'Running 1',
            'Electrical (Encoder)',
            'End Cutter',
            'Electrical (Element)',
            'Cam Track',
            'Electrical (Fan)',
            'Piercing / Pierching',
            'Inner',
            'Electrical (Control)',
            'Electrical (MCB)',
            'Rotary',
            'Cooling',
            'Electrical (Nozzle)',
            'Electrical (Relay)',
            'Pompa Lubricant',
            'Pompa Motor',
            'Electrical (Magnet)',
            'Electrical (PLC)',
            'Carton Sealer',
            'Electrical (Alarm)',
            'Electrical (Overload)'
        ];

        foreach ($items as $item) {
            JenisDt::updateOrCreate(
                ['name' => $item],
                ['aktif' => true]
            );
        }
    }
}
