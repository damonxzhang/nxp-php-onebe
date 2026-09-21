<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\models\WashClothesUser;
use backend\models\WashClothesLog;
use backend\models\WashClothes;

class WashController extends Controller
{
    //刷卡页面
    public function actionCard()
    {
        return $this->render('card');
    }

    //刷卡页面
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 插入刷卡数据
     */
    public function actionInsert()
    {
        //只能是周一周二领取,周五上午投放
        $today = date('N');
        if(in_array($today,[3,4,6,7])){
            return 1;
        }

        $time = time();
        //只能每周五上午7点到12点之间投放
        if($today == 5){
            $startTimeToday = strtotime(date('Y-m-d 07:00:00'));
            $endTimeToday   = strtotime(date('Y-m-d 14:00:00'));
            if ($time < $startTimeToday || $time > $endTimeToday) {
                return 1;
            }
        }

        $get  = Yii::$app->request->get();
        $rfid = $get['rfid'] ?? '';
        $card = $get['card'] ?? '';
        if (empty($rfid) || strlen($rfid) <= 23) {
            return 1;
        }
        
        //检查当天是否有刷卡记录,如果当天已经刷完卡了就不要单独记录到数据库中
        $startToday = date('Y-m-d 00:00:00');
        $endToday   = date('Y-m-d 23:59:59');
        $washClothesLog = WashClothesLog::find()->where(['rfid' => $rfid])->andWhere(['>=','create_time',$startToday])->andWhere(['<','create_time',$endToday])->one();
        if($washClothesLog){
            return 1;
        }

        $date = date('Y-m-d H:i:s');
        $log = new WashClothesLog();
        $log->rfid = $rfid;
        $log->card = $card;
        $log->create_time = $date;
        $log->save(false);
        
        //周五投放
        if ($today == 5){
            $firstDayOfWeek = date('Y-m-d 00:00:00', strtotime("this week"));// 获取本周第一天
            $lastDayOfWeek  = date('Y-m-d 23:59:59', strtotime("this week +6 days")); // 获取本周最后一天
            $washClothes= WashClothes::find()->where(['rfid' => $rfid])->andWhere(['>=','invest_time',$firstDayOfWeek])->andWhere(['<','invest_time',$lastDayOfWeek])->one();
            if($washClothes){
                return 1;
            }

            $washClothesUser = WashClothesUser::findOne(['rfid' => $rfid]);
            $pid      = empty($washClothesUser) ? '' : $washClothesUser->pid;
            $userName = empty($washClothesUser) ? '' : $washClothesUser->user_name;
            $type     = empty($washClothesUser) ? 0 : $washClothesUser->type;

            $model              = new WashClothes();
            $model->rfid        = $rfid;
            $model->pid         = $pid;
            $model->user_name   = $userName;
            $model->type        = $type;
            $model->create_time = date('Y-m-d H:i:s', time());
            $model->invest_time = date('Y-m-d H:i:s', time());
            $model->receive_time= '';
            $model->save(false);
            return 1;
        }

        //周一周二领取静电服
        $lastWeekStart = date('Y-m-d 00:00:00', strtotime('last week Monday', time()));// 上周开始时间
        $lastWeekEnd   = date('Y-m-d 23:59:59', strtotime('last week Sunday 23:59:59', time())); // 上周结束时间
        $washClothes   = WashClothes::find()->where(['rfid' => $rfid])->andWhere(['>=','invest_time',$lastWeekStart])->andWhere(['<','invest_time',$lastWeekEnd])->one();
        if($washClothes && empty($washClothes->receive_time)){
            $washClothes->receive_time = $date;
            $washClothes->save(false);
        }
        return 1;
    }

