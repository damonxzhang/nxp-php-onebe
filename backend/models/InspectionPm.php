<?php

namespace backend\models;

use Yii;

class InspectionPm extends Base
{

    const STATUS_NO_CHECK = 0;
    const STATUS_DONE_CHECK = 1;
    const STATUS_NO_REVIEW  = 2;

    public static $statusList = [
        self::STATUS_NO_CHECK => '未检查',
        self::STATUS_DONE_CHECK  => '已检查',
        self::STATUS_NO_REVIEW  => '待复查'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_pm';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','facility_id','user_info_id','status','created_at','updated_at','review_user_info_id','deal_user_id'], 'integer'],
            [['check_time'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                =>'',
            'facility_id'       =>'设备表id',
            'user_info_id'      =>'检查人id',
            'check_time'        =>'检查时间 (排班检查时间)',
            'status'            =>'检查状态  0 未检查 1已检查 2待复查',
            'created_at'        =>'创建时间',
            'updated_at'        =>'修改时间'
        ];
    }
}

