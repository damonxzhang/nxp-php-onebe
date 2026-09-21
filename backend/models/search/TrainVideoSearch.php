<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\TrainVideo;

/**
 * TrainVideoSearch represents the model behind the search form of `backend\models\TrainVideo`.
 */
class TrainVideoSearch extends TrainVideo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
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
        $query = TrainVideo::find()->where([ 'delete_time'=> NULL ]);

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
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->orderBy('create_time desc');
        return $dataProvider;
    }

    public function getvideoListByArray($ids,$name){
        $query = $this->find();
        if(!empty($name)){
            $query->orFilterWhere(['id' => $ids])
                ->orWhere(['like', 'name', $name]);
        };
        return $query->select('id,name')
            ->orderBy('id desc')
            ->asArray()
            ->all();
    }
}
