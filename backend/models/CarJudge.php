<?php

namespace backend\models;

use common\models\Utility;
use yii\helpers\ArrayHelper;

class CarJudge extends Base
{
    const ROLE_CLASS    = 1;
    const ROLE_ENGINEER = 2;

    public static $roleArr = [
        self::ROLE_CLASS    => '带班',
        self::ROLE_ENGINEER => '产品工程师',
    ];

    const RESULT_FAILE  = 1;
    const RESULT_PASS   = 2;

    
    public static $resultArr = [
        self::RESULT_PASS   => '放行',
        self::RESULT_FAILE  => '不放行',
    ];


    const RECORD_RESPONSIBILITY     = 1;
    const NEGLECTING_RESPONSIBILITY = 2;

    public static $responsibilityArr = [
        self::NEGLECTING_RESPONSIBILITY => '不记责任',
        self::RECORD_RESPONSIBILITY     => '记责任',
    ];

    const DPR_YES  = 1;
    const DPR_NO   = 2;

    public static $dprArr = [
        self::DPR_NO   => '不记责任',
        self::DPR_YES  => '记责任',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_car_judge';
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
                    'method',
                    'result',
                    'responsibility',
                    'dpr',
                    'user_id',
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
            'role'            => '提交工程师',
            'method'          => '处理方法',
            'result'          => '判定结果',
            'responsibility'  => '是否记操作员责任',
            'dpr'             => 'DPR权重',
            'user_id'         => '提交人id',
            'create_time'     => '创建时间'
        ];
    }

    /**
     * 添加
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->car_id         = $data['car_id'];
        $this->role           = $data['role'];
        $this->method         = $data['method'];
        $this->result         = $data['result'];
        $this->responsibility = $data['responsibility'];
        $this->dpr            = $data['dpr'];
        $this->user_id        = $data['user_id'];
        $this->create_time    = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, $this->attributes['id']);
    }

}
