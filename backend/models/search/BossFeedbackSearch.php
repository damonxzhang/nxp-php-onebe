<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BossFeedback;

/**
 * BossFeedback represents the model behind the search form of `backend\models\BossFeedback`.
 */
class BossFeedbackSearch extends BossFeedback
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'm_id', 'feedback_user', 'type', 'action', 'solve_status', 'predict_date', 'complete_date', 'sendMail', 'create_time', 'update_time'], 'integer'],
            [['other_action'], 'safe'],
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
    public function search($params,$m_id)
    {
        $query = BossFeedback::find();

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
            'm_id' => $m_id,
        ]);

        $query->andFilterWhere(['like', 'other_action', $this->other_action]);

        return $dataProvider;
    }
}
