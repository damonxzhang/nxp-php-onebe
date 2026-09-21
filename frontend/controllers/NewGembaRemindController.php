<?php
namespace frontend\controllers;

use backend\models\NewGemba;
use backend\models\NewGembaSendMsg;

class NewGembaRemindController extends  FrontendBaseController {

    /**
     * 发送gemba超时提醒邮件
     * @return string
     */
    public function actionSendEmailRemind(){
        // return '完成！';
        $model = new NewGemba();
        $data = $model->getAllUnfinished();
        // print_r($data);die;
        if(empty($data)){
            return '完成！';
        }
        $i = 0;
        foreach ($data as $val){
            //如果发送次数大于0 说明不是第一次提醒 时间间隔24小时
            if($val['amount'] > 0 ){
                if($this->twentyFourEmail($val)>0){
                    $i = $i +1;

                }
            //第一次发送提醒邮件为72小时未处理
            }else{
                if($this->seventyTwoEmail($val)>0){
                    $i = $i +1;
                }
            }
        }
        return $i;
    }

    /**
     * 24小时邮件提醒
     * @param $data
     * @param $overTime
     * @return bool
     */
    public function twentyFourEmail($data){
        $twoTime = 24*60*60;
        $overTime = $data['send_time'] + $twoTime;
        $sendModel = new NewGembaSendMsg();
        $j = 0;
        if ( $overTime < time() ){
            $j = 1;
            $sendModel->setEmailParams($data);
        }
        return $j;
    }

    /**
     * 72小时邮件提醒
     * @param $data
     * @param $overTime
     * @return bool
     */
    public function seventyTwoEmail($data){
        $oneTime = 72 * 60 * 60;
        $sendModel = new NewGembaSendMsg();
        $attrName = NewGembaSendMsg::$actionTimeKey[$data['steps']];
        $overTime =  $data[$attrName] + $oneTime;
        $j = 0;

        if ( $overTime < time() ){
            $j = 1;
            $sendModel->setEmailParams($data);
        }
        return $j;

    }

}