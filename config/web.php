<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'R21uGgomKG1pHwQge8Jkr3CiU9M3eNWl',
            'baseUrl' => '',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            // ...
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // используем "pretty" в режиме отладки
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],

            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->statusCode == 401) {
                    $response->data = [
                        "message" => "Login failed"
                    ];
                    $response->statusCode = 403;
                }

                if ($response->statusCode == 104) {
                    $response->data = [                        
                        "message" => "Not found",
                        "code" => 404
                    ];
                    $response->statusCode = 404;
                }

                if ($response->statusCode == 103) {
                    $response->data = [                        
                        "message" => "Forbidden for you",                        
                    ];
                    $response->statusCode = 403;
                }

                
            },
        
        ],

        
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 0 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            // 'baseUrl' => 'api',
            'rules' => [
                'GET /' => 'site/index',
                'GET api' => 'site/index',

                'OPTIONS api/registration' => 'api/user/options',
                'OPTIONS /api/login' => 'api/user/options',
                'OPTIONS /api/logout' => 'api/user/options',
                'OPTIONS /api/user/settings' => 'api/user/options',
                'OPTIONS api/books/<id:\d+>' => 'api/book/options',
                'OPTIONS api/books/upload' => 'api/book/options',
                'OPTIONS api/books/<id:\d+>' => 'api/book/options',
                'OPTIONS api/books' => 'api/book/options',
                'OPTIONS api/books/<id:\d+>/change-visibility' => 'api/book/options',
                'OPTIONS api/books/<id:\d+>/progress' => 'api/book/options',
                

                // список всех книг public + pager
                'GET api/books' => 'api/book/user-books',
                'GET api/books/<id:\d+>' => 'api/book/user-book',
                
                'POST api/registration' => 'api/user/register',
                'POST api/login' => 'api/user/login',
                'POST api/logout' => 'api/user/logout',

                'POST api/user/settings' => 'api/user/set-settings',
                'GET api/user/settings' => 'api/user/get-settings',
                

                
                'POST api/books/upload' => 'api/book/upload',

                'POST api/books/<id:\d+>/progress' => 'api/book/read-book',                
                'GET api/books/<id:\d+>/progress' => 'api/book/read-book-info',
                
                'GET api/books/progress' => 'api/book/user-read-books',

                
                'DELETE api/books/<id:\d+>' => 'api/book/delete-book',
                'PATCH api/books/<id:\d+>' => 'api/book/edit-book',
                
                'PUT api/books/<id:\d+>/change-visibility' => 'api/book/change-visibility',
                
            ],
        ]


    ],

    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
    ],

    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['5.144.96.34', '127.0.0.1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['5.144.96.34', '127.0.0.1'],
    ];
}

return $config;
