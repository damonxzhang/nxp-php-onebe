<?php

namespace backend\models;

class CarOperatorJudge extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_car_operator_judge';
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
                    'car_id',
                    'role',
                    'result',
                    'user_id',
                    'reasons_dismissal',
                    'create_time'                    
                ], 'safe'
            ]
        ];
    }
    
    public function getUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'user_id']);

    }

    public function getUserName($id,$role)
    {
        if(empty($id)){
            return '';
        }
        $model = self::findOne(['car_id' => $id,'role'=>$role]);
        if($model){
            $userInfoModel = UserInfo::findOne(['id' => $model->user_id]); 
            return empty($userInfoModel) ? '' : $userInfoModel->chinese_name;
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [      
            'id'              => 'ID',		
            'car_id'          => 'car_id',
            'role'            => '确认人角色',
            'result'          => '改正行动',
            'user_id'         => '提交人id',
            'reasons_dismissal'=>'驳回原因',
            'create_time'     => '创建时间'
        ];
    }
}
