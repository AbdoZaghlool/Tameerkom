<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAX0G7WWA:APA91bFqDR6mXKHUbc3yzSesEU1MtJKgZ3LRKltLEvHclEKiRwNDKbA6lHjP4_Dz1riFxUhQU0hV2AEq9JJb-4CQxR5VJQyubwukIt7sjrhnAJHBjOn02ouTnpSCmqlaUEsaMV9AVz2c'),
        'sender_id' => env('FCM_SENDER_ID', '409124690272'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
