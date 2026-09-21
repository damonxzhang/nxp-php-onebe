<?php

namespace backend\models\search;

use backend\models\ShelfMaterialsBill;
use yii\data\ActiveDataProvider;

class ShelfMaterialsBillSearch extends ShelfMaterialsBill
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
        if(isset($param['id'])){
            $query->where(['id' => $param['id']]);
        }else{
            $query->andFilterWhere([
                'document_no'  => trim($this->document_no),
                'user_id'      => $this->user_id,
                'status'       => $this->status
            ]);
        }
        $query->orderBy(['create_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}