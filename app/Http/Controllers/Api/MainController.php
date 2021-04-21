<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Chat;
use App\City;
use App\Conversation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Provider as ProviderResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Product as ProductResource;
use App\Product;
use App\Property;
use App\Region;
use App\Slider;
use App\User;
use App\UserDevice;
use Illuminate\Http\Request;
use Validator;

class MainController extends Controller
{
    /**
     * get provider by id
     *
     * @param int $id
     * @return void
     */
    public function userById($id)
    {
        $user = User::find($id);
        if ($user) {
            return ApiController::respondWithSuccess(new UserResource($user));
        }
        return ApiController::respondWithServerErrorArray();
    }
  
    public function uploadOnStore()
    {
        return ApiController::respondWithSuccess(1);
    }
    
    /**
     * get product by id
     *
     * @param int $id
     * @return void
     */
    public function productById($id)
    {
        $product = Product::find($id);
        if ($product) {
            return ApiController::respondWithSuccess(new ProductResource($product));
        }
        return ApiController::respondWithServerErrorArray();
    }

    /**
     * get application splashs images
     *
     * @return Json response
     */
    public function splashs()
    {
        $data = Slider::latest()->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'splashs',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorNOTFoundObject(array($err));
        }
        $arr = [];
        foreach ($data as $slider) {
            array_push($arr, [
                'id'          => (int) $slider->id,
                'product_id'  => (int) $slider->product_id,
                'provider_id'  => (int) $slider->provider_id,
                'link'        => (string) $slider->link,
                'image'       => asset('uploads/sliders/' . $slider->image),
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * get all regions which has cities,
     * @return json response
     */
    public function regions()
    {
        $data = Region::whereHas('cities')->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'regions',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorNOTFoundObject(array($err));
        }
        $arr = [];
        foreach ($data as $value) {
            array_push($arr, [
                'id' => $value->id,
                'name' => $value->name,
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * get the cities of given region
     * @param Region $region_id
     * @return json response
     */
    public function cities($region_id = null)
    {
        $data = City::with('region')->filter($region_id)->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'cities',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorNOTFoundObject(array($err));
        }
        $arr = [];
        foreach ($data as $value) {
            array_push($arr, [
                'id'          => $value->id,
                'name'        => $value->name,
                'region_id'   => (int)$value->region_id,
                'region_name' => $value->region->name,
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * get all categories
     *
     * @return Json response
     */
    public function allCategories()
    {
        $data = Category::select('id', 'name')->get();
        if ($data->count() == 0) {
            $err = [
                'key' => 'categories',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorNOTFoundObject(array($err));
        }
        return ApiController::respondWithSuccess($data);
    }


    /**
     * get the whole main category in our app
     *
     * @param int $id
     * @return json resopnes with categories
     */
    public function mainCat(Request $request, $user_id = null)
    {
        $categories = Category::with('products', 'products.values', 'products.values.property')->get();
        
        if ($categories->count() == 0) {
            $err = [
                'key' => 'categories',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorObject(array($err));
        }

        $request['user_id'] = $user_id;
        return ApiController::respondWithSuccess(CategoryResource::collection($categories));
    }
    
    public function properties($cat_id = null)
    {
        $properties = Property::with('values')->filter($cat_id)->get();
        if ($properties->count() == 0) {
            $err = [
                'key' => 'properties',
                'value' => 'لا توجد بيانات حاليا',
            ];
            return ApiController::respondWithErrorObject(array($err));
        }
        $res = [];
        foreach ($properties as $property) {
            array_push($res, [
                'id' => $property->id,
                'name' => $property->name,
                'values' =>$property->values()->pluck('name', 'id')
            ]);
        }
        return ApiController::respondWithSuccess($res);
    }

    /**
     * search storage with user keyword
     * first search in provider to find any matching provider by name,
     * if not we search in products names and details
     * if not then there is no data match
     *
     * @param Request $request
     * @return Json $resposne
     */
    public function search(Request $request)
    {
        $rules = [
            'keyword' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        // search in providers
        $data = Product::with('provider')->whereHas('provider', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->keyword . '%');
        })->get();

        $data = $data->filter(function ($product) {
            return $product['provider']['blocked'] == 0;
        });
        if ($data->count() > 0) {
            return ApiController::respondWithSuccess(ProductResource::collection($data));
        }
        // end search in providers

        // search in products
        $data = Product::with('provider')->where('name', 'like', '%' . $request->keyword . '%')
            ->orWhere('details', 'like', '%' . $request->keyword . '%')
            ->get();
        $data = $data->filter(function ($product) {
            return $product['provider']['blocked'] == 0;
        });
        if ($data->count() > 0) {
            return ApiController::respondWithSuccess(ProductResource::collection($data));
        }
        // end search in products

        // no matching data in database
        $err = [
                'key' => 'search_result',
                'value' => 'لا يوجد بيانات تماثل ' . $request->keyword,
            ];
        return ApiController::respondWithErrorArray($err);
    }

    /**
     * create new conversation between user and provider
     *
     * @param Request $request
     * @return void
     */
    public function createConversation(Request $request)
    {
        $rules = [
            'provider_id'  => 'required|exists:users,id'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }
        $conversation = Conversation::updateOrCreate([
            'user_id' => $request->user()->id,
            'provider_id' => $request->provider_id,
        ]);
        $res = [
            'key' => 'تم انشاء المحادثة', 'conversation_id' => (int) $conversation->id
        ];
        return ApiController::respondWithSuccess($res);
    }

    /**
     * connect room
     *
     * @param Request $request
     * @return void
     */
    public function connectRoom(Request $request)
    {
        $rules = [
            'room_id'  => 'required|exists:conversations,id'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $room = Conversation::find($request->room_id);
        // update last messages seen here.
        $unseenMessages = $room->chats()->where('user_id', '!=', $request->user()->id)->where('seen', 0)->get();
        if ($unseenMessages->count() > 0) {
            $room->chats()->where('user_id', '!=', $request->user()->id)->where('seen', 0)->update(['seen'=>1]);
        }
        //end update seen.
        if ($room->user_id == $request->user()->id) {
            $room->update(['user_online'=>1]);
        } else {
            $room->update(['provider_online'=>1]);
        }
        return response()->json(['mainCode'=> 1,'code' =>  200 , 'user_phone'=>$request->user()->phone_number ], 200);
    }

    /**
     * disconnect room
     *
     * @param Request $request
     * @return void
     */
    public function disconnectRoom(Request $request)
    {
        $room = Conversation::find($request->room_id);
        $user = User::where('phone_number', $request->phone)->first();
        if ($user) {
            if ($room->user_id == $user->id) {
                $room->update(['user_online'=>0]);
            } else {
                $room->update(['provider_online'=>0]);
            }
            return response()->json(['mainCode'=> 1,'code' =>  200 , 'phone'=>$user->phone_number ]);
        } else {
            return response()->json(['mainCode'=> 0,'code' =>  422 ,'message'=> 'user_disconnect' ]);
        }
    }

    /**
     * send chat message
     *
     * @param Request $request
     * @return void
     */
    public function sendMessage(Request $request)
    {
        $rules = [
            'room_id'  => 'required|exists:conversations,id',
            'message'  => 'sometimes',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));
        }

        $request['first'] = $request->user()->id;
        $room = Conversation::whereId($request->room_id)
            ->where('user_online', 1)
            ->where('provider_online', 1)
            ->first();
        if ($room) {
            $request['seen']=1;
        // $room->update(['seen'=>1]);
        } else {
            $room = Conversation::find($request->room_id);
            if ($room->user_online == 0) {
                /* notifications*/
                $devicesTokens = UserDevice::where('user_id', $room->user_id)
                    ->pluck('device_token')
                    ->toArray();
                $title = 'رسائل الدردشة';
                $message = $request->file == null ? $request->message : 'تم  ارسال مرفق';
                if ($devicesTokens) {
                    sendMultiNotification($title, $request->user()->name." : ". $message, $devicesTokens);
                }
                //end notifications/
                // $room->update(['sender_online'=>1]);
            } else {
                /* notifications*/
                $devicesTokens = UserDevice::where('user_id', $room->provider_id)
                    ->pluck('device_token')
                    ->toArray();
                $title = 'رسائل الدردشة';
                $message = $request->file == null ? $request->message : 'تم  ارسال مرفق';
                if ($devicesTokens) {
                    sendMultiNotification($title, $request->user()->name." : ". $message, $devicesTokens);
                }
            }
            // $room->update(['seen'=>0]);
        }
        if ($request->file != null) {
            $chatMessage =  Chat::create([
                'conversation_id' => $request->room_id,
                'user_id'         => $request->user()->id,
                'file'            => $request->file,
            ]);
        } else {
            $chatMessage =  Chat::create([
                'conversation_id' => $request->room_id,
                'user_id'         => $request->user()->id,
                'message'         => $request->message ?? 'message',
            ]);
        }
        $data = [
            'id'              => $chatMessage->id,
            'conversation_id' => intval($chatMessage->conversation_id),
            'user_id'         => $chatMessage->user_id,
            'message'         => $chatMessage->message,
            'file'            => $chatMessage->file == null ? '' : asset('uploads/chats/'.$chatMessage->file),
            'created_at'      => $chatMessage->created_at->format('Y-m-d'),
        ];
        return response()->json(['mainCode'=> 1,'code' =>  200 , 'data'=>$data], 200);
    }

    /**
     * upload chat files to application storage
     *
     * @param Request $request
     * @return void
     */
    public function uploadChatFiles(Request $request)
    {
        $rules = [
            'conversation_id' => 'required|exists:conversations,id',
            'file'            => 'required|max:10000',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ApiController::respondWithErrorObject(validateRules($validation->errors(), $rules));
        }

        $uploaded = UploadImage($request->file('file'), 'chat', '/uploads/chats');
        if ($uploaded) {
            return ApiController::respondWithSuccess($uploaded);
        } else {
            return ApiController::respondWithServerErrorArray();
        }
    }
}