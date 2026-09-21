<?php

namespace backend\models\search;

use backend\models\ShelfMaterialsBillRelation;
use yii\data\ActiveDataProvider;

class ShelfMaterialsBillRelationSearch extends ShelfMaterialsBillRelation
{
    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = ShelfMaterialsBill::find();
        $query->andFilterWhere([
            'model_id'  =>  $this->model_id
        ]);
        $query->orderBy(['create_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}