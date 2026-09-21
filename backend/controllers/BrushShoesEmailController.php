<?php
namespace backend\controllers;

use backend\models\Esdresult;
use backend\models\BrushShoes;
use backend\models\UserInfo;
use Yii;
use yii\web\Controller;

class BrushShoesEmailController extends Controller
{

    /**
     * 检查刷卡机记录发送邮件
     * @date 2021/7/27 10:08
     */
    public function actionSendEmail()
    {
        //洗鞋机数据
        $query = BrushShoes::find();
        $query->alias('b');
        $query->leftJoin(['u' => UserInfo::tableName()], 'b.user_id = u.id');
        $query->distinct();
        $query->select('u.meal_card_code');
        $query->where(['u.shoe_type' => 1]);
        $beforeDateHour = date('Y-m-d H:i:s', strtotime('-26hour'));//当前时间戳-26小时
        $query->andWhere(['>=', 'brush_date', $beforeDateHour]);
        $brushCard = $query->column();

        //三辊闸数据
        $esdQuery = Esdresult::find();
        $esdQuery->select('cardno');
        $esdQuery->distinct();
        $startDate = date("Y-m-d 09:00:00", strtotime("-1 day"));//昨天九点
        $endDate   = date("Y-m-d 07:00:00", time());             //今天七点
        $esdQuery->where(['>', 'timedate', $startDate]);
        $esdQuery->andWhere(['<=', 'timedate', $endDate]);  
        $gateCard = $esdQuery->column();

        $cardResult = array_diff($gateCard, $brushCard);//比较数据中差值
        if(empty($cardResult)) {
            return false;
        }
        $dataProvider = UserInfo::find()->select(['chinese_name', 'pid','area','shift'])->where(['in', 'meal_card_code', $cardResult])->andWhere(['shoe_type' => 1])->andWhere(['status' => 1])->orderby(['area'=>SORT_ASC,'shift'=>SORT_ASC])->asArray()->all();
        $tdData       = '';
        foreach($dataProvider as $value) {
            $tdData .= '<tr class="content"><td>' . $value['chinese_name'] . '</td><td>' . $value['pid'] . '</td><td>' . UserInfo::$areaArr[$value['area']] . '</td><td>' . UserInfo::$shiftArr[$value['shift']] . '</td></tr>';
        }
        $body       = '<div><table border = 1><tr class="th"><th>姓名 </th><th>PID</th><th>区域</th><th>班别</th></tr>' . $tdData . '</table></div>';
        $mail = Yii::$app->mailer->compose();
        // $mail->setTo(['zhx703@163.com']);                            //要发送给那个人的邮箱
        $mail->setTo(['lei.wang_17@nxp.com','jianguo.zhao@nxp.com','Y.Zhao@nxp.com','hong.wu@nxp.com','K.Li@nxp.com','hongliang.ji@nxp.com','P.Wang@nxp.com','guoli.wang@nxp.com','yong.wang_2@nxp.com','xianwen.zhang@nxp.com','Huan.Wang1@nxp.com','yuewei.li@nxp.com','peng.hou@nxp.com','fred.han@nxp.com','jian.shi@nxp.com']);                            //要发送给那个人的邮箱
        $mail->setSubject('Washing machine unpassing card list'); //邮件主题
        $mail->setHtmlBody($body);                                //发送的消息内容
        $mail->send();
        return true;
    }

}