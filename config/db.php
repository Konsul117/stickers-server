<?php

use yii\db\Connection;

$result = [
    'class' => Connection::class,
    'dsn' => '',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8',

    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];

if (file_exists(__DIR__ . '/db-local.php')) {
	$result = \yii\helpers\ArrayHelper::merge($result, require(__DIR__ . '/db-local.php'));
}

return $result;