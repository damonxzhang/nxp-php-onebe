<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
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
        $get  = Yii::$app->request->get();
        $rfid = $get['rfid'];
       
        $startToday = date('Y-m-d 00:00:00', time());
        $endToday   = date('Y-m-d 23:59:59', time());
        $washClothesLog = WashClothesLog::find()->where(['rfid' => $rfid])->andWhere(['>=','create_time',$startToday])->andWhere(['<','create_time',$endToday])->one();
        if($washClothesLog){
            return 1;
        }else{
            $model              = new WashClothesLog();
            $model->rfid        = $rfid;
            $model->card        = $get['card'];
            $model->create_time = date('Y-m-d H:i:s', time());
            $model->save(false);
        }

        $today = date('N'); // 'N' 返回星期几的数字表示，1-7，星期日是7
        //周一周二领取静电服
        if ($today == 1 || $today == 2) {
            $lastWeekStart = date('Y-m-d 00:00:00', strtotime('last week Monday', time()));// 上周开始时间
            $lastWeekEnd   = date('Y-m-d 23:59:59', strtotime('last week Sunday 23:59:59', time())); // 上周结束时间
            $lastWashClothes = WashClothes::find()->where(['rfid' => $rfid])->andWhere(['>=','invest_time',$lastWeekStart])->andWhere(['<','invest_time',$lastWeekEnd])->one();
            if($lastWashClothes && empty($lastWashClothes->receive_time)){
                $lastWashClothes->receive_time = date('Y-m-d H:i:s', time());
                $lastWashClothes->save(false);
            }

        //周五投放静电服
        } elseif ($today == 5){
            // 获取当前时间的时间戳
            $currentTime = time();
            
            // 定义时间范围的时间戳
            $startTimeToday = strtotime(date('Y-m-d 07:00:00'));
            $endTimeToday   = strtotime(date('Y-m-d 12:00:00'));
            
            // 判断当前时间是否在范围内
            if ($currentTime < $startTimeToday || $currentTime > $endTimeToday) {
                return 1;
            }   
            
            // 本周时间格式化为标准日期格式
            $firstDayOfWeek = date('Y-m-d 00:00:00', strtotime("this week"));// 获取本周第一天
            $lastDayOfWeek  = date('Y-m-d 23:59:59', strtotime("this week +6 days")); // 获取本周最后一天
            $thisWashClothes= WashClothes::find()->where(['rfid' => $rfid])->andWhere(['>=','invest_time',$firstDayOfWeek])->andWhere(['<','invest_time',$lastDayOfWeek])->one();
            
            if(empty($thisWashClothes)){
            
                $washClothesUserModel = WashClothesUser::findOne(['rfid' => $rfid]);
                $pid                  = empty($washClothesUserModel) ? '' : $washClothesUserModel->pid;
                $userName             = empty($washClothesUserModel) ? '' : $washClothesUserModel->user_name;
                $types                = empty($washClothesUserModel) ? 0 : $washClothesUserModel->types;
                
                $model              = new WashClothes();
                $model->rfid        = $rfid;
                $model->pid         = $pid;
                $model->user_name   = $userName;
                $model->types       = $types;
                $model->create_time = date('Y-m-d H:i:s', time());
                $model->invest_time = date('Y-m-d H:i:s', time());
                $model->receive_time= '';
                $model->save(false);
            }
        }

        return 1;
    }

    /**
     * 刷卡页面接口
     */
    public function actionGetCard()
    {
        
        //只能是周一周二领取,周五上午投放
        $today = date('N');
        if(in_array($today,[3,4,6,7])){
            $weekArr = ["1"=>"星期一","2"=>"星期二","3"=>"星期三","4"=>"星期四","5"=>"星期五","6"=>"星期六","7"=>"星期日"];
            return $this->json(100, '今天是'.$weekArr[$today].',不能刷卡领取或者清洗!',[]);
        }

        if($today == 5){
            $currentTime = time();
            $startTimeToday = strtotime(date('Y-m-d 07:00:00'));
            $endTimeToday   = strtotime(date('Y-m-d 12:00:00'));
            if ($currentTime < $startTimeToday || $currentTime > $endTimeToday) {
                return $this->json(100, '只能每周五上午7点到12点之间投放!',[]);
            }
        }
        
        $dataProvider = [];
        $startToday = date('Y-m-d 00:00:00', time());
        $endToday   = date('Y-m-d 23:59:59', time());
        
        $washClothesLog = WashClothesLog::find()->where(['>=','create_time',$startToday])->andWhere(['<','create_time',$endToday])->orderBy(['id' => SORT_DESC])->asArray()->all();
        if(empty($washClothesLog)){
            return $this->json(100, '暂无刷卡数据!',[]);
        }

        // 本周时间格式化为标准日期格式
        $firstDayOfWeek = date('Y-m-d 00:00:00', strtotime("this week"));// 获取本周第一天
        $lastDayOfWeek  = date('Y-m-d 23:59:59', strtotime("this week +6 days")); // 获取本周最后一天

        // 上周时间格式化为标准日期格式
        $lastWeekStart = date('Y-m-d 00:00:00', strtotime('last week Monday', time()));// 上周开始时间
        $lastWeekEnd   = date('Y-m-d 23:59:59', strtotime('last week Sunday 23:59:59', time())); // 上周结束时间

        foreach ($washClothesLog as $value) {
            if(strlen($value['rfid']) <= 23){
                continue;
            }
            $washClothesUserModel = WashClothesUser::findOne(['rfid' => $value['rfid']]);
            if(empty($washClothesUserModel)){
                continue;
            }
            
            $userName = $washClothesUserModel->user_name;
            $pid      = $washClothesUserModel->pid;
            $shift    = $area = '';
            $userInfo = UserInfo::findOne(['pid' => $pid]);
            if($userInfo){
                $area  = ArrayHelper::getValue(UserInfo::$areaArr, $userInfo->area,'');
            }

            $status  = 200;
            $message = '';

            // 判断今天是否是周一或者周二那就是领取,要与上周投放的一致
            if ($today == 1 || $today == 2) {
                $WashClothes= WashClothes::find()->where(['rfid' => $value['rfid']])->andWhere(['>=','invest_time',$lastWeekStart])->andWhere(['<','invest_time',$lastWeekEnd])->one();
                if(!$WashClothes){
                    $status  = 100;
                    $message = $userName . ': 上周未投放!';
                }else{
                    $message = $userName . ': 本周领取成功!';
                }

                // 判断今天是否是周三或者周四那就是投放新的静电服,要与上周投放的不一致
            } elseif ($today == 3 || $today == 4){
                $WashClothes= WashClothes::find()->where(['rfid' => $value['rfid']])->andWhere(['>=','invest_time',$lastWeekStart])->andWhere(['<','invest_time',$lastWeekEnd])->one();
                if($WashClothes){
                    $status  = 100;
                    $message = $userName . ': 上周已经清洗过!';
                }else{
                    $thisWashClothes= WashClothes::find()->where(['rfid' => $value['rfid']])->andWhere(['>=','invest_time',$firstDayOfWeek])->andWhere(['<','invest_time',$lastDayOfWeek])->one();
                    if($thisWashClothes){
                        $message = $userName . ': 本周投放成功!';
                    }else{
                        $status  = 100;
                        $message = $userName . ': 本周投放失败!';  
                    }
                }
            }
            $dataProvider[] = ['status' => $status,'user_name' => $userName,'rfid' => $value['rfid'],'pid' => $pid,'create_time' => $value['create_time'],'shift'=>$shift,'area'=>$area,'message'=>$message];
        }
    
        $msg = '';
        $res = 100;
        if(!empty($dataProvider)){
            $msg = isset($dataProvider[0]['message']) ? $dataProvider[0]['message'] : '';
            $res = isset($dataProvider[0]['status']) ? $dataProvider[0]['status'] : 100;
        }

        return $this->json($res, $msg, $dataProvider);
    }

    /**
     * ajax 返回json数据
     */
    public function json($status, $msg, $data = '')
    {
        if ($data) {
            return Json::encode(['status' => $status, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
        } else {
            return Json::encode(['status' => $status, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
        }
    }
}




