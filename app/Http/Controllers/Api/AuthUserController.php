<?php

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthUserController extends Controller
{
    /**
     * register user phone number to db and send activation code to be checked.
     *
     * @param Request $request
     * @return void
     */
    public function registerMobile(Request $request)
    {
        $rules = [
            'phone_number' => 'required|starts_with:05|digits:10',
            'type' => 'required|in:0,1,2',
            'app_signature' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $oldAccounts = User::where('phone_number', $request->phone_number)->get();
        if ($oldAccounts->count() > 0) {
            $oldTypes = $oldAccounts->pluck('type')->toArray();
            if (in_array($request->type, $oldTypes)) {
                $err = [
                    'key' => 'phone_number_exists_before', 'value' => 'هذا الرقم مسجل من قبل ',
                ];
                return ApiController::respondWithErrorArray($err);
            }
        }
    
        $result = substr($request->phone_number, 1);
        $phone = '00966' . $result;

        $code = mt_rand(1000, 9999);
        $jsonObj = array(
            'mobile' => 'tqnee.com.sa',
            'password' => '589935sa',
            'sender' => 'TQNEE',
            'numbers' => $phone,
            'msg' => '<#> كود التأكيد الخاص بك في هوم ميد هو :' . $code . ' لا تقم بمشاركة هذا الكود مع اي شخص ' . $request->app_signature,
            'msgId' => rand(1, 99999),
            'timeSend' => '0',
            'dateSend' => '0',
            'deleteKey' => '55348',
            'lang' => '3',
            'applicationType' => 68,
        );

        $result = $this->sendSMS($jsonObj);
        $created = App\PhoneVerification::updateOrCreate(['phone_number' => $request->phone_number], [
            'code' => $code,
        ]);
        return ApiController::respondWithSuccess('تم ارسال الكود بنجاح');
    }

    /**
     * verify user code if matchs our db code or not
     *
     * @param Request $request
     * @return void
     */
    public function register_phone_post(Request $request)
    {
        $rules = [
            'phone_number' => 'required|starts_with:05|digits:10',
            'code' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = App\PhoneVerification::where('phone_number', $request->phone_number)->orderBy('id', 'desc')->first();
        if ($user) {
            if ($user->code == $request->code) {
                $successLogin = [
                    'key' => 'message',
                    'value' => "كود التفعيل صحيح",
                ];
                $user->delete();
                return ApiController::respondWithSuccess($successLogin);
            } else {
                $errorsLogin = [
                    'key' => 'code',
                    'value' => trans('messages.error_code'),
                ];
                return ApiController::respondWithErrorClient(array($errorsLogin));
            }
        } else {
            $errorsLogin = [
                'key' => 'phone_number',
                'value' => 'رقم الهاتف غير صحيح',
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
    }

    /**
     * resend activation code to user
     *
     * @param Request $request
     * @return void
     */
    public function resend_code(Request $request)
    {
        $rules = [
            'phone_number' => 'required|starts_with:05|digits:10',
            'app_signature' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $code = mt_rand(1000, 9999);
        $result = substr($request->phone_number, 1);
        $phone = '00966' . $result;
        $jsonObj = array(
            'mobile' => 'tqnee.com.sa',
            'password' => '589935sa',
            'sender' => 'TQNEE',
            'numbers' => $phone,
            'msg' => '<#> كود التأكيد الخاص بك في هوم ميد هو :' . $code . ' لا تقم بمشاركة هذا الكود مع اي شخص ' . $request->app_signature,
            'msgId' => rand(1, 99999),
            'timeSend' => '0',
            'dateSend' => '0',
            'deleteKey' => '55348',
            'lang' => '3',
            'applicationType' => 68,
        );
        // دالة الإرسال JOSN
        $result = $this->sendSMS($jsonObj);

        $created = App\PhoneVerification::updateOrCreate(['phone_number' => $request->phone_number], [
            'code' => $code,
        ]);
        return $created
        ? ApiController::respondWithSuccess(trans('messages.success_send_code'))
        : ApiController::respondWithServerErrorObject();
    }

    /**
     * register user data to storage
     *
     * @param Request $request
     * @return UserResource
     */
    public function register(Request $request)
    {
        $rules = [
            'type' => 'required|in:0,1,2',
            'phone_number' => 'required|starts_with:05|digits:10',
            'name' => 'required|max:255',
            'brief' => 'sometimes',
            'image' => 'required_if:type,1,2|mimes:jpeg,bmp,png,jpg|max:3000',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'device_token' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'email' => 'sometimes|email|unique:users',
            'tax_number' => 'sometimes', // required_if:type,1'
            'topic_id' => 'sometimes|array|exists:topics,id', // required_if:type,1'
            'work_start_at' => 'sometimes', // required_if:type,1'
            'work_end_at' => 'sometimes', // required_if:type,1'
            'region_id' => 'sometimes', // required_if:type,1,2'
            'city_id' => 'sometimes', // required_if:type,1,2'
            'bank_name' => 'sometimes', // required_if:type,1,2'
            'bank_user_name' => 'sometimes', // required_if:type,1,2'
            'account_number' => 'sometimes', // required_if:type,1,2'
            'insurance_number' => 'sometimes', // required_if:type,1,2'
            'identity_number' => 'sometimes', // required_if:type,1,2'
            'driver_license' => 'sometimes', // required_if:type,2'
            'driver_type_id' => 'sometimes', // required_if:type,2'
            'car_license' => 'sometimes|mimes:jpeg,jpg,png|max:3000', // required_if:type,2'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $oldAccounts = User::where('phone_number', $request->phone_number)->get();
        if ($oldAccounts->count() > 0) {
            $oldTypes = $oldAccounts->pluck('type')->toArray();
            if (in_array($request->type, $oldTypes)) {
                $err = [
                    'key' => 'phone_number_exists_before', 'value' => 'هذا الرقم مسجل من قبل ',
                ];
                return ApiController::respondWithErrorArray($err);
            }
        }
    
        if ($request->type != 0) {
            $oldIdNumbers = User::where('identity_number', $request->identity_number)->get();
            if ($oldIdNumbers->count() > 0) {
                $oldTypes = $oldIdNumbers->pluck('type')->toArray();
                if (in_array($request->type, $oldTypes)) {
                    $err = [
                    'key' => 'identity_number_exists_before', 'value' => 'رقم الهوية مسجل من قبل ',
                ];
                    return ApiController::respondWithErrorArray($err);
                }
            }
        }

        $user = App\User::create([
            'phone_number' => $request->phone_number,
            'name' => $request->name,
            'brief' => $request->brief,
            'type' => $request->type,
            'password' => Hash::make($request->password),
            'image' => $request->image == null ? null : UploadImage($request->file('image'), 'user', '/uploads/users'),
            'email' => $request->email,
            'region_id' => $request->region_id,
            'city_id' => $request->city_id,
            'active' => $request->type == 0 ? 1 : 0,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'tax_number' => $request->tax_number,
            'bank_name' => $request->bank_name,
            'bank_user_name' => $request->bank_user_name,
            'account_number' => $request->account_number,
            'insurance_number' => $request->insurance_number,
            'driver_license' => $request->driver_license,
            'type_id' => $request->driver_type_id,
            'car_license' => $request->file('car_license') == null ? null : UploadImage($request->file('car_license'), 'car', '/uploads/cars'),
            'identity_number' => $request->identity_number,
            'work_start_at' => $request->work_start_at,
            'work_end_at' => $request->work_end_at,
        ]);
        Auth::guard('api')->check(['phone_number' => $request->phone_number, 'password' => $request->password]);
        $token = generateApiToken($user->id, 15);
        $user->update(['api_token' => $token]);
        $user->devices()->create([
            'device_token' => $request->device_token,
        ]);

        if ($request->type == 1 && $request->topic_id != null) {
            $user->topics()->attach($request->topic_id);
        }

        if ($request->type == 0) {
            event(new \App\Events\ClientRegisterdEvent($user));
        }

        $wallet = ApiController::createUserWallet($user->id);
        if ($request->type == 0) {
            $cart = App\Cart::create(['user_id' => $user->id]);
        }
        $savedUser = array(new UserResource($user));
        return $user ? ApiController::respondWithSuccess($savedUser) : ApiController::respondWithServerErrorArray();
    }

    /**
     * log user into application.
     *
     * @param Request $request
     * @return UserResource obj
     */
    public function login(Request $request)
    {
        $rules = [
            'phone_number' => 'required',
            'password' => 'required',
            'device_token' => 'required',
            'type' => 'required|in:0,1,2',
            'latitude' => 'sometimes|required',
            'longitude' => 'sometimes|required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        if ($check = Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password, 'type' => $request->type])) {
            $user = User::where('phone_number', $request->phone_number)->where('type', $request->type)->first();

            $token = generateApiToken($user->id, 15);
            $user->update([
                'api_token' => $token,
                'latitude' => $request->latitude ?? $user->latitude,
                'longitude' => $request->longitude ?? $user->longitude,
            ]);

            if (Auth::user()->active == 0) {
                $errors = [
                    'key' => 'message',
                    'value' => trans('messages.Sorry_your_membership_was_stopped_by_Management'),
                ];
                return ApiController::respondWithErrorArray($errors);
            }
            //save_device_token....
            if ($request->device_token != null) {
                $created = $user->devices()->updateOrCreate([
                    'device_token' => $request->device_token,
                ]);
                // $created = UserDevice::updateOrCreate(['user_id'=>$user->id],['device_token'=>$request->device_token]);
            }

            return $created
            ? ApiController::respondWithSuccess(array(new UserResource($user)))
            : ApiController::respondWithServerErrorArray();
        } else {
            $user = User::where('phone_number', $request->phone_number)->first();
            if ($user == null) {
                $phone = ['key' => 'phone_number', 'value' => 'رقم الهاتف غير صحيح'];
                return ApiController::respondWithErrorClient(array($phone));
            } elseif ($user->type != $request->type) {
                $type = ['key' => 'user_type', 'value' => 'مستخدم غير صحيح'];
                return ApiController::respondWithErrorClient(array($type));
            } else {
                $password = ['key' => 'password', 'value' => ' الرقم السري غير صحيح'];
                return ApiController::respondWithErrorClient(array($password));
            }
        }
    }

    /**
     * search for user phone and send verification code if exists
     *
     * @param Request $request
     * @return void
     */
    public function forgetPassword(Request $request)
    {
        $rules = [
            'phone_number' => 'required|numeric',
            'app_signature' => 'required',
            'type' => 'required|in:0,1,2',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = App\User::where('phone_number', $request->phone_number)->where('type', $request->type)->first();

        if ($user) {
            if ($user->type != $request->type) {
                $errors = [
                    'key' => 'user_type',
                    'value' => 'نوع المستخدم غير صحيح',
                ];
                return ApiController::respondWithErrorArray($errors);
            }
            $code = mt_rand(1000, 9999);
            $result = substr($request->phone_number, 1);
            $phone = '00966' . $result;
            $jsonObj = array(
                'mobile' => 'tqnee.com.sa',
                'password' => '589935sa',
                'sender' => 'TQNEE',
                'numbers' => $phone,
                'msg' => '<#> كود التأكيد الخاص بك في هوم ميد هو :' . $code . ' لا تقم بمشاركة هذا الكود مع اي شخص ' . $request->app_signature,ure,
                'msgId' => rand(1, 99999),
                'timeSend' => '0',
                'dateSend' => '0',
                'deleteKey' => '55348',
                'lang' => '3',
                'applicationType' => 68,
            );
            // دالة الإرسال JOSN
            $result = $this->sendSMS($jsonObj);
            $updated = $user->update([
                'verification_code' => $code,
            ]);
            $success = [
                'key' => 'message',
                'value' => "تم ارسال الكود بنجاح",
            ];
            return $updated
            ? ApiController::respondWithSuccess($success)
            : ApiController::respondWithServerErrorObject();
        } else {
            $errors = [
                'key' => 'message',
                'value' => 'رقم الهاتف  غير صحيح',
            ];
            return ApiController::respondWithErrorArray($errors);
        }
        $errorsLogin = [
            'key' => 'message',
            'value' => trans('messages.Wrong_phone'),
        ];
        return ApiController::respondWithErrorClient(array($errorsLogin));
    }

    /**
     * verify the code to change password
     *
     * @param Request $request
     * @return void
     */
    public function confirmResetCode(Request $request)
    {
        $rules = [
            'phone_number' => 'required',
            'code' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = App\User::where('phone_number', $request->phone_number)->where('verification_code', $request->code)->first();
        if ($user) {
            $updated = $user->update([
                'verification_code' => null,
            ]);
            $success = [
                'key' => 'message',
                'value' => "الكود صحيح",
            ];
            return $updated
            ? ApiController::respondWithSuccess($success)
            : ApiController::respondWithServerErrorObject();
        } else {
            $errorsLogin = [
                'key' => 'user',
                'value' => trans('messages.error_code'),
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
    }

    /**
     * reset the password to a new one
     *
     * @param Request $request
     * @return void
     */
    public function resetPassword(Request $request)
    {
        $rules = [
            'phone_number' => 'required|numeric',
            'type' => 'required|in:0,1,2',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = App\User::where('phone_number', $request->phone_number)->where('type', $request->type)->first();
        //        $user = User::wherePhone($request->phone)->first();
        if ($user) {
            if ($user->type != $request->type) {
                $errors = [
                    'key' => 'user_type',
                    'value' => 'نوع المستخدم غير صحيح',
                ];
                return ApiController::respondWithErrorArray($errors);
            }

            $updated = $user->update(['password' => Hash::make($request->password)]);
        } else {
            $errorsLogin = [
                'key' => 'message',
                'value' => trans('messages.Wrong_phone'),
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
        return $updated
        ? ApiController::respondWithSuccess(trans('messages.Password_reset_successfully'))
        : ApiController::respondWithServerErrorObject();
    }

    /**
     * change user password
     *
     * @param Request $request
     * @return void
     */
    public function changePassword(Request $request)
    {
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required',
            'password_confirmation' => 'required|same:new_password',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $error_old_password = [
            'key' => 'message',
            'value' => trans('messages.error_old_password'),
        ];
        if (!(Hash::check($request->current_password, auth('api')->user()->password))) {
            return ApiController::respondWithErrorNOTFoundObject(array($error_old_password));
        }
        //        if( strcmp($request->current_password, $request->new_password) == 0 )
        //            return response()->json(['status' => 'error', 'code' => 404, 'message' => 'New password cant be the same as the old one.']);
        //update-password-finally ^^
        $updated = auth('api')->user()->update(['password' => Hash::make($request->new_password)]);
        $success_password = [
            'key' => 'message',
            'value' => trans('messages.Password_reset_successfully'),
        ];
        return $updated
        ? ApiController::respondWithSuccess($success_password)
        : ApiController::respondWithServerErrorObject();
    }

    /**
     * change user phone_number
     *
     * @param Request $request
     * @return void
     */
    public function change_phone_number(Request $request)
    {
        $rules = [
            'phone_number' => 'required|starts_with:05|digits:10',
            'app_signature' => 'required',
            'type' => 'required|in:0,1,2',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $oldAccounts = User::where('phone_number', $request->phone_number)->get();
        if ($oldAccounts->count() > 0) {
            $oldTypes = $oldAccounts->pluck('type')->toArray();
            if (in_array($request->type, $oldTypes)) {
                $err = [
                    'key' => 'phone_number_exists_before', 'value' => 'هذا الرقم مسجل من قبل ',
                ];
                return ApiController::respondWithErrorArray($err);
            }
        }

        $result = substr($request->phone_number, 1);
        $phone = '00966' . $result;
        $code = mt_rand(1000, 9999);
        // dd($phone);
        $jsonObj = array(
            'mobile' => 'tqnee.com.sa',
            'password' => '589935sa',
            'sender' => 'TQNEE',
            'numbers' => $phone,
            'msg' => '<#> كود التأكيد الخاص بك في هوم ميد هو :' . $code . ' لا تقم بمشاركة هذا الكود مع اي شخص ' . $request->app_signature,
            'msgId' => rand(1, 99999),
            'timeSend' => '0',
            'dateSend' => '0',
            'deleteKey' => '55348',
            'lang' => '3',
            'applicationType' => 68,
        );
        // دالة الإرسال JOSN
        $result = $this->sendSMS($jsonObj);
        $updated = App\User::where('id', auth('api')->user()->id)->update([
            'verification_code' => $code,
        ]);
        $success = [
            'key' => 'message',
            'value' => trans('messages.success_send_code'),
        ];
        return $updated
        ? ApiController::respondWithSuccess($success)
        : ApiController::respondWithServerErrorObject();
    }

    /**
     * verify code sent to new phone number before save it
     *
     * @param Request $request
     * @return void
     */
    public function check_code_changeNumber(Request $request)
    {
        $rules = [
            'code' => 'required',
            'phone_number' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $user = App\User::where('id', auth('api')->user()->id)->where('verification_code', $request->code)->first();
        if ($user) {
            $updated = $user->update([
                'verification_code' => null,
                'phone_number' => $request->phone_number,
            ]);
            $success = [
                'key' => 'message',
                'value' => "تم تغيير رقم الهاتف",
            ];
            return $updated
            ? ApiController::respondWithSuccess($success)
            : ApiController::respondWithServerErrorObject();
        } else {
            $errorsLogin = [
                'key' => 'message',
                'value' => trans('messages.error_code'),
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
    }

    /**
     * log user out from our application
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        $users = App\User::where('id', auth('api')->user()->id)->first()->update([
                'api_token' => null,
            ]);
        return $users
        ? ApiController::respondWithSuccess([])
        : ApiController::respondWithServerErrorArray();
    }

    /**
     * user update profile
     *
     * @param Request $request
     * @return void
     */
    public function changeInfo(Request $request)
    {
        $rules = [
            'name' => 'sometimes',
            'email' => 'sometimes',
            'latitude' => 'sometimes',
            'longitude' => 'sometimes',
            'image' => 'sometimes|mimes:jpeg,bmp,png,jpg|max:5000',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = App\User::where('id', $request->user()->id)->first();
        $oldImage = $user->image;
        $updated = $user->update([
            'image' => $request->image == null ? $user->image : UploadImageEdit($request->file('image'), 'user', '/uploads/users', $oldImage),
            'name' => $request->name == null ? $user->name : $request->name,
            'email' => $request->email == null ? $user->email : $request->email,
            'latitude' => $request->latitude == null ? $user->latitude : $request->latitude,
            'longitude' => $request->longitude == null ? $user->longitude : $request->longitude,
        ]);
        // dd($updated);
        return $updated
        ? ApiController::respondWithSuccess([
            'image' => asset('/uploads/users/' . $user->image),
            'name' => $user->name,
            'email' => $user->email == null ? 'null' : $user->email,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
        ])
        : ApiController::respondWithServerErrorObject();
    }

    /**
     * prepare data to send messages using external api
     *
     * @param [type] $jsonObj
     * @return void
     */
    public function sendSMS($jsonObj)
    {
        $contextOptions['http'] = array('method' => 'POST', 'header' => 'Content-type: application/json', 'content' => json_encode($jsonObj), 'max_redirects' => 0, 'protocol_version' => 1.0, 'timeout' => 10, 'ignore_errors' => true);
        $contextResouce = stream_context_create($contextOptions);
        $url = "http://www.alfa-cell.com/api/msgSend.php";
        $arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
        $result = $arrayResult[0];
        return $result;
    }
}