<?php
namespace  backend\models\search;

use backend\models\LogHandoverBatch;
use yii\data\ActiveDataProvider;

class LogHandoverBatchSearch extends LogHandoverBatch {

    public $title;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title'], 'safe'],
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
        $query = LogHandoverBatch::find();
        $query->andFilterWhere(['like', 'title', trim($this->title)]);
        $query->orderBy(['id' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}
