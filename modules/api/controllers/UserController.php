<?php

namespace app\modules\api\controllers;

use app\models\LoginForm;
use app\models\User;
use app\models\UserBookSetting;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\ActiveController;


class UserController extends ActiveController
{
    public $enableCsrfValidation = '';
    public $modelClass = '';

    public function behaviors()
    {
        // $http = ["REQUEST_SCHEME"] . '://';
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => Cors::class,            
            'cors' => [
                'Origin' => [(isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR'])],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => false,
                'actions' => [
                    'logout' => [
                        'Access-Control-Allow-Credentials' => true,
                    ],
                    'set-settings' => [
                        'Access-Control-Allow-Credentials' => true,
                    ],
                    'get-settings' => [
                        'Access-Control-Allow-Credentials' => true,
                    ],
                    
                ]
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'optional' => ['register', 'login', ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // отключить действия "delete" и "create"
        unset(
            $actions['delete'],
            $actions['create'],
            $actions['update'],
            $actions['view'],
            $actions['index']
        );

        return $actions;
    }


    public function actionRegister()
    {
        // var_dump(
        //     Yii::$app->security->generatePasswordHash('Reader1'),
        //     Yii::$app->security->generatePasswordHash('Reader2')
        // );
        // die;

        $user = new User();
        if ($user->load($this->request->post(), '')) {
            if ($user->validate()) {
                $user->password = Yii::$app->security->generatePasswordHash($user->password);
                if ($user->save(false)) {
                    $this->response->statusCode = 201;
                    return $this->asJson([
                        'data' => [
                            'user' => [
                                "id" => $user->id,
                                "name" => $user->name,
                                "email" => $user->email,
                            ],
                            'code' => 201,
                            'message' => 'Пользователь создан',
                        ]
                    ]);
                }
            }

            $this->response->statusCode = 422;
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $user->errors,
                ]
            ]);
        }

        $this->response->statusCode = 400;
    }

    public function actionLogin()
    {
        $login = new LoginForm();
        if ($login->load($this->request->post(), '')) {
            // var_dump($login->email, User::findByUsername($login->email)); die;
            if ($login->validate()) {
                $login->scenario = 'login';
                if ($login->validate()) {
                    $user = $login->getUser();
                    $user->token = Yii::$app->security->generateRandomString();
                    $user->save(false);
                    $this->response->statusCode = 201;
                    return $this->asJson([
                        'data' => [
                            "token" => $user->token,
                            'user' => [
                                "id" => $user->id,
                                "name" => $user->name,
                                "email" => $user->email,
                                "role" => $user->role,
                            ],
                            'code' => 201,
                            'message' => 'Успешная авторизация',
                        ]
                    ]);
                }
                $this->response->statusCode = 422;
                return $this->asJson([
                    'error' => [
                        'code' => 422,
                        'message' => 'Неправильные логин или пароль',
                    ]
                ]);
            }

            $this->response->statusCode = 422;
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $login->errors,
                ]
            ]);
        } else {
            $this->response->statusCode = 400;
        }
    }

    public function actionLogout()
    {
        if ($user = User::findOne(Yii::$app->user->id)) {
            $user->token = null;
            $user->save(false);
            $this->response->statusCode = 204;
            return '';
        }
    }


    public function actionSetSettings()
    {
        $settings = UserBookSetting::findOne(['user_id' => Yii::$app->user->id]);
        if (! $settings) {
            $settings = new UserBookSetting();
            $settings->user_id = Yii::$app->user->id;
        }

        $settings->load($this->request->post(), '');
        if ($settings->validate()) {
            if ($settings->save()) {
                return $this->asJson([
                    "data" => [
                        "settings" => $this->request->post(),
                        "code" => 200,
                        "message" => "Настройки чтения сохранены"
                    ]
                ]);
            } else {
                var_dump($settings->errors);
            }
        }

        $this->response->statusCode = 422;
        return $this->asJson([
            'error' => [
                'code' => 422,
                'message' => 'Validation error',
                'errors' => $settings->errors,
            ]
        ]);
    }


    public function actionGetSettings()
    {
        $settings = UserBookSetting::findOne(['user_id' => Yii::$app->user->id])->attributes;
        unset($settings['id'],$settings['user_id'],);

        return $this->asJson([
            "data" => [
                "settings" => $settings,
                "code" => 200,
                "message" => "Настройки чтения получены"
            ]
        ]);
    }
}
