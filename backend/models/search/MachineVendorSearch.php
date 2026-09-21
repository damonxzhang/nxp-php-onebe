<?php

namespace backend\models\search;

use backend\models\MachineVendor;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class MachineVendorSearch extends MachineVendor
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->setAttributes($params);
        $query = new Query();
        $query->from(MachineVendor::tableName());
        $query->andFilterWhere(['like', 'name', trim($this->name)]);
        $query->andFilterWhere(['=', 'status', trim($this->status)]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }
}

