<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maintenance\MtcMasterChecklistGroup;
use App\Models\Maintenance\MtcMasterChecklist;
use Illuminate\Support\Facades\DB;

class MtcMasterChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Motor Pompa' => [
                'Motor' => [
                    ['field' => 'electrical_motor', 'label' => 'Kondisi Electrical'],
                    ['field' => 'putaran_motor', 'label' => 'Putaran Motor'],
                    ['field' => 'fibrasi_suara_motor', 'label' => 'Fibrasi dan Suara Motor'],
                    ['field' => 'bearing_motor', 'label' => 'Bearing Motor'],
                    ['field' => 'pelumasan_motor', 'label' => 'Pelumasan Motor'],
                    ['field' => 'kebersihan_unit_body_motor', 'label' => 'Kebersihan Unit Body Motor'],
                ],
                'Pompa' => [
                    ['field' => 'putaran_pompa', 'label' => 'Putaran Pompa'],
                    ['field' => 'shaft_karet_coupling_pompa', 'label' => 'Shaft & Karet Coupling'],
                    ['field' => 'fan_belt_pompa', 'label' => 'Fan Belt'],
                    ['field' => 'pressure_pompa', 'label' => 'Pressure Pompa'],
                    ['field' => 'mechanical_seal_pompa', 'label' => 'Mechanical Seal'],
                    ['field' => 'gasket_pompa', 'label' => 'Gasket Pompa'],
                    ['field' => 'impeler', 'label' => 'Kondisi Impeller'],
                    ['field' => 'kebersihan_unit_body_pompa', 'label' => 'Kebersihan Unit & Body Pompa'],
                ],
                'Aksesoris' => [
                    ['field' => 'valve_aksesoris', 'label' => 'Valve'],
                    ['field' => 'cek_valve_aksesoris', 'label' => 'Cek Valve'],
                    ['field' => 'flow_meter_aksesoris', 'label' => 'Flow Meter'],
                    ['field' => 'strainer_aksesoris', 'label' => 'Strainer / Saringan'],
                    ['field' => 'alat_ukur_aksesoris', 'label' => 'Alat Ukur'],
                    ['field' => 'kelengkapan_baut_mur_aksesoris', 'label' => 'Kelengkapan Baut & Mur'],
                ],
                'Gearbox' => [
                    ['field' => 'tambah_ganti_oli_gearbox', 'label' => 'Penambahan / Penggantian Oli Gearbox'],
                    ['field' => 'unit_area_gearbox', 'label' => 'Unit & Area Gearbox'],
                    ['field' => 'oil_seal_gearbox', 'label' => 'Oil Seal Gearbox'],
                    ['field' => 'filter_udara_gearbox', 'label' => 'Filter Udara Gearbox'],
                    ['field' => 'bearing_gearbox', 'label' => 'Bearing Gearbox'],
                ],
            ],

            'Utility' => [
                'Cooling Tower' => [
                    ['field' => 'cleaning_saringan_cooling_tower', 'label' => 'Cleaning Saringan Bak Cooling Tower'],
                    ['field' => 'cleaning_unit_cooling_tower', 'label' => 'Cleaning Unit Cooling Tower'],
                    ['field' => 'cleaning_bak_cooling_tower', 'label' => 'Cleaning Bak Cooling Tower'],
                ],
                'Reverse Osmosis (RO)' => [
                    ['field' => 'check_sensor_tank_farm_ro_produk', 'label' => 'Check Sensor Tank Farm RO Produk'],
                    ['field' => 'cleaning_flow_rate_mmf_1', 'label' => 'Cleaning Flow Rate MMF #1'],
                    ['field' => 'cleaning_flow_rate_mmf_2', 'label' => 'Cleaning Flow Rate MMF #2'],
                    ['field' => 'cleaning_flow_rate_ro_produk', 'label' => 'Cleaning Flow Rate RO Produk'],
                    ['field' => 'cleaning_flow_rate_ro_reject', 'label' => 'Cleaning Flow Rate RO Reject'],
                    ['field' => 'penggantian_micron_filter_cip', 'label' => 'Penggantian Micron Filter CIP'],
                    ['field' => 'penggantian_micron_filter_makeup_water', 'label' => 'Penggantian Micron Filter Make Up Water'],
                    ['field' => 'cleaning_cip_tank', 'label' => 'Cleaning CIP Tank'],
                    ['field' => 'cip_membrane_reverse_osmosis', 'label' => 'CIP Membrane Reverse Osmosis'],
                    ['field' => 'check_fungsi_valve', 'label' => 'Check Fungsi Valve'],
                    ['field' => 'cleaning_unit_ro_mesin', 'label' => 'Cleaning Unit RO Mesin'],
                ],
                'Compressor' => [
                    ['field' => 'sirkulasi_phe_aq55vsd', 'label' => 'Sirkulasi PHE AQ55VSD'],
                    ['field' => 'penggantian_air_ro_aq55vsd', 'label' => 'Penggantian Air RO AQ55VSD'],
                    ['field' => 'cleaning_compressor_aq55vsd', 'label' => 'Cleaning Compressor AQ55VSD'],
                    ['field' => 'cleaning_jalur_cooling_aq55vsd', 'label' => 'Cleaning Jalur Cooling AQ55VSD'],
                    ['field' => 'cleaning_dryer_fd185', 'label' => 'Cleaning Dryer FD185'],
                    ['field' => 'cleaning_compressor_ga37', 'label' => 'Cleaning Compressor GA37'],
                    ['field' => 'cleaning_dryer_fd120', 'label' => 'Cleaning Dryer FD120'],
                    ['field' => 'lubrikasi_motor_compressor_aq55vsd', 'label' => 'Lubrikasi Motor Compressor AQ55VSD'],
                    ['field' => 'cleaning_compressor_sm55', 'label' => 'Cleaning Compressor SM55'],
                ],
                'Tank Farm' => [
                    ['field' => 'cleaning_sensor_level_tank_farm', 'label' => 'Cleaning Sensor Level Tank Farm'],
                    ['field' => 'cleaning_sensor_level_fresh_water_menara', 'label' => 'Cleaning Sensor Level Fresh Water Menara'],
                    ['field' => 'cleaning_sensor_level_ro_reject_menara', 'label' => 'Cleaning Sensor Level RO Reject Menara'],
                    ['field' => 'cleaning_sensor_level_intermediate', 'label' => 'Cleaning Sensor Level Intermediate'],
                ],
                'Boiler' => [
                    ['field' => 'check_safety_valve', 'label' => 'Check Safety Valve'],
                    ['field' => 'cleaning_level_gauge', 'label' => 'Cleaning Level Gauge'],
                    ['field' => 'cleaning_level_transmitter', 'label' => 'Cleaning Level Transmitter'],
                    ['field' => 'check_pressure_transmitter', 'label' => 'Check Pressure Transmitter'],
                    ['field' => 'check_temperature_transmitter', 'label' => 'Check Temperature Transmitter'],
                    ['field' => 'cleaning_sensor_o2_co2', 'label' => 'Cleaning Sensor O2 & CO2'],
                    ['field' => 'check_chaingrate', 'label' => 'Check Chaingrate'],
                    ['field' => 'check_ruang_bakar', 'label' => 'Check Ruang Bakar'],
                    ['field' => 'check_back_chamber', 'label' => 'Check Back Chamber'],
                    ['field' => 'check_guillotine', 'label' => 'Check Guillotine'],
                    ['field' => 'check_wet_ash_conveyor', 'label' => 'Check Wet Ash Conveyor'],
                    ['field' => 'check_bottom_ash_conveyor', 'label' => 'Check Bottom Ash Conveyor'],
                    ['field' => 'check_conveyor_batu_bara', 'label' => 'Check Conveyor Batu Bara'],
                    ['field' => 'check_feeder', 'label' => 'Check Feeder'],
                    ['field' => 'cleaning_bak_wet_ash_conveyor', 'label' => 'Cleaning Bak Wet Ash Conveyor'],
                    ['field' => 'check_feed_tank', 'label' => 'Check Feed Tank'],
                ],
                'WWTP' => [
                    ['field' => 'check_line_limbah', 'label' => 'Check Line Limbah'],
                    ['field' => 'check_line_chemical', 'label' => 'Check Line Chemical'],
                    ['field' => 'check_tangki_kotak', 'label' => 'Check Tangki Kotak'],
                    ['field' => 'check_tangki_bulat', 'label' => 'Check Tangki Bulat'],
                ],
            ],

            'Electrical' => [
                'Panel' => [
                    ['field' => 'check_kunci', 'label' => 'Check Kunci'],
                    ['field' => 'check_koneksi_kabel', 'label' => 'Check Koneksi Kabel'],
                    ['field' => 'check_wiring_panel', 'label' => 'Check Wiring Panel'],
                    ['field' => 'check_lampu_indikator', 'label' => 'Check Lampu Indikator'],
                    ['field' => 'check_name_plate', 'label' => 'Check Name Plate'],
                    ['field' => 'check_unit_electrical', 'label' => 'Check Unit Electrical'],
                    ['field' => 'check_grounding', 'label' => 'Check Grounding'],
                    ['field' => 'check_kebersihan', 'label' => 'Check Kebersihan'],
                    ['field' => 'check_bus_bar', 'label' => 'Check Bus Bar'],
                    ['field' => 'check_nilai_grounding', 'label' => 'Check Nilai Grounding'],
                ],
                'Penerangan' => [
                    ['field' => 'check_kondisi_lampu', 'label' => 'Check Kondisi Lampu'],
                    ['field' => 'check_cover_lampu', 'label' => 'Check Cover Lampu'],
                    ['field' => 'check_wiring_penerangan', 'label' => 'Check Wiring Penerangan'],
                    ['field' => 'check_saklar', 'label' => 'Check Saklar'],
                    ['field' => 'check_penyangga_penerangan', 'label' => 'Check Penyangga Penerangan'],
                ],
                'Sistem Distribusi' => [
                    ['field' => 'check_stecker', 'label' => 'Check Stecker'],
                    ['field' => 'check_stop_kontak', 'label' => 'Check Stop Kontak'],
                    ['field' => 'check_terminal_listrik', 'label' => 'Check Terminal Listrik'],
                    ['field' => 'check_pengabelan_distribusi', 'label' => 'Check Pengkabelan Distribusi'],
                    ['field' => 'check_support_pelindung_distribusi', 'label' => 'Check Support & Pelindung Distribusi'],
                ],
                'Capacitor Bank' => [
                    ['field' => 'check_kondisi_fisik_capacitor', 'label' => 'Check Kondisi Fisik Capacitor'],
                    ['field' => 'check_nilai_farad', 'label' => 'Check Nilai Farad'],
                    ['field' => 'check_nilai_ampere', 'label' => 'Check Nilai Ampere'],
                    ['field' => 'check_kebersihan_capacitor', 'label' => 'Check Kebersihan (Capacitor)'],
                ],
                'Trafo' => [
                    ['field' => 'check_kebocoran_oli_sisi_bawah', 'label' => 'Check Kebocoran Oli Sisi Bawah'],
                    ['field' => 'check_kebocoran_oli_sisi_atas', 'label' => 'Check Kebocoran Oli Sisi Atas'],
                    ['field' => 'check_level_oli', 'label' => 'Check Level Oli'],
                ],
            ],

            'Refrigerasi' => [
                'Unit Indoor' => [
                    ['field' => 'check_filter_udara', 'label' => 'Check Filter Udara'],
                    ['field' => 'check_cover_filter_udara', 'label' => 'Check Cover Filter Udara'],
                    ['field' => 'check_electrical_indoor', 'label' => 'Check Electrical (Indoor)'],
                    ['field' => 'check_suhu_evaporator', 'label' => 'Check Suhu Evaporator'],
                    ['field' => 'check_indikator_display', 'label' => 'Check Indikator Display'],
                    ['field' => 'check_motor_blower', 'label' => 'Check Motor Blower'],
                    ['field' => 'check_fan_belt_blower', 'label' => 'Check Fan Belt Blower'],
                    ['field' => 'check_pelumasan_blower', 'label' => 'Check Pelumasan Blower'],
                    ['field' => 'check_pergerakan_motor_swing', 'label' => 'Check Pergerakan Motor Swing'],
                    ['field' => 'check_kontroler_indoor', 'label' => 'Check Kontroler Indoor'],
                    ['field' => 'check_saluran_drain_kondensasi', 'label' => 'Check Saluran Drain Kondensasi'],
                    ['field' => 'sirkulasi_evaporator', 'label' => 'Sirkulasi Evaporator'],
                ],
                'Unit Outdoor' => [
                    ['field' => 'check_kondisi_kondensor', 'label' => 'Check Kondisi Kondensor'],
                    ['field' => 'check_electrical_outdoor', 'label' => 'Check Electrical (Outdoor)'],
                    ['field' => 'check_motor_fan', 'label' => 'Check Motor Fan'],
                    ['field' => 'check_tekanan_freon', 'label' => 'Check Tekanan Freon'],
                    ['field' => 'pelumasan_motor_fan', 'label' => 'Pelumasan Motor Fan'],
                    ['field' => 'kebersihan_unit_body_outdoor', 'label' => 'Kebersihan Unit & Body Outdoor'],
                ],
                'Jalur Distribusi' => [
                    ['field' => 'check_jalur_freon', 'label' => 'Check Jalur Freon'],
                    ['field' => 'check_jalur_distribusi_udara', 'label' => 'Check Jalur Distribusi Udara'],
                    ['field' => 'check_jalur_return_udara', 'label' => 'Check Jalur Return Udara'],
                    ['field' => 'check_suhu_supply', 'label' => 'Check Suhu Supply', 'type' => 'number'],
                    ['field' => 'check_suhu_return', 'label' => 'Check Suhu Return', 'type' => 'number'],
                    ['field' => 'check_flow_supply', 'label' => 'Check Flow Supply', 'type' => 'number'],
                    ['field' => 'check_flow_return', 'label' => 'Check Flow Return', 'type' => 'number'],
                ],
            ],

            'Electric Engine' => [
                'Forklift Electrical' => [
                    ['field' => 'check_buzzer_back', 'label' => 'Check Buzzer Back'],
                    ['field' => 'check_klakson', 'label' => 'Check Klakson'],
                    ['field' => 'check_pilot_lamp', 'label' => 'Check Pilot Lamp'],
                    ['field' => 'check_lampu_sorot', 'label' => 'Check Lampu Sorot'],
                    ['field' => 'check_lampu_kombinasi_kanan_belakang', 'label' => 'Check Lampu Kombinasi Kanan Belakang'],
                    ['field' => 'check_lampu_kombinasi_kiri_belakang', 'label' => 'Check Lampu Kombinasi Kiri Belakang'],
                    ['field' => 'check_kaca_sepion', 'label' => 'Check Kaca Spion'],
                ],
                'Battery & Charger Electrical' => [
                    ['field' => 'check_battery', 'label' => 'Check Battery'],
                    ['field' => 'check_skun_battery', 'label' => 'Check Skun Battery'],
                    ['field' => 'check_terminal_charger_battery', 'label' => 'Check Terminal Charger Battery'],
                    ['field' => 'check_kunci_kontak', 'label' => 'Check Kunci Kontak'],
                    ['field' => 'check_main_contactor', 'label' => 'Check Main Contactor'],
                    ['field' => 'check_microswitch', 'label' => 'Check Microswitch'],
                    ['field' => 'check_eps_controller', 'label' => 'Check EPS Controller'],
                    ['field' => 'check_steering_motor', 'label' => 'Check Steering Motor (Brush & Commutator)'],
                    ['field' => 'check_fan', 'label' => 'Check Fan'],
                    ['field' => 'check_fuse', 'label' => 'Check Fuse'],
                    ['field' => 'check_display_control', 'label' => 'Check Display Control'],
                    ['field' => 'check_wiring_terminal', 'label' => 'Check Wiring & Terminal'],
                    ['field' => 'check_carbon_brush', 'label' => 'Check Carbon Brush'],
                ],
                'Drive, Steering, Hydraulic & Braking' => [
                    ['field' => 'check_steering_wheel', 'label' => 'Check Steering Wheel'],
                    ['field' => 'check_baut_roda', 'label' => 'Check Baut Roda'],
                    ['field' => 'check_drive_caster_load_wheel', 'label' => 'Check Drive, Caster & Load Wheel'],
                    ['field' => 'check_lift_chain', 'label' => 'Check Lift Chain'],
                    ['field' => 'check_lift_bracket', 'label' => 'Check Lift Bracket'],
                    ['field' => 'check_hydraulic_hose', 'label' => 'Check Hydraulic Hose'],
                    ['field' => 'check_motor_hydraulic_pump', 'label' => 'Check Motor Hydraulic Pump'],
                    ['field' => 'check_fork', 'label' => 'Check Fork'],
                    ['field' => 'check_lift_rollers', 'label' => 'Check Lift Rollers'],
                    ['field' => 'check_mast_rollers', 'label' => 'Check Mast Rollers'],
                    ['field' => 'check_lift_cylinders', 'label' => 'Check Lift Cylinders'],
                    ['field' => 'check_tilt_cylinders', 'label' => 'Check Tilt Cylinders'],
                    ['field' => 'check_control_valve', 'label' => 'Check Control Valve'],
                    ['field' => 'check_hydraulic_tank', 'label' => 'Check Hydraulic Tank'],
                    ['field' => 'check_overhead_guard', 'label' => 'Check Overhead Guard (Pelindung Kemudi)'],
                    ['field' => 'check_all_bolt_nut', 'label' => 'Check All Bolt & Nut'],
                    ['field' => 'check_power_steering', 'label' => 'Check Power Steering'],
                    ['field' => 'check_brake_cam_adjust_bolt', 'label' => 'Check Brake Cam & Adjust Bolt'],
                    ['field' => 'check_axle', 'label' => 'Check Axle'],
                    ['field' => 'check_greasing_point', 'label' => 'Check Greasing Point'],
                    ['field' => 'check_air_spring', 'label' => 'Check Air Spring'],
                    ['field' => 'check_boot_steering', 'label' => 'Check Boot Steering'],
                    ['field' => 'check_wheel_chain', 'label' => 'Check Wheel Chain'],
                ],
                'Oil' => [
                    ['field' => 'ganti_gear_oil', 'label' => 'Ganti Gear Oil'],
                    ['field' => 'ganti_hydraulic_oil', 'label' => 'Ganti Hydraulic Oil'],
                    ['field' => 'ganti_return_filter', 'label' => 'Ganti Return Filter'],
                    ['field' => 'ganti_brake_oil', 'label' => 'Ganti Brake Oil'],
                ],
            ],

            'Diesel Engine' => [
                'Engine' => [
                    ['field' => 'check_kondisi_level_oli_mesin', 'label' => 'Check Kondisi & Level Oli Mesin'],
                    ['field' => 'check_kondisi_radiator_hose', 'label' => 'Check Kondisi Radiator & Hose'],
                    ['field' => 'check_kondisi_level_air_radiator', 'label' => 'Check Kondisi & Level Air Radiator'],
                    ['field' => 'check_water_pump', 'label' => 'Check Water Pump'],
                    ['field' => 'check_injection_pump_injector_piping', 'label' => 'Check Injection Pump, Injector, Piping'],
                    ['field' => 'check_turbocharger_manifold', 'label' => 'Check Turbocharger & Manifold'],
                    ['field' => 'check_fan_v_belt', 'label' => 'Check Fan & V-Belt'],
                    ['field' => 'check_automatic_tensioner_belt', 'label' => 'Check Automatic Tensioner Belt'],
                    ['field' => 'check_engine_mounting', 'label' => 'Check Engine Mounting'],
                    ['field' => 'check_air_filter_condition', 'label' => 'Check Air Filter Condition'],
                    ['field' => 'check_clearence_valve_drain_valve', 'label' => 'Check Clearence Valve (Drain Valve)'],
                    ['field' => 'check_engine_oil_filter', 'label' => 'Check Engine Oil Filter'],
                    ['field' => 'check_air_radiator', 'label' => 'Check Air Radiator'],
                    ['field' => 'check_minyak_kopling', 'label' => 'Check Minyak Kopling'],
                    ['field' => 'check_fuel_filter', 'label' => 'Check Fuel Filter'],
                ],
                'Electric' => [
                    ['field' => 'check_kondisi_aki_level_air_aki', 'label' => 'Check Kondisi Aki & Level Air Aki'],
                    ['field' => 'check_fungsi_starting_motor', 'label' => 'Check Fungsi Starting Motor'],
                    ['field' => 'check_fungsi_alternator', 'label' => 'Check Fungsi Alternator'],
                    ['field' => 'check_sensor_sensor_gauge', 'label' => 'Check Sensor-sensor & Gauge'],
                    ['field' => 'check_fuse_control_switch', 'label' => 'Check Fuse & Control Switch'],
                    ['field' => 'check_control_display', 'label' => 'Check Control Display'],
                    ['field' => 'check_indicator_wiring', 'label' => 'Check Indicator & Wiring'],
                ],
                'Transmisi, Brake & Drive Shaft' => [
                    ['field' => 'check_kondisi_level_oli_transmisi', 'label' => 'Check Kondisi & Level Oli Transmisi'],
                    ['field' => 'check_fungsi_transmisi', 'label' => 'Check Fungsi Transmisi'],
                    ['field' => 'check_filter_oli_transmisi', 'label' => 'Check Filter Oli Transmisi'],
                    ['field' => 'check_fungsi_rem', 'label' => 'Check Fungsi Rem'],
                    ['field' => 'check_oli_tidak_ada_yang_bocor', 'label' => 'Check Oli Tidak Ada yang Bocor'],
                    ['field' => 'check_kondisi_drive_shaft', 'label' => 'Check Kondisi Drive Shaft'],
                ],
                'Hydraulic System' => [
                    ['field' => 'check_kondisi_level_hydraulic_oil', 'label' => 'Check Kondisi & Level Hydraulic Oil'],
                    ['field' => 'check_kondisi_hydraulic_oil_filter', 'label' => 'Check Kondisi Hydraulic Oil Filter'],
                    ['field' => 'check_fungsi_hydraulic_system', 'label' => 'Check Fungsi Hydraulic System'],
                    ['field' => 'check_fungsi_steering_system', 'label' => 'Check Fungsi Steering System'],
                    ['field' => 'check_kondisi_hydraulic_cylinder', 'label' => 'Check Kondisi Hydraulic Cylinder'],
                    ['field' => 'check_kondisi_steering_cylinder', 'label' => 'Check Kondisi Steering Cylinder'],
                    ['field' => 'check_kondisi_axle_oil', 'label' => 'Check Kondisi Axle Oil'],
                    ['field' => 'check_kondisi_baut_roda_hydraulic', 'label' => 'Check Kondisi Baut Roda'],
                    ['field' => 'check_kondisi_bucket_pin_bucket', 'label' => 'Check Kondisi Bucket, Pin Bucket'],
                    ['field' => 'check_kondisi_dump_pin_bushing', 'label' => 'Check Kondisi Dump, Pin & Bushing'],
                ],
                'General' => [
                    ['field' => 'check_klakson', 'label' => 'Check Klakson'],
                    ['field' => 'check_buzzer_back', 'label' => 'Check Buzzer Back'],
                    ['field' => 'check_kondisi_basket_fresh_body', 'label' => 'Check Kondisi Basket Fresh & Body'],
                    ['field' => 'check_kaca_sepion', 'label' => 'Check Kaca Spion'],
                    ['field' => 'check_kondisi_roda_ban', 'label' => 'Check Kondisi Roda/Ban'],
                    ['field' => 'check_baut_roda_general', 'label' => 'Check Baut Roda'],
                    ['field' => 'check_lampu_depan_kanan', 'label' => 'Check Lampu Depan Kanan'],
                    ['field' => 'check_lampu_depan_kiri', 'label' => 'Check Lampu Depan Kiri'],
                    ['field' => 'check_baut_bearing_molen', 'label' => 'Check Baut Bearing Molen'],
                    ['field' => 'check_baut_hanger_as_roda', 'label' => 'Check Baut Hanger As Roda'],
                ],
            ],

            'Sipil' => [
                'Checklist Sipil' => [
                    [
                        'field' => 'plumbing',
                        'label' => 'Plumbing',
                        'standar' => 'Tidak ada kebocoran dan mampet saluran air pada pipa'
                    ],
                    [
                        'field' => 'plafon',
                        'label' => 'Plafon',
                        'standar' => 'Tidak berlubang, berjamur dan retakan pada plafon'
                    ],
                    [
                        'field' => 'lantai',
                        'label' => 'Lantai',
                        'standar' => 'Tidak berlubang, retak, gompal dan jamur pada lantai'
                    ],
                    [
                        'field' => 'dinding',
                        'label' => 'Dinding',
                        'standar' => 'Tidak ada dinding retak, gompal dan cat atau wallpaper (mengelupas, berjamur, kusam)'
                    ],
                    [
                        'field' => 'jendela',
                        'label' => 'Jendela',
                        'standar' => 'Engsel berfungsi dengan baik, tidak ada retakan kaca atau cover, cat tidak kusam dan tulangan tidak cacat'
                    ],
                    [
                        'field' => 'pintu',
                        'label' => 'Pintu',
                        'standar' => 'Engsel berfungsi dengan baik, tidak ada retakan kaca atau cover, cat tidak kusam dan tulangan tidak cacat'
                    ],
                    [
                        'field' => 'rooling_fast_door',
                        'label' => 'Rooling / Fast Door',
                        'standar' => 'Suara halus, rel terlubrikasi, naik dan turun normal'
                    ],
                ],
            ],

            'Battery' => [
                'Pemeriksaan Fisik & Unit' => [
                    ['field' => 'kondisi_skun', 'label' => 'Kondisi Skun'],
                    ['field' => 'kondisi_unit', 'label' => 'Kondisi Unit'],
                    ['field' => 'kondisi_plug_battery', 'label' => 'Kondisi Plug Battery'],
                ],
            ],
        ];

        DB::transaction(function () use ($data) {
            foreach ($data as $jenisMtc => $groups) {
                $groupOrder = 1;
                foreach ($groups as $groupName => $items) {
                    $group = MtcMasterChecklistGroup::firstOrCreate(
                        [
                            'jenis_mtc'  => $jenisMtc,
                            'nama_group' => $groupName,
                        ],
                        [
                            'urutan'     => $groupOrder++,
                            'is_active'  => true,
                        ]
                    );

                    $itemOrder = 1;
                    foreach ($items as $item) {
                        MtcMasterChecklist::firstOrCreate(
                            [
                                'group_id'   => $group->id,
                                'label'      => $item['label'],
                            ],
                            [
                                'jenis_mtc'  => $jenisMtc,
                                'field_key'  => $item['field'] ?? null,
                                'standar'    => $item['standar'] ?? null,
                                'tipe_input' => $item['type'] ?? 'boolean_ok_ng',
                                'urutan'     => $itemOrder++,
                                'is_active'  => true,
                            ]
                        );
                    }
                }
            }
        });
    }
}
