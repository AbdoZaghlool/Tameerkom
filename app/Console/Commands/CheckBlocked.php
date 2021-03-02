<?php

namespace App\Console\Commands;

use App\Events\OrderDoneEvent;
use App\Notification;
use App\Order;
use App\Setting;
use App\User;
use App\UserDevice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckBlocked extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:check-block-state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check provider active orders or unpaid commissoins count to block them if exceeded max value';

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

        // get application limited values.
        $unpaidCommissions = (int) Setting::pluck('unpaid_commissions')->first() ?? 5;
        $activeOrders = (int) Setting::pluck('active_orders_count')->first() ?? 5;
        $providers = User::where('type', '1')->where('active', 1)->get();
        if ($providers->count() > 0) {
            foreach ($providers as $provider) {
                // make default to be unblocked
                $provider->update([
                    'blocked' => 0
                ]);

                // check for active orders count
                if ($provider->providerOrders()->where('status', '0')->count() >= $activeOrders) {
                    $provider->update([
                        'blocked'=>1
                    ]);
                }

                // check for commissions count
                if ($provider->providerOrders()->where('status', '1')->where('payment_status', 0)->count() >= $unpaidCommissions) {
                    $provider->update([
                        'blocked'=>1
                    ]);
                }

                if ($provider->blocked == 1) {
                    $oldNotifications = Notification::where('user_id', $provider->id)->where('type', 4)->first();
                    if ($oldNotifications == null) {
                        $devicesTokens = UserDevice::where('user_id', $provider->id)
                            ->pluck('device_token')
                            ->toArray();
                        $title = 'حظر حسابك';
                        $body = 'تم حظر حسابك مؤقتا برجاء مراجعة طلباتك النشطة او عمولاتك المستحقة';
                        if ($devicesTokens) {
                            sendMultiNotification($title, $body, $devicesTokens);
                        }
                        saveNotification($provider->id, $title, $body, null, 4);
                    }
                }
            }
        }
    }
}