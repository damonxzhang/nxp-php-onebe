<?php

namespace backend\modules\MaintainApi\controllers;

use backend\models\BossFeedback;
use backend\models\BossMaintain;
use backend\models\Maintain;
use backend\models\MaintainAreaHead;
use backend\models\MaintainFeedback;
use backend\models\UserInfo;
use backend\modules\MaintainApi\models\SendEmail;
use yii\web\Controller;

/**
 * Default controller for the `MaintainApi` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $models = Maintain::getDayNoCompletion();//var_dump($models);exit;
        if(!empty($models) && is_array($models)){
            $areaData = [];

            foreach ($models as $model){
                if ($model->access_advice == null)
                    continue;

                $areaData[$model->area]['attr'][] = $model->attributes;
            }
            foreach ($areaData as $k => $v){
                if(!empty($v['attr'])){
                    $this->sendEmailByArea($k,$v['attr']);
                }
            }
        }
    }

    /**
     * 根据区域循环给负责人发送邮件
     * @param $area
     * @param $params
     * @throws \yii\base\InvalidConfigException
     */
    public function sendEmailByArea($area,$params){
        // $users = MaintainAreaHead::getUserEmailByArea(['area' => $area]);
        // foreach ($users as $user){
        //     $params['userName'] = $user['name'];
        //     @$this->sendEmail($user['email'],SendEmail::TIMING_EMAIL_VIEW,'Daily Anomaly Summary',$params);
        // }
        $userEmailArr = MaintainAreaHead::getUserEmail(['area' => $area]);
        @$this->sendEmail($userEmailArr,SendEmail::TIMING_EMAIL_VIEW,'Daily Anomaly Summary',$params);
    }

    /**
     * 发送反馈超时提醒
     * @return string
     */
    public function actionTimeOutRemind(){
        $data = MaintainFeedback::getTimeOutFeedback();
        if(!empty($data)){
            foreach ($data as $val){
                //异常信息
                $maintain = Maintain::findOne($val['id']);
                if(!empty($maintain)){
                    //查询用户邮箱
                    list($to,$cc,$name) = $this->getUserAndLeaderEmail($val);
                    if(!$to){
                        echo "用户".$name."邮件发送失败！请检查邮箱是否无误！ \r\n";
                    }
                    $params = array_merge($maintain->attributes,['name' => $name]);
                    if(!$this->sendEmail($to,SendEmail::TIMEOUT_TIMING_EMAIL_VIEW,'Anomaly Overdue Reminder',$params,$cc)){
                        echo "用户".$name."邮件发送失败！请检查邮箱是否无误！ \r\n";
                    }

                    //更新反馈
                    MaintainFeedback::updateTypeBySendEmail($val['id']);
                }
            }
        }
    }

    /**
     * 定时发送boss直检反馈超时邮件
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBossTimeOutRemind(){
        $data = BossFeedback::unfinishedFeedback();
        $completeIds = [];
        if(!empty($data)){
            foreach ($data as $feedback){
                $feedbackUser = UserInfo::findOne($feedback['feedback_user']);
                $feedback['userName'] = $feedbackUser->chinese_name;
                $res = $this->sendEmail($feedbackUser->email,SendEmail::BOSS_TIMING_EMAIL_VIEW,'Anomaly Overdue Reminder',$feedback);
                if($res){
                    $completeIds[] = $feedback['id'];
                }
            }
            if(!empty($completeIds)){
                BossFeedback::changeSendMailById($completeIds);
            }
        }

    }

    /**
     * 获得未完成反馈的用户
     * @param Maintain $model
     */
    public function getFeedbackUser(Maintain $model){
        $feedbacks = MaintainFeedback::getFeedbackUserIdByMain($model->id);
        $users = [$model->feedback_user,$model->other_feedback_user];
        if(!empty($feedbacks) ){
            foreach ($feedbacks as $user){
                $key = array_search($user,$users);
                if($key){
                    unset($users[$key]);
                }
            }
        }
        return array_filter($users);
    }

    /**
     * 查询用户和用户主管的邮箱
     * @param $user
     * @return array|bool
     */
    public function getUserAndLeaderEmail($user){
        $userModel = new UserInfo();
        $leader_id = $userModel->getLeaderUids($user['feedback_user']);

        $userEmail = $userModel->getUserEmail($user['feedback_user']);
        if(!empty($userEmail[0])){
            $to = $userEmail[0]['email'];
            $name = $userEmail[0]['chinese_name'];
        }else{
            echo 'ID为'.$user['user']."的用户不存在 \r\n";
            return false;
        }
        $leaderEmail = $userModel->getUserEmail($leader_id[0]);
        if(!empty($leaderEmail[0])){
            $cc = $leaderEmail[0]['email'];
        }else{
            $cc = false;
        }
        return [$to,$cc,$name];
    }

    /**
     * 发送邮件
     * @param $email
     * @param $view
     * @param $subject
     * @param $params
     * @param null $cc
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function sendEmail($email,$view,$subject,$params,$cc = null){
        return \Yii::createObject([
            'class' => SendEmail::className(),
            'view' => $view,
            'to' => $email,
            'subject' => $subject,
            'cc' => $cc,
            'renderParams' => $params
        ])->send();
    }

}

