<?php

namespace backend\models;

use Yii;

class InspectionPmRuleSpecial extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_pm_rule_special';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','day'], 'integer'],
            [['date'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                        =>'',
            'user_id'                   =>'',
            'facility_id'               =>'',
            'status'               =>'',
            'type'               =>'',
            'cycle'                     =>''
        ];
    }
}

