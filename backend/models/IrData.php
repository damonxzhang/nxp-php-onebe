<?php

namespace backend\models;

use backend\modules\Appeal\models\AppealFactory;
use Yii;
use yii\helpers\ArrayHelper;

class IrData extends Ir{
    //验证场景
    const SUB_IR_SCE     = 'sub_ir';//提交建议或修改建议
    const DIRECTOR_SCE   = 'director';//主管操作
    const ASSESS_SCE     = 'assess';//评估人评估
    const HEAD_SCE       = 'head';//负责人操作
    const WORK_SCE       = 'work';//实施
    const APPEAL_SCE     = 'appeal';//申诉
    const CONFIRM_SCE    = 'confirm';//确认
    const REWARD_SCE     = 'reward';//奖励
    const GEMBA_SCE       = 'geaba';//奖励


    public static $formNames = [
        self::SUB_IR_STEPS  => '_form',
        self::DIRECTOR_STEPS=> '_director_form',
        self::ASSESS_STEPS  => '_assess_form',
        self::HEAD_STEPS    => '_head_form',
        self::WORK_STEPS    => '_work_form',
        self::CONFIRM_STEPS => '_confirm_form',
        self::REWARD_STEPS  => '_reward_form',
    ];

    /**
     * 根据步骤设置场景
     * @return array
     */
    public function getStepsAndScenario(){
        return [
            self::SUB_IR_STEPS  => self::SUB_IR_SCE,
            self::DIRECTOR_STEPS=> self::DIRECTOR_SCE,
            self::ASSESS_STEPS  => self::ASSESS_SCE,
            self::HEAD_STEPS    => self::HEAD_SCE,
            self::WORK_STEPS    => self::WORK_SCE,
            self::CONFIRM_STEPS => self::CONFIRM_SCE,
            self::REWARD_STEPS  => self::REWARD_SCE,
        ];
    }
    public $master_head_users;//负责人
    public $head_users;//负责人
    public $master_work_users;//实施人
    public $work_users;//实施人

    public $appeal_model;//申诉model

    public $implement;

