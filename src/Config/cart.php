<?php

return [
    'expire' => [
        'cart' => env('GC_CART_EXPIRE_CART', 7), //days
        'wishlist' => env('GC_CART_EXPIRE_WISHLIST', 30), //days
        'compare' => env('GC_CART_EXPIRE_COMPARE', 30), //days
        'lastview' => env('GC_CART_EXPIRE_PRODUCT_LASTVIEW', 30), //days
    ],
    'process' => [
        'other_fee' => [
            'value' => env('GC_PROCESS_OTHER_FEE', 0),
            'title' => env('GC_PROCESS_OTHER_TITLE', 'Other fee'),
        ],
    ],
];
