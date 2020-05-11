<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\ApiResponse;
use app\models\db\Board;
use app\models\db\Sticker;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class BoardController extends BaseController
{
    public $modelClass = Board::class;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'prepareDataProvider' => function () {
                    $result = new ActiveDataProvider();
                    $result->query = Board::find()
                        ->where([Board::ATTR_AUTHOR_ID => Yii::$app->user->id])
                        ->orderBy([Board::ATTR_INDEX => SORT_ASC])
                    ;

                    return $result;
                },
            ],
            'update' => [
                'findModel' => [$this, 'findModel'],
            ],
        ]);
    }

    public function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'batch' => ['POST', 'OPTIONS'],
        ]);
    }

    /**
     * @param int $id
     *
     * @return Board
     *
     * @throws NotFoundHttpException
     */
    public function findModel(int $id)
    {
        $result = Board::findOne([
            Board::ATTR_ID        => $id,
            Board::ATTR_AUTHOR_ID => Yii::$app->user->id,
        ]);

        if ($result !== null) {
            return $result;
        }

        throw new NotFoundHttpException("Object not found: $id");
    }

    public function actionBatch() {
        $ids = json_decode(Yii::$app->request->rawBody, true);

        if (is_array($ids) === false) {
            throw new BadRequestHttpException();
        }

        $belongsToUserIds = Board::find()
            ->select([Board::ATTR_ID])
            ->where([Board::ATTR_ID => $ids])
            ->andWhere([Board::ATTR_AUTHOR_ID => Yii::$app->user->id])
            ->column();

        $belongsToUserIds = array_map(function($item) {
            return (int)$item;
        }, $belongsToUserIds);

        if (count(array_diff($ids, $belongsToUserIds)) > 0) {
            throw new BadRequestHttpException('Некоторые доски вам не принадлежат');
        }

        $t = Yii::$app->db->beginTransaction();
        try {
            Board::deleteAll([
                Board::ATTR_ID        => $ids,
                Board::ATTR_AUTHOR_ID => Yii::$app->user->id,
            ]);

            Sticker::deleteAll([
                Sticker::ATTR_BOARD_ID  => $ids,
                Sticker::ATTR_AUTHOR_ID => Yii::$app->user->id,
            ]);
            $t->commit();
        } catch (Exception $e) {
            $t->rollBack();

            throw $e;
        }

        return new ApiResponse();
    }

    /**
     * @inheritDoc
     *
     * @param Board $model
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        parent::checkAccess($action, $model, $params);

        if (in_array($action, ['create', 'update']) && $model->validate()) {
            //проверка, что у пользователя уже нет доски с таким именем
            $isSameNameExists = Board::find()
                ->where([
                    Board::ATTR_TITLE     => $model->title,
                    Board::ATTR_AUTHOR_ID => Yii::$app->user->id,
                ])
                ->exists();

            if ($isSameNameExists) {
                throw new BadRequestHttpException('Доска с таким именем уже существует.');
            }
        }
    }
}