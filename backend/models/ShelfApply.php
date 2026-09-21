<?php

namespace backend\models;


/**
 * This is the model class for table "nxp_shelf_apply".
 *
 * @property string $id
 * @property string $part_no
 * @property int $user_id
 * @property int $status
 * @property int $count
 * @property int $amount
 * @property string $machine_id
 * @property string $application_time
 */
class ShelfApply extends Base
{
    //领取状态
    const STATUS_RECEIVE   = 1; //已领取
    const STATUS_UNRECEIVE = 2; //未领取
    const STATUS_CANCEL    = 3; //取消

    public static $statusArr = [
        self::STATUS_RECEIVE   => '已领取',
        self::STATUS_UNRECEIVE => '未领取',
        self::STATUS_CANCEL    => '取消',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf_apply';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'amount'], 'integer'],
            [['part_no', 'machine_id', 'application_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'part_no'          => '物料批次',
            'user_id'          => '申请人id',
            'amount'           => '申请数量',
            'status'           => '申请领取状态',
            'machine_id'       => '设备编号',
            'application_time' => '申请时间',
        ];
    }

    /**
     * 人员信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserinfo()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'user_id']);
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->part_no          = $data['part_no'];
        $this->user_id          = $data['user_id'];
        $this->status           = self::STATUS_UNRECEIVE;
        $this->machine_id       = $data['machine_id'];
        $this->amount           = $data['amount'];
        $this->application_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return $this->attributes['id'];
    }

    /**
     * 更新申请状态
     * @return void
     */
    public function applyAccomplish(){
        $count = ShelfMaterials::find()->where(['apply_id' => $this->id,'status' => ShelfMaterials::FETTLE_DOWN])->count();
        if ($count == $this->amount){
            $this->status = self::STATUS_RECEIVE;
            $this->save();
        }
    }

}
