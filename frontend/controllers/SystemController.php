<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class SystemController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionServerTime()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'code' => 200,
            'timestamp' => time(),
            'datetime' => date('Y-m-d H:i:s')
        ];
    }
	
}