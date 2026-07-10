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
        'token' => env('POSTMARK_TOKEN'),
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

    'kampus_api' => [
        'base_url' => env('KAMPUS_API_BASE_URL'),
        'username' => env('KAMPUS_API_USERNAME'),
        'password' => env('KAMPUS_API_PASSWORD'),
        'static_token' => env('KAMPUS_API_STATIC_TOKEN'),
        'timeout'  => env('KAMPUS_API_TIMEOUT', 20),
        'admin' => [
            'usernames' => env('CIS_ADMIN_USERNAMES') ?: env('CIS_KONSELOR_USERNAME'),
            'emails' => env('CIS_ADMIN_EMAILS') ?: env('CIS_KONSELOR_EMAIL'),
            'names' => env('CIS_ADMIN_NAMES') ?: env('CIS_KONSELOR_NAME'),
            'pegawai_ids' => env('CIS_ADMIN_PEGAWAI_IDS'),
            'jabatan' => env('CIS_ADMIN_JABATAN'),
            'specialization' => env('CIS_ADMIN_SPECIALIZATION'),
        ],
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
        'timeout' => env('GROQ_TIMEOUT', 60),
    ],

    'ai' => [
        'engine_url' => env('AI_ENGINE_URL', 'http://127.0.0.1:8001'),
    ],

];
