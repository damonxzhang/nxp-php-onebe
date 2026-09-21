<?php

namespace backend\models;
use yii\data\ActiveDataProvider;

class CarUser extends Base
{
    public $chinese_name;
    public $position;
    public $shift;
    public $area;
    public $role_id;
   
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_car_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'user_id',
                    'create_time'                    
                ], 'safe'
            ]
        ];
    }
    
    public function getUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'user_id']);

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
        $query->alias('cu');
        $query->select([
            'cu.id',
            'u.chinese_name',
            'u.position',
            'u.shift',
            'u.area',
            'u.role_id',
            'cu.create_time',
        ]);
        $query->leftJoin(UserInfo::tableName() . ' u', 'cu.user_id = u.id');
        $query->andFilterWhere(['like', 'u.chinese_name', trim($this->chinese_name)]);
        $dataProvider = new ActiveDataProvider([
            'query'     => $query,
            'pagination' => [
                'pageSize' => 20,
             ],
        ]);
        return $dataProvider;
    }
}
