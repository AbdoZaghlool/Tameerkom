<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Chat extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
                'id'           => (int)$this->id,
                'message'      => (string)$this->message,
                'seen'         => (int)$this->seen,
                'file'         => $this->file == null ? '' : asset('uploads/chats/'.$this->file),
                'sender_id'    => (int)$this->user_id,
                'sender_name'  => $this->user->name,
                'sender_image' => asset('uploads/users/'.$this->user->image),
                'created_at'   => $this->created_at == null ? '' : $this->created_at->format('Y-m-d H:i') ,
        ];
    }
}