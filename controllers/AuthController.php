<?php

namespace app\controllers;

use app\models\db\User;
use app\models\dto\AjaxResponse;
use app\models\dto\auth\AuthRequest;
use app\models\dto\auth\UserFront;
use Yii;
use yii\base\Security;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class AuthController extends Controller
{
    /** @var Security */
    private $security;

    /** @var \yii\web\User */
    private $userService;

    public function __construct($id, $module, Security $security, \yii\web\User $userService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->security    = $security;
        $this->userService = $userService;
    }

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
                'optional' => [
                    'login',
                ],
            ],
            [
                'class' => AccessControl::class,
                'only'  => ['login', 'logout', 'status'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['login'],
                        'roles'   => ['?'],
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['logout', 'status'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
            [
                'class' => VerbFilter::class,
                'actions' => [
                    static::ACTION_LOGIN  => ['post'],
                    static::ACTION_LOGOUT => ['post'],
                    static::ACTION_TOKEN  => ['get'],
                ],
            ]
        ]);
    }

    /**
     * @return AjaxResponse
     *
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {
        $request = new AuthRequest();
        $payload = json_decode(Yii::$app->request->getRawBody(), true);
        if (!$request->load($payload, '')) {
            throw new BadRequestHttpException('Данные не получены');
        }

        if ($request->validate() === false) {
            throw new BadRequestHttpException('Некорретные данные формы');
        }

        /** @var User|null $user */
        $user = User::findOne([
            User::ATTR_USERNAME => $request->login,
        ]);

        if ($user === null) {
            throw new BadRequestHttpException('Пользователь не найден');
        }

        if ($this->security->validatePassword($request->password, $user->password) === false) {
            throw new BadRequestHttpException('Пользователь не найден');
        }

        $user->auth_key = $this->security->generateRandomString();
        if ($user->save() === false) {
            throw new ServerErrorHttpException();
        }

        $response       = new AjaxResponse();
        $response->data = $this->convertUserToFront($user);

        return $response;
    }
    const ACTION_LOGIN = 'login';

    public function actionStatus($token)
    {
        /** @var User|null $user */
        $user = $this->userService->loginByAccessToken($token);

        $response       = new AjaxResponse();
        $response->data = $this->userService->isGuest ? null : $this->convertUserToFront($user);

        return $response;
    }
    const ACTION_TOKEN = 'token';

    public function actionLogout()
    {
        /** @var User $user */
        $user = $this->userService->getIdentity();
        $user->auth_key = '';
        if ($user->save() === false) {
            throw new ServerErrorHttpException();
        }

        $this->userService->logout();
    }
    const ACTION_LOGOUT = 'logout';

    private function convertUserToFront(User $model): UserFront
    {
        $result        = new UserFront();
        $result->name  = $model->username;
        $result->token = $model->auth_key;

        return $result;
    }
}