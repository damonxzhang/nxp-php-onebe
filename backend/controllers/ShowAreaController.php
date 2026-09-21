<?php
namespace backend\controllers;


use yii\web\Controller;


class ShowAreaController extends Controller
{
    public function actionIndex(){
                
        return $this->render('index');

    }

}