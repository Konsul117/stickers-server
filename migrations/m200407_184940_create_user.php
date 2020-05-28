<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m200407_184940_create_user
 */
class m200407_184940_create_user extends Migration
{
    var $tableName = 'user';

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable($this->tableName, [
            'id'           => $this->primaryKey() . ' COMMENT "Уникальный идентификатор пользователя"',
            'username'     => Schema::TYPE_STRING . '(100) NOT NULL COMMENT "Имя (логин) пользователя"',
            'password'     => Schema::TYPE_STRING . '(60) NOT NULL COMMENT "Пароль"',
            'auth_key'     => Schema::TYPE_STRING . '(32) NOT NULL COMMENT "Ключ безопасности"',
            'create_stamp' => Schema::TYPE_DATETIME . ' NOT NULL COMMENT "Дата-врем создания записи"',
            'update_stamp' => Schema::TYPE_DATETIME . ' NOT NULL COMMENT "Дата-врем обновления записи"',
        ], 'COMMENT "Пользователи"');

        $this->createIndex('ix-' . $this->tableName . '[create_stamp]', $this->tableName, ['create_stamp']);
        $this->createIndex('ux-' . $this->tableName . '[username]', $this->tableName, ['username'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable($this->tableName);
    }
}
