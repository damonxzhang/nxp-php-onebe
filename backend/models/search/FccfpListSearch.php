<?php

namespace backend\models\search;

use backend\models\FccfpList;
use yii\data\ActiveDataProvider;

class FccfpListSearch extends FccfpList
{
    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = FccfpList::find();
        $query->where(['!=', 'status', FccfpList::STATUS_PASS]);
        $query->andFilterWhere([
            'containername'  => trim($this->containername)
        ]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

   /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function history($param)
    {
        $this->setAttributes($param);
        $query = FccfpList::find();
        $query->andFilterWhere([
            'containername'  => trim($this->containername),
            'status'         => FccfpList::STATUS_PASS
        ]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}