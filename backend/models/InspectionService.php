<?php

namespace backend\models;

use Yii;

class InspectionService extends Base
{

    const STATUS_DONE = 1;
    const STATUS_ONGOING  = 0;

    public static $statusList = [
        self::STATUS_DONE => '已完成',
        self::STATUS_ONGOING  => '进行中'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','inspection_id','facility_id','content_id','position_id','user_info_id','created_at','updated_at','status'], 'integer'],
            [['description','service_time','file_id'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'inspection_id'         => '报修表id',
            'content_id'            => '维修内容表id',
            'position_id'           => '维修位置表id',
            'user_info_id'          => '维修人id',
            'created_at'            => '创建时间',
            'updated_at'            => '修改时间',
            'status'                => '维修状态  1已完成 2 进行中',
            'description'           => '维修情况说明',
            'service_time'          => '维修时间',
            'file_id'               => '维修拍照id'
        ];
    }
}

