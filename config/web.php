<?php

use app\models\db\User;
use yii\rest\UrlRule;
use yii\web\JsonParser;

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'parsers' => [
	            'application/json' => JsonParser::class,
            ],
            'cookieValidationKey' => 'DSFgksdifhiw899734hekfDFGisjdfi9374',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass'   => User::class,
            'enableAutoLogin' => true,
            'enableSession'   => false,
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'urlManager' => [
	        'enablePrettyUrl'     => true,
	        'enableStrictParsing' => true,
	        'showScriptName'      => false,
	        'rules'               => [
		        [
                    'class'         => UrlRule::class,
                    'controller'    => 'ticket',
                    'pluralize'     => false,
                    'extraPatterns' => [
                        'batch' => 'batch',
                    ],
			    ],
                [
                    'class'         => UrlRule::class,
                    'controller'    => 'auth',
                    'pluralize'     => false,
                    'extraPatterns' => [
                        'login'  => 'login',
                        'status' => 'status',//@TODO-19.04.2020-Kazancev A. что-то придумать с этим, чтобы не перечислять кастомные экшны
                    ],
                ],
	        ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
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
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
