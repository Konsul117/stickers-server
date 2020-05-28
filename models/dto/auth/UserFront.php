<?php

namespace app\models\dto\auth;

/**
 * Фронтовая модель пользователя.
 */
class UserFront
{
    /** @var string */
    public $name;

    /** @var string */
    public $token;
}