    public function scenarios()
    {
        return [
            self::SUB_IR_SCE   => ['subject','advice_type','old_status','old_images','advice_content','create_time'],
            self::DIRECTOR_SCE => ['standard','director_advice','director_submit_time','director_user'],
            self::ASSESS_SCE   => ['assess_status','assess_time','master_head_users','assess_advice','master_head_users','head_users'],
            self::HEAD_SCE     => ['head_advice','master_work_users','work_users','head_users', 'head_submit_time'],
            self::WORK_SCE     => ['work_users','work_status','is_work','work_score','work_content', 'end_time','work_images'],
            self::APPEAL_SCE   => ['status'],
            self::CONFIRM_SCE  => ['status','complete_confirm','confirm_user', 'update_time'],
            self::REWARD_SCE   => ['status', 'type', 'prize', 'update_time'],
            self::GEMBA_SCE   => ['status','old_status','create_user','create_time','steps','old_images','is_draft'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject','advice_type','old_status','advice_content'],'required'],
            [['director_advice'],'required'],
            [['assess_status','assess_user' ],'required'],
            [['head_advice','master_work_users'],'required'],
            [['steps','is_draft','id_work', 'create_user','advice_type', 'assess_status','head_submit_time', 'assess_time','end_time', 'assess_user', 'master_head_users', 'master_work_users', 'work_status','start_time', 'appeal','director_submit_time', 'create_time', 'update_time'], 'integer'],
            [['advice_content', 'work_content','work_users','confirm_user','head_users', 'head_advice'], 'string'],
            [['subject', 'old_status', 'standard', 'director_advice'], 'string', 'max' => 255],
            [['work_score','reward_score'],'double'],
        ];
    }

    public function beforeSave($insert)
    {
        if($insert){
            $this->steps = self::SUB_IR_STEPS;
            $this->status = self::STATUS_AUDIT;
            $this->create_user = Yii::$app->user->identity->user_id;
        }
        return parent::beforeSave($insert);
    }


    /**
     * 根据步骤设置验证场景
     */
    public function setScenarioBySteps(){
        $this->setScenario($this->getStepsAndScenario()[$this->steps]);
    }

    public function setSelectArray(){
        $selectArray = ['work_users', 'head_users'];
        foreach ($selectArray as $value){
            if(!empty($this->$value) && is_array($this->$value)){
                $this->$value = implode(',',$this->$value);
            }
        } 
    }

    /**
     * 根据场景设置参数
     * @return bool
     */
    public function setAttrBySteps(){

        if(!is_int($this->end_time)){
            $this->end_time = strtotime($this->end_time);
        }

        //验证表单
        if(!$this->validate()){
            return false;
        }
        if($this->stick == self::STICK_YES){
            $this->stick = self::STICK_NO;
        }
        switch ($this->steps){
            case self::SUB_IR_STEPS:
                if(!empty($this->old_images)){

                    $this->old_images = implode(',',$this->old_images);
                }
                $this->is_draft = Ir::DRAFT_NO;

                break;
            //主管审核
            case self::DIRECTOR_STEPS:
                $this->director_submit_time = time();
                if(!empty($this->standard)){
                    $this->status = Ir::STATUS_DIE;
                    $this->steps = Ir::SUB_IR_STEPS;
                }
                break;
            //评估
            case self::ASSESS_STEPS:
                $this->assess_time = time();
                $this->assess_user = Yii::$app->user->identity->user_id;
                //接受IR
                if($this->assess_status == self::ASSESS_STATUS_YES){

                    if(empty($this->master_head_users)){
                        $this->addError('master_head_users','请选择负责人！');
                        return false;
                    }

                    //状态为执行中
                    $this->status = self::STATUS_EXECUTE;
                    //保存负责人
                    $irUser = new IrUser();

                    if(!$irUser->saveUsers($this->master_head_users,explode(',',$this->head_users),$this->id,IrUser::HEAD_TYPE)){
                        $this->addError('head_users','保存失败！请稍后重试！');
                        return false;
                    }
                //不接受
                }else{
                    if(empty($this->assess_advice)){
                        $this->addError('master_head_users','请选择输入评估人建议！');
                        return false;
                    }
                    $this->steps  = self::DIRECTOR_STEPS;
                    $this->status = self::STATUS_DIE;
                }
                break;
            //负责人
            case self::HEAD_STEPS:
                $irUser = new IrUser();
                if(!$irUser->saveUsers('',explode(',',$this->head_users),$this->id,IrUser::HEAD_TYPE) || !$irUser->saveUsers($this->master_work_users,explode(',',$this->work_users),$this->id,IrUser::WORK_TYPE)){
                    $this->addError('head_users','保存失败！请稍后重试！');
                    return false;
                }
                $this->head_submit_time = time();
                break;
            //实施人
            case self::WORK_STEPS:

                if(empty($this->start_time)){
                    $this->start_time = time();
                }

                !empty($this->work_images) && $this->work_images = implode(',',$this->work_images);

                //实施中
                if($this->work_status == self::IMPLEMENT_STATUS_ONGOING) {
                    //实施中
                    $this->status = self::STATUS_CARRY_OUT;
                    //未实施完成、步骤不变
                    $this->steps = self::HEAD_STEPS;

                    //完成
                }elseif($this->work_status == self::IMPLEMENT_STATUS_DONE){

                    $this->status = self::STATUS_CONFIRM;
                }
                $irUser = new IrUser();
                //选择可实施  加分
                if($this->is_work == self::IS_WORK_YES && empty($this->work_score)){

                    $this->work_score = self::WORK_SCORE;
					$this->update_time = time();

                }

                if(!$irUser->saveUsers('',explode(',',$this->work_users),$this->id,IrUser::WORK_TYPE)){
                    $this->addError('head_users','保存失败！请稍后重试！');
                    return false;
                }
                break;
            //完成确认
            case self::CONFIRM_STEPS:
                //已完成
                $this->status = self::STATUS_PRIZE;
                break;
            case self::REWARD_STEPS:

                if(!empty(self::$scores[$this->prize])){

                    $this->reward_score = self::$scores[$this->prize];
                }
                $this->status = self::STATUS_OK;
                break;
        }
        return true;
    }

    /**
     * 验证当前用户是否有权限操作
     * @return bool
     */
    public function validateUser(){
        $is_ok = false;
        $login_user_id = Yii::$app->user->identity->user_id;
        $createUser = UserInfo::findOne($this->create_user);
      
        switch ($this->steps){
            //步骤1只有创建人可操作
             case self::SUB_IR_STEPS:
                 if($login_user_id == $this->create_user){
                     $is_ok = true;
                 }
                 break;
            //步骤2只有创建人主管可操作
            case self::DIRECTOR_STEPS:
                $checkSteps = $this->checkDirectorSteps($this->create_user,$login_user_id);
                if($checkSteps  == 2){
                    $is_ok = true;
                }else{
                    if($createUser->leader_uid == $login_user_id){
                        $is_ok = true;
                    }
                }
                break;
            //步骤3只有评估员可操作
            case self::ASSESS_STEPS:
                $checkSteps = $this->checkAssessSteps($this->create_user,$login_user_id);
                if($checkSteps  == 2){
                    $is_ok = true;
                }elseif($checkSteps  == 0){
                    $userInfo = Yii::$app->user->identity->userInfo;
                    if(IrJudge::isAppealUser($userInfo->id,$createUser->area)){
                        $is_ok = true;
                    }
                }
                break;
            //步骤4主负责人可操作
            case self::HEAD_STEPS:
                $irUser = IrUser::findMasterUserId($this->id,IrUser::HEAD_TYPE);

                if($login_user_id == $irUser->user_id){
                    $is_ok = true;
                }
                break;
            //步骤5主实施人可操作
            case self::WORK_STEPS:
                $irUser = IrUser::findMasterUserId($this->id,IrUser::WORK_TYPE);

                if($login_user_id == $irUser->user_id){
                    $is_ok = true;
                }
                break;
            //步骤6创建人或创建人主管操作
            case self::CONFIRM_STEPS:
                if($login_user_id == $this->create_user ||  $createUser->leader_uid == $login_user_id){
                    $is_ok = true;
                }
                break;
            //步骤7评审可员操作
            case self::REWARD_STEPS:
                $userInfo = Yii::$app->user->identity->userInfo;
                if(IrJudgeReward::isRewardUser($userInfo->id,$createUser->area)){
                    $is_ok = true;
                }
                break;

        };
        return $is_ok;
    }

    /**
     * 查询申诉
     * @return mixed
     */
    public function getAppealModel(){
        if($this->status == self::STATUS_APPEAL){
            $this->appeal_model = AppealFactory::create(Appeal::IR_MODEL)->find()->where(['relation_id' => $this->id ])->orderBy('id desc')->limit(1)->one();
        }else{
            $this->appeal_model = AppealFactory::create(Appeal::IR_MODEL);
        }
        return $this->appeal_model;
    }

    /**
     * 所有申诉
     * @return mixed
     */
    public function getAlllAppealParams(){

        return AppealFactory::create(Appeal::IR_MODEL)
            ->find()
            ->where(['relation_id' => $this->id ])
            ->all();
    }

    //判断是否需要走IR特殊审批
    public function checkDirectorSteps($create_user,$logUserId){
        $steps = 0;//0 无特殊审批权限  2特殊审批有权限
        $createUser = UserInfo::findOne($create_user);
        $sectionIr  = $createUser->section_ir;
        //判断是否需要走IR特殊审批
        if($sectionIr == 0){
            if($createUser->leader_uid == $logUserId){
                $steps = 2;
            }
        }else{
            //NLD PM DL和Leaded PM DL
            //在第一轮“直接主管"确认时，规则不变;(原先是谁批复，还让谁批复)
            if($sectionIr == UserInfo::NLD_PM_DL || $sectionIr == UserInfo::LEADED_PM_DL){
                if($createUser->leader_uid == $logUserId){
                    $steps = 2;
                }
            }
            //对于NLD PM IDL和Leaded PM IDL在第一轮"直接主管”确认时，NLDPM DL--是马健负责;LeadedPM DL是史健负责:后续规则保持不变;
            if($sectionIr == UserInfo::NLD_PM_IDL){
                $userIdArr = IrJudgeSpecial::find()->select('user_id')->where(['section_ir' => IrJudgeSpecial::NLD_PM_IDL, 'status' => 1])->column();
                if(in_array($logUserId,$userIdArr)){
                    $steps = 2;
                }
            }
            if($sectionIr == UserInfo::LEADED_PM_IDL){
                $userIdArr = IrJudgeSpecial::find()->select('user_id')->where(['section_ir' => IrJudgeSpecial::LEADED_PM_IDL, 'status' => 1])->column();
                if(in_array($logUserId,$userIdArr)){
                    $steps = 2;
                }
            }
        }
        return $steps;
    }

    public function checkAssessSteps($create_user,$logUserId){
        $steps = 0;//0 无特殊审批权限   1特殊审批无权限 2特殊审批有权限
        $createUser = UserInfo::findOne($create_user);
        $sectionIr  = $createUser->section_ir;
        //判断是否需要走IR特殊审批
        if($sectionIr > 0){
            $steps = 1;
            if($sectionIr == UserInfo::NLD_PM_DL){
                $userIdArr = IrJudgeSpecial::find()->select('user_id')->where(['section_ir' => IrJudgeSpecial::NLD_PM_DL, 'status' => 1])->column();
                if(in_array($logUserId,$userIdArr)){
                    $steps = 2;
                }
            }elseif($sectionIr == UserInfo::LEADED_PM_DL){
                $userIdArr = IrJudgeSpecial::find()->select('user_id')->where(['section_ir' => IrJudgeSpecial::LEADED_PM_DL, 'status' => 1])->column();
                if(in_array($logUserId,$userIdArr)){
                    $steps = 2;
                }
            }elseif($sectionIr == UserInfo::NLD_PM_IDL){
                $userIdArr = IrJudgeSpecial::find()->select('user_id')->where(['section_ir' => IrJudgeSpecial::NLD_PM_IDL, 'status' => 1])->column();
                if(in_array($logUserId,$userIdArr)){
                    $steps = 2;
                }
            }elseif($sectionIr == UserInfo::LEADED_PM_IDL){
                $userIdArr = IrJudgeSpecial::find()->select('user_id')->where(['section_ir' => IrJudgeSpecial::LEADED_PM_IDL, 'status' => 1])->column();
                if(in_array($logUserId,$userIdArr)){
                    $steps = 2;
                }
            }
        }
        return $steps;
    }


    /**
     * 获得当前步骤
     */
    public function getSteps(){
        //当前登录的用
        $logUser = Yii::$app->user->identity->userInfo;
        //创建IR的用户
        $createUser = UserInfo::findOne($this->create_user);
       
        //取消后无法继续编辑
        $steps = $this->appealRights($logUser,$createUser);
        if($steps){
            return $steps;
        }elseif($this->status == self::STATUS_DIE){
            return $steps;
        }

        switch ($this->steps){
            //创建人修改
            case self::SUB_IR_STEPS:
                //创建ir的直属领导意见
                if($this->is_draft == self::DRAFT_YES && $this->create_user == $logUser->id){
                    $steps = self::SUB_IR_STEPS;
                }else{
                    $checkSteps = $this->checkDirectorSteps($this->create_user,$logUser->id);
                    if($checkSteps  == 2){
                        $steps = self::DIRECTOR_STEPS;
                    }else{
                        if($createUser->leader_uid == $logUser->id){
                            $steps = self::DIRECTOR_STEPS;
                        }
                    }
                }
                break;
            //主管意见
            case self::DIRECTOR_STEPS:
                $checkSteps = $this->checkAssessSteps($this->create_user,$logUser->id);
                //判断是否需要走IR特殊审批
                if($checkSteps  == 2){
                    $steps = self::ASSESS_STEPS;
                }elseif($checkSteps  == 0){
                     //评估员创建
                     if(IrJudge::isAppealUser($logUser->id,$createUser->area)){
                        $steps = self::ASSESS_STEPS;
                    }
                }
                break;
            //评估员意见
            case self::ASSESS_STEPS:
                $irUser = IrUser::findMasterUserId( $this->id, IrUser::HEAD_TYPE);
                //主要负责人创建
                if(!empty($irUser) && $logUser->id == $irUser->user_id){
                    $steps = self::HEAD_STEPS;
                }
                break;
            //负责人意见
            case self::HEAD_STEPS:
                $irUser = IrUser::findMasterUserId( $this->id, IrUser::WORK_TYPE);

                //主要负责人创建
                if(!empty($irUser) && $logUser->id == $irUser->user_id){
                    $steps = self::WORK_STEPS;

                }
                break;
            //实施人意见
            case self::WORK_STEPS:
                if ($logUser->id == $this->create_user ||  $createUser->leader_uid == $logUser->id){
                    $steps = Ir::CONFIRM_STEPS;
                }
                break;
            //完成确认
            case self::CONFIRM_STEPS:
                if(IrJudgeReward::isRewardUser($logUser->id,$createUser->area)){
                    $steps = Ir::REWARD_STEPS;
                }
                break;
//            case self::REWARD_STEPS:
//
//                if($logUser->role_id == IrData::ASSESS_USER_ROLE){
//                    $steps = self::REWARD_STEPS;
//                }
//                break;
        }

        return $this->verifyAppeal($steps,$logUser);
    }

    /**
     * 验证是否在申诉，是否有处理申诉资格
     * @return bool
     */
    public function verifyAppeal($steps,$userInfo){

        if($this->status === self::STATUS_APPEAL ){
            // if($userInfo->id == Ir::APPEAL_USER){
            if(in_array($userInfo->id,Ir::$appeal_list)){
                if($this->steps == Ir::SUB_IR_STEPS){
                    return Ir::DIRECTOR_STEPS;
                }elseif($this->steps == Ir::DIRECTOR_STEPS){
                    return Ir::ASSESS_STEPS;
                }else{
                    return $this->steps;
                }
            }else{
                return false;
            }
        }else{
            return $steps;
        }

    }

    /**
     * 允许审核的步骤
     * @return bool
     */
    public function appealRights($user,$createUser){

        //当状态为取消
        if(($this->status == self::STATUS_DIE && $user->id == $this->create_user) || ($this->status == self::STATUS_APPEAL && IrJudge::isAppealUser($user->id,$createUser->area) )){
            if($this->steps == self::SUB_IR_STEPS){
                return self::DIRECTOR_STEPS;

                //评审员评审
            }elseif ($this->steps == self::DIRECTOR_STEPS){
                return self::ASSESS_STEPS;
            }
        }
        return false;
    }

    /**
     * 验证用户是否有权限提交申诉
     * @return bool
     */
    public function validateAppealUser(){
        $login_user_id = Yii::$app->user->identity->user_id;

        $is_ok = false;
        //步骤1、2、3 时 创建人可以申诉
        if($this->status == self::STATUS_DIE && $this->steps == self::SUB_IR_STEPS || $this->steps == self::STATUS_EXECUTE || $this->steps == self::ASSESS_STEPS ){
            if($login_user_id == $this->create_user){
                $is_ok = true;
            }
        }
        //步骤3、4 时 主负责人可以申诉
        if($this->steps == self::ASSESS_STEPS || $this->steps == self::HEAD_STEPS){
            $irUser = IrUser::findMasterUserId($this->id,IrUser::HEAD_TYPE);
            if($login_user_id == $irUser->user_id){
                $is_ok = true;
            }
        }
        //步骤4、5 时 主实施人可以申诉
        if($this->steps == self::HEAD_STEPS || $this->steps == self::WORK_STEPS){
            $irUser = IrUser::findMasterUserId($this->id,IrUser::WORK_TYPE);
            if($login_user_id == $irUser->user_id){
                $is_ok = true;
            }
        }

        //步骤5、6 时 创建人或创建人主管可以申诉
        if($this->steps == self::WORK_STEPS || $this->steps == self::CONFIRM_STEPS){
            $create_user = UserInfo::findOne($this->create_user);
            if($login_user_id == $create_user->id || $login_user_id == $create_user->leader_uid){
                $is_ok = true;
            }
        }
        return $is_ok;
    }

    public function getEmailParams(){



    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'head_users' => '负责人',
            'master_head_users' => '主负责人',
            'work_users' => '实施人',
            'master_work_users' => '主实施人',
        ]); // TODO: Change the autogenerated stub
    }
}