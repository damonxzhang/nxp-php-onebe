<?php

namespace backend\models\search;

use backend\models\FccfpExpireMethod;
use yii\data\ActiveDataProvider;

class FccfpExpireMethodSearch extends FccfpExpireMethod
{
    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = FccfpExpireMethod::find();
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}