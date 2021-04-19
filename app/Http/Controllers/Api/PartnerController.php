<?php

namespace App\Http\Controllers\Api;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProviderResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\User as UserResource;
use App\Rate;
use Validator;
use Auth;

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
     * update family information
     *
     * @param Request $request
     * @return App\Http\Resources\User $userResource
     */
    public function updateInfo(Request $request)
    {
        $rules = [
            'name'             => 'sometimes|max:255',
            'brief'            => 'sometimes|max:255',
            'image'            => 'sometimes|mimes:jpeg,bmp,png,jpg|max:2048',
            'email'            => 'sometimes|email|unique:users,email,' . $this->family->id,
            'region_id'        => 'sometimes|exists:regions,id',
            'city_id'          => 'sometimes|exists:cities,id',
            'tax_number'       => 'sometimes',
            'latitude'         => 'sometimes',
            'longitude'        => 'sometimes',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $user = $this->family;
        $user->update([
            'name'       => $request->name ?? $user->name,
            'brief'      => $request->brief ?? $user->brief,
            'email'      => $request->email ?? $user->email,
            'region_id'  => $request->region_id ?? $user->region_id,
            'city_id'    => $request->city_id ?? $user->city_id,
            'latitude'   => $request->latitude ?? $user->latitude,
            'longitude'  => $request->longitude ?? $user->longitude,
            'tax_number' => $request->tax_number ?? $user->tax_number,
            'image'      => $request->image ==null ? $user->image : UploadImageEdit($request->image, 'user', 'uploads/users', $user->image)
        ]);

        return ApiController::respondWithSuccess(new UserResource($user));
    }

    /**
     * get family orders depends on status
     *  0 => active, 1 => done, 2 => canceled
     *
     * @param Integer $status
     * @return void
     */
    public function myOrders()
    {
        $orders = $this->family->providerOrders()->latest()->get();
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
     * get family commissions
     *
     *
     * @param Integer $status
     * @return void
     */
    public function myCommissions()
    {
        $orders = $this->family->providerOrders()->where('status', '1')->latest()->get();
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
     * upload commission payment image
     *
     * @param Request $request
     * @return void
     */
    public function uploadImage(Request $request)
    {
        $rules = [
            'order_id' => 'required|numeric|exists:orders,id',
            'payment_image' => 'required|mimes:jpg,jpeg,png|max:5000',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $order = Order::find($request->order_id);
        
        $order->update([
            'payment_image' => UploadImage($request->file('payment_image'), 'payments', '/uploads/payment_images')
        ]);
        return ApiController::respondWithSuccess('تم رفع الصورة بنجاح الي الادارة');
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
}