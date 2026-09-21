<?php
namespace  backend\models\search;

use backend\models\SocketInfos;
use yii\data\ActiveDataProvider;
use backend\models\ShelfMaterials;


class SocketInfosSearch extends SocketInfos {

    public $status;
    public $shelf_number;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'action'], 'safe'],
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
        $query = SocketInfos::find();
        // $query->andFilterWhere(['status' => $this->status]);
        // $query->andFilterWhere(['like', 'shelf_number', trim($this->shelf_number)]);
        $query->orderBy(['id'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }



}