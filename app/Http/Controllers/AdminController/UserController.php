<?php

namespace App\Http\Controllers\AdminController;

use App\Cart;
use App\Events\ClientRegisterdEvent;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the customers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::whereType('0')->latest()->get();
        return view('admin.users.users', compact('users'));
    }

    /**
     * display a listing of families
     *
     * @return Response
     */
    public function providers()
    {
        $users = User::where('type', '1')->latest()->get();
        return view('admin.users.providers', compact('users'));
    }

    /**
     * display a listing of drivers
     *
     * @return Response
     */
    public function drivers()
    {
        $users = User::where('type', '2')->latest()->get();
        return view('admin.users.drivers', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        if ($type == 0) {
            return view('admin.users.create_user');
        } elseif ($type == 1) {
            return view('admin.users.create_provider');
        } elseif ($type == 2) {
            return view('admin.users.create_driver');
        } else {
            abort(404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $type)
    {
        $rules = [
            'phone_number' => 'required|unique:users',
            'name' => 'required|max:255',
            'image' => 'required_if:type,1,2|mimes:jpeg,bmp,png,jpg|max:3000',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'active' => 'nullable',
            'email' => 'nullable|email|unique:users',
            'region_id' => 'required_if:type,1,2',
            'city_id' => 'required_if:type,1,2',
            'latitude' => 'required',
            'longitude' => 'required',
            'tax_number' => 'required_if:type,1',
            'topic_id' => 'required_if:type,1|array|exists:topics,id',
            'bank_name' => 'required_if:type,1,2',
            'bank_user_name' => 'required_if:type,1,2',
            'account_number' => 'required_if:type,1,2',
            'insurance_number' => 'required_if:type,1,2',
            'driver_license' => 'required_if:type,2',
            'type_id' => 'required_if:type,2',
            'car_license' => 'required_if:type,2|mimes:jpeg,jpg,png|max:3000',
            'identity_number' => 'required_if:type,1,2',
            'work_start_at' => 'required_if:type,1',
            'work_end_at' => 'required_if:type,1',
        ];
        $this->validate($request, $rules);

        $user = User::create([
            'phone_number' => $request->phone_number,
            'name' => $request->name,
            'type' => $type,
            'active' => $request->active ?? 0,
            'password' => Hash::make($request->password),
            'image' => $request->image == null ? 'default.png' :UploadImage($request->file('image'), 'user', '/uploads/users'),
            'email' => $request->email,
            'region_id' => $request->region_id,
            'city_id' => $request->city_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'tax_number' => $request->tax_number,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'insurance_number' => $request->insurance_number,
            'driver_license' => $request->driver_license,
            'type_id' => $request->type_id,
            'car_license' => $request->file('car_license') == null ? null : UploadImage($request->file('car_license'), 'car', '/uploads/cars'),
            'identity_number' => $request->identity_number,
            'work_start_at' => $request->work_start_at,
            'work_end_at' => $request->work_end_at,
            'available' => 1,
            'verified' => 0,
        ]);

        // create wallet for any user registerd and cart, default address only for clients
        $wallet = ApiController::createUserWallet($user->id);
        if ($request->type == 0) {
            $cart = Cart::create(['user_id' => $user->id]);
            event(new ClientRegisterdEvent($user));
        }

        if ($request->type == 1 && $request->topic_id != null) {
            $user->topics()->sync($request->topic_id);
        }

        flash('تم اضافة المستخدم بنجاح')->success();

        if ($type == 0) {
            return redirect('admin/users');
        } elseif ($type == 1) {
            return redirect('admin/users/providers');
        } else {
            return redirect('admin/users/drivers');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrfail($id);
        if ($user->type == '0') {
            return view('admin.users.edit_user', compact('user'));
        } elseif ($user->type == '1') {
            return view('admin.users.edit_provider', compact('user'));
        } else {
            return view('admin.users.edit_driver', compact('user'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $type)
    {
        $rules = [
            'phone_number' => 'required|unique:users,phone_number,' . $id,
            'name' => 'required|max:255',
            'image' => 'nullable|mimes:jpeg,bmp,png,jpg|max:3000',
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'required_with:password|same:password',
            'active' => 'nullable',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'region_id' => 'required_if:type,1,2',
            'city_id' => 'required_if:type,1,2',
            'latitude' => 'required',
            'longitude' => 'required',
            'tax_number' => 'required_if:type,1',
            'topic_id' => 'required_if:type,1|array',
            'bank_name' => 'required_if:type,1,2',
            'bank_user_name' => 'required_if:type,1,2',
            'account_number' => 'required_if:type,1,2',
            'insurance_number' => 'required_if:type,1,2',
            'driver_license' => 'required_if:type,2',
            'type_id' => 'required_if:type,2',
            'car_license' => 'sometimes|required_if:type,2|mimes:jpeg,jpg,png|max:3000',
            'identity_number' => 'required_if:type,1,2',
            'work_start_at' => 'required_if:type,1',
            'work_end_at' => 'required_if:type,1',
        ];
        $this->validate($request, $rules);

        $user = User::findOrFail($id);
        $user->update([
            'phone_number' => $request->phone_number,
            'name' => $request->name,
            'active' => $request->active,
            'password' => $request->password == null ? $user->password : Hash::make($request->password),
            'email' => $request->email,
            'image' => $request->file('image') == null ? $user->image : UploadImage($request->file('image'), 'image', '/uploads/users'),
            'region_id' => $request->region_id,
            'city_id' => $request->city_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'tax_number' => $request->tax_number,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'insurance_number' => $request->insurance_number,
            'driver_license' => $request->driver_license,
            'type_id' => $request->type_id,
            'car_license' => $request->file('car_license') == null ? $user->car_license : UploadImage($request->file('car_license'), 'car', '/uploads/cars'),
            'identity_number' => $request->identity_number,
            'work_start_at' => $request->work_start_at,
            'work_end_at' => $request->work_end_at,
        ]);

        if ($request->type == 1 && $request->topic_id != null) {
            $user->topics()->sync($request->topic_id);
        }

        flash()->success('تم تعديل بيانات المستخدم');
        if ($type == 0) {
            return redirect('admin/users');
        } elseif ($type == 1) {
            return redirect('admin/users/providers');
        } else {
            return redirect('admin/users/drivers');
        }
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
        $users = User::findOrfail($id);
        $users->password = Hash::make($request->password);
        $users->save();
        return back()->with('information', 'تم تعديل كلمة المرور المستخدم');
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
        ]);
        $users = User::findOrfail($id);
        $users->active = $request->active;
        $users->save();
        return redirect()->back()->with('information', 'تم تعديل اعدادات المستخدم');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $image = $user->image;
        $user->delete();
        if (file_exists(public_path('uploads/users/') . $image) && $image != 'default.png') {
            unlink(public_path('uploads/users/') . $image);
        }
        flash('تم الحذف بنجاح')->warning();
        return back();
    }
}
