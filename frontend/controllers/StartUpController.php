<?php

namespace frontend\controllers;

use backend\models\Blog;
use backend\models\UserInfo;
use common\models\Utility;
use Yii;
use yii\helpers\ArrayHelper;

class StartUpController extends FrontendBaseController
{

    /**
     * 测试打开 巡检接口
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function actionInsapp()
    {
        print_r(1);die;
    }


    /**
     * 定时脚本检查日志过期时间
     */
    public function alarmSendEmail()
    {
        $query = Blog::find();
        $query->select(['id', 'user_id','title','type','content_data','status']);
        $query->where(['<', 'status', 4]);
        $time = date('Y-m-d H:i:s', time());//小于当前时间
        $query->andWhere(['<=', 'alarm_time', $time]);
        $dataProvider = $query->asArray()->all();
        if (!empty($dataProvider)) {
            //批量更新日志表状态为已过期
            $idArr = array_column($dataProvider, 'id');
            Yii::$app->db->createCommand()->update(Blog::tableName(), ['status' => Blog::STATUS_DEFERRED], ['id' => $idArr])->execute();

            $this->sendEmail( $dataProvider);
        }
    }

    /**
     * 发送邮件
     * @param array $idList
     * @return bool
     */
    public function sendEmail($data)
    {
        if(empty($data)){
            return false;
        }
        foreach ($data as $value) {
            $create_user = UserInfo::getUserEmailAndName($value['user_id']);
            if (empty($create_user['email'])) continue;
            $html = $this->renderFile('@common/mail/blog/time-out-remind-view.php',['params' => $value,'userName' => $create_user['chinese_name']]);
            $mail = Yii::$app->mailer->compose();
            $mail->setHtmlBody($html);
            $mail->setTo($create_user['email']); //要发送给那个人的邮箱
            $mail->setSubject('Handover Record Overdue Reminder'); //邮件主题		
            $mail->send();
        }
    }

}


