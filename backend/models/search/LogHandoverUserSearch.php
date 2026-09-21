<?php
namespace  backend\models\search;

use backend\models\LogHandoverUser;
use yii\data\ActiveDataProvider;

class LogHandoverUserSearch extends LogHandoverUser {

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = LogHandoverUser::find();
        $query->orderBy(['id' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}
