<?php

return [

    /*
     * Настройки таблиц
     */

    'datatables' => [
        'ajax' => [
            'index' => [
                'url' => 'back.subscription.data',
                'type' => 'POST',
                'data' => 'function(data) { data._token = $(\'meta[name="csrf-token"]\').attr(\'content\'); }',
            ],
        ],
        'table' => [
            'index' => [
                'paging' => true,
                'pagingType' => 'full_numbers',
                'searching' => true,
                'info' => false,
                'searchDelay' => 350,
                'language' => [
                    'url' => '/admin/js/plugins/datatables/locales/russian.json',
                ],
            ],
        ],
        'columns' => [
            'index' => [
                ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
                ['data' => 'info', 'name' => 'additional_info', 'title' => 'Дополнительная информация', 'visible' => false],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
                ['data' => 'status', 'name' => 'status', 'title' => 'Статус'],
                ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
            ],
        ],
    ],

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

    'mailgun' => [
        'api_key' => '',
        'domain' => '',
        'subscribers_list' => '',
        'secret_passphrase' => '',
    ],

    'mindbox' => [
        'secret' => '',
        'url' => '',
        'brand' => '',
        'point' => '',
    ],
];
