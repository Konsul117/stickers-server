<?php

namespace app\components\actions;

use Yii;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class UpdateAction extends \yii\rest\UpdateAction
{
    /**
     * Мутируем выполнение, вызывая проверку доступа после загрузки данных из запроса в модель,
     * чтобы можно было проверить полученные данные перед сохранением.
     *
     * @inheritDoc
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        $model->scenario = $this->scenario;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}