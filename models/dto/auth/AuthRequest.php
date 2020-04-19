<?php

namespace app\models\dto\auth;

use yii\base\Model;
use yii\validators\RequiredValidator;

class AuthRequest extends Model
{
    /** @var string */
    public $login;
    const ATTR_LOGIN = 'login';

    /** @var string */
    public $password;
    const ATTR_PASSWORD = 'password';

    public function rules()
    {
        return [
            [[static::ATTR_LOGIN, static::ATTR_PASSWORD], RequiredValidator::class],
        ];
    }
}