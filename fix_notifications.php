<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\User::all()->each(function($u) {
    $u->notifications()->where('type', 'deal_won')->get()->each(function($n) use ($u) {
        if ($u->hasRole('Manager')) {
            $d = $n->data;
            $d['url'] = route('manager.pipeline.index');
            $n->data = $d;
            $n->save();
        }
    });
});
echo "Updated notifications!";
