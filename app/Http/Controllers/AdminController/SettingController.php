<?php

namespace App\Http\Controllers\AdminController;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class SettingController extends Controller
{
    /**
     * get the main date of site to view or update it
     *
     * @return void
     */
    public function index()
    {
        $settings = Setting::first();
        return view('admin.settings.index', compact('settings'));
        //
    }

    /**
     * update settings to the storage
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email'        => 'required|email',
            'phone'        => 'required',
            'commission'   => 'required|numeric|between:0,0.2',
            'logo'         => 'nullable|mimes:png,jpg,jpeg,pmb|max:3072',
            'face_url'     => 'nullable',
            'twiter_url'   => 'nullable',
            'snapchat_url' => 'nullable',
            'insta_url'    => 'nullable',
            'youtube_url'  => 'nullable',
        ]);
        $record = Setting::find(1);
        $record->update($request->except('logo'));
        if (!$request->logo == null) {
            $record->update([
                'logo'=> UploadImageEdit($request->logo, 'dashboard-logo', 'images', $record->logo)
            ]);
        }
        return back()->with('success', 'تم حفظ البيانات بنجاح');
    }
}