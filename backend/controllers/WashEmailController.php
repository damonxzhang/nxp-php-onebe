<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\models\WashClothesUser;
use backend\models\WashClothes;
use backend\models\WashClothesCommon;

class WashEmailController extends Controller
{

    /**
     * 提示静电衣清洗，每周一中午12点发送
     */
    public function actionEveryWeekSendEmail()
    {
        $today = date('N'); // 'N' 返回星期几的数字表示，1-7，星期日是7
        if($today != 1) return 1;
        $pidArr = WashClothesUser::find()->select('pid')->where(['status' => 1])->distinct()->column();
        if(empty($pidArr)){
            return 1;
        }
        $lastWeekStart = date('Y-m-d 00:00:00', strtotime('last week Monday', time()));// 上周开始时间
        $lastWeekEnd   = date('Y-m-d 23:59:59', strtotime('last week Sunday 23:59:59', time())); // 上周结束时间
        foreach($pidArr as $pid){
            $lastWashClothes = WashClothes::find()->where(['pid' => $pid])->andWhere(['>=','invest_time',$lastWeekStart])->andWhere(['<','invest_time',$lastWeekEnd])->one();
            if(empty($lastWashClothes)) continue;
            //查询人员数据
            $userInfo  = UserInfo::findOne(['pid' => $pid]);
            if($userInfo && !empty($userInfo->email)){
                $to      = $userInfo->email;
                $subject = '静电服清洗';
                $html    =  $this->renderFile('@common/mail/wash/every-week-send.php',['username' => $userInfo->chinese_name]);
                $mail    = Yii::$app->mailer->compose( );
                $mail->setHtmlBody($html);  //邮件正文
                $mail->setSubject($subject); //邮件主题
                $mail->setTo($to);//收件人
                @$mail->send();
           }
          
        }
        return 1;
    }

    /**
     * 【每周二下班前】提醒文员清空洗衣柜---每周二中午12点
     */
    public function actionTuesdayEmptyEmail()
    {
        $today = date('N'); // 'N' 返回星期几的数字表示，1-7，星期日是7
        if($today != 2) return 1;
        $pidArr = WashClothesUser::find()->select('pid')->where(['status' => 1])->distinct()->column();
        if(empty($pidArr)) return 1;
       
        $clerkEmailArr = WashClothesUser::find()->select('clerk_email')->where(['status' => 1])->distinct()->column();
        if(empty($clerkEmailArr)){
            return 1;
        }
       
        $valueData = [];
        foreach($clerkEmailArr as $clerkEmail){
            if(empty($clerkEmail)) continue;
            $valueData[$clerkEmail] = [];
        }
       
        $lastWeekStart = date('Y-m-d 00:00:00', strtotime('last week Monday', time()));// 上周开始时间
        $lastWeekEnd   = date('Y-m-d 23:59:59', strtotime('last week Sunday 23:59:59', time())); // 上周结束时间
       
        foreach($pidArr as $pid){
            $lastWashClothes = WashClothes::find()->where(['pid' => $pid])->andWhere(['>=','invest_time',$lastWeekStart])->andWhere(['<','invest_time',$lastWeekEnd])->one();
            unset($pidArr[$key]);
            if(empty($lastWashClothes)) continue;
            if(empty($lastWashClothes->receive_time)){
                $status = '未取走';
                $receiveTime = ''; 
            }else{
                $status = '完成'; 
                $receiveTime = date('Y-m-d H:i:s', strtotime($lastWashClothes->receive_time));
            }
            $userInfo = UserInfo::findOne(['pid' => $pid]);
            $username = $userInfo ? $userInfo->chinese_name : '';
            $WashClothesUserModel = WashClothesUser::findOne(['pid' => $pid,'status' => 1]);
            $clerkEmail = $WashClothesUserModel->clerk_email;
            if (array_key_exists($clerkEmail,$valueData)){
                $valueData[$clerkEmail][$pid] = ['username' => $username,'receive_time' => $receiveTime, 'status' => $status];
            }

        }

        foreach($valueData as $clerkEmail => $data){
            if(empty($data)) continue;
            $cc    = ['gang.zhao@nxp.com'];
            $html  = $this->renderFile('@common/mail/wash/tuesday-empty.php',['data' => $data]);
            $mail  = Yii::$app->mailer->compose( );
            $mail->setHtmlBody($html);  //邮件正文
            $mail->setSubject('清空洗衣柜'); //邮件主题
            $mail->setTo($clerkEmail);//收件人
            $mail->setCc($cc);
            @$mail->send();
        }
        return 1;
    }

