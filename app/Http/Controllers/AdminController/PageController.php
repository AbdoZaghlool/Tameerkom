<?php

namespace App\Http\Controllers\AdminController;

use App\AboutUs;
use App\TermsCondition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class PageController extends Controller
{
    /**
     * get view for about us page
     * @return void
     */
    public function about()
    {
        $settings = AboutUs::first();
        return view('admin.pages.about', compact('settings'));
    }

    /**
     * update about us content
     * @param Request $request
     * @return void
     */
    public function store_about(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|string',
        ]);
        AboutUs::updateOrCreate(['id'=>1], [
            'content'=>$request->content
        ]);
        return Redirect::back()->with('success', 'تم حفظ البيانات بنجاح');
    }

    /**
     * get view terms and conditions
     * @return void
     */
    public function terms()
    {
        $settings = TermsCondition::first();
        return view('admin.pages.terms', compact('settings'));
    }

    /**
     * update content of terms and conditions
     * @param Request $request
     * @return void
     */
    public function store_terms(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|string',
        ]);
        TermsCondition::updateOrCreate(['id'=>1], [
                'content'=>$request->content
            ]);
        return Redirect::back()->with('success', 'تم حفظ البيانات بنجاح');
    }
}