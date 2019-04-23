<?php

namespace app\controllers;

use app\models\db\Sticker;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Контроллер тикетов.
 */
class TicketController extends ActiveController {

	public $modelClass = Sticker::class;

	/**
	 * {@inheritdoc}
	 */
	public function behaviors() {
		return array_merge(parent::behaviors(), [
			'contentNegotiator' => [
				'class'   => ContentNegotiator::class,
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
			],
		]);
	}

	public function verbs() {
		return [];
	}

	/**
	 * Получение тикета.
	 *
	 * @param string $id Идентификатор
	 */
//	public function actionGet(string $id) {
//		$this->response->code = ResponseCodeEnum::CODE_OK;
//		$this->response->data = 'test';
//	}
}
