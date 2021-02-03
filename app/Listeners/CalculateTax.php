<?php

namespace App\Listeners;

use App\Events\OrderDoneEvent;
use App\Setting;

class CalculateTax
{

    /**
     * Handle the event.
     * calculating order tax and updating it in db;
     * @param  OrderDoneEvent  $event
     * @return void
     */
    public function handle(OrderDoneEvent $event)
    {
        $tax = Setting::pluck('tax')->first() ?? 0.15;
        $event->order->update([
            'tax' => $event->order->price * $tax,
        ]);
    }
}
