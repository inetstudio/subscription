<?php

return [
    /*
     * Сервис для подписки.
     * mailchimp / mailgun / mindbox / local
     */

    'driver' => env('SUBSCRIPTION_DRIVER', 'local'),

    'mailchimp' => [
        'api_key' => env('SUBSCRIPTION_MAILCHIMP_API_KEY'),
        'subscribers_list' => env('SUBSCRIPTION_MAILCHIMP_SUBSCRIBERS_LIST'),
        'interests' => json_decode(env('SUBSCRIPTION_MAILCHIMP_INTERESTS', '{}'), true),
    ],

    'mindbox' => [
        'secret' => '',
        'url' => '',
        'brand' => '',
        'point' => '',
    ],
];
