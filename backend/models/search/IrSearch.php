<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2020/1/14
 * Time: 11:07
 */
namespace backend\models\search;

use backend\models\Ir;
use Yii;
use backend\models\IrUser;
use backend\models\UserInfo;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use backend\models\IrJudgeReward;
class IrSearch extends Ir{

    public $start_time;//开始时间
    public $over_time;//结束时间
    public $shift;//班别
    public $area;//班别
    public $station_id;//班别
    public $this_ir;//我的ir
    public $relevant;//与我有关的ir
    public $team;//gemba小组

    /**
     * 我参与的页面筛选角色
     */
    const DIRECTOR = 1;//主管
    const ASSESS   = 2;//评审员
    const HEAD     = 3;//负责人
    const WORK     = 4;//实施人
    const APPEAL   = 5;//申诉委员会
    const CINFIRM  = 6;//申诉委员会
    const PRIZE    = 7;//申诉委员会
    public function rules()
    {
        return [
            [['steps','id','status','advice_type','shift','area','this_ir','relevant','station_id'],'integer'],
            [['start_time','over_time'],'string'],
            [['team'],'integer']
        ];
    }

    /**
     *
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params){

        $query = self::find()->alias('i');
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $this->load($params);
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
        $query->orderBy('  i.id desc');

        if(!$this->validate()){
            return $dataProvider;
        }
        $userInfo = \Yii::$app->user->identity->userInfo;

        if(!empty($this->shift)){

            $query->andWhere(['u.shift' => $this->shift]);
        }
        if(!empty($this->station_id)){

            $query->andWhere(['u.station_id' => $this->station_id]);
        }
        if($this->this_ir){
            $query->andWhere(['i.create_user' => $userInfo->id]);
        }

        $query->andFilterWhere([
            'i.id' => $this->id,
            'i.steps' => $this->steps,
            'i.status' => $this->status,
            'i.advice_type' => $this->advice_type,
            'u.area' => $this->area,
            'u.team' => $this->team
        ]);
        if(!empty($this->start_time)){
            $query->andFilterWhere([
                '>','i.create_time',strtotime($this->start_time)
            ]);
        }
        if(!empty($this->over_time)) {
            $query->andFilterWhere([
                '<', 'i.create_time', strtotime($this->over_time)
            ]);
        }
         if(isset($params['type']) && $params['type'] == 'history') {
            $query->andWhere( new Expression('(i.status = '.self::STATUS_DIE.' or i.status = '.self::STATUS_OK.')'));
        }else{
            $query->andWhere(['!=','i.status',self::STATUS_DIE]);
            $query->andWhere(['!=','i.status',self::STATUS_OK]);
        }
        $query->andWhere([ 'i.is_draft' => self::DRAFT_NO] );
        return $dataProvider;
    }

    /**
     *  历史ir搜索
     * @param $params
     * @return ActiveDataProvider
     */
     public function searchHistory($params)
        {
            
            $query        = self::find()->alias('i');
            $dataProvider = new ActiveDataProvider([
                'query' => $query
            ]);
            $this->load($params);
            $query->orderBy('i.id desc');
            
            if (!$this->validate()) {
                return $dataProvider;
            }
            $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
            $query->andFilterWhere(['u.shift' => $this->shift]);
            $uid = Yii::$app->user->identity->user_id;
            if ($this->this_ir) {
                $query->andWhere(['i.create_user' => $uid]);
            }
            if ($this->relevant) {
                $userInfo = Yii::$app->user->identity->userInfo;
                $query->andWhere(new Expression('i.create_user = ' . $userInfo->id . ' or u.leader_uid = ' . $uid . ' or (select count(id) from nxp_ir_user where master = 1 and ir_id = i.id)  IS NOT NULL'));
                if (IrJudge::isAppealUser($userInfo->id)) {
                    $query->orWhere(['area' => $userInfo->area]);
                }
            }
            if ($this->start_time){
                $query->andFilterWhere(['>', 'i.create_time', strtotime($this->start_time)]);
            }
            if ($this->over_time){
                $query->andFilterWhere(['<', 'i.create_time', strtotime($this->over_time)]);
            }
            $query->andWhere(['i.is_draft' => self::DRAFT_NO]);
            $query->andWhere(['in', 'i.status',[self::STATUS_DIE, self::STATUS_OK]]);
            $query->andFilterWhere([
            	'u.area'        => $this->area,
                'u.team'        => $this->team,
                'i.id'          => $this->id,
                'i.steps'       => $this->steps,
                'i.status'      => $this->status,
                'i.advice_type' => $this->advice_type,
            ]);
            return $dataProvider;
        }

