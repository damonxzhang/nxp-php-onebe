<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_ir".
 *
 * @property int $id
 * @property string $subject 标题
 * @property int $advice_type 建议类型
 * @property string $old_status 当前情况
 * @property string $old_images 当前情况图
 * @property int $steps 流程步骤
 * @property string $advice_content 建议内容
 * @property int $create_user 建议人
 * @property string $standard 标准
 * @property string $director_advice 主管意见
 * @property string $director_user 主管
 * @property int $director_submit_time 主管提交时间
 * @property int $assess_status 评估状态
 * @property int $status 状态
 * @property int $assess_time 评估时间
 * @property int $assess_user 评估人
 * @property int $assess_advice 评估人建议
 * @property int $head_advice 负责人意见
 * @property int $head_submit_time 负责提交时间
 * @property int $work_status 实施状态
 * @property string $work_content 实施内容
 * @property string $work_images 实施图
 * @property int $is_work 实施图
 * @property int $start_time 开始实施时间
 * @property int $end_time 预计完成时间
 * @property int $type 建议类型
 * @property int $complete_confirm 完成确认
 * @property int $confirm_user 确认人
 * @property int $prize  奖项
 * @property int $is_draft  草稿
 * @property int $stick  草稿
 * @property int $appeal 申诉状态
 * @property int $create_time 创建时间
 * @property int $work_score 实施奖励分数
 * @property int $reward_score 完成奖励分数
 * @property int $update_time 完成奖励分数
 */
class Ir extends Base
{
    //步骤
    const SUB_IR_STEPS   = 1;
    const DIRECTOR_STEPS = 2;
    const ASSESS_STEPS   = 3;
    const HEAD_STEPS     = 4;
    const WORK_STEPS     = 5;
    const CONFIRM_STEPS  = 6;
    const REWARD_STEPS   = 7;

    public static $stepsArr = [
        self::SUB_IR_STEPS   => '待主管审核',
        self::DIRECTOR_STEPS => '待评估人审核',
        self::ASSESS_STEPS   => '待负责人意见',
        self::HEAD_STEPS     => '待实施人意见',
        self::WORK_STEPS     => '待结果确认',
        self::CONFIRM_STEPS  => '待奖励发放',
        self::REWARD_STEPS   => '已完成',
    ];

   const DRAFT_YES = 1;
   const DRAFT_NO = null;

    //建议类型
    const TYPE_BREAK_FIX = 1;
    const TYPE_IMPOROVEMENT_IR = 2;

    public static $types = [
        self::TYPE_BREAK_FIX => 'Break fix',
        self::TYPE_IMPOROVEMENT_IR => 'Improvement IR'
    ];
    //奖品
    public static $prizes = [
        1 => 'Bravo!（积分兑换）',
        2 => 'PE加1分',
        3 => 'PE加0.5分',
        4 => 'PE加0.4分',
        5 => 'PE加0.3分',
        6 => 'PE加0.2分',
        7 => 'PE加0.1分',
        8 => 'PE不加分'
    ];
    //分数
    public static $scores = [
        1 => 0,
        2 => 1,
        3 => 0.5,
        4 => 0.4,
        5 => 0.3,
        6 => 0.2,
        7 => 0.1,
        8 => 0
    ];

    const WORK_SCORE  = 0.1;

    //审核人角色
    const ASSESS_USER_ROLE = 15;
    //申诉委员会用户id
    const APPEAL_USER = 1;

    public static $appeal_list = [1,31,32,33,34];

    //建议类型
    public static $adviceTypes = [
        1 => 'EHS',
        2 => '质量',
        3 => '生产效率',
        4 => '降低成本',
        5 => '员工士气',
        6 => '5S',
    ];
    //不及格IR评判标准
    public static $standards = [
        1 => '抱怨性质',
        2 => '重复性建议',
        3 => '属于申请人本职工作，例如机器需要维修等或对团队成员的失误疏忽进行指正的',
        4 => '仅指出不合理的地方，但没有相关改善建议',
        5 => '此IR属于Gemba范畴,请重新提交',
    ];
    //图片路径
    public $images_path;

    //审批员操作
    const ASSESS_STATUS_YES = 1;
    const ASSESS_STATUS_NO  = 0;

    public static $assessStatusArr = [
        self::ASSESS_STATUS_NO  => '不接受',
        self::ASSESS_STATUS_YES => '接受',
    ];

