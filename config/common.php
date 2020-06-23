<?php

use app\models\db\User;

return [
    'components' => [
        'user' => [
            'class'           => \yii\web\User::class,
            'identityClass'   => User::class,
            'enableAutoLogin' => true,
            'enableSession'   => false,
        ],
    ],
    'container' => [
        'singletons' => [
            \yii\web\User::class => [
                'class'           => \yii\web\User::class,
                'identityClass'   => User::class,
                'enableAutoLogin' => true,
                'enableSession'   => false,
            ],
        ],
    ],
];
