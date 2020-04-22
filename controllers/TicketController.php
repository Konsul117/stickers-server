<?php

namespace app\controllers;

use app\models\ApiResponse;
use app\models\db\Sticker;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\IndexAction;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

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
			[
				'class' => Cors::class,
			],
            [
                'class'       => CompositeAuth::class,
                'authMethods' => [
                    HttpBearerAuth::class,
                ],
            ],
		]);
	}

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class'       => IndexAction::class,
                'modelClass'  => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'prepareDataProvider' => function () {
                    $result = new ActiveDataProvider();
                    $result->query = Sticker::find()
                        ->where([Sticker::ATTR_AUTHOR_ID => Yii::$app->user->id]);

                    return $result;
                },
            ],
        ]);
    }

	/**
	 * Обновление пачки тикетов.
	 *
	 * @return ApiResponse
	 *
	 * @throws BadRequestHttpException
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 */
	public function actionBatch() {
		if (!is_array(Yii::$app->request->post())) {
			throw new BadRequestHttpException();
		}

		/** @var Sticker[] $inputModels */
		$inputModels = [];
		foreach (Yii::$app->request->post() as $item) {
			$model = new Sticker();

			if (!$model->load($item, '') || !$model->validate()) {
				throw new BadRequestHttpException('Invalid ticket: ' . var_export($model->errors, true));
			}

			$inputModels[$model->id] = $model;
		}

		/** @var Sticker[] $storedModels */
		$storedModels = Sticker::findAll([
			Sticker::ATTR_ID => array_keys($inputModels),
		]);

		$storedModels = ArrayHelper::index($storedModels, Sticker::ATTR_ID);

		$nonExistentModelsIds = array_diff(array_keys($inputModels), array_keys($storedModels));
		if (!empty($nonExistentModelsIds)) {
			throw new BadRequestHttpException('Non existent stickers: ' . implode($nonExistentModelsIds));
		}

		$transaction = Yii::$app->db->beginTransaction();
		try {
			foreach ($inputModels as $inputModel) {
				$storedModel = $storedModels[$inputModel->id];
				$storedModel->attributes = $inputModel->attributes;
				if (!$storedModel->save()) {
					throw new ServerErrorHttpException('Error while saving ticket');
				}
			}
		} catch (Exception $e) {
			$transaction->rollBack();
			throw $e;
		}

		$transaction->commit();

		return new ApiResponse();
	}
}
