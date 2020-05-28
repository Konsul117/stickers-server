<?php

namespace app\components;

use app\components\actions\CreateAction;
use app\components\actions\UpdateAction;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;

abstract class BaseController extends ActiveController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'create' => [
                'class' => CreateAction::class,
            ],
            'update' => [
                'class' => UpdateAction::class,
            ],
        ]);
    }
}