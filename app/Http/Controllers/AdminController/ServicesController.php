<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Service::latest()->get();
        return view('admin.services.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.services.create');
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
            'name'     => 'required',
            'details'  => 'required',
            'price'    => 'required',
            'type'     => 'required',
            'duration' => 'required',
            'image'    => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        Service::create([
            'name'     => $request->name,
            'details'  => $request->details,
            'price'    => $request->price,
            'type'     => $request->type,
            'duration' => $request->duration,
            'image'    => $request->image == null ? null : UploadImage($request->image, 'service', 'uploads/services'),
        ]);
        flash('تم اضافة ' . $request->name . ' بنجاح')->success();
        return redirect()->route('services.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $this->validate($request, [
            'name'     => 'required',
            'details'  => 'required',
            'price'    => 'required',
            'type'     => 'required',
            'duration' => 'required',
            'image'    => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        // dd($service);
        $service->update([
            'name'     => $request->name == null ? $service->name : $request->name,
            'details'  => $request->details == null ? $service->details : $request->details,
            'price'    => $request->price == null ? $service->price : $request->price,
            'type'     => $request->type == null ? $service->type : $request->type,
            'duration' => $request->duration == null ? $service->duration : $request->duration,
            'image'    => $request->image == null ? $service->image : UploadImageEdit($request->image, 'service', 'uploads/services', $service->image),
        ]);
        flash('تم تعديل '.$service->name.' بنجاح')->success();
        return redirect()->route('services.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service->delete();
        flash('تم الحذف بنجاح')->success();
        return back();
    }
}
