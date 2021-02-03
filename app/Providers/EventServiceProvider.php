<?php

namespace App\Providers;

use App\Events\ChangeAvailabliltyEvent;
use App\Events\ClientRegisterdEvent;
use App\Events\OrderDoneEvent;
use App\Listeners\AddDuration;
use App\Listeners\CalculateTax;
use App\Listeners\CreateDefaultAddress;
use App\Listeners\DeleteNotifications;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderDoneEvent::class => [
            DeleteNotifications::class,
            CalculateTax::class,
        ],
        ClientRegisterdEvent::class => [
            CreateDefaultAddress::class,
        ],
        ChangeAvailabliltyEvent::class => [
            AddDuration::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}