    /**
     * 前端定时接口
     */
    public function actionSyncAndGetRecords()
    {

        //只能是周一周二领取,周五上午投放
        $today = date('N');
        if(in_array($today,[3,4,6,7])){
            $weekArr = ["3"=>"星期三","4"=>"星期四","6"=>"星期六","7"=>"星期日"];
            return $this->asJson(['code' => 500, 'msg' =>  '今天是'.$weekArr[$today].',不能刷卡领取或者清洗!']);
        }

        if($today == 5){
            $fridayTime = time();
            $startTimeToday = strtotime(date('Y-m-d 07:00:00'));
            $endTimeToday   = strtotime(date('Y-m-d 14:00:00'));
            if ($fridayTime < $startTimeToday || $fridayTime > $endTimeToday) {
                return $this->asJson(['code' => 500, 'msg' => '只能每周五上午7点到14点之间投放']);
            }
        }

        $startToday = date('Y-m-d 00:00:00');
        $endToday   = date('Y-m-d 23:59:59');
       
        $lastClothesLog = WashClothesLog::find()->where(['>=','create_time',$startToday])->orderBy(['id' => SORT_DESC])->one();
        if(empty($lastClothesLog)){
            return $this->asJson(['code' => 500, 'msg' => '暂无刷卡数据']);
        }

        $thisWeekStart   = date('Y-m-d 00:00:00', strtotime("this week"));// 获取本周第一天
        $lastFridayStart = date('Y-m-d 00:00:00', strtotime('last week Friday 00:00:00', time()));// 上周五开始时间
        $lastFridayEnd   = date('Y-m-d 23:59:59', strtotime('last week Friday 23:59:59', time())); //上周五结束时间

        $lastWashClothes    = WashClothes::find()->where(['>=','invest_time',$lastFridayStart])->andWhere(['<','invest_time',$lastFridayEnd])->andFilterWhere(['not', ['rfid' => '']])->asArray()->all();
        $lastWashClothesArr = ArrayHelper::index($lastWashClothes, 'rfid');
        
        $thisWashClothes    = WashClothes::find()->where(['not', ['rfid' => null]])->andWhere(['>=','invest_time',$thisWeekStart])->asArray()->all();
        $thisWashClothesArr = ArrayHelper::index($thisWashClothes, 'rfid');
        
        $data = [];
        $todayClothesLog = WashClothesLog::find()->where(['>=','create_time',$startToday])->andWhere(['<','create_time',$endToday])->orderBy(['id' => SORT_DESC])->asArray()->all();
        foreach ($todayClothesLog as $value) {
            $washClothesUser = WashClothesUser::findOne(['rfid' => $value['rfid']]);
            if(!$washClothesUser){
                continue;
            }
            $pid = $washClothesUser->pid;
            $userName = $washClothesUser->user_name;
            $exitPid = ArrayHelper::keyExists($pid, $data) ? true : false;
            if(!$exitPid){
                $hatInfo = $clothInfo = $pantsInfo = '';
                $hatColor = $clothColor = $pantsColor = '#FFFFFF';
                $typesArr = WashClothesUser::find()->select('type')->where(['pid' => $pid,'status' => WashClothesUser::STATUS_YES])->column();
                if(!empty($typesArr)){
                    foreach ($typesArr as $typesValue){
                        switch ($typesValue) {
                            case WashClothesUser::TYPE_HAT:
                                $hatInfo = $today == 5 ? '本周未投放' :'上周未投放';
                                $hatColor = '#dc3545';
                                break;
                            case WashClothesUser::TYPE_CLOTH:
                                $clothInfo = $today == 5 ? '本周未投放' :'上周未投放';
                                $clothColor = '#dc3545';
                                break;
                            case WashClothesUser::TYPE_PANTS:
                                $pantsInfo = $today == 5 ? '本周未投放' :'上周未投放';
                                $pantsColor = '#dc3545';
                                break;
                        }
                    }
                }
                $userInfo   = UserInfo::findOne(['pid' => $pid]);
                $data[$pid] = [
                    'id'          => $value['id'],
                    'user_name'   => $userInfo ? $userInfo->chinese_name : '',
                    'pid'         => $pid,
                    'area'        => $userInfo ? ArrayHelper::getValue(UserInfo::$areaArr,$userInfo->area,'') : '',
                    'hat'         => ['wash_time' => '','info' => $hatInfo,'color' => $hatColor],
                    'cloth'       => ['wash_time' => '','info' => $clothInfo,'color' => $clothColor],
                    'pants'       => ['wash_time' => '','info' => $pantsInfo,'color' => $pantsColor]
                ];
            }
            $rfidExists = ArrayHelper::keyExists($value['rfid'], $lastWashClothesArr) ? true : false;

            //周一周二领取
            if ($today == 1 || $today == 2) {
                switch ($washClothesUser->type) {
                    case WashClothesUser::TYPE_HAT:
                        if($rfidExists){
                            $data[$pid]['hat']['wash_time'] = $lastWashClothesArr[$value['rfid']]['receive_time'];
                            $data[$pid]['hat']['info']      = $userName.'本周领取成功';
                            $data[$pid]['hat']['color']     = '#28a745';
                        }else{
                            $data[$pid]['hat']['wash_time'] = '';
                            $data[$pid]['hat']['info']      = $userName.'上周未投放';
                            $data[$pid]['hat']['color']     = '#dc3545';
                        }
                        break;
                    case WashClothesUser::TYPE_CLOTH:
                        if($rfidExists){
                            $data[$pid]['cloth']['wash_time'] = $lastWashClothesArr[$value['rfid']]['receive_time'];
                            $data[$pid]['cloth']['info']      = $userName.'本周领取成功';
                            $data[$pid]['cloth']['color']     = '#28a745';
                        }else{
                            $data[$pid]['cloth']['wash_time'] = '';
                            $data[$pid]['cloth']['info']      = $userName.'上周未投放';
                            $data[$pid]['cloth']['color']     = '#dc3545';
                        }
                        break;
                    case WashClothesUser::TYPE_PANTS:
                        if($rfidExists){
                            $data[$pid]['pants']['wash_time'] = $lastWashClothesArr[$value['rfid']]['receive_time'];
                            $data[$pid]['pants']['info']      = $userName.'本周领取成功';
                            $data[$pid]['pants']['color']     = '#28a745';
                        }else{
                            $data[$pid]['pants']['wash_time'] = '';
                            $data[$pid]['pants']['info']      = $userName.'上周未投放';
                            $data[$pid]['pants']['color']     = '#dc3545';
                        }
                        break;
                }
             //周五投放  
            } elseif ($today == 5){
                $thisRfidExists = ArrayHelper::keyExists($value['rfid'], $thisWashClothesArr) ? true : false;
                switch ($washClothesUser->type) {
                    case WashClothesUser::TYPE_HAT:
                        if($rfidExists){
                            $data[$pid]['hat']['wash_time'] = $lastWashClothesArr[$value['rfid']]['invest_time'];
                            $data[$pid]['hat']['info']      = $userName.'上周已经清洗过';
                            $data[$pid]['hat']['color']     = '#dc3545';
                        }elseif($thisRfidExists){
                            $data[$pid]['hat']['wash_time'] = $thisWashClothesArr[$value['rfid']]['invest_time'];
                            $data[$pid]['hat']['info']      = $userName.'本周投放成功';
                            $data[$pid]['hat']['color']     = '#28a745';
                        }else{
                            $data[$pid]['hat']['wash_time'] = '';
                            $data[$pid]['hat']['info']      = '本周投放失败';
                            $data[$pid]['hat']['color']     = '#dc3545';
                        }
                        break;
                    case WashClothesUser::TYPE_CLOTH:
                        if($rfidExists){
                            $data[$pid]['cloth']['wash_time'] = $lastWashClothesArr[$value['rfid']]['invest_time'];
                            $data[$pid]['cloth']['info']      = $userName.'上周已经清洗过';
                            $data[$pid]['cloth']['color']     = '#dc3545';
                        }elseif($thisRfidExists){
                            $data[$pid]['cloth']['wash_time'] = $thisWashClothesArr[$value['rfid']]['invest_time'];
                            $data[$pid]['cloth']['info']      = $userName.'本周投放成功';
                            $data[$pid]['cloth']['color']     = '#28a745';
                        }else{
                            $data[$pid]['cloth']['wash_time'] = '';
                            $data[$pid]['cloth']['info']      = '本周投放失败';
                            $data[$pid]['cloth']['color']     = '#dc3545';
                        }
                        break;
                    case WashClothesUser::TYPE_PANTS:
                        if($rfidExists){
                            $data[$pid]['pants']['wash_time'] = $lastWashClothesArr[$value['rfid']]['invest_time'];
                            $data[$pid]['pants']['info']      = $userName.'上周已经清洗过';
                            $data[$pid]['pants']['color']     = '#dc3545';
                        }elseif($thisRfidExists){
                            $data[$pid]['pants']['wash_time'] = $thisWashClothesArr[$value['rfid']]['invest_time'];
                            $data[$pid]['pants']['info']      = $userName.'本周投放成功';
                            $data[$pid]['pants']['color']     = '#28a745';
                        }else{
                            $data[$pid]['pants']['wash_time'] = '';
                            $data[$pid]['pants']['info']      = '本周投放失败';
                            $data[$pid]['pants']['color']     = '#dc3545';
                        }
                        break;
                }
            }  
        }
        if(!empty($data)){
            ArrayHelper::multisort($data, 'id', SORT_DESC);
        }

        $status = 500;
        $msg    = '暂无人员刷卡数据';
        $lastClothesLog = WashClothesLog::find()->where(['>=','create_time',$startToday])->andWhere(['<','create_time',$endToday])->orderBy(['id' => SORT_DESC])->one();
        if($lastClothesLog){
            $rfid = $lastClothesLog->rfid;
            $washClothesUser = WashClothesUser::findOne(['rfid' => $rfid]);
            if(!$washClothesUser){
                $msg = '未能查到此卡的人员信息';
            }else{
                $userName = $washClothesUser->user_name;
                $rfidExists = ArrayHelper::keyExists($rfid, $lastWashClothesArr) ? true : false;
                if ($today == 1 || $today == 2) {
                    if($rfidExists){
                        $status = 200;
                        $msg  = $userName.'本周领取成功';
                    }else{
                        $msg  = $userName.'上周未投放';
                    }  
                } elseif ($today == 5){
                    $thisRfidExists = ArrayHelper::keyExists($rfid, $thisWashClothesArr) ? true : false;
                    if($rfidExists){
                        $msg  = $userName.'上周已经清洗过';
                    }elseif($thisRfidExists){
                        $status = 200;
                        $msg  = $userName.'本周投放成功';
                    }else{
                        $msg  = $userName.'本周投放失败';
                    }
                }
            }
        }
        
        return $this->asJson(['code' => 200,'data' => $data,'status' => $status,'msg' => $msg]);
    }

}




