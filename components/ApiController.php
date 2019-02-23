<?php

namespace app\components;

use app\enum\ResponseCodeEnum;
use app\exceptions\ApiException;
use app\models\ApiResponse;
use Yii;
use yii\base\Module;
use yii\filters\Cors;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 * Суперкласс контроллера.
 */
class ApiController extends Controller {

	/** @var ApiResponse */
	protected $response;

	/**
	 * @inheritdoc
	 *
	 * @param ApiResponse $response Модель ответа на запрос API
	 */
	public function __construct(string $id, Module $module, ApiResponse $response, array $config = []) {
		$this->response = $response;
		parent::__construct($id, $module, $config);
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			[
				'class' => Cors::class,
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		Yii::$app->response->format = Response::FORMAT_JSON;

		return parent::beforeAction($action);
	}

	/**
	 * @inheritdoc
	 */
	public function runAction($id, $params = []) {
		try {
			return parent::runAction($id, $params);
		}
		catch (ApiException $e) {
			$response          = new ApiResponse();
			$response->code    = ResponseCodeEnum::CODE_ERROR;
			$response->message = $e->getMessage();

			return $response;
		}
		catch (BadRequestHttpException $e) {
			$response          = new ApiResponse();
			$response->code    = ResponseCodeEnum::CODE_ERROR;
			$response->message = $e->getMessage();

			return $response;
		}
		catch (\Throwable $e) {
			$response          = new ApiResponse();
			$response->code    = ResponseCodeEnum::CODE_EXCEPTION;
			$response->message = 'Внутренняя ошибка сервера';

			return $response;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function afterAction($action, $result) {
		parent::afterAction($action, $result);

		if ($this->response->validate() === false) {
			$response          = new ApiResponse();
			$response->code    = ResponseCodeEnum::CODE_EXCEPTION;
			$response->message = 'Внутренняя ошибка сервера';

			return $response;
		}

		return $this->response;
	}
}
