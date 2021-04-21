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
            'phone_number' => 'required|unique:users|starts_with:05|digits:10',
            'app_signature' => 'sometimes',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $code = mt_rand(1000, 9999);
        $result = $this->sendSMS($request, $code);
        App\PhoneVerification::updateOrCreate(['phone_number' => $request->phone_number], [
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
            'app_signature' => 'sometimes',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $code = mt_rand(1000, 9999);
        $result = $this->sendSMS($request, $code);
        
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
            'type'                  => 'required|in:0,1',
            'phone_number'          => 'required|unique:users|starts_with:05|digits:10',
            'name'                  => 'required|max:255',
            'image'                 => 'required_if:type,1|mimes:jpeg,bmp,png,jpg|max:3000',
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'device_token'          => 'required',
            'email'                 => 'sometimes|email|unique:users',
            'latitude'              => 'sometimes',
            'longitude'             => 'sometimes',
            'commercial_record'     => 'required_if:type,1',
            'commercial_image'      => 'required_if:type,1|mimes:jpeg,jpg,png|max:3000',
            'city_id'               => 'required_if:type,1',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $user = App\User::create([
            'name'              => $request->name,
            'phone_number'      => $request->phone_number,
            'password'          => Hash::make($request->password),
            'active'            => $request->type == 0 ? 1 : 0,
            'type'              => $request->type,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'email'             => $request->email,
            'city_id'           => $request->city_id,
            'commercial_record' => $request->commercial_record,
            'commercial_image'  => $request->commercial_image == null ? null : UploadImage($request->file('commercial_image'), 'commercial', '/uploads/commercial_images'),
            'image'             => $request->image == null ? null : UploadImage($request->file('image'), 'user', '/uploads/users'),
        ]);
        Auth::guard('api')->check(['phone_number' => $request->phone_number, 'password' => $request->password]);
        $token = generateApiToken($user->id, 15);
        $user->update(['api_token' => $token]);
        $user->devices()->create([
            'device_token' => $request->device_token,
        ]);

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
            'latitude' => 'sometimes',
            'longitude' => 'sometimes',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        if ($check = Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password])) {
            $user = $request->user();
            $token = generateApiToken($user->id, 15);
            $user->update([
                'api_token' => $token,
                'latitude' => $request->latitude ?? $user->latitude,
                'longitude' => $request->longitude ?? $user->longitude,
            ]);

            if (Auth::user()->active == 0) {
                $errors = [
                    'key' => 'message',
                    'value' => 'عميلنا العزيز شكرا لاستخدامكم تطبيق تعميركم. برجاء الانتظار لحين تفعيل عضويتك من قبل الادارة',
                ];
                return ApiController::respondWithErrorArray($errors);
            }
            //save_device_token....
            if ($request->device_token != null) {
                $created = $user->devices()->updateOrCreate([
                    'device_token' => $request->device_token,
                ]);
            }

            return $created
            ? ApiController::respondWithSuccess(array(new UserResource($user)))
            : ApiController::respondWithServerErrorArray();
        } else {
            $user = User::where('phone_number', $request->phone_number)->first();
            if ($user == null) {
                $phone = ['key' => 'phone_number', 'value' => 'رقم الهاتف غير صحيح'];
                return ApiController::respondWithErrorClient(array($phone));
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
            'phone_number'  => 'required|numeric|exists:users,phone_number',
            'app_signature' => 'sometimes',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $user = App\User::where('phone_number', $request->phone_number)->first();

        $code = mt_rand(1000, 9999);
        $this->sendSMS($request, $code);
        
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
            'phone_number' => 'required|exists:users,phone_number',
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
            'phone_number'          => 'required|numeric',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = App\User::where('phone_number', $request->phone_number)->first();
        if ($user) {
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
            'phone_number'  => 'required|unique:users|starts_with:05|digits:10',
            'app_signature' => 'sometimes',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $code = mt_rand(1000, 9999);
        $this->sendSMS($request, $code);
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
            'name'              => 'sometimes',
            'email'             => 'sometimes',
            'commercial_record' => 'sometimes',
            'latitude'          => 'sometimes',
            'longitude'         => 'sometimes',
            'city_id'           => 'sometimes|exists:cities,id',
            'image'             => 'sometimes|mimes:jpeg,bmp,png,jpg|max:5000',
            'commercial_image'             => 'sometimes|mimes:jpeg,bmp,png,jpg|max:5000',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $user = $request->user();
        $oldImage = $user->image;
        $updated = $user->update([
            'name'              => $request->name ?? $user->name ,
            'email'             => $request->email ?? $user->email,
            'city_id'           => $request->city_id ?? $user->city_id,
            'commercial_record' => $request->commercial_record ?? $user->commercial_record,
            'latitude'          => $request->latitude ?? $user->latitude,
            'longitude'         => $request->longitude ?? $user->longitude,
            'image'             => $request->image == null ? $user->image : UploadImageEdit($request->file('image'), 'user', '/uploads/users', $oldImage),
            'commercial_image'  => $request->commercial_image == null ? $user->commercial_image : UploadImageEdit($request->file('commercial_image'), 'commercial', '/uploads/commercial_images', $user->commercial_image),
        ]);

        return $updated
        ? ApiController::respondWithSuccess(new UserResource($user))
        : ApiController::respondWithServerErrorObject();
    }

    /**
     * prepare data to send messages using external api
     *
     * @param [type] $jsonObj
     * @return void
     */
    public function sendSMS($request, $code)
    {
        $result = substr($request->phone_number, 1);
        $phone = '00966' . $result;

        $jsonObj = array(
            'mobile'          => 'tqnee.com.sa',
            'password'        => '589935sa',
            'sender'          => 'TQNEE',
            'numbers'         => $phone,
            'msg'             => '<#> كود التأكيد الخاص بك في تعميركم هو :'
            . $code . ' لا تقم بمشاركة هذا الكود مع اي شخص ' . $request->app_signature,
            'msgId'           => rand(1, 99999),
            'timeSend'        => '0',
            'dateSend'        => '0',
            'deleteKey'       => '55348',
            'lang'            => '3',
            'applicationType' => 68,
        );

        $contextOptions['http'] = array('method' => 'POST', 'header' => 'Content-type: application/json', 'content' => json_encode($jsonObj), 'max_redirects' => 0, 'protocol_version' => 1.0, 'timeout' => 10, 'ignore_errors' => true);
        $contextResouce = stream_context_create($contextOptions);
        $url = "http://www.alfa-cell.com/api/msgSend.php";
        $arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
        $result = $arrayResult[0];
        return $result;
    }
}