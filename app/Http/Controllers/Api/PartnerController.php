<?php

namespace App\Http\Controllers\Api;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FamilyResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\User as UserResource;
use App\OrderOffer;
use App\ProductRate;
use App\Product;
use App\Rate;
use App\Service;
use App\Setting;
use App\User;
use App\UserDevice;
use Validator;
use Auth;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    private $family;
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
            if ($userType != '1') {
                $err = [
                    'key' => 'family',
                    'value'=> 'مستخدم غير صحيح'
                ];
                return ApiController::respondWithErrorArray($err);
            }
            $this->family = Auth::user();
            return $next($request);
        });
    }

    /**
     * update work time for family
     *
     * @param Request $request
     * @return Json response
     */
    public function updateWorkTime(Request $request)
    {
        $rules = [
            'work_start_at' => 'required',
            'work_end_at'   => 'required|after:work_start_at',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $this->family->update([
            'work_start_at' => $request->work_start_at,
            'work_end_at'   => $request->work_end_at,
        ]);
        return ApiController::respondWithSuccess('تم تعديل وقت العمل بنجاح');
    }

    /**
     * update family information
     *
     * @param Request $request
     * @return App\Http\Resources\User $userResource
     */
    public function updateInfo(Request $request)
    {
        $rules = [
            'name'             => 'sometimes|max:255',
            'brief'             => 'sometimes|max:255',
            'image'            => 'sometimes|mimes:jpeg,bmp,png,jpg|max:2048',
            'email'            => 'sometimes|email|unique:users,email,' . $this->family->id,
            'region_id'        => 'sometimes|exists:regions,id',
            'city_id'          => 'sometimes|exists:cities,id',
            'topic_id'         => 'sometimes|array|exists:topics,id',
            'latitude'         => 'sometimes',
            'longitude'        => 'sometimes',
            'tax_number'       => 'sometimes',
            'bank_name'        => 'sometimes',
            'bank_user_name'   => 'sometimes',
            'account_number'   => 'sometimes',
            'insurance_number' => 'sometimes',
            'identity_number'  => 'sometimes',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $user = $this->family;
        $user->update([
            'name'             => $request->name == null ? $user->name :$request->name,
            'brief'             => $request->brief == null ? $user->brief :$request->brief,
            'email'            => $request->email == null ? $user->email :$request->email,
            'region_id'        => $request->region_id == null ? $user->region_id :$request->region_id,
            'city_id'          => $request->city_id == null ? $user->city_id :$request->city_id,
            'latitude'         => $request->latitude == null ? $user->latitude :$request->latitude,
            'longitude'        => $request->longitude == null ? $user->longitude :$request->longitude,
            'tax_number'       => $request->tax_number == null ? $user->tax_number :$request->tax_number,
            'bank_name'        => $request->bank_name == null ? $user->bank_name :$request->bank_name,
            'bank_user_name'   => $request->bank_user_name == null ? $user->bank_user_name :$request->bank_user_name,
            'account_number'   => $request->account_number == null ? $user->account_number :$request->account_number,
            'insurance_number' => $request->insurance_number == null ? $user->insurance_number :$request->insurance_number,
            'identity_number'  => $request->identity_number == null ? $user->identity_number :$request->identity_number,
            'image'            => $request->image == null ? $user->image:UploadImageEdit($request->image, 'user', 'uploads/users', $user->image)
        ]);

        if ($request->topic_id != null) {
            $user->topics()->sync($request->topic_id);
        }

        return ApiController::respondWithSuccess(new UserResource($user));
    }

    /**
     * get family orders depends on status
     *  0 => new, 1 => active, 2 => done, 3 => canceled
     *
     * @param Integer $status
     * @return void
     */
    public function myOrders()
    {
        $orders = $this->family->familyOrders()->where('type_id', 1)->latest()->get();
        if ($orders->count() == 0) {
            $err = [
                'key' => 'orders',
                'value'=> 'لا يوجد طلبات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        return ApiController::respondWithSuccess(OrderResource::collection($orders));
    }

    /**
     * get family orders depends on status
     *  0 => new, 1 => active, 2 => done, 3 => canceled
     *
     * @param Integer $status
     * @return void
     */
    public function myScheduledOrders(Request $request)
    {
        $rules = ['status'      => 'sometimes|in:0,1,2'];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $orders = $this->family->familyOrders()
            ->providerStatus($request->status)
            ->where('type_id', 2)->latest()->get();
        if ($orders->count() == 0) {
            $err = [
                'key' => 'orders',
                'value'=> 'لا يوجد طلبات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        return ApiController::respondWithSuccess(OrderResource::collection($orders));
    }

    /**
     * order receivment date is not suitable for family
     *
     * @param Request $request
     * @return Json response
     */
    public function refuseScheduledOrders(Request $request)
    {
        $rules = [
            'order_id'      => 'required|exists:orders,id',
            'suitable_date' => 'required|date_format:Y-m-d H:i',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $order = Order::find($request->order_id);
        if ($this->family->id != $order->provider_id) {
            $err = [
                'key' => 'family',
                'value'=> 'مزود غير صحيحة'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        // send notification to user to update order time
        $devicesTokens =  UserDevice::where('user_id', $order->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        $title = 'رد المزودينة ';
        $body = 'هذا الوقت غير مناسب لتسليم الطلب لا يمكن الطلب قبل ' . $request->suitable_date;
        if ($devicesTokens) {
            sendMultiNotification($title, $body, $devicesTokens, $order->id);
        }
        saveNotification($order->user_id, $title, $body, $order->id, 1);
        $order->update([
            'recieve_at' => $request->suitable_date
        ]);
        $arr = [
            'key'   => 'refuse_order_time',
            'value' => 'تم ارسال اشعار للعميل بالوقت المناسب',
        ];
        return ApiController::respondWithSuccess(array($arr));
    }

    /**
     * accept the order with it's time
     *
     * @param Request $request
     * @return void
     */
    public function acceptScheduledOrders(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        //check if the correct family for order or not.
        $order = Order::find($request->order_id);
        if ($this->family->id != $order->provider_id) {
            $err = [
                'key' => 'family',
                'value'=> 'مزود غير صحيحة'
            ];
            return ApiController::respondWithErrorArray($err);
        }

        if ($order->provider_status != '0') {
            $err = [
                'key' => 'order_type',
                'value'=> 'حالة الطلب لا تسمح بهذة العملية'
            ];
            return ApiController::respondWithErrorArray($err);
        }

        // update order status for user to be accepted
        $order->update([
            'status'          => '1',
            'provider_status' => '1',
        ]);



        $distance = Setting::pluck('distance')->first() ?? 2500;
        $family = $order->provider;
        $address = $order->address;
        $lat = $family->latitude;
        $lon = $family->longitude;
        $userLat = $address->latitude;
        $userLon = $address->longitude;
        // $distanceInBetween = distanceBetweenTowPlaces($lat, $lon, $userLat, $userLon); // distance between user place and family place
        $users = User::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude) ) ) ) AS distance', [$lat, $lon, $lat])
            ->having('distance', '<=', $distance)
            ->where('type', '2')
            ->whereActive(1)
            ->where('available', 1)
            ->whereIn('type_id', [2,3])
            ->get();


        $devicesTokens =  UserDevice::where('user_id', $order->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        $title = 'قبول الطلب';
        $body = 'تم قبول طلبك من قبل المزودينة';
        if ($devicesTokens) {
            sendMultiNotification($title, $body, $devicesTokens, $order->id);
        }
        saveNotification($order->user_id, $title, $body, $order->id, 1);

        // if there is any drivers send them notification to offer prices
        if ($users->count() > 0) {
            foreach ($users as $user) {
                OrderOffer::create([
                    'order_id'  => $order->id,
                    'driver_id' => $user->id,
                ]);
                $devicesTokens =  UserDevice::where('user_id', $user->id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                $title = 'طلب جديد في محيطك';
                $body = 'هنالك طلب جديد برجاء التحقق منه';
                if ($devicesTokens) {
                    sendMultiNotification($title, $body, $devicesTokens, $order->id);
                }
                saveNotification($user->id, $title, $body, $order->id, 1);
            }
            $arr = [
                'key'      => 'drivers_notified',
                'value'    => 'تم قبول الطلب وارسال اشعار للسائقين المتاحين ',
                'order_id' => $order->id
            ];
            return ApiController::respondWithSuccess(array($arr));
        } else {
            //there is no drivers in our region right now return to user to offer him recive from family by himself

            $devicesTokens =  UserDevice::where('user_id', $order->user_id)
                ->get()
                ->pluck('device_token')
                ->toArray();
            $title = 'no_drivers';
            $body = 'لا يوجد سائقين حاليا هل تريد الاستلام بنفسك من مقر المزودينة؟';
            if ($devicesTokens) {
                sendMultiNotification($title, $body, $devicesTokens, $order->id);
            }
            saveNotification($order->user_id, $title, $body, $order->id, 1);
            $err = [
                'key'      => 'لا يوجد مندوبين',
                'value'    => 'تم قبول الطلب واشعار العميل ',
                'order_id' => $order->id
            ];
            return ApiController::respondWithSuccess($err);
        }
    }

    /**
     * post family request to subscribe one of our services
     *
     * @param Request $request
     * @return Json $response
     */
    public function subscribe(Request $request)
    {
        $rules = [
            'service_id' => 'required|exists:services,id',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $service = Service::find($request->service_id);
        $wallet = $this->family->wallet;
        // check if the family subscribed to this service or not
        // if ($this->family->subscriptions()->where('service_id', $request->service_id)->first() != null) {
        //     $err = [
        //         'key'   => 'service_subscriped',
        //         'value' => 'انت مشترك في هذه الخدمة بالفعل'
        //     ];
        //     return ApiController::respondWithErrorArray($err);
        // }
        // check if there is enough money in family wallet or not
        if ((!$wallet->cash > 0) || $service->price > $wallet->cash) {
            $err = [
                'key' => 'wallet.not_enough_money',
                'value'=> 'لا يوجد رصيد كافي في محفظتك برجاء الشحن ثم الطلب مرة اخرى'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        // withdrow cash from wallet and save family service to storage
        $walletUpdated =  $wallet->update([
            'cash' => $wallet->cash - $service->price
        ]);
        // create reference for user with the process done
        $this->family->histories()->create([
            'title' => 'خصم قيمة الاشتراك في خدمة '.$service->name,
            'price' => $service->price
        ]);
        $this->family->subscriptions()->create([
            'service_id' => $service->id
        ]);
        // send notification to family that the subscription done
        return ApiController::respondWithSuccess('تم الاشتراك في خدمة '.$service->name.' بنجاح');
    }

    /**
     * family get comments and rates.
     *
     * @return Json response
     */
    public function usersComments()
    {
        $rates = Rate::where('to_user_id', $this->family->id)->get();
        if ($rates->count() == 0) {
            $err = [
                'key'   => 'comments&rates',
                'value' => 'لا يوجد بيانات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        $arr = [];
        foreach ($rates as $one) {
            array_push($arr, [
                'id'         => (int)$one->id,
                'from_user'  => $one->rateFrom != null  ? (string)$one->rateFrom->name : 'مستخدم غير موجود',
                'user_photo' => $one->rateFrom !== null ? asset('uploads/users/'.$one->rateFrom->image) : 'مستخدم غير موجود',
                'order_id'   => $one->order == null ? 0 : (int)$one->order->id,
                'rate'       => (int)$one->rate,
                'comment'    => (string)$one->note,
                'created_at' => $one->created_at->format('Y-m-d H:i')
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * family get comments and rates for products
     *
     * @return Json response
     */
    public function productsComments()
    {
        // $products = Product::with('rates')->whereHas('rates')->where('provider_id', request()->user()->id)->get();
        $products = request()->user()->products()->with('rates')->whereHas('rates')->get();

        if ($products->count() == 0) {
            $err = [
                'key'   => 'products_comments&rates',
                'value' => 'لا يوجد بيانات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        $arr = [];
        foreach ($products as $product) {
            $rates = [];
            foreach ($product->rates as $rate) {
                array_push($rates, [
                    'rate_id'         => (int)$rate->id,
                    'from_user'  => (string)$rate->user->name,
                    'user_photo'    => asset('uploads/users/'.$rate->user->image),
                    'rate'          => (int)$rate->rate,
                    'comment'       => (string)$rate->note,
                    'created_at'    => $rate->created_at->format('Y-m-d H:i')
                ]);
            }
            array_push($arr, [
                'product_id'    => (int)$product->id,
                'product_name'  => (string)$product->name,
                'product_photo' => asset('uploads/products/'.$product->pictures()->first()->image),
                'rates'         => $rates
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * family see it's store as the users
     *
     * @return Json response
     */
    public function viewMyStore()
    {
        $sliders = $this->family->familySliders;
        $sliders->filter(function ($slide) {
            return $slide->image = asset('uploads/family_sliders/'.$slide->image);
        });
        $data = [
            'family'  => new FamilyResource($this->family),
            'sliders' => $sliders,
        ];
        return ApiController::respondWithSuccess(array($data));
    }
}
