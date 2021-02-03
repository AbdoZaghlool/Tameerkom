<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App;
use App\Contact;
use App\Device;
use App\Setting;
use Carbon\Carbon;

class ProfileController extends Controller
{

    /**
     * get the main data of app.
     *
     * @return void
     */
    public function settings()
    {
        $settings = Setting::first();
        $arr = [
            "id"                        => $settings->id,
            "email"                     => $settings->email,
            "phone"                     => $settings->phone,
            "distance"                  => (int)$settings->distance,
            "family_commission"         => (double)$settings->family_commission,
            "driver_commission"         => (double)$settings->driver_commission,
            "tax"                       => (double)$settings->tax,
            "delivery_time"             => (string)$settings->delivery_time,
            "offer_wait_time"           => (int)$settings->accept_order_time * 60,
            "family_offer_time"         => (int)$settings->family_offer_time * 60,
            "order_payment_time"        => (int)$settings->order_payment_time * 60,
            "scheduled_order_duration"  => (string)$settings->scheduled_order_duration,
            "min_value_withdrow_family" => (string)$settings->min_value_withdrow_family,
            "min_value_withdrow_driver" => (string)$settings->min_value_withdrow_driver,
            // "face_url"         => $settings->face_url,
            // "twiter_url"       => $settings->twiter_url,
            // "youtube_url"      => $settings->youtube_url,
            // "snapchat_url"     => $settings->snapchat_url,
            // "insta_url"        => $settings->insta_url,
            // "version"          => $settings->version,
        ];
        return ApiController::respondWithSuccess(array($arr));
    }

    /**
     * get the about us fields.
     *
     * @return void
     */
    public function about_us()
    {
        $about = App\AboutUs::first();
        $all = [
            // 'title'   => $about->title == null ? ' ' : $about->title,
            'content' => $about->content,
        ];
        return ApiController::respondWithSuccess(array($all));
    }

    /**
     * get terms and conditions
     *
     * @return void
     */
    public function terms_and_conditions()
    {
        $terms = App\TermsCondition::first();
        $all = [
            // 'title' => $terms->title  == null ? ' ' : $terms->title,
            'content' => $terms->content,
        ];
        return ApiController::respondWithSuccess(array($all));
    }

