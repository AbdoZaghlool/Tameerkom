<?php

namespace App\Http\Controllers\AdminController;

use App\History;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * get all orders in the site except canceled
     *
     * @return void
     */
    public function index()
    {
        $records = Order::with('user', 'provider', 'product')->whereIn('status', ['0','1'])->latest()->get();
        return view('admin.orders.index', compact('records'));
    }

    /**
     * get canceled orders from storage
     *
     * @return void
     */
    public function canceled()
    {
        $records = Order::with('user', 'provider', 'product')->where('status', '2')->latest()->get();
        return view('admin.orders.canceled', compact('records'));
    }

    public function providersCanceleRequests()
    {
        $records = Order::with('user', 'provider', 'product')->where('status', '3')->latest()->get();
        return view('admin.orders.canele-requests', compact('records'));
    }

    public function acceptCanceleRequest(Request $request)
    {
        return "true";
        $order = Order::findOrFail($request->order_id);
        $order->status = '2';
        $order->save();
        return json_encode('done');
    }

    /**
     * get specific order from storage
     *
     * @return void
     */
    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function delete(Order $order)
    {
        abort(404);
        $order->delete();
        flash('تم الحذف بنجاح')->success();
        return back();
    }

    /**
     * get compeleted prodcuts to calculate commission
     *
     * @return void
     */
    public function commissons()
    {
        $records = Order::where('status', '1')->where('payment_status', 0)->get();
        return view('admin.commissions.payments', compact('records'));
    }

    /**
     * get paid commissions for compeleted products
     *
     * @return void
     */
    public function paid()
    {
        $records = Order::where('status', '1')->where('payment_status', 1)->get();
        return view('admin.commissions.paid', compact('records'));
    }

    /**
     * prepare view to update commission status
     *
     * @param Product $id
     * @return void
     */
    public function updateStatus($id)
    {
        $bank = Product::find($id);
        return view('admin.commissions.update', compact('bank'));
    }

    /**
     * upadate commission status to be paid
     *
     * @param Request $request
     * @param Product $id
     * @return void
     */
    public function postUpdateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->payment_status = 1;
        $order->save();
        History::create([
            'user_id' =>  $order->provider_id,
            'title'   =>  'عمولة الطلب رقم: '.$id,
            'price'   =>  $order->tax,
        ]);
        return json_encode('done');
    }
}