<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\User::all()->each(function($u) {
    if ($u->hasRole('Manager')) {
        $u->notifications()->where('type', 'App\Notifications\DealWonNotification')->delete();
    }
});
echo "Deleted DealWonNotifications for Managers!";
