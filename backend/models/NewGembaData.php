<?php
namespace backend\models;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class NewGembaData extends NewGemba{

    const CREATE_SEC    = 'create';
    const ASSESS_SEC    = 'assess';
    const WORK_SEC      = 'work';
    const CONFIRM_SEC   = 'confirm';
    const LEADER_SEC    = 'leader';

    public $leader_confirm;

    public static $leaderConfirmList = [
        self::SELECT_NO  => '不合理',
        self::SELECT_YES => '合理'
    ];

    public function scenarios()
    {
        return ArrayHelper::merge([
            self::CREATE_SEC => ['year','title','type', 'files', 'content', 'norm','area', 'confirm','files_mapping', 'week', 'month','confirm_time', 'create_user' , 'auto_complete','start_time', 'end_time'],
            self::ASSESS_SEC => ['pm_visible','assess_advice','assess_status','work_user'],
            self::WORK_SEC => ['work_advice','work_two_advice','work_status','is_accept','end_time','files_mapping'],
            self::CONFIRM_SEC => ['confirm_user','confirm_content','confirm'],
            self::LEADER_SEC => ['leader_status'],
        ],parent::scenarios()) ;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title','type', 'norm', 'week', 'month','title'],'required'],
            ['confirm','required','on' => self::CONFIRM_SEC],
            [['type', 'norm', 'week', 'area','leader_status', 'month', 'end_time', 'confirm', 'is_accept', 'create_user', 'auto_complete', 'pm_visible', 'assess_user', 'assess_status', 'assess_time', 'work_status', 'work_user', 'start_time', 'end_time', 'status', 'draft', 'steps', 'confirm', 'confirm_user', 'confirm_time', 'create_time', 'update_time', 'delete_time'], 'integer'],
            [['title', 'content', 'participant', 'assess_advice', 'work_two_advice', 'confirm_content'], 'string', 'max' => 255],
            [[ 'work_advice', 'files','year','files_mapping'],'string']
        ];
    }

    /**
     * 验证场景对应的步骤
     * @var array
     */
    public $sceBySteps = [
        self::CREATE_STEPS => self::CREATE_SEC,
        self::ASSESS_STEPS => self::ASSESS_SEC,
        self::WORK_STEPS   => self::WORK_SEC,
        self::COMPLETE_STEPS=> self::CONFIRM_SEC,
    ];

    /**
     * 步骤对应的表单
     */
    const UPDATE_FORM = '_form';
    const ASSESS_FORM = '_assess_form';
    const WORK_FORM = '_work_form';
    const CONFIRM_FORM = '_confirm_form';

    public static $formList = [
        self::CREATE_STEPS  => self::UPDATE_FORM,
        self::ASSESS_STEPS  => self::ASSESS_FORM,
        self::WORK_STEPS    => self::WORK_FORM,
        self::COMPLETE_STEPS=> self::CONFIRM_FORM,
        self::AUTO_STEPS    => self::UPDATE_FORM
    ];

    public function behaviors()
    {
        //自动添加新增、修改时间
        return [$this->getTimestampBehaviorArray()];
    }

    /**
     * 设置验证场景
     * @return bool
     */
    public function setScenariosBySteps(){

        if(!in_array($this->steps,array_keys(self::$stepsList))){
            return false;
        }
        if (!empty($this->sceBySteps[$this->steps])){

            $this->setScenario($this->sceBySteps[$this->steps]);
        }
        return true;
    }

    public function setImagesPath(){
        if(!empty($this->files)){
            $this->files = json_decode($this->files,1);
        }
        if(!empty($this->files_mapping)){
            $this->files_mapping = json_decode($this->files_mapping,1);
        }
    }


    /**
     * 根据步骤设置参数
     * @return bool
     */
    public function setAttributesBySteps(){
        $result = true;
        //取消无法继续操作
        if(in_array($this->status,self::$dieStatus) || $this->status == self::STATUS_DIE){
            $this->addError('steps','gemba已经被取消！');

            return false;
        }
        switch ($this->steps){
            //创建
            case self::CREATE_STEPS:

                //自主完成
                if($this->auto_complete == self::AUTO_COMPLETE_YES){
                    if(!in_array($this->confirm,array_keys(self::$confirmList))){
                        $this->addError('id','服务器错误！请稍后重试');
                        $result = false;
                    }

                    //已完成
                    if($this->confirm == self::CONFIRM_YES){
                        if(empty($this->confirm_time)){
                            $this->addError('id','请选择完成时间！');
                            $result = false;
                        }
                        $this->confirm_time = time();
                        $this->confirm_user = \Yii::$app->user->identity->user_id;
                        $this->start_time = time();
                        $this->steps = self::LEADER_STEPS;

                    }else{
                        $this->confirm_time = null;
                        $this->steps = self::AUTO_STEPS;
                    }
                }else{

                    $this->confirm_time = null;
                }

                $this->status = self::STATUS_PERFORM;
                $this->create_user = \Yii::$app->user->identity->user_id;
                break;
            //自主完成
            case self::AUTO_STEPS:
                //自主完成
                if($this->auto_complete == self::AUTO_COMPLETE_YES){
                    if(!in_array($this->confirm,array_keys(self::$confirmList))){
                        $this->addError('id','服务器错误！请稍后重试');
                        $result = false;
                    }

                    //已完成
                    if($this->confirm == self::CONFIRM_YES){
                        if(empty($this->confirm_time)){
                            $this->addError('id','请选择完成时间！');
                            $result = false;
                        }
                        $this->confirm_time = time();
                        $this->confirm_user = \Yii::$app->user->identity->user_id;
                        $this->start_time = time();
                        $this->steps = self::LEADER_STEPS;
                    }else{
                        $this->steps = self::AUTO_STEPS;
                    }
                }else{
                    $this->status = self::STATUS_DIE;
                }
                $this->status = self::STATUS_PERFORM;
                $this->create_user = \Yii::$app->user->identity->user_id;

                break;
            //审核
            case self::ASSESS_STEPS:
                //主管才可以提交下一步
                if(!empty($this->area) && Judge::isJudge(Judge::TYPE_GEMBA,$this->area)){
                    $this->steps = self::CREATE_STEPS;
                    $this->assess_user = \Yii::$app->user->identity->user_id;

                }else{
                    $this->addError('assess_advice','没有权限！');
                    $result = false;
                }

                if($this->assess_user){
                    if( $this->assess_status == self::SELECT_YES){
                        if(empty($this->work_user)){
                            $this->addError('work_user','实施人不能为空！');
                            $result = false;
                        }

                        $this->steps = self::ASSESS_STEPS;

                    }else{
                        if(empty($this->assess_advice)){
                            $this->addError('assess_advice','审核人意见不能为空！');
                            $result = false;
                        }
                        $this->steps = self::ASSESS_REFUSED_STEPS;
                    }
                }

                $this->assess_time = time();
                break;
            //实施
            case self::WORK_STEPS :
            //确认
            case self::CONFIRM_STEPS:

                if($this->is_accept == self::SELECT_YES){
                    if(!empty($this->work_status) ){

                        //实施中
                        if($this->work_status == self::WORK_STATUS_ONGOING){
                            $this->steps = self::WORK_STEPS;
                        //完成
                        }elseIf($this->work_status == self::WORK_STATUS_CONFIRM){
                            $this->steps = self::CONFIRM_STEPS;
                        }

                    }else{
                        $this->addError('steps','完成状态不能为空！');
                        $result = false;
                    }

                    if( empty($this->end_time) ){
                        $this->addError('end_time','预计完成时间不能为空！');
                        $result = false;
                    }
                }else{

                    if ($this->oldAttributes['work_status'] != self::WORK_STATUS_REFUSE){
                        $this->steps = self::WORK_CONFIRM_STEPS;
                    }else{
                        $this->steps = self::REFUSED_WORK_STEPS;
                        $this->status = self::STATUS_PERFORM;
                    }
                    $this->work_status = self::WORK_STATUS_REFUSE;
                }

                $this->end_time = !empty($this->end_time) ? strtotime($this->end_time):null;
                if(empty($this->start_time)){
                    $this->start_time = time();
                }

                break;
            case self::COMPLETE_STEPS :
                $this->confirm_time = time();
                $this->confirm_user = \Yii::$app->user->identity->user_id;

                if($this->confirm == self::SELECT_YES){

                    $this->status = self::STATUS_COMPLETE;

                }else{

                    if(empty($this->oldAttributes['confirm_user'])){

                        $this->steps = self::WORK_STEPS;
                    }else{
                        $this->steps = self::COMPLETE_STEPS;
                        $this->status = self::STATUS_DIE;
                    }

                }
                break;
        }

        return $result;
    }

    /**
     * 获取步骤
     * @return bool|int
     */
    public function getSteps(){
        $steps = false;
        $create_user = UserInfo::findOne($this->create_user);
        $logUserId = \Yii::$app->user->identity->user_id;

        //取消无法继续操作
        if(in_array($this->status,self::$dieStatus) || $this->status == self::STATUS_DIE){
            return $steps;
        }
        switch ($this->steps){
            //待审核
            case self::CREATE_STEPS:

                //草稿
                if($this->draft == self::DRAFT_YES && $this->create_user == $logUserId){
                    $steps = self::CREATE_STEPS;
                //pm可见并且是pm主管
                }elseif(!empty($this->area)  && Judge::isJudge(Judge::TYPE_GEMBA,$this->area)){

                    $steps = self::ASSESS_STEPS;

                }
                break;
            case self::AUTO_STEPS:
                //自主完成
                if($this->auto_complete == self::AUTO_COMPLETE_YES && $logUserId == $this->create_user && $this->confirm != self::SELECT_YES){
                    $steps = self::CREATE_STEPS;
                    break;
                }else{

                    return false;
                }
                break;
            //待实施
            case self::ASSESS_STEPS:
                if($logUserId == $this->work_user){
                    $steps = self::WORK_STEPS;
                }
                break;
            //处理中
            case self::WORK_STEPS:
                if($logUserId == $this->work_user){
                    $steps = self::WORK_STEPS;
                }
                break;
            //待确认
            case self::CONFIRM_STEPS:
                if($logUserId == $this->create_user ){
                    $steps = self::COMPLETE_STEPS;
                }
                break;

        }

        return $steps;
    }

    /**
     * 按钮
     * @return string
     */
    public function getButtonBySteps(){

        $button = '';
        $user_id = \Yii::$app->user->identity->user_id;
        if($this->steps == self::ASSESS_REFUSED_STEPS && $this->create_user == $user_id){
            $button= Html::a('被审核人拒绝','javascript:;',[
                'class' => 'layui-btn layui-btn-normal user_conform',
                'data-text' => '被审核人拒绝',
                'data-url' => \yii\helpers\Url::to(['user-confirm','id' => $this->id])
            ]);

        }elseif($this->steps == self::WORK_CONFIRM_STEPS && $this->create_user == $user_id){
            $button = Html::a('拒绝实施确认','javascript:;',[
                'class' => 'layui-btn layui-btn-normal refused-work-confirm',
                'data-url' => \yii\helpers\Url::to(['user-confirm','id' => $this->id])
            ]);

        }elseif($this->steps == self::REFUSED_WORK_STEPS && $this->create_user == $user_id){
            $button = Html::a('实施人拒绝','javascript:;',[
                'class' => 'layui-btn layui-btn-normal user_conform',
                'data-text' => '实施人拒绝',
                'data-url' => \yii\helpers\Url::to(['user-confirm','id' => $this->id])
            ]);
        }elseif($this->steps == \backend\models\NewGemba::AUTO_CONFIRM_STEPS && $this->leader_status !== null && $this->create_user == $user_id){
            if($this->leader_status == self::SELECT_YES){
                $text = '主管确认有效！';
            }else{
                $text = '主管确认无效！';
            }
            $button = Html::a('完成确认','javascript:;',[
                'class' => 'layui-btn user_conform',
                'data-url' => \yii\helpers\Url::to(['user-confirm','id' => $this->id]),
                'data-text' => $text,
            ]);
       }elseif($this->steps == self::LEADER_STEPS && $this->checkLeaderUser($this->create_user)){
            $button = Html::a('完成确认','javascript:;',[
                'class' => 'layui-btn layui-btn-normal leader_conform',
                'data-url' => \yii\helpers\Url::to(['leader-confirm','id' => $this->id])
            ]);
        }

        return $button;
    }

    /**
     * 获取步骤
     * @return bool|int
     */
    public function validateUser(){
        $result = true;
        $create_user = UserInfo::findOne($this->create_user);
        $log_user_id = \Yii::$app->user->identity->user_id;
        switch ($this->steps){
            //待实施
            case self::ASSESS_STEPS:
                if(!Judge::isJudge(Judge::TYPE_GEMBA,$this->area)){
                    $this->addError('assess_user','当前登录用户没有操作权限！');
                    $result = false;
                }
                break;
            //处理中
            case self::WORK_STEPS:
                if($log_user_id != $this->work_user){
                    $result = false;
                }
                break;
            //待确认
            case self::CONFIRM_STEPS:
                if($log_user_id != $this->create_user ){
                    $result = false;
                }
             break;
        }
        return $result;
    }

}