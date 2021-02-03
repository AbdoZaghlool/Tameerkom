<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\City;
use App\Events\ChangeAvailabliltyEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\FamilyResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Offer as OfferResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Product as ProductResource;
use App\Order;
use App\OrderOffer as Offer;
use App\Product;
use App\Region;
use App\Service;
use App\Slider;
use App\Topic;
use App\Type;
use App\User;
use Illuminate\Http\Request;
use Validator;

class MainController extends Controller
{
    public function userById($id)
    {
        $user = User::find($id);
        if ($user) {
            return ApiController::respondWithSuccess(new UserResource($user));
        }
        return ApiController::respondWithServerErrorArray();
    }

    public function userType(Request $request)
    {
        $rules = [
            'phone_number' => 'required|exists:users',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $user = User::where('phone_number', $request->phone_number)->first();
        return ApiController::respondWithSuccess($user->getType());
    }

    public function orderType(Request $request)
    {
        $rules = [
            'order_id' => 'required|exists:orders,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $order = Order::find($request->order_id);
        return ApiController::respondWithSuccess(new OrderResource($order));
    }

    public function orderOffer(Request $request)
    {
        $rules = [
            'offer_id' => 'required|exists:order_offers,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $order = Offer::find($request->offer_id);
        return ApiController::respondWithSuccess(new OfferResource($order));
    }

    /**
     * get application splashs images
     *
     * @return Json response
     */
    public function splashs()
    {
        $data = Slider::orderBy('id', 'DESC')->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'splashs',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorNOTFoundObject(array($err));
        }
        $arr = [];
        foreach ($data as $slider) {
            array_push($arr, [
                'id' => (int) $slider->id,
                'provider_id' => (int) $slider->provider_id,
                'link' => (string) $slider->link,
                'image' => asset('uploads/sliders/' . $slider->image),
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * get all regions by country id,
     * @param Country $country_id
     * @return json response
     */
    public function regions()
    {
        $data = Region::whereHas('cities')->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'regions',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorNOTFoundObject(array($err));
        }
        $arr = [];
        foreach ($data as $value) {
            array_push($arr, [
                'id' => $value->id,
                'name' => $value->name,
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * get the cities of given region
     * @param Region $region_id
     * @return json response
     */
    public function cities($region_id)
    {
        $data = City::with('region')->where('region_id', $region_id)->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'cities',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorNOTFoundObject(array($err));
        }
        $arr = [];
        foreach ($data as $value) {
            array_push($arr, [
                'id' => $value->id,
                'name' => $value->name,
                'region_name' => $value->region->name,
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     *  type of orders and products
     *
     * @return void
     */
    public function types()
    {
        $data = Type::select('id', 'type')->get();
        return ApiController::respondWithSuccess($data);
    }

    public function allTopics()
    {
        $data = Topic::select('id', 'name')->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'topics',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorNOTFoundObject(array($err));
        }
        return ApiController::respondWithSuccess($data);
    }

    public function allCategories()
    {
        $data = Category::select('id', 'name')->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'categories',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorNOTFoundObject(array($err));
        }
        return ApiController::respondWithSuccess($data);
    }

    /**
     * get all topics add by admin
     *
     * @return Josn response
     */
    public function topics($topic_id, $lat, $long)
    {
        $data = Topic::with('families')->whereHas('families', function ($q) {
            $q->with('familySliders');
        })->get();
        // dd($data);
        // filter data depends on selected category is all or spesefic topic
        if ($topic_id != 0) {
            $data = $data->filter(function ($topic, $key) use ($topic_id) {
                return $topic->id == $topic_id;
            });
        }
        // dd($data);
        if ($data->count() == 0) {
            $err = [
                'key' => 'topics',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorArray($err);
        }
        $arr = [];
        foreach ($data as $topic) {
            $familiesRegisterd = [];
            foreach ($topic->families as $family) {
                // dd($family);
                array_push($familiesRegisterd, [
                    'id' => (int) $family->id,
                    'name' => $family->name,
                    'brief' => $family->brief ?? '',
                    'start_at' => $family->work_start_at ?? '',
                    'end_at' => $family->work_end_at ?? '',
                    'region_name' => $family->region->name ?? '',
                    'city_name' => $family->city->name ?? '',
                    'available' => (int) $family->available,
                    'rate' => $family->getRateValue(),
                    'orders_count' => $family->familyOrders->count(),
                    'porducts_count' => $family->products->count(),
                    'distance' => distanceBetweenTowPlaces($lat, $long, $family->latitude, $family->longitude),
                    'image' => asset('uploads/users/' . $family->image),
                    'sliders' => $family->familySliders()->pluck('image')->transform(function ($slide) {
                        return $slide = asset('uploads/family_sliders/' . $slide);
                    }),
                ]);
            }
            array_push($arr, [
                'id' => $topic->id,
                'name' => $topic->name,
                'families' => $familiesRegisterd,
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * get the whole main category in our app
     *
     * @param User $provider_id
     * @return json resopnes with categories
     */
    public function mainCat($provider_id)
    {
        $providerIds = User::where('type', '1')->pluck('id')->toArray();
        if (!in_array($provider_id, $providerIds)) {
            $err = [
                'key' => 'providers',
                'value' => 'لا يوجد اسره بهذا الرقم',
            ];
            return ApiController::respondWithErrorArray($err);
        }
        // $data = Category::with('products')->whereHas('products', function ($query) use ($provider_id) {
        // $query->where('provider_id', $provider_id);
        // })->orderBy('created_at', 'DESC')->get();
        // dd($data);
        $products = Product::with('provider')->where('provider_id', $provider_id)->latest()->get();

        if ($products->count() == 0) {
            $err = [
                'key' => 'categories',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorObject(array($err));
        }

        // return ApiController::respondWithSuccess(CategoryResource::collection($data));
        return ApiController::respondWithSuccess(ProductResource::collection($products));
    }

    /**
     * update family or driver available status
     *
     * @param Request $request
     * @return Json response
     */
    public function updateAvailable(Request $request)
    {
        $rules = [
            'available' => 'required|in:0,1',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $user = $request->user();
        if ($user == null || ($user->type == '0')) {
            $err = [
                'key' => 'not_valid_user',
                'values' => 'لا يوجد مستخدم حاليا',
            ];
            return ApiController::respondWithErrorArray($err);
        }

        $user->update([
            'available' => $request->available,
        ]);

        event(new ChangeAvailabliltyEvent($user));

        $arr = [
            'key' => 'available',
            'values' => (int) $user->available,
        ];
        return ApiController::respondWithSuccess(array($arr));
    }

    /**
     * check discount code if existce or not and return new value after discount
     *
     * @param Request $request
     * @return json response
     */
    public function checkDiscountCode(Request $request)
    {
        $rules = [
            'family_id' => 'required',
            'price' => 'required',
            'code' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $family = User::whereType('1')->find($request->family_id);
        if ($family == null || $family->familyCoupons->count() == 0) {
            $err = [
                'key' => 'discount_code',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorObject(array($err));
        }
        $coupon = $family->familyCoupons()->where('name', 'like', $request->code)->first();
        if ($coupon == null) {
            $err = [
                'key' => 'discount_code',
                'value' => 'كود خصم غير صحيح',
            ];
            return ApiController::respondWithErrorObject(array($err));
        }
        // here the coupon exists and valid check for uses count to delete it or use it.
        $couponUsedCount = count(Order::where('coupon_id', $coupon->id)->get());
        if ($couponUsedCount >= $coupon->number_of_uses) {
            $coupon->delete();
            $err = [
                'key' => 'discount_code_finished',
                'value' => 'تخطي هذا الكوبون العدد الاقصي المسموح به',
            ];
            return ApiController::respondWithErrorArray($err);
        }
        // the code exists and valid for another uses
        $arr = [
            'coupon_id' => (int) $coupon->id,
            'percentage' => (double) $coupon->percentage,
            'price_after_discount' => (double) $request->price - (double) $coupon->percentage * (double) $request->price,
        ];
        return ApiController::respondWithSuccess(array($arr));
    }

    /**
     * get all services that application admin provide to families
     *
     * @return Json response
     */
    public function services()
    {
        $data = Service::latest()->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'services',
                'value' => 'لا يوجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorArray($err);
        }
        $arr = [];
        foreach ($data as $service) {
            array_push($arr, [
                'id' => (int) $service->id,
                'type' => $service->type == 0 ? 'service' : 'product',
                'name' => $service->name,
                'details' => (string) $service->details,
                'price' => (double) $service->price,
                'duration' => (string) $service->duration,
                'image' => $service->image == null ? "no_image" : asset('uploads/services/' . $service->image),
                'family_subscribed' => (boolean) request()->user()->subscriptions()->where('service_id', $service->id)->first(),
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * search storage with user keyword
     * first search in families to find any matching family by name,
     * if not we search in products names and details
     * if not then there is no data match
     *
     * @param Request $request
     * @return Json $resposne
     */
    public function search(Request $request)
    {
        $rules = [
            'keyword' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        // search in families ids
        if (is_numeric($request->keyword)) {
            // dd($request->keyword);
            $data = User::where('type', '1')->where('active', 1)->where('available', 1)
                ->where('id', $request->keyword)
                ->get();
            if ($data->count() > 0) {
                $request['search_type'] = 'family';
                return ApiController::respondWithSuccess(FamilyResource::collection($data));
            }
        } else {
            // search in families
            $data = User::where('type', '1')->where('active', 1)->where('available', 1)
                ->where('name', 'like', '%' . $request->keyword . '%')
                ->get();
            if ($data->count() > 0) {
                $request['search_type'] = 'family';
                return ApiController::respondWithSuccess(FamilyResource::collection($data));
            }
            // end search in families

            // search in products
            $data = Product::with('provider')->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('details', 'like', '%' . $request->keyword . '%')
                ->get();
            if ($data->count() > 0) {
                $request['search_type'] = 'product';
                $families = $data->unique('provider_id')->pluck('provider');
                return ApiController::respondWithSuccess(FamilyResource::collection($families));
            }
            // end search in products
        }

        // no matching data in database
        $err = [
            'key' => 'search_result',
            'value' => 'لا يوجد بيانات تماثل ' . $request->keyword,
        ];
        return ApiController::respondWithErrorArray($err);
    }
}