                /**
         * 逾期
         * @param $params
         * @return ActiveDataProvider
         */
        public function overdueSearch($params)
        {
            $userInfo     = Yii::$app->user->identity->userInfo;
            $query        = self::find()->alias('i');
            $dataProvider = new ActiveDataProvider([
                'query' => $query
            ]);
            $this->load($params);
            if (!$this->validate()) {
                return $dataProvider;
            }
            $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
            
            //主管
            if (empty($this->relevant) || $this->relevant == self::DIRECTOR) {
                if($userInfo->id == 230){//马建
                    $query->andWhere( new Expression('i.steps = '.self::SUB_IR_STEPS .' and (u.leader_uid ='.$userInfo->id .' or u.section_ir =' . UserInfo::NLD_PM_IDL .')') );
                }elseif($userInfo->id == 224){//史健
                    $query->andWhere( new Expression('u.section_ir =' . UserInfo::LEADED_PM_IDL .' and  i.steps = '.self::SUB_IR_STEPS) );
                }else{
                    $query->andWhere( new Expression('u.section_ir not in (' . UserInfo::NLD_PM_IDL .','. UserInfo::LEADED_PM_IDL . ') and u.leader_uid ='.$userInfo->id .' and  i.steps = '.self::SUB_IR_STEPS) );
                }
                $query->andFilterWhere(['<', 'i.create_time', strtotime('-4day')]);//逾期
                $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
                //评估员
            } elseif ($this->relevant == self::ASSESS) {
                if($userInfo->id == 230){//马建
                    $query->andWhere( new Expression('u.section_ir in (' . UserInfo::NLD_PM_DL .','. UserInfo::NLD_PM_IDL . ') and  i.steps = '.self::DIRECTOR_STEPS) );
                }elseif($userInfo->id == 224){//史健
                    $query->andWhere( new Expression('u.section_ir in (' . UserInfo::LEADED_PM_DL .','. UserInfo::LEADED_PM_IDL . ') and  i.steps = '.self::DIRECTOR_STEPS) );
                }elseif(IrJudge::isAppealUser($userInfo->id)){
                    $query->andWhere( new Expression('u.section_ir not in (' . UserInfo::NLD_PM_DL .','. UserInfo::NLD_PM_IDL .','. UserInfo::LEADED_PM_DL .','. UserInfo::LEADED_PM_IDL . ') and  i.steps = '.self::DIRECTOR_STEPS) );
                }else{
                    $query->andWhere( new Expression('u.section_ir not in (' . UserInfo::NLD_PM_DL .','. UserInfo::NLD_PM_IDL .','. UserInfo::LEADED_PM_DL .','. UserInfo::LEADED_PM_IDL . ')') );
                }

                $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
                $query->andFilterWhere(['<', 'i.director_submit_time', strtotime('-7day')]);
                //负责人
            } elseif ($this->relevant == self::HEAD) {
                $query->andWhere(['!=', new Expression('(select ir_id from nxp_ir_user where master = ' . IrUser::MASTER_YES . ' and ir_id = i.id and user_id = ' . $userInfo->id . ' and type = ' . IrUser::HEAD_TYPE . ')'), false]);
                $query->andFilterWhere(['<', 'i.assess_time', strtotime('-7day')]);
                $query->andWhere(['i.steps' => Ir::ASSESS_STEPS]);
                $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
                //实施人
            } elseif ($this->relevant == self::WORK) {
                $query->andWhere(['!=', new Expression('(select ir_id from nxp_ir_user where master = ' . IrUser::MASTER_YES . ' and ir_id = i.id and user_id = ' . $userInfo->id . ' and type = ' . IrUser::WORK_TYPE . ')'), false]);
                $query->andWhere(['i.end_time'=>'']);
                $query->andFilterWhere(['<', 'i.head_submit_time', strtotime('-15day')]);
                $query->andWhere(['i.steps' => Ir::HEAD_STEPS]);
                $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
                //结果确认
            } elseif ($this->relevant == self::CINFIRM) {
                $query->andWhere(new Expression('i.steps = ' . Ir::WORK_STEPS . ' and (i.create_user = ' . $userInfo->id . ' or u.leader_uid = ' . $userInfo->id . ')'));
                $query->andFilterWhere(['<', 'i.start_time', strtotime('-4day')]);
                $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
                //奖品
            } elseif ($this->relevant == self::PRIZE && IrJudge::isAppealUser($userInfo->id)) {
                $query->andWhere(['i.steps' => Ir::CONFIRM_STEPS]);
                $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
                //不符合要求的
            } else {
                $query->andWhere(['i.id' => 0]);
            }
            $query->andWhere(['i.is_draft' => self::DRAFT_NO]);
            $query->andWhere(['u.area' => $userInfo->area]);
            $query->andWhere(['!=', 'i.status', [self::STATUS_DIE, self::STATUS_APPEAL, self::STATUS_OK]]);
            $query->orderBy('i.id asc');
            return $dataProvider;
        }
        
