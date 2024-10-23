<?php

return [

    'title' => 'サインイン',

    'heading' => 'サインイン',

    'actions' => [

        'register' => [
            'before' => 'or',
            'label' => 'sign up for an account',
        ],

        'request_password_reset' => [
            'label' => 'Forgot password?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'メールアドレス',
        ],

        'password' => [
            'label' => 'パスワード',
        ],

        'remember' => [
            'label' => 'Remember me',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'サインイン',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'これらの資格情報は私たちの記録と一致しません。',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Too many login attempts',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
