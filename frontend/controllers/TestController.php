<?php
namespace frontend\controllers;
use Yii;
class TestController extends  FrontendBaseController {

  
    public function actionSendemail(){
    
        $mail = Yii::$app->mailer->compose();
        $mail->setHtmlBody('测试Onebe发送邮件');
        $mail->setTo(['894698693@qq.com','zhx703@163.com']); //要发送给那个人的邮箱
        $mail->setSubject('测试发送邮件'); //邮件主题		
        $mail->send();
    }

    public function actionGuard(){



    }

}