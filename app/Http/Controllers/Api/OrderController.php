<?php

namespace App\Http\Controllers\Api;

use App\CartItem;
use App\Complaint;
use App\Events\OrderDoneEvent;
use App\History;
use App\Http\Controllers\Controller;
use App\Jobs\UpdateProductNumber;
use App\Order;
use App\OrderOffer;
use App\Rate;
use App\Setting;
use App\User;
use App\UserAdresses;
use App\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;

class OrderController extends Controller
{
    /**
     * Create a new OrderController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:api', 'api'])->except('fatooraStatus');
    }

    /**
     * save current orders to storage
     *  'status'           0 => active, 1 => done, 2 => canceled
     *  'provider_status'  0 => new, 1 => active, 2 => done, 3 => canceled
     * @param Request $request
     * @return Json $response
     */
    public function postOrder(Request $request)
    {
        $rules = [
            'provider_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'count' => 'required',
            'price' => 'required',
            'property_values' => 'required|array',
            'recieve_place' => 'required',
            'notes' => 'sometimes',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = $request->user();

        //save order to storage
        $order = Order::create([
            'provider_id'     => $request->provider_id,
            'product_id'      => $request->product_id,
            'user_id'         => $user->id,
            'price'           => $request->price,
            'count'           => $request->count,
            'recieve_place'   => $request->recieve_place,
            'property_values' => serialize($request->property_values),
            'notes'           => $request->notes,
            'status'          => '0',
        ]);

        //send notification to provider to notify them about new order
        $devicesTokens = UserDevice::where('user_id', $request->provider_id)
            ->pluck('device_token')
            ->toArray();
        $title = 'طلب جديد';
        $body = 'تم استلام طلب جديد برجاء التحقق منه';
        if ($devicesTokens) {
            sendMultiNotification($title, $body, $devicesTokens, $order->id);
        }
        saveNotification($request->provider_id, $title, $body, $order->id, 1);

        return ApiController::respondWithSuccess('تم استلام طلبك بنجاح');
    }




    /**
     * compelete order and determine payment status
     * @param Request $request
     *  payment_status : 0 => wallet, 1 => online
     * @return Json response
     */
    public function compeleteOrder(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $order = Order::find($request->order_id);
        // confirm that the request user is order user
        if ($request->user()->id != $order->user_id) {
            $err = [
                'key' => 'user_not_valid',
                'value' => 'مستخدم غير صحيح',
            ];
            return ApiController::respondWithErrorArray($err);
        }
    }

    /**
     * here is the last operation on order cycle the user finish order after all done
     *
     * @param Request $request
     * @return Json response
     */
    public function finishOrder(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $order = Order::find($request->order_id);
        // confirm that the request user is order user
        if ($request->user()->id != $order->user_id) {
            $err = [
                'key' => 'user_not_valid',
                'value' => 'مستخدم غير صحيح',
            ];
            return ApiController::respondWithErrorArray($err);
        }

        // confirm order status if not
        if ($order->status != '2') {
            $err = [
                'key' => 'order_status',
                'value' => 'لا يمكن اتمام العملية بسبب حالة الطلب',
            ];
            return ApiController::respondWithErrorArray($err);
        }

        $order->update([
            'status' => '3', // 0 => new, 1 => accepted, 2 => active, 3 => done, 4 => canceled
            'provider_status' => '2', // 0 => new, 1 => active, 2 => done, 3 => canceled, 9 => hold
        ]);

        if ($order->delivery_type == null && $order->driver_id != null) {
            $driverOrder = $order->offers()->where('driver_id', $order->driver_id)->first();
            if ($driverOrder != null) {
                $driverOrder->update([
                    'status' => '3', // 0 => new, 1 => hold, 2 => active, 3 => done, 4 => canceled
                ]);
            }
        }

        $res = event(new OrderDoneEvent($order));

        $transactions = $this->transactions($order);
        if ($transactions) {
            //return here that order has done
            return ApiController::respondWithSuccess('تم انهاء الطلب الخاص بكم، برجاء تقييم تجربتك ');
        } else {
            $err = [
                'key' => 'error_on_transaction',
                'value' => 'حدث خطأ برجاء المحاولة لاحقا',
            ];
            return ApiController::respondWithErrorArray($err);
        }
    }

    /**
     * cancele order with reasons
     *
     * @param Request $request
     * @return void
     */
    public function canceleOrder(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
            'notes' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $order = Order::find($request->order_id);
        $order->update([
            'status' => '4',
            'provider_status' => '3',
            'notes' => $request->notes,
        ]);

        $offers = $order->offers()->where('order_id', $order->id)->get();
        if ($offers->count() > 0) {
            foreach ($offers as $offer) {
                $offer->update(['status' => '4']);
            }
        }

        event(new OrderDoneEvent($order));

        return ApiController::respondWithSuccess('تم الغاء الطلب');
    }

    /**
     * after finishing the order the user give rate to family and driver
     *
     * @param Request $request
     * @return Json response
     */
    public function rates(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
            'rate' => 'required|in:1,2,3,4,5',
            'to_user_id' => 'required|exists:users,id',
            'note' => 'sometimes|min:8',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $created = Rate::updateOrCreate([
            'from_user_id' => $request->user()->id,
            'to_user_id' => $request->to_user_id], [
            'order_id' => $request->order_id,
            'rate' => $request->rate,
            'note' => $request->note,
        ]);
        if ($created) {
            return ApiController::respondWithSuccess('تم حفظ تقييمك');
        } else {
            return ApiController::respondWithServerErrorArray();
        }
    }

    /**
     * save user notes and complaints on order
     *
     * @param Request $request
     * @return Json $response
     */
    public function postComplaint(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
            'complaint' => 'required|min:10',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        Complaint::create([
            'user_id' => $request->user()->id,
            'order_id' => $request->order_id,
            'content' => $request->complaint,
        ]);

        return ApiController::respondWithSuccess('تم استلام الشكوى, سيتم الرد عليك في اقرب فرصة');
    }

    public function fatooraStatus()
    {
        $PaymentId = \Request::query('paymentId');
        $resData = MyFatoorahStatus($PaymentId);
        $result = json_decode($resData);
        // dd($result);
        if ($result != null && $result->IsSuccess === true && $result->Data->InvoiceStatus === "Paid") {
            $InvoiceId = $result->Data->InvoiceId;
            $order = Order::where('invoice_id', $InvoiceId)->first();
            $user = $order->user;
            if ($order) {
                $order->update([
                    'payment_status' => 1,
                    'status' => '2', // 0 => new, 1 => accepted, 2 => active, 3 => done, 4 => canceled
                    'provider_status' => '1', // 0 => new, 1 => active, 2 => done, 3 => canceled, 9 => hold
                ]);

                // if there is offer update dirver order status to be active from hold
                if ($order->driver_id) {
                    $offer = $order->offers()->where('driver_id', $order->driver_id)->first();
                    if ($offer) {
                        $offer->update([
                        'status' => '2'
                    ]);
                    }
                }

                History::create([
                    'user_id' => $order->user_id,
                    'title' => 'تم خصم رصيد المحفظة من قيمة الطلب رقم : ' . $order->id,
                    'price' => $user->wallet->cash,
                ]);

                $updated = $user->wallet->update([
                    'cash' => 0,
                ]);

                $user->histories()->create([
                    'title' => 'تم دفع قيمة الطلب رقم: ' . $order->id,
                    'price' => $result->Data->InvoiceValue,
                ]);
                return redirect()->to('/fatoora/success');
            } else {
                return redirect()->to('/fatoora/error');
            }
        } else {
            return redirect()->to('/fatoora/error');
        }
    }

    /**
     * make transaction on user, family and driver wallets
     *
     * @param Order $order
     * @return Boolean $var
     */
    public function transactions(Order $order)
    {
        try {
            // we will use database transactions for more security
            DB::beginTransaction();
            // to be changed for add all money then reduce commission and send notifications to deiver and family
            $familyWallet = $order->provider->wallet;
            $driverWallet = $order->driver_id == null ? null : $order->driver->wallet;

            $familyCom = (double) Setting::pluck('family_commission')->first() ?? 0;
            $driverCom = (double) Setting::pluck('driver_commission')->first() ?? 0;
            $tax = (double) Setting::pluck('tax')->first() ?? 0;

            $totalOrderPrice = (double) $order->price;
            $orderOffer = $order->offers()->where('driver_id', $order->driver_id)->first();
            $orderDeliveryPrice = $orderOffer == null ? 0 : (double) $orderOffer->price;

            // Todo : withdrow tax from offer price
            if ($order->driver_id != null) {

                // send user all money then reduce commission
                $orderDeliveryPrice = $orderDeliveryPrice - ($orderDeliveryPrice * $tax);
                $driverWallet->update([
                    'cash' => $driverWallet->cash += $orderDeliveryPrice,
                ]);
                History::create([
                    'user_id' => $order->driver_id,
                    'title' => 'تم استلام قيمة الطلب رقم : ' . $order->id,
                    'price' => $orderDeliveryPrice,
                ]);
                //send notification to driver to notify him about new balance
                $devicesTokens = UserDevice::where('user_id', $order->driver_id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                $title = 'قيمة الطلب رقم : ' . $order->id;
                $body = 'تم استلام قيمة التوصيل برجاءالتحقق من سجل العمليات';
                if ($devicesTokens) {
                    sendMultiNotification($title, $body, $devicesTokens, $order->id);
                }
                saveNotification($order->driver_id, $title, $body, $order->id, 1);

                // here we reduce the app commission from driver wallet
                $driverWallet->update([
                    'cash' => $driverWallet->cash -= ($orderDeliveryPrice * $driverCom),
                ]);
                History::create([
                    'user_id' => $order->driver_id,
                    'title' => 'تم خصم عمولة الطلب رقم : ' . $order->id,
                    'price' => ($orderDeliveryPrice * $driverCom),
                ]);
            }

            $familyPrice = $totalOrderPrice - $orderDeliveryPrice;
            // reduce tax from family price
            $familyPrice -= ($familyPrice * $tax);

            $familyWallet->update([
                'cash' => $familyWallet->cash + $familyPrice,
            ]);

            History::create([
                'user_id' => $order->provider_id,
                'title' => 'تم استلام قيمة الطلب رقم : ' . $order->id,
                'price' => $familyPrice,
            ]);

            //send notification to provider to notify him about new balance
            $devicesTokens = UserDevice::where('user_id', $order->provider_id)
                ->get()
                ->pluck('device_token')
                ->toArray();
            $title = 'قيمة الطلب رقم : ' . $order->id;
            $body = 'تم استلام قيمة الطلب برجاءالتحقق من سجل العمليات';
            if ($devicesTokens) {
                sendMultiNotification($title, $body, $devicesTokens, $order->id);
            }
            saveNotification($order->provider_id, $title, $body, $order->id, 1);

            // here we reduce the app commission from driver wallet
            $updated = $familyWallet->update([
                'cash' => $familyWallet->cash -= ($familyPrice * $familyCom),
            ]);

            History::create([
                'user_id' => $order->provider_id,
                'title' => 'تم خصم عمولة الطلب رقم : ' . $order->id,
                'price' => ($familyPrice * $familyCom),
            ]);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::rollback();
            return false;
        }
    }

    /**
     * driver notify user that order has been deliverd
     *
     * @param Request $request
     * @return Json response
     */
    public function notifyUser(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $order = Order::find($request->order_id);

        if ($request->user()->id != $order->driver_id) {
            $err = [
                'key' => 'driver_not_valid',
                'value' => 'مستخدم غير صحيح',
            ];
            return ApiController::respondWithErrorArray($err);
        }

        $devicesTokens = UserDevice::where('user_id', $order->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        $title = 'تم التوصيل';
        $body = 'اعلان من السائق بتوصيل الطلب';
        if ($devicesTokens) {
            sendMultiNotification($title, $body, $devicesTokens, $order->id);
        }
        saveNotification($order->user_id, $title, $body, $order->id, 2);

        return ApiController::respondWithSuccess('تم ارسال تنبية للعميل');
    }
}