    /**
     *  根据标题搜索ir
     * @param $subject
     * @return array|bool
     */
    public function searchTitle($subject){

        $query = self::find()->select('id,subject');
        if(!empty($subject)){

            $query->Where(['like','subject',$subject]);
        }
        $data = $query->asArray()->all();
        if(!empty($data)){
            return array_map(function ($arr){
                return ['text' => $arr['subject'],'id' => $arr['id']];
            },$data);
        }else{
            return false;
        }
    }

    /**
     * 草稿箱
     * @return ActiveDataProvider
     */
    public function draftSearch(){
        $query = self::find()->alias('i');
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $query->where([ 'i.is_draft' => self::DRAFT_YES,'create_user' => \Yii::$app->user->identity->user_id]);
        $query->orderBy('i.id desc');

        return $dataProvider;

    }

    /**
     * 与我相关
     * @return ActiveDataProvider
     */
    public function searchCorrelation($params){
        $userInfo = \Yii::$app->user->identity->userInfo;

        $query = self::find()->alias('i');
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $this->load($params);
        $query->orderBy('i.id desc');
        $query->andWhere(['i.is_draft' => self::DRAFT_NO]);

        if(!$this->validate()){

            return $dataProvider;
        }
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');

        //主管
        if(empty($this->relevant) || $this->relevant == self::DIRECTOR){
           
            if($userInfo->id == 230){//马建
                $query->andWhere( new Expression('i.steps = '.self::SUB_IR_STEPS .' and (u.leader_uid ='.$userInfo->id .' or u.section_ir =' . UserInfo::NLD_PM_IDL .')') );
            }elseif($userInfo->id == 224){//史健
                $query->andWhere( new Expression('u.section_ir =' . UserInfo::LEADED_PM_IDL .' and  i.steps = '.self::SUB_IR_STEPS) );
            }else{
                $query->andWhere( new Expression('u.section_ir not in (' . UserInfo::NLD_PM_IDL .','. UserInfo::LEADED_PM_IDL . ') and u.leader_uid ='.$userInfo->id .' and  i.steps = '.self::SUB_IR_STEPS) );
            }
            $query->orderBy('i.id asc');
        //评估员
        }elseif ($this->relevant == self::ASSESS){
            if($userInfo->id == 230){//马建
                $query->andWhere( new Expression('u.section_ir in (' . UserInfo::NLD_PM_DL .','. UserInfo::NLD_PM_IDL . ') and  i.steps = '.self::DIRECTOR_STEPS) );
            }elseif($userInfo->id == 224){//史健
                $query->andWhere( new Expression('u.section_ir in (' . UserInfo::LEADED_PM_DL .','. UserInfo::LEADED_PM_IDL . ') and  i.steps = '.self::DIRECTOR_STEPS) );
            }elseif(IrJudge::isAppealUser($userInfo->id)){
                $query->andWhere( new Expression('u.section_ir not in (' . UserInfo::NLD_PM_DL .','. UserInfo::NLD_PM_IDL .','. UserInfo::LEADED_PM_DL .','. UserInfo::LEADED_PM_IDL . ') and  i.steps = '.self::DIRECTOR_STEPS) );
            }else{
                $query->andWhere( new Expression('u.section_ir not in (' . UserInfo::NLD_PM_DL .','. UserInfo::NLD_PM_IDL .','. UserInfo::LEADED_PM_DL .','. UserInfo::LEADED_PM_IDL . ')') );
            }
            $query->andWhere(['u.area' => $userInfo->area]);
        //负责人
        }elseif($this->relevant  == self::HEAD){
            $query->andWhere(['!=',new Expression('(select ir_id from nxp_ir_user where master = '.IrUser::MASTER_YES.' and ir_id = i.id and user_id = '.$userInfo->id.' and type = '.IrUser::HEAD_TYPE .')'),false]);

            $query->andWhere(['i.steps' => Ir::ASSESS_STEPS]);
        //实施人
        }elseif($this->relevant  == self::WORK){
            $query->andWhere(['!=',new Expression('(select ir_id from nxp_ir_user where master = '.IrUser::MASTER_YES.' and ir_id = i.id and user_id = '.$userInfo->id.' and type = '.IrUser::WORK_TYPE.')' ),false]);

            $query->andWhere(['i.steps' => Ir::HEAD_STEPS]);
        //申诉
        }elseif($this->relevant  == self::APPEAL && in_array($userInfo->id , Ir::$appeal_list) ){
            $query->andWhere(['i.status' => Ir::STATUS_APPEAL]);
            if($userInfo->id != 1){
                $query->andWhere(['u.area' => $userInfo->area]);
            }
            
        //结果确认
        }elseif($this->relevant == self::CINFIRM){
            $query->andWhere(new Expression('i.steps = '.Ir::WORK_STEPS.' and (i.create_user = '.$userInfo->id.' or u.leader_uid = '.$userInfo->id.')'));

        //奖品
        }elseif($this->relevant == self::PRIZE && IrJudgeReward::isRewardUser($userInfo->id)){
            if($userInfo->id == 230){//马建
                $query->orWhere(['i.assess_user' => 230]);
            }
            $query->andWhere(['i.steps' => Ir::CONFIRM_STEPS]);
            $query->andWhere(['u.area' => $userInfo->area]);
        //不符合要求的
        }else{
            $query->andWhere(['i.id' => 0]);
        }

        if($this->relevant != self::APPEAL){
            $query->andWhere(['!=' ,'i.status',Ir::STATUS_DIE]);
            $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
        }
        return $dataProvider;
    }

