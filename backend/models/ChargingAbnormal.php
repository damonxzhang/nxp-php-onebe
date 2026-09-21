<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_charging_abnormal".
 *
 * @property string $id
 * @property int $operation_user_id 操作人
 * @property int $deal_with_user_id 处理人id
 * @property int $admin_user_id 管理员id
 * @property int $type 类型（手动、自动）
 * @property int $status 状态（未处理、已处理、完成）
 * @property string $abnormal_msg 异常备注
 * @property string $deal_with_msg  处理备注
 * @property string $complete_msg   完成备注
 * @property int $warehouse_id   充电仓
 * @property int $warehouse_number   充电仓编号
 * @property int $tank_id   充电柜
 * @property int $wbi_code   身份识别号
 * @property string $create_time 创建时间
 * @property string $deal_with_time 处理时间
 * @property string $complete_time 完成时间
 */
class ChargingAbnormal extends Base
{
    const AUTO_TYPE   = 1;//硬件提交的异常
    const MANUAL_TYPE = 2;//手动提交的异常

    static public $typeArr = [
        self::AUTO_TYPE   => '自动',
        self::MANUAL_TYPE => '手动'
    ];

    const DEAL_STATUS = 1;
    const NOT_STATUS  = 2;
    const OK_STATUS   = 3;

    static public $statusArr = [
        self::DEAL_STATUS => '已处理',
        self::NOT_STATUS  => '未处理',
        self::OK_STATUS   => '已完成'
    ];

    static public $handleStatusArr = [
        self::DEAL_STATUS => '处理',
        self::OK_STATUS   => '完成'
    ];

    const AUTO_SCEN   = 'AUTO';//自动时验证规则
    const MANUAL_SCEN = 'MANUAL';//页面提交时验证规则

    public $warehouse_number; //充电仓编号
    public $wbi_code; //用户识别号
    public $comment;//备注

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_charging_abnormal';
    }

    public function scenarios(){
        return array_merge([
            self::AUTO_SCEN=> ['wbi_code', 'tank_id', 'warehouse_number','abnormal_msg','type','status'] ,
            self::MANUAL_SCEN => ['operation_user_id', 'warehouse_id', 'complete_time', 'deal_with_time', 'complete_msg', 'deal_with_msg', 'tank_id','deal_with_user_id', 'admin_user_id', 'type', 'status', 'created_at'],
        ],parent::scenarios());

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'wbi_code', 'tank_id', 'warehouse_number','abnormal_msg','type','status'], 'required' ],
            [['operation_user_id', 'warehouse_id','tank_id'], 'required'],
            [['operation_user_id', 'deal_with_user_id', 'admin_user_id', 'type', 'status', 'created_at'], 'integer'],
            [['create_time', 'deal_with_time', 'complete_time'], 'safe'],
            [['abnormal_msg','wbi_code'], 'string', 'max' => 100],
            [['deal_with_msg', 'complete_msg'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'operation_user_id' => '操作人',
            'deal_with_user_id' => '处理人',
            'admin_user_id' => '管理员',
            'warehouse_number' => '充电仓编号',
            'tank_id' => '充电柜id',
            'warehouse_id' => '充电仓id',
            'wbi_code' => '身份识别码',
            'type' => '类型',
            'status' => '状态',
            'abnormal_msg' => '异常备注',
            'deal_with_msg' => '处理备注',
            'complete_msg' => '完成备注',
            'create_time' => '创建时间',
            'deal_with_time' => '处理时间',
            'complete_time' => '完成时间',
        ];
    }

    /**
     * 查询到所有未完成的异常信息
     * @param $tankId
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    public function getAbnormalInfo($tankId){
        $model = self::find()->alias('a')
            ->select('a.*,w.number as boxId')
            ->innerJoin(['w' => ChargingWarehouse::tableName()],'a.warehouse_id = w.id')
            ->where(['a.status' => self::DEAL_STATUS])
            ->orWhere(['a.status' => self::NOT_STATUS])
            ->andWhere(['a.tank_id' =>$tankId])
            ->asArray()
            ->orderBy('create_time desc')
            ->all();
        if($model){
            return $model;
        }else{
            return false;
        }
    }

    public function getWarehouse(){
        return $this->hasOne(ChargingWarehouse::className(),['id' => 'warehouse_id']);

    }

}
