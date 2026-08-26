<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phone = '082111831095';

$leads = \App\Models\Lead::where('phone', $phone)->withTrashed()->get();
foreach ($leads as $lead) {
    // Force delete any associated deals
    \App\Models\Deal::where('lead_id', $lead->id)->withTrashed()->forceDelete();
    
    // Force delete any associated activities
    \App\Models\Activity::where('activitable_type', \App\Models\Lead::class)
        ->where('activitable_id', $lead->id)
        ->forceDelete();
        
    // Force delete the lead itself
    $lead->forceDelete();
    echo "Deleted lead ID: " . $lead->id . "\n";
}

echo "Done.";
