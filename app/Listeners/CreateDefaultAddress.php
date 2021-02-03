<?php

namespace App\Listeners;

use App\Events\ClientRegisterdEvent;

class CreateDefaultAddress
{
    /**
     * Handle the event by creating new address to this user.
     *
     * @param  ClientRegisterdEvent  $event
     * @return void
     */
    public function handle(ClientRegisterdEvent $event)
    {
        $user = $event->user;
        $data = request()->all();
        $user->userAddresses()->create([
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'title' => 'عنوان افتراضي',
            'is_default' => 1,
        ]);
    }
}
