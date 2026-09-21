<?php
namespace console\controllers;

use backend\models\Blog;
use backend\models\UserInfo;
use common\models\Utility;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class BlogController extends Controller
{
    /**
     * ./yii blog/index
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function actionIndex()
    {
        $dataProvider = Blog::find()->where(['<', 'status', 4])->asArray()->all(); //获取尚未完成的交接日志数据
        $data = [];
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['title'] = $value['title'];
                $data[$key]['content_data'] =  $value['content_data'];
                $data[$key]['user_id'] = (new UserInfo())->getUserName($value['user_id']);
                $data[$key]['status'] = ArrayHelper::getValue(Blog::$statusArr, $value['status'],'');
                $data[$key]['create_time'] = $value['create_time'];
                $data[$key]['update_time'] = $value['update_time'];
            }
        }
        $fileName = '日志列表_' . time();
        $header = [ '标题', '内容', '创建人', '交接班次', '状态', '创建时间', '更新时间'];
        Utility::exportDataDownload($data, $header, $fileName, $fileName);
        $this->alarmSendEmail();//检查超时日志并发送邮件
    }

    /**
     * 定时脚本检查日志过期时间
     */
    public function alarmSendEmail()
    {
        $query = Blog::find();
        $query -> select('user_id');
        $query -> where(['<', 'status', 4]);
       // $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
       // $startDayTime = date('Y-m-d H:i:s', $endYesterday); //过期时间大于昨天日期结束时间
       // $query->andWhere(['>=', 'alarm_time', $startDayTime]);//大于昨天日期结束时间
        $time = date('Y-m-d H:i:s', time());//小于当前时间
        $query->andWhere(['<=', 'alarm_time', $time]);
        $dataProvider = $query->column();
        if (!empty($dataProvider)) {
            $this->sendEmail($dataProvider);
        }
    }

    /**
     * 发送邮件
     * @param array $idList
     * @return bool
     */
    public function sendEmail($idList = [])
    {
        $userIdList = (new UserInfo())->getUserEmail($idList);
        if (!$userIdList) {
            return false;
        }
        $urlLink = '';
        foreach ($userIdList as $value) {
            if (empty($value['email'])) continue;
            $body = '
                <div class="verify-email">
                    <p>Hello : ' . $value['chinese_name'] . ' </p>
                    <p>您有新的交接日志信息已过期，请登录网站查阅.</p>
                    <a href="' . $urlLink . '" > ' . $urlLink . '</a> 
                </div>';
            $mail = Yii::$app->mailer->compose();
            $mail->setTo($value['email']); //要发送给那个人的邮箱
            $mail->setSubject('You have a new log delivery message'); //邮件主题
            $mail->setHtmlBody($body); //发送的消息内容
            $mail->send();
        }
    }

}