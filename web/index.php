<?php

// comment out the following two lines when deployed to production

$repository = dirname(__DIR__);

defined('YII_ENV') or define('YII_ENV', file_exists($repository . '/.dev') ? 'dev' : 'prod');

defined('YII_DEBUG') or define('YII_DEBUG', YII_ENV === 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = \yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../config/common.php',
    require __DIR__ . '/../config/web.php'
);

(new yii\web\Application($config))->run();