    /**
     * 【每周五中午前】提醒文员填写未送洗原因
     */
    public function actionFridaySendEmail()
    {
        $today = date('N'); // 'N' 返回星期几的数字表示，1-7，星期日是7
        if($today != 5) return 1;
		
		$currentTimestamp = time();
		$fridayTimestampstd  = strtotime('Friday 13:02:00');
        $fridayTimestampend  = strtotime('Friday 13:03:30');
		if ($currentTimestamp < $fridayTimestampstd || $currentTimestamp > $fridayTimestampend) {
           return 1;
        }
		
        $pidArr = WashClothesUser::find()->select('pid')->where(['status' => 1])->distinct()->column();
        if(empty($pidArr)) return 1;

        $clerkEmailArr = WashClothesUser::find()->select('clerk_email')->where(['status' => 1])->distinct()->column();
        if(empty($clerkEmailArr)){
            return 1;
        }
       
        $valueData = [];
        foreach($clerkEmailArr as $clerkEmail){
            if(empty($clerkEmail)) continue;
            $valueData[$clerkEmail] = [];
        }

        $firstDayOfWeek = date('Y-m-d 00:00:00', strtotime("this week"));// 获取本周第一天
        $lastDayOfWeek  = date('Y-m-d 23:59:59', strtotime("this week +6 days")); // 获取本周最后一天
      
        foreach($pidArr as $pid){
            $lastWashClothes = WashClothes::find()->where(['pid' => $pid])->andWhere(['>=','invest_time',$firstDayOfWeek])->andWhere(['<','invest_time',$lastDayOfWeek])->one();
            if(empty($lastWashClothes)){
                $status     = '未送洗';
                $investTime = ''; 
            }else{
                $status     = '完成'; 
                $investTime = date('Y-m-d H:i:s', strtotime($lastWashClothes->invest_time));
            }
            $userInfo = UserInfo::findOne(['pid' => $pid]);
            $username = $userInfo ? $userInfo->chinese_name : '';
        
            $washClothesCommon = WashClothesCommon::findOne(['pid' => $pid,'start_week_time' => $firstDayOfWeek]);
            $common            = empty($washClothesCommon) ? '' : $washClothesCommon->common;
            $WashClothesUserModel = WashClothesUser::findOne(['pid' => $pid,'status' => 1]);
            $clerkEmail = $WashClothesUserModel->clerk_email;
            if (array_key_exists($clerkEmail,$valueData)){
                $valueData[$clerkEmail][$pid] = ['username' => $username,'invest_time' => $investTime, 'status' => $status, 'common' => $common];
            }
          
        }
		
        foreach($valueData as $clerkEmail => $data){
            if(empty($data)) continue;
            $cc    = ['gang.zhao@nxp.com'];
            $html  = $this->renderFile('@common/mail/wash/friday-send.php',['data' => $data]);
            $mail  = Yii::$app->mailer->compose( );
            $mail->setHtmlBody($html);  //邮件正文
            $mail->setSubject('洗静电服送洗'); //邮件主题
            $mail->setTo($clerkEmail);//收件人
            $mail->setCc($cc); 
            @$mail->send();
        }

        return 1;
    }

}
