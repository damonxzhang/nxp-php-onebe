<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_handover_users".
 *
 * @property int $id
 * @property int $user_id 用户
 * @property int $create_time
 * @property int $update_time
 */
class HandoverUsers extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_handover_users';
    }

    public function behaviors()
    {
        return [
            $this->getTimestampBehaviorArray()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['user_id','unique','message' => '用户已存在'],
            [['user_id', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * 是否可以交班
     * @param $user_id
     * @return bool
     */
    public static function CanTransfer($user_id){
        $exists = self::find()->where(['user_id' => $user_id])->exists();

        if($exists){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 查询已存在的用户id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getExistUserId($ids){
        return self::find()->select('user_id')->where(['user_id' => $ids])->column();
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'create_time' => 'Create Date',
            'update_time' => 'Update Date',
        ];
    }
}
