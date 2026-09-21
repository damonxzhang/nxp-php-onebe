<?php

namespace backend\models\search;

use backend\models\Facility;
use backend\models\MachineCheck;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class MachineCheckSearch extends MachineCheck
{
    public $number;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['check_time', 'class', 'number', 'status','process','area','eligibility'], 'safe'],
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
        $query = new Query();
        $query->select(['mc.*','f.number']);
        $query->from(MachineCheck::tableName() . ' mc');
        $query->leftJoin(Facility::tableName() . ' f', 'mc.facility_id = f.id');
        $query->andFilterWhere(['=', 'mc.area', $this->area]);
        $query->andFilterWhere(['=', 'mc.process', $this->process]);
        $query->andFilterWhere(['=', 'mc.class', $this->class]);
        $query->andFilterWhere(['=', 'mc.status', $this->status]);
        $query->andFilterWhere(['=', 'mc.eligibility', $this->eligibility]);
        $query->andFilterWhere(['=', 'mc.check_time', $this->check_time]);
        $query->andFilterWhere(['like', 'f.number', trim($this->number)]);
        $query->orderBy(['mc.id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }
}

