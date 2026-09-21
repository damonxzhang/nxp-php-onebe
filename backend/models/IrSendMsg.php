<?php
namespace backend\models;

use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class IrSendMsg extends Ir{

    public $email_title = 'New IR';
    public $irModel;
    private $to;//收件人
    private $cc = [];//抄送人
    private $recipients;//收信人id

    const DEFAULT_VIEW = 'default';
    const REFUSED_VIEW = 'refused';
    const REFUSED_HEAD_VIEW = 'refused_head';
    const ACHIEVEMENT_VIEW = 'achievement';
    const APPEAL_VIEW = 'appeal';
    const TIMING_VIEW = 'timing';
    const OVERDUE_VIEW = 'overdue';

    public static $emailView = [
        self::DEFAULT_VIEW      => ['subject' => 'NEW IR','view' => '@backend/views/ir/default_email.php'],
        self::REFUSED_VIEW      => ['subject' => 'IR Cancellation','view' => '@backend/views/ir/refused_email.php'],
        self::REFUSED_HEAD_VIEW => ['subject' => 'IR Cancellation,But,need to be fixed.','view' => '@backend/views/ir/refused_head_email.php'],
        self::ACHIEVEMENT_VIEW  => ['subject' => 'IR-Achievement','view' => '@backend/views/ir/achievement_email.php'],
        self::APPEAL_VIEW       => ['subject' => 'IR-Complaint','view' => '@backend/views/ir/appeal_email.php'],
        self::TIMING_VIEW       => ['subject' => 'IR-Pending Approval','view' => '@backend/views/ir/timing_email.php'],
        self::OVERDUE_VIEW      => ['subject' => 'IR-Overdue','view' => '@backend/views/ir/overdue_email.php'],
    ];


    public static $messages = [
        Ir::SUB_IR_STEPS   => ['title' => '主管审核','content_data' => '主管审核'],
        Ir::DIRECTOR_STEPS => ['title' => '评估','content_data' => '评估' ],
        Ir::ASSESS_STEPS   => ['title' => '负责人意见','content_data' => '负责人意见'],
        Ir::HEAD_STEPS     => ['title' => '实施人意见','content_data' => '实施人意见'],
        Ir::WORK_STEPS     => ['title' => '待确认','content_data'=> '待确认'],
        Ir::CONFIRM_STEPS  => ['title' => '奖励发放','content_data' => '奖励发放'],
        Ir::REWARD_STEPS   => ['title' => '已完成','content_data' => '已完成'],
    ];

    /**
     * 发送消息
     * @return bool
     */
    public function sendMessage(){

        if(!empty(self::$messages[$this->irModel->steps])){
            if($this->getRecipients()){
                $model = new Notice();
                return $model->create(ArrayHelper::merge(self::$messages[$this->irModel->steps] ,[
                    'type' => Notice::TYPE_IR,
                ]));
            }
        }
        return false;
    }

    /**
     * 发送邮件
     * @return bool
     */
    public function sendEmail(){
       
        //查询收信人
        $this->getRecipients();
        if(!empty($this->to['email'])){
            $view = self::$emailView[$this->getEmailView()];
            $mail = Yii::$app->mailer->compose($view['view'],['model' => $this->irModel,'name' => $this->to['name']]);
            $mail->setTo($this->to['email']);
            if(count($this->cc) > 0) $mail->setCc($this->cc);
            $mail->setSubject($view['subject']);
            return $mail->send();
        }
    }

    /**
     * 定时发送邮件
     * @param $to
     * @param $name
     * @param null $title
     * @param null $cc
     * @return bool
     */
    public function workSendEmail($to,$name,$title = null,$cc = null){
        if(!$title) $title = 'IR-Overdue';
        $this->getRecipients();
        if(!empty($to)){
            $mail = Yii::$app->mailer->compose(self::$emailView[self::OVERDUE_VIEW]['view'],['model' => $this->irModel,'name' => $name]);
            $mail->setTo($to);
            if(!empty($cc)) $mail->setCc($cc);
            $mail->setSubject($title);
            return $mail->send();
        }
    }

    /**
     * 定时发送邮件
     * @param $to
     * @param $name
     * @param null $title
     * @param null $cc
     * @return bool
     */
    public function timingSendEmail($to,$name,$title = null,$cc = null){
        if(!$title) $title = 'IR-Pending Approval';
        $this->getRecipients();
        if(!empty($to)){
            $mail = Yii::$app->mailer->compose(self::$emailView[self::TIMING_VIEW]['view'],['model' => $this->irModel,'name' => $name,'type' => 'warning']);
            $mail->setTo($to);
            if(!empty($cc)) $mail->setCc($cc);
            $mail->setSubject($title);
            return $mail->send();
        }
    }

    public function getEmailView()
    {
        $model = $this->irModel;
        //主管拒绝
        if ($model->steps == IrData::STATUS_EXECUTE && $model->assess_status == self::SELECT_NO && !empty($this->irModel->master_head_users)) {
            $view = self::REFUSED_HEAD_VIEW;
        //主管拒绝但是选择了负责人
        } elseif ($model->steps == IrData::STATUS_EXECUTE && $model->assess_status == self::SELECT_NO && $model->status != Ir::STATUS_APPEAL) {
            $view = self::REFUSED_VIEW;
        //已完成
        }elseif($model->steps == IrData::REWARD_STEPS && $model->status == IrData::STATUS_OK) {
            $view = self::ACHIEVEMENT_VIEW;

        }elseif($model->status == Ir::STATUS_APPEAL){
            $view = self::APPEAL_VIEW;
        }else {
            $view = self::DEFAULT_VIEW;
        }

        return $view;
    }

    /**
     * 根据步骤获取收信人
     * @return array|bool|\yii\db\ActiveRecord|null
     */
    public function getRecipients(){

        $userModel = new UserInfo();
        $steps = $this->irModel->steps;
        $status = $this->irModel->status;

        if($this->irModel->steps == IrData::DIRECTOR_STEPS && $this->irModel->assess_status == IrData::ASSESS_STATUS_NO && !empty($this->irModel->master_head_users)){

            //查询主管id
            $model = UserInfo::findOne(['id' => $this->irModel->create_user]);
            if($model->leader_uid){
                $leader_uid = $userModel->find()->where(['id' => $model->leader_uid])->select('id,email,chinese_name')->one();
                $this->to['email'][0] = $leader_uid['email'];
                $this->to['name'][0]  = $leader_uid['chinese_name'];
            }else{
                return false;
            }
            //负责人
            $user = $userModel->find()->select('id,email,chinese_name')->where(['id' => $this->irModel->master_head_users])->one();
            $this->recipients = $user->id;
            $this->to['email'][1] = $user['email'];
            $this->to['name'][1]  = $user['chinese_name'];
            
            //评估员
            $query = UserInfo::find();
            $query->alias('u');
            $query->select('u.id');
            $query->innerJoin(['ij' => IrJudge::tableName()],'ij.user_id = u.id');
            $query->andWhere(['ij.type' => IrJudge::TYPE_AUDIT,'ij.status' => IrJudge::STATUS_ON]);
            $query->andWhere(['u.area' => $model->area]);
            $uid = $query->scalar();
            if(!$uid){
                return false;
            }
            $user = UserInfo::findOne(['id' => $uid]);
            $this->cc[] =  $user->email;
        //申诉委员会
        }else if($status == Ir::STATUS_APPEAL){
            $model = UserInfo::findOne(['id' => $this->irModel->create_user]);
            $query = UserInfo::find();
            $query->alias('u');
            $query->select('u.id');
            $query->innerJoin(['ij' => IrJudge::tableName()],'ij.user_id = u.id');
            $query->andWhere(['u.area' => $model->area]);
            $uid = $query->scalar();
            if(!$uid){
                return false;
            }
            $user = $userModel->find()->select('id,email,chinese_name')->where(['id' => $uid])->one();
            $this->recipients = $user->id;
            $this->to = [
                'email' => $user->email,
                'name' => $user->chinese_name,
               // 'appeal_name' => $model->chinese_name
            ];
            $this->irModel['appeal'] = Appeal::findOne(['relation_id' => $this->irModel->id,'type' => $this->irModel->steps]);
            $leader_uid = $userModel->getLeaderUids($this->irModel->create_user);
            if(!empty($leader_uid)){
                $user = $userModel->find()->where(['id' => $leader_uid[0]])->select('id,email,chinese_name')->one();
                if(empty($user)){
                    return false;
                }
                $this->cc[] =  $user->email;
            }
            if($this->irModel['appeal']['appeal_user']){
                $appealUserModel = UserInfo::findOne(['id' => $this->irModel['appeal']['appeal_user']]);
	            if(!empty($appealUserModel)){
	                $this->cc[] =  $appealUserModel->email;
	            }
            }
            

            //给主管发送
        }elseif(($steps == Ir::SUB_IR_STEPS && $status != Ir::STATUS_DIE ) || ( $steps != Ir::SUB_IR_STEPS  && $status == Ir::STATUS_DIE) || $steps == Ir::REWARD_STEPS){
            $leader_uid = $userModel->getLeaderUids($this->irModel->create_user);
            if(!empty($leader_uid)){
                $user = $userModel->find()->where(['id' => $leader_uid[0]])->select('id,email,chinese_name')->one();
                if(empty($user)){
                    return false;
                }
                $this->recipients = $user->id;
                $this->to = [
                    'email' => $user->email,
                    'name' => $user->chinese_name
                ];
            }else{
                return false;
            }
        //给评估员发送
      //  }elseif(  $steps == Ir::DIRECTOR_STEPS  || $steps == Ir::CONFIRM_STEPS ){
        }elseif( $steps == Ir::CONFIRM_STEPS ){
            // $create_user = $userModel->find()->select('area')->where(['id' => $this->irModel->create_user])->limit(1)->one();
            // $users = $userModel->find()->select('u.id,u.email')->alias('u')
            //             ->innerJoin(['ij' => IrJudge::tableName()],'ij.user_id = u.id')
            //             ->where(['ij.type' => IrJudge::TYPE_AUDIT,'ij.status' => IrJudge::STATUS_ON])
            //             ->andWhere(['u.area' => $create_user->area])
            //             ->asArray()
            //             ->all();
            // $params = ArrayHelper::map($users,'id','email');
            // $this->recipients = array_keys($params);

            // $this->to = [
            //     'email' => array_values($params),
            //     'name' => '评估员'
            // ];
            $model = UserInfo::findOne(['id' => $this->irModel->create_user]);
            $query = UserInfo::find();
            $query->alias('u');
            $query->select('u.id');
            $query->innerJoin(['ij' => IrJudge::tableName()], 'ij.user_id = u.id');
            $query->andWhere(['ij.type' => IrJudge::TYPE_AUDIT, 'ij.status' => IrJudge::STATUS_ON]);
            $query->andWhere(['u.area' => $model->area]);
            $uid = $query->scalar();
            if (!$uid) {
                return false;
            }
            $user       = UserInfo::findOne(['id' => $uid]);
            $this->recipients = $user->id;
            $this->to         = [
                'email' => $user->email,
                'name'  => $user->chinese_name
            ];
            
        //负责人和实施人
        }elseif ($steps == Ir::ASSESS_STEPS || $steps == Ir::HEAD_STEPS){

            if($steps == Ir::ASSESS_STEPS){
                $type = IrUser::HEAD_TYPE;
            }else{
                $type = IrUser::WORK_TYPE;
            }
            $user = IrUser::getMasterUserInfo($this->irModel->id,$type);

            $this->recipients[] = $user['id'];
            $this->to = [
                'email' => $user['email'],
                'name' => $user['chinese_name']
            ];
            //次负责人
            $headUser = $userModel->find()->select('id,email')->where(['id' => $this->irModel->head_users])->one();
            if($headUser){
                $this->cc[] = $headUser->email;
            }
        //发送给创建人和创建人主管
        }elseif($steps == Ir::WORK_STEPS || ($status == Ir::STATUS_DIE &&  $steps == Ir::SUB_IR_STEPS )){

            $user = $userModel::findOne(['id' => $this->irModel->create_user]);
            $leader_user = $userModel::findOne(['id' => $user->leader_uid]);
            $this->recipients = [$user->id,$user->leader_uid];
            $this->to = [
                'email' => $user->email,
                'name' => $user->chinese_name
            ];
            $this->cc[] = $leader_user->email;
        }
        if(!empty($this->recipients)){

            return $this->recipients;
        }
        return false;
    }

    /**
     * 查询收件人邮箱
     */
    public function getEmailTo(){
        $users = UserInfo::find()->select('id,chinese_name,email')
                ->where(['id' => $this->recipients])
                ->asArray()
                ->all();
        foreach ($users as $key => $value){
            if( !empty($value) && $value['id'] == $this->recipients[0]){
                $this->to = $value;
            }elseif(!empty($value)){
                $this->cc[] = $value['email'];
            }
        }
    }

    /**
     * 根据条件得到id
     * @return array
     */
    public function getUserIdsByWhere($where){
        return UserInfo::find()->select('id')
                ->where($where)
                ->column();
    }
}