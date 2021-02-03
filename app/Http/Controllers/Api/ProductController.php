<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product as ProductResource;
use App\Picture;
use App\ProductAddition;
use App\Setting;
use Auth;
use Validator;

class ProductController extends Controller
{
    private $family;

    /**
     * Create a new ProductController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $userType = Auth::user()->type;
            if ($userType != '1') {
                $err = [
                    'key' => 'family',
                    'value'=> 'مستخدم غير صحيح'
                ];
                return ApiController::respondWithErrorObject(array($err));
            }
            $this->family = Auth::user();
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = $this->family->products()->latest()->get();
        if ($products->count() ==0) {
            $err = [
                'key' => 'family_products',
                'value'=> 'لا توجد منتجات حاليا'
            ];
            return ApiController::respondWithErrorObject(array($err));
        }
        return ApiController::respondWithSuccess(ProductResource::collection($products));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $limit = Setting::pluck('product_limit')->first();
        if ($this->family->products()->count() >= $limit) {
            if (is_null($this->family->subscriptions()->where('service_id', 2)->first())) {
                $err = [
                    'key'   => 'max_limit_of_products',
                    'value' => 'لقد تجاوزت الحد الاقصي للمنتجات المضافة'
                ];
                return ApiController::respondWithErrorArray($err);
            }
        }

        $rules = [
            'name'              => 'required|min:3',
            'details'           => 'nullable',
            'price'             => 'required|numeric',
            'sku'               => 'required|numeric',
            'type_id'           => 'required|exists:types,id',
            'category_id'       => 'required|exists:categories,id',
            'preparation_time'  => 'required_if:type_id,2,3',
            'main_additions[]'  => 'sometimes|array',
            'more_additions[]'  => 'sometimes|array',
            'image'             => 'required',
            'image.*'           => 'mimes:jpeg,bmp,png,jpg|max:2048',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        // save product to storage
        $product = $this->family->products()->create([
                'name'             => $request->name,
                'details'          => $request->details ,
                'price'            => $request->price ,
                'sku'              => $request->sku ,
                'type_id'          => $request->type_id ,
                'category_id'      => $request->category_id ,
                'preparation_time' => $request->preparation_time ,
            ]);

        // save product images to storage
        $images = $request->file('image');
        if ($images != null) {
            foreach ($images as $value) {
                $product->pictures()->create([
                    'image' => UploadImage($value, 'product'. '_' . randNumber(3), 'uploads/products')
                ]);
            }
        }

        //save prodcut main additions to storage
        $main_additions = $request->main_additions;
        if ($main_additions != null) {
            foreach ($main_additions as $add) {
                $product->additions()->create([
                    'type'  => '0',
                    'name'  => $add['name']??'t_n',
                    'price' => 0,
                ]);
            }
        }

        //save prodcut more additions to storage
        $more_additions = $request->more_additions;
        if ($more_additions != null) {
            foreach ($more_additions as $add) {
                $product->additions()->create([
                    'type'  => '1',
                    'name'  => $add['name'] ?? 't_n',
                    'price' => $add['price'] ?? 1,
                ]);
            }
        }
        return ApiController::respondWithSuccess('تم اضافة المنتج بنجاح');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name'              => 'sometimes|min:3',
            'details'           => 'sometimes',
            'price'             => 'sometimes|numeric',
            'sku'               => 'sometimes|numeric',
            'type_id'           => 'sometimes|exists:types,id',
            'category_id'       => 'sometimes|exists:categories,id',
            'preparation_time'  => 'sometimes|required_if:type_id,2,3',
            'main_additions[]'  => 'sometimes|array',
            'more_additions[]'  => 'sometimes|array',
            'image'             => 'sometimes',
            'image.*'           => 'mimes:jpeg,bmp,png,jpg|max:2048',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        $product = $this->family->products()->where('id', $id)->first();
        if ($product == null) {
            $err = [
                'key' => 'family_product_not_found',
                'value'=> 'هذا المنتج غير متوفر'
            ];
            return ApiController::respondWithErrorNOTFoundArray(array($err));
        }

        // update product to storage
        $product->update([
            'name'             => $request->name == null ? $product->name : $request->name,
            'details'          => $request->details  == null ? $product->details : $request->details,
            'price'            => $request->price  == null ? $product->price : $request->price,
            'sku'              => $request->sku  == null ? $product->sku : $request->sku,
            'type_id'          => $request->type_id  == null ? $product->type_id : $request->type_id,
            'category_id'      => $request->category_id  == null ? $product->category_id : $request->category_id,
            'preparation_time' => $request->preparation_time  == null ? $product->preparation_time : $request->preparation_time,
        ]);

        // save new product images to storage
        $images = $request->file('image');
        if ($images != null) {
            foreach ($images as $value) {
                $product->pictures()->create([
                        'image' => UploadImage($value, 'product'. '_' . randNumber(3), 'uploads/products')
                    ]);
            }
        }

        // save new prodcut main additions to storage
        $main_additions = $request->main_additions;
        if ($main_additions != null) {
            foreach ($main_additions as $add) {
                $product->additions()->create([
                        'type'  => '0',
                        'name'  => $add['name']??'t_n',
                        'price' => 0,
                    ]);
            }
        }

        // save new prodcut more additions to storage
        $more_additions = $request->more_additions;
        if ($more_additions != null) {
            foreach ($more_additions as $add) {
                $product->additions()->create([
                        'type'  => '1',
                        'name'  => $add['name'] ?? 't_n',
                        'price' => $add['price'] ?? 1,
                    ]);
            }
        }

        return ApiController::respondWithSuccess('تم تعديل المنتج بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = $this->family->products()->where('id', $id)->first();
        if ($product == null) {
            $err = [
                'key' => 'family_product_not_found',
                'value'=> 'هذا المنتج غير متوفر'
            ];
            return ApiController::respondWithErrorNOTFoundArray(array($err));
        }

        $imagesToBeDelete = Picture::where('product_id', $id)->pluck('image');
        $deleted = $product->delete();
        if ($deleted) {
            if ($imagesToBeDelete->count() > 0) {
                foreach ($imagesToBeDelete as $image) {
                    if (file_exists(public_path('uploads/products/'.$image))) {
                        unlink(public_path('uploads/products/'.$image));
                    }
                }
            }
            return ApiController::respondWithSuccess('تم حذف المنتج بنجاح');
        } else {
            return ApiController::respondWithServerErrorArray();
        }
    }

    /**
     * delete prodcut image by image id
     *
     * @param Picture $id
     * @return Json response
     */
    public function deleteImageById($id)
    {
        $image = Picture::find($id);
        if ($image == null) {
            $err = [
                'key' => 'product_image_not_found',
                'value'=> 'لا يوجد صورة بهذا الرقم'
            ];
            return ApiController::respondWithErrorNOTFoundArray(array($err));
        }

        $otherImages = $image->product->pictures()->where('id', '!=', $id)->first();
        if ($otherImages == null) {
            $err = [
                'key' => 'no other images',
                'value'=> 'لا يمكن حذف الصورة '
            ];
            return ApiController::respondWithErrorNOTFoundArray(array($err));
        }

        $path = $image->image;
        $deleted = $image->delete();
        if ($deleted) {
            if (file_exists(public_path('uploads/products/'.$path))) {
                unlink(public_path('uploads/products/'.$path));
            }
            return ApiController::respondWithSuccess('تم حذف الصورة بنجاح');
        } else {
            return ApiController::respondWithServerErrorArray();
        }
    }

    /**
     * delete prodcut image by image id
     *
     * @param Picture $id
     * @return Json response
     */
    public function deleteAdditionById($id)
    {
        $add = ProductAddition::find($id);
        if ($add == null) {
            $err = [
                'key' => 'product_addition_not_found',
                'value'=> 'لا يوجد اضافة بهذا الرقم'
            ];
            return ApiController::respondWithErrorNOTFoundArray(array($err));
        }

        $deleted = $add->delete();
        if ($deleted) {
            return ApiController::respondWithSuccess('تم حذف الاضافة بنجاح');
        } else {
            return ApiController::respondWithServerErrorArray();
        }
    }
}