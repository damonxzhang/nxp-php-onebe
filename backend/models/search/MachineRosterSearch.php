<?php

namespace backend\models\search;

use backend\models\MachineRoster;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class MachineRosterSearch extends MachineRoster
{
    public $start_time;
    public $end_time;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class', 'shift', 'work_date', 'start_time', 'end_time'], 'safe'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->setAttributes($params);
        $query           = MachineRoster::find();
        $startCreateTime = ArrayHelper::getValue($this, 'start_time');
        $endCreateTime   = ArrayHelper::getValue($this, 'end_time');;
        $query->andFilterWhere([
            'class' => $this->class,
            'shift' => $this->shift,
        ]);
        $query->andFilterWhere(['>=', 'work_date', $startCreateTime]);
        $query->andFilterWhere(['<=', 'work_date', $endCreateTime]);
        $query->orderBy(['work_date' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }
}

