<?php
return [
    'salons' => [
        'index' => [
            'id',
            'name'
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
        ]
    ],
    'stylists' => [
        'index' => [
            'id',
            'name'
        ],
        'detail' => [
            'id',
            'salon_id',
            'name',
            'name_kana',
            'gender',
            'appoint_fee',
            'stylist_history',
            'skill',
        ]
    ]
];