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
            $types    = empty($washClothesUser) ? 0 : $washClothesUser->types;
            
            $model              = new WashClothes();
            $model->rfid        = $rfid;
            $model->pid         = $pid;
            $model->user_name   = $userName;
            $model->types       = $types;
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
            $endTimeToday   = strtotime(date('Y-m-d 12:00:00'));
            if ($fridayTime < $startTimeToday || $fridayTime > $endTimeToday) {
                return $this->asJson(['code' => 500, 'msg' => '只能每周五上午7点到12点之间投放']);
            }
        }

        $startToday = date('Y-m-d 00:00:00');
        $endToday   = date('Y-m-d 23:59:59');
        $lastClothesLog = WashClothesLog::find()->where(['>=','create_time',$startToday])->orderBy(['id' => SORT_DESC])->one();
        $washClothesLog = WashClothesLog::find()->where(['>=','create_time',$startToday])->andWhere(['<','create_time',$endToday])->orderBy(['id' => SORT_DESC])->asArray()->all();
        if(empty($lastClothesLog)){
            return $this->asJson(['code' => 500, 'msg' => '暂无刷卡数据']);
        }

        $todayStart    = date('Y-m-d 00:00:00');
        $thisWeekStart = date('Y-m-d 00:00:00', strtotime("this week"));// 获取本周第一天
        $lastWeekStart = date('Y-m-d 00:00:00', strtotime('last week Monday', time()));// 上周开始时间
        $lastWeekEnd   = date('Y-m-d 23:59:59', strtotime('last week Sunday 23:59:59', time())); // 上周结束时间
    
        $swipeResults  = $userList = [];
        $pidArr = WashClothes::find()->select('DISTINCT pid')->where(['>=', 'create_time', $todayStart])->orderBy(['create_time' => SORT_DESC])->column();//聚合今日人员刷卡数据
        foreach ($pidArr as $pid) {
            if(empty($pid)) continue;
           
            $userInfo = UserInfo::findOne(['pid' => $pid]);
            if (!$userInfo) continue;
            
            $area  = ArrayHelper::getValue(UserInfo::$areaArr, $userInfo->area,'');
            $userData = [
                'name'  => $userInfo->user_name,
                'pid'   => $userInfo->pid,
                'area'  => $area,
                'hat'   => ['wash_time' => '', 'result' => '','info' => ''],
                'cloth' => ['wash_time' => '', 'result' => '','info' => ''],
                'pants' => ['wash_time' => '', 'result' => '','info' => ''],
            ];

            //周一周二领取
            if ($today == 1 || $today == 2) {
                $userData['hat']   = ['wash_time' => '', 'result' => '上周未投放','info' => ''];
                $userData['cloth'] = ['wash_time' => '', 'result' => '上周未投放','info' => ''];
                $userData['pants'] = ['wash_time' => '', 'result' => '上周未投放','info' => ''];

                // 获取该人员上周刷卡记录
                $washClothes = WashClothes::find()->where(['>=','invest_time',$lastWeekStart])->andWhere(['<','invest_time',$lastWeekEnd])->andWhere(['pid' => $pid])->orderBy(['id' => SORT_DESC])->asArray()->all();
                if (!$washClothes) continue;

                foreach ($washClothes as $wash) {
                    switch ($wash['types']) {
                        case WashClothes::TYPE_HAT:
                            $userData['hat']['wash_time'] = $wash['receive_time'];
                            $userData['hat']['result'] = '本周领取成功';
                            $userData['hat']['info']   = $wash['user_name'].'本周领取成功';
                            break;
                        case WashClothes::TYPE_CLOTH:
                            $userData['cloth']['wash_time'] = $wash['receive_time'];
                            $userData['cloth']['result']    = '本周领取成功';
                            $userData['cloth']['info']      = $wash['user_name'].'本周领取成功';
                            break;
                        case WashClothes::TYPE_PANTS:
                            $userData['pants']['wash_time'] = $wash['receive_time'];
                            $userData['pants']['result']    = '本周领取成功';
                            $userData['pants']['info']      = $wash['user_name'].'本周领取成功';
                            break;
                    }
                } 
            //周五投放  
            } elseif ($today == 5){
                $userData['hat']   = ['wash_time' => '', 'result' => '本周投放失败','info' => ''];
                $userData['cloth'] = ['wash_time' => '', 'result' => '本周投放失败','info' => ''];
                $userData['pants'] = ['wash_time' => '', 'result' => '本周投放失败','info' => ''];

                $washClothes = WashClothes::find()->where(['>=','invest_time',$thisWeekStart])->andWhere(['pid' => $pid])->orderBy(['id' => SORT_ASC])->asArray()->all();
                if (!$washClothes) continue;
                foreach ($washClothes as $wash) {
                    $lastWashClothes= WashClothes::find()->where(['rfid' => $wash['rfid']])->andWhere(['>=','invest_time',$lastWeekStart])->andWhere(['<','invest_time',$lastWeekEnd])->one();
                    switch ($wash['types']) {
                        case WashClothes::TYPE_HAT:
                            $userData['hat']['wash_time'] = $lastWashClothes ? $lastWashClothes->receive_time : $wash['invest_time'];
                            $userData['hat']['result']    = $lastWashClothes ? '上周已经清洗过' : '本周投放成功';
                            $userData['hat']['info']      = $lastWashClothes ? $wash['user_name'].'上周已经清洗过' : $wash['user_name'].'本周投放成功';
                            break;
                        case WashClothes::TYPE_CLOTH:
                            $userData['cloth']['wash_time'] = $lastWashClothes ? $lastWashClothes->receive_time : $wash['invest_time'];
                            $userData['cloth']['result']    = $lastWashClothes ? '上周已经清洗过' : '本周投放成功';
                            $userData['cloth']['info']      = $lastWashClothes ? $wash['user_name'].'上周已经清洗过' : $wash['user_name'].'本周投放成功';
                            break;
                        case WashClothes::TYPE_PANTS:
                            $userData['pants']['wash_time'] = $lastWashClothes ? $lastWashClothes->receive_time : $wash['invest_time'];
                            $userData['pants']['result']    = $lastWashClothes ? '上周已经清洗过' : '本周投放成功';
                            $userData['pants']['info']      = $lastWashClothes ? $wash['user_name'].'上周已经清洗过' : $wash['user_name'].'本周投放成功';
                            break;
                    }
                }
            }
            $userList[] = $userData;
        }

        $lastClothesLog  = WashClothesLog::find()->where(['>=','create_time',$startToday])->orderBy(['id' => SORT_DESC])->one();
        if ($today == 1 || $today == 2) {
            $washClothes = WashClothes::find()->where(['>=','invest_time',$lastWeekStart])->andWhere(['<','invest_time',$lastWeekEnd])->andWhere(['pid' => $pid])->orderBy(['id' => SORT_DESC])->asArray()->all();
                if (!$washClothes) continue;
        }

        $washClothesUser = WashClothesUser::findOne(['rfid' => $lastClothesLog->rfid]);
        $pid      = empty($washClothesUser) ? '' : $washClothesUser->pid;
        $userName = empty($washClothesUser) ? '' : $washClothesUser->user_name;
        $types    = empty($washClothesUser) ? 0 : $washClothesUser->types;
        return $this->asJson([
            'code' => 200,
            'msg' => '同步成功',
            'show' => '同步成功',
            'data' => $userList,
            'swipe_results' => $swipeResults,
            'last_sync' => date('Y-m-d H:i:s')
        ]);
    }

}




