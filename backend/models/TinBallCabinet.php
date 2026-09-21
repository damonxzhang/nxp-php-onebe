<?php

namespace backend\models;

use Yii;

class TinBallCabinet extends Base
{

    //状态
    const STATUS_PENDING    = 1; //待处理
    const STATUS_SUCCESS    = 2; //已完成


    public static $statusArr = [
        self::STATUS_PENDING    => '待处理',
        self::STATUS_SUCCESS    => '已完成',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_tin_ball_cabinet';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'user_id'], 'integer'],
            [[
                'document_no',
                'create_time',
                'update_time',
            ], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'user_id'      => '制单人id',
            'document_no'  => '单据号',
            'abnormal'     => '异常信息',
            'create_time'  => '创建时间',
            'update_time'  => '更新时间',
        ];
    }
 
    /**
     * 人员信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserinfo()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'user_id']);
    }
    
    /**
     * 根据用户ID获取用户名称
     * @param $id
     * @return string
     */
    public function getDocumentNo($id)
    {
        if(empty($id)){
            return '';
        }
        $query = self::findOne(['id' => $id]);
        return empty($query) ? '' : $query->document_no;
    }
}
