<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\City;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Provider as ProviderResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Product as ProductResource;
use App\Product;
use App\Region;
use App\Slider;
use App\User;
use Illuminate\Http\Request;
use Validator;

class MainController extends Controller
{
    /**
     * get provider by id
     *
     * @param int $id
     * @return void
     */
    public function userById($id)
    {
        $user = User::find($id);
        if ($user) {
            return ApiController::respondWithSuccess(new ProviderResource($user));
        }
        return ApiController::respondWithServerErrorArray();
    }

    /**
     * get application splashs images
     *
     * @return Json response
     */
    public function splashs()
    {
        $data = Slider::latest()->get();
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
                'id'          => (int) $slider->id,
                'product_id'  => (int) $slider->product_id,
                'link'        => (string) $slider->link,
                'image'       => asset('uploads/sliders/' . $slider->image),
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * get all regions which has cities,
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
                'id'          => $value->id,
                'name'        => $value->name,
                'region_id'   => (int)$region_id,
                'region_name' => $value->region->name,
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * get all categories
     *
     * @return Json response
     */
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
     * get the whole main category in our app
     *
     * @param int $id
     * @return json resopnes with categories
     */
    public function mainCat(Request $request, $user_id = null)
    {
        $categories = Category::with('products', 'products.values', 'products.values.property')->get();
        
        if ($categories->count() == 0) {
            $err = [
                'key' => 'categories',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorObject(array($err));
        }

        $request['user_id'] = $user_id;
        return ApiController::respondWithSuccess(CategoryResource::collection($categories));
    }

    /**
     * search storage with user keyword
     * first search in provider to find any matching provider by name,
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
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        // search in providers
        $data = Product::with('provider')->whereHas('provider', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->keyword . '%');
        })->get();

        $data = $data->filter(function ($product) {
            return $product['provider']['blocked'] == 0;
        });
        if ($data->count() > 0) {
            return ApiController::respondWithSuccess(ProductResource::collection($data));
        }
        // end search in providers

        // search in products
        $data = Product::with('provider')->where('name', 'like', '%' . $request->keyword . '%')
            ->orWhere('details', 'like', '%' . $request->keyword . '%')
            ->get();
        $data = $data->filter(function ($product) {
            return $product['provider']['blocked'] == 0;
        });
        if ($data->count() > 0) {
            return ApiController::respondWithSuccess(ProductResource::collection($data));
        }
        // end search in products

        // no matching data in database
        $err = [
                'key' => 'search_result',
                'value' => 'لا يوجد بيانات تماثل ' . $request->keyword,
            ];
        return ApiController::respondWithErrorArray($err);
    }
}