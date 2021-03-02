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
     *  'status' 0 => active, 1 => done, 2 => canceled, 3 => waiting
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
     * compelete order and determine commission status
     * @param Request $request
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
        if ($request->user()->id != $order->provider_id) {
            $err = [
                'key' => 'user_not_valid',
                'value' => 'مستخدم غير صحيح',
            ];
            return ApiController::respondWithErrorArray($err);
        }

        $taxValue = Setting::pluck('commission')->first() ?? 0;

        $order->update([
            'status' => '1',
            'tax'    => $order->price * $taxValue,
            'payment_status' => $taxValue == 0 ? 1 : 0 // if the commission = 0 make order paid for commission
        ]);

        return ApiController::respondWithSuccess('تم اكمال الطلب بنجاح، برجاء دفع العمولة');
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
            'notes'    => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $order = Order::find($request->order_id);
        if ($request->user()->id != $order->provider_id) {
            $err = [
                'key' => 'user_not_valid',
                'value' => 'مستخدم غير صحيح',
            ];
            return ApiController::respondWithErrorArray($err);
        }

        $order->update([
            'status' => '3',
            'notes' => $request->notes,
        ]);

        return ApiController::respondWithSuccess(' تم تقديم طلب الالغاءالى الادراة وبانتظار المراجعة');
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
            'comment' => 'sometimes|min:8',
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
            'comment' => $request->comment,
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

        return ApiController::respondWithSuccess('تم استلام الشكوى, سيتم اتخاذ الاجراء المناسب');
    }
}