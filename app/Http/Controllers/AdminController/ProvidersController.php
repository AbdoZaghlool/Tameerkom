<?php

namespace App\Http\Controllers\AdminController;

use App\User;
use App\Rate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = User::where('type', 1)
            ->orWhere('type', 2)
            ->orWhere('type', 3)
            ->get();
        return view('admin.providers.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.providers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        //'name','type','phone_number','email','country_id','region_id','city_id','active','blocked','verified','image','latitude','longitude','password'
        $this->validate($request, [
            'phone'                 => 'required|unique:users,phone_number',
            'name'                  => 'required|max:50',
            'country_id'               => 'required',
            'image'                 => 'nullable|mimes:jpeg,bmp,png,jpg|max:5000',
            'password'              => 'required|confirmed|string|min:6',
            // 'brief'                 => 'required|max:255',
            // 'city_id'               => 'required',
            // 'service_id*'            => 'required',
            // 'password_confirmation' => 'required|same:password',
            // 'active'                => 'required',
            // 'longitude'             => 'required',
            // 'latitude'              => 'required',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'type'         => 1,
            'phone_number'  => $request->phone,
            'country_id' => $request->country_id,
            'password'     => \Hash::make($request->password),
            'image'        => $request->file('image') == null ? 'default.png' : UploadImage($request->file('image'), 'image', '/uploads/users'),
        ]);

        // dd($user);

        flash('تم اضافة مزود الخدمة بنجاح')->success();
        return redirect()->route('providers.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(User $provider)
    {
        // dd($provider);
        $cat_id = $provider->services->first() == null ? 0 : $provider->services->first()->main_category_id;
        return view('admin.providers.edit', compact('provider', 'cat_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $provider_id)
    {

        $this->validate($request, [
            'phone' => 'required|numeric|unique:users,phone_number,' . $provider_id,
            'name' => 'required|max:50',
            'country_id'               => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:5000',
        ]);
        // dd($request->all());
        $provider = User::findOrfail($provider_id);
        // dd($request->all());
        $provider->update([
            'phone_number' => $request->phone,
            'name' => $request->name,
            'country_id' => $request->country_id,
            'image' => $request->file('image') == null ? $provider->image : UploadImage($request->file('image'), 'image', '/uploads/users'),
        ]);
        flash('تم تعديل بيانات المستخدم')->success();
        return redirect()->route('providers.index');
    }

    /**
     * update password to the storage
     *
     * @param Request $request
     * @param User $id
     * @return void
     */
    public function update_pass(Request $request, $id)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ]);
        $provider = User::findOrFail($id);
        $provider->password = \Hash::make($request->password);
        $provider->save();
        flash('تم تعديل كلمة المرور المستخدم')->success();
        return back();
    }

    /**
     * update privacy option to the storage
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function update_privacy(Request $request, $id)
    {
        $this->validate($request, [
            'active' => 'required',
            'verified' => 'required',
        ]);
        $provider = User::findOrFail($id);
        $provider->active = $request->active;
        $provider->verified = $request->verified;
        $provider->save();
        flash('تم تعديل اعدادات المستخدم')->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $provider = User::findOrFail($id);
        $image = $provider->image;
        $provider->delete();
        if ($image != 'default.png' && file_exists(public_path('uploads/users/' . $image))) {
            unlink(public_path('uploads/users/' . $image));
        }
        flash('تم الحذف بنجاح')->success();
        return back();
    }
}
