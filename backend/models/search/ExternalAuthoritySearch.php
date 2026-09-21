<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ExternalAuthority;

/**
 * ExternalAuthoritySearch represents the model behind the search form of `backend\models\ExternalAuthority`.
 */
class ExternalAuthoritySearch extends ExternalAuthority
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'system_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['name'], 'safe'],
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
        $query = ExternalAuthority::find();

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
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
