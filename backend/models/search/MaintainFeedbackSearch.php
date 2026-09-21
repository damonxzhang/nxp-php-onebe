<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MaintainFeedback;

/**
 * MaintainFeedbackSearch represents the model behind the search form of `backend\models\MaintainFeedback`.
 */
class MaintainFeedbackSearch extends MaintainFeedback
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'm_id', 'type', 'feedback_type', 'feedback_user', 'other_feedback', 'action', 'other_action', 'solve_status', 'predict_date', 'complete_date', 'create_time', 'update_time'], 'integer'],
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
    public function search($id)
    {
        $query = MaintainFeedback::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // grid filtering conditions
        $query->Where([ 'm_id' => $id ]);

        return $dataProvider;
    }
}