    /**
     * save the user messages to storage
     *
     * @param Request $request
     * @return void
     */
    public function contacts(Request $request)
    {
        $rules = [
            'name'    => 'required',
            'phone'   => 'required',
            'email'   => 'required|email',
            'message' => 'required|string|min:5|max:350'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        Contact::create([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'email'   => $request->email,
            'message' => $request->message,
        ]);
        return ApiController::respondWithSuccess('تم استلام طلبك سيتم الرد عليك في اقرب وقت');
    }

    /**
     *  pay money to charge personal pocket for users
     *
     * @param Request $request
     * @return Json response
     */
    public function charge_electronic_pocket(Request $request)
    {
        $rules = [
            'cash'           => 'required',
            'payment_method' => 'required|in:2,6,11',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $user  = $request->user();
        $pocket = $user->wallet;
        $cash = $request->cash;

        // dd($cash);

        $data = "{\"PaymentMethodId\":\"$request->payment_method\",\"CustomerName\": \"$user->name\",\"DisplayCurrencyIso\": \"SAR\",
        \"MobileCountryCode\":\"+966\",\"CustomerMobile\": \"$user->phone_number\",
        \"CustomerEmail\": \"email@mail.com\",\"InvoiceValue\": $cash,\"CallBackUrl\": \"http://homemade.tqnee.com/check-status\",
        \"ErrorUrl\": \"http://127.0.0.1:8000/check-status\",\"Language\": \"ar\",\"CustomerReference\" :\"ref 1\",
        \"CustomerCivilId\":12345678,\"UserDefinedField\": \"Custom field\",\"ExpireDate\": \"\",
        \"CustomerAddress\" :{\"Block\":\"\",\"Street\":\"\",\"HouseBuildingNo\":\"\",\"Address\":\"\",\"AddressInstructions\":\"\"},
        \"InvoiceItems\": [{\"ItemName\": \"$user->name\",\"Quantity\": 1,\"UnitPrice\": $cash}]}";

        $fatooraRes = MyFatoorah($data);
        $result = json_decode($fatooraRes);
        // dd($result);
        if ($result != null &&$result->IsSuccess === true) {
            $pocket->update([
                'invoice_id' => $result->Data->InvoiceId
            ]);
            $all = [];
            array_push($all, [
                'key'  => 'charge_electronic_pocket',
                'payment_url' => $result->Data->PaymentURL,
            ]);
            return ApiController::respondWithSuccess($all);
        } else {
            $err = [];
            array_push($err, [
                'key'  => 'charge_electronic_pocket',
                'value' => 'حدث خطأ ما برجاء المحاولة لاحقا',
            ]);
            return ApiController::respondWithErrorArray($err);
        }
    }

    /**
     * check user pocket charge if done or not
     *
     * @return void
     */
    public function fatooraStatus()
    {
        $PaymentId = \Request::query('paymentId');
        $resData = MyFatoorahStatus($PaymentId);
        $result = json_decode($resData);
        // dd($result);
        if ($result->IsSuccess === true && $result->Data->InvoiceStatus === "Paid") {
            $InvoiceId = $result->Data->InvoiceId;
            $check = App\Wallet::where('invoice_id', $InvoiceId)->first();

            $user = $check->user;
            if ($check) {
                $check->update([
                    'cash'   => $check->cash + $result->Data->InvoiceValue ,
                ]);
                $user->histories()->create([
                    'title' => 'شحن رصيد في المحفظة',
                    'price' => $result->Data->InvoiceValue
                ]);
                return redirect()->to('/fatoora/success');
            } else {
                return redirect()->to('/fatoora/error');
            }
        } else {
            return redirect()->to('/fatoora/error');
        }
    }

    /**
     * get user rate
     *
     * @return void
     */
    public function myRate(){
        $user = request()->user();
        $rate = $user->getRateValue();
        return ApiController::respondWithSuccess($rate);
    }

    /**
     * get user wallet cash money
     *
     * @return void
     */
    public function getWallet()
    {
        $user = request()->user();
        $wallet = $user->wallet;
        if ($wallet== null) {
            $arr = [
                'key' => 'wallet',
                'value' => 'عذرا حدث خطأ برجاء المحاولة لاحقا'
            ];
            return ApiController::respondWithErrorArray($arr);
        }
        $arr = [
            'id'   => $wallet->id,
            'cash' => $wallet->cash
        ];
        return ApiController::respondWithSuccess(array($arr));
    }

    /**
     * user send request to withdrow money and will be turned throw bank account
     *
     * @param Request $request
     * @return json response
     */
    public function pullMoney(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric|not_in:0',
            ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = $request->user();
        if ($user->wallet == null) {
            $arr = [
                    'key' => 'wallet',
                    'value' => 'عذرا حدث خطأ برجاء المحاولة لاحقا'
                ];
            return ApiController::respondWithErrorObject(array($arr));
        }
        
        if ($user->wallet->cash < $request->amount) {
            $arr = [
                    'key' => 'wallet_cash',
                    'value' => 'رصيدك الحالي لا يسمح باتمام العملية'
                ];
            return ApiController::respondWithErrorObject(array($arr));
        }
        if ($user->wallet->pull_request == 1) {
            $arr = [
                'key' => 'wallet',
                'value' => 'لقد قدمت طلبا بالفعل برجاء الانتظار حتي يتم الرد من الادارة'
            ];
            return ApiController::respondWithErrorObject(array($arr));
        }
        $updated = $user->wallet()->update([
            'pull_request' => 1,
            'amount' => $request->amount,
        ]);
        if ($updated == 0) {
            $arr = [
                    'key' => 'wallet',
                    'value' => 'عذرا حدث خطأ برجاء المحاولة لاحقا'
                ];
            return ApiController::respondWithErrorObject(array($arr));
        }
        $arr = [
            'key' => 'wallet',
            'value' => 'تم تقديم طلبك الي الادارة بنجاح'
        ];
        return ApiController::respondWithSuccess(array($arr));
    }

    /**
     * user histories and transactions
     *
     * @return void
     */
    public function histories()
    {
        $user = auth()->user();
        $data = $user->histories()->orderBy('created_at', 'DESC')->get();
        if ($data->count() == 0) {
            $arr = [
                'key' => 'histories',
                'value' => 'لا يوجد بيانات حاليا'
            ];
            return ApiController::respondWithErrorArray($arr);
        }
        $arr = [];
        foreach ($data as $history) {
            array_push($arr, [
                'id'         => $history->id,
                'title'      => $history->title,
                'price'      => (double)$history->price,
                'created_at' => $history->created_at == null ? '' : $history->created_at->format('Y-m-d H:i') ,
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }


    /**
     * save user device token to storage,
     *  @param Request $request,
     *  @return Json response
     */
    public function saveToken(Request $request)
    {
        $rules = ['token' => 'required'];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }
        $created = Device::updateOrCreate(['token'=>$request->token]);
        return $created
            ? ApiController::respondWithSuccess('تمت الاضافه بنجاح')
            : ApiController::respondWithServerErrorArray();
    }
}
