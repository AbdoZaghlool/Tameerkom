<?php

namespace App\Console\Commands;

use App\Events\OrderDoneEvent;
use App\Order;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:check-durations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check orders for family acceptance time, drivers offers acceptance and order payment time to make order canceled';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('oleeeee...');
        // get application limited times.
        $paymentTime = (int) Setting::pluck('order_payment_time')->first();
        $offerTime = (int) Setting::pluck('accept_order_time')->first();
        $familyTime = (int) Setting::pluck('family_offer_time')->first();
        // calculate current order time and scheduled order time
        $currentOrdersTime = $paymentTime + $offerTime;
        $scheduledOrdersTime = $paymentTime + $offerTime + $familyTime;
        // get current time to compare with order created_at.
        $now = Carbon::now();
        // get all orders where not paid yet
        $orders = Order::whereNotIn('status', ['2', '3', '4'])->get();

        if ($orders->count()) {
            foreach ($orders as $order) {
                $orderCreatedAt = $order->created_at;
                // dump(gmdate('i', $now->diffInMinutes($orderCreatedAt)), $orderCreatedAt->format('H:i:s'));

                if ($order->type_id == 1) {
                    // the order is current if order new then check for offers
                    if ($now->format('Y-m-d H:i:s') > $orderCreatedAt->addMinutes($currentOrdersTime)->format('Y-m-d H:i:s')) {
                        $order->update([
                            'status' => '4',
                            'provider_status' => '3',
                            'notes' => 'انقضت مهلة الطلب',
                        ]);

                        if ($order->driver_id != null) {
                            $offer = $order->offers()->where('driver_id', $order->driver_id)->first();
                            if ($offer) {
                                $offer->update([
                                    'status' => '4',
                                ]);
                            }
                        }

                        event(new OrderDoneEvent($order));
                    }

                } else {
                    // the order is scheduled, here we will check for family acceptance time.
                    if ($now > $orderCreatedAt->addMinutes($scheduledOrdersTime)) {
                        $order->update([
                            'status' => '4',
                            'provider_status' => '3',
                            'notes' => 'انقضت مهلة الطلب',
                        ]);

                        if ($order->driver_id != null) {
                            $offer = $order->offers()->where('driver_id', $order->driver_id)->first();
                            if ($offer) {
                                $offer->update([
                                    'status' => '4',
                                ]);
                            }

                        }

                        event(new OrderDoneEvent($order));

                    }
                }

            }
        }
    }
}
