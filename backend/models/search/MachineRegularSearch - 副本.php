<?php

namespace backend\models\search;

use backend\models\MachineRegular;
use yii\data\ActiveDataProvider;

class MachineRegularSearch extends MachineRegular
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area', 'process','status','class'], 'safe'],
        ];
    }


    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->setAttributes($params);
        $query = MachineRegular::find();
        $query->andFilterWhere(['process' => $this->process]);
        $query->andFilterWhere(['area' => $this->area]);
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['class' => $this->class]);
        $query->orderBy(['process' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }
}


