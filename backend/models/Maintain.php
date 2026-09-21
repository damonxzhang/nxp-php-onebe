<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "nxp_maintain".
 *
 * @property int $id
 * @property string|null $number 编号
 * @property string|null $material_number 物料编号
 * @property int|null $f_id 设备id
 * @property int|null $operation_user 操作人员
 * @property int|null $happen_date 发生日期
 * @property int|null $shift 班别
 * @property int|null $area 区域
 * @property int|null $other_feedback_user 其他反馈用户
 * @property int|null $director_user 主管
 * @property int|null $things 问题事项
 * @property int|null $description 问题描述
 * @property int|null $other_things 其他事项
 * @property int|null $create_user 创建人
 * @property int|null $type 问题类型
 * @property int|null $other_type 问题类型
 * @property string|null $files 附件
 * @property int|null $rejects 次品数量
 * @property int|null $solve_status 解决状态
 * @property int|null $status 状态
 * @property int|null $access_status 审批状态
 * @property string|null $access_advice 审批意见
 * @property int|null $access_user 审批人
 * @property int|null $access_date 审批时间
 * @property int|null $feedback_user 反馈用户
 * @property int|null $boss boss直捡
 * @property int|null $create_time 创建时间
 * @property int|null $update_time 更新时间
 * @property int|null $delete_time 删除时间
 */
class Maintain extends \backend\models\Base
{


    const BOSS_YES = 1;
    const BOSS_NO  = 0;

    /**
     * 问题类型
     */
    const FACILITY_PROBLEM_TYPE = 1;
    const MATERIALS_PROBLEM_TYPE= 2;
    const USER_PROBLEM_TYPE     = 3;
    const FIVE_S_PROBLEM_TYPE   = 4;
    const OTHER_PROBLEM_TYPE    = 5;

    public static $problem = [
        self::FACILITY_PROBLEM_TYPE  => '设备问题',
        self::MATERIALS_PROBLEM_TYPE  => '物料问题',
        self::USER_PROBLEM_TYPE  => '人员问题',
        self::FIVE_S_PROBLEM_TYPE  => '5s问题',
        self::OTHER_PROBLEM_TYPE  => '其他问题',
    ];

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

    public static $accessStatusList= [
        self::SELECT_YES => '情况属实',
        self::SELECT_NO =>  '不属于异常问题'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_maintain';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' =>'create_time',
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
            [['f_id', 'source', 'operation_user', 'happen_date','area', 'shift', 'boss', 'director_user', 'things',  'other_feedback_user', 'create_user', 'type',  'status', 'rejects', 'solve_status', 'access_status', 'access_user', 'access_date', 'feedback_user', 'create_time', 'update_time', 'delete_time'], 'integer'],
            [['access_advice','description'], 'string'],
            [['number','material_number', 'process', 'files','other_things'], 'string', 'max' => 255],
        ];
    }

    public function getUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'create_user']);
    }
    public function getFacility(){
        return $this->hasOne(Facility::className(),['id' => 'f_id']);
    }

    /**
     * 查询前12小时创建的异常
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDayNoCompletion(){
        $end_time =  time();
        $start_time = strtotime( date('Y-m-d H:i',$end_time).' -12 hours');
        return self::find()
                ->andWhere(['>=','create_time',$start_time])
                ->andWhere(['<=','create_time',$end_time])
                ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => '编号',
            'f_id' => '设备编号',
            'material_number' => '问题物料批号',
            'operation_user' => '操作人员',
            'happen_date' => '发生日期',
            'shift' => '班别',
            'area' => '区域',
            'boss' => 'boss直捡',
            'director_user' => '主管带班',
            'process' => '工序',
            'things' => '异常类型',
            'description' => '问题描述',
            'other_things' => '其他问题',
            'create_user' => '创建人',
            'type' => '问题类型',
            'other_type' => '其他类型',
            'files' => '附件',
            'rejects' => '次品数量',
            'solve_status' => '解决状态',
            'access_status' => '审批状态',
            'access_advice' => '审批意见',
            'access_user' => '审批人',
            'access_date' => '审批时间',
            'feedback_user' => '反馈用户',
            'other_feedback_user' => '其他反馈用户',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'delete_time' => '删除时间',
            'source' => '数据来源',
        ];
    }
}
