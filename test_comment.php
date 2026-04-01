<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::capture());

use App\Models\Deal;
use App\Http\Controllers\ActivityController;
use Illuminate\Http\Request;

$deal = Deal::first();
if (!$deal) {
    echo "No deal found to test with.\n";
    exit;
}

$request = new Request();
$request->replace([
    'loggable_type' => 'App\Models\Deal',
    'loggable_id' => $deal->id,
    'comment' => 'Test comment from script at ' . date('Y-m-d H:i:s')
]);

$controller = app(ActivityController::class);
$response = $controller->store($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Body: " . json_encode($response->getData()) . "\n";
