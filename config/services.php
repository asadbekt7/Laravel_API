<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'staff_api' => [
        'base_url' => env('STAFF_API_BASE_URL'),
        'timeout'  => env('STAFF_API_TIMEOUT', 30),
    ],
    'room_api' => [
        'base_url' => env('ROOM_API_BASE_URL', 'https://manage.uwed.uz/api/timetable/public/v1/search/rooms'),
        'timeout'  => env('ROOM_API_TIMEOUT', 10),
    ],

    'my_uwed' => [
        'enabled' => env('MY_UWED_AUTH_ENABLED', true),
        'secret'  => env('MY_UWED_JWT_SECRET'),
        'cookie'  => env('MY_UWED_COOKIE') ?: null,
        'issuer'  => env('MY_UWED_ISSUER') ?: null,
    ],

];
