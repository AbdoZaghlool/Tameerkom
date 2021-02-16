<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product as ProductResource;
use App\Picture;
use Auth;
use Validator;

class ProductController extends Controller
{
    private $provider;

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
                    'key' => 'provider',
                    'value'=> 'مستخدم غير صحيح'
                ];
                return ApiController::respondWithErrorObject(array($err));
            }
            $this->provider = Auth::user();
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
        $products = $this->provider->products()->with('values', 'values.property')->latest()->get();
        if ($products->count() ==0) {
            $err = [
                'key' => 'provider_products',
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
        $rules = [
            'name'                 => 'required|min:3',
            'details'              => 'nullable',
            'price'                => 'required|numeric',
            'category_id'          => 'required|exists:categories,id',
            'property_value_id'    => 'required|array',
            'image'                => 'required',
            'image.*'              => 'mimes:jpeg,bmp,png,jpg|max:2048',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        // save product to storage
        $product = $this->provider->products()->create([
            'name'             => $request->name,
            'details'          => $request->details ,
            'price'            => $request->price ,
            'category_id'      => $request->category_id ,
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

        //save prodcut property valeus to storage
        $values = $request->property_value_id;
        if ($values != null) {
            $product->values()->sync($values);
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
            'name'                 => 'sometimes|min:3',
            'details'              => 'sometimes',
            'price'                => 'sometimes|numeric',
            'category_id'          => 'sometimes|exists:categories,id',
            'property_value_id'    => 'sometimes|array',
            'image'                => 'sometimes',
            'image.*'              => 'mimes:jpeg,bmp,png,jpg|max:2048',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        $product = $this->provider->products()->where('id', $id)->first();
        if ($product == null) {
            $err = [
                'key' => 'provider_product_not_found',
                'value'=> 'هذا المنتج غير متوفر'
            ];
            return ApiController::respondWithErrorNOTFoundArray(array($err));
        }

        // update product to storage
        $product->update([
            'name'             => $request->name == null ? $product->name : $request->name,
            'details'          => $request->details  == null ? $product->details : $request->details,
            'price'            => $request->price  == null ? $product->price : $request->price,
            'category_id'      => $request->category_id  == null ? $product->category_id : $request->category_id,
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

        //save prodcut property valeus to storage
        $values = $request->property_value_id;
        if ($values != null) {
            $product->values()->sync($values);
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
        $product = $this->provider->products()->where('id', $id)->first();
        if ($product == null) {
            $err = [
                'key' => 'provider_product_not_found',
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
}