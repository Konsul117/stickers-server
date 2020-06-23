<?php

namespace app\commands;

use app\models\db\Board;
use app\models\db\User;
use yii\base\Security;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Первичная установка приложения
 */
class UserController extends Controller {
    /** @var Security */
    private $security;

    public function __construct($id, $module, Security $security, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->security = $security;
    }

    /**
	 * Создание учётной записи пользователя.
	 */
	public function actionCreate() {
		$this->stdout('Добавление администратора' . PHP_EOL);

		$this->stdout('Логин: ' . PHP_EOL);
		$username = Console::stdin();

		$this->stdout('Пароль: ' . PHP_EOL);
		$password = Console::stdin();

		$transaction = \Yii::$app->db->beginTransaction();
		$result = $this->saveUserInner($username, $password);

		if ($result) {
		    $transaction->commit();
		    $this->stdout('Пользователь добавлен' . PHP_EOL, Console::FG_GREEN);
        } else {
		    $transaction->rollBack();
        }
	}

	private function saveUserInner(string $username, string $password): bool {
        $user           = new User();
        $user->username = $username;
        $user->password = $this->security->generatePasswordHash($password);

        if ($user->validate() === false) {
            $this->stdout('Ошибки при вводе данных: ' . print_r($user->getErrors(), true));

            return false;
        }

        if (!$user->save()) {
            $this->stderr('Ошибка при добавлении пользователя: ' . var_export($user->errors, true));

            return false;
        }

        $board = new Board();
        $board->author_id = $user->id;
        $board->index = 1;
        $board->title = 'Главная';

        if (!$board->save()) {
            $this->stderr('Ошибка при создании доски пользователя: ' . var_export($board->errors, true));

            return false;
        }

        return true;
    }

}
