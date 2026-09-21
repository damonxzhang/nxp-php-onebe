<?php
namespace backend\controllers;


use yii\web\Controller;


class ShowViewController extends Controller
{
    public function actionIndex(){
                
        return $this->render('index');

    }

}