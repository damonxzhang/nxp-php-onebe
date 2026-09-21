<?php
namespace  backend\models\search;

use backend\models\LogHandover;
use yii\data\ActiveDataProvider;

class LogHandoverSearch extends LogHandover {

    public $title;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['content','date','classes','section'], 'safe'],
        ];
    }

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {

        $this->setAttributes($param);
        $query = LogHandover::find();
        $query->where(['is_del' => 0]);
        $query->andFilterWhere(['like', 'content', trim($this->content)]);
        $query->andFilterWhere(['date' => $this->date]);
        $query->andFilterWhere(['classes' => $this->classes]);
        $query->andFilterWhere(['section' => $this->section]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}
