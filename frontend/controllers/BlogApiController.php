<?php

namespace frontend\controllers;

use backend\models\Blog;
use backend\models\UserInfo;
use common\models\Utility;
use Yii;
use yii\helpers\ArrayHelper;

class BlogApiController extends FrontendBaseController
{

    /**
     * 日志定时导出接口
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function actionBlog()
    {
        $dataProvider = Blog::find()->where(['<', 'status', 4])->asArray()->all(); //获取尚未完成的交接日志数据
        $data         = []; 
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['title']        = $value['title'];
                $data[$key]['content_data'] = $value['content_data'];
                $data[$key]['user_id']      = (new UserInfo())->getUserName($value['user_id']);
                $data[$key]['status']       = ArrayHelper::getValue(Blog::$statusArr, $value['status'], '');
                $data[$key]['create_time']  = $value['create_time'];
                $data[$key]['update_time']  = $value['update_time'];
            }
        }
        $fileName = '日志列表_' . time();
        $header   = ['标题', '内容', '创建人', '交接班次', '状态', '创建时间', '更新时间'];
        Utility::exportDataDownload($data, $header, $fileName, $fileName);
        $this->alarmSendEmail();//检查超时日志并发送邮件
        exit;
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


