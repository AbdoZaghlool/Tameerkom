<?php

namespace App\Listeners;

use App\Events\ChangeAvailabliltyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class AddDuration
{
    /**
     * Handle the event.
     *
     * @param  ChangeAvaialblityEvent  $event
     * @return void
     */
    public function handle(ChangeAvailabliltyEvent $event)
    {
        $user = $event->user;
        // $duration =;
        if (request()->available == 1) {
            $user->periods()->create([
                'start_time' => Carbon::now()
            ]);
        } else {
            $duration = $user->periods()->where('end_time', null)->latest()->first();
            if ($duration) {
                $duration->update([
                    'end_time'   => Carbon::now(),
                    'time_count' => now()->diffInHours($duration->start_time)
                ]);
            }
        }
    }
}