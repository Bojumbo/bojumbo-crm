<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = [
    'automations' => \App\Models\Automation::all()->toArray(),
    'latest_deals' => \App\Models\Deal::orderBy('id', 'desc')->limit(3)->get()->toArray(),
    'pipelines' => \App\Models\Pipeline::with('stages')->get()->toArray(),
];

file_put_contents('debug_dump.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Dumped to debug_dump.json\n";
