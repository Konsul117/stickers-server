<?php

namespace app\controllers;

use app\components\ApiController;
use app\enum\ResponseCodeEnum;

/**
 * Контроллер на случай некоректного URL.
 */
class ErrorController extends ApiController {

	/**
	 * Экшн возврата ошибки.
	 */
	public function actionIndex() {
		$this->response->code    = ResponseCodeEnum::CODE_ERROR;
		$this->response->message = 'Неизвестная команда';
	}
}