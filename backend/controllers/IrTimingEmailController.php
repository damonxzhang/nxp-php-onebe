<?php
namespace backend\controllers;

use backend\models\Base;
use backend\models\IrEmailLog;
use backend\models\Ir;
use backend\models\IrData;
use backend\models\IrJudge;
use backend\models\IrSendMsg;
use backend\models\IrUser;
use backend\models\UserInfo;
use yii\web\Controller;
use yii\db\Expression;

/**
 * Ir定时提醒
 * Class IrTimingEmailController
 * @package backend\controllers
 */
class IrTimingEmailController extends Controller {

    /**
     *  超时提醒提醒
     */
    public function actionRemind(){
        $this->directorTiming();//创建到主管超时提醒
        $this->assessTiming(); //评估人超时和提醒
        $this->headTiming();//负责人超时
        $this->workTiming();//实施人超时
        $this->confirmTiming();//实施人到待确认超时
        $this->rewardTiming();//缺完成到奖励加分
        $this->workOverdueTiming();//实施人过期
    }
    
    /**
     * 邮件发送
     * @param $type
     * @param $email
     */
    public function sendEmail(Ir $model,$to,$name,$title = null,$cc = null){
        if(is_array($to)){
            $to  = array_filter($to);
        }
        $users = $this->getIrJudgeUsers($model->create_user);
        $emails = array_map(function($user){
            if(!empty($user['email'])){
                return $user['email'];
            }
        },$users);
        
        if ($cc){
            $cc = array_merge($cc,$emails);
        }else{
            $cc = $emails;
        }
        if(is_array($cc)){
            $cc  = array_filter($cc);
        }
        $emailModel = \Yii::createObject(['class' => IrSendMsg::className(), 'irModel' => $model]);
        //发送邮件
        $emailModel->timingSendEmail($to,$name,$title,$cc);
        $model->setAttribute('stick',Ir::STICK_YES);
        $model->setScenario(IrData::APPEAL_SCE);
        echo '发送成功！';
    }
    
