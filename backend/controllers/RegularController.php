<?php
namespace backend\controllers;

use backend\models\MachineRegular;
use backend\models\MachineRoster;
use backend\models\MachineCheck;
use yii\web\Controller;
use yii\helpers\Json;

class RegularController extends Controller
{
   
    /**
     * @return bool
     */
    public function actionRegular()
    {
        $query = MachineRegular::find();
        $query->where(['type' => MachineRegular::TYPE_N]);
        $query->andWhere(['status' => MachineRegular::STATUS_YES]);
        $query->asArray();
        $regularData = $query->all();
        if (!$regularData) {
            return false;
        }
        $toady = date("w", time());
        $workDate = date("Y-m-d", time());
        foreach ($regularData as $regular) {
            foreach (Json::decode($regular['week']) as $week) {
                if ($toady == $week) {
                    $roster = MachineRoster::findOne(['class' =>$regular['class'], 'week' => $toady,'work_date' => $workDate]);
                    if ($roster){
                        foreach (Json::decode($regular['facility_id']) as $facilityId) {
                            $insert            = [
                                'facility_id' => $facilityId,
                                'regular_id'  => $regular['id'],
                                'roster_id'   => $roster->id,
                                'class'       => $regular['class'],
                                'area'        => $regular['area'],
                                'process'     => $regular['process'],
                                'check_time'  => date('Y-m-d', time()),
                            ];
                            $checkData = MachineCheck::find()->where($insert)->one();
                            if ($checkData){
                                continue;
                            }
                            $insert['status']      = MachineCheck::STATUS_NO;
                            $insert['create_time'] = date('Y-m-d H:i:s', time());
                            $model             = new MachineCheck();
                            $model->attributes = $insert;
                            $model->save(false);
                        }
                    }
                }
            }
        }
        return true;
    }
}


