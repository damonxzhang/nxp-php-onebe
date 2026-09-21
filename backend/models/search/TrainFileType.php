<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\TrainFileType as TrainFileTypeModel;

/**
 * TrainFileType represents the model behind the search form of `backend\models\TrainFileType`.
 */
class TrainFileType extends TrainFileTypeModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status', 'create_time', 'update_time'], 'integer'],
            [['name'], 'safe'],
        ];
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
        $query = TrainFileTypeModel::find()->where(['create_user' => \Yii::$app->user->identity->user_id]);

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
            'type' => $this->type,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->orderBy('id desc');
        return $dataProvider;
    }
}
