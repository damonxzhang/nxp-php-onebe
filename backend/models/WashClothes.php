<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class WashClothes extends Base
{

    public $shift;
    public $area;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_wash_clothes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['user_name','rfid','pid','create_time','invest_time','receive_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => '',
            'user_name'   => '',
            'rfid'        => '',
            'pid'         => '',
            'create_time' => '',
            'invest_time' => '',
            'receive_time'=> '',
        ];
    }


    // public function search($param){
    //     $this->setAttributes($param);
    //     $query = self::find();
    //     $query->alias('wc');
    //     $query->select(['wc.*','u.shift','u.area']);
    //     $query->leftJoin(UserInfo::tableName() . ' u', 'u.pid = wc.pid');
    //     if (isset($param['user_name']))
    //         $query->andFilterWhere(['like','wc.user_name',$param['user_name']]);
    //     if (isset($param['pid']))
    //         $query->andFilterWhere(['like','wc.pid',$param['pid']]);
    //     $query->orderBy(['wc.id' => SORT_DESC]);
    //     $dataProvider = new ActiveDataProvider(['query' => $query]);
    //     return $dataProvider;
    // }

    public function search($param){
        $this->setAttributes($param);
        $query = self::find();
        $query->alias('wc');
        $query->select(['wc.*','u.shift','u.area']);
        $query->leftJoin(UserInfo::tableName() . ' u', 'u.pid = wc.pid');
        $query->where(['>', 'wc.pid', 0]);
        if (isset($param['user_name']))
            $query->andFilterWhere(['like','wc.user_name',$param['user_name']]);
        if (isset($param['pid']))
            $query->andFilterWhere(['like','wc.pid',$param['pid']]);
        $query->orderBy(['wc.id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}
