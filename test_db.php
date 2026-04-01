<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$automations = \App\Models\Automation::all();
echo "Total automations: " . $automations->count() . "\n";
foreach ($automations as $a) {
    echo "ID: {$a->id}, Pipe: {$a->pipeline_id}, Stage: {$a->pipeline_stage_id}, Action: {$a->action_type}, Payload: " . json_encode($a->action_payload) . "\n";
}

$deals = \App\Models\Deal::with('fieldValues')->get();
echo "Total Deals: " . $deals->count() . "\n";