    //状态
    const STATUS_DIE        = 0;
    const STATUS_AUDIT      = 1;
    const STATUS_EXECUTE    = 2;
    const STATUS_APPEAL     = 3;
    const STATUS_CARRY_OUT  = 4;
    const STATUS_CONFIRM    = 5;
    const STATUS_PRIZE      = 6;
    const STATUS_OK         = 7;

    public static $statusArr = [
        self::STATUS_DIE        => '已取消',
        self::STATUS_AUDIT      => '待审核',
        self::STATUS_EXECUTE    => '执行中',
        self::STATUS_CARRY_OUT  => '实施中',
        self::STATUS_APPEAL     => '申诉中',
        self::STATUS_CONFIRM    => '待确认',
        self::STATUS_PRIZE      => '奖励发放',
        self::STATUS_OK         => '已完成'
    ];

    public static $assessListShowSteps = [
        self::DIRECTOR_STEPS,
        self::HEAD_STEPS,
        self::WORK_STEPS,
        self::CONFIRM_STEPS,
    ];

    //实施人状态
    const IMPLEMENT_STATUS_ONGOING  = 2;//进行中
    const IMPLEMENT_STATUS_DONE     = 3;//完成

    public static $implementStatus = [
        self::IMPLEMENT_STATUS_ONGOING  =>'ONGOING',
        self::IMPLEMENT_STATUS_DONE     =>'DONE',

    ];

    const IS_WORK_YES = 1;
    const IS_WORK_NO  = 0;

    static public $isWork = [
        self::IS_WORK_YES => '可实施',
        self::IS_WORK_NO  => '不可实施'
    ];

    /**
     * 是否置顶
     */
    const STICK_YES = 1;
    const STICK_NO  = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_ir';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => false,
                'updatedAtAttribute' =>'update_time',
                'value' => time()
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['advice_type', 'steps', 'update_time', 'stick', 'create_user','director_submit_time','confirm_user', 'assess_status', 'assess_time', 'assess_user', 'head_advice', 'head_submit_time', 'work_status', 'end_time', 'appeal','start_time', 'create_time'], 'integer'],
            [['advice_content', 'work_content','complete_confirm'], 'string'],
            [['subject', 'old_status', 'standard', 'director_advice', 'assess_advice', 'type', 'prize'], 'string', 'max' => 255],
            [['work_score','reward_score'],'double'],
        ];
    }

    /**
     * 查询文件路径
     * @return $this
     */
    public function getFilePath(){

        $image_name = ['old_images','work_images'];

        $file_ids = [];
        foreach ($image_name as $value){
            if(!empty($this->$value)){
                $this->$value = explode(',',$this->$value);
                $file_ids = array_merge($file_ids , $this->$value);
            }
        }

        //拿到所有图片
        $this->images_path = (new FileInfo())->getFilePathById($file_ids);

        return $this;
    }

    /**
     * @return array
     */
    public function getRelevanceUsers($type){
        $model = new IrUser();
        $userInfo = $model->getUserInfo($this->id,$type);
        if($userInfo){
            return $userInfo;
        }else{
            return [];
        }
    }

    /**
     * 查询创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'create_user']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'subject'               => '标题',
            'advice_type'           => '建议类型',
            'status'                => '状态',
            'old_status'            => '目前状况',
            'old_images'            => '状况图',
            'steps'                 => '下一步操作',
            'advice_content'        => '建议内容',
            'create_user'           => '建议人',
            'standard'              => '标准',
            'director_advice'       => '主管意见',
            'director_user'         => '主管',
            'director_submit_time'  => '主管提交时间',
            'assess_status'         => '评估状态',
            'assess_time'           => '评估时间',
            'assess_user'           => '评估人',
            'assess_advice'         => '评估人建议',
            'head_advice'           => '负责人建议',
            'work_status'           => '实施状态',
            'type'                  =>  '建议类型',
            'complete_confirm'      =>  '完成确认',
            'confirm_user'          =>  '确认人',
            'prize'                 =>  '奖项',
            'work_content'          => '实施内容',
            'work_images'           => '实施图片',
            'is_draft'              => '草稿',
            'end_time'              => '预计完工时间',
            'appeal'                => '申诉',
            'stick'                 => '置顶',
            'create_time'           => '创建时间',
            'update_time'           => '更新时间',
            'work_score'            => '实施分数',
            'reward_score'          => '完成分数',
        ];
    }
}
