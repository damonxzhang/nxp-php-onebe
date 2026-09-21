<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_maintain_area_head".
 *
 * @property int $id
 * @property int|null $area
 * @property int|null $user_id
 * @property int|null $status
 */
class MaintainAreaHead extends \backend\models\Base
{

    static public $statusList = [
        self::SELECT_YES => '开启',
        self::SELECT_NO  => '关闭'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_maintain_area_head';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area', 'user_id', 'status'], 'integer'],
        ];
    }

    /**
     * 修改状态
     * @return bool
     */
    public function changeStatus(){
        if($this->status == self::SELECT_YES){
            $this->status = self::SELECT_NO;
        }else{
            $this->status = self::SELECT_YES;
        }
        return $this->save();
    }

    /**
     * 根据区域查询负责人
     * @param $area
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getUserEmailByArea($area){
        return self::find()->alias('mau')->select('u.chinese_name as name,u.email')
                ->innerJoin(['u' => UserInfo::tableName()],'mau.user_id = u.id')
                ->where(['mau.area' => $area,'mau.status' => self::SELECT_YES])
                ->asArray()
                ->all();
    }
 
    public static function getUserEmail($area){
        return self::find()->alias('mau')->select('u.email')
            ->innerJoin(['u' => UserInfo::tableName()],'mau.user_id = u.id')
            ->where(['mau.area' => $area,'mau.status' => self::SELECT_YES])
            ->column();
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'area' => Yii::t('app', '区域'),
            'user_id' => Yii::t('app', '用户'),
            'status' => Yii::t('app', '状态'),
        ];
    }
}
