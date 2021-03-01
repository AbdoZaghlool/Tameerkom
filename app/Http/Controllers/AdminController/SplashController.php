<?php

namespace App\Http\Controllers\AdminController;

use App\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SplashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $splashs = Slider::latest()->get();
        return view('admin.splashs.index', compact('splashs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.splashs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image'       => 'required|mimes:jpeg,jpg,png|max:2048',
            'product_id'  => 'nullable|exists:products,id',
            'link'        => 'nullable',
        ]);
        if ($request->product_id && $request->link) {
            flash('لا يمكن اضافة الرابط والمنتج معا')->error();
            return back()->withInput();
        }
        Slider::create([
            'image'       => UploadImage($request->image, 'slide', 'uploads/sliders'),
            'product_id'  => $request->product_id == 0 ? null : $request->product_id,
            'link'        => $request->link,
        ]);
        flash('تم اضافة البانر بنجاح');
        return redirect()->route('splashs.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Splash  $splash
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $splash)
    {
        return view('admin.splashs.edit', compact('splash'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Splash  $splash
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slider $splash)
    {
        $this->validate($request, [
            'product_id' => 'nullable|exists:products,id',
            'image'       => 'nullable|mimes:jpeg,jpg,png|max:2048'
        ]);

        if ($request->product_id && $request->link) {
            flash('لا يمكن اضافة الرابط والمنتج معا')->error();
            return back()->withInput();
        }

        $splash->update([
            'product_id' => $request->product_id ?? $splash->product_id,
            'link'        => $request->link ?? $splash->link,
            'image'       => $request->image == null ? $splash->image : UploadImageEdit($request->image, 'slide', 'uploads/sliders', $splash->image)
        ]);
        flash('تم تعديل البانر بنجاح');
        return redirect()->route('splashs.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Splash  $splash
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slider $splash)
    {
        $image = $splash->image;
        $splash->delete();
        if (file_exists(public_path('uploads/sliders/' . $image))) {
            unlink(public_path('uploads/sliders/' . $image));
        }
        flash('تم الحذف بنجاح');
        return back();
    }
}