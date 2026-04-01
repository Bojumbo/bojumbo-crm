<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$template = \App\Models\DocumentTemplate::first();
if ($template) {
    // Очищаємо ID від крапок, пробілів та інших символів
    $cleanId = preg_replace('/[^a-zA-Z0-9_-]/', '', $template->google_drive_id);
    $template->update(['google_drive_id' => $cleanId]);
    echo "ID виправлено на: " . $cleanId . "\n";
} else {
    echo "Шаблонів не знайдено.\n";
}