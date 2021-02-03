<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Order;

class OrdersController extends Controller
{
    /**
     * get all orders in the site except canceled
     *
     * @return void
     */
    public function index()
    {
        $records = Order::with('user', 'provider', 'driver')->where('status', '!=', '4')->latest()->get();
        return view('admin.orders.index', compact('records'));
    }

    /**
     * get canceled orders from storage
     *
     * @return void
     */
    public function canceled()
    {
        $records = Order::where('status', '4')->latest()->get();
        return view('admin.orders.canceled', compact('records'));
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
}
