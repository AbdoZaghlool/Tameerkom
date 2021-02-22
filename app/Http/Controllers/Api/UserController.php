<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\Provider as ProviderResource;
// use App\Http\Resources\ProviderResource;
use App\Http\Resources\User as AppUser;
use App\Order;

use App\ProductRate;
use Validator;
use Auth;
use App\User;
use App\UserDevice;

class UserController extends Controller
{
    protected $user;
    /**
     * Create a new PartnerController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:api', 'api']);
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $userType = $user->type;
            if ($userType != '0') {
                $err = [
                    'key' => 'user',
                    'value'=> 'مستخدم غير صحيح'
                ];
                return ApiController::respondWithErrorArray($err);
            }
            $this->user = $user;
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
     *    0 => active, 1 => compeleted, 3 => canceled
     *
     * @param Integer $status
     * @return OrderResource response
     */
    public function myOrders()
    {
        $user = request()->user();
        $orders = $user->userOrders()->where('status', '!=', '4')->latest()->get();
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
     * add provider to user favourites
     *
     * @param Request $request
     * @return Json response
     */
    public function favouriteProvider(Request $request)
    {
        $rules = [
            'provider_id' => 'required|exists:users,id',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        $exists = $this->user->favouritUsers()->where('provider_id', $request->provider_id)->first();
        if ($exists) {
            $err = [
                'key' => 'favourite_providers',
                'value' => 'المصنع موجود بالفعل في مفضلاتك'
            ];
            return ApiController::respondWithErrorArray($err);
        }

        $this->user->favouritUsers()->attach($request->provider_id);
        return ApiController::respondWithSuccess('تمت الاضافة للمفضلة بنجاح');
    }

    /**
     * remove provider from user favourites
     *
     * @param Request $request
     * @return Json response
     */
    public function unFavouriteProvider(Request $request)
    {
        $rules = [
            'provider_id' => 'required|exists:favourite_users,provider_id',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $this->user->favouritUsers()->detach($request->provider_id);

        return ApiController::respondWithSuccess('تم حذف المزود من المفضلة بنجاح');
    }

    /**
     * get all user favourites
     *
     * @param Request $request
     * @return Json response
     */
    public function getFavouriteProviders()
    {
        $favourites = $this->user->favouritUsers;
        if ($favourites->count() == 0) {
            $err = [
                'key' => 'favourite_providers',
                'value' => 'لا يوجد بيانات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        return ApiController::respondWithSuccess(ProviderResource::collection($favourites));
    }


    /**
     * add provider to user favourites
     *
     * @param Request $request
     * @return Json response
     */
    public function favouriteProduct(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        $exists = $this->user->favouritProducts()->where('product_id', $request->product_id)->first();
        if ($exists) {
            $err = [
                'key' => 'favourite_product',
                'value' => 'المنتج موجود بالفعل في مفضلاتك'
            ];
            return ApiController::respondWithErrorArray($err);
        }

        $this->user->favouritProducts()->attach($request->product_id);
        return ApiController::respondWithSuccess('تمت الاضافة للمفضلة بنجاح');
    }

    /**
     * remove provider from user favourites
     *
     * @param Request $request
     * @return Json response
     */
    public function unFavouriteProduct(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:favourites,product_id',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $this->user->favouritProducts()->detach($request->product_id);

        return ApiController::respondWithSuccess('تم حذف المنتج من المفضلة بنجاح');
    }

    /**
     * get all user favourites
     *
     * @param Request $request
     * @return Json response
     */
    public function getFavouriteProducts()
    {
        $favourites = $this->user->favouritProducts;
        // dd($favourites);
        if ($favourites->count() == 0) {
            $err = [
                'key' => 'favourite_products',
                'value' => 'لا يوجد بيانات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        return ApiController::respondWithSuccess(ProductResource::collection($favourites));
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
            'product_id' => $request->product_id], [
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