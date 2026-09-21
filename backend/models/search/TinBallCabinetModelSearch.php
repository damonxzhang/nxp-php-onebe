<?php

namespace backend\models\search;

use backend\models\TinBallCabinetModel;
use yii\data\ActiveDataProvider;

class TinBallCabinetModelSearch extends TinBallCabinetModel
{
    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = TinBallCabinetModel::find();
        $query->andFilterWhere([
            'part_no'  => trim($this->part_no)
        ]);
        $query->orderBy(['create_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}