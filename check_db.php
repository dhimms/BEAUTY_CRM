<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phone = '082111831095';
$email = 'nyomanviveka22@gmail.com';

echo "Leads:\n";
print_r(\App\Models\Lead::where('phone', $phone)->orWhere('email', $email)->withTrashed()->get()->toArray());

echo "\nCustomers:\n";
print_r(\App\Models\Customer::where('phone', $phone)->orWhere('email', $email)->withTrashed()->get()->toArray());
