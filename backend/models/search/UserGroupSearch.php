<?php
namespace  backend\models\search;

use backend\models\UserGroup;
use yii\data\ActiveDataProvider;

class UserGroupSearch extends UserGroup {

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
        $query = UserGroup::find();
        $query->andFilterWhere(['like', 'name', trim($this->name)]);
        $query->orderBy(['id' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}
