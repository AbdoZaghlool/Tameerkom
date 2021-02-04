<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\City;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\FamilyResource;
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
     * @param [type] $id
     * @return void
     */
    public function userById($id)
    {
        $user = User::find($id);
        if ($user) {
            return ApiController::respondWithSuccess(new UserResource($user));
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
                'id' => $value->id,
                'name' => $value->name,
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
    public function mainCat($id = null)
    {
        $categories = Category::with('products')->filter($id)->latest()->get();

        if ($categories->count() == 0) {
            $err = [
                'key' => 'categories',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorObject(array($err));
        }

        return ApiController::respondWithSuccess(CategoryResource::collection($categories));
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