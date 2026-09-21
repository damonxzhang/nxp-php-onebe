<?php
namespace backend\models\search;

use backend\models\Flow;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class FlowSearch extends Flow
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * 查询流程
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = Flow::find();
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}