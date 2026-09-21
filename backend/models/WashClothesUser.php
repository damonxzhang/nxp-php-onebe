<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class WashClothesUser extends Base
{

    // 物资类型
    const TYPE_HAT   = 1;
    const TYPE_CLOTH = 2;
    const TYPE_PANTS = 3;

    static $typeArr = [
        self::TYPE_HAT   => '帽子',
        self::TYPE_CLOTH => '静电服',
        self::TYPE_PANTS => '裤子',
    ];
	
    const STATUS_YES = 1;
    const STATUS_NO = 2;

    static $statusArr = [
        self::STATUS_YES => '启用',
        self::STATUS_NO => '禁用'
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_wash_clothes_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','max_number','type'], 'integer'],
            [['user_name','rfid','create_time','pid','str','year'], 'string'],
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
            'create_time' => '',
            'pid'         => '',
            'str'         => '',
            'year'        => '',
        ];
    }

    public function search($param){
        $this->setAttributes($param);
        $query = self::find();
        if (isset($param['user_name']))
            $query->andFilterWhere(['like','user_name',$param['user_name']]);
        if (isset($param['pid']))
            $query->andFilterWhere(['like','pid',$param['pid']]);
        $query->orderBy(['id' => SORT_DESC,'year'=> SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}
