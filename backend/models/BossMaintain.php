<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_boss_maintain".
 *
 * @property int $id
 * @property string|null $number 编号
 * @property int|null $f_id 设备
 * @property int|null $status 完成状态
 * @property string|null $location_num 位置编号
 * @property int|null $shift 班别
 * @property int|null $area 区域
 * @property int|null $type 类型
 * @property int|null $head_user 主管带班
 * @property string|null $things 问题事项
 * @property int|null $create_user 创建人
 * @property int|null $feedback_user 设备反馈人员
 * @property int|null $rejects 次品数量
 * @property string|null $files 附件
 * @property int|null $create_time 创建时间
 * @property int|null $update_time 更新时间
 */
class BossMaintain extends \backend\models\Base
{

    const STATUS_DIE    = 0;
    const STATUS_MARCH  = 1;
    const STATUS_FINISH = 2;

    /**
     * 实施状态
     * @var array
     */
    public static $statusList = [
        self::STATUS_DIE => '已取消',
        self::STATUS_MARCH => '执行中',
        self::STATUS_FINISH=> '已完成'
    ];
    public static $typeList = [
        0 => '其他',
        1 => '质量',
        2 => '5S',
        3 => '标准化',
        4 => '团队管理',
        5 => '安全',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_boss_maintain';
    }

    public function behaviors()
    {
        return [$this->getTimestampBehaviorArray()];
    }

    public function beforeSave($insert)
    {
        if($insert){
            (new MaintainReportRecord())->createRecord();
            $this->update_time = time();
        }
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_id','things','number',],'required'],
            [['f_id', 'status', 'shift', 'area', 'head_user', 'feedback_user', 'rejects',  'update_time'], 'integer'],
            [['things'], 'string'],
            [['number'], 'string', 'max' => 50],
            [['location_num', 'files'], 'string', 'max' => 255],
            [['create_time'],'safe']
        ];
    }

    /**
     * 新增异常
     * @return bool
     */
    public function newBossMaintain(){
        $this->create_user = Yii::$app->user->identity->user_id;
        $this->status = self::STATUS_MARCH;
        $this->create_time = strtotime($this->create_time);
        if (!empty($this->files)) {
            $this->files        = implode(',', $this->files);
        }
        return $this->save();
    }

    /**
     * 更新次品数量
     * @param $rejects
     * @return bool
     */
    public function changeMaintain($rejects){
        $this->rejects = $rejects;
        $types = BossFeedback::getTypes($this->id);
        if(count($types) == 2){
            $this->status = self::STATUS_FINISH;
        }
        return $this->save();
    }

    /**
     * 验证和设置异常设备信息
     * @return bool
     */
    public function setFacilityInfo(){
        $facility = Facility::findOne($this->f_id);
        if(empty($facility)){
            $this->addError('f_id','设备不存在');
            return false;
        }
        $this->feedback_user = $facility->head_user;
        $this->area = $facility->area;
        return true;
    }

    /**
     * 验证是否反馈
     * @return bool
     */
    public function checkFeedback(){
        $userId = Yii::$app->user->identity->user_id;
        $isLeader = $this->isLeader($userId);
        //可反馈用户为带班主管与设备负责人以及区域负责人
        if($this->head_user == $userId || $this->feedback_user == $userId || $isLeader){
            $feedbackUsers = BossFeedback::findAll(['m_id'=>$this->id]);
            //验证是否提交过反馈
            if(!empty($feedbackUsers)){
                 foreach ($feedbackUsers as $users){
                     //已提交反馈
                     if($users['feedback_user'] == $userId){
                         //反馈已完成 并且不是区域负责人
                         if($users['solve_status'] == BossFeedback::SOLVE_STATUS_YES && !$isLeader){
                             $this->addError('solve_status','已经完成反馈!');
                             return false;
                         //是区域负责人
                         }elseif($isLeader && BossFeedback::feedbackExistByType($this->id,BossFeedback::LEADER_TYPE)){

                             $this->addError('solve_status','已经完成反馈!');
                             return false;
                         }else;{
                             return true;
                         }
                     }
                 }
                 return true;
            }else{
                return true;
            }
        }
        $this->addError('solve_status','没有权限!');
        return false;
    }

    /**
     * 是否是区域主管
     * @param $user_id
     * @return bool
     */
    public function isLeader($user_id){
        $ids = BossMaintainLeader::getAreaLeaderUserId($this->f_id);
        if(in_array($user_id,$ids)){
            return true;
        }
        return false;
    }

    public function getUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'create_user']);
    }
    public function getFacility(){
        return $this->hasOne(Facility::className(),['id' => 'f_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'number' => Yii::t('app', '编号'),
            'create_user' => Yii::t('app', '创建人'),
            'f_id' => Yii::t('app', '设备'),
            'status' => Yii::t('app', '完成状态'),
            'location_num' => Yii::t('app', '位置编号'),
            'shift' => Yii::t('app', '班别'),
            'area' => Yii::t('app', '区域'),
            'head_user' => Yii::t('app', '主管带班'),
            'things' => Yii::t('app', '问题事项'),
            'feedback_user' => Yii::t('app', '设备反馈人员'),
            'rejects' => Yii::t('app', '次品数量'),
            'files' => Yii::t('app', '附件'),
            'create_time' => Yii::t('app', '发生日期'),
            'update_time' => Yii::t('app', 'Update Time'),
            'process' => Yii::t('app','工序'),
        ];
    }
}
