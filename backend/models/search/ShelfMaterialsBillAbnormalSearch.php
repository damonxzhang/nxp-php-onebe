<?php

namespace backend\models\search;

use backend\models\ShelfMaterialsBillAbnormal;
use yii\data\ActiveDataProvider;

class ShelfMaterialsBillAbnormalSearch extends ShelfMaterialsBillAbnormal
{
    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = ShelfMaterialsBillAbnormal::find();
        $query->andFilterWhere([
            'bill_id'  => $param['bill_id'],
          //  'status'   => 0
        ]);
        $query->orderBy(['create_time' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}