<?php

namespace App\Listeners;

use App\Events\OrderDoneEvent;
use App\Order;

class DeleteNotifications
{
    /**
     * Handle the event.
     * deleting all notifications that belongs to this order
     * @param  OrderDoneEvent  $event
     * @return void
     */
    public function handle(OrderDoneEvent $event)
    {
        $ids = \App\Notification::where('order_id', $event->order->id)->pluck('id');
        if ($ids->count()) {
            \App\Notification::destroy($ids);
        }
    }
}
