<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Deal;
use App\Models\ServiceTicket;
use App\Models\LeadSource;
use App\Models\PipelineStage;
use App\Models\LostReason;
use App\Observers\AuditObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(AuditObserver::class);
        Customer::observe(AuditObserver::class);
        Lead::observe(AuditObserver::class);
        Deal::observe(AuditObserver::class);
        ServiceTicket::observe(AuditObserver::class);
        LeadSource::observe(AuditObserver::class);
        PipelineStage::observe(AuditObserver::class);
        LostReason::observe(AuditObserver::class);
    }
}
