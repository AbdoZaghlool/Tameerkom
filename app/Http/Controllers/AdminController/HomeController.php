<?php

namespace App\Http\Controllers\AdminController;

use App\City;
use App\Order;
use App\History;
use App\Http\Controllers\Controller;
use App\Picture;
use App\Product;
use App\Property;
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


    public function catProperties($id)
    {
        $props = Property::with('values')->where('category_id', $id)->get();
        return view('admin.products.div', ['props'=> $props])->render();
    }

    public function productValues($product_id)
    {
        $product = Product::with('values')->find($product_id);
        $props = Property::with('values')->where('category_id', $product->category_id)->get();
        return view('admin.products.edit-div', ['product'=> $product, 'props' => $props])->render();
    }

    public function deleteImage($id)
    {
        $image = Picture::find($id);
        $value = $image->image;
        $deleted = $image->delete();
        if ($deleted) {
            if (file_exists(public_path('uploads/products/' . $value))) {
                unlink(public_path('uploads/products/' . $value));
            }
            $v = '{"message":"done"}';
            return response()->json($v);
        }
    }

    public function catProperties($id)
    {
        $props = Property::with('values')->where('category_id', $id)->get();
        return view('admin.products.div', ['props'=> $props])->render();
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
