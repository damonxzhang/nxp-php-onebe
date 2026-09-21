<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class WashClothesCommon extends Base
{
    public $chinese_name;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_wash_clothes_common';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['pid','create_time','start_week_time','end_week_time','common'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => '',
            'pid'            => '',
            'create_time'    => '',
            'start_week_time'=> '',
            'end_week_time'  => '',
            'common'         => ''
        ];
    }

}
