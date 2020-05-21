<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\ApiResponse;
use app\models\db\Board;
use app\models\db\Sticker;
use app\models\dto\sticker\MoveChange;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Контроллер тикетов.
 */
class TicketController extends BaseController {

	public $modelClass = Sticker::class;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'prepareDataProvider' => function () {
                    $boardId = Yii::$app->request->get('boardId');

                    if (!$boardId || !is_numeric($boardId)) {
                        return null;
                    }

                    $result = new ActiveDataProvider();
                    $result->query = Sticker::find()
                        ->where([Sticker::ATTR_AUTHOR_ID => Yii::$app->user->id])
                        ->andWhere([Sticker::ATTR_BOARD_ID => $boardId])
                    ;

                    return $result;
                },
            ],
            'update' => [
                'findModel' => [$this, 'findModel'],
            ],
        ]);
    }

    /**
     * @param int $id
     *
     * @return Sticker
     *
     * @throws NotFoundHttpException
     */
    public function findModel(int $id)
    {
        $result = Sticker::findOne([
            Sticker::ATTR_ID        => $id,
            Sticker::ATTR_AUTHOR_ID => Yii::$app->user->id,
        ]);

        if ($result !== null) {
            return $result;
        }

        throw new NotFoundHttpException("Object not found: $id");
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

		/** @var MoveChange[] $movements */
		$movements = [];
		foreach (Yii::$app->request->post() as $item) {
			$move = new MoveChange();

			if (!$move->load($item, '') || !$move->validate()) {
				throw new BadRequestHttpException('Invalid ticket: ' . var_export($move->errors, true));
			}

			$movements[$move->stickerId] = $move;
		}

		/** @var Sticker[] $storedModels */
		$storedModels = Sticker::findAll([
			Sticker::ATTR_ID => array_keys($movements),
		]);

		$storedModels = ArrayHelper::index($storedModels, Sticker::ATTR_ID);

		$nonExistentModelsIds = array_diff(array_keys($movements), array_keys($storedModels));
		if (!empty($nonExistentModelsIds)) {
			throw new BadRequestHttpException('Non existent stickers: ' . implode($nonExistentModelsIds));
		}

		$transaction = Yii::$app->db->beginTransaction();
		try {
			foreach ($movements as $movement) {
				$storedModel = $storedModels[$movement->stickerId];
				$storedModel->index = $movement->index;
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

    /**
     * @inheritDoc
     *
     * @param Sticker $model
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        parent::checkAccess($action, $model, $params);

        if (in_array($action, ['create', 'update']) && $model->validate()) {
            //проверка, что доска, к которой будет привязан стикер, принадлежит текущему пользователю
            $isBoardValid = Board::find()
                ->where([
                    Board::ATTR_ID        => $model->board_id,
                    Board::ATTR_AUTHOR_ID => Yii::$app->user->id,
                ])
                ->exists();

            if ($isBoardValid === false) {
                throw new ForbiddenHttpException('Указанная доска не существует или недоступна.');
            }
        }

        if ($action === 'delete') {
            if ($model->author_id !== Yii::$app->user->id) {
                throw new ForbiddenHttpException('Указанная доска не существует или недоступна.');
            }
        }
    }
}
