<?php

namespace app\models\db;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\validators\RequiredValidator;
use yii\web\IdentityInterface;

/**
 * Модель пользователя
 *
 * @property string $id               Уникальный идентификатор пользователя
 * @property string $username         Имя (логин) пользователя
 * @property string $password         Пароль
 * @property string $auth_key         Ключ безопасности
 * @property string $create_stamp     Дата-врем создания записи
 * @property string $update_stamp     Дата-врем обновления записи
 */
class User extends ActiveRecord implements IdentityInterface {

    const ATTR_ID           = 'id';
    const ATTR_USERNAME     = 'username';
    const ATTR_PASSWORD     = 'password';
    const ATTR_AUTH_KEY     = 'auth_key';
    const ATTR_CREATE_STAMP = 'create_stamp';
    const ATTR_UPDATE_STAMP = 'update_stamp';

    /**
     * @return array
     */
    public function behaviors() {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'create_stamp',
                'updatedAtAttribute' => 'update_stamp',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [[static::ATTR_USERNAME, static::ATTR_PASSWORD], RequiredValidator::class],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id/* , 'status' => self::STATUS_ACTIVE */]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne([static::ATTR_AUTH_KEY => $token]);
    }

    public static function findByUsername($username) {
        return static::findOne([static::ATTR_USERNAME => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->auth_key === $authKey;
    }
}