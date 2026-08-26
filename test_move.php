<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/sales/deals/1/move-stage', 'POST', ['stage_id' => 2]);
$deal = \App\Models\Deal::find(1);
$controller = new \App\Http\Controllers\Sales\DealController(new \App\Services\DealService());
\Illuminate\Support\Facades\Auth::loginUsingId($deal->assigned_to);
$response = $controller->moveStage($request, $deal);
echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Content: " . $response->getContent() . "\n";
