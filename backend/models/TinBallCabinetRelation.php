<?php

namespace backend\models;

use Yii;

class TinBallCabinetRelation extends Base
{
	//单位
    const UNIT_ITEM   = 'ITEM'; //条
    const UNIT_KPC    = 'KPC'; //KPC
    const UNIT_PC     = 'PC'; //PC
    const UNIT_BOTTLE = 'BOTTLE'; //瓶

    public static $unitArr = [
        self::UNIT_ITEM   => '条',
        // self::UNIT_KPC    => 'KPC',
        // self::UNIT_PC     => 'PC',
        self::UNIT_BOTTLE => '瓶',
    ];
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_tin_ball_cabinet_relation';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_id', 'bill_id','user_id'], 'integer'],
            [['create_time','update_time'], 'string'],
            [['apply_number','middle_number','product_number','abnormal_number','print_number'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'user_id'          => '用户表id',
            'model_id'         => '物料型号表id',
            'bill_id'          => '物料单表id',
            'create_time'      => '创建时间',
            'update_time'      => '更新时间',
            'apply_number'     => '申请数量',
            'print_number'     => '打印数量',
            'middle_number'    => '中间库库存',
            'product_number'   => '产线库存',
            'abnormal_number'  => '异常数量',
        ];
    }

}