    /**
     * 查询所以被评估的
     */
    public function searchToAssess(){
        return self::find()->alias('i')->select(new Expression(
            '
            assess_user,
            steps,
            stick,
            COUNT(id) as num'
        ))->groupBy(
            ' 
            assess_user,
            steps, 
            stick,
            id'
        )->where(['is not','assess_user',null])->orderBy('steps')
        ->andWhere(['!=','status', self::STATUS_DIE])//已取消
        ->andWhere(['!=','status' , self::ASSESS_STEPS])//申诉
        ->andWhere(['!=','status' , self::REWARD_STEPS])//完成
        ->andWhere(['!=','steps' , self::ASSESS_STEPS])//评估
        ->asArray()->all();
    }

    /**
     * 统计主管逾期IR
     * @param array $param
     * @return bool|int|string|null
     */
    public function searchLeaderData($param = [])
    {
        $query = self::find();
        $query->alias('i');
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
        if($param['id'] == 230){//马建
            $query->andWhere( new Expression(' (u.leader_uid ='.$param["id"] .' or u.section_ir =' . UserInfo::NLD_PM_IDL .')') );
        }elseif($param['id'] == 224){//史健
            $query->andWhere( new Expression(' (u.leader_uid ='.$param["id"] .' or u.section_ir =' . UserInfo::LEADED_PM_IDL .')') );
        }else{
            $query->andWhere( new Expression('u.section_ir not in (' . UserInfo::NLD_PM_IDL .','. UserInfo::LEADED_PM_IDL . ') and u.leader_uid =' . $param['id']) );
        }
        $query->andWhere([
            'i.steps'      => self::SUB_IR_STEPS,
            'i.is_draft'   => self::DRAFT_NO,
        ]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
        if (isset($param['type']) && $param['type'] == 'overdue') {
            $query->andFilterWhere(['<', 'i.create_time', strtotime('-4day')]);//逾期
        } else {
            $query->andFilterWhere(['>=', 'i.create_time', strtotime('-4day')]);//进行中
        }
        return $query->count();
    }

    /**
     * 统计负责人逾期IR
     * @param array $param
     * @return bool|int|string|null
     */
    public function searchHeadData($param = [])
    {
        $query = self::find();
        $query->alias('i');
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
        $query->andWhere(['!=', new Expression('(select ir_id from nxp_ir_user where master = ' . IrUser::MASTER_YES . ' and ir_id = i.id and user_id = ' . $param['id'] . ' and type = ' . IrUser::HEAD_TYPE . ')'), false]);
        $query->andWhere([
            'i.steps' => Ir::ASSESS_STEPS,
            'i.is_draft' => self::DRAFT_NO,    //不是草稿
        ]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
        if (isset($param['type']) && $param['type'] == 'overdue') {
            $query->andFilterWhere(['<', 'i.assess_time', strtotime('-7day')]);//逾期
        } else {
            $query->andFilterWhere(['>=', 'i.assess_time', strtotime('-7day')]);//进行中
        }
        return $query->count();
    }

    /**
     * 统计实施人逾期IR
     * @param array $param
     * @return bool|int|string|null
     */
    public function searchWorkData($param = [])
    {
        $query = self::find();
        $query->alias('i');
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
        $query->andWhere(['!=', new Expression('(select ir_id from nxp_ir_user where master = ' . IrUser::MASTER_YES . ' and ir_id = i.id and user_id = ' . $param['id'] . ' and type = ' . IrUser::WORK_TYPE . ')'), false]);
        $query->andWhere([
            'i.steps' => Ir::HEAD_STEPS,
            'i.is_draft' => self::DRAFT_NO,  //不是草稿
        ]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
        $query->andWhere(['i.end_time'=>'']);
        if (isset($param['type']) && $param['type'] == 'overdue') {
            $query->andFilterWhere(['<', 'i.head_submit_time', strtotime('-15day')]);//逾期
        } else {
            $query->andFilterWhere(['>=', 'i.head_submit_time', strtotime('-15day')]);//未开始
        }
        return $query->count();
    }
        /**
     * 统计实施人逾期IR
     * @param array $param
     * @return bool|int|string|null
     */
    public function searchWorkDataNew($param = [])
    {
        $query = self::find();
        $query->alias('i');
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
        $query->andWhere(['!=', new Expression('(select ir_id from nxp_ir_user where master = ' . IrUser::MASTER_YES . ' and ir_id = i.id and user_id = ' . $param['id'] . ' and type = ' . IrUser::WORK_TYPE . ')'), false]);
        $query->andWhere([
            'i.steps' => Ir::HEAD_STEPS,
            'i.is_draft' => self::DRAFT_NO,  //不是草稿
        ]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
        $query->andWhere(['!=' ,'i.end_time','']);
        if (isset($param['type']) && $param['type'] == 'overdue') {
            $query->andFilterWhere(['<', 'i.end_time', time()]);//过期
        } else {
            $query->andFilterWhere(['>=', 'i.end_time', time()]);//进行中
        }
        return $query->count();
    }
    /**
     * 统计结果确认逾期IR
     * @param array $param
     * @return bool|int|string|null
     */
    public function searchConfirmData($param = [])
    {
        $query = self::find();
        $query->alias('i');
        $query->innerJoin(['u' => UserInfo::tableName()], 'i.create_user = u.id');
        $query->andWhere([
            'u.leader_uid' => $param['id'],
            'i.steps'      => self::WORK_STEPS,
            'i.is_draft'   => self::DRAFT_NO,
        ]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_DIE]);
        $query->andWhere(['!=' ,'i.status',Ir::STATUS_APPEAL]);
        if (isset($param['type']) && $param['type'] == 'overdue') {
            $query->andFilterWhere(['<', 'i.start_time', strtotime('-4day')]);//逾期
        } else {
            $query->andFilterWhere(['>=', 'i.start_time', strtotime('-4day')]);//进行中
        }
        return $query->count();
    }
    
}