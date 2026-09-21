<?php

namespace backend\models;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;

/**
 * This is the model class for table "nxp_new_gemba".
 *
 * @property int $id
 * @property string|null $title 标题
 * @property int|null $type 类型
 * @property int|null $old_status 当前状态
 * @property int|null $number 编号
 * @property string|null $content 内容
 * @property string|null $files 文件
 * @property int|null $norm 标准
 * @property int|null $area 区域
 * @property int|null $week 周别
 * @property int|null $month 月份
 * @property string|null $year 年
 * @property int|null $leader_status 主管确认
 * @property string|null $participant 建议实施人
 * @property int|null $create_user 创建人
 * @property int|null $auto_complete 自动完成
 * @property int|null $pm_visible pm可见
 * @property int|null $assess_user 审核人
 * @property int|null $assess_status 审核状态
 * @property int|null $assess_time 审核时间
 * @property int|null $assess_advice 审核意见
 * @property int|null $work_status 实施状态
 * @property int|null $is_accept 是否接受
 * @property int|null $work_user 实施人
 * @property string|null $work_advice 实施意见
 * @property string|null $work_two_advice 实施二次意见
 * @property int|null $start_time 开始时间
 * @property int|null $end_time 预计结束时间
 * @property int|null $status 状态
 * @property int|null $draft 草稿
 * @property int|null $steps 步骤
 * @property int|null $confirm 是否确认完成
 * @property string|null $confirm_content 确认内容
 * @property int|null $confirm_user 确认人
 * @property int|null $confirm_time 确认时间
 * @property int|null $create_time 创建时间
 * @property int|null $update_time 修改时间
 * @property int|null $delete_time 删除时间
 */
class NewGembaMapping extends \backend\models\Base
{
    const SELECT_YES = 1;
    const SELECT_NO  = 0;
    public static $selectList = [
        self::SELECT_YES => '是',
        self::SELECT_NO  => '否',
    ];

    public static $leaderStatusList = [
        self::SELECT_YES => '有效',
        self::SELECT_NO  => '无效'
    ];


    /**
     * 是否自主完成
     */
    const AUTO_COMPLETE_YES =1;
    const AUTO_COMPLETE_NO  = 0;

    public static $autoCompleteList = [
        self::AUTO_COMPLETE_NO => '否',
        self::AUTO_COMPLETE_YES=> '是'
    ];

    /**
     * 是否完成
     */
    const CONFIRM_YES = 1;
    const CONFIRM_NO  = 0;

    public static $confirmList = [
        self::CONFIRM_NO => '否',
        self::CONFIRM_YES=> '是',
    ];

    /**
     * 实施步骤
     */
    const CREATE_STEPS          = 1;
    const ASSESS_STEPS          = 2;
    const WORK_STEPS            = 3;
    const CONFIRM_STEPS         = 4;
    const COMPLETE_STEPS        = 5;
    const LEADER_STEPS          = 6;
    const AUTO_STEPS            = 7;
    const AUTO_CONFIRM_STEPS    = 8;
    const REFUSED_WORK_STEPS    = 9;
    const WORK_CONFIRM_STEPS    = 10;
    const ASSESS_REFUSED_STEPS  = 11;
    const CANCEL_STEPS          = 20;

    public static $stepsList = [
        self::CREATE_STEPS          => '待审核',
        self::ASSESS_STEPS          => '待实施',
        self::WORK_STEPS            => '实施中',
        self::CONFIRM_STEPS         => '待确认',
        self::COMPLETE_STEPS        => '已完成',
        self::LEADER_STEPS          => '待主管确认',
        self::AUTO_STEPS            => '自主完成中',
        self::AUTO_CONFIRM_STEPS    => '自主完成确认',
        self::WORK_CONFIRM_STEPS    => '实施人第一次拒绝待确认',
        self::REFUSED_WORK_STEPS    => '实施人第二次拒绝待确认',
        self::ASSESS_REFUSED_STEPS  => '审核人拒绝待确认',
        self::CANCEL_STEPS          => '已取消',
    ];


