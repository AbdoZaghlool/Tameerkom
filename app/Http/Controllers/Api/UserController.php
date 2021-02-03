<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order as OrderResource;
use App\Order;
use App\OrderOffer;
use App\ProductRate;
use Validator;
use Auth;
use App\User;
use App\UserDevice;

class UserController extends Controller
{
    /**
     * Create a new PartnerController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:api', 'api']);
        $this->middleware(function ($request, $next) {
            $userType = Auth::user()->type;
            if ($userType != '0') {
                $err = [
                    'key' => 'user',
                    'value'=> 'مستخدم غير صحيح'
                ];
                return ApiController::respondWithErrorArray($err);
            }
            return $next($request);
        });
    }

    /**
     * change user image
     *
     * @param Request $request
     * @return void
     */
    public function change_image(Request $request)
    {
        $rules = [
            'image' => 'required|mimes:jpeg,bmp,png,jpg|max:5000',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = User::where('id', $request->user()->id)->first();
        $updated =  $user->update([
            'image' =>  UploadImageEdit($request->file('image'), 'image', '/uploads/users', $request->user()->image),
        ]);
        $success = [
            'key' => 'image',
            'value' => User::find($request->user()->id)->image
        ];
        return $updated
            ? ApiController::respondWithSuccess($success)
            : ApiController::respondWithServerErrorObject();
    }

    /**
     * get user current orders
     *    0 => new, 1 => accepted, 2 => active, 3 => done, 4 => canceled
     *
     * @param Integer $status
     * @return OrderResource response
     */
    public function myCurrentOrders()
    {
        $user = request()->user();
        $orders = $user->userOrders()->where('type_id', 1)->latest()->get();
        if ($orders->count() == 0) {
            $err = [
                'key' => 'orders',
                'value'=> 'لا يوجد طلبات حاليا'
            ];
            return ApiController::respondWithErrorObject(array($err));
        }
        return ApiController::respondWithSuccess(OrderResource::collection($orders));
    }

    /**
     * get user scheduled orders
     *    0 => new, 1 => accepted, 2 => active, 3 => done, 4 => canceled
     *
     * @param Integer $status
     * @return OrderResource response
     */
    public function myScheduledOrders(Request $request)
    {
        $rules = ['status' => 'sometimes|in:0,1,2,3'];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $user = request()->user();
        $orders = $user->userOrders()
            ->status($request->status)
            ->where('type_id', 2)
            ->latest()
            ->get();
        if ($orders->count() == 0) {
            $err = [
                'key' => 'scheduled_orders',
                'value'=> 'لا يوجد طلبات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        return ApiController::respondWithSuccess(OrderResource::collection($orders));
    }

    /**
     * get user order offers
     *
     * @param Order $order_id
     * @return Json response
     */
    public function orderOffers($order_id)
    {
        // check if $order_id exists withen this user or not
        if (request()->user()->userOrders()->whereId($order_id)->first() == null) {
            $err = [
                'key' => 'order',
                'value'=> 'لا يوجد طلب بهذا الرقم'
            ];
            return ApiController::respondWithErrorObject(array($err));
        }
        //find offers that drivers add prices on it
        $offers = OrderOffer::where('order_id', $order_id)->where('status', '1')->get();
        if ($offers->count() == 0) {
            $err = [
                'key' => 'offers',
                'value'=> 'لا يوجد عروض حاليا'
            ];
            return ApiController::respondWithErrorObject(array($err));
        }
        $arr = [];
        foreach ($offers as $offer) {
            $driver = User::find($offer->driver_id);
            array_push($arr, [
                'id'           => $offer->id,
                'driver_id'    => $offer->driver_id,
                'driver_name'  => $offer->driver->name,
                'driver_image' => asset('uploads/users/'.$offer->driver->image),
                'price'        => $offer->price,
                'rate'         => (int)$driver->getRateValue()
            ]);
        }
        usort($arr, function ($item1, $item2) {
            return $item1['price'] <=> $item2['price'];
        });
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * accept driver offer on user order
     *
     * @param Request $request
     * @return Json response
     */
    public function acceptOrderOffer(Request $request)
    {
        $rules = ['offer_id' => 'required|exists:order_offers,id'];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        if (! $request->user()->type == '0') {
            $err = [
                'key' => 'user',
                'value'=> 'مستخدم غير صحيح'
            ];
            return ApiController::respondWithErrorObject(array($err));
        }


        // find offer in  holded offers
        $offer = OrderOffer::where('status', '1')->find($request->offer_id);
        if ($offer == null) {
            $err = [
                'key' => 'offer',
                'value'=> 'لا يوجد عروض الان'
            ];
            return ApiController::respondWithErrorObject(array($err));
        }

        if ($request->user()->userOrders()->whereId($offer->order_id)->first() == null) {
            $err = [
                'key' => 'order',
                'value'=> 'لا يوجد طلب بهذا الرقم'
            ];
            return ApiController::respondWithErrorObject(array($err));
        }

        // update offer status from hold to active
        // $offer->update([
        //     'status' => '2'
        // ]);
        // update order user_status to be active and provider_status to be active and update price to be old+offerPrice
        $order = $offer->order;
        $order->update([
            'driver_id'       => $offer->driver_id,
            'price'           => (double)$order->price + (double)$offer->price,
            'status'          => '1',// now it is accepted for user
            'provider_status' => '9' // now it is hold for family
        ]);

        // delete other offers on this order;
        $toBeDeleted = OrderOffer::where('order_id', $order->id)->where('id', '!=', $request->offer_id)->pluck('id');
        OrderOffer::destroy($toBeDeleted);

        // delete driver offers for other holded or new current orders
        if ($order->type_id == 1) {
            $driver = $order->driver;
            $otherOrdersOffers = OrderOffer::with('order')->where('driver_id', $driver->id)->whereNotIn('status', ['2','3'])->where('order_id', '!=', $order->id)
                ->whereHas('order', function ($q) {
                    $q->where('type_id', 1);
                })
                ->pluck('id');
            OrderOffer::destroy($otherOrdersOffers);
        }

        // send notification to driver
        $devicesTokens = UserDevice::where('user_id', $order->driver_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        $title = 'قبول العرض';
        $body = 'تم قبول عرضك علي الطلب';
        if ($devicesTokens) {
            sendMultiNotification($title, $body, $devicesTokens, $order->id);
        }
        saveNotification($order->driver_id, $title, $body, $order->id, 2);

        return ApiController::respondWithSuccess('تمت الموافقه علي عرض السعر ');
    }

    /**
     * udpate order recievmnet time
     *
     * @param Request $request
     * @return void
     */
    public function changeScheduledOrderTime(Request $request)
    {
        $rules = [
            'order_id'   => 'required|exists:orders,id',
            'recieve_at' => 'required|date_format:Y-m-d H:i|after:today'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $user = $request->user();
        $order = Order::find($request->order_id);
        if ($user->id != $order->user_id) {
            $err = [
                'key' => 'user_order',
                'value'=> 'مستخدم غير صحيح لهذا الطلب'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        $order->update([
            'recieve_at' => $request->recieve_at
        ]);

        // send notification to family to notify them about the changes happend with order recieve time.
        $devicesTokens = UserDevice::where('user_id', $order->provider_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        $title = 'تحديث علي الطلب';
        $body = 'قام المستخدم بتحديث وقت التسليم بالوقت  التالي '.$request->recieve_at;
        if ($devicesTokens) {
            sendMultiNotification($title, $body, $devicesTokens, $order->id);
        }
        saveNotification($order->provider_id, $title, $body, $order->id, 2);

        return ApiController::respondWithSuccess('تم تعديل الوقت برجاء الانتظار قليلا');
    }

    /**
     * get user finished orders
     *
     * @param Request $request
     * @return Json $response
     */
    public function finishedOrders()
    {
        $user = request()->user();
        $orders = $user->userOrders()->where('status','3')->get();
        if($orders->count() == 0){
            $err = [
                'key' => 'user_finished_orders',
                'value'=> 'لا يوجد طلبات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        return ApiController::respondWithSuccess(OrderResource::collection($orders));
    }

    /**
     * user rate prodcut and write notes if exists
     *
     * @param Request $request
     * @return Json $response
     */
    public function rateProduct(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'rate'       => 'required|in:1,2,3,4,5',
            'notes'      => 'sometimes|min:8'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        $created = ProductRate::updateOrCreate([
            'user_id'    => $request->user()->id,
            'product_id' => $request->product_id],[
            'notes'      => $request->notes,
            'rate'       => $request->rate,
        ]);
        if ($created) {
            return ApiController::respondWithSuccess('تم حفظ تقييمك للمنتج بنجاح');
        } else {
            return ApiController::respondWithServerErrorArray();
        }
    }
}
