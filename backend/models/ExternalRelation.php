<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_external_relation".
 *
 * @property int $id
 * @property int $system_id 系统id
 * @property int $authority_id 权限id
 * @property int $user_id 用户id
 * @property int $status 状态
 * @property int $create_time
 * @property int $update_time
 */
class ExternalRelation extends \yii\db\ActiveRecord
{
    const STATUS_YES = 1;
    const STATUS_NO  = 2;
    static public $statusArr = [
        self::STATUS_YES => '开启',
        self::STATUS_NO  => '关闭'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_external_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['system_id', 'authority_id','status', 'user_id', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'system_id' => '系统',
            'authority_id' => '权限名称',
            'user_id' => '用户',
            'status' => '状态',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    public function getUserIdByAuthority($authority_id){
        //return $this->find()->select('user_id')->where(['authority_id' => $authority_id])->column();
        $data =  self::find()->select('user_id')->where(['authority_id' => $authority_id])->column();
        if(!empty($data)){
            return $data;
        }else{
            return '';
        }
    }


    public function getStatus($authority_id){
        $model = $this->find()->select('status')->where(['authority_id' => $authority_id])->orderBy('id asc')->limit(1)->one();
        if(!empty($model)){
            return $model;
        }else{
            return $this;
        }
    }
}