    /**
     * gemba状态
     */
    const STATUS_PERFORM    = 1;
    const STATUS_DIE        = 2;
    const STATUS_COMPLETE   = 3;

    public static $statusList = [
        self::STATUS_PERFORM    => '执行中',
        self::STATUS_DIE        => '已取消',
        self::STATUS_COMPLETE   => '已完成',
    ];

    /**
     * 列表展示的状态
     * @var array
     */
    public static $showStatus = [
        self::STATUS_PERFORM
    ];

    /**
     * 历史展示的状态
     * @var array
     */
    public static $dieStatus = [
        self::STATUS_DIE,
        self::STATUS_COMPLETE
    ];

    /**
     * 是否有标准
     */
    const NORM_YES = 1;
    const NORM_NO  = 0;
    public static $normList = [
        self::NORM_NO => '否',
        self::NORM_YES=> '是'
    ];

    const DRAFT_YES = 1;
    const DRAFT_NO  = null;

    public static $draftList = [
        self::DRAFT_NO => '否',
        self::DRAFT_YES=> '是'
    ];
    /**
     * 实施状态
     * @var array
     */
    const WORK_STATUS_ONGOING = 1;
    const WORK_STATUS_CONFIRM = 2;
    const WORK_STATUS_REFUSE = 3;

    public static $implementStatus = [
        self::WORK_STATUS_ONGOING =>'ONGOING',
        self::WORK_STATUS_CONFIRM =>'DONE',
    ];

    const SAFE_TYPE         = 1;
    const FIVE_S_TYPE       = 2;
    const MASS_TYPE         = 3;
    const EFFICIENCY_TYPE   = 4;
    const STANDARD_TYPE     = 5;
    const MATERIAL_TYPE     = 6;

    public static $typeList = [
        self::SAFE_TYPE         => '安全',
        self::FIVE_S_TYPE       => '5s',
        self::MASS_TYPE         => '质量',
        self::EFFICIENCY_TYPE   => '生产效率',
        self::STANDARD_TYPE     => '标准化作业',
        self::MATERIAL_TYPE     => '物料材料管理'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_new_gemba_mapping';
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

    public function checkLeaderUser($id){
        $userInfoModel = UserInfo::findOne(['id'=>$id]);
        $id = Yii::$app->user->identity->user_id;
        if ($id == $userInfoModel->leader_uid){
           return true;
        }
        return false;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'title'             => '标题',
            'type'              => '类型',
            'number'            => '编号',
            'old_status'        => '当前状态',
            'content'           => '建议内容',
            'files'             => '文件',
            'norm'              => '标准',
            'week'              => '周',
            'month'             => '月',
            'year'              => '年',
            'participant'       => '建议实施人',
            'create_user'       => '创建人',
            'auto_complete'     => '自主完成',
            'pm_visible'        => 'PM可见',
            'assess_user'       => '审核人',
            'assess_status'     => '审核状态',
            'assess_advice'     => '审核意见',
            'assess_time'       => '审核时间',
            'work_status'       => '实施状态',
            'area'              => '区域',
            'work_user'         => '实施人',
            'work_advice'       => '实施意见',
            'work_two_advice'   => '实施二次意见',
            'is_accept'         => '是否接受',
            'start_time'        => '开始时间',
            'end_time'          => '结束时间',
            'status'            => '状态',
            'draft'             => '草稿',
            'steps'             => '步骤',
            'confirm'           => '确认完成',
            'confirm_content'   => '确认内容',
            'confirm_user'      => '确认人',
            'confirm_time'      => '确认时间',
            'create_time'       => '创建时间',
            'update_time'       => '更新时间',
            'delete_time'       => '删除时间',
            'team'       => 'gemba小组',
            'shift'       => '班次',
            'section'       => '部门',
        ];
    }
}
