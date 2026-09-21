<?php

namespace backend\models;

use Yii;

class InspectionPmRule extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_pm_rule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','user_id','facility_id','cycle','status','type'], 'integer']
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

