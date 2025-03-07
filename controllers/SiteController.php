<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->asJson(['message' => 'api-books']);
    }

    
    public function actionError()
    {
        Yii::$app->response->statusCode = 400;
        return $this->asJson(['error' => 'Bad request']);
    }
}
