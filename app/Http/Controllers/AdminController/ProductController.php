<?php

namespace App\Http\Controllers\AdminController;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($provider_id = null)
    {
        return view('admin.products.index', ['products'=>Product::with('provider')->provider($provider_id)->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules($request->method()));
        $product = Product::create($request->only('name','details','price','category_id','provider_id'));
        $this->createAdditions($product,$request);
        flash('تم اضافة المنتج بنجاح')->success();
        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit',['product'=>$product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->validate($request, $this->rules($request->method()));
        $product->update($request->only('name','details','price','category_id','provider_id'));
        $this->createAdditions($product,$request);
        flash('تم تعديل المنتج بنجاح')->success();
        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $images = $product->pictures();
        $product->delete();
        foreach ($images as $image) {
            if(file_exists(public_path('uploads/products/'.$image)));
            unlink(public_path('uploads/products/'.$image));
        }
        flash('تم الحذف بنجاح');
        return back();
    }

    protected function rules($method)
    {
        if($method == "PUT"){
            return [
                'name'                 => 'required|min:3',
                'details'              => 'nullable',
                'price'                => 'required|numeric',
                'category_id'          => 'required|exists:categories,id',
                'provider_id'          => 'required|exists:users,id',
                'type'    => 'required_if:category_id,1,2,3|array',
                'size'    => 'required_if:category_id,1,3|array',
                'shape'    => 'required_if:category_id,1|array',
                'pressure'    => 'required_if:category_id,2|array',
                'area'      => 'required_if:category_id,2|array',
                'place'    => 'required_if:category_id,3|array',
                'image'                => 'nullable|array',
                'image.*'              => 'mimes:jpeg,bmp,png,jpg|max:2048',
            ];
        }

        return [
            'name'                 => 'required|min:3',
            'details'              => 'nullable',
            'price'                => 'required|numeric',
            'category_id'          => 'required|exists:categories,id',
            'provider_id'          => 'required|exists:users,id',
            'type'    => 'required_if:category_id,1,2,3|array',
            'size'    => 'required_if:category_id,1,3|array',
            'shape'    => 'required_if:category_id,1|array',
            'pressure'    => 'required_if:category_id,2|array',
            'area'      => 'required_if:category_id,2|array',
            'place'    => 'required_if:category_id,3|array',
            'image'                => 'required|array',
            'image.*'              => 'mimes:jpeg,bmp,png,jpg|max:2048',
        ];
    }

    protected function createAdditions($product,$request)
    {
        // save product images to storage
        if ($request->file('image') != null) {
            foreach ($request->file('image') as $value) {
                $product->pictures()->create([
                    'image' => UploadImage($value, 'product'. '_' . randNumber(3), 'uploads/products')
                ]);
            }
        }

        // dd($request->all(),'fromfun');
        $request['property_value_id'] = null;
        if($request->category_id == 1){
            //iron
            $request['property_value_id'] = array_merge($request->shape,$request->type,$request->size);
        }elseif($request->category_id == 2){
            // khara
            $request['property_value_id'] = array_merge($request->pressure,$request->type,$request->area);
        }elseif($request->category_id == 3){
            //tabok
            $request['property_value_id'] = array_merge($request->size,$request->type,$request->place);
        }

        // dd($request->property_value_id);
        //save prodcut property valeus to storage
        if ($request->property_value_id != null) {
            $product->values()->sync($request->property_value_id);
        }
    }
}