<?php
namespace  backend\models\search;

use backend\models\UserStation;
use yii\data\ActiveDataProvider;

class UserStationSearch extends UserStation {

    public $station;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['station'], 'safe'],
        ];
    }

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = UserStation::find();
        $query->andFilterWhere(['like', 'station', trim($this->station)]);
        $query->orderBy(['id' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}