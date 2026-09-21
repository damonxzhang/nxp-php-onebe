<?php
namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use backend\models\Car;
use backend\models\CarDefect;
use backend\models\CarJudge;

use yii\web\Controller;
class CarlinkController extends Controller{

    public function actionView(){
        $id            = Yii::$app->request->get('id');
        $carModel      = Car::findOne($id);
        $carModel->setImagesPath();
        $carModel->setSecondImagesPath();
        $carJudgeModel = CarJudge::find()->where(['car_id' => $id])->asArray()->all();
        $dafectArr     = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON])->asArray()->all(),'defect','id');
       
        return $this->render('view', [
            'carModel'      => $carModel,
            'dafectArr'     => $dafectArr,
            'carJudgeModel' => $carJudgeModel
        ]);
    }

}