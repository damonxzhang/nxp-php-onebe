<?php

namespace backend\models;

use Yii;

class Inspection extends Base
{

    const STATUS_PENDING = 0;
    const STATUS_NORMAL  = 1;
    const STATUS_REPAIR  = 2;
    const STATUS_CONFIRMED  = 3;

    public static $statusList = [
        self::STATUS_PENDING => '待处理',
        self::STATUS_NORMAL  => '正常',
        self::STATUS_REPAIR  => '维修中',
        self::STATUS_CONFIRMED  => '待确认'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['facility_id', 'status', 'created_at', 'updated_at', 'operate', 'question_type', 'user_info_id'], 'integer'],
            [['relevant_people', 'repair_time', 'explanation', 'repair_file_id', 'part_no'], 'string']
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'facility_id'     => '设备id',
            'status'          => '维修状态',
            'relevant_people' => '设备相关人',
            'repair_time'     => '报修时间',
            'explanation'     => '情况说明',
            'repair_file_id'  => '报修拍照说明图片id',
            'created_at'      => '创建时间',
            'updated_at'      => '修改时间',
            'operate'         => '后续操作',
            'question_type'   => '问题类型',
            'user_info_id'    => '提建议人id',
            'part_no'         => '关联批号',
        ];
    }


    //设备
    public function getFacility()
    {
        return $this->hasOne(Facility::tableName(), ['id' => 'facility_id']);
    }

    //人员
    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::tableName(), ['id' => 'user_info_id']);
    }

}


