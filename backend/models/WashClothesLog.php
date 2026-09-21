<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class WashClothesLog extends Base
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_wash_clothes_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['card','rfid','create_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'    =>'',
            'rfid'  =>'',
            'create_time' => '',
            'card'   =>''
        ];
    }

    // public function search($param){
    //     $this->setAttributes($param);
    //     $query = self::find();
    //     $query->alias('wcl');
    //     $query->select(['wcl.*','u.shift','u.area','wcu.user_name','wcu.pid']);
    //     $query->leftJoin(WashClothesUser::tableName() . ' wcu', 'wcu.rfid = wcl.rfid');
    //     $query->leftJoin(UserInfo::tableName() . ' u', 'u.pid = wcu.pid');
    //     if (isset($param['user_name']))
    //         $query->andFilterWhere(['like','wcu.user_name',$param['user_name']]);
    //     if (isset($param['pid']))
    //         $query->andFilterWhere(['like','wcu.pid',$param['pid']]);
    //     $query->orderBy(['wcl.id' => SORT_DESC]);
    //     $dataProvider = new ActiveDataProvider(['query' => $query]);
    //     return $dataProvider;
    // }


}
