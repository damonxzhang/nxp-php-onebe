<?php

namespace backend\models;

use Yii;

class FacilityUser extends Base
{

    const STATUS_OFF = 0;
    const STATUS_ON  = 1;

    public static $statusList = [
        self::STATUS_ON     => '启用',
        self::STATUS_OFF    => '禁用'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_facility_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'facility_id','user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'facility_id'      => '软件名称',
            'user_id'   => '软件版本',
        ];
    }
}


