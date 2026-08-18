<?php

namespace App\Providers;

use App\Models\{Lead, Deal, Customer, User, ServiceTicket};
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Lead::observe(AuditObserver::class);
        Deal::observe(AuditObserver::class);
        Customer::observe(AuditObserver::class);
        User::observe(AuditObserver::class);
        ServiceTicket::observe(AuditObserver::class);
    }
}