<?php

namespace App\Http\Controllers\AdminController;

use App\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Topic::latest()->get();
        return view('admin.topics.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.topics.create');
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
            'name' =>'required'
        ]);
        Topic::create($request->except('_token'));
        flash('تم اضافة القسم بنجاح');
        return redirect()->route('topics.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function edit(Topic $topic)
    {
        return view('admin.topics.edit', compact('topic'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Topic  $topic`
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Topic $topic)
    {
        $this->validate($request, [
            'name' =>'required'
        ]);
        $topic->update($request->except('_token'));
        flash('تم تعديل القسم بنجاح');
        return redirect()->route('topics.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);
        if ($topic->families()->count() > 0) {
            flash('لا يمكن حذف التصنيف لان به اسر منتجة')->error()->important();
            return back();
        }
        $topic->delete();
        flash('تم حذف التصنيف')->warning();
        return back();
    }
}
