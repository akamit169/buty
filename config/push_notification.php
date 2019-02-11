<?php

return [  
    'apple' => [

        'sandbox' => [
            'url' => 'ssl://gateway.sandbox.push.apple.com:2195',
            'pem_file' => public_path('apple_pems') . '/devpushcert.pem',
            'passphrase' => ''
        ],
        'production' => [
            'url' => 'ssl://gateway.push.apple.com:2195',
            'pem_file' => public_path('apple_pems') . '/pushcert.pem',
            'passphrase' => ''
        ]
    ],

];
