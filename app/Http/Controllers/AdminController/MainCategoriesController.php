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
        ]);
        Category::create([
            'name' => $request->name,
        ]);
        flash('تم اضافة القسم بنجاح')->success();
        return redirect()->route('main-categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $Category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $Category)
    {
        //
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
        ]);
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
        ]);
        flash('تم تعديل القسم بنجاح')->success();
        return redirect()->route('main-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $Category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $Category)
    {
        $check = $Category->products == null ? 0 : $Category->products;
        if ($check->count() > 0) {
            flash('لا يمكن حذف القسم لان به منتجات')->error();
            return back();
        }
        $Category->delete();
        flash('تم حذف القسم بنجاح')->warning();
        return back();
    }
}
