<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\TrainCourses;

/**
 * TrainCoursesSearch represents the model behind the search form of `backend\models\TrainCourses`.
 */
class TrainCoursesSearch extends TrainCourses
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'author', 'coach', 'type', 'video', 'create_time', 'update_time'], 'integer'],
            [['name', 'effect', 'comment', 'ppt'], 'safe'],
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
        $query = TrainCourses::find();

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
            'author' => $this->author,
            'coach' => $this->coach,
            'type' => $this->type,
            'video' => $this->video,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'effect', $this->effect])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'ppt', $this->ppt]);

        $query->orderBy('id desc');
        return $dataProvider;
    }

    public function searchByName($name){
        return $this->find()->select('id ,name as text')->filterWhere(['like','name',$name])->asArray()->limit(30)->all();
    }
}
