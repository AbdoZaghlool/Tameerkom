<?php

namespace App\Http\Controllers\Api;

use App\FamilySlider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Validator;

class SliderController extends Controller
{
    private $family;

    /**
     * Create a new SliderController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $userType = Auth::user()->type;
            if ($userType != '1') {
                $err = [
                    'key' => 'family',
                    'value'=> 'مستخدم غير صحيح'
                ];
                return ApiController::respondWithErrorObject(array($err));
            }
            $this->family = Auth::user();
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = $this->family->familySliders;
        if ($sliders->count() == 0) {
            $err = [
                'key' => 'sliders',
                'value'=> 'لا توجد بيانات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        $arr = [];
        foreach ($sliders as $slider) {
            array_push($arr, [
                'id'    => $slider->id,
                'name'  => $slider->name,
                'image' => asset('uploads/family_sliders/'.$slider->image),
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $rules = [
            'name'    => 'required',
            'image'   => 'required|array',
            'image.*' => 'mimes:jpeg,bmp,png,jpg|max:2048',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        if ($this->family->familySliders()->count() >= 5) {
            $err = [
                'key' => 'sliders_count',
                'value'=> 'لا يمكنك اضافة اكثر من 5'
            ];
            return ApiController::respondWithErrorArray($err);
        }

        if ($request->image != null) {
            foreach ($request->image as $key => $value) {
                $this->family->familySliders()->create([
                    'name'  => $request->name,
                    'image' => UploadImage($value, 'slider'.randNumber(2), 'uploads/family_sliders'),
                ]);
            }
        }

        return ApiController::respondWithSuccess('تم اضافة البانر');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FamilySlider  $familySlider
     * @return \Illuminate\Http\Response
     */
    public function show(FamilySlider $familySlider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FamilySlider  $familySlider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FamilySlider $familySlider)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FamilySlider  $familySlider
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $familySlider = $this->family->familySliders()->where('id', $id)->first();
        if ($familySlider == null) {
            $err = [
                'key' => 'sliders',
                'value'=> 'لا يوجد بيانات حاليا'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        $image = $familySlider->image;
        $familySlider->delete();
        if (file_exists(public_path('uploads/family_sliders/'.$image))) {
            unlink(public_path('uploads/family_sliders/'.$image));
        }
        return ApiController::respondWithSuccess('تم حذف الصورة بنجاح');
    }
}
