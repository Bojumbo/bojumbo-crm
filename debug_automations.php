<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- AUTOMATIONS ---\n";
$automations = \App\Models\Automation::all();
if ($automations->isEmpty()) {
    echo "No automations found.\n";
} else {
    foreach ($automations as $a) {
        echo "ID: {$a->id}, Pipeline ID: {$a->pipeline_id}, Stage ID: {$a->pipeline_stage_id}, Action: {$a->action_type}, Payload: " . json_encode($a->action_payload) . ", Active: " . ($a->is_active ? 'YES' : 'NO') . "\n";
    }
}

echo "\n--- LATEST DEALS ---\n";
$deals = \App\Models\Deal::orderBy('id', 'desc')->limit(5)->get();
foreach ($deals as $d) {
    $title = $d->getFieldValue(2001);
    $stage = $d->getFieldValue(2006);
    echo "Deal ID: {$d->id}, Title: {$title}, Stage: {$stage}, Created: {$d->created_at}\n";
}
