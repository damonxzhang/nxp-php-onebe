<?php
namespace frontend\controllers;


use backend\models\ChargingLog;
use backend\models\ChargingTank;
use common\models\GlobalFun;

class ChargingLogController extends  FrontendBaseController{

    /**
     *  保存日志
     */
    public function actionLog(){

        $params = $this->validateRequest();
        $model = new ChargingLog();

        !empty($params['tankId']) && $tank = ChargingTank::findOne($params['tankId']);

        if(empty($tank)){
            return $this->outputJson('false','未知充电柜');
        }
        $model->setScenario('newSave');
        $model->setAttributes(array_merge($model->replaceKey($params) ,['tank_number' => $tank->number]));
        $model->setUserId();
        if($model->save()){

            return $this->outputJson('true','保存成功');
        }else{
            return $this->outputJson('false',GlobalFun::errorToString($model->getErrors()));
        }

    }




}