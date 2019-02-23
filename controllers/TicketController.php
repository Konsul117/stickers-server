<?php

namespace app\controllers;

use app\components\ApiController;
use app\enum\ResponseCodeEnum;

/**
 * Контроллер тикетов.
 */
class TicketController extends ApiController {

	/**
	 * Получение тикета.
	 *
	 * @param string $id Идентификатор
	 */
	public function actionGet(string $id) {
		$this->response->code = ResponseCodeEnum::CODE_OK;
		$this->response->data = 'test';
	}
}
