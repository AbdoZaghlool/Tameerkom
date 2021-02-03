<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAL2IRphk:APA91bFpUP0wZo7lH9mbwe0PSVulPMxUNBW2tiJ3oDslfFSiSbPbusla6uqU5AE955zaoQbp65Su7bf7__hDSn2b508K5Ea2HbrWLKMwzt2QkIwK27q-y0IM20yYLzJ_OlCgLh6Y7GRk'),
        'sender_id' => env('FCM_SENDER_ID', '203508786713'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
