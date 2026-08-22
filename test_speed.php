<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$candidate = App\Models\User::where('email', 'ahmed@example.com')->first();
$service = app(App\Services\ChatbotService::class);

$tests = [
    [$candidate, 'Should I learn React or Vue?'],
    [$candidate, 'What skills should I learn?'],
    [$candidate, 'Compare remote vs on-site jobs salary'],
];

foreach ($tests as [$user, $q]) {
    $start = microtime(true);
    $r = $service->respond($q, $user);
    $elapsed = round(microtime(true) - $start, 1);
    echo "[{$elapsed}s] Q: {$q}\nA: {$r['text']}\n\n";
}
