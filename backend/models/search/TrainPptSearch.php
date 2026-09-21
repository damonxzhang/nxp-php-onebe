<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\TrainPpt;

/**
 * TrainPptSearch represents the model behind the search form of `backend\models\TrainPpt`.
 */
class TrainPptSearch extends TrainPpt
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
        $query = TrainPpt::find()->where([ 'delete_time'=> NULL ]);
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
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        $query->orderBy('id desc');
        return $dataProvider;
    }


    public function getPptListByArray($ids,$name){
        $query = $this->find();
        if(!empty($name)){
            $query->orFilterWhere(['id' => $ids])
                ->orFilterWhere(['like', 'name', $name]);

        };
        $query->select('id,name')
        ->orderBy('id desc')
        ->asArray();
        return $query->all();
    }
}
