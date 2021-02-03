<?php

namespace App\Http\Controllers\Api;

use App\About;
use App\ContactUs;
use App\Order;
use App\Notification;
use App\TermsCondition;
use App\UserDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Wallet;
use Validator;
use Carbon;

class ApiController extends Controller
{
    public function getImagesPath()
    {
        $data['user'] = imgPath('users');
        $data['company'] = imgPath('companies');
        $data['group'] = imgPath('groups');
        $data['app_adds'] = imgPath('app_adds');
        return $this->respondWithSuccess($data);
    }

    public function contactUs(Request $request)
    {
        $rules = [
            'name'      => 'required|max:255',
            'email'     => 'required|max:194',
            'message'   => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithError(validateRules($validator->errors(), $rules));
        }
        $created = ContactUs::create($request->all());
        return $created
            ? $this->respondWithSuccess($created)
            : $this->respondWithServerError();
    }


    public function listNotifications(Request $request)
    {
        $data = Notification::Where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        $arr = [];
        if ($data->count() > 0) {
            foreach ($data as $value) {
                array_push($arr, [
                    'id'       => (int)$value->id ,
                    'user_id'  => (int)$value->user_id ,
                    'type'     => (int)$value->type ,
                    'title'    => $value->title ,
                    'message'  => $value->message ,
                    'order_id' => (int)$value->order_id ,
                    'is_read'  => (int)$value->is_read
                ]);
            }
            return $this->respondWithSuccess($arr);
        }

        $err = [
            'key' => 'notifications',
            'value'=> 'لا توجد بيانات حاليا'
        ];
        return $this->respondWithErrorObject(array($err));
    }

    public function delete_Notifications($id, Request $request)
    {
        $data = Notification::Where('id', $id)->where('user_id', $request->user()->id)->delete();
        return $data
            ? $this->respondWithSuccess([])
            : $this->respondWithServerErrorArray();
    }

    public function read_all_notification(Request $request)
    {
        $all = Notification::Where('user_id', $request->user()->id)
        ->get();
        if ($all->count() > 0) {
            foreach ($all as $data) {
                $data->update([
                    'is_read' => '1'
                    ]);
            }
        }
        return $all ? $this->respondWithSuccess([
            'read_all_notification' => 'تم تحديد الكل كمقرؤء بنجاح'
            ])
            : $this->respondWithServerErrorArray();
    }



    public function unread_notification_count(Request $request)
    {
        $all = Notification::Where('user_id', $request->user()->id)
                ->where('is_read', '0')
                ->get();
        return $all
                ? $this->respondWithSuccess([
                'unread_notification_count' => $all->count()
                ])
                : $this->respondWithServerErrorArray();
    }

    public function readNotification($id, Request $request)
    {
        $data = Notification::Where('id', $id)->where('user_id', $request->user()->id);
        $update = $data->update([
                        'is_read' => 1
                    ]);
        return $update
                        ? $this->respondWithSuccess([])
                        : $this->respondWithServerErrorArray();
    }



    public static function createUserWallet($userId)
    {
        $created = Wallet::updateOrCreate(['user_id' => $userId], [ 'cash' => 0 ]);
        return $created;
    }

    public static function createProviderDeviceToken($userId, $deviceToken)
    {
        $created = UserDevice::create(['provider_id' => $userId, 'device_token' => $deviceToken]);
        return $created;
    }

    public static function respondWithSuccess($data)
    {
        http_response_code(200);
        return response()->json(['mainCode' => 1, 'code' =>  http_response_code(), 'data' => $data, 'error' => null])->setStatusCode(200);
    }


    public static function respondWithErrorArray($errors)
    {
        http_response_code(422);  // set the code
        return response()->json(['mainCode' => 0, 'code' =>  http_response_code(), 'data' => null, 'error' => array($errors)])->setStatusCode(422);
    }

    public static function respondWithErrorObject($errors)
    {
        http_response_code(422);  // set the code
        return response()->json(['mainCode' => 0, 'code' =>  http_response_code(), 'data' => null, 'error' => $errors])->setStatusCode(422);
    }

    public static function respondWithErrorNOTFoundObject($errors = null)
    {
        http_response_code(404);  // set the code
        return response()->json(['mainCode' => 0, 'code' =>  http_response_code(), 'data' => null, 'error' => $errors])->setStatusCode(404);
    }

    public static function respondWithErrorNOTFoundArray($errors)
    {
        http_response_code(404);  // set the code
        return response()->json(['mainCode' => 0, 'code' =>  http_response_code(), 'data' => null, 'error' => $errors])->setStatusCode(404);
    }

    public static function respondWithErrorClient($errors)
    {
        http_response_code(400);  // set the code
        return response()->json(['mainCode' => 0, 'code' =>  http_response_code(), 'data' => null, 'error' => $errors])->setStatusCode(400);
    }

    public static function respondWithErrorAuthObject($errors)
    {
        http_response_code(401);  // set the code
        return response()->json(['mainCode' => 0, 'code' =>  http_response_code(), 'data' => null, 'error' => $errors])->setStatusCode(401);
    }

    public static function respondWithErrorAuthArray($errors)
    {
        http_response_code(401);  // set the code
        return response()->json(['mainCode' => 0, 'code' =>  http_response_code(), 'data' => null, 'error' => $errors])->setStatusCode(401);
    }

    public static function respondWithServerErrorArray()
    {
        $errors = [
            'key' => 'message',
            'value' => 'حدث خطأ, برجاء المحاولة لاحقا'
        ];
        http_response_code(500);
        return response()->json(['mainCode' => 0, 'code' =>  http_response_code(), 'data' => null, 'error' => array($errors)])->setStatusCode(500);
    }

    public static function respondWithServerErrorObject()
    {
        $errors = [
            'key' => 'message',
            'value' => 'Sorry something went wrong, please try again'
        ];
        http_response_code(500);
        return response()->json(['mainCode' => 0, 'code' =>  http_response_code(), 'data' => null, 'error' => $errors])->setStatusCode(500);
    }
}
