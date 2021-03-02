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
            'phone_number'          => 'required|unique:users',
            'name'                  => 'required|max:255',
            'email'                 => 'nullable|email|unique:users',
            'image'                 => 'required_if:type,1|mimes:jpeg,bmp,png,jpg|max:3000',
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'active'                => 'nullable',
            'city_id'               => 'required_if:type,1',
            'latitude'              => 'required',
            'longitude'             => 'required',
            'commercial_record'     => 'required_if:type,1',
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
            'city_id' => $request->city_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'commercial_record' => $request->commercial_record,
        ]);

        flash('تم اضافة المستخدم بنجاح')->success()->important();

        if ($type == 0) {
            return redirect('admin/users');
        } elseif ($type == 1) {
            return redirect('admin/users/providers');
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
            'phone_number'      => 'required|unique:users,phone_number,' . $id,
            'name'              => 'required|max:255',
            'image'             => 'nullable|mimes:jpeg,bmp,png,jpg|max:3000',
            'password'          => 'nullable|string|min:6',
            'password_confirmation' => 'required_with:password|same:password',
            'active'            => 'nullable',
            'email'             => 'nullable|email|unique:users,email,' . $id,
            'city_id'           => 'required_if:type,1',
            'latitude'          => 'required',
            'longitude'         => 'required',
            'commercial_record' => 'required_if:type,1',
        ];

        $this->validate($request, $rules);

        $user = User::findOrFail($id);
        $user->update([
            'phone_number'      => $request->phone_number,
            'name'              => $request->name,
            'active'            => $request->active,
            'password'          => $request->password == null ? $user->password : Hash::make($request->password),
            'email'             => $request->email,
            'image'             => $request->file('image') == null ? $user->image : UploadImage($request->file('image'), 'image', '/uploads/users'),
            'city_id'           => $request->city_id,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'commercial_record' => $request->commercial_record,
        ]);

        flash()->success('تم تعديل بيانات المستخدم')->important();
        if ($type == 0) {
            return redirect('admin/users');
        } elseif ($type == 1) {
            return redirect('admin/users/providers');
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
     * @param int $id
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
        flash('تم الحذف بنجاح')->warning()->important();
        return back();
    }
}