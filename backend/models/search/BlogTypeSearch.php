<?php
namespace  backend\models\search;

use backend\models\BlogType;
use yii\data\ActiveDataProvider;

class BlogTypeSearch extends BlogType {

    public $name;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'safe'],
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
        $query = BlogType::find();
        $query->andFilterWhere(['like', 'name', trim($this->name)]);
        $query->orderBy(['id' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}