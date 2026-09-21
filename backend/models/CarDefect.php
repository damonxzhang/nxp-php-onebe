<?php
namespace backend\models;

use yii\data\ActiveDataProvider;

class CarDefect extends Base
{
    const TYPE_SMALL  = 1;
    const TYPE_Big    = 2;
    public static $types = [
        self::TYPE_SMALL => '小材料',
        self::TYPE_Big   => '大材料'
    ];

    
    public static $statusWeight = [
        1=>1,
        2=>2,
        3=>3,
        4=>4,
        5=>5
    ];

    const STATUS_OFF    = 0;
    const STATUS_ON     = 1;

    public static $statusList = [
        self::STATUS_ON  => '开启',
        self::STATUS_OFF => '关闭',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_car_defect';
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();
        $query->where(['type' => $params['type']]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['defect', 'create_time', 'update_time'], 'string'],
            [['type', 'status', 'weight'], 'integer'],
        ];
    }

 

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'defect'      => '缺陷项目',
            'weight'      => '权重',
            'type'        => '类型',
            'status'      => '状态',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
        ];
    }

}
