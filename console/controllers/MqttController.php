<?php

namespace console\controllers;

use backend\models\Shelf;
use backend\models\ShelfMaterials;
use common\models\phpMQTT;
use common\models\SendMqtt;
use yii\console\Controller;

class MqttController extends Controller
{
    /**
     * 定时更新货架表状态
     * ./yii mqtt/index
     */
    public function actionIndex()
    {
        //获取已上架并且为入货架的物料
        $shelfIdList = ShelfMaterials::find()->where(['fettle' => ShelfMaterials::FETTLE_UP, 'status' => ShelfMaterials::STATUS_PENDING])->asArray()->all();
        if (empty($shelfIdList)) {
            return;
        }
        foreach ($shelfIdList as $value) {
            $endTime    = strtotime($value['end_time']); //截止时间
            $expireTime = strtotime($value['expire_time']);//过期时间
            $status     = '';
            //当前时间大于醒料完成时间 可领取
            if (time() > $endTime) {
                $status = SendMqtt::SHELF_RECEIVE;//可领取
            }
            //获取六个小时后的时间 黄灯预警
            if ($expireTime < strtotime("+6 hours")) {
                $status = SendMqtt::SHELF_WARNING;//预警中
            }
            //获取三十分钟后的时间 物料过期
            if ($expireTime < strtotime("+30 minutes")) {
                $status = SendMqtt::SHELF_EXPIRE;//过期
				$materials = ShelfMaterials::findOne(['id' => $value['id']]);
				$materials->fettle = ShelfMaterials::FETTLE_DOWN;
				$materials->save();
				
            }
            if (empty($status)) {
                continue;
            }
            //修改货架状态
            $model         = Shelf::findOne($value['shelf_id']);
            $model->status = $status;
            $result        = $model->save();
            if ($result) {
                $sendMqtt   = new SendMqtt();
                $shelfModel = Shelf::findOne(['id' => $value['shelf_id']]);
                $sendMqtt->publishMessage($shelfModel->mark, $shelfModel->number, $status); //给下位机发消息
                usleep(500000);
            }
        }
    }

    /**
     * 循环接收服务器的消息
     *  E:\backstage\php-7.1.30-Win32-VC14-x64\php.exe E:\backstage\www\advanced\yii  mqtt/subscribe
     * ./yii mqtt/subscribe
     * @return bool
     */
    public function actionSubscribe()
    {
        ignore_user_abort(true); //后台运行
        set_time_limit(0);       //取消脚本运行时间的超时上限
        date_default_timezone_set('PRC'); //设置中国时区

        $sendMqtt   = new SendMqtt();
        $mqttServer = new phpMQTT($sendMqtt->serverHost, $sendMqtt->serverPort, $sendMqtt->serverClientId);
        $connect    = $mqttServer->connect(true, NULL, $sendMqtt->username, $sendMqtt->password); //链接MQTT
        if (!$connect) {
            return false;
        }
        //订阅消息
        $key          = $sendMqtt->devClientName; //订阅dev2ser
        $topics[$key] = array("qos" => 0, "function" => array($this, "getMessage")); //使用回调函数订阅消息
        $mqttServer->subscribe($topics, 0);

        //无限循环获取消息
        while ($mqttServer->proc()) {
        }
        $mqttServer->close();
    }

    /**
     * 回调获取订阅的消息
     * @param $topic
     * @param $message
     * @return bool
     */
    public function getMessage($topic, $message)
    {
        $message = trim($message);
        if (empty($message)) {
            return false;
        }
        //重启货架控制系统
        $message = strtoupper($message);//将字符串转大写
        $message = str_replace(' ', '', $message);//字符串去除空格

        //响应设备消息
        $chekStr = substr($message, 6, 2);
        if ($chekStr == '05') {
            $mark   = (int)substr($message, 4, 2); //截取货架编号
            $number = substr($message, 8, 2); //截取灯的ID
            if ($number && $mark) {
                $model = Shelf::findOne(['number' => SendMqtt::$LampArr[$number], 'mark' => $mark]);//查询货架数据
                if ($model) {
                    (new SendMqtt())->publishMessage($model['mark'], $model['number'], $model['status']);
                }
            }
        }
    }

}

