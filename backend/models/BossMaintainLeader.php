<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_boss_maintain_leader".
 *
 * @property int|null $user_id 用户id
 * @property int|null $area 区域
 */
class BossMaintainLeader extends \backend\models\Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_boss_maintain_leader';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'area'], 'required'],
            [['id','user_id', 'area'], 'integer'],
        ];
    }

    /**
     * 查询区域负责人的用户id
     * @param $id
     * @return array
     */
    public static function getAreaLeaderUserId($id){
        return self::find()->alias('l')
            ->select('user_id')
            ->leftJoin(['f' => Facility::tableName()],'f.area = l.area')
            ->where(['f.id' => $id])
            ->column();

    }
    /**
     * 根据区域查询用户
     * @param $area
     * @return bool|string
     */
    public static function getLeaderByArea($area){
        $names = self::find()->alias('b')->innerJoin(['u' => UserInfo::tableName()],'u.id = b.user_id')->select('u.chinese_name')->where(['b.area' => $area])->column();
        if(!empty($names)){
            return implode(',',$names);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '负责人名称'),
            'area' => Yii::t('app', '负责人区域'),
        ];
    }
}
