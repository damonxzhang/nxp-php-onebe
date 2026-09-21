<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MaintainAreaHead;

/**
 * MaintainAreaHeadSearch represents the model behind the search form of `backend\models\MaintainAreaHead`.
 */
class MaintainAreaHeadSearch extends MaintainAreaHead
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'area', 'user_id', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MaintainAreaHead::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'area' => $this->area,
            'user_id' => $this->user_id,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
