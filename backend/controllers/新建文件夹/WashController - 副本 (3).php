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
            $endTimeToday   = strtotime(date('Y-m-d 12:00:00'));
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
            $type    = empty($washClothesUser) ? 0 : $washClothesUser->type;
            $area     = empty($washClothesUser) ? 0 : $washClothesUser->area;

            $model              = new WashClothes();
            $model->rfid        = $rfid;
            $model->pid         = $pid;
            $model->area        = $area;
            $model->user_name   = $userName;
            $model->type       = $type;
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
        // if(in_array($today,[3,4,6,7])){
        //     $weekArr = ["3"=>"星期三","4"=>"星期四","6"=>"星期六","7"=>"星期日"];
        //     return $this->asJson(['code' => 500, 'msg' =>  '今天是'.$weekArr[$today].',不能刷卡领取或者清洗!']);
        // }
        $today = 5;

        // if($today == 5){
        //     $fridayTime = time();
        //     $startTimeToday = strtotime(date('Y-m-d 07:00:00'));
        //     $endTimeToday   = strtotime(date('Y-m-d 12:00:00'));
        //     if ($fridayTime < $startTimeToday || $fridayTime > $endTimeToday) {
        //         return $this->asJson(['code' => 500, 'msg' => '只能每周五上午7点到12点之间投放']);
        //     }
        // }

        $startToday = date('Y-m-d 00:00:00');
        $endToday   = date('Y-m-d 23:59:59');
       
        $lastClothesLog = WashClothesLog::find()->where(['>=','create_time',$startToday])->orderBy(['id' => SORT_DESC])->one();
        if(empty($lastClothesLog)){
            return $this->asJson(['code' => 500, 'msg' => '暂无刷卡数据']);
        }

        $swipeResults = [];
        $query = WashClothesUser::find();
        $query->alias('w');
        $query->select('u.chinese_name,u.area,w.pid,w.type');
        $query->leftJoin(UserInfo::tableName() . ' u', 'w.pid = u.pid');
        $query->where(['w.status' => WashClothesUser::STATUS_YES]);
        $query->asArray();
        $dataProvider = $query->all();

        foreach ($dataProvider as $value) {
            $pid = $value['pid'];
            $exitUser = ArrayHelper::keyExists($pid, $swipeResults) ? true : false;
            if(!$exitUser){
                $swipeResults[$pid] = [
                    'user_name'   => $value['chinese_name'],
                    'pid'         => $pid,
                    'area'        =>  ArrayHelper::getValue(UserInfo::$areaArr, $value['area'],''),
                    'hat_exist'   => 0,
                    'cloth_exist' => 0,
                    'pants_exist' => 0,
                    'hat'         => ['wash_time' => '', 'result' => '','info' => '','color' => '#FFFFFF'],
                    'cloth'       => ['wash_time' => '', 'result' => '','info' => '','color' => '#FFFFFF'],
                    'pants'       => ['wash_time' => '', 'result' => '','info' => '','color' => '#FFFFFF']
                ];
            }else{
                switch ($value['type']) {
                    case WashClothesUser::TYPE_HAT:
                        $swipeResults[$pid]['hat_exist']  = 1;
                        break;
                    case WashClothesUser::TYPE_CLOTH:
                        $swipeResults[$pid]['cloth_exist'] = 1;
                        break;
                    case WashClothesUser::TYPE_PANTS:
                        $swipeResults[$pid]['pants_exist'] = 1;
                        break;
                }
            }   
        }

        $thisWeekStart   = date('Y-m-d 00:00:00', strtotime("this week"));// 获取本周第一天
        $lastFridayStart = date('Y-m-d 00:00:00', strtotime('last week Friday 00:00:00', time()));// 上周五开始时间
        $lastFridayEnd   = date('Y-m-d 23:59:59', strtotime('last week Friday 23:59:59', time())); //上周五结束时间

        $lastWashClothes    = WashClothes::find()->where(['>=','invest_time',$lastFridayStart])->andWhere(['<','invest_time',$lastFridayEnd])->andFilterWhere(['not', ['rfid' => '']])->asArray()->all();
        $lastWashClothesArr = ArrayHelper::index($lastWashClothes, 'rfid');
        
        $thisWashClothes    = WashClothes::find()->where(['not', ['rfid' => null]])->andWhere(['>=','invest_time',$thisWeekStart])->asArray()->all();
        $thisWashClothesArr = ArrayHelper::index($thisWashClothes, 'rfid');
        
        $todayClothesLog = WashClothesLog::find()->where(['>=','create_time',$startToday])->andWhere(['<','create_time',$endToday])->orderBy(['id' => SORT_DESC])->asArray()->all();
  
        foreach ($todayClothesLog as $value) {
            $washClothesUser = WashClothesUser::findOne(['rfid' => $value['rfid']]);
            if(!$washClothesUser){
                continue;
            }

            $pid = $washClothesUser->pid;
            $userName = $washClothesUser->user_name;
            $rfidExists = ArrayHelper::keyExists($value['rfid'], $lastWashClothesArr) ? true : false;

            //周一周二领取
            if ($today == 1 || $today == 2) {
     
                switch ($washClothesUser->type) {
                    case WashClothesUser::TYPE_HAT:
                        if($rfidExists){
                            $swipeResults[$pid]['hat']['wash_time'] = $lastWashClothesArr[$value['rfid']]['receive_time'];
                            $swipeResults[$pid]['hat']['result']    = '本周领取成功';
                            $swipeResults[$pid]['hat']['info']      = $userName.'本周领取成功';
                            $swipeResults[$pid]['hat']['color']     = '#28a745';
                        }else{
                            $swipeResults[$pid]['hat']['wash_time'] = '';
                            $swipeResults[$pid]['hat']['result']    = '上周未投放';
                            $swipeResults[$pid]['hat']['info']      = $userName.'上周未投放';
                            $swipeResults[$pid]['hat']['color']     = '#dc3545';
                        }
                        break;
                    case WashClothesUser::TYPE_CLOTH:
                        if($rfidExists){
                            $swipeResults[$pid]['cloth']['wash_time'] = $lastWashClothesArr[$value['rfid']]['receive_time'];
                            $swipeResults[$pid]['cloth']['result']    = '本周领取成功';
                            $swipeResults[$pid]['cloth']['info']      = $userName.'本周领取成功';
                            $swipeResults[$pid]['cloth']['color']     = '#28a745';
                        }else{
                            $swipeResults[$pid]['cloth']['wash_time'] = '';
                            $swipeResults[$pid]['cloth']['result']    = '上周未投放';
                            $swipeResults[$pid]['cloth']['info']      = $userName.'上周未投放';
                            $swipeResults[$pid]['cloth']['color']     = '#dc3545';
                        }
                        break;
                    case WashClothesUser::TYPE_PANTS:
                        if($rfidExists){
                            $swipeResults[$pid]['pants']['wash_time'] = $lastWashClothesArr[$value['rfid']]['receive_time'];
                            $swipeResults[$pid]['pants']['result']    = '本周领取成功';
                            $swipeResults[$pid]['pants']['info']      = $userName.'本周领取成功';
                            $swipeResults[$pid]['pants']['color']     = '#28a745';
                        }else{
                            $swipeResults[$pid]['pants']['wash_time'] = '';
                            $swipeResults[$pid]['pants']['result']    = '上周未投放';
                            $swipeResults[$pid]['pants']['info']      = $userName.'上周未投放';
                            $swipeResults[$pid]['pants']['color']     = '#dc3545';
                        }
                        break;
                }
             //周五投放  
            } elseif ($today == 5){
                $thisRfidExists = ArrayHelper::keyExists($value['rfid'], $thisWashClothesArr) ? true : false;
                switch ($washClothesUser->type) {
                    case WashClothesUser::TYPE_HAT:
                        if($rfidExists){
                            $swipeResults[$pid]['hat']['wash_time'] = $lastWashClothesArr[$value['rfid']]['invest_time'];
                            $swipeResults[$pid]['hat']['result']    = '上周已经清洗过';
                            $swipeResults[$pid]['hat']['info']      = $userName.'上周已经清洗过';
                            $swipeResults[$pid]['hat']['color']     = '#dc3545';
                        }elseif($thisRfidExists){
                            $swipeResults[$pid]['hat']['wash_time'] = $thisWashClothesArr[$value['rfid']]['invest_time'];
                            $swipeResults[$pid]['hat']['result']    = '本周投放成功';
                            $swipeResults[$pid]['hat']['info']      = $userName.'本周投放成功';
                            $swipeResults[$pid]['hat']['color']     = '#28a745';
                        }else{
                            $swipeResults[$pid]['hat']['wash_time'] = '';
                            $swipeResults[$pid]['hat']['result']    = '本周投放失败';
                            $swipeResults[$pid]['hat']['info']      = '本周投放失败';
                            $swipeResults[$pid]['hat']['color']     = '#dc3545';
                        }
                        break;
                    case WashClothesUser::TYPE_CLOTH:
                        if($rfidExists){
                            $swipeResults[$pid]['cloth']['wash_time'] = $lastWashClothesArr[$value['rfid']]['invest_time'];
                            $swipeResults[$pid]['cloth']['result']    = '上周已经清洗过';
                            $swipeResults[$pid]['cloth']['info']      = $userName.'上周已经清洗过';
                            $swipeResults[$pid]['cloth']['color']     = '#dc3545';
                        }elseif($thisRfidExists){
                            $swipeResults[$pid]['cloth']['wash_time'] = $thisWashClothesArr[$value['rfid']]['invest_time'];
                            $swipeResults[$pid]['cloth']['result']    = '本周投放成功';
                            $swipeResults[$pid]['cloth']['info']      = $userName.'本周投放成功';
                            $swipeResults[$pid]['cloth']['color']     = '#28a745';
                        }else{
                            $swipeResults[$pid]['cloth']['wash_time'] = '';
                            $swipeResults[$pid]['cloth']['result']    = '本周投放失败';
                            $swipeResults[$pid]['cloth']['info']      = '本周投放失败';
                            $swipeResults[$pid]['cloth']['color']     = '#dc3545';
                        }
                        break;
                    case WashClothesUser::TYPE_PANTS:
                        if($rfidExists){
                            $swipeResults[$pid]['pants']['wash_time'] = $lastWashClothesArr[$value['rfid']]['invest_time'];
                            $swipeResults[$pid]['pants']['result']    = '上周已经清洗过';
                            $swipeResults[$pid]['pants']['info']      = $userName.'上周已经清洗过';
                            $swipeResults[$pid]['pants']['color']     = '#dc3545';
                        }elseif($thisRfidExists){
                            $swipeResults[$pid]['pants']['wash_time'] = $thisWashClothesArr[$value['rfid']]['invest_time'];
                            $swipeResults[$pid]['pants']['result']    = '本周投放成功';
                            $swipeResults[$pid]['pants']['info']      = $userName.'本周投放成功';
                            $swipeResults[$pid]['pants']['color']     = '#28a745';
                        }else{
                            $swipeResults[$pid]['pants']['wash_time'] = '';
                            $swipeResults[$pid]['pants']['result']    = '本周投放失败';
                            $swipeResults[$pid]['pants']['info']      = '本周投放失败';
                            $swipeResults[$pid]['pants']['color']     = '#dc3545';
                        }
                        break;
                }
            }
        }

     //   echo "<pre/>"; var_dump($swipeResults);die;
        
        return $this->asJson([
            'code' => 200,
            'msg' => '同步成功',
            'data' => $swipeResults
        ]);
    }

}




