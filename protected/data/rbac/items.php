<?php
return [
    'signup' => [
        'type' => 2,
    ],
    'login' => [
        'type' => 2,
    ],
    'logout' => [
        'type' => 2,
    ],
    'createBook' => [
        'type' => 2,
    ],
    'updateOwnBooks' => [
        'type' => 2,
    ],
    'updateOtherBooks' => [
        'type' => 2,
    ],
    'deleteOwnBooks' => [
        'type' => 2,
    ],
    'deleteOtherBooks' => [
        'type' => 2,
    ],
    'guest' => [
        'type' => 1,
        'ruleName' => 'userRole',
        'children' => [
            'signup',
            'login',
        ],
    ],
    'user' => [
        'type' => 1,
        'ruleName' => 'userRole',
        'children' => [
            'logout',
            'createBook',
            'updateOwnBooks',
            'deleteOwnBooks',
        ],
    ],
    'admin' => [
        'type' => 1,
        'ruleName' => 'userRole',
        'children' => [
            'updateOtherBooks',
            'deleteOtherBooks',
            'user',
        ],
    ],
];
