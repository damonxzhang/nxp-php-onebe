<?php

namespace backend\models;

class CarOperator extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_car_operator';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'trace_no',
                    'car_id',
                    'reasons_defects',
                    'corrective_action',
                    'user_id',
                    'date',
                    'second_files_mapping',
                    'create_time'                    
                ], 'safe'
            ]
        ];
    }

    public function getUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'user_id']);

    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [      
            'id'              => 'ID',		
            'car_id'          => 'car_id',
            'trace_no'        => 'TRACE NO',
            'reasons_defects' => '缺陷原因',
            'corrective_action' => '改正行动',
            'user_id'         => '提交人id',
            'date'            => '日期',
            'second_files_mapping'   => '图片',
            'create_time'     => '创建时间'
        ];
    }
}
