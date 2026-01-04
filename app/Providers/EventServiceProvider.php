<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Order;
use App\Models\ElderlyCare;
use App\Models\HomeXray;
use App\Models\MedicalTest;
use App\Models\RequestNurse;
use App\Models\AppointmentProvider;
use App\Observers\OrderObserver;
use App\Observers\ElderlyCareObserver;
use App\Observers\HomeXrayObserver;
use App\Observers\MedicalTestObserver;
use App\Observers\RequestNurseObserver;
use App\Observers\AppointmentProviderObserver;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
     public function boot()
    {
        Order::observe(OrderObserver::class);
        ElderlyCare::observe(ElderlyCareObserver::class);
        HomeXray::observe(HomeXrayObserver::class);
        MedicalTest::observe(MedicalTestObserver::class);
        RequestNurse::observe(RequestNurseObserver::class);
        AppointmentProvider::observe(AppointmentProviderObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
