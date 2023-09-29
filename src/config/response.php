<?php
return [
    'customers' => [
        'index' => [
            'id',
            'name',
            'description',
        ],
        'detail' => [
            'name',
            'name_kana',
            'gender',
            'uuid',
            'status',
            'email',
            'phone_number',
            'zipcode',
            'address',
        ],
    ],
    'salons' => [
        'index' => [
            'id',
            'name',
            'description',
        ],
        'detail' => [
            'id',
            'name',
            'description',
            'zipcode',
            'address',
            'phone_number',
            'start_time',
            'closing_time',
            'holiday',
            'payment_methods',
        ],
    ],
    'stylists' => [
        'index' => [
            'id',
            'name',
            'salon_name',
        ],
        'detail' => [
            'id',
            'salon_id',
            'salon_name',
            'name',
            'name_kana',
            'gender',
            'appoint_fee',
            'stylist_history',
            'skill',
        ],
    ]
];