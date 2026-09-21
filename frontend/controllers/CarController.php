<?php

namespace frontend\controllers;

use backend\models\UserInfo;
use Yii;
use backend\models\Car;
class CarController extends FrontendBaseController
{

    /**
     * 8天内未处理发邮件通知
     */
    public function actionIndex()
    {
        $dateTime = date('Y-m-d H:i:s',time()-691200);
        $query = Car::find()->where(['in', 'second_status', [1,2,3]])->andFilterWhere(['<=', 'first_success_time', $dateTime])->asArray()->all();
    
        if(empty($query)){
            return false;
        }
        foreach ($query as $value) {
            $model    = Car::findOne($value['id']);
            $userInfo = UserInfo::findOne(['sfc_number' => $model->operator]);
            if(empty($userInfo)){
                continue;
            }
            $leaderModel = UserInfo::findOne(['id' => $userInfo->leader_uid]);
            if(empty($leaderModel)){
                continue;
            }
            $cc = UserInfo::find()->select('email')->where(['section' => $userInfo->section])->andWhere(['position' => UserInfo::POSITION_PRODUCT])->column();
            if($userInfo->section == UserInfo::SECTION_LEADED ){
                $cc[] = 'hongliang.ji@nxp.com';
            }elseif($userInfo->section == UserInfo::SECTION_NON ){
                $cc[] = 'hongyue.kang@nxp.com';
            }
            $email = $leaderModel->email; 
            $body =  "<html><body><div>Hi All:</div><br/>DPR编号为:<h2 style='color: red;'>" . $value['trace_no'] . "</h2><br/><h2 >责任人" .  $userInfo['chinese_name'] . "</h2>已超期未返回，请加速处理 <br/> <div>Link to this Handover Record:<a href='<?=\Yii::$app->request->hostInfo.'/car/index?id=".$value['id']." ?>'><?= ". $value['id'] ."?></a></div><div>请使用Google浏览器打开上述链接</div></body></html>";
            $mail = Yii::$app->mailer->compose();
            $mail->setHtmlBody($body);
            $mail->setTo($email); //要发送给那个人的邮箱
            $mail->setCc($cc); //抄送的人
            $mail->setSubject('DPR已超期未处理'); //邮件主题		
            $mail->send();
          
        }
        return true;
    }
  
}