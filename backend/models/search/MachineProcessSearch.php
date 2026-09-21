<?php

namespace backend\models\search;

use backend\models\MachineProcess;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MachineProcessSearch extends MachineProcess
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'createdt', 'updatedt'], 'safe'],
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
        $query = MachineProcess::find();
        $query->andFilterWhere(['like', 'name', trim($this->name)]);
        $query->andFilterWhere(['=', 'status', $this->status]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }
}
