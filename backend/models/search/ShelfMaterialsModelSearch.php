<?php

namespace backend\models\search;

use backend\models\ShelfMaterialsModel;
use yii\data\ActiveDataProvider;

class ShelfMaterialsModelSearch extends ShelfMaterialsModel
{
    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = ShelfMaterialsModel::find();
        $query->andFilterWhere([
            'part_no'  => trim($this->part_no),
            'type'     => $this->type
        ]);
        $query->orderBy(['create_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}