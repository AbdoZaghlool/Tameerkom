<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App;
use App\Chat;
use App\Contact;
use App\Conversation;
use App\Device;
use App\Http\Resources\Chat as AppChat;
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
            "id"                  => (int)$settings->id,
            "email"               => (string)$settings->email,
            "phone"               => (string)$settings->phone,
            "distance"            => (int)$settings->distance,
            "commission"          => (double)$settings->commission,
            'bank_name'           => (string)$settings->bank_name,
            'bank_number'         => (string)$settings->bank_number,
            'active_orders_count' => (int)$settings->active_orders_count,
            'unpaid_commissions'  => (int)$settings->unpaid_commissions,
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
     * get user rate
     *
     * @return void
     */
    public function myRate()
    {
        $user = request()->user();
        $rate = $user->getRateValue();
        return ApiController::respondWithSuccess($rate);
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
     * user conversations
     *
     * @return void
     */
    public function myConversations()
    {
        $user = request()->user();

        $data = Conversation::with('user', 'provider')
            ->where('user_id', $user->id)->orWhere('provider_id', $user->id)
            ->orderBy('created_at', 'DESC')->get();

        if ($data->count() == 0) {
            $arr = [
                'key' => 'conversations',
                'value' => 'لا يوجد بيانات حاليا'
            ];
            return ApiController::respondWithErrorArray($arr);
        }

        $arr = [];
        foreach ($data as $conversation) {
            $lastMessage = $conversation->chats()->first();
            array_push($arr, [
                'id'             => $conversation->id,
                'user_id'        => $conversation->user_id,
                'user_name'      => $conversation->user->name,
                'user_image'     => asset('uploads/users/'.$conversation->user->image),
                'provider_id'    => $conversation->provider_id,
                'provider_name'  => $conversation->provider->name,
                'provider_image' => asset('uploads/users/'.$conversation->provider->image),
                'last_message'   => $lastMessage == null ? '' : $lastMessage->message,
                'created_at'     => $conversation->created_at == null ? '' : $conversation->created_at->format('Y-m-d H:i') ,
            ]);
        }
        return ApiController::respondWithSuccess($arr);
    }

    /**
     * get conversation messages
     *
     * @param int $id
     * @return void
     */
    public function conversationMessages($id)
    {
        $messages = Chat::where('conversation_id', $id)->get();
        if (!$messages->count() > 0) {
            $err = [
                'key' => 'messages',
                'value' => 'لا يوجد رسائل'
            ];
            return ApiController::respondWithErrorArray($err);
        }
        return ApiController::respondWithSuccess(AppChat::collection($messages));
    }
}