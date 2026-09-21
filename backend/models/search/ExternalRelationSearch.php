<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ExternalRelation;

/**
 * ExternalRelationSearch represents the model behind the search form of `backend\models\ExternalRelation`.
 */
class ExternalRelationSearch extends ExternalRelation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'system_id', 'authority_id', 'user_id', 'create_time', 'update_time'], 'integer'],
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
    public function search($params,$pid)
    {
        $query = ExternalRelation::find()->where(['system_id' => $pid]);

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

        // grids filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'system_id' => $this->system_id,
            'authority_id' => $this->authority_id,
            'user_id' => $this->user_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        return $dataProvider;
    }
}
