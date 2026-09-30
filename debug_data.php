<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = new Illuminate\Http\Request();
$ctrl = new App\Http\Controllers\Utility\WWTPController();
$res = $ctrl->wwtp_wco_data($req);
$d = $res->getData(true);

echo "card7 daily count: " . count($d['card7_safety_perf']['daily_aggregated'] ?? []) . "\n";
print_r($d['card7_safety_perf']['daily_aggregated'] ?? []);
echo "card11 categories count: " . count($d['card11_trend_effluent_mingguan']['categories'] ?? []) . "\n";
echo "card12 categories count: " . count($d['card12_trend_kepatuhan_effluent_cod']['categories'] ?? []) . "\n";
echo "card14 categories count: " . count($d['card14_influent_mingguan']['categories'] ?? []) . "\n";
echo "card17 values count: " . count($d['card17_hse_training_effluent_tss']['values'] ?? []) . "\n";
