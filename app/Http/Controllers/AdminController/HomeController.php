<?php

namespace App\Http\Controllers\AdminController;

use App\City;
use App\Setting;
use App\Order;
use App\Device;
use App\History;
use App\Http\Controllers\Controller;
use App\User;
use App\Membership;
use App\Product;
use App\Subscription;
use App\Topic;
use App\UserDevice;
use App\VerificationRequest;
use App\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin')->except('ordersStats');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = DB::table('users')->count();
        $admins = DB::table('admins')->count();
        $ordersStats = $this->ordersStats();
        return view('admin.home', compact('users', 'admins', 'ordersStats'));
    }

    /**
     * get regions from ajax call
     *
     * @param City $id
     * @return json
     */
    public function filterdUsers(Request $request, $id =null)
    {
        if ($id != null) {
            $topic = Topic::with('families')->find($id);
            $userIds = $topic->families()->where('active', 1)->get();
            return $userIds;
        } else {
            $users = User::with('topics')->where('type', '1')->where('active', 1)
                ->where(function ($q) use ($request) {
                    if ($request->city_id != null) {
                        $q->where('city_id', $request->city_id);
                    }
                    if ($request->topic_id != null) {
                        $q->whereHas('topics', function ($qq) use ($request) {
                            $qq->where('topic_id', $request->topic_id);
                        });
                    }
                    if ($request->name != null) {
                        $q->where('name', 'like', '%'.$request->name.'%');
                    }
                })
                ->get()->toArray();
            return $users;
        }
    }

    /**
     * get subscriptions view
     *
     * @return void
     */
    public function subscriptions()
    {
        $records = Subscription::with('service', 'family')->latest()->get();
        return view('admin.subscriptions.index', compact('records'));
    }

    /**
     * get verification requests view
     *
     * @return void
     */
    public function getPullRequests()
    {
        $records = Wallet::where('pull_request', 1)->latest()->get();
        return view('admin.wallets.index', compact('records'));
    }

    /**
     * update pull request to be accepted or not
     *
     * @param Request $request
     * @param Wallet $id
     * @return void
     */
    public function postPullRequests(Request $request, $id)
    {
        $wallet = Wallet::find($id);
        if (!$wallet == null) {
            $amount = $wallet->amount;
            $wallet->update([
                'cash' => $wallet->cash -= $wallet->amount ,
                'pull_request' => 0,
                'amount' => null,
            ]);

            History::create([
                'user_id' => $wallet->user_id,
                'title' => 'تم تحويل المبلغ المطلوب',
                'price' => $amount,
            ]);

            // $devicesTokens = UserDevice::where('user_id', $wallet->user_id)
            //     ->get()
            //     ->pluck('device_token')
            //     ->toArray();
            // $title = 'سحب الرصيد';
            // $body = 'تم تحويل المبلغ المطلوب سحبه الى حسابك البنكي';
            // if ($devicesTokens) {
            //     sendMultiNotification($title, $body, $devicesTokens, 11);
            // }
            // saveNotification($wallet->user_id, $title, $body, null, 4);


            flash('تم تأكيد التحويل بنجاح');
            return back();
        } else {
            flash('حدث خطأ برجاء المحاولة لاحقا')->error();
            return back();
        }
    }

    /**
     * get view for notifications
     *
     * @return void
     */
    public function sendNotifications()
    {
        return view('admin.notifications.send');
    }

    /**
     * send public notification to all users
     *
     * @param Request $request
     * @return void
     */
    public function postSendNotifications(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
        ]);
        $users = User::get();
        $devicesTokens = UserDevice::pluck('device_token')->toArray();
        if ($devicesTokens) {
            sendMultiNotification($request->title, $request->body, $devicesTokens);
        }
        foreach ($users as $user) {
            saveNotification($user->id, $request->title, $request->body, null, 5);
        }
        flash('تم ارسال الاشعار للمستخدمين بنجاح');
        return back();
    }

    /**
     * get view to select specific users to be notified
     *
     * @return void
     */
    public function sendUserNotifications()
    {
        return view('admin.notifications.send-one');
    }

    /**
     * send public notifications to group of users
     * @param Request $request,
     * @return void
     */
    public function postSendUserNotifications(Request $request)
    {
        $this->validate($request, [
            'user_id*' => 'required',
            'title'    => 'required',
            'body'     => 'required',
        ]);
        foreach ($request->user_id as $one) {
            $user = User::find($one);
            $devicesTokens = UserDevice::where('user_id', $user->id)
                ->get()
                ->pluck('device_token')
                ->toArray();
            if ($devicesTokens) {
                sendMultiNotification($request->title, $request->body, $devicesTokens);
            }
            saveNotification($user->id, $request->title, $request->body, null, 5);
        }
        flash('تم ارسال الاشعار للمستخدمين بنجاح');
        return back();
    }

    /**
     * get wallet charge view
     *
     * @return void
     */
    public function getChargeWallet()
    {
        return view('admin.wallets.send-one');
    }

    /**
     * send public notifications to group of users
     * @param Request $request,
     * @return void
     */
    public function postChargeWallet(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'amount'  => 'required|numeric|gt:0',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->wallet()->update([
            'cash' => $user->wallet->cash + $request->amount
        ]);

        History::create([
            'user_id' => $user->id,
            'title'   => 'شحن رصيد من ادارة التطبيق',
            'price'   => $request->amount
        ]);

        flash('تم شحن محفظة المستخدم بنجاح');
        return back();
    }
    
    
    /**
     * return view with order vats
     *
     * @return void
     */
    public function vat()
    {
        $orders = Order::where('status','3')->get();
        return view('admin.vats.index',compact('orders'));
    }

    /**
     * get orders status our application
     */
    public function ordersStats()
    {
        if (request()->ajax()) {
            $fams = \App\User::whereHas('familyOrders')->withCount('familyOrders')->get();
            $families = $fams->pluck('name')->toArray();
            $count = $fams->pluck('family_orders_count')->toArray();
            return ['families' => $families, 'count' => $count];
        }
        $ordersByMonth = Order::select([
            DB::raw('count(id) as quantity'),
            DB::raw('MONTHNAME(created_at) as month'),
            // DB::raw('year(created_at) as year')
        ])
        ->groupBy('month')
        ->get();

        $months= $ordersByMonth->pluck('month');
        $quantity= $ordersByMonth->pluck('quantity')->toArray();
        // dd($months);

        return $ordersByMonth;
    }
}
