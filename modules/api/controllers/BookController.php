<?php

namespace app\modules\api\controllers;

use app\models\Book;
use app\models\LoginForm;
use app\models\Pager;
use app\models\User;
use Yii;
use yii\base\DynamicModel;
use yii\data\ArrayDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\VarDumper;
use yii\rest\ActiveController;
use yii\web\UploadedFile;

class BookController extends ActiveController
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
            ]
        ];
        // $behaviors['corsFilter'] = [
        //     'class' => Cors::class,
        //     'cors' => [
        //         // 'Origin' => ['*'],
        //         'Origin' => [(isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR'])],
        //         'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        //         // 'Access-Control-Request-Headers' => ['content-type', 'application/json', 'Authorization'],
        //         'Access-Control-Request-Headers' => ['*'],
        //         // 'Access-Control-Allow-Credentials' => true,
        //         'actions' => [

        //             'change-visibility' => [
        //                 'Access-Control-Allow-Credentials' => true,
        //             ],

        //             'upload' => [
        //                 'Access-Control-Allow-Credentials' => true,
        //             ],

        //             'user-books' => [
        //                 'Access-Control-Allow-Credentials' => false,
        //             ],

        //             'user-book' => [
        //                 'Access-Control-Allow-Credentials' => true,
        //             ],
        //             'delete-book' => [
        //                 'Access-Control-Allow-Credentials' => true,
        //             ],
        //             'edit-book' => [
        //                 'Access-Control-Allow-Credentials' => true,
        //             ],
        //             'read-book' => [
        //                 'Access-Control-Allow-Credentials' => true,
        //             ],
        //             'read-book-info' => [
        //                 'Access-Control-Allow-Credentials' => true,
        //             ],
        //             'user-read-book' => [
        //                 'Access-Control-Allow-Credentials' => true,
        //             ],





        //         ],
        //     ],
        // ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'optional' => ['user-books', 'user-book'],
            'except' => ['options'],
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
        // $actions['options'] = [
        //     'class' => 'yii\rest\OptionsAction',
        //     // optional:
        //     'collectionOptions' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
        //     'resourceOptions' => ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        // ];
        return $actions;
    }


    public function actionUpload()
    {
        if (! Yii::$app->user->identity->isAdmin) {
            $book = new Book(['scenario' => Book::SCENARIO_UPLOAD]);
            if ($book->load($this->request->post(), '')) {
                $book->user_id = Yii::$app->user->id;
                $book->file = UploadedFile::getInstanceByName('file');

                if ($book->upload()) {
                    if ($book->save(false)) {
                        $this->response->statusCode = 201;
                        return $this->asJson([
                            'data' => [
                                'book' => [
                                    "id" => $book->id,
                                    "title" => $book->title,
                                    "author" => $book->author,
                                    "description" => $book->description,
                                    "file_url" => Yii::$app->request->hostInfo . '/' . $book->file_html,
                                ],
                                'code' => 201,
                                'message' => 'Книга успешно загружена',
                            ]
                        ]);
                    }
                }

                $this->response->statusCode = 422;
                return $this->asJson([
                    'error' => [
                        'code' => 422,
                        'message' => 'Validation error',
                        'errors' => $book->errors,
                    ]
                ]);
            }
        }

        $this->response->statusCode = 400;
    }

    public function adminBooks()
    {
        $query = Book::getBook([
            'add_bublic_info' => true,
        ]);

        return $this->asJson([
            'data' => [
                'books' => $query,
                'code' => 200,
                'message' => 'Список книг получен',
            ]
        ]);;
    }







    public function actionUserBooks()
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 'admin') {
            return $this->adminBooks();
        }

        $query = Book::getBook([
            'add_public_book' => true,
        ]);

        if (! $this->request->get()) {
            return $this->asJson([
                'data' => [
                    'books' => $query,
                    'code' => 200,
                    'message' => 'Список книг получен',
                ]
            ]);
        }

        $pager = new Pager();
        $pager->load($this->request->get(), '');

        if ($pager->validate()) {
            // пагинация
            $dataProvider = new ArrayDataProvider([
                'allModels' => $query,
                'pagination' => [
                    'pageSize' => $pager->count,
                    'page' => $pager->page - 1,
                ]
            ]);

            return  $this->asJson([
                'data' => [
                    'books' => $dataProvider->getModels(),
                    'code' => 200,
                    'message' => 'Список книг для указанной страницы получен',
                    "total_books" => count($query),
                ]
            ]);
        }


        $this->response->statusCode = 422;
        return $this->asJson([
            'error' => [
                'code' => 422,
                'message' => 'Validation error',
                'errors' => $pager->errors,
            ]
        ]);
    }


    public function actionUserBook($id)
    {


        $book = Book::getBook([
            'book_id' => $id,
            'add_user_id' => true,
            'add_bublic_info' => true
        ]);

        if (! $book) {
            Yii::$app->response->statusCode = 104;
            return $this->asJson([1]);
        }

        // проверка что книга опубликована

        if (Yii::$app->user->isGuest) {
            if (! $book['is_public']) {
                Yii::$app->response->statusCode = 103;
                return $this->asJson([1]);
            }

            unset($book['user_id']);
            return $this->asJson([
                'data' => [
                    'book' => $book,
                    'code' => 200,
                    'message' => 'Информация о книге получена',
                ]
            ]);
        }

        
        // проверка что книга не usera 
        if ($book['user_id'] != Yii::$app->user->id) {
            Yii::$app->response->statusCode = 103;
            return $this->asJson([1]);
        }

        unset($book['user_id']);

        return $this->asJson([
            'data' => [
                'book' => $book,
                'code' => 200,
                'message' => 'Информация о книге получена',
            ]
        ]);
    }


    public function actionDeleteBook($id)
    {
        $book = Book::findOne($id);

        if (! $book) {
            Yii::$app->response->statusCode = 104;
            return $this->asJson([1]);
        }

        if ($book->user_id != Yii::$app->user->id) {
            Yii::$app->response->statusCode = 103;
            return $this->asJson([1]);
        }

        $file_name = Yii::getAlias('@webroot')
            . '/'
            . $book->file_html;

        if (file_exists($file_name)) {
            unlink($file_name);
        } else {
            return "book not found";
        }

        $book->delete();

        return $this->asJson([
            'data' => [
                'code' => 200,
                'message' => 'Книга успешно удалена',
            ]
        ]);
    }


    public function actionEditBook($id)
    {
        $book = Book::findOne($id);

        if (! $book) {
            Yii::$app->response->statusCode = 104;
            return $this->asJson([1]);
        }

        if ($book->user_id != Yii::$app->user->id) {
            Yii::$app->response->statusCode = 103;
            return $this->asJson([1]);
        }


        $book->load(Yii::$app->request->post(), '');
        if (!$book->save()) {
            var_dump($book->errors);
            die;
        }


        return $this->asJson([
            'data' => [
                'book' => Book::getBook(['book_id' => $id]),
                'code' => 200,
                'message' => 'Информация о книге обновлена',
            ]
        ]);
    }


    public function actionReadBook(int $id)
    {
        $book = Book::findOne($id);


        if (! $book) {
            Yii::$app->response->statusCode = 104;
            return $this->asJson([1]);
        }

        if ($book->user_id != Yii::$app->user->id) {
            Yii::$app->response->statusCode = 103;
            return $this->asJson([1]);
        }

        if ($this->request->isPost) {

            $progress = $this->request->post('progress');
            $model = DynamicModel::validateData(compact('progress'), [
                [['progress'], 'required'],
                [['progress'], 'integer'],
                [['progress'], 'integer', 'min' => 0, 'max' => 100],
            ]);

            if ($model->hasErrors()) {
                $this->response->statusCode = 422;
                return $this->asJson([
                    'error' => [
                        'code' => 422,
                        'message' => 'Validation error',
                        'errors' => $model->errors,
                    ]
                ]);
            } else {
                $book->scenario = Book::SCENARIO_PROGRESS;
                $book->load($this->request->post(), '');
                if (!$book->save()) {
                    $this->response->statusCode = 422;
                    return $this->asJson([
                        'error' => [
                            'code' => 422,
                            'message' => 'Validation error',
                            'errors' => $book->errors,
                        ]
                    ]);
                }
            }
        }

        return $this->asJson([
            'data' => [
                'book_id' => $book->id,
                'progress' => $book->progress,
                'code' => 200,
                'message' => 'Прогресс чтения сохранен',
            ]
        ]);
    }


    public function actionReadBookInfo(int $id)
    {
        $book = Book::findOne($id);

        if (! $book) {
            Yii::$app->response->statusCode = 104;
            return $this->asJson([1]);
        }

        if ($book->user_id != Yii::$app->user->id) {
            Yii::$app->response->statusCode = 103;
            return $this->asJson([1]);
        }

        return $this->asJson([
            'data' => [
                'book_id' => $book->id,
                'progress' => $book->progress,
                'code' => 200,
                'message' => 'Прогресс чтения получен',
            ]
        ]);
    }


    public function actionUserReadBooks()
    {
        $books = Book::getBook([
            'user_id' => Yii::$app->user->id,
            'progress' => true,
            'progress_isset' => true,
        ]);

        return $this->asJson([
            'data' => [
                'books' => $books,
                'code' => 200,
                'message' => 'Список книг, которые читает пользователь, получен',
                'total_books' => count($books)
            ]
        ]);
    }


    public function actionChangeVisibility($id)
    {
        if (Yii::$app->user->identity->role != 'admin') {
            Yii::$app->response->statusCode = 103;
            return $this->asJson([1]);
        }

        $book = Book::findOne($id);

        if (! $book) {
            Yii::$app->response->statusCode = 104;
            return $this->asJson([1]);
        }

        $book->is_public = null;
        $book->scenario = Book::SCENARIO_PUBLIC;
        $book->load(Yii::$app->request->post(), '');

        if (! $book->validate()) {
            $this->response->statusCode = 422;
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $book->errors,
                ]
            ]);
        }

        if (!$book->save()) {
            var_dump($book->errors);
            die;
        }


        return $this->asJson([
            'data' => [
                'book' => [
                    'id' => $book->id,
                    'is_public' => $book->is_public ? true : false
                ],
                'code' => 200,
                'message' => 'Доступность книги изменена',
            ]
        ]);
    }
}