    /**
     * 创建到主管4天后发生提醒
     */
    public function directorTiming(){
        $query = IrData::find();
        $query->where(['steps'=> Ir::SUB_IR_STEPS, 'is_draft'=> Ir::DRAFT_NO]);
        $query->andWhere(['!=' ,'status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'status',Ir::STATUS_APPEAL]);
        $query->andWhere(['<', 'create_time', strtotime('-4day')]);//逾期
        $model = $query->all();
        if(!empty($model)){
            foreach ($model as $value){
                $cc = [];
                $userInfo = UserInfo::findOne(['id' => $value->create_user]);
                if($userInfo->leader_uid){
                    $users = UserInfo::find()->select('email,chinese_name as name')->where(['id' => $userInfo->leader_uid])->asArray()->one();
                    if(!empty($users['email'])){
                         $cc[] = $this->getIrJudgeEmail($value->create_user);
                        $this->sendEmail($value,$users['email'],$users['name'],'',$cc);
                    }
                }
            }
        }
    }

    /**
     * 奖励发放超时
     */
    public function rewardTiming(){
        $query = IrData::find();
        $query->where(['steps'=> IrData::CONFIRM_STEPS, 'is_draft'=> IrData::DRAFT_NO]);
        $query->andWhere(['!=' ,'status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'status',Ir::STATUS_APPEAL]);
        $query->andWhere(['<', 'update_time', strtotime('-7day')]);//逾期
        $model = $query->all();
        if(!empty($model)){
            foreach ($model as $value){
                $users = $this->getIrJudgeUsers($value->create_user);
                if (empty($users)) {
                    continue;
                }
                $to = array_map(function($user){
                    if(!empty($user['email'])){
                        return $user['email'];
                    }
                },$users);
                $this->sendEmail($value,$to,'评估员');
            }
        }
    }

    /**
     * 实施完成到确认超时提醒
     */
    public function confirmTiming(){
        $query = IrData::find();
        $query->where(['steps'=> IrData::WORK_STEPS, 'is_draft'=> IrData::DRAFT_NO]);
        $query->andWhere(['!=' ,'status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'status',Ir::STATUS_APPEAL]);
        $query->andFilterWhere(['<', 'start_time', strtotime('-4day')]);//逾期
        $model = $query->all();
        if(!empty($model)){
            foreach ($model as $key => $value){
                $cc = [];
                $userInfo = UserInfo::findOne(['id' => $value->create_user]);
                if($userInfo->leader_uid){
                    $users = UserInfo::find()->select('email,chinese_name as name')->where(['id' => $userInfo->leader_uid])->asArray()->one();
                    if(!empty($users['email'])){
                         $cc[] = $this->getIrJudgeEmail($value->create_user);
                        $this->sendEmail($value,$users['email'],$users['name'],'',$cc);
                    }
                }
            }
        }
    }
    
    
    /**
     * 主管到评估超时提醒
     */
    public function assessTiming(){
        $query = IrData::find();
        $query->where(['steps'=> Ir::DIRECTOR_STEPS, 'is_draft'=> Ir::DRAFT_NO]);
        $query->andWhere(['!=' ,'status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'status',Ir::STATUS_APPEAL]);
        $query->andWhere(['<', 'director_submit_time', strtotime('-7day')]);//逾期
        $model = $query->all();
        if(!empty($model)){
            foreach ($model as $value){
                $users = $this->getIrJudgeUsers($value->create_user);
                if (empty($users)) {
                    continue;
                }
                $to = array_map(function($user){
                    if(!empty($user['email'])){
                        return $user['email'];
                    }
                },$users);
                $this->sendEmail($value,$to,'评估员');
            }
        } 
    }

    /**
     * 拿到对应班别的审批人
     * @param $create_user
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getIrJudgeUsers($create_user){
        $user = UserInfo::find()->select('shift')->where(['id' => $create_user])->one();
        return UserInfo::find()->alias('iu')
            ->innerJoin(['j' => IrJudge::tableName()],'j.user_id = iu.id')
            ->where(['iu.shift' => $user->shift,'j.status' => Base::SELECT_YES])
            ->all();
    }
   
    /**
     * 根据用户id获取评估人
     * @param $userId
     * @return string
     */
    public function getIrJudgeEmail($userId){
        $userInfoModel = UserInfo::findOne(['id' => $userId]);
        $query = UserInfo::find();
        $query->alias('u');
        $query->select('j.user_id');
        $query->innerJoin(['j' => IrJudge::tableName()],'j.user_id = u.id');
        $query->where(['j.type' => IrJudge::TYPE_AUDIT,'j.status' => IrJudge::STATUS_ON]);
        $query->andWhere(['u.area' => $userInfoModel->area]);
        $judgeUserId = $query->scalar();
        if ($judgeUserId){
            $userInfoModel = UserInfo::findOne(['id' => $judgeUserId]);
            return $userInfoModel->email;
        }
        return '';
    }

     
    /**
     * 负责人超时提醒
     * @param $steps
     */
    public function headTiming(){
       
        $query = IrData::find();
        $query->alias('i');
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
        $query->andWhere(['!=', new Expression('(select ir_id from nxp_ir_user where master = ' . IrUser::MASTER_YES . ' and ir_id = i.id and type = ' . IrUser::HEAD_TYPE . ')'), false]);
        $query->andWhere([
            'i.steps' => Ir::ASSESS_STEPS,
            'i.is_draft' => IrData::DRAFT_NO,    //不是草稿
        ]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
        $query->andWhere(['i.head_submit_time'=>'']);
        $query->andFilterWhere(['<', 'i.assess_time', strtotime('-7day')]);//逾期
        $model = $query->all();
        if(!empty($model)){
          foreach ($model as $value){
                $cc = [];
                $irUser = IrUser::findOne(['ir_id' => $value->id,'master' => IrUser::MASTER_YES,'type' => IrUser::HEAD_TYPE]);
                if($irUser){
                    $users = UserInfo::find()->select('email,chinese_name as name')->where(['id' => $irUser->user_id])->asArray()->one();
                    if(!empty($users['email'])){
                        $cc[] = $this->getIrJudgeEmail($value->create_user);
                        $this->sendEmail($value,$users['email'],$users['name'],'',$cc);
                    }
                }
            }
        }
       
    }
    
    /**
     *  实施人逾期提醒
     * @param $steps
     */
    public function workTiming(){
        
        $query = IrData::find();
        $query->alias('i');
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
        $query->andWhere(['!=', new Expression('(select ir_id from nxp_ir_user where master = ' . IrUser::MASTER_YES . ' and ir_id = i.id  and type = ' . IrUser::WORK_TYPE . ')'), false]);
        $query->andWhere([
            'i.steps' => Ir::HEAD_STEPS,
            'i.is_draft' => IrData::DRAFT_NO,  //不是草稿
        ]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
        $query->andWhere(['i.end_time'=>'']);
        $query->andFilterWhere(['<', 'i.head_submit_time', strtotime('-15day')]);//逾期
        $model = $query->all();
        if(!empty($model)){
             foreach ($model as $value){
                $cc = [];
                $irUser = IrUser::findOne(['ir_id' => $value->id,'master' => IrUser::MASTER_YES,'type' => IrUser::WORK_TYPE]);
                if($irUser){
                    $users = UserInfo::find()->select('email,chinese_name as name')->where(['id' => $irUser->user_id])->asArray()->one();
                    if(!empty($users['email'])){
                        $cc[] = $this->getIrJudgeEmail($value->create_user);
                        $this->sendEmail($value,$users['email'],$users['name'],'',$cc);
                    }
                }
            }
        }
    }
     /**
     * 实施人过期提醒
     * @param $steps
     */
    public function workOverdueTiming(){
        
        $query = IrData::find();
        $query->alias('i');
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
        $query->andWhere(['!=', new Expression('(select ir_id from nxp_ir_user where master = ' . IrUser::MASTER_YES . ' and ir_id = i.id  and type = ' . IrUser::WORK_TYPE . ')'), false]);
        $query->andWhere([
            'i.steps' => Ir::HEAD_STEPS,
            'i.is_draft' => IrData::DRAFT_NO,  //不是草稿
        ]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
        $query->andWhere(['!=' ,'i.end_time','']);
        $query->andWhere(['<', 'i.end_time', time()]);//过期
        $model = $query->all();
        if(!empty($model)){
            foreach ($model as $value){
                $cc = [];
                $irUser = IrUser::findOne(['ir_id' => $value->id,'master' => IrUser::MASTER_YES,'type' => IrUser::WORK_TYPE]);
                if($irUser){
                    $users = UserInfo::find()->select('email,chinese_name as name')->where(['id' => $irUser->user_id])->asArray()->one();
                    if(!empty($users['email'])){
                        $model = UserInfo::findOne(['id' => $value->create_user]);
                        if($model->leader_uid){
                            $userModel = UserInfo::findOne(['id' => $model->leader_uid]);
                            $cc[] = $userModel->email;
                        }
                        $cc[] = $this->getIrJudgeEmail($value->create_user);
                        $this->workSendEmail($value,$users['email'],$users['name'],'',$cc);
                    }
                }
            }
        }
    }
    
    public function workSendEmail(Ir $model,$to,$name,$title = null,$cc = null){
        if(is_array($to)){
            $to  = array_filter($to);
        }
        $users = $this->getIrJudgeUsers($model->create_user);
        $emails = array_map(function($user){
            if(!empty($user['email'])){
                return $user['email'];
            }
        },$users);
        
        if ($cc){
            $cc = array_merge($cc,$emails);
        }else{
            $cc = $emails;
        }
        if(is_array($cc)){
            $cc  = array_filter($cc);
        }
        $emailModel = \Yii::createObject(['class' => IrSendMsg::className(), 'irModel' => $model]);
        //发送邮件
        $emailModel->workSendEmail($to,$name,$title,$cc);
        $model->setAttribute('stick',Ir::STICK_YES);
        $model->setScenario(IrData::APPEAL_SCE);
        echo '发送成功！';
    }


}