<?php

namespace backend\models;



/**
 * This is the model class for table "nxp_shelf".
 *
 * @property string $id
 * @property string $shelf_number
 * @property int $status
 * @property int $row
 * @property int $mark
 * @property int $number
 * @property int $floor
 * @property int $car_number
 * @property int $column
 * @property string $create_time 创建时间
 */
class ShelfMaterialAuthority extends Base
{
    //货架当前状态
    const STATUS_TRIAL   = 1; //使用中
    const STATUS_FORBID  = 2; //2禁用
    public static $statusArr = [
        self::STATUS_TRIAL   => '可领取',
        self::STATUS_FORBID  => '醒料中',
    ];

    const AUTHORITY_ACTION = 1;

    public $authorityList = [
        self::AUTHORITY_ACTION => '货架操作权限'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf_material_authority';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'user_id'      => '操作员ID',
            'authority'    => '权限',
            'status'       => '状态',
        ];
    }

    public function getUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'user_id']);
    }


    public static function checkAuthority($user_id){
        if (self::find()->where(['user_id' => $user_id])->exists()){
            return true;
        }
        return false;
    }
}
