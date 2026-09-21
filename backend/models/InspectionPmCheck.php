<?php

namespace backend\models;

use Yii;

class InspectionPmCheck extends Base
{

    const TYPE_CHECK  = 1;
    const TYPE_REVIEW = 2;

    public static $typeList = [
        self::TYPE_CHECK  => '检查',
        self::TYPE_REVIEW => '复查'
    ];


    const RESULT_OK  = 1;
    const RESULT_ABNORMAL = 2;

    public static $resultList = [
        self::RESULT_OK  => '正常',
        self::RESULT_ABNORMAL => '异常'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_pm_check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pm_id', 'group_item_id', 'group_item_result', 'review_type', 'user_info_id', 'type','created_at'], 'integer'],
            [['check_time'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => '',
            'pm_id'         => '检查表id',
            'group_item_id' => '检查项id',
            'group_item_result' => '检查项结果',
            'check_time'    => '检查时间',
            'type'          => '分类',
            'review_type'   => '复查确认',
            'created_at'   => '创建时间',
            'user_info_id'  => '检查复查人id'
        ];
    }
}

