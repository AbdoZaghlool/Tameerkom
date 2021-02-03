<?php

namespace App\Http\Controllers\AdminController;

use App\AboutUs;
use App\TermsCondition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Text;
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
        AboutUs::first()->update([
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
        TermsCondition::first()->update([
                'content'=>$request->content
            ]);
        return Redirect::back()->with('success', 'تم حفظ البيانات بنجاح');
    }

    /**
     * get view for texts in application
     * @return void
     */
    public function texts()
    {
        $settings = Text::get();
        return view('admin.pages.texts', compact('settings'));
    }

    /**
     * update content of all texts in one shoot
     * @param Request $request
     * @return void
     */
    public function store_texts(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            '1'=>'nullable',
            '2'=>'nullable',
            '3'=>'nullable',
            '4'=>'nullable',
            '5'=>'nullable'
        ]);
        foreach ($request->except('_token') as $key => $value) {
            Text::find($key)->update([
                'content'=>$value
            ]);
        }
        flash('تم تعديل المحتويات بنجاح');
        return back();
    }
}
