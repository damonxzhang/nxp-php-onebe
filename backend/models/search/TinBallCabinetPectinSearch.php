<?php

namespace backend\models\search;

use backend\models\TinBallCabinetPectin;
use yii\data\ActiveDataProvider;

class TinBallCabinetPectinSearch extends TinBallCabinetPectin
{
    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = TinBallCabinetPectin::find();
        $query->orderBy(['create_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}