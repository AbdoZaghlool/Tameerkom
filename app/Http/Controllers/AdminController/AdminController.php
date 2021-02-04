<?php

namespace App\Http\Controllers\AdminController;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * get all admins
     *
     * @return void
     */
    public function index()
    {
        $data = Admin::latest()->get();
        return view('admin.admins.admins.index', compact('data'));
    }

    /**
     * get admin create form
     *
     * @return void
     */
    public function create()
    {
        return view('admin.admins.admins.create');
    }

    /**
     * store new admin to storage
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required',
            ]);
        $request['remember_token'] = Str::random(60);
        $request['password'] = Hash::make($request->password);
        Admin::create($request->all());
        return redirect(url('/admin/admins'))->with('msg', 'تم الاضافه بنجاح');
    }

    /**
     * get admin edit view
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        $data = Admin::find($id);
        return view('admin.admins.admins.edit', compact('data'));
    }

    /**
     * update admin info to storage
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //        dd($request->role);
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $id,
            'phone' => 'required',
        ]);
        $request['remember_token'] = Str::random(60);
        Admin::where('id', $id)->first()->update($request->all());
        return redirect(url('/admin/admins'))->with('msg', 'تم التعديل بنجاح');
    }

    /**
     * get admin profile page
     *
     * @return void
     */
    public function my_profile()
    {
        $data = Admin::find(Auth::id());
        return view('admin.admins.profile.profile', compact('data'));
    }

    /**
     * update admin profile info in storage
     *
     * @param Request $request
     * @return void
     */
    public function my_profile_edit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:admins,email,' . Auth::id(),
            'phone' => 'required',
        ]);
        $data = Admin::where('id', Auth::id())->update(['name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        return redirect(url('/admin/profile'))->with('msg', 'تم التعديل بنجاح');
    }

    /**
     * get admin password view
     *
     * @return void
     */
    public function change_pass()
    {
        return view('admin.admins.profile.change_pass');
    }

    /**
     * update admin password
     *
     * @param Request $request
     * @return void
     */
    public function change_pass_update(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ]);
        $updated = Admin::where('id', Auth::id())->update([
            'password' => Hash::make($request->password),
        ]);
        return redirect(url('/admin/profileChangePass'))->with('msg', 'تم التعديل بنجاح');
    }

    /**
     * delete admin from storage
     *
     * @param int $id
     * @return void
     */
    public function admin_delete($id)
    {
        Admin::where('id', $id)->delete();
        return back()->with('msg', 'تم الحذف بنجاح');
    }
}