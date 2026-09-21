<?php

namespace backend\models\search;

use backend\models\BrushShoes;
use backend\models\UserInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class BrushShoesSearch extends BrushShoes
{
    public $area;
    public $shift;
    public $chinese_name;
    public $start_time;
    public $end_time;
    public $meal_card_code;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['area', 'shift', 'chinese_name', 'start_time', 'end_time','meal_card_code'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * 数据查询
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->setAttributes($params);
        $startTime = ArrayHelper::getValue($this, 'start_time');
        $endTime   = ArrayHelper::getValue($this, 'end_time');
        $query = new Query();
        $query->from(BrushShoes::tableName() . ' bs');
        $query->select('bs.brush_date,bs.id,u.chinese_name,u.area,u.shift,u.meal_card_code,u.id as user_id');
        $query->leftJoin(UserInfo::tableName() . ' u', 'bs.user_id = u.id');
        $query->andFilterWhere(['u.area' => $this->area]);
        $query->andFilterWhere(['u.shift' => $this->shift]);
        $query->andFilterWhere(['like', 'u.chinese_name', trim($this->chinese_name)]);
        $query->andFilterWhere(['like', 'u.meal_card_code', trim($this->meal_card_code)]);
        $query->andFilterWhere(['>=', 'bs.brush_date', $startTime]);
        $query->andFilterWhere(['<=', 'bs.brush_date', $endTime]);
        $query->orderBy(['bs.brush_date' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}


