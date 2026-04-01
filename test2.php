<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$deal = \App\Models\Deal::with('fieldValues')->find(5);
if ($deal) {
    \Illuminate\Support\Facades\Log::info("Test run from CLI script.");
    \App\Services\AutomationService::handleStageChange($deal, 4);
    echo "Done running.\n";
}
else {
    echo "Deal not found.\n";
}
