<?php

namespace App\Http\Controllers\AdminController;

use App\City;
use App\Order;
use App\History;
use App\Http\Controllers\Controller;
use App\User;
use App\UserDevice;

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
        return view('admin.home', compact('users', 'admins'));
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

    public function providersCanceleRequests()
    {
        $records = Order::with('user', 'provider', 'product')->where('status', '3')->latest()->get();
        return view('admin.orders.canele-requests', compact('records'));
    }

    public function acceptCanceleRequest(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->status = '2';
        $order->save();
        return json_encode('done');
    }
}