<?php
namespace backend\models;

use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "nxp_car_high_level".
 *
 * @property int $id
 * @property int|null $type 类型
 * @property int|null $status 状态
 * @property int|null $user_id 用户
 * @property int|null $create_time 创建时间
 * @property int|null $update_time 创建时间
 */
class CarHighLevel extends Base
{
    const TYPE_SMALL  = 1;
    const TYPE_Big    = 2;
    public static $types = [
        self::TYPE_SMALL => '小材料',
        self::TYPE_Big   => '大材料'
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
        return 'nxp_car_high_level';
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
            [['type', 'status', 'user_id', 'create_time', 'update_time'], 'safe'],
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
            'id'          => 'ID',
            'type'        => '类型',
            'status'      => '状态',
            'user_id'     => '负责人',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
        ];
    }

}
