<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$deal = \App\Models\Deal::find(1);
$user = \App\Models\User::find($deal->assigned_to);
\Illuminate\Support\Facades\Auth::login($user);

$response = $app->handle(
    \Illuminate\Http\Request::create('/sales/deals/1/move-stage', 'POST', ['stage_id' => 2])
);

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Content: " . $response->getContent() . "\n";
