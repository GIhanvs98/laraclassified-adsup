<?php

return [

    'default' => env('SMS_GATEWAY', 'ozonedesk'),

    'country-code' => env('SMSHUB_LK_COUNTRY_CODE', '+94'),

    'country-code-symbol' => env('SMSHUB_LK_COUNTRY_CODE_SYMBOL', 'LK'),

    'providers' => [

        'ozonedesk' => [

            'api-token' => env('SMS_API_TOKEN'),

            'api-url' => env('SMS_API_URL', 'http://send.ozonedesk.com/api/v2/send.php'),

            'user-id' => env('SMS_USER_ID'),

            'sender-id' => env('SMS_SENDER_ID'),

        ],

        'smshub' => [

            'api-token' => env('SMS_API_TOKEN'),

            'api-url' => env('SMS_API_URL', 'https://api.smshub.lk/api/v1/send/single'),
        ],
    ]
];