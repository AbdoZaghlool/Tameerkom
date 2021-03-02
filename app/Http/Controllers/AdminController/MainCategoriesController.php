<?php

namespace App\Http\Controllers\AdminController;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Category::latest()->get();
        return view('admin.main-categories.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.main-categories.create');
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
            'name' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:3072',
        ]);
        Category::create([
            'name' => $request->name,
            'image' => UploadImage($request->file('image'), 'category', '/uploads/categories')
        ]);
        flash('تم اضافة القسم بنجاح')->success()->important();
        return redirect()->route('main-categories.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $Category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.main-categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $Category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:3072',
        ]);
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'image' => $request->file('image') == null ? $category->image : UploadImageEdit($request->file('image'), 'category', '/uploads/categories', $category->image)
        ]);
        flash('تم تعديل القسم بنجاح')->success()->important();
        return redirect()->route('main-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $Category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $check = $category->products == null ? 0 : $category->products;
        if ($check->count() > 0) {
            flash('لا يمكن حذف القسم لان به منتجات')->error();
            return back();
        }
        $image = $category->image;
        
        $category->delete();
        if (file_exists(public_path('/uploads/categories/'.$image))) {
            unlink(public_path('/uploads/categories/'.$image));
        }
        flash('تم حذف القسم بنجاح')->warning()->important();
        return back();
    }
}