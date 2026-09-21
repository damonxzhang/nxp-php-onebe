<?php

namespace backend\models;

use Yii;

class InspectionPmCheckPicture extends Base
{

    const TYPE_CHECK  = 1;
    const TYPE_REVIEW = 2;

    public static $typeList = [
        self::TYPE_CHECK  => '检查',
        self::TYPE_REVIEW => '复查'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_pm_check_picture';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pm_id', 'pm_file_id', 'type', 'pm_check_id'], 'integer']
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
            'pm_file_id'    => '检查图片id',
            'type'          => '分类',
            'pm_check_id'  => '检查表id'
        ];
    }
}


