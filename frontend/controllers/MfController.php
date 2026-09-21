<?php

namespace frontend\controllers;

use backend\models\UserInfo;
use Yii;
use backend\models\BossMaintain;
use backend\models\search\BossMaintainSearch;
use yii\helpers\ArrayHelper;
use backend\models\BossFeedback;

class MfController extends FrontendBaseController
{

    /**
     * 定时脚本检查过期时间
     */
    public function actionUpdate()
    {
        $query = BossMaintain::find();
        $query->select(['id', 'number','feedback_user']);
        $query->where(['status'=> 1]);
        $time = time() - 72*3600;//超过72小时
        $query->andWhere(['<=', 'update_time', $time]);
        $dataProvider = $query->asArray()->all();
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $value) {
                $feedback_user = UserInfo::getUserEmailAndName($value['feedback_user']);
                if (empty($feedback_user['email'])) continue;
              
                $html = $this->renderFile('@common/mail/mf/update-email-view.php',['params' => $value,'userName' => $feedback_user['chinese_name']]);
                $mail = Yii::$app->mailer->compose();
                $mail->setHtmlBody($html);
                $mail->setTo($feedback_user['email']); //要发送给那个人的邮箱
                $mail->setSubject('MTFinding待处理'); //邮件主题		
                $mail->send();
                
            }
        }
    }

    public function actionOver()
    {
        $query = BossMaintain::find();
        $query->select(['b.id', 'b.number','b.feedback_user']);
        $query->alias('b');
        $query->leftJoin(BossFeedback::tableName() . ' bf', 'bf.m_id = b.id');
        $query->where(['b.status'=> 1]);
        $query->andWhere(['<=', 'predict_date', time()]);
        $dataProvider = $query->asArray()->all();
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $value) {
                $feedback_user = UserInfo::getUserEmailAndName($value['feedback_user']);
                if (empty($feedback_user['email'])) continue;
                $html = $this->renderFile('@common/mail/mf/over-email-view.php',['params' => $value,'userName' => $feedback_user['chinese_name']]);
                $mail = Yii::$app->mailer->compose();
                $mail->setHtmlBody($html);
                $mail->setTo($feedback_user['email']); //要发送给那个人的邮箱
                $mail->setSubject('MTFinding超时待处理'); //邮件主题		
                $mail->send();
                
            }
        }
    }


}


