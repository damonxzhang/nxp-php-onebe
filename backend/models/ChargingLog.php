<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;

/**
 * This is the model class for table "nxp_charging_log".
 *
 * @property string $id
 * @property string $wbi_code 识别码
 * @property int $user_id 操作人
 * @property int $tank_number 充电柜编号
 * @property int $type 类型（登录、借设备、换设备、跟换电池、提交异常）
 * @property int $warehouse_number 充电仓编号
 * @property string $equipment_number
 * @property string $common 记录信息
 * @property int $created_at 创建时间
 */
class ChargingLog extends Base
{
    //登录、借设备、还设备、跟换电池、提交异常登录
    const TYPE_LOGIN        = 1;
    const TYPE_TAKE         = 2;
    const TYPE_ALSO         = 3;
    const TYPE_REPLACE      = 4;
    const TYPE_ABNORMAL     = 5;
    const TYPE_HANDOVER     = 6;

    static public $typeArr = [
        self::TYPE_LOGIN        => '登录',
        self::TYPE_TAKE         => '借设备',
        self::TYPE_ALSO         => '还设备',
        self::TYPE_REPLACE      => '更换电池',
        self::TYPE_ABNORMAL     => '提交异常',
        self::TYPE_HANDOVER     => '交接',
    ];

    public  function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',// 自己根据数据库字段修改
                'updatedAtAttribute' => 'updated_at',// 自己根据数据库字段修改
                'value'   => time(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_charging_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'tank_number', 'type','wbi_code'], 'required','on' => 'newSave'],
            [['user_id','type', 'equipment_stype', 'created_at','updated_at'], 'integer'],
            [['wbi_code', 'tank_number',  'warehouse_number','equipment_number'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 100],
            ['type','validateType']
        ];
    }

    /**
     * 验证日志类型
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function validateType($attribute,$params){
        if(!in_array($this->$attribute,array_keys(self::$typeArr))){

            $this->addError($attribute,'未知的日志类型');
            return false;
        }

    }

    /**
     * 保存日志
     * @param $tank_number 充电柜编号
     * @param $warehouse_number充电仓编号
     * @param $equipment_number设备编号
     * @param $wbi_code 用户编号
     * @param $type
     * @param null $msg
     * @return bool
     */
    public static function saveNewBlog($tank_number,$warehouse_number,$equipment_number,$wbi_code,$type,$msg = null){
        $model = new Self();
        // if(!empty($wbi_code)){
        //     $this->wbi_code = $wbi_code;
        // }
        $user_id1 = '';
        if(!empty($wbi_code)){
            $userModel = UserInfo::findOne(['badge_code' => $wbi_code]);
            $user_id1 = $userModel->id;
        }
        $model->setAttributes([
            'wbi_code' => $wbi_code,
            'user_id' => $user_id1,
            'type' =>  $type,
            'tank_number' => $tank_number,
            'warehouse_number' => $warehouse_number,
            'equipment_number' => $equipment_number,
            'comment' => $msg,
        ]);
        // $model->setUserId($wbi_code);
        // $model->setUserId();
        if($model->save()){
            return true;
        }else{
            throw new Exception($model->errorToString());
        }
    }


    /**
     * 设置用户id
     */
    public function setUserId($wbi_code = null){
        if(!empty($wbi_code)){
            $this->wbi_code = $wbi_code;
        }
        if(!empty($this->wbi_code)){
            $userModel = new UserInfo();
            $this->user_id = $userModel->getUserByWbiCode($this->wbi_code)->id;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wbi_code' => '用户识别码',
            'user_id' => '用户id',
            'tank_number' => '充电柜编号',
            'type' => '类型',
            'warehouse_number' => '充电仓编号',
            'equipment_number' => '设备编号',
            'common' => '备注信息',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
