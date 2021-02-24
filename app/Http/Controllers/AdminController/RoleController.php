<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Permission;
use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $records = Role::get();
        return view('admin.roles.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all()->pluck('id', 'display_name');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $rules = [
            'name'            => 'required',
            'permission_list' => 'required|array',
        ];
        $this->validate($request, $rules);
        $record = Role::create($request->only('name', 'display_name'));
        $record->permissions()->attach($request['permission_list']);
        flash('تم اضافة الرتبة بنجاح');
        return redirect('/admin/roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permissions = Permission::all();
        $model = Role::findOrFail($id);
        return view('admin.roles.edit', compact('model', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Illuminate\Http\Request $request
     * @param int $id
     * @return Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'permission_list' => 'required|array',
        ];
        $validator = $this->validate($request, $rules);
        if ($validator) {
            $record = Role::findOrFail($id);

            $record->update($request->all());
            $record->permissions()->sync($request->input('permission_list'));
            $record->save();
            flash('تم تعديل الرتبة بنجاح');
            return redirect()->route('roles.index');
        } else {
            return $php_errormsg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = Role::findOrFail($id);
        if ($record->delete()) {
            return redirect('/role');
        } else {
            return $php_errormsg;
        }
    }
}