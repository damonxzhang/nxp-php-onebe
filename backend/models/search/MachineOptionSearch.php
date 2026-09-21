<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MachineOption;

class MachineOptionSearch extends MachineOption
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status','model_id'], 'safe'],
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
        $query = MachineOption::find();
        $query->where(['model_id' => $params['model_id']]);
        $query->andFilterWhere(['status' => $this->status]);
        $query->orderBy(['position' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }
}
