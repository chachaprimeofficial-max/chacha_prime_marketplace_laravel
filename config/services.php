<?php

return [
    'payments' => [
        'pingpong' => ['webhook_secret' => env('PINGPONG_WEBHOOK_SECRET')],
        'lianlianpay' => ['webhook_secret' => env('LIANLIANPAY_WEBHOOK_SECRET')],
        'worldfirst' => ['webhook_secret' => env('WORLDFIRST_WEBHOOK_SECRET')],
        'alipay' => ['webhook_secret' => env('ALIPAY_WEBHOOK_SECRET')],
        'wechat_pay' => ['webhook_secret' => env('WECHAT_PAY_WEBHOOK_SECRET')],
        'paypal' => ['webhook_secret' => env('PAYPAL_WEBHOOK_SECRET')],
    ],
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    ],
];
